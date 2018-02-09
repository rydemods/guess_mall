<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-2";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$multi_distype=$_POST["multi_distype"];
$multi_dispos=$_POST["multi_dispos"];
$multi_changetype=$_POST["multi_changetype"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "multi_distype		= '{$multi_distype}', ";
	$sql.= "multi_dispos		= '{$multi_dispos}', ";
	$sql.= "multi_changetype	= '{$multi_changetype}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('설정이 완료되었습니다.');}</script>";

	$_shopdata->multi_distype=$multi_distype;
	$_shopdata->multi_dispos=$multi_dispos;
	$_shopdata->multi_changetype=$multi_changetype;
}

$multi_distype=$_shopdata->multi_distype;
$multi_dispos=$_shopdata->multi_dispos;
$multi_changetype=$_shopdata->multi_changetype;
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 다중이미지 관리 &gt;<span>상품 다중이미지 설정</span></p></div></div>
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
					<div class="title_depth3">상품 다중이미지 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 상품의 다중이미지의 디스플레이 위치를 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type value="up">
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_multi_distype1" name=multi_distype value="0" <?php if($multi_distype=="0") echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_multi_distype1>상품 다중이미지 아이콘으로 표시</label></TD>
					<TD class="table_cell" align="center"><input type=radio id="idx_multi_distype2" name=multi_distype value="1" <?php if($multi_distype=="1") echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_multi_distype2>상품 다중이미지로 표시</label></TD>
				</TR>
				<TR>
					<TD class="td_con1"  align="center"><img src="images/product_imgmulticonfig1.gif" border="0" class="imgline"></TD>
					<TD class="td_con1" align="center"><img src="images/product_imgmulticonfig2.gif" border="0" class="imgline"> </TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_multi_dispos1" name=multi_dispos value="0" <?php if($multi_dispos=="0") echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_multi_dispos1>상품 확대창에서 다중이미지 오른쪽 출력</label></TD>
					<TD class="table_cell" align="center"><input type=radio id="idx_multi_dispos2" name=multi_dispos value="1" <?php if($multi_dispos=="1") echo " checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_multi_dispos2>상품 확대창에서 다중이미지 아래쪽 출력</label></TD>
				</TR>
				<tr>
					<TD class="td_con1"  align="center"><img src="images/product_imgmulticonfig3.gif" border="0" class="imgline"></TD>
					<TD class="td_con1"  align="center"><img src="images/product_imgmulticonfig4.gif" border="0" class="imgline"></TD>
				</tr>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td width="100%">
		                <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding="0" width="100%" border=0>
						<TR>
							<th style="width:300"><span>상품 확대창 다중이미지 전환방법 설정</span></th>
							<TD class="td_con1"><input type=radio name=multi_changetype value="0" <?php if($multi_changetype=="0") echo "checked"?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <span class="font_orange">마우스를 작은 상품이미지에 <b>갖다대면</span></b> 큰 상품이미지가 변경됩니다.<br><input type=radio name=multi_changetype value="1" <?php if($multi_changetype=="1") echo "checked"?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <span class="font_orange">마우스를 작은 상품이미지에 <b>클릭하면</span></b> 큰 상품이미지가 변경됩니다.</TD>
						</TR>
						</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>다중이미지는 대,중,소 이미지외 추가이미지를 이용해서 여러타입으로 표현할 수 있는 기능입니다.</span></dt>
	
						</dl>
						<dl>
							<dt><span>다중이미지 출력위치는 이미지와 같이 [메인 이미지의 오른쪽, 왼쪽] 선택할 수 있습니다.</span></dt>

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
