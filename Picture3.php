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

            echo 'Saved to ', $whole_path;
            if($save_mtime) {
                if(touch($whole_path, $this->mtime)) {
                    echo " (mtime changed)";
                    exec("rsync -akvR /data4/./2/0/2010_11_05_00015690_1.jpg resize01.local::retargeting/", $devnull);
                    exec("rsync -akvR /data4/./2/0/2010_11_05_00015690_1.jpg resize03.local::retargeting/", $devnull);
                } else {
                    echo " (mtime ERROR)";
                }
            }
            echo "\n";
        }
        else{
            echo 'coud n\'t save an image to ', $whole_path, "\n";
        }
    }
}
