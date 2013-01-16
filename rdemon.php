<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

ini_set('display_errors',true);
error_reporting(E_ALL);

require 'Cfg.php';
require 'DB.php';
require 'Picture.php';
require 'PictureIterator.php';
require 'Resizer.php';

Cfg::init('config.ini');

$r=new Resizer(500,500,array('red'=>255,'green'=>255,'blue'=>255));

$di=new PictureIterator($r);

while(true){
    $di->iterateThrough(Cfg::TMP_INFO_PATH());
}
