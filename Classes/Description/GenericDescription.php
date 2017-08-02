<?php
namespace Kitsunet\ImageManipulation\Description;

/**
 *
 */
class GenericDescription implements ManipulationDescriptionInterface
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
     * GenericDescription constructor.
     *
     * @param string $type
     * @param array $options
     */
    public function __construct(string $type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
