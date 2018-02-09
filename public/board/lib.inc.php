<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
$board=$_REQUEST["board"];
$num=$_REQUEST["num"];
$exec=$_REQUEST["exec"];
$view=$_REQUEST["view"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$search=$_REQUEST["search"];
$s_check=$_REQUEST["s_check"];
$pridx=$_REQUEST["pridx"];
$error=$_REQUEST["error"];
$mypageid=$_REQUEST["mypageid"];
if($_data->icon_type == "tem_001"){
	if(is_array($s_check)){
		foreach($s_check as $k => $v){
			if($v == 'on'){
				$arrayUri[] = "s_check[".$k."]=on";
				$checked['s_check'][$k]['on'] = "checked";
				$checkedData['s_check'][$k]['on'] = $v;
			}
		}
		$s_check = urlencode(implode("&", $arrayUri));
	}else{
		$checked['s_check']['all']['on'] = "checked";
	}
}

$nameLength=20;

$filepath = $Dir.DataDir."shopimages/board/".$board;
$file_icon_path = "images/file_icon";

$server = $_SERVER['SERVER_NAME']; 
$file = $_SERVER['SCRIPT_NAME']; 
$query = $_SERVER['QUERY_STRING']; 
$chUrl = "http://$server$file";

if($query) $chUrl.="?$query"; 

$setup = setup_info();
if(strlen($setup['board'])==0) {
	echo "<html><head><title></title></head><body onload=\"alert('해당 게시판이 존재하지 않습니다.');\"></body></html>";exit;
}
if($setup['use_hidden']=="Y") {
	alert_go('해당 게시판은 사용하실 수 없는 게시판입니다',-1);
}

$member= member_info();

function setup_info() {
	global $setup, $board;
	if (isset($setup)) {
		return $setup;
	} else {
		$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='".$board."'",get_db_conn()));
		if($setup['board_width']>0 && $setup['board_width']<100) $setup['board_width']=$setup['board_width']."%";
		else if($setup['board_width']==0) $setup['board_width']="100%";
		if($setup['comment_width']>0 && $setup['comment_width']<100) $setup['comment_width']=$setup['comment_width']."%";
		else if($setup['comment_width']==0) $setup['comment_width']="100%";
		if(strlen($setup['notice'])>0) {
			$setup['notice']=getTitle($setup['notice']);
			$setup['notice']=getStripHide($setup['notice']);
		}
		if($setup['use_wrap']=="N") $setup['wrap']="off";
		else if($setup['use_wrap']=="Y") $setup['wrap']="on";

		//$setup['board_skin']="W01";
		if($setup['img_maxwidth']<=0 || strlen($setup['img_maxwidth'])==0) $setup['img_maxwidth']=650;

		$setup['max_filesize'] = $setup['max_filesize']*(1024*100);
		$setup['btype']=$setup['board_skin'][0];
		$setup['title_length']=65;

		return $setup;
	}
}

function member_info() {
	global $board, $member, $setup, $_ShopInfo;

	$member['id']=$_ShopInfo->getMemid();
	if($setup['writer_gbn']=="0") {	//회원이름으로 set
		$member['name']=$_ShopInfo->getMemname();
	} else if($setup['writer_gbn']=="1") {	//회원 아이디로 set
		$member['name']=$_ShopInfo->getMemid();
	}
	$member['nickname']=$_ShopInfo->getNickName();
	$member['email']=$_ShopInfo->getMememail();
	$member['group_code']=$_ShopInfo->getMemgroup();
	$member['authidkey']=$_ShopInfo->getAuthidkey();

	##########default setting#####
	$member['grant_write']="N";
	$member['grant_list']="N";
	$member['grant_view']="N";
	$member['grant_reply']="N";
	$member['grant_comment']="N";
	#############################

	$cadname=$board."_ADMIN";
	if(isCookieVal($_ShopInfo->getBoardadmin(),$cadname)) {
		$member['admin']="SU";
	}

	//게시물 쓰기권한 set
	if($setup['grant_write']=="N") $member['grant_write']="Y";
	else if($setup['grant_write']=="Y") {
		if(strlen($member['id'])>0 && strlen($member['authidkey'])>0) {
			$member['grant_write']="Y";
		} else if($member['admin']=="SU") {
			$member['grant_write']="Y";
		}
	} else if($setup['grant_write']=="A") {
		if($member['admin']=="SU") {
			$member['grant_write']="Y";
		}
	}
	//게시물 보기권한
	if($setup['grant_view']=="N") {
		$member['grant_list']="Y";
		$member['grant_view']="Y";
	} else if($setup['grant_view']=="U") {
		$member['grant_list']="Y";
		if(strlen($member['id'])>0 && strlen($member['authidkey'])>0) {
			$member['grant_view']="Y";
		}
	} else if($setup['grant_view']=="Y") {
		if(strlen($member['id'])>0 && strlen($member['authidkey'])>0) {
			$member['grant_list']="Y";
			$member['grant_view']="Y";
		}
	}
	//답변달기 권한
	if($setup['grant_reply']=="N") $member['grant_reply']="Y";
	else if($setup['grant_reply']=="Y") {
		if(strlen($member['id'])>0 && strlen($member['authidkey'])>0) {
			$member['grant_reply']="Y";
		} else if($member['admin']=="SU") {
			$member['grant_reply']="Y";
		}
	} else if($setup['grant_reply']=="A") {
		if($member['admin']=="SU") {
			$member['grant_reply']="Y";
		}
	}
	//댓글달기 권한
	if($setup['grant_comment']=="N") $member['grant_comment']="Y";
	else if($setup['grant_comment']=="Y") {
		if(strlen($member['id'])>0 && strlen($member['authidkey'])>0) {
			$member['grant_comment']="Y";
		} else if($member['admin']=="SU") {
			$member['grant_comment']="Y";
		}
	} else if($setup['grant_comment']=="A") {
		if($member['admin']=="SU") {
			$member['grant_comment']="Y";
		}
	}
	//특정회원그룹
	if(strlen($setup['group_code'])==4) {
		$member['grant_write']="N";
		$member['grant_list']="N";
		$member['grant_view']="N";

		
		//if($setup['group_code']==$member['group_code']) {
		if($_ShopInfo->memlevel>=$setup['group_level']) {
			if($setup['grant_write']!="A") {
				$member['grant_write']="Y";
			}
			$member['grant_list']="Y";
			$member['grant_view']="Y";
		}
	}

	if($member['admin']=="SU") {
		$member['grant_write']="Y";
		$member['grant_list']="Y";
		$member['grant_view']="Y";
		$member['grant_reply']="Y";
		$member['grant_comment']="Y";
	}
	return $member;
}


function isFilter($filter,$memo,&$findFilter) {
	$use_filter = explode(",",$filter);
	$isFilter = false;
	for($i=0;$i<count($use_filter);$i++) {
		if($use_filter[$i][0]!='/')
		  $use_filter[$i][0] = '/'.$use_filter[$i][0].'/i';
		if (preg_match($use_filter[$i],$memo)) {
			$findFilter = $use_filter[$i];
			$isFilter = true;
			break;
		}
	}
	return $isFilter;
}

function reWriteForm() {
	global $exec, $_POST;
	if ($_POST['up_html']) $up_html = "checked";
	$up_subject = urlencode(stripslashes($_POST['up_subject']));
	$up_memo = urlencode(stripslashes($_POST['up_memo']));
	$up_name = urlencode(stripslashes($_POST['up_name']));

	if($mypageid){
	echo "<form name=reWriteForm method=post action=".$PHP_SELF."?pagetype=write&exec=".$exec."&mypageid=".$mypageid.">\n";	
	}else{
	echo "<form name=reWriteForm method=post action=".$PHP_SELF."?pagetype=write&exec=".$exec.">\n";	
	}
	
	echo "<input type=hidden name=\"mode\" value=\"reWrite\">\n";
	echo "<input type=hidden name=\"thisBoard[is_secret]\" value=\"{$_POST['up_is_secret']}\">\n";
	echo "<input type=hidden name=\"thisBoard[name]\" value=\"$up_name\">\n";
	echo "<input type=hidden name=\"thisBoard[passwd]\" value=\"{$_POST['up_passwd']}\">\n";
	echo "<input type=hidden name=\"thisBoard[email]\" value=\"{$_POST['up_email']}\">\n";
	echo "<input type=hidden name=\"thisBoard[use_html]\" value=\"$up_html\">\n";
	echo "<input type=hidden name=\"thisBoard[title]\" value=\"$up_subject\">\n";
	echo "<input type=hidden name=\"thisBoard[content]\" value=\"$up_memo\">\n";
	echo "<input type=hidden name=\"thisBoard[pos]\" value=\"{$_POST['pos']}\">\n";

	echo "<input type=hidden name=num value=\"{$_POST['num']}\">\n";
	echo "<input type=hidden name=board value=\"{$_POST['board']}\">\n";
	echo "<input type=hidden name=s_check value=\"{$_POST['s_check']}\">\n";
	echo "<input type=hidden name=search value=\"{$_POST['search']}\">\n";
	echo "<input type=hidden name=block value=\"{$_POST['block']}\">\n";
	echo "<input type=hidden name=gotopage value=\"{$_POST['gotopage']}\">\n";
	echo "<input type=hidden name=pridx value=\"{$_POST['pridx']}\">\n";
	echo "</form>\n";
	echo "<script>document.reWriteForm.submit();</script>";
	exit;
}

function getNewImage($writetime) {
	global $setup;
	$isnew=false;
	if($setup['newimg']=="0") {	//1일
		if(date("Ymd",$writetime)==date("Ymd")) {
			$isnew=true;
		}
	} else if($setup['newimg']=="1") {//2일
		if(date("Ymd",$writetime+(60*60*24*1))>=date("Ymd")) {
			$isnew=true;
		}
	} else if($setup['newimg']=="2") {//24시간
		if(($writetime+(60*60*24))>=time()) {
			$isnew=true;
		}
	} else if($setup['newimg']=="3") {//36시간
		if(($writetime+(60*60*36))>=time()) {
			$isnew=true;
		}
	} else if($setup['newimg']=="4") {//48시간
		if(($writetime+(60*60*48))>=time()) {
			$isnew=true;
		}
	}
	return $isnew;
}

function getTimeFormat($writetime) {
	global $setup;
	$reg_date="";
	if($setup['datedisplay']=="Y") {	//시간 포함
		$reg_date=date("Y/m/d H:i",$writetime);
	} else if($setup['datedisplay']=="O") {	//년월일만
		$reg_date=date("Y/m/d",$writetime);
	}
	return $reg_date;
}

function getSecret($query,&$row) {
	global $_ShopInfo,$_POST,$setup,$member,$view,$board,$num,$block,$gotopage,$search,$s_check,$mypageid;

	if ($setup['use_lock']!="N") {
		$result = pmysql_query($query,get_db_conn());
		$view_ok = pmysql_num_rows($result);
		if (!$view_ok || $view_ok == -1) {
			if($mypageid){
				echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&mypageid=$mypageid');\"></body></html>";
			}else{
				echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search');\"></body></html>";	
			}
			exit;
		}
		$row = pmysql_fetch_array($result);

		$rowset['is_secret'] = $row['is_secret'];
		$rowset['passwd'] = $row['passwd'];
		$rowset['passwd_self'] = $row['passwd'];
		$rowset['num'] = $num;
		if ($row['pos'] > 0) {
			$query2 = "SELECT num,passwd,is_secret FROM tblboard WHERE board='".$board."' AND thread = {$row['thread']} AND pos = 0 ";
			$result2 = pmysql_query($query2,get_db_conn());
			$row2 = pmysql_fetch_array($result2);

			$rowset['is_secret'] = $row2['is_secret'];
			$rowset['passwd'] = $row2['passwd'];
			$rowset['num'] = $row2['num'];
		}

		if ($rowset['is_secret'] == "1") {
			$cname=$board."_".$row['thread']."_".$rowset['num']."S";
			if ($_POST['up_passwd'] != "") {
				if(strlen($rowset['passwd'])==16 || strlen($rowset['passwd_self'])==16) {
					$sql9 = "SELECT PASSWORD('".$_POST["up_passwd"]."') AS new_passwd";
					$result9 = pmysql_query($sql9,get_db_conn());
					$row9=@pmysql_fetch_object($result9);
					$new_passwd = $row9->new_passwd;
					@pmysql_free_result($result);
				} else {
					$new_passwd="";
				}
				
				if ($rowset['passwd_self']==$_POST['up_passwd'] || $rowset['passwd']==$_POST['up_passwd'] || $setup['passwd']==$_POST['up_passwd'] || (strlen($new_passwd)>0 && ($rowset['passwd_self']==$new_passwd || $rowset['passwd']==$new_passwd))) {
					//게시판 관리자 쿠키 세팅
					if($setup['passwd']==$_POST['up_passwd']) {
						$cadname=$board."_ADMIN";
						$cadnamrarray=getBoardCookieArray($_ShopInfo->getBoardadmin());
						$cadnamrarray[$cadname]="OK";
						$_ShopInfo->setBoardadmin(serialize($cadnamrarray));
						$_ShopInfo->Save();
						$isSecret=true;
					} else {
						$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numS"]);
						$cookiearray[$cname]="OK";
						setBoardCookieArray("board_thread_numS",$cookiearray,1800,"/".RootPath.BoardDir,"");
						$isSecret = true;
					}
					
					if(strlen($new_passwd)>0 && ($rowset['passwd']==$new_passwd || $rowset['passwd_self']==$new_passwd)) {
						if($row['pos'] > 0 && $rowset['passwd']==$new_passwd) {
							@pmysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$board."' AND num='".$rowset['num']."' ",get_db_conn());
							$rowset['passwd']=$_POST["up_passwd"];
						} else {
							@pmysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$board."' AND num='".$num."' ",get_db_conn());
							$rowset['passwd']=$_POST["up_passwd"];
							$rowset['passwd_self']=$_POST["up_passwd"];
						}
					}
				} else {
					$error="1";
				}
			} else {
				$isSecret = isCookieVal($_COOKIE["board_thread_numS"],$cname);
			}
		} else {
			$isSecret = true;
		}

		if ($view=="1") {
			if ($isSecret || $member['admin']=="SU") {
				$isAccessUp=false;
				$cname=$board."_".$num."V";
				if($setup['hitplus']=="Y") {	//동일인 조회수 증가 금지 (30분으로 제한)
					if(isCookieVal($_COOKIE["board_thread_numV"],$cname)) {
						$isAccessUp=true;
					}
				}
				if(!$isAccessUp) {
					//cookie set
					$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numV"]);
					$cookiearray[$cname]="OK";
					setBoardCookieArray("board_thread_numV",$cookiearray,1800,"/".RootPath.BoardDir,"");

					$qry = "UPDATE tblboard SET access=access+1 WHERE board='".$board."' AND num = '".$num."' ";
					$update = pmysql_query($qry,get_db_conn());
				}
				
				if($mypageid){
					header("Location:board.php?pagetype=view&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&mypageid=$mypageid");		
				}else{
					header("Location:board.php?pagetype=view&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
				}
				
				exit;
			} else {
				
				if($mypageid){
					header("Location:board.php?pagetype=passwd_confirm&exec=secret&top_num={$rowset['num']}&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&error=$error&mypageid=$mypageid");
				
				}else{
					header("Location:board.php?pagetype=passwd_confirm&exec=secret&top_num={$rowset['num']}&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&error=$error");
				}
				exit;
			}
		} else {
			if (!$isSecret && $member['admin']!="SU") {
				if($mypageid){
					header("Location:board.php?pagetype=passwd_confirm&board=$board&exec=secret&top_num={$rowset['num']}&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&error=$error&mypageid=$mypageid");
				}else{
					header("Location:board.php?pagetype=passwd_confirm&board=$board&exec=secret&top_num={$rowset['num']}&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&error=$error");
				}
				
				exit;
			}
		}
	} else {
		if ($view == "1") {
			$isAccessUp=false;
			$cname=$board."_".$num."V";
			if($setup['hitplus']=="Y") {	//동일인 조회수 증가 금지 (30분으로 제한)
				if(isCookieVal($_COOKIE["board_thread_numV"],$cname)) {
					$isAccessUp=true;
				}
			}
			if(!$isAccessUp) {
				//cookie set
				$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numV"]);
				$cookiearray[$cname]="OK";
				setBoardCookieArray("board_thread_numV",$cookiearray,1800,"/".RootPath.BoardDir,"");

				$qry = "UPDATE tblboard SET access = access+1 WHERE board='".$board."' AND num = '".$num."' ";
				$update = pmysql_query($qry,get_db_conn());
			}
			
			if($mypageid){
				header("Location:board.php?pagetype=view&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check&mypageid=$mypageid");
			}else{
				header("Location:board.php?pagetype=view&num=$num&board=$board&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");	
			}
			
			exit;
		} else {
			$result = pmysql_query($query,get_db_conn());
			$view_ok = pmysql_num_rows($result);
			if (!$view_ok || $view_ok == -1) {
				
				if($mypageid){
					echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&mypageid=$mypageid');\"></body></html>";
				}else{
					echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search');\"></body></html>";
				}
				exit;	
			}
			$row = pmysql_fetch_array($result);
		}
	}
}

function setBoardCookieArray($cookiename,$arrayval,$time=0,$path="",$domain="") {
	$tmp = serialize($arrayval);
	$time=time()+$time;
	setcookie($cookiename,$tmp,$time,$path,$domain);
}

function getBoardCookieArray($cookie) {
	$tmp=array();
	if(isset($cookie)) {
		$tmp=unserialize(stripslashes($cookie));
	}
	return $tmp;
}

function isCookieVal($cookie,$cookiename) {
	$tmp=unserialize(stripslashes($cookie));
	if($tmp[$cookiename]=="OK") {
		return true;
	} else {
		return false;
	}
}

function writeSecret($exec,$is_secret,$pos) {
	global $setup;

	if ($exec == "reply") $disabled = "disabled";
	if ($exec == "modify" && $pos != "0") $disabled = "disabled";

	if($setup['use_lock']=="A") {
		echo "<select name=tmp_is_secret disabled>
			<option value=\"0\">사용안함</option>
			<option value=\"1\" selected>잠금사용</option>
			</select> &nbsp; <FONT COLOR=\"red\">자동잠금기능</FONT>
		";
	} else if($setup['use_lock']=="Y") {
		${"select".$is_secret} = "selected";
		echo "<select name=tmp_is_secret $disabled>
			<option value=\"0\" $select0>사용안함</option>
			<option value=\"1\" $select1>잠금사용</option>
			</select>
		";
	}
}

function MakeBoardTop($setup) {
	global $_ShopInfo,$imgdir,$mypageid,$pagetype;
	$left_name=end(explode('_',$setup['board_skin']));
	
	if($mypageid){
		if(strpos($left_name,"TEM")===false){
			
		$Dir="../";
		$boardtop = "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
		$boardtop.= "<tr>\n";
		$boardtop.= "	<TD width=52><IMG SRC=".$imgdir."/board_titlebg_head.gif WIDTH=52 HEIGHT=79 ALT=></TD>\n";
		$boardtop.= "	<TD valign=top background=".$imgdir."/board_titlebg_bg.gif style=padding-top:25px;><span style=font-size:9pt;><b><font color=\"#000000\" style=\"font-size:11pt;\">".$setup['board_name']."</font></b></span></TD>\n";
		$boardtop.= "	<TD width=40><IMG SRC=".$imgdir."/board_titlebg_tail.gif WIDTH=40 HEIGHT=79 ALT=></TD>\n";
		$boardtop.= "</tr>\n";
		$boardtop.= "</table>\n";
	
		if($pagetype=="list"){
			if($setup['board_skin']=='L03')$skin_type="skin1";
			else if($setup['board_skin']=='L02')$skin_type="skin2";
			else if($setup['board_skin']=='L01')$skin_type="skin3";
			else $skin_type="skin1";

			$boardtop.= "<TR>";
			$boardtop.= "<TD style=\"padding:5px;padding-top:0px;\">";
			$boardtop.= "<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">";
			$boardtop.= "<TR>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu1.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_orderlist.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu2.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_personal.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu3.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."wishlist.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu4.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_reserve.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu5.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_coupon.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu6.gif\" BORDER=\"0\"></A></TD>";
			if(getVenderUsed()) {$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_custsect.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu9.gif\" BORDER=\"0\"></A></TD>";
			}
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_usermodify.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu7.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"".$Dir.FrontDir."mypage_memberout.php\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu8.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD><A HREF=\"../board/board.php?board=qna&mypageid=1\"><IMG SRC=\"".$Dir."images/common/mypersonal_".$skin_type."_menu10r.gif\" BORDER=\"0\"></A></TD>";
			$boardtop.= "<TD width=\"100%\" background=\"".$Dir."images/common/mypersonal_skin3_menubg.gif\"></TD>";
			$boardtop.= "</TR>";
			$boardtop.= "</TABLE>";
			$boardtop.= "</TD>";
			$boardtop.= "</TR>";

		}
		}
	}else{
	if(strpos($left_name,"TEM")===true){
	$boardtop = "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
	$boardtop.= "<tr>\n";
	$boardtop.= "	<TD width=52><IMG SRC=".$imgdir."/board_titlebg_head.gif WIDTH=52 HEIGHT=79 ALT=></TD>\n";
	$boardtop.= "	<TD valign=top background=".$imgdir."/board_titlebg_bg.gif style=padding-top:25px;><span style=font-size:9pt;><b><font color=\"#000000\" style=\"font-size:11pt;\">".$setup['board_name']."</font></b></span></TD>\n";
	$boardtop.= "	<TD width=40><IMG SRC=".$imgdir."/board_titlebg_tail.gif WIDTH=40 HEIGHT=79 ALT=></TD>\n";
	$boardtop.= "</tr>\n";
	$boardtop.= "</table>\n";
	$boardtop.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"".$setup['board_width']."\" style=\"table-layout:fixed\">\n";
	}
	
	$sql = "SELECT * FROM tblboardadmin ";
	$sql.= "ORDER BY date DESC ";
	$result=pmysql_query($sql,get_db_conn());
	$boardgroup = "<select onchange=\"document.location.href=this.value\" style=\"font-size:11px;\">";
	while($row=pmysql_fetch_object($result)) {
		if($row->use_hidden!="Y") {
			$select='';
			if($setup['board']==$row->board) $select="selected";
			
			if($mypageid){
				$boardgroup.= "<option value=\"board.php?pagetype=list&board=".$row->board."&mypageid=".$mypageid."\" ".$select.">".strip_tags($row->board_name)."</option>\n";
			}else{
				$boardgroup.= "<option value=\"board.php?pagetype=list&board=".$row->board."\" ".$select.">".strip_tags($row->board_name)."</option>\n";	
			}
			
		}
	}
	pmysql_free_result($result);
	$boardgroup.= "</select>";

	$boardtop.= "<tr>\n";
	$boardtop.= "	<td align=\"right\" style=\"padding-bottom:5px;\">\n";
	$boardtop.= "	".$boardgroup."\n";
	$boardtop.= "	</td>\n";
	$boardtop.= "</tr>\n";
	$boardtop.= "</table>\n";
	}
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='board' AND filename='".$setup['board']."' AND leftmenu='Y' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row= pmysql_fetch_object($result)) {
		$pattern=array("[DIR]","[BOARDGROUP]","[BOARDNAME]");
		$replace=array(DirPath,$boardgroup,$setup['board_name']);
		$boardtop=str_replace($pattern,$replace,$row->body);
	}
	pmysql_free_result($result);
	return $boardtop;
}
