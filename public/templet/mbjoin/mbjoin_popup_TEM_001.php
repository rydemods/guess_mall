 <?php
/********************************************************************* 
// 파 일 명		: mbjoin_popup_TEM_001.php 
// 설     명		: 회원가입(팝업형) 템플릿
// 상세설명	: 회원가입(팝업형) 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2015.10.28 - 김재수
//						- 2015.12.21 - 김재수 (추천 이메일 추가)
// 
*********************************************************************/ 
?>
<!-- 메인 컨텐츠 -->
<h3 class="tit_pop">회원가입</h3>
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type=hidden name=type value="">
<input type=hidden name=email_checked id=email_checked>
<input type="hidden" id="email_check" value="0" />
<input type="hidden" id="rec_id" name="rec_id" value="" />
<?php if($recom_ok!="Y"){?>
<input type="hidden" name="rec_email">
<?}?>
<div class="popup_layer_block">
    <ul class="list_join">
        <li>
        <h4 class="stit_pop">이메일 <span>(아이디로 사용됩니다.) </h4>
        <input type="text" name="email" title="이메일 입력" tabindex="1">
        </li>
        <li>
        <h4 class="stit_pop">비밀번호 <span>(영문 숫자 조합 8자 이상으로 입력해주세요.) </h4>
        <input type="password" id="user-pass" name="passwd1" title="비밀번호 입력" tabindex="2">
        </li>
        <li>
        <h4 class="stit_pop">비밀번호 확인 <span>(위 입력한 비밀번호를 다시 한번 입력해주세요.) </h4>
        <input type="password" id="identify-pass" name="passwd2" title="이메일 입력" tabindex="3">
        </li>
		<?php
			if($recom_ok=="Y"){
		?>
        <li>
        <h4 class="stit_pop">추천인 이메일 <span>(추천인이 있을 경우 이메일을 입력해주세요.)</h4>
        <input type="text" name="rec_email" title="추천인 이메일 입력" tabindex="4">
        </li>
		<?php
			}
		?>
        <li>
        <h4 class="stit_pop">개인정보 이용약관 동의 <label><input type="checkbox" name='agree'>약관에 동의</label> </h4>
        <div class="agree">
            <?=$agreement?>
        </div>
        <h4 class="sbox_pop"><input type="checkbox" name="news_mail_yn" value='Y' id="news_mail_yn">이메일 수신동의 <input type="checkbox" name="news_sms_yn" value='Y' id="news_sms_yn">SMS 수신동의</h4>
        </li>
    </ul>
    <div class="btn_group_l">
        <a href="javascript:CheckForm('');" class="btn_black fl">회원가입</a>
        <a href="javascript:facebook_open('/front/member_join_facebook.php');" class="btn_blue fr"><img src="../images/common/ico_facebook.gif" alt=" "> 페이스북으로 회원가입</a>
    </div>
    <div class="pop_info_list">
        <ul class="info_pop_list">
            <li>이미 교육할인 스토어 회원이세요? <a href="javascript:parent.pushLogin();">로그인해주세요</a></li>
        </ul>
    </div>
</div>
</form>
<!-- //메인 컨텐츠 -->