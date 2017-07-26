<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

use Kitsunet\ImageManipulation\Blob\BlobInterface;

/**
 * An interface for image blobs that can be manipulated
 *
 */
interface ImageBlobInterface extends BlobInterface
{
    /**
     * @return BoxInterface
     */
    public function getSize(): BoxInterface;
}
