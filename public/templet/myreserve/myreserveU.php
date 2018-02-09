<?
$menu_myhome="".$Dir.FrontDir."mypage.php";
$menu_myorder="".$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal="".$Dir.FrontDir."mypage_personal.php";
$menu_mywish="".$Dir.FrontDir."wishlist.php";
$menu_myreserve="".$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon="".$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo="".$Dir.FrontDir."mypage_usermodify.php";
$menu_myout="".$Dir.FrontDir."mypage_memberout.php";
$menu_faq="../board/board.php?board=qna&mypageid=1";
if(getVenderUsed()) { $menu_mycustsect=$Dir.FrontDir."mypage_custsect.php"; } 

if(strpos($body,"[IFRESERVE]")!=0) {
	$ifreservenum=strpos($body,"[IFRESERVE]");
	$endreservenum=strpos($body,"[IFENDRESERVE]");
	$elsereservenum=strpos($body,"[IFELSERESERVE]");

	$reservestartnum=strpos($body,"[FORRESERVE]");
	$reservestopnum=strpos($body,"[FORENDRESERVE]");
	$ifreserve=substr($body,$ifreservenum+11,$reservestartnum-($ifreservenum+11))."[RESERVEVALUE]".substr($body,$reservestopnum+15,$elsereservenum-($reservestopnum+15));

	$reserve_useadd=substr($body,$elsereservenum+15,$endreservenum-$elsereservenum-15);

	$mainreserve=substr($body,$reservestartnum,$reservestopnum-$reservestartnum+15);

	$body=substr($body,0,$ifreservenum)."[ORIGINALRESERVE]".substr($body,$endreservenum+14);
}

$reserve=number_format($reserve);
$maxreserve=number_format($maxreserve);


$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
$paging = new Paging($sql,10,10,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
$sql.= "ORDER BY date DESC";
$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
$cnt=0;
while($row=pmysql_fetch_object($result)) {
	$tempreserve.=$mainreserve;

	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
	$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);

	$ordercode="";
	$orderprice="";
	$orderdata=$row->orderdata;
	if(strlen($orderdata)>0) {
		$tmpstr=explode("=",$orderdata);
		$ordercode=$tmpstr[0];
		$orderprice=$tmpstr[1];
	}

	$content="";
	if(strlen($ordercode)>0) {
		$content="<A HREF=\"javascript:OrderDetailPop('".$ordercode."')\">".$row->content."</A>";
	} else {
		$content=$row->content;
	}
	if(strlen($orderprice)>0 && $orderprice>0) {
		$ordprice=number_format($orderprice);
	} else {
		$ordprice="-";
	}

	if(strpos($tempreserve,"[IFWON]")!=0){
		if(strlen($orderprice)>0 && $orderprice>0) {
			$pattern=array("[IFWON]","[IFENDWON]");
			$replace=array("","");
			$tempreserve=str_replace($pattern,$replace,$tempreserve);
		} else {
			$ifnum=strpos($tempreserve,"[IFWON]");
			$endnum=strpos($tempreserve,"[IFENDWON]")+10;
			$tempreserve=substr($tempreserve,0,$ifnum).substr($tempreserve,$endnum);
		}
	}

	$price=number_format($row->reserve);

	$cnt++;

	$pattern=array("[DATE]","[CONTENT]","[ORDPRICE]","[PRICE]","[FORRESERVE]","[FORENDRESERVE]");
	$replace=array($date,$content,$ordprice,$price,"","");

	$tempreserve=str_replace($pattern,$replace,$tempreserve);
}
pmysql_free_result($result);

if($cnt>0) {
	$originalreserve=$ifreserve;
	$pattern=array("[RESERVEVALUE]");
	$replace=array($tempreserve);
	$originalreserve=str_replace($pattern,$replace,$originalreserve);
} else {
	$originalreserve=$reserve_useadd;
}

$page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;


$pattern=array(
	"[MENU_MYHOME]",
	"[MENU_MYORDER]",
	"[MENU_MYPERSONAL]",
	"[MENU_MYWISH]",
	"[MENU_MYRESERVE]",
	"[MENU_MYCOUPON]",
	"[MENU_MYINFO]",
	"[MENU_MYOUT]",
	"[MENU_FAQ]",
	"[MENU_MYCUSTSECT]",

	"[ID]",
	"[NAME]",
	"[RESERVE]",
	"[MAXRESERVE]",
	"[ORIGINALRESERVE]",
	"[PAGE]"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_faq,$menu_mycustsect,$id,$name,$reserve,$maxreserve,$originalreserve,$page);

$body=str_replace($pattern,$replace,$body);

echo $body;
