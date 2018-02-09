<?php
/*********************************************************************
// 파 일 명		: membermodify_TEM_001.php
// 설     명		: 회원정보 수정/관리 HTML
// 상세설명	: 마이페이지에서 회원정보 수정 HTML
// 작 성 자		: hspark
// 수 정 자		: 2015.10.30 - 김재수
//
//
*********************************************************************/
?>
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">회원정보 수정</h2>

		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<?php
			include ($Dir.FrontDir."mypage_TEM01_left.php");
			?>
			<!-- //LNB -->
			<article class="my-content">
				
				<fieldset>
					<legend>회원가입 양식 폼</legend>
					<p class="ta-r fz-13 txt-toneB"><strong class="point-color">*</strong>표시는 필수항목입니다.</p>
					<table class="th-left mt-10">
						<caption>회원가입 양식</caption>
						<colgroup>
							<col style="width:178px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label>이름</label></th>
								<td>
								<?
								if ($auth_type!='old') {
								?>
									<?=$name?>
								<?
								} else {
								?><input type="text" id="user-name" name="name" value="<?=$name?>" title="이름을 입력하세요." tabindex="1" maxlength="20"><?}?></td>
							</tr>
							<tr>
								<th scope="row"><label>생년월일</label></th>
								<td>
								<?
								if ($auth_type!='old') {
								?>
									<?=$birth1?>년 <?=$birth2?>월 <?=$birth3?>일
								<?
								} else {
								?>
									<div class="select">
										<select id="birth1" name="birth1" style="width:70px" tabindex="2" >
										<?for($y=2017;$y >= 1900;$y--) {?>
											<option value="<?=$y?>"><?=$y?></option>
										<?}?>
										</select>
									</div>
									<span class="txt">-</span>
									<div class="select">
										<select id="birth2" name="birth2" style="width:50px" tabindex="2" >
										<?for($m=1;$m <= 12;$m++) {?>
											<option value="<?=$m<10?'0'.$m:$m?>"><?=$m?></option>
										<?}?>
										</select>
									</div>
									<span class="txt">-</span>
									<div class="select">
										<select id="birth3" name="birth3" style="width:50px" tabindex="2" >
										<?for($d=1;$d <= 31;$d++) {?>
											<option value="<?=$d<10?'0'.$d:$d?>"><?=$d?></option>
										<?}?>
										</select>
									</div>
								<?}?>
									<div class="radio ml-20">
										<input type="radio" name="lunar" id="lunarA" value="1"<?=$lunar=='1'?' checked':''?><?if ($auth_type!='old') {?> disabled='disabled'<?}?> >
										<label for="lunarA">양력</label>
									</div>
									<div class="radio ml-10">
										<input type="radio" name="lunar" id="lunarB" value="0"<?=$lunar=='0'?' checked':''?><?if ($auth_type!='old') {?> disabled='disabled'<?}?>>
										<label for="lunarB">음력</label>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label>성별</label></th>
								<td>
								<?
								if ($auth_type!='old') {
								?>
									<?
									
										if($gender=='1' || $gender=='3'){
										 	echo "남자";
										}
										if($gender=='2' || $gender=='4'){	
											echo "여자";	
										}
									
									?>
								<?
								} else {
								?>
									<div class="radio ml-20">
										<input type="radio" name="gender" id="genderA" value="1"<?=$gender=='1' || $gender=='3'?' checked':''?>>
										<label for="genderA">남자</label>
									</div>
									<div class="radio ml-10">
										<input type="radio" name="gender" id="genderB" value="2"<?=$gender=='2' || $gender=='4'?' checked':''?>>
										<label for="genderB">여자</label>
									</div>
								<?}?>
								</td>
							</tr>
							<tr>
								<th scope="row"><label>아이디</label></th>
								<td><?=$id?></td>
							</tr>
							<tr>
								<th scope="row"><label for="mbReg_pw1" class="essential">비밀번호</label></th>
								<td>
									<div class="input-cover">
										<input type="password" style="width:270px" id="mbReg_pw1" name="passwd1" title="비밀번호 입력자리" placeholder="영문,숫자 포함 8~20자리">
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="mbReg_pw2" class="essential">비밀번호 확인</label></th>
								<td>
									<div class="input-cover">
										<input type="password" style="width:270px" id="mbReg_pw2" name="passwd2" title="비밀번호 재입력자리" placeholder="비밀번호 재입력">
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label>주소</label></th>
								<td>
									<input type="hidden" id="home_post1" name = 'home_post1' value="<?=$home_post1?>">
									<input type="hidden" id='home_post2' name = 'home_post2' value="<?=$home_pos2?>">

									<ul class="input-multi input-cover">
										<li><input type="text" name = 'home_zonecode' id = 'home_zonecode' value="<?=$home_post?>" title="우편번호 입력자리" style="width:125px" readonly>
											<button class="btn-basic" onclick="openDaumPostcode();return false;" ><span tabindex="5">주소찾기</span></button></li>
										<li><input type="text" name = 'home_addr1' id = 'home_addr1' value="<?=$home_addr1?>" title="검색된 주소" class="w100-per" readonly ></li>
										<li><input type="text" name = 'home_addr2' id = 'home_addr2' value="<?=$home_addr2?>" title="상세주소 입력" class="w100-per" tabindex="6"></li>
									</ul>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="home_tel1">전화번호</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select id="home_tel1" name="home_tel1" style="width:110px" tabindex="7">
												<option value="02" <?if($home_tel_arr[0]=="02"){?>selected<?}?>>02</option>
												<option value="031" <?if($home_tel_arr[0]=="031"){?>selected<?}?>>031</option>
												<option value="032" <?if($home_tel_arr[0]=="032"){?>selected<?}?>>032</option>
												<option value="033" <?if($home_tel_arr[0]=="033"){?>selected<?}?>>033</option>
												<option value="041" <?if($home_tel_arr[0]=="041"){?>selected<?}?>>041</option>
												<option value="042" <?if($home_tel_arr[0]=="042"){?>selected<?}?>>042</option>
												<option value="043" <?if($home_tel_arr[0]=="043"){?>selected<?}?>>043</option>
												<option value="044" <?if($home_tel_arr[0]=="044"){?>selected<?}?>>044</option>
												<option value="051" <?if($home_tel_arr[0]=="051"){?>selected<?}?>>051</option>
												<option value="052" <?if($home_tel_arr[0]=="052"){?>selected<?}?>>052</option>
												<option value="053" <?if($home_tel_arr[0]=="053"){?>selected<?}?>>053</option>
												<option value="054" <?if($home_tel_arr[0]=="054"){?>selected<?}?>>054</option>
												<option value="055" <?if($home_tel_arr[0]=="055"){?>selected<?}?>>055</option>
												<option value="061" <?if($home_tel_arr[0]=="061"){?>selected<?}?>>061</option>
												<option value="062" <?if($home_tel_arr[0]=="062"){?>selected<?}?>>062</option>
												<option value="063" <?if($home_tel_arr[0]=="063"){?>selected<?}?>>063</option>
												<option value="064" <?if($home_tel_arr[0]=="064"){?>selected<?}?>>064</option>
											</select>
										</div>
										<span class="txt">-</span>
										<input type="text" class="numbersOnly" id="home_tel2" name="home_tel2" value="<?=$home_tel_arr[1]?>" title="선택 전화번호 가운데 입력자리" style="width:110px" tabindex="8" maxlength="4">
										<span class="txt">-</span>
										<input type="text" class="numbersOnly" id="home_tel3" name="home_tel3" value="<?=$home_tel_arr[2]?>" title="선택 전화번호 마지막 입력자리" style="width:110px" tabindex="9" maxlength="4">
										<input type="hidden" name="home_tel" id="home_tel" value="<?=$home_tel?>">									
										
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="mobile1" class="essential">휴대폰 번호</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select id="mobile1" name="mobile1" style="width:110px" tabindex="10"  <?=$mobile_arr[0]!="" && $auth_type!='old'?" disabled=\"disabled\"":""?>>
												<option value="010" <?if($mobile_arr[0]=="010"){?>selected<?}?>>010</option>
												<option value="011" <?if($mobile_arr[0]=="011"){?>selected<?}?>>011</option>
												<option value="016" <?if($mobile_arr[0]=="016"){?>selected<?}?>>016</option>
												<option value="017" <?if($mobile_arr[0]=="017"){?>selected<?}?>>017</option>
												<option value="018" <?if($mobile_arr[0]=="018"){?>selected<?}?>>018</option>
												<option value="019" <?if($mobile_arr[0]=="019"){?>selected<?}?>>019</option>
											</select>
										</div>
										<span class="txt">-</span>
										<input type="text" class="numbersOnly" id="mobile2" name="mobile2" value="<?=$mobile_arr[1]?>" title="필수 휴대폰 번호 가운데 입력자리" style="width:110px" tabindex="11"<?=$mobile_arr[1]!="" && $auth_type!='old'?" readonly":""?> maxlength="4">
										<span class="txt">-</span>
										<input type="text" class="numbersOnly"  id="mobile3" name="mobile3" value="<?=$mobile_arr[2]?>" title="필수 휴대폰 번호 마지막 입력자리" style="width:110px" tabindex="12"<?=$mobile_arr[2]!="" && $auth_type!='old'?" readonly":""?> maxlength="4">
										<input type="hidden" name="mobile" id="mobile" value="<?=$mobile?>">
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="email" class="essential">이메일</label></th>
								<td>
									<div class="input-cover">
										<input type="text" id="email1" name="email1" value="<?=$email_arr[0]?>" style="width:190px" title="이메일 입력" tabindex="14" onChange="javascript:$('input[name=email_checked]').val('0');" >
										<span class="txt">@</span>
										<input type="text" id="email2" name="email2" value="<?=$email_arr[1]?>" title="도메인 직접 입력" class="ml-10" style="width:170px;<?if($email_com!="custom"){?>display: none;<?}?>" onChange="javascript:$('input[name=email_checked]').val('0');"> <!-- [D] 직접입력시 인풋박스 출력 -->
										&nbsp;<div class="select" >
											<select style="width:170px" tabindex="15" id="email_com" onchange="customChk(this.value);" >
												<option value="">선택</option>
												<option value="custom" <?if($email_com=="custom"){?>selected<?}?>>직접입력</option>
												<option value="naver.com" <?if($email_com=="naver.com"){?>selected<?}?>>naver.com</option>
												<option value="daum.net" <?if($email_com=="daum.net"){?>selected<?}?>>daum.net</option>
												<option value="gmail.com" <?if($email_com=="gmail.com"){?>selected<?}?>>gmail.com</option>
												<option value="nate.com" <?if($email_com=="nate.com"){?>selected<?}?>>nate.com</option>
												<option value="yahoo.co.kr" <?if($email_com=="yahoo.co.kr"){?>selected<?}?>>yahoo.co.kr</option>
												<option value="lycos.co.kr" <?if($email_com=="lycos.co.kr"){?>selected<?}?>>lycos.co.kr</option>
												<option value="empas.com" <?if($email_com=="empas.com"){?>selected<?}?>>empas.com</option>
												<option value="hotmail.com" <?if($email_com=="hotmail.com"){?>selected<?}?>>hotmail.com</option>
												<option value="msn.com" <?if($email_com=="msn.com"){?>selected<?}?>>msn.com</option>
												<option value="hanmir.com" <?if($email_com=="hanmir.com"){?>selected<?}?>>hanmir.com</option>
												<option value="chol.net" <?if($email_com=="chol.net"){?>selected<?}?>>chol.net</option>
												<option value="korea.com" <?if($email_com=="korea.com"){?>selected<?}?>>korea.com</option>
												<option value="netsgo.com" <?if($email_com=="netsgo.com"){?>selected<?}?>>netsgo.com</option>
												<option value="dreamwiz.com" <?if($email_com=="dreamwiz.com"){?>selected<?}?>>dreamwiz.com</option>
												<option value="hanafos.com" <?if($email_com=="hanafos.com"){?>selected<?}?>>hanafos.com</option>
												<option value="freechal.com" <?if($email_com=="freechal.com"){?>selected<?}?>>freechal.com</option>
												<option value="hitel.net" <?if($email_com=="hitel.net"){?>selected<?}?>>hitel.net</option>
											</select>
											<input type="hidden" id="email" name="email" value="<?=$email?>">
										</div>
										
										<button class="btn-basic" onclick="ValidFormEmail('1','');return false;"><span>중복확인</span></button>
									</div>
								</td>
							</tr>
							
							<tr>
								<th scope="row"><label for="" class="">추가정보</label></th>
								<td>
									<div class="input-cover">
										<label>
											<span class="fz-13 pr-5">키(cm)</span>
											<input type="text" class="numbersOnly" name="height" value="<?=$height?>" title="키" style="width:50px" maxlength="3" tabindex="16">
										</label>
										<label class="pl-20">
											<span class="fz-13 pr-5">몸무게(kg)</span>
											<input type="text" class="numbersOnly"  name="weigh" value="<?=$weigh?>" title="몸무게" style="width:50px"  maxlength="3" tabindex="17">
										</label>
										<span class="fz-12 pl-20">※ 추가정보 모두 입력시 <?=$reserve_join_over?> E포인트 적립</span>
										
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="my_job">직업</label></th>
								<td>
									<div class="input-cover">
										<div class="select" id="my_job" title="직업 선택">
											<select style="width:190px" name="job_code">
												<option value="">선택</option>
												<option value="01" <?if($job_code=="01"){?>selected<?}?>>주부</option>
												<option value="02" <?if($job_code=="02"){?>selected<?}?>>자영업</option>
												<option value="03" <?if($job_code=="03"){?>selected<?}?>>사무직</option>
												<option value="04" <?if($job_code=="04"){?>selected<?}?>>생산/기술직</option>
												<option value="05" <?if($job_code=="05"){?>selected<?}?>>판매직</option>
												<option value="06" <?if($job_code=="06"){?>selected<?}?>>보험업</option>
												<option value="07" <?if($job_code=="07"){?>selected<?}?>>은행/증권업</option>
												<option value="08" <?if($job_code=="08"){?>selected<?}?>>전문직</option>
												<option value="09" <?if($job_code=="09"){?>selected<?}?>>공무원</option>
												<option value="10" <?if($job_code=="10"){?>selected<?}?>>농축산업</option>
												<option value="11" <?if($job_code=="11"){?>selected<?}?>>학생</option>
												<option value="12" <?if($job_code=="12"){?>selected<?}?>>기타</option>
											</select>
											<input type="hidden" name="job" id="job" value="<?=$job?>">
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label>마케팅 활동 동의</label></th>
								<td>
									<div class="mrk-agree">
										<p>신원몰이 제공하는 다양한 이벤트 및 혜택 안내에 대한 수신동의 여부를 확인해주세요.</p>
										<p>수신 체크 시 고객님을 위한 다양하고 유용한 정보를 제공합니다.</p>
										<div class="mt-10">
											<div class="checkbox">
												<input type="checkbox" id="news_mail_yn" name="news_mail_yn" value="Y" <?=$checked['news_mail_yn']['Y']?>>
												<label for="news_mail_yn">이메일 수신</label>
											</div>
											<div class="checkbox ml-60">
												<input type="checkbox" id="news_sms_yn" name="news_sms_yn" value="Y" <?=$checked['news_sms_yn']['Y']?>>
												<label for="news_sms_yn">SMS 수신</label>
											</div>
											<div class="checkbox ml-60">
												<input type="checkbox" id="mrkAgree_talk" name="news_kakao_yn" value="Y" <?=$checked['news_kakao_yn']['Y']?>>
												<label for="mrkAgree_talk">카카오톡 수신</label>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="btnPlace mt-40">
						<button type="button" onclick="CheckForm('1');return false;" class="btn-point h-large w250" tabindex="20"><span>확인</span></button>
					</div>
				</fieldset>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->