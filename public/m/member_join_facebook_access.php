<?php
/********************************************************************* 
// 파 일 명		: member_join_facebook_access.php 
// 설     명		: 회원가입 페이스북 정보로 회원가입
// 상세설명	: 회원가입시 페이스북의 정보확인후 회원가입 및 수정.
// 작 성 자		: 2015.10.28 - 김재수
// 수 정 자		: 
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

	// 쿠키변수 생성
	function set_cookie($cookie_name, $value, $expire)
	{
		setcookie(md5($cookie_name), base64_encode($value), time() + $expire, '/'.RootPath , getCookieDomain());
	}
	
	// 쿠키변수값 얻음
	function get_cookie($cookie_name)
	{
		$cookie = md5($cookie_name);
		if (array_key_exists($cookie, $_COOKIE))
			return base64_decode($_COOKIE[md5($cookie_name)]);
		else
			return "";
	}

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

	// library 로드, 변수 설정 등
	require_once($Dir."plugin/sns/facebook/src/facebookoauth.php");
	//$consumer_key = $config['cf_facebook_appid'];
	//$consumer_secret = $config['cf_facebook_secret'];
	$consumer_key = "820599761372048";
	$consumer_secret = "f0b7f0eb3f59a1516838f1115b48cf5b";

	// FacebookOAuth object 생성
	$connection = new FacebookOAuth($consumer_key, $consumer_secret);

	// 토큰 수령
	$access_token = $connection->getAccessToken($_REQUEST['code']);

	$token = $access_token['oauth_token'];
	//$token = $access_token['access_token'];

	// Access token 을 포함한 TwitterOAuth object 생성
	$connection = new FacebookOAuth($consumer_key, $consumer_secret, $token);

	// get user profile
	 $parameters['fields'] = "id,name,email,birthday,link";

	$user = $connection->get('me', $parameters);

	 if($user -> email && $user -> name){
		 
	
		// 가입금 축하적립금 사용유무 체크
		$reserve_join		= (int)$_data->reserve_join;

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

		$u_id		= "SFB_".$user -> id;
		$m_id	= "";
		$mf_id	= "";
		//$name = iconv("UTF-8", "euc-kr", $user -> name);		
				
		$u_sql		= "SELECT id FROM tblmember WHERE id='{$u_id}' ";
		$u_result	= pmysql_query($u_sql,get_db_conn());

		if($u_row = pmysql_fetch_object($u_result)) {
			$m_id = $u_row -> id;
		}
		pmysql_free_result($u_result);		
				
		$ul_sql		= "SELECT id FROM tblmember WHERE mb_facebook_email = TRIM('".$user -> email."') ";
		$ul_result	= pmysql_query($ul_sql,get_db_conn());

		if($ul_row = pmysql_fetch_object($ul_result)) {
			$mf_id = $ul_row -> id;
		}
		pmysql_free_result($ul_result);

		/*$u_sql		= "SELECT id FROM tblmemberout WHERE id='{$u_id}' ";
		$u_result	= pmysql_query($u_sql,get_db_conn());

		if($u_row = pmysql_fetch_object($u_result)) {

		}
		pmysql_free_result($u_result);*/

		$onload="";

		if(!$m_id && !$mf_id){
			
			$id					= $u_id;
			$email				= trim($user -> email);
			$password1		= md5($u_id.'_'.date("YmdHis"));
			$rec_id				= get_cookie('rec_id');
			$news_mail_yn	= get_cookie('news_mail_yn');
			$news_sms_yn	= get_cookie('news_sms_yn');

			$name				= str_replace(" ", "", $user -> name);
			$name				= trim($name);
				
			$nickname				= cut_str($email,4,"");
			$strlen_nickname	= strlen($nickname);

			for($i = $strlen_nickname; $i < 10; $i++){
				$nickname .= "*";
			}

			$nickname	= "";
		
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

			if(ord($onload)) {

			} else {
				if(!$onload) {
					if(!$onload) {
						$sql = "SELECT email FROM tblmember WHERE email='{$email}' ";
						$result=pmysql_query($sql,get_db_conn());
						if($row=pmysql_fetch_object($result)) {
							$onload="이미 가입된 이메일입니다.\\n\\n아이디/비밀번호 찾기를 사용하시기 바랍니다.";
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
						$mb_type	= "facebook";

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
						$sql.= "confirm_yn	= '{$confirm_yn}', ";
						$sql.= "mb_facebook_oauthtoken = '". $token ."', ";
						$sql.= "mb_facebook_email = '". $user -> email ."', ";
					
						// 추천인 아이디가 있을경우 등록한다. (2015.12.21 - 김재수)
						if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
							$sql.= "rec_id	= '{$rec_id}', ";
						}

						if(ord($group_code)) {
							$sql.= "group_code='{$group_code}', ";
						}

						$sql.= "mb_facebook_id = '". $user -> id ."' WHERE id='{$id}' ";

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

								set_cookie('rec_id', '', 60*10);
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

							alert_close("등록되었습니다.\\n{$msg}\\n감사합니다.",'/m/index.php','opener');
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
				alert_close($onload);
			}


			/*var_dump(
				array(
					array('ss_mb_id', $mb_id),
					array('ss_mb_name', $mb_name),
					array('ss_facebook_user', $user -> id),
					array('facebook_token', $token),
					array('ck_facebook_checked', true, 86400*31)
				)
			);*/
		} else {
			$onload="이미 가입된 이메일입니다.\\n\\n아이디/비밀번호 찾기를 사용하시기 바랍니다.";
			if(ord($onload)) {
				alert_close($onload);
			}
		}

	}else{
		//alert("페이스북 사용권한을 확인 해주셔야 이용가능합니다.","/");
		//echo (iconv("UTF-8", "euc-kr", "페이스북 사용권한을 확인 해주셔야 이용가능합니다."));
		$onload="페이스북 사용권한을 확인 해주셔야 이용가능합니다.";
		if(ord($onload)) {
			alert_close($onload);
		}
	}


?>