<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."conf/config.linker.php");
include("access.php");

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type = $_POST["type"];
$shoplinker_id = $_POST["shoplinker_id"];
$customer_id = $_POST["customer_id"];
$shoplinker_tax = $_POST["shoplinker_tax"];

if ($type == "up"){

	$f = fopen($Dir."conf/config.linker.php","w+");
	fwrite($f,"<?\r\n");
	fwrite($f,"\$linkerData[shoplinker_id] = '".$shoplinker_id."'; \r\n");
	fwrite($f,"\$linkerData[customer_id] = '".$customer_id."'; \r\n");
	fwrite($f,"\$linkerData[shoplinker_tax] = '".$shoplinker_tax."'; \r\n");
	fwrite($f,"?>\r\n");
	fclose($f);
	chmod($Dir."conf/config.linker.php",0777);

	$onload="<script>window.onload=function(){alert(\"정보 수정이 완료되었습니다.\");location.replace('linker_settings.php');}</script>";
}

$checked['shoplinker_tax'][$linkerData['shoplinker_tax']] = "checked";

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

function CheckForm() {
	var form = document.form1;

	if (!form.shoplinker_id.value) {
		form.shoplinker_id.focus();
		alert("샵링커 ID를 입력하세요.");
		return;
	}
	if (!form.customer_id.value) {
		form.customer_id.focus();
		alert("고객 ID를 입력하세요.");
		return;
	}

	form.type.value="up";
	form.submit();
}
</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 샵링커 상품관리 &gt;<span>샵링커 연동 설정</span></p></div></div>

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
				<input type=hidden name=type>
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr><td height="8"></td></tr>
					<tr>
						<td>
							<!-- 페이지 타이틀 -->
							<div class="title_depth3">샵링커 연동 설정</div>
						</td>
					</tr>
					<tr>
						<td>
							<!-- 소제목 -->
							<div class="title_depth3_sub">샵링커 정보 등록</div>
						</td>
					</tr>
					<tr><td height="3"></td></tr>
					<tr>
						<td>
							<div class="table_style01">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>샵링커 고객사ID</span></th>
								<td class="td_con1"><input type="text" name="shoplinker_id" value="<?=$linkerData['shoplinker_id']?>" size="20" maxlength="30" class="input"></td>
							</tr>
							<tr>
								<th><span>샵링커 고객사코드</span></th>
								<td class="td_con1"><input type="text" name="customer_id" value="<?=$linkerData['customer_id']?>" size="20" maxlength="20" class="input"></td>
							</tr>
							<tr>
								<th><span>샵링커 과세</span></th>
								<td class="td_con1">
									<input type = 'radio' name = 'shoplinker_tax' value = '001' <?=$checked['shoplinker_tax']['001']?>>과세&nbsp;&nbsp;
									<input type = 'radio' name = 'shoplinker_tax' value = '002' <?=$checked['shoplinker_tax']['002']?>>면세&nbsp;&nbsp;
									<span class="font_blue">* 입력하지 않으면 과세로 데이터 전송 됩니다.</span>
								</td>
							</tr>
							</table>
							</div>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
					</tr>
					<tr><td height="20"></td></tr>
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
<?=$onload?>
<?php 
include("copyright.php");
