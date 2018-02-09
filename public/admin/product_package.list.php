<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$Scrolltype=$_REQUEST["Scrolltype"];
$sort=$_REQUEST["sort"];

$onload=$_POST["onload"];
$mode=$_POST["mode"];

$package_num=$_REQUEST["package_num"];
$package_name=$_POST["package_name"];
$package_type=$_POST["package_type"];
$package_product_list=$_POST["package_product_list"];
$package_title_list=$_POST["package_title_list"];
$package_price_list=$_POST["package_price_list"];

if ($mode=="update" || $mode=="delete") {
	if($mode=="update") {
		if(ord($package_title_list) && ord($package_name)) {
			$sql = "SELECT num FROM tblproductpackage ";
			$sql.= "WHERE num = '".(int)$package_num."' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=@pmysql_fetch_object($result);

			if($row) {
				pmysql_free_result($result);
				$sql = "UPDATE tblproductpackage SET ";
				$sql.= "package_name = '{$package_name}', ";
				$sql.= "package_type = '".($package_type=="Y"?"Y":"N")."', ";
				$sql.= "package_title = '{$package_title_list}', ";
				$sql.= "package_price = '{$package_price_list}', ";
				$sql.= "package_list = '{$package_product_list}' ";
				$sql.= "WHERE num = '".(int)$package_num."' ";
				pmysql_query($sql,get_db_conn());
			} else {
				$sql = "INSERT INTO tblproductpackage(
				package_name ,
				package_type ,
				package_title,
				package_price,
				package_list) VALUES (
				'{$package_name}', 
				'".($package_type=="Y"?"Y":"N")."', 
				'{$package_title_list}', 
				'{$package_price_list}', 
				'{$package_product_list}')";
				pmysql_query($sql,get_db_conn());
			}
		}
	} else {
		$sql = "SELECT num FROM tblproductpackage ";
		$sql.= "WHERE num = '".(int)$package_num."' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=@pmysql_fetch_object($result);

		if($row) {
			pmysql_free_result($result);
			$sql = "UPDATE tblproduct SET ";
			$sql.= "package_num = '0' ";
			$sql.= "WHERE package_num = '".(int)$package_num."' ";
			pmysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblproductpackage ";
			$sql.= "WHERE num = '".(int)$package_num."' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	
	$onload = "<script type=\"text/javascript\">alert(\"패키지 그룹 처리가 정상 완료 됐습니다.\");</script>";
	echo "
		<form name=form1 action=\"{$_SERVER['PHP_SELF']}\" method=post>\n
		<input type=hidden name=mode value=\"\">
		<input type=hidden name=sort value=\"{$sort}\">
		<input type=hidden name=Scrolltype value=\"{$Scrolltype}\">
		<input type=hidden name=onload value=\"".htmlspecialchars($onload)."\">
		</form>\n
		<script type=\"text/javascript\">
		<!--
			document.form1.submit();
		//-->
		</script>
	";
	exit;
}

$imagepath=$Dir.DataDir."shopimages/product/";
?>

<?php include("header.php"); ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<!--
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
-->
<script type="text/javascript">
 function init(){
  var doc= document.getElementById("divName");
  if(doc.offsetHeight!=0){
   pageheight = doc.offsetHeight;
   parent.document.getElementById("ListFrame").height=pageheight+"px";
   }
 }

 window.onload=function(){
  init();
 }
</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script language="JavaScript">
<!--
var ProductInfoStop="";

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.submit();
}

function SelectListUpdate(idx) {
	if(idx != document.form1.package_num.value) {
		document.form1.mode.value = '';
		document.form1.package_num.value = idx;
		document.form1.submit();
	}
}

function SelectListDelete(idx) {
	if(confirm("해당 패키지 그룹 정보를 삭제하겠습니까?")) {
		document.form2.package_num.value = idx;
		document.form2.submit();
	}
}

function onMouseColor(argValue) {
	if(document.form1.package_num.value != argValue) {
		return true;
	} else {
		return false;
	}
}

function DivScrollActive(arg1,divscroll_id,ListTable_id,activetype) {
	if(!self.id) {
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
	
	if(document.getElementById(divscroll_id) && document.getElementById("ListTTableId") && document.getElementById(ListTable_id) && parent.document.getElementById(self.id)) {
		if(!document.getElementById(divscroll_id).height) {
			document.getElementById(divscroll_id).height=document.getElementById(divscroll_id).offsetHeight;
		}

		if(arg1>0) {
			if(document.getElementById(ListTable_id).offsetHeight > document.getElementById(divscroll_id).offsetHeight) {
				document.getElementById(divscroll_id).style.height="100%";
				parent.document.getElementById(self.id).style.height=document.getElementById("ListTTableId").offsetHeight;
			}
		} else {
			var default_divwidth = document.getElementById("ListTTableId").offsetHeight-document.getElementById(divscroll_id).offsetHeight+document.getElementById(divscroll_id).height;
			document.getElementById(divscroll_id).style.height=document.getElementById(divscroll_id).height;
			parent.document.getElementById(self.id).style.height=default_divwidth;
		}
	}
	
	document.form1.Scrolltype.value = arg1;
}

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function package_title_change(packageFormtype) {
	form = document.form1;
	form.package_title_name.value="";
	form.package_update_type.value="";
	form.package_title_list.value="";
	form.package_price_list.value="";
	form.package_modify_list.value="";
	
	if(document.getElementById("btnidx")){
		document.getElementById("btnidx").src = "images/btn_input.gif";
	}

	if(packageFormtype=="Y") {
		packageFormSubmit();
	}
}

function packageFormSubmit() {
	form = document.form1;
	
	if(form.package_title.selectedIndex<0) {
		document.packageForm.package_title_code.value = "";
		document.packageForm.package_title_list.value = "";
		document.packageForm.package_price_list.value = "";
	} else {
		document.packageForm.package_title_code.value = form.package_title.selectedIndex;
		package_array_num = form.package_title.options[form.package_title.selectedIndex].value.split('');
		document.packageForm.package_title_list.value = package_array_num[1];
		document.packageForm.package_price_list.value = package_array_num[2];
	}
	document.packageForm.submit();
}

function package_title_modify() {
	form = document.form1;
	if(form) {
		if(form.package_title.selectedIndex<0) {
			alert("수정할 패키지 타이틀을 선택해 주세요.");
			form.package_title.focus();
		}
		else {
			form.package_update_type.value="modify";
			package_array_num=form.package_title.options[form.package_title.selectedIndex].value.split('');
			
			form.package_title_name.value=package_array_num[0];
			form.package_modify_list.value=package_array_num[1];
			form.package_price_list.value=package_array_num[2];

			if(document.getElementById("btnidx")){
				document.getElementById("btnidx").src = "images/btn_modify.gif";
			}
		}
	} else {
		alert("패키지 타이틀 추가 중 오류가 발생됐습니다.");
		return;
	}
}

function package_title_update() {
	form = document.form1;
	if(form) {
		if(form.package_title_name.value.length>0) {
			if(form.package_update_type.value=="modify") {
				form.package_title.options[form.package_title.selectedIndex].value=form.package_title_name.value+""+form.package_modify_list.value+""+form.package_price_list.value;
				form.package_title.options[form.package_title.selectedIndex].text=form.package_title_name.value;
			} else {
				form.package_title.options[form.package_title.length] = new Option(form.package_title_name.value,form.package_title_name.value+""+"", false, false);
			}
			package_title_change('');
		} else {
			alert("패키지 타이틀을 입력해 주세요.");
			form.package_title_name.focus();
			return;
		}
	} else {
		alert("패키지 타이틀 추가 중 오류가 발생됐습니다.");
		return;
	}
}

function package_title_move(movetype) {
	form = document.form1;
	if(form) {
		if(form.package_title.selectedIndex<0) {
			alert("이동할 패키지 타이틀을 선택해 주세요.");
			form.package_title.focus();
		} else {
			var moveobj_value = form.package_title.options[form.package_title.selectedIndex].value;
			var moveobj_text = form.package_title.options[form.package_title.selectedIndex].text;

			if(movetype=="up") {
				if(form.package_title.selectedIndex>0 && form.package_title.options[form.package_title.selectedIndex-1]) {
					var movego_value = form.package_title.options[form.package_title.selectedIndex-1].value;
					var movego_text = form.package_title.options[form.package_title.selectedIndex-1].text;
					form.package_title.options[form.package_title.selectedIndex-1].value=moveobj_value;
					form.package_title.options[form.package_title.selectedIndex-1].text=moveobj_text;
					form.package_title.options[form.package_title.selectedIndex].value=movego_value;
					form.package_title.options[form.package_title.selectedIndex].text=movego_text;
					form.package_title.options[form.package_title.selectedIndex].selected=false;
					form.package_title.options[form.package_title.selectedIndex-1].selected=true;
				}
			} else {
				if(form.package_title.options[form.package_title.selectedIndex+1]) {
					var movego_value = form.package_title.options[form.package_title.selectedIndex+1].value;
					var movego_text = form.package_title.options[form.package_title.selectedIndex+1].text;
					form.package_title.options[form.package_title.selectedIndex+1].value=moveobj_value;
					form.package_title.options[form.package_title.selectedIndex+1].text=moveobj_text;
					form.package_title.options[form.package_title.selectedIndex].value=movego_value;
					form.package_title.options[form.package_title.selectedIndex].text=movego_text;
					form.package_title.options[form.package_title.selectedIndex].selected=false;
					form.package_title.options[form.package_title.selectedIndex+1].selected=true;
				}
			}
			package_title_change('');
		}
	}
}

function package_title_delete() {
	form = document.form1;
	if(form) {
		if(form.package_title.selectedIndex<0) {
			alert("삭제할 패키지 타이틀을 선택해 주세요.");
			form.package_title.focus();
		} else {
			if(confirm("선택된 패키지 타이틀을 삭제 하겠습니까?")) {
				form.package_title.options[form.package_title.selectedIndex]=null;
				package_title_change('Y');
			}
		}
	}
}

function package_list_update(packagelistproduct,packagepriceproduct) {
	form = document.form1;
	if(form.package_title.selectedIndex<0) {
		alert('패키지 타이틀이 선택 안돼 있습니다.');
		form.package_title.focus();
		return false;
	} else {
		form.package_update_type.value="modify";
		package_array_num=form.package_title.options[form.package_title.selectedIndex].value.split('');
		form.package_title.options[form.package_title.selectedIndex].value=package_array_num[0]+""+packagelistproduct+""+packagepriceproduct;
		form.package_title.options[form.package_title.selectedIndex].text=package_array_num[0];
		package_title_change('');
		return true;
	}
}

function FormSubmit() {
	form = document.form1;

	if(form.package_name.value.length<1) {
		alert("패키지 그룹명을 입력해 주세요.");
		form.package_name.focus();
		return;
	}

	if(form.package_title.options.length<1) {
		alert("패키지 그룹에 등록할 패키지를 하나 이상은 등록하셔야 됩니다.");
		form.package_title_name.focus();
		return;
	}

	form.package_title_list.value="";
	form.package_update_type.value="";
	form.package_price_list.value="";
	form.package_modify_list.value="";
	for(var i=0; i<form.package_title.length; i++) {
		package_array_num = "";
		package_array_num = form.package_title.options[i].value.split('');
		
		if (i==0) {
			form.package_title_list.value=package_array_num[0];
			if(package_array_num[1].length>0) {
				form.package_product_list.value=","+package_array_num[1];
			}
			form.package_price_list.value=package_array_num[2];
		} else {
			form.package_title_list.value+=""+package_array_num[0];
			if(package_array_num[1].length>0) {
				form.package_product_list.value+=","+package_array_num[1];
			} else {
				form.package_product_list.value+="";
			}
			form.package_price_list.value+=""+package_array_num[2];
		}
	}

	if(confirm("패키지 그룹 정보를 저장하겠습니까?")) {
		document.form1.mode.value = "update";
		form.submit();
	}
}
//-->
</script>
<div id="divName">
<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="1337" style="table-layout:fixed" bgcolor="#FFFFFF">
<tr>
	<td valign="top" width="100%" height="100%">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
	<input type="hidden" name="package_num" value="<?=((int)$package_num?$package_num:"")?>">
	<input type="hidden" name="package_update_type">
	<input type="hidden" name="package_title_list">
	<input type="hidden" name="package_product_list" value="">
	<input type="hidden" name="package_price_list" value="">
	<input type="hidden" name="package_modify_list" value="">
	<tr>
		<td width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">패키지 그룹 목록</div>
				</td>
			</tr>
		<tr>
			<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
				<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
				<TR>
					<TD width="100%">
                    
					<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
					<col width="250"></col>
					<col width=""></col>
					<col width="200"></col>
					<tr height="30">
						<td><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('num');">진열순</a> | <A HREF="javascript:GoSort('package_name');">패키지 그룹명</a></td>
						<td align=center>&nbsp;</td>
						<td align="right"><a href="javascript:DivScrollActive('1','divscroll0','List0TableId',1);"><span style="letter-spacing:-0.5pt;" class="font_orange"><b>전체펼침</b></span></a>&nbsp;&nbsp;<a href="javascript:DivScrollActive('0','divscroll0','List0TableId',1);"><b>펼침닫기</b></a></td>
					</tr>
					</table>
                  
					</TD>
				</TR>
				<TR>
					<TD width="100%">
					<DIV id="divscroll0" style="position:relative;width:100%;height:278px;bgcolor:#FFFFFF;overflow-x:hidden;overflow-y:auto;" class="table_style02">
					<TABLE id="List0TableId" border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=5;
?>
					<col width=50></col>
					<col width=></col>
					<col width=80></col>
					<col width=60></col>
					<col width=60></col>
					<TR align="center">
						<th>No</th>
						<th>패키지 그룹명</th>
						<th>패키지 필수</th>
						<th>수정</th>
						<th>삭제</th>
					</TR>
<?php
			$prcode_selected[((int)$package_num>0?$package_num:"")] = " bgcolor=\"#EFEFEF\"";

			$sql = "SELECT * FROM tblproductpackage ";
			if ($sort=="package_name")	$sql.= "ORDER BY package_name ";
			else						$sql.= "ORDER BY num DESC ";
			$result = pmysql_query($sql,get_db_conn());
			$t_count = @pmysql_num_rows($result);
			$cnt=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
				echo "<tr align=\"center\"{$prcode_selected[$row->num]} id=\"pidx_{$row->num}\" onmouseover=\"if(onMouseColor('{$row->num}'))this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(onMouseColor('{$row->num}'))this.style.backgroundColor='';\">\n";
				echo "	<TD>{$number}</td>\n";
				echo "	<TD><div class=\"ta_l\">{$row->package_name}</div></td>\n";
				echo "	<TD><b>".($row->package_type=="Y"?"<font color=\"#FF4C00\">Y</font>":"N")."</b></td>\n";
				echo "	<TD><img src=\"images/btn_edit.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"SelectListUpdate('{$row->num}')\"></td>";
				echo "	<TD><img src=\"images/btn_del.gif\" border=\"0\" style=\"cursor:hand;\" onclick=\"SelectListDelete('{$row->num}')\"></td>\n";
				echo "</tr>\n";
				$cnt++;
			}
			pmysql_free_result($result);
			if ($cnt==0) {
				echo "<tr><td colspan=\"{$colspan}\" align=\"center\">검색된 패키지 그룹이 존재하지 않습니다.</td></tr>";
			}
?>
				<TR>
						<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
					</TR>
					</table>
					</div>
					</TD>
				</TR>
				<tr>
					<td valign="top">
					<table border=0 cellpadding=0 cellspacing=0 width="100%" bgcolor=FFFFFF>
					<tr>
						<td colspan="3">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 id="packageidx">
						<tr>
							<td style="BORDER-top:#E3E3E3 1pt solid;"><img width="0" height="0"></td>
						</tr>
<?php
	if((int)$package_num>0) {
		$sql = "SELECT * FROM tblproductpackage ";
		$sql.= "WHERE num = '".(int)$package_num."' ";
		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		
		$package_title_exp=explode("",$row->package_title);
		$package_list_exp=explode("",$row->package_list);
		$package_price_exp=explode("",$row->package_price);

		for($i=1; $i<count($package_title_exp); $i++) {
			$package_title_str .= "<option value=\"{$package_title_exp[$i]}".substr($package_list_exp[$i],1)."{$package_price_exp[$i]}\">{$package_title_exp[$i]}</option>\n";
		}
	}
?>
						<tr>
							<td style="padding:5px;padding-left:0px;padding-right:0px;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td valign="top" bgcolor="#F8F8F8" style="border:2px #B9B9B9 solid;border-bottom:0px;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
									<td height="7"></td>
								</tr>
								<tr>
									<td align="center" height="30"><span class="font_orange"><b>패키지 그룹명</span> : </b><input type=text name="package_name" value="<?=$row->package_name?>" size="50" maxlength="50" class="input" onKeyDown="chkFieldMaxLen(50)"> <input type="checkbox" name="package_type" id="package_typeidx" value="Y"<?=($row->package_type=="Y"?" checked":"")?>><span class="font_orange"><b>필수</b></span></td>
								</tr>
								<tr>
									<td height="3"></td>
								</tr>
								<tr>
									<td style="padding-left:5px;padding-right:5px;padding-bottom:10px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td style="padding:5px;" valign="top" bgcolor="#F8F8F8" style="border:2px #B9B9B9 solid;border-top:0px;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
									<td valign="top" bgcolor="#FFF7F0" style="border:2px #FF7100 solid;">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<tr>
										<td height="7"></td>
									</tr>
									<tr>
										<td align="center" height="30"><b>패키지 타이틀 관리</b></td>
									</tr>
									<tr>
										<td height="3"></td>
									</tr>
									<tr>
										<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
									</tr>
									<tr>
										<td height="5"></td>
									</tr>
									<tr>
										<td align="center" style="padding-left:5px;padding-right:5px;">
										<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td width="100%" rowspan="2">
											<select name="package_title" size="10" style="width:100%;" onchange="package_title_change('Y');" ondblclick="package_title_modify();">
											<?=$package_title_str?>
											</select>
											</td>
											<td style="padding-left:5px;" valign="top" align="center">
											<table cellpadding="0" cellspacing="0">
											<TR>
												<TD align=middle style="padding-bottom:2px;"><A href="JavaScript:package_title_move('up');"><IMG src="images/code_up.gif" align=absMiddle border="0"></A></td>
											</tr>
											<TR>
												<TD align=middle><IMG src="images/code_sort.gif" align=absMiddle border="0"></td>
											</tr>
											<TR>
												<TD align=middle><A href="JavaScript:package_title_move('down');"><IMG src="images/code_down.gif" align=absMiddle border="0" vspace="2"></A></td>
											</tr>
											</table>
											</td>
										</tr>
										<tr>
											<td style="padding-left:5px;" valign="bottom">
											<table cellpadding="0" cellspacing="0" valign="bottom">
											<TR>
												<TD align=middle style="padding-bottom:2px;"><a href="javascript:package_title_modify();"><IMG src="images/btn_edit.gif" align=absMiddle border=0></a></td>
											</tr>
											<TR>
												<TD align=middle><a href="javascript:package_title_delete();"><IMG src="images/btn_del.gif" align=absMiddle border=0></a></td>
											</tr>
											</table>
											</td>
										</tr>
										<tr>
											<td height="5"></td>
										</tr>
										<tr>
											<td colspan="2"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
										</tr>
										<tr>
											<td height="5"></td>
										</tr>
										<tr>
											<td><input type="text" name="package_title_name" maxlength="100" value="" onKeyDown="chkFieldMaxLen(100)" class="input" style="width:100%;" id="package_title_nameidx"></td>
											<td style="padding-left:5px;"><a href="javascript:package_title_update();"><img src="images/btn_input.gif" id="btnidx" border="0" align="absmiddle" vspace="2"></a></td>
										</tr>
										</table>
										</td>
									</tr>
									<tr>
										<td height="10"></td>
									</tr>
									</table>
									</td>
								</tr>
								<tr>
									<td height="20"></td>
								</tr>
								<tr>
									<td height=514 valign="top">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height=100%>
									<tr>
										<td height=100% valign="top"><IFRAME name="PackageListFrame" src="product_package.list.detail.php" width=100% height=100% frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
									</tr>
									</table>
									</td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</TR>
					</table>
					</TD>
				</TR>
				
				<tr>
					<td align="center" style="padding:10px;"><span class="font_orange"><b>**** 패키지 그룹은 최종적으로 적용하기 버튼을 누르셔야만 적용됩니다. ****</b></span><br><a href="javascript:FormSubmit();"><img src="images/botteon_save.gif" border="0"></a></td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				</TABLE>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</form>
	</table>
	</td>
</tr>
<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode value="delete">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
<input type=hidden name=package_num>
</form>
<form name=packageForm action="product_package.list.detail.php" method=post target=PackageListFrame>
<input type=hidden name="package_title_code">
<input type=hidden name="package_title_list">
<input type=hidden name="package_price_list">
</form>
</table>
</div>
<?php
$script_echo = "<script>";
if($Scrolltype>0)
	$script_echo .= "DivScrollActive('1','divscroll0','List0TableId',0);";
else
	$script_echo .= "DivScrollActive('0','divscroll0','List0TableId',0);";

$script_echo .= "</script>";
?>
<?=$script_echo?>
<?=stripslashes($onload)?>
</body>
</html>
