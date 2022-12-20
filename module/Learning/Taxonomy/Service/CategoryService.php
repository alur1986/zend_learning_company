<?php

namespace Learning\Taxonomy\Service;

use Taxonomy\Service\CategoryService as AbstractService;
use Doctrine\Common\Collections\Criteria;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryService extends AbstractService
{

    /**
     * Find ALL categories by site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllCategoriesBySiteId ($siteId, $group = null)
    {
        $categories = parent::findAllCategoriesBySiteId($siteId, 'activity');

        // create a tree structure
        $categories = $this->createTree($categories);

        return $categories;
    }

    /**
     * Find ONE category by ID
     *
     * @param integer $categoryId
     * @return Entity\Taxonomy
     */
    public function findOneCategoryById ($categoryId)
    {
        $category = parent::findOneTaxonomyById($categoryId);

        return $category;
    }

    /**
     * Find ONE category by slug
     *
     * @param string $slug
     * @return Entity\Taxonomy
     */
    public function findOneCategoryBySlug ($slug)
    {
        $repository = $this->taxonomyRepository();

        // create query
        $qb = $repository->createQueryBuilder('taxonomy')
            ->select('taxonomy, site, activities')
            ->leftJoin('taxonomy.site', 'site')
            ->leftJoin('taxonomy.activities', 'activities')
            ->where('taxonomy.slug = :slug')
            ->setParameter('slug', $slug)
            ->add('orderBy', 'activities.title ASC');

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Create ONE new category
     *
     * @param array|\Traversable $data
     * @param integer $siteId
     * @return Entity\Taxonomy
     */
    public function createCategory ($data, $siteId = null)
    {
        $data['term_group'] = 'activity';
        $category = parent::createCategory($data, $siteId);
        return $category;
    }
}