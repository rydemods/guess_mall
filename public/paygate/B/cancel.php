<?php
/*
�ſ�ī��/�ڵ���/�ǽð�������ü ���ó��
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
	alert_go('������ ����ID�� �����ϴ�.',-1);
}
if (empty($mertkey)) {
	alert_go('������ ���� mertkey�� �����ϴ�.',-1);
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	alert_go('�ش� ���ΰ��� �������� �ʽ��ϴ�.',-1);
}
pmysql_free_result($result);

$tblname="";
if(strstr("CP", $paymethod[0]))	$tblname="tblpcardlog";
else if($paymethod=="M")					$tblname="tblpmobilelog";
else if($paymethod=="V")					$tblname="tblptranslog";
else {
	alert_go('�߸��� ó���Դϴ�.',-1);
}

//���������� ���翩�� Ȯ��
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$trans_code=$row->trans_code;
	if($row->ok=="C") {	//�̹� ���ó���� ��
		echo "<script>alert('".get_message("�ش� �������� �̹� ���ó���Ǿ����ϴ�. ���θ��� ��ݿ��˴ϴ�.")."')</script>\n";
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
			//������� ó��
			exit;
		}
	}
} else {
	alert_go(get_message("�ش� ���ΰ��� �������� �ʽ��ϴ�."),-1);
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
		if($row->ok=="C") {	//�̹� ���ó���� ��
			echo "<script>alert('".get_message("�̹� ��ҵ� �ŷ� ��ҿ�û���Դϴ�.\\n\\n���θ��� ��ݿ��˴ϴ�.")."');</script>\n";
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
				//������� ó��
				exit;
			}
		} else {
			echo "<script>alert('".get_message("���ó���� �����Ͽ����ϴ�.\\n\\nLG������ �������� ���������� �������� Ȯ���Ͻñ� �ٶ��ϴ�.")."');</script>\n";
		}
	}
	pmysql_free_result($result);
} else if(($respcode=="0000") || ($paytype=="SC0030" && $respcode=="RF00")) {
	echo "<script>alert('".get_message("������Ұ� ���������� ó���Ǿ����ϴ�.\\n\\LG������ �������������� ��ҿ��θ� �� Ȯ���Ͻñ� �ٶ��ϴ�.")."');</script>\n";

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
		//������� ó��
		exit;
	}
} else {
	alert_go(get_message("���ó���� �Ʒ��� ���� ������ �����Ͽ����ϴ�.\\n\\n���л��� : $respmsg"),-1);
}
