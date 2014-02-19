<?php
session_start();

/*
 * 
$link = mysqli_connect("localhost", "root", "", "captcha");
$sql="CREATE TABLE Persons(FirstName CHAR(30),LastName CHAR(30),Age INT)";
$result = mysqli_query( $link, $sql);
 * 
 */
function math( $part ){
	global $wpdb;
	global $options;
    global $capth_def_setting;
	
	$num = array(); 
	$num[0] = "๐";
	$num[1] = "๑";
	$num[2] = "๒";
	$num[3] = "๓";
	$num[4] = "๔";
	$num[5] = "๕";
	$num[6] = "๖";
	$num[7] = "๗";
	$num[8] = "๘";
	$num[9] = "๙";
	
	$key = $options['app_key'];
	$ctr = $options['ctr']++;
	update_option( 'capth_options', $options, '', 'yes' );
	
	srand(make_seed($ctr.$key.($ctr+1)));
	$_SESSION["seed"] = make_seed(($ctr+1).$key.$ctr);
	
	$mode = rand(1, 3);
	$first_num;
	$second_num;
	$ans_num;
	$op;
	if( $mode == 1 ){
		$first_num = rand( 1, 99);
		$second_num = rand( 1, 99);
		$ans_num = $first_num + $second_num;
		$op = "บวก";
	}else if( $mode == 2 ){
		$first_num = rand( 1, 99);
		$second_num = rand( 1, 99);
		if( $first_num < $second_num ){
			$temp_num = $first_num;
			$first_num = $second_num;
			$second_num = $temp_num;	
		}
		$ans_num = $first_num - $second_num;
		$op = "ลบ";
	}else if( $mode == 3 ){
		$first_num = rand( 1, 9);
		$second_num = rand( 1, 9);
		$ans_num = $first_num * $second_num;
		$op = "คูณ";
	}
	
	$str = "";
	if ( $first_num >= 10 ){
		$str .= $num[ $first_num / 10 ];
	}
	$str .= $num[ $first_num % 10 ];
	
	$str .= " $op ";
	
	if ( $second_num >= 10 ){
		$str .= $num[ $second_num / 10 ];
	}
	$str .= $num[ $second_num % 10 ];
	$_SESSION["capth_str"] = $str;
	
	$randnum = rand(0, 1000);
	
    $url = plugins_url('captcha-in-thai/image.php');
    $url .= "?font_size={$capth_def_setting['font_size']}";
    $url .= "&width={$capth_def_setting['width']}";
    $url .= "&height={$capth_def_setting['height']}";
    
    if( "login" == $part ){
        $url .= "&font_color=".substr($options['login_font_color'], 1);
        $url .= "&bg_color=".substr($options['login_bg_color'], 1);
    }else if( "register" == $part ){
        $url .= "&font_color=".substr($options['register_font_color'], 1);
        $url .= "&bg_color=".substr($options['register_bg_color'], 1);
    }else if( "comment" == $part ){
        $url .= "&font_color=".substr($options['comment_font_color'], 1);
        $url .= "&bg_color=".substr($options['comment_bg_color'], 1);
    }else if( "lostpassword" == $part ){
		$url .= "&font_color=".substr($options['lostpassword_font_color'], 1);
        $url .= "&bg_color=".substr($options['lostpassword_bg_color'], 1);
	}
	
	
	$url .= "&noise={$options['noise']}";
	$url .= "&distorted={$options['distorted']}";

	echo "<br />";
	
	$info = "หาค่าต่อไปนี้<br />&nbsp;เช่น ๑ บวก ๑ ให้ตอบ ๒ หรือตอบ 2";
}

function make_seed($key)
{
  list($usec, $sec) = explode(' ', microtime());
  $m = ((float) $sec + ((float) $usec * 100000)).$key;
  return hexdec(substr(sha1($m), 3,13));
}

?>
