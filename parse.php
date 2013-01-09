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

$xml=new SimpleXMLElement(file_get_contents(XML_FILE_NAME));

$count=array();
foreach($xml->xpath(PICTURES_XPATH) as $picture){
    $path=substr($picture, PATH_START);
    echo $path,"\n";
}
