<?php 
//$pridx=$_pdata->pridx;

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

$sql = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='studyb' AND recipe_no='{$recipe_no}' ";
if ($qnasetup->use_reply != "Y") {
	$sql.= "AND pos = 0 AND depth = 0 ";
}
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count=$row->t_count;
pmysql_free_result($result);
$pagecount = (($t_count - 1) / $qnasetup->list_num) + 1;

$qna_all=$Dir.BoardDir."board.php?board=studyb";
if($qnasetup->grant_write=="N") {
	$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=studyb&exec=write&recipe_no={$recipe_no}";
} else if($qnasetup->grant_write=="Y") {
	if(strlen($_ShopInfo->getMemid())>0) {
		$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=studyb&exec=write&recipe_no={$recipe_no}";
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
			$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=studyb&exec=write&recipe_no={$recipe_no}";
		}
	}
}

?>

	<table class="th_top_st" width="846" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed">
			<colgroup><col width="50" /><col width="" /><col width="70" /><col width="120" /><!--<col width="70" />--></colgroup>
<?php 
	$imgdir=$Dir.BoardDir."images/skin/".$qnasetup->board_skin;
	$sql = "SELECT * FROM tblboard WHERE board='studyb' AND recipe_no='{$recipe_no}' ";
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

			#### 테스트 중 ####
			if($row->is_secret!="1") {
				$subject = "<a href=\"javascript:view_qnacontent('{$j}')\">";
			} else {
				$subject = "<a href=\"javascript:view_qnacontent('S')\">";
			}
			###################

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
			$subject .= "<img src=\"{$imgdir}/x.gif\" width=\"{$wid}\" height=\"2\" border=\"0\" style=\"float:left\">";
			$subject .= "<img src=\"{$imgdir}/re_mark.gif\" border=\"0\" style=\"float:left\">";
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
			$subject .= "&nbsp;<img src=\"{$imgdir}/icon_new.gif\" border=\"0\" align=\"absmiddle\" class=\"img_ib\">";
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
		echo "	<td style=\"text-align:left;\">{$subject}</td>\n";
		echo "	<td>{$str_name}</td>\n";
		if($qnasetup->datedisplay!="N"){
			echo "	<td class=\"verdana\">{$date}</td>\n";
		}
		//echo "	<td class=\"verdana\">{$hit}</td>\n";
		echo "</tr>\n";
//		if($isgrantview) {
			if($row->is_secret!="1") {
		?>
				<tr id="qnacontent<?=$j?>" style="display:none">
				<td class="commnet" colspan="4"><?=$row->content?></td>
				</tr>
		<?
			}
//		}

		$j++;
	}
	pmysql_free_result($result);
	$a_div_prev_page=$a_prev_page=$print_page=$a_next_page=$a_div_next_page="";
	if($j==0) {
		echo "<tr><td colspan=\"{$colspan}\" height=\"26\" align=\"center\">등록된 질문이 없습니다.</td></tr>\n";
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
				$a_first_block .= "<a href=\"javascript:GoPage_prqna(\"prqna\",0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";
			}
			if ($nowblock > 0) {
				$a_prev_page .= "<a href=\"javascript:GoPage_prqna(\"prqna\",".($nowblock-1).",".($qnasetup->page_num*($qnablock-1)+$qnasetup->page_num).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$qnasetup->page_num} 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}
			if (intval($total_block) <> intval($nowblock)) {
				for ($gopage = 1; $gopage <= $qnasetup->page_num; $gopage++) {
					if ((intval($nowblock*$qnasetup->page_num) + $gopage) == intval($qnagotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$qnasetup->page_num) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage_prqna(\"prqna\",{$nowblock},".(intval($nowblock*$qnasetup->page_num) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$qnasetup->page_num) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$qnasetup->page_num) + $gopage)."]</FONT></a> ";
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
						$print_page .= "<a href='javascript:GoPage_prqna(\"prqna\",{$nowblock},".(intval($nowblock*$qnasetup->page_num) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$qnasetup->page_num) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$qnasetup->page_num) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($qnasetup->list_num*$qnasetup->page_num)) - 1;
				$last_gotopage = ceil($t_count/$qnasetup->list_num);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage_prqna(\"prqna\",{$last_block},{$last_gotopage});' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...{$last_gotopage}]</FONT></a>";
			}

			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage_prqna(\"prqna\",".($nowblock+1).",".($qnasetup->page_num*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 {$qnasetup->page_num} 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";
				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
	}
?>
	</table>

	<table width="846">
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center" style="padding-top:10" style="font-size:11px;"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
	<td align="right">
		<?php if ($_data->ETCTYPE["REVIEW"]=="Y") {?>
		<A HREF="<?=$Dir.BoardDir?>board.php?board=<?=$qnasetup->board?>" class="btn_buy">전체보기</a>
		<?php }?>
		<A HREF="<?=$qna_write?>" class="btn_buy">질문등록</A>
	</td>
	</tr>
	</table>
