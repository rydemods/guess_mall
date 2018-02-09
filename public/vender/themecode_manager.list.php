<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$code_a=$_REQUEST["code_a"];
if(strlen($code_a)!=3) $code_a="";
$code_a_name="";

$mode=$_POST["mode"];
if($mode=="disptypeupdate" && strlen($_POST["code_disptype"])==2) {
	$code_disptype=$_POST["code_disptype"];
	if($code_disptype!="YY" && $code_disptype!="YN" && $code_disptype!="NY") {
		exit;
	}
	$sql = "UPDATE tblvenderstore SET code_distype='".$code_disptype."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	pmysql_query($sql,get_db_conn());
	echo "<html></head><body onload=\"alert('카테고리 노출 설정이 완료되었습니다.')\"></body></html>";exit;
} else if($mode=="update") {
	if(strlen($code_a)==3) {
		$sql = "SELECT code_name FROM tblvenderthemecode ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND code_a='".$code_a."' AND code_b='000' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$code_a_name=$row->code_name;
		} else $code_a="";
		pmysql_free_result($result);
	}

	$code_cnt=$_POST["code_cnt"];
	$delCnt=$_POST["delCnt"];
	$savecodes=explode("=",$_POST["savecodes"]);
	$delcodes=explode("=",$_POST["delcodes"]);

	$delsql="";
	$delprdt="";
	$delthemedesign="";
	if(count($delcodes)>0) {
		$j=0;
		for($i=0;$i<count($delcodes);$i++) {
			if(strlen($delcodes[$i])==6) {
				if($j>0) {
					$delsql.=" OR ";
					$delprdt.=" OR ";
				}
				if(strlen($code_a)==3) {
					$delsql.="(code_a='".substr($delcodes[$i],0,3)."' AND code_b='".substr($delcodes[$i],3,3)."') ";
					$delprdt.="(themecode='".$delcodes[$i]."') ";
				} else {
					$delsql.="(code_a='".substr($delcodes[$i],0,3)."') ";
					$delprdt.="(themecode LIKE '".substr($delcodes[$i],0,3)."%') ";
				}
				if(substr($delcodes[$i],3,3)=="000") {
					$delthemedesign.=substr($delcodes[$i],0,3).",";
				}
				$j++;
			}
		}
	}
	if(strlen($delsql)>0) {
		$sql = "DELETE FROM tblvenderthemecode WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND (".$delsql.") ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND (".$delprdt.") ";
			pmysql_query($sql,get_db_conn());

			if(strlen($delthemedesign)>0) {
				//대분류 화면관리 delete (tblvendercodedesign)
				$delthemedesign=rtrim($delthemedesign,',');
				$delthemedesign=str_replace(',','\',\'',$delthemedesign);
				$sql = "DELETE FROM tblvendercodedesign WHERE vender='".$_VenderInfo->getVidx()."' AND code IN ('".$delthemedesign."') AND tgbn='20' ";
				pmysql_query($sql,get_db_conn());
			}
		}
	}

	$codes_in=array();
	if(count($savecodes)>0) {
		$j=0;
		for($i=0;$i<count($savecodes);$i++) {
			$sequence=9999-$i;
			$temp=explode("",$savecodes[$i]);
			if(strlen($temp[0])==0) {
				if(strlen($temp[1])>0) {
					$codes_in[$j]["sequence"]=$sequence;
					$codes_in[$j]["code_name"]=$temp[1];
					$j++;
				}
			} else {
				$sql = "UPDATE tblvenderthemecode SET sequence='".$sequence."', ";
				$sql.= "code_name='".$temp[1]."' ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "AND code_a='".substr($temp[0],0,3)."' AND code_b='".substr($temp[0],3,3)."' ";
				pmysql_query($sql,get_db_conn());
			}
		}
	}
	if(count($codes_in)>0) {
		if(strlen($code_a)==3) {	//중분류 생성
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND themecode='".$code_a."000' ";
			pmysql_query($sql,get_db_conn());

			$sql = "SELECT MAX(code_b) as maxcode_b FROM tblvenderthemecode ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND code_a='".$code_a."' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			$in_code_b=(int)$row->maxcode_b+1;
			$in_code_b = sprintf("%03d",$in_code_b);
			for($i=0;$i<count($codes_in);$i++) {
				$sql = "INSERT INTO tblvenderthemecode (
				vender		,
				code_a		,
				code_b		,
				code_name	,
				sequence	) VALUES (
				'".$_VenderInfo->getVidx()."', 
				'".$code_a."', 
				'".$in_code_b."', 
				'".$codes_in[$i]["code_name"]."', 
				'".$codes_in[$i]["sequence"]."')";
				if(pmysql_query($sql,get_db_conn())) {
					$in_code_b=(int)$in_code_b+1;
					$in_code_b = sprintf("%03d",$in_code_b);

					//기존 대분류에 등록된 상품 해제
					$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND themecode='".$code_a."000' ";
					pmysql_query($sql,get_db_conn());
				}
			}
		} else {				//대분류 생성
			$sql = "SELECT MAX(code_a) as maxcode_a FROM tblvenderthemecode ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			$in_code_a=(int)$row->maxcode_a+1;
			$in_code_a = sprintf("%03d",$in_code_a);
			$in_code_b="000";
			for($i=0;$i<count($codes_in);$i++) {
				$sql = "INSERT INTO tblvenderthemecode(
				vender		,
				code_a		,
				code_b		,
				code_name	,
				sequence	) VALUES (
				'".$_VenderInfo->getVidx()."', 
				'".$in_code_a."', 
				'".$in_code_b."', 
				'".$codes_in[$i]["code_name"]."', 
				'".$codes_in[$i]["sequence"]."')";
				if(pmysql_query($sql,get_db_conn())) {
					$sql = "INSERT INTO tblvendercodedesign(
					vender		,
					code		,
					tgbn		,
					hot_used	,
					hot_dispseq	,
					hot_linktype) VALUES (
					'".$_VenderInfo->getVidx()."', 
					'".$in_code_a."', 
					'20', 
					'1', 
					'118', 
					'1')";
					pmysql_query($sql,get_db_conn());

					$in_code_a=(int)$in_code_a+1;
					$in_code_a = sprintf("%03d",$in_code_a);					
				}
			}
		}
	}

	echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
}

if(strlen($code_a)==3 && strlen($code_a_name)==0) {
	$sql = "SELECT code_name FROM tblvenderthemecode ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code_a='".$code_a."' AND code_b='000' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$code_a_name=$row->code_name;
	} else $code_a="";
	pmysql_free_result($result);
}

?>

<html>
<head>
<title></title>
<link rel=stylesheet href="style.css" type=text/css>
<script language=javascript src="themecodemgr.js.php"></script>

</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type="hidden" name="mode">
<input type="hidden" name="code_a" value="<?=$code_a?>">
<input type="hidden" name="code_cnt">
<input type="hidden" name="delCnt">

<span id="oData"></span>

<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
<tr>
	<td bgcolor=FEFCE2 style=padding:5,10>
	<B>대분류명</B>
<?
	if(strlen($code_a_name)>0) {
		echo " <B>: ".$code_a_name."</B>";
		echo "<br><img width=0 height=1><br><B>중분류명</B>";
	}
?>
	</td>
</tr>
<tr>
	<td>
	<select name=code size=13 style=width:435 onClick="f_setEdit()" >
<?
	$sql = "SELECT code_a,code_b,code_name FROM tblvenderthemecode ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(strlen($code_a)==3) {
		$sql.= "AND code_a='".$code_a."' AND code_b!='000' ";
	} else {
		$sql.= "AND code_b='000' ";
	}
	$sql.= "ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		echo "<option value=\"".$row->code_a.$row->code_b."\">".$row->code_name."</option>\n";
	}
	pmysql_free_result($result);
?>
	</select>
	</td>
</tr>
</table>
</form>
</body>
</html>
