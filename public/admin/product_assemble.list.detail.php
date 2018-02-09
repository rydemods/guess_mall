<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$assemble_title_code=$_POST["assemble_title_code"];
$assemble_title_list=$_POST["assemble_title_list"];
$assemble_basic_code=$_POST["assemble_basic_code"];

$mode=$_POST["mode"];
$code=$_POST["code"];
$keyword=$_POST["keyword"];
$searchtype=$_POST["searchtype"];
if (ord($searchtype)==0) $searchtype=0;

if(strlen($code)==12) {
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');	
}

include("header.php"); 
?>
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
		alert("구성 상품에 포함할 상품을 선택하세요.");
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

	if(num+insert_count>=30){
		alert("구성 상품은 최대 30개까지 등록가능합니다.");
		return;
	} else {
		if (confirm("해당 상품을 구성 상품으로 포함하시겠습니까?")){
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

			if(parent.assemble_list_update(temp,document.form1.assemble_basic_code.value)) {
				document.form1.assemble_title_list.value = temp;
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
		document.form1.assemble_title_list.value = codes;
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
		if(!confirm("선택하신 상품을 구성 상품에서 삭제하시겠습니까?")) return;
		document.form1.mode.value="delete";
		codes = "";
		num = document.form1.comcode.length-1;
		delcode=document.form1.comcode.options[document.form1.comcode.selectedIndex].value;
		j=-1;
		for (i=0;i<=num;i++) {
			if(delcode!=document.form1.comcode.options[i].value){
				j++;
				if (j==0) codes = document.form1.comcode.options[i].value;
				else codes+=","+document.form1.comcode.options[i].value;
			}
		}
		if(parent.assemble_list_update(codes,document.form1.assemble_basic_code.value)) {
			document.form1.assemble_title_list.value = codes;
			document.form1.submit();
		}
	} else {
		alert('삭제하실 상품을 선택하세요');
	}
}

function move(gbn) {
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

function move_save() {
	if (confirm("현재의 내용을 저장하시겠습니까?")) {
		codes = getCodes();

		if(parent.assemble_list_update(codes,document.form1.assemble_basic_code.value)) {
			document.form1.assemble_title_list.value = codes;
			document.form1.submit();
		}
	}
}

function basic_save() {
	if(document.form1.comcode.selectedIndex<0) {
		alert("등록된 구성 상품을 선택해 주세요.");
		document.form1.comcode.focus();
		return;
	} else {
		if(document.form1.assemble_basic_code.value.length>0) {
			if(document.form1.comcode.options[document.form1.comcode.selectedIndex].value==document.form1.assemble_basic_code.value) {
				if(!document.form1.assemble_basic_check.checked) {
					if(confirm("현재 선택된 구성 상품을 기본 구성 상품에서 제외 시키겠습니까?")) {
						codes = getCodes();
						if(parent.assemble_list_update(codes,"")) {
							document.form1.assemble_title_list.value = codes;
							document.form1.assemble_basic_code.value="";
							document.form1.submit();
						}
					}
				}
			} else {
				if(document.form1.assemble_basic_check.checked) {
					if(confirm("현재 선택된 구성 상품을 기본 구성 상품으로 변경하겠습니까?")) {
						codes = getCodes();
						if(parent.assemble_list_update(codes,document.form1.comcode.options[document.form1.comcode.selectedIndex].value)) {
							document.form1.assemble_title_list.value = codes;
							document.form1.assemble_basic_code.value=document.form1.comcode.options[document.form1.comcode.selectedIndex].value;
							document.form1.submit();
						}
					}
				}
			}
		} else {
			if(document.form1.assemble_basic_check.checked) {
				if(confirm("현재 선택된 구성 상품을 기본 구성 상품으로 적용하겠습니까?")) {
					codes = getCodes();
					if(parent.assemble_list_update(codes,document.form1.comcode.options[document.form1.comcode.selectedIndex].value)) {
						document.form1.assemble_title_list.value = codes;
						document.form1.assemble_basic_code.value=document.form1.comcode.options[document.form1.comcode.selectedIndex].value;
						document.form1.submit();
					}
				}
			}
		}
	}
}

function comcode_change() {
	if(document.form1.comcode.options[document.form1.comcode.selectedIndex].value==document.form1.assemble_basic_code.value) {
		document.form1.assemble_basic_check.checked=true;
	} else {
		document.form1.assemble_basic_check.checked=false;
	}
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
<?php
	if(ord($assemble_title_code)) {
?>
<tr>
	<td valign="top" bgcolor="#F1FFEF">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=assemble_title_list>
	<input type=hidden name=assemble_title_code value="<?=$assemble_title_code?>">
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<tr>
		<td style="border:1px #B9B9B9 solid;" bgcolor="#FFFFFF">
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<TD>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<TD class="table_cell" width="90"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품보기선택</TD>
				<TD class="td_con1"><span style="letter-spacing:-0.5pt;"><input type=radio id="idx_searchtype1" name=searchtype value="0" style="border:none" onclick="ViewLayer('layer1')" <?php if($searchtype=="0") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>카테고리별 상품 보기</label> <input type=radio id="idx_searchtype2" name=searchtype value="1" style="border:none" onclick="ViewLayer('layer2')" <?php if($searchtype=="1") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>검색으로 상품 보기</label></span></TD>
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
			<DIV class=MsgrScroller id=contentDiv style="width=100%;height:140px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
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
			<div id=layer2 style="margin-left:0;display:<?=($searchtype=="1"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<TD class="table_cell" width="90"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품명 입력</TD>
				<TD class="td_con1">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="99%"><input type=text name=keyword size=30 value="<?=$keyword?>" onKeyDown="CheckKeyPress()" class="input" style="width:100%;"></td>
					<td width="1%" align="right"><a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" width="50" height="25" border="0" align=absmiddle hspace="2"></a></td>
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
		<TD width="100%" align="center" style="border-left:1px #F1FFEF solid;"><img src="images/product_collectionlist_img1.gif" width="80" height="23" border="0"></TD>
	</TR>
	<TR>
		<TD width="100%" align="center"><select name=prcode size=7 style="width:100%;" class="select" multiple>
<?php
			if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>3)) {
				$sql = "SELECT pridx,productname,quantity,display FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
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
	<TR>
		<TD height="5"></TD>
	</TR>
	<TR>
		<TD width="100%" align="center"><a href="javascript:InsertCollection();"><img src="images/btn_assemblelist.gif" border="0"></a></TD>
	</TR>
	<TR>
		<TD height="5"></TD>
	</TR>
	<TR>
		<TD width="100%">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height="100%">
		<TR>
			<TD width="100%" height="100%"><select name=comcode size=9 style="width:100%;" class="select" onchange="comcode_change();">
<?php
				$mode="insert";
				$count=1;
				if(ord($assemble_title_list)){
					$sql = "SELECT productname,pridx,quantity,display FROM tblproduct 
					WHERE pridx IN ('".str_replace(",","','",ltrim($assemble_title_list,','))."') 
					AND assembleuse != 'Y' 
					AND vender = '0'
					ORDER BY FIELD(pridx,'".str_replace(",","','",ltrim($assemble_title_list,','))."') ";
					$result = pmysql_query($sql,get_db_conn());
					while ($row = pmysql_fetch_object($result)) {
						if($assemble_basic_code==$row->pridx) {
							$assemble_basic_codeValue=$assemble_basic_code;
							echo "<option value=\"{$row->pridx}\">{$count}.[기본]{$row->productname} [재고:".((ord($row->quantity)==0)?"무제한":$row->quantity."개")." ,".(($row->display=="Y")?"판매중":"판매중지")."]</option>\n";
						} else {
							echo "<option value=\"{$row->pridx}\">{$count}.{$row->productname} [재고:".((ord($row->quantity)==0)?"무제한":$row->quantity."개")." ,".(($row->display=="Y")?"판매중":"판매중지")."]</option>\n";
						}
						$count++;
					}
					$mode="modify";
				}
?>
				</select></TD>
			<TD noWrap align="center" width="50">
			<table cellpadding="0" cellspacing="0" width="34">
			<TR>
				<TD align="center"><A href="JavaScript:move('up');"><IMG src="images/code_up.gif" align="absMiddle" border="0" vspace="2"></A></td>
			</tr>
			<TR>
				<TD align="center"><A href="JavaScript:move('down');"><IMG src="images/code_down.gif" align="absMiddle" border="0" vspace="2"></A></td>
			</tr>
			<TR>
				<TD align="center"><A href="JavaScript:move_save();"><IMG src="images/code_save2.gif" align="absMiddle" border="0" vspace="2"></A></td>
			</tr>
			<TR>
				<TD align="center"><A href="JavaScript:Delete();"><IMG src="images/code_delete.gif" align="absMiddle" border="0" vspace="2"></A></td>
			</tr>
			</table>
			</TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=assemble_basic_code value="<?=$assemble_basic_codeValue?>">
	<TR>
		<TD height="2"></TD>
	</TR>
	<TR>
		<TD colspan="2"><input type=checkbox name="assemble_basic_check" id="idx_assemble_basic_check" value="Y"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_assemble_basic_check><span class="font_orange"><b>기본 구성 상품으로 적용(판매가격적용)</b></span></label>&nbsp;<a href="javascript:basic_save();"><img src="images/btn_save1.gif" border="0" vspace="0" align="absmiddle"></a><br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange" style="font-size:8pt;">* 등록된 구성 상품 중 하나만 적용 가능합니다.</span></TD>
	</TR>
	<TR>
		<TD height="2"></TD>
	</TR>
	</form>
	<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value="changecode">
	<input type=hidden name=code>
	<input type=hidden name=assemble_title_list value="<?=$assemble_title_list?>">
	<input type=hidden name=assemble_title_code value="<?=$assemble_title_code?>">
	<input type=hidden name=assemble_basic_code value="<?=$assemble_basic_codeValue?>">
	</form>
	</TABLE>
<?php
		$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
		$sql.= "ORDER BY sequence DESC ";
		include("codeinit.php");
	} else {
?>
	<TABLE WIDTH="100%" height="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<td align="center" bgcolor="#F1FFEF">구성 상품 타이틀을 선택해 주세요.</td>
	</tr>
	</table>
<?php
	}
?>
	</td>
</tr>
</table>
</body>
</html>
