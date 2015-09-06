<?php
/**
 * This class contains logic to accept contact and group requests
*/
class AcceptRequest
{
	/**
	 * Accept Contact Request
	 * @param name name of user
	 * @param cnm name of contact
	*/
	function acceptCReq($name, $cnm)
	{
		global $site_path;

		$return_val = 'error';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($fl) && file_exists($fl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['con']) && is_array($udtls['con'])) {
				$udtls['con'] = array_merge($udtls['con'], array($cnm)) ;
				$udtls['con'] = array_unique($udtls['con']);
			} else {
				$udtls['con'] = array($cnm);
			}
			if(isset($udtls['conr']) && is_array($udtls['conr'])) {
				$key = array_search($cnm, $udtls['conr']);
				unset($udtls['conr'][$key]);
			} else {
				$udtls['conr'] = array();
			}
			if(isset($udtls['con']) && is_array($udtls['con'])) {
				if(file_put_contents($fl, json_encode($udtls)) !== false) {
					$return_val = 'success';
				}
				@ chmod($fl, 0777);
			}
		}
		return $return_val;
	}

	/**
	 * Accept Group Request
	 * @param name name of user
	 * @param grpnm name of group
	*/
	function acceptGReq($name, $grpnm)
	{
		global $site_path, $group_prefix;

		$return_val = 'error';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($fl) && file_exists($fl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(!isset($udtls['grp']) || !is_array($udtls['grp'])) { $udtls['grp'] = array(); }
			if(!isset($udtls['grpr']) || !is_array($udtls['grpr'])) { $udtls['grpr'] = array(); }
			$gkv = array_search($grpnm, $udtls['grpr']);
			if($gkv !== false) {
				unset($udtls['grpr'][$gkv]);
				$udtls['grp'][$gkv] = $grpnm;
				//
				$gnm = $grpnm.'-'.$gkv;
				$gfl = $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR.$gnm.'.u';
				$gdtl = file_get_contents($gfl);
				$gdtls = array();
				if(trim($gdtl) != '') {
					$gdtls = @ json_decode($gdtl, 1);
					if(!is_array($gdtls)) { $gdtls = array(); }
				}
				if(!isset($gdtls['con']) || !is_array($gdtls['con'])) { $gdtls['con'] = array(); }
				if(!isset($gdtls['conr']) || !is_array($gdtls['conr'])) { $gdtls['conr'] = array(); }
				$k = array_search($name, $gdtls['conr']);
				if($k !== false) {
					unset($gdtls['conr'][$k]);
					$gdtls['conr'] = array_values($gdtls['conr']);
					$gdtls['con'][] = $name;
					$gdtls['con'] = array_values(array_unique($gdtls['con']));
					file_put_contents($gfl, json_encode($gdtls), LOCK_EX);
				}
				file_put_contents($fl, json_encode($udtls), LOCK_EX);
				$fldnm = $group_prefix.$gnm; 	// (strcasecmp($name, $gnm) > 0)? $gnm.'-'.$name : $name.'-'.$gnm;
				if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
				}
				$return_val = 'success';
			}
		}
		return $return_val;
	}
}
?>