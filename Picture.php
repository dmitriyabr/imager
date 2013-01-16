<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */ 
class Picture {

    private $path;
    private $image;
    private $mtime;

    public function __construct($image, $path, $mtime)
    {
        $this->image=$image;
        $this->path=$path;
        $this->mtime=$mtime;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function duplicate($image=null, $path=null, $mtime=null)
    {
        return new Picture(
            is_null($image) ? $this->image : $image,
            is_null($path)  ? $this->path  : $path,
            is_null($mtime) ? $this->mtime : $mtime
        );
    }

    public function save($root_directory)
    {
        $whole_path=$root_directory.$this->path;

        $directory=dirname($whole_path);
        if(!file_exists($directory)){

            mkdir($directory,0777,true);
        }
        if(imagejpeg($this->image,$whole_path , 100)){
           DB::setMTime($this->path, $this->mtime);
        }
        else{
            echo 's';
        }
        echo 'S';
    }


    /**
     * @param $info
     * @param $source
     * @return bool|Picture
     */
    public static function createFromInfo($info, $source)
    {
        $fp=fopen($info,'r');
        if($fp===false){
            echo 'f';
            return false;
        }
        $path=trim(fgets($fp));
        $mtime=intval(trim(fgets($fp)));
        fclose($fp);
        if($mtime<=DB::getMTime($path)){
            echo 'm';
            return false;
        }

        $image=imagecreatefromjpeg($source);
        if($image===false){
            echo 'i';
            return false;
        }
        echo 'P';
        return new Picture($image, $path, $mtime);

    }
}
