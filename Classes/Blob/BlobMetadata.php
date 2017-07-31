<?php
namespace Kitsunet\ImageManipulation\Blob;

/**
 * A simple metadata container to accompany blobs.
 */
class BlobMetadata
{
    /**
     * @var array
     */
    protected $properties;

    /**
     * BlobMetadata constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getProperty($name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }
}
