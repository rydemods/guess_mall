<?php include_once('outline/header_m.php'); ?>

<?
include_once($Dir."lib/forum.class.php");
$forum = new FORUM('main');

$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$cate_C = $forum->cate_C;
$recent_forum = $forum->recent_forum;
$recommend_forum = $forum->recommend_forum;
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>포럼</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="mypage_sub">
    <h3 class="h3_forum">추천포럼</h3>
	<div class="wrap_rcmd_forum">
	<?if($recommend_forum){?>
		<ul class="rcmd_forum">
			<!-- 반복 -->
			<?foreach($recommend_forum as $r_val){?>
			<li>
				<h4><?=$r_val[0]->code_name?><a href="/m/forum_list.php?forum_code=<?=$r_val[0]->code?>" class="more">더보기</a></h4>
				<!-- <div class="preview">
					<a href="/m/forum_view.php?index=<?=$r_val[0]->index?>">
						<img src="static/img/test/@forum_img01.jpg" alt="">
						<p class="tit"><?=$r_val[0]->title?></p>
						<p class="txt"><?=$r_val[0]->content?></p>
					</a>
				</div> --><!-- [D] 2016-09-27 요청으로 삭제 -->
				<ul class="list">
				<?for($i=1; $i<6; $i++){?>
					<li><a href="/m/forum_view.php?index=<?=$r_val[$i]->index?>"><?=$r_val[$i]->title?></a></li>
				<?}?>
				</ul>
			</li>
			<?}?>
			<!-- //반복 -->

		</ul><!-- //.rcmd_forum -->
		<?}?>
	</div><!-- //.wrap_rcmd_forum -->
<?if($recent_forum){?>
	<div class="wrap_recent_forum">
		<h4>최근 방문 포럼</h4>
		<ul class="recent_forum">
			<!-- 반복 -->
		<?foreach($recent_forum as $key=>$val){?>
			<?if( $key==0 || ($key % 4)==0 ) {?>
			<li>
				<ul class="list clear">
			<?}?>
					<li><a href="/m/forum_list.php?forum_code=<?=$val->code_a.$val->code_b.$val->code_c?>"><img src="static/img/icon/icon_recent_forum.png" alt="icon"><?=$val->code_name?></a></li>
			<?if( ($key+1)%4 == 0){?>
				</ul>
			</li>
			<?}?>
		<?}?>
			<!-- //반복 -->
			
		</ul><!-- //.recent_forum -->
	</div><!-- //.wrap_recent_forum -->
<?}else{?>
	<!-- 최근 방문 포럼이 없는 경우 -->
	<div class="wrap_recent_forum">
		<h4>최근 방문 포럼</h4>
		<div class="recent_forum_none">
			<img src="static/img/icon/icon_forum_none.png" alt="icon">
			<span>최근 방문 포럼이 없습니다.</span>
		</div>
	</div><!-- //.wrap_recent_forum -->
	<!-- //최근 방문 포럼이 없는 경우 -->
<?}?>
	<div class="btn_apply_forum">
		<div class="ment">
			<img src="static/img/icon/icon_forum_apply.png" alt="icon">
			<span>개성있는 포럼을 신청해 보세요.</span>
		</div>
		<button type="button" class="btn-point"><a href="/m/forum_apply_list.php">포럼개설 신청</a></button>
	</div><!-- //.btn_apply_forum -->

	<div class="wrap_category_forum">
		<h3 class="h3_forum">포럼 카테고리</h3>
		<ul class="category_forum">
		<?foreach($cate_A as $val){?>
			<li>
				<button type="button" class="depth1"><?=$val->code_name?></button><!-- [D] 포럼 카테고리 1depth -->
				<div class="depth1_sub">
				<?foreach($cate_B[$val->code_a] as $val2){?>
					<p class="depth2"><?=$val2->code_name?></p><!-- [D] 포럼 카테고리 2depth -->
					<ul class="depth3"><!-- [D] 포럼 카테고리 3depth -->
					<?foreach($cate_C[$val2->code_a.$val2->code_b] as $val3){?>
						<li><a href="/m/forum_list.php?forum_code=<?=$val3->code_a.$val3->code_b.$val3->code_c?>"><p><?=$val3->code_name?><span class="point-color">(<?=$val3->l_count?>)</span></p></a></li>
					<?}?>
					</ul><!-- //.depth3 -->
				<?}?>
				</div><!-- //.depth1_sub -->
			</li>
		<?}?>
		</ul><!-- //.category_forum -->
	</div><!-- //.wrap_category_forum -->

	<div class="banner_forum hide"><a href="#"><img src="/front/@forum_banner_img.jpg" alt="배너"></a></div>

</div><!-- //.mypage_sub -->

<? include_once('outline/footer_m.php'); ?>
