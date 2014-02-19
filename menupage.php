<?php
function menu_page()
{
	session_start();
	global $options;
	global $wpdb;
	if(isset($_POST["agree"]) ){
		$options["user_agreement"] = "yes";
		list($usec, $sec) = explode(' ', microtime());
		$_SESSION["time"] = (float)$sec + (float)$usec*100000 - (float)$_SESSION["time"];
		$options['app_key'] = keygen($_SESSION["time"]);
		update_option( 'capth_options', $options, '', 'yes' );
		
	// check ว่า  created database หรือยัง	
		
	if( 'no' == $options['install_db'] ){

		function remove_comments(&$output)
		{
  		 	$lines = explode("\n", $output);
   			$output = "";

   			// try to keep mem. use down
  			 $linecount = count($lines);

   			$in_comment = false;
   			for($i = 0; $i < $linecount; $i++)
  			 {
      			if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
      			{
        			 $in_comment = true;
     			 }

     			 if( !$in_comment )
     			 {
       			  $output .= $lines[$i] . "\n";
     			 }

    			 if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
    			 {
      			   $in_comment = false;
     			 }
   			}

   			unset($lines);
   			return $output;
			}

			//
			// remove_remarks will strip the sql comment lines out of an uploaded sql file
			//
			function remove_remarks($sql)
			{
   			$lines = explode("\n", $sql);

   			// try to keep mem. use down
   			$sql = "";

   			$linecount = count($lines);
   			$output = "";

   			for ($i = 0; $i < $linecount; $i++)
  			 {
   			   if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
   			   {
    			     if (isset($lines[$i][0]) && $lines[$i][0] != "#")
    			     {
      			      $output .= $lines[$i] . "\n";
     			    }
     			    else
      			    {
     			       $output .= "\n";
     			    }
        			 // Trading a bit of speed for lower mem. use here.
        			 $lines[$i] = "";
     			 }
   			}

   			return $output;

			}
			
			//
			// split_sql_file will split an uploaded sql file into single sql statements.
			// Note: expects trim() to have already been run on $sql.
			//
			function split_sql_file($sql, $delimiter)
			{
   			// Split up our string into "possible" SQL statements.
  			 $tokens = explode($delimiter, $sql);

   			// try to save mem.
   			$sql = "";
   			$output = array();

  			 // we don't actually care about the matches preg gives us.
   			$matches = array();

   			// this is faster than calling count($oktens) every time thru the loop.
   			$token_count = count($tokens);
  			 for ($i = 0; $i < $token_count; $i++)
   			{
     			 // Don't wanna add an empty string as the last thing in the array.
     			 if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
     			 {
      			   // This is the total number of single quotes in the token.
      			   $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
      			   // Counts single quotes that are preceded by an odd number of backslashes,
      			   // which means they're escaped quotes.
      			   $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

       			   $unescaped_quotes = $total_quotes - $escaped_quotes;

       			  // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
       			  if (($unescaped_quotes % 2) == 0)
       			  {
         			   // It's a complete sql statement.
         			   $output[] = $tokens[$i];
           			 // save memory.
           			 $tokens[$i] = "";
        			 }
        			 else
        			 {
          			  // incomplete sql statement. keep adding tokens until we have a complete one.
         			   // $temp will hold what we have so far.
          			  $temp = $tokens[$i] . $delimiter;
          			  // save memory..
          			  $tokens[$i] = "";

          			  // Do we have a complete statement yet?
           			 $complete_stmt = false;

          			  for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
          			  {
          			     // This is the total number of single quotes in the token.
          			     $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
          			     // Counts single quotes that are preceded by an odd number of backslashes,
           			    // which means they're escaped quotes.
         			      $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

         			      $unescaped_quotes = $total_quotes - $escaped_quotes;

          					     if (($unescaped_quotes % 2) == 1)
          					     {
          			        // odd number of unescaped quotes. In combination with the previous incomplete
           			       // statement(s), we now have a complete statement. (2 odds always make an even)
          					        $output[] = $temp . $tokens[$j];

              			    // save memory.
          					        $tokens[$j] = "";
         					        $temp = "";

               			   // exit the loop.
         					         $complete_stmt = true;
               			   // make sure the outer loop continues at the right point.
       		        			   $i = $j;
        		    			  }
        		     			  else
        		     			  {
                 			 // even number of unescaped quotes. We still don't have a complete statement.
                			  // (1 odd and 1 even always make an odd)
           		  			     $temp .= $tokens[$j] . $delimiter;
                			  // save memory.
          		 			       $tokens[$j] = "";
             					  }

          					  } // for..
        					 } // else
     					 }
   					}

   					return $output;
				}

		$dbms_schema = 'http://www.captcha.in.th/database/captcha-in-thai-host.sql';
		$data = file_get_contents($dbms_schema);
		$sql_query = $data;
		$sql_query = remove_remarks($sql_query);
		$sql_query = split_sql_file($sql_query, ';');


		foreach($sql_query as $sql){
		$wpdb->query($sql);
		}
		$options['install_db'] = 'yes';
		update_option( 'capth_options', $options, '', 'yes' );
		//update_option( 'install_db', 'yes' );
	}
		
		
		//////////////////////////////////////////////////////

	}
	if( 'no' == $options["user_agreement"]){
		list($usec, $sec) = explode(' ', microtime());
		$_SESSION["time"] = (float)$sec + (float)$usec*100000;
		menu_page_user_agreement();
	}else{
		menu_page_setting();
	}
}

function keygen($seed){
	$seed = $seed . $_SERVER['SERVER_NAME'] . $_SERVER['REMOTE_ADDR'];
	$seed = hexdec(substr(sha1($seed), 5,15));
	srand($seed);
	$key = "";
	$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));
	for($i = 0 ; $i<16; $i++){
		$key .= $inputs[rand(0,61)];
	}
	return $key;
}

function menu_page_user_agreement(){
	?>
	<center>
		<br />
		<h2>นโยบายความเป็นส่วนตัว</h2><br />
		<div style="width: 800px; height: 400px; overflow-y: scroll; scrollbar-arrow-color:blue; scrollbar-face-color: #e7e7e7; scrollbar-3dlight-color: #a0a0a0; scrollbar-darkshadow-color:#888888">  
  
			<p align="justify">  
				<b>บทนำ</b><br />
				อัพเดทล่าสุดเมื่อ วันที่ 21 มกราคม พ.ศ.255ึ<br />
				&nbsp;&nbsp;&nbsp;&nbsp;CAPTCHA in Thai 2nd (“แคปต์ชาในภาษาไทย” หรือ “เรา”) คือ ผู้เป็นเจ้าของปลั๊กอิน CAPTCHA in Thai 2nd (“ปลั๊กอิน”) และดำเนินการเว็บไซต์ captcha.in.th โดยมีวัตถุประสงค์ใช้เป็นโครงงานจบการศึกษาของนักศึกษาชั้นปีที่ 4 ของคณะวิศวกรรมศาสตร์ ภาควิชาไฟฟ้าและคอมพิวเตอร์ มหาวิทยาลัยธรรมศาสตร์ และเก็บรวบรวมข้อมูลสถิติของผู้ที่ใช้งานปลั๊กอินตัวนี้ นโยบายความเป็นส่วนตัวต่อไปนี้ จะอธิบายถึงวิธีที่เราในฐานะผู้บริหารจัดการข้อมูล รวบรวม ใช้ เปิดเผย และดำเนินการเกี่ยวกับข้อมูลที่สามารถระบุตัวตนของท่านได้ (“ข้อมูลส่วนบุคคล”) ซึ่งมีความเกี่ยวข้องกับการให้บริการของเรา ผ่านทางเว็บไซต์ www.captcha.in.th (“เว็บไซต์”) การอ้างอิงถึงเว็บไซต์ดังกล่าว ในนโยบายความเป็นส่วนตัวในฉบับนี้ หมายรวมถึงผลิตภัณฑ์ที่พัฒนามาจากเว็บไซต์อีกด้วย อาทิ ปลั๊กอินและแอพพลิเคชัน เมื่อท่านเยี่ยมชมหรือใช้งานปลั๊กอินและเว็บไซต์ของเรา ท่านได้ยินยอมให้เรารวบรวม ใช้ เปิดเผย และดำเนินการเกี่ยวกับข้อมูลส่วนบุคคลของท่านตามที่ได้ระบุไว้ในนโยบายความเป็นส่วนตัวนี้ ข้อมูลทั้งหมดที่เรารวบรวมและดำเนินการจะถูกเก็บไว้ในเซิร์ฟเวอร์ของคณะวิศวกรรมศาสตร์ มหาวิทยาลัยธรรมศาสตร์<br />
				&nbsp;&nbsp;&nbsp;&nbsp;แคปต์ชาในภาษาไทยจะทำการปรับปรุงนโยบายความเป็นส่วนตัวนี้ เมื่อถึงเวลาที่สมควร เพื่อสะท้อนให้เห็นถึงกฎหมายที่เปลี่ยนแปลงไป ขั้นตอนการรวบรวมข้อมูลส่วนบุคคลและแนวทางการนำข้อมูลดังกล่าวไปใช้ จุดเด่นของปลั๊กอินและเว็บไซต์ หรือความก้าวหน้าทางเทคโนโลยี หากเราเปลี่ยนแปลงวิธีการรวบรวมหรือวิธีการใช้ข้อมูลส่วนบุคคล เราจะแจ้งการเปลี่ยนแปลงให้ท่านได้ทราบในนโยบายความเป็นส่วนตัวนี้ โดยจะระบุวันที่เริ่มมีผลบังคับใช้ไว้ที่ด้านบนของนโยบายความเป็นส่วนตัว ดังนั้น ท่านจึงควรตรวจดูนโยบายความเป็นส่วนตัวนี้เป็นประจำ เพื่อให้ทรายถึงนโยบายและแนวทางการปฏิบัติล่าสุดของเรา นอกจากนี้ แคปต์ชาในภาษาไทยยังจะแจ้งการเปลี่ยนแปลงดังกล่าวให้ท่านทราบก่อนที่จะเริ่มนำมาใช้ โปรดหยุดใช้ปลั๊กอินและเว็บไซต์ของเรา หากท่านไม่เห็นด้วยกับการเปลี่ยนแปลงหรือปรับปรุงใดๆ ในนโยบายความเป็นส่วนตัว การเข้าใช้งานปลั๊กอินและเว็บไซต์นี้หลังวันที่ที่การปรับปรุงนโยบายความเป็นส่วนตัวมีผลบังคับใช้นั้นถือว่าท่านได้ยอมรับการปรับปรุงดังกล่าวแล้ว<br />
				<br />
				<b>เรารวบรวมข้อมูลอะไรบ้าง</b><br />
       			&nbsp;&nbsp;&nbsp;&nbsp;แคปต์ชาในภาษาไทยรวบรวมข้อมูลส่วนบุคคลของท่าน ซึ่งท่านมอบให้เราในระหว่างที่ใช้บริการปลั๊กอินและเว็บไซต์ ข้อมูลส่วนตัวดังกล่าวรวมถึง ชื่อโดนเมนที่ใช้ติดตั้งการใช้บริการปลั๊กอิน ข้อมูลในการตอบแคปต์ชา วัน เดือน ปี และเวลาที่ตอบแคปต์ชาของท่าน นอกจากนั้น เรายังรวบรวมข้อมูลที่ไม่ระบุตัวตนของท่าน ซึ่งอาจเชื่อมโยงไปสู่ข้อมูลส่วนตัวของท่านได้โดยข้อมูลดังกล่าว ได้แก่ การตั้งค่าค้นหาที่เกี่ยวข้องกับการค้นหาสิ่งใดสิ่งหนึ่งโดยเฉพาะ<br />
				<br />
				<b>เราปกป้องข้อมูลส่วนตัวของท่านอย่างไร</b><br />
				&nbsp;&nbsp;&nbsp;&nbsp;เพื่อป้องกันการเข้าถึงข้อมูลส่วนตัวของท่านโดยไม่ได้รับอนุญาต เราได้นำกระบวนการทางด้านการปฎิบัติ ทางด้านอิเล็คโทรนิกส์ มาใช้อย่างเหมาะสมเพื่อปกป้องข้อมูลส่วนตัวจากการทำลายที่เกิดขึ้นโดยอุบัติเหตุหรือผิดกฎหมาย การสูญหายโดยอุบัติเหตุ การดัดแปลง และการเปิดเผยหรือการเข้าถึงข้อมูลโดยไม่ได้รับอนุญาต<br />
				<br />
				<b>เราใช้ข้อมูลที่รวบรวมมาอย่างไร</b><br />
       			&nbsp;&nbsp;&nbsp;&nbsp;เรานำข้อมูลส่วนบุคคลและข้อมูลอื่นๆ ที่รวมรวบผ่านทางปลั๊กอินและเว็บไซต์ เพื่อรวบรวมข้อมูลสถิติของผู้ใช้งาน และแสดงให้ผู้ใช้งานได้ทราบถึง คำถามแคปต์ชา คำตอบแคปต์ชา ในแต่ละช่วงเวลา และจำนวนของผู้ที่ตอบแคปต์ชา อีกทั้งเพื่อสร้างสิ่งใหม่ๆ ซึ่งจะทำให้ท่านใช้บริการของปลั๊กอินได้ง่ายยิ่งขึ้น<br />
				<br />
				<b>เราแบ่งปันข้อมูลส่วนบุคคลกับใคร</b><br />
        		&nbsp;&nbsp;&nbsp;&nbsp;แคปต์ชาในภาษาไทยอาจแบ่งปันข้อมูลส่วนตัวของท่านกับบุคคลที่สาม เพื่อให้บริการในฐานะตัวแทนของเรา เช่น เว็บโฮสติ้งและวิเคราะห์ข้อมูล เพื่อทำการวิเคราะห์ และบริการของเราซึ่งรวมถึงบนเว็บไซต์ของบุคคลที่สามเหล่านั้นด้วย บุคคลที่สามดังกล่าวจะอยู่ภายใต้ข้อบังคับในการรักษาความปลอดภัยและความลับของข้อมูลส่วนบุคคล และจะใช้ข้อมูลส่วนบุคคลตามคำสั่งของเราเท่านั้น<br />
				&nbsp;&nbsp;&nbsp;&nbsp;เราจะไม่เปิดเผยข้อมูลส่วนบุคคลของท่านต่อบุคคลที่สามโดยไม่ได้รับการยินยอมจากท่าน เว้นเสียแต่จะเป็นการเปิดเผยข้อมูลดังที่มีระบุในนโยบายความเป็นส่วนตัวนี้ หรือการเปิดเผยข้อมูลตามกฎหมายของเขตอำนาจศาลใดๆ ที่เกี่ยวข้องดังกล่าวจะกล่าวถึงด้านล่าง<br />
        		&nbsp;&nbsp;&nbsp;&nbsp;แคปต์ชาในภาษาไทยอาจเปิดเผยข้อมูลส่วนบุคคลโดยชอบด้วยกฎหมายที่เกี่ยวข้องเพื่อเป็นการป้องกันตัวเองจากความรับผิดชอบ เพื่อปฏิบัติตามกฎหมายศาล ตามกระบวนการยุติธรรม ตามคำร้องขอจากเจ้าหน้าที่ผู้รักษากฎหมาย หรือตามความจำเป็นอื่นๆ เพื่อให้สอดคล้องกับกฎหมายที่บังคับใช้ นอกจากนี้ แคปต์ชาในภาษาไทยยังอาจเปิดเผยข้อมูลส่วนบุคคลโดยชอบด้วยกฎหมายที่เกี่ยวข้องเพื่อเป็นการบังคับใช้ข้อกำหนดและเงื่อนไขที่เกี่ยวข้องกับบริการของเรา หรือเพื่อปกป้องสิทธิ ทรัพย์สิน หรือความปลอดภัยของแคปต์ชาในภาษาไทย ผู้ใช้ และผู้อื่น<br />
				<br />
				<b>การติดต่อเรา</b>
        		&nbsp;&nbsp;&nbsp;&nbsp;หากท่านมีคำถามที่เกี่ยวข้องกับนโยบายความเป้นส่วนตัวของเรา ท่านสามารถติดติอผ่านหน้า “ติดต่อเรา” ได้ทางเว็บไซต์ www.captcha.in.th<br />
			</p>  
		  
		</div>
		<br />
		<form method="post" action="admin.php?page=capthsetting">
			<input type="submit" name="agree" value="ยอมรับเงื่อนไขการใช้งาน" />
		</form>
	</center>
	<?php
}


function menu_page_setting(){
    global $options;
    global $capth_def_setting;
    if( isset( $_REQUEST['cptch_form_submit'] ))
    {
        if( 'yes' != $_REQUEST['question_type1'] && 'yes' != $_REQUEST['question_type2'] && 'yes' != $_REQUEST['question_type3']
         && 'yes' != $_REQUEST['question_type4'] && 'yes' != $_REQUEST['question_type5'] ){
            $err = "การบันทึกล้มเหลว<br />กรุณาเลือกชนิดคำถามอย่างน้อย 1 ชนิด และทำการบันทึกใหม่อีกครั้ง";
        }else{
            foreach ($options as $key => $value) {
                $options[$key] = $_REQUEST[$key];
            }
            //$options = array_merge( $options, $request_options );
            update_option( 'capth_options', $options, '', 'yes' );
        }
    }
?>

<div style="font-size: 26px">แคปต์ชาในภาษาไทย</div>
<br />
<form method="post" action="admin.php?page=capthsetting">
  <label></label>
  <table width="930" border="0">
  	<tr>
  		<td colspan="4" align="center"><span style="color: red"><?php if( isset($err) ) echo $err ?></span></td>
  	</tr>
  	<tr>
  		 <td colspan="4" align="center"><p align="center"><span class="style27">&#3649;&#3588;&#3611;&#3605;&#3660;&#3594;&#3634;&#3651;&#3609;&#3616;&#3634;&#3625;&#3634;&#3652;&#3607;&#3618;</span></p></td>
  	</tr>

  	<tr>
            <td colspan="4" align="center"><input type="submit" name="name="cptch_form_submit" value="Save" /></td>
    </tr>
    <tr>
      <td width="170"> </td>
      <td width="310"> </td>
      <td width="160">&nbsp;</td>
      <td width="290"> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td><span class="style29">&#3611;&#3619;&#3632;&#3648;&#3616;&#3607;&#3588;&#3635;&#3606;&#3634;&#3617;</span></td>
      <td><span class="style89">
      <label><input type="checkbox" name="question_type1" value="yes" <?= ('yes' == $options['question_type1']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;คำที่มักเขียนผิด &nbsp;&nbsp;</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label><input type="checkbox" name="question_type2" value="yes" <?= ('yes' == $options['question_type2']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;คำอ่าน &nbsp;&nbsp;</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label><input type="checkbox" name="question_type3" value="yes" <?= ('yes' == $options['question_type3']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;สุภาษิต &nbsp;&nbsp;</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>
        <input type="checkbox" name="question_type4" value="yes" <?= ('yes' == $options['question_type4']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;คำราชาศัพท์ &nbsp;&nbsp;</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label><input type="checkbox" name="question_type5" value="yes" <?= ('yes' == $options['question_type5']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;การคำนวณทางคณิตศาสตร์&nbsp;&nbsp;</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
<!--
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>
        <input type="checkbox" name="checkbox5" value="checkbox" />
การคำนวณทางคณิตศาสตร์</label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
-->
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td><span class="style87">&#3619;&#3641;&#3611;&#3649;&#3610;&#3610;&#3605;&#3633;&#3623;&#3629;&#3633;&#3585;&#3625;&#3619;</span></td>
      <td><span class="style89">
        <label><input type="radio" name="font_random" value="yes" <?= ('yes' == $options['font_random']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิดการใช้งานแบบสุ่ม &nbsp;&nbsp;<br /></label>
      </span></td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>
        <input type="radio" name="font_random" value="no" <?= ('no' == $options['font_random']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิดการใช้งานแบบกำหนดเอง &nbsp;&nbsp;</label>
      </span></td>
      <td>
        <label>
        <select name="font_name" size="0.5">
          <option value="<?php global $options; echo$options['font_name']?>" selected="selected"><?php
					global $options;
					echo $options['font_name'];
					?></option>
        			<option value="TH Bai Jamjuree CP">TH Bai Jamjuree CP</option>
        			<option value="TH Chakra Petch">TH Chakra Petch</option>
        			<option value="TH Charm of AU">TH Charm of AU</option>
        			<option value="TH Chamornman">TH Charmonman</option>
        			<option value="TH Fah Kwang">TH Fah Kwang</option>
        			<option value="TH K2D July8">TH K2D July8</option>
        			<option value="TH KoHo">TH KoHo</option>
        			<option value="TH Krub">TH Krub</option>
        			<option value="TH Mali Grade 6">TH Mali Grade 6</option>
        			<option value="TH Kodchasan">TH Kodchasan</option>
        			<option value="TH Niramit AS">TH Niramit AS</option>
        			<option value="TH Srisakdi">TH Srisakdi</option>
        			<option value="TH Sarabun PSK">TH Sarabun PSK</option>
        </select>
        </label>
      </td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>ความหนาของตัวอักษร</label>
      </span></td>
      <td>
        <label>
            	<input type="radio" name="font_bold" value="yes" <?= ( 'yes' == $options['font_bold'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิด &nbsp;&nbsp;
            	<input type="radio" name="font_bold" value="no" <?= ( 'no' == $options['font_bold'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด &nbsp;&nbsp;
        </label>
      </td>
      <td> </td>
    </tr>
    <tr>
    <tr>
      <td><span class="style87">&#3585;&#3634;&#3619;&#3649;&#3626;&#3604;&#3591;&#3612;&#3621;</span></td>
      <td><span class="style89">
        <label> การบิดเบี้ยวของตัวอักษร</label>
      </span></td>
        <label>
        <td colspan="2"><input type="radio" onclick="show_all()" name="distorted" value="high" <?= ( 'high' == $options['distorted'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;มาก  &nbsp;&nbsp;
                            <input type="radio" onclick="show_all()" name="distorted" value="med" <?= ( 'med' == $options['distorted'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;กลาง  &nbsp;&nbsp;
                            <input type="radio" onclick="show_all()" name="distorted" value="low" <?= ( 'low' == $options['distorted'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;น้อย  &nbsp;&nbsp;
                            <input type="radio" onclick="show_all()" name="distorted" value="no" <?= ( 'no' == $options['distorted'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
        </label>
      <td> </td>
    </tr>
      <td> </td>
      <td><span class="style89">
        <label>เส้นและจุด</label>
      </span></td>
      <td colspan="2"><input type="radio" onclick="show_all()" name="noise" value="high" <?= ( 'high' == $options['noise'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;มาก  &nbsp;&nbsp;
                            <input type="radio" onclick="show_all()" name="noise" value="med" <?= ( 'med' == $options['noise'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;กลาง  &nbsp;&nbsp;                          
                            <input type="radio" onclick="show_all()" name="noise" value="low" <?= ( 'low' == $options['noise'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;น้อย  &nbsp;&nbsp;                          
                            <input type="radio" onclick="show_all()" name="noise" value="no" <?= ( 'no' == $options['noise'] ) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
      <tr>
      <td></td>
      <td></td>
      <td>
        <label>ลักษณะของเส้น
        <select name="line" size="0.5" onclick="show_all()">
        	<option selected="selected" value="<?php global $options; echo$options['line']?>">
          	<?php	global $options;
          			if('straight' == $options['line']){echo "เส้นตรง";} ;
					if('curve' == $options['line']){echo "เส้นโค้ง";} ;
					if('wavy' == $options['line']){echo "เส้นหยัก";} ;
			?></option>
          <option onclick="show_all()" value="straight">เส้นตรง</option>
          <option onclick="show_all()" value="curve">เส้นโค้ง</option>
          <option onclick="show_all()" value="wavy">เส้นหยัก</option>
        </select>
        </label>
      </td>
      </tr>
    </tr>
    
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td><span class="style87">&#3586;&#3609;&#3634;&#3604;&#3586;&#3629;&#3591;&#3585;&#3619;&#3629;&#3610;</span></td>
      <td><span class="style89">&#3648;&#3621;&#3639;&#3629;&#3585;&#3586;&#3609;&#3634;&#3604;&#3586;&#3629;&#3591;&#3585;&#3619;&#3629;&#3610;</span></td>
      <td>
        <label>
        <select name="frame_size">
            		<option value="<?php global $options; echo$options['frame_size']?>" select="selected"><?php global $options; echo$options['frame_size']?></option>
            		<option value="300x120">300x120</option>
            		<option value="250x100">250x100</option>
            		<option value="200x80">200x80</option>
            	</select>
        </label>
      </td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td><span class="style87">&#3585;&#3634;&#3619;&#3648;&#3611;&#3636;&#3604;&#3651;&#3594;&#3657;&#3591;&#3634;&#3609;</span></td>
      <td><span class="style89">
        <label>หน้าเข้าสู่ระบบ</label></span></td>
      <td><input type="radio" name="captcha_login" value="yes" <?= ('yes' == $options['captcha_login']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิด &nbsp;&nbsp;<input type="radio" name="captcha_login" value="no" <?= ('no' == $options['captcha_login']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
      <td rowspan="3"><b>ตัวอย่าง</b><div id="login_show"></div></td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">
        <div align="right">สีตัวอักษร</div>
      </div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="login_font_color" id="login_font_color" onchange="showimage('login_show')" value=<?=$options['login_font_color'] ?>></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีพื้นหลัง</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="login_bg_color" id="login_bg_color" onchange="showimage('login_show')" value=<?=$options['login_bg_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td><span class="style87">
        <label>
        <input type="checkbox" name="login_color" value="yes" <?= ('yes' == $options['login_color']) ? "checked='checked'" : "'no' = {$options['login_color']}" ?> />
&#3626;&#3640;&#3656;&#3617;&#3626;&#3637;&#3605;&#3633;&#3623;&#3629;&#3633;&#3585;&#3625;&#3619;&#3649;&#3621;&#3632;&#3626;&#3637;&#3614;&#3639;&#3657;&#3609;&#3627;&#3621;&#3633;&#3591;</label>
      </span></td>
    </tr>
        <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>หน้าสมัครสมาชิก</label>
      </span></td>
      <td><input type="radio" name="captcha_register" value="yes" <?= ('yes' == $options['captcha_register']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิด &nbsp;&nbsp;<input type="radio" name="captcha_register" value="no" <?= ('no' == $options['captcha_register']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
      <td rowspan="3"><b>ตัวอย่าง</b><div id="register_show"></div></td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีตัวอักษร</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="register_font_color" id="register_font_color" onchange="showimage('register_show')" value=<?=$options['register_font_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีพื้นหลัง</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="register_bg_color" id="register_bg_color" onchange="showimage('register_show')" value=<?=$options['register_bg_color'] ?> /></td>
      <td> </td>
    </tr>

    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td><span class="style87">
        <label>
        <input type="checkbox" name="reg_color" value="yes" <?= ('yes' == $options['reg_color']) ? "checked='checked'" :"'no' = {$options['reg_color']}" ?> />
&#3626;&#3640;&#3656;&#3617;&#3626;&#3637;&#3605;&#3633;&#3623;&#3629;&#3633;&#3585;&#3625;&#3619;&#3649;&#3621;&#3632;&#3626;&#3637;&#3614;&#3639;&#3657;&#3609;&#3627;&#3621;&#3633;&#3591;</label>
      </span></td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>หน้าลืมรหัสผ่าน</label>
      </span></td>
      <td><input type="radio" name="captcha_lostpassword" value="yes" <?= ('yes' == $options['captcha_lostpassword']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิด &nbsp;&nbsp;<input type="radio" name="captcha_lostpassword" value="no" <?= ('no' == $options['captcha_lostpassword']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
      <td rowspan="3"><b>ตัวอย่าง</b><div id="lostpassword_show"></div></td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีตัวอักษร</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="lostpassword_font_color" id="lostpassword_font_color" onchange="showimage('lostpassword_show')" value=<?=$options['lostpassword_font_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีพื้นหลัง</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="lostpassword_bg_color" id="lostpassword_bg_color" onchange="showimage('lostpassword_show')" value=<?=$options['lostpassword_bg_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td><span class="style87">
        <label>
         <input type="checkbox" name="lost_color" value="yes" <?= ('yes' == $options['lost_color']) ? "checked='checked'" : "'no' = {$options['lost_color']}" ?> />
&#3626;&#3640;&#3656;&#3617;&#3626;&#3637;&#3605;&#3633;&#3623;&#3629;&#3633;&#3585;&#3625;&#3619;&#3649;&#3621;&#3632;&#3626;&#3637;&#3614;&#3639;&#3657;&#3609;&#3627;&#3621;&#3633;&#3591;</label>
      </span></td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><span class="style89">
        <label>หน้าแสดงความคิดเห็น</label>
      </span></td>
      <td><input type="radio" name="captcha_comments" value="yes" <?= ('yes' == $options['captcha_comments']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;เปิด &nbsp;&nbsp;<input type="radio" name="captcha_comments" value="no" <?= ('no' == $options['captcha_comments']) ? "checked='checked'" : NULL ?> />&nbsp;&nbsp;ปิด</td>
      <td rowspan="3"><b>ตัวอย่าง</b><div id="comment_show"></div></td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีตัวอักษร</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="comment_font_color" id="comment_font_color" onchange="showimage('comment_show')" value=<?=$options['comment_font_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td><div align="right" class="style89">สีพื้นหลัง</div></td>
      <td><input class="color {hash:true,pickerInset:5,pickerFaceColor:'#B8FCFC'}" name="comment_bg_color" id="comment_bg_color" onchange="showimage('comment_show')" value=<?=$options['comment_bg_color'] ?> /></td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td><span class="style87">
        <label>
        <input type="checkbox" name="com_color" value="yes" <?= ('yes' == $options['com_color']) ? "checked='checked'" : "'no' = {$options['com_color']}" ?> />
&#3626;&#3640;&#3656;&#3617;&#3626;&#3637;&#3605;&#3633;&#3623;&#3629;&#3633;&#3585;&#3625;&#3619;&#3649;&#3621;&#3632;&#3626;&#3637;&#3614;&#3639;&#3657;&#3609;&#3627;&#3621;&#3633;&#3591;</label>
      </span></td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <tr>
      <td> </td>
      <td> </td>
      <td>&nbsp;</td>
      <td> </td>
    </tr>
    <input type="hidden" name="cptch_form_submit" value="submit" /><br />
  </table>
</form>

<script type="text/javascript">
    <!--    
        function showimage( ele )
        {
            url = "<?= plugins_url("captcha-in-thai/show_in_settings.php")  ?>";
            url += "?font_size=" + "<?= $capth_def_setting['font_size'] ?>";
            url += "&width=" + "<?= $capth_def_setting['width'] ?>";
            url += "&height=" + "<?= $capth_def_setting['height'] ?>";
            if( ele == "login_show" ){
                fontcol = document.getElementById('login_font_color').value;
                bgcol = document.getElementById('login_bg_color').value;
            }else if( ele == "register_show" ){
                fontcol = document.getElementById('register_font_color').value;
                bgcol = document.getElementById('register_bg_color').value;
            }else if( ele == "comment_show" ){
                fontcol = document.getElementById('comment_font_color').value;
                bgcol = document.getElementById('comment_bg_color').value;
            }else if( ele == "lostpassword_show" ){
                fontcol = document.getElementById('lostpassword_font_color').value;
                bgcol = document.getElementById('lostpassword_bg_color').value;
            }
            url += "&font_color=" + fontcol.substr( 1 );
            url += "&bg_color=" + bgcol.substr( 1 );
            url += "&show_in_setting=1";
            url += "&distorted='yes'";
            url += "&str=<?=urlencode( stripslashes("ทดสอบ"))?>";
            
            var noise = document.getElementsByName('noise');
            for ( i=0 ; i<4 ; i++ ){
                if ( noise[i].checked == true ){
                    url += "&noise="+noise[i].value;
                } 
            }
            
            var distorted = document.getElementsByName('distorted');
            for ( i=0 ; i<4 ; i++ ){
                if ( distorted[i].checked == true ){
                    url += "&distorted="+distorted[i].value;
                }
            }

            document.getElementById(ele).innerHTML = '<iframe src="'+url+'" style="border:none" width="350" scrolling="no"></iframe>';
        }
        
        function show_all( ){
            showimage( 'login_show' );
            showimage( 'register_show' );
            showimage( 'comment_show' );
        }
    //-->
</script>
<script type="text/javascript" src=<?=plugins_url("captcha-in-thai/jscolor/jscolor.js")?>></script>

<script type="text/javascript">
    <!--    
        showimage("login_show");
        showimage("register_show");
        showimage("comment_show");
        showimage("lostpassword_show");
    //-->
</script>


<?php
} // end function menu_page
?>