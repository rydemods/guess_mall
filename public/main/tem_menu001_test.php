<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<link rel="stylesheet" type="text/css" href="<?=$Dir?>css/style_eco.css" />
<!--<link rel="stylesheet" href="<?=$Dir?>css/tem_001.css" />-->
<?
	if ($_data->frame_type!="N") include($Dir.MainDir.$_data->onetop_type.".php");
	else if($_data->align_type=="Y") echo "<center>";
?>



<SCRIPT LANGUAGE="JavaScript">
<!--
var quickview_path="<?=$Dir.FrontDir?>product.quickview.xml.php";
var quickfun_path="<?=$Dir.FrontDir?>product.quickfun.xml.php";
function sendmail() {
	window.open("<?=$Dir.FrontDir?>email.php","email_pop","height=100,width=100");
}
function estimate(type) {
	if(type=="Y") {
		window.open("<?=$Dir.FrontDir?>estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
	} else if(type=="O") {
		if(typeof(top.main)=="object") {
			top.main.location.href="<?=$Dir.FrontDir?>estimate.php";
		} else {
			document.location.href="<?=$Dir.FrontDir?>estimate.php";
		}
	}
}
function privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function order_privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function logout() {
	location.href="<?=$Dir.MainDir?>main.php?type=logout";
}
function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}
function memberout() {
	if(typeof(top.main)=="object") {
		top.main.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	} else {
		document.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	}
}
function notice_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
	}
}
function information_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
	}
}
function GoPrdtItem(prcode) {
	window.open("<?=$Dir.FrontDir?>productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}

<?if($_data->layoutdata["MOUSEKEY"][3]=="Y"){?>
function funkeyclick() {
    if (navigator.appName=="Netscape" && (e.which==3 || e.which==2)) return;
    else if (navigator.appName=="Microsoft Internet Explorer" && (event.button==2 || event.button==3 || event.keyCode==93)) return;

    if(navigator.appName=="Microsoft Internet Explorer" && (event.ctrlKey && event.keyCode==78)) return false;
}
document.onmousedown=funkeyclick;
document.onkeydown=funkeyclick;
<?}?>
//-->
</SCRIPT>

<?
if($_data->layoutdata["SHOPBGTYPE"][0]=="B") {			//배경색 설정
	echo "<style>\n";
	if($_data->layoutdata["SHOPBGTYPE"][1]=="Y") {
		echo "#tableposition { background-color: transparent; }\n";
	} else {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	}
	if($_data->layoutdata["BGCOLOR"][0]=="N") {
		echo "BODY {background-color: ".(strlen(substr($_data->layoutdata["BGCOLOR"],1,7))==7?substr($_data->layoutdata["BGCOLOR"],1,7):"#FFFFFF")."}\n";
	} else {
		echo "BODY {background-color: transparent}\n";
	}
	echo "</style>\n";
} else if($_data->layoutdata["SHOPBGTYPE"][0]=="I") {	//백그라운드 설정
	echo "<style>\n";
	if($_data->layoutdata["SHOPBGTYPE"][1]=="N") {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	} else {
		echo "#tableposition { background-color: transparent; }\n";
	}
	if(file_exists($Dir.DataDir."shopimages/etc/background.gif")) {
		echo "BODY {\n";
		echo "background-image: url('".$Dir.DataDir."shopimages/etc/background.gif');\n";
		$background_repeat=array("A"=>"repeat","B"=>"repeat-x","C"=>"repeat-y","D"=>"no-repeat");
		echo "background-repeat: ".$background_repeat[$_data->layoutdata["BACKGROUND"][2]].";\n";
		$background_position=array("A"=>"top left","B"=>"top center","C"=>"top right","D"=>"center left","E"=>"center center","F"=>"center right","G"=>"bottom left","H"=>"bottom center","I"=>"bottom right");
		echo "background-position: ".$background_position[$_data->layoutdata["BACKGROUND"][1]].";\n";
		if($_data->layoutdata["BACKGROUND"][0]=="Y") {
			echo "background-attachment: fixed;\n";
		}
	}
	echo "</style>\n";
}
?>

