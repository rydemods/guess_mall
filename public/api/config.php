<?php

// ================================================================
// 아자샵 쇼핑몰인 경우 아래 내용을 include한다.
// ================================================================
$Dir = $_SERVER[DOCUMENT_ROOT]."/";
include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/sync.class.php");
include_once($Dir."/lib/shopdata.php");


// ================================================================
// 크로스도메인 관련 헤더 설정
// ================================================================

header("Access-Control-Allow-Origin: *");

// ==================================================================================================================
// 설정값 입력 시작
// ==================================================================================================================
if (!$_shopdata) {
	$_shopdata=new ShopData($_ShopInfo);
	$_shopdata=$_shopdata->shopdata;
	$_ShopInfo->getPgdata();
	$_shopdata->escrow_id	= $_data->escrow_id;
	$_shopdata->trans_id		= $_data->trans_id;
	$_shopdata->virtual_id		= $_data->virtual_id;
	$_shopdata->card_id		= $_data->card_id;
	$_shopdata->mobile_id	= $_data->mobile_id;
}

// 접속 허용 아이피
$arrAllowRemoteIP = array();

// 개발서버
$arrAllowRemoteIP["dev"] = array(
    "182.162.154.102",
    "218.234.32.17",
	"218.234.32.10",
    "218.234.32.123",
    "182.162.154.106",
	"182.162.154.107",
    "218.234.32.105",
    "218.234.32.118",
	"218.234.32.59",
	"218.234.32.5",
	"218.234.32.75",
);

// 실서버
$arrAllowRemoteIP["real"] = array(
    "182.162.154.109",
    "182.162.154.110",
);

// 세정I&C 서버
$arrAllowRemoteIP["sejung"] = array(
    "211.168.129.40",
    "123.140.2.187",
    "115.88.106.145",
    "115.88.106.146",
    "115.88.106.147",
);

// 접속용 Key
$arrAllowKey = array();
$arrAllowKey["dev"]     = "6e39a4f3f5c0d2c1b694b72abc66541accd90a6745520ab04fecf122a5845f4d69bf65"; // 개발서버용
$arrAllowKey["real"]    = "6e39a4f3f5c0d2c1b19bea2de87b5d57754d056a883a45dc912caf46f87a";           // 실서버용
$arrAllowKey["sejung"]  = "0c10511854890c7f25e8cae10719670b34ca653a4d3866";                         // 세정I&C용

// ==================================================================================================================
// 설정값 입력 끝
// ==================================================================================================================

$api_key    = $_POST["api_key"];

$resultTotArr = array();
$resultArr = array();

if ( !in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["dev"]) &&
    !in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["real"]) &&
    !in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["sejung"]) ) {
    $resultTotArr["result"]  = $resultArr;
    $resultTotArr["code"]    = "1";
    $resultTotArr["message"] = "허용되지 않은 아이피입니다.";

    echo json_encode($resultTotArr);
    exit;
} elseif ( in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["dev"]) && $api_key != $arrAllowKey["dev"] ) {
    $resultTotArr["result"]  = $resultArr;
    $resultTotArr["code"]    = "1";
    $resultTotArr["message"] = "api키값이 유효하지 않습니다.";

    echo json_encode($resultTotArr);
    exit;
} elseif ( in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["real"]) && $api_key != $arrAllowKey["real"] ) {
    $resultTotArr["result"]  = $resultArr;
    $resultTotArr["code"]    = "1";
    $resultTotArr["message"] = "api키값이 유효하지 않습니다.";

    echo json_encode($resultTotArr);
    exit;
} elseif ( in_array($_SERVER["REMOTE_ADDR"], $arrAllowRemoteIP["sejung"]) && $api_key != $arrAllowKey["sejung"] ) {
    $resultTotArr["result"]  = $resultArr;
    $resultTotArr["code"]    = "1";
    $resultTotArr["message"] = "api키값이 유효하지 않습니다.";

    echo json_encode($resultTotArr);
    exit;
}

//$arrDate = explode("-", date("Y-m-d"));
//$logDir = $Dir . "api/log/" . $arrDate[0] . "/" . $arrDate[1] . "/" . $arrDate[2] . "/" . $_SERVER["REMOTE_ADDR"];
$logDir = $Dir . "api/log/";
// 로그 기록할 디렉토리가 없으면 생성
if ( !is_dir($logDir) ) {
    mkdir($logDir, 0777, true);
}

if ( is_dir($logDir) ) {
    /*
        $content  = "URI : " . $_SERVER["REQUEST_URI"] . "\n";
        $content .= "IP : " . $_SERVER["REMOTE_ADDR"] . "\n";
        $content .= "====================================================\n";
        foreach ( $_POST as $key => $val ) {
            if ( is_array($val) ) {
                $content .= "[{$key}]\n";
                foreach ( $val as $k => $v ) {
                    if ( is_array($v) ) {
                        foreach ( $v as $k2 => $v2 ) {
                            $content .= "\t{$k2} => {$v2}\n";
                        }
                    } else {
                        $content .= "\t{$k} => {$v}\n";
                    }
                }
            } else {
                $content .= "[{$key}] => {$val}\n";
            }
        }
        $content .= "====================================================\n";
    */
    $content = date("Y-m-d H:i:s")." [{$_SERVER['REMOTE_ADDR']}] {$_SERVER['REQUEST_URI']} ".json_encode_kr($_POST).PHP_EOL;
    $fn = $logDir . "api_" . date("Ymd") . ".log";
    $fp = fopen($fn, "a+");
    fwrite($fp, $content);
    fclose($fp);
    chmod($fn,0644);
}

function api_log($str){
	global $Dir;
	$logDir = $Dir . "api/log/";
	// 로그 기록할 디렉토리가 없으면 생성
	if ( !is_dir($logDir) ) {
		mkdir($logDir, 0777, true);
	}
	if ( is_dir($logDir) ) {
		$content = date("Y-m-d H:i:s")." [{$_SERVER['REMOTE_ADDR']}] ".$str.PHP_EOL;
		$fn = $logDir . "api_ch" . date("Ymd") . ".log";
		$fp = fopen($fn, "a+");
		fwrite($fp, $content);
		fclose($fp);
		chmod($fn,0644);
	}
}

function cj_dbconnect(){
	$username = "SHINWON";
	#$password = "sejungcldev!#$1"; // OPENDBT (TEST)
	$password = "shinwon!#$1";     // OPENDB  (REAL)
	#$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1523)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDBT)))";
	//$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1523)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDBT )))";
	$conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1521)) (CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDB)))";
	$cj_dbconn = oci_connect($username, $password, $conn_str, "UTF8");
	if( $cj_dbconn ){
		$res = oci_parse($cj_dbconn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		oci_execute($res);
		oci_free_statement($res);
		return $cj_dbconn;
	}else
		return false;
}

?>
