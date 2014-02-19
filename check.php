<?php
session_start();
function check_answer($hostname, $ip, $answer) {
	global $wpdb;
	$response;
	$query = "SELECT answer, ct_question.question_id, question
                FROM ct_question
                JOIN (  SELECT question_id
                        FROM ct_user_question
                        WHERE user_ip = '$ip' 
                     ) as q
                ON ct_question.question_id = q.question_id";

	$row = $wpdb -> get_row($query, ARRAY_A);

	$check;
	if (strcmp($row['answer'], $answer) == 0) {
		$check = 'y';
		$response[0] = 'true';
	} else {
		$check = 'n';
		$response[0] = 'false';
	}
	$query = "INSERT INTO ct_answer VALUES ( NULL , '$hostname' , '$ip', '{$row['question_id']}', '$answer' , '$check' )";
	$wpdb -> query($query);
	$response[1] = $row['question'];
	$response[2] = $row['answer'];

	return $response;
}
?>
