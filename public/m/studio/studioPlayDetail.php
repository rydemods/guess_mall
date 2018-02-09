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
			
			<!-- 플레이더스타 내용 -->
			<article class="studio-play-detail-content">
				<div class="studio-play-title">
					<h3>C.A.S.H ART COLLABORATION</h3>
					<button class="btn-share" onclick="popup_open('#popup-sns');return false;"><span class="ir-blind">공유</span></button>
				</div>
				<div class="studio-play-content-inner">
					<img src="../static/img/test/@studio_play_detail_content.jpg" alt="休, 일상의 여유가 필요해. photo by 박병근 - 비도 오고 우울한날, 집안에서 나 혼자만 시간이 멈춘듯하다.">
				</div>
			</article>
			<!-- // 플레이더스타 내용 -->
			
			<div class="btnwrap studio-play-detail-btn">
				<div class="box">
					<a class="btn-def" href="#">이전</a>
					<a class="btn-def" href="#">목록</a>
					<a class="btn-def" href="#">다음</a>
				</div>
			</div>
			
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>