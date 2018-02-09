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
?>
<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
	function confirmDelete(mno){
		if(confirm("원료를 삭제 하시겠습니까?")){
			location.href = "material_form_indb.php?mode=materialDel&mno="+mno+"&returnUrl=./material_list.php";
		}
	}
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
					<div class="title_depth3">원료 기본정보</div>
					<div class="table_style02">
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
							<tr>
								<td>
									<table width='100%' cellpadding=0 cellspacing=0 border=0>
										<tr class=rndbg>
											<th width = '100'>Name(Kor)</th>
											<th width = '100'>Name(Eng)</th>
											<th>NaOH</th>
											<th>KOH</th>
											<th>Lauric</th>
											<th>Myristic</th>
											<th>Palmitic</th>
											<th>Stearic</th>
											<th>Ricinoleic</th>
											<th>Oleic</th>
											<th>Linoleic</th>
											<th>Linolenic</th>
											<th>Hardness</th>
											<th>Cleansing</th>
											<th>Condition</th>
											<th>Bubbly</th>
											<th>Creamy</th>
											<th>수정</th>
											<th>삭제</th>
										</tr>
										<?
										$sql = "select * from tblmaterials order by name asc";
										$result = pmysql_query($sql,get_db_conn());
										while($data=pmysql_fetch($result)) {
										?>
											<tr height=25>
												<td><b><?=$data[name]?></b></td>
												<td><b><?=$data[ename]?></b></td>
												<td><b><?=$data[naoh]?></b></td>
												<td><b><?=$data[koh]?></b></td>
												<td><b><?=$data[lauric]?></b></td>
												<td><b><?=$data[myristic]?></b></td>
												<td><b><?=$data[palmitic]?></b></td>
												<td><b><?=$data[stearic]?></b></td>
												<td><b><?=$data[ricinoleic]?></b></td>
												<td><b><?=$data[oleic]?></b></td>
												<td><b><?=$data[linoleic]?></b></td>
												<td><b><?=$data[linolenic]?></b></td>
												<td><b><?=$data[hardness]?></b></td>
												<td><b><?=$data[cleansing]?></b></td>
												<td><b><?=$data[conditions]?></b></td>
												<td><b><?=$data[bubbly]?></b></td>
												<td><b><?=$data[creamy]?></b></td>
												<td>
													<a href="material_form_reg.php?mno=<?=$data[mno]?>"><img src="./img/btn/btn_cate_modify.gif"></a>
												</td>
												<td>
													<a href="javascript:confirmDelete('<?=$data[mno]?>');"><img src="./img/btn/btn_cate_del01.gif"></a>
												</td>
											</tr>
										<?}?>
										</table>
								</td>
							</tr>
						<tr><td height="50"></td></tr>
						</table>
					</div>
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