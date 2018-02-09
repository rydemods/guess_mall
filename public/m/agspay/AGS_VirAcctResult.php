<?php

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
Header("Pragma: no-cache");
 /***************************************************************************************************************
 * 올더게이트로부터 가상계좌 입/출금 데이타를 받아서 상점에서 처리 한 후 
 * 올더게이트로 다시 응답값을 리턴하는 페이지입니다.
 * 상점 DB처리 부분을 업체에 맞게 수정하여 작업하시기 바랍니다.
***************************************************************************************************************/

/*********************************** 올더게이트로 부터 넘겨 받는 값들 시작 *************************************/
$trcode     = trim( $_POST["trcode"] );					    //거래코드
$service_id = trim( $_POST["service_id"] );					//상점아이디
$orderdt    = trim( $_POST["orderdt"] );				    //승인일자
$virno      = trim( $_POST["virno"] );				        //가상계좌번호
$deal_won   = trim( $_POST["deal_won"] );					//입금액
$ordno		= trim( $_POST["ordno"] );                      //주문번호
$inputnm	= trim( $_POST["inputnm"] );					//입금자명
/*********************************** 올더게이트로 부터 넘겨 받는 값들 끝 *************************************/

/***************************************************************************************************************
 * 상점에서 해당 거래에 대한 처리 db 처리 등....
 *
 * trcode = "1" ☞ 일반가상계좌 입금통보전문
 * trcode = "2" ☞ 일반가상계좌 취소통보전문
 *
***************************************************************************************************************/
* ※ 에스크로가상계좌의 경우 입금자명 값은 통보전문에 들어가지 않습니다.
***************************************************************************************************************/
$rSuccYn="n";
$date=date("YmdHis");

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordno."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	if(strlen(AdminMail)>0) {
		@mail(AdminMail,"[PG] ".$ordno." 주문번호 존재하지 않음","$sql");
	}
}
pmysql_free_result($result);

$tblname="";
if(strstr("P", $paymethod)) {
	$tblname="tblpcardlog";
} else if(strstr("OQ", $paymethod)) {
	$tblname="tblpvirtuallog";
}

if(strlen($_SERVER['HTTP_HOST'])>0) {
	$envhttphost = $_SERVER['HTTP_HOST'];
} else {
	$envhttphost = getUriDomain($_SERVER['REQUEST_URI']);
}

if(strlen(RootPath)>0) {
	$hostscript=$envhttphost.$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$envhttphost."/";
}
$return_host=$envhttphost;
$return_script=str_replace($envhttphost,"",$shopurl).FrontDir."payresult/allthegate.php";
$query="ordercode=".$ordno."&trcode=".$trcode;

if(($trcode==1 || $trcode==2 || $trcode==3 || $trcode==4) && ($paymethod=="Q" || $paymethod=="O")) {
	####################### ok가 "M|Y", status가 "N"인 경우에만 정상처리 ########################
	$sql = "SELECT ok, status, noti_id FROM ".$tblname." ";
	$sql.= "WHERE ordercode='".$ordno."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if(($trcode==2 || $trcode==4) && ($paymethod=="O" || $paymethod=="Q")) {
			$query.="&price=".$deal_won."&ok=C";
			if(strstr("Y", $row->ok)) {
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
					$sql.= "WHERE ordercode='".$ordno."'";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rSuccYn  = "y";// 정상 : y 실패 : n
					} else {
						$rSuccYn  = "n";// 정상 : y 실패 : n
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$ordno." 미입금 처리 오류","$sql");
						}
					}
				} else {
					$rSuccYn  = "n";// 정상 : y 실패 : n
				}
			} else {
				$rSuccYn  = "y";// 정상 : y 실패 : n
			}
		} else {
			$query.="&price=".$deal_won."&ok=Y";
			if($row->ok=="M" && $row->status=="N") {
				$send_data=SendSocketPost($return_host, $return_script, $query);
				$send_data=substr($send_data,strpos($send_data,"RESULT=")+7);
				if (substr($send_data,0,2)=="OK") {
					$sql = "UPDATE ".$tblname." SET ";
					$sql.= "ok			= 'Y', ";
					$sql.= "bank_price	= '".$deal_won."', ";
					$sql.= "remitter	= '".$inputnm."', ";
					$sql.= "bank_code	= '', ";
					$sql.= "bank_date	= '".$orderdt."', ";
					$sql.= "receive_date= '".$date."' ";
					$sql.= "WHERE ordercode='".$ordno."' ";
					pmysql_query($sql,get_db_conn());
					if(!pmysql_error()) {
						$rSuccYn  = "y";// 정상 : y 실패 : n
					} else {
						$rSuccYn  = "n";// 정상 : y 실패 : n
						if(strlen(AdminMail)>0) {
							@mail(AdminMail,"[PG] ".$ordno." 가상계좌 입금통보 업데이트 오류","$sql");
						}
					}
				} else {
					$rSuccYn  = "n";// 정상 : y 실패 : n
				}
			} else {
				$rSuccYn  = "y";// 정상 : y 실패 : n
			}
		}
	} else {
		$rSuccYn  = "y";// 정상 : y 실패 : n
	}
}
 


/******************************************처리 결과 리턴******************************************************/
$rResMsg  = "";


//정상처리 경우 거래코드|상점아이디|주문일시|가상계좌번호|처리결과|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

echo $rResMsg;
/******************************************처리 결과 리턴******************************************************/
?> 