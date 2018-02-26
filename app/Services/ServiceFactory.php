<?php
/**
 * 服务调度工厂类
 * @author Red-Bo
 * @date 2018-02-26 14:33:00
 */
namespace App\Services;

class  ServiceFactory
{
    /**
     * 服务静态存储变量
     * @var array
     */
    protected  static  $_services = [];
    
    /**
     * 服务获取调度方法
     * @param string $class 服务名称
     * @return mixed
     * @author Red-Bo
     */
    public static function getService($class)
    {
        $className = self::getClassRealName($class);
        if(!isset(self::$_services[$className]))
        {
            return self::$_services[$className] = new $className();
        }
        else
        {
            return self::$_services[$className];
        }
    }
    
    /**
     * 获取服务的路径
     * @param  string $class
     * @return string
     * @author Red-Bo
     */
    protected static function  getClassRealName($class)
    {
        $namespace = __NAMESPACE__;
        $className = $namespace. '\\'. $class;
        return $className;
    }
}