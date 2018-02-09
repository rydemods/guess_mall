<?php
/*********************************************************************
 // 파 일 명		: refound_call.php
 // 설     명		: 가상계좌 환불 PG신청
 // 상세설명	:  반품 완료된 가상계좌 건을 PG에 환불 신청
 // 작 성 자		: 2016-01-09 - daeyeob(김대엽)
 //
 *********************************************************************/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
header("Content-Type:text/html; charset=euc-kr;");

/*
 # 환불 정보(가상계좌)
 # MID : 상점아이디
 # TID : 거래아이디
 # CancelAmt : 취소금액
 # CancelPw : 취소패스워드
 # CancelPw : 취소패스워드
 # RefundBankCd : 환불은행코드
 # RefundAcctNo : 환불계좌번호
 # RefundAcctNm : 환불계좌주명
 # CancelMSG : 취소메시지
 # PartialCancelCode : 부분취소여부
 # SignData : 해시검증값
 # EncodeKey : 가맹점키
 # CancelPw : 취소패스워드
 */

$ordercode = $_REQUEST['ordercode'];
$pc_type = $_REQUEST['pc_type'];
$MID = "shoemarkem";
$TID = iconv("UTF-8","EUC-KR",$_REQUEST["TID"]);
$CancelAmt = $_REQUEST["CancelAmt"];
$CancelPw = "shoemarkem";
$RefundBankCd = iconv("UTF-8","EUC-KR",$_REQUEST["RefundBankCd"]);
$RefundAcctNo =iconv("UTF-8","EUC-KR",$_REQUEST["RefundAcctNo"]);
$RefundAcctNo = str_replace("-","",$RefundAcctNo);
$RefundAcctNm = iconv("UTF-8","EUC-KR",$_REQUEST["RefundAcctNm"]);
$CancelMSG = iconv("UTF-8","EUC-KR",$_REQUEST["CancelMSG"]);
if($pc_type == "ALL"){
	#전체취소
	$PartialCancelCode = "0";
}else{
	#부분취소
	$PartialCancelCode = "1";
}


$EncodeKey = "+jqEF34tA1Yt5pA8LyYxa/2lMVHO+0YtPQzs1IAWt4A6LVjRMZKJ5J9ioeAfFscOv2KIwSpL5Hi76QZzvHIL1Q=="; // 상점키

$EncData = "TID=".$TID."&MID=".$MID."&CancelAmt=".$CancelAmt."&CancelPw=".$CancelPw."&EncodeKey=".$EncodeKey;

// MAKE SHA-256 HASH DATA
$SignData = hash('sha256', $EncData);

$RequestData = "MID=".$MID;
$RequestData .= "&TID=".$TID;
$RequestData .= "&CancelAmt=".$CancelAmt;
$RequestData .= "&CancelPw=".$CancelPw;
$RequestData .= "&RefundBankCd=".$RefundBankCd;
$RequestData .= "&RefundAcctNo=".trim($RefundAcctNo);
$RequestData .= "&RefundAcctNm=".$RefundAcctNm;
$RequestData .= "&CancelMSG=".$CancelMSG;
$RequestData .= "&PartialCancelCode=".$PartialCancelCode;
$RequestData .= "&SignData=".$SignData;

// DO cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://staging.nicepay.co.kr/v2/api/merchant/vbank_refund.jsp");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec ($ch);

curl_close ($ch);

// PARSE KEY & VALUE
$arr_data = explode("&", $result);
$arr_length = count($arr_data);
$key_value_array = array();
for($i=0;$i<$arr_length-1;$i++)
{
	list($key, $value) = explode("=", $arr_data[$i]);
	$key_value_array[$key] = $value;
}

$return = Array();

if($key_value_array['ResultCode'] == 0000 || $key_value_array['ResultCode'] == 2211){
	imaginationAccountRefoundLog($ordercode,$key_value_array['ResultCode'], $key_value_array['ResultMsg'], $TID);
	$return['result'] = "success";
}else{
	//가상계좌 환불 Log
	imaginationAccountRefoundLog($ordercode,$key_value_array['ResultCode'], $key_value_array['ResultMsg'], $TID);
	$return['result'] = "fail";
}

echo json_encode($return);
?>
