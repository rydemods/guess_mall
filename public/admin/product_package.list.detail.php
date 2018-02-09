<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$package_title_code=$_POST["package_title_code"];
$package_title_list=$_POST["package_title_list"];
$package_price_list=$_POST["package_price_list"];

$mode=$_POST["mode"];
$code=$_POST["code"];
$keyword=$_POST["keyword"];
$searchtype=$_POST["searchtype"];
if (ord($searchtype)==0) $searchtype=0;

if(strlen($code)==12) {
	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);
}
?>
<?php include("header.php"); ?>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 350;HEIGHT: 150;}
</STYLE>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var code="<?=$code?>";
var mode="<?=$mode?>";
var cnt=0;
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(mode.length>0 || cnt>0) {
		if(selcode.length==12 && selcode!="000000000000" && seltype.indexOf("X")!=-1) {
			document.form2.mode.value="";
			document.form2.code.value=selcode;
			document.form2.submit();
		}
	}
	cnt++;
}

document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function InsertCollection() {
	if (document.form1.prcode.selectedIndex==-1) {
		alert("패키지에 포함할 상품을 선택하세요.");
		document.form1.prcode.focus();
		return;
	}

	var num = document.form1.comcode.length-1;
	var insert_count=0;
	var insert_prcode = new Array();
	var insert_prcodeok = new Array();
	var insert_same = true;

	for(j=0; j<document.form1.prcode.options.length; j++) {
		if(document.form1.prcode.options[j].selected) {
			insert_prcode[insert_count] = document.form1.prcode.options[j].value;
			insert_count++;
		}
	}

	if(num+insert_count>=10){
		alert("패키지는 최대 10개까지 등록가능합니다.");
		return;
	} else {
		if (confirm("해당 상품을 패키지 상품으로 포함하시겠습니까?")){
			temp = "";
			temp2 = "";
			var m=0;
			for(k=0; k<insert_count; k++) {
				insert_same = true;
				for (i=0;i<=num;i++) {
					if(document.form1.comcode.options[i].value == insert_prcode[k]){
						insert_same=false;
						break;
					} 
				}

				if(insert_same) {
					insert_prcodeok[m] = insert_prcode[k];
					m++;
				}
			}
			
			for (i=0;i<=num;i++) {
				if (i==0) temp = document.form1.comcode.options[i].value;
				else temp+=","+document.form1.comcode.options[i].value;
			}
			
			if(m>0) {
				for (m=0;m<insert_prcodeok.length;m++) {
					if (m==0) temp2 = insert_prcodeok[m];
					else temp2+=","+insert_prcodeok[m];
				}
			}

			if(num==-1) temp=temp2;
			else temp+=","+temp2;

			prices = getPrice();
			if(parent.package_list_update(temp,prices)) {
				document.form1.package_title_list.value = temp;
				document.form1.package_price_list.value = prices;
				document.form1.submit();
			}
		}
	}
}

var shop="layer1";
var ArrLayer = new Array ("layer1","layer2");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementById(ArrLayer[i]).style.display="";
			else
				document.getElementById(ArrLayer[i]).style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}

function CheckSearch() {
	document.form1.mode.value = "";
	if (document.form1.keyword.value.length<2) {
		if(document.form1.keyword.value.length==0) alert("검색어를 입력하세요.");
		else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
		document.form1.keyword.focus();
		return;
	} else {
		codes = "";
		for (i=0;i<=(document.form1.comcode.length-1);i++) {
			if (i==0) codes = document.form1.comcode.options[i].value;
			else codes+=","+document.form1.comcode.options[i].value;
		}
		document.form1.code.value = "";
		document.form1.package_title_list.value = codes;
		document.form1.submit();
	}
}

function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch()
	}
}

function Delete() {
	if(document.form1.comcode.selectedIndex !=-1) {
		if(!confirm("선택하신 상품을 패키지에서 삭제하시겠습니까?")) return;
		document.form1.mode.value="delete";
		num = document.form1.comcode.length-1;
		
		var delete_count=0;
		var delete_prcode = new Array();
		var delete_prcodeno = new Array();
		var delete_same = true;

		for(j=0; j<document.form1.comcode.options.length; j++) {
			if(document.form1.comcode.options[j].selected) {
				delete_prcode[delete_count] = document.form1.comcode.options[j].value;
				delete_count++;
			}
		}

		codes = "";
		var m=0;
		for (i=0;i<=num;i++) {
			delete_same = true;
			for(k=0; k<delete_count; k++) {
				if(document.form1.comcode.options[i].value==delete_prcode[k]){
					delete_same=false;
					break;
				}
			}

			if(delete_same) {
				delete_prcodeno[m] = document.form1.comcode.options[i].value;
				m++;
			}
		}
		
		if(m>0) {
			for (m=0;m<delete_prcodeno.length;m++) {
				if (m==0) codes = delete_prcodeno[m];
				else codes+=","+delete_prcodeno[m];
			}
		}

		prices = getPrice();
		if(parent.package_list_update(codes,prices)) {
			document.form1.package_title_list.value = codes;
			document.form1.package_price_list.value = prices;
			document.form1.submit();
		}
	} else {
		alert("삭제하실 상품을 선택하세요");
	}
}

function move(gbn) {
	var move_count=0;
	for(j=0; j<document.form1.comcode.options.length; j++) {
		if(document.form1.comcode.options[j].selected) {
			move_count++;
		}
	}
	if(move_count>1) {
		alert("순서변경할 상품 하나만 선택해 주세요.");
		return;
	}

	change_idx = document.form1.comcode.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 상품을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 상품은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.comcode.length-1)) {
		alert("선택하신 상품은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.comcode.options[idx].value;
	idx_text = document.form1.comcode.options[idx].text;

	document.form1.comcode.options[idx].value = document.form1.comcode.options[change_idx].value;
	document.form1.comcode.options[idx].text = document.form1.comcode.options[change_idx].text;

	document.form1.comcode.options[change_idx].value = idx_value;
	document.form1.comcode.options[change_idx].text = idx_text;

	document.form1.comcode.selectedIndex = idx;
}

function basic_save() {
	if (document.form1.pricemo.value.length>0) {
		if(document.form1.pricemotype.value=="Y") {
			if(isDigitSpecial(document.form1.pricemo.value,".")) {
				alert("할인/할증률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.pricemo.focus();
				return;
			}
			if(getSplitCount(document.form1.pricemo.value,".")>2) {
				alert("할인/할증률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.pricemo.focus();
				return;
			}
			if(getPointCount(document.form1.pricemo.value,".",2)) {
				alert("할인/할증률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.pricemo.focus();
				return;
			}
			if(Number(document.form1.pricemo.value)>100 || Number(document.form1.pricemo.value)<0) {
				alert("할인/할증률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.pricemo.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.pricemo.value,"")) {
				alert("할인/할증금은 숫자로만 입력하세요.");
				document.form1.pricemo.focus();
				return;
			}
		}
	}

	if (confirm("\n         현재 패키지 상품의 정보를 저장하시겠습니까?\n\n※ 최종 적용은 하단의 적용하기 버튼을 누르셔야만 저장됩니다.")) {
		codes = getCodes();
		prices = getPrice();
		if(parent.package_list_update(codes,prices)) {
			document.form1.package_title_list.value = codes;
			document.form1.package_price_list.value = prices;
			document.form1.submit();
		}
	}
}

function chkFieldMaxLenFunc(thisForm,pricemoType) {
	if (pricemoType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=7; }
	if (thisForm.pricemo.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.pricemo.value = thisForm.pricemo.value.cut(max);
		thisForm.pricemo.focus();
	}
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

function getPrice() {
	prices = "";
	if(document.form1.pricemo.value.length>0) {
		prices = document.form1.pricemo.value+","+document.form1.pricemotype.value+","+document.form1.priceupdown.value+","+document.form1.pricedanwi.value+","+document.form1.pricecut.value;
	}
	return prices;
}

function getCodes() {
	codes = "";
	for (i=0;i<=(document.form1.comcode.length-1);i++) {
		if (i==0) codes = document.form1.comcode.options[i].value;
		else codes+=","+document.form1.comcode.options[i].value;
	}
	return codes;
}

//-->
</SCRIPT>
<TABLE WIDTH="100%" height="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td bgcolor="#F1FFEF" style="border:2px #57B54A solid;" valign="top">
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<?php
	if(ord($package_title_code)) {
?>
	<tr>
		<td valign="top" bgcolor="#F1FFEF" height="100%" width="100%" style="padding:5px;">
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type=hidden name=mode value="<?=$mode?>">
		<input type=hidden name=code value="<?=$code?>">
		<input type=hidden name=package_title_list>
		<input type=hidden name=package_title_code value="<?=$package_title_code?>">
		<input type=hidden name=package_price_list value="<?=$package_price_list?>">
		<tr>
			<td height="7" colspan="3"></td>
		</tr>
		<tr>
			<td align="center" height="30" colspan="3"><b>패키지 상품 목록 관리</b></td>
		</tr>
		<tr>
			<td height="3" colspan="3"></td>
		</tr>
		<tr>
			<td colspan="3"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
		</tr>
		<tr>
			<td height="5" colspan="3"></td>
		</tr>
		<tr>
			<td width="50%" valign="bottom" bgcolor="#F1FFEF">
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<tr>
				<td style="border:1px #B9B9B9 solid;" bgcolor="#FFFFFF">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<TD class="table_cell" width="90" height="40"><img src="images/icon_point2.gif" border="0">상품보기선택</TD>
						<TD class="td_con1"><span style="letter-spacing:-0.5pt;"><input type=radio id="idx_searchtype1" name=searchtype value="0" style="border:none" onclick="ViewLayer('layer1')" <?php if($searchtype=="0") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>카테고리별 상품 보기</label> <input type=radio id="idx_searchtype2" name=searchtype value="1" style="border:none" onclick="ViewLayer('layer2')" <?php if($searchtype=="1") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>검색 상품 보기</label></span></TD>
					</tr>
					<TR>
						<TD colspan="2" bgcolor="#B9B9B9" height="1"></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<tr>
					<TD>
					<div id=layer1 style="margin-left:0;display:hide; display:<?=($searchtype=="0"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<DIV class=MsgrScroller id=contentDiv style="width=100%;height:164px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('');">최상위 카테고리</span></td>
					</tr>
					<tr>
						<!-- 상품카테고리 목록 -->
						<td id="code_list" nowrap valign=top></td>
						<!-- 상품카테고리 목록 끝 -->
					</tr>
					</table>
					</DIV>
					</DIV>
					</div>
					<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="1"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<TD class="table_cell" width="90"><img src="images/icon_point2.gif" border="0">상품명 입력</TD>
						<TD class="td_con1">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="99%"><input type=text name=keyword size=30 value="<?=$keyword?>" onKeyDown="CheckKeyPress()" class="input" style="width:100%;"></td>
							<td width="1%" align="right"><a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" border="0" align=absmiddle hspace="2"></a></td>
						</tr>
						</table>
						</TD>
					</tr>
					</table>
					</div>
					</TD>
				</tr>
				</table>
				</td>
			</tr>
			<TR>
				<TD width="100%" align="center" style="border-left:1px #B9B9B9 solid;border-right:1px #B9B9B9 solid;" height="40"><img src="images/product_collectionlist_img1.gif" border="0"></TD>
			</TR>
			<TR>
				<TD width="100%" align="center"><select name=prcode size=10 style="width:100%;" class="select" multiple>
<?php
		if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>3)) {
			$sql = "SELECT pridx,productname,quantity,display FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode  and c_maincate=1)";
			if($searchtype=="0") $sql.= "WHERE b.c_category LIKE '{$code}%' ";
			else $sql.= "WHERE productname LIKE '%{$keyword}%' ";
			$sql.= "AND assembleuse != 'Y' ";
			$sql.= "AND vender = '0' ";
			$sql.= "ORDER BY productname";
			$result = pmysql_query($sql,get_db_conn());

			$count = 0;
			while ($row = pmysql_fetch_object($result)) {
				$count++;
				$quantity=(ord($row->quantity)==0)?"무제한":$row->quantity."개";
				$display=($row->display=="Y")?"판매중":"판매중지";
				if ($prcode == $row->pridx) {
					echo "<option selected value=\"{$row->pridx}\">{$row->productname} [재고:{$quantity} ,{$display}]</option>\n";
					$productname=$row->productname;
				} else {
					echo "<option value=\"{$row->pridx}\">{$row->productname} [재고:{$quantity} ,{$display}]</option>\n";
				}
			}
			pmysql_free_result($result);
		}
?>
				</select></TD>
			</TR>
			</table>
			</td>
			<td width="10" bgcolor="#F1FFEF" nowrap></td>
			<td width="50%" valign="bottom" bgcolor="#F1FFEF">
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<tr>
				<td align="center" height="207" style="border:1px #B9B9B9 solid;border-bottom:0px;" valign="top">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="40" style="border-bottom:1px #B9B9B9 solid;" align="center"><span class="font_orange"><b>패키지 상품 설정 주의사항</b></span></td>
				</tr>
				<tr>
					<td style="padding:5px;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td style="padding:2px;padding-left:10px;padding-right:5px;">1. 패키지 가격(절사)<br><b>&nbsp;&nbsp;</b>&nbsp;패키지 상품들의 판매가격 합계 + 할인/할증에 따른 가격</td>
					</tr>
					<tr>
						<td style="padding:2px;padding-left:10px;padding-right:5px;">2. 할인/할증률일 경우 패키지 상품들의 판매가격 합계에<br><b>&nbsp;&nbsp;</b>&nbsp;맞춰 변경된 가격이 산출됩니다.</td>
					</tr>
					<tr>
						<td style="padding:2px;padding-left:10px;padding-right:5px;">3. 패키지 등록 상품 순서변경 또는 할인/할증 변경 후<br><b>&nbsp;&nbsp;</b>&nbsp;필히 저장하기 버튼을 클릭하세요.</td>
					</tr>
					<tr>
						<td style="padding:2px;padding-left:10px;padding-right:5px;">4. 할인/할증을 적용하지 않을 경우 미입력 후 저장하세요.</td>
					</tr>
					<tr>
						<td style="padding:2px;padding-left:10px;padding-right:5px;">5. 할인/할증률 입력은 소수점 둘째자리까지 가능합니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center" height="40" style="border:1px #B9B9B9 solid;border-bottom:0px;"><b>등록된 패키지 상품</b></td>
			</tr>
			<TR>
				<TD width="100%" valign="top" height="100%">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height="100%">
				<TR>
					<TD width="100%" height="100%" valign="top"><select name=comcode size=10 style="width:100%;" class="select" multiple>
<?php
		$mode="insert";
		$count=1;
		if(ord($package_title_list)){
			$sql = "SELECT productname,pridx,quantity,display FROM tblproduct 
			WHERE pridx IN ('".str_replace(",","','",trim($package_title_list,','))."') 
			AND assembleuse != 'Y' 
			AND vender = '0' 
			ORDER BY FIELD(pridx,'".str_replace(",","','",trim($package_title_list,','))."')";
			$result = pmysql_query($sql,get_db_conn());
			while ($row = pmysql_fetch_object($result)) {
				echo "<option value=\"{$row->pridx}\">{$count}.{$row->productname} [재고:".((ord($row->quantity)==0)?"무제한":$row->quantity."개")." ,".(($row->display=="Y")?"판매중":"판매중지")."]</option>\n";
				$count++;
			}
			$mode="modify";
		}
?>
					</select></TD>
					<TD noWrap align="center" valign="top" height="100%" style="border:1px #B9B9B9 solid;border-left:0px;padding:1px;padding-left:3px;padding-right:3px;">
					<table cellpadding="0" cellspacing="0" height="100%">
					<TR>
						<TD align="center" style="padding-bottom:2px;" valign="top"><A href="JavaScript:move('up');"><IMG src="images/code_up.gif" align="absMiddle" border="0"></A><br><IMG src="images/code_sort.gif" align="absMiddle" border="0" vspace="2"><br><A href="JavaScript:move('down');"><IMG src="images/code_down.gif" align="absMiddle" border="0"></A></td>
					</tr>
					<TR>
						<TD align="center" valign="bottom"><A href="JavaScript:Delete();"><IMG src="images/code_delete.gif" align="absMiddle" border="0"></A></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<TR>
			<TD bgcolor="#F1FFEF" align="center" style="padding-top:5px;border:1px #B9B9B9 solid;border-top:0px;"><a href="javascript:InsertCollection();"><img src="images/btn_packagelist.gif" border="0"></a></TD>
			<td bgcolor="#F1FFEF"></td>
			<td bgcolor="#F1FFEF" valign="top" style="border:1px #B9B9B9 solid;border-top:0px;">
<?php
	if(ord($package_price_list)) {
		$package_price_list_exp = explode(",",$package_price_list);

		if(ord($package_price_list_exp[0]) && (int)$package_price_list_exp[0]>0) {
			$pricemo		= $package_price_list_exp[0];
			$pricemotype	= $package_price_list_exp[1];
			$priceupdown	= $package_price_list_exp[2];
			$pricedanwi		= $package_price_list_exp[3];
			$pricecut		= $package_price_list_exp[4];
		}
	}

	if($pricemotype!="Y" && $pricemotype!="N") {
		$pricemotype="Y";
	}
	if($priceupdown!="Y" && $priceupdown!="N") {
		$priceupdown="Y";
	}
	if($pricedanwi!="1" && $pricedanwi!="10" && $pricedanwi!="100" && $pricedanwi!="1000") {
		$pricedanwi="1";
	}
	if($pricecut!="F" && $pricecut!="R" && $pricecut!="C") {
		$pricecut="F";
	}
?>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding-top:5px;padding-left:5px;padding-right:5px;">등록된 패키지 합계금액에 <input type=text name=pricemo value="<?=$pricemo?>" size=6 maxlength=7 style="text-align:right" class="input" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.pricemotype.value);"> <select name=pricemotype onchange="chkFieldMaxLenFunc(this.form,this.value);" class="input">
					<option value="Y"<?=($pricemotype!="N"?" selected":"")?>>%
					<option value="N"<?=($pricemotype=="N"?" selected":"")?>>원
					</select>를 <select name=priceupdown class="input">
					<option value="Y"<?=($priceupdown!="N"?" selected":"")?>>할인
					<option value="N"<?=($priceupdown=="N"?" selected":"")?>>할증
					</select></td>
				<td rowspan="2" style="padding-top:5px;padding-left:3px;padding-right:3px;border-left:1px #B9B9B9 solid;"><a href="javascript:basic_save();"><img src="images/code_save2.gif" border="0" vspace="0" align="absmiddle"></a></td>
			</tr>
			<tr>
				<td width="100%" style="padding-top:5px;padding-bottom:3px;padding-left:5px;padding-right:5px;">절사는 <select name=pricedanwi class="input">
					<option value="1"<?=($pricedanwi=="1"?" selected":"")?>>1
					<option value="10"<?=($pricedanwi=="10"?" selected":"")?>>10
					<option value="100"<?=($pricedanwi=="100"?" selected":"")?>>100
					<option value="1000"<?=($pricedanwi=="1000"?" selected":"")?>>1000
				</select>원 단위로 <select name=pricecut class="input">
					<option value="F"<?=($pricecut=="F"?" selected":"")?>>내림
					<option value="R"<?=($pricecut=="R"?" selected":"")?>>반올림
					<option value="C"<?=($pricecut=="C"?" selected":"")?>>올림
				</select> 절사</td>
			</tr>
			</table>
			</td>
		</TR>
		<TR>
			<TD colspan="2" height="5" bgcolor="#F1FFEF"></TD>
		</TR>
		</form>
		<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type=hidden name=mode value="changecode">
		<input type=hidden name=code>
		<input type=hidden name=package_title_list value="<?=$package_title_list?>">
		<input type=hidden name=package_title_code value="<?=$package_title_code?>">
		<input type=hidden name=package_price_list value="<?=$package_price_list?>">
		</form>
		</table>
<?php
		$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
		$sql.= "ORDER BY sequence DESC ";
		include("codeinit.php");
	} else {
?>
		<TABLE WIDTH="100%" height="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<td align="center" bgcolor="#F1FFEF">패키지 타이틀을 선택해 주세요.</td>
		</tr>
		</table>
<?php
	}
?>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
