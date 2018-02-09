<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

############### 입점기능 및 미니샵 기능 사용여부 체크(기간 체크는 관리툴에서만 한다) ###################
$vauthkey=getVenderUsed();
if($vauthkey["OK"]!="OK" || $vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl() || ($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd"))) {
	echo "<html>\n";
	echo "<head></head>\n";
	echo "<script>\n";
	echo "function vender_errmsg() {\n";
	echo "	alert(\"입점기능 및 미니샵 사용 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.\");\n";
	echo "	if(opener) {\n";
	echo "		window.close();\n";
	echo "		if(opener.parent) {\n";
	echo "			opener.parent.location.href='".$Dir."';\n";
	echo "		} else {\n";
	echo "			opener.location.href='".$Dir."';\n";
	echo "		}\n";
	echo "	} else {\n";
	echo "		if(parent) {\n";
	echo "			parent.location.href='".$Dir."';\n";
	echo "		} else {\n";
	echo "			document.location.href='".$Dir."';\n";
	echo "		}\n";
	echo "	}\n";
	echo "}\n";
	echo "</script>\n";
	echo "<body onload=\"vender_errmsg()\">\n";
	echo "</body>\n";
	echo "</html>";
	exit;
}
########################################################################################################


$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);

if(strlen($_VenderInfo->getId())==0 || strlen($_VenderInfo->getAuthkey())==0) {
	if($_SERVER['SCRIPT_NAME']!="/".RootPath.VenderDir."logout.php") {
		echo "<script>\n";
		echo "	alert(\"정상적인 경로로 다시 접속하시기 바랍니다.\");\n";
		echo "	if(opener) {\n";
		echo "		window.close();\n";
		echo "		if(opener.parent) {\n";
		echo "			opener.parent.location.href='logout.php';\n";
		echo "		} else {\n";
		echo "			opener.location.href='logout.php';\n";
		echo "		}\n";
		echo "	} else {\n";
		echo "		if(parent) {\n";
		echo "			parent.location.href='logout.php';\n";
		echo "		} else {\n";
		echo "			document.location.href='logout.php';\n";
		echo "		}\n";
		echo "	}\n";
		echo "</script>\n";
		exit;
	}
}

if(!$isaccesspass) {
	$_VenderInfo->VenderAccessCheck();

	$_venderdata=$_VenderInfo->getVenderdata();
	$sql = "SELECT * FROM tblshopinfo ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$_venderdata->shopname=$row->shopname;
	$_venderdata->etcfield=$row->etcfield;

	if(strlen(RootPath)>0) {
		$hostscript=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$pathnum=@strpos($hostscript,RootPath);
		$shopurl=substr($hostscript,0,$pathnum).RootPath;
	} else {
		$shopurl=$_SERVER['HTTP_HOST']."/";
	}

	$_venderdata->shopurl=$shopurl;

	if(MinishopType=="ON") {
		$minishopurl=$shopurl."minishop/".$_venderdata->id;
	} else {
		$minishopurl=$shopurl."minishop.php?storeid=".$_venderdata->id;
	}

	/*if($_venderdata->disabled==1) {
		echo "<script>\n";
		echo "	alert(\"해당 업체는 승인 대기상태이므로 이용이 불가능합니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.\");\n";
		echo "	if(opener) {\n";
		echo "		window.close();\n";
		echo "		if(opener.parent) {\n";
		echo "			opener.parent.location.href='logout.php';\n";
		echo "		} else {\n";
		echo "			opener.location.href='logout.php';\n";
		echo "		}\n";
		echo "	} else {\n";
		echo "		if(parent) {\n";
		echo "			parent.location.href='logout.php';\n";
		echo "		} else {\n";
		echo "			document.location.href='logout.php';\n";
		echo "		}\n";
		echo "	}\n";
		echo "</script>\n";
		exit;
	}*/

	include("cache.php");
}
