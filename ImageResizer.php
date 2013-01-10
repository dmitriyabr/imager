<?php
/**
 * Created by JetBrains PhpStorm.
 * User: funtaps
 * Date: 17.05.12
 * Time: 15:36
 */
class ImageResizer{
    private $_color_borders;
    private $_start_size;
    private $_source_img;
    private $_top;
    private $_bottom;
    private $_left;
    private $_right;
    private $bg_color;
    const VERTICAL_OFFSET=10;
    const HORIZONTAL_OFFSET=10;

    public function __construct($source_file, $bg_color=null){
        $this->bg_color=$bg_color;
        $this->_start_size = getimagesize($source_file);
        $this->_source_img=imagecreatefromjpeg($source_file);
        $this->getColorBorders();
        $this->get_top_bottom_left_right();
    }

    public function __destruct(){
        imagedestroy($this->_source_img);
    }

    public function resizeAndSave($target_file,$target_width=null, $target_height=null,  $quality=100){
        $real_width=($this->_right-$this->_left)+self::HORIZONTAL_OFFSET*2;
        $real_height=($this->_bottom-$this->_top)+self::VERTICAL_OFFSET*2;



        if(is_null($target_width)&&is_null($target_height)){
            $target_img=imagecreatetruecolor($real_width,$real_height);
            imagecopy(
                $target_img,
                $this->_source_img,
                0, 0,
                $this->_left-self::HORIZONTAL_OFFSET, $this->_top-self::VERTICAL_OFFSET,
                $real_width,$real_height
            );
        }
        else{
            if(is_null($target_width)){
                $target_width=$real_width;
            }
            if(is_null($target_height)){
                $target_height=$real_height;
            }


            $target_img = imagecreatetruecolor($target_width, $target_height);

            $bg_color=$this->getBgColor($this->_source_img);
            imagefill($target_img, 0, 0, $bg_color);



            $ratio=max($real_width/$target_width, $real_height/$target_height, 1);
            $width=$real_width/$ratio;
            $height=$real_height/$ratio;
            $x=($target_width-$width)/2;
            $y=($target_height-$height)/2;
            imagecopyresampled(
                $target_img,
                $this->_source_img,
                $x, $y,
                $this->_left-self::HORIZONTAL_OFFSET, $this->_top-self::VERTICAL_OFFSET,
                $width, $height,
                $real_width, $real_height
            );
        }

        imagejpeg($target_img, $target_file, $quality);
        imagedestroy($target_img);
    }




    private function getColorBorders(){
        $corners=array(
            imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,0,0)),
            imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,0,$this->_start_size[1]-1)),
            imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$this->_start_size[0]-1,0)),
            imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$this->_start_size[0]-1,$this->_start_size[1]-1))
        );
        $corner_colors=array('red'=>array(),'green'=>array(),'blue'=>array());
        foreach($corners as $corner){
            $corner_colors['red'][]=$corner['red'];
            $corner_colors['green'][]=$corner['green'];
            $corner_colors['blue'][]=$corner['blue'];
        }

        $this->_color_borders=array();
        foreach($corner_colors as $chanel_name => $chanel){
            $this->_color_borders[$chanel_name]=array('max'=>max($chanel)+10,'min'=>min($chanel)-10);
        }
    }

    private function get_top_bottom_left_right(){
        $this->_top=null;
        for($y=0;$y<$this->_start_size[1];$y++){
            for($x=0;$x<$this->_start_size[0];$x++){
                $color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
                if($this->isNotABackGround($color)){
                    $this->_top=$y;
                    break;
                }
            }
            if(!is_null($this->_top)){
                break;
            }
        }
        $this->_top=max($this->_top,self::VERTICAL_OFFSET);


        $this->_bottom=null;
        for($y=$this->_start_size[1]-1;$y>=0;$y--){
            for($x=0;$x<$this->_start_size[0];$x++){
                $color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
                if($this->isNotABackGround($color)){
                    $this->_bottom=$y;
                    break;
                }
            }
            if(!is_null($this->_bottom)){
                break;
            }
        }
        $this->_bottom=min($this->_bottom,$this->_start_size[1]-self::VERTICAL_OFFSET);


        $this->_left=null;
        for($x=0;$x<$this->_start_size[0];$x++){
            for($y=0;$y<$this->_start_size[1];$y++){
                $color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
                if($this->isNotABackGround($color)){
                    $this->_left=$x;
                    break;
                }
            }
            if(!is_null($this->_left)){
                break;
            }
        }
        $this->_left=max($this->_left,self::HORIZONTAL_OFFSET);


        $this->_right=null;
        for($x=$this->_start_size[0]-1;$x>=0;$x--){
            for($y=0;$y<$this->_start_size[1];$y++){
                $color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
                if($this->isNotABackGround($color)){
                    $this->_right=$x;
                    break;
                }
            }
            if(!is_null($this->_right)){
                break;
            }
        }
        $this->_right=min($this->_right,$this->_start_size[0]-self::HORIZONTAL_OFFSET);
    }



    private function isNotABackGround($cur_color){
        foreach($this->_color_borders as $chanel_name => $chanel){
            if($chanel['max']<$cur_color[$chanel_name] || $cur_color[$chanel_name]<$chanel['min']){
                return true;
            }
        }
        return false;
    }


    private function getBgChanel($channel_name){
        if(!is_null($this->bg_color) && isset($this->bg_color[$channel_name])){
            return $this->bg_color[$channel_name];
        }
        return ($this->_color_borders[$channel_name]['max']+$this->_color_borders[$channel_name]['min'])/2;
    }

    private function getBgColor($buffer){

        return imagecolorallocate(
            $buffer,
            $this->getBgChanel('red'),
            $this->getBgChanel('green'),
            $this->getBgChanel('blue')
        );
    }

}
