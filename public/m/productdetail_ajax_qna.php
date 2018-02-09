<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$pridx = $_REQUEST['pridx'];
$page = $_REQUEST['page'];
//$productcode = $_REQUEST['productcode'];

//Q&A카운트
//$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc ";
$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc ";
$qnares = pmysql_query($qnasql,get_mdb_conn());
$qnacount = pmysql_num_rows($qnares );

//Q&A리스크
$qnalist_num = 5;
$qnaoffset = ($page-1) * $qnalist_num;
$qnatotalpage = ceil($qnacount / $qnalist_num);
//$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc limit $qnalist_num offset $qnaoffset ";
$qnasql = "SELECT * FROM tblboard WHERE board = 'qna' and pridx = '".$pridx."' and pos = '0' and depth = '0' ORDER BY num desc limit $qnalist_num offset $qnaoffset ";
$qnares = pmysql_query($qnasql,get_mdb_conn());
while($qnarow = pmysql_fetch_object($qnares)){
	$writetime = date("Y/m/d", $qnarow->writetime);
	$qnarow->writetime = $writetime;
	$qnaloop[] = $qnarow;
}

?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<? if($qnacount>0) : ?>
	<ul class="review">
		<?foreach($qnaloop as $qna){?>
			<?
				list($qnaCount)=pmysql_fetch("SELECT count(num) FROM tblboardcomment WHERE board = 'qna' and parent = '".$qna->num."'");
			?>
		<li>
			<a class="title" href="#" title="펼쳐보기">
				<span class="id"><?=$qna->name?> (<?=$qna->writetime?>)</span>
				<div class="title">
					<?=$qna->title?>
			<? if( $qna->is_secret == '1' ){ ?>
					<img src="../img/icon/icon_reply_secret.gif" alt="비밀글" style='width: 15px;' >
			<?	} ?>
				</div>
				<div class="process">답변</div>
			</a>
<?php
				if( $qna->is_secret == '0' || $_ShopInfo->getmemid() == $qna->mem_id || $_ShopInfo->getId() ){
?>
			<div class="content">
				<p class="ques">
					<?=nl2br($qna->content)?>
				</p>
				<?
					$qna_reply_sql = "SELECT * FROM tblboardcomment WHERE board = 'qna' and parent = '".$qna->num."' order by num desc";
					$qna_reply_res = pmysql_query($qna_reply_sql,get_mdb_conn());
					while($qna_reply_row = pmysql_fetch_object($qna_reply_res)){
				?>
				<p class="answer">
					<span class="date"><strong>답변</strong> (<?=date("Y/m/d",$qna_reply_row->writetime)?>)</span>
					<?=nl2br($qna_reply_row->comment)?>
				</p>
				<?
					}
				?>
			</div>
<?php
				}
?>
		</li>
		<?}?>
	</ul>
	<div class="page_num">
	<? if($page > 1) : ?>
		<a  href="javascript:qnaPage('<?=$page-1?>');"  class="btn prev">이전</a>
	<? endif;?>
		<span><?=$page?> / <?=$qnatotalpage?></span>
	<? if($page != $qnatotalpage ) : ?>
		<a  href="javascript:qnaPage('<?=$page+1?>');"  class="btn next">다음</a>
	<? endif;?>
	</div>
<? endif; ?>









