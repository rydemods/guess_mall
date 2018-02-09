<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>상품 Q&#38;A</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<p class="goods-qna-note">
				고객님의 문의에 최대한 빨리 답변 드리도록 하겠습니다.<br>
				질문에 대한 답변은 마이페이지에서도 확인 하실 수 있습니다.
			</p>
			
			<!-- Q&A 글쓰기 -->
			<div class="goods-qna-write">
				<section class="write-open">
					<h3>공개여부</h3>
					<label><input class="radio-def" type="radio" name="radio-open" checked><span>공개</span></label>
					<label><input class="radio-def" type="radio" name="radio-open"><span>비공개</span></label>
				</section>
				<section class="write-pw">
					<h3>비밀번호</h3>
					<input type="password">
				</section>
				<section class="write-title">
					<h3>제목</h3>
					<input type="text" placeholder="제목을 입력하세요" title="제목">
				</section>
				<section class="write-content">
					<h3>내용</h3>
					<textarea placeholder="내용을 입력하세요" title="내용"></textarea>
				</section>
				<p class="write-note">
					상품에 관한문의만 작성해주세요<br>
					배송,결제,교환/반품에 대한 문의는 1:1문의를 이용해주세요
				</p>
				<div class="btnwrap">
					<div class="box">
						<a class="btn-def" href="#">등록</a>
						<a class="btn-def" href="#">목록</a>
					</div>
				</div>
			</div>
			<!-- // Q&A 글쓰기 -->
			
		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>