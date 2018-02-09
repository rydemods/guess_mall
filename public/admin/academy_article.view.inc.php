<?php // hspark
$num=$_REQUEST["num"];

$qry = "WHERE 1=1 ";
if(ord($board)) $qry.= "AND board='{$board}' ";
if(ord($s_check) && ord($search)) {
	$orSearch = explode(" ",$search);
	// 검색어가 있는경우 쿼리문에 조건추가...........
	switch ($s_check) {
		case "c":
			$qry = "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$qry .= " OR ";
				}
				$qry .= "title LIKE '%{$orSearch[$oo]}%' 
				OR content LIKE '%{$orSearch[$oo]}%' ";
			}
			$qry .= ") ";
			break;
		case "n":
			$qry.= "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$qry .= " OR ";
				}
				$qry .= "a.name LIKE '%{$orSearch[$oo]}%' ";
			}
			$qry .= ") ";
			break;
	}
}

$sql = "SELECT * FROM tblboard 
WHERE num='{$num}' ";
if(ord($board)) $sql.= "AND board='{$board}' ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_array($result);
pmysql_free_result($result);

if(!$row) {
	alert_go('해당 게시글이 존재하지 않습니다.',-1);
}

$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$row['board']}'",get_db_conn()));
if($setup['board_width']<100) $setup['board_width']=$setup['board_width']."%";
if($setup['comment_width']<100) $setup['comment_width']=$setup['comment_width']."%";
if(ord($setup['notice'])) {
	$setup['notice']=getTitle($setup['notice']);
	$setup['notice']=getStripHide($setup['notice']);
}
if($setup['use_wrap']=="N") $setup['wrap']="off";
else if($setup['use_wrap']=="Y") $setup['wrap']="on";

$setup['max_filesize'] = $setup['max_filesize']*(1024*100);
$setup['btype']=$setup['board_skin'][0];
$setup['title_length']=65;

$setup['page_num'] = 10;
$setup['list_num'] = 20;

$filepath = $Dir.DataDir."shopimages/board/".$row['board'];

if($setup['use_reply']=="N") {
	$reply_start="<!--";
	$reply_end="-->";
}

if($setup['use_lock']=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

$this_board=$row['board'];
$this_num = $row['num'];
$this_thread = $row['thread'];
$this_pos = $row['pos'];
$this_id = $row['id'];
$this_comment = $row['total_comment'];
$pridx=$row['pridx'];

$row['title'] = stripslashes($row['title']);
$row['title'] = getTitle($row['title']);
$row['title'] = getStripHide($row['title']);
$row['name'] = getStripHide(stripslashes($row['name']));

if (ord($row['email'])) {
	$strName = "<a href='mailto:{$row['email']}' style=\"text-decoration:underline\">{$row['name']} [{$row['email']}]</a>";
} else {
	$strName = "<A style=\"cursor:point;text-decoration:underline\">{$row['name']}</A>";
}

$v_access = $row['access'];
$v_vote = $row['vote'];

if ($setup['use_lock']=="A" || $setup['use_lock']=="Y") {
	if ($row['is_secret'] == "1") {
		$secret_img = "<img src={$imgdir}/lock.gif border=0 align=absmiddle>";
	} else {
		$secret_img = "";
	}
}

if(ord($row['filename'])) {
	$file_name1='';	//다운로드 링크
	$upload_file1='';	//이미지 태그
	
	$cut_file=explode("|",$row['filename']);
	$cut_vfile=explode("|",$row['vfilename']);
	
	$f_cnt=0;
	foreach($cut_file as $k){
		
		$attachfileurl=$filepath."/".$cut_vfile[$f_cnt];
		if(file_exists($attachfileurl)) {
			$file_name1[]=FileDownload($this_board,$cut_vfile[$f_cnt],$k)." (".ProcessBoardFileSize($this_board,$cut_vfile[$f_cnt]).")";

			$ext = strtolower(pathinfo($cut_vfile[$f_cnt],PATHINFO_EXTENSION));
			if(in_array($ext,array('gif','jpg','png'))) {
				$imgmaxwidth=ProcessBoardFileWidth($this_board,$cut_vfile[$f_cnt]);
				if($setup['img_maxwidth']<$imgmaxwidth) {
					$imgmaxwidth=$setup['img_maxwidth'];
				}
				//$upload_file1[]="<img src=\"".ImageAttachUrl($this_board,$cut_vfile[$f_cnt])."\" border=0 width=\"{$imgmaxwidth}\">";
			}
		}	
		$f_cnt++;
	}
	

/*
	$attachfileurl=$filepath."/".$row['v_file'];
	if(file_exists($attachfileurl)) {
		$file_name1=FileDownload($this_board,$row['v_file'],$row['filename'])." (".ProcessBoardFileSize($this_board,$row['v_file']).")";

		$ext = strtolower(pathinfo($row['v_file'],PATHINFO_EXTENSION));
		if(in_array($ext,array('gif','jpg','png'))) {
			$imgmaxwidth=ProcessBoardFileWidth($this_board,$row['v_file']);
			if($setup['img_maxwidth']<$imgmaxwidth) {
				$imgmaxwidth=$setup['img_maxwidth'];
			}
			$upload_file1="<img src=\"".ImageAttachUrl($this_board,$row['v_file'])."\" border=0 width=\"{$imgmaxwidth}\">";
		}
	}
	*/
}

$strIp = "IP : ".$row['ip'];

$strDate = date("Y/m/d (H:i)",$row['writetime']);
$strSubject = stripslashes($row['title']);
$strSubject = getStripHide($strSubject);
$strSubject = $secret_img.$strSubject;

$board=$row['board'];

if ($row['use_html'] == "1") {
	$memo = stripslashes($row['content']);
} else {
	$memo = stripslashes(nl2br($row['content']));
}

$nowblock = $block;
$curpage  = $block * $setup['page_num'] + $gotopage;

$sql = "SELECT COUNT(*) as t_count FROM tblboard ".$qry;
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
$t_count = $row->t_count;
$pagecount = (($t_count - 1) / $setup['list_num']) + 1;
pmysql_free_result($result);

//comment
if ($setup['use_comment']=="Y" && $this_comment > 0) {
	$com_query = "SELECT * FROM tblboardcomment WHERE board='{$this_board}' 
	AND parent = $this_num ORDER BY num ASC ";
	$com_result = @pmysql_query($com_query,get_db_conn());
	$com_rows = @pmysql_num_rows($com_result);

	if ($com_rows <= 0) {
		@pmysql_query("UPDATE tblboard SET total_comment='0' WHERE board='{$this_board}' AND num='{$this_num}'");
	} else {
		$com_list=array();
		while($com_row = pmysql_fetch_array($com_result)) {
			$com_list[count($com_list)] = $com_row;
		}
		pmysql_free_result($com_result);
	}
}

//윗글
$p_query  = "SELECT num,thread,title,name,email FROM tblboard {$qry} ";
$p_query .= "AND pos = 0 AND thread < '{$this_thread}' AND deleted != '1' ";
$p_query .= "ORDER BY thread DESC limit 1" ;
$p_result = pmysql_query($p_query,get_db_conn());
$p_row = pmysql_fetch_array($p_result);
pmysql_free_result($p_result);

if (!$p_row['num']) {
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

	$prevTitle = "<a href='{$_SERVER['PHP_SELF']}?exec=view&num={$p_row['num']}&board={$board}&block={$block}&gotopage={$gotopage}&category={$category}&search={$search}&s_check={$s_check}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전글 : {$prevTitle}';return true\">{$prevTitle}</a>";
	$prevName = $p_row['name'];
	$prevEmail = $p_row['email'];

	if (ord($prevEmail)) {
		$prevName = "<a href=mailto:{$prevEmail} onmouseout=\"window.status=''\" onmouseover=\"window.status='{$prevEmail}'; return true\">{$prevName}</a>";
	}
}


//아랫글
$n_query  = "SELECT num,thread,title,name,email FROM tblboard {$qry} 
AND pos = 0 AND thread > '{$this_thread}' AND deleted != '1' 
ORDER BY thread limit 1" ;
$n_result = pmysql_query($n_query,get_db_conn());
$n_row = pmysql_fetch_array($n_result);
pmysql_free_result($n_result);

if (!$n_row['num']) {
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

	$nextTitle = "<a href='{$_SERVER['PHP_SELF']}?exec=view&num={$n_row['num']}&board={$board}&block={$block}&gotopage={$gotopage}&category={$category}&search={$search}&s_check={$s_check}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음글 : {$nextTitle}';return true\">{$nextTitle}</a>";
	$nextName = $n_row['name'];
	$nextEmail = $n_row['email'];

	if (ord($nextEmail)) {
		$nextEmail = "<a href=mailto:{$nextEmail} onmouseout=\"window.status=''\" onmouseover=\"window.status='{$nextEmail}'; return true\">{$nextName}</a>";
	}
}

//관련답변글 뽑아내는 루틴
if ($setup['use_reply'] == "Y") {
	$query2  = "SELECT num, thread, pos, depth, name, email, deleted, title, writetime 
	FROM tblboard WHERE board='{$this_board}' 
	AND thread = {$this_thread} 
	ORDER BY pos ";
	$result_re = pmysql_query($query2, get_db_conn());
	$total_re = pmysql_num_rows($result_re);
	if ($total_re == 1) {
		$hide_reply_start = "<!--";
		$hide_reply_end = "-->";
	} else {
		while ($row5 = pmysql_fetch_array($result_re)) {
			$td_bgcolor='';
			if ($num == $row5['num']) {
				$td_bgcolor = $list_mouse_over_color;
			}
			$row5['title'] = getTitle($row5['title']);
			$row5['title'] = getStripHide($row5['title']);
			$row5['name'] = len_title($row5['name'], $nameLength);
			$row5['name'] = getStripHide($row5['name']);

			$tr_str1 .= "<TR><TD colspan=\"5\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD></TR>";
			if ($row5['deleted'] != "1") {
				$tr_str1 .= "<TR style=\"CURSOR:hand;\" onClick=\"location='{$_SERVER['PHP_SELF']}?exec=view&num={$row5['num']}&board={$board}&block={$nowblock}&gotopage={$gotopage}&category={$category}&search={$search}&s_check={$s_check}';\"><TD class=\"board_con1s\" width=30>&nbsp;</TD>";

				$tr_str1 .= "<TD class=\"board_con1s\"><a href='{$_SERVER['PHP_SELF']}?exec=view&num={$row5['num']}&board={$board}&search={$search}&s_check={$s_check}' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='답변글 : {$row5['title']}';return true\">";
			} else {
				$tr_str1 .= "<TR><TD class=\"board_con1s\" width=30>&nbsp;</TD>
				<TD class=\"board_con1s\">";
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

				$tr_str1 .= "<img src={$imgdir}/x.gif width={$wid} height=2 border=0>";
				$tr_str1 .= "<img src={$imgdir}/re_mark.gif border=0 align=absmiddle>";

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

			if ($row5['writetime']+(60*60*24)>time()) {
				$tr_str1 .= "&nbsp;<img src={$imgdir}/icon_new.gif border=0>&nbsp;";
			}

			$tr_str1 .= "</TD>
			<TD class=\"board_con1\" align=\"center\">{$row5['name']}</TD>
			<TD class=\"board_con1s\" align=\"center\">".date("Y/m/d",$row5['writetime'])."</TD>
			<TD class=\"board_con1s\" align=\"center\"></TD></tr>";
		}
		pmysql_free_result($result_re);
	}
} else {
	$hide_reply_start = "<!--";
	$hide_reply_end = "-->";
	$reply_start = "<!--";
	$reply_end = "-->";
}

if($setup['btype']=="L") {
	if(ord($pridx) && $pridx>0) {
		if($prqnaboard!=$this_board) $pridx="";
	}
	if(ord($pridx)) {
		$sql = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage,selfcode FROM tblproduct 
		WHERE pridx='{$pridx}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($_pdata=pmysql_fetch_object($result)) {
			include("academy_article.prqna_top.inc.php");
		} else {
			$pridx="";
		}
		pmysql_free_result($result);
	}
}
?>
<STYLE type=text/css>
	#menuBar {
	}
	#contentDiv {
		WIDTH: 690; 
	}
</STYLE>
<script>
function check_del(url) {
	if(confirm("삭제 하시겠습니까?")) {
		document.location.href=url;
	}
}
</script>

<table border=0 cellpadding=0 cellspacing=1 width="100%">
<tr>
	<td height=15 style="padding-left:5"><B>[<?=$setup['board_name']?>]</B></td>
	<td align=right class="board_con1s"><?=$strIp?></td>
</tr>
</table>
<TABLE cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td width="100%">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<TD background="images/table_top_line1.gif" colspan="4" width="762"><img src=img/table_top_line1.gif height=2></TD>
	</TR>
	<TR>
		<TD class="board_cell1" align="center" width="50"><p align="center">글제목</TD>
		<TD class="board_cell1" align="center" width="683" colspan="3"><p align="left"><B><span class="font_orange"><?=$strSubject?></span></B></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD align="center" height="30" width="50" class="board_con1s"><p align="center">글쓴이</TD>
		<TD align="center" height="30" width="50%" class="board_con1"><p align="left"><A href="cooperation_board_view.php"><B><?=$strName?></B></A></TD>
		<TD align="center" height="30" class="board_con1s">작성일</TD>
		<TD align="center" height="30" width="231" class="board_con1"><?=$strDate?></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD class="board_con1s" align="center" width="50"><p align="center">첨부파일</TD>
		<TD class="board_con1" align="center" width="50%">
		<?php if (count($file_name1)) { ?>
		<TABLE border=0 cellpadding=3 cellspacing=0 width=100%>
		<TR>
			<TD width=20></TD>
			<TD style='word-break:break-all;'>다운로드 : <?=implode(",",$file_name1)?></TD>
			<TD align=right></TD>
		</TR>
		</TABLE>
		<?php } ?>
		</TD>
		<TD class="board_con1s" align="center">조회수</TD>
		<TD class="board_con1" align="center" width="231"><?=$v_access?></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD class="board_con1" width="753" colspan="4">
		<table cellpadding="0" cellspacing="0" width="100%" height="300">
		<tr>
			<td valign="top">
			<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: hidden">
			<DIV id=bodyList>
			<TABLE border=0 cellspacing=0 cellpadding=10 style="table-layout:fixed">
			<TR>
				<TD style='word-break:break-all;' bgcolor=#ffffff valign=top>
				<?php if (count($upload_file1)) {?>
				<span style="width:100%;line-height:160%;text-align:<?=$setup['img_align']?>"> 
				<?=implode("<br>",$upload_file1)?>
				</span>
				<?php }?>

				<span style="width:100%;line-height:160%;"> 
				<?=$memo?>
				</span>
				</TD>
			</TR>
			</TABLE>
			</DIV>
			</DIV>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	</TABLE>
	</td>
</TR>
<TR>
	<TD width="100%">
<?php
	if ($setup['use_comment'] == "Y") {
		echo "<script>\n";
		echo "function chkCommentForm() {\n";
		echo "	if (!comment_form.up_name.value) {\n";
		echo "		alert('이름을 입력 하세요.');\n";
		echo "		comment_form.up_name.focus();\n";
		echo "		return;\n";
		echo "	}\n";
		echo "	if (!comment_form.up_comment.value) {\n";
		echo "		alert('내용을 입력 하세요.');\n";
		echo "		comment_form.up_comment.focus();\n";
		echo "		return;\n";
		echo "	}\n";
		//echo "	document.comment_form.mode.value='comment_result';\n";
		echo "	document.comment_form.submit();\n";
		echo "}\n";
		echo "</script>\n";


		echo "<form method='post' name='comment_form' action='academy_article.php'>\n";
		echo "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\">\n";

		
		echo "<input type=hidden name=exec value=\"{$exec}\">\n";
		echo "<input type=hidden name=board value=\"{$board}\">\n";
		echo "<input type=hidden name=num value=\"{$this_num}\">\n";
		echo "<input type=hidden name=block value=\"{$block}\">\n";
		echo "<input type=hidden name=gotopage value=\"{$gotopage}\">\n";
		echo "<input type=hidden name=category value=\"{$category}\">\n";
		echo "<input type=hidden name=search value=\"{$search}\">\n";
		echo "<input type=hidden name=s_check value=\"{$s_check}\">\n";
		echo "<input type=hidden name=mode value='comment_result'>\n";
		echo "<TR>\n";
		echo "	<TD>\n";

		echo "	<TABLE cellSpacing=0 cellPadding=4 width=\"100%\">\n";
		echo "	<TR>\n";
		echo "		<TD class=tk1 width=581 bgColor=#fafafa colSpan=2>	&nbsp;글쓴이 : <INPUT class=input maxLength=20 size=10 name=up_name></TD>\n";
		echo "	</TR>\n";
		echo "	<TR align=middle>\n";
		echo "		<TD align=left width=\"100%\" bgColor=#fafafa><TEXTAREA class=input style=\"PADDING-RIGHT: 5pt; PADDING-LEFT: 5pt; PADDING-BOTTOM: 5pt; WIDTH: 100%; PADDING-TOP: 5pt; HEIGHT: 70px\" name=up_comment></TEXTAREA></TD>\n";
		echo "		<TD align=right width=\"72\" bgColor=#fafafa><A href=\"javascript:chkCommentForm();\"><IMG height=69 src=\"images/comment.gif\" width=72 border=0></A></TD>\n";
		echo "	</TR>\n";
		echo "	</TABLE>\n";

		echo "	</TD>\n";
		echo "</TR>\n";
		
		
	
		/*
		if ($setup['use_comment'] == "Y") {
			echo "<BR>\n";
			echo "<TABLE CELLSPACING=0 cellpadding=0 border=0 style=\"TABLE-LAYOUT:FIXED\">\n";
			echo "	<TR> \n";
			echo "		<TD HEIGHT=\"20\">▣ <b>댓글 쓰기</b> <현재 <b><font color=\"#4499DD\">{$this_comment}</font></b>건></TD>\n";
			echo "	</TR>\n";
			echo "</TABLE>\n";
			echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 STYLE=\"TABLE-LAYOUT:FIXED\">\n";
			echo "	<TR HEIGHT=1 BGCOLOR={$setup['title_color']}><TD></TD></TR>\n";
			echo "</TABLE>\n";
		}
		*/

		for ($jjj=0;$jjj<count($com_list);$jjj++) {
			$c_num = $com_list[$jjj][num];
			$c_name = $com_list[$jjj][name];

			$c_uip=$com_list[$jjj][ip];

			$c_writetime = date("Y-m-d H:i:s",$com_list[$jjj][writetime]);
			$c_comment = nl2br(stripslashes($com_list[$jjj][comment]));
			$c_ip = $com_list[$jjj][ip];
			$c_comment = getStripHide($c_comment);

			echo "<TR>\n";
			echo "	<TD>\n";
			echo "	<TABLE cellSpacing=0 cellPadding=0 width=\"760\">\n";
			echo "	<TR>\n";
			echo "		<TD width=\"760\" background=\"images/bbs_line1.gif\"></TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD width=\"760\" height=5></TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD class=tk1 width=\"760\" height=22><B><span class=\"font_blue\">{$c_name}</span></B> / <span class=\"board_con1s\">{$c_writetime} ({$c_ip})</span><A style=\"CURSOR:hand;\" onclick=\"check_del('{$_SERVER['PHP_SELF']}?mode=comment_del&board={$board}&num={$num}&c_num={$c_num}&s_check={$s_check}&search={$search}&block={$block}&gotopage={$gotopage}&category={$category}')\"><IMG SRC=\"{$imgdir}/del_x.gif\" width=27 border=0 align=\"absmiddle\" vspace=\"4\" alt=\"삭제\"></A></TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD style='word-break:break-all;' class=tk1 width=\"760\" height=22>{$c_comment}</TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD width=\"760\" height=5></TD>\n";
			echo "	</TR>\n";
			echo "	</TABLE>\n";
			echo "	</TD>\n";
			echo "</TR>\n";
		}
		echo "<TR>\n";
		echo "	<TD width=\"760\" background=\"images/bbs_line1.gif\"></TD>\n";
		echo "</TR>\n";
		echo "<TR>\n";
		echo "	<td></td>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</form>\n";
	}
?>
	</TD>
</TR>
<TR>
	<TD bgcolor=#FFFFFF>
	<!-- 버튼 관련 출력 -->
	<TABLE border=0 cellspacing=0 cellpadding=0 width="100%">
	<TR height=40>
		<TD WIDTH="100%"><p align="right">
		<?=$reply_start?><A HREF="<?=$_SERVER['PHP_SELF']?>?exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&category=<?=$category?>"><IMG SRC="<?=$imgdir?>/butt-reply.gif" border=0></A><?=$reply_end?>
		
		<A HREF="<?=$_SERVER['PHP_SELF']?>?exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&category=<?=$category?>"><IMG SRC="<?=$imgdir?>/butt-modify.gif" border=0></A>

		<A HREF="<?=$_SERVER['PHP_SELF']?>?exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>&category=<?=$category?>"><IMG SRC="<?=$imgdir?>/butt-delete.gif" border=0></A>

		<A HREF="<?=$_SERVER['PHP_SELF']?>?exec=write&board=<?=$board?>&category=<?=$category?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A>
		
		<A HREF="<?=$_SERVER['PHP_SELF']?>?board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&category=<?=$category?>"><IMG SRC="<?=$imgdir?>/butt-list.gif" border=0></A>

		</td>
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<td width="100%"><p>&nbsp;</p></td>
</TR>
<?=$hide_reply_start?>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><p><img src="images/academy_article_reply.gif" width="129" height="28" border="0"></p></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellspacing="2" width="100%" bgcolor="#0099CC">
		<tr>
			<td bgcolor="white">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD class="board_cell1" align="center"></TD>
				<TD class="board_cell1" align="center" width="465"><p align="center">글제목</TD>
				<TD class="board_cell1" align="center">글쓴이</TD>
				<TD class="board_cell1" align="center">작성일</TD>
				<TD class="board_cell1" align="center"></TD>
			</TR>
			<?=$tr_str1?>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<?=$hide_reply_end?>
<tr>
	<td width="100%"><p>&nbsp;</p></td>
</tr>
<tr>
	<td width="100%">
	<table cellpadding="0" cellspacing="0" width="100%">
	<TR>
		<TD>
		
		<div class="table_style03">
		<TABLE cellSpacing=0 cellPadding=0 width="100%">
		<?php if (!$hide_prev_start || !$hide_next_start) {?>
		<?php }?>
		<?=$hide_prev_start?>
		<TR onClick="location='<?=$_SERVER['PHP_SELF']?>?exec=view&board=<?=$board?>&num=<?=$p_row['num']?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&category=<?=$category?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD align=right width=71 height=27><IMG height=14 src="images/bbs_pre.gif" width=62 border=0></TD>
			<TD width="671"><?=$prevTitle?></TD>
		</TR>
		<?=$hide_prev_end?>
		<?=$hide_next_start?>
		<TR onClick="location='<?=$_SERVER['PHP_SELF']?>?exec=view&board=<?=$board?>&num=<?=$n_row['num']?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&category=<?=$category?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD align=right width=71 height=27><IMG height=14 src="images/bbs_next.gif" width=62 border=0></TD>
			<TD width="688"><?=$nextTitle?></TD>
		</TR>
		<?=$hide_next_end?>
		<?php if (!$hide_prev_start || !$hide_next_start) {?>
		<?php }?>
		</TABLE>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<BR><BR>
