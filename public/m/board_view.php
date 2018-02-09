	<?php
	include_once('outline/header_m.php');
	$b_num = $_GET[boardnum];
	$board = $_GET[board];

	$sql = "UPDATE tblboard SET access=access+1 WHERE board='{$board}' AND num='{$b_num}' ";
	pmysql_query($sql,get_db_conn());

	$qry = "select * from tblboard where board='{$board}' AND num='{$b_num}' ";
	$result = pmysql_query($qry);
	$row = pmysql_fetch_object($result);

	$date_view = date("Y-m-d",$row->writetime);

	$boardname_sql = "SELECT board_name FROM tblboardadmin WHERE board='{$board}'";
	$boardname_res = pmysql_query($boardname_sql);
	$boardname_row = pmysql_fetch_object($boardname_res);
	pmysql_free_result($boardname_res);

	list($prev_num)=pmysql_fetch_array(pmysql_query("SELECT num FROM tblboard WHERE board='notice' AND num < '{$b_num}' ORDER BY writetime DESC limit 1"));
	list($next_num)=pmysql_fetch_array(pmysql_query("SELECT num FROM tblboard WHERE board='notice' AND num > '{$b_num}' ORDER BY writetime ASC limit 1"));
?>	
	<div class="sub-title">
		<h2>공지사항</h2>
		<a class="btn-prev" href="javascript:;" onclick='history.back(-1); return false;'><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
	</div>
	
	<div class="board-detail-wrap">
		<div class="board-view">
			<h3 class="title"><?=$row->subject?> <span class="date"><?=$date_view?></span></h3>
			<div class="content">
				<?=nl2br($row->content)?>
			</div>
		</div>

		<div class="btnwrap">
			<div class="box">
				<?if ($prev_num) {?><a class="btn-def" href="javascript:ViewNotice('<?=$prev_num?>','<?=$_GET[block]?>','<?=$_GET[gotopage]?>','<?=$board?>');">이전</a><?}?>
				<a class="btn-def" href="javascript:ListNotice('<?=$_GET[block]?>','<?=$_GET[gotopage]?>','<?=$board?>');">목록</a>
				<?if ($next_num) {?><a class="btn-def" href="javascript:ViewNotice('<?=$next_num?>','<?=$_GET[block]?>','<?=$_GET[gotopage]?>','<?=$board?>');">다음</a><?}?>
			</div>
		</div>
	</div>
	
<script>
function ListNotice(block,gotopage,board){
	location.href="cscenter.php?csMenu="+board+"&block="+block+"&gotopage="+gotopage;
}
function ViewNotice(boardnum,block,gotopage,board) {
	if(!block){
		var block = 0;
	}
	location.href="board_view.php?board="+board+"&boardnum="+boardnum+"&block="+block+"&gotopage="+gotopage;
}
</script>

<? include_once('outline/footer_m.php'); ?>