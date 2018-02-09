<?php
$subTitle = "고객센터";
include_once('outline/header_m.php');

$faq_type=$_GET[faq_type];

if($faq_type!='')$faq_on[$faq_type]="class='on'";
else $faq_on_total="class='on'";

if($faq_type!='')$where[]=" faq_type='{$faq_type}'";
$where[]=" b.secret='1'";
//공지사항
$sql="select COUNT(*) as t_count from tblfaq a left join tblfaqcategory b on (a.faq_type=b.num)";
if($where)$sql.="where".implode(" and ",$where);
$paging = new Paging($sql);
$t_count = $paging->t_count;

$sql = "select * from tblfaq a left join tblfaqcategory b on (a.faq_type=b.num) ";
if($where)$sql.="where".implode(" and ",$where);
$sql.=" order by a.date desc";
$sql = $paging->getSql($sql);

$result = pmysql_query($sql,get_db_conn());
$cnt=0;

##카테고리 쿼리
$cate_qry="select * from tblfaqcategory where secret='1' order by sort_num";
$cate_result=pmysql_query($cate_qry);

?>
<main id="content" class="subpage">

<article class="mypage">
   <ul class="mypage_gnb">
	<li><a href="board.php?board=notice">공지사항</a></li>
	<li class="on">FAQ</li>
<?php if($_data->personal_ok=="Y")	:	?>
	<li><a href="mypage_personal.php">1:1문의</a></li>
<?php endif;	?>
   </ul>
 <section class="customer">
<h3>FAQ</h3>
	<div class="select">
<form name=frmtype method=get action="<?=$_SERVER['PHP_SELF']?>">
		<select id="faq_type" name="faq_type" onchange="javascript:faq();">
			<option value="" <?if($faq_type == "") : ?> selected <?endif;?> >전체</option>
<?php
while($cate_data = pmysql_fetch_object($cate_result)){
?>
			<option value="<?=$cate_data->num?>" <?if($faq_type == $cate_data->num) : ?> selected <?endif;?> ><?=$cate_data->faq_category_name?></option>
<?}?>
		</select>
</form>
	</div>

	<ul class="faq_wrap">
<?php if($t_count > 0 ) :?>
<?php
while($data = pmysql_fetch_object($result)){
?>
		<li>
		<a onclick="javascript:answer('<?=$data->no?>');"><span class="faq_q">Q</span><?=$data->faq_title?></a>
		<div id="answer<?=$data->no?>" class="answer" style="display:none;">
		<span class="faq_a">A</span><?=$data->faq_content?>
		</div>
		</li>
<?}?>
<?	else : ?>
		<li>
		<a align="center"> 게시물이 존재하지 않습니다</a>
		</li>
<?	endif; ?>

	</ul>
</section>

</article>
</div>
</main>

<SCRIPT>
function faq(){
	document.frmtype.submit();
}
function answer(no){
	$(".answer").hide('fast');
	$("#answer"+no).toggle('fast');
}
</SCRIPT>

<? include_once('outline/footer_m.php'); ?>