<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 적용 상품군 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function ACodeSendIt(code,type) {
	document.sForm.code.value=code;
	document.sForm.type.value=type;
	murl = "coupon_productchoice.ctgr.php?code="+code+"&depth=2";
	surl = "coupon_productchoice.ctgr.php?depth=3";
	durl = "coupon_productchoice.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;

	productSubmit(code);
}

function productSubmit(code) {
	document.sForm.codetype[1].checked=true;
	if(code.length==0) {
		document.testform.target="PrdtListIfrm";
		document.testform.action="coupon_productchoice.select.php";
		document.testform.submit();
	} else {
		if(document.sForm.code.value.length>=3 && document.sForm.type.value=="X") {
			document.sForm.target="PrdtListIfrm";
			document.sForm.action="coupon_productchoice.select.php";
			document.sForm.submit();
		}
	}
}


function ChangeProduct() {
	document.sForm.codetype[2].checked=true;
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 55;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	form=document.sForm;
	codetype="";
	for(i=0;i<form.codetype.length;i++) {
		if(form.codetype[i].checked) {
			codetype=form.codetype[i].value;
			break;
		}
	}
	if(codetype=="ALL") {
		opener.document.form1.productcode.value="ALL";
		opener.document.form1.productname.value="전체상품";
		opener.ViewLayer('layer1','none');
	} else if (codetype=="CODE") {
		if(form.code.value.length!=12){
			alert('쿠폰 적용을 원하시는 분류를 선택하세요');
			return;
		}
		opener.document.form1.productcode.value=form.code.value;
		code_name="";
		if(form.code1.value.length==12) {
			if(code_name.length>0) code_name+=" > ";
			code_name+=form.code1.options[form.code1.selectedIndex].text;
			if(BCodeCtgr.document.iForm.code.value.length==12) {
				if(code_name.length>0) code_name+=" > ";
				code_name+=BCodeCtgr.document.iForm.code.options[BCodeCtgr.document.iForm.code.selectedIndex].text;
				if(CCodeCtgr.document.iForm.code.value.length==12) {
					if(code_name.length>0) code_name+=" > ";
					code_name+=CCodeCtgr.document.iForm.code.options[CCodeCtgr.document.iForm.code.selectedIndex].text;
					if(DCodeCtgr.document.iForm.code.value.length==12) {
						if(code_name.length>0) code_name+=" > ";
						code_name+=DCodeCtgr.document.iForm.code.options[DCodeCtgr.document.iForm.code.selectedIndex].text;
					}
				}
			}
		}

		opener.document.form1.productname.value=code_name;
		opener.ViewLayer('layer1','block');
	} else if (codetype=="PRODUCT") {
		obj=PrdtListIfrm.document.frmList.prcode;
		if(obj.value.length==0){
			alert('쿠폰 적용을 원하시는 상품을 선택하세요');
			obj.focus();
			return;
		}

		code_name="";
		if(form.code1.value.length==12) {
			if(code_name.length>0) code_name+=" > ";
			code_name+=form.code1.options[form.code1.selectedIndex].text;
			if(BCodeCtgr.document.iForm.code.value.length==12) {
				if(code_name.length>0) code_name+=" > ";
				code_name+=BCodeCtgr.document.iForm.code.options[BCodeCtgr.document.iForm.code.selectedIndex].text;
				if(CCodeCtgr.document.iForm.code.value.length==12) {
					if(code_name.length>0) code_name+=" > ";
					code_name+=CCodeCtgr.document.iForm.code.options[CCodeCtgr.document.iForm.code.selectedIndex].text;
					if(DCodeCtgr.document.iForm.code.value.length==12) {
						if(code_name.length>0) code_name+=" > ";
						code_name+=DCodeCtgr.document.iForm.code.options[DCodeCtgr.document.iForm.code.selectedIndex].text;
					}
				}
			}
		}
		code_name+=" > "+obj.options[obj.selectedIndex].text;

		opener.document.form1.productcode.value=obj.value;
		opener.document.form1.productname.value=code_name;
		opener.ViewLayer('layer1','block');
	} else {
		alert("쿠폰 적용 상품군 선택이 안되었습니다.");
		return;
	}
	window.close();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;overflow-y:hidden;" onLoad="PageResize();">
<table border=0 cellpadding=0 cellspacing=0 width=320 style="table-layout:fixed;" id=table_body>
<tr>
	<td width=100%>
	<table border=0 cellpadding=3 cellspacing=0 width=100% style="table-layout:fixed;">
	<tr>
		<td bgcolor="#5F9FDF" style="padding-left:15"><FONT COLOR="#ffffff"><B>쿠폰 적용 상품군 선택</B></FONT></td>
	</tr>
	</table>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=code value="">
	<input type=hidden name=type value="">
	<tr><td height=5></td></tr>
	<tr>
		<td align=center>

		<table border=0 cellpadding=0 cellspacing=0 width=98%>
		<tr>
			<td style="padding-left:5;padding-bottom:5">
			<input type=radio id="idx_codetype1" name=codetype value="ALL" checked> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_codetype1>모든 상품에 쿠폰 혜택이 적용합니다.</label>
			<br>
			<input type=radio id="idx_codetype2" name=codetype value="CODE"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_codetype2>일부 분류의 모든상품에만 적용합니다.</label>
			<br>
			<input type=radio id="idx_codetype3" name=codetype value="PRODUCT"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_codetype3>일부 상품에만 적용합니다.</label>
			</td>
		</tr>
		</table>

		<table border=0 cellpadding=0 cellspacing=0 width=98% style="table-layout:fixed">
		<col width=155></col>
		<col width=4></col>
		<col width=></col>
		<tr>
			<td>
			<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value,this.options[this.selectedIndex].otype)">
			<option value="" otype="">------ 대 분 류 ------</option>
<?
			$sql = "SELECT SUBSTR(productcode,1,3) as prcode FROM tblproduct ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "GROUP BY prcode ";
			$result=pmysql_query($sql,get_db_conn());
			$codes="";
			while($row=pmysql_fetch_object($result)) {
				$codes.=$row->prcode.",";
			}
			pmysql_free_result($result);
			if(strlen($codes)>0) {
				$codes=rtrim($codes,',');
				$prcodelist=str_replace(',','\',\'',$codes);
			}
			if(strlen($prcodelist)>0) {
				$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
				$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
				$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					echo "<option value=\"".$row->code_a."000000000\" otype=\"".substr($row->type,-1,1)."\"";
					if($row->code_a==substr($code,0,3)) echo " selected";
					echo ">".$row->code_name."</option>\n";
				}
				pmysql_free_result($result);
			}
?>
			</select>
			</td>
			<td></td>
			<td>
			<iframe name="BCodeCtgr" src="coupon_productchoice.ctgr.php?depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
			</td>
		</tr>
		<tr>
			<td>
			<iframe name="CCodeCtgr" src="coupon_productchoice.ctgr.php?depth=3" width="155" height="21" scrolling=no frameborder=no></iframe>
			</td>
			<td></td>
			<td>
			<iframe name="DCodeCtgr" src="coupon_productchoice.ctgr.php?depth=4" width="155" height="21" scrolling=no frameborder=no></iframe>
			</td>
		</tr>
		</table>

		<table border=0 cellpadding=0 cellspacing=0 width=98% style="table-layout:fixed">
		<tr>
			<td>
			<iframe name="PrdtListIfrm" src="coupon_productchoice.select.php" width="100%" height="200" scrolling=no frameborder=no></iframe>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<A HREF="javascript:CheckForm()"><img src=images/btn_confirm03.gif border=0></A>
		&nbsp;
		<A HREF="javascript:window.close()"><img src=images/btn_cancel05.gif border=0></A>
		</td>
	</tr>
	<tr><td height=20></td></tr>
	</form>
	</table>
	</td>
</tr>

<form name=testform method=post></form>

</table>
</body>
</html>
