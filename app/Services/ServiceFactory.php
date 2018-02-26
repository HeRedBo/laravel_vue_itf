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
     * 模型静态存储变量
     * @var array
     */
    protected  static  $_models  = [];

    private  static  $model_path = "\\App\\Models";
    
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
     * 服务工程 模型方法
     *
     * @param string $class
     * @return mixed
     */
    public  static  function  getModel($class)
    {
        $className = self::getModelRealName($class);
        if(!isset(self::$_models[$className]))
        {
            return self::$_models[$className] = new $className();
        }
        else
        {
            return self::$_models[$className];
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

    protected  static  function  getModelRealName($class)
    {
        $namespace = self::$model_path;
        $modelName = $namespace .'\\'. $class;
        return $modelName;
    }
}