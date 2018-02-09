<?php
/********************************************************************* 
// 파 일 명		: member_join.php 
// 설     명		: 회원가입 정보등록
// 상세설명	: 회원가입시 정보를 등록
// 작 성 자		: hspark
// 수 정 자		: 2015.10.27 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
	session_start();

#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	/*if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}*/
	
	// 가입금 축하적립금 사용유무 체크
	$reserve_join				= (int)$_data->reserve_join;

	//추천인 관련 셋팅 (2015.12.21 - 김재수 추가)
	$recom_ok					= $_data->recom_ok;							// 추천인 사용유무
	$recom_addreserve		= (int)$_data->recom_addreserve;		// 추천시 추가로받을 적립금 금액
	$recom_memreserve	= (int)$_data->recom_memreserve;		// 추천인에게 주는 적립금 금액
	$recom_limit				= $_data->recom_limit;						// 추천수 제한
	if(ord($recom_limit)==0) $recom_limit=9999999;						// 0 이면 무한으로 받을수 있도록 처리

	if($_data->group_code){
		$group_code=$_data->group_code;
	}else{
		$group_code="";	
	}

	$type					= $_POST["type"];	

	if($type=="") { //가입폼일 경우 이용약관을 불러옴.
		$sql="SELECT agreement,privercy FROM tbldesign ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$agreement=$row->agreement;
		$privercy_exp=@explode("=", $row->privercy);
		$privercy=$privercy_exp[1];
		//exdebug($privercy);
		pmysql_free_result($result);

		if(ord($agreement)==0) {
			$buffer = file_get_contents($Dir.AdminDir."agreement.txt");
			$agreement=$buffer;
		}

		$pattern=array("[SHOP]","[COMPANY]");
		$replace=array($_data->shopname, $_data->companyname);
		$agreement = str_replace($pattern,$replace,$agreement);
		$agreement = preg_replace('/[\\\\\\\]/',"",$agreement);
	}

	if($type=="insert") { // 등록시	

		$onload="";

		$email=trim($_POST["email"]);	
		$passwd1=$_POST["passwd1"];
		$passwd2=$_POST["passwd2"];
		$rec_id=$_POST["rec_id"];
		$news_mail_yn=$_POST["news_mail_yn"];
		$news_sms_yn=$_POST["news_sms_yn"];

		$id	= "";
		
		for ($i = 0; $i < 3; $i++) {
			if (!$id) {
				
				$onload="";

				$u_id		= "WEB_".uniqid('');
				
				$u_sql		= "SELECT id FROM tblmember WHERE id='{$u_id}' ";
				$u_result	= pmysql_query($u_sql,get_db_conn());

				if($u_row = pmysql_fetch_object($u_result)) {
					$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
				}
				pmysql_free_result($u_result);

				$u_sql		= "SELECT id FROM tblmemberout WHERE id='{$u_id}' ";
				$u_result	= pmysql_query($u_sql,get_db_conn());

				if($u_row = pmysql_fetch_object($u_result)) {
					$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
				}
				pmysql_free_result($u_result);

				if(!$onload) {
					$id	= $u_id;
					$onload="";
				}
			}
		}

		if ($id	 == "") {
			$onload="가입에 실패하였습니다.\\n\\n다시 해당정보를 입력하시고 가입해 주시기 바랍니다.";
		}

		$tmp_email	= explode("@",$email);
		$name			= cut_str($tmp_email[0],4,"");
		$strlen_name	= strlen($name);

		for($i = $strlen_name; $i < 10; $i++){
			$name .= "*";
		}
		$nickname		= $name;
		
		// 추천인 아이디가 있을경우 다시한번 체크한다. (2015.12.21 - 김재수)
		if($recom_ok=="Y" && ord($rec_id)) {
			$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE id='".trim($rec_id)."' AND member_out!='Y' ";
			$rec_result = pmysql_query($sql,get_db_conn());
			$rec_row = pmysql_fetch_object($rec_result);
			$rec_num = $rec_row->cnt;
			pmysql_free_result($rec_result);

			$rec_cnt=0;
			$sql = "SELECT rec_cnt FROM tblmember_reccnt WHERE rec_id='".trim($rec_id)."'";
			$rec_result = pmysql_query($sql,get_db_conn());
			if($rec_row = pmysql_fetch_object($rec_result)) {
				$rec_cnt = (int)$rec_row->rec_cnt;
			}
			pmysql_free_result($rec_result);
		}

		/*if(isdev()) {
			//var_dump($_POST);
			echo $recom_ok."<br>";
			echo $rec_num."<br>";
			echo $rec_cnt."<br>";
			echo $recom_limit."<br>";
			echo ord($rec_id);
			exit;
		}*/

		if(ord($onload)) {

		} else if(strlen(trim($email))==0) {
			$onload="이메일을 입력하세요.";
		} else if(!ismail($email)) {
			$onload="이메일 입력이 잘못되었습니다.";
		
		} else {
			if(!$onload) {
				if(!$onload) {
					$sql = "SELECT email FROM tblmember WHERE email='{$email}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$onload="이메일이 중복되었습니다.\\n\\n다른 이메일을 사용하시기 바랍니다.";
					}
					pmysql_free_result($result);
				}

				if(!$onload) {
					$sql = "SELECT mb_facebook_email FROM tblmember WHERE mb_facebook_email='{$email}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$onload="이메일이 중복되었습니다.\\n\\n다른 이메일을 사용하시기 바랍니다.";
					}
					pmysql_free_result($result);
				}
				
				if(!$onload) {
					//insert					

					if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
						$news_yn="Y";
					} else if($news_mail_yn=="Y") {
						$news_yn="M";
					} else if($news_sms_yn=="Y") {
						$news_yn="S";
					} else {
						$news_yn="N";
					}

					$confirm_yn="Y";
					$ip = $_SERVER['REMOTE_ADDR'];
					$date=date("YmdHis");
					$date2=date("Y-m-d");
					$mb_type	= "web";

					 $shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd1))));

					(get_session('rf_url_id')) ? $rf_url_id	= get_session('rf_url_id') : $rf_url_id	= '';
					$mb_referrer1	= $rf_url_id;
					$mb_referrer2	= $rf_url_id;
					
					BeginTrans();
					
					$sql = "INSERT INTO tblmember(id) VALUES('{$id}')";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblmember SET ";
					$sql.= "id			= '{$id}', ";
					$sql.= "passwd		= '".$shadata."', ";
					$sql.= "name		= '{$name}', ";
					$sql.= "nickname	= '{$nickname}', ";
					$sql.= "email		= '{$email}', ";
					$sql.= "news_yn		= '{$news_yn}', ";
					$sql.= "joinip		= '{$ip}', ";
					$sql.= "ip			= '{$ip}', ";
					$sql.= "reserve		= '{$reserve_join}', ";
					$sql.= "date		= '{$date}', ";
					$sql.= "mb_nick_date		= '{$date2}', ";
					$sql.= "mb_email_certify		= '{$date}', ";
					$sql.= "mb_type		= '{$mb_type}', ";
					$sql.= "mb_referrer1		= '{$mb_referrer1}', ";
					$sql.= "mb_referrer2		= '{$mb_referrer2}', ";
					
					// 추천인 아이디가 있을경우 등록한다. (2015.12.21 - 김재수)
					if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
						$sql.= "rec_id	= '{$rec_id}', ";
					}

					if(ord($group_code)) {
						$sql.= "group_code='{$group_code}', ";
					}

					$sql.= "confirm_yn	= '{$confirm_yn}' WHERE id='{$id}'";

					//echo $sql;
					//exit;
					$insert=pmysql_query($sql,get_db_conn());

					if (pmysql_errno()==0) {
						CommitTrans();

						//if($_SERVER["REMOTE_ADDR"] == '218.234.32.102'){
							//가입회원 이전 URL 정보 저장 (2015.11.30 김재수 // 2015.12.04 김재수 수정)
							(get_session('rf_url_address')) ? $rf_url_address	= get_session('rf_url_address') : $rf_url_address	= '';
							(get_session('rf_url_address_type')) ? $rf_url_address_type	= get_session('rf_url_address_type') : $rf_url_address_type	= '';
							if ($rf_url_address && $id) {
								if (!$rf_url_address_type) $rf_url_address_type = "B";
								$sql = "INSERT INTO tblmember_rf(id,rf_url,rf_type,date) VALUES (
								'{$id}', 
								'".urlencode($rf_url_address)."', 
								'{$rf_url_address_type}', 
								'{$date}') ";
								pmysql_query($sql,get_db_conn());
							}
						/*} else {
							//가입회원 이전 URL 정보 저장 (2015.11.30 김재수)
							(get_session('rf_url_address')) ? $rf_url_address	= get_session('rf_url_address') : $rf_url_address	= '';
							if ($rf_url_address && $id) {
								$rf_url_address_arr		= explode("rf=", $rf_url_address);
								$rf_url_address_type	= substr($rf_url_address_arr[1],0,1);
								$rf_url_address_type	= trim($rf_url_address_type);
								if (!$rf_url_address_type) $rf_url_address_type = "B";
								$sql = "INSERT INTO tblmember_rf(id,rf_url,rf_type,date) VALUES (
								'{$id}', 
								'".urlencode($rf_url_address)."', 
								'{$rf_url_address_type}', 
								'{$date}') ";
								pmysql_query($sql,get_db_conn());
							}
						}*/

						// 가입 적립금이 있는지 체크한다. (사용시 변경할 예정)
						if ($reserve_join>0) {
							/*$sql = "INSERT INTO tblreserve(id,reserve,reserve_yn,content,orderdata,date) VALUES (
							'{$id}', 
							{$reserve_join}, 
							'Y', 
							'가입축하 적립금입니다. 감사합니다.', 
							'', 
							'".date("YmdHis",time()-1)."') ";
							$insert = pmysql_query($sql,get_db_conn());*/
							//insert_point($id, $reserve_join, '가입축하 적립금', '@join', $_ShopInfo->id, date("Ymd"), 0);
						}

						//추천인 적립금이 있는지 체크한다.
						if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
							$rec_id_reserve=0;
							$id_reserve=0;
							if($recom_addreserve>0) {
								//SetReserve($id,$recom_addreserve,"추천인 적립금입니다. 감사합니다.");
								insert_point($id, $recom_addreserve, '추천인 적립금', '@recomment_from', $_ShopInfo->id, date("Ymd"), 0);

								$id_reserve=$recom_addreserve;
							}
							if($recom_memreserve>0) {
								//$mess=$id."님이 추천하셨습니다. 감사합니다.";
								//SetReserve($rec_id,$recom_memreserve,$mess);
								insert_point($rec_id, $recom_memreserve, "아이디 ".$email.'님이 추천', '@recomment_to', $_ShopInfo->id, date("Ymd"), 0);

								$rec_id_reserve=$recom_memreserve;
							}

							//추천인을 등록한다.
							if($rec_cnt>0) {	//update
								$sql2 = "UPDATE tblmember_reccnt SET rec_cnt=rec_cnt+1 ";
								$sql2.= "WHERE rec_id='{$rec_id}' ";
							} else {			//insert
								$sql2 = "INSERT INTO tblmember_reccnt(rec_id,rec_cnt,date) VALUES ( 
								'{$rec_id}', 
								'1', 
								'{$date}')";
							}
							pmysql_query($sql2,get_db_conn());
							
							$sql2 = "INSERT INTO tblmember_recinfo(rec_id,id,rec_id_reserve,id_reserve,date) VALUES (
							'{$rec_id}', 
							'{$id}', 
							'{$rec_id_reserve}', 
							'{$id_reserve}', 
							'{$date}') ";
							pmysql_query($sql2,get_db_conn());
						}

						//쿠폰발생 (회원가입시 발급되는 쿠폰)
						if($_data->coupon_ok=="Y") {
							$date = date("YmdHis");
							$sql = "SELECT coupon_code, date_start, date_end FROM tblcouponinfo ";
							$sql.= "WHERE display='Y' AND issue_type='M' ";
							$sql.= "AND (date_end>'".substr($date,0,10)."' OR date_end='')";
							$result = pmysql_query($sql,get_db_conn());

							$sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
							$couponcnt ="";
							$count=0;
							
							while($row = pmysql_fetch_object($result)) {
								if($row->date_start>0) {
									$date_start=$row->date_start;
									$date_end=$row->date_end;
								} else {
									$date_start = substr($date,0,10);
									$date_end = date("Ymd23",strtotime("+".abs($row->date_start)." day"));
								}
								$sql.=" ('{$row->coupon_code}','{$id}','{$date_start}','{$date_end}','{$date}'),";
								$couponcnt="'{$row->coupon_code}',";
								$count++;
							}
							pmysql_free_result($result);
							if($count>0) {
								$sql = rtrim($sql,',');
								pmysql_query($sql,get_db_conn());
								if(!pmysql_errno()) {
									$couponcnt = rtrim($couponcnt,',');
									$sql = "UPDATE tblcouponinfo SET issue_no=issue_no+1 ";
									$sql.= "WHERE coupon_code IN ({$couponcnt})";
									pmysql_query($sql,get_db_conn());
									$msg = "회원 가입시 쿠폰이 발급되었습니다.";
								}
							}
						}

						//가입메일 발송 처리
						if(ord($email)) {
							SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id);
						}

						//가입 SMS 발송 처리
						$sql = "SELECT * FROM tblsmsinfo WHERE (mem_join='Y' OR admin_join='Y') ";
						$result= pmysql_query($sql,get_db_conn());
						if($row=pmysql_fetch_object($result)) {
							$sms_id=$row->id;
							$sms_authkey=$row->authkey;

							$admin_join=$row->admin_join;
							$mem_join=$row->mem_join;
							$msg_mem_join=$row->msg_mem_join;
							
							$pattern=array("[ID]","[NAME]");
							$replace=array($id,$name);
							$msg_mem_join=str_replace($pattern,$replace,$msg_mem_join);
							$msg_mem_join=AddSlashes($msg_mem_join);
							$smsmessage=$name."님이 {$id}로 회원가입하셨습니다.";
							$adminphone=$row->admin_tel;
							if(strlen($row->subadmin1_tel)>8) $adminphone.=",".$row->subadmin1_tel;
							if(strlen($row->subadmin2_tel)>8) $adminphone.=",".$row->subadmin2_tel;
							if(strlen($row->subadmin3_tel)>8) $adminphone.=",".$row->subadmin3_tel;

							$fromtel=$row->return_tel;
							pmysql_free_result($result);

							$mobile=str_replace(" ","",$mobile);
							$mobile=str_replace("-","",$mobile);
							$adminphone=str_replace(" ","",$adminphone);
							$adminphone=str_replace("-","",$adminphone);

							$etcmessage="회원가입 축하메세지(회원)";
							$date=0;
							//if($mem_join=="Y") {
							if(true) {
								$temp=SendSMS($sms_id, $sms_authkey, $mobile, "", $fromtel, $date, $msg_mem_join, $etcmessage);
							}
							
							if($row->sleep_time1!=$row->sleep_time2) {
								$date="0";
								$time = date("Hi");
								if($row->sleep_time2<"12" && $time<=sprintf("%02d59",$row->sleep_time2)) $time+=2400;
								if($row->sleep_time2<"12" && $row->sleep_time1>$row->sleep_time2) $row->sleep_time2+=24;

								if($time<sprintf("%02d00",$row->sleep_time1) || $time>=sprintf("%02d59",$row->sleep_time2)){
									if($time<sprintf("%02d00",$row->sleep_time1)) $day = 0;
									else $day=1;
									
									$date = date("Y-m-d",strtotime("+{$day} day")).sprintf(" %02d:00:00",$row->sleep_time1);
								}			
							}
							$etcmessage="회원가입 축하메세지(관리자)";
							if($admin_join=="Y") {
								$temp=SendSMS($sms_id, $sms_authkey, $adminphone, "", $fromtel, $date, $smsmessage, $etcmessage);
							}
						}

						alert_go("등록되었습니다.\\n{$msg}\\n감사합니다.","/","parent");
						//http://ytn.ajashop.co.kr/front/member_joinend.php?email=ssuya@gmail.com&name=ssuya*****&id=WEB_562f4cd603c20

						//alert_go("등록되었습니다.\\n{$msg}\\n감사합니다.",$Dir.FrontDir."member_joinend.php?name={$name}&id={$id}");
						//echo "<script>location.href='".$Dir.FrontDir."member_joinend.php?name=$name&id=$id'</script>";
					} else {
						RollbackTrans();
						$onload="ID가 중복되었거나 회원등록 중 오류가 발생하였습니다.";
					}
				}
			}
		}
		if(ord($onload)) {
			alert_go($onload,"member_join_popup.php");
		}
	}
?>


<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="">
<meta name="Author" content="">
<meta name="Keywords" content="<?=$_data->shopkeyword?>">
<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?=$_data->shoptitle?></title>
<link href="../css/common.css" rel="stylesheet" type="text/css" />
<!-- 이전 J-QUERY 문제 발생으로 버전업 S(2015.11.23 김재수 추가)-->
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="../js/jquery-migrate-1.1.1.min.js" type="text/javascript"></script>
<!-- 이전 J-QUERY 문제 발생으로 버전업 E(2015.11.23 김재수 추가)-->
<script src="../js/common.js" type="text/javascript"></script>
<script src="../lib/lib.js.php" type="text/javascript"></script>
<?php include_once($Dir.LibDir."analyticstracking.php") ?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function CheckFormData(data) {
	var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
	}		
	if ( count == data.length )
		return(true);
	else
		return(false);
}

function CheckForm() {
	
	form=document.form1;	

	var email = form.email.value;
	if(email.length==0){
		alert("이메일을 입력하세요."); form.email.focus(); return;
	}
	
	if(form.passwd1.value.length==0) {
		alert("비밀번호를 입력하세요."); form.passwd1.focus(); return;
	}

	if(form.passwd1.value.length<6 || form.passwd1.value.length>20) {
		alert("비밀번호는 6자 이상 20자 이하로 입력하셔야 합니다."); form.passwd1.focus(); return;
	}
	if (CheckFormData(form.passwd1.value)==false) {
   		alert("비밀번호는 영문, 숫자를 조합하여 6~20자 이내로 등록이 가능합니다."); form.passwd1.focus(); return;			
   	}

	if(form.passwd1.value!=form.passwd2.value) {
		alert("비밀번호가 일치하지 않습니다."); form.passwd2.focus(); return;
	}	
	
	if (!form.agree.checked) {
		alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		form.agree.focus();
		return;
	}

	var rec_email = form.rec_email.value;

	if(form.rec_email.value.length > 0) {
		$.ajax({ 
			type: "GET", 
			url: "<?=$Dir.FrontDir?>iddup.proc.php", 
			data: "email=" + rec_email + "&mode=rec_email",
			dataType:"json", 
			success: function(data) {
				if (data.code == '0')
				{				
					alert(data.msg);
					return;
				} else {		
					form.rec_id.value	= data.code;
					CheckFormSubmit(form, email);
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
				return;
			}
		}); 
	} else {
		CheckFormSubmit(form, email);
	}
}

function CheckFormSubmit(form, email) {
	if($('#email_check').val() == "0"){		
		$.ajax({ 
			type: "GET", 
			url: "<?=$Dir.FrontDir?>iddup.proc.php", 
			data: "email=" + email + "&mode=email",
			dataType:"json", 
			success: function(data) {
				if (data.code != '1')
				{				
					alert(data.msg);
					return;
				} else {					
					form.type.value="insert";
				<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
					form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
				<?php }?>
					if(confirm("회원가입을 하겠습니까?"))
						form.submit();
					else
						return;
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
				return;
			}
		}); 
	}
}

//페이스북으로 가입시 팝업창으로 띄울수 있도록 추가(2015.10.30 - 김재수)
function facebook_open(url){
	
	form=document.form1;	

	var rec_email			= form.rec_email.value;
	var news_mail_yn	= "";
	var news_sms_yn	= "";

	if (form.news_mail_yn.checked == true)
	{
		news_mail_yn	= form.news_mail_yn.value;
	}

	if (form.news_sms_yn.checked == true)
	{
		news_sms_yn	= form.news_sms_yn.value;
	}

	//alert(news_mail_yn+"/"+news_sms_yn);

	
	if (!form.agree.checked) {
		alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		form.agree.focus();
		return;
	}

	if(form.rec_email.value.length > 0) {
		$.ajax({ 
			type: "GET", 
			url: "<?=$Dir.FrontDir?>iddup.proc.php", 
			data: "email=" + rec_email + "&mode=rec_email",
			dataType:"json", 
			success: function(data) {
				if (data.code == '0')
				{				
					alert(data.msg);
					return;
				} else {		
					var popup= window.open(url+"?rec_id="+data.code+"&news_mail_yn="+news_mail_yn+"&news_sms_yn="+news_sms_yn, "_facebookPopupWindow", "width=0, height=0");
					popup.focus();
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
				return;
			}
		}); 
	} else {
		var popup= window.open(url, "_facebookPopupWindow", "width=0, height=0");
		popup.focus();
	}
}
//-->
</SCRIPT>
<script>
$(document).ready(function(){	
	$("input[name='email']").keyup(function(){
		$("#email_check").val('0');
	})
})
</script>
</head>
<body>
 <span style="display:none;"><?=$_data->countpath?></span>

<?
	include ($Dir.TempletDir."mbjoin/mbjoin_popup{$_data->design_mbjoin}.php");
?>

</body>
</html>
