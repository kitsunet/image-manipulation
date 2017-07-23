<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

/**
 * Manipulation descriptions are a meta format for descripting generic
 * manipulation operations applied on images. Any given description contains
 * all information to turn it into a sepcific implentation changing an image
 * with a specific library.
 *
 * @package Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description
 */
interface ManipulationDescriptionInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getOptions();
}
