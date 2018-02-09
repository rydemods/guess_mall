<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:../index.php");
	exit;
}

################## 변수들 ##############################

		$id=trim($_POST["id"]);
		$passwd1=$_POST["passwd1"];
		$passwd2=$_POST["passwd2"];
		///$name=trim($_SESSION[ipin][name]);
		$name=trim($_POST["name"]);
		$resno1=trim($_POST["resno1"]);
		$resno2=trim($_POST["resno2"]);
		$email=trim($_POST["email"]);
		$news_mail_yn=$_POST["news_mail_yn"];
		$news_sms_yn=$_POST["news_sms_yn"];
		$home_tel=implode("-",$_POST['home_tel']);
		$home_post1=trim($_POST["home_post1"]);
		$home_post2=trim($_POST["home_post2"]);
		$home_addr1=trim($_POST["home_addr1"]);
		$home_addr2=trim($_POST["home_addr2"]);
		$mobile=implode('-',$_POST['mobile']);
		$office_post1=trim($_POST["office_post1"]);
		$office_post2=trim($_POST["office_post2"]);
		$office_addr1=trim($_POST["office_addr1"]);
		$office_addr2=trim($_POST["office_addr2"]);
		$rec_id=trim($_POST["rec_id"]);
		$etc=$_POST["etc"];
		$dupinfo=trim($_POST["dupinfo"]);
		$birth=trim($_POST["birth"]);
		$group_code=(trim($_POST["group_code"]))?trim($_POST["group_code"]):"0003";
		
###########################################################

//################### Insert ##############################
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

				if($_data->member_baro=="Y") $confirm_yn="N";
				else $confirm_yn="Y";
				//사업자회원 승인관련
				//if($mem_type=='1')  $confirm_yn="N";

				$home_addr=$home_addr1."=".$home_addr2;
				$office_addr="";
				if(strlen($office_post)==6) $office_addr=$office_addr1."=".$office_addr2;

				$sql = "INSERT INTO tblmember(id) VALUES('{$id}')";
				pmysql_query($sql,get_db_conn());
				$sql = "UPDATE tblmember SET ";
				$sql.= "id			= '{$id}', ";
				$sql.= "passwd		= '".md5($passwd1)."', ";
				$sql.= "name		= '{$name}', ";
				$sql.= "resno		= '{$resno}', ";
				$sql.= "email		= '{$email}', ";
				$sql.= "mobile		= '{$mobile}', ";
				$sql.= "news_yn		= '{$news_yn}', ";
				$sql.= "gender		= '{$gender}', ";
				$sql.= "home_post	= '{$home_post}', ";
				$sql.= "home_addr	= '{$home_addr}', ";
				$sql.= "home_tel	= '{$home_tel}', ";
				$sql.= "reserve		= '{$reserve_join}', ";
				$sql.= "joinip		= '{$ip}', ";
				$sql.= "ip			= '{$ip}', ";
				$sql.= "date		= '{$date}', ";
				$sql.= "confirm_yn	= '{$confirm_yn}', ";
				$sql.= "dupinfo	= '{$dupinfo}', ";
				$sql.= "birth	= '{$birth}', ";
				
				if($mem_type){
					$sql.= "office_post	= '{$office_post}', ";
					$sql.= "office_addr	= '{$office_addr}', ";
					$sql.= "office_tel	= '{$office_tel}', ";
					$sql.= "office_name	= '{$office_name}', ";
					$sql.= "office_representative	= '{$office_representative}', ";
					$sql.= "office_no	= '{$office_no}', ";
					$sql.= "mem_type	= '{$mem_type}', ";
				}
				
				if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
					$sql.= "rec_id	= '{$rec_id}', ";
				}
				if(ord($group_code)) {
					$sql.= "group_code='{$group_code}', ";
				}
				$sql.= "etcdata		= '{$etcdata}' WHERE id='{$id}'";
				$insert=pmysql_query($sql,get_db_conn());
				if (pmysql_errno()==0) {
					if ($reserve_join>0) {
						$sql = "INSERT INTO tblreserve(id,reserve,reserve_yn,content,orderdata,date) VALUES (
						'{$id}', 
						{$reserve_join}, 
						'Y', 
						'가입축하 적립금입니다. 감사합니다.', 
						'', 
						'".date("YmdHis",time()-1)."') ";
						$insert = pmysql_query($sql,get_db_conn());
					}

					if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && ord($rec_id)) {
						$rec_id_reserve=0;
						$id_reserve=0;
						if($recom_addreserve>0) {
							SetReserve($id,$recom_addreserve,"추천인 적립금입니다. 감사합니다.");

							$id_reserve=$recom_addreserve;
						}
						if($recom_memreserve>0) {
							$mess=$id."님이 추천하셨습니다. 감사합니다.";
							SetReserve($rec_id,$recom_memreserve,$mess);

							$rec_id_reserve=$recom_memreserve;
						}

						//추천인 등록
						if($rec_cnt>0) {	//update
							$sql2 = "UPDATE tblrecommendmanager SET rec_cnt=rec_cnt+1 ";
							$sql2.= "WHERE rec_id='{$rec_id}' ";
						} else {			//insert
							$sql2 = "INSERT INTO tblrecommendmanager(rec_id,rec_cnt,date) VALUES ( 
							'{$rec_id}', 
							'1', 
							'{$date}')";
						}
						pmysql_query($sql2,get_db_conn());
						
						$sql2 = "INSERT INTO tblrecomendlist(rec_id,id,rec_id_reserve,id_reserve,date) VALUES (
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
						SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name);
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
						if($mem_join=="Y") {
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

					//alert_go("등록되었습니다.\\n{$msg}\\n감사합니다.",$Dir.MainDir."main.php");
					echo "<script>location.href='".$Dir.FrontDir."member_joinend.php?name=$name&id=$id'</script>";
				} else {
					$onload="ID가 중복되었거나 회원등록 중 오류가 발생하였습니다.";
				}



//############ Insert 끝 #####################
?>