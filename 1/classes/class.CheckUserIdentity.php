<?php
/**
 * This class contains logic to check user's identity
*/
class CheckUserIdentity
{
	/**
	 * checks user identity
	 * @param name name of user
	 * @param email password of user
	 * @param pass password
	 * @param cpcode extra code used for change password
	*/
	function checkIdentity($name, $email, $pass, $cpcode)
	{
		global $site_path, $site_url;

		$return_val = 'error';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($fl) && file_exists($fl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0) { 	// && !isset($udtls['prf']['vcode'])
				if(isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name && isset($udtls['prf']['email']) && $udtls['prf']['email'] == $email && isset($udtls['prf']['paswd']) && $udtls['prf']['paswd'] == $pass && !isset($udtls['prf']['vcode'])) {
					$return_val = 'success:'.$name;
				} else if($cpcode != '' && isset($udtls['prf']['name']) && $udtls['prf']['name'] == $name && isset($udtls['prf']['email']) && $udtls['prf']['email'] == $email && isset($udtls['prf']['cpcode']) && $udtls['prf']['cpcode'] == $cpcode && !isset($udtls['prf']['vcode'])) {
					$udtls['prf']['paswd'] = $pass;
					@ file_put_contents($fl, json_encode($udtls), LOCK_EX);
					@ chmod($fl, 0777);
					$return_val = 'success:'.$name;
				} else {
					$return_val = 'error';
				}
			} else {
				$udtls['prf']['name'] = $name;
				$udtls['prf']['email'] = $email;
				$udtls['prf']['paswd'] = $pass;
				$udtls['prf']['vcode'] = uniqid(true);
				@ file_put_contents($fl, json_encode($udtls), LOCK_EX);
				@ chmod($fl, 0777);
				// mail($email, 'Email Verification', $site_path.'uverify.php?code='.$udtls['pref']['vcode'], $headers);
				include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Mail.php');
				$mailer_obj = new Mail();
				$mbody = '<a href="'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'">'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'</a>';
				$rtrn_val = $mailer_obj->sendMail($email, $mbody);
				if(!$rtrn_val) {
					@ unlink($fl);
					$return_val = 'e-error';
				}
				$return_val = 'wait';
			}
		} else {
			$udtls['prf']['name'] = $name;
			$udtls['prf']['email'] = $email;
			$udtls['prf']['paswd'] = $pass;
			$udtls['prf']['vcode'] = uniqid(true);
			@ file_put_contents($fl, json_encode($udtls), LOCK_EX);
			@ chmod($fl, 0777);
			// mail($email, 'Email Verification', $site_path.'uverify.php/?code='.$udtls['pref']['vcode'], $headers);
			include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Mail.php');
			$mailer_obj = new Mail();
			$mbody = '<a href="'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'">'.$site_url.'uverify.php?name='.$name.'&code='.$udtls['prf']['vcode'].'</a>';
			$rtrn_val = $mailer_obj->sendMail($email, $mbody);
			if(!$rtrn_val) {
				@ unlink($fl);
				$return_val = 'e-error';
			}
			$return_val = 'wait';
		}
		return $return_val;
	}
}
?>