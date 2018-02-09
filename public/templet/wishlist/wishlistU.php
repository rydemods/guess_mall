<?
$menu_myhome=$Dir.FrontDir."mypage.php";
$menu_myorder=$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal=$Dir.FrontDir."mypage_personal.php";
$menu_mywish=$Dir.FrontDir."wishlist.php";
$menu_myreserve=$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon=$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo=$Dir.FrontDir."mypage_usermodify.php";
$menu_myout=$Dir.FrontDir."mypage_memberout.php";
$menu_faq="../board/board.php?board=qna&mypageid=1";
if(getVenderUsed()) { $menu_mycustsect=$Dir.FrontDir."mypage_custsect.php"; } 

if($num=strpos($body,"[SORT_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$sort_style=$s_tmp[1];
}

if($num=strpos($body,"[LISTNUM_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$listnum_style=$s_tmp[1];
}

if($num=strpos($body,"[WISH_MARKS_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$marks_style=$s_tmp[2];
}

if($num=strpos($body,"[WISH_MEMOTXT_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$memo_style=$s_tmp[2];
}

if(strlen($memo_style)==0) $prlist_style="width:380px";


if(strpos($body,"[IFWISH]")!=0) {
	$ifwishnum=strpos($body,"[IFWISH]");
	$endwishnum=strpos($body,"[IFENDWISH]");
	$elsewishnum=strpos($body,"[IFELSEWISH]");

	$wishstartnum=strpos($body,"[FORWISH]");
	$wishstopnum=strpos($body,"[FORENDWISH]");
	$optionstartnum=strpos($body,"[IFOPTION]");
	$optionstopnum=strpos($body,"[IFENDOPTION]");

	$ifwish=substr($body,$ifwishnum+8,$wishstartnum-($ifwishnum+8))."[WISHVALUE]".substr($body,$wishstopnum+12,$elsewishnum-($wishstopnum+12));

	$nowish=substr($body,$elsewishnum+12,$endwishnum-$elsewishnum-12);

	$mainwish=substr($body,$wishstartnum,$optionstartnum-$wishstartnum)."[OPTIONVALUE]".substr($body,$optionstopnum+13,$wishstopnum-$optionstopnum+1);

	$optionwish=substr($body,$optionstartnum+10,$optionstopnum-$optionstartnum-10);

	$body=substr($body,0,$ifwishnum)."[ORIGINALWISH]".substr($body,$endwishnum+11);
}

include("wishlist_text.php");

$pattern=array(
	"/(\[MENU_MYHOME\])/",
	"/(\[MENU_MYORDER\])/",
	"/(\[MENU_MYPERSONAL\])/",
	"/(\[MENU_MYWISH\])/",
	"/(\[MENU_MYRESERVE\])/",
	"/(\[MENU_MYCOUPON\])/",
	"/(\[MENU_MYINFO\])/",
	"/(\[MENU_MYOUT\])/",
	"/(\[MENU_FAQ\])/",
	"/(\[MENU_MYCUSTSECT\])/",

	"/(\[TOTAL\])/",
	"/(\[CHECKALL\])/",
	"/(\[CHECKDEL\])/",
	"/(\[SORT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])/",
	"/(\[LISTNUM((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])/",
	"/(\[ORIGINALWISH\])/",
	"/(\[PAGE\])/"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_faq,$menu_mycustsect,$total,$checkall,$checkdel,$selsort,$sellistnum,$originalwish,$page);

$body=preg_replace($pattern,$replace,$body);

echo $body;

