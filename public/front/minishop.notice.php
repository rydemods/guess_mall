<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/paging.php");
include_once($Dir."lib/venderlib.php");

$sellvidx=$_REQUEST["sellvidx"];

$_MiniLib=new _MiniLib($sellvidx);
$_MiniLib->_MiniInit();

if(!$_MiniLib->isVender) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}
$_minidata=$_MiniLib->getMiniData();

$_MiniLib->getCode();
$_MiniLib->getThemecode();

$strlocation="<A HREF=\"http://".$_ShopInfo->getShopurl()."\">홈</A> > <A HREF=\"http://".$_ShopInfo->getShopurl().FrontDir."minishop.php?sellvidx={$_minidata->vender}\"><B>{$_minidata->brand_name}</B></A>";


$type=$_REQUEST["type"];	//list, view
$artid=$_REQUEST["artid"];

if($type!="list" && $type!="view") $type="list";

if($type=="view") {
	$sql = "SELECT * FROM tblvendernotice WHERE vender='{$_minidata->vender}' AND date='{$artid}' ";
	$result=pmysql_query($sql,get_db_conn());
	$noticedata=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$sql = "UPDATE tblvendernotice SET access=access+1 WHERE vender='{$_minidata->vender}' AND date='{$artid}' ";
	pmysql_query($sql,get_db_conn());

}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/minishop.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER['PHP_SELF']?>?sellvidx=<?=$_minidata->vender?>&block="+block+"&gotopage="+gotopage;
}
//-->
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php  include ($Dir."lib/menu_minishop.php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:5">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr><td height=5></td></tr>
	<tr>
		<td style="padding-left:5"><img src="<?=$Dir?>images/minishop/title_notice.gif" border=0></td>
	</tr>
	<tr><td height=20></td></tr>
	<tr>
		<td style="padding-left:5">
		
<?php if($type=="list"){?>
		
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=60></col>
		<col width=1></col>
		<col width=120></col>
		<col width=1></col>
		<col width=></col>
		<col width=1></col>
		<col width=80></col>
		<tr height=21>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table0.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table1.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table2.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table3.gif" border=0></td>
		</tr>
		<tr>
			<td colspan=7 height=12 background="<?=$Dir?>images/minishop/notice_tabletoplinebg.gif"></td>
		</tr>
<?php 
		$sql = "SELECT COUNT(*) as t_count FROM tblvendernotice WHERE vender='{$_minidata->vender}' ";
		$paging = new Paging($sql,10,10);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT date,subject,access FROM tblvendernotice ";
		$sql.= "WHERE vender='{$_minidata->vender}' ";
		$sql.= "ORDER BY date DESC ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			echo "<tr height=25>\n";
			echo "	<td align=center>{$number}</td>\n";
			echo "	<td colspan=2 align=center>{$date}</td>\n";
			echo "	<td colspan=2 style=\"padding-left:15\"><A HREF=\"javascript:GoNoticeView('{$_minidata->vender}','{$row->date}','{$block}','{$gotopage}')\">".strip_tags($row->subject)."</A></td>\n";
			echo "	<td colspan=2 align=center>{$row->access}</td>\n";
			echo "</tr>\n";
			echo "<tr><td colspan=\"7\" height=\"1\" bgcolor=\"#EFEBEF\"></td></tr>\n";
			$i++;
		}
		pmysql_free_result($result);
		echo "<tr><td colspan=\"7\" height=\"2\" bgcolor=\"#000000\"></td></tr>\n";
		if($i>0) {
			$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "<tr><td colspan=7 align=center style=\"padding-top:10\">{$pageing}</td></tr>\n";
		}
?>
		</table>

<?php } elseif($type=="view"){?>

		<table width=100% border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td>
			<table width="100%" border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
			<col width=80></col>
			<col width=230></col>
			<col width=55></col>
			<col width=></col>
			<tr>
				<td width="60" height="25" style="padding-left:25">제&nbsp;&nbsp;&nbsp;목 :</td>
				<td width="671" colspan="3" align="left"><font COLOR="#FF3300"><B><?=$noticedata->subject?></B></font></td>
			</tr>
			<tr height=25>
				<td style="padding-left:25">등록일 :</td>
				<td><?=substr($noticedata->date,0,4)."/".substr($noticedata->date,4,2)."/".substr($noticedata->date,6,2)?></td>
				<td>조회수 :</td>
				<td><?=$noticedata->access?></td>
			</tr>
			<tr>
				<td colspan=4 height=12 background="<?=$Dir?>images/minishop/notice_tabletoplinebg.gif"></td>
			</tr>
			<tr>
				<td style="padding:10,25" colspan="4">
				
				<?=nl2br($noticedata->content)?>

				</td>
			</tr>
			<tr>
				<td height=8></td>
			</tr>
			<tr>
				<td colspan="4" height="2" bgcolor="#000000"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="30" align="right"><a href="javascript:GoNoticeList('<?=$_minidata->vender?>','<?=$block?>','<?=$gotopage?>')"><img src="<?=$Dir?>images/minishop/btn_notice_list.gif" border=0></a></td>
		</tr>
		</table>
<?php }?>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
