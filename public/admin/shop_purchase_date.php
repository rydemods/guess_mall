<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
if($_POST['mode'] == "date") {
//     print_r($_POST);
    extract($_POST);
    exdebug($purchaseDate);

    $f = fopen($Dir."conf/config.purchase_date.php","w");

	fwrite($f,"<?\n");
	fwrite($f,"\$purchaseDateSet['purchaseDate']  = '$purchaseDate'; \n");
	fwrite($f,"?>\n");
	fclose($f);	@chmod($Dir."conf/config.purchase_date.php",0777);
	@chmod($Dir."conf/config.purchase_date.php",0777);
}
?>

<?php 
include("header.php"); 
include_once($Dir."conf/config.purchase_date.php");
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	form.submit();
}

function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode < 48 || charCode > 57){
		return false;
	}
	// Textbox value

	return true;
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>구매확정 관련 기간설정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">구매확정 관련 기간설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>구매확정 관련 기간을 설정할 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">기간 설정</div>
				</td>
			</tr>
            <form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="date">
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>기간 입력</span></th>
					<td><input type='text' name="purchaseDate" value="<?=$purchaseDateSet['purchaseDate']?>" size=100 label="sns 포인트" onkeypress="return isNumberKey(event)" class=input></td>
				</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
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
