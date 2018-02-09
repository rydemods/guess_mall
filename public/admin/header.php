<?php // hspark
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
include_once("../lib/adminlib.php");
include_once("../conf/config.php");

?>
<html>
<head>
<title>관리자 페이지</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="style.css">
<link type="text/css" rel="stylesheet" href="static/css/style.css">
<link type="text/css" rel="stylesheet" href="static/css/util.css">
<link rel='shortcut icon' href="../static/img/common/favicon.ico" type="image/x-ico" >
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="static/js/ui.js"></script>
<script language="JavaScript">
var bottomtxt = "쇼핑몰 관리자 페이지에 오신것을 환영합니다.";
function _shopstatus() {
	window.status = bottomtxt;
	timerID= setTimeout("_shopstatus()", 30);
}
_shopstatus();

function menu_over(name){
	$("#on_dd"+name).toggle();
	$("#on_dt"+name).toggleClass("this");
}

function on_menu(num){
	for(i=0;i<$("input[name='menu_open[]']").length;i++){

		$("#on_dd"+i).css("display","none");
		$("#on_dt"+i).removeClass("this");

	}
		$("#on_dd"+num).css("display","block");
		$("#on_dt"+num).addClass("this");

}

function hiddenLeft(){
	if($("#leftMenus").css("display")!="none"){
		$("#leftMenus").css("display","none");
		$("#close_img_off").css("display","block");
		$("#menu_width").css("width","38");

	}else{
		$("#leftMenus").css("display","block");
		$("#close_img_off").css("display","none");
		$("#menu_width").css("width","240");

	}
}

</script>
</head>
<!--
<body text="black" link="blue" vlink="purple" alink="red" class="bg" oncontextmenu="return false" oncontextmenu="alert('붙여넣기를 하시려면 Control키 + V를 같이 누르시면 됩니다.');return false;">
-->
<body text="black" link="blue" vlink="purple" alink="red" class="bg">
