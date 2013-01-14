<?php
/**
 * Created by IntelliJ IDEA.
 * User: kfuntov
 * Date: 09.01.13
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */

const XML_FILE_NAME='ymarket.xml';
const PICTURES_XPATH='//offer/picture';
const PATH_START=43;
const TEMP_IMAGE_FILE='temp.jpg';
const EXPORT_PATH='/data/';

$xml=new SimpleXMLElement(file_get_contents(XML_FILE_NAME));

foreach($xml->xpath(PICTURES_XPATH) as $picture){

        echo '.';
        $path=substr($picture, PATH_START);
        $directory=dirname($path);
        if(!file_exists(EXPORT_PATH.$directory)){

            //    exec('mkdir -p '.EXPORT_PATH.$directory);
            mkdir(EXPORT_PATH.$directory,0777,true);
		echo EXPORT_PATH.$directory,"\n";
        }
        exec("wget $picture -O ".TEMP_IMAGE_FILE.' > /dev/null');
        exec('php resize_target_image.php "'.TEMP_IMAGE_FILE.'" "'.EXPORT_PATH.$path.'" 500 500');
        echo '.';
    
}


