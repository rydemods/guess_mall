<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$type=$_REQUEST["type"];	//list, view
$code=$_REQUEST["code"];

if($type!="list" && $type!="view") $type="list";

$list=$_SERVER['PHP_SELF']."?block={$block}&gotopage=".$gotopage;
$close="javascript:window.close()";
if($type=="view") {
	$sql = "UPDATE tblnotice SET access=access+1 WHERE date='{$code}' ";
	pmysql_query($sql,get_db_conn());

	$sql = "SELECT * FROM tblnotice WHERE date='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$access=$row->access;
		$subject=$row->subject;
		$content="<pre>{$row->content}</pre>";
	} else {
		alert_go('해당 공지사항이 없습니다.',$list);
	}
	pmysql_free_result($result);

	//이전글
	$sql = "SELECT * FROM tblnotice WHERE date>'{$code}' ";
	$sql.= "ORDER BY date ASC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$prev="<a href=\"{$_SERVER['PHP_SELF']}?type=view&code={$row->date}\">{$row->subject}</a>";
	} else {
		$prev="<a href=\"javascript:alert('이전글이 존재하지 않습니다.')\">이전글이 존재하지 않습니다.</a>";
	}
	pmysql_free_result($result);

	//다음글
	$sql = "SELECT * FROM tblnotice WHERE date<'{$code}' ";
	$sql.= "ORDER BY date DESC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$next="<a href=\"{$_SERVER['PHP_SELF']}?type=view&code={$row->date}\">{$row->subject}</a>";
	} else {
		$next="<a href=\"javascript:alert('다음글이 존재하지 않습니다.')\">다음글이 존재하지 않습니다.</a>";
	}
	pmysql_free_result($result);

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='noticeview' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$size=explode("",$row->filename);
		$xsize=(int)$size[0];
		$ysize=(int)$size[1];
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);

		$body="<script>window.resizeTo({$xsize},{$ysize});</script>\n".$body;
		$newdesign="Y";
	}
	pmysql_free_result($result);

	if($newdesign!="Y") {
		if(file_exists($Dir.TempletDir."notice/noticeview{$_data->design_notice}.php")) {
			$buffer = file_get_contents($Dir.TempletDir."notice/noticeview{$_data->design_notice}.php");
			$body=$buffer;
		}
	}

	$pattern=array ("[DIR]","[SUBJECT]","[CONTENT]","[DATE]","[ACCESS]","[PREV]","[NEXT]","[LIST]","[CLOSE]");
	$replace=array ($Dir,$subject,$content,$date,$access,$prev,$next,$list,$close);
	$body=str_replace($pattern,$replace,$body);
} else {

	$listing="";
	$pageing="";

	$listing.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$listing.="<col width=3></col><col width=></col><col width=85></col><col width=3></col>\n";
	$listing.="<tr height=30 bgcolor=#f2f2f2>\n";
	$listing.="	<td>&nbsp;</td>\n";
	$listing.="	<td align=center>제 목</td>\n";
	$listing.="	<td align=center>게시일</td>\n";
	$listing.="	<td>&nbsp;</td>\n";
	$listing.="</tr>\n";
	$listing.="<tr><td colspan=4 height=1 bgcolor=#dadada></td></tr>\n";

	$sql = "SELECT COUNT(*) as t_count FROM tblnotice ";
	$paging = new Paging($sql,10,10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "SELECT date,subject FROM tblnotice ";
	$sql.= "ORDER BY date DESC ";
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$n_bgcolor = "#FFFFFF";
		if ($i % 2 == 0) $n_bgcolor = "#f4f4f4";

		$listing.="<tr>\n";
		$listing.="	<td></td>\n";
		$listing.="	<td colspan=2>\n";
		$listing.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
		$listing.="	<col width=></col><col width=85></col>\n";
		$listing.="	<tr height=25 bgcolor={$n_bgcolor}>\n";
		$listing.="		<td style=\"padding-left:10\"><A HREF=\"{$_SERVER['PHP_SELF']}?type=view&code={$row->date}\">{$row->subject}</A></td>\n";
		$listing.="		<td align=center>{$date}</td>\n";
		$listing.="	</tr>\n";
		$listing.="	</table>\n";
		$listing.="	</td>\n";
		$listing.="	<td></td>\n";
		$listing.="</tr>\n";
	}
	pmysql_free_result($result);

	$listing.="<tr><td colspan=4 height=1 bgcolor=#dadada></td></tr>\n";
	$listing.="</table>\n";

	$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='noticelist' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$size=explode("",$row->filename);
		$xsize=(int)$size[0];
		$ysize=(int)$size[1];
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);

		$body="<script>window.resizeTo({$xsize},{$ysize});</script>\n".$body;
		$newdesign="Y";
	}
	pmysql_free_result($result);
	
	if($newdesign!="Y") {
		if(file_exists($Dir.TempletDir."notice/noticelist{$_data->design_notice}.php")) {
			$buffer = file_get_contents($Dir.TempletDir."notice/noticelist{$_data->design_notice}.php");
			$body=$buffer;
		}
	}

	$pattern=array ("[DIR]","[LISTING]","[PAGEING]","[CLOSE]");
	$replace=array ($Dir,$listing,$pageing,$close);
	$body=str_replace($pattern,$replace,$body);
}
?>
<html>
<head>
<title>공지사항</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER['PHP_SELF']?>?block="+block+"&gotopage="+gotopage;
}
//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<?=$body?>
</body>
</html>
