<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation\Description;

/**
 *
 */
class GenericManipulationDescription implements ManipulationDescriptionInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * GenericManipulationDescription constructor.
     *
     * @param string $type
     * @param array $options
     */
    public function __construct(string $type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
