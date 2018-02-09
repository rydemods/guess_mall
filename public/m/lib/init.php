<?
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
define("DirPath", $Dir);
define("RootPath", "");

define('BLADE_EXT', '.htm'); 
define("AdminDir", "admin/");
define("MainDir", "main/");
define("AdultDir", "adult/");
define("AuctionDir", "auction/");
define("BoardDir", "board/");
define("FrontDir", "front/");
define("GongguDir", "gonggu/");
define("PartnerDir", "partner/");
define("RssDir", "rss/");
define("TempletDir", "templet/");
define("SecureDir", "ssl/");
define("VenderDir", "vender/");
define("CashcgiDir", "cash.cgi/");
define("AuthkeyDir", "authkey/");
define("LibDir", "lib/");
define("MDir", "m/");

define("DataDir", "data/");
define("ImageDir", DataDir."shopimages/");

define("MinishopType", "OFF");

#암호/복호화 키입니다. (해당 쇼핑몰에서 꼭 수정하시기 바랍니다.)
define("enckey", "password");

#시스템 관리자 메일
define("AdminMail", ""); 
