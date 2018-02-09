<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_frame_type=$_POST["up_frame_type"];
$up_align_type=$_POST["up_align_type"];
$up_predit_type=$_POST["up_predit_type"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "frame_type		= '{$up_frame_type}', ";
	$sql.= "align_type		= '{$up_align_type}', ";
	$sql.= "predit_type		= '{$up_predit_type}' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	//$onload = "<script> alert('설정이 완료되었습니다.'); </script>";
	$onload="<script>window.onload=function(){alert(\"설정이 완료되었습니다.\");}</script>";
}

$sql = "SELECT frame_type, align_type, predit_type FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$frame_type= $row->frame_type;
	$align_type= $row->align_type;
	$predit_type = $row->predit_type;
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(){
	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>프레임/정렬 설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">프레임/정렬 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>프레임, 페이지 정렬설정, 상품상세입력의 웹편집기 사용을 일괄 적용할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">프레임 타입 설정</div>
				</td>
			</tr>			
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 투프레임 타입(상단+메인프레임) : 쇼핑몰 페이지 주소 고정 및 상단메뉴 고정(새로고침 F5 - 쇼핑몰 메인으로 이동) </li>
                            <li>2)원프레임 타입(주소고정) : 쇼핑몰 페이지 주소가 항상 메인도메인명으로 고정(새로고침 F5 - 쇼핑몰 메인으로 이동) </li>
                            <li>3)원프레임 타입(주소변동) : 쇼핑몰의 각 페이지 주소를 그대로 노출하여 표시(새로고침 F5 - 현재 페이지 유지)</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_frame_type1" name=up_frame_type value="N" <?php if($frame_type == "N") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type1>투프레임 타입(상단+메인프레임)</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_frame_type2" name=up_frame_type value="Y" <?php if($frame_type == "Y") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type2>원프레임 타입(주소고정)</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_frame_type3" name=up_frame_type value="A" <?php if($frame_type == "A") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type3>원프레임 타입(주소변동)</label></TD>
				</TR>
				<TR>
					<TD class="td_con1" align="center"><img src="images/shop_framepage.gif" border="0"></TD>
					<TD class="td_con1" align="center"><img src="images/shop_noframepage.gif" border="0"></TD>
					<TD class="td_con1" align="center"><img src="images/shop_noframepage.gif" border="0"></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">페이지 정렬</div>
				</td>
			</tr>
            <tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 투프레임과 가운데 정렬을 선택할 경우 스크롤바 때문에 쇼핑몰의 형태가 어긋날 수 있습니다.</li>
                            <li>2) 원프레임에 가운데 정렬을 하실것을 권장합니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_align_type1" name=up_align_type value="N" <?php if($align_type == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_align_type1>좌측 정렬(좌측여백 X, 우측여백 O)</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_align_type2" name=up_align_type value="Y" <?php if($align_type == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_align_type2>가운데 정렬(좌측여백 O, 우측여백 O)</label></TD>
				</TR>
				<TR>
					<TD align="center"><img src="images/shop_alignleft.gif" border="0"></TD>
					<TD class="td_con1" align="center"><img src="images/shop_aligncenter.gif" border="0"> </TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<!--tr>
				<td>
					<div class="title_depth3_sub">상품 상세 정보 입력 타입</div>
				</td>
			</tr>
            <tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 상품등록시 상세정보 입력 타입을 편집기사용 또는 미사용을 일괄 적용할 수 있습니다.</li>
                            <li>2) 입력 타입을 변경할 경우 기존 입력모양이 달라질 수 있습니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9;">
				<TR>
					<TD class="table_cell" align="center"><input type=radio id="idx_predit_type1" name=up_predit_type value="Y" <?php if($predit_type == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_predit_type1>웹편집기로 입력(<b>권장</b>)</label></TD>
					<TD class="table_cell1" align="center"><input type=radio id="idx_predit_type2" name=up_predit_type value="N" <?php if($predit_type == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_predit_type2>단순 입력창에서 입력(개별 HTML 방식)</label></TD>
				</TR>
				<TR>
					<TD align="center"><img src="images/shop_detailediter.gif" border="0"></TD>
					<TD class="td_con1" align="center"> <img src="images/shop_detailhtml.gif" border="0"></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr-->
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
							<dt><span>화면설정을 자유롭게 설정</span></dt>
							<dd>
								- 프레임과 좌우정렬을 자유롭게 가능합니다.<br>
								- 원프레임에서 디자인 한 후 투프레임으로 사용할 경우 상하좌우 라인이 정확히 일치하지 않을 수 있습니다.<br>
								- 좌우정렬을 변경하면 기존 디자인에 변화가 있을 수 있습니다.<br>
								- 상품의 특성이나 쇼핑몰에 변화를 줄 때 좌우정렬 및 디자인을 변경하면서 사용하실 수 있습니다.<br>
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			<input type=hidden id="idx_predit_type1" name=up_predit_type value="Y">
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
