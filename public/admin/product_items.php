<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

extract($_REQUEST);

$sql = "SELECT etctype FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$etctype= "";
$branduse="";
$brandleft="";
$brandlefty="";
$brandleftl="";
$brandpro="";
$brandmap="";
$brandmapt="";
if($row=pmysql_fetch_object($result)) {
	if (ord($row->etctype)) {
		$etctemp = @explode("",$row->etctype);
		
		for($i=0; $i<count($etctemp); $i++) {
			if (ord($etctemp[$i])) {
				if(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDUSE=") {
					$branduse=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][10]) && substr($etctemp[$i],0,10) == "BRANDLEFT=") {
					$brandleft=$etctemp[$i][10];
				} elseif(ord(substr($etctemp[$i],11,3)) && substr($etctemp[$i],0,11) == "BRANDLEFTY=") {
					$brandlefty=substr($etctemp[$i],11,3);
				} elseif(ord($etctemp[$i][11]) && substr($etctemp[$i],0,11) == "BRANDLEFTL=") {
					$brandleftl=$etctemp[$i][11];
				} elseif(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDPRO=") {
					$brandpro=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][9]) && substr($etctemp[$i],0,9) == "BRANDMAP=") {
					$brandmap=$etctemp[$i][9];
				} elseif(ord($etctemp[$i][10]) && substr($etctemp[$i],0,10) == "BRANDMAPT=") {
					$brandmapt=$etctemp[$i][10];
				} else {
					$etctempvalue[] = $etctemp[$i];
				}
			} else {
				$etctempvalue[] = "";
			}
		}

		$etctype = @implode("",$etctempvalue);
	}
}
pmysql_free_result($result);

$type=$_POST["type"];
$up_branduse=$_POST["up_branduse"];
$up_brandleft=$_POST["up_brandleft"];
$up_brandlefty=(int)$_POST["up_brandlefty"];
$up_brandleftl=$_POST["up_brandleftl"];
$up_brandpro=$_POST["up_brandpro"];
$up_brandmap=$_POST["up_brandmap"];
$up_brandmapt=$_POST["up_brandmapt"];

if($type=="up") {
	$branduse="N";
	$brandleft="N";
	$brandlefty="";
	$brandleftl="N";
	$brandpro="N";
	$brandmap="N";
	$brandmapt="N";
	if(ord($up_branduse) && $up_branduse=="Y") { 
		$etctype.="BRANDUSE=Y";
		$branduse="Y";
		if(ord($up_brandleft) && $up_brandleft=="Y") {
			$etctype.="BRANDLEFT=Y";
			$brandleft="Y";

			if($up_brandlefty>0) {
				$etctype.="BRANDLEFTY={$up_brandlefty}";
				$brandlefty=$up_brandlefty;
			}
			if(ord($up_brandleftl) && ($up_brandleftl=="Y" || $up_brandleftl=="B" || $up_brandleftl=="A")) {
				$etctype.="BRANDLEFTL={$up_brandleftl}";
				$brandleftl=$up_brandleftl;
			}
		}
		if(ord($up_brandpro) && $up_brandpro=="Y") {
			$etctype.="BRANDPRO=Y";
			$brandpro="Y";
		}
		if(ord($up_brandmap) && $up_brandmap=="Y") {
			$etctype.="BRANDMAP=Y";
			$brandmap="Y";

			if(ord($up_brandmapt) && $up_brandmapt=="Y") {
				$etctype.="BRANDMAPT=Y";
				$brandmapt="Y";
			}
		}
	}

	$sql="UPDATE tblshopinfo SET etctype='{$etctype}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('아이템 관련 페이지 설정이 완료되었습니다.');}</script>";
} else if($type=="save") {
	if($edittype == "insert") {
		if(ord($up_brandname)) {
			$sql = "INSERT INTO tblproductitem(itemname) VALUES ('{$up_brandname}')";
			if(pmysql_query($sql,get_db_conn())) {
				$onload="<script>window.onload=function(){ alert('아이템 등록이 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductitem.cache");
			} else {
				alert_go('동일명이 존재합니다. 다른 아이템명을 입력해 주세요.',-1);
			}
		} else {
			alert_go('추가할 아이템명을 입력해 주세요.',-1);
		}
	} else if($edittype == "update") {
		if(ord($up_brandname) && (int)$up_brandlist>0) {
			$sql = "UPDATE tblproductitem SET ";
			$sql.= "itemname	= '{$up_brandname}' ";
			$sql.= "WHERE itidx = '{$up_brandlist}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$onload="<script>window.onload=function(){ alert('브랜드 수정이 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductitem.cache");
			} else {
				alert_go('동일명이 존재합니다. 다른 아이템명을 입력해 주세요.',-1);
			}
		} else if((int)$up_brandlist<1) {
			alert_go('수정할 아이템을 선택해 주세요.',-1);
		} else {
			alert_go('추가할 아이템명을 입력해 주세요.',-1);
		}
	} else if($edittype == "delete") {
		if((int)$up_brandlist>0) {
			$sql = "DELETE FROM tblproductitem ";
			$sql.= "WHERE itidx = '{$up_brandlist}' ";
			if(pmysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblproduct ";
				$sql.= "SET brand = null ";
				$sql.= "WHERE brand = '{$up_brandlist}' ";
				pmysql_query($sql,get_db_conn());
				$onload="<script>window.onload=function(){ alert('브랜드 삭제가 정상 완료되었습니다.');}</script>";
				DeleteCache("tblproductitem.cache");
			}
		} else {
			alert_go('삭제할 아이템을 선택해 주세요.',-1);
		}
	}
}

if($branduse != "Y") {
	$branddisabled="disabled";
	$brandleftdisabled="disabled";
	$brandmapdisabled="disabled";
} else if($brandleft != "Y") {
	$brandleftdisabled="disabled";
} else if($brandmap != "Y") {
	$brandmapdisabled="disabled";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(typeval) {
	form = document.form1;
	var submit_val = "";
	
	if(typeval == "up") {
		var brandleftyval = document.form1.up_brandlefty.value;
		if(document.form1.up_brandlefty.disabled == false && (!brandleftyval || isNaN(brandleftyval) || parseInt(brandleftyval)<1 || brandleftyval != parseInt(brandleftyval))) {
			alert('아이템 목록 높이는 0보다 큰 숫자를 입력해 주세요.');
			form.up_brandname.focus();
			submit_val = "no";
		} else if(confirm("브랜드 페이지 설정을 적용하겠습니까?")){
			submit_val = "ok";
		} else {
			submit_val = "no";
		}
	} else if(typeval == "save") {
		if(form.edittype.value == "update" || form.edittype.value == "insert") {
			if(!form.up_brandname.value) {
				alert('아이템 명을 입력해 주세요.');
				form.up_brandname.focus();
				submit_val = "no";
			}

			for(var i=0; i<form.up_brandlist.options.length; i++) {
				if(form.up_brandname.value == form.up_brandlist.options[i].text) {
					alert('현재 동일 아이템 명이 존재합니다. 다른 아이템명을 입력 해 주세요.');
					form.up_brandname.focus();
					submit_val = "no";
					break;
				}
			}
		}
		
		if(!submit_val) {
			if(form.edittype.value == "update" && confirm("해당 아이템이 입력된 상품의 아이템도 같이 변경됩니다.\n\n선택된 아이템명을 정말 변경하겠습니까?")) {
				submit_val = "ok";
			} else if(form.edittype.value == "insert" && confirm("신규로 아이템을 추가하겠습니까?")) {
				submit_val = "ok";
			} else if(form.edittype.value == "delete") {
				if(confirm("해당 아이템이 입력된 상품의 아이템도 같이 삭제됩니다.\n\n선택된 아이템을 정말 삭제하겠습니까?")) {
					submit_val = "ok";
				} else {
					edittype_select("insert");
				}
			}
		}
	}
	
	if(submit_val == "ok") {
		form.type.value=typeval;
		form.submit();
	}
}

function brandleft_change(form) {
	if(form.up_branduse[0].checked == true && form.up_brandleft[0].checked == true) {
		form.up_brandlefty.disabled = false;
		form.up_brandleftl[0].disabled = false;
		form.up_brandleftl[1].disabled = false;
		form.up_brandleftl[2].disabled = false;
	} else {
		form.up_brandlefty.disabled = true;
		form.up_brandleftl[0].disabled = true;
		form.up_brandleftl[1].disabled = true;
		form.up_brandleftl[2].disabled = true;
	}
}

function brandmap_change(form) {
	if(form.up_branduse[0].checked == true && form.up_brandmap[0].checked == true) {
		form.up_brandmapt[0].disabled = false;
		form.up_brandmapt[1].disabled = false;
	} else {
		form.up_brandmapt[0].disabled = true;
		form.up_brandmapt[1].disabled = true;
	}
}

function branduse_change(form) {
	if(form.up_branduse[0].checked == true) {
		form.up_brandleft[0].disabled = false;
		form.up_brandpro[0].disabled = false;
		form.up_brandmap[0].disabled = false;
		form.up_brandleft[1].disabled = false;
		form.up_brandpro[1].disabled = false;
		form.up_brandmap[1].disabled = false;
	} else {
		form.up_brandleft[0].disabled = true;
		form.up_brandpro[0].disabled = true;
		form.up_brandmap[0].disabled = true;
		form.up_brandleft[1].disabled = true;
		form.up_brandpro[1].disabled = true;
		form.up_brandmap[1].disabled = true;
	}
	brandleft_change(form);
	brandmap_change(form);
}

function edittype_select(edittypeval) {
	form = document.form1;
	if((edittypeval == "update" || edittypeval == "delete") && form.up_brandlist.selectedIndex<0) {
		alert('변경할 아이템을 선택해 주세요.');
		form.up_brandlist.focus();
	} else {
	
		form.edittype.value="";

		if(edittypeval == "update") {
			document.getElementById("update").style.backgroundColor = "#FF4C00";
			document.getElementById("insert").style.backgroundColor = "#FFFFFF";
			document.getElementById("delete").style.backgroundColor = "#FFFFFF";
			form.edittype.value = "update";
			form.up_brandname.value = form.up_brandlist.options[form.up_brandlist.selectedIndex].text;
		} else if(edittypeval == "insert") {
			document.getElementById("update").style.backgroundColor = "#FFFFFF";
			document.getElementById("insert").style.backgroundColor = "#FF4C00";
			document.getElementById("delete").style.backgroundColor = "#FFFFFF";
			form.edittype.value = "insert";
			form.up_brandname.value = "";
		} else if(edittypeval == "delete") {
			document.getElementById("update").style.backgroundColor = "#FFFFFF";
			document.getElementById("insert").style.backgroundColor = "#FFFFFF";
			document.getElementById("delete").style.backgroundColor = "#FF4C00";
			form.edittype.value = "delete";
			CheckForm('save');
		}
	}
}

function brandlist_change() {
	form = document.form1;
	if(form.edittype.value == "update") {
		form.up_brandname.value = form.up_brandlist.options[form.up_brandlist.selectedIndex].text;
	}
}

function defaultreset() {
	branduse_change(document.form1);
	if(document.form1.edittype.value == "update") {
		edittype_select("update");
	} else {
		edittype_select("insert");
	}
}

function SearchSubmit(seachIdxval) {
	form = document.form1;
	form.type.value="";
	form.edittype.value="";
	form.seachIdx.value = seachIdxval;
	form.submit();
}
</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
<body onLoad="defaultreset();">
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>상품 아이템 관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>

	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 아이템 설정 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>아이템 추가,수정,삭제가 가능하며 브랜드 관련 페이지의 출력 설정을 할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 아이템 관련 설정</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=edittype value="insert">
			<input type=hidden name=seachIdx value="<?=$seachIdx?>">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td>
						<TABLE cellSpacing=0 cellPadding="0" width="100%" border=0>
                        <TR>
							<TD><div class="point_title"><input type=radio id="idx_branduse1" name=up_branduse value="Y"<?=($branduse=="Y"?" checked":"")?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_branduse1><b>아이템 페이지 사용함</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio id="idx_branduse2" name=up_branduse value="N"<?=($branduse!="Y"?" checked":"")?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_branduse2><b>아이템 페이지 사용안함</b></label></div></TD>
						</TR>
						<TR>
							<TD>
                            <div class="table_style01">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<th><span>좌측메뉴 아이템 목록</span></th>
								<TD class="td_con1">
                                <div class="table_none">
                                <table width="100%">
                                <tr>
                                    <td width="50%"><input type=radio id="idx_brandleft1" name=up_brandleft value="Y"<?=($brandleft=="Y"?" checked":"")?> <?=$branddisabled?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandleft1>좌측메뉴 아이템 목록 사용함</label></td>
                                    <td><input type=radio id="idx_brandleft2" name=up_brandleft value="N"<?=($brandleft!="Y"?" checked":"")?> <?=$branddisabled?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandleft2>좌측메뉴 아이템 목록 사용안함</label></td>
                                </tr>
                                <tr>
                                    <td colspan=2> 
                                        <TABLE width="100%"cellSpacing=0 cellPadding=0 border=0 style="border:1px #EDEDED solid;">
                                        <col width="140"></col>
                                        <col></col>
                                        <tr>
                                            <TD bgcolor="#F8F8F8" style="padding-left:5px;padding-right:5px;">아이템 목록 높이</TD>
                                            <td style="padding-left:5px;padding-right:5px;border-left:1px #EDEDED solid;">&nbsp;<input type=text name="up_brandlefty" value="<?=$brandlefty?>" size="3" maxlength="3" style="width:40;" class="input" <?=$brandleftdisabled?>> 픽셀 <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">※ 0보다 큰 숫자만 가능합니다.</span></td>
                                        </tr>
                                        <TR>
                                            <TD colspan="2" bgcolor="#EDEDED" height="1"></TD>
                                        </TR>
                                        <tr>
                                            <TD bgcolor="#F8F8F8" style="padding-left:5px;padding-right:5px;">아이템 목록 출력</TD>
                                            <td style="padding-left:5px;padding-right:5px;border-left:1px #EDEDED solid;">
                                            <input type=radio name="up_brandleftl" id="idx_brandleftl1" value="Y"<?=($brandleftl=="Y"?" checked":"")?> <?=$brandleftdisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_brandleftl1">메인 페이지 + 아이템 전용페이지에서 출력</label><br>
                                            <input type=radio name="up_brandleftl" id="idx_brandleftl2" value="B"<?=($brandleftl=="B"?" checked":"")?> <?=$brandleftdisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_brandleftl2">아이템 전용페이지에서만 출력</label><br>
                                            <input type=radio name="up_brandleftl" id="idx_brandleftl3" value="A"<?=($brandleftl=="A"?" checked":"")?> <?=$brandleftdisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_brandleftl3">모든 페이지에서 출력</label>
                                            </td>
                                        </tr>
                                        </table>
                                        <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">※ 아이템 전용페이지란 '아이템 상품 목록 페이지' 와 '아이템맵 페이지' 입니다.</span></td>
                                </tr>
                                </table>
                                </div>
   		                        </TD>								
							</TR>
							<TR>
								<th><span>아이템 상품 목록 페이지</span></th>
								<TD class="td_con1">
                                <div class="table_none">
                                <table width="100%">
                                <tr>
                                	<td width="50%"><input type=radio id="idx_brandpro1" name=up_brandpro value="Y"<?=($brandpro=="Y"?" checked":"")?> <?=$branddisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandpro1>아이템 상품 목록 페이지 사용함</label>
                                    </td>
                              		<td><input type=radio id="idx_brandpro2" name=up_brandpro value="N"<?=($brandpro!="Y"?" checked":"")?> <?=$branddisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandpro2>아이템 상품 목록 페이지 사용안함</label>
                                    </td>
                                </tr>
                                </table>
                                </div>
                                </TD>
							</TR>
							<TR>
								<th><span>아이템 맵 페이지</span></th>
								<TD class="td_con1">
                                <div class="table_none">
                                <table width="100%">
                                <tr>
                                	<td width="50%"><input type=radio id="idx_brandmap1" name=up_brandmap value="Y"<?=($brandmap=="Y"?" checked":"")?> <?=$branddisabled?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandmap1>아이템 맵 페이지 사용함</label>
                                    </td>
                                	<td><input type=radio id="idx_brandmap2" name=up_brandmap value="N"<?=($brandmap!="Y"?" checked":"")?> <?=$branddisabled?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandmap2>아이템 맵 페이지 사용안함</label>
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="2">
                                    <TABLE width="100%"cellSpacing=0 cellPadding=0 border=0 style="border:1px #EDEDED solid;">
                                    <col width="140"></col>
                                    <col></col>
                                    <tr>
                                        <TD bgcolor="#F8F8F8" style="padding-left:5px;padding-right:5px;">아이템맵 우선 정렬 선택</TD>
                                        <td style="padding-left:5px;padding-right:5px;border-left:1px #EDEDED solid;"><input type=radio name="up_brandmapt" id="idx_brandmapa" value="N"<?=($brandmapt!="Y"?" checked":"")?> <?=$brandmapdisabled?> onClick="branduse_change(this.form);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandmapa>영어우선</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type=radio name="up_brandmapt" id="idx_brandmapb" value="Y"<?=($brandmapt=="Y"?" checked":"")?> <?=$brandmapdisabled?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_brandmapb>한글우선</label></td>
                                    </tr>
                                    </table>
                                    </td>
                                </tr>
                                </table>
                                </div>
                                </TD>
							</TR>
							</TABLE>
                            </div>
							</TD>
						</TR>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif"  border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 아이템 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
					<tr>
						<td>
                       	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
							<col width="200"></col>
							<col></col>
							<tr>
								<th><span>상품 아이템 목록</span></th>
								<td class="td_con1">
								<div class="table_none">
                                <table border=0 cellpadding=0 cellspacing=0 width="100%">
								<tr>
									<td style="padding:5px;padding-left:2px;padding-right:2px;letter-spacing:1.5pt;"><b><a href="javascript:SearchSubmit('A');"><span id="A">A</span></a> 
									<a href="javascript:SearchSubmit('B');"><span id="B">B</span></a> 
									<a href="javascript:SearchSubmit('C');"><span id="C">C</span></a> 
									<a href="javascript:SearchSubmit('D');"><span id="D">D</span></a> 
									<a href="javascript:SearchSubmit('E');"><span id="E">E</span></a> 
									<a href="javascript:SearchSubmit('F');"><span id="F">F</span></a> 
									<a href="javascript:SearchSubmit('G');"><span id="G">G</span></a> 
									<a href="javascript:SearchSubmit('H');"><span id="H">H</span></a> 
									<a href="javascript:SearchSubmit('I');"><span id="I">I</span></a> 
									<a href="javascript:SearchSubmit('J');"><span id="J">J</span></a> 
									<a href="javascript:SearchSubmit('K');"><span id="K">K</span></a> 
									<a href="javascript:SearchSubmit('L');"><span id="L">L</span></a> 
									<a href="javascript:SearchSubmit('M');"><span id="M">M</span></a> 
									<a href="javascript:SearchSubmit('N');"><span id="N">N</span></a> 
									<a href="javascript:SearchSubmit('O');"><span id="O">O</span></a> 
									<a href="javascript:SearchSubmit('P');"><span id="P">P</span></a> 
									<a href="javascript:SearchSubmit('Q');"><span id="Q">Q</span></a> 
									<a href="javascript:SearchSubmit('R');"><span id="R">R</span></a> 
									<a href="javascript:SearchSubmit('S');"><span id="S">S</span></a> 
									<a href="javascript:SearchSubmit('T');"><span id="T">T</span></a> 
									<a href="javascript:SearchSubmit('U');"><span id="U">U</span></a> 
									<a href="javascript:SearchSubmit('V');"><span id="V">V</span></a> 
									<a href="javascript:SearchSubmit('W');"><span id="W">W</span></a> 
									<a href="javascript:SearchSubmit('X');"><span id="X">X</span></a> 
									<a href="javascript:SearchSubmit('Y');"><span id="Y">Y</span></a> 
									<a href="javascript:SearchSubmit('Z');"><span id="Z">Z</span></a></b></td>
									<td width="50" align="center" nowrap><b><a href="javascript:SearchSubmit('전체');"><span id="전체">전체</span></a></b></td>
								</tr>
								<tr>
									<td>
									<select name="up_brandlist" size="20" style="width:100%;" onChange="brandlist_change();">
<?php
$sql = "SELECT * FROM tblproductitem ";

if(preg_match("/^[A-Z]/", $seachIdx)) {
	$sql.= "WHERE itemname LIKE '{$seachIdx}%' OR itemname LIKE '".strtolower($seachIdx)."%' ";	
	$sql.= "ORDER BY itemname ";
} else if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
	if($seachIdx == "ㄱ") $sql.= "WHERE (itemname >= 'ㄱ' AND itemname < 'ㄴ') OR (itemname >= '가' AND itemname < '나') ";
	if($seachIdx == "ㄴ") $sql.= "WHERE (itemname >= 'ㄴ' AND itemname < 'ㄷ') OR (itemname >= '나' AND itemname < '다') ";
	if($seachIdx == "ㄷ") $sql.= "WHERE (itemname >= 'ㄷ' AND itemname < 'ㄹ') OR (itemname >= '다' AND itemname < '라') ";
	if($seachIdx == "ㄹ") $sql.= "WHERE (itemname >= 'ㄹ' AND itemname < 'ㅁ') OR (itemname >= '라' AND itemname < '마') ";
	if($seachIdx == "ㅁ") $sql.= "WHERE (itemname >= 'ㅁ' AND itemname < 'ㅂ') OR (itemname >= '마' AND itemname < '바') ";
	if($seachIdx == "ㅂ") $sql.= "WHERE (itemname >= 'ㅂ' AND itemname < 'ㅅ') OR (itemname >= '바' AND itemname < '사') ";
	if($seachIdx == "ㅅ") $sql.= "WHERE (itemname >= 'ㅅ' AND itemname < 'ㅇ') OR (itemname >= '사' AND itemname < '아') ";
	if($seachIdx == "ㅇ") $sql.= "WHERE (itemname >= 'ㅇ' AND itemname < 'ㅈ') OR (itemname >= '아' AND itemname < '자') ";
	if($seachIdx == "ㅈ") $sql.= "WHERE (itemname >= 'ㅈ' AND itemname < 'ㅊ') OR (itemname >= '자' AND itemname < '차') ";
	if($seachIdx == "ㅊ") $sql.= "WHERE (itemname >= 'ㅊ' AND itemname < 'ㅋ') OR (itemname >= '차' AND itemname < '카') ";
	if($seachIdx == "ㅋ") $sql.= "WHERE (itemname >= 'ㅋ' AND itemname < 'ㅌ') OR (itemname >= '카' AND itemname < '타') ";
	if($seachIdx == "ㅌ") $sql.= "WHERE (itemname >= 'ㅌ' AND itemname < 'ㅍ') OR (itemname >= '타' AND itemname < '파') ";
	if($seachIdx == "ㅍ") $sql.= "WHERE (itemname >= 'ㅍ' AND itemname < 'ㅎ') OR (itemname >= '파' AND itemname < '하') ";
	if($seachIdx == "ㅎ") $sql.= "WHERE (itemname >= 'ㅎ' AND itemname < 'ㅏ') OR (itemname >= '하' AND itemname < '') ";
	$sql.= "ORDER BY itemname ";
} else if($seachIdx == "기타") {
	$sql.= "WHERE (itemname < 'ㄱ' OR itemname >= 'ㅏ') AND (itemname < '가' OR itemname >= '') AND (itemname < 'a' OR itemname >= '{') AND (itemname < 'A' OR itemname >= '[') ";
	$sql.= "ORDER BY itemname ";
} else {
	$sql.= "ORDER BY itemname ";
}

$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	echo "<option value=\"{$row->itidx}\">{$row->itemname}</option>";
}
?>
									</select></td>
									<td width="50" align="center" nowrap style="line-height:21px;" valign="top"><b><a href="javascript:SearchSubmit('ㄱ');"><span id="ㄱ">ㄱ</span></a><br>
									<a href="javascript:SearchSubmit('ㄴ');"><span id="ㄴ">ㄴ</span></a><br>
									<a href="javascript:SearchSubmit('ㄷ');"><span id="ㄷ">ㄷ</span></a><br>
									<a href="javascript:SearchSubmit('ㄹ');"><span id="ㄹ">ㄹ</span></a><br>
									<a href="javascript:SearchSubmit('ㅁ');"><span id="ㅁ">ㅁ</span></a><br>
									<a href="javascript:SearchSubmit('ㅂ');"><span id="ㅂ">ㅂ</span></a><br>
									<a href="javascript:SearchSubmit('ㅅ');"><span id="ㅅ">ㅅ</span></a><br>
									<a href="javascript:SearchSubmit('ㅇ');"><span id="ㅇ">ㅇ</span></a><br>
									<a href="javascript:SearchSubmit('ㅈ');"><span id="ㅈ">ㅈ</span></a><br>
									<a href="javascript:SearchSubmit('ㅊ');"><span id="ㅊ">ㅊ</span></a><br>
									<a href="javascript:SearchSubmit('ㅋ');"><span id="ㅋ">ㅋ</span></a><br>
									<a href="javascript:SearchSubmit('ㅌ');"><span id="ㅌ">ㅌ</span></a><br>
									<a href="javascript:SearchSubmit('ㅍ');"><span id="ㅍ">ㅍ</span></a><br>
									<a href="javascript:SearchSubmit('ㅎ');"><span id="ㅎ">ㅎ</span></a><br>
									<a href="javascript:SearchSubmit('기타');"><span id="기타">기타</span></a></b></td>
								</tr>
								</table>
                                </div>
								</td>
							</tr>
							<tr>
								<th><span>편집 모드 선택</span></th>
								<td class="td_con1" align="center">
                                <div class="table_none">
								<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
								<tr>
									<td id="insert" style="background-color:#FF4C00;padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_add2.gif" border="0" style="cursor:hand;" onClick="edittype_select('insert');"></div></td>
									<td style="padding-left:20px;padding-right:20px;">
									<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
									<tr>
										<td id="update" style="padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_edit.gif" border="0" style="cursor:hand;" onClick="edittype_select('update');"></div></td>
									</tr>
									</table>
									</td>
									<td id="delete" style="padding:5px;"><div style="padding:5px;background-color:'#FFFFFF';"><img src="images/btn_del.gif" border="0" style="cursor:hand;" onClick="edittype_select('delete');"></div></td>
								</tr>
								</table>
                                </div>
								</td>
							</tr>
							<tr>
								<th><span>상품 아이템명</span></th>
								<td style="padding:5px;" class="td_con1"><input type=text name="up_brandname" value="" size="50" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:CheckForm('save');"><img src="images/btn_save.gif" border="0" hspace="5" align="absmiddle"></a></td>
							</tr>
							</table>
                        </div>
						</td>
					</tr>
				</form>
				<tr><td height=20></td></tr>
				<tr>
					<td colspan="2">
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>아이템 페이지 설정</span></dt>
							<dd>- 아이템 페이지 사용함으로 설정시에만 각각의 세부 페이지 사용여부를 설정할 수 있습니다.</dd>
	
						</dl>
						<dl>
							<dt><span>상품 아이템 관리</span></dt>
							<dd>
							- 편집 모드에 따라 브랜드를 등록/수정/삭제가 가능합니다.<br>
							- <font color="#FF4C00">편집 모드에 따라 변경된 내용은 해당 브랜드가 입력된 상품에도 동일하게 적용됩니다.</font><br>
							- 등록된 브랜드는 상품등록/수정시 브랜드를 선택할 수 있습니다.<br>
							- 상품등록/수정시 직접입력한 브랜드는 아이템 목록에 자동 등록됩니다.
							</dd>

						</dl>

									
					</div>
					</td>
				</tr>
				<tr>
					<td height="50" colspan="2"></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script language="javascript">
<!--
<?php
	if(ord($seachIdx)) {
		echo "document.getElementById(\"$seachIdx\").style.color=\"#FF4C00\";";
	} else {
		echo "document.getElementById(\"전체\").style.color=\"#FF4C00\";";
	}
?>
//-->
</script>
<?=$onload?>
<?php 
include("copyright.php");
