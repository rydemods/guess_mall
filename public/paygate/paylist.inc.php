<?php
//header("Content-Type: text/html; charset=UTF-8");
//$goodname=titleCut(27,$goodname);
# 주문 금액 최종 ( ordersend에서는 가격을 나누어서 넣는다 )
// 2015 11 19 유동혁
//$last_price = $last_price + $deli_price - $usereserve - $dc_price;
#카드쿠폰 추가
if( count( $tmp_use_card ) == 1 ){
	$use_card = $tmp_use_card[0];
	$used_card_yn = 'Y';
} else if( count( $tmp_use_card ) > 1 ){
	$use_card = implode( ':', $tmp_use_card );
	$used_card_yn = 'Y';
} else {
	$used_card_yn = 'N';
}

if($pg_type=="A") {
    // ======================================================
    // KCP 
    // ======================================================

	if (file_exists($Dir.DataDir."shopimages/etc/cardimg_kcp.gif")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_kcp.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_kcp.jpg")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_kcp.jpg";
	else $sitelogo = "";

	$sitecd=$pgid_info["ID"];
	$sitekey=$pgid_info["KEY"];
	if (strstr("QP", $paymethod)) $escrow="Y";
	else $escrow="N";

    $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) ) {
        // 모바일 기기인 경우 모바일 pg로 연결
        $pgurl=$Dir."m/paygate/card_gate.php";
    } else {
        $pgurl=$Dir."paygate/".$pg_type."/charge_exe.php";
    }

	$pgurl .= "?sitecd=".$sitecd."&sitekey=".urlencode($sitekey)."&escrow=".$escrow."&paymethod=".$paymethod."&goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode)."&buyername=".urlencode($sender_name)."&buyermail=".urlencode($sender_email)."&buyertel1=".urlencode($sender_tel)."&buyertel2=".urlencode($sender_tel)."";
	if($escrow=="Y") {
		$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";
	}
	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
	$pgurl.="&use_card=".$use_card."&used_card_yn=".$used_card_yn;

} else if($pg_type=="B") {
	if (file_exists($Dir.DataDir."shopimages/etc/cardimg_dacom.gif")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_dacom.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_dacom.jpg")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_dacom.jpg";
	else $sitelogo = "";

	$mid=$pgid_info["ID"];
	$mertkey=$pgid_info["KEY"];
	if (strstr("QP", $paymethod)) $escrow="Y";
	else $escrow="";

	$pgurl=$Dir."paygate/".$pg_type."/charge.php?memid=".$_ShopInfo->getMemid()."&shopname=".urlencode($_data->shopname)."&companynum=".$_data->companynum."&mid=".$mid."&mertkey=".$mertkey."&escrow=".$escrow."&paymethod=".$paymethod."&goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode)."&pid=".encrypt_md5($sender_resno)."&buyername=".urlencode($sender_name)."&buyermail=".urlencode($sender_email)."&buyertel=".urlencode($sender_tel)."&receiver=".urlencode($receiver_name)."&receivertel=".urlencode($receiver_tel1)."";

	$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";

	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
} else if($pg_type=="C") {
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

	$pgurl=$Dir."paygate/".$pg_type."/charge.php?storeid=".$storeid."&storenm=".urlencode(titleCut(47,$_data->shopname))."&ordno=".urlencode($ordercode)."&prodnm=".urlencode($goodname)."&amt=".$last_price."&userid=".$_ShopInfo->getMemid()."&useremail=".urlencode($sender_email)."&ordnm=".urlencode($sender_name)."&ordphone=".urlencode($sender_tel)."&rcpnm=".urlencode($receiver_name)."&rcpphone=".urlencode($receiver_tel1)."&escrow=".$escrow."&paymethod=".$paymethod."&hp_id=".$hp_id."&hp_pwd=".encrypt_md5($hp_pwd)."&hp_unittype=".$hp_unittype."&hp_subid=".$hp_subid."&prodcode=".$prodcode;

	//."&companynum=".$_data->companynum."&pid=".encrypt_md5($sender_resno)."";

	$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";

	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
} else if($pg_type=="D") {
	if (file_exists($Dir.DataDir."shopimages/etc/cardimg_inipay.gif")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_inipay.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_inipay.jpg")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_inipay.jpg";
	else $sitelogo = "";

	if (file_exists($Dir.DataDir."shopimages/etc/cardimgleft_inipay.gif")) 
		$sitelogoleft = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimgleft_inipay.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimgleft_inipay.jpg")) 
		$sitelogoleft = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimgleft_inipay.jpg";
	else $sitelogoleft = "";

	if (strstr("QP", $paymethod)) { 
		$escrow="Y";
		$sitecd=$pgid_info["EID"];
		$pgurl=$Dir."paygate/".$pg_type."/escrow/charge.php";
	} else {
		$escrow="N";
		$sitecd=$pgid_info["ID"];
		$hpunittype=$pgid_info["HP_UNITType"];
		$pgurl=$Dir."paygate/".$pg_type."/charge.php";
	}
	$pgurl.="?sitecd=".$sitecd."&escrow=".$escrow."&paymethod=".$paymethod."&goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode)."&hpunittype=".$hpunittype."&buyername=".urlencode($sender_name)."&buyermail=".urlencode($sender_email)."&buyertel1=".urlencode($sender_tel)."&buyertel2=".urlencode($sender_tel)."";
	$pgurl.="&receivername=".urlencode($receiver_name)."&receivertel=".urlencode($receiver_tel11.$receiver_tel12.$receiver_tel13)."&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";
	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo)."&sitelogoleft=".urlencode($sitelogoleft);
} else if($pg_type=="E" || $pg_type=="F" ) {
    // ====================================================================================
    // 다날 휴대폰 결제, 페이코 결제
    // ====================================================================================

    $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && $pg_type == "E" ) {
        // 모바일 기기인 경우 모바일 pg로 연결
//        $pgurl=$Dir."m/paygate/card_gate.php";

        $pgurl=$Dir."paygate/" . $pg_type . "_M/charge.php";
    } else {
        $pgurl=$Dir."paygate/".$pg_type."/charge.php";
    }

    $session_val = isset($_SESSION["ACCESS"]) ? $_SESSION["ACCESS"] : "";

	$pgurl .= "?paymethod=".$paymethod."&goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode)."&buyername=".urlencode($sender_name)."&buyermail=".urlencode($sender_email)."&buyertel1=".urlencode($sender_tel)."&buyertel2=".urlencode($sender_tel)."";
	$pgurl .= "&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
	$pgurl .= "&use_card=".$use_card."&used_card_yn=".$used_card_yn."&cpname=".urlencode(titleCut(47,$_data->shopname));
    $pgurl .= "&session_val=".$session_val;

/*
    $pgurl  = $Dir."danal/Web/Ready.php";
    $pgurl .= "?goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode);
*/
} else if( $pg_type == "G" ){
    // ====================================================================================
    // NICEPAY
    // ====================================================================================
	if (file_exists($Dir.DataDir."shopimages/etc/cardimg_kcp.gif")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_kcp.gif";
	else if (file_exists($Dir.DataDir."shopimages/etc/cardimg_kcp.jpg")) 
		$sitelogo = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/etc/cardimg_kcp.jpg";
	else $sitelogo = "";

    $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

    if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT'])) {
        $pgurl=$Dir."paygate/" . $pg_type . "_M/charge.php";
    } else {
        $pgurl=$Dir."paygate/".$pg_type."/charge.php";
    }
	$sitecd=$pgid_info["ID"];
	$sitekey=$pgid_info["KEY"]; // 체크요망 ."==" charge에서 처리
    $sitepw = $pgid_info["PW"];

	$pgurl .= "?sitecd=".$sitecd."&sitekey=".urlencode($sitekey)."&sitepw=".$sitepw."&escrow=".$escrow."&paymethod=".$paymethod."&goodname=".urlencode($goodname)."&price=".$last_price."&ordercode=".urlencode($ordercode)."&buyername=".urlencode($sender_name)."&buyermail=".urlencode($sender_email)."&buyertel1=".urlencode($sender_tel)."&buyertel2=".urlencode($sender_tel)."";
	if($escrow=="Y") {
		$pgurl.="&rpost=".$rpost."&raddr1=".urlencode($raddr1)."&raddr2=".urlencode($raddr2)."";
	}
	$pgurl.="&quotafree=".$card_splittype."&quotamonth=".$card_splitmonth."&quotaprice=".$card_splitprice."&sitelogo=".urlencode($sitelogo);
	$pgurl.="&use_card=".$use_card."&used_card_yn=".$used_card_yn;
}
