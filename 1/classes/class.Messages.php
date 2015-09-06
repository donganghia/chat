<?php
/**
 * This class contains logic to fetch new messages
*/
class Messages
{
	/**
	 * Fetch new messages
	 * @param name name of user
	 * @param type type of request / response (AJAX or SSE)
	*/
	function fetchMessages($name, $type)
	{
		global $site_path, $long_polling_inerval, $group_prefix;

		$dtls = array('gen'=>'');
		$time = 0;
		$umdo = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		while(count(array_filter($dtls)) > 0 || $time < $long_polling_inerval) 	// trim($dtls['gen']) == '' ||
		{
			$fls = scandir($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR);
			foreach($fls as $ky => $vl) {
				if(!in_array($vl, array('.', '..'))) {
					// delete old files, can be shifted to cron (if using db save data into db before delete)
					$mtime = @filemtime($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
					if($mtime && $mtime < strtotime("-1 minutes")) {
						@ unlink($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
						// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
					}
					//
					if(!in_array($name.'_'.$vl, $fls) && strpos($vl, '_') === false) {
						// echo $site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl;
						if(is_file($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl) && file_exists($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl)) {
							$cnt = file_get_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$vl);
							if(trim($cnt) != '') {
								$dtls['gen'] .= (trim($dtls['gen']) != '')? "<hr style='border-style:dashed;' />".$cnt : $cnt;
							}
							file_put_contents($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.$vl,'');
							@ chmod($site_path.'tmp'.DIRECTORY_SEPARATOR.'mt'.DIRECTORY_SEPARATOR.$name.'_'.$vl, 0777);
						}
					}
					// @ unlink($site_path.'tmp/mt/'.$vl);
				}
			}
			//
			if(file_exists($umdo)) {
				$uflds = scandir($umdo);
				foreach($uflds as $key => $val) {
					if(strpos($val, $group_prefix) !== false && strpos($val, $group_prefix) === 0) {
						// $ci = substr($val, strpos($val,':')+1, strpos($val,'-')-2);
						$ci = $val;
					} else {
						$ci = trim(str_replace($name,'',$val),'-');
					}
					$umd = $site_path.'tmp'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$val.DIRECTORY_SEPARATOR;
					if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
						if(file_exists($umd)) {
							$ufls = scandir($umd);
							foreach($ufls as $ky => $vl) {
								if(!in_array($vl, array('.', '..'))) {
									// delete old files, can be shifted to cron (if using db save data into db before delete)
									$mtime = @filemtime($umd.$vl);
									if($mtime && $mtime < strtotime("-1 minutes")) {
										@ unlink($umd.$vl);
										// @ unlink($site_path.'tmp/mt/'.$name.'_'.$vl);
									}
									//
									if(!in_array($name.'_'.$vl, $ufls) && strpos($vl, '_') === false) { 	// && in_array($vl, $cids)
										if(is_file($umd.$vl) && file_exists($umd.$vl)) {
											$cnt = file_get_contents($umd.$vl);
											if(trim($cnt) != '') {
												$dtls[$ci] = (isset($dtls[$ci]))? $dtls[$ci] : ''; 	// [$vl]
												$dtls[$ci] .= (trim($dtls[$ci]) != '')? "<hr style='border-style:dashed;' />".$cnt : $cnt; 	// [$vl]
											}
											file_put_contents($umd.$name.'_'.$vl,'');
											@ chmod($umd.$name.'_'.$vl, 0777);
										}
									}
									//
								}
							}
						}
						// @ unlink($site_path.'tmp/mt/'.$vl);
					}
				}
			}
			//
			if(count(array_filter($dtls)) > 0) { 	// trim($dtls['gen']) != '' ||
				// $dtls = utf8_encode($dtls);
				$dtls = array_map('utf8_encode', $dtls);
				break;
			}
			usleep(500000);
			$time = $time + 500000;
			// break;
		}
		// print_r($dtls); exit;
		$dtls = array_filter($dtls);
		$this->sendMessage(time(), $dtls, $type);
		$this->afterMessageFetch($name, $dtls);
		return true;
	}

	/**
	 * Send messages to user
	 * @param lid latest unique identification token
	 * @param dtls message text
	 * @param typ type of request / response (AJAX or SSE)
	*/
	function sendMessage($lid, $dtls, $typ='ajx')  //
	{
		$retry = 500; 	// 1000
		if($typ == 'sse') {
			ob_clean();
			header("Connection: keep-alive");
			// recommended to prevent caching of event data.
			header("pragma: no-cache,no-store");
			header('Cache-Control: no-cache,no-store,must-revalidate,max-age=0,max-stale=0');
			header("Expires: Sun, 31 Jan 2010 10:10:10 GMT");
			header('Content-Type: text/event-stream');
            //
			echo "retry: ".$retry . "\r\n";
			echo "id: $lid" . "\r\n";
			echo "data: " . json_encode($dtls) . "\r\n";
			echo "\r\n";
			//
			ob_flush();
			flush();
		} else {
			ob_clean();
			echo json_encode(array('lastEventId' => $lid, 'data' => $dtls)); 	// JSON_FORCE_OBJECT
			//
			ob_flush();
			flush();
		}
		// return '';
	}

	/**
	 * Some extra processing after fetching messages
	 * @param name name of user
	 * @param dtls message text
	*/
	function afterMessageFetch($name, &$dtls)
	{
		global $site_path, $group_prefix;

		$umdo = $site_path.'files'.DIRECTORY_SEPARATOR.'um'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
		// clean old history files
		if(is_file($site_path."h".DIRECTORY_SEPARATOR.$name.'.html') && file_exists($site_path."h".DIRECTORY_SEPARATOR.$name.'.html')) {
			$mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR.$name.'.html');
			if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.'.html', 0777);
				@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR.$name.'.html', '');
				@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.'.html', 0777);
			}
		}
		// removing old history files
		if(file_exists($umdo)) {
			$uflds = scandir($umdo);
			foreach($uflds as $key => $val) {
				if(!in_array($val, array('.', '..'))) { 	// && in_array($val, $cids)
					if(is_file($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html') && file_exists($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html')) {
						$mtime = @filemtime($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html');
						if($mtime && (strtotime('+1 minutes') - $mtime) > strtotime('-7 days')) {
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0777);
							@ file_put_contents($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', '');
							@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$val.'.html', 0777);
						}
					}
					//
				}
			}
		}
		//write to history file
		if(isset($dtls['gen']) && trim($dtls['gen']) != '' && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR.$name.'.html', "a+")) {
			@ fwrite($fl, $dtls['gen']."<hr style='border-style:dashed;' />"); 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
			@ fclose($fl);
			@ chmod($site_path."h".DIRECTORY_SEPARATOR.$name.'.html', 0777);
		}
		if(is_array($dtls)) {
			foreach ($dtls as $key => $value) {
				if(strpos($key, $group_prefix) !== false && strpos($key, $group_prefix) === 0) {
					$fldnm = $key.'-'.$name;
				} else {
					$fldnm = (strcasecmp($name, $key) > 0)? $key.'-'.$name : $name.'-'.$key;
				}
				if($key != 'gen' && trim($value) != '' && $fl = @fopen($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.'.html', "a+")) {
					$vl = $key;
					@ fwrite($fl, $value."<hr style='border-style:dashed;' />"); 	// ." <i style='float:right;'>(".$date.")</i><hr style='border-style:dashed;' />"
					@ fclose($fl);
					@ chmod($site_path."h".DIRECTORY_SEPARATOR."uh".DIRECTORY_SEPARATOR.$fldnm.'.html', 0777);
				}
			}
		}
		// reg
		if(file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u') && is_file($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u')) {
			$udtl = file_get_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('')));
			$udtls = array();
			if(trim($udtl) != '') {
				$udtls = @ json_decode($udtl, 1);
				$udtl = '';
				if(!is_array($udtls)) { $udtls = array(); }
			}
			$udtls['lst'] = date('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode($udtls));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0777);
		} else {
			$udtls['lst'] = date('Y-m-d h:i:s A');
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(json_encode($udtls)));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'un'.DIRECTORY_SEPARATOR.$name.'.u', 0777);
		}
		// online
		$mtime = @ filemtime($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u');
		if(! file_exists($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u') || ($mtime && $mtime < strtotime("-30 seconds"))) {
			file_put_contents($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', json_encode(array('lastseen'=>date('Y-m-d H:i:s'))));
			@ chmod($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$name.'.u', 0777);
		}
		// remove offline
		$udrs = scandir($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR);
		foreach($udrs as $ky => $vl) {
			if(!in_array($vl, array('.', '..'))) {		// delete old files, can be shifted to cron
				$mtime = @ filemtime($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
				if($mtime && $mtime < strtotime("-1 minutes")) {
					@ unlink($site_path.'files'.DIRECTORY_SEPARATOR.'ou'.DIRECTORY_SEPARATOR.$vl);
				}
			}
		}
	}
}
?>