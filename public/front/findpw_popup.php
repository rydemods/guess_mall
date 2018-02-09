<?php 
	session_start();

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="">
<meta name="Author" content="">
<meta name="Keywords" content="<?=$_data->shopkeyword?>">
<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?=$_data->shoptitle?></title>
<link href="../css/common.css" rel="stylesheet" type="text/css" />
<!-- 이전 J-QUERY 문제 발생으로 버전업 S(2015.11.23 김재수 추가)-->
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="../js/jquery-migrate-1.1.1.min.js" type="text/javascript"></script>
<!-- 이전 J-QUERY 문제 발생으로 버전업 E(2015.11.23 김재수 추가)-->
<script src="../js/common.js" type="text/javascript"></script>
<script src="../lib/lib.js.php" type="text/javascript"></script>
<script src="../plugin/kcaptcha.with.jquery/kcaptcha/md5.js" type="text/javascript"></script>
<script src="../plugin/kcaptcha.with.jquery/kcaptcha/jquery.kcaptcha.js" type="text/javascript"></script>
<?php include_once($Dir.LibDir."analyticstracking.php") ?>
<script>

	function go_submit(){		
		var email = document.findform.email.value;
		if(email.length==0){
			alert("이메일을 입력하세요."); document.findform.email.focus(); return;
		}
		
		if ($('#writekey') != 'undefined')
		{
			if (hex_md5($('#writekey').val()) != md5_norobot_key) {
				alert('자동등록방지 숫자가 틀립니다.');
				$('#writekey').select().focus();
				return;
			} else {		
				$.ajax({ 
					type: "GET", 
					url: "<?=$Dir.FrontDir?>ajax_findpw.php", 
					data: "email=" + email,
					dataType:"json", 
					success: function(data) {
						alert(data.msg);
						parent.findClose('findpw_pop','all_body');
					},
					error: function(result) {
						alert("에러가 발생하였습니다."); 
					}
				}); 
			}
		}
	}
</script>
</head>
<body>

<h3 class="tit_pop2">비밀번호 찾기</h3>
<div class="popup_layer_block">
<form name="findform" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="dinfo">
    <p class="stit_txt_pop">가입하실 때 사용한 이메일 주소를 입력해 주시면, <br>해당 이메일로 임시 비밀번호를 전송해 드립니다.</p>
    <ul class="list_login">
        <li><input type="text" name="email" id="" maxlength="100" title="이메일 입력" placeholder="이메일" ></li>
        <li><img alt=" "  class="auto_no"  id='kcaptcha_image' style="cursor:pointer;"> <input type="text" title="자동가입 입력방지 문자 입력" class="auto_txt"  name="writekey" id="writekey"></li>		
    </ul>
    <p class="inpotext_in" >자동등록방지 숫자를 순서대로 입력하세요.</p>
    <div class="btn_group_l">
        <a href="javascript:go_submit();" class="btn_black auto">임시 비밀번호 받기</a>
    </div>
    <div class="pop_info_list">
        <ul class="info_pop_list">
            <li>
            아직 교육 할인 스토어 회원이 아니세요? <a href="javascript:parent.pushJoin();">회원가입을 해주세요.</a> <br >
            <p class="txt_pop_info">대학생만 회원가입이 가능합니다.</p>
            </li>
        </ul>
    </div>
</form>
</div>

</body>
</html>
