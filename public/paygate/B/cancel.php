<?php
/*
신용카드/핸드폰/실시간계좌이체 취소처리
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$mid=$_POST["mid"];
$mertkey=$_POST["mertkey"];
$ordercode=$_POST["ordercode"];
$return_host=$_POST["return_host"];
$return_script=$_POST["return_script"];
$return_data=$_POST["return_data"];
$return_type=$_POST["return_type"];
$ip=$_SERVER['REMOTE_ADDR'];

if (empty($mid)) {
	alert_go('데이콤 상점ID가 없습니다.',-1);
}
if (empty($mertkey)) {
	alert_go('데이콤 고유 mertkey가 없습니다.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go('해당 승인건이 존재하지 않습니다.',-1);
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M")					$tblname="tblpmobilelog";
else if($paymethod=="V")					$tblname="tblptranslog";
else {
	alert_go('잘못된 처리입니다.',-1);
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if($row->ok=="C") {	//이미 취소처리된 건
		echo "<script>alert('".get_message("해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.")."')</script>\n";
		if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
			echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
			echo "<input type=hidden name=rescode value=\"C\">\n";
			$text = explode("&",$return_data);
			for ($i=0;$i<sizeOf($text);$i++) {
				$textvalue = explode("=",$text[$i]);
				echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
			}
			echo "</form>";
			echo "<script>document.form1.submit();</script>";
			exit;
		} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
			$return_data.="&rescode=C";
			//소켓통신 처리
			exit;
		}
	}
} else {
	alert_go(get_message("해당 승인건이 존재하지 않습니다."),-1);
}
pmysql_free_result($result);

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

if(strlen($respcode)==0) {
	$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$trans_code=$row->trans_code;
		if($row->ok=="C") {	//이미 취소처리된 건
			echo "<script>alert('".get_message("이미 취소된 거래 취소요청건입니다.\\n\\n쇼핑몰에 재반영됩니다.")."');</script>\n";
			if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
				echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
				echo "<input type=hidden name=rescode value=\"C\">\n";
				$text = explode("&",$return_data);
				for ($i=0;$i<sizeOf($text);$i++) {
					$textvalue = explode("=",$text[$i]);
					echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
				}
				echo "</form>";
				echo "<script>document.form1.submit();</script>";
				exit;
			} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
				$return_data.="&rescode=C";
				//소켓통신 처리
				exit;
			}
		} else {
			echo "<script>alert('".get_message("취소처리가 실패하였습니다.\\n\\nLG데이콤 상점관리 페이지에서 결제건을 확인하시기 바랍니다.")."');</script>\n";
		}
	}
	pmysql_free_result($result);
} else if(($respcode=="0000") || ($paytype=="SC0030" && $respcode=="RF00")) {
	echo "<script>alert('".get_message("승인취소가 정상적으로 처리되었습니다.\\n\\LG데이콤 관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.")."');</script>\n";

	if ($return_type=="form" && strlen($return_host)>0 && strlen($return_script)>0) {
		echo "<form name=form1 action=\"http://$return_host$return_script\" method=post>\n";
		echo "<input type=hidden name=rescode value=\"C\">\n";
		$text = explode("&",$return_data);
		for ($i=0;$i<sizeOf($text);$i++) {
			$textvalue = explode("=",$text[$i]);
			echo "<input type=hidden name=".$textvalue[0]." value=\"".$textvalue[1]."\">\n";
		}
		echo "</form>";
		echo "<script>document.form1.submit();</script>";
		exit;
	} else if($return_type=="socket" && strlen($return_host)>0 && strlen($return_script)>0) {
		$return_data.="&rescode=C";
		//소켓통신 처리
		exit;
	}
} else {
	alert_go(get_message("취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : $respmsg"),-1);
}
