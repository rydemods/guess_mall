<?
	function connectKakaoToken($snsKtConfig){
		$CLIENT_ID     = $snsKtConfig["restKey"]; 
		$REDIRECT_URI  = urlChange( $snsKtConfig["callbackUrl"] );
		$TOKEN_API_URL = "https://kauth.kakao.com/oauth/token";
        $sslVerifypeer = false;

		$code   = $_GET["code"];
		$params = sprintf( 'grant_type=authorization_code&client_id=%s&redirect_uri=%s&code=%s', $CLIENT_ID, $REDIRECT_URI, $code);

        // ssl curl 2016-12-08 유동혁
        $tmpUrl = parse_url( $REDIRECT_URI );
        if( $tmpUrl['scheme'] == 'https' ){
            $sslVerifypeer = true;
        }

		$opts = array(
			CURLOPT_URL => $TOKEN_API_URL,
			CURLOPT_SSL_VERIFYPEER => $sslVerifypeer,
			CURLOPT_SSLVERSION => 1,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false
		);

		$curlSession = curl_init();
		curl_setopt_array($curlSession, $opts);
		$accessTokenJson = curl_exec($curlSession);
		curl_close($curlSession);

		return $accessTokenJson;
	}

	function connectKakaoUser($decodeToken){
		$TOKEN_API_URL = "https://kapi.kakao.com/v1/user/me";
        $sslVerifypeer = false;

        // ssl curl 2016-12-08 유동혁
        if( $_SERVER['HTTPS'] == "on" ){
            $sslVerifypeer = true;
        }

		$opts = array(
			CURLOPT_URL => $TOKEN_API_URL,
			CURLOPT_SSL_VERIFYPEER => $sslVerifypeer,
			CURLOPT_SSLVERSION => 1,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $decodeToken->access_token
			)
		);
		 
		$curlSession = curl_init();
		curl_setopt_array($curlSession, $opts);
		$accessTokenJsonUser = curl_exec($curlSession);
		curl_close($curlSession);

		return $accessTokenJsonUser;
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

?>