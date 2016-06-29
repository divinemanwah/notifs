<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monsterid {

	private $seed;
	private $size;

	public function __construct($params) {
		
		$this->seed=$params['seed'];
		$this->size=$params['size'];
	}

	public function build_monster(){
	
		// init random seed
		if($this->seed) srand( hexdec(substr(md5($this->seed),0,6)) );

		// throw the dice for body parts
		$parts = array(
			'legs' => rand(1,5),
			'hair' => rand(1,5),
			'arms' => rand(1,5),
			'body' => rand(1,15),
			'eyes' => rand(1,15),
			'mouth'=> rand(1,10)
		);

		// create backgound
		$monster = @imagecreatetruecolor(120, 120)
			or die("GD image create failed");
		$white   = imagecolorallocate($monster, 255, 255, 255);
		imagefill($monster,0,0,$white);

		// add parts
		foreach($parts as $part => $num){
			$file = dirname(__FILE__).'/parts/'.$part.'_'.$num.'.png';

			$im = @imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($monster,$im,0,0,0,0,120,120);
			imagedestroy($im);

			// color the body
			if($part == 'body'){
				$color = imagecolorallocate($monster, rand(20,235), rand(20,235), rand(20,235));
				imagefill($monster,60,60,$color);
			}
		}

		// restore random seed
		if($this->seed) srand();

		// resize if needed, then output
		if($this->size && $this->size < 400){
			$out = @imagecreatetruecolor($this->size,$this->size)
				or die("GD image create failed");
			imagecopyresampled($out,$monster,0,0,0,0,$this->size,$this->size,120,120);
			// header ("Content-type: image/png");
			ob_start();
			imagepng($out);
			imagedestroy($out);
			imagedestroy($monster);
			return 'data:image/png;base64,' . base64_encode(ob_get_clean());
		}else{
			// header ("Content-type: image/png");
			ob_start();
			imagepng($monster);
			imagedestroy($monster);
			return 'data:image/png;base64,' . base64_encode(ob_get_clean());
		}
	}
}