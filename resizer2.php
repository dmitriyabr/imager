<?php
/**
 * Created by IntelliJ IDEA.
 * User: kfuntov
 * Date: 14.02.13
 * Time: 13:21
 * To change this template use File | Settings | File Templates.
 */

include 'ImageResizer.php';

function resize_recursive($dir_name, $config){
    foreach(scandir($dir_name) as $inner){
        if($inner[0]==='.')
            continue;
        $whole_path=$dir_name.DIRECTORY_SEPARATOR.$inner;
        if(is_dir($whole_path))
            resize_recursive($whole_path, $config);
        elseif(is_file($whole_path) && (substr($inner,-4)==='.jpg'||substr($inner,-5)==='.jpeg')){
            $ir=new ImageResizer($whole_path,array('red'=>255,'green'=>255,'blue'=>255));
            $ir->resizeAndSave(str_replace($config['SOURCE_DIR'],$config['TARGET_DIR'],$whole_path),$config['TARGET_WIDTH'],$config['TARGET_HEIGHT']);
        }

    }
}

$config=parse_ini_file('config2.ini');
resize_recursive($config['SOURCE_DIR'], $config);