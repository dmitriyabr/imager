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

    public function save($root_directory, $save_mtime=true)
    {
        $whole_path=$root_directory.$this->path;

        $directory=dirname($whole_path);
        if(!file_exists($directory)){

            mkdir($directory,0777,true);
        }
        if(imagejpeg($this->image,$whole_path , 100)){

            echo 'Saved ';
            if($save_mtime)
                DB::setMTime($this->path, $this->mtime);
        }
        else{
            echo 'coud n\'t save an image to '.$whole_path;
        }
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
            echo 'already up to date';
            return false;
        }

        $image=imagecreatefromjpeg($source);
        if($image===false){
            echo 'could n\'t read an image' ;
            return false;
        }
        echo 'Read ';
        return new Picture($image, $path, $mtime);

    }
}
