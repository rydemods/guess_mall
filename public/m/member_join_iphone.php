<?
	session_start();
	include_once('outline/header_m.php');

	if(strlen($_MShopInfo->getMemid())!=0) {
		echo ("<script>location.replace('/m/');</script>");
		exit;
	}



	######################## 회원가입 시작 ########################

	if(strlen($_MShopInfo->getMemid())>0) {
		header("Location:index.php");
		exit;
	}

	$ip = $_SERVER['REMOTE_ADDR'];

	$mem_type =($_GET['mem_type'])?$_GET['mem_type']:$_POST['mem_type'];
	$auth_type =($_GET['auth_type'])?$_GET['auth_type']:$_POST['auth_type']; // 인증타입

	// 가입금 축하적립금 사용유무 체크
	$reserve_join=(int)$_data->reserve_join;

	//추천인 관련 셋팅 (2015.12.21 - 김재수 추가)
	$recom_ok					= $_data->recom_ok;							// 추천인 사용유무
	$recom_addreserve		= (int)$_data->recom_addreserve;		// 추천시 추가로받을 적립금 금액
	$recom_memreserve	= (int)$_data->recom_memreserve;		// 추천인에게 주는 적립금 금액
	$recom_limit				= $_data->recom_limit;						// 추천수 제한
	if(ord($recom_limit)==0) $recom_limit=9999999;						// 0 이면 무한으로 받을수 있도록 처리

	if($_data->group_code){
		$group_code=$_data->group_code;
	}elseif($mem_type=="1"){
		$group_code="0004";
	}else{
		$group_code="0007";
	}

	$member_addform=$_data->member_addform;
	$adultauthid='';
	$adultauthpw='';
	if(ord($_data->adultauth)) {
		$tempadult=explode("↑=↑",$_data->adultauth);
		if($tempadult[0]=="Y") {
			$adultauthid=$tempadult[1];
			$adultauthpw=$tempadult[2];
		}
	}

	$type=$_POST["type"];

	if($type=="insert") {
		$history="-1";
		$sslchecktype="";
		if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
			$sslchecktype="ssl";
			$history="-2";
		}
		if($sslchecktype=="ssl") {
			$secure_data=getSecureKeyData($_POST["sessid"]);
			if(!is_array($secure_data)) {
				alert_go('보안인증 정보가 잘못되었습니다.',(int)$history);
			}
			foreach($secure_data as $key=>$val) {
				${$key}=$val;
			}
		} else {
			$id=trim($_POST["id"]);
			$passwd1=$_POST["passwd1"];
			$passwd2=$_POST["passwd2"];
			///$name=trim($_SESSION[ipin][name]);
			$name=trim($_POST["name"]);
			if (is_array($_POST[interest])) $interest = array_sum($_POST[interest]);
			if(!$interest) $interest = 0;
		}

		$onload="";
		$resno=$resno1.$resno2;

		for($i=0;$i<10;$i++) {
			if(strpos($etc[$i],"↑=↑")) {
				$onload="추가정보에 입력할 수 없는 문자가 포함되었습니다.";
				break;
			}
			if($i!=0) {
				$etcdata=$etcdata."↑=↑";
			}
			$etcdata=$etcdata.$etc[$i];
		}

		if(ord($onload)) {

		} else if($_data->resno_type!="N" && strlen(trim($resno))!=13) {
			$onload="주민등록번호 입력이 잘못되었습니다.";
		} else if($_data->resno_type!="N" && !chkResNo($resno)) {
			$onload="잘못된 주민등록번호 입니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
		} else if($_data->resno_type!="N" && getAgeResno($resno)<14) {
			$onload="만 14세 미만의 아동은 회원가입시 법적대리인의 동의가 있어야 합니다!\\n\\n 당사 쇼핑몰로 연락주시기 바랍니다.";
		} else if($_data->resno_type!="N" && $_data->adult_type=="Y" && getAgeResno($resno)<19) {
			$onload="본 쇼핑몰은 성인만 이용가능하므로 회원가입을 하실 수 없습니다.";
		} else if(strlen(trim($id))==0) {
			$onload="아이디 입력이 잘못되었습니다.";
		} else if(!IsAlphaNumeric($id)) {
			$onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
		} else if(!preg_match("/(^[0-9a-zA-Z]{4,12}$)/",$id)) {
			$onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
		} else if(strlen(trim($name))==0) {
			$onload="이름 입력이 잘못되었습니다.";
		} else if($rec_num==0 && strlen($rec_id)!=0) {
			$onload="추천인 ID 입력이 잘못되었습니다.";
		} else {

			if(!$onload) {
				if(!$onload) {
					$sql = "SELECT id FROM tblmember WHERE id='{$id}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
					}
					pmysql_free_result($result);
				}
				if(!$onload) {
					$sql = "SELECT id FROM tblmemberout WHERE id='{$id}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
					}
					pmysql_free_result($result);
				}
				if(!$onload) {
					//insert
					$date=date("YmdHis");
					$gender=$_POST[gender];
					$home_post=$home_post1.$home_post2;
					$office_post=$office_post1.$office_post2;
					if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
						$news_yn="Y";
					} else if($news_mail_yn=="Y") {
						$news_yn="M";
					} else if($news_sms_yn=="Y") {
						$news_yn="S";
					} else {
						$news_yn="N";
					}

					$shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd1))));

					if($_data->member_baro=="Y" && $mem_type=='1') $confirm_yn="N";
					else $confirm_yn="Y";
					//사업자회원 승인관련
					//if($mem_type=='1')  $confirm_yn="N";

					$home_addr=$home_addr1."↑=↑".$home_addr2;
					$office_addr="";
					if(strlen($office_post)==6) $office_addr=$office_addr1."↑=↑".$office_addr2;

					if ($auth_type == "real") $auth_type = "mobile";

					BeginTrans();
					$sql = "INSERT INTO tblmember(id) VALUES('{$id}')";
					pmysql_query($sql,get_db_conn());
					$sql = "UPDATE tblmember SET ";
					$sql.= "id			= '{$id}', ";
					$sql.= "passwd		= '".$shadata."', ";
					$sql.= "name		= '{$name}', ";
					$sql.= "nickname	= '{$nickname}', ";
					$sql.= "resno		= '{$resno}', ";
					$sql.= "email		= '{$email}', ";
					$sql.= "mobile		= '{$mobile}', ";
					$sql.= "news_yn		= '{$news_yn}', ";
					$sql.= "gender		= '{$gender}', ";
					//$sql.= "home_zonecode	= '{$home_zonecode}', ";
					//$sql.= "home_post	= '{$home_post}', ";
					$sql.= "home_post	= '{$home_zonecode}', ";
					$sql.= "home_addr	= '{$home_addr}', ";
					$sql.= "home_tel	= '{$home_tel}', ";
					$sql.= "reserve		= '{$reserve_join}', ";
					$sql.= "joinip		= '{$ip}', ";
					$sql.= "ip			= '{$ip}', ";
					$sql.= "date		= '{$date}', ";
					$sql.= "confirm_yn	= '{$confirm_yn}', ";
					$sql.= "dupinfo	= '{$dupinfo}', ";
					$sql.= "birth	= '{$birth}', ";
					$sql.= "married_yn	= '{$married_yn}', ";
					$sql.= "married_date	= '{$married_date}', ";
					$sql.= "job	= '{$job}', ";
					$sql.= "interest	= '{$interest}', ";
					$sql.= "memo	= '{$memo}', ";


					if(ord($group_code)) {
						$sql.= "group_code='{$group_code}', ";
					}

					$sql.= "auth_type='{$auth_type}', ";

					$sql.= "etcdata		= '{$etcdata}' WHERE id='{$id}'";
					$insert=pmysql_query($sql,get_db_conn());
					if (pmysql_errno()==0) {
						CommitTrans();
						if ($reserve_join>0) {
							insert_point($id, $reserve_join, '가입축하 적립금', '@join', $_ShopInfo->id, date("Ymd"), 0);
						}

						//쿠폰발생 (회원가입시 발급되는 쿠폰)
						if($_data->coupon_ok=="Y") {
                            /*
							$date = date("YmdHis");
							$sql = "SELECT coupon_code, date_start, date_end FROM tblcouponinfo ";
							$sql.= "WHERE display='Y' AND issue_type='M' ";
							$sql.= "AND (coupon_is_mobile='A' OR coupon_is_mobile='M' OR coupon_is_mobile='B') ";
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
                            */
                            include_once("../lib/coupon.class.php");
                            $_CouponInfo->new CouponInfo( '2' );
                            $_CouponInfo->search_coupon( '', $id ); // 쿠폰 확인
                            $ci->set_couponissue(); // 등록 테이블
                            $ci->insert_couponissue(); // 발급
						}

						if (get_session('ACCESS') == 'app') {
							$access_type	= "app";
						} else {
							$access_type	= "mobile";
						}

						//---------------------------------------------------- 가입시 로그를 등록한다. ----------------------------------------------------//
						$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$id."','join','".$access_type."','".date("YmdHis")."')";
						pmysql_query($memLogSql,get_db_conn());
						//---------------------------------------------------------------------------------------------------------------------------------//

						//가입메일 발송 처리
						if(ord($email)) {
							//SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id);
						}

						//가입 SMS 발송 처리
                        /*
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
                        */
                        $mem_return_msg = sms_autosend( 'mem_join', $id, '', '' );
						$admin_return_msg = sms_autosend( 'admin_join', $id, '', '' );

						alert_go("등록되었습니다.\\n{$msg}\\n감사합니다.",$Dir.MDir."member_joinend_iphone.php?id={$id}&name={$name}&email={$email}");
						//echo "<script>location.href='".$Dir.FrontDir."member_joinend.php?name=$name&id=$id'</script>";
					} else {
						RollbackTrans();
						$onload="ID가 중복되었거나 회원등록 중 오류가 발생하였습니다.";
					}
				}
			}
		}
		if(ord($onload)) {
			alert_go($onload,"member_join_iphone.php?mem_type={$mem_type}");
		}
	}
	if($_SESSION[ipin][birthdate]){
		$arrDateBirth[0] = substr($_SESSION[ipin][birthdate], 0, 4);
		$arrDateBirth[1] = substr($_SESSION[ipin][birthdate], 4, 2);
		$arrDateBirth[2] = substr($_SESSION[ipin][birthdate], 6, 2);
		$strDateBirth = implode("-", $arrDateBirth);
	}
	if($_SESSION[ipin][gender]){
		$genderSess = $_SESSION[ipin][gender];
	}else{
		$genderSess ='1';
	}
	$married_yn_default = 'N';
	$checked[gender][$genderSess] = "checked";
	$checked[married_yn][$married_yn_default] = "checked";

	if(true) {
		$temp=SendSMS($sms_id, $sms_authkey, $mobile, "", $fromtel, $date, $msg_mem_join, $etcmessage);
		//exdebug($temp);
	}

######################## 회원가입 끝 ########################
?>

<script type="text/javascript">

function ValidFormName(){ //이름 유효성 체크
	var val			= $("input[name=name]").val();

	if (val == '') {
		alert($("input[name=name]").attr("title"));
		$("input[name=name]").focus();
		return;
	} else {

		// 한글 이름 2~4자 이내
		// 영문 이름 2~10자 이내 : 띄어쓰기(\s)가 들어가며 First, Last Name 형식
		// 한글 또는 영문 사용하기(혼용X)

		if (!(new RegExp(/^[가-힣]{2,4}|[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/)).test(val)) {
			alert("한글(2~4자 이내) 또는 영문(2~10자 이내)으로 사용 가능합니다.");
			$("input[name=name]").focus();
			return;
		} else {
			$("#name_checked").val("1");
			ValidFormId('');
		}
	}
}

function ValidFormId(type) { //아이디 유효성 체크
	var val	= $("input[name=id]").val();
	var str_chk = "N";
	if (val == '') {
		alert($("input[name=id]").attr("title"));
		$("input[name=id]").focus();
		$("#chk_id_yn").val(str_chk);
		$("#chk_id").val('');
		return;
	} else {
		if (type == '')
		{
			if ($("#chk_id_yn").val() == 'N')
			{
				alert($("input[name=chk_id]").attr("title"));
				return;
			}
		}
		if (!(new RegExp(/^[a-zA-Z0-9]{5,16}$/)).test(val)) {
			alert("5~16자 이내 영문과 숫자만 사용 가능합니다.");
			$("input[name=id]").focus();
			$("#chk_id_yn").val(str_chk);
			$("#chk_id").val('');
			return;
		} else {
			$.ajax({
				type: "GET",
				url: "<?=$Dir.FrontDir?>iddup.proc.php",
				data: "id=" + val + "&mode=id",
				dataType:"json",
				success: function(data) {
					$("#id_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=id]").focus();
						$("#chk_id_yn").val(str_chk);
						$("#chk_id").val('');
						return;
					} else {
						str_chk	= "Y";
						$("#chk_id_yn").val(str_chk);
						$("#chk_id").val(val);
						if (type == '')
						{
							ValidFormPassword();
						} else {
							alert(data.msg);
						}
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
					$("input[name=id]").focus();
					$("#chk_id_yn").val(str_chk);
					$("#chk_id").val('');
					return;
				}
			});
		}
	}
}

function ValidFormPassword(){//비밀번호 유효성 체크
	var val	= $("input[name=passwd1]").val();
	if (val == '') {
		alert($("input[name=passwd1]").attr("title"));
		$("input[name=passwd1]").focus();
		return;
	} else {
		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).*$/)).test(val)) {
			alert("8~20자 이내 영문, 숫자, 특수문자(!@#$%^&amp;*) 3가지 조합으로 이루어져야 합니다.");
			$("input[name=passwd1]").focus();
			return;
		} else {
			$("#passwd1_checked").val("1");
			ValidFormPasswordRe();
		}
	}
}

function ValidFormPasswordRe(){//비밀번호 확인 유효성 체크
	var val			= $("input[name=passwd2]").val();
	var pw1_val	= $("input[name=passwd1]").val();
	if (val == '') {
		alert($("input[name=passwd2]").attr("title"));
		$("input[name=passwd2]").focus();
		return;
	} else {
		if (val != pw1_val) {
			alert("비밀번호를 다시 확인해 주세요.");
			$("input[name=passwd2]").focus();
			return;
		} else {
			$("#passwd2_checked").val("1");
			//ValidFormAddr();
            CheckFormSubmit();
		}
	}
}

function ValidFormAddr(){//주소 유효성 체크
	var home_zonecode	= $("input[name=home_zonecode]").val();
	var home_post1			= $("input[name=home_post1]").val();
	var home_post2			= $("input[name=home_post2]").val();
	var home_addr1			= $("input[name=home_addr1]").val();
	var home_addr2			= $("input[name=home_addr2]").val();
	if (home_zonecode == '' || home_addr1 == '' || home_addr2 == '') {
		alert($("input[name=home_addr2]").attr("title"));
		$("input[name=home_addr2]").focus();
		return;
	} else {
		$("#home_addr_checked").val("1");
		ValidFormEmail();
	}
}

function ValidFormEmail() {//이메일 유효성 체크
	var val			= $("input[name=email]").val();

	if (val == '') {
		alert($("input[name=email]").attr("title"));
		$("input[name=email]").focus();
		return;
	} else {
		if (!(new RegExp(/^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/)).test(val)) {
			alert("잘못된 이메일 형식입니다.");
			$("input[name=email]").focus();
			return;
		} else {
			$.ajax({
				type: "GET",
				url: "<?=$Dir.FrontDir?>iddup.proc.php",
				data: "email=" + val + "&mode=email",
				dataType:"json",
				success: function(data) {
					$("#email_checked").val(data.code);
					if (data.code == 0) {
						alert(data.msg);
						$("input[name=email]").focus();
						return;
					} else {
						ValidFormMobile();
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다.");
					$("input[name=email]").focus();
					return;
				}
			});
		}
	}
}

function ValidFormMobile() {
	var mobile2			= $("#mobile2").val();
	var mobile3			= $("#mobile3").val();

	if (mobile2 == '' || mobile3 == '') {
		alert($("#mobile3").attr("title"));
		if (mobile2 == '') {
			$("#mobile2").focus();
			return;
		} else if (mobile3 == '') {
			$("#mobile3").focus();
			return;
		}
	} else {
		$("#mobile_checked").val("1");
		CheckFormSubmit();
	}
}

function CheckForm(){
	ValidFormName();
}

function CheckFormSubmit(){

	form=document.form1;

	var id_checked				= $("input[name=id_checked]").val();
	var passwd1_checked		= $("input[name=passwd1_checked]").val();
	var passwd2_checked		= $("input[name=passwd2_checked]").val();
	var name_checked			= $("input[name=name_checked]").val();
	var home_addr_checked	= $("input[name=home_addr_checked]").val();
	var email_checked			= $("input[name=email_checked]").val();
	var mobile_checked		= $("input[name=mobile_checked]").val();

	//alert(id_checked+"\n"+passwd1_checked+"\n"+passwd2_checked+"\n"+name_checked+"\n"+home_addr_checked+"\n"+email_checked+"\n"+mobile_checked);
	//if (id_checked == '1' && passwd1_checked == '1' && passwd2_checked == '1' && name_checked == '1' && home_addr_checked == '1' && email_checked == '1' && mobile_checked == '1')
    if (id_checked == '1' && passwd1_checked == '1' && passwd2_checked == '1' && name_checked == '1')
	{
		form.type.value="insert";
        //alert("insert");

	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
			form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join_iphone.php';
	<?php }?>
		if(confirm("회원가입을 하겠습니까?"))
			form.submit();
		else
			return;
	} else {
		return;
	}
}

// function openDaumPostcode() {
// 	new daum.Postcode({
// 		oncomplete: function(data) {
// 			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
// 			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
// 			document.getElementById('home_zonecode').value = data.zonecode;
// 			document.getElementById('home_post1').value = data.postcode1;
// 			document.getElementById('home_post2').value = data.postcode2;
// 			document.getElementById('home_addr1').value = data.address;
// 			document.getElementById('home_addr2').value = '';
// 			document.getElementById('home_addr2').focus();
// 			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
// 			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
// 			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
// 			//document.getElementById('addr').value = addr;


// 		}
// 	}).open();
// }

function email_set(email){
	document.form1.email_addr.value=email;
}
</script>

	<div id="addressWrap" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;width:20px;right:0px;top:-1px;z-index:9999" onclick="foldDaumPostcode()" alt="접기 버튼">
	</div>

	<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post" onSubmit="return CheckForm();">

	<input type=hidden name=type value="">
	<input type=hidden name=auth_type value="<?=$auth_type?>">
	<input type="hidden" name="dupinfo" value="<?=$_SESSION[ipin][dupinfo]?>">
	<input type=hidden name=mem_type value="<?=$mem_type?>">
	<input type=hidden name=id_checked id=id_checked value="0">
	<input type=hidden name=passwd1_checked id=passwd1_checked value="0">
	<input type=hidden name=passwd2_checked id=passwd2_checked value="0">
	<input type=hidden name=name_checked id=name_checked value="<?if($name) { echo "1";} else {echo "0";}?>">
	<input type=hidden name=home_addr_checked id=home_addr_checked value="0">
	<input type=hidden name=email_checked id=email_checked value="0">
	<input type=hidden name=mobile_checked id=mobile_checked value="0">
	<input type=hidden name=nick_checked id=nick_checked>
	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
	<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
	<?php }?>
		<div class="sub-title">
			<h2>회원가입</h2>
			<a class="btn-prev" href="member_agree.php"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			<div class="js-sub-menu">
				<button class="js-btn-toggle" title="펼쳐보기"><img src="./static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
				<div class="js-menu-content">
					<ul>
						<li><a href="login.php">로그인</a></li>
						<li><a href="findid.php">아이디 찾기</a></li>
						<li><a href="findpw.php">비밀번호 찾기</a></li>
						<li><a href="member_agree.php">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="member-wrap">
			<div class="inner">
				<div class="join-flow">
					<img src="./static/img/common/join_flow02.gif" alt="02.회원정보 입력">
					<h3 class="title hide">회원정보 입력</h3>
					<p>회원정보는 개인정보 보호방침.취급방침에 따라<br>안전하게 보호됩니다.</p>
				</div>
			</div><!-- //.inner -->


			<div class="line-title">기본정보 입력<span class="point">항목은 필수 입력 항목입니다.</span></div>
			<div class="join-form">
				<ul>
					<li>
						<div class="user">이름
						<strong><?if($name){?><?=$name?><input type="hidden" id="join-name" name="name" value="<?=$name?>" title="이름을 입력하세요." ><?}else{?><input type="text" id="join-name" name="name" value="<?=$name?>" title="이름을 입력하세요." ><?}?></strong></div>
					</li>
					<li>
						<label for="join-id">아이디</label>
						<div class="input-cover">
							<div class="input"><input type="text" id="join-id" name="id" value = '<?=$id?>' title='아이디를 입력해주세요.' onchange="this.form.chk_id_yn.value='N'"></div>
							<button class="btn-def" type='button' onClick="javascript:ValidFormId('CHK')"><span>중복확인</span></button>
							<input type="hidden" name="chk_id_yn" id="chk_id_yn" value="N" />
							<input type="hidden" name="chk_id" id="chk_id" value="" title="아이디 중복확인이 필요합니다."/>
						</div>
					</li>
					<li>
						<label for="join-pw1">비밀번호</label>
						<input type="password" class="w100-per" id="join-pw1" name="passwd1" value = '<?=$passwd1?>' title="비밀번호를 입력하세요." >
						<p class="att-ment">※ 영문, 숫자, 특수문자(!@#$%^&amp;*) 3가지 조합하여 8~20자리로 만들어 주세요</p>
					</li>
					<li>
						<label for="join-pw2">비밀번호 확인</label>
						<input type="password" class="w100-per" id="join-pw2" name="passwd2" value = '<?=$passwd2?>' title="비밀번호를 한 번 더 입력하세요.">
					</li>
					<!-- <li>
						<label for="join-address">주소</label>
						<input type="text" class="" id="home_zonecode"  name='home_zonecode' value="<?=$home_zonecode?>" title="" readonly>
						<input type="hidden" name='home_post1' id='home_post1' value="<?=$home_post1?>" title="우편번호 앞 입력자리">
						<input type="hidden" name='home_post2' id='home_post2' value="<?=$home_post2?>" title="우편번호 뒤 입력자리">
						<a href="javascript:openDaumPostcode();" class="btn-def">우편번호 찾기</a>

						<input type="text" class="w100-per mt-5" name='home_addr1' id='home_addr1' value="<?=$home_addr1?>" title="주소를 입력해 주세요." readonly>
						<input type="text" class="w100-per mt-5" name='home_addr2' id='home_addr2' value="<?=$home_addr2?>" title="주소를 입력해 주세요.">
					</li>
					<li>
						<label for="join-email">E-mail</label>
						<input type="email" class="w100-per" id="join-email" name="email" title="이메일을 입력해 주세요.">
					</li>
					<li>
						<label for="mobile2">휴대폰 번호</label>
						<div class="tel-input">
							<div class="select-def">
								<select name='mobile[]' id = 'mobile1'>
									<option value="010">010</option>
									<option value="011">011</option>
									<option value="016">016</option>
									<option value="017">017</option>
									<option value="018">018</option>
									<option value="019">019</option>
								</select>
							</div>
							<div><input type="tel" name='mobile[]' id="mobile2"></div>
							<div><input type="tel" name='mobile[]' id = 'mobile3' title="휴대폰번호를 입력해 주세요."></div>
						</div>
					</li>
					<li>
						<div class="mrk-agree">
							<div>
								<p>메일 수신여부</p>
								<input type="radio" id="mail-agree1"  name='news_mail_yn' id="" value="Y">
								<label for="mail-agree1">수신</label>
								<input type="radio" id="mail-agree2"  name='news_mail_yn' id="" value="N" checked>
								<label for="mail-agree2">비수신</label>
							</div>
							<div>
								<p>SMS 수신여부</p>
								<input type="radio" id="sms-agree1" name='news_sms_yn' id="" value="Y">
								<label for="sms-agree1">수신</label>
								<input type="radio" id="sms-agree2" name='news_sms_yn' id="" value="N" checked>
								<label for="sms-agree2">비수신</label>
							</div>
						</div>
					</li> -->
				</ul>
			</div>

			<div class="btnwrap page-end">
				<div class="box">
					<a class="btn-def" href="javascript:CheckForm('<?=$mem_type?>');">가입완료</a>
					<a class="btn-function" href="member_agree.php">취소</a>
				</div>
			</div>


		</div><!-- //.member-wrap -->
	</form>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 찾기 화면을 넣을 element
    var element_layer = document.getElementById('addressWrap');

    function foldDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function openDaumPostcode() {
        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('home_zonecode').value = data.zonecode; //5자리 새우편번호 사용
 				document.getElementById('home_post1').value = data.postcode1;
 				document.getElementById('home_post2').value = data.postcode2;
                document.getElementById('home_addr1').value = fullAddr;
 				document.getElementById('home_addr2').value = '';
	 			document.getElementById('home_addr2').focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';

                // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                document.body.scrollTop = currentScroll;
            },
            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
            onresize : function(size) {
            		//console.log("Size:", size, element_layer)
                //element_layer.style.height = size.height+'px';
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = (window.innerWidth || document.documentElement.clientWidth)-20; //우편번호서비스가 들어갈 element의 width
        var height = (window.innerHeight || document.documentElement.clientHeight)-200; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 1; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>

<? include_once('outline/footer_m.php'); ?>