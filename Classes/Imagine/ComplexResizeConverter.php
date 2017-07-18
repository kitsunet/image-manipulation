<?php
namespace Kitsunet\ImageManipulation\Imagine;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description\ComplexResizeDescription;

/**
 *
 */
class ComplexResizeConverter
{
    /**
     * @var ImageBlobInterface
     */
    protected $imageBlob;

    /**
     * @var ComplexResizeDescription
     */
    protected $description;

    /**
     * ComplexResizeConverter constructor.
     *
     * @param ImageBlobInterface $imageBlob
     * @param ComplexResizeDescription $description
     */
    public function __construct(ImageBlobInterface $imageBlob, ComplexResizeDescription $description)
    {

    }
}
