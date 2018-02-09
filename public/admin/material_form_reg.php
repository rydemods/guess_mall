<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql ="select * from tblmaterials where mno = '".$_GET[mno]."'";
$result = pmysql_query($sql);
$data=pmysql_fetch($result);
//bodyframe
?>
<?php include("header.php"); ?>
<style>
	.table_style01 table tr td{
		padding:2px 5px 2px 5px;
	}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>카테고리 관리</span></p></div></div>

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
				<form name=form1 action="./material_form_indb.php" method=post target = 'bodyframe'>
					<?if($_GET[mno]){?>
						<input type = 'hidden' name = 'mode' value = 'materialMod'>
						<input type = 'hidden' name = 'mno' value = '<?=$data[mno]?>'>
					<?}else{?>
						<input type = 'hidden' name = 'mode' value = 'materialReg'>
					<?}?>
					<input type = "hidden" name='returnUrl' value="<?=$_SERVER[HTTP_REFERER]?>">
					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
						<tr>
							<td height="8"></td>
						</tr>
						<tr>
							<td>
								<!-- 페이지 타이틀 -->
								<div class="title_depth3">원료 기본정보</div>
							</td>
						</tr>
						<tr>
							<td>
								<!-- 소제목 -->
								<div class="title_depth3_sub">가성소다계산기에 노출 될 원료명 등록 처리</div>
							</td>
						</tr>
						<tr>
							<td height=3></td>
						</tr>
						<tr>
							<td>
								<div class="table_style01">
									<table cellspacing=0 cellpadding=0 width="90%" border=0>
										<tr>
											<th nowrap><span>원료명(한글)</span></th>
											<td>
												<div style="height:25;"><input type=text name='name' style="width:100px;" value="<?=$data[name]?>" required label="원료명" class="line"></div>
											</td>
											<th nowrap><span>원료명(영문)</span></th>
											<td>
												<div style="height:25;"><input type=text name='ename' style="width:100px;" value="<?=$data[ename]?>" required label="원료명" class="line"></div>
											</td>
											<th nowrap><span>NaOH</span></th>
											<td>
												<input type=text name=naoh value="<?=$data[naoh]?>" style="width:100px;">
											</td>
											<th nowrap><span>KOH</span></th>
											<td>
												<input type=text name=koh value="<?=$data[koh]?>" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th nowrap><span>Lauric</span></th>
											<td>
												<input type=text name=lauric value="<?=$data[lauric]?>" style="width:100px;">
											</td>
											<th nowrap><span>Myristic</span></th>
											<td>
												<input type=text name=myristic value="<?=$data[myristic]?>" style="width:100px;">
											</td>
											<th nowrap><span>Palmitic</span></th>
											<td>
												<input type=text name=palmitic value="<?=$data[palmitic]?>" style="width:100px;">
											</td>
											<th nowrap><span>Stearic</span></th>
											<td>
												<input type=text name=stearic value="<?=$data[stearic]?>" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th nowrap><span>Ricinoleic</span></th>
											<td>
												<input type=text name=ricinoleic value="<?=$data[ricinoleic]?>" style="width:100px;">
											</td>
											<th nowrap><span>Oleic</span></th>
											<td>
												<input type=text name=oleic value="<?=$data[oleic]?>" style="width:100px;">
											</td>
											<th nowrap><span>Linoleic</span></th>
											<td>
												<input type=text name=linoleic value="<?=$data[linoleic]?>" style="width:100px;">
											</td>
											<th nowrap><span>Linolenic</span></th>
											<td>
												<input type=text name=linolenic value="<?=$data[linolenic]?>" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th nowrap><span>Hardness</span></th>
											<td>
												<input type=text name=hardness value="<?=$data[hardness]?>" style="width:100px;">
											</td>
											<th nowrap><span>Cleansing</span></th>
											<td>
												<input type=text name=cleansing value="<?=$data[cleansing]?>" style="width:100px;">
											</td>
											<th nowrap><span>Condition</span></th>
											<td>
												<input type=text name=conditions value="<?=$data[conditions]?>" style="width:100px;">
											</td>
											<th nowrap><span>Bubbly</span></th>
											<td>
												<input type=text name=bubbly value="<?=$data[bubbly]?>" style="width:100px;">
											</td>
										</tr>
										<tr>
											<th nowrap><span>Creamy</span></th>
											<td colspan = '7'>
												<input type=text name=creamy value="<?=$data[creamy]?>" style="width:100px;">
											</td>
										</tr>
										<tr>
											<td colspan = '8' align ='center' style = 'background:white;'>
												<?if($_GET[mno]){?>
													<input type=image src="./images/botteon_save.gif">
												<?}else{?>
													<input type=image src="./images/botteon_save.gif">
												<?}?>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					<tr><td height="50"></td></tr>
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
<?php
include("copyright.php");
?>
<?=$onload?>