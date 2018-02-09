<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

############### 전화번호 ####################
$shoptel="";
$match=array();
if (preg_match("/\[SHOPTEL([a-zA-Z0-9_\/\-.]{0,})\]/",$leftbody,$match)) {
    $shop_telicon=substr($match[1],1);
    if (strlen($shop_content)>0) {
    	$shoptel="&nbsp;<img src=\"".$shop_telicon."\" align=absmiddle>:";
    }
    $shoptel.="&nbsp;".$_data->info_tel;
    $tel_str="<br><img width=0 height=5><br>".((strlen($shop_telicon)>0)?"&nbsp;<img src=\"".$shop_telicon."\" align=absmiddle>:":"");
    $shoptel=str_replace(",",$tel_str."&nbsp;",$shoptel);
}


############### 배너 관련 ####################
$left_banner="";
if($_data->banner_loc=="L") {
	$left_banner.="<table border=0 cellpadding=0 cellspacing=0>\n";
	$sql = "SELECT * FROM tblbanner ORDER BY date DESC ";
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$left_banner.="<tr>\n";
		$left_banner.="	<td align=center valign=top>";
		$left_banner.="<A HREF=\"http".($row->url_type=="S"?"s":"")."://".$row->url."\" target=".$row->target."\"><img src=\"".$Dir.DataDir."shopimages/banner/".$row->image."\" border=\"".$row->border."\"></A>";
		$left_banner.="</td>\n";
		$left_banner.="</tr>\n";
		$left_banner.="<tr><td height=2></td></tr>\n";
		$i++;
	}
	pmysql_free_result($result);
	$left_banner.="</table>\n";
}

################# 공지사항 ##################
$left_notice="";
if($lnotice_yn=="Y") {
	$left_notice.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($lnotice_title=="Y") {
		$left_notice.="<tr>\n";
		$left_notice.="	<td align=center><A HREF=\"javascript:notice_view('list','')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."design/main_notice_title.gif\" border=0 alt=\"공지사항\"></A></td>\n";
		$left_notice.="</tr>\n";
	}
	$left_notice.="<tr>\n";
	$left_notice.="	<td style=\"padding:5\">\n";
	$sql = "SELECT date,subject FROM tblnotice ";
	$sql.= "ORDER BY date DESC LIMIT ".$_data->main_notice_num;
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."]";
		if($lnotice_new=="Y") {
			$t = sscanf($row->date,'%4s%2s%2s%2s%2s%2s');
			$ntime = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:{$t[4]}:00");
			$nicon="";
			if($ntime>time()) $nicon=" <img src=\"".$Dir."images/common/new.gif\" border=0 align=absmiddle>";
		}
		$left_notice.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_notice.="<tr><td>";
		if($lnotice_type=="1") {
			$nfstr="".$i.". ";
		} else if($lnotice_type=="2") {
			$nfstr="".$date." ";
		} else if($lnotice_type=="3") {
			$nfstr="<img src=\"".$Dir."images/noticedot.gif\" border=0 align=absmiddle>";
		} else if($lnotice_type=="4") {
			$nfstr="";
		}
		$left_notice.="<A HREF=\"javascript:notice_view('view','".$row->date."')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainnotice\">".$nfstr."".($lnotice_titlelen>0?titleCut($lnotice_titlelen,$row->subject):$row->subject)."</FONT></A>".$nicon;
		$left_notice.="</td></tr>\n";
		$left_notice.="<tr><td height=".$lnotice_gan."></td></tr>\n";
		$left_notice.="</table>\n";
	}
	pmysql_free_result($result);
	if($i==0) {
		$left_notice.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_notice.="<tr><td align=center class=\"mainnotice\">등록된 공지사항이 없습니다.</td></tr>";
		$left_notice.="</table>";
	}
	$left_notice.="	</td>\n";
	$left_notice.="</tr>\n";
	$left_notice.="</table>\n";
}


################# 컨텐츠정보 ##################
$left_info="";
if($linfo_yn=="Y") {
	$left_info.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($linfo_title=="Y") {
		$left_info.="<tr>\n";
		$left_info.="	<td align=center><A HREF=\"javascript:information_view('list','')\" onmouseover=\"window.status='정보조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."design/main_info_title.gif\" border=0 alt=\"컨텐츠 정보\"></A></td>\n";
		$left_info.="</tr>\n";
	}
	$left_info.="<tr>\n";
	$left_info.="	<td style=\"padding:5\">\n";
	$sql = "SELECT date,subject FROM tblcontentinfo ";
	$sql.= "ORDER BY date DESC LIMIT ".$_data->main_info_num;
	$result=pmysql_query($sql,get_db_conn());
	$i=0;
	while($row=pmysql_fetch_object($result)) {
		$i++;
		$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."]";
		$left_info.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_info.="<tr><td>";
		if($linfo_type=="1") {
			$nfstr="".$i.". ";
		} else if($linfo_type=="2") {
			$nfstr="".$date." ";
		} else if($linfo_type=="3") {
			$nfstr="<img src=\"".$Dir."images/infodot.gif\" border=0 align=absmiddle>";
		} else if($linfo_type=="4") {
			$nfstr="";
		}
		$left_info.="<A HREF=\"javascript:information_view('view','".$row->date."')\" onmouseover=\"window.status='정보조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maininfo\">".$nfstr."".($linfo_titlelen>0?titleCut($linfo_titlelen,$row->subject):$row->subject)."</FONT></A>".$nicon;
		$left_info.="</td></tr>\n";
		$left_info.="<tr><td height=".$linfo_gan."></td></tr>\n";
		$left_info.="</table>\n";
	}
	pmysql_free_result($result);
	if($i==0) {
		$left_info.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_info.="<tr><td align=center class=\"maininfo\">등록된 정보가 없습니다.</td></tr>";
		$left_info.="</table>";
	}
	$left_info.="	</td>\n";
	$left_info.="</tr>\n";
	$left_info.="</table>\n";
}


############### 특별상품 관련 #################
$lspeitem="";
if (strpos($leftbody,"[SPEITEM")) {
	$lspecial_title=(substr($leftbody,strpos($leftbody,"[SPEITEM")+9,1)=="N")?"N":"Y";

	$lspeitem.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($lspecial_title=="Y") {
		$lspeitem.="<tr>\n";
		$lspeitem.="	<td align=center><img src=\"".$Dir.DataDir."design/main_special_title.gif\" border=0 alt=\"특별상품\"></td>\n";
		$lspeitem.="</tr>\n";
	}
	$lspeitem.="<tr>\n";
	$lspeitem.="	<td style=\"padding:5\">\n";
	if($_data->main_special_type=="Y") {
		$lspeitem.="<SCRIPT language=JavaScript>\n";
		$lspeitem.="<!--\n";
		$lspeitem.="var lToggle=1;\n";
		$lspeitem.="function lspecial_stop(chk) {\n";
		$lspeitem.="	lToggle = 0;\n";
		$lspeitem.="	lspecial.stop();\n";
		$lspeitem.="}\n";
		$lspeitem.="function lspecial_start(chk) {\n";
		$lspeitem.="	lToggle = 1;\n";
		$lspeitem.="	lspecial.start();\n";
		$lspeitem.="}\n";
		$lspeitem.="//-->\n";
		$lspeitem.="</SCRIPT>\n";
		$lspeitem.="<MARQUEE id=lspecial onmouseover=lspecial_stop(1) onmouseout=lspecial_start(1) scrollAmount=2 direction=up height=80>\n";
	}
	$lspeitem.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='4' ";
	$result=pmysql_query($sql,get_db_conn());
	$lsp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$lsp_prcode=str_replace(',','\',\'',$row->special_list);
	}
	pmysql_free_result($result);

	$i=0;
	if(strlen($lsp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.tinyimage, a.date, a.etctype, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$lsp_prcode."') ";
		$sql.= "AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$lsp_prcode."') ";
		$sql.= "LIMIT ".$_data->main_special_num;
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$i++;
			if($i>1) $lspeitem.="<tr><td height=5></td>\n";
			$lspeitem.="<tr height=80>\n";
			$lspeitem.="	<td>\n";
			$lspeitem.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$lspeitem.="	<tr>\n";
			$lspeitem.="		<td width=85 align=center valign=top>\n";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
				$lspeitem.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if ($width[0]>=$width[1] && $width[0]>=80) $lspeitem.="width=80 ";
				else if ($width[1]>=80) $lspeitem.="height=80 ";
			} else {
				$lspeitem.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
			}
			$lspeitem.="	></A>";
			$lspeitem.="		</td>\n";
			$lspeitem.="		<td valign=top>\n";
			$lspeitem.="		<table border=0 cellpadding=0 cellspacing=0>\n";
			$lspeitem.="		<tr>\n";
			$lspeitem.="			<td><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainspname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
			$lspeitem.="		</tr>\n";
			$lspeitem.="		<tr>\n";
			$lspeitem.="			<td class=\"mainspprice\">".dickerview($row->etctype,number_format($row->sellprice)."원")." ";
			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") echo soldout();
			$lspeitem.="			</td>\n";
			$lspeitem.="		</tr>\n";
			$lspeitem.="		</table>\n";
			$lspeitem.="		</td>\n";
			$lspeitem.="	</tr>\n";
			$lspeitem.="	</table>\n";
			$lspeitem.="	</td>\n";
			$lspeitem.="</tr>\n";
		}
		pmysql_free_result($result);
	}
	if($_data->main_special_type!="Y" && $i==0) {
		$lspeitem.="<tr><td height=18 align=center class=\"mainspname\">등록된 특별상품이 없습니다.</td></tr>";
	}
	$lspeitem.="	</table>\n";
	if($_data->main_special_type=="Y") {
		$lspeitem.="</MARQUEE>\n";
	}
	$lspeitem.="	</td>\n";
	$lspeitem.="</tr>\n";
	$lspeitem.="</table>\n";
}


############## 투표관련 #######################
$lpoll="";
if (strpos($leftbody,"[POLL")) {
	$lpoll_title=(substr($leftbody,strpos($leftbody,"[POLL")+6,1)=="N")?"N":"Y";

	$sql = "SELECT * FROM tblsurveymain WHERE display='Y' ";
	$sql.= "ORDER BY survey_code DESC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$choice=array(1=>&$row->survey_select1,&$row->survey_select2,&$row->survey_select3,&$row->survey_select4,&$row->survey_select5);

	$lpoll.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($lpoll_title=="Y") {
		$lpoll.="<tr>\n";
		$lpoll.="	<td align=center><img src=\"".$Dir.DataDir."design/main_poll_title.gif\" border=0 alt=\"투표\"></td>\n";
		$lpoll.="</tr>\n";
	}
	$lpoll.="<tr>\n";
	$lpoll.="	<td style=\"padding:5\">\n";
	$lpoll.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$lpoll.="	<tr>\n";
	$lpoll.="		<td class=\"mainpoll\" style=\"padding-left:3;padding-right:3\"><B>".$row->survey_content."</B></td>\n";
	$lpoll.="	</tr>\n";
	$lpoll.="	<form name=lpoll_form method=post>\n";
	$lpoll.="	<tr>\n";
	$lpoll.="		<td align=center style=\"padding:5\">\n";
	$lpoll.="		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$lpoll.="		<col width=10></col>\n";
	$lpoll.="		<col width=></col>\n";
	for($i=1;$i<=count($choice);$i++) {
		if(strlen($choice[$i])>0) {
			$lpoll.="<tr>\n";
			$lpoll.="	<td><input type=radio id=\"idx_lpoll_sel".$i."\" name=poll_sel value=\"".$i."\"></td>\n";
			$lpoll.="	<td class=\"mainpoll\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_lpoll_sel".$i.">".$choice[$i]."</label></td>\n";
			$lpoll.="</tr>\n";
		}
	}
	$lpoll.="		</table>\n";
	$lpoll.="		</td>\n";
	$lpoll.="	</tr>\n";
	$lpoll.="	<tr>\n";
	$lpoll.="		<td align=center style=\"padding-top:5\">\n";
	$lpoll.="		<A HREF=\"javascript:lpoll_result('result','".$row->survey_code."')\"><img src=\"".$Dir."images/survey/poll_bt01.gif\" border=0></A>\n";
	$lpoll.="		&nbsp;\n";
	$lpoll.="		<A HREF=\"javascript:lpoll_result('view','".$row->survey_code."')\"><img src=\"".$Dir."images/survey/poll_bt02.gif\" border=0></A>\n";
	$lpoll.="		</td>\n";
	$lpoll.="	</tr>\n";
	$lpoll.="	</form>\n";
	$lpoll.="	</table>\n";
	$lpoll.="	</td>\n";
	$lpoll.="</tr>\n";
	$lpoll.="</table>\n";
}


############### 로그인 관련 ###################
if (strpos($leftbody,"[LOGINFORM]")) {
	if (strlen($_ShopInfo->getMemid())>0) {
		if ($_ShopInfo->getMemreserve()>0) {
			$reserve_message= "현재적립금 : ".number_format($_ShopInfo->getMemreserve())."원";
		}
		$left_loginform="";
		$left_loginform.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_loginform.="<tr>\n";
		$left_loginform.="	<td>\n";
		$left_loginform.="	<font color=orange><b>".$_ShopInfo->getMemname()."</b></font>님 환영합니다.<br>".$reserve_message."\n";
		$left_loginform.="	</td>\n";
		$left_loginform.="</tr>\n";
		$left_loginform.="<tr>\n";
		$left_loginform.="	<td align=center style=\"padding-top:10\">\n";
		$left_loginform.="	<A HREF=\"".$Dir.FrontDir."mypage_usermodify.php\" $main_target>정보수정</A> | <A HREF=\"".$Dir.MainDir."main.php?type=logout\" $main_target>로그아웃</A>\n";
		$left_loginform.="	</td>\n";
		$left_loginform.="</tr>\n";
		$left_loginform.="</table>\n";
	} else {
		$left_loginform ="<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_loginform.="<form name=leftloginform method=post action=".$Dir.MainDir."main.php>\n";
		$left_loginform.="<input type=hidden name=type value=login>\n";
		if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
			$left_loginform.="<input type=hidden name=shopurl value=\"".$_SERVER['HTTP_HOST']."\">\n";
			$left_loginform.="<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n";
			$left_loginform.="<IFRAME id=leftloginiframe name=leftloginiframe style=\"display:none\"></IFRAME>";
		}
		$left_loginform.="<tr>\n";
		$left_loginform.="	<td>\n";
		$left_loginform.="	<table border=0 cellpadding=0 cellspacing=0>\n";
		$left_loginform.="	<tr>\n";
		$left_loginform.="		<td style=\"padding-left:4\"><input type=text name=id maxlength=20 style=\"width:100\"></td>\n";
		$left_loginform.="	</tr>\n";
		$left_loginform.="	<tr>\n";
		$left_loginform.="		<td style=\"padding-left:4\"><input type=password name=passwd maxlength=20 onkeydown=\"LeftCheckKeyLogin()\" style=\"width:100\"></td>\n";
		$left_loginform.="	</tr>\n";
		$left_loginform.="	</table>\n";
		$left_loginform.="	</td>\n";
		$left_loginform.="	<td style=\"padding-left:5\"><a href=\"javascript:left_login_check()\"><img src=".$Dir."images/btn_login.gif border=0></a></td>\n";
		$left_loginform.="</tr>\n";
		$left_loginform.="<tr>\n";
		$left_loginform.="	<td colspan=2>";
		if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
			$left_loginform.="	<input type=checkbox name=ssllogin value=Y><A HREF=\"javascript:sslinfo()\">보안 접속</A>";
		}
		$left_loginform.="	</td>\n";
		$left_loginform.="</tr>\n";
		$left_loginform.="</form>\n";
		$left_loginform.="<tr>\n";
		$left_loginform.="	<td colspan=2 style=\"padding-left:4;padding-top:7\">\n";
		$left_loginform.="	<A HREF=\"".$Dir.FrontDir."member_agree.php\"><B>회원가입</B></A> <B>|</B> <A HREF=\"".$Dir.FrontDir."findpwd.php\"><B>비밀번호 분실</B></A>\n";
		$left_loginform.="	</td>\n";
		$left_loginform.="</tr>\n";
		$left_loginform.="</table>\n";
	}
} else if (strpos($leftbody,"[LOGINFORMU]")) {
	if (strlen($_ShopInfo->getMemid())>0) {
		$sql = "SELECT body FROM tbldesignnewpage WHERE type='logoutform' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$left_loginformu=$row->body;
		$left_loginformu=str_replace("[DIR]",$Dir,$left_loginformu);
		pmysql_free_result($result);
		$pattern_logout=array("[ID]","[NAME]","[RESERVE]","[LOGOUT]","[MEMBEROUT]","[MEMBER]","[MYPAGE]","[TARGET]");
		$replace_logout=array($_ShopInfo->getMemid(),$_ShopInfo->getMemname(),number_format($_ShopInfo->getMemreserve()),$Dir.MainDir."main.php?type=logout","javascript:memberout()",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php","");
		$left_loginformu = str_replace($pattern_logout,$replace_logout,$left_loginformu);
	} else {
		$sql = "SELECT body FROM tbldesignnewpage WHERE type='loginform' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$left_loginformu=$row->body;
		$left_loginformu=str_replace("[DIR]",$Dir,$left_loginformu);
		pmysql_free_result($result);
		$idfield="";
		if($posnum=strpos($left_loginformu,"[ID")) {
			$s_tmp=explode("_",substr($left_loginformu,$posnum+1,strpos($left_loginformu,"]",$posnum)-$posnum-1));
			$idflength=(int)$s_tmp[1];
			if($idflength==0) $idflength=80;

			$idfield="<input type=text name=id maxlength=20 style=\"width:$idflength\">";
		}
		$pwfield="";
		if($posnum=strpos($left_loginformu,"[PASSWD")) {
			$s_tmp=explode("_",substr($left_loginformu,$posnum+1,strpos($left_loginformu,"]",$posnum)-$posnum-1));
			$pwflength=(int)$s_tmp[1];
			if($pwflength==0) $pwflength=80;

			$pwfield="<input type=password name=passwd maxlength=20 onkeydown=\"LeftCheckKeyLogin()\" style=\"width:$pwflength\">";
		}
		$pattern_login=array("(\[ID(\_){0,1}([0-9]{0,3})\])","(\[PASSWD(\_){0,1}([0-9]{0,3})\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[LOGIN\])","(\[TARGET\])");
		$replace_login=array($idfield,$pwfield,"<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","javascript:left_login_check()",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."findpwd.php",$Dir.FrontDir."login.php","");
		$left_loginformu = preg_replace($pattern_login,$replace_login,$left_loginformu);
	}
	$left_loginformu="<table border=0 cellpadding=0 cellspacing=0>\n<form name=leftloginform method=post action=".$Dir.MainDir."main.php>\n<tr>\n<td>\n".$left_loginformu."\n<input type=hidden name=type value=login>\n";
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$left_loginformu.="<input type=hidden name=shopurl value=\"".$_SERVER['HTTP_HOST']."\">\n<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n<IFRAME id=leftloginiframe name=leftloginiframe style=\"display:none\"></IFRAME>\n";
	}
	$left_loginformu.="</td>\n</tr>\n</form>\n</table>\n";
}


################ 상품리스트 자동표시 관련 ###############
if(strlen($brand_link)==0 || (strlen($brand_link)>0 && strlen($brand_qry)>0)) {
	$prlist="";
	$match=array();
	if (preg_match("/\[PRLIST([a-zA-Z0-9_?\/\-.]+)\]/",$leftbody,$match)) {
		$prlist_tmp=substr($match[1],1);
		$prlist_val=explode("_",$prlist_tmp);
		$tr_gap_height=(strpos($prlist_val[0],"?")===false && strlen($prlist_val[0])!=0)?$prlist_val[0]:"5"; // 리스트 세로 간격
		$table_width=(strpos($prlist_val[1],"?")===false && strlen($prlist_val[1])!=0)?$prlist_val[1]:"200"; // 테이블 가로 길이

		$h_line=($prlist_val[2]=="Y")?"Y":"N"; // 세로라인 유무
		$line_backimg=(strpos($prlist_val[3],"?")===false && strlen($prlist_val[3])!=0)?$prlist_val[3]:""; // 라인 백그라운드 경로 및 이름
		$icon_name=(strpos($prlist_val[4],"?")===false && strlen($prlist_val[4])!=0)?$prlist_val[4]:""; // 아이콘 경로 및 이름
		$prlist="<table border=0 cellpadding=0 cellspacing=0 width=$table_width>\n";
		$prlist.="<tr height=8><td width=8></td><td width=".($table_width-16)."></td><td width=8></td></tr>\n";

		$sql = "SELECT code_a as code, type, code_name FROM tblproductcode ";
		$sql.= "WHERE group_code!='NO' ";
		$sql.= $brand_qry;
		$sql.= "AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
		$result=pmysql_query($sql,get_db_conn());
		$count=0;

		if(strlen($brand_link)>0) {
			$blistbrand_link = "productblist.php?".$brand_link;
		} else {
			$blistbrand_link = "productlist.php?";
		}
		while ($row=pmysql_fetch_object($result)) {
			if ($count!=0) {
				$prlist.="<tr height=$tr_gap_height><td colspan=3></td></tr>\n";
				if ($h_line=="Y") {
					$prback_img="".$Dir."images/common/line.gif";
					if(strlen($line_backimg)>0) {
						$prback_img=$line_backimg;
					}
					$prlist.="<tr height=1><td></td><td background=".$prback_img."></td><td></td></tr>\n";
				}
				$prlist.="<tr height=$tr_gap_height><td colspan=3></td></tr>\n";
			}
			$prlist.="<tr><td></td><td> ";
			if (strlen($icon_name)>0) {
				$prlist.="<img src=\"$icon_name\" align=absmiddle> ";
			}
			$prlist.="<a href=\"".$Dir.FrontDir.$blistbrand_link."code=".$row->code."\" onMouseOver=\"window.status='상품리스트';return true;\"><font class=leftprname>".$row->code_name."</a></td>\n";
			$prlist.="    <td></td></tr>\n";
			$count++;
		}
		$prlist.="</table>\n";
		pmysql_free_result($result);
	}
}


################ 게시판리스트 자동표시 관련 #############
$boardlist="";
$b_match=array();
if (preg_match("/\[BOARDLIST([a-zA-Z0-9_?\/\-.]+)\]/",$leftbody,$b_match)) {
	$boardlist_tmp=substr($b_match[1],1);
	$boardlist_val=explode("_",$boardlist_tmp);
	$btr_gap_height=(strpos($boardlist_val[0],"?")===false && strlen($boardlist_val[0])!=0)?$boardlist_val[0]:"5"; // 리스트 세로 간격
	$btable_width=(strpos($boardlist_val[1],"?")===false && strlen($boardlist_val[1])!=0)?$boardlist_val[1]:"200"; // 테이블 가로 길이
	$bh_line=($boardlist_val[2]=="Y")?"Y":"N"; // 세로라인 유무
	$bline_backimg=(strpos($boardlist_val[3],"?")===false && strlen($boardlist_val[3])!=0)?$boardlist_val[3]:""; // 라인 백그라운드 경로 및 이름
	$bicon_name=(strpos($boardlist_val[4],"?")===false && strlen($boardlist_val[4])!=0)?$boardlist_val[4]:""; // 아이콘 경로 및 이름

	$boardlist="<table border=0 cellpadding=0 cellspacing=0 width=".$btable_width.">\n";
	$boardlist.="<tr height=8><td width=8></td><td width=".($btable_width-16)."></td><td width=8></td></tr>\n";

	$sql = "SELECT board,board_name,use_hidden FROM tblboardadmin ";
	$sql.= "ORDER BY date DESC ";
	$result=pmysql_query($sql,get_db_conn());
	$count=0;
	while ($row=pmysql_fetch_object($result)) {
		if($row->use_hidden!="Y") {
			if ($count!=0) {
				$boardlist.="<tr height=".$btr_gap_height."><td colspan=3></td></tr>\n";
				if ($bh_line=="Y") {
					$bdback_img="".$Dir."images/common/line.gif";
					if(strlen($bline_backimg)>0) {
						$bdback_img=$bline_backimg;
					}
					$boardlist.="<tr height=1><td></td><td background=".$prback_img."></td><td></td></tr>\n";
				}
				$boardlist.="<tr height=".$btr_gap_height."><td colspan=3></td></tr>\n";
			}
			$boardlist.="<tr><td></td><td> ";
			if (strlen($bicon_name)>0) {
				$boardlist.="<img src=\"".$bicon_name."\" align=absmiddle> ";
			}
			$boardlist.="<a href=\"".$Dir.BoardDir."board.php?board=".$row->board."\" onMouseOver=\"window.status='게시판 조회';return true;\"><font class=leftcommunity>".$row->board_name."</font></a></td>\n";
			$boardlist.="    <td></td></tr>\n";
			$count++;
		}
	}
	if ($_data->ETCTYPE["REVIEW"]=="Y") {
		$boardlist.="<tr height=".$btr_gap_height."><td colspan=3></td></tr>\n";
		if ($bh_line=="Y") {
			$bdback_img="".$Dir."images/common/line.gif";
			if(strlen($bline_backimg)>0) {
				$bdback_img=$bline_backimg;
			}
			$boardlist.="<tr height=1><td></td><td background=".$bdback_img."></td><td></td></tr>\n";
			$boardlist.="<tr height=".$btr_gap_height."><td colspan=3></td></tr>\n";
		}
		$boardlist.="<tr><td></td><td> ";
		if (strlen($bicon_name)>0) {
			$boardlist.="<img src=\"".$bicon_name."\" align=absmiddle> ";
		}
		$boardlist.="<a href=\"".$Dir.FrontDir."reviewall.php\" onMouseOver=\"window.status='사용후기 조회';return true;\"><font class=leftcommunity>사용후기 모음</font></a></td>\n";
		$boardlist.="    <td></td></tr>\n";
	}
	$boardlist.="</table>\n";
	pmysql_free_result($result);
}

################ 브랜드리스트 자동표시 관련 #############
$brandlist="";
if ($posnum=strpos($leftbody,"[BRANDLIST")) {
	$s_tmp=explode("_",substr($leftbody,$posnum+1,strpos($leftbody,"]",$posnum)-$posnum-1));
	$flength=(int)$s_tmp[1];
	if($flength==0) $flength=100;
	
	$brandlist.="<div style=\"width:100%;height=".$flength.";overflow-x:hidden;overflow-y:scroll;margin:0;\" id=\"brandlist_div\"><ul style=\"position:absolute;margin:0;padding:0;list-style:none;\" id=\"brandlist_ul\">\n";

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$sql.= "ORDER BY brandname ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$brandlist.="<li style=\"width:100%;margin:0;padding:0;list-style:none;\" id=\"brandlist_li\"><A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a></li>\n";
	}
	pmysql_free_result($result);

	$brandlist.="</ul></div>\n";
}
