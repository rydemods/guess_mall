<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>상품리뷰작성</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<!-- 리뷰작성 -->
			<div class="form_photo_write goods-review-write">
				<section class="write-item">
					<h3>상품</h3>
					<span>2WAYS DOWN JUMPER 겨울 자켓 이로더</span>
				</section>
				<section class="write-title">
					<h3>제목</h3>
					<input type="text" placeholder="제목을 입력하세요" title="제목">
				</section>
				<section class="write-score">
					<h3>별점</h3>
					<div class="select-def">
						<select>
							<option value="5" title="별점 5점만점에 5점">★★★★★</option>
							<option value="4" title="별점 5점만점에 4점">★★★★</option>
							<option value="3" title="별점 5점만점에 3점">★★★</option>
							<option value="2" title="별점 5점만점에 2점">★★</option>
							<option value="1" title="별점 5점만점에 1점">★</option>
						</select>
					</div>
				</section>
				<section class="write-content">
					<h3>내용</h3>
					<textarea placeholder="내용을 입력하세요" title="내용"></textarea>
				</section>
				<section class="write-upload">
					<h3>이미지등록 (최대4장)</h3>
					<ul>
						<li><label><span>이미지등록</span><input type="file"></label></li>
						<li><label><span>이미지등록</span><input type="file"></label></li>
						<li><label><span>이미지등록</span><input type="file"></label></li>
						<li><label><span>이미지등록</span><input type="file"></label></li>
					</ul>
					<p class="note">파일명: 한글,영문,숫자 / 용량: 5M이하 / 파일형식: GIF,JPG</p>
				</section>
				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="#">등록</a>
						<a class="btn-def" href="#">취소</a>
					</div>
				</div>
			</div>
			<!-- // 리뷰작성 -->
			
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>