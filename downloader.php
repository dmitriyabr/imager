<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kirill.funtov
 * Date: 14.01.13
 * Time: 12:30
 * To change this template use File | Settings | File Templates.
 */

$staring_counter=0;
if(isset($argv[1])){
    $staring_counter=intval($argv[1]);
}


$pass_count=0;
if(isset($argv[2])){
    $pass_count=intval($argv[2]);
}
$config=parse_ini_file('config.ini');

$paths=array();
$xml=new SimpleXMLElement(file_get_contents($config['XML_FILE_NAME']));
$counter=$staring_counter;

foreach($xml->xpath($config['PICTURES_XPATH']) as $picture){
    $path=substr($picture, $config['PATH_START']);
    if(isset($paths[$path])){
        continue;
    }
    $paths[$path]=true;
    if($pass_count>0){
        $pass_count--;
        continue;
    }
    $tmp_img=$config['TMP_IMAGE_PATH'].$counter.'.jpg';
    $tmp_info=$config['TMP_INFO_PATH'].$counter.'.inf';
    exec("wget $picture -O $tmp_img -o /dev/null");
    $mtime=filemtime($tmp_img);
    file_put_contents($tmp_info,$path."\n".$mtime);
    $counter++;
    echo ($counter-$staring_counter)."\n";
}