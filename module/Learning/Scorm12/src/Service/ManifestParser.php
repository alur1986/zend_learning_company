<?php

namespace Scorm12\Service;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Stdlib\AbstractEntity;

class ManifestParser extends AbstractEntity
{

    /**
     * The parsed manifest content
     *
     * @var array
     */
    protected $manifestContent;

    /**
     * Constructor
     *
     * @param string $manifestFileName
     */
    public function __construct ($manifestFileName)
    {
        $this->manifestContent = $this->readManifestFile($manifestFileName);
    }

    /**
     * Read the Scorm 1.2 manifest file, parse and return as an multi-dimensional array
     *
     * @param string $manifestFileName
     * @throws Exception\DomainException
     * @return array
     */
    public function readManifestFile ($manifestFileName)
    {
        // check if manifest file exists
        if (!file_exists($manifestFileName) ||  !realpath($manifestFileName)) {
            throw new Exception\DomainException(sprintf('Manifest file "%s" does not exist or is not readable.', $manifestFileName));
        }

		// read the contents of the manifest file into XML string
        $content = file_get_contents($manifestFileName);

        // strip some unnecessary strings from the manifest XML string
        $content = str_replace([ "<vocabulary>", "</vocabulary>", "adlcp:", 'type="aicc_script"' ], [ "", "", "" ], $content);

        // read the XML manifest file
        $reader = new \Zend\Config\Reader\Xml();
        $content = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $content);
        $content = $reader->fromString($content);
        $this->manifestContent = $content;

        return $this->manifestContent;
    }

    /**
     * Get the manifest metadata
     *
     * @return array
     */
    public function getMetadata ()
    {
        $this->metadata = $this->manifestContent['metadata'];
        return $this->metadata;
    }

    /**
     * Get the manifest organizations
     *
     * @return array
     */
    public function getOrganizations ()
    {
        $this->organizations = $this->manifestContent['organizations'];
        return $this->organizations;
    }

    /**
     * Get the manifest resources
     *
     * @return array
     */
    public function getResources ()
    {
        $this->resources = $this->manifestContent['resources'];
        return $this->resources;
    }

    /**
     * Get the items from the first organization in the manifest
     *
     * @return array
     */
    public function getItems ()
    {
        $organizations = $this->organizations;
        $organization = is_numeric(key(current($organizations))) ? current($organizations['organization']) : $organizations['organization'];
        $item = $organization['item'];
        $items = is_numeric(key($item)) ? $item : [ $item ];

        foreach ($items as $key => $item) {
            $identifierRef = isset($item['identifierref']) ? $item['identifierref'] : null;
            $resource = $this->getResource($identifierRef);
            $item['itemlocation'] = isset($resource['href']) ? $resource['href'] : null;
            $items[$key] = $item;
        }

        $this->items = $items;

        return $this->items;
    }

    /**
     * Retrieve the resource entry for ONE item using identifierRef
     *
     * @param string $identifierRef
     * @return array
     */
    public function getResource ($identifierRef)
    {
        $resources = $this->resources;
        $resources = is_numeric(key($resources['resource'])) ? $resources['resource'] : [
            $resources['resource']
        ];
        foreach ($resources as $resource) {
            if (array_key_exists('identifier', $resource) && $resource['identifier'] === $identifierRef) {
                return $resource;
            }
        }

        return [];
    }
}
