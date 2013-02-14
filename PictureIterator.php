<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */ 
class PictureIterator {

    /**
     * @var Resizer
     */
    private $resizer;


    private $info;

    public function __construct(Resizer $r)
    {
        $this->resizer=$r;
    }



    public function iterateThrough($dirname)
    {
        $directory=dir($dirname);
        $counter=0;
        while (false !== ($info = $directory->read())) {
            if(substr($info,-4)!=='.inf'){
                continue;
            }
            $counter++;
            echo "\n",$counter, " $info : ";
            $this->info=$info;
            if(!$this->lock()){
                echo 'locked';
                continue;
            }
            $picture=Picture::createFromInfo($this->getLockedInfoName(), $this->getPictureSource());
            if($picture!==false){
                $picture=$this->resizer->resize($picture);
                $picture->save(Cfg::EXPORT_PATH());
                echo "OK";
            }
            $this->cleanup();
        }
    }

    private function cleanup(){
        unlink($this->getLockedInfoName());
        unlink($this->getPictureSource());
    }


    private function getInfoName()
    {
        return Cfg::TMP_INFO_PATH().$this->info;
    }

    private function getLockedInfoName()
    {
        return $this->getInfoName().'l';
    }

    private function getPictureSource()
    {
        return Cfg::TMP_IMAGE_PATH().substr($this->info,0,-3).'jpg';
    }

    private function lock()
    {
        @ $result =rename($this->getInfoName(),$this->getLockedInfoName());
        return $result;
    }


}
