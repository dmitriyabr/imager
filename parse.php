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

$count=0;
foreach($xml->xpath(PICTURES_XPATH) as $picture){
    $count++;
    if($count<-5){

        echo '.';
        $path=substr($picture, PATH_START);
        $directory=dirname($path);
        if(!file_exists($directory)){
            mkdir($directory,0777,true);
        }
        passthru("wget $picture -O ".TEMP_IMAGE_FILE.' > /dev/null');
        passthru('php resize_target_image.php "'.TEMP_IMAGE_FILE.'" "'.EXPORT_PATH.$path.'" 500 500');
    }
}


echo "$count\n\n";
