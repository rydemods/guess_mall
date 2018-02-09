<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

function name_check($name, $jumin1, $jumin2, $id, $pw) {
	global $Dir, $history;
	if(ord($history)==0) $history="-1";
	$name_result=0;
	$file_jumin = $Dir.DataDir."cache/name/CACHE_$name"."_".md5("$jumin1$jumin2");
	$file_denyY = $Dir.DataDir."cache/name/CACHE_Y_".$_SERVER['REMOTE_ADDR'];
	$file_denyN = $Dir.DataDir."cache/name/CACHE_N_".$_SERVER['REMOTE_ADDR'];

	if(strpos(" ".$file_jumin,"..")!==FALSE) $file_jumin=str_replace("..","",$file_jumin);
	if(strpos(" ".$file_denyY,"..")!==FALSE) $file_denyY=str_replace("..","",$file_denyY);
	if(strpos(" ".$file_denyN,"..")!==FALSE) $file_denyN=str_replace("..","",$file_denyN);

	include($Dir."lib/cache_namecheck.php");

	if ($name_result==119) {
		alert_go("[".$_SERVER['REMOTE_ADDR']."] IP에서 다량 실명인증 시도가 있습니다.\\n실명인증 서비스 상점을 보호하기위해 로그기록하고, 실명인증을 거부합니다.",(int)$history);
	}

	if ($name_result!=1) {
		$host=$_SERVER['HTTP_HOST'];
		$port=80;
		$path="/".RootPath.FrontDir."getnamecheck.php";
		$name = str_replace(" ","",$name);

		$query="id=".$id."&pw=".$pw."&name=".$name."&jumin1=".$jumin1."&jumin2=".$jumin2;
		$fp = @fsockopen($host, $port, $errno, $errstr, 5);
		if(!$fp) {
			$strLine = " result value=5";
			@flush();
		} else {
			$cmd = "POST $path HTTP/1.0\n";
			fputs($fp, $cmd);
			$cmd = "Host: $host\n";
			fputs($fp, $cmd);
			$cmd = "Content-type: application/x-www-form-urlencoded\n";
			fputs($fp, $cmd);
			$cmd = "Content-length: " . strlen($query) . "\n";
			fputs($fp, $cmd);
			$cmd = "Connection: close\n\n";
			fputs($fp, $cmd);
			fputs($fp, $query);
			flush();
			while($currentHeader = fgets($fp,4096)) {
				if($currentHeader == "\r\n") {
					break;
				}
			}

			$strLine = "";
			while(!feof($fp)) {
				$strLine .= fgets($fp, 4096);
			}
		}

		$name_result = substr($strLine,strpos($strLine,"result value=")+13,1);
		if ($name_result==1 && strlen($file_jumin)>0) {
			file_put_contents($file_jumin,"OK");
		}

		if($name_result==1 || $name_result==2 || $name_result==4) {
			if(file_exists($file_denyY)) {
				$temp = file_get_contents($file_denyY);
				if (strlen($temp)==0) $temp=0;

				$filetime = filemtime($file_denyY);
				$timegan=(time()-$filetime);
				$temp ++;
			} else {
				$temp=1;
			}
			file_put_contents($file_denyY,$temp);
		}
	}
	
	if($name_result!=1) {
?>
		<html>
		<head>
			<title>실명인증</title>
			<style>
			td {font-family:Tahoma;color:666666;font-size:9pt;}
			tr {font-family:Tahoma;color:666666;font-size:9pt;}
			BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

			A:link{color:#5A595A;text-decoration:none;}
			A:visited{color:#5A595A;text-decoration:none;}
			A:active{color:#5A595A;text-decoration:none;}
			A:hover{color:#5A595A;text-decoration:underline;}
			</style>
		</head>
		<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" background="<?=$Dir.AdultDir?>images/errobg.gif">
		<table cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td width="100%" height="100%" align="center">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><IMG SRC="<?=$Dir.AdultDir?>images/erro_top.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir.AdultDir?>images/erro_07bg.gif">
				<table align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="<?=$Dir.AdultDir?>images/erro_01.gif" border="0"><br></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-left:30px;line-height:110%;">
<?php
						if($name_result==2) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">입력하신 성명과 주민번호가 일치하지 않습니다.</font><br>다시한번 정확히 입력하여 주시기 바랍니다.</span></b>";
						else if($name_result==3) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">입력하신 성명과 주민번호가 서울신용평가에서 제공하는 실명DB에 데이터가 없습니다.</font><br>다시한번 정확히 입력하여 주시기 바랍니다.</span></b>";
						else if($name_result==4) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">주민번호 오류입니다.</font><br>다시한번 정확히 입력하여 주시기 바랍니다.</span></b>";
						else if($name_result==5) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">현재 서울신용평가 데이터베이스와 연결이 원활하지 못합니다.</font><br>잠시 후 다시 접속하세요. (서울신용평가정보 문의 : 02-512-1300)</span></b>";
						else if($name_result==7) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">고객님은 명의도용방지를 신청하셨습니다.</font><br><a href=http://www.siren24.com target=_blank><u>www.siren24.com</u></a>에서 일시해제하셔야 합니다.</span></b>";
						else if($name_result!=1) echo "<b><span style=\"font-size:9pt; letter-spacing:-0.5pt;\"><font color=\"#FF4518\">현재 서울신용평가 데이터베이스와 연결이 원활하지 못합니다.</font><br>잠시 후 다시 접속하세요. (문의 : 02-512-1300)</span></b>";
?>
						</td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td style="padding-left:30px;">
<?php
						if($name_result==2) echo "<font color=\"#FF4518\"><b>성명 불일치</b></span>";
						else if($name_result==3) echo "<font color=\"#FF4518\"><b>DB 미존재</b></span>";
						else if($name_result==4) echo "<font color=\"#FF4518\"><b>주민번호 오류</b></span>";
						else if($name_result==5) echo "<font color=\"#FF4518\"><b>DB 연결 오류</b></span>";
						else if($name_result==7) echo "<font color=\"#FF4518\"><b>명의도용방지</b></span>";
						else if($name_result!=1) echo "<font color=\"#FF4518\"><b>DB 연결 오류</b></span>";
?>
						<a href="JavaScript:history.go(<?=$history?>)"><IMG SRC="<?=$Dir.AdultDir?>images/erro_04.gif" border="0" hspace="5" align="absmiddle"></a></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style="line-height:110%;"><img src="<?=$Dir.AdultDir?>images/erro_line.gif" border="0" vspace="10"></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-left:30px;line-height:110%;"><span style="font-size:8pt; letter-spacing:-0.5pt;"><font color="#666666" face="돋움"><b>성명정정 
						문의</b> : 서울신용평가정보(주)<br>TEL 02)512-1300 
						FAX 02)3449-1678 (운영시간 : 09:30~17:30)<br>귀하의 
						신분증(주민등록증 등)을 복사하여 연락처를 
						기재하여 팩스로 보내주십시오.<br>24시간 
						이내에 정정 처리를 해 드리겠습니다.</font></span></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><IMG SRC="<?=$Dir.AdultDir?>images/erro_down.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</body>
		</html>
<?php
		exit;
	}
}

function getNameCheck($name, $jumin1, $jumin2, $id, $pw) {
	global $Dir;
	$name_result=0;
	$file_jumin = $Dir.DataDir."cache/name/CACHE_$name"."_".md5("$jumin1$jumin2");
	$file_denyY = $Dir.DataDir."cache/name/CACHE_Y_".$_SERVER['REMOTE_ADDR'];
	$file_denyN = $Dir.DataDir."cache/name/CACHE_N_".$_SERVER['REMOTE_ADDR'];

	if(strpos(" ".$file_jumin,"..")>0) $file_jumin=str_replace("..","",$file_jumin);
	if(strpos(" ".$file_denyY,"..")>0) $file_denyY=str_replace("..","",$file_denyY);
	if(strpos(" ".$file_denyN,"..")>0) $file_denyN=str_replace("..","",$file_denyN);

	include($Dir."lib/cache_namecheck.php");

	if ($name_result==119) {
		return "[".$_SERVER['REMOTE_ADDR']."] IP에서 다량 실명인증 시도가 있습니다.\\n\\n실명인증 서비스 상점을 보호하기위해 로그기록하고, 실명인증을 거부합니다.";
		exit;
	}

	if ($name_result!=1) {
		$host=$_SERVER['HTTP_HOST'];
		$port=80;
		$path="/".RootPath.FrontDir."getnamecheck.php";
		$name = str_replace(" ","",$name);

		$query="id=".$id."&pw=".$pw."&name=".$name."&jumin1=".$jumin1."&jumin2=".$jumin2;
		$fp = fsockopen($host, $port, $errno, $errstr, 5);
		if(!$fp) {
			$strLine = " result value=5";
			flush();
		} else {
			$cmd = "POST $path HTTP/1.0\n";
			fputs($fp, $cmd);
			$cmd = "Host: $host\n";
			fputs($fp, $cmd);
			$cmd = "Content-type: application/x-www-form-urlencoded\n";
			fputs($fp, $cmd);
			$cmd = "Content-length: " . strlen($query) . "\n";
			fputs($fp, $cmd);
			$cmd = "Connection: close\n\n";
			fputs($fp, $cmd);
			fputs($fp, $query);
			flush();
			while($currentHeader = fgets($fp,4096)) {
				if($currentHeader == "\r\n") {
					break;
				}
			}

			$strLine = "";
			while(!feof($fp)) {
				$strLine .= fgets($fp, 4096);
			}
		}

		$name_result = substr($strLine,strpos($strLine,"result value=")+13,1);
		if($name_result==1 && strlen($file_jumin)>0) {
			file_put_contents($file_jumin,"OK");			
		}

		if ($name_result==1 || $name_result==2 || $name_result==4) {
			if(file_exists($file_denyY)) {
				$temp = file_get_contents($file_denyY);
				if (strlen($temp)==0) $temp=0;

				$filetime = filemtime($file_denyY);
				$timegan=(time()-$filetime);
				$temp ++;
			} else {
				$temp=1;
			}
			file_put_contents($file_denyY,$temp);
		}
	}

	if($name_result!=1) {
		if($name_result==2) return "회원님이 입력하신 성명과 주민번호가 일치하지 않습니다.\\n\\n다시한번 정확히 입력하여 주시기 바랍니다.";
		else if($name_result==3) return "입력하신 성명과 주민번호가 서울신용평가에서 제공하는 실명DB에 데이터가 없습니다.\\n\\n다시한번 정확히 입력하여 주시기 바랍니다.";
		else if($name_result==4) return "주민번호 오류입니다. 다시한번 정확히 입력하여 주시기 바랍니다.";
		else if($name_result==5) return "서울신용평가 데이터베이스와 연결이 안됩니다.잠시 후 다시 접속하세요.\\n\\n서울신용평가정보 문의 : 02)512-1300";
		else if($name_result==7) return "고객님은 명의도용방지를 신청하셨습니다.\\n\\www.siren24.com에서 일시해제하셔야 합니다.";
		else if($name_result!=1) return "서울신용평가 데이터베이스와 연결이 안됩니다. 잠시 후 다시 접속하세요.\\n\\n서울신용평가정보 문의 : 02)512-1300";
	} else {
		return "";
	}
}

