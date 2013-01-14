<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kirill.funtov
 * Date: 14.01.13
 * Time: 12:30
 * To change this template use File | Settings | File Templates.
 */

$config=parse_ini_file('config.ini');

    $xml=new SimpleXMLElement(file_get_contents($config['XML_FILE_NAME']));
    $counter=0;
    foreach($xml->xpath($config['PICTURES_XPATH']) as $picture){
        $tmp_img=$config['TMP_IMAGE_PATH'].$counter.'.jpg';
        $tmp_info=$config['TMP_INFO_PATH'].$counter.'.inf';
       // exec("wget $picture -O $tmp_img -o /dev/null");
        exec("wget $picture -O $tmp_img");
        $path=substr($picture, $config['PATH_START']);
        $mtime=filemtime($tmp_img);
        file_put_contents($tmp_info,$path."\n".$mtime);
        $counter++;
        //echo "$counter\n";
    }