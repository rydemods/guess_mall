<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$tmp_filter=explode("#",$_shopdata->filter);
$filter = $tmp_filter[0];
$arfilter = explode("=",$filter);
$review_filter = $tmp_filter[1];

$type=$_POST["type"];
$patten=(array)$_POST["patten"];
$replace=(array)$_POST["replace"];

if ($type=="update") {
	if($_POST[del]){
		$arrDel = explode("||", $_POST[del]);
		$strDel = implode("','", array_filter($arrDel));
		pmysql_query("DELETE  FROM tblmanufacturer WHERE num in ('".$strDel."')", get_db_conn());
	}
	foreach($_POST[name] as $v){
		if($v) pmysql_query("INSERT INTO tblmanufacturer(name) VALUES ('{$v}')", get_db_conn());
	}
	foreach($_POST[db_name] as $k => $v){
		if($v) pmysql_query("UPDATE tblmanufacturer SET name = '{$v}' WHERE NUM = '{$k}'", get_db_conn());
	}
	
	alert_go('수정되었습니다.',$Dir.AdminDir."manufacturer_reg.php");
}

$sql = "SELECT * FROM tblmanufacturer ORDER BY num ASC ";
$result=pmysql_query($sql,get_db_conn());
$arrManufact = array();
while($row=pmysql_fetch_object($result)){
	$arrManufact[$row->num] = $row->name;
}
pmysql_free_result($result);


?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.type.value="update";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 기타관리 &gt;<span>상품상세 업체등록</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type='hidden' name='type'>
				<input type='hidden' name='del'>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr><td height="8"></td></tr>
					<tr>
						<td>
							<!-- 페이지 타이틀 -->
							<div class="title_depth3">상품상세 업체등록</div>
						</td>
					</tr>
					<tr>
						<td>
							<!-- 소제목 -->
							<div class="title_depth3_sub"><span>상품상세정보란에 업체명 등록해 주는 기능입니다.</span></div>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td>
							<div class="table_style02">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<TR align=center>
										<th colspan = '2'>업체명</th>
									</TR>
									<tr>
										<td width = '90%'><!--input type=text name="name[]" maxlength=40 style="WIDTH: 99%" class=input--></td>
										<td>
											<a href = "javascript:;" class = 'CLS_rowAdd'><img src = './images/btn_addr.gif'></a>
										</td>
									</tr>
									<?if(count($arrManufact) > 0){?>
									<?foreach($arrManufact as $key => $val){?>
									<tr>
										<td width = '90%'><input type=text name="db_name[<?=$key?>]" value = '<?=$val?>' maxlength=40 style="WIDTH: 99%" class=input></td>
										<td>
											<a href = "javascript:;" class = 'CLS_rowDel' ids = '<?=$key?>||'><img src = './images/btn_del6r.gif'></a>
										</td>
									</tr>
									<?}?>
									<?}?>
									<tbody id = 'ID_tbody'>
									</tbody>
								</TABLE>
							</div>
						</td>
					</tr>
					<tr><td height=10></td></tr>																				
					<tr>
						<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif"  border="0"></a></td>
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

<script>
	$(document).ready(function(){
		$(".CLS_rowAdd").click(function(){
			$("#ID_tbody").append("<tr><td width = '90%'><input type=text name='name[]' maxlength=40 style='WIDTH: 99%' class=input></td><td><a href = 'javascript:;' class = 'CLS_rowDel'><img src = './images/btn_del6r.gif'></a></td></tr>");
		})
		$(document).on("click", ".CLS_rowDel", function(){
			if($(this).attr('ids')){
				var deleteItem = $("input[name='del']").val()+$(this).attr('ids');
				$("input[name='del']").val(deleteItem);
			}
			$(this).parent().parent().remove();
		});

	})
</script>
<?=$onload?>
<?php 
include("copyright.php");
