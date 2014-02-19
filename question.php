<?php
function question() {
	global $wpdb;
	global $options;
	global $capth_def_setting;
	if (isset($_REQUEST['show_in_setting'])) {
		$str_id = -1;
	} else {
		$hostname = $_SERVER['SERVER_NAME'];
		$user_ip = $_SERVER['REMOTE_ADDR'];
		$query = "SELECT * FROM ct_question WHERE";
		$is_first_type = TRUE;

		if ('no' == $options['question_type1']) {
			if ($is_first_type == FALSE) {
				$query .= " OR";
			}
			$query .= " type_id = 1 ";
			$is_first_type = FALSE;
		}

		if ('yes' == $options['question_type2']) {
			if ($is_first_type == FALSE) {
				$query .= " OR";
			}
			$query .= " type_id = 2 ";
			$is_first_type = FALSE;
		}

		if ('yes' == $options['question_type3']) {
			if ($is_first_type == FALSE) {
				$query .= " OR";
			}
			$query .= " type_id = 3 ";
			$is_first_type = FALSE;
		}
		if ('yes' == $options['question_type4']) {
			if ($is_first_type == FALSE) {
				$query .= " OR";
			}
			$query .= " type_id = 4 ";
			$is_first_type = FALSE;
		}

		$query .= "ORDER BY RAND() LIMIT 1";

		$row = $wpdb -> get_row($query, ARRAY_A);
		$str = $row['question'];
		/////เช็คว่าถ้าซ้ำให้ทับของเก่า

		$id = $row['question_id'];

		$query = "SELECT * FROM ct_user_question WHERE user_ip = '$user_ip' ";
		$check = $wpdb -> get_row($query, ARRAY_A);

		if ($wpdb -> num_rows) {
			$query = "DELETE FROM ct_user_question WHERE user_ip = '$user_ip'";
			$wpdb -> query($query);
		}

		$query = "INSERT INTO ct_user_question ( user_ip, question_id )
VALUES ( '$user_ip','$id' )";
		$wpdb -> query($query);

		$query = "SELECT * FROM ct_user_question WHERE user_ip = '$user_ip' ";
		$check = $wpdb -> get_row($query, ARRAY_A);
		$temp = $wpdb -> get_row($query, ARRAY_A);
		$str_id = $temp['id'];


		$query = "SELECT info FROM ct_questiontype WHERE type_id = {$row['type_id']}";
		$temp = $wpdb -> get_row($query, ARRAY_A);
		$info = $temp['info'];

	}
	return array('info' => $info, 'str_id' => $str_id, 'type_id'=>$row['type_id']);
}
?>
