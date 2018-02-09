<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

	$sql="select * from tblmobileShopInfo";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);


	$checked[useyn][$row->useyn]='checked';

include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 모바일샵 &gt; 모바일샵 관리 &gt;<span>모바일샵 기본서정</span></p></div></div>
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
			<?php include("menu_mobileShop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">모바일샵 기본설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>모바일샵 사용여부 및 로고를 등록할수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="indb_mobileShop.php" method=post enctype="multipart/form-data">
			<input type=hidden name=type />
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=139></col>
				<col width=></col>
				<TR>
					<th><span>모바일샵 사용여부</span></th>
					<TD>
						<input type="radio" name="useyn" id="useyn_y" value="y" <?=$checked[useyn]['y']?> />사용
						<input type="radio" name="useyn" id="useyn_n" value="n" <?=$checked[useyn]['n']?> />미사용
					</TD>
				</TR>
				<TR>
					<th><span>로고 등록</span></th>
					<TD>
						<input type="file" name="mobile_logo" id="mobile_logo" /><br />
						<? if($row->logo_img){ ?>
							<img src=<?=$row->logo_img?> style="border:0px;"/>
						<?}else{?>
							(등록된 로고가 없습니다.)
						<?}?>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="text-align:center">
					<input type="image" id="submit_set" src="images/botteon_save.gif" />
				</td>
			</tr>
			<tr><td height=40></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>메뉴얼</p></div>
						
						<dl>
							<dt><span>모바일샵 기본설정</span></dt>
							<dd>- ....<br>
							- ....<br>
							- ....
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			
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
