<?php 
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$productcode = $_GET['productcode'];
	$qry = "WHERE productcode='{$productcode}' ";
	if($_data->review_type=="A") $qry.= "AND display='Y' ";
	$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
	$sql.= $qry;
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$t_count_review = (int)$row->t_count;
	$totmarks = (int)$row->totmarks;
	if($totmarks > 0 && $t_count_review > 0){
		$avg_review = ceil((($totmarks*20)/$t_count_review*10))/10;
	}else{
		$avg_review = 0;
	}
?>	
	<p class="title">
		<span><?=$row->t_count?></span>건의 리뷰가 있습니다.
		<a href="" class="btn_more"></a>
	</p>
	<ul class="list">
<?php			
	$sqlReview = "SELECT * FROM tblproductreview {$qry} ORDER BY num DESC limit 3 offset 0";
	$resultReview=pmysql_query($sqlReview,get_db_conn());
	while($row=pmysql_fetch_object($resultReview)) {
		$colorStar = "";
		for($i=0;$i<$row->marks;$i++) {
			$colorStar .= "★";
		}
		$noColorStar = "";
		for($i=$row->marks;$i<5;$i++) {
			$noColorStar .= "★";
		}

		$rev_content = $row->content; 
		$rev_content = cutStringDot($rev_content, 32);
?>
		<li><a href=""><?=$rev_content?></a></li>
	<!--<ul class="right_review">
		<?if($row->upfile){?><p class="pic"></p><?}?>
		<li>
			<div class="star_color"><?=$colorStar?><span><?=$noColorStar?></span></div>
		</li>
		<li class="id"><?=$row->name?></li>
		<li class="content"><?=$rev_content?></li>
	</ul>-->
<?
	}
?>
	</ul>
	<!--ul class="right_review">
		<p class="pic"></p>
		<li>
			<div class="star_color">★★★★<span>★</span></div>
		</li>
		<li class="id">beabearion</li>
		<li class="content">얼마전 메일링을 받아 기획전을..</li>
	</ul>
	<ul class="right_review">
		<p class="pic"></p>
		<li>
			<div class="star_color">★★★★<span>★</span></div>
		</li>
		<li class="id">beabearion</li>
		<li class="content">얼마전 메일링을 받아 기획전을..</li>
	</ul-->
	<!--<div class="total_score">
		<dl>
			<dt><img src="../img/common/customer_score.gif" alt="" /></dt>
			<dd><?=$avg_review?> <img src="../img/common/jum.gif" alt="" /></dd>
		</dl>
	</div>-->