<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 * Trait TemporaryFileFromStreamTrait
 *
 * @package Kitsunet\ImageManipulation\ImageBlob
 */
trait TemporaryFileFromStreamTrait
{
    /**
     * @var string
     * @internal
     */
    private $_temporaryFilename;

    /**
     * Generate a temporary filename unique to this instance
     *
     * @param string $fileExtension
     * @return string
     */
    protected function getTemporaryFilename(string $fileExtension = ''): string
    {
        if ($this->_temporaryFilename === null) {
            $extension = $fileExtension ? '.' . $fileExtension : '';
            $this->_temporaryFilename = FLOW_PATH_TEMPORARY . 'imageblob_temporary_' . getmypid() . '_' . spl_object_hash($this) . $extension;
        }

        return $this->_temporaryFilename;
    }

    /**
     * removes any leftover temporary files
     */
    public function __destruct()
    {
        $filename = $this->getTemporaryFilename();
        @unlink($filename);
    }
}
