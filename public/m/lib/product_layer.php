<!-- 상세 > 리뷰 리스트 -->
<div class="layer-dimm-wrap goodsReview-list">
	<div class="layer-inner">
		<h2 class="layer-title">리뷰</h2>
		<div class="popup-summary"><p>고객님의 소중한 후기를 남겨주시기 바랍니다.</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="review-point mt-10">
				<div class="icon">
					<!-- <img src="/sinwon/web/static/img/icon/rating1.png" alt="5점 만점 중 1점">
					<img src="/sinwon/web/static/img/icon/rating2.png" alt="5점 만점 중 2점">
					<img src="/sinwon/web/static/img/icon/rating3.png" alt="5점 만점 중 3점">
					<img src="/sinwon/web/static/img/icon/rating4.png" alt="5점 만점 중 4점"> -->
					<img src="/sinwon/web/static/img/icon/rating5.png" alt="5점 만점 중 5점">
				</div>
				<span class="point-num">5.0</span>
				<button class="btn-point h-large" type="button" id="btn-reviewWrite"><span>리뷰작성</span></button>
			</div>

			<div class="goods-sort clear mt-20">
				<div class="total-ea fz-15">전체 <strong>235</strong></div>
				<div class="sort-by ">
					<label for="sort_by">Sort by</label>
					<div class="select">
						<select name="" id="sort_by">
							<option>일반리뷰</option>
							<option>포토리뷰</option>
						</select>
					</div>
				</div>
			</div>

			<table class="th-top mt-10">
				<caption>상품 리뷰 리스트</caption>
				<colgroup>
					<col style="width:94px">
					<col style="width:auto">
					<col style="width:134px">
					<col style="width:114px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">상품평</th>
						<th scope="col">작성일</th>
						<th scope="col">작성일</th>
						<th scope="col">작성자</th>
					</tr>
				</thead>
				<tbody data-ui="TabMenu">
					<tr data-content="menu">
						<td class="score-icon"><img src="/sinwon/web/static/img/icon/rating5.png" alt="5점 만점 중 5점"></td>
						<td class="subject">정말 마음에 듭니다.</td>
						<td>2017.01.14</td>
						<td>newew**</td>
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output">
								<p>멋지네요 이것이 존멋</p>
							</div>
						</td>
					</tr>
					<tr data-content="menu">
						<td>
							<div class="score-icon"><img src="/sinwon/web/static/img/icon/rating4.png" alt="5점 만점 중 4점"></div>
						</td>
						<td class="subject">간디작살 존멋 핵간지 <i class="icon-photo ml-5"></i></td>
						<td>2017.01.14</td>
						<td>bestkid**</td>
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output">
								<p>멋지네요 이것이 존멋</p>
								<p>공유하고 싶어서 리뷰 씁니다.</p>
								<p></p>
								<p><img src="/sinwon/web/static/img/test/@goods_thumb300_07.jpg" alt=""></p>
							</div>
						</td>
					</tr>
					<tr data-content="menu">
						<td class="score-icon"><img src="/sinwon/web/static/img/icon/rating3.png" alt="5점 만점 중 3점"></td>
						<td class="subject">정말 마음에 듭니다.</td>
						<td>2017.01.14</td>
						<td>newew**</td>
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output">
								<p>멋지네요 이것이 존멋</p>
							</div>
						</td>
					</tr>
					<!-- <tr><td colspan="4">게시글이 없습니다.</td></tr> --> <!-- [D] 게시글 없는 경우 -->
				</tbody>
			</table>
			<div class="list-paginate mt-20 mb-20">
				<a href="#" class="prev-all"></a>
				<a href="#" class="prev"></a>
				<a href="#" class="number on">1</a>
				<a href="#" class="number">2</a>
				<a href="#" class="number">3</a>
				<a href="#" class="number">4</a>
				<a href="#" class="number">5</a>
				<a href="#" class="number">6</a>
				<a href="#" class="number">7</a>
				<a href="#" class="number">8</a>
				<a href="#" class="number">9</a>
				<a href="#" class="number">10</a>
				<a href="#" class="next on"></a>
				<a href="#" class="next-all on"></a>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 리뷰 리스트 -->

<!-- 상세 > 리뷰 작성 -->
<div class="layer-dimm-wrap goodsReview-write">
	<div class="layer-inner">
		<h2 class="layer-title">리뷰작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-left">
				<caption>리뷰 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label>상품명</label></th>
						<td>레이어드 스타일 티셔츠</td>
					</tr>
					<tr>
						<th scope="row"><label>상품평가</label></th>
						<td>
							<ul class="appraisal">
								<li class="clear">
									<div class="sort">사이즈</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-size5" name="ratingSize" ><label for="rating-size5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-size4" name="ratingSize"><label for="rating-size4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-size3" name="ratingSize"><label for="rating-size3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-size2" name="ratingSize"><label for="rating-size2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-size1" name="ratingSize" checked><label for="rating-size1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">색상</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-color5" name="ratingColor" ><label for="rating-color5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-color4" name="ratingColor"><label for="rating-color4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-color3" name="ratingColor"><label for="rating-color3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-color2" name="ratingColor"><label for="rating-color2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-color1" name="ratingColor" checked><label for="rating-color1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">배송</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-delivery5" name="ratingDelivery" ><label for="rating-delivery5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-delivery4" name="ratingDelivery"><label for="rating-delivery4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-delivery3" name="ratingDelivery"><label for="rating-delivery3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-delivery2" name="ratingDelivery"><label for="rating-delivery2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-delivery1" name="ratingDelivery" checked><label for="rating-delivery1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
									</div>
								</li>
								<li class="clear">
									<div class="sort">품질/만족도</div>
									<div class="rating clear">
										<input type="radio" class="rating-input" id="rating-quality5" name="ratingQuality" ><label for="rating-quality5" class="rating-star score5"><p>5점 만점 중<span>5</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-quality4" name="ratingQuality"><label for="rating-quality4" class="rating-star score4"><p>5점 만점 중<span>4</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-quality3" name="ratingQuality"><label for="rating-quality3" class="rating-star score3"><p>5점 만점 중<span>3</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-quality2" name="ratingQuality"><label for="rating-quality2" class="rating-star score2"><p>5점 만점 중<span>2</span>점</p></label>
										<input type="radio" class="rating-input" id="rating-quality1" name="ratingQuality" checked><label for="rating-quality1" class="rating-star score1"><p>5점 만점 중<span>1</span>점</p></label>
									</div>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>사이즈 정보</label></th>
						<td>
							<div class="body-spec">
								<label>키(cm) <input type="text" title="키 입력"></label>
								<label class="pl-20">몸무게(kg) <input type="text" title="키 입력"></label>
								<span>*숫자만 입력가능합니다.</span>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="review_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="review_title"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="review_textarea" class="essential">내용</label></th>
						<td><textarea id="review_textarea" class="w100-per" style="height:192px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label>사진</label></th>
						<td>
							<div class="box-photoUpload">
								<div class="filebox preview-image">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file"><span><i class="icon-photo-grey"></i></span><button class="del" type="button"></button></label> 
									<input type="file" id="input-file" class="upload-hidden"> 
								</div>
								<div class="filebox preview-image">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file"><span><i class="icon-photo-grey"></i></span><button class="del" type="button"></button></label> 
									<input type="file" id="input-file" class="upload-hidden"> 
								</div>
								<div class="filebox preview-image">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file"><span><i class="icon-photo-grey"></i></span><button class="del" type="button"></button></label> 
									<input type="file" id="input-file" class="upload-hidden"> 
								</div>
								<div class="filebox preview-image">
									<input class="upload-nm hide" value="파일선택" disabled="disabled" >
									<label class="photoBox" for="input-file"><span><i class="icon-photo-grey"></i></span><button class="del" type="button"></button></label> 
									<input type="file" id="input-file" class="upload-hidden"> 
								</div>
							</div>
							<p class="pt-5">파일명: 한글, 영문, 숫자 / 파일 크기: 3mb 이하 / 파일 형식: GIF, JPG, JPEG</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="btnPlace mt-20">
				<button class="btn-line h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="submit"><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 리뷰 작성 -->

<!-- 상세 > Q&A 리스트 -->
<div class="layer-dimm-wrap goodsQna-list">
	<div class="layer-inner">
		<h2 class="layer-title">Q&amp;A</h2>
		<div class="popup-summary"><p>상품관련 문의사항을 남겨주시기 바랍니다.</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<div class="ta-r mt-10"><button class="btn-line fz-14 w100" type="button" id="btn-qnaWrite"><span>문의하기</span></button></div>
			<table class="th-top mt-10">
				<caption>상품 Q&A 리스트</caption>
				<colgroup>
					<col style="width:auto">
					<col style="width:134px">
					<col style="width:114px">
					<col style="width:80px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">문의</th>
						<th scope="col">작성일</th>
						<th scope="col">작성자</th>
						<th scope="col">상태</th>
					</tr>
				</thead>
				<tbody data-ui="TabMenu">
					<tr data-content="menu">
						<td class="subject"><i class="mark">Q</i>입고 예정있나요?</td>
						<td>2017.01.14</td>
						<td>newew**</td>
						<td class="point-color">답변대기</td>
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output ">
								<p>사이즈가 있나요?</p>
								<p>정말 마음에 들어서 꼭 사고 싶습니다.</p>
							</div>
							<div class="answer-user"><i class="mark point-color">A</i><span>관리자 <em>|</em> 2017.01.20</span></div>
							<div class="board-answer editor-output">
								<p>안녕하세요. 고객님</p>
								<p>해당 상품은 재입고 예정이 없습니다. 감사합니다.</p>
							</div>
						</td>
					</tr>
					<tr data-content="menu">
						<td class="subject"><i class="mark">Q</i>배송 언제 되뇽?</td>
						<td>2017.01.14</td>
						<td>stocbl**</td>
						<td>답변완료</td> <!-- [D] 답변완료시 .point-color 제거 -->
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output ">
								<p>사이즈가 있나요?</p>
								<p>정말 마음에 들어서 꼭 사고 싶습니다.</p>
							</div>
							<div class="answer-user"><i class="mark point-color">A</i><span>관리자 <em>|</em> 2017.01.20</span></div>
							<div class="board-answer editor-output">
								<p>안녕하세요. 고객님</p>
								<p>해당 상품은 재입고 예정이 없습니다. 감사합니다.</p>
							</div>
						</td>
					</tr>
					<tr data-content="menu">
						<td class="subject"><i class="mark">Q</i>사이즈 문의드립니다. <i class="icon-secret ml-10">비밀글</i></td>
						<td>2017.01.14</td>
						<td>testor**</td>
						<td>답변완료</td> <!-- [D] 답변완료시 .point-color 제거 -->
					</tr>
					<tr data-content="content">
						<td colspan="4" class="reset">
							<div class="board-answer editor-output ">
								<p>큰가요 작은가요?</p>
								<p>작은가요 큰가요?</p>
							</div>
							<div class="answer-user"><i class="mark point-color">A</i><span>관리자 <em>|</em> 2017.01.20</span></div>
							<div class="board-answer editor-output">
								<p>안녕하세요. 고객님</p>
								<p>감사합니다.</p>
							</div>
						</td>
					</tr>
					<!-- <tr><td colspan="4">게시글이 없습니다.</td></tr> --> <!-- [D] 게시글 없는 경우 -->
				</tbody>
			</table>
			<div class="list-paginate mt-20 mb-20">
				<a href="#" class="prev-all"></a>
				<a href="#" class="prev"></a>
				<a href="#" class="number on">1</a>
				<a href="#" class="number">2</a>
				<a href="#" class="number">3</a>
				<a href="#" class="number">4</a>
				<a href="#" class="number">5</a>
				<a href="#" class="number">6</a>
				<a href="#" class="number">7</a>
				<a href="#" class="number">8</a>
				<a href="#" class="number">9</a>
				<a href="#" class="number">10</a>
				<a href="#" class="next on"></a>
				<a href="#" class="next-all on"></a>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > Q&A 리스트 -->

<!-- 상세 > Q&A 작성-->
<div class="layer-dimm-wrap goodsQna-write">
	<div class="layer-inner">
		<h2 class="layer-title">Q&A 작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-left">
				<caption>Q&A 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="qna_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="qna_title"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="qna_textarea" class="essential">내용</label></th>
						<td><textarea id="qna_textarea" class="w100-per" style="height:272px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="qna_email">이메일</label></th>
						<td>
							<div class="input-cover">
								<input type="text"  style="width:190px" title="이메일 입력" id="qna_email">
								<span class="txt">@</span>
								<div class="select">
									<select style="width:170px">
										<option value="">naver.com</option>
										<option value="">직접입력</option>
									</select>
								</div>
								<input type="text" title="도메인 직접 입력" class="ml-10" style="width:164px"> <!-- [D] 직접입력시 인풋박스 출력 -->
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>휴대폰 번호</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select style="width:110px">
										<option value="">선택</option>
										<option value="">010</option>
									</select>
								</div>
								<span class="txt">-</span>
								<input type="text" title="휴대폰 가운데 번호 입력" style="width:110px">
								<span class="txt">-</span>
								<input type="text" title="휴대폰 마지막 번호 입력" style="width:110px">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>공개여부</label></th>
						<td>
							<div class="checkbox">
								<input type="checkbox" id="secret_check">
								<label for="secret_check">비공개</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="att pt-10"><span class="point-color">*</span> 표시는 필수항목입니다.</p>
			<div class="btnPlace mt-20">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="submit"><span>등록</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > Q&A 작성 -->

<!-- 상세 > 상품상세정보 -->
<div class="layer-dimm-wrap goodsDetail-pop">
	<div class="layer-inner">
		<h2 class="layer-title">상품 상세정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="editor-output">
				<p><img src="/sinwon/web/static/img/test/@goods_detail650.jpg" alt=""></p>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 상품상세정보 -->

<!-- 상세 > 배송반품 -->
<div class="layer-dimm-wrap goodsDelivery-pop">
	<div class="layer-inner">
		<h2 class="layer-title">배송반품</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<section class="delivery-info">
				<h3 class="title">배송정보</h3>
				<ul>
					<li>배송 방법 : 택배</li>
					<li>배송 지역 : 전국지역 </li>
					<li>배송 비용 : 개별배송상품을 제외하고 배송비는 [무료]입니다. </li>
					<li>배송 기간 : 3일 ~ 7일</li>
					<li>배송 안내 : 산간벽지나 도서지방은 별도의 추가금액을 지불하셔야 하는 경우가 있습니다. </li>
					<li class="point-color">※ 고객님께서 주문하신 상품은 입금 확인후 배송해 드립니다. <br>다만, 상품종류에 따라서 상품의 배송이 다소 지연될 수 있습니다. </li>
				</ul>
			</section>

			<section class="delivery-info mt-30">
				<h3 class="title">교환 및 반품</h3>
				<dl>
					<dt>교환 및 반품이 가능한 경우</dt>
					<dd>- 상품을 공급 받으신 날로부터 7일이내 단, 가전제품의 경우 포장을 개봉하였거나 포장이 훼손되어 상품가치가 상실된 경우에는 교환/반품이 불가능합니다.</dd>
					<dd>- 공급받으신 상품 및 용역의 내용이 표시.광고 내용과  다르거나 다르게 이행된 경우에는 공급받은 날로부터 3월이내, 그사실을 알게 된 날로부터 30일이내</dd>
				</dl>
				<dl>
					<dt>교환 및 반품이 불가능한 경우</dt>
					<dd>- 고객님의 책임 있는 사유로 상품등이 멸실 또는 훼손된 경우. 단, 상품의 내용을 확인하기 위하여 포장 등을 훼손한 경우는 제외 </dd>
					<dd>- 포장을 개봉하였거나 포장이 훼손되어 상품가치가 상실된 경우 (예 : 가전제품, 식품, 음반 등, 단 액정화면이 부착된 노트북, LCD모니터, 디지털 카메라 등의 불량화소에  따른 반품/교환은 제조사 기준에 따릅니다.)  </dd>
					<dd>- 고객님의 사용 또는 일부 소비에 의하여 상품의 가치가 현저히 감소한 경우 단, 화장품등의 경우 시용제품을 제공한 경우에 한 합니다. </dd>
					<dd>- 시간의 경과에 의하여 재판매가 곤란할 정도로 상품등의 가치가 현저히 감소한 경우</dd>
					<dd>- 복제가 가능한 상품등의 포장을 훼손한 경우 <br>(자세한 내용은 고객만족센터 1:1 E-MAIL상담을 이용해 주시기 바랍니다.) </dd>
					<dd class="point-color">※ 고객님의 마음이 바뀌어 교환, 반품을 하실 경우 상품반송 비용은 고객님께서 부담하셔야 합니다. <br>(색상 교환, 사이즈 교환 등 포함)</dd>
				</dl>
			</section>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 배송반품 -->

<!-- 상세 > 매장픽업 -->
<div class="layer-dimm-wrap find-shopPickup">
	<div class="layer-inner">
		<h2 class="layer-title">매장선택</h2>
		<div class="popup-summary"><p>※ 원하는 날짜, 원하는 매장에서 상품을 픽업하는 맞춤형 배송 서비스입니다.</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<div class="shop-search">
				<label>픽업 가능 매장 검색</label>
				<div class="select">
					<select>
						<option value="">시&middot;도</option>
					</select>
				</div>
				<div class="select">
					<select>
						<option value="">구&middot;군</option>
					</select>
				</div>
				<div class="select">
					<select>
						<option value="">수령일 선택</option>
					</select>
				</div>
			</div>

			<div class="mt-25 clear">
				<div class="shopList-wrap">
					<section class="shopList">
						<h4 class="title">동일 브랜드 매장정보</h4>
						<ul>
							<li>
								<div class="radio">
									<input type="radio" name="pickShop" id="pickSame_shop01">
									<label for="pickSame_shop01">[VIKI] 강남직영점</label>
								</div>
								<div class="point-color">재고있음</div>
							</li>
							<li>
								<div class="radio">
									<input type="radio" name="pickShop" id="pickSame_shop02">
									<label for="pickSame_shop02">[VIKI] 강남직영점</label>
								</div>
							</li>
						</ul>
					</section>
					<section class="shopList mt-15">
						<h4 class="title">기타 매장정보</h4>
						<ul>
							<li>
								<div class="radio">
									<input type="radio" name="pickShop" id="pickOther_shop01">
									<label for="pickOther_shop01">[VIKI] 강남직영점</label>
								</div>
								<div class="point-color">3~5일 소요</div>
							</li>
							<li>
								<div class="radio">
									<input type="radio" name="pickShop" id="pickOther_shop02">
									<label for="pickOther_shop02">[VIKI] 역삼직영점</label>
								</div>
								<div class="point-color">3~5일 소요</div>
							</li>
						</ul>
					</section>
				</div><!-- //.shopList-wrap -->
				<div class="shopDetail-wrap">
					<dl>
						<dt>[VIKI]강남직영점</dt>
						<dd><span>주소</span>서울 강남구 언주역</dd>
						<dd><span>TEL</span>(02)1234-1234</dd>
					</dl>
					<div class="map-local">구글지도 연동</div>
				</div><!-- //.shopDetail-wrap -->
			</div>
			<div class="btnPlace mt-40">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button"><span>선택</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 매장픽업 -->

<!-- 상세 > 당일수령 -->
<div class="layer-dimm-wrap find-shopToday">
	<div class="layer-inner">
		<h2 class="layer-title">매장선택</h2>
		<div class="popup-summary"><p>※ 원하는 날짜, 원하는 매장에서 상품을 픽업하는 맞춤형 배송 서비스입니다. <br>수령지를 입력하신 후 발송 가능 매장을 검색하세요(오후 4시전 주문시 당일수령 가능)</p></div>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<div class="shop-search">
				<label>수령지 정보 입력</label>
				<fieldset>
					<legend>수령지 검색</legend>
					<input type="text" title="검색할 주소지 입력" placeholder="주소검색">
					<input type="text" title="검색할 상세주소지 입력" placeholder="상세주소 입력">
					<button class="btn-point" type="submit"><span>발송 가능 매장 찾기</span></button>
				</fieldset>
			</div>

			<div class="mt-25 clear">
				<div class="shopList-wrap with-deliveryPrice">
					<div class="inner">
						<section class="shopList">
							<h4 class="title">동일 브랜드 매장정보</h4>
							<ul>
								<li>
									<div class="radio">
										<input type="radio" name="pickToday" id="pickToday_shop01">
										<label for="pickToday_shop01">[VIKI] 강남직영점</label>
									</div>
									<div class="point-color">재고있음</div>
								</li>
								<li>
									<div class="radio">
										<input type="radio" name="pickToday" id="pickToday_shop02">
										<label for="pickToday_shop02">[VIKI] 강남직영점</label>
									</div>
								</li>
								<li>
									<div class="radio">
										<input type="radio" name="pickToday" id="pickToday_shop03">
										<label for="pickToday_shop03">[VIKI] 강남직영점</label>
									</div>
									<div class="point-color">재고있음</div>
								</li>
							</ul>
						</section>
					</div>
					<div class="delivery-price clear"><label>배송비</label><strong class="point-color">9,300<span>원</span></strong></div>
				</div><!-- //.shopList-wrap -->
				<div class="shopDetail-wrap">
					<dl>
						<dt>[VIKI]강남직영점</dt>
						<dd><span>주소</span>서울 강남구 언주역</dd>
						<dd><span>TEL</span>(02)1234-1234</dd>
					</dl>
					<div class="map-local">구글지도 연동</div>
				</div><!-- //.shopDetail-wrap -->
			</div>
			<div class="btnPlace mt-40">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button"><span>선택</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 당일수령 -->

<!-- 주문 > 배송지목록 -->
<div class="layer-dimm-wrap popList delivery">
	<div class="layer-inner">
		<h2 class="layer-title">배송지 목록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<ul class="list">
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list1">
						<label for="deliver_list1"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list2">
						<label for="deliver_list2"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list3">
						<label for="deliver_list3"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지 서울 강남구 강남대로 123번지 서울 강남구 강남대로 123번지</p>
					</div>
				</li>
			</ul>
			<div class="btnPlace mt-10">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button"><span>적용</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 배송지목록 -->

<!-- 주문 > 쿠폰목록 -->
<div class="layer-dimm-wrap popList coupon">
	<div class="layer-inner">
		<h2 class="layer-title">쿠폰 목록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<ul class="list">
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_couponList" id="coupon_list1">
						<label for="coupon_list1"></label>
					</div>
					<div class="coupon-item ml-10"><strong>10%</strong>쿠폰</div>
					<div class="content w200 ml-35">
						<p class="txt-toneB">브랜드 데이 특별 할인 쿠폰</p>
						<p class="point-color">10% 할인</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_couponList" id="coupon_list2">
						<label for="coupon_list2"></label>
					</div>
					<div class="coupon-item ml-10"><strong>15%</strong>쿠폰</div>
					<div class="content w200 ml-35">
						<p class="txt-toneB">브랜드 데이 특별 할인 쿠폰</p>
						<p class="point-color">15% 할인</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_couponList" id="coupon_list3">
						<label for="coupon_list3"></label>
					</div>
					<div class="coupon-item ml-10"><strong>20%</strong>쿠폰</div>
					<div class="content w200 ml-35">
						<p class="txt-toneB">브랜드 데이 특별 할인 쿠폰</p>
						<p class="point-color">20% 할인</p>
					</div>
				</li>
			</ul>
			<div class="btnPlace mt-10">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button"><span>적용</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 쿠폰목록 -->

<!-- 주문 > 매장안내 -->
<div class="layer-dimm-wrap pop-infoStore">
	<div class="layer-inner">
		<h2 class="layer-title">매장 위치정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<h3 class="store-title">[VIKI]강남직영점</h3>
			<table class="th-left mt-15">
				<caption>매장 정보</caption>
				<colgroup>
					<col style="width:180px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label>주소</label></th>
						<td>서울 강남구 강남대로 238-11</td>
					</tr>
					<tr>
						<th scope="row"><label>운영시간</label></th>
						<td>평일 09:00 ~ 18:00 (토/일 09:00 ~ 18:00)</td>
					</tr>
					<tr>
						<th scope="row"><label>휴무정보</label></th>
						<td>매주 일요일 / 국경일</td>
					</tr>
					<tr>
						<th scope="row"><label>매장 전화번호</label></th>
						<td>02-5212-2512</td>
					</tr>
				</tbody>
			</table>
			<div class="map-local mt-10">구글지도 위치</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 매장안내 -->