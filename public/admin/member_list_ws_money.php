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
$group_result=pmysql_query("select name, reserve from tblmember where id='$_GET[id]'");
$group_data=pmysql_fetch($group_result);
$name = $group_data[name];
$emoney = $group_data[reserve];


### 목록
/*
$pg = new Page($_GET[page],10);
$db_table = "gd_log_wsmoney";
$pg->field = "*, date_format( regdt, '%Y.%m.%d' ) as regdts"; # 필드 쿼리
$where[] = "m_no='$_GET[m_no]'";
$pg->setQuery($db_table,$where,$orderby="regdt desc");
$pg->exec();

$res = $db->query($pg->query);
*/

$sql0 = "select count(*) from tblwsmoneylog where id='$_GET[id]'";
$sql = "select * from tblwsmoneylog where id='$_GET[id]' order by regdt desc";
$paging = new Paging($sql0,10,10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
?>

<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = 650;
	var oHeight = 530;

	window.resizeTo(oWidth,oHeight);
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function popup(sno,width,height){
	window.open("about:blank","modform","width=250,height=200,scrollbars=yes");
	document.modform.target="modform";
	document.modform.sno.value=sno;
	document.modform.action="./modMoney.php";
	document.modform.submit();
}

//-->
</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p></p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=get>
<input type=hidden name=block value="">
<input type=hidden name=gotopage value="">
<input type=hidden name=id value="<?=$_GET[id]?>">
<div class="table_style02">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class=rndbg>
	<th>번호</th>
	<th>연장 일자</th>
	<th>당시 누적금액</th>
	<th>당시 도매등급</th>
</tr>
<col width=50 align=center><col width=80 align=center><col width=80 align=center><col width=80 align=center>
<?
$sql = $paging->getSql($sql);
$result = pmysql_query($sql,get_db_conn());
$cnt=0;
while($row=pmysql_fetch_object($result)) {	
	$group_result_name=pmysql_query("select group_name from tblmembergroup where group_level =".$row->group_level);
	$group_data_name=pmysql_fetch($group_result_name);
	$grpnm = $group_data_name[group_name];
	$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
?>
<tr height=25 align="center">
	<td><font class=ver81 color=616161><?=$number?></td>
	<td><font class=ver81 color=616161><?=reset(explode(" ",$row->regdt))?></td>
	<td><font class=ver81 color=0074BA><b><span onclick="popup('<?=$row->sno?>',200,100)" style="cursor:pointer"><?=number_format($row->wsmoney)?></span></b></font>원</td>
	<td><font class=ver81 color=333333><?=$grpnm?>(<?=$row->group_level?>)</td>
	
</tr>
<?			
	$cnt++;
} 
?>
</table>
</div>
<div style = 'text-align:center;'><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div>
</form>
<form name=modform  method=post>
	<input type=hidden name=sno>
</form>
<?=$onload?>
</body>
</html>
