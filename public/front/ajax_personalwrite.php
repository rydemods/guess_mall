<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_mdata=$row;
	}
	pmysql_free_result($result);

	$ip=$_SERVER["REMOTE_ADDR"];
	$date=date("YmdHis");

	$up_email = $_POST['up_email'];
	$up_subject = $_POST['up_subject'];
	$up_content = $_POST['up_content'];
	$head_title = $_POST['head_title'];
	$hp = $_POST['hp0']."-".$_POST['hp1']."-".$_POST['hp2'];
	$chk_sms = $_POST['chk_sms'];
	$chk_mail = $_POST['chk_mail'];
	if(!$chk_sms) $chk_sms = 'N';
	if(!$chk_mail) $chk_mail = 'N';
	$sql = "
				INSERT INTO
					tblpersonal
					(
						id,
						name,
						email,
						ip,
						subject,
						date,
						content	,
						head_title,
						\"HP\",
						chk_sms,
						chk_mail
					)
				VALUES
					(
						'{$_mdata->id}',
						'{$_mdata->name}',
						'{$up_email}',
						'{$ip}',
						'{$up_subject}',
						'{$date}',
						'{$up_content}',
						{$head_title},
						'{$hp}',
						'{$chk_sms}',
						'{$chk_mail}'
					)
	";
	if(pmysql_query($sql,get_db_conn())) {

		//이메일 보내기
		if ($chk_sms == 'Y') {
		}

		//SMS 보내기
		if ($chk_mail == 'Y') {
		}

		$message = "정상적으로 등록되었습니다.";
	} else {
		$message = "오류가 발생하였습니다.";
	}

	$resultData = array("msg"=>urlencode($message));
	echo urldecode(json_encode($resultData));
?>