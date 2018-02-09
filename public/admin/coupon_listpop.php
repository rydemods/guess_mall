<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}


if($_POST['del_code_idx']){
	#sdebug("DELETE FROM tblcouponissue WHERE coupon_code||id = '".$_POST['del_code_idx']."'");
	pmysql_query("DELETE FROM tblcouponissue WHERE coupon_code||id = '".$_POST['del_code_idx']."'", get_db_conn());
}
$id = $_POST["id"];
?>
<html>
<head>
<title><?=$id?>회원님의 회원쿠폰 보유내역</title>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
	document.onkeydown = CheckKeyPress;
	document.onkeyup = CheckKeyPress;
	function CheckKeyPress() {
		ekey = event.keyCode;

		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	}

	function PageResize() {
		var oWidth = document.all.table_body.clientWidth + 240;
		//var oHeight = document.all.table_body.clientHeight + 55;
		var oHeight = 400;

		window.resizeTo(oWidth,oHeight);
	}
	$(document).ready(function(){
		$(".CLS_objDelete").click(function(){
			if(confirm("해당 쿠폰을 제거 하시겠습니까?")){
				$("input[name='del_code_idx']").val($(this).attr('idx'));
				$("form[name='delFrm']").submit();
			}
		})
	})
//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p><?=$id?>회원님의 회원쿠폰 보유내역</p></div>
<!-- onLoad="PageResize();"-->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" style = 'height:400px;'>
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td width="100%">
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<col width = '10%'><col width = '*%'><col width = '15%'><col width = '15%'><col width = '15%'><col width = '15%'><col width = '10%'>
		<TR>
			<th>쿠폰코드</th>
			<th>쿠폰명</th>
			<th>쿠폰적용상품</th>
			<th>사용가능금액</th>
			<th>할인/적립</th>
			<th>유효기간</th>
			<th>삭제</th>
		</TR>
<?php
	$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, 
	a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end, b.id 
	FROM tblcouponinfo a, tblcouponissue b 
	WHERE b.id='{$id}' AND a.coupon_code=b.coupon_code 
	AND (b.date_end>='".date("YmdH")."' OR b.date_end='') AND b.used='N' ";
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$cnt++;
		if($row->sale_type<=2) {
			$dan="%";
		} else {
			$dan="원";
		}
		if($row->sale_type%2==0) {
			$sale = "&nbsp;<img src=\"images/icon_off.gif\" border=\"0\" align = 'absmiddle'>";
		} else {
			$sale = "&nbsp;<img src=\"images/icon_point6.gif\" border=\"0\" align = 'absmiddle'>";
		}
		$prleng=strlen($row->productcode);
		if($row->productcode=="ALL") {
			$product="전체상품";
		} else {		
			$product = getCodeLoc($row->productcode);

			if($prleng==18) {
				$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='{$row->productcode}' ";
				$result2 = pmysql_query($sql2,get_db_conn());
				if($row2 = pmysql_fetch_object($result2)) {
					$product.= " > ".$row2->product;
				}
				pmysql_free_result($result2);
			}
			if($row->use_con_type2=="N") $product="[{$product}] 제외";
		}
		
		$date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ <br>".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
		echo "<tr>\n";
		echo "	<TD>{$row->coupon_code}</td>\n";
		echo "	<TD>{$row->coupon_name}</td>\n";
		echo "	<TD>{$product}</td>\n";
		echo "	<TD><b><span class=\"font_orange\">".($row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상")."</span></b></TD>\n";
		echo "	<TD><font color=\"".($sale=="할인"?"#FF0000":"#0000FF")."\">".number_format($row->sale_money).$dan.$sale."</font></td>\n";
		echo "	<TD>{$date}</td>\n";
		echo "	<TD><a href = 'javascript:;' class = 'CLS_objDelete' idx = '".$row->coupon_code.$row->id."'><b>[삭제]</b></a></td>\n";
		echo "</tr>\n";
	}
	pmysql_free_result($result);
	if($cnt==0) {
		echo "<tr><td colspan=\"7\" >보유한 쿠폰내역이 없습니다.</td></tr>\n";
	}
?>
		</TABLE>
        </div>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="10" border=0></a></TD>
</TR>
</TABLE>

<form name='delFrm' action="coupon_listpop.php" method='POST'>
	<input type = 'hidden' name = 'del_code_idx' value = ''>
	<input type = 'hidden' name = 'id' value = '<?=$id?>'>
</form>

</body>
</html>