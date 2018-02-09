<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD><A HREF="../board/board.php?board=qna&mypageid=1"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu10r.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	
	<tr>
		<td height="20"></td>
	</tr>
	
	
	<tr>
		<td>
		<!-- 목록 부분 시작 -->
		<table cellpadding="0" cellspacing="0" width="100%" border="0" style="table-layout:fixed">
			<col width="57"></col>
			<col></col>
			<col width="47"></col>
			<col width="80"></col>
			<col width="55"></col>
			<col width="110"></col>
			
			<TR align="center">
				<TD align="left" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_no.gif" border="0"></TD>
				<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_subject.gif" border="0"></TD>
				<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_file.gif" border="0"></TD>
				<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_write2.gif" border="0"></TD>
				<TD background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_count2.gif" border="0"></TD>
				<TD align="right" background="<?=$imgdir?>/board_skin1_t_bg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_t_date.gif" border="0"></TD>
			</TR>
<?

	$i = 0;
	while($row = pmysql_fetch_array($res)) {
		$row['title'] = stripslashes($row['title']);
		if($setup['use_html']!="Y") {
			$row['title'] = strip_tags($row['title']);
			$row['content'] = strip_tags($row['content']);
		}
		$row['title']=getTitle($row['title']);
		$row['title']=getStripHide($row['title']);
		$row['content']=getStripHide(stripslashes($row['content']));
		if($row['use_html']=="0") {
			//$row['content']=nl2br($row['content']);
			$row['content']=nl2br(htmlspecialchars($row['content']));
		}
		$row['name'] = stripslashes(strip_tags($row['name']));
		$deleted = $row['deleted'];
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);

		$prview_img='';
		
		if($prqnaboard=="qna") {
			if(strlen($row['pridx'])>0 && $row['pridx']>0 && strlen($row['productcode'])>0) {
				$prview_img="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row['productcode']."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=".$imgdir."/btn_prview.gif border=0 align=absmiddle></A>";
			}
		}
		$subject='';
		if ($deleted != "1" && $setup['btype']!="B") {
			$subject = "<a href='mypage_qna_view.php?pagetype=view&view=1&num=".$row['num']."&board=qna&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."'>";
		}
		$depth = $row['depth'];
		if($setup['title_length']>0) {
			$len_title = $setup['title_length'];
		}
		$wid = 1;
		if ($depth > 0) {
			if ($depth == 1) {
				$wid = 2;
			} else {
				$wid = (2 * $depth) + (12 * ($depth-1));
			}
			$subject .= "<img src=\"".$imgdir."/x.gif\" width=".$wid."\" height=\"2\" border=\"0\">";
			$subject .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\" align=\"absmiddle\">";
			if ($len_title) {
				$len_title = $len_title - (3 * $depth);
			}
		}
		$title = $row['title'];
		if ($len_title) {
			$title = len_title($title, $len_title);
		}
		$subject .=  $title;
		if ($deleted != "1" && $setup['btype']!="B") {
			$subject .= "</a>";
		}
		$new_img='';
		if (getNewImage($row['writetime'])) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
			$new_img .= "<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">&nbsp;";
		}
		

		if ($setup['use_comment']=="Y" && $row['total_comment'] > 0) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_memo.gif\" border=\"0\" align=\"absmiddle\">&nbsp;<font style=\"font-size:8pt;\">(<font color=\"#FF0000\">".$row['total_comment']."</font>)</font>";
		}

		$comment_tot = $row['total_comment'];
		//$user_name = len_title($row['name'], $nameLength);
		$user_name = $row['name'];
		$str_name = $user_name;

		$reg_date = getTimeFormat($row['writetime']);
		$hit = $row['access'];

		if($row['filename'] && ($deleted != "1")) {
			$file_name = strtolower(pathinfo($row["filename"],PATHINFO_EXTENSION));			
			if($file_name == 'zip' || $file_name == 'arj' || $file_name == 'gz' || $file_name == 'tar') {
				$file_icon = "compressed.gif";
			} elseif ($file_name == 'rar') {
				$file_icon = "ra.gif";
			} elseif ($file_name == 'exe') {
				$file_icon = "exe.gif";
			} elseif($file_name == 'gif') {				
				$file_icon = "gif.gif";
			} elseif($file_name == 'jpg' || $file_name == 'jpeg') {
				$file_icon = "jpg.gif";
			} elseif($file_name == 'mpeg' || $file_name == 'mpg' || $file_name == 'asf' || $file_name == 'avi' || $file_name == 'swf') {
				$file_icon = "movie.gif";
			} elseif($file_name == 'mp3' || $file_name == 'rm' || $file_name == 'ram') {
				$file_icon = "sound.gif";
			}elseif($file_name == 'pdf') {
				$file_icon = "pdf.gif";
			} elseif($file_name == 'ppt') {
				$file_icon = "ppt.gif";
			} elseif($file_name == 'doc') {
				$file_icon = "doc.gif";
			} elseif($file_name == 'hwp') {
				$file_icon = "hwp.gif";
			} else {
				$file_icon = "txt.gif";
			}
			$file_icon = "<IMG SRC=\"../board/images/file_icon/".$file_icon."\" border=0>";
		} else {
			$file_icon = "-";
		}
	
	
?>
			<TR height="28" align="center" bgcolor="<?=$list_bg_color?>" onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>';" onMouseOut="this.style.backgroundColor='';">
				<TD nowrap style="font-size:11px;"><?=$number?></TD>
				<TD nowrap align="left" style="word-break:break-all;padding-left:5px;padding-right:5px;"><?=$subject?> <?=$prview_img?></TD> 
				<TD nowrap><?=$file_icon?></TD>
				<TD nowrap style="font-size:11px;"><?=$str_name?></TD>
				<?=$hide_hit_start?>
				<TD nowrap style="font-size:11px;"><?=$hit?></TD>
				<?=$hide_hit_end?>
				<?=$hide_date_start?>
				<TD nowrap style="font-size:11px;"><?=$reg_date?></TD>
				<?=$hide_date_end?>
			</TR>
			<TR>
				<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
			</TR>
<?	
		$i++;

	}
	pmysql_free_result($res);
	
	if(!$i){
		

?>			
			<TR>
				<TD height="30" align="center" bgcolor="<?=$list_bg_color?>" colspan="<?=$table_colcnt?>" style='word-break:break-all;'>등록된 게시물이 없습니다.</TD> 
			</TR>
			<TR>
				<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
			</TR>
	
<?}?>
			
			<TR>
				<TD colspan="<?=$table_colcnt?>">
				<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
				<TR>
					<TD bgcolor="#F4F9FD" style="padding:10px;padding-top:15px;padding-bottom:15px;">
					<table cellpadding="0" cellspacing="0">
					<form method=get name=frm action="<?=$PHP_SELF?>" onSubmit="return schecked()">
					<input type="hidden" name="pagetype" value="list">
					<input type="hidden" name="board" value="qna">
					<tr>
						<td style="font-size:11px;letter-spacing:-0.5pt;"><input type=radio name="s_check" value="c" checked style="border:none;background-color:#F4F9FD;">제목+내용</td>
						<td style="padding-left:5px;padding-right:5px;"><input type=text name="search" value="<?=$search?>" size="12" class="input"></td>
						<td><INPUT type="image" src="<?=$imgdir?>/butt-go.gif" border="0" align="absMiddle" style="border:none"></td>
					</tr>
					</FORM>
					</table>
					</TD>
				</TR>
				<TR>
					<TD height="1" bgcolor="#81BFEA"></TD>
				</TR>
				
				<TR>
					<TD align="center" width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img1.gif" border="0"></td>
						<td width="100%" align="center" background="<?=$imgdir?>/board_skin1_imgbg.gif">
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
						<!-- 페이지 출력 ---------------------->
						<?=$paging->a_prev_page?>
						<?=$paging->print_page?>
						<?=$paging->a_next_page?>
						<!-- 페이지 출력 끝 -->
							<td width=1 nowrap class=ndv></td>
						</tr>
						</table>
						</td>
						<td align="right" background="<?=$imgdir?>/board_skin1_imgbg.gif"><IMG SRC="<?=$imgdir?>/board_skin1_img2.gif" border="0"></td>
					</tr>
					</table>
					</TD>
				</TR>
				<tr>
					<td height="20"></td>
				</tr>
				</TABLE>
				</TD>
			</TR>
		</table>
	</td>
	</tr>
	<!-- 목록 부분 끝 -->
	
	
	</table>
	</td>
</tr>
</table>
