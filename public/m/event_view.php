<?php
	include_once('outline/header_m.php');
	$b_num = $_GET[boardnum];
	$board = $_GET[board];

	$sql = "UPDATE tblboard SET access=access+1 WHERE board='{$board}' AND num='{$b_num}' ";
	pmysql_query($sql,get_db_conn());

	$qry = "select * from tblboard where board='{$board}' AND num='{$b_num}' ";
	$result = pmysql_query($qry);
	$row = pmysql_fetch_object($result);

	$date_view = date("Y-m-d H:i",$row->writetime);

	$boardname_sql = "SELECT board_name FROM tblboardadmin WHERE board='{$board}'";
	$boardname_res = pmysql_query($boardname_sql);
	$boardname_row = pmysql_fetch_object($boardname_res);
	$board_name		= $boardname_row->board_name;
	pmysql_free_result($boardname_res);
?>
<main id="content" class="subpage">
<article class="mypage">
 <section class="customer">
 <h3><?=$board_name?></h3>

<table class="notice_view">
	<tr>
		<th class="title"><?=$row->title?></th>
	</tr>
	<tr>
		<td class="date"><?=$_data->shoptitle?>&nbsp;&nbsp;|&nbsp;&nbsp;<?=$date_view?></td>
	</tr>
	<tr>
		<td class="contents">
			<?=$row->content?>
		</td>
	</tr>
</table>

<div class="btn_wirte">
	<a href="event_list.php"><input type="button" value="목록보기" /></a>
</div>
</section>
<script>
function ListNotice(block,gotopage,board){
	location.href="event_list.php";
}
</script>
</article>
</div>

</main>

<? include_once('outline/footer_m.php'); ?>