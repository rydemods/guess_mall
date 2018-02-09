<?php 
session_start();
/**
* 
* 아이디 및 비밀번호 찾기 체크 페이지
* 
*/

Header("Content-type: text/html; charset=utf-8");

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$message	= "";
$code		= "0";

$mode		= $_REQUEST['mode'];
$cert_type	= $_REQUEST['cert_type']; // mobile : 모바일, email : 이메일, ipin : 아이핀
$access_type	= $_REQUEST['access_type']; // 접근 위치 - m : 모바일웹에서 접근

#####실명인증 결과에 따른 분기
$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));

if($CertificationData->realname_check || $CertificationData->ipin_check){
	if(($cert_type != 'email' && $_SESSION[ipin][dupinfo]) || ($cert_type == 'email')){

		$u_name	= $_REQUEST['name'];
		$u_id			= $_REQUEST['id'];
		$u_email	= $_REQUEST['email'];
		$u_mobile	= $_REQUEST['mobile'];
		$u_ch_pwd	= $_REQUEST['ch_pwd'];



		$qry_where	= "";
		$dupinfo_yn	= '';
		if ($u_name) $qry_where .= "AND name = '{$u_name}' ";
		if ($u_id) $qry_where .= "AND id = '{$u_id}' ";
		if ($u_email) $qry_where .= "AND email = '{$u_email}' ";
		if ($u_mobile) $qry_where .= "AND replace(mobile,'-','') = replace('{$u_mobile}','-','') and auth_type='' ";
		if ($cert_type != 'email') {

			if ($_SESSION[ipin][dupinfo]) {
				$dupinfo_yn	= 'Y';
				$qry_where .= "AND dupinfo='{$_SESSION[ipin][dupinfo]}' ";
			} else {
				$dupinfo_yn	= 'N';
				$qry_where .= "AND dupinfo='' ";
			}
		}

		if($mode == "findid"){
			if ($qry_where) {
				list($mem_id, $mem_name, $mem_mail, $mem_date)=pmysql_fetch("select id, name, email, date from tblmember where 1=1 {$qry_where}");

				if(!$mem_id){
					if ($cert_type == "mobile") {
						$message	= "휴대폰 인증된 아이디가 없습니다";
					} else {
						$message	= "입력하신 정보와 일치하는 아이디가 없습니다.";
					}
				} else {

					$code		= "1";
					if ($access_type == 'mobile') {
						//$message	= substr($mem_id,0,-4)."****";
						$message	= $mem_id;
					} else {
						//$mem_id_exp	= explode("@", $mem_id);
						//$mem_id=substr($mem_id_exp[0],0,-4)."****"."@".$mem_id_exp[1];
						if ($access_type == 'pc_store') {
							$message	= "가입하신 아이디는 <span class='find_pw_memid'>".$mem_id."</span>";
						} else if ($access_type == 'm_store') {
// 							$message	= "가입하신 아이디는<br> <span class='point-color'>".$mem_id."</span> 입니다.";
							$message	= $mem_id;
						} else {
							//$message	= "<p>회원님의 아이디는</p><p><em>".$mem_id."</em> 입니다</p>";
							$message = $mem_id;
						}
					}
				}
			} else {
				$message	= "인증 실패. 관리자에게 문의해주세요.";
			}
		} else if ($mode == "findpw" || $mode == "findpw_change"){
			if ($qry_where) {
				list($mem_id, $mem_name, $mem_mail, $mem_date)=pmysql_fetch("select id, name, email, date from tblmember where 1=1 {$qry_where}");
				if(!$mem_id){
					$message	= "일치하는 회원정보가 없습니다.";
				} else {
					$code		= "1";
					if ($cert_type == "mobile" || $cert_type == "ipin") {
						if($dupinfo_yn=='Y') {
							if ($mode == "findpw") {
								$mem_id_exp	= explode("@", $mem_id);
								$change_mem_id=substr($mem_id_exp[0],0,-4)."****"."@".$mem_id_exp[1];
								$message	= $mem_id;
							} else if ($mode == "findpw_change") {
								$message	= "새로운 비밀번호 등록이 완료되었습니다.";	

								$shadata	= "*".strtoupper(SHA1(unhex(SHA1($u_ch_pwd))));					
								$sql = "UPDATE tblmember SET passwd='".$shadata."' ";
								$sql.= "WHERE id='{$mem_id}' ";

								pmysql_query($sql,get_db_conn());
							}
						} else {
							$code		= "0";
							$message	= "비밀번호는 휴대폰번호(-제외)로 로그인 하시기 바랍니다.";
						}
					} else if ($cert_type == "email") {
						//$message	= "임시 비밀번호가 발송되었습니다.<br>발급된 비밀번호는 임시 비밀번호이므로 로그인 후 반드시 변경하시기 바랍니다.";
						//$mem_mail_arr	= explode("@", $mem_mail);
						//$message	= "회원가입시 등록하신 @".$mem_mail_arr[1]." 이메일로 발송되었습니다.";	
						if ($access_type == 'pc_store') {
							$message	= "새로운 비밀번호가 전송되었습니다.";			
						} else if ($access_type == 'm_store') {
							$message	= "<span class='point-color'>새로운 비밀번호가 전송되었습니다.</span>";			
						} else {						
							$message	= "회원가입시 등록하신 메일 및 휴대폰번호로 임시 비밀번호를 발송해드렸습니다.";	
						}

						$passwd	= substr(rand(0,9999999),0,8);
						$shadata	= "*".strtoupper(SHA1(unhex(SHA1($passwd))));					
						$sql = "UPDATE tblmember SET passwd='".$shadata."' ";
						$sql.= "WHERE id='{$mem_id}' ";

						if(pmysql_query($sql,get_db_conn())){
							//이메일로 보낸다.
							SendPassMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->info_email, $mem_mail, $mem_name, $mem_id, $passwd);

							//SMS 발송
							sms_autosend( 'mem_passwd', $mem_id, '', $passwd );

							//SMS 관리자 발송
							sms_autosend( 'admin_passwd', $mem_id, '', $passwd );
						}
					}
				}
			} else {
				$message	= "인증 실패. 관리자에게 문의해주세요.";
			}
		}
	} else {
		$message	= "인증 실패. 관리자에게 문의해주세요.";
	}
}

$resultData = array("msg"=>urlencode($message), "code"=>$code, "changeid"=>urlencode($change_mem_id), "now_find_type"=>$mode);
echo urldecode(json_encode($resultData));
?>
