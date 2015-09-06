<?php
/**
 * This class contains logic to initialize chat with contacts
*/
class InitContactChat
{
	/**
	 * Initializes chat with contact
	 * @param name name of user
	 * @param cnm name of contact
	*/
	function initCChat($name, $cnm)
	{
		global $site_path;

		$return_val = 'error';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$cfl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$cnm.'.u';
		if(is_file($fl) && file_exists($fl) && is_file($cfl) && file_exists($cfl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			$cdtl = file_get_contents($cfl);
			$cdtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(trim($cdtl) != '') {
				$cdtls = @ json_decode($cdtl, 1);
				if(!is_array($cdtls)) { $cdtls = array(); }
			}
			$inb = 0;
			if(isset($udtls['con']) && is_array($udtls['con']) && in_array($cnm, $udtls['con'])) {
				$inb = 1;
			} else if(isset($udtls['conr']) && is_array($udtls['conr'])) {
				$inb = -1;
			}
			if(isset($cdtls['con']) && is_array($cdtls['con']) && in_array($name, $cdtls['con'])) {
				$inb = 2;
			} else if(isset($cdtls['conr']) && is_array($cdtls['conr'])) {
				$inb = 0;
			}
			if($inb == 2) {
				$fldnm = (strcasecmp($name, $cnm) > 0)? $cnm.'-'.$name : $name.'-'.$cnm;
				if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
				}
				if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$cnm.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
				}
				if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
					@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
				}
				$return_val = 'success:'.$fldnm;
			} else {
				$return_val = $inb;
			}
		}
		return $return_val;
	}
}
?>