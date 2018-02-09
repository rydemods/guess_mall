<?php
/********************************************************************* 
// 파 일 명		: login_TEM_001.php 
// 설     명		: 로그인 템플릿
// 상세설명	: 회원 로그인 템플릿
// 작 성 자		: hspark
// 수 정 자		: 2015.10.28 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보를 설정한다.
#---------------------------------------------------------------
	$sel_hide[$mode] = 'style="display:none"';	//선택하지 않은 레이어는 숨기기 위해
	$sel_on[$mode] =' class=on';	// 선택한 탭을 on 시키기 위해
	
	if(!$chUrl){
	$chUrl=trim(urldecode($_SERVER["HTTP_REFERER"]));
	}

	$page_code = "login";
?>
<!-- 메인 컨텐츠 -->
<h3 class="tit_pop">LOGIN</h3>
<div class="popup_layer_block">
<form action="[FORM_ACTION]" method="post" name="form1">
<input type=hidden name=chUrl value="<?=$chUrl?>">
    <ul class="list_login">
        <li><input type="text" class="id_pw" name="email" id="" maxlength="100" onblur="document.form1.passwd.focus(); "onkeypress="if(event.keyCode==13){CheckForm();}" title="이메일 입력" placeholder="이메일"/></li>
        <li><input type="password" class="id_pw" name="passwd" maxlength="20" id="" onkeypress="if(event.keyCode==13){CheckForm();}"  title="비밀번호 입력" placeholder="비밀번호"/></li>
        <li><input type="checkbox" id="login_save" name='emailsave'><label for="login_save">로그인 유지하기 <span>(해당 컴퓨터에서만 가능합니다.)</span></label></li>
    </ul>
    <div class="btn_group_l">
        <a href="javascript:;" onclick="JavaScript:CheckForm();" class="btn_black fl">로그인</a>
        <a href="javascript:;" onclick="javascript:facebook_open('/front/member_join_facebook.php?access=1');" class="btn_blue fr"><img src="../images/common/ico_facebook.gif" alt=" "> 페이스북으로 로그인</a>
    </div>
    <div class="pop_info_list">
        <ul class="info_pop_list">
            <li>
            아이디가 없으세요? <?if (get_session('rf_url_id')) {?><a href="javascript:;" onclick="javascript:parent.pushJoin()"><?} else {?><a href="javascript:;" onclick="parent.location.href='<?=$Dir.FrontDir."store_member.php"?>';"><?}?>회원가입을 해주세요.</a> <br >
            <p class="txt_pop_info">대학생만 회원가입이 가능합니다.</p>
            </li>
            <li><a href="javascript:;" onclick="javascript:parent.pushFind();" >비밀번호를 잊으셨나요?</a></li>
        </ul>
    </div>
</form>
<form action="[FORM_ACTION]" method="post" name="form11">
<input type=hidden name=chUrl value="<?=$chUrl?>">
<input type=hidden name=facebook_id value="">
<input type=hidden name=facebook_email value="">
<input type=hidden name=facebook_name value="">
<input type=hidden name=facebook_token value="">
</form>
</div>
<!-- //메인 컨텐츠 -->