<?php
/*
* admin/sns_list.php
* access_token 을 받아올 때의 return_uri 는 Instagram에 등록되어있어야 한다.
*/
$code = $_GET['code'];
if($code){
$insta = new Instagram;
	$s_check	= $_COOKIE[insta_brand];
	if ($s_check == '1') {
		$insta->client_id	= Instagram::$vk_client_id;
		$insta->client_secret	= Instagram::$vk_client_secret;
	} else if ($s_check == '2') {
		$insta->client_id	= Instagram::$sg_client_id;
		$insta->client_secret	= Instagram::$sg_client_secret;
	} else if ($s_check == '3') {
		$insta->client_id	= Instagram::$sgf_client_id;
		$insta->client_secret	= Instagram::$sgf_client_secret;
	} else if ($s_check == '4') {
		$insta->client_id	= Instagram::$vh_client_id;
		$insta->client_secret	= Instagram::$vh_client_secret;
	} else if ($s_check == '5') {//20180123 bshan
		$insta->client_id	= Instagram::$bb_client_id;
		$insta->client_secret	= Instagram::$bb_client_secret;
	} else if ($s_check == '6') {//20180124 bshan
		$insta->client_id	= Instagram::$si_client_id;
		$insta->client_secret	= Instagram::$si_client_secret;
	}
	$data = array("code"=>$code, "redirect_uri"=>Instagram::$redirect_uri);
	$insta->api = "oauth/access_token";
	$res = $insta->get_json($data);
	if($res){
		$token = $res->access_token;
		//setcookie("insta_token", $token, 0, "/".RootPath, getCookieDomain());
		setcookie("insta_token", $token, time()+31536000);
		echo "<script>window.opener.location='/admin/sns_list.php?mode=search&s_check={$s_check}';self.close();</script>";
		exit;
	}
}

/**
* Instagram API
* Sendbox 모드로 사용하며, Sendbox user로 등록된 사용자만 조회할 수 있다.
* 
* User : cashstores (운영서버)
* client_id : 88f94ff7a006483683ef53e45a3d63f5
* client_secret : ebf47638d7d54799ba833b2033d70b04
* redirect_uri : http://www.cash-stores.com/admin/sns_list.php
*/
class Instagram {
	public $debug = false;
	public $domain = "https://api.instagram.com";
	public static $vk_client_id = "8cec8c68981b4d088f3d2972de69850d";
	public static $vk_client_secret = "a0f077f0ae5f4350873bce8a60507a61";
	public static $sg_client_id = "bade05c28f4c46ce84fff07f7cffa452";
	public static $sg_client_secret = "49782a73ea5c483984f67f6e8817116c";
	public static $sgf_client_id = "acde497f5b12446bb6f9efb3bc3206e8";
	public static $sgf_client_secret = "a9a09f70cedf472ca21f320ff8c2ce55";
	public static $vh_client_id = "55d16b08d6f1418995c5f025fed1a32f";
	public static $vh_client_secret = "f0a0879095754a67a1ca84fb72aa372c";
	public static $bb_client_id = "95954c84630b4df6937bd939e23ae943";				//20180123 bshan
	public static $bb_client_secret = "50eee21b7a094e9393d7282b323a0138";			//20180123 bshan
	public static $si_client_id = "cd79a5f91fc5457e8946469b3e9040b8";				//20180143 bshan
	public static $si_client_secret = "e08132db1e764ef8b0c8853d7debeba1";			//20180124 bshan
	public static $redirect_uri = "http://shinwonmall.com/admin/sns_list.php";

	public $client_id = "8cec8c68981b4d088f3d2972de69850d";
	public $client_secret = "a0f077f0ae5f4350873bce8a60507a61";
	public $grant_type = "authorization_code";
	public $method = 1; //0=GET, 1=POST

	function __construct($mode=0) {
		if($this->debug){
			echo "<h3>Debug</h3>\r\n";
			echo "<table style='width:800px'>\r\n";
		}
	}

	function close_table() {
		if($this->debug) echo "</table>\r\n";
	}

	public function debug($param, $subtitle="") {
		if($this->debug){
			$bt = debug_backtrace();
			$method = $bt[1]["class"]."::".$bt[1]["function"];
			if($subtitle) $method = "{$method}<br />→<strong>{$subtitle}</strong>";
			echo "<tr>\r\n";
			echo "<td>{$method}</td><td class='debug'><xmp>";
			print_r($param);
			echo "</xmp></td>\r\n";
			echo "</tr>\r\n";
		}
	}

	public function get_json($data){
		if($this->api=="oauth/access_token"){
			$data["client_id"]     = $this->client_id;
			$data["client_secret"] = $this->client_secret;
			$data["grant_type"]    = $this->grant_type;
		}
		foreach($data as $name => $value){
			$t_param[] = "{$name}={$value}";
		}
		$this->debug($this->api,"API");
		$this->debug($data,"Input DATA");
		//this->param = mb_convert_encoding(implode("&",$t_param), 'EUC-KR', 'UTF-8');
		$this->param = implode("&",$t_param);
		$this->url = "{$this->domain}/{$this->api}";
		if(!$this->method) $this->url .= "?".$this->param;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, $this->method);
		if($this->method) curl_setopt($ch, CURLOPT_POSTFIELDS, $this->param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('charset="utf-8"'));
		$result = curl_exec($ch);
		curl_close($ch);
		$this->debug($result,"Output");
		$this->close_table();
		return json_decode($result);
	}
}