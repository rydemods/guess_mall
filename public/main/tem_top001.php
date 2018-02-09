<?
// 퇴사자 강제 로그아웃 2018년1월4일 이전 로그아웃 안한 회원
//	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		$staff_id = substr($_ShopInfo->getMemid(), 0, 2);
		if($_ShopInfo->staff_yn == "N" && $staff_id == "sw"){
			list($log_data)=pmysql_fetch_array(pmysql_query("select date from tblmemberlog where id='".$_ShopInfo->getMemid()."' order by date desc limit 1" ));
			if($log_data < "20180104000000"){
				$logouturl = $Dir.MainDir."main.php?type=logout";
				Header("Location: ".$logouturl);
			}
		}
//	}
?>
<?php
/*********************************************************************
// 파 일 명		: tem_top001.php
// 설     명		: 상단 템플릿
// 상세설명	: 상단 ( 대메뉴, 검색, 로그인, 회원가입) 템플릿
// 작 성 자		: 2015.11.02 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
// 수 정 자		: 2017.01.20 - 위민트
//
*********************************************************************/


shopSslChange(); // ssl 처리 2016-12-08 유동혁

include_once($Dir."lib/basket.class.php");  // 장바구니 내용을 구하기 위해서

// 쿼리 위민트 170205
include_once("tem_top001_sql.php");


$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';

// 모바일인지 pc인지 체크
if(preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && !$_GET[pc]) {

    $m_referrer_tmp			= parse_url($_SERVER['HTTP_REFERER']);
    $m_referrer_url			= $m_referrer_tmp['host'];

	if ((strpos($_SERVER["REQUEST_URI"],'/front/') !== false || strpos($_SERVER["REQUEST_URI"],'/board/') !== false) && $m_referrer_url != $_SERVER['HTTP_HOST']) { // 서브페이지로 올 경우에만 적용하고 아닐경우는 index.php 에서 경로 재설정을 한다.
		//게시판일 경우
		if ($_GET['board']) {
			$mainurl= str_replace('/board/','/m/',$_SERVER["REQUEST_URI"]);
			if ($_GET['pagetype'] == 'view') { // 상세보기 일 경우
				if ($_GET['board'] == 'event') { // 이벤트 상세 보기일 경우
					$mainurl= "/m/event_view.php";
				} else {
					$mainurl= "/m/board_view.php";
				}
				$mainurl .= "?board=".$_GET['board']."&boardnum=".$_GET['num'];
			}
		} else {
			$mainurl= str_replace('/front/','/m/',$_SERVER["REQUEST_URI"]);
			$mainurl= str_replace('csfaq.php','customer_faq.php',$mainurl); // FAQ 경로 재설정
		}
		//echo $mainurl;
		Header("Location: ".$mainurl);
		exit;
	}
}

// productlist.php 의 code
$productlist_code   = $_GET['code'];
$productlist_code_a = substr($productlist_code, 0, 3);

// productdetail.php 의 productcode
$productdetail_code = $_GET['productcode'];

list($code_a,$code_b,$code_c,$code_d) = sscanf($productlist_code,'%3s%3s%3s%3s');
$code=$code_a.$code_b.$code_c.$code_d;
$thisCate = getDecoCodeLoc( $code );
$thisCate2 = getDecoCodeLoc($productdetail_code);

// 매장코드
$bridx      		= $_GET['bridx'];

//1:1 문의를 위한 회원 데이터를 가져온다.
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
    $hptemp	= explode('-',$row->mobile);
    $c_hp0		= $hptemp[0];
    $c_hp1		= $hptemp[1];
    $c_hp2		= $hptemp[2];
    $c_email	= $row->email;
}
pmysql_free_result($result);

#상품 페이스북 공유
$facebook_share = '';
if( $_GET['productcode'] ){
    $facebook_share = FacebookShare( $_GET['productcode'] );
    $twitter_share = TwitterShare( $_GET['productcode'] );
}

#프로모션 페이스북, 트위터 메타테그 생성 (2016-03-17 김재수 추가)
 if (strpos($_SERVER["REQUEST_URI"],'promotion_detail.php') !== false && $_GET['idx']) {

	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, thumb_img from  tblpromo WHERE idx = '".$_GET['idx']."'"));

	if( is_file($Dir.'/data/shopimages/timesale/'.$share_img) ){
		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/timesale/".$share_img;
	}

	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }else if(strpos($_SERVER["REQUEST_URI"],'magazine_detail.php') !== false && $_GET['no']){
 	//매거진 상세 페이스북, 트위터 메타태그 추가(2016-09-24)
 	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tblmagazine WHERE no = '".$_GET['no']."'"));

 	if( is_file($Dir.'/data/shopimages/magazine/'.$share_img) ){
 		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/magazine/".$share_img;
 	}

 	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
 	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
 	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
 	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
 	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
 	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

 	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
 	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
 	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }else if(strpos($_SERVER["REQUEST_URI"],'lookbook_view.php') !== false && $_GET['no']){
 	//룩북 상세 페이스북, 트위터 메타태그 추가(2016-09-24)
 	list($share_title, $share_content, $share_img)=pmysql_fetch_array(pmysql_query("select  title, content, img_file from  tbllookbook WHERE no = '".$_GET['no']."'"));

 	if( is_file($Dir.'/data/shopimages/lookbook/'.$share_img) ){
 		$share_thumb_img = "http://".$_SERVER[HTTP_HOST]."/data/shopimages/lookbook/".$share_img;
 	}

 	$facebook_share  = "<meta property='og:site_name' content='".$_data->shoptitle."'/>\n";
 	$facebook_share .= "<meta property=\"og:type\" content=\"website\" />\n";
 	$facebook_share .= "<meta property=\"og:title\" content=\"".$_data->shoptitle."\" />\n";
 	$facebook_share .= "<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" />\n";
 	$facebook_share .= "<meta property=\"og:description\" content=\"이벤트 - ".addslashes($share_title)."\" />\n";
 	$facebook_share .= "<meta property=\"og:image\" content=\"".$share_thumb_img."\" />\n";

 	$twitter_share  = "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
 	$twitter_share .= "<meta name=\"twitter:site\" content=\"@".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:title\" content=\"".$_data->shoptitle."\">\n";
 	$twitter_share .= "<meta name=\"twitter:description\" content=\"이벤트 - ".addslashes($share_title)."\">\n";
 	$twitter_share .= "<meta name=\"twitter:image\" content=\"".$share_thumb_img."\">\n";
 }



/*
// =====================================================================================================================================
// 장바구니
// =====================================================================================================================================
$Basket = new Basket();
$arrProdCode = array();
if($Basket->basket){
foreach( $Basket->basket as $bkVal ){
    array_push($arrProdCode, $bkVal->productcode);
}
}
$basket_products_html = MakeHeaderPreviewList('basket', count($arrProdCode), get_product_list($arrProdCode), '/front/basket.php');

// =====================================================================================================================================
// 위시리스트
// =====================================================================================================================================
$arrProdCode = array();
if ( $_ShopInfo->getMemid() != "" ) {
    $sql  = "SELECT productcode FROM tblwishlist WHERE id = '" . $_ShopInfo->getMemid() . "' ORDER BY wish_idx desc ";
    $result = pmysql_query($sql);

    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrProdCode, $row['productcode']);
    }
}
$wishlist_products_html = MakeHeaderPreviewList('wish', count($arrProdCode), get_product_list($arrProdCode), '/front/wishlist.php');

// =====================================================================================================================================
// 최근 본 상품
// =====================================================================================================================================
$today_product = today_product();
$recent_view_products_html = MakeHeaderPreviewList('view', count($today_product), $today_product, '/front/lately_view.php');
*/
//

// =====================================================================================================================================
// 검색어 리스트
// =====================================================================================================================================
$arrSearchKeyword = explode( ",", $_data->search_info['keyword'] );
 
// =====================================================================================================================================
// My Keyword
// =====================================================================================================================================
$arrMyKeyword = array();
if ( $_ShopInfo->getMemid() != "" ) {
    $result = pmysql_query($sql_mykeyword);
    while ( $row = pmysql_fetch_array($result) ) {
        array_push($arrMyKeyword, $row['keyword']);
    }
}

// 공지사항 1개
list($notice_num, $notice_title) = pmysql_fetch("SELECT  num, title  FROM tblboard WHERE board = 'notice' AND notice='0' AND deleted='0' AND pos = 0 AND depth = 0 ORDER BY thread, pos LIMIT 1");
// 장바구니
if ($_ShopInfo->getMemid()) { // 로그인 했을 경우
	// SHOPPING BAG
	//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='".$_ShopInfo->getMemid()."'"));
	#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
/*	list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='".$_ShopInfo->getMemid()."' group by a.basketidx) and id='".$_ShopInfo->getMemid()."'"));*/
	list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket a left join tblproduct b on a.productcode=b.productcode WHERE 1=1 and a.id='".$_ShopInfo->getMemid()."' and b.hotdealyn='N' and b.display='Y' group by a.id"));
} else {
	// SHOPPING BAG
	//list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));
	#핫딜 상품 장바구니수량에 포함 안시키기위한 쿼리 수정2016-09-21
/*	list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket WHERE basketidx not in ( SELECT  a.basketidx FROM tblbasket a left join tblproduct b on(a.productcode=b.productcode) WHERE b.hotdealyn='Y' and id='' AND tempkey='".$_ShopInfo->getTempkey()."' group by a.basketidx) and  id='' AND tempkey='".$_ShopInfo->getTempkey()."'"));*/
	list($icon_gnb_basket_cnt)=pmysql_fetch_array(pmysql_query("select count(*) FROM tblbasket a left join tblproduct b on a.productcode=b.productcode WHERE 1=1 and a.id='' AND tempkey='".$_ShopInfo->getTempkey()."' and b.hotdealyn='N' and b.display='Y' group by a.id"));
}

//아울렛추가 201705 
if(!$brand_idx) $bridx_class_on["main"]="class='on'";
else $bridx_class_on[$brand_idx]="class='on'";

//echo $_SESSION[brand_session_no];
if($_SESSION[brand_session_no]==""){
	if($_SERVER['PHP_SELF']=="/front/outlet.php"){
		$bridx_class_on["main"]="class=''";
		$_SESSION[brand_outlet]="Y";
	}else if($_SERVER['PHP_SELF']=="/index.htm"){
		unset($_SESSION[brand_outlet]);		
	}else{
		$bridx_class_on["main"]="class=''";
	}	
}
	



?>
<!doctype html>
<html lang="ko">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=1200,user-scalable=yes,target-densitydpi=device-dpi">
	<meta name="format-detection" content="telephone=no, address=no, email=no">
	<meta name="Keywords" content="<?=$_data->shopkeyword?>">
	<meta name="Description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
    <META NAME="ROBOTS" CONTENT="INDEX, FOLLOW">
    <!-- 20170523 수정 -->
    <meta name="robots" content="index">
    <meta name="googlebot" content="index"> 

    <title><?=$_data->shoptitle?></title>

	<!-- 페이스북 쉐어 Start (2016.02.11 유동혁) -->
	<?=$facebook_share?>
	<!-- 페이스북 쉐어 End (2016.02.11 유동혁) -->
	<!-- 트위터 쉐어 Start (2016.02.11 유동혁) -->
	<?=$twitter_share?>
	<!-- 페이스북 쉐어 End (2016.02.11 유동혁) -->

	<!-- 리뉴얼 (2017.01.20 위민트) -->
  	<link rel="stylesheet" href="/sinwon/web/static/css/common.min.css">
    <link rel="stylesheet" href="/sinwon/web/static/css/component.min.css">
    <link rel="stylesheet" href="/sinwon/web/static/css/content.min.css">
    <link rel="stylesheet" href="/sinwon/web/static/css/jquery.bxslider.css">
    <link rel="stylesheet" href="/sinwon/web/static/css/jquery.mCustomScrollbar-3.1.3.min.css">
    <link rel="stylesheet" href="/sinwon/web/static/css/nouislider.css">
	
	<script src="/sinwon/web/static/js/jquery-1.12.0.min.js"></script>
	<script src="../static/js/ui_sinwon.min.js"></script>
	<script src="/sinwon/web/static/js/dev.min.js"></script>
	<script src="/sinwon/web/static/js/jquery.mCustomScrollbar.concat-3.1.3.min.js"></script>
	<script src="/sinwon/web/static/js/jquery.masonry.min.js"></script>
	<script src="/sinwon/web/static/js/placeholders.min.min.js"></script>
	<script src="/sinwon/web/static/js/jquery.bxslider.min.js"></script>
	<script src="/sinwon/web/static/js/nouislider.min.js"></script>
	<script src="/sinwon/web/static/js/wNumb.min.js"></script>
	<script src="/sinwon/web/static/js/buildV63.js"></script>
	<!-- <script src="../static/js/buildV63.js"></script> -->
	<!-- // 리뉴얼 (2017.01.20 위민트) -->
		<!-- A Square|Site Analyst MEMBER v7.5 Start -->
		<!--  엔서치 스크립트 2017-09-11 -->
		<script language='javascript'>
		var _ag   = 0 ;			// 로그인사용자 나이
		var _id   = '';    			// 로그인사용자 아이디
		var _mr   = '';        	// 로그인사용자 결혼여부 ('single' , 'married' )
		var _gd   = '';				// 로그인사용자 성별 ('man' , 'woman')
		var _ud1 = '' ;			// 사용자 정의변수 1 ( 2 ~ 10 정수값)
		var _ud2 = '' ;			// 사용자 정의변수 2 ( 2 ~ 10 정수값)
		var _ud3 = '' ;			// 사용자 정의변수 3 ( 2 ~ 10 정수값)

		var _skey = '' ;			// 내부검색어

		var _jn = '' ;				//  가입탈퇴 ( 'join','withdraw' ) 
		var _jid = '' ;				// 가입시입력한 ID

		</script>
	<!--  엔서치 스크립트 2017-09-11 -->
	
	
	<!-- 이전버젼 참고 (2017.01.20 위민트) -->
<!-- 	<link rel="stylesheet" type="text/css" href="../static/css/common.css?v=1"> -->
<!--     <link rel="stylesheet" type="text/css" href="../static/css/component.css?v=1"> -->
<!--     <link rel="stylesheet" type="text/css" href="../static/css/content.css"> -->
<!--     <link rel="stylesheet" type="text/css" href="../static/css/jquery.bxslider.css"> -->
    <!-- <link rel='shortcut icon' href="../static/img/common/hot-t.ico" type="image/x-ico" > -->
    <link rel='shortcut icon' href="../static/img/common/favicon.ico" type="image/x-ico" >

<!-- 	<script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/jquery.bxslider.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/slick.min.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/TweenMax.min.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/masonry.pkgd.min.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/select_type01.js"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/ui.js?v=12"></script> -->
<!-- 	<script type="text/javascript" src="../static/js/common_ui.js"></script> -->
	<!--// 이전버젼 참고 (2017.01.20 위민트) -->

	<!-- jquery 연속방지 js추가 2016-09-25 -->
	<script src="../js/jquery.blockUI.min.js"></script>
	<script type="text/javascript" src="../static/js/dev.min.js?v=2"></script>
	<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
	<script src="//connect.facebook.net/ko_KR/all.js"></script>
	<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
    <script type="text/javascript">
    <!--
        //console.log( $.ajaxSetup() ); 
    //-->
    </script>

    <!--[if lt IE 9]>
    <script type="text/javascript" src="/sinwon/web/static/js/html5shiv.js"></script>
    <![endif]-->

    <!-- IE8 반응형 대응 플러그인 -->
    <script type="text/javascript" src="../static/js/respond.min.js"></script>

	<!-- 공통 스크립트, 다음 주소팝업, 분석스크립트 Start (2016.07.28 - 김재수) -->
	<script src="../lib/lib.js.php" type="text/javascript"></script>
<?php if($_SERVER["REQUEST_URI"]!='/index.htm') { ?>
    <?php if( $_SERVER['HTTPS'] == 'on' ){ ?>
        <script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
    <?php }else{ ?>
        <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
    <?php } ?>
<?php } ?>
	<?php include_once($Dir.LibDir."analyticstracking.php") ?>
	<!-- 공통 스크립트, 다음 주소팝업, 분석스크립트 End (2016.07.28 - 김재수) -->

<!-- 페이스북 상세페이지 조회 이벤트 -->
<!-- Facebook Pixel Code -->
<?php
	if (strpos($_SERVER["REQUEST_URI"],'productdetail.php') !== false) { // 상세페이지
?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1962299760700853'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'ViewContent');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1962299760700853&ev=PageView&noscript=1"/></noscript>

<?php
	}else if (strpos($_SERVER["REQUEST_URI"],'basket.php') !== false) { // 장바구니
?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1962299760700853'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'AddToCart');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1962299760700853&ev=PageView&noscript=1"/></noscript>
<!-- 전환페이지 설정 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("3","1"); // 전환유형, 전환가치 설정해야함. 설치매뉴얼 참고
</script> 
<?php
	}else if (strpos($_SERVER["REQUEST_URI"],'orderend.php') !== false) { // 구매완료

	foreach( $productArr as $prKey=>$prVal ){
		$curreny = 'KRW';
		$face_arr[] = '{value:"'.$prVal['price'].'", currency: "'.$curreny.'"  }';
		$total_price+=$prVal["price"];
	}
	$face_items = implode( ',', $face_arr );
?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1962299760700853'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'Purchase', <?=$face_items?>);
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1962299760700853&ev=PageView&noscript=1"/></noscript>
<!-- 전환페이지 설정 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("1","<?=$total_price?>"); // 전환유형, 전환가치 설정해야함. 설치매뉴얼 참고
</script> 
<?php
	}else if (strpos($_SERVER["REQUEST_URI"],'member_joinend.php') !== false) { //회원가입 완료
?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1962299760700853'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'CompleteRegistration');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1962299760700853&ev=PageView&noscript=1"/></noscript>
<!-- 전환페이지 설정 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> 
<script type="text/javascript"> 
var _nasa={};
_nasa["cnv"] = wcs.cnv("2","1"); // 전환유형, 전환가치 설정해야함. 설치매뉴얼 참고
</script> 
<?php
	}else{
?>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- 페이스북 마케팅 공통  -->

<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1962299760700853'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1962299760700853&ev=PageView&noscript=1"/></noscript>
<?php
	}
?>

</head>
<body>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-99599198-1', 'auto');
  ga('send', 'pageview');

</script>
<!-- 구글 마케팅 공통  -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 852381434;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/852381434/?guid=ON&amp;script=0"/>
</div>
</noscript>


<a href="#contents" class="skip">Skip to Content</a>

<div id="header">
<!--
	<div class="header-wideBanner">
		<a href="<?=$topTitle_banner_img_row['banner_link']?>"><img src="<?=$topTitleBannerImg?>" alt=""></a>
	</div>-->
	<header class="header-wrap">
		<div class="top-menu">
			<div class="inner-align clear">
				<ul class="brand-nav clear">
					<li><a href="/" <?=$bridx_class_on["main"]?>><?=$_data->shoptitle?></a></li>
					<?
					// 상품브랜드 정보
					foreach ($brand_list as $brand){
					?>
					<li><a href="<?=$Dir.FrontDir?>brand_main.php?bridx=<?=$brand['bridx']?>" <?=$bridx_class_on[$brand['bridx']]?>><?=$brand['brandname']?></a></li>
					<?
					}
					?>
					<li><a href="/front/outlet.php?lgb=outlet" class="<?if($_SESSION[brand_outlet]=="Y" ){echo "on";};?>"><strong>OUTLET</strong></a></li>
				</ul>
				<ul class="gnb-util-menu">
					<li><a href="<?=$Dir.FrontDir?>customer_notice.php">공지사항</a></li>
					<?if(strlen($_ShopInfo->getMemid())==0){?>
					<li><a href="<?=$Dir.FrontDir?>login.php?chUrl=<?=$_SERVER[REQUEST_URI]?>">로그인</a></li>
					<li><a href="<?=$Dir.FrontDir?>member_certi.php">회원가입</a></li>
					<?}else{?>
					<li><a href="javascript:logout();">로그아웃</a></li>
					<?}?>
					<li><a href="<?=$Dir.FrontDir?>mypage.php">마이페이지</a></li>
				</ul>
			</div><!-- //.inner-align -->
		</div><!-- //.top-menu -->
		<div class="gnb-wrap clear">
			<div class="inner-align clear">
				<?
// 				echo $brand_idx;
// 				exit();
				if($brand_idx){?>
				<h1 class="header-logo"><?if(file_exists($cfg_img_path[brand_log].$brand_logo) && $brand_logo){?><a href="/"><img src="<?=$cfg_img_path[brand_log].$brand_logo?>" alt="SW eshop" style="max-width:189px"></a><?}?></h1>
				<nav class="gnb clear">
					<h2>쇼핑 카테고리</h2>
					<!--ul class="category clear"> 2017년 01월 08일 수정
						<li><a href="<?=$Dir.FrontDir?>brand_main.php?bridx=<?=$brand_idx ?>" class="c1">브랜드 소개</a></li>
						<li><a href="<?=$Dir.FrontDir?>brand_store.php?bridx=<?=$brand_idx ?>" class="c1">매장</a></li>
						<?if($brand_idx !='305') {?><li><a href="/front/lookbook_list.php?bridx=<?=$brand_idx ?>" class="c1">LOOKBOOK</a></li><?}?>
						<li><a href="/front/ecatalog_list.php?bridx=<?=$brand_idx ?>" class="c1">E-CATALOG</a></li>

					</ul-->
					
					<ul class="category clear">
						<li><a href="<?=$Dir.FrontDir?>brand_main.php?bridx=<?=$brand_idx ?>" class="c1">BRAND</a></li>
						<li><a href="/front/ecatalog_list.php?bridx=<?=$brand_idx ?>" class="c1">COLLECTION</a></li>
						<li><a href="/front/lookbook_list.php?bridx=<?=$brand_idx ?>" class="c1">LOOKBOOK</a></li>
						<?if($brand_idx == "301" || $brand_idx == "302" || $brand_idx == "303" ) { //여성복(이사베이 제외)?>
						<li><a href="<?=$Dir.FrontDir?>openguide.php?bridx=<?=$brand_idx ?>" class="c1">OPEN GUIDE</a></li>
						<?}?>
						<li><a href="<?=$Dir.FrontDir?>brand_qna.php?bridx=<?=$brand_idx ?>" class="c1">Q&amp;A</a></li>
						<li><a href="<?=$Dir.FrontDir?>brand_store.php?bridx=<?=$brand_idx ?>" class="c1">STORE</a></li><!-- 브랜드별 매장위치 20170405 -->
					</ul>
				</nav>
				<span class="divide-line"></span>
				<nav class="gnb clear">
					<h2>프로모션 카테고리</h2>
					<ul class="category clear">
						
						<li>
							<a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$brand_main_cate[$brand_idx]?>" class="c1">E-SHOP</a>
							<div class="under-c1">
								<div class="inner clear">
									<?foreach(Category_list("001") as $cl2=>$clv2){
										list($cate_b_count)=pmysql_fetch("select count(no) from tblproductbrand_cate where bridx='".$brand_idx."' and cate_code like '".$clv2->code_a.$clv2->code_b."%'");
										if($cate_b_count){
											foreach(Category_list($clv2->code_a,$clv2->code_b) as $cl3=>$clv3){
												list($cate_c_count)=pmysql_fetch("select count(no) from tblproductbrand_cate where bridx='".$brand_idx."' and cate_code like '".$clv2->code_a.$clv2->code_b.$clv3->code_c."%'");
												if($cate_c_count){
										?>
												<div class="cate-c2">
													<h3><a href="<?=$Dir.FrontDir."productlist.php?code=".$clv2->code_a.$clv2->code_b.$clv3->code_c?>"><?=$clv3->code_name?></a></h3>
													<ul>
													<?foreach(Category_list($clv2->code_a,$clv2->code_b,$clv3->code_c) as $cl4=>$clv4){
														list($cate_d_count)=pmysql_fetch("select count(no) from tblproductbrand_cate where bridx='".$brand_idx."' and cate_code like '".$clv2->code_a.$clv2->code_b.$clv3->code_c.$clv4->code_d."%'");
														if($cate_d_count){
														?>
														<li><a href="<?=$Dir.FrontDir."productlist.php?code=".$clv2->code_a.$clv2->code_b.$clv3->code_c.$clv4->code_d?>"><?=$clv4->code_name?></a></li>
														<?}?>
													<?}?>
													</ul>
												</div>
												<?}?>
											<?}?>
										<?}?>
									<?}?>
									<?
									list($gnb_banner) = pmysql_fetch("SELECT gnb_banner_img_pc FROM tblmainbrand where brand_bridx='".$brand_idx."'  and gnb_banner_img_pc !='' order by bno desc limit 1");
									$bannerImg = getProductImage($banner_imagepath, $gnb_banner);
									?>
									<div class="banner-img"><a href="<?=$temp_brand_link?>"><img src="<?=$bannerImg ?>" alt="카테고리별 배너"></a></div>
								</div>
							</div>
						</li>
					</ul>
				</nav>
				<!-- 20170705 브랜드 매장 검색기능추가 -->
				<div class="util-local">
					<button type="button" id="searchLayer-open"><span><i class="icon-zoom">검색하기</i></span></button>
					<a href="javascript:chkAuthMemLoc('<?=$Dir.FrontDir?>basket.php','pc');" class="cart"><i class="icon-cart"><?=number_format($icon_gnb_basket_cnt)?></i></a>
				</div>
				<?}else {
					// 브랜드 코드로 탑 메뉴 로고 변경
					if($bridx == null || $bridx == ''){
						
						if($_SESSION[brand_outlet]=="Y"){
							echo '<h1 class="header-logo"><a href="/front/outlet.php?lgb=outlet"><img src="/sinwon/web/static/img/common/logo_outlet.jpg" alt="SW eshop" ></a></h1>';
						}else{
							echo '<h1 class="header-logo"><a href="/"><img src="/sinwon/web/static/img/common/h1_logo.gif" alt="SW eshop" style="max-width:189px"></a></h1>'; //기존 로고 임시 주석처리(2017-12-14)
							//echo '<h1 class="header-logo"><a href="/"><img src="/sinwon/web/static/img/common/h1_logo_holiday.gif" alt="SW eshop" style="max-width:189px"></a></h1>'; //크리스마스 이후 삭제
						}
						
						
					} else {
						
						if($bridx){
						$temp_sql = "SELECT bridx,brandname,logo_img FROM tblproductbrand WHERE bridx = '".$bridx."'";
						$temp_result=pmysql_query($temp_sql,get_db_conn());
						if($temp_row=pmysql_fetch_object($temp_result)) {
							$temp_log = $temp_row->logo_img;
						}
						
						}
					}
				?>
				
				<nav class="gnb clear">
					<h2>쇼핑 카테고리</h2>
					<ul class="category clear!!">
						<li><a href="<?=$Dir.FrontDir?>promotion_detail.php?idx=104&event_type=1 " class="c1">NEW ITEM <font color='red'>SALE</font></a></li>
						<? 
						
						if($_SESSION[brand_outlet]=="Y"){
						
							
							$sql_dept1_list = $sql_dept1_out_list;
							$sql_dept2_list	= $sql_dept2_out_list;
							$sql_dept3_list	= $sql_dept3_out_list;
							
						}
						
						//echo $sql_dept2_list;
						
						$dept1_res = pmysql_query($sql_dept1_list,get_db_conn());	
						
						$param_dept_b = "";
						while($dept1_row = pmysql_fetch_object($dept1_res)){
							$dept1_name = $dept1_row->code_name;
							$param_dept_b = $dept1_row->code_b;
						?>
						<li <?if($_SESSION[brand_outlet]!="Y"){?>class="with-brand"<?}?>><!-- 여성, 남성 카테고리에만 .with-brand 클래스 추가(2017-05-26) -->
							<a href="<?=$Dir.FrontDir."productlist.php?code=".$dept1_row->cate_code?>" class="c1"><?=$dept1_name?></a>
							<div class="under-c1">
								<div class="inner clear">
									<!-- 브랜드 로고 메뉴 추가(2017-05-26) -->
									<?if($_SESSION[brand_outlet]!="Y"){?>
									<div class="cate-c2 cate-brand">
										<h3><a href="#">브랜드</a></h3>
										<ul>
											<?if($dept1_row->code_b=="001"){?>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001001&bridx=301"><img src="../static/img/common/brand_logo_bb.png" alt="BESTI BELLI"></a></li>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001001&bridx=302"><img src="../static/img/common/brand_logo_viki2.png" alt="VIKI"></a></li>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001001&bridx=303"><img src="../static/img/common/brand_logo_si2.png" alt="SI"></a></li>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001001&bridx=304"><img src="../static/img/common/brand_logo_isabey.png" alt="ISABEY"></a></li>
											<?}else if($dept1_row->code_b=="004"){?>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001004&bridx=305"><img src="../static/img/common/brand_logo_sieg_hd.png" alt="SIEG"></a></li>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001004&bridx=306"><img src="../static/img/common/brand_logo_siegf_hd.png" alt="SIEGF"></a></li>
											<li><a href="<?=$Dir.FrontDir?>productlist.php?code=001004&bridx=307"><img src="../static/img/common/brand_logo_vda_hd.png" alt="VDA"></a></li>
											
											<?}?>
										</ul>
									</div>
									<?}?>
									<!-- //브랜드 로고 메뉴 추가(2017-05-26) -->
									<?
									$sql_dept2_list_2 = str_replace("[param_dept_b]", $param_dept_b, $sql_dept2_list);
									
									$dept2_res = pmysql_query($sql_dept2_list_2,get_db_conn());
									while($dept2_row = pmysql_fetch_object($dept2_res)){
										$dept2_name = $dept2_row->code_name;
										$param_dept_c = $dept2_row->code_c;
									?>
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."productlist.php?code=".$dept2_row->cate_code?>"><?=$dept2_name?></a></h3>
										<ul>
											<?
											$sql_dept3_list_2 = str_replace("[param_dept_b]", $param_dept_b, $sql_dept3_list);
											$sql_dept3_list_3 = str_replace("[param_dept_c]", $param_dept_c, $sql_dept3_list_2);
											$dept3_res = pmysql_query($sql_dept3_list_3,get_db_conn());
											while($dept3_row = pmysql_fetch_object($dept3_res)){
												$dept3_name = $dept3_row->code_name;
											?>
												<li><a href="<?=$Dir.FrontDir."productlist.php?code=".$dept3_row->cate_code?>"><?=$dept3_name?></a></li>
											<?php }?>
										</ul>
									</div>
									<?}?>
									<?php 
									// 카테고리 배너
									$category_banner = fnGetCategoryBanner($dept1_row->cate_code);
									$bannerImg = getProductImage($banner_imagepath, $category_banner['banner_img']);
									?>
									<div class="banner-img"><a href="<?=$category_banner['banner_link']?>"><img src="<?=$bannerImg?>" alt="카테고리별 배너"></a></div>
								</div>
							</div>
						</li>
						<?
						}
						?>
						<li><a href="<?=$Dir.FrontDir?>promotion_detail.php?idx=27&event_type=1&view_mode=9cf781b93e9b540250b05da2595d47ea0.jpg&view_type=R" class="c1">수트라운지</a></li>
						<li class="brand-cate hide">
							<a href="#" class="c1">브랜드</a>
							<div class="under-c1">
								<div class="inner clear">
									<?
									// 상품브랜드 정보
									foreach ($brand_list as $brand){
									?>
									<div class="cate-c2">
										<h3>
											<a href="<?=$Dir.FrontDir?>brand_main.php?bridx=<?=$brand['bridx']?>"><?=$brand['brandname']?></a>
										</h3>
									</div>
									<?
									}
									?>
								</div>
							</div>
						</li>
					</ul>
				</nav><!-- //.gnb-menu -->
				<span class="divide-line"></span>
				<nav class="gnb clear">
					<h2>프로모션 카테고리</h2>
					<ul class="category clear">
						<li>
							<a href="#" class="c1">스타일</a>
							<div class="under-c1">
								<div class="inner clear">
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."ecatalog_list.php" ?>">E-CATALOG</a></h3>
									</div>
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."lookbook_list.php" ?>">LOOKBOOK</a></h3>
									</div>
									<!--<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."magazine_list.php" ?>">MAGAZINE</a></h3>
									</div>-->
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."instagramlist.php" ?>">INSTAGRAM</a></h3>
									</div>
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."movie_list.php" ?>">MOVIE</a></h3>
									</div>
								</div>
							</div><!-- //.under-c1 -->
						</li>
						<li><a href="<?=$Dir.FrontDir."promotion.php?ptype=special"?>" class="c1">기획전</a></li>
						<li><a href="<?=$Dir.FrontDir."promotion.php?ptype=event"?>" class="c1">이벤트</a></li>
						<!--
						<li>
							<a href="<?=$Dir.FrontDir."promotion.php"?>" class="c1">프로모션</a>
							<div class="under-c1">
								<div class="inner clear">
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."promotion.php?ptype=event"?>">이벤트</a></h3>
									</div>
		
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."promotion.php?ptype=special"?>">기획전</a></h3>
									</div>
									
									<div class="cate-c2">
										<h3><a href="<?=$Dir.FrontDir."promotion_roulette.php"?>">룰렛이벤트</a></h3>
									</div>
									
								</div>
							</div>
						</li>
						<li><a href="<?=$Dir.FrontDir."show_window.php"?>" class="c1">쇼윈도</a></li>-->
					</ul>
				</nav><!-- //.gnb-menu -->
				<div class="util-local">
					<button type="button" id="searchLayer-open"><span><i class="icon-zoom">검색하기</i></span></button>
					<a href="javascript:chkAuthMemLoc('<?=$Dir.FrontDir?>basket.php','pc');" class="cart"><i class="icon-cart"><?=number_format($icon_gnb_basket_cnt)?></i></a>
				</div>
				<?}?>
			</div><!-- //.inner-align -->
		</div><!-- //.header-gnb -->
	</header>
	<div class="header-search">
		<button type="button" class="search-close" id="searchLayer-close"><span><i class="icon-layer-close">닫기</i></span></button>
		<div class="inner">
			<div class="none-result hide"> <!-- [D] 검색결과 없는 경우 .hide 삭제 -->
				<strong class="point-color">'코트'</strong>의 검색 결과 <strong class="point-color">총 0개</strong>입니다.
			</div>
			<fieldset>
				<legend>상품 검색</legend>
				<form name=formForSearch action="../front/productsearch.php" method=get onsubmit="proSearchChk();return false;">
					<input type="text" name="search" placeholder="" title="검색어 입력">
					<button class="find" type="submit"><i class="icon-find">찾기</i></button>
				</form>
			</fieldset>
			<div class="search-keyword">
				<div class="tab" data-ui="TabMenu">
					<div class="btn clear">
						<a data-content="menu" class="active">추천 검색어</a>
						<a data-content="menu">최근 검색어</a>
					</div>
					<!-- 추천 검색어 -->
					<div class="list active" data-content="content">
						<ul>
							<?php for ( $i = 0; $i < 5; $i++ ) { // 인기키워드 중 상위 5개 ?>
								<li><a href="/front/productsearch.php?search=<?=urlencode($arrSearchKeyword[$i])?>&thr=sw"><span><?=$i+1?>.</span><?=$arrSearchKeyword[$i]?></a></li>
							<?php } ?>
						</ul>
						<?php if(count($arrSearchKeyword) == 0){?>
							<div class="none"><!-- [D] 결과 없을 시 .hide 클래스 삭제 -->
								<p class="mt-20">추천 검색어가 없습니다.</p>
							</div>
						<?php }?>
					</div>
					<!-- 최근 검색어 -->
					<div class="list" data-content="content">
						<ul>
							<?php for ( $i = 0; $i < count($arrMyKeyword); $i++ ) { ?>
                            	<li><a href="/front/productsearch.php?search=<?=urlencode($arrMyKeyword[$i])?>&thr=sw"><?=$arrMyKeyword[$i]?></a></li>
                            <?php } ?>
						</ul>
						<?php if(count($arrMyKeyword) == 0){?>
						<div class="none"><!-- [D] 결과 없을 시 .hide 클래스 삭제 -->
							<p class="mt-20">최근 검색어가 없습니다.</p>
						</div>
						<?php }?>
					</div>
				</div>
			</div><!-- //.search-keyword -->
			<ul class="none-attention hide"> <!-- [D] 검색결과 없는 경우 .hide 삭제 -->
				<li>단어의 철자 및 띄어쓰기를 확인해주세요.</li>
				<li>검색어가 올바른지 다시 한번 확인해주세요.</li>
				<li>특수문자를 제외하고 검색해주세요.</li>
			</ul>
		</div>
	</div><!-- //.header-search -->
</div><!-- //#header -->
<?php
$urls = array('/','/index2.htm','/front/productlist.php2','/front/brand_main.php','/front/brand_detail.php','/front/outlet.php','/front/lookbook_list.php','/front/ecatalog_list.php','/front/brand_store.php',
'/front/promotion_detail2.php','/front/promotion.php','/m/index.htm','/front/storeList.php','/front/instagramlist.php','/m/movie_list.php',
        '/m/productlist.php','/m/brand_main.php','/m/brand_detail.php','/m/ecatalog_list.php','/m/promotion.php','/m/lookbook_list.php','/m/promotion_detail2.php','/front/productdetail2.php','/m/productdetail2.php');
if ($HTML_CACHE_EVENT!="OK" && in_array($TEMP_SCRIPTNM,$urls) && $_SERVER['REQUEST_METHOD']=="GET" ) {

$cache_file_name2 = $_SERVER['DOCUMENT_ROOT'].'/'.DataDir.'cache/'.urlsafe_b64encode($_SERVER['REQUEST_URI']).'_.'.$b_idx;

if($_SERVER["REQUEST_URI"]=='/index.htm') {
                $coos = array();
                foreach ($_COOKIE as $key=>$val) {
                        if(strpos($key,'layerNotOpen')===0) {
                                $coos[] = substr($key,12);
                        }
                }
                asort($coos);
                $cache_file_name2 .= '~'.implode('.',$coos);
}
//$cache_file_name2 .= '@'.$_ShopInfo->getMemid();

function html_cache2($buffer) {
        global $cache_file_name2,$HTML_ERROR_EVENT;
    if(strlen($buffer)>10000) {
        file_put_contents($cache_file_name2,$buffer.pack("L",strlen($buffer)+4));
    }
        return $buffer;
}

function html_cache_out2() {
        global $cache_file_name2;

    $buffer = file_get_contents($cache_file_name2);
    list(,$len) = unpack("L",substr($buffer,-4));
    if($len==strlen($buffer)) {
        echo(substr($buffer,0,-4)); exit;
    }
}
if(strpos($TEMP_SCRIPTNM,'productdetail.php')>0) $ctime = 60*10;
        else $ctime = 60*30;


        if (file_exists($cache_file_name2) && time()-filemtime($cache_file_name2)<$ctime) {
                html_cache_out2();
        } else {
                $HTML_CACHE_EVENT="OK";
                ob_start("html_cache2");
        }
}
?>
<!-- 카운트..제발 지우지 좀 마!!!! -->
<span class="hide"><?=$_data->countpath?></span>
<!-- ajax loading img -->
<div class="dimm-loading" id="dimm-loading">
	<div id="loading"></div>
</div>
<!-- // ajax loading img-->
