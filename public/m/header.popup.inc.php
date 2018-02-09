<?
if(substr(getenv("SCRIPT_NAME"),-15)=="/header.inc.php") {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>		
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="format-detection" content="telephone=no" />
	<?if($facebook_meta1){?>
		<?=$facebook_meta1?>
		<?=$facebook_meta2?>
		<?=$facebook_meta3?>
	<?}?>
    <title><?=$_data->shoptitle?></title>
    <meta name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
    <meta name="keywords" content="<?=$_data->shopkeyword?>">
<?php
    if (file_exists($Dir.DataDir."shopimages/etc/favicon.png") === true) {
        $favicon = "favicon.png";
    } else if (file_exists($Dir.DataDir."shopimages/etc/favicon.ico") === true) {
        $favicon = "favicon.ico";
    }
    if (strlen($favicon) > 0 ) {
?>
	<link rel="shortcut icon" type="image/x-icon" href="<?=$Dir.DataDir."shopimages/etc/".$favicon?>" /> 
<?php
    }
?>
	<!--<link type="text/css" rel="stylesheet" media="screen" href="./style/001/style.css" /> -->
	<link type="text/css" rel="stylesheet" media="screen and (min-width: 330px)" href="./style/001/wide.css" />
	<link type="text/css" href="css/common.css" rel="stylesheet">
	<link type="text/css" href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<?php if (file_exists($Dir.DataDir."shopimages/etc/mobile_web_icon.png") === true) { ?>
    <link rel="apple-touch-icon-precomposed" href="<?=$Dir.DataDir?>shopimages/etc/mobile_web_icon.png"/>
<?php } else { ?>
	<link rel="apple-touch-icon-precomposed" href="images/shopgo.png" />
<?php } ?>
	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../js/jcarousellite_1.0.js"></script>
	<script type="text/javascript" src="js/jquery.slides.js"></script>
	<script type="text/javascript" src="js/jquery.metadata.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/kakao.link.js"></script>
	<script type="text/javascript">
    var StringBuffer = function() {
    	this.buffer = new Array();
    };
    StringBuffer.prototype.append = function(str) {
    	this.buffer[this.buffer.length] = str;
    };

    StringBuffer.prototype.toString = function() {
    	return this.buffer.join("");
    };

	$(function() {
		setTimeout(scrollTo, 0, 0, 1);
		
		$('a[target="_blank"]').click(function() {
			if (confirm('This link opens in a new window.')) {
                return true;
            } else {
                return false;
            }
		});

		$('li.back a').click(function() {
			window.history.back();
			return false;
		});
	});
	</script>

	<script type="text/javascript">
	function openSearchTop(){
		if(document.getElementById("layerSearchForm").style.display == "none"){
			document.getElementById("topSearchBtn").src = "./style/001/images/btn_close3.gif";
			document.getElementById("topSearchBtn").width = "26";
			document.getElementById("topSearchBtn").height = "28";
			document.getElementById("layerSearchForm").style.display = "";
			document.getElementById("layerSearchForm").className = "searchLayer";
		}else{
			document.getElementById("topSearchBtn").src = "./style/001/images/btn_search4.gif";
			document.getElementById("topSearchBtn").width = "26";
			document.getElementById("topSearchBtn").height = "28";
			document.getElementById("layerSearchForm").style.display = "none";
			document.getElementById("layerSearchForm").className = "";
		}
	}
	</script>

	<script type="text/javascript">
	var backurls = "";
	if(history.length == 1) {
		backurls = "";
	} else {
		backurls = "javascript:history.back();";
	}
	function fn_GoBackurl(strback) {
		var ressdata = "";
		if(backurls.length==0) {
			resdata = "";
		} else {
			resdata = "<a href='"+strback+"'><img src='style/001/images/icon_arrow.gif' width='33' height='27' alt='Back' /></a>";
		}
		return resdata;
	}
	</script>

</head>
<body>