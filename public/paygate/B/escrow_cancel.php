<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

echo "RESULT=";

$mid=$_REQUEST["mid"];
$mertkey=$_REQUEST["mertkey"];
$ordercode=$_REQUEST["ordercode"];

if (empty($mid)) {
	echo "NO|데이콤 상점ID가 없습니다.";exit;
}
if (empty($mertkey)) {
	echo "NO|데이콤 고유 mertkey가 없습니다.";exit;
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

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if(!strstr("QP", $paymethod[0])) {
		echo "NO|해당 에스크로 결제건이 존재하지 않습니다.";exit;
	}
	if($row->ok=="C") {
		echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다.";
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
			$mod_type="STE4";
			if($row->paymethod=="Q") {
				//환불 또는 발급계좌해지 세팅
				if($row->ok=="Y") {	//환불처리
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|해당 에스크로 결제건은 환불수취계좌 정보를 등록하셔야 최소처리가 가능합니다.\\n\\n환불계좌수기입력 후 취소처리 하시기 바랍니다."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
				}
			}
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
					if(strlen($row->refund_account)==0 || strlen($row->refund_name)==0 || strlen($row->refund_bank_code)==0) {
						echo "NO|해당 에스크로 결제건은 환불수취계좌 정보를 등록하셔야 최소처리가 가능합니다.\\n\\n환불계좌수기입력 후 취소처리 하시기 바랍니다."; exit;
					}
					$refund_account=$row->refund_account;
					$refund_account=str_replace("-","",$refund_account);
					$refund_nm=$row->refund_name;
					$bank_code=$row->refund_bank_code;
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

//입금전 그냥 취소처리 (데이콤에서는 "발급계좌해지"가 없기 때문에 자체DB만 취소처리 한다.)
if($mod_type=="STE5") {
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	$sql.= "status		= 'G' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
}

if(strlen(RootPath)>0) {
	$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER['HTTP_HOST']."/";
}
$note_url="http://".$shopurl."paygate/B/dacom_process.php";
$ret_url="http://".$shopurl;

$hashdata=md5($mid.$ordercode.$mertkey);
$query="mid=".$mid."&oid=".$ordercode."&tid=".$trans_code."&ret_url=".$ret_url."&note_url=".$note_url."&hashdata=".$hashdata;
if($paymethod=="Q") {
	$query.="&bankcode=".$bank_code."&account=".$refund_account."&paytype=SC0040";
}

$temp = SendSocketPost("pg.dacom.net","/common/cancel.jsp",$query);
//$temp = SendSocketPost("pg.dacom.net","/common/cancel.jsp",$query,7080);

$respcode="";
$paytype="";
$respmsg="";
if(strpos($temp,"respcode\" value=\"")) {
	$respcode = substr($temp,strpos($temp,"respcode\" value=\"")+17,4);
}
if(strpos($temp,"paytype\" value=\"")) {
	$paytype = substr($temp,strpos($temp,"paytype\" value=\"")+16,6);
}
if(strpos($temp,"respmsg\" value=\"")) {
	$tempmsg = substr($temp,strpos($temp,"respmsg\" value=\"")+16);
	$respmsg = substr($tempmsg,0,strpos($tempmsg,"\" >"));
}

if(strlen($respcode)==0) {
	$tempmsg = substr($temp,strpos($temp,"alert('")+7);
	$respmsg = substr($tempmsg,0,strpos($tempmsg,"');"));
}

/*
$fp = fopen("log/test.txt", "a+");
fwrite($fp, $temp);
fclose($fp);
*/

#################### 에스크로 취소 결과 처리 ###################
if(strlen($respcode)==0) {
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$isupdate=false;
	if($paymethod=="P") {
		if($row->ok=="C") {
			$isupdate=true;
		}
	} else if($paymethod=="Q") {
		if($row->status=="F") {
			$isupdate=true;
		}
	}

	if($isupdate) {
		echo "OK|해당 에스크로 결제건은 이미 취소처리 되었습니다.\\n\\n쇼핑몰에 재반영됩니다.";
	} else {
		echo "NO|취소처리가 아래와 같은 사유로 실패하였습니다.";
		if(strlen($respmsg)>0) {
			echo "\\n\\n실패사유 : $respmsg";
		}
	}
	exit;
} else if ($respcode=="0000" || $respcode=="RF00") {
	$sql = "UPDATE ".$tblname." SET ";
	$sql.= "ok			= 'C', ";
	if($paymethod=="Q") {
		$sql.= "status	= 'F' ";
	} else if($paymethod=="P") {
		$sql.= "status	= 'X' ";
	}
	$sql.= "WHERE ordercode='".$ordercode."' ";
	pmysql_query($sql,get_db_conn());
	echo "OK"; exit;
} else {
	echo "NO|취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $respmsg";
	exit;
}
