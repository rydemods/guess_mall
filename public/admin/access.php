<?php // hspark
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$connect_ip = $_SERVER["REMOTE_ADDR"];
$id         = $_ShopInfo->getId();
$authkey    = $_ShopInfo->getAuthkey();
$shopurl    = $_ShopInfo->getShopurl();
if (ord($id) == 0 || ord($authkey) == 0) {
    echo "<script>
    alert(\"정상적인 경로로 다시 접속하시기 바랍니다.\");
    if(opener) {
    window.close();
    if(opener.parent) {
    opener.parent.location.href='logout.php';
    } else {
    opener.location.href='logout.php';
    }
    } else {
    if(parent) {
    parent.location.href='logout.php';
    } else {
    document.location.href='logout.php';
    }
    }
    </script>";
    exit;
}
$_usersession = new UserSession($id, $authkey);
if (!$_usersession->isallowedip($_SERVER["REMOTE_ADDR"])) {
    echo "<script>
    alert(\"해당 아이피 접근이 불가능합니다.\");
    if(opener) {
    window.close();
    if(opener.parent) {
    opener.parent.location.href='logout.php';
    } else {
    opener.location.href='logout.php';
    }
    } else {
    if(parent) {
    parent.location.href='logout.php';
    } else {
    document.location.href='logout.php';
    }
    }
    </script>";
    exit;
}
$_shopdata = $_usersession->getshopdata();
include("cache.php");

