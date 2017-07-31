<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * Contract for a service that can manipulate PersistentResources.
 */
interface ResourceProcessorInterface
{
    /**
     * @param PersistentResource $originalResource
     * @param array $manipulationDescriptions
     * @return array resource, width, height as keys
     */
    public function processResource(PersistentResource $originalResource, array $manipulationDescriptions);
}
