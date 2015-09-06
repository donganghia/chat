<?php
/**
 * This class contains logic to send / reply message
*/
class Reply
{
	/**
	 * Send new messages
	 * @param name name of user
	 * @param ci contact identifier
	 * @param msg text message
	 * @param typ broadcast or specific contacts or individual contact / group or all contacts
	*/
	function sendMessage($name, $ci, $msg, $typ, $gcid='')
	{
		global $site_path, $group_prefix;

		// $return_val = 'error';
		$date = date('Y-m-d h:i:s a');
		$flnm = "";
		$cids = array();
		$sfls = array();
		$uflnm = $site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u';
		if(is_file($uflnm) && file_exists($uflnm)) {
			$ufn = uniqid(true);
			$udtl = file_get_contents($uflnm);
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
			}
			if(!is_array($udtls)) { $udtls = array(); }
			if(!isset($udtls['con'])) { $udtls['con'] = array(); }
			if(!isset($udtls['grp'])) { $udtls['grp'] = array(); }
			//
			if($ci != '') {
				if(strpos($ci, ',') !== false) {
					$cids = @ explode(',', $cids);
					$cids = array_values(array_unique(array_filter($cids)));
				} else {
					$cids[] = $ci;
				}
			}
			if($typ == 'public') {
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
			} else if($typ == 'all') {
				//
				$ufls = array();
				if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR)) {
					$ufls = scandir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR);
				} else {
					@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR, 0777, true);
				}
				if(is_array($ufls)) {
					foreach ($ufls as $key => $val) {
						if(in_array($val, array('.','..'))) {
							unset($ufls[$key]);
						} else {
							if(in_array($val, $udtls['con'])) {
								$fldnm = (strcasecmp($name, $val) > 0)? $val.'-'.$name : $name.'-'.$val;
								if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
									@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
									if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
										@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
									}
								}
								$ufls[$key] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
							}
						}
					}
				}
				//
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
			} else if($typ == 'contacts') {
				$ufls = array();
				foreach ($cids as $key => $val) {
					if(in_array($val, $udtls['con'])) {
						$fldnm = (strcasecmp($name, $val) > 0)? $val.'-'.$name : $name.'-'.$val;
						if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
							//
							if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
								@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
							}
						}
						$ufls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
					}
				}
				if(count($ufls) > 0) { $sfls = array_merge($sfls, $ufls); }
			} else if(trim($ci) != '' && trim(strtolower($ci)) != 'general') {
				$fldnm = '';
				if(in_array($ci, $udtls['con'])) {
					$fldnm = (strcasecmp($name, $ci) > 0)? $ci.'-'.$name : $name.'-'.$ci;
				// } else if(in_array($ci, $udtls['grp'])) {
				} else if(trim($gcid) != '' && isset($udtls['grp'][$gcid]) && $udtls['grp'][$gcid] = $ci) {
					$gkv = $gcid; 	// array_search($ci, $udtls['grp']);
					$fldnm = $group_prefix.$ci.'-'.$gkv;
				}
				if($fldnm != '') {
					if(!file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
						@ mkdir($site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
						//
						if(!file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR)) {
							@ mkdir($site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR, 0777, true);
						}
					}
					$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$fldnm.DIRECTORY_SEPARATOR;
				}
			} else {
				$sfls[] = $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR;
			}
			//
			if(trim($msg) != '') {
				$msg = $name.': '.$msg;
				$msg = str_replace(array("<br />\n", "`", "<script", "</script", "</ script"), array("<br />", "'", "&lt;script", "&lt;/script", "&lt;/script"), nl2br($msg));
				$hmsg = $msg." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />";
				if(count(array_filter($_FILES['files']['name'])) > 0) {
					$msg = $msg." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />";
				} else {
					$msg = $msg." <i style='float:right;'>(".$date.")</i><br/>";
				}
				foreach ($sfls as $key => $value) {
					file_put_contents($value.$ufn.'.mt', $msg);
					@ chmod($value.$ufn.'.mt', 0777);
				}
			}
			$msg = $hmsg = "";
			if(isset($_FILES['files']['name']) && is_array($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0) {
				for($l=0; $l < count($_FILES['files']['name']); $l++) {
					if($_FILES['files']['error'][$l] == 0) {
						$fl = $this->fileCopy($_FILES['files']['name'][$l], $_FILES['files']['tmp_name'][$l], $_FILES['files']['type'][$l]);
						//$fls = array_merge($fls, array($fl));
						if($msg == "") { $msg  = $hmsg = $name.': '.'File(s) : '; }
						// $msg = $fl . "<i style='float:right;'>(".$date.")</i>" . (($l == (count($_FILES['files']['name'])-1))? "<br/>" : "<hr style='border-style:dashed;' />");
						// $hmsg = $fl . "<i style='float:right;'>(".$date.")</i>" . "<hr style='border-style:dashed;' />";
						$msg .= "<br/>".$fl.' ';
						$hmsg .= "<br/>".$fl.' ';
					}
				}
				//
				if(trim($msg) != '') {
					// $ufln = uniqid(true);
					$msg = $msg . " <i style='float:right;'>(".$date.")</i><br/>";
					foreach ($sfls as $key => $value) {
						$flnm = $value.$ufn.'.mt';
						if(trim($flnm) != '' && is_file($flnm) && file_exists($flnm)) {
							$fl = fopen($flnm, "a+");
							fwrite($fl, $msg);
							fclose($fl);
							@ chmod($flnm, 0777);
						}
					}
				}
				//
			}
			//
		}
	}

	/**
	 * used for file upload
	 * @param fileName name of file
	 * @param filePath path of file to copy from
	 * @param fileType type of file
	*/
	function fileCopy($fileName, $filePath, $fileType='')
	{
		global $site_path, $site_url;
		// if(strlen($fileName) != '') {
			// $fileName = substr($fileName,0,10) . substr($fileName, strrpos($fileName,'.'));
		// }
		// $fileName = str_replace(array(' ','-'), '', $fileName);
		$file = $site_path."pub".DIRECTORY_SEPARATOR.$fileName;
		$url = $site_url."pub".DIRECTORY_SEPARATOR.$fileName;
		$json = '';
		$txt = '';
		if(move_uploaded_file($filePath, $file)) {
			@ chmod($file, 0777);
			$txt = "<a href=\"$url\" target=\"_blank\">$fileName</a> (file)";
			// $json = array('txt' => $txt);
		}
		// if(is_array($json)) { $json = json_encode($json); }
		return $txt;
	}
}
?>