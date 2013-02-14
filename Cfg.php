<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 11:58
 * To change this template use File | Settings | File Templates.
 */ 
class Cfg {

    private static $config=array();

    public static function init($config_file)
    {
            self::$config=parse_ini_file($config_file);
    }

    public static function __callStatic($name, $args){
        if(isset(self::$config[$name])){
            return self::$config[$name];
        }
        return null;
    }

}
