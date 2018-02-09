<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$addr_group=$_POST["addr_group"];
$search=$_POST["search"];

include_once("../lib/adminlib.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
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

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.tels_chk[i].checked=chkval;
	}
}

function SearchGroup() {
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.search.value="";
	document.form1.submit();
}

function ToAddressAdd(tel_txt,tel_val) {
	try {
		if(tel_txt.length<12 || tel_txt.length>13) {
			alert("전화번호 입력이 잘못되었습니다. ("+tel_txt+")");
			return;
		}
		to_list=opener.document.form1.to_list;
		if(to_list.options.length>50) {
			alert("받는 사람은 1회 50명 까지 가능합니다.");
			return;
		}
		for(i=1;i<to_list.options.length;i++) {
			if(tel_val==to_list.options[i].value) {
				//alert("이미 추가된 번호입니다.\n\n다시 확인하시기 바랍니다.");
				return;
			}
		}

		new_option = opener.document.createElement("OPTION");
		new_option.text=tel_txt;
		new_option.value=tel_val;
		to_list.add(new_option);
		cnt=to_list.options.length - 1;
		to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
	} catch (e) {

	}
}


function select(mobile) {
	tel_val=mobile.replace("-","");
	ToAddressAdd(mobile,tel_val);
}

function select_list() {
	issel=false;
	for(i=1;i<document.form1.tels_chk.length;i++) {
		if(document.form1.tels_chk[i].checked) {
			issel=true;
			tel_val=document.form1.tels_chk[i].value.replace("-","");
			ToAddressAdd(document.form1.tels_chk[i].value,tel_val);
		}
	}
	if(issel==false) {
		alert("선택하신 SMS번호가 없습니다.");
		return;
	}
}

function search_name() {
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>SMS 주소 등록/수정</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" oncontextmenu="return false">
<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<tr>
	<TD style="padding:10pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><a href="javascript:select_list();"><img src="images/btn_select1a.gif" border="0"></a>&nbsp;&nbsp;그룹선택 <select name=addr_group onChange="SearchGroup();" class="select">
			<option value="">전체</option>
<?php
			$sql = "SELECT addr_group FROM tblsmsaddress GROUP BY addr_group ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				echo "<option value=\"{$row->addr_group}\"";
				if($addr_group==$row->addr_group) echo " selected";
				echo ">{$row->addr_group}</option>\n";
			}
			pmysql_free_result($result);
?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<input type=hidden name=tels_chk>
		<TR>
			<th><input type=checkbox name=allcheck onClick="CheckAll()"></th>
			<th>이름</th>
			<th>휴대폰번호</th>
		</TR>
<?php
		$qry = "WHERE 1=1 ";
		if(ord($addr_group)) $qry.= "AND addr_group='{$addr_group}' ";
		if(ord($search)) $qry.= "AND name LIKE '{$search}%' ";

		$sql = "SELECT COUNT(*) as t_count FROM tblsmsaddress ".$qry;
		$paging = new Paging($sql,10,20);
		$t_count = $paging->t_count;	
		$gotopage = $paging->gotopage;

		$sql = "SELECT * FROM tblsmsaddress {$qry} ";
		$sql.= "ORDER BY name ASC ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			echo "<tr>\n";
			echo "	<TD><input type=checkbox name=tels_chk value=\"{$row->mobile}\"></td>\n";
			echo "	<TD><b><span class=\"font_orange\">{$row->name}</span></b></TD>\n";
			echo "	<TD><A HREF=\"javascript:select('{$row->mobile}')\">{$row->mobile}</A></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><td class=\"td_con1\" colspan=3 align=center>조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
		}
?>
		</TABLE>
        </div>
		</td>
	</tr>
	<tr>
		<td height=10></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" class="font_size" align="center">
<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
			</td>
		</tr>
		<tr>
			<td width="100%" class="main_sfont_non" height=10></td>
		</tr>
		<tr>
			<td width="100%" class="main_sfont_non">
			<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
			<tr>
				<td width="859" bgcolor="white" align="center">이름검색 : <input type=text name=search value="<?=$search?>" size=30 class="input"> <a href="javascript:search_name();"><img alt=검색 align=absMiddle border=0 src="images/icon_search.gif"></a></td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>
</body>
</html>
