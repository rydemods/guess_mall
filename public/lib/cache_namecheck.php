<?
/*
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$second = 900;		// 15분간 한IP에서 실명인증을
$maxvisit = 6;		// 6번이상할경우
$no_second = 600;	// 10분간 실명인증을 막음

if (file_exists($file_jumin)) { 
	$filetime = @filemtime($file_jumin);
	$timegan=(time()-$filetime);
	if ($timegan>$second) {
		@unlink($file_jumin);
		$name_result=0;
	} else {
		$name_result=1;
		return; 
	} 
}


// $file_denyN 가 존재하면 차단임..
if (file_exists($file_denyN)) {
	$filetime = @filemtime($file_denyN);
	$timegan=(time()-$filetime);
	// 파일생성된지 no_seond초가 지나면 다시 오픈
	if ($timegan>=$no_second) {
		@unlink($file_denyN);
	} else {
		$name_result=119;
		return;
	}
}

if (!file_exists($file_denyY)) {
	file_put_contents($file_denyY,"1");
} else {
	$temp = file_get_contents($file_denyY);
	if (strlen($temp)==0) $temp=0;

	$filetime = @filemtime($file_denyY);
	$timegan=(time()-$filetime);
	if ($timegan>$second) {
		@unlink($file_denyY); // 파일생성된지  x 초 지나면 다시 오픈
	} else {
		if ($temp>=$maxvisit) { // x 초 안에 방문자가 x 명이상이면
			file_put_contents($file_denyN,$temp);
			unlink($file_denyY);
			$name_result=119;
			return; 
		}
	}
}
*/
?>
