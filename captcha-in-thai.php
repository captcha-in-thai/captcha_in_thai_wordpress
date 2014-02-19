<?php
/*
Plugin Name: CAPTCHA In Thai 2nd
Plugin URI: http://www.captcha.in.th
Description: CAPTCHA in Thai language.
Version: 1.0 Beta
Author: ENGR TU
Author URI: http://www.captcha.in.th
License: GPLv2 or later
*/

/*  Copyright 2013  Nattapon and Thanate  (email : nattapon_wora@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//add admin menu
require_once('menupage.php');
require_once('check.php');
require_once('question.php');
require_once('image.php');
//require_once('offline.php');
global $wpdb;
$capth_def_setting = array(
        'font_size' => 30,
        'width' => 250,
        'height' => 100,
        'font_color' => "#000000",
        'bg_color' => "#64dcdc",
    );

add_action( 'admin_menu', 'add_menu' );

function add_menu()
{
    add_menu_page( 'CAPTCHA in Thai', 'CAPTCHA in Thai', 'manage_options', 'capthsetting', 'menu_page', plugins_url('captcha-in-thai/images/icon.png'));
    add_action( 'admin_init', 'setting' );
}

function setting()
{
    global $options;
    global $capth_def_setting;
    $option_defaults = array(
        'login_font_color' => $capth_def_setting['font_color'],
        'login_bg_color' => $capth_def_setting['bg_color'],
        'comment_font_color' => $capth_def_setting['font_color'],
        'comment_bg_color' => $capth_def_setting['bg_color'],
        'register_font_color' => $capth_def_setting['font_color'],
        'register_bg_color' => $capth_def_setting['bg_color'],
        'lostpassword_font_color' => $capth_def_setting['font_color'],
        'lostpassword_bg_color' => $capth_def_setting['bg_color'],
        
		'font_bold' => 'no',
        'font_random' => 'yes',
        'font_name' => 'TH Sarabun PSK',
        'frame_size' => '250x100',
        'random_color' => 'no',
        'line' => 'straight',
        
		
        'question_type1' => 'yes',
        'question_type2' => 'yes',
        'question_type3' => 'yes',
        'question_type4' => 'yes',
        'question_type5' => 'yes',
        'com_color' => 'no',
        'lost_color' => 'no',
        'reg_color' => 'no',
        'login_color' => 'no',
        
        'captcha_login' => 'no',
        'captcha_comments' => 'no',
        'captcha_register' => 'no',
        'captcha_lostpassword' => 'no',
        'hide_register_user' => 'yes',
        
		'distorted' => 'med',
		'noise' => 'med',
		
		'user_agreement' => 'no',
		'app_key' => 'none',
		
		'ctr' => 0,
		
		'install_db' => 'no',

    );
    
    
    if( !get_option( 'capth_options' ) )
    {
        add_option( 'capth_options', $option_defaults, '', 'yes' );
    }
	
    $options = get_option( 'capth_options' );
    $options = array_merge( $option_defaults, $options );
	
	if($options != get_option( 'capth_options' ) ){
		delete_option( 'capth_options' );
		add_option( 'capth_options', $options, '', 'yes' );
	}
	
} // end function setting

//display captcha in login form
$options = get_option( 'capth_options' );




if( 'yes' == $options['captcha_login']){
    add_action( 'login_form', 'login' );
    add_filter( 'login_errors', 'login_post' );
    add_filter( 'login_redirect', 'login_check'); 
}

if( 'yes' == $options['captcha_comments'] ){
	add_action( 'comment_form_after_fields', 'comment', 1 );
	add_action( 'comment_form_logged_in_after', 'comment', 1 );
    //add_action( 'comment_form', 'comment' );
    add_filter( 'preprocess_comment', 'comment_post' );
}

if( 'yes' == $options['captcha_register'] ){
    add_action( 'register_form', 'register' );
    add_action( 'register_post', 'register_post', 10, 3 );
}

if( 'yes' == $options['captcha_lostpassword'] ){
    add_action( 'lostpassword_form', 'lostpassword' );
    add_action( 'lostpassword_post', 'lostpassword_post', 10, 3 );
}


//function for add settings menu
function menu_link($links, $file)
{
    $this_plugin = plugin_basename(__FILE__);

    if ( $file == $this_plugin){
        $settings_link = '<a href="admin.php?page=capthsetting">Settings</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
} // end function menu_link

function login(){
    if( session_id() == "" ){
        session_start();
    }
	
    if( ! $_SESSION["login_failed"] ){
        // don't do anyting
    }else{
    	global $captcha_host;
		if( isset( $_SESSION["login"] ) ){
            unset( $_SESSION["login"]);
        }
        
        if( isset( $_SESSION['error'] ) ) {
            echo "<br /><span style='color:red'>". $_SESSION['error'] ."</span><br />";
            unset( $_SESSION['error'] );
        }
	    
	    display( "login" );

    }

    return true;
}

function register(){
	display( "register" );
    return true;
}

function lostpassword(){
	display( "lostpassword" );
    return true;
}

function comment(){
    global $options;
	global $captcha_host;

    if ( is_user_logged_in() && 'yes' == $options['hide_register_user'] ) {
        return true;
    }
    display( "comment" );
    return true;
}

function display( $part ){
    global $options;
    global $capth_def_setting;
 	$question = question();
	$url = create_image($part,$question['str_id']);
 ?>
<br />
<table width="285" height="231" border="0" cellpadding="0" cellspacing="0" bgcolor="#FF4200">
  <tr>
    <td width="16" height="10"><img src="<?= plugins_url('captcha-in-thai/images/br1.png') ?>" width="15" height="19" /></td>
    <td width="95" height="10">&nbsp;</td>
    <td width="38" height="10">&nbsp;</td>
    <td width="81" height="10">&nbsp;</td>
    <td width="40" height="10">&nbsp;</td>
    <td width="15" height="10"><img src="<?= plugins_url('captcha-in-thai/images/br2.png') ?>" width="15" height="19" /></td>
  </tr>
  <tr>
    <td height="25">&nbsp;</td>
    <td colspan="4" bgcolor="#FFFFFF"><div align="center"><?= isset($question['info'])?$question['info']:NULL ?></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="19">&nbsp;</td>
    <td colspan="4" rowspan="4" align="center" bgcolor="#FFFFFF"><img src="<?= $url ?>" width="250" height="128" /></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td height="47">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="19">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr bgcolor="#FF4200">
    <td width="16" height="31">&nbsp;</td>
    <td><div align="center"></td>
    <td height="40"></td>
    <td align="center" valign="middle"><img src="<?= plugins_url('captcha-in-thai/images/engr2.png') ?>" width="85" height="40" /></td>
    <td><img src="<?= plugins_url('captcha-in-thai/images/capt.png') ?>" width="40" height="40" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<table>
	<tr>
		<td><input  width="48" type="text" name="answer" id="answer" /></td>
		<td valign="top"><a href="<?= $_SERVER['REQUEST_URI'] ?>" ><img src="<?= plugins_url('captcha-in-thai/images/refresh.bmp') ?>" width="40" height="40" /></a></td>
	</tr>
</table>
<input type="hidden" name="code" id="code" />
<?php
    return true;
} // end function display

function login_post($errors) {
    $_SESSION["login_failed"] = true;
    // Delete errors, if they set
    if( isset( $_SESSION['error'] ) )
        unset( $_SESSION['error'] );

    // If captcha not complete, return error
    if ( isset( $_REQUEST['answer'] ) && "" ==  $_REQUEST['answer'] ) {
        return $errors.'<strong>ERROR</strong>: กรุณากรอก CAPTCHA';
    }
    	$res = check_answer($_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR'], $_REQUEST['answer'] );
    	if ( 0 == strcmp($res[0], 'true') ) {
    		unlink("captcha-in-thai.png");
        	// captcha was matched
    	} else {
        	return $errors."<strong>ERROR</strong>: คุณกรอก CAPTCHA ไม่ถูกต้อง<br />คำถาม : {$res[1]} <br /> เฉลย : {$res[2]}";
    	}
	
  return($errors);
} // end function cptch_login_post

function login_check($url) {
    $_SESSION["login_failed"] = true;
    if( session_id() == "" ){
        session_start();
    }

    if ( isset( $_REQUEST['answer'] ) ){
        	$res = check_answer($_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR'], $_REQUEST['answer'] );

        	if( 0 == strcmp($res[0], 'true') ){
           	 	$_SESSION['login'] = 'true';
            	unset( $_SESSION["login_failed"] );
				unlink("captcha-in-thai.png");
            	return $url;
        	}
	        else {
	            $_SESSION['error'] = "คุณกรอก CAPTCHA ไม่ถูกต้อง <br />คำถาม : {$res[1]} <br /> เฉลย : {$res[2]}";
	            wp_clear_auth_cookie();
	            return $_SERVER["REQUEST_URI"];
        	}
    }else{
        return $url;
    }
         
} // end function cptch_login_post

function register_post($login,$email,$errors) {

    // If captcha is blank - add error
    if ( isset( $_REQUEST['answer'] ) && "" ==  $_REQUEST['answer'] ) {
        $errors->add('captcha_blank', 'กรุณากรอก CAPTCHA');
        return $errors;
    }
    
    if ( isset( $_REQUEST['answer'] ) ){
	        $res = check_answer($_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR'], $_REQUEST['answer'] );
	        if( 0 == strcmp($res[0], 'true') ){
	            unlink("captcha-in-thai.png");
	        }
	        else {
	            $errors->add( 'captcha_wrong' , "คุณกรอก CAPTCHA ไม่ถูกต้อง<br />คำถาม : {$res[1]} <br /> เฉลย : {$res[2]}" );
	        }
		
    }
    return($errors);
} // end function cptch_register_post

function lostpassword_post() {
	global $captcha_host;

	if ( isset( $_REQUEST['answer'] ) && "" ==  $_REQUEST['answer'] ) {
        wp_die( 'กรุณากรอก CAPTCHA.' );
    }

        $res = check_answer($_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR'], $_REQUEST['answer'] );
        if( 0 == strcmp($res[0], 'true') ){
        	unlink("captcha-in-thai.png");
            return;
        }
        else {
            wp_die( "คุณกรอก CAPTCHA ไม่ถูกต้อง<br />คำถาม : {$res[1]} <br /> เฉลย : {$res[2]}");
        }
	
} // function cptch_lostpassword_post

function comment_post($comment) { 
    global $options;
	global $captcha_host;

    if ( is_user_logged_in() && 'yes' == $options['hide_register_user'] ) {
        return $comment;
    }
    
    // If captcha is empty
    if ( isset( $_REQUEST['answer'] ) && "" ==  $_REQUEST['answer'] )
        wp_die( 'กรุณากรอก CAPTCHA.' );
	    $res = check_answer($_SERVER['SERVER_NAME'], $_SERVER['REMOTE_ADDR'], $_REQUEST['answer'] );
	    if( 0 == strcmp($res[0], 'true') ){
	    	unlink("captcha-in-thai.png");
	        return $comment;
	    }
	    else {
	        wp_die( "คุณกรอก CAPTCHA ไม่ถูกต้อง<br />คำถาม : {$res[1]} <br /> เฉลย : {$res[2]}" );
	    }
} // end function cptch_comment_post


function tran_num( $old ){
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
	
	$new = "";
	if ( $old >= 100 ){
		$new .= $num[ $old / 100 ];
		$old %= 100;
	}
	
	if ( $old >= 10 ){
		$new .= $num[ $old / 10 ];
		$old %= 10;
	}
	
	$new .= $num[ $old ];
	return $new;
	
}

//สร้าง link ไปหน้า setting
add_filter( 'plugin_action_links', 'menu_link', 10, 2 );
?>