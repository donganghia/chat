<?php
/**
 * This class contains logic to display users chat history
*/
class ChatHistory
{
	/**
	 * displays user history
	 * @param name name of user
	 * @param q password of user
	*/
	function getHistory($name, $q)
	{
		global $site_path;

		$eauth = false;
		if(isset($_POST) && count($_POST) > 0 && isset($_POST['pass']) && trim($_POST['pass']) != '') {
			$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
			if(is_file($fl) && file_exists($fl)) {
				$udtl = file_get_contents($fl);
				$udtls = array();
				if(trim($udtl) != '') {
					$udtls = @ json_decode($udtl, 1);
					if(!is_array($udtls)) { $udtls = array(); }
				}
				$pass = $_POST['pass'];
				if(isset($udtls['prf']) && is_array($udtls['prf']) && count($udtls['prf']) > 0 && isset($udtls['prf']['paswd']) && trim($udtls['prf']['paswd']) === $pass) {
					if($q == 'General-'.$name || $q == $name.'-General') {
						$hf = $site_path.'h'.DIRECTORY_SEPARATOR.$name.'.html';
					} else {
						$hf = $site_path.'h'.DIRECTORY_SEPARATOR.'uh'.DIRECTORY_SEPARATOR.$q.'.html';
					}
					echo $cnt = file_get_contents($hf); exit;
				}
				$eauth = true;
			}
		}
		return $eauth;
	}
}
?>