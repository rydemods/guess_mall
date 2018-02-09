<?
	session_start();

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."conf/config.sns.php");

	//header("Location:".$Dir.MDir."index.php");
	//exit;

	include_once('outline/header_m.php');

	if(strlen($_MShopInfo->getMemid())>0) {
		$mem_auth_type	= getAuthType($_MShopInfo->getMemid());
		if ($mem_auth_type != 'sns') {
			header("Location:".$Dir.MDir."index.php");
			exit;
		}
		
		$jmem_sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
		$jmem_result=pmysql_query($jmem_sql,get_db_conn());
		if($jmem_row=pmysql_fetch_object($jmem_result)) {
			if($jmem_row->member_out=="Y") {
				$_MShopInfo->SetMemNULL();
				$_MShopInfo->Save();
				alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."member_agree.php");exit;
			}

			if($jmem_row->authidkey!=$_ShopInfo->getAuthidkey()) {
				$_MShopInfo->SetMemNULL();
				$_MShopInfo->Save();
				alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."index.php");exit;
			}

			$jmem_id=$jmem_row->id;
			$jmem_name=$jmem_row->name;		

			if($jmem_row->news_yn=="Y") {
				$news_mail_yn="Y";
				$news_sms_yn="Y";
			} else if($jmem_row->news_yn=="M") {
				$news_mail_yn="Y";
				$news_sms_yn="N";
				$news_kakao_yn="N";
			} else if($jmem_row->news_yn=="S") {
				$news_mail_yn="N";
				$news_sms_yn="Y";
				$news_kakao_yn="N";
			} else if($jmem_row->news_yn=="N") {
				$news_mail_yn="N";
				$news_sms_yn="N";
				$news_kakao_yn="N";
			} else if($jmem_row->news_yn=="1") {
				$news_mail_yn="Y";
				$news_sms_yn="Y";
				$news_kakao_yn="Y";
			} else if($jmem_row->news_yn=="2") {
				$news_mail_yn="Y";
				$news_sms_yn="N";
				$news_kakao_yn="Y";
			} else if($jmem_row->news_yn=="3") {
				$news_mail_yn="N";
				$news_sms_yn="Y";
				$news_kakao_yn="Y";
			} else if($jmem_row->news_yn=="K") {
				$news_mail_yn="N";
				$news_sms_yn="N";
				$news_kakao_yn="Y";
			}
			$checked['news_mail_yn'][$news_mail_yn] = "checked";
			$checked['news_sms_yn'][$news_sms_yn] = "checked";
		} else {
			$_MShopInfo->SetMemNULL();
			$_MShopInfo->Save();
				alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."member_agree.php");
		}
		pmysql_free_result($jmem_result);
	}

	$ip = $_SERVER['REMOTE_ADDR'];

	$auth_type		= ($_GET['auth_type']!='')?$_GET['auth_type']:$_POST['auth_type']; // 인증타입
	$mem_type	= ($_GET['mem_type']!='')?$_GET['mem_type']:$_POST['mem_type']; // 회원구분 (0 : 일반)
	$join_type		= ($_GET['join_type']!='')?$_GET['join_type']:$_POST['join_type']; // 가입구분 (0 : 14세 미만, 1 : 14세 이상)
	$staff_join		= ($_GET['staff_join']!='')?$_GET['staff_join']:$_POST['staff_join']; // 임직원 가입유무
	$cooper_join		= ($_GET['cooper_join']!='')?$_GET['cooper_join']:$_POST['cooper_join']; // 협력업체 가입유무

	if ($auth_type != 'sns') {
		#####실명인증 결과에 따른 분기
		//if($CertificationData->realname_check || $CertificationData->ipin_check){
			if(!$_SESSION[ipin][name]) {
				//alert_go('회원가입을 위해 본인 인증이 필요합니다.',$Dir.MDir."member_agree.php");
				alert_go('회원가입을 위해 본인 인증이 필요합니다.',$Dir.MDir."member_certi.php");
			}
			if($_SESSION[ipin][dupinfo]){
				$check_ipin=pmysql_fetch_object(pmysql_query("select count(id) as check_id from tblmember where dupinfo='{$_SESSION[ipin][dupinfo]}'"));
				if($check_ipin->check_id){
					//alert_go('이미 가입된 회원입니다.',$Dir.MDir."member_agree.php");
					alert_go('이미 가입된 회원입니다.',$Dir.MDir."login.php");
					exit;
				}
			}
		//}

		$name=trim(iconv("CP949", "UTF-8", $_SESSION[ipin][name]));
		$gender=trim($_SESSION[ipin][gender]);
		$birthdate=mb_substr(trim($_SESSION[ipin][birthdate]),"2");
		$mobileno=trim($_SESSION[ipin][mobileno]);
		
		$birthdate1 = substr($_SESSION[ipin][birthdate],0,4);
		$birthdate2 = substr($_SESSION[ipin][birthdate],4,2);
		$birthdate3 = substr($_SESSION[ipin][birthdate],6,2);
		
		//echo strlen($mobileno);
		if(strlen($mobileno)==11){
			$mobileno1 = substr($mobileno,0,3);
			$mobileno2 = substr($mobileno,3,4);
			$mobileno3 = substr($mobileno,7,4);
		}else if(strlen($mobileno)==10){
			$mobileno1 = substr($mobileno,0,3);
			$mobileno2 = substr($mobileno,3,3);
			$mobileno3 = substr($mobileno,6,4);
		}

	}
	$c_today = date("Ymd");

//	if($c_today < '20171231'){
//		include_once($Dir."conf/config.point.new.php"); 
//	}else{
		include_once($Dir."conf/config.point.2018.php"); 
//	}

	// 가입금 축하 적립금
	$reserve_join=(int)$pointSet_new['agree_point'];
	$reserve_join_over=(int)$pointSet_new['over_point'];

	//추천인 관련 셋팅
	$recom_ok					= 'Y';																// 추천인 사용유무	
	$recom_addreserve		= (int)$pointSet['addRecommand']['point'];		// 추천시 추가로받을 적립금 금액
	$recom_memreserve	= (int)$pointSet['recommand']['point'];				// 추천인에게 주는 적립금 금액
	$recom_limit				= (int)$pointSet['recommand']['count'];			// 추천수 제한

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
			if ($mem_auth_type != 'sns') {
				$name				= trim($_POST["name"]);										// 이름
				$id					= trim($_POST["id"]);											// 아이디
			} else {
				$name				= $jmem_name;										// 이름
				$id					= $jmem_id;											// 아이디
			}
			//$birth					= trim($_POST["birth1"]).trim($_POST["birth2"]);		// 생년월일
			$birth					= trim($_SESSION[ipin][birthdate]);										// 생년월일
			$gender				= trim($_SESSION[ipin][gender]);									// 성별
			$dupinfo				= trim($_SESSION[ipin][dupinfo]);
			$conninfo				= trim($_SESSION[ipin][conninfo]);
			$passwd1			= $_POST["passwd1"];										// 비밀번호
			$passwd2			= $_POST["passwd2"];										// 비밀번호 확인
			$home_zonecode	= trim($_POST["home_zonecode"]);
			$home_post1			= trim($_POST["home_post1"]);
			$home_post2			= trim($_POST["home_post2"]);
			$home_addr1			= trim($_POST["home_addr1"]);
			$home_addr2			= trim($_POST["home_addr2"]);

			$email				= $_POST["email"]? trim($_POST["email"]):$id;		// 이메일
			$news_mail_yn	= $_POST["news_mail_yn"];								// 메일수신여부
			$news_sms_yn	= $_POST["news_sms_yn"];								// SMS수신여부
			$news_kakao_yn  = $_POST["news_kakao_yn"];								// 카카오수신여부
			$nickname = $_POST['nickname'];
			//$home_tel			= trim($_POST['home_tel1'] ."-". $_POST['home_tel2'] ."-". $_POST['home_tel3']);	// 전화번호
			if ($_POST['home_tel2'] != '' && $_POST['home_tel3'] !='') {
				$home_tel			= trim($_POST['home_tel']);	// 전화번호
			} else {
				$home_tel			= "";
			}
			$height				= trim($_POST['height']);									// 키
			$weigh				= trim($_POST['weigh']);									// 몸무게
			$lunar				= trim($_POST['lunar']);									// 음력여부 1양력 0음력
			$job				= trim($_POST['job']);									//직업text
			$job_code		= trim($_POST['job_code']);									//직업코드
			/*$mobile				= trim($_POST['mobile1'] ."-". $_POST['mobile2'] ."-". $_POST['mobile3']);		// 휴대폰
			if ($mobile) {
				$mobile	= addMobile($mobile);
			}*/
			$mobile		= trim($_POST['mobile']);									//휴대폰
			//$sns_type			= $_POST["sns_type"];										// 간편 가입한 SNS : id||code
			$sns_type			= $_SESSION[sns][sns_login_id];									
						
			$emp_id		= trim($_POST['emp_id']);									//사번

			$rec_id				= trim($_POST["rec_id"]);									// 추천인 아이디
			$erp_member_id				= trim($_POST["erp_member_id"]);									// 인증정보	

			if (!$erp_member_id) {
				//echo $name."/".$mobile;
				$send_name	= iconv('utf-8','euc-kr',$name);
				if (!$send_name) $send_name	= $name;
				$meberinfo	= getErpMeberinfo($send_name, $mobile);
				//var_dump($meberinfo);
				$code		= $meberinfo['p_err_code'];
				$p_data		= $meberinfo['p_data'];
				if ($code == '0') {
					$erp_member_id = $p_data['member_id'];
				}
			}
			
			$gdn_name		= trim($_POST["gdn_name"]);												// 보호자 이름
			//$gdn_birth			= trim($_POST["gdn_birth1"]).trim($_POST["gdn_birth2"]);		// 생년월일
			$gdn_birth			= trim($_POST["gdn_birth1"]);												// 보호자 생년월일
			$gdn_gender		= trim($_POST["gdn_gender"]);											// 보호자 성별
			$gdn_email		= trim($_POST["gdn_email"]);												// 보호자 이메일

			$gdn_mobile		= trim($_POST["gdn_mobile"]);												// 보호자 휴대폰
			if ($gdn_mobile) {
				$gdn_mobile	= addMobile($gdn_mobile);
			}
			//20170830 제휴사 추가
			if($_POST["cpCode"]){
				$company_code = $_POST["cpCode"]; // 제휴사코드
			}

		}

		$onload="";		

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

		} else if($mem_auth_type != 'sns' && strlen(trim($name))==0) {
			$onload="이름 입력이 잘못되었습니다.";		
		} else if($mem_auth_type != 'sns' && strlen(trim($id))==0) {
			$onload="아이디 입력이 잘못되었습니다.";
		} else if(strlen($birth)!=0 && strlen($birth) < 6) {
			$onload="생년월일을 입력하세요.";		
		} else if($auth_type != 'sns' && strlen(trim($mobile))==0) {
			$onload="휴대전화를 입력하세요.";
		} else if($rec_num==0 && strlen($rec_id)!=0) {
			$onload="추천인 ID 입력이 잘못되었습니다.";
		} else if($mem_auth_type != 'sns' && $join_type=='0' && strlen($gdn_name)!=0) {
			$onload="보호자 이름 입력이 잘못되었습니다.";
		} else if($join_type=='0' && strlen($gdn_birth)!=0 && strlen($gdn_birth) < 6) {
			$onload="보호자 생년월일 입력이 잘못되었습니다.";
		} else if($join_type=='0' && strlen($gdn_email)==0) {
			$onload="보호자 이메일 입력이 잘못되었습니다.";
		} else if($join_type=='0' && strlen(trim($gdn_mobile))==0) {
			$onload="보호자 휴대폰 입력이 잘못되었습니다.";
		} else {	
			if(!$onload) {
				$month_date	= date("YmdHis", strtotime('-1 month'));
				$sql = "SELECT id FROM tblmemberout WHERE name='{$name}' AND tel = '{$mobile}' AND date > '".$month_date."' order by date desc limit 1";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$onload="기존에 탈퇴한 회원입니다.\\n\\n재가입은 한달후 가능합니다.";
				}
				pmysql_free_result($result);
			}
			if(!$onload) {
				if ($mem_auth_type != 'sns') {
					$sql = "SELECT id FROM tblmember WHERE id='{$id}' ";
					$result=pmysql_query($sql,get_db_conn());
					if($row=pmysql_fetch_object($result)) {
						$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
					}
					pmysql_free_result($result);

					if(!$onload) {
						$sql = "SELECT id FROM tblmemberout WHERE id='{$id}' ";
						$result=pmysql_query($sql,get_db_conn());
						if($row=pmysql_fetch_object($result)) {
							$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
						}
						pmysql_free_result($result);
					}
				}

				if(!$onload) {
					//insert
					$date=date("YmdHis");

					//뉴스레터수신여부(Y:메일,핸폰수신동의,M:메일수신동의,S:핸폰수신동의,N:수신거부)';
					//1카카오포함전체,2카카오+이메일,3카카오+SMS+,K카카오
					/*if($news_mail_yn=="Y") {
						$news_yn="M";
					}
					if($news_sms_yn=="Y") {
						$news_yn="S";
					}
					if($news_kakao_yn=="Y") {
						$news_yn="K";
					}
					if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
						$news_yn="Y";
					}
					if($news_mail_yn=="Y" && $news_kakao_yn=="Y") {
						$news_yn="2";
					}
					if($news_sms_yn=="Y" && $news_kakao_yn=="Y") {
						$news_yn="3";
					}
					if($news_mail_yn=="Y" && $news_sms_yn=="Y" && $news_kakao_yn=="Y") {
						$news_yn="1";
					}*/

					
					if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
						$news_yn="Y";
					} else if($news_mail_yn=="Y") {
						$news_yn="M";
					} else if($news_sms_yn=="Y") {
						$news_yn="S";
					} else {
						$news_yn="N";
					}

					if($news_kakao_yn!="Y") $news_kakao_yn	= "N";					
					
					
					

					//if(ord($sns_type)) $passwd1	= $sns_type;

					$shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd1))));

					$confirm_yn="Y";

					$home_addr			=$home_addr1."↑=↑".$home_addr2;
					if ($job_code=='') $job	= "";

					if ($auth_type == "real") $auth_type = "mobile";

					BeginTrans();
					if ($mem_auth_type != 'sns') {
						$sql = "INSERT INTO tblmember(id) VALUES('{$id}')";
						pmysql_query($sql,get_db_conn());
					}
					$sql = "UPDATE tblmember SET ";
					$sql.= "id			= '{$id}', ";
					$sql.= "passwd		= '".$shadata."', ";
					$sql.= "name		= '{$name}', ";
					$sql.= "email		= '{$email}', ";
					$sql.= "mobile		= '{$mobile}', ";
					$sql.= "home_post	= '{$home_zonecode}', ";
					$sql.= "home_addr	= '{$home_addr}', ";
					
					$sql.= "home_tel		= '{$home_tel}', ";
					$sql.= "height			= '{$height}', ";
					$sql.= "weigh			= '{$weigh}', ";
					$sql.= "lunar			= '{$lunar}', ";
					$sql.= "job				= '{$job}', ";
					$sql.= "job_code		= '{$job_code}', ";

					$sql.= "news_yn		= '{$news_yn}', ";
					$sql.= "kko_yn		= '{$news_kakao_yn}', ";
					if(ord($birth)) $sql.= "birth		= '{$birth}', ";
					if(ord($birth)) $sql.= "gender		= '{$gender}', ";
					if(ord($nickname)) $sql.=" nickname = '{$nickname}', ";
					$sql.= "joinip		= '{$ip}', ";
					$sql.= "ip			= '{$ip}', ";
					if ($mem_auth_type != 'sns') {
						$sql.= "date		= '{$date}', ";
					} else {
						$sql.= "trandate		= '{$date}', ";
					}
					$sql.= "confirm_yn	= '{$confirm_yn}', ";
					$sql.= "dupinfo	= '{$dupinfo}', ";
					$sql.= "conninfo	= '{$conninfo}', ";
					
					if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
						$sql.= "rec_id	= '{$rec_id}', ";
					}
					if(ord($group_code)) {
						$sql.= "group_code='{$group_code}', ";
					}

					if(ord($sns_type)) $sql.= "sns_type		= '{$sns_type}', ";
					if(ord($emp_id)) {
						$sql.= "staff_yn		= 'Y', ";
						$sql.= "erp_emp_id		= '{$emp_id}', ";
					}

//					if(ord($cooper_join) && $cooper_join == 'Y') {
//						$sql.= "cooper_yn		= 'Y', ";
//					}

					if(ord($cooper_join) && $cooper_join == 'Y') {
						$sql.= "cooper_yn		= 'Y', ";
						$sql.= "company_code='{$company_code}', ";
						list($company_group)=pmysql_fetch_array(pmysql_query("select group_no from tblcompanygroup where group_code='{$company_code}'"));
						$sql.= "company_group='{$company_group}', ";
					}

					if(ord($company_code)) { //2017-08-04 제휴사 추가
						$sql.= "cooper_yn		= 'Y', ";
						$sql.= "company_code='{$company_code}', ";
						list($company_group)=pmysql_fetch_array(pmysql_query("select group_no from tblcompanygroup where group_code='{$company_code}'"));
						$sql.= "company_group='{$company_group}', ";
					}

					$sql.= "mb_type		= 'web', ";
					$sql.= "erp_mem_id		= '{$erp_member_id}', ";

					$sql.= "auth_type='{$auth_type}' WHERE id='{$id}'";
					
				
					//if ($id == "marine3769") {
					////	echo $sql;
					//	exit;
					//}
					$insert=pmysql_query($sql,get_db_conn());
					if (pmysql_errno()==0) {
						CommitTrans();
						
						// 알림톡 커밋완료후
						$alim = new ALIM_TALK();
						$alim->makeAlimTalkSearchData($id, 'SCC04', "", "");
						
                        // ERP로 회원정보 전송..2016-12-19
                        sendErpMemberInfo($id,'join');
						getErpMeberPoint($id);
                        
                        // 회원가입 알림톡
                        //$alim = new ALIM_TALK();
                       // $alim->makeAlimTalkSearchData($id, 'SCC04', "", "");

						if ($auth_type != 'sns') {
							if ($reserve_join>0) {
								insert_point_act($id, $reserve_join, '가입축하 포인트', '@join', $_ShopInfo->id, date("Ymd"), 0);
								
								
								if($height!="" and $weigh!=""){ //추가정보 입력포인트
									insert_point_act($id, $reserve_join_over, '추가정보입력 포인트', '@join_add', $_ShopInfo->id, 'join_add', 0);
								}
								// 2018년1월1일 통합회원 전환 포인트 변경 - 신원 권정수
								$reserve_join_off	= '5000';
								if($erp_member_id!=""){ //오프라인회원 통합회원 전환시 입력포인트
									insert_point_act($id, $reserve_join_off, '통합회원 전환 포인트', '@join_off', $_ShopInfo->id, 'join_off', 0);
								}
							}

							if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
								$rec_id_reserve=0;
								$id_reserve=0;
								if($recom_addreserve>0) {
									insert_point_act($id, $recom_addreserve, '추천인 포인트', '@recomment_from', $_ShopInfo->id, date("Ymd"), 0);

									$id_reserve=$recom_addreserve;
								}
								if($recom_memreserve>0) {
									$mess=$id."님이 추천하셨습니다. 감사합니다.";
									insert_point_act($rec_id, $recom_memreserve, "아이디 ".$id.'님이 추천', '@recomment_to', $_ShopInfo->id, date("Ymd"), 0);

									$rec_id_reserve=$recom_memreserve;
								}

								//추천인 등록
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
								include_once("../lib/coupon.class.php");
								$_CouponInfo = new CouponInfo( '2' );
								$_CouponInfo->search_coupon( '', $id ); // 쿠폰 확인
								$_CouponInfo->set_couponissue( $id ); // 등록 테이블
								$_CouponInfo->insert_couponissue(); // 발급
							}
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
							SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $id);
						}

						//가입 SMS 발송 처리
						$mem_return_msg = sms_autosend( 'mem_join', $id, '', '' );
						$admin_return_msg = sms_autosend( 'admin_join', $id, '', '' );

						$_ShopInfo->setCheckSns("");
						$_ShopInfo->setCheckSnsLogin("");
						$_ShopInfo->setCheckSnsAccess("");
						$_ShopInfo->setCheckSnsChurl("");
						$_ShopInfo->Save();

						if ($mem_auth_type != 'sns') {
							$alert_join_text	= "회원";
						} else {
							$alert_join_text	= "정회원";
						}
						alert_go("{$alert_join_text}가입이 완료되었습니다.\\n{$msg}\\n감사합니다.",$Dir.MDir."member_joinend.php?auth_type={$auth_type}&name={$name}&id={$id}&bf_auth_type={$mem_auth_type}");
					} else {
						RollbackTrans();
						$onload="회원등록 중 오류가 발생하였습니다.";
					}
				}
			}
		}
		if(ord($onload)) {
			alert_go($onload,"member_join.php?mem_type={$mem_type}");
		}
	}


	if(ord($news_mail_yn)==0) $news_mail_yn="Y";
	if(ord($news_sms_yn)==0) $news_sms_yn="Y";
	if(true) {
		$temp=SendSMS($sms_id, $sms_authkey, $mobile, "", $fromtel, $date, $msg_mem_join, $etcmessage);
		//exdebug($temp);
	}
?>

<script type="text/javascript">

function ValidFormId(jointype, type) { //아이디 유효성 체크

	var val	= $("input[name=id]").val();
	if (val == '') {
		alert("아이디를 입력해 주세요.");
		$("input[name=id]").focus();
		return;
	} else {
		if (val.match(/[^a-zA-Z0-9]/)!=null) {
			alert("아이디는 숫자와 영문만 입력할 수 있습니다.");			
			$("input[name=id]").focus();
			return;			
		}else if (val.length < 4 || val.length > 20) {
			alert("아이디는 4자 이상, 20자 이하여야 합니다.");
			$("input[name=id]").focus();
			return;		
		} else {
			if (type=='S') {
				if ($("#id_checked").val() == "0") {
					alert("아이디 중복확인을 해주세요.");
					$("input[name=id]").focus();
					return;
				} else {
					ValidFormPassword(jointype, type);
					return;
				}
			} else {
				$.ajax({ 
					type: "GET", 
					url: "../front/iddup.proc.php", 
					data: "id=" + val + "&mode=id",
					dataType:"json", 
					success: function(data) {
						if(data.code=='0'){
							alert(data.msg);
							$("input[name=id]").focus();
							return;
						}else{
							$("#id_checked").val('1');
							alert('사용 가능한 아이디 입니다.');
							return;
						}						
					},
					error: function(result) {
						$("input[name=id]").parent().find(".type_txt1").html("에러가 발생하였습니다."); 
						$("input[name=id]").focus();
					}
				}); 
			}
		}
	}
}

function ValidFormPassword(jointype, type){//비밀번호 유효성 체크
	var val	= $("input[name=passwd1]").val();
	if (val == '') {
		alert("비밀번호를 입력해 주세요.");
		$("input[name=passwd1]").focus();
		return;
	}else{
		if (!(new RegExp(/^.*(?=.{8,20})(?=.*[a-zA-Z])(?=.*[0-9]).*$/)).test(val)) {
			alert('"8~20자 이내 영문, 숫자 조합으로 이루어져야 합니다.');	
			$("input[name=passwd1]").focus();
			return;
		} else {
			if (type=='S') ValidFormPasswordRe(jointype, type);
			$("#passwd1_checked").val("1");		
			return;
		}
	}
}

function ValidFormPasswordRe(jointype, type){ //비밀번호 확인 유효성 체크
	var val			= $("input[name=passwd2]").val();
	var pw1_val	= $("input[name=passwd1]").val();

	if (val == '') {
		alert("비밀번호 확인을 입력해 주세요.");
		$("input[name=passwd2]").focus();
		return;
	} else {
		if (val != pw1_val) {			
			alert("비밀번호가 일치하지 않습니다.");	
			$("input[name=passwd2]").focus();
			return;
		} else {
			$("#passwd2_checked").val("1");
			if (type=='S') ValidFormAddr(jointype, type);
			return;
		}
	}
}

function ValidFormAddr(jointype, type){ // 주소 유효성 체크
	var home_zonecode	= $("input[name=home_zonecode]").val();
	var home_post1			= $("input[name=home_post1]").val();
	var home_post2			= $("input[name=home_post2]").val();
	var home_addr1			= $("input[name=home_addr1]").val();
	var home_addr2			= $("input[name=home_addr2]").val();

	if (home_zonecode != '' || home_addr1 != '' || home_addr2 != '') {
		if (home_zonecode.length > 5) {
			alert("신주소를 입력해 주세요.");
			return;
		} else {
			if (home_addr1 == '' || home_addr2 == '') {
				alert("주소를 입력해 주세요.");
				return;
			} else {
				$("#home_addr_checked").val("1");
				ValidFormMobile(jointype, type);
				return;
			}
		}
	} else {
		$("#home_addr_checked").val("1");
		ValidFormMobile(jointype, type);
		return;
	}
}

function ValidFormMobile(jointype, type) { //휴대폰번호 유효성 체크
	var mobile2			= $("input[name=mobile2]").val();
	var mobile3			= $("input[name=mobile3]").val();

	if (mobile2 == '' || mobile3 == '') {
		alert('휴대폰 번호를 입력해 주세요');
		if (mobile2 == '') {
			$("input[name=mobile2]").focus();
		} else if (mobile3 == '') {
			$("input[name=mobile3]").focus();
		}
		return;
	} else {
		var u_name_val	= $("input[name=name]").val();
		var u_mobile_val	= $("select[name=mobile1] option:selected").val()+$("input[name=mobile2]").val()+$("input[name=mobile3]").val();
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&mobile=" + u_mobile_val + "&mode=erp_mem_chk",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {
					//alert(data.msg.eshop_id);
					if (data.msg.eshop_id =='') {
						if ($("input[name=erp_member_yn]").val() == 'Y') {		
							$("#mobile_checked").val("1");
							ValidFormEmail(jointype, type);
							return;
						} else {
							if ($("input[name=staff_join]").val() == 'Y' || $("input[name=cooper_join]").val() == 'Y') {
								$("form[name=form1]").find("input[name=erp_member_id]").val(data.msg.member_id);	
								$("#mobile_checked").val("1");
								ValidFormEmail(jointype, type);
							} else {
								if (confirm("회원님은 신원의 오프라인 매장 회원이십니다. 신원 통합몰 회원 전환으로 가입하시겠습니까?")) {
									location.href="member_switch.php";
									return;
								} else {
									return;
								}
							}
						}
					} else {					
						if (confirm("회원님은 통합 회원이십니다. 신원 통합몰 로그인으로 이동하시겠습니까?")) {
							location.href="login.php";
							return;
						} else {
							return;
						}
					}					
				} else if (data.code == 99) {				
					if (confirm("회원님은 통합 회원이십니다. 신원 통합몰 로그인으로 이동하시겠습니까?")) {
						location.href="login.php";
						return;
					} else {
						return;
					}
				} else {				
					$("#mobile_checked").val("1");
					ValidFormEmail(jointype, type);
					return;
				}
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				$("input[name=mobile1]").focus();
				return;
			}
		});
	}
}

function ValidFormEmail(jointype, type) { //이메일 유효성 체크
	var val1	= $("input[name=email1]").val();
	var val2	= $("input[name=email2]").val();
	if (val1 == '') {
		alert('이메일을 등록해 주세요');
		$("input[name=email1]").focus();
		return;
	} else {

		if($('#email_com').val()==''){
			alert("이메일 도메인을 선택해 주세요");
			$('#email_com').focus();
			return;
		}

		if (val2 == '') {
			alert("이메일 도메인을 입력해 주세요");
			$("input[name=email2]").focus();		
			return;
		} else {
			if (type=='S') {
				if ($("#email_checked").val() == '0')	{
					alert("이메일 중복확인을 해주세요.");
					$("input[name=email1]").focus();
					return;
				} else {
					if ($("input[name=staff_join]").val() == 'Y') {
						ValidFormEmp(jointype, type);
					} else {
						$("#emp_checked").val("1");
						CheckFormSubmit();
					}
					return;
				}
			} else {			
				var val = val1 + '@' + val2;
				$.ajax({ 
					type: "GET", 
					url: "../front/iddup.proc.php", 
					data: "email=" + val + "&mode=email",
					dataType:"json", 
					success: function(data) {			
						if (data.code == 0) {							
							alert(data.msg);
							return;
						} else {
							$("#email_checked").val('1');
							alert("등록하신 이메일 "+val+" 는(은) 사용 가능 합니다.");
							return;
						}
						
					},
					error: function(result) {
						alert("에러가 발생하였습니다.");
						$("input[name=email1]").focus();
					}
				}); 
			}
		}
	}
}

function ValidFormEmp(jointype, type) { //임직원 유효성 체크
	var val			= $("input[name=emp_id]").val();

	if (val == '') {
		alert("사번을 입력해 주세요.");
		$("input[name=emp_id]").focus();
		return;
	} else {
		var u_name_val	= $("input[name=name]").val();
		$.ajax({
			type: "GET",
			url: "<?=$Dir.FrontDir?>iddup.proc.php",
			data: "name=" + u_name_val + "&emp_id=" + val + "&mode=erp_emp_chk",
			dataType:"json",
			success: function(data) {
				if (data.code == 0) {
					$("#emp_checked").val("1");
					CheckFormSubmit();
				} else if (data.code == '-1') {			
					alert("등록하신 "+u_name_val+"님의 사번 "+val+" 는(은) 존재하지 않습니다.");
					$("input[name=emp_id]").focus();
					return;
				} else if (data.code == '-2') {			
					alert("등록하신 "+u_name_val+"님의 사번 "+val+" 는(은) 이미 가입된 사번입니다.");
					$("input[name=emp_id]").focus();
					return;
				}
				return;
			},
			error: function(result) {
				alert("에러가 발생하였습니다.");
				$("input[name=mobile1]").focus();
				return;
			}
		});
	}
}

function ValidFormCPcode(jointype, type) { // 2017-08-04 추가 제휴사코드 체크

	var val	= $("input[name=cpCode]").val();

	if (val == '') {
		alert("제휴사 코드를 입력해 주세요.");
		$("input[name=cpCode]").focus();
		return;
	} else {
		if (val.match(/[^a-zA-Z0-9]/)!=null) {
			alert("제휴사코드는 숫자와 영문만 입력할 수 있습니다.");			
			$("input[name=cpCode]").focus();
			return;			
		}else if (val.length != 8) {
			alert("제휴사코드는 8자 입니다.");
			$("input[name=cpCode]").focus();
			return;		
		} else {
			if (type=='S') {
				if ($("#cp_checked").val() == "0") {
					alert("제휴사코드 확인을 해주세요.");
					$("input[name=id]").focus();
					return;
				}
			} else {
				$.ajax({ 
					type: "GET", 
					url: "../front/cpcode.proc.php", 
					data: "cpCode=" + val + "&mode=cpCode",
					dataType:"json", 
					success: function(data) {
						if(data.code=='0'){
							alert(data.msg);
							$("input[name=cpCode]").focus();
							return;
						}else{
							$("#cp_checked").val('1');
							alert('올바른 제휴사 코드 입니다.');
							return;
						}						
					},
					error: function(result) {
						$("input[name=cpCode]").parent().find(".type_txt1").html("에러가 발생하였습니다."); 
						$("input[name=cpCode]").focus();
					}
				}); 
			}
		}
	}
}


function CheckForm(jointype) {	
	$("#passwd1_checked").val("0");
	$("#passwd2_checked").val("0");
	$("#home_addr_checked").val("0");
	$("#mobile_checked").val("0");
	$("#emp_checked").val("0");
	ValidFormId(jointype, "S");	
}

function CheckFormSubmit(){
	
	form=document.form1;	

	var id_checked				= $("input[name=id_checked]").val();
	var passwd1_checked		= $("input[name=passwd1_checked]").val();
	var passwd2_checked		= $("input[name=passwd2_checked]").val();
	var home_addr_checked	= $("input[name=home_addr_checked]").val();
	var mobile_checked		= $("input[name=mobile_checked]").val();
	var email_checked			= $("input[name=email_checked]").val();
	var emp_checked			= $("input[name=emp_checked]").val();

	$('#job').val($("select[name=job_code] option:selected").text());
	$('#home_tel').val($("select[name=home_tel1] option:selected").val()+"-"+$("input[name=home_tel2]").val()+"-"+$("input[name=home_tel3]").val());
	$('#mobile').val($("select[name=mobile1] option:selected").val()+"-"+$("input[name=mobile2]").val()+"-"+$("input[name=mobile3]").val());
	$('#email').val($("input[name=email1]").val()+"@"+$("input[name=email2]").val());

	//alert(id_checked+"/"+passwd1_checked+"/"+passwd2_checked+"/"+home_addr_checked+"/"+mobile_checked+"/"+email_checked+"/"+emp_checked);

	if (id_checked == '1' && passwd1_checked == '1' && passwd2_checked == '1' && home_addr_checked == '1' && mobile_checked == '1' && email_checked == '1' && emp_checked == '1') 
	{
		form.type.value="insert";

	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
			form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
	<?php }?>
		if(confirm("회원가입을 하겠습니까?"))
			form.submit();
		else
			return;
	} else {
		return;
	}
}

function customChk(val){

	if((val=='custom')){
		$('#email2').show();
		$('#email2').val('');	
		$('#email2').focus();	
	}else{
		$('#email2').hide();
		$('#email2').val(val);	
	}
	
	
}

function submitCancel(){
	if(confirm('입력된 내용이 모두 삭제됩니다. 취소하시겠습니까?')){
		var add_par	= "";
		if ($("input[name=staff_join]").val() == 'Y') {
			add_par	= "?staff_join=Y";
		} else if ($("input[name=cooper_join]").val() == 'Y') {
			add_par	= "?cooper_join=Y";
		}
		location.href="member_certi.php"+add_par;
	}
}
</script>
<?
$erp_member_yn				= $_POST['erp_member_yn'];
$erp_member_id					= $_POST['erp_member_id'];
$erp_cust_name					= $_POST['erp_cust_name'];
$erp_birthday						= $_POST['erp_birthday'];
$erp_birth_gb						= $_POST['erp_birth_gb'];
$erp_cell_phone_no1			= $_POST['erp_cell_phone_no1'];
$erp_cell_phone_no2			= $_POST['erp_cell_phone_no2'];
$erp_cell_phone_no3			= $_POST['erp_cell_phone_no3'];
$erp_sex_gb						= $_POST['erp_sex_gb'];
$erp_job_cd						= $_POST['erp_job_cd'];
$erp_home_zip_old_new		= $_POST['erp_home_zip_old_new'];
$erp_home_zip_no				= $_POST['erp_home_zip_no'];
$erp_home_addr1				= $_POST['erp_home_addr1'];
$erp_home_addr2				= $_POST['erp_home_addr2'];
$erp_sms_yn						= $_POST['erp_sms_yn'];
$erp_kakao_yn					= $_POST['erp_kakao_yn'];
$erp_email1						= $_POST['erp_email1'];
$erp_email2						= $_POST['erp_email2'];
$erp_home_tel_no1				= $_POST['erp_home_tel_no1'];
$erp_home_tel_no2				= $_POST['erp_home_tel_no2'];
$erp_home_tel_no3				= $_POST['erp_home_tel_no3'];

$erp_home_zip_no	= str_replace("-", "", $erp_home_zip_no);
$erp_home_post1	= substr($erp_home_zip_no,0,3);
$erp_home_post2	= substr($erp_home_zip_no,3,3);

$mobileno1	= $mobileno1?$mobileno1:$erp_cell_phone_no1;
$mobileno2	= $mobileno2?$mobileno2:$erp_cell_phone_no2;
$mobileno3	= $mobileno3?$mobileno3:$erp_cell_phone_no3;

$email_domain_arr	= array("naver.com","daum.net","gmail.com","nate.com","yahoo.co.kr","lycos.co.kr","empas.com","hotmail.com","msn.com","hanmir.com","chol.net","korea.com","netsgo.com","dreamwiz.com","hanafos.com","freechal.com","hitel.net");

if (in_array($erp_email2, $email_domain_arr)) {
	$erp_email_com	= $erp_email2;
} else {
	$erp_email_com	= $erp_email2?"custom":"";
}
?>

	<div id="addressWrap" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;width:20px;right:0px;top:-1px;z-index:9999" onclick="foldDaumPostcode()" alt="접기 버튼">
	</div>

	<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post" onSubmit="return CheckForm(<?=$jointype?>);">

	<input type=hidden name=type value="">
	<input type=hidden name=staff_join value="<?=$staff_join?>">
	<?if ($staff_join != 'Y') {?><input type=hidden name=emp_id value=""><?}?>
	<input type=hidden name=cooper_join value="<?=$cooper_join?>">
	<input type=hidden name=erp_member_yn value="<?=$erp_member_yn?>">
	<input type="hidden" name="erp_member_id" value="<?=$erp_member_id?>">
	<input type=hidden name=auth_type value="<?=$auth_type?>">
	<input type="hidden" name="dupinfo" value="<?=$_SESSION[ipin][dupinfo]?>">
	<input type="hidden" name="conninfo" value="<?=$_SESSION[ipin][conninfo]?>">
	<input type=hidden name=mem_type value="<?=$mem_type?>">
	<input type=hidden name=join_type value="<?=$join_type?>">
	<input type=hidden name=name_checked id=name_checked value="<?if($name&&$join_type=='1') { echo "1";} else {echo "0";}?>">
	<input type=hidden name=id_checked id=id_checked value="0">
	<input type=hidden name=passwd1_checked id=passwd1_checked value="0">
	<input type=hidden name=passwd2_checked id=passwd2_checked value="0">
	<input type=hidden name=home_addr_checked id=home_addr_checked value="0">
	<input type=hidden name=mobile_checked id=mobile_checked value="0">
	<input type=hidden name=email_checked id=email_checked value="0">
	<input type=hidden name=emp_checked id=emp_checked value="0">

	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
	<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
	<?php }?>

	
<!-- 내용 -->
<main id="content" class="subpage with_bg">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>회원가입</span>
		</h2>
		<div class="page_step join_step">
			<ul class="ea4 clear">
				<li><span class="icon_join_step01"></span>본인인증</li>
				<li><span class="icon_join_step02"></span>약관동의</li>
				<li class="on"><span class="icon_join_step03"></span>정보입력</li>
				<li><span class="icon_join_step04"></span>가입완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->

	<section class="joinpage join_form">
		<div class="form_notice"><strong class="point-color">*</strong> 표시는 필수항목입니다.</div>
		
		<div class="board_type_write">
			<dl>
				<dt>이름</dt>
				<dd><?=$name?><input type="hidden" id="user-name" name="name" value="<?=$name?>" title="이름을 입력하세요." tabindex="1"></dd>
			</dl>
			<dl>
				<dt>생년월일</dt>
				<dd>
					<span><?=$birthdate1?>년 <?=$birthdate2?>월 <?=$birthdate3?>일</span>
					<label class="ml-10" for="birth_typeA">
						<input type="radio" class="radio_def" name="lunar" id="birth_typeA" checked="" value="1">
						<span>양력</span>
					</label>
					<label class="ml-5" for="birth_typeB">
						<input type="radio" class="radio_def" name="lunar" id="birth_typeB" value="0">
						<span>음력</span>
					</label>
				</dd>
			</dl>
			<dl>
				<dt>성별</dt>
				<dd>
					<?
					
						if($gender=='1' || $gender=='3'){
							echo "남자";
						}
						if($gender=='2' || $gender=='4'){	
							echo "여자";	
						}
					
					?>
					
				</dd>
			</dl>
			<dl>
				<dt><span class="required">아이디</span></dt>
				<dd>
					<div class="input_addr">
						<input type="text" class="w100-per" id="user-id" name="id" tabindex="2" title="아이디 입력자리"  placeholder="아이디 입력" onChange="javascript:$('input[name=id_checked]').val('0');">
						<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="ValidFormId('1','');return false;">중복확인</a></div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><span class="required">비밀번호</span></dt>
				<dd>
					<input type="password" class="w100-per" id="user-pwd1" name="passwd1" title="비밀번호 입력자리" tabindex="3" placeholder="비밀번호 입력 (영문, 숫자 포함 8~20자리)">
					<input type="password" class="w100-per mt-5" id="user-pwd2" name="passwd2" title="비밀번호 재입력자리" title="비밀번호를&nbsp;한 번 더&nbsp;입력하세요." tabindex="4" placeholder="비밀번호 확인">
				</dd>
			</dl>
			<dl>
				<dt><span class="required">주소</span></dt>
				<dd>
					<input type="hidden" id="home_post1" name = 'home_post1' value="<?=$erp_home_post1?>">
					<input type="hidden" id='home_post2' name = 'home_post2' value="<?=$erp_home_post2?>">
					<div class="input_addr">
						<input type="text" class="w100-per" name = 'home_zonecode' id = 'home_zonecode' value="<?=$erp_home_zip_no?>" title="우편번호 입력자리" placeholder="우편번호" readonly>
						<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="openDaumPostcode();return false;">주소찾기</a></div>
					</div>
					<input type="text" class="w100-per mt-5" name = 'home_addr1' id = 'home_addr1' value="<?=$erp_home_addr1?>" title="검색된 주소" placeholder="기본주소" readonly>
					<input type="text" class="w100-per mt-5" name = 'home_addr2' id = 'home_addr2' value="<?=$erp_home_addr2?>" title="상세주소 입력" placeholder="상세주소" tabindex="6">
				</dd>
			</dl>
			<dl>
				<dt>전화번호</dt>
				<dd>
					<div class="input_tel">
						<select class="select_line" id="home_tel1" name="home_tel1" tabindex="7">
							<option value="02" <?if($erp_home_tel_no1=="02"){?>selected<?}?>>02</option>
							<option value="031" <?if($erp_home_tel_no1=="031"){?>selected<?}?>>031</option>
							<option value="032" <?if($erp_home_tel_no1=="032"){?>selected<?}?>>032</option>
							<option value="033" <?if($erp_home_tel_no1=="033"){?>selected<?}?>>033</option>
							<option value="041" <?if($erp_home_tel_no1=="041"){?>selected<?}?>>041</option>
							<option value="042" <?if($erp_home_tel_no1=="042"){?>selected<?}?>>042</option>
							<option value="043" <?if($erp_home_tel_no1=="043"){?>selected<?}?>>043</option>
							<option value="044" <?if($erp_home_tel_no1=="044"){?>selected<?}?>>044</option>
							<option value="051" <?if($erp_home_tel_no1=="051"){?>selected<?}?>>051</option>
							<option value="052" <?if($erp_home_tel_no1=="052"){?>selected<?}?>>052</option>
							<option value="053" <?if($erp_home_tel_no1=="053"){?>selected<?}?>>053</option>
							<option value="054" <?if($erp_home_tel_no1=="054"){?>selected<?}?>>054</option>
							<option value="055" <?if($erp_home_tel_no1=="055"){?>selected<?}?>>055</option>
							<option value="061" <?if($erp_home_tel_no1=="061"){?>selected<?}?>>061</option>
							<option value="062" <?if($erp_home_tel_no1=="062"){?>selected<?}?>>062</option>
							<option value="063" <?if($erp_home_tel_no1=="063"){?>selected<?}?>>063</option>
							<option value="064" <?if($erp_home_tel_no1=="064"){?>selected<?}?>>064</option>
						</select>
						<span class="dash"></span>
						<input type="tel" id="home_tel2" name="home_tel2" value="<?=$erp_home_tel_no2?>" title="선택 전화번호 가운데 입력자리" tabindex="8" maxlength="4">
						<span class="dash"></span>
						<input type="tel" id="home_tel3" name="home_tel3" value="<?=$erp_home_tel_no3?>" title="선택 전화번호 마지막 입력자리" tabindex="9" maxlength="4">
					</div>
					<input type="hidden" name="home_tel" id="home_tel">
				</dd>
			</dl>
			<dl>
				<dt><span class="required">휴대폰 번호</span></dt>
				<dd>
					<div class="input_tel">
						<select class="select_line" id="mobile1" name="mobile1" tabindex="10"  <?=$mobileno1!=""?" disabled=\"disabled\"":""?>>
							<option value="010" <?if($mobileno1=="010"){?>selected<?}?>>010</option>
							<option value="011" <?if($mobileno1=="011"){?>selected<?}?>>011</option>
							<option value="016" <?if($mobileno1=="016"){?>selected<?}?>>016</option>
							<option value="017" <?if($mobileno1=="017"){?>selected<?}?>>017</option>
							<option value="018" <?if($mobileno1=="018"){?>selected<?}?>>018</option>
							<option value="019" <?if($mobileno1=="019"){?>selected<?}?>>019</option>
						</select>
						<span class="dash"></span>
						<input type="tel" id="mobile2" name="mobile2" value="<?=$mobileno2?>" title="필수 휴대폰 번호 가운데 입력자리" tabindex="11"<?=$mobileno2!=""?" readonly":""?> maxlength="4">
						<span class="dash"></span>
						<input type="tel" id="mobile3" name="mobile3" value="<?=$mobileno3?>" title="필수 휴대폰 번호 마지막 입력자리" tabindex="12"<?=$mobileno3!=""?" readonly":""?> maxlength="4">
					</div>
					<input type="hidden" name="mobile" id="mobile">
				</dd>
			</dl>
			<dl>
				<dt><span class="required">이메일</span></dt>
				<dd>
					<div class="input_addr">
						<div class="input_mail">
							<input type="text" class="w100-per" id="email1" name="email1" value="<?=$erp_email1?>" title="이메일 입력" tabindex="14" onChange="javascript:$('input[name=email_checked]').val('0');">
							<span class="at">&#64;</span>
							<select class="select_line" tabindex="15" id="email_com" onchange="customChk(this.value);">
								<option value="">선택</option>
								<option value="custom" <?if($erp_email_com=="custom"){?>selected<?}?>>직접입력</option>
								<option value="naver.com" <?if($erp_email_com=="naver.com"){?>selected<?}?>>naver.com</option>
								<option value="daum.net" <?if($erp_email_com=="daum.net"){?>selected<?}?>>daum.net</option>
								<option value="gmail.com" <?if($erp_email_com=="gmail.com"){?>selected<?}?>>gmail.com</option>
								<option value="nate.com" <?if($erp_email_com=="nate.com"){?>selected<?}?>>nate.com</option>
								<option value="yahoo.co.kr" <?if($erp_email_com=="yahoo.co.kr"){?>selected<?}?>>yahoo.co.kr</option>
								<option value="lycos.co.kr" <?if($erp_email_com=="lycos.co.kr"){?>selected<?}?>>lycos.co.kr</option>
								<option value="empas.com" <?if($erp_email_com=="empas.com"){?>selected<?}?>>empas.com</option>
								<option value="hotmail.com" <?if($erp_email_com=="hotmail.com"){?>selected<?}?>>hotmail.com</option>
								<option value="msn.com" <?if($erp_email_com=="msn.com"){?>selected<?}?>>msn.com</option>
								<option value="hanmir.com" <?if($erp_email_com=="hanmir.com"){?>selected<?}?>>hanmir.com</option>
								<option value="chol.net" <?if($erp_email_com=="chol.net"){?>selected<?}?>>chol.net</option>
								<option value="korea.com" <?if($erp_email_com=="korea.com"){?>selected<?}?>>korea.com</option>
								<option value="netsgo.com" <?if($erp_email_com=="netsgo.com"){?>selected<?}?>>netsgo.com</option>
								<option value="dreamwiz.com" <?if($erp_email_com=="dreamwiz.com"){?>selected<?}?>>dreamwiz.com</option>
								<option value="hanafos.com" <?if($erp_email_com=="hanafos.com"){?>selected<?}?>>hanafos.com</option>
								<option value="freechal.com" <?if($erp_email_com=="freechal.com"){?>selected<?}?>>freechal.com</option>
								<option value="hitel.net" <?if($erp_email_com=="hitel.net"){?>selected<?}?>>hitel.net</option>
							</select>
							<input type="hidden" id="email" name="email">
						</div>
						<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="ValidFormEmail('1','');return false;">중복확인</a></div>
					</div>
					<input type="text" class="w100-per mt-5" id="email2" name="email2" value="<?=$erp_email2?>" title="도메인 직접 입력" style="<?if($erp_email_com!="custom"){?>display: none;<?}?>" onChange="javascript:$('input[name=email_checked]').val('0');" placeholder="직접입력">
				</dd>
			</dl>
			<!--  
			<dl>
				<dt>추가정보</dt>
				<dd class="body_info">
					<label>키(cm)<input type="tel" name="height" value="" title="키" maxlength="3" tabindex="16"></label>
					<label>몸무게(kg)<input type="tel" name="weigh" value="" title="몸무게" maxlength="3" tabindex="17"></label>
					<p class="ment mt-5">※ 추가정보 모두 입력시 <?=$reserve_join_over?> E포인트 적립</p>
				</dd>
			</dl>
			-->
			<dl>
				<dt>추가정보</dt>
				<dd class="body_info">
					<label>키(cm)<input type="text" value="160" tabindex="16"></label>
					<label>몸무게(kg)<input type="text" value="60" tabindex="17"></label>
					<p class="ment mt-5">※ 추가정보 모두 입력시 <?=$reserve_join_over?> E포인트 적립</p>
				</dd>
			</dl>
			<dl>
				<dt>직업</dt>
				<dd>
					<select class="select_line w100-per" name="job_code">
						<option value="">선택</option>
						<option value="01" <?if($erp_job_cd=="01"){?>selected<?}?>>주부</option>
						<option value="02" <?if($erp_job_cd=="02"){?>selected<?}?>>자영업</option>
						<option value="03" <?if($erp_job_cd=="03"){?>selected<?}?>>사무직</option>
						<option value="04" <?if($erp_job_cd=="04"){?>selected<?}?>>생산/기술직</option>
						<option value="05" <?if($erp_job_cd=="05"){?>selected<?}?>>판매직</option>
						<option value="06" <?if($erp_job_cd=="06"){?>selected<?}?>>보험업</option>
						<option value="07" <?if($erp_job_cd=="07"){?>selected<?}?>>은행/증권업</option>
						<option value="08" <?if($erp_job_cd=="08"){?>selected<?}?>>전문직</option>
						<option value="09" <?if($erp_job_cd=="09"){?>selected<?}?>>공무원</option>
						<option value="10" <?if($erp_job_cd=="10"){?>selected<?}?>>농축산업</option>
						<option value="11" <?if($erp_job_cd=="11"){?>selected<?}?>>학생</option>
						<option value="12" <?if($erp_job_cd=="12"){?>selected<?}?>>기타</option>
					</select>
					<input type="hidden" name="job" id="job">
				</dd>
			</dl>
			<?if ($staff_join == 'Y') {?>
			<dl>
				<dt>사번</dt>
				<dd>
					<div class="input_emp_id">
						<input type="text" class="w100-per" id="user-emp-id" name="emp_id" tabindex="2" title="사번 입력자리" placeholder="사번 입력">										
					</div>
				</dd>
			</dl>
			<?}?>
			<dl>
				<dt>제휴사 코드</dt>
				<dd>
					<div class="input_addr">
						<input type="text" class="w100-per" id="cpCode" name="cpCode" tabindex="10" maxlength=8 title="제휴사 코드  입력자리" placeholder="제휴사 코드 입력" onChange="javascript:$('input[name=id_checked]').val('0');">										
						<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="ValidFormCPcode('1','');return false;return false;">코드확인</a></div>
						<p class="ment mt-5">※ 제휴사 코드는 8자리 입니다.</p>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>마케팅 활동 동의</dt>
				<dd>
					신원몰이 제공하는 다양한 이벤트 및 혜택 안내에 대한 수신동의 여부를 확인해주세요. <br>수신 체크 시 고객님을 위한 다양하고 유용한 정보를 제공합니다.
					<div class="btn_area mt-10">
						<ul class="ea3">
							<li><label for="news_mail_yn"><input type="checkbox" class="check_def" id="news_mail_yn" name="news_mail_yn" value="Y" checked> <span>이메일 수신</span></label></li>
							<li><label for="news_sms_yn"><input type="checkbox" class="check_def" id="news_sms_yn" name="news_sms_yn" value="Y" checked> <span>SMS 수신</span></label></li>
							<li><label for="mrkAgree_talk"><input type="checkbox" class="check_def" id="mrkAgree_talk" name="news_kakao_yn" value="Y" checked> <span>카카오톡 수신</span></label></li>
						</ul>
					</div>
				</dd>
			</dl>
		</div><!-- //.board_type_write -->

		<div class="btn_area mt-30">
			<ul class="ea2">
				<li><a href="javascript:;" onclick="submitCancel();" class="btn-line h-input">취소</a></li>
				<li><a href="javascript:CheckForm('1');" class="btn-point h-input">확인</a></li>
			</ul>
		</div>

	</section><!-- //.joinpage -->

</main>
<!-- //내용 -->
	</form>

<?php if( $_SERVER['HTTPS'] == 'on' ){ ?>
    <script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<?php }else{ ?>
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php } ?>
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