<?php
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

if(strpos($body,"[IFORDER]")!==false) {
	$ifordernum=strpos($body,"[IFORDER]");
	$endordernum=strpos($body,"[IFENDORDER]");
	$elseordernum=strpos($body,"[IFELSEORDER]");

	$orderstartnum=strpos($body,"[FORORDER]");
	$orderstopnum=strpos($body,"[FORENDORDER]");

	$iforder=substr($body,$ifordernum+9,$orderstartnum-($ifordernum+9))."[ORDERVALUE]".substr($body,$orderstopnum+13,$elseordernum-($orderstopnum+13));

	$noorder=substr($body,$elseordernum+13,$endordernum-$elseordernum-13);

	$mainorder=substr($body,$orderstartnum,$orderstopnum-$orderstartnum+13);

	$productstartnum=strpos($mainorder,"[FORPRODUCT]");
	$productstopnum=strpos($mainorder,"[FORENDPRODUCT]");

	$mainproduct=substr($mainorder,$productstartnum,$productstopnum-$productstartnum+15);

	$ifdelisearchnum=strpos($mainproduct,"[IFDELISEARCH]");
	$enddelisearchnum=strpos($mainproduct,"[IFENDDELISEARCH]");
	$elsedelisearchnum=strpos($mainproduct,"[IFELSEDELISEARCH]");

	$ifdelisearch=substr($mainproduct,$ifdelisearchnum+14,$elsedelisearchnum-($ifdelisearchnum+14));
	$nodelisearch=substr($mainproduct,$elsedelisearchnum+18,$enddelisearchnum-$elsedelisearchnum-18);
	$mainproduct=substr($mainproduct,0,$ifdelisearchnum)."[DELISEARCHVALUE]".substr($mainproduct,$enddelisearchnum+17);

	$mainorder=substr($mainorder,0,$productstartnum)."[ORIGINALPRODUCT]".substr($mainorder,$productstopnum+15);

	$body=substr($body,0,$ifordernum)."[ORIGINALORDER]".substr($body,$endordernum+12);
}

$ord1="";
$ord2="";
$ord3="";
$ord4="";
if (preg_match("/\[ORD1([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ord1_tmp=ltrim($match[1],'_');
	$ord1_val=explode("_",$ord1_tmp);
	$ord1_off=$ord1_val[0];
	$ord1_on=$ord1_val[1];
	if(strlen($ord1_on)==0) $ord1_on=$ord1_off;
	if($ordgbn=="A") {
		$ord1="<A HREF=\"javascript:GoOrdGbn('A')\"><img src=\"".$ord1_on."\" border=0></A>";
	} else {
		$ord1="<A HREF=\"javascript:GoOrdGbn('A')\"><img src=\"".$ord1_off."\" border=0></A>";
	}
}
if (preg_match("/\[ORD2([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ord2_tmp=ltrim($match[1],'_');
	$ord2_val=explode("_",$ord2_tmp);
	$ord2_off=$ord2_val[0];
	$ord2_on=$ord2_val[1];
	if(strlen($ord2_on)==0) $ord2_on=$ord2_off;
	if($ordgbn=="S") {
		$ord2="<A HREF=\"javascript:GoOrdGbn('S')\"><img src=\"".$ord2_on."\" border=0></A>";
	} else {
		$ord2="<A HREF=\"javascript:GoOrdGbn('S')\"><img src=\"".$ord2_off."\" border=0></A>";
	}
}
if (preg_match("/\[ORD3([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ord3_tmp=ltrim($match[1],'_');
	$ord3_val=explode("_",$ord3_tmp);
	$ord3_off=$ord3_val[0];
	$ord3_on=$ord3_val[1];
	if(strlen($ord3_on)==0) $ord3_on=$ord3_off;
	if($ordgbn=="C") {
		$ord3="<A HREF=\"javascript:GoOrdGbn('C')\"><img src=\"".$ord3_on."\" border=0></A>";
	} else {
		$ord3="<A HREF=\"javascript:GoOrdGbn('C')\"><img src=\"".$ord3_off."\" border=0></A>";
	}
}
if (preg_match("/\[ORD4([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ord4_tmp=ltrim($match[1],'_');
	$ord4_val=explode("_",$ord4_tmp);
	$ord4_off=$ord4_val[0];
	$ord4_on=$ord4_val[1];
	if(strlen($ord4_on)==0) $ord4_on=$ord4_off;
	if($ordgbn=="R") {
		$ord4="<A HREF=\"javascript:GoOrdGbn('R')\"><img src=\"".$ord4_on."\" border=0></A>";
	} else {
		$ord4="<A HREF=\"javascript:GoOrdGbn('R')\"><img src=\"".$ord4_off."\" border=0></A>";
	}
}


$search_btn1="\"javascript:GoSearch('TODAY')\"";
$search_btn2="\"javascript:GoSearch('15DAY')\"";
$search_btn3="\"javascript:GoSearch('1MONTH')\"";
$search_btn4="\"javascript:GoSearch('3MONTH')\"";
$search_btn5="\"javascript:GoSearch('6MONTH')\"";

$search_date ="<select name=\"s_year\" onchange=\"ChangeDate('s')\" style=\"font-size:11px\">\n";
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	$search_date.="<option value=\"".$i."\"";
	if($s_year==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 년\n";
$search_date.="<select name=\"s_month\" onchange=\"ChangeDate('s')\" style=\"font-size:11px\">\n";
for($i=1;$i<=12;$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($s_month==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 월\n";
$search_date.="<select name=\"s_day\" style=\"font-size:11px\">\n";
for($i=1;$i<=get_totaldays($s_year,$s_month);$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($s_day==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 일\n";
$search_date.="~ \n";
$search_date.="<select name=\"e_year\" onchange=\"ChangeDate('e')\" style=\"font-size:11px\">\n";
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	$search_date.="<option value=\"".$i."\"";
	if($e_year==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 년\n";
$search_date.="<select name=\"e_month\" onchange=\"ChangeDate('e')\" style=\"font-size:11px\">\n";
for($i=1;$i<=12;$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($e_month==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 월\n";
$search_date.="<select name=\"e_day\" style=\"font-size:11px\">\n";
for($i=1;$i<=get_totaldays($e_year,$e_month);$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($e_day==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 일\n";

$search_ok="\"javascript:CheckForm()\"";


$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);

$s_curtime=strtotime("$s_year-$s_month-$s_day");
$s_curdate=date("Ymd",$s_curtime);
$e_curtime=strtotime("$e_year-$e_month-$e_day");
$e_curdate=date("Ymd",$e_curtime)."999999999999";

$sql = "SELECT COUNT(*) as t_count FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
elseif($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
elseif($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
$paging = new Paging($sql,10,10,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn ";
$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
elseif($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
elseif($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
$sql.= "ORDER BY ordercode DESC ";
$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
$cnt=0;
while($row=pmysql_fetch_object($result)) {
	$temporder.=$mainorder;

	$order_date=substr($row->ordercode,0,4).".".substr($row->ordercode,4,2).".".substr($row->ordercode,6,2);
	if (strstr("B", $row->paymethod[0])) $order_method="무통장 입금";
	elseif (strstr("V", $row->paymethod[0])) $order_method="실시간계좌이체";
	elseif (strstr("O", $row->paymethod[0])) $order_method="가상계좌";
	elseif (strstr("Q", $row->paymethod[0])) $order_method="가상계좌-<FONT COLOR=\"red\">매매보호</FONT>";
	elseif (strstr("C", $row->paymethod[0])) $order_method="신용카드";
	elseif (strstr("P", $row->paymethod[0])) $order_method="신용카드-<FONT COLOR=\"red\">매매보호</FONT>";
	elseif (strstr("M", $row->paymethod[0])) $order_method="휴대폰";
	else $order_method="";

	$order_price=number_format($row->price);
	$order_detail="\"javascript:OrderDetailPop('".$row->ordercode."')\"";

	$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	$result2=pmysql_query($sql,get_db_conn());
	$jj=0;
	$originalproduct="";
	while($row2=pmysql_fetch_object($result2)) {
		$tempproduct=$mainproduct;

		$order_name=$row2->productname;
		$order_delistat="";
		if ($row2->deli_gbn=="C") $order_delistat="주문취소";
		elseif ($row2->deli_gbn=="D") $order_delistat="취소요청";
		elseif ($row2->deli_gbn=="E") $order_delistat="환불대기";
		elseif ($row2->deli_gbn=="X") $order_delistat="발송준비";
		elseif ($row2->deli_gbn=="Y") $order_delistat="발송완료";
		elseif ($row2->deli_gbn=="N") {
			if (strlen($row->bank_date)<12 && strstr("BOQ", $row->paymethod[0])) $order_delistat="입금확인중";
			elseif ($row->pay_admin_proc=="C" && $row->pay_flag=="0000") $order_delistat="결제취소";
			elseif (strlen($row->bank_date)>=12 || $row->pay_flag=="0000") $order_delistat="발송준비";
			else $order_delistat="결제확인중";
		} elseif ($row2->deli_gbn=="S") {
			$order_delistat="발송준비";
		} elseif ($row2->deli_gbn=="R") {
			$order_delistat="반송처리";
		} elseif ($row2->deli_gbn=="H") {
			$order_delistat="발송완료 [정산보류]";
		}

		$order_delicom="";
		$order_delisearch="";

		$deli_url="";
		$trans_num="";
		$company_name="";
		if($row2->deli_gbn=="Y") {
			if($row2->deli_com>0 && $delicomlist[$row2->deli_com]) {
				$deli_url=$delicomlist[$row2->deli_com]->deli_url;
				$trans_num=$delicomlist[$row2->deli_com]->trans_num;
				$company_name=$delicomlist[$row2->deli_com]->company_name;

				$order_delicom=$company_name;

				if(strlen($row2->deli_num)>0 && strlen($deli_url)>0) {
					if(strlen($trans_num)>0) {
						$arrtransnum=explode(",",$trans_num);
						$pattern=array("[1]","[2]","[3]","[4]");
						$replace=array(substr($row2->deli_num,0,$arrtransnum[0]),substr($row2->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
						$deli_url=str_replace($pattern,$replace,$deli_url);
					} else {
						$deli_url.=$row2->deli_num;
					}
					$order_delisearch="javascript:DeliSearch('".$deli_url."')";
				}
				$pattern=array("[ORDER_DELICOM]","[ORDER_DELISEARCH]");
				$replace=array($order_delicom,$order_delisearch);

				$delisearchval=str_replace($pattern,$replace,$ifdelisearch);
			} else {
				$delisearchval=$nodelisearch;
			}
		} else {
			$delisearchval=$nodelisearch;
		}

		$pattern=array("[ORDER_NAME]","[ORDER_DELISTAT]","[ORDER_DETAIL]","[DELISEARCHVALUE]","[FORPRODUCT]","[FORENDPRODUCT]");
		$replace=array($order_name,$order_delistat,$order_detail,$delisearchval,"","");
		$originalproduct.=str_replace($pattern,$replace,$tempproduct);
	}
	pmysql_free_result($result2);

	$cnt++;

	$pattern=array("[ORDER_DATE]","[ORDER_METHOD]","[ORDER_PRICE]","[ORDER_DETAIL]","[ORIGINALPRODUCT]","[FORORDER]","[FORENDORDER]");
	$replace=array($order_date,$order_method,$order_price,$order_detail,$originalproduct,"","");

	$temporder=str_replace($pattern,$replace,$temporder);
}
pmysql_free_result($result);

if($cnt>0) {
	$originalorder=$iforder;
	$pattern=array("[ORDERVALUE]");
	$replace=array($temporder);
	$originalorder=str_replace($pattern,$replace,$originalorder);
} else {
	$originalorder=$noorder;
}

$page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;


$pattern=array(
	"(\[MENU_MYHOME\])",
	"(\[MENU_MYORDER\])",
	"(\[MENU_MYPERSONAL\])",
	"(\[MENU_MYWISH\])",
	"(\[MENU_MYRESERVE\])",
	"(\[MENU_MYCOUPON\])",
	"(\[MENU_MYINFO\])",
	"(\[MENU_MYOUT\])",
	"(\[MENU_FAQ\])",
	"(\[MENU_MYCUSTSECT\])",

	"(\[SEARCH_BTN1\])",
	"(\[SEARCH_BTN2\])",
	"(\[SEARCH_BTN3\])",
	"(\[SEARCH_BTN4\])",
	"(\[SEARCH_BTN5\])",
	"(\[SEARCH_DATE\])",
	"(\[SEARCH_OK\])",

	"(\[ORD1([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORD2([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORD3([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORD4([a-zA-Z0-9_?\/\-.]+)\])",

	"(\[ORIGINALORDER\])",
	"(\[PAGE\])"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_faq,$menu_mycustsect,$search_btn1,$search_btn2,$search_btn3,$search_btn4,$search_btn5,$search_date,$search_ok,$ord1,$ord2,$ord3,$ord4,$originalorder,$page);

$body=preg_replace($pattern,$replace,$body);

echo $body;
