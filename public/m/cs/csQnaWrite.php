<?php
include_once('../outline/header.php')
?>

		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>1:1상담</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<div class="cs-qna-wrap">
				<h3 class="title">고객님의 궁금하신 사항을 신속하게 답변해 드리겠습니다.</h3>

				<!-- 사진 업로드 작성 -->
				<div class="form_photo_write">
					<section class="with-select">
						<h3>상담유형</h3>
						<div class="select-def">
							<select>
								<option value="1">배송관련</option>
							</select>
						</div>
					</section>
					<section class="with-select">
						<h3>관련상품</h3>
						<div class="select-def">
							<select>
								<option value="1">구매한 상품을 선택 해주세요</option>
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
					<section class="phone">
						<h3>핸드폰번호</h3>
						<div class="tel-input">
							<div class="select-def">
								<select>
									<option value="1">010</option>
								</select>
							</div>
							<div><input type="tel" id="join-tel"></div>
							<div><input type="tel"></div>
						</div>
						<div class="replay-sms">
							<label for="reply-sms">핸드폰으로 답변받음</label>
							<input type="checkbox" id="reply-sms">
						</div>
					</section>
					<div class="btnwrap">
						<div class="box">
							<a class="btn-def" href="#">등록</a>
							<a class="btn-def" href="#">취소</a>
						</div>
					</div>
				</div>
				<!-- // 사진 업로드 작성 -->
			</div>
			
			
		</main>
		<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>