<?php
include_once('../outline/header.php')
?>

		<!-- 내용 -->
		<main id="content">
			
			<div class="sub-title">
				<h2>CS CENTER</h2>
				<a class="btn-prev" href="#"><img src="../static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
			</div>
			
			<div class="js-tab-component">
				<div class="content-tab">
					<div class="js-menu-list">
						<div class="js-tab-line"></div>
						<ul>
							<li class="js-tab-menu"><a href="#"><span>공지사항</span></a></li>
							<li class="js-tab-menu"><a href="#"><span>1:1상담</span></a></li>
							<li class="js-tab-menu on"><a href="#"><span>FAQ</span></a></li>
						</ul>
					</div>
				</div>
				
				<div class="js-tab-content cs-notice-wrap">
					<table class="th-top">
						<caption>공지사항 리스트</caption>
						<colgroup>
							<col style="width:45px">
							<col style="width:auto">
							<col style="width:90px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">NO</th>
								<th scope="col">1:1상담</th>
								<th scope="col">FAQ</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>5</td>
								<td class="subject"><a href="noticeView.php">내부시스템 작업으로 사이트 일시 중단안내</a></td>
								<td>2016-02-25</td>
							</tr>
							<tr>
								<td>4</td>
								<td class="subject"><a href="noticeView.php">내부시스템 작업으로 사이트 일시 중단안내</a></td>
								<td>2016-02-25</td>
							</tr>
							<tr>
								<td>3</td>
								<td class="subject"><a href="noticeView.php">내부시스템 작업으로 사이트 일시 중단안내</a></td>
								<td>2016-02-25</td>
							</tr>
							<tr>
								<td>2</td>
								<td class="subject"><a href="noticeView.php">내부시스템 작업으로 사이트 일시 중단안내</a></td>
								<td>2016-02-25</td>
							</tr>
							<tr>
								<td>1</td>
								<td class="subject"><a href="noticeView.php">내부시스템 작업으로 사이트 일시 중단안내</a></td>
								<td>2016-02-25</td>
							</tr>
						</tbody>
					</table>
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
				</div><!-- //공지사항 -->

				<div class="js-tab-content goods-qna-list cs">
					<h3 class="title">고객님의 궁금하신 사항을 신속하게 답변해 드리겠습니다.</h3>
					
					<!-- 내역 없는경우 -->
					<div class="none-ment hide">
						<p>문의하신 내역이 없습니다.</p>
					</div><!-- //내역 없는경우 -->
					
					<ul class="js-goods-qna-accordion">
						<li>
							<dl>
								<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
									<button type="button" title="펼쳐보기">
										<span class="qna-title">소재문의 - 소재가 모직으로 되어 있는데 몇프로 인가요?<img class="ico-lock" src="../static/img/icon/ico_lock_open.png" alt="내가 쓴 비밀글"></span>
										<span class="qna-id">&nbsp;</span>
										<span class="box">
											<span class="qna-date">2016-01-31</span>
											<span class="qna-condition"><strong>답변완료</strong></span>
										</span>
									</button>
								</dt>
								<dd class="js-goods-qna-accordion-content">
									<p class="qna-question">소재가 모직으로 되어 있는데 몇프로 인가요?</p>
									<p class="qna-answer">
										안녕하세요 c.a.s.h 담당자 입니다.<br>
										모직 100%로 되어 있습니다.<br>
										구매에 참고 해주세요!
									</p>
								</dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
									<button type="button" title="펼쳐보기">
										<span class="qna-title">배송문의<img class="ico-lock" src="../static/img/icon/ico_lock_close.png" alt="비밀글"></span>
										<span class="qna-id">&nbsp;</span>
										<span class="box">
											<span class="qna-date">2016-01-31</span>
											<span class="qna-condition">답변 전</span>
										</span>
									</button>
								</dt>
								<dd class="js-goods-qna-accordion-content">
									<p class="qna-question">소재가 모직으로 되어 있는데 몇프로 인가요?</p>
								</dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
									<button type="button" title="펼쳐보기">
										<span class="qna-title">결제문의</span>
										<span class="qna-id">&nbsp;</span>
										<span class="box">
											<span class="qna-date">2016-01-31</span>
											<span class="qna-condition">답변 전</span>
										</span>
									</button>
								</dt>
								<dd class="js-goods-qna-accordion-content">
									<p class="qna-question">소재가 모직으로 되어 있는데 몇프로 인가요?</p>
								</dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
									<button type="button" title="펼쳐보기">
										<span class="qna-title">결제문의</span>
										<span class="qna-id">&nbsp;</span>
										<span class="box">
											<span class="qna-date">2016-01-31</span>
											<span class="qna-condition"><strong>답변완료</strong></span>
										</span>
									</button>
								</dt>
								<dd class="js-goods-qna-accordion-content">
									<p class="qna-question">소재가 모직으로 되어 있는데 몇프로 인가요?</p>
									<p class="qna-answer">
										안녕하세요 c.a.s.h 담당자 입니다.<br>
										모직 100%로 되어 있습니다.<br>
										구매에 참고 해주세요!
									</p>
								</dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt class="js-goods-qna-accordion-menu" title="펼쳐보기">
									<button type="button" title="펼쳐보기">
										<span class="qna-title">결제문의</span>
										<span class="qna-id">&nbsp;</span>
										<span class="box">
											<span class="qna-date">2016-01-31</span>
											<span class="qna-condition"><strong>답변완료</strong></span>
										</span>
									</button>
								</dt>
								<dd class="js-goods-qna-accordion-content">
									<p class="qna-question">소재가 모직으로 되어 있는데 몇프로 인가요?</p>
									<p class="qna-answer">
										안녕하세요 c.a.s.h 담당자 입니다.<br>
										모직 100%로 되어 있습니다.<br>
										구매에 참고 해주세요!
									</p>
								</dd>
							</dl>
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
					<div class="btnwrap">
						<div class="box">
							<a class="btn-def" href="csQnaWrite.php">1:1상담</a>
						</div>
					</div>
				</div><!-- //1:1상담 -->

				<div class="js-tab-content faq-wrap">
					<h4 class="title">고객님들의 자주 묻는 질문을 확인하세요!</h4>
					<ul class="faq-category">
						<li><a href="#" class="on">전체</a></li>
						<li><a href="#">상품</a></li>
						<li><a href="#">주문/결제</a></li>
						<li><a href="#">배송관련</a></li>
						<li><a href="#">취소/환불</a></li>
						<li><a href="#">반품/교환</a></li>
						<li><a href="#">회원서비스</a></li>
						<li><a href="#">회원가입/탈퇴</a></li>
						<li><a href="#">기타</a></li>
					</ul>
					<form class="faq-search">
						<fieldset>
							<legend>자주묻는 질문 검색</legend>
							<div class="input-cover"><input type="search" title="검색어 입력자리"></div>
							<button class="btn-def" type="submit"><span>검색</span></button>
						</fieldset>
					</form>
					<ul class="js-faq-accordion">
						<li>
							<dl>
								<dt class="js-faq-accordion-menu">회원정보를 변경 하고 싶어요</dt>
								<dd class="js-faq-accordion-content">웹사이트 페이지 상단 / 회원정보 수정 모바일 페이지 하단 마이페이지 사용자 설정에서 터치하면 수정이 가능합니다.</dd>
							</dl>
						</li>
						<li>
							<dl>
								<dt class="js-faq-accordion-menu">모바일 결제가 잘 안되요! 어디서 어떻게 해야 되는 검미까?</dt>
								<dd class="js-faq-accordion-content">웹사이트 페이지 상단 / 회원정보 수정 모바일 페이지 하단 마이페이지 사용자 설정에서 터치하면 수정이 가능합니다.</dd>
							</dl>
						</li>
					</ul>
				</div><!-- //FAQ -->
			</div><!-- //.js-tab-component -->
			
			
		</main>
		<!-- // 내용 -->

<?php
include_once('../outline/footer.php')
?>