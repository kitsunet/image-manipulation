<?php
namespace Kitsunet\ImageManipulation\Blob;

/**
 * A simple metadata container to accompany blobs.
 */
class BlobMetadata
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $mediaType;

    /**
     * @var array
     */
    protected $options;

    /**
     * BlobMetadata constructor.
     *
     * @param string $filename
     * @param string $mediaType
     * @param array $options
     */
    public function __construct(string $filename, string $mediaType, array $options)
    {
        $this->filename = $filename;
        $this->mediaType = $mediaType;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $namespace
     * @return array
     */
    public function getOptionsInNamespace(string $namespace): array
    {
        return $this->options[$namespace] ?? [];
    }

    /**
     * @param string $filename
     * @return static
     */
    public function withFilename(string $filename)
    {
        return new static($filename, $this->mediaType, $this->options);
    }

    /**
     * @param string $mediaType
     * @return static
     */
    public function withMediaType(string $mediaType)
    {
        return new static($this->filename, $mediaType, $this->options);
    }

    /**
     * @param array $options
     * @return static
     */
    public function withOptions(array $options)
    {
        return new static($this->filename, $this->mediaType, $options);
    }
}
