<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	
	<title>룰렛</title>

	<script type="text/javascript" src="js/embedFlash.js"></script>
	<script type="text/javascript">
		
		function goLogin(){
			alert("로그인 하세요");
			location.href = "../login.php"; 			/*?chUrl= 붙이장*/
		}
		
		function noChange(){
			alert("참여기회가 없습니다");
		}
		
		function goClose(){
			//alert("팝업 닫기, 페이지 새로고침");			/* 페이지를 닫을것인가?*/
		}
	
		function resultError(_value){
			// setdata 의 resultNum 값이 1 ~ 7 이 아니면 호출
			alert("오류가 발생했습니다. 잠시 후 다시 참여해 주세요. errorCode : " + _value);
		}
	
	</script>

</head>

<body>

	<div>
		<script type="text/javascript">
			// 아래 getUrl 과 setUrl 부분에 php 경로를 상대 또는 절대로 넣어줌
			// 페이지 로드 시 getdata를 불러와 플래시를 세팅하고, START 버튼을 누르면 setdata를 불러와 룰렛의 결과 값을 결정함
			// 관리자에서 불러오는 이미지 경로는 getdata 에 등록되고, 이미지 사이즈는 psd 에 가이드 영역 표시
			embed_flash("roulette.swf", "rouletteFlash", "500", "500", "getUrl=getdata.php&setUrl=setdata.php", "transparent");
		</script>
	</div>

</body>

</html>