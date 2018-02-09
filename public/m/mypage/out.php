<?php
include_once('../outline/header.php')
?>
		<!-- 내용 -->
		<main id="content">

			<div class="sub-title">
				<h2>회원탈퇴</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>

			<div class="mypage-wrap member-out">
				
				<div class="att-title">소멸예정 내역</div>
				<div class="my-benfit">
					<p class="name"><strong>VIP 홍길동</strong> (hongkd) 님</p>
					<ul class="now">
						<li>할인쿠폰<strong>2</strong></li>
						<li>마일리지<strong>30,000 M</strong></li>
						<li>진행주문 건<strong>2</strong></li>
					</ul>
					<ul class="attention">
						<li>홍길동님은 현재 진행중인 주문건이 1건 있습니다.</li>
						<li>진행중인 주문이 완료 되어야 탈퇴처리 가능하십니다.</li>
					</ul>
				</div>

				<ul class="form-input">
					<li>
						<label for="reason">탈퇴사유</label>
						<div class="select-def">
							<select>
								<option value="1">탈퇴사유를 선택해주세요</option>
							</select>
						</div>
					</li>
					<li>
						<label for="out-content">내용</label>
						<textarea name="" id="out-content" cols="30" rows="10"></textarea>
					</li>
				</ul>

				<div class="btn-place"><a href="#" class="btn-def">회원탈퇴</a></div>

				<dl class="attention">
					<dt>회원탈퇴 전 확인사항</dt>
					<dd>회원탈퇴 시 회원님께서 보유 하셨던 마일리지, 쿠폰, 회원정보는 확인이 불가능합니다.</dd>
					<dd>거래정보가 있는 경우, 판매 거리 정보관리를 위하여 구매와 관련된 상품정보, 아이디, 거래내역 등에 대한 기본정보는 탈퇴 후 5년간 보관됩니다. </dd>
					<dd>회원탈퇴 후 재가입 시에는 신규 회원가입으로 처리되며 탈퇴 전 사용한 아이디는 다시 사용할 수 없습니다.</dd>
					<dd>진행중인 거래 내역이 있는 경우에는 즉시 탈퇴가 불가능하며, 거래종료 후 탈퇴가 가능합니다.</dd>
				</dl>
				
			</div><!-- //.mypage-wrap -->

			

		</main>
		<!-- // 내용 -->
<?php
include_once('../outline/footer.php')
?>