<?php
	$admin_email = "lqfbua@gmail.com";
	$admin_email = "endway08@gmail.com";
	$result = array('success' => false);
	if(isset($_POST) && isset($_POST['reg_type'])) {
		$post = $_POST;
		$errors = array();
		if(!isset($post['dob']) or !$post['dob']) {
			$errors['dob'] = 'Укажите дату рождения';
		}
		if(!isset($post['email']) or !$post['email']) {
			$errors['email'] = 'Укажите email адрес';
		}
		if(!isset($post['region']) or !$post['region']) {
			$errors['region'] = 'Укажите область';
		}

		switch(@$post['reg_type']) {
			case "anonymous":
				$body = "email: $post[email]\n region: $post[region]";
				break;
			case "id_offline":
				if(!isset($post['hash']) or !$post['hash']) {
					$errors['hash'] = 'Неверно указан один из параметров';
				}

				$link = mysql_connect('localhost', 'www-data', '') or die('Could not connect to server.');
				mysql_select_db('liquid_feedback', $link) or die('Could not select database.');

				if (get_magic_quotes_gpc()) {
					$hash = stripslashes($post['hash']);
				}
				$query = mysql_query("SELECT COUNT(*) as count FROM member WHERE identification='".$hash."'");
				if($query) while ($row = mysql_fetch_object($query)) {
					$result = $row;
				}
				if($result && $result->count > 0) {
					$errors['hash'] = 'Пользователь существует';
				}
				$body = "email: $post[email]\n region: $post[region]\n hash: $post[hash]";
				break;
			default:
			case "id_online":
				$errors['reg_type'] = "Временно не доступно";
				break;
		}

		if(count($errors) > 0) {
			$result['errors'] = $errors;
		} else {
			$result['message'] = $body;
			$result['success'] = mail($admin_email, 'LiquidFeedbackUA Registration', $body);
		}
	}
	die(json_encode($result));
?>