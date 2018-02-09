<?

	// library 로드, 변수 설정 등
	include_once("/plugin/sns/facebook/src/facebookoauth.php");


	function connectFacebookUser($consumer_key, $consumer_secret){
		$connection = new FacebookOAuth($consumer_key, $consumer_secret);
		$access_token = $connection->getAccessToken($_REQUEST['code']);

		$token = $access_token['oauth_token'];
		$connection = new FacebookOAuth($consumer_key, $consumer_secret, $token);

		$parameters['fields'] = "id,name,email,birthday,link";
		$user = $connection->get('me', $parameters);

		return $user;
	}
?>