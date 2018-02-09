<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/forum.class.php");

$forum = new FORUM('main');

$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$cate_C = $forum->cate_C;
$recent_forum = $forum->recent_forum;
$recommend_forum = $forum->recommend_forum;
?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li class="on">FORUM</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner forum-wrap">
		<main class="forum-main-wrap">
			<h2>FORUM</h2>
			<section class="forum-content">
				<!-- 추천 포럼 -->
				<div class="recommend">
					<h3>추천포럼</h3>
				<?if($recommend_forum){?>
					<div class="recommend-rolling">

						<ul class="contbox">
						<?$key=0;?>
						<?foreach($recommend_forum as $r_val){?>
							<?if( ($key ==0)  || ($key % 2)==0 ){?>
							<li>
								<div class="cont-left">
							<?}else{?>
								<div class="cont-right">
							<?}?>
									<div class="title">
										<p><?=$r_val[0]->code_name?></p>
										<a href="/front/forum_list.php?forum_code=<?=$r_val[0]->code?>" class="more">더보기</a>
									</div>
									<!--<div class="forum-list">
										<a href="/front/forum_view.php?index=<?=$r_val[0]->index?>">
											<div class="photo"><img src="../static/img/test/@forum_list_img01.jpg" alt=""></div>
											<dl class="forum-cont">
												<dt><?=$r_val[0]->title?></dt>
												<dd class="ellipsis-line">
													<?=$r_val[0]->content?>
												</dd>
											</dl>
										</a>
									</div>-->
									<ul class="list-txt mt-20">
									<?for($i=1; $i<6; $i++){?>
										<li><a href="/front/forum_view.php?index=<?=$r_val[$i]->index?>"><?=$r_val[$i]->title?></a></li>
									<?}?>
									</ul>
								</div>

							<?if( ($key +1)% 2 ==0 ){?>
							</li>
							<?}?>
						<?$key++;?>
						<?}?>

						</ul>
					</div>
					<?}?>
				</div>
				<!-- // 추천 포럼 -->

				<!-- 최근 방문 포럼-->
				<div class="lately">
					<h3>최근 방문 포럼</h3>
					<div class="lately-wrap">
					<?if($recent_forum){?>
						<ul class="lately-forum">
						<?foreach($recent_forum as $val){?>
							<li><a href="/front/forum_list.php?forum_code=<?=$val->code_a.$val->code_b.$val->code_c?>"><?=$val->code_name?></a></li>
						<?}?>
						<!-- <li><a href="#">나이키</a></li> -->
						</ul>
					<?}else{?>
						<div class="no-forum">최근 방문 포럼이 없습니다.</div><!-- // [D] 데이터 없을시 노출-->
					<?}?>
					</div>
					<div class="btn-forum">
						<!--<span>개성있는 포럼을 신청해 보세요.</span>-->
						<a class="btn-type1 c1" href="/front/forum_request_list.php">포럼개설 신청</a>
					</div>
				</div>
				<!-- // 최근 방문 포럼-->
			</section>
			<div class="mt-20">
				<a href="javascript:;"><img src="@forum_banner_img.jpg" alt=""></a>
			</div>

			<section class="forum-category">
			<?if($cate_A){?>
				<ul>
				<?foreach($cate_A as $val){?>
					<li>
						<h4><?=$val->code_name?></h4>
						<?foreach($cate_B[$val->code_a] as $val2){?>
						<dl>
							<dt><a><?=$val2->code_name?></a></dt>
							<?foreach($cate_C[$val2->code_a.$val2->code_b] as $val3){?>
								<dd>
									<a href="/front/forum_list.php?forum_code=<?=$val3->code_a.$val3->code_b.$val3->code_c?>">
										<?=$val3->code_name?><span>(<?=$val3->l_count?>)</span>
									</a>
								</dd>
							<?}?>
						</dl>
						<?}?>
					</li>
				<?}?>
				<!--
					<li>
						<h4>일상/문화</h4>
						<dl>
							<dt><a href="#">음식</a></dt>
							<dd><a href="#">맛집<span>(12)</span></a></dd>
							<dd><a href="#">다이어트<span>(10)</span></a></dd>
							<dd><a href="#">요리<span>(0)</span></a></dd>
							<dd><a href="#">커피<span>(120)</span></a></dd>
							<dd><a href="#">주당<span>(0)</span></a></dd>
							<dd><a href="#">안익음식이 좋다<span>(120)</span></a></dd>
							<dd><a href="#">빵<span>(0)</span></a></dd>
						</dl>
					</li>
				-->
				</ul>
			<?}?>
			</section>
		</main>
	</div>
</div>

<?php
include ($Dir."lib/bottom.php")
?>
