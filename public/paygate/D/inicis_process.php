<?php

//*******************************************************************************
// FILE NAME : INIpayResult.php
// DATE : 2006.05
// 이니시스 가상계좌 입금내역 처리demon으로 넘어오는 파라메터를 control 하는 부분 입니다.
//*******************************************************************************

$TEMP_IP = $_SERVER['REMOTE_ADDR'];
$PG_IP  = substr($TEMP_IP,0, 10);

if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138")  //PG에서 보냈는지 IP로 체크
{
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	Header("Pragma: no-cache");

	@extract($_GET);
	@extract($_POST);
	@extract($_SERVER);

	//**********************************************************************************
	//  이부분에 로그파일 경로를 수정해주세요.	
	$INIpayHome = $_SERVER['DOCUMENT_ROOT']."/".RootPath."paygate/D";      // 이니페이 홈디렉터리
	//**********************************************************************************

	$msg_id = $msg_id;	     //메세지 타입
	$tno = $no_tid;	     //거래번호
	$order_no = $no_oid;	     //상점 주문번호
	$id_merchant = $id_merchant;   //상점 아이디
	$cd_bank = $cd_bank;	   //거래 발생 기관 코드
	$cd_deal = $cd_deal;	   //취급 기관 코드
	$dt_trans = $dt_trans;	 //거래 일자
	$tm_trans = $tm_trans;	 //거래 시간
	$no_msgseq = $no_msgseq;       //전문 일련 번호
	$cd_joinorg = $cd_joinorg;     //제휴 기관 코드

	$dt_transbase = $dt_transbase; //거래 기준 일자
	$noti_id = $no_transeq;     //거래 일련 번호
	$type_msg = $type_msg;	 //거래 구분 코드
	$cl_close = $cl_close;	 //마감 구분코드
	$cl_kor = $cl_kor;	     //한글 구분 코드
	$no_msgmanage = $no_msgmanage; //전문 관리 번호
	$no_vacct = $no_vacct;	 //가상계좌번호
	$amt_input = $amt_input;       //입금금액
	$amt_check = $amt_check;       //미결제 타점권 금액
	$nm_inputbank = $nm_inputbank; //입금 금융기관명
	$nm_input = $nm_input;	 //입금 의뢰인
	$dt_inputstd = $dt_inputstd;   //입금 기준 일자
	$dt_calculstd = $dt_calculstd; //정산 기준 일자
	$flg_close = $flg_close;       //마감 전화

	$logfile = fopen( $INIpayHome . "/log/result.log", "a+" );
	
	fwrite( $logfile,"************************************************");
	fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
	fwrite( $logfile,"NO_TID : ".$no_tid."\r\n");
	fwrite( $logfile,"NO_OID : ".$no_oid."\r\n");
	fwrite( $logfile,"NO_VACCT : ".$no_vacct."\r\n");
	fwrite( $logfile,"AMT_INPUT : ".$amt_input."\r\n");
	fwrite( $logfile,"NM_INPUTBANK : ".$nm_inputbank."\r\n");
	fwrite( $logfile,"NM_INPUT : ".$nm_input."\r\n");
	fwrite( $logfile,"************************************************");
	fwrite( $logfile,"전체 결과값"."\r\n");
	fwrite( $logfile, $msg_id."\r\n");
	fwrite( $logfile, $cd_bank."\r\n");
	fwrite( $logfile, $dt_trans."\r\n");
	fwrite( $logfile, $tm_trans."\r\n");
	fwrite( $logfile, $no_msgseq."\r\n");
	fwrite( $logfile, $type_msg."\r\n");
	fwrite( $logfile, $cl_close."\r\n");
	fwrite( $logfile, $cl_kor."\r\n");
	fwrite( $logfile, $no_msgmanage."\r\n");
	fwrite( $logfile, $amt_check."\r\n");
	fwrite( $logfile, $dt_inputstd."\r\n");
	fwrite( $logfile, $dt_calculstd."\r\n");
	fwrite( $logfile, $flg_close."\r\n");
	fwrite( $logfile, "\r\n");
	fclose( $logfile );

	$rescode="";
	$date=date("YmdHis");
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

	if(strstr("OQ", $paymethod)) {
		if(strlen(RootPath)>0) {
			$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
			$pathnum=@strpos($hostscript,RootPath);
			$shopurl=substr($hostscript,0,$pathnum).RootPath;
		} else {
			$shopurl=$_SERVER['HTTP_HOST']."/";
		}

		if(strlen($type_msg)==0 && $paymethod=="Q") {
			$check_host=$_SERVER['HTTP_HOST'];
			$check_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl)."paygate/D/escrow/INIescrow.php";
			$check_query="hanatid=".$tno."&EscrowType=dr&invno=check&transtype=S0";			
			$check_data=SendSocketPost($check_host, $check_script, $check_query);
			$check_data_exp = explode("|",$check_data);
			
			if($check_data_exp[0]!="4913") {
				$type_msg="0200";
			}
		}

		$tblname="";
		if(strstr("P", $paymethod)) {
			$tblname="tblpcardlog";
		} else if(strstr("OQ", $paymethod)) {
			$tblname="tblpvirtuallog";
		}

		$return_host=$_SERVER['HTTP_HOST'];
		$return_script=str_replace($_SERVER['HTTP_HOST'],"",$shopurl).FrontDir."payresult/inicis.php";
		$query="ordercode=".$order_no."&type_msg=".$type_msg;

		####################### ok가 "M|Y", status가 "N"인 경우에만 정상처리 ########################
		$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
		$sql.= "WHERE ordercode='".$order_no."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$query.="&price=".$amt_input."&ok=";
			if($type_msg=="0400") $query.="C";
			else $query.="Y";

			if($type_msg=="0400") {
				//if($row->noti_id==$noti_id) {
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
							$sql.= "WHERE ordercode='".$order_no."' ";
							pmysql_query($sql,get_db_conn());
							if(!pmysql_error()) {
								$rescode="OK";
							} else {
								if(strlen(AdminMail)>0) {
									@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보취소 업데이트 오류","$sql");
								}
							}
						}
					}
				//}
			} else {
				if($row->ok=="M" && $row->status=="N") {
					$send_data=SendSocketPost($return_host, $return_script, $query);
					$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
					if (substr($send_data,0,2)=="OK") {
						$sql = "UPDATE ".$tblname." SET ";
						$sql.= "ok			= 'Y', ";
						$sql.= "bank_price	= '".(int)$amt_input."', ";
						$sql.= "remitter	= '".$nm_input."', ";
						$sql.= "bank_code	= '".substr($cd_bank,-2)."', ";
						$sql.= "bank_date	= '".$dt_trans.$tm_trans."', ";
						//$sql.= "noti_id		= '".$noti_id."', ";
						$sql.= "receive_date= '".$date."' ";
						$sql.= "WHERE ordercode='".$order_no."' ";
						pmysql_query($sql,get_db_conn());
						if(!pmysql_error()) {
							$rescode="OK";
						} else {
							if(strlen(AdminMail)>0) {
								@mail(AdminMail,"[PG] ".$order_no." 가상계좌 입금통보 업데이트 오류","$sql");
							}
						}
					}
				}
			}
		}
	}
}
echo $rescode;
