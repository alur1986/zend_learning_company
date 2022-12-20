<?php

namespace Report\Service;

use Report\Service\Options as AbstractOptions;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class TemplateService extends AbstractService
{

    /**
     * The Report service options
     *
     * @var AbstractOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param AbstractOptions $options
     */
    public function __construct ($entityManager, AbstractOptions $options)
    {
        $this->options = $options;
        parent::__construct($entityManager);
    }

    /**
     * Find ALL templates given the site ID
     *
     * @param integer $siteId
     * @param string $type
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllTemplatesBySiteId ($siteId, $type)
    {
        $entityManager = $this->getEntityManager();
        $repository = $this->reportTemplateRepository();

        // create query
        $qb = $repository->createQueryBuilder('template')
            ->select('template, site')
            ->leftJoin('template.site', 'site')
            ->where('site.siteId = :siteId AND template.type = :type')
            ->setParameter('siteId', $siteId)
            ->setParameter('type', $type)
            ->add('orderBy', 'template.title ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ONE template by template ID
     *
     * @param integer $templateId
     * @return Entity\ReportTemplate
     */
    public function findOneTemplateById ($templateId)
    {
        $repository = $this->reportTemplateRepository();

        // create query
        $qb = $repository->createQueryBuilder('template')
            ->select('template, site')
            ->leftJoin('template.site', 'site')
            ->where('template.templateId = :templateId')
            ->setParameter('templateId', $templateId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE template by slug and site ID
     *
     * @param string $slug
     * @param integer $siteId
     * @return Entity\ReportTemplate
     */
    public function findOneTemplateBySlug ($slug, $siteId, $type)
    {
        $entityManager = $this->getEntityManager();
        $repository = $this->reportTemplateRepository();

        // create query
        $qb = $repository->createQueryBuilder('template')
            ->select('template, site')
            ->leftJoin('template.site', 'site')
            ->where('template.slug = :slug AND site.siteId = :siteId AND template.type = :type')
            ->setParameter('slug', $slug)
            ->setParameter('siteId', $siteId)
        	->setParameter('type', $type);

        // execute query
        $result = $repository->fetchOne($qb);

        // if not found, check the config array
        if (!$result) {
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
            $options = $this->options;

            // create the magic method for the report config
            $key = substr($type, strlen('report-'));
            $method = 'get' . ucfirst(Stdlib\StringUtils::camelCase($key));
            $config = $options->{$method}();
            if (!$config) {
                return null;
            }
            $templates = isset($config['templates']) ? $config['templates'] : null;

            if (isset($templates[$slug])) {
                $config = $templates[$slug];

                $template = new Entity\ReportTemplate();
                $template = Stdlib\ObjectUtils::hydrate($config, $template);
                $template['type'] = $type;
                $template['site'] = $site;
                $template['slug'] = Stdlib\StringUtils::dashed($slug);

                // save in repository
                // $entityManager->persist($template);
                // $entityManager->flush($template);
                // $entityManager->clear();
                return $template;
            }
        }

        return $result;
    }

    /**
     * Create ONE template
     *
     * @param array|\Traversable $data
     * @param integer $siteId
     * @return Entity\ReportTemplate
     * @throws \Exception
     */
    public function createTemplate ($data, $siteId)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            // check if template title is given
            if (!isset($data['title']) || !$data['title']) {
                throw new Exception\InvalidArgumentException(sprintf('Template title must be provided'), 500);
            }

            // create the slug
            $title = $data['title'];
            $type = array_key_exists('type', $data) ? $data['type'] : null;

            // in order to ensure each site has a 'default' template
            if ($this->_hasTemplate($type, $siteId)) {
                $data['slug'] = $this->createSlug($title, $type, $siteId);
            } else {
                $data['slug'] = 'default';
            }

            $entityManager = $this->getEntityManager();
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // create new report template entity
            $template = new Entity\ReportTemplate();
            $template = Stdlib\ObjectUtils::hydrate($data, $template);
            $template['site'] = $site;

            // save in repository
            $entityManager->persist($template);
            $entityManager->flush($template);

            return $template;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE template in repository
     *
     * @param Entity\ReportTemplate $data
     * @throws \Exception
     * @return Entity\ReportTemplate
     */
    public function updateTemplate ($data)
    {
        try {
            $entityManager = $this->getEntityManager();
            $templateId = $data['template_id'];
            $template = $entityManager->getReference('Savvecentral\Entity\ReportTemplate', $templateId);
            $template = Stdlib\ObjectUtils::hydrate($data, $template);
            $siteId = $template['site']['site_id'];
            $template['slug'] = $this->createSlug($template['title'], $template['type'], $siteId, $template['template_id']);

            // save in repository
            $entityManager = $this->getEntityManager();
            $entityManager->persist($template);
            $entityManager->flush($template);

            return $template;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE template from repository
     * @param integer $templateId
     * @throws \Exception
     */
    public function deleteTemplate ($templateId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $template = $entityManager->getReference('Savvecentral\Entity\ReportTemplate', $templateId);

            // save in repository
            $entityManager->remove($template);
            $entityManager->flush($template);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a slug
     *
     * @param string $title The title to convert to slug
     * @param integer $siteId The site ID of the article is associated with
     * @param integer $templateId The page ID (for update) of the current page
     * @return string $slug The resulting string slug
     */
    public function createSlug ($title, $type, $siteId, $templateId = null)
    {
        $slug = Stdlib\StringUtils::slugify($title);

        // check if the slug is unique for the site
        $isUnique = $this->_isUniqueSlug($slug, $type, $siteId, $templateId);
        if (!$isUnique) {
            $suffix = 1;

            // keep searching a new slug that is unique
            do {
                $newSlug = sprintf("%s-%s", $slug, $suffix);
                $isUnique = $this->_isUniqueSlug($newSlug, $type, $siteId, $templateId);
                $suffix++;
            }
            while (!$isUnique);
            $slug = $newSlug;
        }

        return $slug;
    }

    /**
     * Check if the slug is unique to the site
     *
     * @param string $slug The slug to search in the database
     * @param integer $siteId The site ID associated with the slug
     * @param integer $templateId The page ID (for update) of the current page
     * @return boolean FALSE if the slug is not unique, TRUE is unique
     */
    private function _isUniqueSlug ($slug, $type, $siteId, $templateId = null)
    {
        $repository = $this->reportTemplateRepository();
        $routeMatch = $this->routeMatch();

        // create query
        $qb = $repository->createQueryBuilder('template')
            ->select('template, site')
            ->leftJoin('template.site', 'site')
            ->where('site.siteId = :siteId AND template.slug = :slug AND template.type = :type')
            ->setParameter('siteId', $siteId)
            ->setParameter('slug', $slug)
            ->setParameter('type', $type);

        // if template ID is provided, exclude that template from the search
        if ($templateId) {
            $qb->andWhere('template.templateId != :templateId')
                ->setParameter('templateId', $templateId);
        }

        // execute query
        $result = $repository->fetchOne($qb);

        return $result ? false : true;
    }

    /**
     * Check if a site has a template of the type provided
     *
     * @param $type
     * @param $siteId
     * @return bool
     */
    private function _hasTemplate($type, $siteId)
    {
        $repository = $this->reportTemplateRepository();

        // create query
        $qb = $repository->createQueryBuilder('template')
            ->select('template, site')
            ->leftJoin('template.site', 'site')
            ->where('site.siteId = :siteId AND template.type = :type')
            ->setParameter('siteId', $siteId)
            ->setParameter('type', $type);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result ? false : true;
    }

    /**
     * Get the report template doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function reportTemplateRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\ReportTemplate');
        return $repository;
    }
}