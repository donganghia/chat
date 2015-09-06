<?php
/**
 * This class contains logic to provide contacts of a group
*/
class GroupDetails
{
	/**
	 * Contacts of a group
	 * @param name name of user
	 * @param grpnm name of group
	*/
	function groupContacts($name, $grpnm)
	{
		global $site_path;

		$grpcnms = '';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($fl) && file_exists($fl)) {
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['grp']) && is_array($udtls['grp'])) {
				// $grpcnms = $udtls['grp'][$grpnm];
				$gkv = array_search($grpnm, $udtls['grp']);
				if($gkv !== false) {
					$gnm = $grpnm.'-'.$gkv;
					$gfl = $site_path.'files'.DIRECTORY_SEPARATOR.'grp'.DIRECTORY_SEPARATOR.$gnm.'.u';
					$gdtl = file_get_contents($gfl);
					$gdtls = array();
					if(trim($gdtl) != '') {
						$gdtls = @ json_decode($gdtl, 1);
						if(!is_array($gdtls)) { $gdtls = array(); }
					}
					if(isset($gdtls['con'])) {
						$grpcnms = @ implode(',', $gdtls['con']);
					}
					if(isset($gdtls['conr'])) {
						$grpcnms .= @ ','.implode(',', $gdtls['conr']);
					}
					$grpcnms = trim(trim($grpcnms, ','));
				}
			}
		}
		return $grpcnms;
	}
}
?>