<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$design=$_POST["design"];

//if($type=="update" && strlen($design)==3) {
if($type=="update") {
	$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='{$design}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$sql = "UPDATE tblshopinfo SET 
		frame_type	= '{$row->frame_type}', 
		top_type	= '{$row->top_type}', 
		menu_type	= '{$row->menu_type}', 
		main_type	= '{$row->main_type}', 
		title_type	= 'N', 
		icon_type	= '{$row->icon_type}' ";
		if(pmysql_query($sql,get_db_conn())) {
			DeleteCache("tblshopinfo.cache");
			$onload="<script>window.onload=function(){alert(\"메인화면 템플릿 설정이 완료되었습니다.\");}</script>";

			$_shopdata->frame_type=$row->frame_type;
			$_shopdata->top_type=$row->top_type;
			$_shopdata->menu_type=$row->menu_type;
			$_shopdata->main_type=$row->main_type;
			$_shopdata->title_type="N";
			$_shopdata->icon_type=$row->icon_type;
		}
	}
	pmysql_free_result($result);
}

if($_shopdata->top_type=="topp"
|| $_shopdata->menu_type=="menup"
|| $_shopdata->main_type=="mainm"
|| $_shopdata->main_type=="mainn"
|| $_shopdata->main_type=="mainp"
|| $_shopdata->title_type=="Y") {
	$_shopdata->icon_type="U";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?\n\n변경된 디자인은 쇼핑몰에 바로 적용됩니다.")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

var icon_type="<?=$_shopdata->icon_type?>";
function design_preview(design) {
	icon_type=design;
	document.all["preview_img"].src="images/sample/main"+design+".gif";
	document.all["Bimage"].src="images/sample/main"+design+"B.gif";
}

function ChangeDesign(tmp) {
	if(typeof(document.form1["design"][tmp])=="object") {
		document.form1["design"][tmp].checked=true;
		design_preview(document.form1["design"][tmp].value);
	} else {
		document.form1["design"].checked=true;
		design_preview(document.form1["design"].value);
	}
}

function changeMouseOver(img) {
	 img.style.border='1 dotted #999999';
}
function changeMouseOut(img,dot) {
	 img.style.border="1 "+dot;
}

function preview_over(obj) {
	try {
		if(icon_type!="U") {
			document.all[obj].style.display = "block";
		}
	} catch(e) {}
}

function preview_out(obj) {
	try {
		if(icon_type!="U") {
			document.all[obj].style.display = "none";
		}
	} catch(e) {}
}

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 템플릿-메인 및 카테고리 &gt;<span>메인화면 템플릿</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">메인화면 템플릿</div>
                </td>
            </tr>
			<tr>
				<td>
					<div class="title_depth3_sub"><span>쇼핑몰 메인화면 디자인을 선택하여 사용하실 수 있습니다.</span></div>
				</td>
			</tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">현재 운영중인 쇼핑몰 메인 템플릿</div>
				</td>
			</tr>
			<tr>
				<td>
					<p align="center">
						<img id="preview_img" src="images/sample/main<?=$_shopdata->icon_type?>.gif" border=0 style="cursor:hand;" onmouseover="preview_over('Bdiv')" onmouseout="preview_out('Bdiv')" style="border-width:3pt; border-color:rgb(222,222,222); border-style:solid;" class="imgline">
						<div id="Bdiv" style="position:absolute; z-index:3;width:100%;display:none;">
							<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 align = 'center'><tr><td><img name=Bimage src="images/sample/main<?=$_shopdata->icon_type?>B.gif"></td></tr></table>
						</div>
					</p>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 메인 템플릿 선택하기<span>메인템플릿에 따라 서브페이지 전체 디자인 컨셉이 같이 변경됩니다.</span></div>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" >
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%"><div class="point_title">템플릿 선택하기</div></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:10pt;" bgcolor="#f8f8f8">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%">
								<table cellpadding="0" cellspacing="0" width="100%">
<?php
		$sql = "SELECT * FROM tbltempletinfo ORDER BY icon_type DESC ";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			if($i==0) echo "<tr>\n";
			if($i>0 && $i%3==0) echo "</tr>\n<tr>\n";
			if($i%3==0) {
				echo "<td width=\"246\"><p align=\"center\">";
			} else {
				echo "<td width=\"246\"><p align=\"center\">";
			}
			echo "<img src=\"images/sample/main{$row->icon_type}.gif\" border=0 style='border:1 dotted #FFFFFF' onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign({$i});' class=\"imgline1\">";
			echo "<br><input type=radio id=\"idx_design{$i}\" name=design value=\"{$row->icon_type}\" ";
			if($_shopdata->icon_type==$row->icon_type) echo "checked";
			echo " onclick=\"design_preview('{$row->icon_type}')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
			echo "</td>\n";
			$i++;
		}
		pmysql_free_result($result);
		if($i%3!=0) {
			for($j=$i;$j<3;$j++) echo "<td width=\"246\"><p align=\"center\">&nbsp;</td>\n";
			echo "</tr>\n";
		}
?>
								</table>
								</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>템플릿 선택하기</span></dt>
						<dd>
							- 원하시는 메인화면 템플릿을 선택하십시요. 선택하지 않으면 기본값으로 설정됩니다.<br>
							- 템플릿 사용을 원치 않으셔서 자체 디자인을 하실 경우에는 "개별디자인-메인 및 상하단"에서 디자인 하신 후 옵션설정에서 디자인 설정을 하시면 됩니다.<br>
							- 해당 이미지를 선택하신 후 "적용하기" 버튼을 누르셔야 변경이 됩니다.<br>
							- 왼쪽 이미지에 마우스를 올리시면 큰 이미지를 보실 수 있습니다.
						</dd>
					</dl>
					<dl>
						<dt><span>	메인본문 레이아웃 변경 및 우측의 메뉴를 보이지 않게 하기</span></dt>
						<dd>
							- <a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</a> 에서 매크로명령어를 이용하여 레이아웃을 자유롭게 변경 가능합니다.<br>
						</dd>
					</dl>
					<dl>
						<dt><span>	개별 디자인</span></dt>
						<dd>
							- <a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');">디자인관리 > 개별디자인- 메인 및 상하단</a> 에서 상단 와 좌측, 본문, 하단등의 개별 디자인을 할 수 있습니다.<br>
							- 개별 디자인 사용시 템플릿은 적용되지 않습니다.
						</dd>
					</dl>
				</div>

				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
