<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kfuntov
 * Date: 15.01.13
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */ 
class Resizer {

    private $source_img;
    private $source_width=500;
    private $source_height=500;
    private $bg_color=array('red'=>255, 'green'=>255, 'blue'=>255);

    const VERTICAL_OFFSET=10;
    const HORIZONTAL_OFFSET=10;

    public function __construct($bg_color)
    {
        $this->bg_color=$bg_color;

    }

    /**
     * @param Picture $source
     * @return Picture
     */
    public function resize($source)
    {

        $this->source_img=$source->getImage();
        $this->source_width=imagesx($this->source_img);
        $this->source_height=imagesy($this->source_img);
        list($t,$b,$l,$r)=$this->getTBLR($this->getColorBorders());
        $real_width=($r-$l)+self::HORIZONTAL_OFFSET*2;
        $real_height=($b-$t)+self::VERTICAL_OFFSET*2;

        $target_size = max($real_width, $real_height);

        $target_img = imagecreatetruecolor($target_size, $target_size);

        $bg_color=$this->getBgColor($this->source_img);
        imagefill($target_img, 0, 0, $bg_color);
        $x=($target_size-$real_width)/2;
        $y=($target_size-$real_height)/2;
        imagecopy(
            $target_img,
            $this->source_img,
            $x, $y,
            $l-self::HORIZONTAL_OFFSET, $t-self::VERTICAL_OFFSET,
            $real_width, $real_height
        );

        echo 'Resized. ';
        return $source->duplicate($target_img);
    }

    private function getBgColor($buffer){
        return imagecolorallocate(
            $buffer,
            $this->bg_color['red'],
            $this->bg_color['green'],
            $this->bg_color['blue']
        );
    }




    private function getColorBorders(){
        $corners=array(
            imagecolorsforindex($this->source_img,imagecolorat($this->source_img,0,0)),
            imagecolorsforindex($this->source_img,imagecolorat($this->source_img,0,$this->source_height-1)),
            imagecolorsforindex($this->source_img,imagecolorat($this->source_img,$this->source_width-1,0)),
            imagecolorsforindex($this->source_img,imagecolorat($this->source_img,$this->source_width-1,$this->source_height-1))
        );
        $corner_colors=array('red'=>array(),'green'=>array(),'blue'=>array());
        foreach($corners as $corner){
            $corner_colors['red'][]=$corner['red'];
            $corner_colors['green'][]=$corner['green'];
            $corner_colors['blue'][]=$corner['blue'];
        }

        $color_borders=array();
        foreach($corner_colors as $chanel_name => $chanel){
            $color_borders[$chanel_name]=array('max'=>max($chanel)+10,'min'=>min($chanel)-10);
        }
        return $color_borders;
    }

    private function getTBLR($color_borders){
        return array(
            $this->getTop($color_borders),
            $this->getBottom($color_borders),
            $this->getLeft($color_borders),
            $this->getRight($color_borders)
        );
    }

    const MAIN_STEP=2;
    const SECOND_STEP=5;

    /**
     * @param $color_borders
     * @return mixed
     */
    private function getRight($color_borders)
    {
        $right = null;
        for ($x1 = $this->source_width - 1; $x1 >= 0; $x1-=self::MAIN_STEP) {
            for ($y1 = 0; $y1 < $this->source_height; $y1+=self::SECOND_STEP) {
                $color1 = imagecolorsforindex($this->source_img, imagecolorat($this->source_img, $x1, $y1));
                if ($this->isNotIn($color1, $color_borders)) {
                    $right = $x1;
                    break;
                }
            }
            if (!is_null($right)) {
                break;
            }
        }
        $right = min($right, $this->source_width - self::HORIZONTAL_OFFSET);
        return $right;
    }


    /**
     * @param $color_borders
     * @return mixed
     */
    private function getLeft($color_borders)
    {
        $left = null;
        for ($x1 = 0; $x1 < $this->source_width; $x1+=self::MAIN_STEP) {
            for ($y1 = 0; $y1 < $this->source_height; $y1+=self::SECOND_STEP) {
                $color1 = imagecolorsforindex($this->source_img, imagecolorat($this->source_img, $x1, $y1));
                if ($this->isNotIn($color1, $color_borders)) {
                    $left = $x1;
                    break;
                }
            }
            if (!is_null($left)) {
                break;
            }
        }
        $left = max($left, self::HORIZONTAL_OFFSET);
        return $left;
    }

    /**
     * @param $color_borders
     * @return mixed
     */
    private function getBottom($color_borders)
    {
        $bottom = null;
        for ($y1 = $this->source_height - 1; $y1 >= 0; $y1-=self::MAIN_STEP) {
            for ($x1 = 0; $x1 < $this->source_width; $x1+=self::SECOND_STEP) {
                $color1 = imagecolorsforindex($this->source_img, imagecolorat($this->source_img, $x1, $y1));
                if ($this->isNotIn($color1, $color_borders)) {
                    $bottom = $y1;
                    break;
                }
            }
            if (!is_null($bottom)) {
                break;
            }
        }
        $bottom = min($bottom, $this->source_height - self::VERTICAL_OFFSET);
        return $bottom;
    }

    /**
     * @param $color_borders
     * @return array
     */
    private function getTop($color_borders)
    {
        $top = null;
        for ($y1 = 0; $y1 < $this->source_height; $y1+=self::MAIN_STEP) {
            for ($x1 = 0; $x1 < $this->source_width; $x1+=self::SECOND_STEP) {
                $color = imagecolorsforindex($this->source_img, imagecolorat($this->source_img, $x1, $y1));
                if ($this->isNotIn($color, $color_borders)) {
                    $top = $y1;
                    break;
                }
            }
            if (!is_null($top)) {
                break;
            }
        }
        $top = max($top, self::VERTICAL_OFFSET);
        return $top;
    }

    private function isNotIn($cur_color, $color_borders){
        foreach($color_borders as $chanel_name => $chanel){
            if($chanel['max']<$cur_color[$chanel_name] || $cur_color[$chanel_name]<$chanel['min']){
                return true;
            }
        }
        return false;
    }

}
