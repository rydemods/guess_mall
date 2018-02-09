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

//최근 문의내역
if(strpos($body,"[IFPERSONAL]")!=0) {
	$ifpersonalnum=strpos($body,"[IFPERSONAL]");
	$endpersonalnum=strpos($body,"[IFENDPERSONAL]");
	$elsepersonalnum=strpos($body,"[IFELSEPERSONAL]");

	$personalstartnum=strpos($body,"[FORPERSONAL]");
	$personalstopnum=strpos($body,"[FORENDPERSONAL]");

	$ifpersonal=substr($body,$ifpersonalnum+12,$personalstartnum-($ifpersonalnum+12))."[PERSONALVALUE]".substr($body,$personalstopnum+16,$elsepersonalnum-($personalstopnum+16));

	$nopersonal=substr($body,$elsepersonalnum+16,$endpersonalnum-$elsepersonalnum-16);

	$mainpersonal=substr($body,$personalstartnum,$personalstopnum-$personalstartnum+16);

	$body=substr($body,0,$ifpersonalnum)."[ORIGINALPERSONAL]".substr($body,$endpersonalnum+15);

	$sql = "SELECT COUNT(*) as t_count FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$paging = new Paging($sql,10,10,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "SELECT idx,subject,date,re_date FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "ORDER BY idx DESC";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$temppersonal.=$mainpersonal;

		$personal_num = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

		$personal_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
		$personal_redate="-";
		if(strlen($row->re_date)==14) {
			$personal_redate = substr($row->re_date,0,4)."/".substr($row->re_date,4,2)."/".substr($row->re_date,6,2)." (".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
		}
		$personal_subject="<A HREF=\"javascript:ViewPersonal('".$row->idx."')\"><FONT COLOR=\"#000000\"><B>".strip_tags($row->subject)."</B></FONT></A>";
		if(strlen($row->re_date)==14) {
			$personal_reply="<img src=\"".$Dir."images/common/mypersonal_skin_icon1.gif\" border=0 align=absmiddle>";
		} else {
			$personal_reply="<img src=\"".$Dir."images/common/mypersonal_skin_icon2.gif\" border=0 align=absmiddle>";
		}
		$cnt++;
		$pattern=array("[PERSONAL_NUM]","[PERSONAL_DATE]","[PERSONAL_SUBJECT]","[PERSONAL_REPLY]","[PERSONAL_REDATE]","[FORPERSONAL]","[FORENDPERSONAL]");
		$replace=array($personal_num,$personal_date,$personal_subject,$personal_reply,$personal_redate,"","");

		$temppersonal=str_replace($pattern,$replace,$temppersonal);
	}
	pmysql_free_result($result);
	if($cnt>0) {
		$originalpersonal=$ifpersonal;
		$pattern=array("[PERSONALVALUE]");
		$replace=array($temppersonal);
		$originalpersonal=str_replace($pattern,$replace,$originalpersonal);
	} else {
		$originalpersonal=$nopersonal;
	}
}

$page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;

$total=$t_count;
$write="\"javascript:PersonalWrite()\"";


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

	"[TOTAL]",
	"[WRITE]",

	"[ORIGINALPERSONAL]",
	"[PAGE]"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_faq,$menu_mycustsect,$total,$write,$originalpersonal,$page);

$body=str_replace($pattern,$replace,$body);

echo $body;

