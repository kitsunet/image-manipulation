<?php
namespace Kitsunet\ImageManipulation\ImageBlob;

/**
 *
 */
abstract class ConfigurationToDescriptionMapper
{
    /**
     * @param array $descriptionConfiguration
     * @return array
     */
    public static function mapDescriptionConfiguration(array $descriptionConfiguration)
    {
        $descriptionConfiguration = array_filter($descriptionConfiguration, function ($item) {
            if ($item === null) {
                return false;
            }
            if (!isset($item['class'])) {
                return false;
            }

            return true;
        });

        return array_map(function (array $item) {
            $arguments = $item['arguments'] ?? [];
            return static::instantiateClass($item['class'], $arguments);
        }, $descriptionConfiguration);
    }

    /**
     * @param $className
     * @param $arguments
     * @return mixed
     *
     * @TODO: Shameless copy from Flow ObjectManager, should probably become static method in ObjectHandling.
     */
    protected function instantiateClass($className, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                $object = new $className();
                break;
            case 1:
                $object = new $className($arguments[0]);
                break;
            case 2:
                $object = new $className($arguments[0], $arguments[1]);
                break;
            case 3:
                $object = new $className($arguments[0], $arguments[1], $arguments[2]);
                break;
            case 4:
                $object = new $className($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;
            case 5:
                $object = new $className($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
                break;
            case 6:
                $object = new $className($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
                break;
            default:
                $class = new \ReflectionClass($className);
                $object = $class->newInstanceArgs($arguments);
        }
        return $object;
    }
}
