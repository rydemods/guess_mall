<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->estimate_ok!="Y" && $_data->estimate_ok!="O") {
	alert_go('견적서 기능 선택이 안되었습니다.','c');
}

$printval=$_POST["printval"];

if(ord($printval)==0) {
	alert_go('선택된 상품이 없습니다.','c');
}

$prlist="";
$arr_prcnt=array();
$arrval=explode("|",$printval);
for($i=0;$i<count($arrval);$i++) {
	$tmp=explode(",",$arrval[$i]);
	$tmp[1]=(int)$tmp[1];
	if(strlen($tmp[0])==18 && $tmp[1]>0) {
		$arr_prcnt[$tmp[0]]=$tmp[1];
		$prlist.=",{$tmp[0]}";
	}
}
$prlist=ltrim($prlist,',');
$prlist=str_replace(',','\',\'',$prlist);

if(ord($prlist)==0) {
	alert_go('선택된 상품이 없습니다.','c');
}

?>
<html>
<head>
<title>온라인견적서 인쇄하기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:787878;font-size:9pt;}

tr {font-family:돋음;color:787878;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:787878;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
resizeTo(700,620);
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<tr>
	<td align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=50%>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center style="font-size:18"><B>견 적 서</B></td>
	</tr>
	<tr><td height=10></td></tr>
	<tr><td height=1 bgcolor=#787878></td></tr>
	</table>
	</td>
</tr>
<tr><td height=10></td></tr>
<tr>
	<td valign=bottom style="padding:7">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr>
		<td width=40% valign=bottom>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878>
		<col width=60></col>
		<col width=></col>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">견적일자</td>
			<td style="padding-left:5"><?php echo date("Y")."년 ".date("m")."월 ".date("d")."일 ".date("H")."시 ".date("i")."분"?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">유효기간</td>
			<td style="padding-left:5">견적 후 일주일</td>
		</tr>
		</table>
		</td>
		<td width=7% nowrap></td>
		<td width=53%>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style="table-layout:fixed">
		<col width=100></col>
		<col width=></col>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">사업자등록번호</td>
			<td style="padding-left:5"><?=$_data->companynum?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">회 사 명</td>
			<td style="padding-left:5"><?=$_data->companyname?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">대표자 성명</td>
			<td style="padding-left:5"><?=$_data->companyowner?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">업태/종목</td>
			<td style="padding-left:5"><?=$_data->companybiz?> / <?=$_data->companyitem?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">사업장 주소</td>
			<td style="padding-left:5"><?=$_data->companyaddr?></td>
		</tr>
		<tr bgcolor=#FFFFFF>
			<td align=right style="padding-right:5">사업장 전화번호</td>
			<td style="padding-left:5"><?=$_data->info_tel?></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td style="padding:7">
<?php 
	$sql = "SELECT a.productcode,a.productname,a.sellprice,a.production,a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('{$prlist}') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'{$prlist}') ";
	$result=pmysql_query($sql,get_db_conn());
	$cnt=0;
	$total=0;
	$price=0;
	$vat=0;
	$estlist="";
	while($row=pmysql_fetch_object($result)) {
		$total+=$row->sellprice*$arr_prcnt[$row->productcode];
		$price+=round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1);
		$vat+=(($row->sellprice*$arr_prcnt[$row->productcode])-round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1));
		$cnt++;

		$estlist.="<tr bgcolor=#ffffff>\n";
		$estlist.="	<td align=center>{$cnt}</td>\n";
		$estlist.="	<td align=center>".viewselfcode($row->productname,$row->selfcode)."</td>\n";
		$estlist.="	<td align=center>{$row->production}</td>\n";
		$estlist.="	<td align=center>{$arr_prcnt[$row->productcode]}개</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($row->sellprice)."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format(round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1))."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format((($row->sellprice*$arr_prcnt[$row->productcode])-round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1)))."원</td>\n";
		$estlist.="</tr>\n";
	}
	pmysql_free_result($result);
?>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr>
		<td width=50%>※ 아래와 같이 견적합니다.</td>
		<td width=50% align=right>견적합계 : \<?=number_format($total)?>원, VAT포함</td>
	</tr>
	</table>
	<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style="table-layout:fixed">
	<col width=30></col>
	<col width=></col>
	<col width=90></col>
	<col width=50></col>
	<col width=80></col>
	<col width=80></col>
	<col width=70></col>
	<tr bgcolor=#f4f4f4 height=25>
		<td align=center>No</td>
		<td align=center>상품명</td>
		<td align=center>제조사</td>
		<td align=center>수량</td>
		<td align=center>상품단가</td>
		<td align=center>상품금액</td>
		<td align=center>세액</td>
	</tr>
<?php 
	echo $estlist;
	if($cnt<15) {
		for($i=$cnt;$i<=15;$i++) {
			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "	<td></td>\n";
			echo "</tr>\n";
		}
	}
?>
	</table>
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td height=2></td></tr>
	</table>
	<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style="table-layout:fixed">
	<col width=30></col>
	<col width=></col>
	<col width=90></col>
	<col width=50></col>
	<col width=80></col>
	<col width=80></col>
	<col width=70></col>
	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">상품금액</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($price)?>원</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">부가세(10%)</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($vat)?>원</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td colspan=5 align=right style="padding-right:5">합 계</td>
		<td colspan=2 align=right style="padding-right:5"><?=number_format($total)?>원</td>
	</tr>
	<tr bgcolor=#f4f4f4>
		<td colspan=7 style="padding-left:5">비 고</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td colspan=7 style="padding:10">
		<?=$_data->estimate_msg?>&nbsp;
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td align=center style="padding-top:10">
	<A HREF="javascript:print()"><img src="<?=$Dir?>images/common/estimate/icon_print.gif" border=0></A>
	&nbsp;
	<A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/estimate/icon_close.gif" border=0></A>
	</td>
</tr>
<tr><td height=20></td></tr>
</table>

<script>print();</script>
</body>
</html>
