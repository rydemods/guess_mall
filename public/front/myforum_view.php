<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="../front/mypage.php">마이 페이지</a></li>
			<li class="on">나의 포럼</li>
		</ul>
	</div>
	<!-- // 네비게이션 -->
	<div class="inner">
		<main class="mypage_wrap board-list-wrap view"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->
			<article class="mypage_content">
				<section class="mypage_main">

				<div class="title_type1">
					<h3>나의 포럼</h3>
				</div>

				<div class="list-wrap">
					<table class="th_left">
					<caption></caption>
					<colgroup>
						<col style="width:160px">
						<col style="width:325px">
						<col style="width:160px">
						<col style="width:auto">
					</colgroup>
					<tbody>
						<tr>
							<th scope="row">카테고리</th>
							<td colspan="3">맛집</td>
						</tr>
						<tr>
							<th scope="row">제목</th>
							<td colspan="3" class="subject">올림픽 카테고리를 만들면 어떨까요? 여러분들,, 댓글좀 달아주세요. 아자!</td>
						</tr>
						<tr>
							<th scope="row">글쓴이</th>
							<td colspan="3" class="write">
							fisdvhs01
							<div class="hits"><span class="date mr-10">2016.07.01  15:28 </span> 조회수&nbsp; | &nbsp;28</div>
							</td>
						</tr>
						<tr>
							<td colspan="4">
								<div class="cont-box">
									<p><img src="../static/img/test/@forum_cont_img01.jpg" alt=""></p>
									<p>MILSPECTOR는 1940년대의 군 활동화에 기반들 두고 만들어져 마찰이나 열에 쉽게 손상되지 않는 내구성이 강한 코듀라(CORDURA)를 사용해 제작되었다.<br>
									이는 가볍고 부드러워 빨리 건조가 되며, 오랜 기간 사용해도 쉽게 변색이 되지 않는 것이 특징이다. 스니커 앞부분에는 흙이나 기타 이물질이 들어오는 것을 막아주는<br>
									개념으로 킬티(Kilt)로 장식한 것이 눈에 띈다. 또한 옛날 스니커 제작 방식이었던 벌커나이즈드(Vulcanized) 제법으로 제작되어 어퍼와 아웃솔의 결합이 뛰어나<br>
									모양이 쉽게 변하지 않는다. 이는 요즘 출시되는 본드 접착 방식의 스니커즈들과는 다른 뛰어난 착화감과 내구성을 보여준다.</p>
								</div>
								<div class="ta-c mb-20"><button type="button" class="like_m comp-like btn-like" title="선택 안됨"><span><strong>좋아요</strong>55</span></button></div>
							</td>
						</tr>
					</tbody>
					</table>
					<div class="tag-wrap">
						<div class="hero-info-tag">
							<h4>TAG</h4>
							<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
							<ul>
								<li><a href="javascript:;">MEN</a></li>
								<li><a href="javascript:;"> 신발</a></li>
								<li><a href="javascript:;"> WHITE</a></li>
								<li><a href="javascript:;"> 단화</a></li>

							</ul>
						</div>

						<div class="hero-info-share">
							<ul>
								<li><a href="javascript:;" id="facebook-link"><img src="../static/img/btn/btn_share_facebook.png" alt="페이스북으로 공유"></a></li>
								<li><a href="javascript:;" id="twitter-link"><img src="../static/img/btn/btn_share_twitter.png" alt="트위터로 공유"></a></li>
								<li><a href="javascript:;" id="band-link"><img src="../static/img/btn/btn_share_blogger.png" alt="밴드로 공유"></a></li>
								<!-- <li><a href="javascript:;"><img src="../static/img/btn/btn_share_instagram.png" alt="인스타그램으로 공유"></a></li> -->
								<li><a href="javascript:kakaoStory();" id="kakaostory-link"><img src="../static/img/btn/btn_share_kakaostory.png" alt="카카오스토리로 공유"></a></li>
								<li><a href="javascript:ClipCopy('http://test-hott.ajashop.co.kr/front/magazine_detail.php?no=7');">URL</a></li>
							</ul>
						</div>
					</div>

					<section class="goods-detail-review">
						<h5>댓글<span>(20)</span></h5>
						<table class="board">
							<caption>리뷰게시판</caption>
							<tbody class="on">
								<tr>
									<td>
										<div class="reply_wrap icon">
											<div class="reply-reg-box">
												<form>
													<legend>리뷰에 댓글 작성</legend>
													<div class="review_comment_form">
														<textarea name="review_comment"></textarea>
														<div class="btn_review_write review-comment-write"><a href="javascript:;">입력</a></div>
														<div class="txt-r">0/<em>300</em></div>
														<!-- <center><button class="btn-type1 review-comment-write" type="submit">OK</button></center> -->
													</div>
												</form>
												<p>* 20자 이상 입력해 주세요. </p>
												<p>* 로그인후 작성하실 수 있습니다.</p>
											</div>
											<div class="reply_comment" id="">
												<div class="answer">
													<span class="name"><i><img src="../static/img/test/@test_myicon01.gif"></i>web_classic@naver.com(2016-08-31)</span>
													<p>테스트에요 테스트</p>
													<div class="btn-feeling mt-5">
														<a class="btn-good-feeling on" href="#">15</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
														<a href="#" class="btn-bad-feeling">0</a>
													</div>
													<div class="buttonset">
														<a href="javascript:;">댓글</a>
														<a href="javascript:;">삭제</a>
													</div>
												</div>
												<div class="answer">
													<span class="name"><i><img src="../static/img/test/@test_myicon02.gif"></i>web_classic@naver.com(2016-08-31)</span>
													<p>테스트에요 테스트</p>
													<div class="btn-feeling mt-5">
														<a class="btn-good-feeling on" href="#">15</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
														<a href="#" class="btn-bad-feeling">0</a>
													</div>
													<div class="buttonset">
														<a href="javascript:;">댓글</a>
													</div>
												</div>

												<!-- [D] re댓글 시작 -->
												<div class="re-reply-wrap">
													<div class="reply-reg-box bor-t">
														<form>
															<legend>리뷰에 댓글 작성</legend>
															<div class="review_comment_form"><textarea name="review_comment"></textarea>
																<div class="btn_review_write review-comment-write">
																	<a href="javascript:;">입력</a>
																	<a href="javascript:;" class="cancel">취소</a>
																</div>
																<!-- <center><button class="btn-type1 review-comment-write" type="submit">OK</button></center> -->
																<div class="txt-r">0/<em>300</em></div>
															</div>
														</form>
													</div>
													<div class="re-reply">
														<div class="answer reply"> <!-- // [D] 댓글에 댓글의 경우 .reply 클래스 추가 -->
															<span class="name"><i><img src="../static/img/test/@test_myicon02.gif"></i>web_classic@naver.com(2016-08-31)</span>
															<p>테스트에요 테스트</p>
															<div class="btn-feeling mt-5">
																<a class="btn-good-feeling on" href="#">15</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
																<a href="#" class="btn-bad-feeling">0</a>
															</div>
														</div>
														<div class="answer reply"> <!-- // [D] 댓글에 댓글의 경우 .reply 클래스 추가 -->
															<span class="name"><i><img src="../static/img/test/@test_myicon01.gif"></i>web_classic@naver.com(2016-08-31)</span>
															<p>테스트에요 테스트</p>
															<div class="btn-feeling mt-5">
																<a class="btn-good-feeling on" href="#">15</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
																<a href="#" class="btn-bad-feeling">0</a>
															</div>
														</div>
													</div>
													<div class="list-paginate mb-20">
														<span class="border_wrap">
															<a href="javascript:;" class="prev-all"></a>
															<a href="javascript:;" class="prev"></a>
														</span>
														<a class="on">1</a>
														<span class="border_wrap">
															<a href="javascript:;" class="next"></a>
															<a href="javascript:;" class="next-all"></a>
														</span>
													</div>
												</div>
												<!-- // [D] re댓글 시작 -->

												<div class="answer">
													<span class="name"><i><img src="../static/img/test/@test_myicon01.gif"></i>web_classic@naver.com(2016-08-31)</span>
													<p>테스트에요 테스트</p>
													<div class="btn-feeling mt-5">
														<a class="btn-good-feeling on" href="#">15</a> <!-- // [D] 버튼 선택시 on클래스 추가 -->
														<a href="#" class="btn-bad-feeling">0</a>
													</div>
													<div class="buttonset">
														<a href="javascript:;">댓글</a>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<span class="border_wrap">
								<a href="javascript:;" class="prev-all"></a>
								<a href="javascript:;" class="prev"></a>
							</span>
							<a class="on">1</a>
							<span class="border_wrap">
								<a href="javascript:;" class="next"></a>
								<a href="javascript:;" class="next-all"></a>
							</span>
						</div>
					</section>
					<div class="btn_wrap">
						<a href="#" class="btn-type1">목록</a>
						<a href="#" class="btn-type1 c1">수정</a>
						<a href="#" class="btn-type1">삭제</a>
					</div>
				</div>

			</section>
		</article>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->



<?php
include ($Dir."lib/bottom.php")
?>
