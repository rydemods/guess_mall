<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
/*

안쓰는 파일



*/


?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>push 발송타겟</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}
//-->
</SCRIPT>

</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>push 발송타겟</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>push 발송타겟</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
	<div class="push_pop">
        <ul>
			<li>회원선택 : <span>사용자 전체</span></li>  
            <li>구매이력 회원 : <span>오늘</span></li>            
        </ul>
  		<div class="txt">
			<span class="blue bold">0,000,000</span>건<br /> 
			<p class="date"><span>YYYY</span>-<span>MM</span>-<span>DD</span> 00시 00분</p>
			Push를 발송하겠습니까?
		</div>
    	<div class="btn_center">
    		<img src="images/btn_send.gif" alt="발송"/>
            <img src="images/btn_cancel2.gif" alt="취소"/>
        </div>
  	</div>
	<!-- 높이 210px -->
</body>
