<?php
/**
 * Created by JetBrains PhpStorm.
 * User: funtaps
 * Date: 17.05.12
 * Time: 15:36
 */
class ImageResizer{
  private $_borders;
	private $_start_size;
	private $_source_img;
	private $_top;
	private $_bottom;
	private $_left;
	private $_right;
	private $_new_height;
	private $_new_width;

	public function resize($source_file){
		$this->_start_size = getimagesize('source/'.$source_file);
		$this->_source_img=imagecreatefromjpeg('source/'.$source_file);
		$this->get_borders();
		$this->get_top_bottom_left_right();



		$this->_new_height=($this->_bottom-$this->_top)+20;
		$this->_new_width=($this->_right-$this->_left)+20;

		$buffer=imagecreatetruecolor($this->_new_width,$this->_new_height);
		imagecopy($buffer,$this->_source_img,0,0,$this->_left-10,$this->_top-10,$this->_new_width,$this->_new_height);
		$bgcolor=imagecolorallocate($buffer,$this->get_bg_chanel('red'),$this->get_bg_chanel('green'),$this->get_bg_chanel('blue'));

		$this->img_resize($buffer,array($this->_new_width,$this->_new_height),'target/'.$source_file,90,65,$bgcolor);

	
		imagedestroy($this->_source_img);
		imagedestroy($buffer);
	}

	private function get_borders(){
		$angles=array(
			imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,0,0)),
			imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,0,$this->_start_size[1]-1)),
			imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$this->_start_size[0]-1,0)),
			imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$this->_start_size[0]-1,$this->_start_size[1]-1))
		);
		$angl_colors=array('red'=>array(),'green'=>array(),'blue'=>array());
		foreach($angles as $angle){
			$angl_colors['red'][]=$angle['red'];
			$angl_colors['green'][]=$angle['green'];
			$angl_colors['blue'][]=$angle['blue'];
		}

		$this->_borders=array();
		foreach($angl_colors as $chanel_name => $chanel){
			$this->_borders[$chanel_name]=array('max'=>max($chanel)+10,'min'=>min($chanel)-10);
		}
	}

	private function get_top_bottom_left_right(){
		$this->_top=null;
		for($y=0;$y<$this->_start_size[1];$y++){
			for($x=0;$x<$this->_start_size[0];$x++){
				$color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
				if(!$this->background_color($color)){
					$this->_top=$y;
					break;
				}
			}
			if(!is_null($this->_top)){
				break;
			}
		}
		if($this->_top<10)$this->_top=10;
		$this->_bottom=null;
		for($y=$this->_start_size[1]-1;$y>=0;$y--){
			for($x=0;$x<$this->_start_size[0];$x++){
				$color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
				if(!$this->background_color($color)){
					$this->_bottom=$y;
					break;
				}
			}
			if(!is_null($this->_bottom)){
				break;
			}
		}
		if($this->_bottom+10>$this->_start_size[1])$this->_bottom=$this->_start_size[1]-10;
		$this->_left=null;
		for($x=0;$x<$this->_start_size[0];$x++){
			for($y=0;$y<$this->_start_size[1];$y++){
				$color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
				if(!$this->background_color($color)){
					$this->_left=$x;
					break;
				}
			}
			if(!is_null($this->_left)){
				break;
			}
		}
		if($this->_left<10)$this->_left=10;
		$this->_right=null;
		for($x=$this->_start_size[0]-1;$x>=0;$x--){
			for($y=0;$y<$this->_start_size[1];$y++){
				$color=imagecolorsforindex($this->_source_img,imagecolorat($this->_source_img,$x,$y));
				if(!$this->background_color($color)){
					$this->_right=$x;
					break;
				}
			}
			if(!is_null($this->_right)){
				break;
			}
		}
		if($this->_right+10>$this->_start_size[0])$this->_right=$this->_start_size[0]-10;

	}


	private function img_resize($isrc,$size, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
	{
		$x_ratio = $width / $size[0];
		$y_ratio = $height / $size[1];
		$ratio       = min($x_ratio, $y_ratio);
		$use_x_ratio = ($x_ratio == $ratio);

		$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
		$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
		$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
		$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

		$idest = imagecreatetruecolor($width, $height);

		imagefill($idest, 0, 0, $rgb);
		imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
			$new_width, $new_height, $size[0], $size[1]);

		imagejpeg($idest, $dest, $quality);

		imagedestroy($idest);

		return true;

	}

	private function background_color($cur_color){
		foreach($this->_borders as $chanel_name => $chanel){
			if($chanel['max']<$cur_color[$chanel_name] || $cur_color[$chanel_name]<$chanel['min']){
				return false;
			}
		}
		return true;
	}


	private function get_bg_chanel($cnannel_name){
		return ($this->_borders[$cnannel_name]['max']+$this->_borders[$cnannel_name]['min'])/2;
	}

}
