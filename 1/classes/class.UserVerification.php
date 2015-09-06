<?php
/**
 * This class contains logic to verify user's account
*/
class UserVerification
{

	/**
	 * function to verify user
	 * @param name name of user
	 * @param code verification code
	*/
	function verifyUser($name, $code)
	{
		global $site_path;

		$return_val = 'Error, <a href="'.$site_url.'sc.php">Back To App</a>';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';

		if(is_file($fl) && file_exists($fl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0 && isset($udtls['prf']['vcode']) && trim($udtls['prf']['vcode']) === $code) {
				unset($udtls['prf']['vcode']);
				@ file_put_contents($fl, json_encode($udtls), LOCK_EX);
				@ chmod($fl, 0777);
				$return_val = 'Success, <a href="'.$site_url.'sc.php">Back To App</a>';
			} else {
				$return_val = 'Error, <a href="'.$site_url.'sc.php">Back To App</a>';
			}
		}
		return $return_val;
	}
}
?>