			<div class="contentsBody">

				
				<div id="tabs-container">
					<ul class="tabs-menu">
						<li class="on"><a href="#tab-1">적립금</a></li>
						<li><a href="#tab-2">예치금</a></li>
						<li><a href="#tab-3">쿠폰</a></li>
					</ul>
					<div class="tab-content-wrap">
						<div id="tab-1" class="tab-content">
							<p class="dot-title">
								적립내역
								<a href="#" class="btn-line">접기</a>
								<a href="#" class="btn-line">펼치기</a>
							</p>
							<table class="th-top">
								<caption>적립내역</caption>
								<colgroup>
									<col style="width:auto"><col style="width:130px"><col style="width:320px"><col style="width:230px">
								</colgroup>
								<thead>
									<tr>
										<th scope="col">증감여부</th>
										<th scope="col">적립금<a href="#" class="icon-question"></a></th>
										<th scope="col">내용</th>
										<th scope="col">관련주문선택</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<select>
												<option>(+)적립금 증액</option>
												<option>(-)적립금 증액</option>
											</select>
										</td>
										<td><input type="text" class="w100-per"></td>
										<td><input type="text" class="w100-per"></td>
										<td>
											<input type="text" class="w100">
											<a href="#" class="btn-line">검색</a>
											<a href="#" class="btn-line">비우기</a>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="btn-place"><button class="btn-line big" type="button"><span>추가</span></button></div>
						</div>
						<div id="tab-2" class="tab-content">
							<table class="th-top">
								<caption>적립내역</caption>
								<colgroup>
									<col style="width:35px"><col style="width:auto"><col style="width:100px"><col style="width:145px">
									<col style="width:85px"><col style="width:85px"><col style="width:85px"><col style="width:130px">
								</colgroup>
								<thead>
									<tr>
										<th colspan="8" class="ta-l">선택항목 <button class="btn-line del" type="button"><span>삭제</span></button></th>
									</tr>
									<tr>
										<th scope="col"><input type="checkbox"></th>
										<th scope="col">상세내용</th>
										<th scope="col">적립금 유형</th>
										<th scope="col">일자</th>
										<th scope="col">적립(+)</th>
										<th scope="col">적립(-)</th>
										<th scope="col">잔액</th>
										<th scope="col">관련주문</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><input type="checkbox"></td>
										<td class="ta-l">상품구매시 사용한 적립금</td>
										<td></td>
										<td>2016-02-23 12:33:53</td>
										<td><strong class="color-blue">2,000</strong></td>
										<td><strong class="color-red">2,000</strong></td>
										<td><strong>2,000</strong></td>
										<td>3213544-351351</td>
									</tr>
								</tbody>
							</table>
							<div class="paginate">
								<a href="#" class="prev"></a>
								<a href="#" class="on">1</a>
								<a href="#">2</a>
								<a href="#">3</a>
								<a href="#" class="next"></a>
							</div>
						</div>
						<div id="tab-3" class="tab-content">
							<table class="th-top">
								<caption>전화상담메모</caption>
								<colgroup>
									<col style="width:150px"><col style="width:200px"><col style="width:auto"><col style="width:150px">
								</colgroup>
								<thead>
									<tr>
										<th scope="col">고객정보</th>
										<th scope="col">상담구분</th>
										<th scope="col">구체적인 상담내용</th>
										<th scope="col">상담자</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<ul>
												<li><input type="text" class="w100-per" name="" id=""></li>
												<li><input type="text" class="w100-per" name="" id=""></li>
											</ul>
										</td>
										<td>
											<ul>
												<li><input type="radio" name="cs-type" id="cs-type1"> <label for="cs-type1">현금영수증 문의</label></li>
												<li><input type="radio" name="cs-type" id="cs-type2"> <label for="cs-type2">배송관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type3"> <label for="cs-type3">상품관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type4"> <label for="cs-type4">업체관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type5"><label for="cs-type5">반품관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type6"> <label for="cs-type6">입금관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type7"> <label for="cs-type7">주문관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type8"> <label for="cs-type8">취소관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type9"> <label for="cs-type9">교환관련</label></li>
												<li><input type="radio" name="cs-type" id="cs-type10"> <label for="cs-type10">적립금/쿠폰</label></li>
											</ul>
										</td>
										<td class="ta-l">
											<div>
												<strong>메모구분 :</strong>
												<input type="radio" name="memo-type" id="memo-type1">
												<label for="memo-type1">SK인터넷폰</label>
												<input type="radio" name="memo-type" id="memo-type2">
												<label for="memo-type2">KT일반전화</label>
											</div>
											<textarea name="" id="" cols="30" rows="10" class="w100-per mt-10"></textarea>
										</td>
										<td><input type="text" name="" id="" class="w100-per"></td>
									</tr>
								</tbody>
							</table>
							<div class="btn-place">
								<a href="#" class="btn-dib on">등록</a>
							</div>
						</div>
					</div>
				</div>

				<ul class="attention">
					<li>- 일반 설명시</li>
					<li>- 공지사항등 안내문구 적을때 사용하세요</li>
					<li>- 아님말구</li>
				</ul>
				
				<h3 class="table-title mt-50">회원상세정보</h3>
				<p class="dot-title">회원정보 검색</p>
				<table class="th-left" >
					<caption>회원 기본정보 출력</caption>
					<colgroup>
						<col style="width:18%"><col style="width:32%"><col style="width:18%"><col style="width:32%">
					</colgroup>
					<tbody>
						<tr>
							<th scope="col"><label for="inp-pwd01">비밀번호</label></th>
							<td colspan="3">
								<input type="password" id="inp-pwd01">
								<a href="#" class="btn-line">더보기</a>
								<a href="#" class="icon-question"></a>
								<br>
								<span class="txt-lh">- 영문 또는 숫자로 4~16자까지 입력가능</span>
							</td>
						</tr>
						<tr>
							<th scope="col">회원구분</th>
							<td>개인회원 <a href="#" class="btn-line">수정</a></td>
							<th scope="col">회원등급</th>
							<td>
								<select>
									<option>일반회원</option>
									<option>높은회원</option>
									<option>낮은회원</option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="col"><label for="inp-address01">주소</label></th>
							<td colspan="3">
								<div><input type="text" title="우편번호 자리"> <a href="#" class="btn-line">우편번호 검색</a></div>
								<div class="mt-5"><input class="w500" type="text" title="주소 자리"> </div>
								<div class="mt-5"><input class="w500" type="text" title="주소 상세자리"> </div>
							</td>
						</tr>
						<tr>
							<th scope="col">SMS수신여부<span><a href="#">법적고지</span></span></th>
							<td>
								<input type="radio" name="sms-radio" id="radio-agree01">
								<label for="radio-agree01">수신</label>
								<input type="radio" name="sms-radio"  id="radio-no01">
								<label for="radio-no01">수신거부</label>
							</td>
							<th scope="col">뉴스메일수신여부<span><a href="#">법적고지</span></span></th>
							<td>
								<input type="radio" name="sms-radio" id="radio-agree02">
								<label for="radio-agree02">수신</label>
								<input type="radio" name="sms-radio"  id="radio-no02">
								<label for="radio-no02">수신거부</label>
							</td>
						</tr>
						<tr>
							<th scope="col">특별 관리 회원</th>
							<td colspan="3">
								<input type="checkbox" id="checkbox-agree01">
								<label for="checkbox-agree01">특별 관리 회원 입니다.</label>
							</td>
						</tr>
						<tr>
							<th scope="col">제3자 정보제공<br>동의현황</th>
							<td colspan="3">
								<table class="th-top inner">
									<colgroup><col style="width:auto"><col style="width:85px"><col style="width:85px"><col style="width:85px"><col style="width:85px"></colgroup>
									<thead>
										<tr>
											<th scope="col">제공서비스</th>
											<th scope="col">제고받는자</th>
											<th scope="col">보유기간</th>
											<th scope="col">동의여부</th>
											<th scope="col">일자</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="ta-l">취급위탁 동의가 필요한 서비스가 없습니다.</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
								</table>
								<p class="txt-lh">- 취급위탁에 대한 동의 여부를 고객이 변경할 수 있으며, 최근 동의여부와 일자를 확인 할 수 있습니다.</p>
							</td>
						</tr>
					</tbody>
				</table>

				<h3 class="table-title mt-50">전체주문 조회</h3>
				<table class="th-left">
					<caption>전체주문 조회</caption>
					<colgroup>
						<col style="width:18%"><col style="width:82%">
					</colgroup>
					<thead>
						<tr>
							<th colspan="2">
								<button href="#" class="btn-function"><span>검색조회 항목 저장</span></button><a href="#" class="icon-question"></a>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">기간</th>
							<td>
								<div class="date-choice">
									<select>
										<option>주문일</option>
										<option>입금일</option>
									</select>
									<button href="#" class="btn-line on"><span>오늘</span></button>
									<button href="#" class="btn-line"><span>3일</span></button>
									<button href="#" class="btn-line"><span>7일</span></button>
									<button href="#" class="btn-line"><span>15일</span></button>
									<button href="#" class="btn-line"><span>1개월</span></button>
									<button href="#" class="btn-line"><span>3개월</span></button>
									<button href="#" class="btn-line"><span>6개월</span></button>
									<input class="w100" type="text" name="cal-date">
									<input type="image" src="static/img/btn/btn_cal.gif" alt="달력">
									<span class="txt-lh">~</span>
									<input class="w100" type="text" name="cal-date">
									<input type="image" src="static/img/btn/btn_cal.gif" alt="달력">
									<button href="#" class="btn-line"><span>기간 설정</span></button>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="keyword01">검색어</label></th>
							<td>
								<div>
									<select>
										<option>주문자 아이디</option>
										<option>입금자 이름</option>
									</select>
									<input type="text" id="keyword01" class="w400">
									<input type="image" src="static/img/btn/input_add.gif" alt="달력">
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">주문상태</th>
							<td>
								<div>
									<input type="checkbox" name="" id="order-type01" checked>
									<label for="order-type01">상품준비중</label>
									<input type="checkbox" name="" id="order-type02">
									<label for="order-type02">배송준비중</label>
									<input type="checkbox" name="" id="order-type03">
									<label for="order-type03">배송보류</label>
									<input type="checkbox" name="" id="order-type04">
									<label for="order-type04">배송대기</label>
									<input type="checkbox" name="" id="order-type05">
									<label for="order-type05">배송완료</label>
								</div>
								<div class="mt-5">
									<select>
										<option>입금전취소</option>
										<option>opt1</option>
									</select>
									<select>
										<option>취소</option>
										<option>opt1</option>
									</select>
									<select>
										<option>교환</option>
										<option>opt1</option>
									</select>
									<select>
										<option>반품</option>
										<option>opt1</option>
									</select>
									<select>
										<option>환불</option>
										<option>opt1</option>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">입금/결제상태</th>
							<td>
								<input type="radio" name="order-step-type" id="step-type01" checked>
								<label for="order-type01">전체</label>
								<input type="radio" name="order-step-type" id="step-type02">
								<label for="step-type02">입금전</label>
								<input type="radio" name="order-step-type" id="step-type03">
								<label for="step-type03">추가입금대기</label>
								<input type="radio" name="order-step-type" id="step-type04">
								<label for="step-type04">입금완료(수동)</label>
								<input type="radio" name="order-step-type" id="step-type05">
								<label for="step-type05">입금완료(자동)</label>
								<input type="radio" name="order-step-type" id="step-type06">
								<label for="step-type06">결제완료</label>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="detail-find"><input type="image" src="static/img/btn/detail_search.gif" alt="상세검색"></div>
				<div class="btn-place">
					<a href="#" class="btn-dib on">검색</a>
					<a href="#" class="btn-dib">초기화</a>
				</div>
				
				<h3 class="table-title mt-50">기본정보 <a href="#" class="btn-line">더보기</a></h3>
				<table class="th-left">
					<caption>회원 기본정보 출력</caption>
					<colgroup>
						<col style="width:18%"><col style="width:32%"><col style="width:18%"><col style="width:32%">
					</colgroup>
					<tbody>
						<tr>
							<th scope="col">아이디</th>
							<td>gmldud</td>
							<th scope="col">회원등급</th>
							<td>일반회원</td>
						</tr>
						<tr>
							<th scope="col">총적립금<span>보조설명</span></th>
							<td>2,390</td>
							<th scope="col">인증 수단</th>
							<td>휴대폰인증</td>
						</tr>
						<tr>
							<th scope="col">주소</th>
							<td colspan="3">서울시 강남구 논현동 1111 거기</td>
						</tr>
					</tbody>
				</table>

				<h3 class="table-title mt-50">주문정보 <a href="#" class="btn-line">자세히</a><a href="#" class="icon-question"></a></h3>
				<table class="th-top">
					<caption>주문정보 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto"><col style="width:75px">
						<col style="width:75px"><col style="width:75px"><col style="width:75px"><col style="width:75px"><col style="width:75px">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">주문일</th>
							<th scope="row">주문번호</th>
							<th scope="row">실결제금액</th>
							<th scope="row">결제수단</th>
							<th scope="row">결제<a href="#" class="icon-question"></a></th>
							<th scope="row">배송<a href="#" class="icon-question"></a></th>
							<th scope="row">교환<a href="#" class="icon-question"></a></th>
							<th scope="row">취소<a href="#" class="icon-question"></a></th>
							<th scope="row">메모</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>2</td>
							<td>2016-02-19 12:33</td>
							<td>20163213513131Aa</td>
							<td>33,100</td>
							<td>
								<div class="pay-type-icon">
									<!-- <span class="card">카</span>
									<span class="mileage">적</span>
									<span class="coupon">쿠</span> -->
									<span class="imagine">가</span>
									<span class="phone">휴</span>
									<span class="bank">무</span>
								</div>
							</td>
							<td>완료</td>
							<td class="point">배송전</td>
							<td class="point">교환안함</td>
							<td class="point">취소안함</td>
							<td><a href="" class="btn-function line">ADMIN</a></td>
						</tr>
						<tr>
							<td>1</td>
							<td>2016-02-19 12:33</td>
							<td>20163213513131Aa</td>
							<td>33,100</td>
							<td>
								<div class="pay-type-icon">
									<span class="card">카</span>
									<span class="mileage">적</span>
									<span class="coupon">쿠</span>
									<!-- <span class="imagine">가</span>
									<span class="phone">휴</span>
									<span class="bank">무</span> -->
								</div>
							</td>
							<td>완료</td>
							<td class="point">배송전</td>
							<td class="point">교환안함</td>
							<td class="point">취소안함</td>
							<td><a href="" class="btn-function line">ADMIN</a></td>
						</tr>
					</tbody>
				</table>

				<h3 class="table-title mt-50">회원메모 <a href="#" class="btn-line">더보기</a></h3>
				<table class="th-top">
					<caption>회원메모 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto">
					</colgroup>
					<thead>
						<tr>
							<th scope="row" colspan="4" class="ta-r"><button class="btn-function on" type="button"><span>메모작성</span></button></th>
						</tr>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">주문일</th>
							<th scope="row">주문번호</th>
							<th scope="row">실결제금액</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>2</td>
							<td>2016-02-19 12:33</td>
							<td>20163213513131Aa</td>
							<td class="ta-l">메모내역이 나오겠지</td>
						</tr>
						<tr>
							<td colspan="4" class="ta-c">회원메모 내역이 없습니다.</td>
						</tr>
					</tbody>
				</table>

				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd>
				</dl>


			</div><!-- //.contentsBody -->