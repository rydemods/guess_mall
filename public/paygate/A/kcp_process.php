<?php
header("Content-Type: text/html; charset=UTF-8");
//if($_SERVER['REMOTE_ADDR']!="203.238.36.58" && $_SERVER['REMOTE_ADDR']!="203.238.36.160" && $_SERVER['REMOTE_ADDR']!="203.238.36.161" && $_SERVER['REMOTE_ADDR']!="203.238.36.173" && $_SERVER['REMOTE_ADDR']!="203.238.36.178") exit;
$ip_arr = array('203.238.36.173','203.238.36.178','210.122.73.58');
if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //아이피 인증

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");

/* ============================================================================== */
/* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
/* = -------------------------------------------------------------------------- = */
/* =   에스크로 서비스의 경우, 가상계좌 입금 통보 데이터와 가상계좌 환불        = */
/* =   통보 데이터, 구매확인/구매취소 통보 데이터, 배송시작 통보 데이터 등을    = */
/* =   KCP 를 통해 별도로 통보 받을 수 있습니다. 이러한 통보 데이터를 받기      = */
/* =   위해 가맹점측은 결과를 전송받는 페이지를 마련해 놓아야 합니다.           = */
/* =   현재의 페이지를 업체에 맞게 수정하신 후, KCP 관리자 페이지에 등록해      = */
/* =   주시기 바랍니다. 등록 방법은 연동 매뉴얼을 참고하시기 바랍니다.          = */
/* ============================================================================== */


/* ============================================================================== */
/* =   02. 공통 통보 데이터 받기                                                = */
/* = -------------------------------------------------------------------------- = */
$site_cd      = $_POST [ "site_cd"  ];                 // 사이트 코드
$tno          = $_POST [ "tno"      ];                 // KCP 거래번호
$order_no     = $_POST [ "order_no" ];                 // 주문번호
$tx_cd        = $_POST [ "tx_cd"    ];                 // 업무처리 구분 코드
$tx_tm        = $_POST [ "tx_tm"    ];                 // 업무처리 완료 시간

/* = -------------------------------------------------------------------------- = */
$ipgm_name    = "";                                    // 주문자명
$remitter     = "";                                    // 입금자명
$ipgm_mnyx    = "";                                    // 입금 금액
$bank_code    = "";                                    // 은행코드
$account      = "";                                    // 가상계좌 입금계좌번호
$op_cd        = "";                                    // 처리구분 코드
$noti_id      = "";                                    // 통보 아이디

/* = -------------------------------------------------------------------------- = */
$refund_nm    = "";                                    // 환불계좌주명
$refund_mny   = "";                                    // 환불금액
$bank_code    = "";                                    // 은행코드

/* = -------------------------------------------------------------------------- = */
$st_cd        = "";                                    // 구매확인 코드
$can_msg      = "";                                    // 구매취소 사유

/* = -------------------------------------------------------------------------- = */
$waybill_no   = "";                                    // 운송장 번호
$waybill_corp = "";                                    // 택배 업체명

$logdata="";
while(@list($key,$val)=@each($_POST)) {
	$logdata.="$key => $val, ";
}
//@backup_save_sql($logdata);
$logdata='';

/* = -------------------------------------------------------------------------- = */
/* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
/* = -------------------------------------------------------------------------- = */
if ( $tx_cd == "TX00" ) {
	$ipgm_name = $_POST[ "ipgm_name" ];					// 주문자명
	$remitter  = $_POST[ "remitter"  ];					// 입금자명
	$ipgm_mnyx = $_POST[ "ipgm_mnyx" ];					// 입금 금액
	$bank_code = $_POST[ "bank_code" ];					// 은행코드
	$account   = $_POST[ "account"   ];					// 가상계좌 입금계좌번호
	$op_cd     = $_POST[ "op_cd"     ];					// 처리구분 코드
	$noti_id   = $_POST[ "noti_id"   ];					// 통보 아이디
}

/* = -------------------------------------------------------------------------- = */
/* =   02-2. 가상계좌 환불 통보 데이터 받기                                     = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX01" ) {
	$refund_nm  = $_POST[ "refund_nm"  ];				// 환불계좌주명
	$refund_mny = $_POST[ "refund_mny" ];				// 환불금액
	$bank_code  = $_POST[ "bank_code"  ];				// 은행코드
}

/* = -------------------------------------------------------------------------- = */
/* =   02-3. 구매확인/구매취소 통보 데이터 받기                                 = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX02" ) {
	$st_cd = $_POST[ "st_cd" ];							// 구매확인 코드

	if ( $st_cd == "N" ) {								// 구매확인 상태가 구매취소인 경우
		$can_msg = $_POST[ "can_msg" ];					// 구매취소 사유
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   02-4. 배송시작 통보 데이터 받기                                          = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX03" ) {
	$waybill_no   = $_POST[ "waybill_no"   ];			// 운송장 번호
	$waybill_corp = $_POST[ "waybill_corp" ];			// 택배 업체명
}

/* = -------------------------------------------------------------------------- = */
/* =   02-5. 모바일안심결제 통보 데이터 받기                                    = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX08" ) {
	$ipgm_mnyx = $_POST[ "ipgm_mnyx" ];					// 입금 금액
	$bank_code = $_POST[ "bank_code" ];					// 은행코드
}
/* ============================================================================== */


/* ============================================================================== */
/* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
/* = -------------------------------------------------------------------------- = */
/* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업을  = */
/* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
/* =   프로세스가 구성되어 있습니다. 소스에서 result 라는 Form 값을 생성 하신   = */
/* =   후, DB 작업이 성공 한 경우, result 의 값을 "0000" 로 세팅해 주시고,      = */
/* =   DB 작업이 실패 한 경우, result 의 값을 "0000" 이외의 값으로 세팅해 주시  = */
/* =   기 바랍니다. result 값이 "0000" 이 아닌 경우에는 재통보를 받게 됩니다.   = */
/* = -------------------------------------------------------------------------- = */

$rescode="";
$date=date("YmdHis");
if(preg_match("/^(TX00|TX01|TX02|TX03|TX04|TX05|TX06|TX07)$/", $tx_cd)) {
	$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$order_no."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
	} else {
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$order_no." 주문번호 존재하지 않음","$sql");
		}
	}
	pmysql_free_result($result);
}

$tblname="";
if(strstr("P", $paymethod)) {
	$tblname="tblpcardlog";
} else if(strstr("OQ", $paymethod)) {
	$tblname="tblpvirtuallog";
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/kcp.php";
$query="ordercode=".$order_no."&tx_cd=".$tx_cd;

$f = fopen("./log/kcp_common_return_".date("Ymd").".txt","a+");
fwrite($f,"########################################## START Common_Return_".$_POST[order_no]." ".date("Y-m-d H:i:s")."\r\n");
fwrite($f,"site_cd = ".$_POST [ "site_cd"  ]."\r\n");
fwrite($f,"tno = ".$_POST [ "tno"  ]."\r\n");
fwrite($f,"order_no = ".$_POST [ "order_no"  ]."\r\n");
fwrite($f,"tx_cd = ".$_POST [ "tx_cd"  ]."\r\n");
fwrite($f,"tx_tm = ".$_POST [ "tx_tm"  ]."\r\n");
fwrite($f,"ipgm_name = ".$_POST [ "ipgm_name"  ]."\r\n");
fwrite($f,"remitter = ".$_POST [ "remitter"  ]."\r\n");
fwrite($f,"ipgm_mnyx = ".$_POST [ "ipgm_mnyx"  ]."\r\n");
fwrite($f,"bank_code = ".$_POST [ "bank_code"  ]."\r\n");
fwrite($f,"account = ".$_POST [ "account"  ]."\r\n");
fwrite($f,"op_cd = ".$_POST [ "op_cd"  ]."\r\n");
fwrite($f,"noti_id = ".$_POST [ "noti_id"  ]."\r\n");
fwrite($f,"cash_a_no = ".$_POST [ "cash_a_no"  ]."\r\n");
fwrite($f,"cash_a_dt = ".$_POST [ "cash_a_dt"  ]."\r\n");
fwrite($f,"REMOTE_ADDR = ".$_SERVER[REMOTE_ADDR]."\r\n");
fwrite($f,"########################################## END Common_Return_".$_POST[order_no]." ".date("Y-m-d H:i:s")."\r\n\r\n");
fclose($f);
chmod("./log/kcp_common_return_".date("Ymd").".txt",0777);
/* = -------------------------------------------------------------------------- = */
/* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
/* = -------------------------------------------------------------------------- = */
if ( $tx_cd == "TX00" ) {
	if(strstr("OQ", $paymethod)) {
		####################### ok가 "M|Y", status가 "N"인 경우에만 정상처리 ########################
		$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
		$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
        //log_txt_tmp($sql);
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$query.="&price=".$ipgm_mnyx."&ok=";
			if($op_cd=="13") $query.="C";
			else $query.="Y";

			if($op_cd=="13") {
				if($row->noti_id==$noti_id) {
					if($row->ok=="Y" && $row->status=="N") {
						$send_data=SendSocketPost($return_host, $return_script, $query);
						$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
						if (substr($send_data,0,2)=="OK") {
							$sql = "UPDATE ".$tblname." SET ";
							$sql.= "ok			= 'M', ";
							$sql.= "bank_price	= NULL, ";
							$sql.= "remitter	= '', ";
							$sql.= "bank_code	= '', ";
							$sql.= "bank_date	= '', ";
							$sql.= "receive_date= '' ";
							$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
							pmysql_query($sql,get_db_conn());
							if(!pmysql_error()) {
								$rescode="0000";
							} else {
								if(strlen(AdminMail)>0) {
									@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보취소 업데이트 오류","$sql");
								}
							}
						}
					}
				}
			} else {
				if($row->ok=="M" && $row->status=="N") {
					$send_data=SendSocketPost($return_host, $return_script, $query);
					$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
					if (substr($send_data,0,2)=="OK") {
						$sql = "UPDATE ".$tblname." SET ";
						$sql.= "ok			= 'Y', ";
						$sql.= "bank_price	= '".$ipgm_mnyx."', ";
						$sql.= "remitter	= '". iconv("EUC-KR","UTF-8",$remitter)."', ";
						$sql.= "bank_code	= '".$bank_code."', ";
						$sql.= "bank_date	= '".$tx_tm."', ";
						$sql.= "receive_date= '".$date."', ";
						$sql.= "noti_id		= '".$noti_id."' ";
						$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
						pmysql_query($sql,get_db_conn());
                        //log_txt_tmp($sql);
						if(!pmysql_error()) {
							$rescode="0000";
						} else {
							if(strlen(AdminMail)>0) {
								@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보 업데이트 오류","$sql");
							}
						}
					}
				}
			}
/*
			$send_data=SendSocketPost($return_host, $return_script, $query);
			$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
			if (substr($send_data,0,2)=="OK") {
				//DB업데이트
				$sql = "UPDATE ".$tblname." SET ";
				if($op_cd=="13") {
					$sql.= "ok			= 'N', ";
					$sql.= "bank_price	= NULL, ";
					$sql.= "remitter	= '', ";
					$sql.= "bank_code	= '', ";
					$sql.= "bank_date	= '', ";
					$sql.= "receive_date= '' ";
				} else {
					$sql.= "ok			= 'Y', ";
					$sql.= "bank_price	= '".$ipgm_mnyx."', ";
					$sql.= "remitter	= '".$remitter."', ";
					$sql.= "bank_code	= '".$bank_code."', ";
					$sql.= "bank_date	= '".$tx_tm."', ";
					$sql.= "receive_date= '".$date."', ";
					$sql.= "noti_id		= '".$noti_id."' ";
				}
				$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
				pmysql_query($sql,get_db_conn());
				if(!pmysql_error()) {
					$rescode="0000";
				} else {
					if(strlen(AdminMail)>0) {
						@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보 업데이트 오류","$sql");
					}
				}
			}
*/
		} else {
			$rescode="0000";
		}
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-2. 가상계좌 환불 통보 데이터 DB 처리 작업 부분                        = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX01" ) {
	if(strstr("Q", $paymethod)) {
		########################## status가 "F"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if(strstr("CY", $row->ok) && $row->status=="F") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok				= 'C', ";
					$sql.= "status			= 'E', ";
					$sql.= "refund_name		= '".$refund_nm."', ";
					$sql.= "refund_bank_code= '".$bank_code."', ";
					$sql.= "refund_price	= '".$refund_mny."', ";
					$sql.= "refund_date		= '".$tx_tm."', ";
					$sql.= "refund_receive_date='".$date."' ";
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 가상계좌 환불통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-3. 구매확인/구매취소 통보 데이터 DB 처리 작업 부분                    = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX02" ) {
	
	//구매확인은 솔루션 기간으로 한다.2017-04-07
	if(strstr("QP", $paymethod) && $st_cd=="N") {
		########################## status가 "S"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="S") {
				if(strstr("YS", $st_cd)) $query.="&ok=Y";
				else if($st_cd=="N") $query.="&ok=C";
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					if(strstr("YS", $st_cd)) {	//구매확인
						$sql.= "status	= 'Y' ";
					} else if($st_cd=="N") {				//구매취소
						$sql.= "status	= 'H' ";
					}
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 구매확인/구매취소통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
	
}

/* = -------------------------------------------------------------------------- = */
/* =   03-4. 배송시작 통보 데이터 DB 처리 작업 부분                             = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX03" ) {
	if(strstr("QP", $paymethod)) {
		########################## status가 "N"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="N") {
				$query.="&deli_num=".$deli_numb."&deli_name=".$deli_corp;
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "status	= 'S' ";
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 배송시작통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-5. 정산보류 통보 데이터 DB 처리 작업 부분                             = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX04" ) {
	if(strstr("QP", $paymethod)) {
		########################## status가 "S"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="S") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "status	= 'H' ";
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 정산보류통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-6. 즉시취소 통보 데이터 DB 처리 작업 부분                             = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX05" ) {
	if(strstr("QP", $paymethod)) {
		########################## status가 "N"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="N") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					if($paymethod=="P") {
						$sql.= "ok		= 'C', ";
						$sql.= "status	= 'D' ";
					} else if($paymethod=="Q") {
						$sql.= "status	= 'F' ";	//환불대기 세팅
					}
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 즉시취소통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-7. 취소 통보 데이터 DB 처리 작업 부분                                 = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX06" ) {
	if(strstr("QP", $paymethod)) {
		########################## status가 "H"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="Y" && $row->status=="H") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					if($paymethod=="P") {
						$sql.= "ok		= 'C', ";
						$sql.= "status	= 'X' ";
					} else if($paymethod=="Q") {
						$sql.= "status	= 'F' ";	//환불대기 세팅
					}
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 취소통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-8. 발급계좌해지 통보 데이터 DB 처리 작업 부분                         = */
/* = -------------------------------------------------------------------------- = */
else if ( $tx_cd == "TX07" ) {
	if(strstr("Q", $paymethod)) {
		########################## status가 "N"인 경우, ok가 "M"인 경우에만 정상처리 #########################
		$sql="SELECT ok, status FROM ".$tblname." WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->ok=="M" && $row->status=="N") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok		= 'C', ";
					$sql.= "status	= 'G' ";	//발급계좌해지 (G)
					$sql.= "WHERE ordercode='".$order_no."' AND trans_code='".$tno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rescode="0000";
					} else {
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$order_no." 발급계좌해지통보 업데이트 오류","$sql");
						}
					}
				}
			} else {
				$rescode="0000";
			}
		} else {
			$rescode="0000";
		}
		pmysql_free_result($result);
	}
}

/* = -------------------------------------------------------------------------- = */
/* =   03-9. 모바일안심결제 통보 데이터 DB 처리 작업 부분                       = */
/* = -------------------------------------------------------------------------- = */
/*
else if ( $tx_cd == "TX08" ) {

}
*/
/* ============================================================================== */


//$rescode="0000";	//성공적으로 처리되었을 때....

/* ============================================================================== */
/* =   04. result 값 세팅 하기                                                  = */
/* ============================================================================== */
?>
<html><body><form><input type="hidden" name="result" value="<?=$rescode?>"></form></body></html>