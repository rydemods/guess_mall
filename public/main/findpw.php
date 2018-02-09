<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");


	if(strlen($_ShopInfo->getMemid())>0) {
 		//Header("Location:/main");
		//exit;
	}
	
?>
<!doctype html>
<html lang="ko">

<head>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"> --><!-- [D] 2016-07-15 업체 요청으로 변경 -->
	<meta name="viewport" content="width=device-width">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
	<meta name="naver-site-verification" content="c987a802f9de2166805938fea147976a77a089d7">
	<meta name="description" content="핫티 HOT TREND PLEX STORE - 프리미엄 멀티 플랙스 스토어 " />
    <meta name="keywords" content="나이키, 아디다스, 뉴발란스, 리복, 푸마, 테바, 라코스테, 케즈, 슈페르가, 크록스, 스케쳐스, 블루마운틴, 컨버스, 버켄스탁, 닥터마틴, 멀티샵, 운동화, 신발, 샌들 " />

    <title>핫티 공식 온라인 스토어</title>
    
    <link rel="stylesheet" href="../static_imsi/css/common.css">
    <link rel="stylesheet" href="../static_imsi/css/component.css">
    <link rel="stylesheet" href="../static_imsi/css/content.css">
	<link rel="canonical" href="http://www.hot-t.co.kr/">
	
	<script src="../static_imsi/js/jquery-1.12.0.min.js"></script>
	<script src="../static_imsi/js/ui.js"></script>

	<script type="text/javascript">
	
	function CheckForm() {
		var name	= "";
		var id			= "";
		var email	= "";

		
		try {
			
			if(document.form1.id.value.length==0) {
				alert("회원 아이디를 입력하세요.");
				document.form1.id.focus();
				return;
			}
			if(document.form1.name.value.length==0) {
				alert("이름을 입력하세요.");
				document.form1.name.focus();
				return;
			}

			if(document.form1.mobile.value.length==0) {
				alert("휴대폰번호를 입력하세요.");
				document.form1.mobile.focus();
				return;
			}

			if(document.form1.change_password.value.length==0) {
				alert("변경하실 비밀번호를 입력하세요.");
				document.form1.change_password.focus();
				return;
			}
			id=document.form1.id.value;
			name=document.form1.name.value;
			mobile=document.form1.mobile.value;
			change_password=document.form1.change_password.value;


			

			$.ajax({ 
				type: "POST", 
				url: "<?=$Dir.MainDir?>find_pw_indb.php", 
				data: {id:id, name:name, mobile:mobile, change_password:change_password},
				dataType:"json", 
				success: function(data) {
					alert(data.msg);
					if(data.msg!='일치하는 회원정보가 없습니다.'){
						$(location).attr('href','/main');
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
				}
			}); 
		} catch (e) {
			//alert(document.form1.passwd.value);
			alert(e);
			alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
		}
	}
	
	</script>

</head>

<body>

<div class="video-wrap">
	<iframe id="video" width="1920" height="1080" src="https://www.youtube.com/embed/U2izvP4lWWQ?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;modestbranding=1&amp;loop=1&playlist=U2izvP4lWWQ"  frameborder="0" allowFullScreen></iframe>
</div>
<form name=form1 action="find_pw_indb.php" method=post>
	<input type=hidden name=type value="">
	<input type=hidden name=signagetype value="1">
	<div id="wrap">
		<div class="comingsoon sub">
			<h1 class="title">Online store coming soon</h1>
			<div class="box">
				<a href="/main/" class="btn-close">창 닫기 버튼</a>
				<div class="inner">
					<div class="logo"><img src="../static_imsi/img/common/logo.png" alt="HOT:T"></div>
					
					<div class="login_area">
						<input type="text" name="id"  id="id"placeholder="아이디(이메일 형식)">
						<input type="text" name="name" id="name" placeholder="이름">
						<input type="tel" name="mobile" id="mobile" placeholder="휴대폰번호(-은 입력안하셔도 됩니다)">
						<input type="password" name="change_password" id="change_password" placeholder="변경할 비밀번호">
						<button type="button" onclick="CheckForm()" class="btn-point">비밀번호 변경</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
</body>

</html>

