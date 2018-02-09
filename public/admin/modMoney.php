<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include_once($Dir."lib/adminlib.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}

$sno=$_POST[sno];


list($price)=pmysql_fetch_array(pmysql_query("select wsmoney from tblwsmoneylog where sno='".$sno."'"));
?>

<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = 250;
	var oHeight = 200;

	window.resizeTo(oWidth,oHeight);
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function go_indb(){
	document.idxform.submit();
}
//-->
</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p></p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<form name=idxform action="modMoney_indb.php" method=post>
<input type="hidden" name="sno" value="<?=$sno?>">
<input type="hidden" name="mode" value="modmoney">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<td align=right width="150">
<input type="text" value="<?=$price?>" name="wsmoney" style="text-align: right">
</td>

<td style="padding-top:3px;padding-left:5px">
	<a href="javascript:go_indb();"><img src="images/btn_edit.gif"></a>
</td>
</table>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<td align=center><font color=red>※ ","제외 숫자만 입력하세요.</font></td>
</table>
</form>

<?=$onload?>
</body>
</html>
