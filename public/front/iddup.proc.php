<?php
/********************************************************************* 
// 파 일 명		: iddup.proc.php 
// 설     명		: 아이디, 닉네임, 이메일 체크
// 상세설명	: 아이디, 닉네임, 이메일 유무를 체크함
// 작 성 자		: hspark
// 수 정 자		: 2015.10.29 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보를 설정한다.
#---------------------------------------------------------------
Header("Content-type: text/html; charset=utf-8");

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$access_type	= $_GET['access_type'];
if ($access_type == 'mobile') {
	$mem_id= $_GET['mem_id'];
} else {
	$mem_id=$_ShopInfo->getMemid();
}

if($_GET[mode] == 'id'){
	$id=$_GET["id"];
	$code="0";
	if(strtolower($id)=="admin") {
		$message="이미 사용중 입니다.";
	} else {
		$sql = "SELECT id FROM tblmember WHERE id='{$id}'";
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$message="이미 사용 중입니다.";
		} else {
			$sql = "SELECT id FROM tblmemberout WHERE id='{$id}' ";
			$result2 = pmysql_query($sql,get_db_conn());
			if($row2=pmysql_fetch_object($result2)) {
				$message="이미 사용 중입니다.";
			} else {
				$sql3 = "SELECT id FROM tblmemberlog WHERE id='{$id}' AND type='out' limit 1 ";
				$result3 = pmysql_query($sql3,get_db_conn());
				if($row3=pmysql_fetch_object($result3)) {
					$message="이미 사용 중입니다.";
				} else {
					$message="사용 가능합니다.";
					$code=1;
				}
			}
			pmysql_free_result($result2);
		}
		pmysql_free_result($result);
	}
}else if($_GET[mode] == 'nickname'){
	$nickname=$_GET["nickname"];
	if($nickname){
		$code="0";
		$sql = "SELECT nickname FROM tblmember WHERE nickname='{$nickname}'";
		if($mem_id){
			$sql.=" and id!='".$mem_id."'";
		}
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$message="닉네임이 중복되었습니다.";
		} else {
			$message="사용가능한 닉네임 입니다.";
			$code=1;
		}
	}else{
		$message="닉네임을 입력해주세요.";
	}
	pmysql_free_result($result);
}else if($_GET[mode] == 'email'){
	$email=$_GET["email"];
	$code="0";
	if(!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$)", $email)) {
		$message="잘못된 이메일 형식입니다.1";
	} else {
		//$sql = "SELECT email FROM tblmember WHERE email='{$email}' or mb_facebook_email = '{$email}') ";
		$sql = "SELECT email FROM tblmember WHERE email='{$email}' ";
		if($mem_id){
			$sql.=" and id!='".$mem_id."'";
		}
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$message="이미 사용 중입니다.";
		} else {
			$message="사용 가능합니다.";
			$code=1;
		}
		pmysql_free_result($result);
	}
}else if($_GET[mode] == 'rec_email'){ // 추천인 이메일 검색
	$email=$_GET["email"];
	$code="0";
	if(!ereg("(^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$)", $email)) {
		$message=" 추천인 이메일이 잘못된 이메일 형식입니다.";
	} else {
		$sql = "SELECT id, email FROM tblmember WHERE (email='{$email}' or mb_facebook_email = '{$email}') AND member_out != 'Y' ";

		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$message="";
			$code=$row->id;
		} else {
			$message="추천인 이메일이 존재하지 않습니다.";
		}
		pmysql_free_result($result);
	}
}else if($_GET[mode] == 'passwd'){ // 기존 비밀번호 체크
	$passwd	= $_GET["passwd"];
	$shadata	= "*".strtoupper(SHA1(unhex(SHA1($passwd))));
	$code="0";
	$sql = "SELECT id FROM tblmember WHERE passwd='{$shadata}' and id='".$mem_id."'";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$message="";
		$code=1;
	} else {
		$message="비밀번호를 잘못 입력하셨습니다.";
	}
	pmysql_free_result($result);
}else if($_GET[mode] == 'pwd_change'){ // 기존 비밀번호 체크 및 비밀번호 변경
	$old_passwd	= $_GET["old_passwd"];
	$passwd		= $_GET["passwd"];
	$shadata0	= "*".strtoupper(SHA1(unhex(SHA1($old_passwd))));
	$code="0";
	$sql = "SELECT id FROM tblmember WHERE passwd='{$shadata0}' and id='".$mem_id."'";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$message="";
		$code=1;	
		$shadata	= "*".strtoupper(SHA1(unhex(SHA1($passwd))));					
		$up_sql = "UPDATE tblmember SET passwd='".$shadata."' ";
		$up_sql.= "WHERE id='".$mem_id."'";

		pmysql_query($up_sql,get_db_conn());
	} else {
		$message="비밀번호를 잘못 입력하셨습니다.";
	}
	pmysql_free_result($result);
}else if($_GET[mode] == 'erp_mem_chk'){ // erp 회원체크
	$name	= iconv('utf-8','euc-kr',$_GET["name"]);
	if (!$name) $name	= $_GET["name"];
	$mobile	= $_GET["mobile"];	
	list($old_id, $old_name, $old_mobile) = pmysql_fetch(" SELECT id, name, mobile FROM tblmember WHERE replace(mobile,'-','') = '{$mobile}' AND member_out='N'");
	//echo " SELECT id, name, mobile FROM tblmember WHERE name = '".iconv('euc-kr','utf-8',$_GET["name"])."' AND mobile = '{$mobile}' ";

	if($old_id){
		$code="99";
		$message="이미 가입된 회원입니다.";
	} else {
		list($old_name, $old_mobile) = pmysql_fetch(" SELECT name, mobile FROM tblmember WHERE id = '{$mem_id}' AND member_out='N'");
		$old_mobile	= str_replace("-","",$old_mobile);

		if($mem_id && $old_name == $_GET["name"] && $old_mobile == $mobile){
			$code="99";
			$message="이미 가입된 회원입니다.";
		} else {
			$meberinfo	= getErpMeberinfo($name, $mobile);
			$code		= $meberinfo['p_err_code'];
			$message	= $code == '0'?$meberinfo['p_data']:$meberinfo['p_err_text'];
		}
	}

	//$message	= "{$mem_id}/{$old_name}}/".$_GET["name"]."/{$old_mobile}/{$mobile}";
}else if($_GET[mode] == 'erp_mem_chk2'){ // erp 회원체크2
	$name	= iconv('utf-8','euc-kr',$_GET["name"]);
	if (!$name) $name	= $_GET["name"];
	$mobile	= $_GET["mobile"];	
	list($old_id, $old_name, $old_mobile) = pmysql_fetch(" SELECT id, name, mobile FROM tblmember WHERE replace(mobile,'-','') = '{$mobile}' AND id != '{$mem_id}' AND member_out='N' ");
	//echo " SELECT id, name, mobile FROM tblmember WHERE name = '".iconv('euc-kr','utf-8',$_GET["name"])."' AND mobile = '{$mobile}' ";

	if($old_id){
		$code="99";
		$message="이미 가입된 회원입니다.";
	} else {
		list($old_name, $old_mobile) = pmysql_fetch(" SELECT name, mobile FROM tblmember WHERE id != '{$mem_id}' AND member_out='N' ");
		$old_mobile	= str_replace("-","",$old_mobile);

		if($mem_id && $old_name == $_GET["name"] && $old_mobile == $mobile){
			$code="99";
			$message="이미 가입된 회원입니다.";
		} else {
			$meberinfo	= getErpMeberinfo($name, $mobile);
			$code		= $meberinfo['p_err_code'];
			$message	= $code == '0'?$meberinfo['p_data']:$meberinfo['p_err_text'];
		}
	}

	//$message	= "{$mem_id}/{$old_name}}/".$_GET["name"]."/{$old_mobile}/{$mobile}";
}else if($_GET[mode] == 'erp_emp_chk'){ // erp 임직원체크
	$name	= iconv('utf-8','euc-kr',$_GET["name"]);
	if (!$name) $name	= $_GET["name"];
	$emp_id	= $_GET["emp_id"];	

	$meberinfo	= getErpEmpMeberinfo($name, $emp_id);
	$code		= $meberinfo['p_err_code'];
	if ($code == '0') {
		$message	= $meberinfo['p_data'];
		$sql = "SELECT erp_emp_id, name FROM tblmember WHERE erp_emp_id='{$emp_id}' AND member_out='N'";
		if($mem_id){
			$sql.=" and id!='".$mem_id."'";
		}
		$result = pmysql_query($sql,get_db_conn());
		if ($row=pmysql_fetch_object($result)) {
			$code="-2";
			$message="이미 가입된 회원입니다.";
		}
	} else {
		$message	= $meberinfo['p_err_text'];
	}
}

$message	= $_GET[mode]=='emp_chk'?$message:$message;
$resultData = array("msg"=>$message, "code"=>$code);
echo urldecode(json_encode($resultData));
?>