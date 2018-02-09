<?php 
$pridx=$_pdata->pridx;

$qnablock=$_REQUEST["qnablock"];
$qnagotopage=$_REQUEST["qnagotopage"];

if ($qnablock != "") {
	$nowblock = $qnablock;
	$curpage  = $qnablock * $qnasetup->page_num + $qnagotopage;
} else {
	$nowblock = 0;
	$curpage="";
}

if (empty($qnagotopage)) {
	$qnagotopage = 1;
}
$colspan=4;
if($qnasetup->datedisplay!="N") $colspan=5;

$sql = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='{$qnasetup->board}' AND pridx='{$pridx}' ";
if ($qnasetup->use_reply != "Y") {
	$sql.= "AND pos = 0 AND depth = 0 ";
}
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count=$row->t_count;
pmysql_free_result($result);
$pagecount = (($t_count - 1) / $qnasetup->list_num) + 1;

$qna_all=$Dir.BoardDir."board.php?board=".$qnasetup->board;
if($qnasetup->grant_write=="N") {
	$qna_write=$Dir.BoardDir."board.php?pagetype=write&board={$qnasetup->board}&exec=write&pridx={$pridx}";
} else if($qnasetup->grant_write=="Y") {
	if(strlen($_ShopInfo->getMemid())>0) {
		$qna_write=$Dir.BoardDir."board.php?pagetype=write&board={$qnasetup->board}&exec=write&pridx={$pridx}";
	} else {
		$qna_write="javascript:check_login()";
	}
} else {
	$qna_write="javascript:view_qnacontent('W')";
}

$isgrantview=false;
if($qnasetup->grant_view=="N") {
	$isgrantview=true;
} else if($setup['grant_view']=="U") {
	if(strlen($_ShopInfo->getMemid())>0) {
		$isgrantview=true;
	}
}

if(strlen($qnasetup->group_code)==4) {
	$isgrantview=false;
	$qna_write="javascript:view_qnacontent('W')";
	if($qnasetup->group_code==$_ShopInfo->getMemgroup()) {
		$isgrantview=true;
		if($qnasetup->grant_write!="A") {
			$qna_write=$Dir.BoardDir."board.php?pagetype=write&board={$qnasetup->board}&exec=write&pridx={$pridx}";
		}
	}
}

?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td valign="bottom" style="font-size:11px;letter-spacing:-0.5pt;">* 상품에 대해 궁금한 점이 있으시면 글을 올려주세요. 친절히 답변해드립니다.</td>
	<td align="right"><A HREF="<?=$qna_all?>"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_totalview.gif" border="0"></A> <A HREF="<?=$qna_write?>"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_qna_write.gif" border="0"></A></td>
</tr>
<tr>
	<td colspan="2">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<col width="50"></col>
	<col></col>
	<col width="100"></col>
	<?php if($qnasetup->datedisplay!="N"){?>
	<col width="100"></col>
	<?php }?>
	<col width="50"></col>
	<tr><td colspan="<?=$colspan?>" height="1" bgcolor="#000000"></td></tr>
	<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
		<td><b>번호</b></td>
		<td><b>제목</b></td>
		<td><b>작성자</b></td>
		<?php if($qnasetup->datedisplay!="N"){?>
		<td><b>작성일</b></td>
		<?php }?>
		<td><b>조회</b></td>
	</tr>
	<tr><td colspan="<?=$colspan?>" height="1" bgcolor="#dddddd"></td></tr>
<?php 
	$imgdir=$Dir.BoardDir."images/skin/".$qnasetup->board_skin;
	$sql = "SELECT * FROM tblboard WHERE board='{$qnasetup->board}' AND pridx='{$pridx}' ";
	if ($qnasetup->use_reply != "Y") {
		$sql.= "AND pos = 0 AND depth = 0 ";
	}
	$sql.= "ORDER BY thread,pos LIMIT {$qnasetup->list_num} OFFSET ".($qnasetup->list_num*($qnagotopage-1));
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($qnasetup->list_num * ($qnagotopage-1))-$j);
		$row->title = stripslashes($row->title);
		if($qnasetup->use_html!="Y") {
			$row->title = strip_tags($row->title);
			$row->content = strip_tags($row->content);
		}
		$row->title = strip_tags($row->title);
		$row->title=getTitle($row->title);
		$row->title=getStripHide($row->title);
		$row->content=getStripHide(stripslashes($row->content));
		if($row->use_html!="1") {
			$row->content=nl2br($row->content);
		}
		$row->name = stripslashes(strip_tags($row->name));

		if($qnasetup->datedisplay=="Y") {
			$date=date("Y/m/d H:i",$row->writetime);
		} else if($qnasetup->datedisplay=="O") {
			$date=date("Y/m/d",$row->writetime);
		}

		$subject='';
		if ($row->deleted!="1") {
			if($isgrantview) {
				if($row->is_secret!="1") {
					$subject = "<a href=\"javascript:view_qnacontent('{$j}')\">";
				} else {
					$subject = "<a href=\"javascript:view_qnacontent('S')\">";
				}
			} else {
				$subject = "<a href=\"javascript:view_qnacontent('N')\">";
			}
		} else {
			$subject = "<a href=\"javascript:view_qnacontent('D')\">";
		}
		$depth = $row->depth;
		if($qnasetup->title_length>0) {
			$len_title = $qnasetup->title_length;
		}
		$wid = 1;
		if ($depth > 0) {
			if ($depth == 1) {
				$wid = 6;
			} else {
				$wid = (6 * $depth) + (4 * ($depth-1));
			}
			$subject .= "<img src=\"{$imgdir}/x.gif\" width=\"{$wid}\" height=\"2\" border=\"0\">";
			$subject .= "<img src=\"{$imgdir}/re_mark.gif\" border=\"0\">";
			if ($len_title) {
				$len_title = $len_title - (3 * $depth);
			}
		}
		$title = $row->title;
		if ($len_title) {
			$title = titleCut($len_title,$title);
		}
		$subject .=  $title;
		if ($row->deleted!="1") {
			$subject .= "</a>";
		}
		$new_img='';
		$isnew=false;
		if($qnasetup->newimg=="0") {	//1일
			if(date("Ymd",$row->writetime)==date("Ymd")) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="1") {//2일
			if(date("Ymd",$row->writetime+(60*60*24*1))>=date("Ymd")) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="2") {//24시간
			if(($row->writetime+(60*60*24))>=time()) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="3") {//36시간
			if(($row->writetime+(60*60*36))>=time()) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="4") {//48시간
			if(($row->writetime+(60*60*48))>=time()) {
				$isnew=true;
			}
		}

		if ($isnew) {
			$subject .= "&nbsp;<img src=\"{$imgdir}/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
			$new_img .= "<img src=\"{$imgdir}/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
		}
		if ($qnasetup->use_comment=="Y" && $row->total_comment > 0) {
			$subject .= "&nbsp;<img src=\"{$imgdir}/icon_memo.gif\" border=\"0\" align=\"absmiddle\">&nbsp;<font style=\"font-size:8pt;\">(<font color=\"#FF0000\">{$row->total_comment}</font>)</font>";
		}

		$comment_tot = $row->total_comment;
		$user_name = $row->name;
		$str_name = $user_name;
		$hit = $row->access;

		echo "<tr height=\"26\" color=\"#333333\" align=\"center\">\n";
		echo "	<td class=\"verdana\">{$number}</td>\n";
		echo "	<td align=\"left\">{$subject}</td>\n";
		echo "	<td>{$str_name}</td>\n";
		if($qnasetup->datedisplay!="N"){
			echo "	<td class=\"verdana\">{$date}</td>\n";
		}
		echo "	<td class=\"verdana\">{$hit}</td>\n";
		echo "</tr>\n";
		if($isgrantview) {
			if($row->is_secret!="1") {
				echo "<tr id=\"qnacontent{$j}\" style=\"display:none\">\n";
				echo "	<td colspan=\"{$colspan}\">\n";
				echo "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" bgcolor=\"#f0f0f0\" style=\"table-layout:fixed;\">\n";
				echo "	<tr>\n";
				echo "		<td style=\"border:#f0f0f0 solid 1px\">\n";
				echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" bgcolor=\"#F1F1F1\" style=\"table-layout:fixed;\">\n";
				echo "		<tr>\n";
				echo "			<td align=\"center\" style=\"padding:8px;\">\n";
				echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed;\">\n";
				echo "			<tr>\n";
				echo "				<td bgcolor=\"#FFFFFF\" style=\"border:#f0f0f0 solid 1px;padding:8px;\">\n";
				echo "				<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				echo "				<tr><td>\n";
				echo "				{$row->content}\n";
				echo "				</td>\n";
				echo "				</tr>\n";
				echo "				<tr>\n";
				echo "					<td align=\"right\"><a href=\"javascript:view_qnacontent('{$j}')\"><img src=\"{$Dir}images/common/event_popup_close.gif\" border=\"0\"></a></td>\n";
				echo "				</tr>\n";
				echo "				</table>\n";
				echo "			</tr>\n";
				echo "			</table>\n";
				echo "			</td>\n";
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";
				echo "	</td>\n";
				echo "</tr>\n";
			}
		}
		echo "<tr><td colspan=\"{$colspan}\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>\n";
		$j++;
	}
	pmysql_free_result($result);
	$a_div_prev_page=$a_prev_page=$print_page=$a_next_page=$a_div_next_page="";
	if($j==0) {
		echo "<tr><td colspan=\"{$colspan}\" height=\"26\" align=\"center\">등록된 상품문의가 없습니다.</td></tr>\n";
	} else {
		$total_block = intval($pagecount / $qnasetup->page_num);

		if (($pagecount % $qnasetup->page_num) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		$a_first_block="";
		$a_last_block="";
		$a_prev_page="";
		$a_next_page="";
		$print_page="";
		$lastpage="";
		if (ceil($t_count/$qnasetup->list_num) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href=\"javascript:GoPage(\"prqna\",0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";
			}
			if ($nowblock > 0) {
				$a_prev_page .= "<a href=\"javascript:GoPage(\"prqna\",".($nowblock-1).",".($qnasetup->page_num*($qnablock-1)+$qnasetup->page_num).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$qnasetup->page_num} 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}
			if (intval($total_block) <> intval($nowblock)) {
				for ($gopage = 1; $gopage <= $qnasetup->page_num; $gopage++) {
					if ((intval($nowblock*$qnasetup->page_num) + $gopage) == intval($qnagotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$qnasetup->page_num) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"prqna\",{$nowblock},".(intval($nowblock*$qnasetup->page_num) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$qnasetup->page_num) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$qnasetup->page_num) + $gopage)."]</FONT></a> ";
					}
				}
			} else {
				if (($pagecount % $qnasetup->page_num) == 0) {
					$lastpage = $qnasetup->page_num;
				} else {
					$lastpage = $pagecount % $qnasetup->page_num;
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$qnasetup->page_num) + $gopage == intval($qnagotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$qnasetup->page_num) + $gopage)."</FONT> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"prqna\",{$nowblock},".(intval($nowblock*$qnasetup->page_num) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$qnasetup->page_num) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$qnasetup->page_num) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($qnasetup->list_num*$qnasetup->page_num)) - 1;
				$last_gotopage = ceil($t_count/$qnasetup->list_num);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"prqna\",{$last_block},{$last_gotopage});' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...{$last_gotopage}]</FONT></a>";
			}

			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"prqna\",".($nowblock+1).",".($qnasetup->page_num*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$qnasetup->page_num} 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";
				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
	}
?>
	<tr>
		<td colspan="<?=$colspan?>" align="center" style="padding-top:10" style="font-size:11px;"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</td>
</tr>
</table>
