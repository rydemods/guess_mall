<?
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$sql = "SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid ";
$sql.= "FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);
$tax_no=$row->tax_cnum;
$mcht_name=$row->tax_cname;
$sell_name=$row->tax_cowner;
$sell_addr=$row->tax_caddr;
$sell_tel=$row->tax_ctel;

$type=$row->tax_type;
$rate=$row->tax_rate;
$kcp_mid=$row->tax_mid;
$kcp_tid=$row->tax_tid;
if(strlen($tax_no)==0) {
	exit;
}


$return_data="";
//$tsdtime=date("YmdHis");
$return_data.="midbykcp=".$kcp_mid;
$return_data.="&termid=".$kcp_tid;
$return_data.="&cashipaddress1=203.238.36.160";
$return_data.="&cashportno1=9981";
$return_data.="&cashipaddress2=203.238.36.161";
$return_data.="&cashportno2=9981";
$return_data.="&extend2=".$_SERVER["REMOTE_ADDR"];

$return_data.="&orderid=".urlencode($ordercode);
//$return_data.="&tsdtime=".substr($tsdtime,2);
if($flag=="Y") $return_data.="&cashtype=AUTH";
else if($flag=="C") $return_data.="&cashtype=VOID";

$return_data.="&mcht_name=".urlencode(strip_tags($mcht_name));
$return_data.="&sell_type=1";
$return_data.="&sell_name=".urlencode(strip_tags($sell_name));
$return_data.="&sell_addr=".urlencode(strip_tags($sell_addr));
$return_data.="&sell_tel=".urlencode(strip_tags($sell_tel));

$sql = "SELECT * FROM tbltaxsavelist WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($flag=="Y") $tsdtime=date("YmdHis");
	else $tsdtime=$row->tsdtime;
	
	$row->productname=substr($row->productname,0,30);

	$return_data.="&tsdtime=".substr($tsdtime,2);
	$return_data.="&tax_no=".$row->tax_no;
	$return_data.="&tr_code=".$row->tr_code;
	$return_data.="&id_info=".$row->id_info;
	$return_data.="&prod_name=".urlencode(urldecode($row->productname));
	$return_data.="&amt1=".$row->amt1;
	$return_data.="&amt2=".$row->amt2;
	$return_data.="&amt3=".$row->amt3;
	$return_data.="&amt4=".$row->amt4;
	$return_data.="&cons_name=".urlencode($row->name);
	$return_data.="&cons_tel=".$row->tel;
	$return_data.="&cons_email=".$row->email;
} else {
	exit;
}
pmysql_free_result($result);

//cgi 호출
$host_url=$_SERVER['HTTP_HOST'];
$host_cgi="/".RootPath.CashcgiDir."bin/cgiway.cgi";

$temp=GetTaxsaveResult($host_url,$host_cgi,$return_data);

$data=substr($temp,strpos($temp,"RESULT=")+7);
$okresult=explode("|",$data);


if(sizeOf($okresult)!=3) {
	$msg="현금영수증 서버 연결이 실패하였습니다.";
} else {
	if($okresult[0]=="ERROR") {
		$sql = "UPDATE tbltaxsavelist SET ";
		$sql.= "tsdtime		= '".$tsdtime."', ";
		$sql.= "error_msg	= '[".$okresult[1]."] ".$okresult[2]."' ";
		$sql.= "WHERE ordercode='".$ordercode."'";
		pmysql_query($sql,get_db_conn());
		if($flag=="Y") $tmpmsg="발급이";
		else if($flag=="C") $tmpmsg="취소가";
		$msg="현금영수증 ".$tmpmsg." 실패하였습니다.\\n\\n--------------------실패사유--------------------\\n\\n".$okresult[2];
	} else if($flag=="Y" && $okresult[0]=="OK") {
		$sql = "UPDATE tbltaxsavelist SET ";
		$sql.= "tsdtime		= '".$tsdtime."', ";
		$sql.= "type		= 'Y', ";
		$sql.= "authno		= '".$okresult[1]."', ";
		$sql.= "mtrsno		= '".$okresult[2]."', ";
		$sql.= "oktime		= '".$tsdtime."', ";
		$sql.= "error_msg	= '' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		$msg="현금영수증 발급이 정상적으로 처리되었습니다.";
	} else if($flag=="C" && $okresult[0]=="CANCEL") {
		$sql = "UPDATE tbltaxsavelist SET ";
		$sql.= "tsdtime		= '".$tsdtime."', ";
		$sql.= "type		= 'C', ";
		$sql.= "authno		= '".$okresult[1]."', ";
		$sql.= "mtrsno		= '".$okresult[2]."', ";
		$sql.= "error_msg	= '' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		$msg="현금영수증 취소가 정상적으로 처리되었습니다.";
	}
}


function getParse($temp) {
	$val = array();
	$list = explode("<br>\n",$temp);
	for ($i=0;$i<count($list); $i++) {
		$data = explode("=",$list[$i]);
		$val[$data[0]] = $data[1];
	}
	if(strlen($val["error_msg"])>0) {
		$res="RESULT=ERROR|".$val["mrspc"]."|".$val["error_msg"];
	} else if(strlen($val["resp_msg"])>0 && $val["mrspc"]!="00") {
		$res="RESULT=ERROR|".$val["mrspc"]."|".$val["resp_msg"];
	} else if ($val["msg_type"]=="7100" || $val["msg_type"]=="7102") {
		$res="RESULT=".($val["msg_type"]=="7100"?"OK":"CANCEL")."|".$val["authno"]."|".$val["mtrsno"];
	}
	return $res;
}

function GetTaxsaveResult($host, $path, $query) {
	$fp = fsockopen($host, 80, &$errno, &$errstr, 12);
	if(!$fp) {
		flush();
		fclose($fp);
		return "ERROR : $errstr ($errno)";
	} else {
		$cmd = "POST $path HTTP/1.0\n";
		fputs($fp, $cmd);
		$cmd = "Host: $host\n";
		fputs($fp, $cmd);
		$cmd = "Content-type: application/x-www-form-urlencoded\n";
		fputs($fp, $cmd);
		$cmd = "Content-length: " . strlen($query) . "\n";
		fputs($fp, $cmd);
		$cmd = "Connection: close\n\n";
		fputs($fp, $cmd);
		fputs($fp, $query);
		flush();
		
		while($currentHeader = fgets($fp,4096)) {
			if($currentHeader == "\r\n") {
				break;
			}
		}

		$strLine = "";
		while(!feof($fp)) {
			$strLine .= fgets($fp, 4096);
		}
		fclose($fp);

		$temp=getParse($strLine);

		return $temp;
	}
}
