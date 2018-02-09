<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

$mode=$_POST["mode"];
$theme_sectcode=$_REQUEST["theme_sectcode"];
$themeGoodNm=$_POST["themeGoodNm"];

if($mode=="update" && strlen($theme_sectcode)>=3) {
	$theme_code_a=substr($theme_sectcode,0,3);
	$theme_code_b=substr($theme_sectcode,3,3);
	$sql = "SELECT code_a,code_b FROM tblvenderthemecode WHERE vender='".$_VenderInfo->getVidx()."' AND code_a='".$theme_code_a."' ";
	if(strlen($theme_code_b)==3) {
		$sql.= "AND code_b='".$theme_code_b."' ";
	} else {
		$sql.= "AND code_b='000' ";
	}
	$result=pmysql_query($sql,get_db_conn());
	if(!$row=pmysql_fetch_object($result)) {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
	pmysql_free_result($result);

	$upprdtcode=(array)$_POST["upprdtcode"];
	$upthemecode=(array)$_POST["upthemecode"];
	$themelist=array();
	for($i=0;$i<count($upprdtcode);$i++) {
		if(strlen($upthemecode[$i])==0) {
			$themelist[]=$upprdtcode[$i];
		}
	}
	$delprlist="";
	if(count($themelist)>0) {
		$themeincode=$theme_sectcode;
		if(strlen($themeincode)==3) $themeincode.="000";

		$sql = "INSERT INTO tblvenderthemeproduct VALUES ";
		for($i=0;$i<count($themelist);$i++) {
			$delprlist.=$themelist[$i].",";
			$sql.= "('".$_VenderInfo->getVidx()."','".$themeincode."','".$themelist[$i]."','".date("YmdHis")."'),";
		}
		$sql=rtrim($sql,',');
		$delprlist=rtrim($delprlist,',');
		$delprlist=str_replace(',','\',\'',$delprlist);
		
		$delsql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' AND productcode IN ('".$delprlist."') ";
		if(pmysql_query($delsql,get_db_conn())) {
			pmysql_query($sql,get_db_conn());
		} else {
			$iserror=true;
		}
	}
	if(!$iserror) {
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');form1.submit()\">\n";
		echo "<form name=\"form1\" method=post target='ThemePrdtListIfrm'> \n";
		echo "<input type=hidden name='theme_sectcode' value='".$theme_sectcode."'>\n";
		echo "<input type=hidden name='themeGoodNm' value='".$themeGoodNm."'>\n";
		echo "</form>\n";
		echo "</body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
} else if($mode=="delete" && strlen($theme_sectcode)>=3) {
	$upprdtcode=(array)$_POST["upprdtcode"];
	$upthemecode=(array)$_POST["upthemecode"];
	$valid_flag=(array)$_POST["valid_flag"];
	$delprlist="";
	while(list($key,$val)=each($valid_flag)) {
		if(strlen($upthemecode[$val])>0) {
			$delprlist.=$upprdtcode[$val].",";
		}
	}
	$delprlist=rtrim($delprlist,',');
	$delprlist=str_replace(',','\',\'',$delprlist);
	
	if(strlen($delprlist)>0) {
		$delsql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' AND productcode IN ('".$delprlist."') ";
		if(!pmysql_query($delsql,get_db_conn())) {
			$iserror=true;
		}
	}
	if(!$iserror) {
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');form1.submit()\">\n";
		echo "<form name=\"form1\" method=post target='ThemePrdtListIfrm'> \n";
		echo "<input type=hidden name='theme_sectcode' value='".$theme_sectcode."'>\n";
		echo "<input type=hidden name='themeGoodNm' value='".$themeGoodNm."'>\n";
		echo "</form>\n";
		echo "</body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}


$setup[page_num] = 10;
$setup[list_num] = 12;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

$t_count=0;
if(strlen($theme_sectcode)>=3) {
	$sql = "SELECT COUNT(*) as t_count FROM tblvenderthemeproduct a, tblproduct b ";
	$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND a.themecode LIKE '".$theme_sectcode."%' ";
	$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
	$sql.= "AND b.display='Y' ";
	if(strlen($themeGoodNm)>0) {
		$sql.= "AND b.productname LIKE '%".$themeGoodNm."%' ";
	}
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$t_count = $row->t_count;
	pmysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
}
?>

<html>
<head>
<title></title>
<link rel="stylesheet" href="style.css" type="text/css">
<script language="javascript" src="themeCtgrPrdtPage.js.php"></script>
</head>

<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 onload="calculageHeightSize();" >
<div name="ifr" id="ifr">    
<form name="iForm" method="post">
<input type="hidden" name="mode">
<input type="hidden" name="theme_sectcode" value="">
<input type="hidden" name="themeGoodNm" value="">
<table id="tbList" width=684 border=0 cellspacing=1 cellpadding=0 bgcolor=E7E7E7>
<tr height=28 align=center bgcolor=F5F5F5>
	<td width=100><input type=checkbox name="allChk" title="전체선택" onClick="checkedAll()"><img src=images/btn_delete06.gif border=0 align=absmiddle style="cursor:hand" onClick="deleteData()" ></td>
	<td width=170 class=blackb>상품코드</td>
	<td width=414 class=blackb>상품명</td>
</tr>
<?
if($t_count>0) {
	$sql = "SELECT a.themecode,a.productcode,b.productname FROM tblvenderthemeproduct a, tblproduct b ";
	$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND a.themecode LIKE '".$theme_sectcode."%' ";
	$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
	$sql.= "AND b.display='Y' ";
	if(strlen($themeGoodNm)>0) {
		$sql.= "AND b.productname LIKE '%".$themeGoodNm."%' ";
	}
	//$sql.= "ORDER BY a.date DESC ";
	$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
		echo "<tr height=28 align=center bgcolor=FFFFFF onMouseOver=\"tbList.clickedRowIndex=this.rowIndex;\">\n";
		echo "	<td><input type=\"checkbox\" name=\"valid_flag\" value=\"".$i."\">\n";
		echo "	<input type=\"hidden\" name=\"upthemecode[]\" value=\"".$row->themecode."\">\n";
		echo "	</td>\n";
		echo "	<td><input type=\"text\" name=\"upprdtcode[]\" value=\"".$row->productcode."\" size=\"18\" readonly></td>\n";
		echo "	<td>".$row->productname."</td>\n";
		echo "</tr>\n";
		$i++;
	}
}
?>
</table>
</form>

<table width=684 border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
<tr>
	<td>
	<form name="pageForm" method="post"> 
	<input type=hidden name='theme_sectcode' value='<?=$theme_sectcode?>'>
	<input type=hidden name='block'>
	<input type=hidden name='gotopage'>
	<input type=hidden name='themeGoodNm' value=''>
	</form>
	<table width=100% border=0 cellspacing=0 cellpadding=0  >
	<tr>
		<td width=340 align=left>전체 건수 <?=$t_count?> 건</td>
		<td width=340 align=right>

		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height=10></td>
</tr>
<tr>
	<td align=center><a href="javascript:saveData();"><img src=images/btn_save01.gif border=0></a></td>
</tr>		  
</table> 
</div>		  
</body>
</html>
