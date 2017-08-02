<?php
namespace Kitsunet\ImageManipulation\ImageBlob\Manipulation;

use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\SystemLoggerInterface;

/**
 * A operation that just passes through the incoming imageblob and logs the call.
 */
class LoggingPassthroughImageManipulation implements ImageManipulationInterface
{
    /**
     * @var SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * @var array
     */
    protected $options;

    /**
     * LoggingPassthroughImageManipulation constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param SystemLoggerInterface $systemLogger
     */
    public function injectSystemLogger(SystemLoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options): LoggingPassthroughImageManipulation
    {
        return new static($options);
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        $this->systemLogger->log(
            sprintf('LoggingPassthroughImageManipulation was applied to blob of class "%s" and previously created with options: %s', get_class($image), json_encode($this->options)),
            LOG_DEBUG,
            null,
            'ImageBlob'
        );

        return $image;
    }
}
