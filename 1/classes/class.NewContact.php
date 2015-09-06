<?php
/**
 * This class contains logic to handle adding new contacts
*/
class NewContact
{
	/**
	 * Add new contact
	 * @param name name of user
	 * @param cnm name of contact
	*/
	function addNewContact($name, $cnm)
	{
		global $site_path;

		$return_val = 'error';
		$fl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		$cfl = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$cnm.'.u';
		if(is_file($fl) && file_exists($fl) && is_file($cfl) && file_exists($cfl)) {
			$cdtl = file_get_contents($cfl);
			$cdtls = array();
			if(trim($cdtl) != '') {
				$cdtls = @ json_decode($cdtl, 1);
				if(!is_array($cdtls)) { $cdtls = array(); }
			}
			if(!isset($cdtls['con']) || !is_array($cdtls['con'])) { $cdtls['con'] = array(); }
			if(!isset($cdtls['conr']) || !is_array($cdtls['conr'])) { $cdtls['conr'] = array(); }
			if(isset($cdtls['conr']) && is_array($cdtls['conr'])) {
				if(isset($cdtls['con']) && is_array($cdtls['con']) && !in_array($name, $cdtls['con'])) {
					$cdtls['conr'] = array_filter($cdtls['conr']);
					$cdtls['conr'] = array_merge($cdtls['conr'], array($name)) ;
					$cdtls['conr'] = array_unique($cdtls['conr']);
				}
			} else {
				if(isset($cdtls['con']) && is_array($cdtls['con']) && !in_array($name, $cdtls['con'])) {
					$cdtls['conr'] = array($name);
				}
			}
			//
			$udtl = file_get_contents($fl);
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				if(!is_array($udtls)) { $udtls = array(); }
			}
			if(isset($udtls['con']) && is_array($udtls['con'])) {
				$udtls['con'] = array_filter($udtls['con']);
				$udtls['con'] = array_merge($udtls['con'], array($cnm)) ;
				$udtls['con'] = array_unique($udtls['con']);
			} else {
				$udtls['con'] = array($cnm);
			}
			if(isset($udtls['conr']) && is_array($udtls['conr'])) {
				$ky = array_search($cnm, $udtls['conr']);
				if($ky !== false) {
					unset($udtls['conr'][$ky]);
					$udtls['conr'] = array_values($udtls['conr']);
				}
			}
			$cdtls = array_filter($cdtls);
			$udtls = array_filter($udtls);
			if(isset($udtls['con']) && is_array($udtls['con']) && count($udtls['con']) > 0 &&
				((isset($cdtls['conr']) && is_array($cdtls['conr']) && count($cdtls['conr']) > 0) ||
					(isset($cdtls['con']) && is_array($cdtls['con']) && count($cdtls['con']) > 0)
				)
			) {
				if(file_put_contents($cfl, json_encode($cdtls)) !== false) {
					if(file_put_contents($fl, json_encode($udtls)) !== false) {
						@ chmod($cfl, 0777);
						@ chmod($fl, 0777);
						$return_val = 'success';
					} else {
						$key = array_search($name, $cdtls['conr']);
						// $k = array_search($name, $cdtls['con']);
						if($key === false) { 	// && $k === false
							unset($cdtls['conr'][$key]);
							file_put_contents($cfl, json_encode($cdtls));
							$return_val = 'error';
						}
						@ chmod($cfl, 0777);
						@ chmod($fl, 0777);
					}
				}
			}
		}
		return $return_val;
	}
}
?>