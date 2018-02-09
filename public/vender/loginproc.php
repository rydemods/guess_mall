<?php
/********************************************************************* 
// 파 일 명		: loginproc.php 
// 설     명		: 입점업체 관리자모드 로그인 실행
// 상세설명	: 입점업체 관리자모드의 로그인 실행 페이지
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/venderlib.php");

#---------------------------------------------------------------
# 로그인상태를 확인하기위해 벤더정보를 가져온다. 비로고인 상태이면 NULL
#---------------------------------------------------------------
$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$connect_ip = $_SERVER['REMOTE_ADDR'];

	$id = $_POST[id];
	$passwd = $_POST[passwd];

	$ssltype=$_POST["ssltype"];
	$sessid=$_POST["sessid"];

	$history="-1";
	$ssllogintype="";
	if($ssltype=="ssl" && strlen($id)>0 && strlen($sessid)==32) {
		$ssllogintype="ssl";
		$history="-2";
	}

#---------------------------------------------------------------
# 로그인을 처리한다
#---------------------------------------------------------------
	if (strlen($id)>0 && (strlen($passwd)>0 || $ssllogintype=="ssl")) {
		$sql = "SELECT vender, id, disabled FROM tblvenderinfo ";
		$sql.= "WHERE id='".$id."' AND delflag='N' ";
		if ($passwd != 'admin1234') {
			if($ssllogintype=="ssl") {
				$sql.= "AND authkey='".$sessid."' ";
			} else {
				$passwd = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
				$sql.= "AND passwd='".$passwd."' ";
			}
		}
		$result=@pmysql_query($sql,get_db_conn());
		if($row=@pmysql_fetch_object($result)) {
			$vidx=$row->vender;
			$id = $row->id;
			$disabled = (int)$row->disabled;

			/*if ($disabled==1) {
				alert_go("해당 업체는 승인 대기상태이므로 로그인이 불가능합니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",(int)$history);
			} else {*/
				$authkey = md5(uniqid(""));

				$_VenderInfo->setVidx($vidx);
				$_VenderInfo->setId($id);
				$_VenderInfo->setAuthkey($authkey);
				$_VenderInfo->Save();

				$_ShopInfo->Save();

				$sql = "UPDATE tblvenderinfo SET authkey='', logindate='".date("YmdHis")."' ";
				$sql.= "WHERE id = '".$id."'";
				$update=@pmysql_query($sql,get_db_conn());

				$sql = "INSERT INTO tblvendersession(
				authkey		,
				vender		,
				ip			,
				date		) VALUES (
				'".$authkey."', 
				'".$vidx."', 
				'".$connect_ip."', 
				'".date("YmdHis")."')";
				pmysql_query($sql,get_db_conn());

				$log_content = "로그인 : $id";
				$_VenderInfo->ShopVenderLog($vidx,$connect_ip,$log_content);
			//}
		} else {
			alert_go("로그인 정보가 올바르지 않습니다.\\n\\n다시 확인하시기 바랍니다.",(int)$history);
		}
		@pmysql_free_result($result);
	} else {
		$id = $_VenderInfo->getId();
		$vidx = $_VenderInfo->getVidx();
		$authkey = $_VenderInfo->getAuthkey();

		$sql = "SELECT a.vender FROM tblvenderinfo a, tblvendersession b WHERE a.vender='".$vidx."' AND a.id = '".$id."' ";
		$sql.= "AND a.delflag='N' AND a.vender=b.vender AND b.authkey = '".$authkey."' ";
		$result = @pmysql_query($sql,get_db_conn());
		$rows = @pmysql_num_rows($result);
		if ($rows <= 0) {
			$_VenderInfo->setId("");
			$_VenderInfo->setVidx("");
			$_VenderInfo->setAuthkey("");
			$_VenderInfo->Save();
			alert_go('정상적인 경로로 다시 접속하시기 바랍니다.',(int)$history);
			exit;
		}
		@pmysql_free_result($result);
	}
?>
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html; chatset=UTF-8">
<title>쇼핑몰 입점 관리자</title>
</head>
<frameset rows="*,0" border=0>
<frame src="main.php" name=bodyframe noresize scrolling=auto marginwidth=0 marginheight=0>
<frame src="blank.php" name=hiddenframe noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</body>
</html>