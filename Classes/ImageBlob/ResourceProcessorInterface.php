<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Neos\Flow\ResourceManagement\PersistentResource;

/**
 * Contract for a service that can manipulate PersistentResources.
 */
interface ResourceProcessorInterface
{
    /**
     * @param PersistentResource $originalResource
     * @param ImageManipulationInterface[] $manipulations
     * @return array resource, width, height as keys
     */
    public function processResource(PersistentResource $originalResource, array $manipulations);
}
