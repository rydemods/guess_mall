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


//로얄회원 관련
$royalvalue="";
if(strpos($body,"[IFROYAL]")!=0) {
	$ifroyalnum=strpos($body,"[IFROYAL]");
	$endroyalnum=strpos($body,"[IFENDROYAL]");
	$mainroyal=substr($body,$ifroyalnum+9,$endroyalnum-$ifroyalnum-9);
	$body=substr($body,0,$ifroyalnum)."[ROYALVALUE]".substr($body,$endroyalnum+12);

	$royal_img="";
	$royal_msg1="";
	$royal_msg2="";
	if(strlen($_ShopInfo->getMemid())>0 && strlen($_ShopInfo->getMemgroup())>0) {
		$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
		$sql = "SELECT a.name,b.group_code,b.group_name,b.group_payment,b.group_usemoney,b.group_addmoney ";
		$sql.= "FROM tblmember a, tblmembergroup b WHERE a.id='".$_ShopInfo->getMemid()."' AND b.group_code=a.group_code ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif")) {
				$royal_img="<img src=\"".$Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif\" border=0>";
			} else {
				$royal_img="<img src=\"".$Dir."images/common/group_img.gif\" border=0>\n";
			}
			$royal_msg1="<B>".$row->name."</B>님은 <B><FONT COLOR=\"#EE1A02\">[".$row->group_name."]</FONT></B> 회원입니다.";
			if ($row->group_code[0]!="M") {
				$royal_msg2 = "<B>".$row->name."</B>님이 <FONT COLOR=\"#EE1A02\"><B>".number_format($row->group_usemoney)."원</B></FONT> 이상 ".$arr_dctype[$row->group_payment]."구매시,";
				$type=substr($row->group_code,0,2);
				if($type=="RW") $royal_msg2.="적립금에 ".number_format($row->group_addmoney)."원을 <font color=#EE1A02><B>추가 적립</B></font>해 드립니다.";
				else if($type=="RP") $royal_msg2.="구매 적립금의 ".number_format($row->group_addmoney)."배를 <font color=#EE1A02><B>적립</B></font>해 드립니다.";
				else if($type=="SW") $royal_msg2.="구매금액 ".number_format($row->group_addmoney)."원을 <font color=#EE1A02><B>추가 할인</B></font>해 드립니다.";
				else if($type=="SP") $royal_msg2.="구매금액의 ".number_format($row->group_addmoney)."%를 <font color=#EE1A02><B>추가 할인</B></font>해 드립니다.";
			} else {
				$royal_msg2="";
			}
			if(strpos($mainroyal,"[IFROYALMSG2]")!=0) {
				if(strlen($royal_msg2)>0) {
					$pattern=array("[IFROYALMSG2]","[IFENDROYALMSG2]");
					$replace=array("","");
					$mainroyal=str_replace($pattern,$replace,$mainroyal);
				} else {
					$ifmsg2num=strpos($mainroyal,"[IFROYALMSG2]");
					$endmsg2num=strpos($mainroyal,"[IFENDROYALMSG2]")+16;
					$mainroyal=substr($mainroyal,0,$ifmsg2num).substr($mainroyal,$endmsg2num);
				}
			}


			$pattern=array("[ROYAL_IMG]","[ROYAL_MSG1]","[ROYAL_MSG2]");
			$replace=array($royal_img,$royal_msg1,$royal_msg2);
			$royalvalue=str_replace($pattern,$replace,$mainroyal);
		}
		pmysql_free_result($result);
	}
}

//최근 주문내역 관련
$ordervalue="";
if(strpos($body,"[IFORDER]")!=0) {
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

	$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
	$result=pmysql_query($sql,get_db_conn());
	$delicomlist=array();
	while($row=pmysql_fetch_object($result)) {
		$delicomlist[$row->code]=$row;
	}
	pmysql_free_result($result);

	$curdate=date("Ymd",strtotime('-1 month'));
	$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn ";
	$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "AND ordercode >= '".$curdate."' AND (del_gbn='N' OR del_gbn='A') ";
	$sql.= "ORDER BY ordercode DESC LIMIT 5 ";
	$result=pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$temporder.=$mainorder;

		$order_date=substr($row->ordercode,0,4).".".substr($row->ordercode,4,2).".".substr($row->ordercode,6,2);
	
		if (strstr("B",$row->paymethod[0])) $order_method="무통장 입금";
		else if (strstr("V",$row->paymethod[0])) $order_method="실시간계좌이체";
		else if (strstr("O",$row->paymethod[0])) $order_method="가상계좌";
		else if (strstr("Q",$row->paymethod[0])) $order_method="가상계좌-<FONT COLOR=\"red\">매매보호</FONT>";
		else if (strstr("C",$row->paymethod[0])) $order_method="신용카드";
		else if (strstr("P",$row->paymethod[0])) $order_method="신용카드-<FONT COLOR=\"red\">매매보호</FONT>";
		else if (strstr("M",$row->paymethod[0])) $order_method="휴대폰";
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
			else if ($row2->deli_gbn=="D") $order_delistat="취소요청";
			else if ($row2->deli_gbn=="E") $order_delistat="환불대기";
			else if ($row2->deli_gbn=="X") $order_delistat="발송준비";
			else if ($row2->deli_gbn=="Y") $order_delistat="발송완료";
			else if ($row2->deli_gbn=="N") {
				if (strlen($row->bank_date)<12 && strstr("BOQ", $row->paymethod[0])) $order_delistat="입금확인중";
				else if ($row->pay_admin_proc=="C" && $row->pay_flag=="0000") $order_delistat="결제취소";
				else if (strlen($row->bank_date)>=12 || $row->pay_flag=="0000") $order_delistat="발송준비";
				else $order_delistat="결제확인중";
			} else if ($row2->deli_gbn=="S") {
				$order_delistat="발송준비";
			} else if ($row2->deli_gbn=="R") {
				$order_delistat="반송처리";
			} else if ($row2->deli_gbn=="H") {
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
}

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

	$sql = "SELECT idx,subject,date,re_date FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "ORDER BY idx DESC LIMIT 5 ";
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$temppersonal.=$mainpersonal;

		$personal_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
		$personal_redate="-";
		if(strlen($row->re_date)==14) {
			$personal_redate = substr($row->re_date,0,4)."/".substr($row->re_date,4,2)."/".substr($row->re_date,6,2)." (".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
		}
		$personal_subject="<A HREF=\"javascript:ViewPersonal('".$row->idx."')\"><FONT COLOR=\"#000000\">".strip_tags($row->subject)."</FONT></A>";
		if(strlen($row->re_date)==14) {
			$personal_reply="<img src=\"".$Dir."images/common/mypersonal_skin_icon1.gif\" border=0 align=absmiddle>";
		} else {
			$personal_reply="<img src=\"".$Dir."images/common/mypersonal_skin_icon2.gif\" border=0 align=absmiddle>";
		}
		$cnt++;
		$pattern=array("[PERSONAL_DATE]","[PERSONAL_SUBJECT]","[PERSONAL_REPLY]","[PERSONAL_REDATE]","[FORPERSONAL]","[FORENDPERSONAL]");
		$replace=array($personal_date,$personal_subject,$personal_reply,$personal_redate,"","");

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

//위시리스트 관렴
$wish_list="";
$match=array();
$default_wish=array("5","N","N");
if (preg_match("/\[WISH_LIST([0-9NY]{0,3})\]/",$body,$match)) {
	$match_array=explode("_",$match[1]);
	for ($i=0;$i<strlen($match_array[0]);$i++) {
		$default_wish[$i]=$match_array[0][$i];
	}
	$wish_cols=(int)$default_wish[0];
	$wish_price=$default_wish[1];		// 소비자가 표시여부
	$wish_reserve=$default_wish[2];		// 적립금 표시여부

	if($wish_cols==0 || $wish_cols==9) $wish_cols=5;

	$wish_colnum=$wish_cols;
	$wish_product_num=$wish_cols;
	if($wish_cols==6)		$wish_imgsize=$_data->primg_minisize-5;
	else if($wish_cols==7)	$wish_imgsize=$_data->primg_minisize-10;
	else if($wish_cols==8)	$wish_imgsize=$_data->primg_minisize-20;
	else					$wish_imgsize=$_data->primg_minisize;

	$wish_list.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	for($j=0;$j<$wish_cols;$j++) {
		
		$wish_list.="<col width=".floor(100/$wish_cols)."%></col>\n";
	}
	$wish_list.="<tr>\n";

	$sql = "SELECT b.productcode,b.productname,b.sellprice,b.quantity,b.reserve,b.reservetype,b.tinyimage, ";
	$sql.= "b.consumerprice,b.option_price,b.option_quantity,b.selfcode,b.etctype FROM tblwishlist a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
	$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
	$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "AND b.display='Y' LIMIT ".$wish_product_num." ";
	$result=pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		
		$wish_list.="<td align=center valign=top>\n";
		$wish_list.="<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"W".$row->productcode."\" onmouseover=\"quickfun_show(this,'W".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'W".$row->productcode."','none')\">\n";
		$wish_list.="<tr>\n";
		$wish_list.="	<td align=center style=\"padding-left:5px;padding-right:5px;\">";
		$wish_list.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
		if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$wish_list.="<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if($_data->ETCTYPE["IMGSERO"]=="Y") {
				if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $wish_list.="height=".$_data->primg_minisize2." ";
				else if (($width[1]>=$width[0] && $width[0]>=$wish_imgsize) || $width[0]>=$wish_imgsize) $wish_list.="width=".$wish_imgsize." ";
			} else {
				if ($width[0]>=$width[1] && $width[0]>=$wish_imgsize) $wish_list.="width=".$wish_imgsize." ";
				else if ($width[1]>=$wish_imgsize) $wish_list.="height=".$wish_imgsize." ";
			}
		} else {
			$wish_list.="<img src=\"".$Dir."images/no_img.gif\" border=0 width=".$wish_imgsize." align=center";
		}
		$wish_list.="	></A></td>\n";
		$wish_list.="</tr>\n";
		$wish_list.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','W','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
		$wish_list.="<tr>\n";
		$wish_list.="	<td align=center valign=top style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
		$wish_list.="</tr>\n";
		if($wish_price=="Y") {	//소비자가
			$wish_list.="<tr>\n";
			$wish_list.="	<td align=center valign=top style=\"padding-left:5px;padding-right:5px;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle><s>".number_format($row->consumerprice)."원</s>";
			$wish_list.="	</td>\n";
			$wish_list.="</tr>\n";
		}
		$wish_list.="<tr>\n";
		$wish_list.="	<td align=center valign=top style=\"padding-left:5px;padding-right:5px;\" class=\"prprice\">";
		if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
			$wish_list.=$dicker;
		} else if(strlen($_data->proption_price)==0) {
			$wish_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
			if (strlen($row->option_price)!=0) $wish_list.="<FONT color=red>(옵션변동)</FONT>";
		} else {
			$wish_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
			if (strlen($row->option_price)==0) $wish_list.=number_format($row->sellprice)."원";
			else $wish_list.=str_replace("[PRICE]",number_format($row->sellprice),$_data->proption_price);
		}
		if ($row->quantity=="0") $wish_list.=soldout(1);
		$wish_list.="	</td>\n";
		$wish_list.="</tr>\n";
		$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
		if($wish_reserve=="Y" && $reserveconv>0) {	//적립금
			$wish_list.="<tr>\n";
			$wish_list.="	<td align=center valign=top style=\"padding-left:5px;padding-right:5px;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
			$wish_list.="	</td>\n";
			$wish_list.="</tr>\n";
		}
		$wish_list.="</table>\n";
		$wish_list.="</td>";

		$cnt++;
	}
	if($cnt>0 && $cnt<$wish_cols) {
		for($k=0; $k<($wish_cols-$cnt); $k++) {
			$wish_list.="<td></td>\n<td></td>\n";
		}
	}
	pmysql_free_result($result);
	if ($cnt==0) {
		$wish_list.="<td height=40 colspan=".$wish_colnum." align=center>WishList에 담긴 상품이 없습니다.</td>";
	}
	
	$wish_list.="	</tr>\n";
	$wish_list.="	</table>\n";
}



$id=$_mdata->id;
$name=$_mdata->name;
$email=$_mdata->email;
$address=explode("=",$_mdata->home_addr);
$address1=$address[0];
$address2=$address[1];
$tel=$_mdata->home_tel;
$mobile=$_mdata->mobile;
$reserve=number_format($_mdata->reserve);
$reserve_more="".$Dir.FrontDir."mypage_reserve.php";
$coupon=number_format($coupon_cnt);
$coupon_more="".$Dir.FrontDir."mypage_coupon.php";

$order_more="".$Dir.FrontDir."mypage_orderlist.php";
$personal_more="".$Dir.FrontDir."mypage_personal.php";
$wish_more="".$Dir.FrontDir."wishlist.php";

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

	"(\[ID\])",
	"(\[NAME\])",
	"(\[EMAIL\])",
	"(\[ADDRESS1\])",
	"(\[ADDRESS2\])",
	"(\[TEL\])",
	"(\[MOBILE\])",
	"(\[RESERVE\])",
	"(\[RESERVE_MORE\])",
	"(\[COUPON\])",
	"(\[COUPON_MORE\])",
	"(\[ROYALVALUE\])",
	"(\[ORDER_MORE\])",
	"(\[ORIGINALORDER\])",
	"(\[PERSONAL_MORE\])",
	"(\[ORIGINALPERSONAL\])",
	"(\[WISH_MORE\])",
	"(\[WISH_LIST([0-9NY]{0,3})\])"
);
$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_faq,$menu_mycustsect,$id,$name,$email,$address1,$address2,$tel,$mobile,$reserve,$reserve_more,$coupon,$coupon_more,$royalvalue,$order_more,$originalorder,$personal_more,$originalpersonal,$wish_more,$wish_list);

$body=preg_replace($pattern,$replace,$body);

echo $body;
