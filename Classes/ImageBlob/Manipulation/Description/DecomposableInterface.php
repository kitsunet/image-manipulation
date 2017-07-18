<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;

/**
 *
 */
interface DecomposableInterface
{
    public function decompose(BoxInterface $imageSize);
}
