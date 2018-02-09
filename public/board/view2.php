<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

/*
if($setup['btype']=="B") {	//블로그형 게시판은 view페이지가 없다.
	header("Location:board.php?pagetype=list&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check);
	exit;
}
*/

if($member['grant_view']!="Y") {
	if(strlen($setup['group_code'])==4 && $setup['group_code']!=$member['group_code']) {
		$errmsg="이용 권한이 없습니다.";
		alert_go($errmsg,-1);
	} else {
		$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
		alert_go($errmsg,-1);
	}
}

switch ($s_check) {
	case "c":
		$check_c = "checked";
		break;
	case "n":
		$check_n = "checked";
		break;
	default:
		$check_c = "checked";
		break;
}

$orSearch = explode(" ",$search);
switch ($s_check) {
	case "c":
		$sql_search = "AND (";
		for($oo=0;$oo<count($orSearch);$oo++) {
			if ($oo > 0) {
				$sql_search .= " OR ";
			}
			$sql_search .= "title LIKE '%" . $orSearch[$oo] . "%' ";
			$sql_search .= "OR content LIKE '%" . $orSearch[$oo] . "%' ";
		}
		$sql_search .= ") ";
		break;
	case "n":
		$sql_search = "AND (";
		for($oo=0;$oo<count($orSearch);$oo++) {
			if ($oo > 0) {
				$sql_search .= " OR ";
			}
			$sql_search .= "name LIKE '%" . $orSearch[$oo] . "%' ";
		}
		$sql_search .= ") ";
		break;
}

$query  = "SELECT * FROM tblboard WHERE board='".$board."' ";
$query .= "AND num = '".$num."' ";

getSecret($query,$row);

$this_num = $row['num'];
$this_thread = $row['thread'];
$this_pos = $row['pos'];
$this_prev = $row['prev_no'];
$this_next = $row['next_no'];
$this_id = $row['id'];
$this_comment = $row['total_comment'];
$pridx=$row['pridx'];
$recipe_no=$row['recipe_no'];

$row['title'] = stripslashes($row['title']);
$row['title'] = getTitle($row['title']);
$row['title'] = getStripHide($row['title']);
$row['name'] = getStripHide(stripslashes($row['name']));


if($_data->icon_type == 'tem_001'){
	$strName = strip_tags($row['name']);
}else{
	if (strlen($row['email'])>0 && $member['admin']=="SU") {
	$strName = "<a href='mailto:".$row['email']."' style=\"text-decoration:underline\">".$row['name']." [".$row['email']."]</a>";
	} else {
		if($setup['use_hide_email']=="Y") {
			$strName = "<A style=\"cursor:point;text-decoration:underline\">".$row['name']."</A>";
		} else {
			if(strlen($row['email'])>0) {
				$strName = "<a href='mailto:".$row['email']."' style=\"text-decoration:underline\">".$row['name']." [".$row['email']."]</a>";
			} else {
				$strName = "<A style=\"cursor:point;text-decoration:underline\">".$row['name']."</A>";
			}
		}
	}
	
}

$v_access = $row['access'];
$v_vote = $row['vote'];

if ($setup['use_lock']=="A" || $setup['use_lock']=="Y") {
	if ($row['is_secret'] == "1") {
		$secret_img = "<img src=".$imgdir."/lock.gif border=0 align=absmiddle>";
	} else {
		$secret_img = "";
	}
}

if(strlen($row['filename'])>0) {
	$file_name1='';	//다운로드 링크
	$upload_file1='';	//이미지 태그
	
	$cut_file=explode("|",$row['filename']);
	$cut_vfile=explode("|",$row['vfilename']);
	
	$f_cnt=0;
	foreach($cut_file as $k){
		
		$attachfileurl=$filepath."/".$cut_vfile[$f_cnt];
		if(file_exists($attachfileurl)) {
			$file_name1[]=FileDownload($board,$cut_vfile[$f_cnt],$k)." (".ProcessBoardFileSize($board,$cut_vfile[$f_cnt]).")";

			$ext = strtolower(pathinfo($cut_vfile[$f_cnt],PATHINFO_EXTENSION));
			if(in_array($ext,array('gif','jpg','png'))) {
				$imgmaxwidth=ProcessBoardFileWidth($board,$cut_vfile[$f_cnt]);
				if($setup['img_maxwidth']<$imgmaxwidth) {
					$imgmaxwidth=$setup['img_maxwidth'];
				}
				//$upload_file1[]="<img src=\"".ImageAttachUrl($this_board,$cut_vfile[$f_cnt])."\" border=0 width=\"{$imgmaxwidth}\">";
			}
		}	
		$f_cnt++;
	}
/*
	$attachfileurl=$filepath."/".$row['filename'];
	if(file_exists($attachfileurl)) {
		$file_name1=FileDownload($board,$row['filename'],$row['filename'])." (".ProcessBoardFileSize($board,$row['filename']).")";

		$ext = strtolower(pathinfo($row["filename"],PATHINFO_EXTENSION));
		if(in_array($ext,array('gif','jpg','png'))) {
			$imgmaxwidth=ProcessBoardFileWidth($board,$row['filename']);
			if($setup['img_maxwidth']<$imgmaxwidth) {
				$imgmaxwidth=$setup['img_maxwidth'];
			}
			$upload_file1="<img src=\"".ImageAttachUrl($board,$row['filename'])."\" border=0 width=\"".$imgmaxwidth."\">";
		}
	}
	*/
}

if ($setup['use_hide_ip']=="N" || $member['admin']=="SU") {
	$strIp = "IP : ".$row['ip'];
}

$strDate = getTimeFormat($row['writetime']);
$strSubject = stripslashes($row['title']);
$strSubject = getStripHide($strSubject);
$strSubject = $secret_img.$strSubject;

if ($row['use_html'] == "1") {
	$memo = stripslashes($row['content']);
	if($_data->icon_type == 'tem_001'){

		$oDiv = explode("|",$row['vfilename']);
		if (!strstr($memo,"[:이미지1:]") && $oDiv[0] && eregi("(gif|jpg|bmp|png)$",$oDiv[0])){
			//$memo = "<div><img src=\"download.php?id=".$this->id."&no=".$this->data['no']."&div=0&mode=1\" border=\"0\" /></div><br>".$memo;
		}
		$memo	= preg_replace("/\[\:이미지([0-9]+)\:\]/e","'<img src=\"codeReplaceImage.php?id=".$row[board]."&no=".$row[num]."&mode=1&div='.(\\1-1).'\">'",$memo);
		$memo	= preg_replace("/\[\:시작\:\]/e","'<div id=\"startDiv\">'",$memo);
		$memo	= preg_replace("/\[\:끝\:\]/e","'</div>'",$memo);


	}
} else {
	//$memo = stripslashes(nl2br($row['content']));
	//$memo = nl2br($row['content']);
	$memo = stripslashes($row['content']);
}

$nowblock = $block;
$curpage  = $block * $setup['page_num'] + $gotopage;

$t_count = $setup['total_article'];

if ($s_check) {
	$sql2  = "SELECT COUNT(*) FROM tblboard WHERE board='".$board."' ";
	$sql2 = $sql2.$sql_search;
	$result2 = pmysql_query($sql2,get_db_conn());
	$row2 = pmysql_fetch_row($result2);

	$t_count = $row2[0];

	pmysql_free_result($result2);
}

$pagecount = (($t_count - 1) / $setup['list_num']) + 1;

include ("top.php");

if($_data->icon_type == 'tem_001'){
	include ($dir."/view_head.php");
}


//comment
if ($setup['use_comment']=="Y" && $this_comment > 0) {
	$com_query = "SELECT * FROM tblboardcomment WHERE board='".$board."' ";
	$com_query.= "AND parent = $this_num ORDER BY num ASC ";
	$com_result = @pmysql_query($com_query,get_db_conn());
	$com_rows = @pmysql_num_rows($com_result);

	if ($com_rows <= 0) {
		@pmysql_query("UPDATE tblboard SET total_comment='0' WHERE board='$board' AND num='$this_num'");
	} else {
		$com_list=array();
		while($com_row = pmysql_fetch_array($com_result)) {
			$com_list[count($com_list)] = $com_row;
		}
		pmysql_free_result($com_result);
	}
}

//윗글
if ($s_check) {
	$p_query  = "SELECT num,thread,title,name,email FROM tblboard WHERE board='$board' ";
	$p_query .= "AND pos = 0 AND thread < '$this_thread' AND deleted != '1' ";
	if ($sql_search) $p_query .= $sql_search." ";
	$p_query .= "ORDER BY thread DESC limit 1" ;
} else {
	$p_query  = "SELECT num,thread,title,name,email FROM tblboard WHERE board='$board' ";
	$p_query .= "AND num = $this_prev ";
}
$p_result = pmysql_query($p_query,get_db_conn());
$p_row = pmysql_fetch_array($p_result);
pmysql_free_result($p_result);

if (!$p_row['num'] || $mypageid) {
	$hide_prev_start = "<!--";
	$hide_prev_end = "-->";
} else {
	$p_row['name'] = stripslashes($p_row['name']);
	$prevTitle = getTitle($p_row['title']);
	$prevTitle = getStripHide($prevTitle);

	if ($setup['title_length'] > 0) {
		$len_title = $setup['title_length'];

		$prevTitle = len_title($prevTitle,$len_title);
	}

	$prevTitle = "<a href='board.php?pagetype=view&view=1&num=".$p_row['num']."&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전글 : ".$prevTitle."';return true\">".$prevTitle."</a>";
	$prevName = $p_row['name'];
	$prevEmail = $p_row['email'];

	if ($prevEmail && $member['admin'] == "SU") {
		$prevName = "<a href=mailto:".$prevEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$prevEmail."'; return true\">".$prevName."</a>";
	}
}


//아랫글
if ($s_check) {
	$n_query  = "SELECT num,thread,title,name,email FROM tblboard WHERE board='$board' ";
	$n_query .= "AND pos = 0 AND thread > '$this_thread' AND deleted != '1' ";
	if ($sql_search) $n_query .= $sql_search." ";
	$n_query .= "ORDER BY thread limit 1" ;
} else {
	$n_query  = "SELECT num,thread,title,name,email FROM tblboard WHERE board='$board' ";
	$n_query .= "AND num = $this_next ";
}
$n_result = pmysql_query($n_query,get_db_conn());
$n_row = pmysql_fetch_array($n_result);
pmysql_free_result($n_result);

if (!$n_row['num'] || $mypageid) {
	$hide_next_start = "<!--";
	$hide_next_end = "-->";
} else {
	$n_row['name'] = stripslashes($n_row['name']);
	$nextTitle = getTitle($n_row['title']);
	$nextTitle = getStripHide($nextTitle);

	if ($setup['title_length'] > 0) {
		$len_title = $setup['title_length'];

		$nextTitle = len_title($nextTitle,$len_title);
	}

	$nextTitle = "<a href='board.php?pagetype=view&view=1&num=".$n_row['num']."&board=".$board."&block=".$block."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음글 : ".$nextTitle."';return true\">".$nextTitle."</a>";
	$nextName = $n_row['name'];
	$nextEmail = $n_row['email'];

	if ($nextEmail && $member['admin'] == "SU") {
		$nextEmail = "<a href=mailto:".$nextEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$nextEmail."'; return true\">".$nextName."</a>";
	}
}

//관련답변글 뽑아내는 루틴
if ($setup['use_reply'] == "Y") {
	$query2  = "SELECT num, thread, pos, depth, name, email, deleted, title, writetime ";
	$query2 .= "FROM tblboard WHERE board='".$board."' ";
	$query2 .= "AND thread = ".$this_thread." ";
	$query2 .= "ORDER BY pos ";
	$result_re = pmysql_query($query2, get_db_conn());
	$total_re = pmysql_num_rows($result_re);

	if ($total_re == 1) {
		$hide_reply_start = "<!--";
		$hide_reply_end = "-->";
	} else {
		if($total_re>1) {
			$tr_str1 .= "<TR height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\" style=\"letter-spacing:-0.5pt;\">\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>글제목</b></TD>\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>글쓴이</b></TD>\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>작성일</b></TD>\n";
			$tr_str1 .= "</TR>\n";
			while ($row5 = pmysql_fetch_array($result_re)) {
				$td_style='';
				if ($num == $row5['num']) {
					$td_style = "color:#FF6600;font-size:11px;letter-spacing:0pt;font-weight:bold;";
				}
				$row5['title'] = getTitle($row5['title']);
				$row5['title'] = getStripHide($row5['title']);
				$row5['name'] = len_title($row5['name'], $nameLength);
				$row5['name'] = getStripHide($row5['name']);

				$tr_str1 .= "<tr><td height=\"1\" bgcolor=\"$list_divider\" colspan=\"3\"></td></tr>";
				if ($row5['deleted'] != "1") {
					if($mypageid){
						$tr_str1 .= "<tr height=\"30\" style=\"CURSOR:hand;\" onClick=\"location='board.php?pagetype=view&view=1&num=".$row5['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."&mypageid=".$mypageid."';\">";
						$tr_str1 .= "<td style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\"><a href='board.php?pagetype=view&view=1&num=".$row5['num']."&board=".$board."&search=".$search."&s_check=".$s_check."&mypageid=".$mypageid."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='답변글 : ".$row5['title']."';return true\"><span style=\"$td_style\">";
						
					}else{
						$tr_str1 .= "<tr height=\"30\" style=\"CURSOR:hand;\" onClick=\"location='board.php?pagetype=view&view=1&num=".$row5['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."';\">";
					$tr_str1 .= "<td style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\"><a href='board.php?pagetype=view&view=1&num=".$row5['num']."&board=".$board."&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='답변글 : ".$row5['title']."';return true\"><span style=\"$td_style\">";	
					}
					
				} else {
					$tr_str1 .= "<tr height=\"30\">";
					$tr_str1 .= "<td style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\"><span style=\"$td_style\">\"";
				}
							
				$wid = 1;
				$depth = $row5['depth'];

				if ($setup['title_length'] > 0) {
					$len_title = $setup['title_length'];
				}

				if ($depth > 0) {
					if ($depth == 1) {
						$wid = 6;
					} else {
						$wid = (6 * $depth) + (4 * ($depth-1));
					}

					$tr_str1 .= "<img src=\"".$imgdir."/x.gif\" width=\"".$wid."\" height=\"2\" border=\"0\">";
					$tr_str1 .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\" align=\"absmiddle\">";

					if ($len_title) {
						$len_title = $len_title - (3 * $depth);
					}
				}

				$title = $row5['title'];

				if ($len_title) {
					$title = len_title($title, $len_title);
				}
			
				$tr_str1 .=  $title;

				if ($row5['deleted'] != "1") {
					$tr_str .= "</A>";
				}

				if (getNewImage($row5['writetime'])) {
					$tr_str1 .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\">&nbsp;";
				}

				$tr_str1 .= "</td>";
				$tr_str1 .= "<td align=\"center\" style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\">".$row5['name']."</td>";

				$tr_str1 .= "<td align=\"center\" class=\"list_text\" style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\">".getTimeFormat($row5['writetime'])."</td>";
				$tr_str1 .= "</tr>";
			}
		}
		pmysql_free_result($result_re);
	}
}

if($setup['btype']=="L") {
	if(strlen($pridx)>0 && $pridx>0) {
		$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
		if($prqnaboard!=$board) $pridx="";
	}
	if(strlen($pridx)>0 && $_data->icon_type != 'tem_001') {
		$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE pridx='".$pridx."' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$result=pmysql_query($sql,get_db_conn());
		if($_pdata=pmysql_fetch_object($result)) {
			include("prqna_top.php");
		} else {
			$pridx="";
		}
		pmysql_free_result($result);
	}
}

include ($dir."/view.php");

if ($setup['use_comment'] == "Y" && $member['grant_comment']=="Y") {
	
	@include ($dir."/comment_head.php");
}

for ($jjj=0;$jjj<count($com_list);$jjj++) {
	$c_num = $com_list[$jjj]['num'];
	$c_name = $com_list[$jjj]['name'];

	if($setup['use_comip']!="Y") {
		$c_uip=$com_list[$jjj]['ip'];
	}

	$comUserId='';

	$c_writetime = getTimeFormat($com_list[$jjj]['writetime']);
	//$c_comment = nl2br(stripslashes($com_list[$jjj]['comment']));
	//$c_comment = nl2br(htmlspecialchars($com_list[$jjj]['comment']));
	$c_comment = nl2br($com_list[$jjj]['comment']);
	$c_ip = $com_list[$jjj]['ip'];
	$c_comment = getStripHide($c_comment);

	@include($dir."/comment_list2.php");
}
if ($setup['use_comment'] == "Y" && $member['grant_comment']=="Y") {
	@include($dir."/comment_write.php");
}

include ($dir."/view_foot.php");
include ("bottom.php");
