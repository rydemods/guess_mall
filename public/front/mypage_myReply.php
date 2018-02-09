<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
pmysql_free_result($result);

$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$ordgbn=$_POST["ordgbn"];
if(!strstr("ASCR",$ordgbn)) {
	$ordgbn="A";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 주문/배송 조회</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<tr>
<td align=center>

<div id="container">
<!-- LNB -->
	<?	include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	<!-- #LNB -->

	<div class="contents_side">
	<? include $Dir.FrontDir."mypage_menu.php";?>




<div class="my_reply_detail">

             <div class="my_replylist_title">
			 <span class="my_replylist_titleimg"><img src="<?=$Dir?>image/mypage/reply_reply_title.gif" alt="내가 작성한 최근 댓글" /></span>
			 </div>

             <div class="my_replylist_warp">

             <div class="my_replylist_bar">
			 <ul>
			 <li class="cell10"><img src="<?=$Dir?>image/mypage/reply_menu01.gif" alt="번호"></li>
			 <li class="cell15"><img src="<?=$Dir?>image/mypage/reply_menu02.gif" alt="카테고리"></li>
			 <li class="cell55"><img src="<?=$Dir?>image/mypage/reply_menu03.gif" alt="내용"></li>
			 <li class="cell20"><img src="<?=$Dir?>image/mypage/reply_menu04.gif" alt="날짜"></li>
			 </ul>
			 </div>

			 <div class="my_replylist_list">

<?
		$sql = "SELECT COUNT(*) as t_count FROM tblboard a left join tblboardadmin b on (a.board=b.board)";
		$sql.= "WHERE a.mem_id='".$_ShopInfo->getMemid()."' and a.pos!=0 and a.depth!=0 ";
		$paging = new Tem001_Paging($sql,10,10,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT a.board,a.title,a.writetime,a.num,b.board_name FROM tblboard a left join tblboardadmin b on (a.board=b.board)";
		$sql.= "WHERE a.mem_id='".$_ShopInfo->getMemid()."' and a.pos!=0 and a.depth!=0 ";
		$sql.= "ORDER BY a.num DESC";
		$sql = $paging->getSql($sql);
		
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

			$date=date('Y-m-d',$row->writetime);
			
?>
			 <ul>
			 <li class="cell10"><?=$number?></li>
			 <li class="cell15 fontBlue"><?=$row->board_name?></li>
			 <li class="cell55"><a href="<?=$Dir.BoardDir?>board.php?pagetype=view&num=<?=$row->num?>&board=<?=$row->board?>&block=&gotopage=1&search=&s_check="><?=$row->title?></a></li>
			 <li class="cell20"><?=$date?></li>
			 </ul>

<?
			
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<ul><li style='width:850px'>해당내역이 없습니다.</li></ul>";
		}
?>

	
				<div class="paging"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div><!-- paging 끝 -->

			 </div><!-- my_writelist_list 끝 -->

			 </div><!-- my_writelist_warp 끝 -->
		 </div>
 </div>
</td>
</tr>
</form>


<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
