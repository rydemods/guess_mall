
<!-- 마이페이지 > 1:1 작성-->
<div class="layer-dimm-wrap myQna-write">
	<div class="layer-inner">
		<h2 class="layer-title">1:1 문의</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-left">
				<caption>1:1 문의 작성</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="my_qna_type" class="essential">상담유형</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select style="width:170px" id="my_qna_type" title="상담유형 선택">
										<option value="">선택</option>
									</select>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="my_qna_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per" title="제목 입력" id="my_qna_title"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="my_qna_textarea" class="essential">내용</label></th>
						<td><textarea id="my_qna_textarea" class="w100-per" style="height:272px"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="my_qna_email">이메일</label></th>
						<td>
							<div class="input-cover">
								<input type="text"  style="width:190px" title="이메일 입력" id="my_qna_email">
								<span class="txt">@</span>
								<div class="select">
									<select style="width:170px" title="이메일 도메인 선택">
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
									<select style="width:110px" title="휴대폰 앞자리 선택">
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
						<th scope="row"><label>파일첨부</label></th>
						<td>
							<div class="filebox no-photo">
								<input class="upload-nm h-medium" value="파일선택" disabled="disabled">
								<label for="file_name" class="btn-basic ">찾기</label> 
								<input type="file" id="file_name" class="upload-hidden"> 
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
</div><!-- //마이페이지 > 1:1 작성 -->

