<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$code=$_REQUEST["code"];
if(ord($code)) {
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='community' AND code='{$code}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$isnew=true;
		$newobj = new stdClass;
		$newobj->subject=$row->subject;
		$newobj->menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$newobj->member_type=$filename[0];
		$newobj->menu_code=$filename[1];
		$newobj->body=$row->body;
		$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
		if(strlen($newobj->member_type)>1) {
			$newobj->group_code=$newobj->member_type;
			$newobj->member_type="G";
		}
	}
	pmysql_free_result($result);
}
if($isnew!=true) {
	alert_go('해당 페이지가 존재하지 않습니다.',-1);
}

if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
			exit;
		} else {
			alert_go('해당 페이지 접근권한이 없습니다.',$Dir.MainDir."main.php");
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
//-->
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php 
$boardval=array();
for($i=1;$i<=9;$i++) {
	if($num=strpos($newobj->body,"[BOARD".$i)) {
		$boardval[$i]->board_type="Y";
		$boardval[$i]->board_datetype=$newobj->body[$num+7];
		$boardval[$i]->board_num=(int)($newobj->body[$num+8]);
		$boardval[$i]->board_gan=(int)($newobj->body[$num+9]);
		$boardval[$i]->board_reply=$newobj->body[$num+10];

		$board_tmp=explode("_",substr($newobj->body,$num+1,strpos($newobj->body,"]",$num)-$num-1));

		$boardval[$i]->board_titlelen=$board_tmp[1];
		$boardval[$i]->board_code=substr($newobj->body,$num+13+strlen($boardval[$i]->board_titlelen),strpos($newobj->body,"]",$num)-$num-13-strlen($boardval[$i]->board_titlelen));

		$boardval[$i]->board_titlelen=(int)$boardval[$i]->board_titlelen;
		if($boardval[$i]->board_num==0) $boardval[$i]->board_num=5;
		if(ord($boardval[$i]->board_code)==0) $boardval[$i]->board_type="";
	}
}

################## 게시판 #################
$board1=""; $board2=""; $board3=""; $board4=""; $board5=""; $board6=""; $board7=""; $board8=""; $board9="";
for($i=1;$i<=9;$i++) {
	if($boardval[$i]->board_type=="Y") {
		${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		${"board".$i}.="<tr>\n";
		${"board".$i}.="	<td style=\"padding:5\">\n";

		$sql = "SELECT num, title, writetime FROM tblboard WHERE board='{$boardval[$i]->board_code}' ";
		$sql.= "AND deleted!='1' ";
		if($boardval[$i]->board_reply=="N") $sql.= "AND pos=0 ";
		$sql.= "ORDER BY thread ASC LIMIT ".$boardval[$i]->board_num;
		$result=@pmysql_query($sql,get_db_conn());
		$j=0;
		while($row=pmysql_fetch_object($result)) {
			$j++;
			$date="";
			if($boardval[$i]->board_datetype=="1") {
				$date="[".date("m/d",$row->writetime)."] ";
			} else if($boardval[$i]->board_datetype=="2") {
				$date="[".date("Y/m/d",$row->writetime)."] ";
			}
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td>";
			${"board".$i}.="<A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board={$boardval[$i]->board_code}&num={$row->num}\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</FONT></A>";
			${"board".$i}.="</td></tr>\n";
			${"board".$i}.="<tr><td height={$boardval[$i]->board_gan}></td></tr>\n";
			${"board".$i}.="</table>\n";
		}
		pmysql_free_result($result);
		if($j==0) {
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>";
			${"board".$i}.="</table>";
		}
		${"board".$i}.="	</td>\n";
		${"board".$i}.="</tr>\n";
		${"board".$i}.="</table>\n";
	}
}

$pattern=array(
			"/\[BOARD1([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD2([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD3([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD4([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD5([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD6([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD7([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD8([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/",
			"/\[BOARD9([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\]/"
			);
$replace=array($board1,$board2,$board3,$board4,$board5,$board6,$board7,$board8,$board9);
$newobj->body=preg_replace($pattern,$replace,$newobj->body);

if($newobj->menu_type=="Y") {
	include ($Dir.MainDir.$_data->menu_type.".php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else if($newobj->menu_type=="T" && $_data->frame_type!="N") {
	include ($Dir.MainDir."nomenu.php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else {
	echo $newobj->body;
}
?>
</BODY>
</HTML>
