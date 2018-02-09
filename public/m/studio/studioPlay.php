<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>PLAY THE STAR</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
				<div class="js-sub-menu">
					<button class="js-btn-toggle" title="펼쳐보기"><img src="../static/img/btn/btn_arrow_down.png" alt="메뉴"></button>
					<div class="js-menu-content">
						<ul>
							<li><a href="#">LOOKBOOK</a></li>
							<li><a href="#">PRESS</a></li>
							<li><a href="#">스타가 되고싶니</a></li>
							<li><a href="#">PLAY THE STAR</a></li>
							<li><a href="#">SNS</a></li>
						</ul>
					</div>
				</div>
			</div>
			
			<!-- 플레이더스타 리스트 -->
			<div class="studio-play-list">
				<ul>
					<li>
						<a class="btn-detail" href="#">
							<figure>
								<img src="../static/img/test/@studio_play_list1.jpg" alt="">
								<figcaption>WE ARE # 스타가되고싶니</figcaption>
							</figure>
						</a>
					</li>
					<li>
						<a class="btn-detail" href="#">
							<figure>
								<img src="../static/img/test/@studio_play_list2.jpg" alt="">
								<figcaption>#스타가되고싶니 컨테스트</figcaption>
							</figure>
						</a>
					</li>
					<li>
						<a class="btn-detail" href="#">
							<figure>
								<img src="../static/img/test/@studio_play_list3.jpg" alt="">
								<figcaption>PLAY THE STAR</figcaption>
							</figure>
						</a>
					</li>
					<li>
						<a class="btn-detail" href="#">
							<figure>
								<img src="../static/img/test/@studio_play_list4.jpg" alt="">
								<figcaption>[ACC] MEN 60% SALE!</figcaption>
							</figure>
						</a>
					</li>
				</ul>
				<div class="paginate">
					<div class="box">
						<a class="btn-page-first" href="#"><span class="ir-blind">처음</span></a>
						<a class="btn-page-prev" href="#"><span class="ir-blind">이전</span></a>
						<ul>
							<li class="on" title="선택됨"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
						</ul>
						<a class="btn-page-next" href="#"><span class="ir-blind">다음</span></a>
						<a class="btn-page-last" href="#"><span class="ir-blind">마지막</span></a>
					</div>
				</div>
			</div>
			<!-- // 플레이더스타 리스트 -->
			
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>