<? 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."lib/coupon.class.php");

    // 2016-06-28 기획전 등에서 url 로 쿠폰 받을때 요청했던 첫페이지로 돌아가기 위해..
    if($_GET['ret_url']) {
        // 로긴을 거쳐왔을 경우..
        $ret_url = substr($_SERVER["QUERY_STRING"], strpos($_SERVER["QUERY_STRING"], "ret_url=")+8);
    }else{
        // 이미 로긴상태일때
        $ret_url = $_SERVER['HTTP_REFERER'];
    }

    if($ret_url == "") $ret_url = "/m/";
    //exdebug($ret_url);
    //exit;

	if(strlen($_MShopInfo->getMemid())==0) {
		//Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
        // 2016-06-28 비로긴상태일때, 원래 요청했던 페이지정보를 가지고 감.
		Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl()."&ret_url=".$ret_url);
		exit;
	}
	

	//$coupon	= '59154325';
	//$encrypt_coupon	= encrypt_md5("COUPON|6|".$coupon,"*ghkddnjsrl*");
	$encrypt_coupon	= $_GET['coupon'];
	$decrypt_coupon	= decrypt_authkey($encrypt_coupon);
	$exp_coupon			= explode("|", $decrypt_coupon);

	//exdebug($exp_coupon);

	$memid	= $_MShopInfo->getMemid();
	//echo $memid;
	//exit;

	#쿠폰 설정 
	$_CouponInfo = new CouponInfo( $exp_coupon[1] ); 
	//exdebug($_CouponInfo);

	#쿠폰 확인 
	$msg = $_CouponInfo->search_coupon( $exp_coupon[2], $memid ); 
	if( $msg == '0' ){
		$alert_text	= "해당하는 쿠폰이 없습니다.";
	} else if( $msg == '4' ) {
		//$alert_text	= "이미 발급된 쿠폰 입니다.";
		$alert_text	= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '5' ) {
		//$alert_text	= "같은 쿠폰의 사용하지 않은 쿠폰이 존재 합니다.";
		$alert_text	= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '1' ) {
		$_CouponInfo->set_couponissue( $memid ); 
		if ($_CouponInfo->issue_type != '0') {
			#insert 설정 
			$return_data = $_CouponInfo->insert_couponissue(); 
			if( $return_data[0] === 0 ) {
				$alert_text	= "쿠폰이 발급되었습니다.";
			} else {
				$alert_text	= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.1";
			}
		} else {
			$alert_text	= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.2";
		}
	}

	//echo "<html><head></head><body onload=\"alert('".$alert_text."');location.replace('/m/');\"></body></html>";exit;
    // 2016-06-28 요청했던 페이지로 되돌림.
    echo "<html><head></head><body onload=\"alert('".$alert_text."');location.replace('".$ret_url."');\"></body></html>";exit;
?>