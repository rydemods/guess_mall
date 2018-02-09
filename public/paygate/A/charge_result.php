<?php
//header("Content-Type: text/html; charset=UTF-8");
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

require "global.lib.php";


//res_msg res_cd
//phpinfo();
//exit;
# 결제 체크
$paycode    = $_POST['param_opt_2']; // 결제코드
$basketidxs = $_POST['param_opt_3']; // 장바구니 idxs

$f = fopen("./log/kcp_charge_result_".date("Ymd").".txt","a+");
fwrite($f,"date : ".date("Y-m-d H:i:s")."\r\n");
fwrite($f,"ordercode : ".$ordr_idxx."\r\n");
fwrite($f,"tran_cd : ".$tran_cd."\r\n");
fwrite($f,"bank_issu : ".$bank_issu."\r\n");
fwrite($f,"req_tx : ".$req_tx."\r\n");
fwrite($f,"bSucc : ".$bSucc."\r\n");
fwrite($f,"res_cd : ".$res_cd."\r\n");
fwrite($f,"isreload : ".$isreload."\r\n");
fwrite($f,"use_pay_method : ".$use_pay_method."\r\n");
fwrite($f,"pay_method : ".$_POST[pay_method]."\r\n");
fwrite($f,"res_cd : ".$_POST[res_cd]."\r\n");
fwrite($f,"res_msg : ".$_POST[res_msg]."\r\n");
fwrite($f,"결제코드 : ".$paycode."\r\n");
fwrite($f,"장바구니idx : ".$basketidxs."\r\n");
fwrite($f,"escw_used : ".$escw_used."\r\n");
fwrite($f,"pay_mod : ".$pay_mod."\r\n");




if ( $bank_issu != "SCOB" ) {
	$c_PayPlus=new C_PP_CLI;
	############### 승인요청 ###################
	if($req_tx=="pay") {
		$c_PayPlus->mf_set_encx_data($_POST["enc_data"] , $_POST["enc_info"]);
	}
	
	############## 실행 ########################
	if($tran_cd!="") {
		$c_PayPlus->mf_do_tx($trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "", $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx, $cust_ip, $g_conf_log_level, 0, $g_conf_mode);

		$tno=$c_PayPlus->mf_get_res_data("tno");
	} else {
		$c_PayPlus->m_res_cd="9562";
		$c_PayPlus->m_res_msg="연동 오류.";
	}

	$res_cd=$c_PayPlus->m_res_cd;
	$res_msg=$c_PayPlus->m_res_msg;
	
	$bSucc=true;

	################# 승인결과처리 #################
	if ($req_tx=="pay") {
		if( $res_cd=="0000") {
			################## 05-1. 신용카드 승인 결과 처리 #############
			if ($use_pay_method=="100000000000") {
				$card_cd          = $c_PayPlus->mf_get_res_data( "card_cd"   );  // 카드 코드
				$card_name        = $c_PayPlus->mf_get_res_data( "card_name" );  // 카드 종류
				$app_time         = $c_PayPlus->mf_get_res_data( "app_time"  );  // 승인 시간
				$app_no           = $c_PayPlus->mf_get_res_data( "app_no"    );  // 승인 번호
				$noinf            = $c_PayPlus->mf_get_res_data( "noinf"     );  // 무이자 여부 ( 'Y' : 무이자 )
				$quota            = $c_PayPlus->mf_get_res_data( "quota"     );  // 할부 개월
			}

			/* = -------------------------------------------------------------------------- = */
			/* =   05-1-1. 금융결제원 계좌이체 승인 결과 처리                               = */
			/* = -------------------------------------------------------------------------- = */
			if ($use_pay_method == "010000000000" ) {
				$bank_name        = $c_PayPlus->mf_get_res_data( "bank_name" );  // 이체한 은행 이름
			}

			################# 05-2. 가상계좌 승인 결과 처리 #############
			if ($use_pay_method=="001000000000") {
				$bankname         = $c_PayPlus->mf_get_res_data( "bankname"  );  // 입금할 은행 이름
				$depositor        = $c_PayPlus->mf_get_res_data( "depositor" );  // 입금할 계좌 예금주
				$account          = $c_PayPlus->mf_get_res_data( "account"   );  // 입금할 계좌 번호
			}

			################# 05-3. 휴대폰 승인 결과 처리 ###############
			if ($use_pay_method=="000010000000") {
				$app_time         = $c_PayPlus->mf_get_res_data( "hp_app_time"  );  // 승인 시간
				$commid           = $c_PayPlus->mf_get_res_data( "commid"	     ); // 통신사 코드
                $mobile_no        = $c_PayPlus->mf_get_res_data( "mobile_no"	 ); // 휴대폰 번호
			}
			    /* = -------------------------------------------------------------------------- = */
    /* =   05-7. 현금영수증 결과 처리                                               = */
    /* = -------------------------------------------------------------------------- = */
            $cash_authno  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호

			
		}
		$escw_yn = $c_PayPlus->mf_get_res_data( "escw_yn"  ); // 에스크로 여부 
	}
} else {	########## 08. 계좌이체 결과 처리 (전문통신을 하지 않는 경우) ##############
	$res_cd    = $_POST["res_cd"];	// 응답코드
	$res_msg   = $_POST["res_msg"];	// 응답메시지
	
	if ($use_pay_method=="010000000000") {
		$bank_name=$_POST[ "bank_name" ];	// 은행명
	}
}
fwrite($f,"escw_yn : ".$escw_yn."\r\n");
fwrite($f,"----------------------------------\r\n");
fclose($f);
chmod("./log/kcp_charge_result_".date("Ymd").".txt",0777);
if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}

$return_host=$_SERVER['HTTP_HOST'];
$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payprocess.php";
$return_resurl=$shopurl.FrontDir."payresult.php?ordercode=".$ordr_idxx."&paycode=".$paycode;

$isreload=false;
$tblname="";
$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordr_idxx."' ";
//exdebug($sql);
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
	if(strstr("CP", $paymethod)) $tblname="tblpcardlog";
	else if(strstr("OQ", $paymethod)) $tblname="tblpvirtuallog";
	else if($paymethod=="M") $tblname="tblpmobilelog";
	else if($paymethod=="V") $tblname="tblptranslog";
}
pmysql_free_result($result);

if(strlen($tblname)>0) {
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordr_idxx."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$isreload=true;
		$pay_data=$row->pay_data;
		$good_mny = $row->price;
		if ($row->ok=="Y") {
			$PAY_GLAG="0000";
			$DELI_GBN="N";
		} else if ($row->ok=="N") {
			$PAY_FLAG="9999";
			$DELI_GBN="C";
		}
		if(strstr("CP", $paymethod)) $PAY_AUTH_NO = "00000000";
	}
	pmysql_free_result($result);
}



if ( $bank_issu != "SCOB" ) {
	if ($req_tx == "pay") {    // 거래 구분 : 승인
		
		if ($bSucc) {
			if($isreload!=true) {
				$date=date("YmdHis");
				if ($res_cd == "0000") {	//정상승인
					$PAY_FLAG="0000";
					$DELI_GBN="N";
					$MSG1=$res_msg;
					$pay_data=$res_msg;
					$ok="Y";
					if ($use_pay_method == "100000000000") {	//신용카드
						$tblname="tblpcardlog";
						$paymethod="C";
						if($pay_mod=="Y") $paymethod="P";
						$PAY_AUTH_NO=$app_no;
						$MSG1="정상승인 - 승인번호 : ".$PAY_AUTH_NO;
						$pay_data="승인번호 : ".$app_no."";
					} else if ($use_pay_method == "010000000000") {	//계좌이체
						$tblname="tblptranslog";
						$paymethod="V";
						$PAY_AUTH_NO="";
						$card_name="";
						$noinf="";
						$quota="";
					} else if ($use_pay_method == "001000000000") { //가상계좌
						$ok="M";
						$tblname="tblpvirtuallog";
						$paymethod="O";
						if($pay_mod=="Y") $paymethod="Q";
						$PAY_AUTH_NO="";
						$card_name="";
						$noinf="";
						$quota="";
						$pay_data=$bankname." ".$account." (예금주:".$depositor.")";
					} else if ($use_pay_method == "000010000000") { //휴대폰
						$tblname="tblpmobilelog";
						$paymethod="M";
						$PAY_AUTH_NO="";
						$card_name="";
						$noinf="";
						$quota="";
					}
					$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
					pmysql_query($sql,get_db_conn());
					//backup_save_sql($sql);
					/*
					$sql = "INSERT ".$tblname." SET ";
					$sql.= "ordercode		= '".$ordr_idxx."', ";
					$sql.= "trans_code		= '".$tno."', ";
					$sql.= "pay_data		= '".$pay_data."', ";
					$sql.= "pgtype			= 'A', ";
					$sql.= "ok				= '".$ok."', ";
					$sql.= "okdate			= '".$date."', ";
					$sql.= "price			= '".$good_mny."', ";
					if ($use_pay_method == "100000000000") {		//신용카드
						$sql.= "status			= 'N', ";
						$sql.= "paymethod		= '".$paymethod."', ";
						$sql.= "edidate			= '".$date."', ";
						$sql.= "cardname		= '".$card_name."', ";
						$sql.= "noinf			= '".$noinf."', ";
						$sql.= "quota			= '".$quota."', ";
					} else if($use_pay_method == "010000000000") {	//계좌이체
						$sql.= "bank_name		= '".$bank_name."', ";
					} else if($use_pay_method=="001000000000") {	//가상계좌
						$sql.= "status			= 'N', ";
						$sql.= "paymethod		= '".$paymethod."', ";
						$sql.= "sender_name		= '".$buyr_name."', ";
						$sql.= "account			= '".$account."', ";
					} else if ($use_pay_method == "000010000000") { //휴대폰

					}
					$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
					$sql.= "goodname		= '".$good_name."', ";
					$sql.= "msg				= '".$MSG1."' ";
					*/
					if ($use_pay_method == "100000000000") {//신용카드
						$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota";
						$addQueryVal = ", 'N', '".$paymethod."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."'";
					} else if($use_pay_method == "010000000000") {//계좌이체
						$addQueryCol = ", bank_name";
						$addQueryVal = ", '".$bank_name."'";
					} else if($use_pay_method=="001000000000") {//가상계좌
						$addQueryCol = ", status, paymethod, sender_name, account";
						$addQueryVal = ", 'N', '".$paymethod."', '".$buyr_name."', '".$account."'";
					} else if ($use_pay_method == "000010000000") {//휴대폰
					}
					$sql = "
						INSERT INTO ".$tblname." 
						(
							ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."
						)
						VALUES
						(
							'".$ordr_idxx."', '".$tno."', '".$pay_data."', 'A', '".$ok."', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."
						)
					";
					pmysql_query($sql,get_db_conn());
					//backup_save_sql($sql);
				} else {	//승인실패
					
					$PAY_FLAG="9999";
					$DELI_GBN="C";
					$MSG1=$res_msg;
					$PAY_AUTH_NO="";
					$pay_data=$res_msg;
					if ($use_pay_method == "100000000000") {	//신용카드
						$tblname="tblpcardlog";
						$paymethod="C";
						if($pay_mod=="Y") $paymethod="P";
					} else if ($use_pay_method == "010000000000") {	//계좌이체
						$tblname="tblptranslog";
						$paymethod="V";
						$card_name="";
						$noinf="";
						$quota="";
					} else if ($use_pay_method == "001000000000") { //가상계좌
						$tblname="tblpvirtuallog";
						$paymethod="O";
						if($pay_mod=="Y") $paymethod="Q";
						$card_name="";
						$noinf="";
						$quota="";
					} else if ($use_pay_method == "000010000000") { //휴대폰
						$tblname="tblpmobilelog";
						$paymethod="M";
						$card_name="";
						$noinf="";
						$quota="";
					}
					

					$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
					pmysql_query($sql,get_db_conn());
					//backup_save_sql($sql);
					/*
					$sql = "INSERT ".$tblname." SET ";
					$sql.= "ordercode		= '".$ordr_idxx."', ";
					$sql.= "trans_code		= '".$tno."', ";
					$sql.= "pay_data		= 'ERROR', ";
					$sql.= "pgtype			= 'A', ";
					$sql.= "ok				= 'N', ";
					$sql.= "okdate			= '".$date."', ";
					$sql.= "price			= '".$good_mny."', ";
					if ($use_pay_method == "100000000000") {		//신용카드
						$sql.= "status			= 'N', ";
						$sql.= "paymethod		= '".$paymethod."', ";
						$sql.= "edidate			= '".$date."', ";
						$sql.= "cardname		= '".$card_name."', ";
						$sql.= "noinf			= '".$noinf."', ";
						$sql.= "quota			= '".$quota."', ";
					} else if($use_pay_method == "010000000000") {	//계좌이체
						$sql.= "bank_name		= '".$bank_name."', ";
					} else if($use_pay_method=="001000000000") {	//가상계좌
						$sql.= "status			= 'N', ";
						$sql.= "paymethod		= '".$paymethod."', ";
						$sql.= "sender_name		= '".$buyr_name."', ";
						$sql.= "account			= '".$account."', ";
					} else if ($use_pay_method == "000010000000") { //휴대폰

					}
					$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
					$sql.= "goodname		= '".$good_name."', ";
					$sql.= "msg				= '".$MSG1."' ";
					*/
					
					if ($use_pay_method == "100000000000") {//신용카드
						$addQueryCol = ", status, paymethod, edidate, cardname, noinf, quota";
						$addQueryVal = ", 'N', '".$paymethod."', '".$date."', '".$card_name."', '".$noinf."', '".$quota."'";
					} else if($use_pay_method == "010000000000") {//계좌이체
						$addQueryCol = ", bank_name";
						$addQueryVal = ", '".$bank_name."'";
					} else if($use_pay_method=="001000000000") {//가상계좌
						$addQueryCol = ", status, paymethod, sender_name, account";
						$addQueryVal = ", 'N', '".$paymethod."', '".$buyr_name."', '".$account."'";
					} else if ($use_pay_method == "000010000000") {//휴대폰
					}
					 if ($_POST[res_cd] == "3001"){ //사용자 결제 취소 처리
						//$_POST[pay_method] : 100000000000
						//$_POST[res_cd] : 3001
						//$_POST[res_msg] : 사용자 취소(-005)
						if ($_POST[pay_method] == "100000000000") {	//신용카드
							$tblname2="tblpcardlog";
						} else if ($_POST[pay_method] == "010000000000") {	//계좌이체
							$tblname2="tblptranslog";
						} else if ($_POST[pay_method] == "001000000000") { //가상계좌
							$tblname2="tblpvirtuallog";
						} else if ($_POST[pay_method] == "000010000000") { //휴대폰
							$tblname2="tblpmobilelog";
						}
						$sql = "
							INSERT INTO ".$tblname2." 
								(
									ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."									
								)
								VALUES
								(
									'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$_POST[res_msg]."' ".$addQueryVal."						
								)
						";
					} else {
						$sql = "
								INSERT INTO ".$tblname." 
								(
									ordercode, trans_code, pay_data, pgtype, ok, okdate, price, ip, goodname, msg ".$addQueryCol."									
								)
								VALUES
								(
									'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."' ".$addQueryVal."						
								)
						";
					}
					/*
					echo $res_cd."<br>";
					echo $res_msg."<br>";
					echo $req_tx."<br>";
					echo $bSucc."<br>";
					echo $use_pay_method."<br>";
					echo $tblname."<br>";
					echo $paymethod."<br>";
					echo $sql."<br>";
					*/
					//exit;
					pmysql_query($sql,get_db_conn());
					//backup_save_sql($sql);
					
				}
			}			
			$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
            $return_data .= "&paycode=".$paycode."&basketidxs=".$basketidxs;
			$return_data2=str_replace("'","",$return_data);
			$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
			pmysql_query($sql,get_db_conn());
			//backup_save_sql($sql);

			$temp = SendSocketPost($return_host,$return_script,$return_data);
			if( trim( $temp ) != "ok" ) {
				//error (메일 발송)
				if(strlen(AdminMail)>0) {
					@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
				}
                // error ( 결제취소 )
                $bSucc = false;
			} else {
				//pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
			}
		}
	}
} else {
	if($isreload!=true) {
		$date=date("YmdHis");
		if ($res_cd == "0000") {	//정상승인
			$PAY_FLAG="0000";
			$DELI_GBN="N";
			$MSG1=$res_msg;
			$pay_data=$res_msg;
			$ok="Y";
			if ($use_pay_method == "010000000000") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod="V";
				$PAY_AUTH_NO="";
				$card_name="";
				$noinf="";
				$quota="";

				$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
				pmysql_query($sql,get_db_conn());
				//backup_save_sql($sql);
				/*
				$sql = "INSERT ".$tblname." SET ";
				$sql.= "ordercode		= '".$ordr_idxx."', ";
				$sql.= "trans_code		= '".$tno."', ";
				$sql.= "pay_data		= '".$pay_data."', ";
				$sql.= "pgtype			= 'A', ";
				$sql.= "ok				= '".$ok."', ";
				$sql.= "okdate			= '".$date."', ";
				$sql.= "price			= '".$good_mny."', ";
				$sql.= "bank_name		= '".$bank_name."', ";
				$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
				$sql.= "goodname		= '".$good_name."', ";
				$sql.= "msg				= '".$MSG1."' ";
				*/
				$sql = "
							INSERT INTO ".$tblname." 
							(
								ordercode, trans_code, pay_data, pgtype, ok, okdate, price, bank_name, ip, goodname, msg
							)
							VALUES
							(
								'".$ordr_idxx."', '".$tno."', '".$pay_data."', 'A', '".$ok."', '".$date."', '".$good_mny."', '".$bank_name."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
							)
				";
				pmysql_query($sql,get_db_conn());
				//backup_save_sql($sql);
			}			
		} else {	//승인실패
			$PAY_FLAG="9999";
			$DELI_GBN="C";
			$MSG1=$res_msg;
			$PAY_AUTH_NO="";
			$pay_data=$res_msg;
			if ($use_pay_method == "010000000000") {	//계좌이체
				$tblname="tblptranslog";
				$paymethod="V";
				$card_name="";
				$noinf="";
				$quota="";

				$sql = "INSERT INTO tblpordercode VALUES ('".$ordr_idxx."','".$paymethod."') ";
				pmysql_query($sql,get_db_conn());
				//backup_save_sql($sql);
				/*
				$sql = "INSERT ".$tblname." SET ";
				$sql.= "ordercode		= '".$ordr_idxx."', ";
				$sql.= "trans_code		= '".$tno."', ";
				$sql.= "pay_data		= 'ERROR', ";
				$sql.= "pgtype			= 'A', ";
				$sql.= "ok				= 'N', ";
				$sql.= "okdate			= '".$date."', ";
				$sql.= "price			= '".$good_mny."', ";
				$sql.= "bank_name		= '".$bank_name."', ";
				$sql.= "ip				= '".$_SERVER['REMOTE_ADDR']."', ";
				$sql.= "goodname		= '".$good_name."', ";
				$sql.= "msg				= '".$MSG1."' ";
				*/
				$sql = "
							INSERT INTO ".$tblname." 
							(
								ordercode, trans_code, pay_data, pgtype, ok, okdate, price, bank_name, ip, goodname, msg
							)
							VALUES
							(
								'".$ordr_idxx."', '".$tno."', 'ERROR', 'A', 'N', '".$date."', '".$good_mny."', '".$bank_name."', '".$_SERVER['REMOTE_ADDR']."', '".$good_name."', '".$MSG1."'
							)
				";
				pmysql_query($sql,get_db_conn());
				//backup_save_sql($sql);
			}
		}
	}

	$return_data="ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=$pay_data&pay_flag=$PAY_FLAG&pay_auth_no=$PAY_AUTH_NO&deli_gbn=$DELI_GBN&message=$MSG1";
    $return_data .= "&paycode=".$paycode."&basketidxs=".$basketidxs;
	$return_data2=str_replace("'","",$return_data);
	$sql = "INSERT INTO tblreturndata VALUES ('".$ordr_idxx."','".date("YmdHis")."','".$return_data2."') ";
	pmysql_query($sql,get_db_conn());
	//backup_save_sql($sql);

	$temp = SendSocketPost($return_host,$return_script,$return_data);
	if( trim( $temp ) != "ok" ) {
		//error (메일 발송)
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"[PG] ".$ordr_idxx." 결제정보 업데이트 오류","$return_host<br>$return_script<br>$return_data");
		}
        // error 주문취소
        $bSucc = false;
	} else {
		//pmysql_query("DELETE FROM tblreturndata WHERE ordercode='".$ordr_idxx."'",get_db_conn());
	}
}

if (  $req_tx == "pay" )
{
    if( $res_cd == "0000" )
    {
        if ( $bSucc === false ) // $bSucc == "false"
        {
            $c_PayPlus->mf_clear();

            $tran_cd = "00200000";
			
    /* ============================================================================== */
    /* =   07-1.자동취소시 에스크로 거래인 경우                                     = */
    /* = -------------------------------------------------------------------------- = */
                // 취소시 사용하는 mod_type
                $bSucc_mod_type = "";

                // 에스크로 가상계좌 건의 경우 가상계좌 발급취소(STE5)
                if ( $escw_yn == "Y" && $use_pay_method == "001000000000" )
                {
                    $bSucc_mod_type = "STE5";
                }
                // 에스크로 가상계좌 이외 건은 즉시취소(STE2)
                else if ( $escw_yn == "Y" )
                {
                    $bSucc_mod_type = "STE2";
                }
                // 에스크로 거래 건이 아닌 경우(일반건)(STSC)
                else
                {
                    $bSucc_mod_type = "STSC"; 
                }
	/* = -------------------------------------------------------------------------- = */
	/* =   07-1. 자동취소시 에스크로 거래인 경우 처리 END                           = */
    /* = ========================================================================== = */
                /*
                $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type              );  // 원거래 변경 요청 종류
                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                $c_PayPlus->mf_set_modx_data( "mod_desc", "가맹점 결과 처리 오류 - 가맹점에서 취소 요청" );  // 변경 사유

            $c_PayPlus->mf_do_tx( "", $g_conf_home_dir, $g_conf_site_cd, "", $tran_cd, "",
                              $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, "3", 0, 0, $g_conf_log_path); // 응답 전문 처리

                $res_cd  = $c_PayPlus->m_res_cd;
                $res_msg = $c_PayPlus->m_res_msg;
*/
        // End of [res_cd = "0000"]
    /* = -------------------------------------------------------------------------- = */
    /* =   07. 승인 결과 DB 처리 END                                                = */
    /* = ========================================================================== = */

            $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type                       );  // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유
            /*
            $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                          $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                          $cust_ip, $g_conf_log_level, 0, 0, $g_conf_log_path ); // 응답 전문 처리
            */
            $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "", 
                          $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx, 
                          $cust_ip, $g_conf_log_level, 0, $g_conf_mode );

            $res_cd  = $c_PayPlus->m_res_cd;
            $res_msg = $c_PayPlus->m_res_msg;
            $msg     = $res_msg." ( db error) ";
            //$PAY_FLAG = '9999';
            $fc = fopen("./log/kcp_cancel_result_".date("Ymd").".txt","a+");
            fwrite($fc,"date : ".date("Y-m-d H:i:s")."\r\n");
            fwrite($fc,"ordercode : ".$ordr_idxx."\r\n");
            fwrite($fc,"tran_cd : ".$tran_cd."\r\n");
            fwrite($fc,"bank_issu : ".$bank_issu."\r\n");
            fwrite($fc,"req_tx : ".$req_tx."\r\n");
            fwrite($fc,"bSucc : ".$bSucc."\r\n");
            fwrite($fc,"trans_code : ".$tno."\r\n");
            fwrite($fc,"res_cd : ".$res_cd."\r\n");
            fwrite($fc,"res_msg : ".$res_msg."\r\n");
            fwrite($fc,"isreload : ".$isreload."\r\n");
            fwrite($fc,"use_pay_method : ".$use_pay_method."\r\n");
            fwrite($fc,"결제코드 : ".$paycode."\r\n");
            fwrite($fc,"장바구니idx : ".$basketidxs."\r\n");
            fwrite($fc,"----------------------------------\r\n");
            fclose( $fc );
            chmod("./log/kcp_cancel_result_".date("Ymd").".txt",0777);

            /*
            $return_data  = "ordercode=".$ordr_idxx."&real_price=".$good_mny."&pay_data=".$pay_data."&pay_flag=".$PAY_FLAG;
            $return_data .= "&pay_auth_no=".$PAY_AUTH_NO."&deli_gbn=".$DELI_GBN."&message=".$msg;
            $return_data2 = str_replace("'","",$return_data);
            $returndataSql = "UPDATE SET return_data = '".$return_data2."' WHERE ordercode = '".$ordr_idxx."' ";
            pmysql_query( $returndataSql, get_db_conn() );
            */
            $sql = "UPDATE ".$tblname." SET ok = 'C', msg = msg||' >> 결과 처리 오류 - 자동 취소' ";
            $sql.= "WHERE ordercode ='".$ordr_idxx."' AND trans_code='".$tno."' ";
            pmysql_query( $sql, get_db_conn() );

            $sql_tmp = "UPDATE tblorderinfotemp SET deli_gbn = 'C', pay_data = '결제정보 작성 중 주문취소'  WHERE ordercode = '".$ordr_idxx."' ";
            pmysql_query( $sql_tmp, get_db_conn() );
            $sql_ptmp = "UPDATE tblorderproducttemp SET deli_gbn = 'C' WHERE ordercode = '".$ordr_idxx."' ";
            pmysql_query( $sql_ptmp, get_db_conn() );
        }
    }
} // End of [res_cd = "0000"]

echo "<script>";
echo "parent.parent.location.href=\"http://".$return_resurl."\";\n";
echo "window.close();";
echo "</script>";
exit;
