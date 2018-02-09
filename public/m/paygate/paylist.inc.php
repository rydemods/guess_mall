<?php
$goodname=titleCut(27,$goodname);

if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.gif")) 
	$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.gif";
else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_allthegate.jpg")) 
	$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_allthegate.jpg";
else $sitelogo = "";

$storeid=$pgid_info["ID"];
$hp_id=$pgid_info["HP_ID"];
$hp_pwd=$pgid_info["HP_PWD"];
$hp_unittype=$pgid_info["HP_UNITType"];
$hp_subid=$pgid_info["HP_SUBID"];
$prodcode=$pgid_info["ProdCode"];

if (strstr("QP", $paymethod)) $escrow="Y";
else $escrow="";


if(strstr($_SERVER[HTTP_USER_AGENT],"Mobile")){
	$mobile = ".mobile";
}

$pgurl=$Dir."m/paygate/AGS_pay.php?storeid=".$storeid."&storenm=".urlencode(titleCut(47,$_data->shopname))."&ordno=".urlencode($ordercode)."&prodnm=".urlencode($goodname)."&amt=".$last_price."&userid=".$_ShopInfo->getMemid()."&useremail=".urlencode($sender_email)."&ordnm=".urlencode($sender_name)."&ordphone=".urlencode($sender_tel)."&rcpnm=".urlencode($receiver_name)."&rcpphone=".urlencode($receiver_tel1)."&escrow=".$escrow."&paymethod=".$paymethod."&hp_id=".$hp_id."&hp_pwd=".encrypt_md5($hp_pwd)."&hp_unittype=".$hp_unittype."&hp_subid=".$hp_subid."&prodcode=".$prodcode;

//."&companynum=".$_data->companynum."&pid=".encrypt_md5($sender_resno)."";

$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";

$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
