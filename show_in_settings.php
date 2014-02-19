<?php
session_start();

putenv('GDFONTPATH=' . realpath('.'));

$font = "font/catthai.ttf";
$font_size = $_GET['font_size'];
$width = 250;
$height = 100;

$font_color = $_GET['font_color'];
$bg_color = $_GET['bg_color'];

if(isset( $_SESSION['capth_str'] )){
	$str = $_SESSION['capth_str'];
	$seed = $_SESSION['seed'];
}else{
	$str = "ทดสอบ";
	list($usec, $sec) = explode(' ', microtime());
  	$seed = (float) $sec + ((float) $usec * 100000);
}
srand();
session_destroy();
$xfont = rand(10, 100);
$yfont = rand(40, 80);
$image = imagecreate($width, $height); 
$bg = imagecolorallocate($image, hexdec (substr($bg_color, 0 , 2)) ,hexdec (substr($bg_color, 2 , 2)),hexdec (substr($bg_color, 4 , 2)) ); 
$font_line_color = imagecolorallocate($image, hexdec (substr($font_color, 0 , 2)) ,hexdec (substr($font_color, 2 , 2)),hexdec (substr($font_color, 4 , 2)) );

$angle = rand(-10, 10);

$val['noise']['high'] = array( 'dot' => $width*$height/10 , 'line' => 20 , 'bgline' => 10);
$val['noise']['med'] = array( 'dot' => $width*$height/20 , 'line' => 10 , 'bgline' => 5);
$val['noise']['low'] = array( 'dot' => $width*$height/40 , 'line' => 5 , 'bgline' => 5);

$val['distorted']['high'] = array( 'period' => rand(8,10) , 'amp' => rand(7, 9) );
$val['distorted']['med'] = array( 'period' => rand(5,7) , 'amp' => rand(5, 6) );
$val['distorted']['low'] = array( 'period' => rand(2,4) , 'amp' => rand(2, 4) );

imagettftext($image,$font_size,$angle,$xfont,$yfont,$font_line_color,$font,$str);

if( $_GET['distorted'] != 'no' ){
	$width2 = $width*2;
	$height2 = $height*2;
	$image2 = imagecreatetruecolor($width2,$height2);
	
	imagecopyresampled($image2, $image, 0,0,0,0,$width2,$height2,$width,$height);
	
	//ทำให้ข้อความเป็นคลื่น
	$period = $val['distorted'][$_GET['distorted']]['period'];
	$amp = $val['distorted'][$_GET['distorted']]['amp'];
    
	$phase = rand(0,(int)(2*pi()*100))/100.0;
	for ($i=0; $i < $width2 ; $i+= 2) {
		imagecopy($image2,$image2,$i-2,sin($i/$period+$phase) * $amp, $i, 0, 2, $height2);
	}
	
	imagecopyresampled($image, $image2, 0,0,0,0,$width,$height,$width2,$height2);
	imagedestroy($image2); 	
}

if( $_GET['noise'] != 'no' ){
	//สร้างจุด
	for ($i=0; $i < $val['noise'][$_GET['noise']]['dot'] ; $i++)
	{
	    $cx = rand(0, $width);
	    $cy = rand(0, $height);
		imageellipse($image, $cx, $cy, 1, 1, $font_line_color);
	}
	
	//สร้างเส้นเดียวกับตัวอักษร ทับบนตัวอักษร
	for ($i=0; $i <= $val['noise'][$_GET['noise']]['line']/2 ; $i++) 
	{
		$x_start = ($xfont-20)>=0?$xfont-20:0;
		$y_start = ($yfont-30)>=0?$yfont-30:0;
		$y_end = $yfont+20;
	    $x1 = rand($x_start, $width-10);
	    $x2 = rand($x_start, $width-10);
	    $y1 = rand($y_start, $y_end);
	    $y2 = rand($y_start-10, $y_end+10);
	    imageline($image, $x1, $y1, $x2, $y2, $font_line_color);
	}
	
	//สร้างเส้นสีเดียวกับตัวอักษร กระจายบนภาพ
	for ($i=0; $i <= $val['noise'][$_GET['noise']]['line']/2 ; $i++) 
	{
	    $x1 = rand(0, $width);
	    $x2 = rand(0, $width);
	    $y1 = rand(0, $height);
	    $y2 = rand(0, $height);
	    imageline($image, $x1, $y1, $x2, $y2, $font_line_color);
	}
	
	//สร้างเส้นสีเดียวกับพื้นหลัง เพื่อทำให้ตัวอักษรขาดออกจากกัน
	for ($i=0; $i < $val['noise'][$_GET['noise']]['bgline'] ; $i++) 
	{
	    $x_start = $xfont;
		$y_start = ($yfont-30)>=0?$yfont-30:0;
		$y_end = $yfont;
	    $x1 = rand($x_start, $width-10);
	    $x2 = rand($x_start, $width-10);
	    $y1 = rand($y_start, $y_end);
	    $y2 = rand($y_start-10, $y_end+10);
	    imageline($image, $x1, $y1, $x2, $y2, $bg);
	}	
}

header("Content-type:image/png"); 
imagepng($image); 
imagedestroy($image); 

?>