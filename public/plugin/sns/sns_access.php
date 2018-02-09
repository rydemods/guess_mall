<?php
	session_start();
	#---------------------------------------------------------------
	# 기본정보 설정파일을 가져온다.
	#---------------------------------------------------------------
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."conf/config.sns.php");

	$sns			= $_REQUEST['sns'];
	$sns_login = $_REQUEST['sns_login'];
	$sns_ac		= $_REQUEST['ac'];
	$sns_churl	= $_REQUEST['churl'];

	function goto_url_frame($url)
	{
		$url = str_replace("&amp;", "&", $url);

		echo '<script>';
		echo 'parent.location.replace("'.$url.'");';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
		echo '</noscript>';
	}
    // ssl 변경 2016-12-08 유동혁
    function urlChange( $url ){

        $returnURL = $url;

        if( empty( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == "off" ){
            // http
        } else {
            // https
            $tmpRedirectURL = parse_url( $url );
            if( $tmpRedirectURL['scheme'] == 'http' ){
                $returnURL = str_replace( 'http', 'https', $url );
            }
        }

        return $returnURL;

    }

	$_ShopInfo->setCheckSns($sns);
	$_ShopInfo->setCheckSnsLogin($sns_login);
	$_ShopInfo->setCheckSnsAccess($sns_ac);
	$_ShopInfo->setCheckSnsChurl($sns_churl);
	$_ShopInfo->Save();
	//============================================================================
	// 페이스북 로그인
	//----------------------------------------------------------------------------
	if($sns == $snsFbConfig["use"]){


		$consumer_key = $snsFbConfig["authKey"];
		$domain = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $domain = urlChange( $domain );
		$args = "scope=email&client_id=" . $consumer_key. "&display=popup&redirect_uri=" . $domain . 'plugin/sns/sns_proc.php';
		$uri = "https://graph.facebook.com/oauth/authorize?" . $args;
		goto_url_frame($uri);


	}else if($sns == $snsNvConfig["use"]){


		require './naver/class.naverOAuth.php';
		$nid_ClientID = $snsNvConfig['clientId'];
		$nid_ClientSecret = $snsNvConfig['clientSecret'];
		$nid_RedirectURL = urlChange( $snsNvConfig['callbackUrl'] );
		$request = new OAuthRequest( $nid_ClientID, $nid_ClientSecret, $nid_RedirectURL );
		$request -> set_state();
		$request -> request_auth();


	}else if($sns == $snsKtConfig["use"]){

		$restApiKey = $snsKtConfig["restKey"];
		$redirectUri = urlChange( $snsKtConfig["callbackUrl"] );
		$args = "client_id=".$restApiKey."&redirect_uri=".$redirectUri."&response_type=code";
		$uri = "https://kauth.kakao.com/oauth/authorize?" . $args;

		goto_url_frame($uri);
	}
	/*
	else if($sns == $snsItConfig["use"]){

		$restApiKey = $snsItConfig["clientId"];
		$redirectUri = $snsItConfig["callbackUrl"];
		//https://www.instagram.com/oauth/authorize/?client_id=aaf8c26c12c74cf1b649df32744909e0&redirect_uri=http://test2-sejung.ajashop.co.kr/plugin/sns/sns_proc.php&response_type=code&scope=basic+public_content+follower_list+comments+relationships+likes
		//https://www.instagram.com/oauth/authorize/?client_id=CLIENT-ID&redirect_uri=REDIRECT-URI&response_type=code
		
		$args = "client_id=".$restApiKey."&redirect_uri=".$redirectUri."&response_type=code";
		$uri = "https://www.instagram.com/oauth/authorize/?" . $args;
		goto_url_frame($uri);
	}
	*/
?>