<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$sitecd=$_REQUEST["sitecd"];
$ordercode=$_REQUEST["ordercode"];
$curgetid=$_REQUEST["curgetid"];

if (empty($sitecd)) {
	echo "NO|KCP 고유ID가 없습니다.";exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

$tblname="";
if(strstr("Q", $paymethod[0]))		$tblname="tblpvirtuallog";
else if($paymethod=="P")					$tblname="tblpcardlog";
else {
	echo "NO|잘못된 처리입니다.";exit;
}

$mod_type="";
$refund_account="";
$refund_nm="";
$bank_code="";

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.";
		exit;
	}
	switch($row->status) {
		case "S":
			echo "NO|해당 에스크로 결제건은 상품 배송중입니다.\\n\\n정산보류 후 취소처리가 가능합니다."; exit;
			break;
		case "D":
		case "X":
		case "C":
			echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다."; exit;
			break;
		case "H":
			//정산보류된건에대해서취소처리 변수 세팅
			$mod_type="STE2";
			break;
		case "Y":
			echo "NO|해당 에스크로 결제건은 구매확인 처리가 되어 취소가 불가능합니다."; exit;
			break;
		case "E":
			echo "NO|해당 에스크로 결제건은 환불처리 되었습니다."; exit;
			break;
		case "G":
			echo "NO|해당 에스크로 결제건은 발급계좌가 해지되었습니다."; exit;
			break;
		case "N":
			if($row->paymethod=="Q") {
				//환불 또는 발급계좌해지 세팅
				if($row->ok=="Y") {	//환불처리
					$mod_type="STE2";
				} else {			//발급계좌해지
					$mod_type="STE5";
				}
			} else if($row->paymethod=="P") {
				//즉시취소 세팅
				$mod_type="STE2";
			}
			break;
		default:
			exit;
			break;
	}
} else {
	echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
}
pmysql_free_result($result);

if($paymethod=="Q" && $mod_type!="STE5" && $row->status!="C") {
	$check_host=$_SERVER['HTTP_HOST'];
	$check_script="/".RootPath."paygate/D/escrow/INIescrow.php";
	
	$check_query="hanatid=".$row->trans_code."&mid=".$sitecd."&EscrowType=rr&adminID=".$curgetid."&adminName=관리자&returntype=0";
	$check_data=SendSocketPost($check_host, $check_script, $check_query);
	$check_data_exp = explode("|",$check_data);
	
	$res_cd = $check_data_exp[0];
	$res_msg = $check_data_exp[1];
} else if($mod_type=="STE5") {
	$res_cd = "0000";
	$res_msg = "입금전 취소";
}

################## 배송시작 결과 처리 ################
if($res_cd!="0000") {
	echo "NO|에스크로 취소 처리를 아래와 같은 사유로 전달하지 못하였습니다.\\n\\n실패사유 : $res_msg";
	exit;
} else {
	//DB 업데이트
	$sql = "UPDATE ".$tblname." SET ";
	if($mod_type=="STE2") {	//배송전 즉시취소
		$sql.= "ok			= 'C', ";
		$sql.= "status		= 'C' ";
	} else if($mod_type=="STE4") {	//정산보류된 결제건 취소
		$sql.= "ok			= 'C', ";
		if($paymethod=="Q") {
			$sql.= "status	= 'F' ";
		} else if($paymethod=="P") {
			$sql.= "status	= 'X' ";
		}
	} else if($mod_type=="STE5") {	//발급계좌 해지
		$sql.= "ok			= 'C', ";
		$sql.= "status		= 'G' ";
	}
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}
