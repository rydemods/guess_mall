<?
$menu_myhome="".$Dir.FrontDir."mypage.php";
$menu_myorder="".$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal="".$Dir.FrontDir."mypage_personal.php";
$menu_mywish="".$Dir.FrontDir."wishlist.php";
$menu_myreserve="".$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon="".$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo="".$Dir.FrontDir."mypage_usermodify.php";
$menu_myout="".$Dir.FrontDir."mypage_memberout.php";
$menu_mycustsect="".$Dir.FrontDir."mypage_custsect.php";

$checkall="javascript:CheckAll()";
$checkdelete="javascript:goDeleteMinishop()";
$checkmailyes="javascript:addAgreeMailAll()";
$checkmailno="javascript:delAgreeMailAll()";

$t_count=0;
$pagecount=0;
if(strpos($body,"[IFMINI]")!=0) {
	$ifmininum=strpos($body,"[IFMINI]");
	$endmininum=strpos($body,"[IFENDMINI]");
	$elsemininum=strpos($body,"[IFELSEMINI]");

	$ministartnum=strpos($body,"[FORMINI]");
	$ministopnum=strpos($body,"[FORENDMINI]");

	$ifmini=substr($body,$ifmininum+8,$ministartnum-($ifmininum+8))."[MINIVALUE]".substr($body,$ministopnum+12,$elsemininum-($ministopnum+12));

	$nomini=substr($body,$elsemininum+12,$endmininum-$elsemininum-12);

	$mainmini=substr($body,$ministartnum,$ministopnum-$ministartnum+12);

	$ifmailoknum=strpos($mainmini,"[IFMAILOK]");
	$endmailoknum=strpos($mainmini,"[IFENDMAILOK]");
	$elsemailoknum=strpos($mainmini,"[IFELSEMAILOK]");

	$ifmailok=substr($mainmini,$ifmailoknum+10,$ministartnum-($ifmininum+10))."[MINIVALUE]".substr($body,$ministopnum+14,$elsemininum-($ministopnum+14));

	$nomailok=substr($mainmini,$elsemailoknum+14,$endmailoknum-$elsemailoknum-14);
	$yesmailok=substr($mainmini,$ifmailoknum+10,$elsemailoknum-($ifmailoknum+10));

	$mainmini=substr($mainmini,0,$ifmailoknum)."[MAILOKVALUE]".substr($mainmini,$endmailoknum+13);
	
	$body=substr($body,0,$ifmininum)."[ORIGINALMINI]".substr($body,$endmininum+11);

	$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.vender=b.vender ";

	$sql = "SELECT COUNT(*) as t_count FROM tblregiststore a, tblvenderstore b ".$qry;
	$paging = new Paging($sql,10,10,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "SELECT a.vender, a.email_yn, b.id, b.brand_name, b.hot_used, b.hot_linktype ";
	$sql.= "FROM tblregiststore a, tblvenderstore b ".$qry." ";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

		$mini_checkbox="<input type=checkbox name=sels value=\"".$row->vender."\">";
		$mini_link="javascript:GoMinishop('".(MinishopType=="ON"?$Dir."minishop/".$row->id:$Dir."minishop.php?storeid=".$row->id)."')";
		$mini_logoimg="";
		if(file_exists($Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif")) {
			$mini_logoimg="<img src=".$Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif border=0>";
		} else {
			$mini_logoimg="<img src=".$Dir."images/minishop/logo.gif border=0>";
		}
		$mini_mail="";
		$mailok="";
		if($row->email_yn=="Y") {
			$mini_mail="수신";
			$mailok="javascript:miniMailAgree('del',".$row->vender.")";
			$mailokvalue=$yesmailok;
		} else {
			$mini_mail="거부";
			$mailok="javascript:miniMailAgree('add',".$row->vender.")";
			$mailokvalue=$nomailok;
		}
		$mini_name=$row->brand_name;

		$minihot="";
		if (preg_match("/\[MINIHOT([1-6]{0,1})\]/",$mainmini,$match)) {
			$minihotnum=$match[1];
			if(strlen($minihotnum)==0) $minihotnum=3;
			$minihot.="<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
			$minihot.="<tr>\n";
			if($row->hot_used=="1") {
				$hot_prcode='';
				$isnot_hotspecial=false;
				$sql = "SELECT a.productcode,a.productname,a.sellprice,a.consumerprice,a.reserve,a.production, ";
				$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= "WHERE 1=1 ";
				if($row->hot_linktype=="2") {
					$sql2 = "SELECT special_list FROM tblvenderspecialmain WHERE vender='".$row->vender."' AND special='3' ";
					$result2=pmysql_query($sql2,get_db_conn());
					if($row2=pmysql_fetch_object($result2)) {
						$hot_prcode=str_replace(',','\',\'',$row2->special_list);
					}
					pmysql_free_result($result2);
					if(strlen($hot_prcode)>0) {
						$sql.= "AND a.productcode IN ('".$hot_prcode."') ";
					} else {
						$isnot_hotspecial=true;
					}
				}
				$sql.= "AND a.vender='".$row->vender."' AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if($row->hot_linktype=="1" || $isnot_hotspecial) {
					$sql.= "ORDER BY a.sellcount DESC ";
				} else if($_minidata->hot_linktype=="2") {
					$sql.= "ORDER BY FIELD(a.productcode,'".$hot_prcode."') ";
				}
				$sql.= "LIMIT 3 ";
				$result2=pmysql_query($sql,get_db_conn());
				while($row2=pmysql_fetch_object($result2)) {
					$minihot.="<td width=80 align=center style=\"padding:7,0\">\n";
					$minihot.="<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
					$minihot.="<tr>\n";
					$minihot.="	<td width=62 height=62 style=\"border:1px #dddddd solid\">\n";
					$minihot.="	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
					if(strlen($row2->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage)){
						$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row2->tinyimage);
						$minihot.="<img src=\"".$Dir.DataDir."shopimages/product/".$row2->tinyimage."\"";
						if($file_size[0]>=$file_size[1]) $minihot.=" width=60";
						else $minihot.=" height=60";
						$minihot.=" border=0></a>";
					} else {
						$minihot.="<img src=\"".$Dir."images/no_img.gif\" width=60 border=0></a>";
					}
					$minihot.="	</td>\n";
					$minihot.="</tr>\n";
					$minihot.="<tr>\n";
					$minihot.="	<td align=center style=\"font-size:8pt;padding-top:5\">\n";
					$minihot.="	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">".str_replace("...","..",titleCut(20,strip_tags($row2->productname)))."</A>";
					$minihot.="	</td>\n";
					$minihot.="</tr>\n";
					$minihot.="<tr><td align=center style=\"font-size:8pt;color:red;padding-top:5\"><B>".number_format($row2->sellprice)."</B></td></tr>\n";
					$minihot.="</table>\n";
					$minihot.="</td>\n";
				}
				pmysql_free_result($result2);
			} else {
				$minihot.="<td>&nbsp;</td>\n";
			}
			$minihot.="</tr>\n";
			$minihot.="</table>\n";
		}

		$tempmini.=$mainmini;

		$cnt++;

		$pattern=array("[MAILOK]");
		$replace=array($mailok);
		$mailokvalue=str_replace($pattern,$replace,$mailokvalue);

		$pattern=array("[MINI_CHECKBOX]","[MINI_LINK]","[MINI_LOGOIMG]","[MINI_MAIL]","[MAILOK]","[MINI_NAME]","[MAILOKVALUE]","[MINIHOT([1-6]{0,1})]","[FORMINI]","[FORENDMINI]");
		$replace=array($mini_checkbox,$mini_link,$mini_logoimg,$mini_mail,$mailok,$mini_name,$mailokvalue,$minihot,"","");

		$tempmini=str_replace($pattern,$replace,$tempmini);
	}
	pmysql_free_result($result);
	if($cnt>0) {
		$originalmini=$ifmini;
		$pattern=array("[MINIVALUE]");
		$replace=array($tempmini);
		$originalmini=str_replace($pattern,$replace,$originalmini);
	} else {
		$originalmini=$nomini;
	}
}

if($cnt>0) {
	$page=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
}

$pattern=array(
	"[MENU_MYHOME]",
	"[MENU_MYORDER]",
	"[MENU_MYPERSONAL]",
	"[MENU_MYWISH]",
	"[MENU_MYRESERVE]",
	"[MENU_MYCOUPON]",
	"[MENU_MYINFO]",
	"[MENU_MYOUT]",
	"[MENU_MYCUSTSECT]",

	"[CHECKALL]",
	"[CHECKDELETE]",
	"[CHECKMAILYES]",
	"[CHECKMAILNO]",
	"[ORIGINALMINI]",
	"[PAGE]"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_mycustsect,$checkall,$checkdelete,$checkmailyes,$checkmailno,$originalmini,$page);

$body=str_replace($pattern,$replace,$body);

echo $body;
