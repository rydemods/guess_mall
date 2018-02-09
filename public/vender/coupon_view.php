<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

$coupon_code=$_POST["coupon_code"];
$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '".$coupon_code."' AND vender='".$_VenderInfo->getVidx()."' ";
$result = pmysql_query($sql,get_db_conn());
if(!$row=pmysql_fetch_object($result)) {
	echo "<script>alert('해당 쿠폰 정보가 존재하지 않습니다.');window.close();</script>";
	exit;
}
pmysql_free_result($result);

$arissuetype =array("D"=>"삭제 쿠폰","M"=>"회원 가입시 발급","N"=>"개별 발급용 쿠폰","Y"=>"쿠폰 클릭시 발급");
if($row->date_start>0) { 
	$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."[".substr($row->date_start,8,2).":00] ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)."[".substr($row->date_end,8,2).":00]";
	$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
} else {
	$date = abs($row->date_start)."일동안";
	$date2 = date("m/d")." ~ ".date("m/d",strtotime("+".abs($row->date_start)." day"));
}
if($row->sale_type<=2) {
	$dan="%";
} else {
	$dan="원";
}
if($row->sale_type%2==0) {
	$sale = "할인";
} else {
	$sale = "적립";
}
$prleng=strlen($row->productcode);
if($row->productcode=="ALL") {
	$product="전체상품";
} else {
	if($prleng==12) {
		$sql2 = "SELECT code_name as product FROM tblproductcode WHERE code_a='".substr($row->productcode,0,3)."' ";
		if(substr($row->productcode,3,3)!="000") {
			$sql2.= "AND (code_b='".substr($row->productcode,3,3)."' OR code_b='000') ";
			if(substr($row->productcode,6,3)!="000") {
				$sql2.= "AND (code_c='".substr($row->productcode,6,3)."' OR code_c='000') ";
				if(substr($row->productcode,9,3)!="000") {
					$sql2.= "AND (code_d='".substr($row->productcode,9,3)."' OR code_d='000') ";
				} else {
					$sql2.= "AND code_d='000' ";
				}
			} else {
				$sql2.= "AND code_c='000' ";
			}
		} else {
			$sql2.= "AND code_b='000' AND code_c='000' ";
		}
		$sql2.= "AND type IN ('L','LX','LM''LMX') 
		ORDER BY code_a,code_b,code_c,code_d ASC ";
		$result2 = pmysql_query($sql2,get_db_conn());
		$_=array();
		while($row2=pmysql_fetch_object($result2)) {
			$_[] = $row2->product;	
		}
		$product = implode(" > ",$_);
		pmysql_free_result($result2);
	}
	if($prleng==18) {
		$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$row->productcode."' ";
		$result2 = pmysql_query($sql2,get_db_conn());
		if($row2 = pmysql_fetch_object($result2)) {
			$product.= " > ".$row2->product;
		}
		pmysql_free_result($result2);
	}
	if($row->use_con_type2=="N") $product="[".$product."] 제외";
}
if($row->member=="ALL") {
	$membermsg = "[전체회원]";
} else if($row->member!="") {
	$sql2 = "SELECT group_name FROM tblmembergroup WHERE group_code='".$row->member."' ";
	$result2 = pmysql_query($sql2,get_db_conn());
	if($row2 = pmysql_fetch_object($result2)) $membermsg = "[그룹회원 : ".$row2->group_name."]";
	else $membermsg = "[개별회원]";
	pmysql_free_result($result2);
} else {
	$membermsg = "[개별회원]";
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>쿠폰정보 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 55;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=650 style="table-layout:fixed;" id=table_body>
<tr>
	<td width=100% align=center>
	<table border=0 cellpadding=3 cellspacing=0 width=100% style="table-layout:fixed;">
	<tr>
		<td height=30 bgcolor="#F9799A" style="padding-left:15"><FONT COLOR="#ffffff"><B>쿠폰 상세정보</B></FONT></td>
	</tr>
	</table>

	<table width="95%" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
	<col width=150></col>
	<col width=></col>
	<tr><td colspan=2 height=20></td></tr>
	<tr>
		<td colspan=2><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>쿠폰 기본정보</B></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor=red></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 쿠폰코드</td>
		<td style=padding:7,10><B><?=$row->coupon_code?></B></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 쿠폰이름</td>
		<td style=padding:7,10><B><?=$row->coupon_name?></B></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 유효기간</td>
		<td style=padding:7,10><?=$date?></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 할인 속성</td>
		<td style=padding:7,10><?=number_format($row->sale_money).$dan.$sale?> 쿠폰</td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 쿠폰 사용가능 결제금액</td>
		<td style=padding:7,10><?=$row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상 구매시에만 사용가능"?></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr><td colspan=2 height=20></td></tr>
	<tr>
		<td colspan=2><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>쿠폰 부가정보</B></td>
	</tr>
	<tr><td colspan=2 height=1 bgcolor=red></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 적용 상품군</td>
		<td style=padding:7,10><?=$product?></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 쿠폰 발급조건</td>
		<td style=padding:7,10><?=$arissuetype[$row->issue_type]." ".$membermsg?></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 발행 가능 쿠폰 수</td>
		<td style=padding:7,10><?=$row->issue_tot_no=="0"?"무제한":number_format($row->issue_tot_no)."명"?></td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 쿠폰 이미지</td>
		<td style="padding:7,10;padding-bottom:3">
<?php if(file_exists($Dir.DataDir."shopimages/etc/COUPON".$row->coupon_code.".gif")) { ?>
			<img src="<?=$Dir.DataDir?>shopimages/etc/COUPON<?=$row->coupon_code?>.gif" align=absmiddle border=0>
<?php } else { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="352" style="table-layout:fixed;">
		<col width="5"></col>
		<col width=></col>
		<col width="5"></col>
		<tr>
			<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table01.gif" border="0"></td>
		</tr>
		<tr>
			<td background="<?=$Dir?>images/common/coupon_table02.gif"><IMG SRC="<?=$Dir?>images/common/coupon_table02.gif" border="0"></td>
			<td width="100%" style="padding:3pt;" background="<?=$Dir?>images/common/coupon_bg.gif">
			<table align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td style="padding-bottom:4pt;"><IMG SRC="<?=$Dir?>images/common/coupon_title<?=$row->sale_type?>.gif" border="0"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><font color="#585858" style="font-size:11px;letter-spacing:-0.5pt;">유효기간 : <?=$date2?></font><?=($row->bank_only=="Y"?"<font color=\"#0000FF\">(현금결제만 가능)</font>":"")?></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" align="right"><font color="#FF5000" style="font-family:sans-serif;font-size:48px;line-height:45px"><b><font color="#FF6600" face="강산체"><?=number_format($row->sale_money)?></font></b></td>
					<td><IMG SRC="<?=$Dir?>images/common/coupon_text<?=$row->sale_type?>.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
			<td background="<?=$Dir?>images/common/coupon_table04.gif"><IMG SRC="<?=$Dir?>images/common/coupon_table04.gif" border="0"></td>
		</tr>
		<tr>
			<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table03.gif" border="0"></td>
		</tr>
		</table>
<?php
		}
?>
		</td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr>
		<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:6> 발행된 쿠폰수</td>
		<td style=padding:7,10><b><?=number_format($row->issue_no)?></b></font>개 (실 누적 발급 쿠폰 수)</td>
	</tr>
	<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
	<tr><td colspan=2 height=10></td></tr>
	<tr>
		<td colspan=2 align=center>
		<A HREF="javascript:window.close()"><img src=images/btn_close03.gif border=0></A>
		</td>
	</tr>
	<tr><td colspan=2 height=20></td></tr>
	</table>

	<table border=0 cellpadding=2 cellspacing=0 width=100% style="table-layout:fixed;">
	<tr>
		<td bgcolor="#F9799A" height=5 align=center></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
