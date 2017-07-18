<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

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
