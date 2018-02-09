<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	$email	= $_GET['email'];

	$sql = "SELECT * FROM tblmember WHERE email='{$email}' or mb_facebook_email='{$email}'";

	$result=pmysql_query($sql,get_db_conn());

	if(pmysql_num_rows($result)==0) {
		$message = '입력하신 정보와 일치하는 회원이 없습니다.';
	} else {
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		
		if($row->member_out=="Y") {	//탈퇴한 회원
			$message = "입력하신 정보는 탈퇴한 회원입니다.";
		} else {

			$passwd=substr(rand(0,9999999),0,8);

			 $shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
			
			$sql = "UPDATE tblmember SET passwd='".$shadata."' ";
			$sql.= "WHERE id='{$row->id}'";
			pmysql_query($sql,get_db_conn());

			$mess2=$row->email."로 메일을 ";

			SendPasswordMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->info_email, $row->email, $row->name, $row->id, $passwd);

			$message = "{$mess2} 발송하였습니다.";
		}
	}

	$resultData = array("msg"=>urlencode($message));
	echo urldecode(json_encode($resultData));
?>