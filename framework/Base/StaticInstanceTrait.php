<?php

namespace Mix\Base;

/**
 * Trait InstanceTrait
 * @author 刘健 <coder.liu@qq.com>
 */
trait StaticInstanceTrait
{

    /**
     * 使用静态方法创建实例
     * @param mixed ...$args
     * @return $this
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }

    /**
     * 创建实例，通过配置名
     * @param $name
     * @return $this
     */
    public static function newInstanceByConfig($name)
    {
        $class  = get_called_class();
        $config = \Mix::app()->config($name);
        $object = \Mix::createObject($config);
        if (get_class($object) != $class) {
            throw new \Mix\Exceptions\ConfigException('实例化类型与配置类型不符');
        }
        return $object;
    }

}
