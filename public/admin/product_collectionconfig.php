<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-3";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_collyes=$_POST["up_collyes"];
$up_coll_num=$_POST["up_coll_num"];
$up_coll_loc=$_POST["up_coll_loc"];

if ($type=="modify") {
	if($up_collyes!="Y") $up_coll_loc=0;
	$sql = "UPDATE tblshopinfo SET coll_loc = '{$up_coll_loc}', coll_num = '{$up_coll_num}' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('관련상품 진열관리 설정이 완료되었습니다.');} </script>";
}

$sql = "SELECT coll_loc,coll_num FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$coll_loc = $row->coll_loc;
	$coll_num = $row->coll_num;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_collyes[0].checked) {
		if(form.up_coll_loc[0].checked!=true && form.up_coll_loc[1].checked!=true && form.up_coll_loc[2].checked!=true) {
			alert("관련상품 위치설정을 하세요.");
			return;
		}
	}
	document.form1.type.value="modify"
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 관련상품 관리 &gt;<span>관련상품 진열방식 설정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">관련상품 진열방식 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 상세페이지에서 관련상품의 진열여부 및 진열상품수, 진열위치를 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<th><span>관련상품 진열여부</span></th>
                    <td class="td_con1"><input type=radio id="idx_up_collyes1" name=up_collyes value="Y" <?=$coll_loc>0?"checked":""?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_collyes1>관련상품 진열함</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_up_collyes2" name=up_collyes value="N" <?=$coll_loc>0?"":"checked"?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_collyes2>관련상품 진열안함</label></span></td>
                </tr>
                <tr>
                	<th><span>진열 상품수 설정</span></th>
                    <td class="td_con1"><input type=radio id="idx_up_coll_num1" name=up_coll_num value="4" <?php if ($coll_num==4) echo "checked"; ?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_num1>4개</label>&nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_up_coll_num2" name=up_coll_num value="5" <?php if ($coll_num==5) echo "checked"; ?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_num2>5개</label>&nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_up_coll_num3" name=up_coll_num value="6" <?php if ($coll_num==6) echo "checked"; ?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_num3>6개</label>&nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_up_coll_num4" name=up_coll_num value="7" <?php if ($coll_num==7) echo "checked"; ?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_num4>7개</label>&nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_up_coll_num5" name=up_coll_num value="8" <?php if ($coll_num==8) echo "checked"; ?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_num5>8개</label></td>
				</tr>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">관련상품 위치설정</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_up_coll_loc1" name=up_coll_loc value=1 <?php if($coll_loc==1) echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_loc1>상품상세정보 상단</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_up_coll_loc2" name=up_coll_loc value=2 <?php if($coll_loc==2) echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_loc2>상품상세정보 하단</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_up_coll_loc3" name=up_coll_loc value=3 <?php if($coll_loc==3) echo "checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_up_coll_loc3>상품상세정보 오른쪽</label></TD>
				</TR>
				<TR>
					<TD class="td_con1" align="center"><img src="images/collectionconfig_img1.gif" border="0" class="imgline"></TD>
					<TD class="td_con1" align="center"><img src="images/collectionconfig_img2.gif" border="0" class="imgline"></TD>
					<TD class="td_con1" align="center"><img src="images/collectionconfig_img3.gif" border="0" class="imgline"></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>관련상품 진열방식 설정</span></dt>
							<dd>
							- 관련상품 출력은 [관련상품 진열여부]에서 "관련상품 진열함"을 선택해야만 출력됩니다.<br>
							- 상품상세페이지 본문 가로사이즈에 맞게 관련상품 출력갯수를 조절하시기 바랍니다.<br>
							- 관련상품 관련상품 위치설정에 따라서 상품상세페이지의 상품상세정보 상단/하단/오른쪽에 출력됩니다.
							</dd>
	
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<?=$onload?>
<?php 
include("copyright.php");
