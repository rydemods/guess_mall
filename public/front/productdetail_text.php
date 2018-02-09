<?php 
$prname=viewproductname($_pdata->productname,$_pdata->etctype,"");
$production=$_pdata->production;
$madein=$_pdata->madein;
$model=$_pdata->model;
$brand=$_pdata->brand;
$brandcode=$_pdata->brandcode;
$opendate=@substr($_pdata->opendate,0,4).(@substr($_pdata->opendate,4,2)?"-".@substr($_pdata->opendate,4,2):"").(@substr($_pdata->opendate,6,2)?"-".@substr($_pdata->opendate,6,2):"");
$selfcode=$_pdata->selfcode;
$addcode=$_pdata->addcode;
$consumprice=number_format($_pdata->consumerprice)."원";
$reserve=number_format($_pdata->reserve)."원";

if(ord($_pdata->userspec)) {
	$specarray=explode("=",$_pdata->userspec);
	for($i=0; $i<count($specarray); $i++) {
		$specarray_exp = explode("", $specarray[$i]);
		if(ord($specarray_exp[0]) || ord($specarray_exp[1])) {
			${"uspecname".($i+1)}=$specarray_exp[0];
			${"uspecvalue".($i+1)}=$specarray_exp[1];
		} else {
			${"uspecname".($i+1)}="";
			${"uspecvalue".($i+1)}="";
		}
	}
}

$quantity="<input type=text name=quantity value=\"".($miniq>1?$miniq:"1")."\" size=4 style='text-align:right'".($_pdata->assembleuse=="Y"?" readonly":" onkeyup=\"strnumkeyup(this)\"").">";
$quantity_up="\"javascript:change_quantity('up')\"";
$quantity_dn="\"javascript:change_quantity('dn')\"";

$detail="";
if(ord($detail_filter)) {
	$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
}

if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
	$detail.="<pre>{$_pdata->content}</pre>";
else if(strpos($_pdata->content,"</")!=false)
	$detail.=nl2br($_pdata->content);
else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
	$detail.=nl2br($_pdata->content);
else
	$detail.=str_replace(" ","&nbsp;",nl2br($_pdata->content));



if(strlen($arr_productcode["prev"])==18) {
	$prev="\"".$Dir.FrontDir."productdetail.php?productcode=".$arr_productcode["prev"].$add_query."&sort={$sort}\" onmouseover=\"window.status='이전상품 조회';return true;\" onmouseout=\"window.status='';return true;\"";
} else {
	$prev="\"javascript:alert('이전 상품이 없습니다.')\"";
}
if(strlen($arr_productcode["next"])==18) {
	$next="\"".$Dir.FrontDir."productdetail.php?productcode=".$arr_productcode["next"].$add_query."&sort={$sort}\" onmouseover=\"window.status='다음상품 조회';return true;\" onmouseout=\"window.status='';return true;\"";
} else {
	$next="\"javascript:alert('다음 상품이 없습니다.')\"";
}

if($_data->ETCTYPE["BRANDPRO"]=="Y" && ord($brandcode)) {
	$brandlink="\"".$Dir.FrontDir."productblist.php?brandcode={$brandcode}\"";
} else {
	$brandlink="\"javascript:void(0);\"";
}

$reviewall="";
$review_write="";
$review_result="";
$review_total="";
$review_average="";
$review_hide_start="";
$review_hide_end="";

$review_marks="";
$review_list="";
if($_data->review_type!="N") {
	if ($_data->ETCTYPE["REVIEW"]=="Y") {
		$reviewall="\"".$Dir.FrontDir."reviewall.php\" onmouseover=\"window.status='사용후기 모음';return true;\" onmouseout=\"window.status='';return true;\"";
	} else {
		$reviewall="\"javascript:alert('사용후기 모음 기능 설정이 안되었습니다.')\"";
	}

	if(strlen($_ShopInfo->getMemid())==0 && $_data->review_memtype=="Y") {
		$review_write="\"javascript:check_login()\"";
	} else {
		$review_write="\"javascript:review_write()\"";
	}
	$review_result="\"javascript:CheckReview()\"";

	$colspan=4;
	if($reviewdate!="N") $colspan=5;
	$qry = "WHERE productcode='{$productcode}' ";
	if($_data->review_type=="A") $qry.= "AND display='Y' ";
	$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
	$sql.= $qry;
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$t_count = (int)$row->t_count;
	$totmarks = (int)$row->totmarks;
	$marks=@ceil($totmarks/$t_count);
	pmysql_free_result($result);
	$paging = new Paging($t_count,10,$listnum,'GoPage',true);
	$gotopage = $paging->gotopage;

	$review_total=$t_count;
	for($i=0;$i<$marks;$i++) $review_average.="<FONT color={$review_average_color2}>★</FONT>";
	for($i=$marks;$i<5;$i++) $review_average.="<FONT color={$review_average_color1}>★</FONT>";

	$review_hide_start="<span id=reviewwrite style=\"display:none;\">";
	$review_hide_end="</span>";
	$review_show_start="<span id=reviewwrite style=\"display:'';\">";
	$review_show_end="</span>";


	$review_name="<input type=text name=rname size=10 style=\"{$reviewname_style}\">";
	$review_area="<textarea name=rcontent style=\"{$reviewarea_style}\"></textarea>";

	$review_marks.="<input type=radio name=rmarks value=1 style=\"border:0\"><FONT color={$review_marks_color}><B>★</B></FONT>";
	$review_marks.="&nbsp;";
	$review_marks.="<input type=radio name=rmarks value=2 style=\"border:0\"><FONT color={$review_marks_color}><B>★★</B></FONT>";
	$review_marks.="&nbsp;";
	$review_marks.="<input type=radio name=rmarks value=3 style=\"border:0\"><FONT color={$review_marks_color}><B>★★★</B></FONT>";
	$review_marks.="&nbsp;";
	$review_marks.="<input type=radio name=rmarks value=4 style=\"border:0\"><FONT color={$review_marks_color}><B>★★★★</B></FONT>";
	$review_marks.="&nbsp;";
	$review_marks.="<input type=radio name=rmarks value=5 checked style=\"border:0\"><FONT color={$review_marks_color}><B>★★★★★</B></FONT>";

	$review_mark1="<input type=radio name=rmarks value=1 style=\"border:0\">";
	$review_mark2="<input type=radio name=rmarks value=2 style=\"border:0\">";
	$review_mark3="<input type=radio name=rmarks value=3 style=\"border:0\">";
	$review_mark4="<input type=radio name=rmarks value=4 style=\"border:0\">";
	$review_mark5="<input type=radio name=rmarks value=5 style=\"border:0\">";

	$review_list.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$review_list.="<col width=50></col>\n";
	$review_list.="<col width=></col>\n";
	$review_list.="<col width=80></col>\n";
	if($reviewdate!="N") {
		$review_list.="<col width=80></col>\n";
	}
	$review_list.="<col width=90></col>\n";
	$review_list.="<tr><td colspan=\"{$colspan}\" height=1 bgcolor=#dddddd></td></tr>\n";
	$review_list.="<tr height=25 bgcolor=#f0f0f0>\n";
	$review_list.="	<td align=center style=\"color:#000000\">번호</td>\n";
	$review_list.="	<td align=center style=\"color:#000000\">사용후기</td>\n";
	$review_list.="	<td align=center style=\"color:#000000\">작성자</td>\n";
	if($reviewdate!="N") {
		$review_list.="<td align=center style=\"color:#000000\">작성일</td>\n";
	}
	$review_list.="	<td align=center style=\"color:#000000\">평점</td>\n";
	$review_list.="</tr>\n";
	$review_list.="<tr><td colspan={$colspan} height=1 bgcolor=#dddddd></td></tr>\n";
	$sql = "SELECT * FROM tblproductreview {$qry} ";
	$sql.= "ORDER BY num DESC ";
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());
	$j=0;
	while($row=pmysql_fetch_object($result)) {
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$j);

		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
		$review_list.="<tr height=25>\n";
		$review_list.="	<td align=center class=verdana>{$number}</td>\n";
		$review_list.="	<td style=\"padding:0,5,0,5\">";
		if($reviewlist=="Y") {
			$review_list.="<A HREF=\"javascript:view_review({$j})\">".titleCut(55,$content[0])."</A>";
		} else {
			$review_list.="<A HREF=\"javascript:review_open('{$row->productcode}',{$row->num})\">".titleCut(55,$content[0])."</A>";
		}
		if(ord($content[1])) $review_list.="<img src=\"{$Dir}images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
		$review_list.="	</td>\n";
		$review_list.="	<td align=center>{$row->name}</td>\n";
		if($reviewdate!="N") {
			$review_list.="	<td align=center class=verdana>{$date}</td>\n";
		}
		$review_list.="	<td align=center>";
		for($i=0;$i<$row->marks;$i++) {
			$review_list.="<FONT color=#000000>★</FONT>";
		}
		for($i=$row->marks;$i<5;$i++) {
			$review_list.="<FONT color=#CACACA>★</FONT>";
		}
		$review_list.="	</td>\n";
		$review_list.="</tr>\n";
		if($reviewlist=="Y") {
			$review_list.="<tr id=reviewspan style=\"display:none; xcursor:hand\">\n";
			$review_list.="	<td colspan={$colspan}>\n";
			$review_list.="	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style=\"table-layout:fixed\">\n";
			$review_list.="	<tr>\n";
			$review_list.="		<td style=\"border:#f0f0f0 solid 1px\">\n";
			$review_list.="		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style=\"table-layout:fixed\">\n";
			$review_list.="		<tr>\n";
			$review_list.="			<td align=center style=\"padding:15\">\n";
			$review_list.="			<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			$review_list.="			<tr>\n";
			$review_list.="				<td bgcolor=#FFFFFF style=\"border:#f0f0f0 solid 1px; padding:15\">\n";
			$review_list.="				<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			$review_list.="				<tr><td>".nl2br($content[0])."</td></tr>\n";
			if(ord($content[1])) {
				$review_list.="	<tr><td style=\"padding:5 5 5 10px\"><img src=\"{$Dir}images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1])."</td></tr>\n";
			}
			$review_list.="				</table>\n";
			$review_list.="				</td>\n";
			$review_list.="			</tr>\n";
			$review_list.="			</table>\n";
			$review_list.="			</td>\n";
			$review_list.="		</tr>\n";
			$review_list.="		<tr>\n";
			$review_list.="			<td align=right style=\"padding-right:10\"><a href=\"javascript:view_review({$j})\"><img src=\"{$Dir}images/common/review/review_close.gif\" border=0></a></td>\n";
			$review_list.="		</tr>\n";
			$review_list.="		<tr><td height=10></td></tr>\n";
			$review_list.="		</table>\n";
			$review_list.="		</td>\n";
			$review_list.="	</tr>\n";
			$review_list.="	</table>\n";
			$review_list.="	</td>\n";
			$review_list.="</tr>\n";
		}
		$review_list.="<tr><td colspan=\"{$colspan}\" height=1 bgcolor=#dddddd></td></tr>\n";
		$j++;
	}
	pmysql_free_result($result);
	if($j==0) {
		$review_list.="<tr><td colspan={$colspan} height=25 align=center>등록된 사용후기가 없습니다.</td></tr>\n";
	} else {
		$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
	}
	$review_list.="<tr><td colspan=\"{$colspan}\" height=1 bgcolor=#dddddd></td></tr>\n";
	$review_list.="<tr>\n";
	$review_list.="	<td colspan=\"{$colspan}\" align=center style=\"padding-top:10\">\n";
	$review_list.=$pageing;
	$review_list.="	</td>\n";
	$review_list.="</tr>\n";
	$review_list.="</table>\n";

} else {
	$reviewall="\"javascript:alert('사용후기 기능 설정이 안되었습니다.')\"";
	$review_write="\"javascript:alert('사용후기 기능 설정이 안되었습니다.')\"";
	$review_result="\"javascript:alert('사용후기 기능 설정이 안되었습니다.')\"";
}

//상품Q&A
$qna_write="";
$qna_all="";
$qna_list="";
if(ord($qnasetup->board)){
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

	$qna_list = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$qna_list.= "<col width=50></col>\n";
	$qna_list.= "<col width=></col>\n";
	$qna_list.= "<col width=100></col>\n";
	if($qnasetup->datedisplay!="N"){
		$qna_list.= "<col width=100></col>\n";
	}
	$qna_list.= "<col width=50></col>\n";
	$qna_list.= "<tr><td colspan=\"{$colspan}\" height=2 bgcolor=#dddddd></td></tr>\n";
	$qna_list.= "<tr height=25 bgcolor=#f0f0f0>\n";
	$qna_list.= "	<td align=center style=\"color:#000000\">번호</td>\n";
	$qna_list.= "	<td align=center style=\"color:#000000\">제목</td>\n";
	$qna_list.= "	<td align=center style=\"color:#000000\">작성자</td>\n";
	if($qnasetup->datedisplay!="N"){
		$qna_list.= "	<td align=center style=\"color:#000000\">작성일</td>\n";
	}
	$qna_list.= "	<td align=center style=\"color:#000000\">조회</td>\n";
	$qna_list.= "</tr>\n";
	$qna_list.= "<tr><td colspan={$colspan} height=1 bgcolor=#dddddd></td></tr>\n";

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
			$subject .= "<img src={$imgdir}/x.gif width={$wid} height=2 border=0>";
			$subject .= "<img src={$imgdir}/re_mark.gif border=0>";
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
			$subject .= "&nbsp;<img src={$imgdir}/icon_new.gif border=0 align=absmiddle>&nbsp;";
			$new_img .= "<img src={$imgdir}/icon_new.gif border=0 align=absmiddle>&nbsp;";
		}
		if ($qnasetup->use_comment=="Y" && $row->total_comment > 0) {
			$subject .= " <img src={$imgdir}/icon_memo.gif border=0 align=absmiddle>&nbsp;<font style=font-size:8pt;font-family:Tahoma;font-weight:normal>(<font color=red>{$row->total_comment}</font>)</font>";
		}

		$comment_tot = $row->total_comment;
		$user_name = $row->name;
		$str_name = $user_name;
		$hit = $row->access;

		$qna_list.= "<tr height=25>\n";
		$qna_list.= "	<td align=center class=verdana>{$number}</td>\n";
		$qna_list.= "	<td align=left>{$subject}</td>\n";
		$qna_list.= "	<td align=center>{$str_name}</td>\n";
		if($qnasetup->datedisplay!="N"){
			$qna_list.= "	<td align=center class=verdana>{$date}</td>\n";
		}
		$qna_list.= "	<td align=center class=verdana>{$hit}</td>\n";
		$qna_list.= "</tr>\n";
		if($isgrantview) {
			if($row->is_secret!="1") {
				$qna_list.= "<tr id=\"qnacontent{$j}\" style=\"display:none\">\n";
				$qna_list.= "	<td colspan={$colspan}>\n";
				$qna_list.= "	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style=\"table-layout:fixed\">\n";
				$qna_list.= "	<tr>\n";
				$qna_list.= "		<td style=\"border:#f0f0f0 solid 1px\">\n";
				$qna_list.= "		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style=\"table-layout:fixed\">\n";
				$qna_list.= "		<tr>\n";
				$qna_list.= "			<td align=center style=\"padding:15\">\n";
				$qna_list.= "			<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
				$qna_list.= "			<tr>\n";
				$qna_list.= "				<td bgcolor=#FFFFFF style=\"border:#f0f0f0 solid 1px; padding:15\">\n";
				$qna_list.= "				{$row->content}\n";
				$qna_list.= "				</td>\n";
				$qna_list.= "			</tr>\n";
				$qna_list.= "			</table>\n";
				$qna_list.= "			</td>\n";
				$qna_list.= "		</tr>\n";
				$qna_list.= "		<tr>\n";
				$qna_list.= "			<td align=right style=\"padding-right:10\"><a href=\"javascript:view_qnacontent('{$j}')\"><img src=\"{$Dir}images/common/btn_qnaclose.gif\" border=0></a></td>\n";
				$qna_list.= "		</tr>\n";
				$qna_list.= "		<tr><td height=10></td></tr>\n";
				$qna_list.= "		</table>\n";
				$qna_list.= "		</td>\n";
				$qna_list.= "	</tr>\n";
				$qna_list.= "	</table>\n";
				$qna_list.= "	</td>\n";
				$qna_list.= "</tr>\n";
			}
		}
		$qna_list.= "<tr><td colspan=\"{$colspan}\" height=1 bgcolor=#dddddd></td></tr>\n";
		$j++;
	}
	pmysql_free_result($result);
	$a_div_prev_page=$a_prev_page=$print_page=$a_next_page=$a_div_next_page="";
	if($j==0) {
		$qna_list.= "<tr><td colspan={$colspan} height=25 align=center>등록된 상품문의가 없습니다.</td></tr>\n";
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
				$a_first_block .= "<a href='javascript:GoPage(\"prqna\",0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";
			}
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(\"prqna\",".($nowblock-1).",".($qnasetup->page_num*($qnablock-1)+$qnasetup->page_num).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 {$qnasetup->page_num} 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

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

	$qna_list.= "<tr><td colspan=\"{$colspan}\" height=1 bgcolor=#dddddd></td></tr>\n";
	$qna_list.= "<tr>\n";
	$qna_list.= "	<td colspan=\"{$colspan}\" align=center style=\"padding-top:10\">\n";
	$qna_list.= $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	$qna_list.= "	</td>\n";
	$qna_list.= "</tr>\n";
	$qna_list.= "</table>\n";

} else {
	$qna_all="\"javascript:alert('상품Q&A 기능 설정이 안되었습니다.')\"";
	$qna_write="\"javascript:alert('상품Q&A 기능 설정이 안되었습니다.')\"";
	$qna_list="";
}

$primage="";
if($multi_img=="Y") {
	$primage.="<iframe src=\"".$Dir.FrontDir."primage_multiframe.php?productcode={$productcode}&thumbtype={$thumbtype}\" frameborder=0 width=300 height={$multi_height}></iframe>";
} else {
	if(ord($_pdata->maximage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
		$imgsize=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
		if(($imgsize[1]>550 || $imgsize[0]>750) && $multi_img!="I") $imagetype=1;
		else $imagetype=0;
	}
	if(ord($_pdata->minimage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->minimage)) {
		$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->minimage);
		if($width[0]>=300) $width[0]=300;
		else if (ord($width[0])==0) $width[0]=300;
		$primage.="<a href=\"javascript:primage_view('{$_pdata->maximage}','{$imagetype}')\">";
		$primage.="<img src=\"".$Dir.DataDir."shopimages/product/{$_pdata->minimage}\" border=0 width={$width[0]}></a><br><br>\n";
	} else {
		$primage.="<img src=\"{$Dir}images/no_img.gif\" border=0><br><br>\n";
	}
	if($multi_img=="I") {
		$primage.="<a href=\"javascript:primage_view('{$_pdata->maximage}','{$imagetype}')\"><img src=\"{$Dir}images/common/product/".substr($_cdata->detail_type,0,5)."/btn_zoom.gif\" border=0 align=absmiddle></a>\n";
	} else if(ord($_pdata->maximage)) {
		$primage.="<a href=\"javascript:primage_view('{$_pdata->maximage}','{$imagetype}')\"><img src=\"{$Dir}images/common/product/".substr($_cdata->detail_type,0,5)."/btn_zoom.gif\" border=0 align=absmiddle></a>\n";
	}
	$primage.="</a>\n";
}

$collection=$collection_body;



$prinfo="";
$prinfo.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$prinfo.="<col width=90></col>\n";
$prinfo.="<col width=></col>\n";

$prproductname ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">상품명</td>\n";
$prproductname.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->productname}</td>\n";

if(ord($_pdata->production)) {
	$prproduction ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">제조회사</td>\n";
	$prproduction.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->production}</td>\n";
}
if(ord($_pdata->madein)) {
	$prmadein ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">원산지</td>\n";
	$prmadein.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->madein}</td>\n";
}
if(ord($_pdata->model)) {
	$prmodel ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">모델명</td>\n";
	$prmodel.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->model}</td>\n";
}
if(ord($_pdata->brand)) {
	$prbrand ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">브랜드</td>\n";
	$prbrand.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->brand}</td>\n";
}
if(ord($_pdata->userspec)) {
	$specarray= explode("=",$_pdata->userspec);
	for($i=0; $i<count($specarray); $i++) {
		$specarray_exp = explode("", $specarray[$i]);
		if(ord($specarray_exp[0]) || ord($specarray_exp[1])) {
			${"pruserspec".$i} ="<td><IMG SRC=\"{$Dir}images/common/product/{$_cdata->detail_type}/pdetail_skin_point.gif\" border=\"0\"></td>\n";
			${"pruserspec".$i}.="<td>{$specarray_exp[0]}</td>\n";
			${"pruserspec".$i}.="<td></td>";
			${"pruserspec".$i}.="<td>{$specarray_exp[1]}</td>\n";
		} else {
			${"pruserspec".$i} = "";
		}
	}
}
if(ord($_pdata->opendate)) {
	$propendate ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">출시일</td>\n";
	$propendate.="<td bgcolor=#fafafa style=\"padding-left:10\">".@substr($_pdata->opendate,0,4).(@substr($_pdata->opendate,4,2)?"-".@substr($_pdata->opendate,4,2):"").(@substr($_pdata->opendate,6,2)?"-".@substr($_pdata->opendate,6,2):"")."</td>\n";
}
if(ord($_pdata->selfcode)) {
	$prselfcode ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">진열코드</td>\n";
	$prselfcode.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->selfcode}</td>\n";
}
if($_pdata->consumerprice>0) {
	$prconsumerprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">소비자가격</td>\n";
	$prconsumerprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle><strike>".number_format($_pdata->consumerprice)."</strike>원</td>\n";
}
if($_pdata->reserve>0) {
	$prreserve ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">적립금</td>\n";
	$prreserve.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle>".number_format($_pdata->reserve)."원</td>\n";
}

$sellprice="";
$gongprice="";
$dollar="";
$detailhidden="";
$SellpriceValue=0;
if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0) {
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">판매가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\">{$dicker}</td>\n";
	$prdollarprice="";
	$priceindex=0;
	$gong_sellprice=number_format($_pdata->sellprice);
	$sellprice=$dicker;
} else if(ord($optcode)==0 && ord($_pdata->option_price)) {
	$option_price = $_pdata->option_price;
	$pricetok=explode(",",$option_price);
	$priceindex = count($pricetok);
	for($tmp=0;$tmp<=$priceindex;$tmp++) {
		$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
		$pricetok[$tmp]=number_format($pricetok[$tmp]);
	}
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">판매가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT></td>\n";
	$detailhidden.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";

	$prdollarprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">해외화폐</td>\n";
	$prdollarprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT></td>\n";

	$detailhidden.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
	$gong_sellprice=number_format($_pdata->sellprice);
	$sellprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT>";
	$dollar="<FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT>";
	$SellpriceValue=str_replace(",","",$pricetok[0]);
} else if(ord($optcode)) {
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">판매가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT></td>\n";
	$detailhidden.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";

	$prdollarprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">해외화폐</td>\n";
	$prdollarprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT></td>\n";

	$detailhidden.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
	$gong_sellprice=number_format($_pdata->sellprice);
	$sellprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT>";
	$dollar="<FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT>";
	$SellpriceValue=$_pdata->sellprice;
} else if(ord($_pdata->option_price)==0) {
	if($_pdata->assembleuse=="Y") {
		$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">판매가격</td>\n";
		$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</FONT></td>\n";
		$detailhidden.="<input type=hidden name=price value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."\">\n";

		$prdollarprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">해외화폐</td>\n";
		$prdollarprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)." {$ardollar[2]}</FONT></td>\n";

		$detailhidden.="<input type=hidden name=dollarprice value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)."\">\n";
		$priceindex=0;
		$gong_sellprice=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice));
		$sellprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</FONT>";
		$dollar="<FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)." {$ardollar[2]}</FONT>";
		$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
	} else {
		$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">판매가격</td>\n";
		$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT></td>\n";
		$detailhidden.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";

		$prdollarprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">해외화폐</td>\n";
		$prdollarprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT></td>\n";

		$detailhidden.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
		$priceindex=0;
		$gong_sellprice=number_format($_pdata->sellprice);
		$sellprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT>";
		$dollar="<FONT id=\"idx_dollarprice\">{$ardollar[0]} ".number_format($_pdata->sellprice/$ardollar[1],2)." {$ardollar[2]}</FONT>";
		$SellpriceValue=$_pdata->sellprice;
	}
}

if($_pdata->reservetype=="Y") {
	$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"N");
	$reserve="<FONT id=\"idx_reserve\">".number_format($reserveconv)."원</font>";
	if($reserveconv>0) {
		$prreserve ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">적립금</td>\n";
		$prreserve.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/reserve_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_reserve\">".number_format($reserveconv)."원</font></td>\n";
	}
}

if(ord($_pdata->addcode)) {
	$praddcode ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">특이사항</td>\n";
	$praddcode.="<td bgcolor=#fafafa style=\"padding-left:10\">{$_pdata->addcode}</td>\n";
}

$prquantity ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">구매수량</td>\n";
$prquantity.="<td bgcolor=#fafafa style=\"padding-left:10\">\n";
$prquantity.="<table border=0 cellpadding=0 cellspacing=0>\n";
$prquantity.="<tr>\n";
$prquantity.="	<td rowspan=3 width=40><input type=text name=quantity value=\"".($miniq>1?$miniq:"1")."\" size=4 style='text-align:right'".($_pdata->assembleuse=="Y"?" readonly":" onkeyup=\"strnumkeyup(this)\"").">";
$prquantity.="	</td>";
$prquantity.="	<td><a href=\"javascript:change_quantity('up')\"><img src={$Dir}images/common/btn_plus.gif border=0></a></td>\n";
$prquantity.="	<td rowspan=3 width=30 align=center valign=middle>EA</td>\n";
$prquantity.="</tr>\n";
$prquantity.="<tr><td height=1></td></tr>\n";
$prquantity.="<tr>\n";
$prquantity.="	<td><a href=\"javascript:change_quantity('dn')\"><img src={$Dir}images/common/btn_minus.gif border=0></a></td>\n";
$prquantity.="</tr>\n";
$prquantity.="</table>\n";
$prquantity.="</td>\n";

// 패키지 선택 출력
$arrpackage_title=array();
$arrpackage_list=array();
$arrpackage_price=array();
$arrpackage_pricevalue=array();
if((int)$_pdata->package_num>0) {
	$sql = "SELECT * FROM tblproductpackage WHERE num='".(int)$_pdata->package_num."' ";
	$result = pmysql_query($sql,get_db_conn());
	$package_count=0;
	if($row = @pmysql_fetch_object($result)) {
		pmysql_free_result($result);
		if(ord($row->package_title)) {
			$arrpackage_title = explode("",$row->package_title);
			$arrpackage_list = explode("",$row->package_list);
			$arrpackage_price = explode("",$row->package_price);

			$package_listrep = str_replace("","",$row->package_list);

			if(ord($package_listrep)) {
				$sql = "SELECT pridx,productcode,productname,sellprice,tinyimage,quantity,etctype FROM tblproduct ";
				$sql.= "WHERE pridx IN ('".str_replace(",","','",ltrim($package_listrep,','))."') ";
				$sql.= "AND assembleuse!='Y' ";
				$sql.= "AND display='Y' ";
				$result2 = pmysql_query($sql,get_db_conn());
				while($row2 = @pmysql_fetch_object($result2)) {
					$arrpackage_proinfo['productcode'][$row2->pridx] = $row2->productcode;
					$arrpackage_proinfo['productname'][$row2->pridx] = $row2->productname;
					$arrpackage_proinfo['sellprice'][$row2->pridx] = $row2->sellprice;
					$arrpackage_proinfo['tinyimage'][$row2->pridx] = $row2->tinyimage;
					$arrpackage_proinfo['quantity'][$row2->pridx] = $row2->quantity;
					$arrpackage_proinfo['etctype'][$row2->pridx] = $row2->etctype;
				}
				@pmysql_free_result($result2);
			}

			for($t=1; $t<count($arrpackage_list); $t++) {
				$arrpackage_pricevalue[0]=0;
				$arrpackage_pricevalue[$t]=0;
				if(ord($arrpackage_list[$t])) {
					$arrpackage_list_exp = explode(",",$arrpackage_list[$t]);
					$sumsellprice=0;
					for($tt=0; $tt<count($arrpackage_list_exp); $tt++) {
						$sumsellprice += (int)$arrpackage_proinfo['sellprice'][$arrpackage_list_exp[$tt]];
					}

					if((int)$sumsellprice>0) {
						$arrpackage_pricevalue[$t]=(int)$sumsellprice;
						if(ord($arrpackage_price[$t])) {
							$arrpackage_price_exp = explode(",",$arrpackage_price[$t]);
							if(ord($arrpackage_price_exp[0]) && $arrpackage_price_exp[0]>0) {
								$sumsellpricecal=0;
								if($arrpackage_price_exp[1]=="Y") {
									$sumsellpricecal = ((int)$sumsellprice*$arrpackage_price_exp[0])/100;
								} else {
									$sumsellpricecal = $arrpackage_price_exp[0];
								}
								if($sumsellpricecal>0) {
									if($arrpackage_price_exp[2]=="Y") {
										$sumsellpricecal = $sumsellprice-$sumsellpricecal;
									} else {
										$sumsellpricecal = $sumsellprice+$sumsellpricecal;
									}
									if($sumsellpricecal>0) {
										if($arrpackage_price_exp[4]=="F") {
											$sumsellpricecal = floor($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else if($arrpackage_price_exp[4]=="R") {
											$sumsellpricecal = round($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else {
											$sumsellpricecal = ceil($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										}
										$arrpackage_pricevalue[$t]=$sumsellpricecal;
									}
								}
							}
						}
					}
				}
				$propackage_option.= "<option value=\"{$t}\" style=\"color:#ffffff;\">{$arrpackage_title[$t]}</option>\n";
				$package_count++;
			}
		}
	}

	if($package_count>0) {
		$prpackage ="<select name=\"package_idx\" size=\"1\" style=\"font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
		if($_data->proption_size>0) $prpackage.="style=\"width : {$_data->proption_size}px;\" ";
		$prpackage.=")\" onchange=\"packagecal()\">\n";
		$prpackage.="<option value=\"\" style=\"color:#ffffff;\">패키지를 선택하세요</option>\n";
		$prpackage.="<option value=\"\" style=\"color:#ffffff;\">-------------------\n";
		$prpackage.=$propackage_option;
		$prpackage.="</select>\n";
		$detailhidden.="<input type=hidden name=\"package_type\" value=\"{$row->package_type}\">\n";
	}
}

$packagevalue="";
if(ord($prpackage)) {
	$pattern=array("[PACKAGESELECT]");
	$replace=array($prpackage);
	$packagevalue=str_replace($pattern,$replace,$bodypackage);
}

$proption1="";
if(ord($_pdata->option1)) {
	$temp = $_pdata->option1;
	$tok = explode(",",$temp);
	$count=count($tok);
	$proption1.="$tok[0] : ";
	if ($priceindex!=0) {
		$proption1.="<select name=option1 size=1 ";
		if($_data->proption_size>0) $proption1.="style=\"width : {$_data->proption_size}px\" ";
		$proption1.="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
		if(ord($_pdata->option2)) $proption1.="document.form1.option2.selectedIndex-1";
		else $proption1.="''";
		$proption1.=")\">\n";
	} else {
		$proption1.="<select name=option1 size=1 ";
		if($_data->proption_size>0) $proption1.="style=\"width : {$_data->proption_size}px\" ";
		$proption1.="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
		if(ord($_pdata->option2)) $proption1.="document.form1.option2.selectedIndex-1";
		else $proption1.="''";
		$proption1.=")\">\n";
	}

	$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
	$proption1.="<option value=\"\">옵션을 선택하세요\n";
	$proption1.="<option value=\"\">-----------------\n";
	for($i=1;$i<$count;$i++) {
		if(ord($tok[$i])) $proption1.="<option value=\"$i\">$tok[$i]\n";
		if(ord($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
	}
	$proption1.="</select>\n";
}

$proption2="";
if(ord($_pdata->option2)) {
	$temp = $_pdata->option2;
	$tok = explode(",",$temp);
	$count2=count($tok);
	$proption2.="$tok[0] : ";
	$proption2.="<select name=option2 size=1 ";
	if($_data->proption_size>0) $proption2.="style=\"width : {$_data->proption_size}px\" ";
	$proption2.="onchange=\"change_price(0,";
	if(ord($_pdata->option1)) $proption2.="document.form1.option1.selectedIndex-1";
	else $proption2.="''";
	$proption2.=",document.form1.option2.selectedIndex-1)\">\n";
	$proption2.="<option value=\"\">옵션을 선택하세요\n";
	$proption2.="<option value=\"\">-----------------\n";
	for($i=1;$i<$count2;$i++) if(ord($tok[$i])) $proption2.="<option value=\"$i\">$tok[$i]\n";
	$proption2.="</select>\n";
}

if(ord($optcode)) {
	$sql = "SELECT * FROM tblproductoption WHERE option_code='{$optcode}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row = pmysql_fetch_object($result)) {
		$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
		$opti=0;
		$option_choice = $row->option_choice;
		$exoption_choice = explode("",$option_choice);
		while(ord($optionadd[$opti])) {
			$proption3.="[OPT]";
			$proption3.="<select name=mulopt onchange=\"chopprice('$opti')\"";
			if($_data->proption_size>0) $proption3.=" style=\"width : {$_data->proption_size}px\"";
			$proption3.=">";
			$opval = str_replace('"','',explode("",$optionadd[$opti]));
			$proption3.="<option value=\"0,0\">--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
			$opcnt=count($opval);
			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				$proption3.="<option value=\"{$opval[$j]}\">";
				if($exop[1]>0) $proption3.=$exop[0]."(+{$exop[1]}원)";
				else if($exop[1]==0) $proption3.=$exop[0];
				else $proption3.=$exop[0]."({$exop[1]}원)";
			}
			$proption3.="</select>[OPTEND]";
			$detailhidden.="<input type=hidden name=opttype value=0><input type=hidden name=optselect value={$exoption_choice[$opti]}>\n";
			$opti++;
		}
		$detailhidden.="<input type=hidden name=mulopt><input type=hidden name=opttype><input type=hidden name=optselect>";
	}
	pmysql_free_result($result);
}

for($i=0;$i<$prcnt;$i++) {
	if($arexcel[$i][0]=="O") {	//공백
		$prinfo.="<tr><td colspan=2 height=5></td></tr>\n";
	} else if ($arexcel[$i]=="7") {	//옵션
		if(ord($proption1) || ord($proption2) || ord($proption3)) {
			$proption ="<tr height=28>";
			$proption.="	<td bgcolor=#f4f4f4 style=\"padding-left:10\">상품옵션</td>";
			$proption.="	<td bgcolor=#fafafa style=\"padding-left:10\">\n";
			$proption.="	<table border=0 cellpadding=0 cellspacing=0>\n";
			if(ord($proption1)) {
				$proption.="	<tr><td align=right style=\"padding:2\">{$proption1}</td></tr>\n";
			}
			if(ord($proption2)) {
				$proption.="	<tr><td align=right style=\"padding:2\">{$proption2}</td></tr>\n";
			}
			if(ord($proption3)) {
				$pattern=array("[OPT]","[OPTEND]");
				$replace=array("<tr height=28><td>","</td></tr>");
				$proption.=str_replace($pattern,$replace,$proption3);
			}
			$proption.="	</table>\n";
			$proption.="	</td>\n";
			$proption.="</tr>\n";

			$prinfo.=$arproduct[$arexcel[$i]];
		} else {
			$detailhidden ="<input type=hidden name=option1>\n";
			$detailhidden.="<input type=hidden name=option2>\n";
		}
	} else if(ord($arproduct[$arexcel[$i]])) {	//
		$prinfo.="<tr height=28>{$arproduct[$arexcel[$i]]}</tr>\n";
		$prinfo.="<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";
		if($arexcel[$i]=="9") $dollarok="Y";
	}
}
$prinfo.="</table>\n";

$option1="";
$option2="";
if(ord($proption3)) {
	$pattern=array("[OPT]","[OPTEND]");
	$replace=array("<tr height=28><td>","</td></tr>");
	$option1=str_replace($pattern,$replace,$proption3);
	$option1="<table border=0 cellpadding=0 cellspacing=0>{$option1}</table>\n";
} else {
	$option1=$proption1;
	$option2=$proption2;
}

$optionvalue="";
if(ord($option1) || ord($option2)) {
	$pattern=array("[OPTION1]","[OPTION2]");
	$replace=array($option1,$option2);
	$bodyoption=str_replace($pattern,$replace,$bodyoption);

	if(strpos($bodyoption,"[IFOPTION1]")!=0) {
		$ifoption1num=strpos($bodyoption,"[IFOPTION1]");
		$endoption1num=strpos($bodyoption,"[IFENDOPTION1]");
		$bodyoption1=substr($bodyoption,$ifoption1num+11,$endoption1num-$ifoption1num-11);
		$bodyoption=substr($bodyoption,0,$ifoption1num)."[OPTION1VALUE]".substr($bodyoption,$endoption1num+14);
	}
	if(strpos($bodyoption,"[IFOPTION2]")!=0) {
		$ifoption2num=strpos($bodyoption,"[IFOPTION2]");
		$endoption2num=strpos($bodyoption,"[IFENDOPTION2]");
		$bodyoption2=substr($bodyoption,$ifoption2num+11,$endoption2num-$ifoption2num-11);
		$bodyoption=substr($bodyoption,0,$ifoption2num)."[OPTION2VALUE]".substr($bodyoption,$endoption2num+14);
	}
	$pattern=array("[OPTION1VALUE]","[OPTION2VALUE]");
	$replace=array($bodyoption1,$bodyoption2);
	$bodyoption=str_replace($pattern,$replace,$bodyoption);
	$optionvalue=$bodyoption;
}

$vendervalue="";
if($_vdata->vender>0) {
	$vender_name=$_vdata->brand_name;
	$vender_minishop="javascript:GoMinishop('".$Dir.(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$_vdata->id."')";
	$vender_prdtcnt=$_vdata->prdt_cnt;
	$vender_regist="javascript:custRegistMinishop()";
	$pattern=array("[VENDER_NAME]","[VENDER_MINISHOP]","[VENDER_PRDTCNT]","[VENDER_REGIST]");
	$replace=array($vender_name,$vender_minishop,$vender_prdtcnt,$vender_regist);
	$vendervalue=str_replace($pattern,$replace,$bodyvender);
}


$gonginfo="";
$gonginfo.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$gonginfo.="<col width=90></col>\n";
$gonginfo.="<col width=></col>\n";
if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0) {
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">현재가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\">{$dicker}</td>\n";
	$priceindex=0;

	$gong_sellprice=number_format($_pdata->sellprice);
} else if(ord($optcode)==0 && ord($_pdata->option_price)) {
	$option_price = $_pdata->option_price;
	$pricetok=explode(",",$option_price);
	$priceindex = count($pricetok);
	for($tmp=0;$tmp<=$priceindex;$tmp++) {
		$pricetok[$tmp]=number_format($pricetok[$tmp]);
	}
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">현재가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">{$pricetok[0]}원</FONT></td>\n";

	$gong_sellprice=$pricetok[0];
	$gongprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">{$pricetok[0]}원</FONT>";
} else if(ord($optcode)) {
	$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">현재가격</td>\n";
	$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT></td>\n";

	$gong_sellprice=number_format($_pdata->sellprice);
	$gongprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT>";
} else if(ord($_pdata->option_price)==0) {
	if($_pdata->assembleuse=="Y") {
		$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">현재가격</td>\n";
		$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</FONT></td>\n";
		$priceindex=0;

		$gong_sellprice=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice));
		$gongprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."원</FONT>";
	} else {
		$prsellprice ="<td bgcolor=#f4f4f4 style=\"padding-left:10\">현재가격</td>\n";
		$prsellprice.="<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle><FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT></td>\n";
		$priceindex=0;

		$gong_sellprice=number_format($_pdata->sellprice);
		$gongprice="<FONT id=\"idx_price\" style=\"color:red;font-weight:bold\">".number_format($_pdata->sellprice)."원</FONT>";
	}
}

$gonginfo.="<tr height=28>\n";
$gonginfo.="	<td bgcolor=#f4f4f4 style=\"padding-left:10\">시중가격</td>\n";
$gonginfo.="	<td bgcolor=#fafafa style=\"padding-left:10\"><img src=\"{$Dir}images/common/won_icon2.gif\" border=0 align=absmiddle><strike><B>".number_format($_pdata->consumerprice)."원</B></strike></td>\n";
$gonginfo.="</tr>\n";
$gonginfo.="<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";

$gonginfo.="<tr>\n";
$gonginfo.="	<td bgcolor=#f4f4f4 style=\"padding-left:10\">가격변동표</td>\n";
$gonginfo.="	<td bgcolor=#ffffff style=\"padding:15,10,5,10\">\n";
$gonginfo.="	<table border=0 cellpadding=0 cellspacing=0>\n";
$gonginfo.="	<tr>\n";
$gonginfo.="		<td>\n";
$gonginfo.="		<table border=0 cellpadding=0 cellspacing=0 width=195 height=52 background=\"{$Dir}images/common/gong_graph.gif\" style=\"table-layout:fixed\">\n";
$gonginfo.="		<tr>\n";
$gonginfo.="			<td>\n";
$gonginfo.="			<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$gonginfo.="			<tr>\n";
$gonginfo.="				<td width=45% align=center style=\"padding:0,0,12,7\">".number_format($_pdata->consumerprice)."원</td>\n";
$gonginfo.="				<td width=55% colspan=2></td>\n";
$gonginfo.="			</tr>\n";
$gonginfo.="			<tr>\n";
$gonginfo.="				<td width=45%></td>\n";
$gonginfo.="				<td id=\"idx_price_graph\" width=40% align=center style=\"padding:0,5,0,3;color:red;font-weight:bold\">{$gong_sellprice}원</td>\n";
$gonginfo.="				<td width=15%></td>\n";
$gonginfo.="			</tr>\n";
$gonginfo.="			</table>\n";
$gonginfo.="			</td>\n";
$gonginfo.="		</tr>\n";
$gonginfo.="		</table>\n";
$gonginfo.="		</td>\n";
$gonginfo.="	</tr>\n";
$gonginfo.="	<tr>\n";
$gonginfo.="		<td style=\"padding-top:3\"><img width=30 height=0>시작가 <img width=38 height=0>현재가</td>\n";
$gonginfo.="	</tr>\n";
$gonginfo.="	</table>\n";
$gonginfo.="	</td>\n";
$gonginfo.="</tr>\n";
$gonginfo.="<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";

$gonginfo.="<tr height=28>{$prsellprice}</tr>\n";
$gonginfo.="<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";
$gonginfo.="<tr><td colspan=2 height=10></td></tr>\n";
$gonginfo.="<tr height=28>{$prquantity}</tr>\n";
$gonginfo.="<tr><td height=1 bgcolor=#FFFFFF></td></tr>\n";
if(ord($proption1) || ord($proption2) || ord($proption3)) {
	$proption ="<tr height=28>";
	$proption.="	<td bgcolor=#f4f4f4 style=\"padding-left:10\">상품옵션</td>";
	$proption.="	<td bgcolor=#fafafa style=\"padding-left:10\">\n";
	$proption.="	<table border=0 cellpadding=0 cellspacing=0>\n";
	if(ord($proption1)) {
		$proption.="	<tr><td align=right style=\"padding:2\">{$proption1}</td></tr>\n";
	}
	if(ord($proption2)) {
		$proption.="	<tr><td align=right style=\"padding:2\">{$proption2}</td></tr>\n";
	}
	if(ord($proption3)) {
		$pattern=array("[OPT]","[OPTEND]");
		$replace=array("<tr height=28><td>","</td></tr>");
		$proption.=str_replace($pattern,$replace,$proption3);
	}
	$proption.="	</table>\n";
	$proption.="	</td>\n";
	$proption.="</tr>\n";
	$gonginfo.=$arproduct[$arexcel[$i]];
}
$gonginfo.=$proption;
$gonginfo.="</table>\n";



$gongtable="";
$gongtable.="	<table border=0 cellpadding=0 cellspacing=0>\n";
$gongtable.="	<tr>\n";
$gongtable.="		<td>\n";
$gongtable.="		<table border=0 cellpadding=0 cellspacing=0 width=195 height=52 background=\"{$Dir}images/common/gong_graph.gif\" style=\"table-layout:fixed\">\n";
$gongtable.="		<tr>\n";
$gongtable.="			<td>\n";
$gongtable.="			<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$gongtable.="			<tr>\n";
$gongtable.="				<td width=45% align=center style=\"padding:0,0,12,7\">".number_format($_pdata->consumerprice)."원</td>\n";
$gongtable.="				<td width=55% colspan=2></td>\n";
$gongtable.="			</tr>\n";
$gongtable.="			<tr>\n";
$gongtable.="				<td width=45%></td>\n";
$gongtable.="				<td id=\"idx_price_graph\" width=40% align=center style=\"padding:0,5,0,3;color:red;font-weight:bold\">{$gong_sellprice}원</td>\n";
$gongtable.="				<td width=15%></td>\n";
$gongtable.="			</tr>\n";
$gongtable.="			</table>\n";
$gongtable.="			</td>\n";
$gongtable.="		</tr>\n";
$gongtable.="		</table>\n";
$gongtable.="		</td>\n";
$gongtable.="	</tr>\n";
$gongtable.="	<tr>\n";
$gongtable.="		<td style=\"padding-top:3\"><img width=30 height=0>시작가 <img width=38 height=0>현재가</td>\n";
$gongtable.="	</tr>\n";
$gongtable.="	</table>\n";



$detail_script="";
$detail_script.="<script language=\"JavaScript\">\n";
$detail_script.="var miniq=".($miniq>1?$miniq:1).";\n";
$detail_script.="var ardollar=new Array(3);\n";
$detail_script.="ardollar[0]=\"{$ardollar[0]}\";\n";
$detail_script.="ardollar[1]=\"{$ardollar[1]}\";\n";
$detail_script.="ardollar[2]=\"{$ardollar[2]}\";\n";
if(ord($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		$detail_script.="num = new Array(";
		for($i=0;$i<$maxnum;$i++) {
			if ($i!=0) $detail_script.=",";
			if(ord($optioncnt[$i])==0) $detail_script.="100000";
			else $detail_script.=$optioncnt[$i];
		}
		$detail_script.=");\n";
	}

	$detail_script.="function change_price(temp,temp2,temp3) {\n";
	$detail_script.=(ord($dicker))?"return;\n":"";
	$detail_script.="	if(temp3==\"\") temp3=1;\n";
	$detail_script.="	price = new Array(";
	if($priceindex>0) {
		$detail_script.="'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',";
		for($i=0;$i<$priceindex;$i++) {
			if ($i!=0) {
				$detail_script.=",";
			}
			$detail_script.="'{$pricetok[$i]}'";
		}
	}
	$detail_script.=");\n";
	$detail_script.="	doprice = new Array(";
	if($priceindex>0) {
		$detail_script.="'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',";
		for($i=0;$i<$priceindex;$i++) {
			if ($i!=0) {
				$detail_script.=",";
			}
			$detail_script.="'{$pricetokdo[$i]}'";
		}
	}
	$detail_script.=");\n";
	$detail_script.="	if(temp==1) {\n";
	$detail_script.="		if (document.form1.option1.selectedIndex>".($priceindex+2).")";
	$detail_script.="			temp = {$priceindex};\n";
	$detail_script.="		else temp = document.form1.option1.selectedIndex;\n";
	$detail_script.="		document.form1.price.value = price[temp];\n";
	$detail_script.="		document.all[\"idx_price\"].innerHTML = document.form1.price.value+\"원\";\n";

	if($_pdata->reservetype=="Y" && $_pdata->reserve>0) {
		$detail_script.="		if(document.getElementById(\"idx_reserve\")) {\n";
		$detail_script.="			var reserveInnerValue=\"0\";\n";
		$detail_script.="			if(document.form1.price.value.length>0) {\n";
		$detail_script.="				var ReservePer={$_pdata->reserve};\n";
		$detail_script.="				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,\"\"));\n";
		$detail_script.="				if(ReservePriceValue>0) {\n";
		$detail_script.="					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+\"\";\n";
		$detail_script.="					var result = \"\";\n";
		$detail_script.="					for(var i=0; i<reserveInnerValue.length; i++) {\n";
		$detail_script.="						var tmp = reserveInnerValue.length-(i+1);\n";
		$detail_script.="						if(i%3==0 && i!=0) result = \",\" + result;\n";
		$detail_script.="						result = reserveInnerValue.charAt(tmp) + result;\n";
		$detail_script.="					}\n";
		$detail_script.="					reserveInnerValue = result;\n";
		$detail_script.="				}\n";
		$detail_script.="			}\n";
		$detail_script.="			document.getElementById(\"idx_reserve\").innerHTML = reserveInnerValue+\"원\";\n";
		$detail_script.="		}\n";
	}

	$detail_script.="		if(typeof(document.form1.dollarprice)==\"object\") {\n";
	$detail_script.="			try {\n";
	$detail_script.="				document.form1.dollarprice.value = doprice[temp];\n";
	$detail_script.="				document.all[\"idx_dollarprice\"].innerHTML=ardollar[0]+\" \"+document.form1.dollarprice.value+\" \"+ardollar[2];\n";
	$detail_script.="			} catch (e) {}\n";
	$detail_script.="		}\n";
	$detail_script.="	}\n";
	$detail_script.="	packagecal(); //패키지 상품 적용\n";
	$detail_script.="	if(temp2>0 && temp3>0) {\n";
	$detail_script.="		if(num[(temp3-1)*10+(temp2-1)]==0){\n";
	$detail_script.="			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');\n";
	$detail_script.="			if(document.form1.option1.type!=\"hidden\") document.form1.option1.focus();\n";
	$detail_script.="			return;\n";
	$detail_script.="		}\n";
	$detail_script.="	} else {\n";
	$detail_script.="		if(temp2<=0 && document.form1.option1.type!=\"hidden\") document.form1.option1.focus();\n";
	$detail_script.="		else document.form1.option2.focus();\n";
	$detail_script.="		return;\n";
	$detail_script.="	}\n";
	$detail_script.="}\n";

} else if(ord($optcode)) {
	$detail_script.="function chopprice(temp){\n";
	$detail_script.=(ord($dicker))?"return;\n":"";
	$detail_script.="	ind = document.form1.mulopt[temp];\n";
	$detail_script.="	price = ind.options[ind.selectedIndex].value;\n";
	$detail_script.="	originalprice = document.form1.price.value.replace(/,/g, \"\");\n";
	$detail_script.="	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);\n";
	$detail_script.="	if(price.indexOf(',')>0) {\n";
	$detail_script.="		optprice = price.substring(price.indexOf(',')+1);\n";
	$detail_script.="	} else {\n";
	$detail_script.="		optprice=0;\n";
	$detail_script.="	}\n";
	$detail_script.="	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);\n";
	$detail_script.="	if(typeof(document.form1.dollarprice)==\"object\") {\n";
	$detail_script.="		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);\n";
	$detail_script.="		try {\n";
	$detail_script.="			document.all[\"idx_dollarprice\"].innerHTML=ardollar[0]+\" \"+document.form1.dollarprice.value+\" \"+ardollar[2];\n";
	$detail_script.="		} catch (e) {}\n";
	$detail_script.="	}\n";
	$detail_script.="	document.form1.opttype[temp].value=optprice;\n";
	$detail_script.="	var num_str = document.form1.price.value.toString()\n";
	$detail_script.="	var result = '';\n";
	$detail_script.="	for(var i=0; i<num_str.length; i++) {\n";
	$detail_script.="		var tmp = num_str.length-(i+1)\n";
	$detail_script.="		if(i%3==0 && i!=0) result = ',' + result\n";
	$detail_script.="		result = num_str.charAt(tmp) + result\n";
	$detail_script.="	}\n";
	$detail_script.="	document.form1.price.value = result;\n";
	$detail_script.="	document.all[\"idx_price\"].innerHTML=document.form1.price.value+\"원\";\n";
	$detail_script.="	packagecal(); //패키지 상품 적용\n";
	$detail_script.="}\n";
}
if($_pdata->assembleuse=="Y") {
	$detail_script.="function setTotalPrice(tmp) {\n";
	$detail_script.=(ord($dicker))?"return;\n":"";
	$detail_script.="	var i=true;\n";
	$detail_script.="	var j=1;\n";
	$detail_script.="	var totalprice=0;\n";
	$detail_script.="	while(i) {\n";
	$detail_script.="		if(document.getElementById(\"acassemble\"+j)) {\n";
	$detail_script.="			if(document.getElementById(\"acassemble\"+j).value) {\n";
	$detail_script.="				arracassemble = document.getElementById(\"acassemble\"+j).value.split(\"|\");\n";
	$detail_script.="				if(arracassemble[2].length) {\n";
	$detail_script.="					totalprice += arracassemble[2]*1;\n";
	$detail_script.="				}\n";
	$detail_script.="			}\n";
	$detail_script.="		} else {\n";
	$detail_script.="			i=false;\n";
	$detail_script.="		}\n";
	$detail_script.="		j++;\n";
	$detail_script.="	}\n";
	$detail_script.="	totalprice = totalprice*tmp;\n";
	$detail_script.="	var num_str = totalprice.toString();\n";
	$detail_script.="	var result = '';\n";
	$detail_script.="	for(var i=0; i<num_str.length; i++) {\n";
	$detail_script.="		var tmp = num_str.length-(i+1);\n";
	$detail_script.="		if(i%3==0 && i!=0) result = ',' + result;\n";
	$detail_script.="		result = num_str.charAt(tmp) + result;\n";
	$detail_script.="	}\n";
	$detail_script.="	if(typeof(document.form1.price)==\"object\") { document.form1.price.value=totalprice; }\n";
	$detail_script.="	if(typeof(document.form1.dollarprice)==\"object\") {\n";
	$detail_script.="		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);\n";
	$detail_script.="		document.all[\"idx_dollarprice\"].innerHTML=ardollar[0]+\" \"+document.form1.dollarprice.value+\" \"+ardollar[2];\n";
	$detail_script.="	}\n";
	$detail_script.="	if(document.getElementById(\"idx_assembleprice\")) { document.getElementById(\"idx_assembleprice\").value = result; }\n";
	$detail_script.="	if(document.getElementById(\"idx_price\")) { document.getElementById(\"idx_price\").innerHTML = result+\"원\"; }\n";
	$detail_script.="	if(document.getElementById(\"idx_price_graph\")) { document.getElementById(\"idx_price_graph\").innerHTML = result+\"원\"; }\n";
	$detail_script.="}\n";
}

$detail_script.="function packagecal() { \n";
$detail_script.=(count($arrpackage_pricevalue)==0?"return;\n":"");
$detail_script.="	pakageprice = new Array(";
for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { $detail_script.=",";} $detail_script.="'{$arrpackage_pricevalue[$i]}'"; }
$detail_script.=");\n";
$detail_script.="	var result = \"\";\n";
$detail_script.="	var intgetValue = document.form1.price.value.replace(/,/g, \"\");\n";
$detail_script.="	var temppricevalue = \"0\";\n";
$detail_script.="	for(var j=1; j<pakageprice.length; j++) { \n";
$detail_script.="		if(document.getElementById(\"idx_price\"+j)) { \n";
$detail_script.="			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();\n";
$detail_script.="			result=\"\";\n";
$detail_script.="			for(var i=0; i<temppricevalue.length; i++) { \n";
$detail_script.="				var tmp = temppricevalue.length-(i+1);\n";
$detail_script.="				if(i%3==0 && i!=0) result = \",\" + result;\n";
$detail_script.="				result = temppricevalue.charAt(tmp) + result;\n";
$detail_script.="			}\n";
$detail_script.="			document.getElementById(\"idx_price\"+j).innerHTML=result+\"원\";\n";
$detail_script.="		}\n";
$detail_script.="	}\n";
$detail_script.="	if(typeof(document.form1.package_idx)==\"object\") { \n";
$detail_script.="		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);\n";
$detail_script.="		if(packagePriceValue>0) { \n";
$detail_script.="			result = \"\";\n";
$detail_script.="			packagePriceValue = packagePriceValue.toString();\n";
$detail_script.="			for(var i=0; i<packagePriceValue.length; i++) { \n";
$detail_script.="				var tmp = packagePriceValue.length-(i+1);\n";
$detail_script.="				if(i%3==0 && i!=0) result = \",\" + result;\n";
$detail_script.="				result = packagePriceValue.charAt(tmp) + result;\n";
$detail_script.="			}\n";
$detail_script.="			returnValue = result;\n";
$detail_script.="		} else {\n";
$detail_script.="			returnValue = \"0\";\n";
$detail_script.="		}\n";
$detail_script.="		if(document.getElementById(\"idx_price\")) {\n";
$detail_script.="			document.getElementById(\"idx_price\").innerHTML=returnValue+\"원\";\n";
$detail_script.="		}\n";
$detail_script.="		if(document.getElementById(\"idx_price_graph\")) {\n";
$detail_script.="			document.getElementById(\"idx_price_graph\").innerHTML=returnValue+\"원\";\n";
$detail_script.="		}\n";
$detail_script.="		if(typeof(document.form1.dollarprice)==\"object\") {\n";
$detail_script.="			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;\n";
$detail_script.="			if(document.getElementById(\"idx_dollarprice\")) {\n";
$detail_script.="				document.getElementById(\"idx_dollarprice\").innerHTML=ardollar[0]+\" \"+document.form1.dollarprice.value+\" \"+ardollar[2];\n";
$detail_script.="			}\n";
$detail_script.="		}\n";
$detail_script.="	}\n";
$detail_script.="}\n";
$detail_script.="</script>\n";
echo $detail_script;

if(ord($dicker)==0) {
	if(ord($_pdata->quantity) && $_pdata->quantity<=0) {
		$baro="\"javascript:alert('품절된 상품입니다.')\"";
		$basketin="\"javascript:alert('품절된 상품입니다.')\"";
	} else {
		$baro="\"javascript:CheckForm('ordernow','{$opti}')\" onmouseover=\"window.status='바로구매';return true;\" onmouseout=\"window.status='';return true;\"";
		$basketin="\"javascript:CheckForm('','{$opti}')\" onmouseover=\"window.status='장바구니 담기';return true;\" onmouseout=\"window.status='';return true;\"";
		
		#비즈 스프링용 템프릿 변수 추가
		if($biz[bizNumber]){
			$basket_in_biz="onMouseDown=\"eval('try{ _trk_clickTrace( \'SCI\', \'".$_pdata->productname."\' ); }catch(_e){ }');\"";
		}else{
			$basket_in_biz="";
		}
	}
	if (strlen($_ShopInfo->getMemid())>0) {
		$wishin="\"javascript:CheckForm('wishlist','{$opti}')\" onmouseover=\"window.status='WishList담기';return true;\" onmouseout=\"window.status='';return true;\"";
	} else {
		$wishin="\"javascript:check_login()\" onmouseover=\"window.status='WishList담기';return true;\" onmouseout=\"window.status='';return true;\"";
	}
} else {
	$baro="\"javascript:alert('구매가 불가능한 상품입니다.')\" onmouseover=\"window.status='바로구매';return true;\" onmouseout=\"window.status='';return true;\"";
	$basketin="\"javascript:alert('구매가 불가능한 상품입니다.')\" onmouseover=\"window.status='장바구니 담기';return true;\" onmouseout=\"window.status='';return true;\"";
	$wishin="\"javascript:alert('구매가 불가능한 상품입니다.')\" onmouseover=\"window.status='WishList담기';return true;\" onmouseout=\"window.status='';return true;\"";
}

if($package_count>0) { //패키지 상품 출력
	$packagetable ="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$packagetable.="<tr>\n";
	$packagetable.="	<td bgcolor=\"#FFFFFF\" style=\"border:1px #EDEDED solid;\">\n";
	$packagetable.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$packagetable.="	<col width=\"130\"></col>\n";
	$packagetable.="	<col width=\"\"></col>\n";
	$packagecoll=5;
	for($j=1; $j<count($arrpackage_title); $j++) {
		$arrpackage_list_exp = explode(",", $arrpackage_list[$j]);
		$packagetable.="	<tr>\n";
		$packagetable.="		<td align=\"center\" bgcolor=\"#F8F8F8\" style=\"padding:5px;border-right:1px #EDEDED solid;border-bottom:1px #EDEDED solid;\">\n";
		$packagetable.="		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$packagetable.="		<tr>\n";
		$packagetable.="			<td align=\"center\"><b>{$arrpackage_title[$j]}</b></td>\n";
		$packagetable.="		</tr>\n";
		$packagetable.="		<tr>\n";
		$packagetable.="			<td align=\"center\" style=\"padding:3px;\">".(ord($dicker)?$dicker:"<img src=\"{$Dir}images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b><FONT color=\"#F02800\" id=\"idx_price{$j}\">".number_format($SellpriceValue+$arrpackage_pricevalue[$j])."원</font></b>")."</td>\n";
		$packagetable.="		</tr>\n";
		$packagetable.="		</table>\n";
		$packagetable.="		</td>\n";
		$packagetable.="		<td style=\"border-bottom:1px #EDEDED solid;\">\n";
		$packagetable.="		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=100%>\n";
		$packagetable.="		<tr>\n";
		$packagetable.="			<td width=100% style=\"padding:5\">\n";
		$packagetable.="			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$packagetable.="			<tr>\n";
		$packagetable.="				<td width=\"".ceil(100/$packagecoll)."%\" valign=\"top\" align=\"center\" style=\"padding:5px;\">\n";
		$packagetable.="				<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"90\">\n";
		$packagetable.="				<tr>\n";
		$packagetable.="					<td align=\"center\" valign=middle style=\"border:1px #EAEAEA solid;padding:10px;\" bgcolor=\"#EDEDED\">\n";
		if (ord($_pdata->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->tinyimage)) {
			$packagetable.="<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($_pdata->tinyimage)."\" border=\"0\" ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$_pdata->tinyimage);
			if($width[0]>$width[1]) $packagetable.="width=\"70\"> ";
			else $packagetable.="height=\"70\">";
		} else {
			$packagetable.="<img src=\"{$Dir}images/no_img.gif\" width=\"70\" border=\"0\">";
		}
		$packagetable.="</td>\n";
		$packagetable.="				</tr>\n";
		$packagetable.="				<tr>\n";
		$packagetable.="					<td height=\"3\"></td>\n";
		$packagetable.="				</tr>\n";
		$packagetable.="				<tr>\n";
		$packagetable.="					<td align=\"center\" style=\"word-break:break-all;padding:10px;padding-top:0px;color:#BEBEBE;\"><b>기본상품</b></td>\n";
		$packagetable.="				</tr>\n";
		$packagetable.="				</table>\n";
		$packagetable.="				</td>\n";
		for($ttt=1; $ttt<count($arrpackage_list_exp); $ttt++) {
			if(ord($arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]])) {
				$packagetable.="				".($ttt%$packagecoll==0?"</tr><tr>":"")."\n";
				$packagetable.="				<td width=\"".ceil(100/$packagecoll)."%\" valign=\"top\" align=\"center\" style=\"padding:5px;\">\n";
				$packagetable.="				<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"90\">\n";
				$packagetable.="				<tr>\n";
				$packagetable.="					<td valign=\"top\">\n";
				$packagetable.="					<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" id=\"P{$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}\" onmouseover=\"quickfun_show(this,'P{$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}','')\" onmouseout=\"quickfun_show(this,'P{$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}','none')\">\n";
				$packagetable.="					<tr>\n";
				$packagetable.="						<td align=\"center\" valign=middle style=\"border:1px #EAEAEA solid;padding:10px;\" bgcolor=\"#EDEDED\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode={$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">\n";
				if (ord($arrpackage_proinfo['tinyimage'][$arrpackage_list_exp[$ttt]]) && file_exists($Dir.DataDir."shopimages/product/".$arrpackage_proinfo['tinyimage'][$arrpackage_list_exp[$ttt]])) {
					$packagetable.="<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($arrpackage_proinfo['tinyimage'][$arrpackage_list_exp[$ttt]])."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$arrpackage_proinfo['tinyimage'][$arrpackage_list_exp[$ttt]]);
					if($width[0]>$width[1]) $packagetable.="width=\"70\"> ";
					else $packagetable.="height=\"70\">";
				} else {
					$packagetable.="<img src=\"{$Dir}images/no_img.gif\" width=\"70\" border=\"0\" align=\"center\">";
				}
				$packagetable.="</A></td>\n";
				$packagetable.="					</tr>\n";
				$packagetable.="					<tr>\n";
				$packagetable.="						<td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('{$Dir}','P','{$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}','".($arrpackage_proinfo['quantity'][$arrpackage_list_exp[$ttt]]=="0"?"":"1")."')</script>":"")."</td>\n";
				$packagetable.="					</tr>\n";
				$packagetable.="					<tr>\n";
				$packagetable.="						<td align=\"center\" style=\"word-break:break-all;padding:10px;padding-top:0px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode={$arrpackage_proinfo['productcode'][$arrpackage_list_exp[$ttt]]}\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($arrpackage_proinfo['productname'][$arrpackage_list_exp[$ttt]],$arrpackage_proinfo['etctype'][$arrpackage_list_exp[$ttt]],"")."</FONT></A></td>\n";
				$packagetable.="					</tr>\n";
				$packagetable.="					</table>\n";
				$packagetable.="					</td>\n";
				$packagetable.="				</tr>\n";
				$packagetable.="				</table>\n";
				$packagetable.="				</td>\n";
			}
		}

		if($ttt<$packagecoll) {
			$empty_count = $packagecoll-$ttt;
			for($ttt=0; $ttt<$empty_count; $ttt++) {
				$packagetable.="				<td width=\"".ceil(100/$packagecoll)."%\"></td>\n";

			}
		}

		$packagetable.="			</tr>\n";
		$packagetable.="			</table>\n";
		$packagetable.="			</td>\n";
		$packagetable.="		</tr>\n";
		$packagetable.="		</table>\n";
		$packagetable.="		</td>\n";
		$packagetable.="	</tr>\n";
	}
	$packagetable.="	</table>\n";
	$packagetable.="	</td>\n";
	$packagetable.="</tr>\n";
	$packagetable.="</table>\n";
} //패키지 상품 출력 끝

if($_pdata->assembleuse=="Y" && count($_adata)>0) {
	$assembletable = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$assembletable.= "<tr>\n";
	$assembletable.= "	<td>\n";

	$assemble_type_exp = explode("",$_adata->assemble_type);
	$assemble_title_exp = explode("",$_adata->assemble_title);
	$assemble_pridx_exp = explode("",$_adata->assemble_pridx);
	$assemble_list_exp = explode("",$_adata->assemble_list);

	if(count($assemble_type_exp)>0) {
		$assembletable.= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$assembletable.= "	<input type=hidden name=assemble_type value=\"".implode("|",$assemble_type_exp)."\">\n";
		$assembletable.= "	<input type=hidden name=assemble_list value=\"\">\n";
		$assembletable.= "	<input type=hidden name=assembleuse value=\"Y\">\n";
		$assembletable.= "	<col width=\"60\"></col>\n";
		$assembletable.= "	<col width=\"\"></col>\n";
		for($j=1; $j<count($assemble_type_exp); $j++) {
			$assemble_list_pexp = explode(",",$assemble_list_exp[$j]);
			$assembletable.= "	<tr>\n";
			$assembletable.= "		<td valign=\"bottom\" style=\"padding:5px;\">";
			if(ord($assemble_pridx_exp[$j]) && (ord($_acdata[$assemble_pridx_exp[$j]]->quantity)==0 || $_acdata[$assemble_pridx_exp[$j]]->quantity>=$miniq)) {
				if(ord($_acdata[$assemble_pridx_exp[$j]]->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$_acdata[$assemble_pridx_exp[$j]]->tinyimage)) {
					$assembletable.= "<a href=\"javascript:assemble_proinfo('{$j}');\"><img src=\"".$Dir.DataDir."shopimages/product/{$_acdata[$assemble_pridx_exp[$j]]->tinyimage}\" border=\"0\" id=\"acimage{$j}\" width=\"50\" height=\"40\"></a>\n";
				} else {
					$assembletable.= "<a href=\"javascript:assemble_proinfo('{$j}');\"><img src=\"{$Dir}images/acimage.gif\" border=\"0\" id=\"acimage{$j}\" width=\"50\" height=\"40\"></a>\n";
				}
				$assemble_state = "M";
			} else {
				$assembletable.= "<a href=\"javascript:assemble_proinfo('{$j}');\"><img src=\"{$Dir}images/acimage.gif\" border=\"0\" id=\"acimage{$j}\" width=\"50\" height=\"40\"></a>\n";
				$assemble_state = "A";
			}
			$assembletable.= "</td>\n";
			$assembletable.= "		<td valign=\"bottom\" style=\"padding:5px;\">\n";
			$assembletable.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$assembletable.= "		<tr>\n";
			$assembletable.= "			<td colspan=\"2\"><span style=\"font-size:12px;\"><b>{$assemble_title_exp[$j]}</b></font></td>\n";
			$assembletable.= "		</tr>\n";
			$assembletable.= "		<tr>\n";
			$assembletable.= "			<td width=\"100%\"><select name=\"acassembleselect[]\" id=\"acassemble{$j}\" onchange=\"setAssenbleChange(this,'{$j}');\" onclick=\"setCurrentSelect(this.selectedIndex);\" style=\"font-size:12px;letter-spacing:-0.5pt;width:100%;\">\n";
			$assembletable.= "			<option value=\"\">".($assemble_type_exp[$j]=="Y"?"&nbsp;&nbsp;&nbsp;━━━━━━━━━━━━━━━━&nbsp;[필수항목] 선택해 주세요&nbsp;━━━━━━━━━━━━━━━━━&nbsp;&nbsp;":"&nbsp;&nbsp;&nbsp;━━━━━━━━━━━━━━━━━━━&nbsp;선택해 주세요&nbsp;&nbsp;━━━━━━━━━━━━━━━━━━━ ")."</option>\n";
			for($k=1; $k<count($assemble_list_pexp); $k++) {
				if(ord($_acdata[$assemble_list_pexp[$k]]->pridx) && (ord($_acdata[$assemble_list_pexp[$k]]->quantity)==0 || $_acdata[$assemble_list_pexp[$k]]->quantity>0)) {
					if($_acdata[$assemble_list_pexp[$k]]->pridx==$_acdata[$assemble_pridx_exp[$j]]->pridx) {
						$assembletable.= "<option value=\"{$_acdata[$assemble_list_pexp[$k]]->productcode}|{$_acdata[$assemble_list_pexp[$k]]->quantity}|{$_acdata[$assemble_list_pexp[$k]]->sellprice}|G|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" selected style=\"color:#FF00FF;\">{$_acdata[$assemble_list_pexp[$k]]->productname} / 기본선택</option>\n";
					} else {
						$minus_price = 0;
						$minus_price = $_acdata[$assemble_list_pexp[$k]]->sellprice - $_acdata[$assemble_pridx_exp[$j]]->sellprice;
						if($minus_price>0) {
							$assembletable.= "<option value=\"{$_acdata[$assemble_list_pexp[$k]]->productcode}|{$_acdata[$assemble_list_pexp[$k]]->quantity}|{$_acdata[$assemble_list_pexp[$k]]->sellprice}|{$assemble_state}|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" style=\"color:#FF4C00;\">".$_acdata[$assemble_list_pexp[$k]]->productname.($minus_price>0?" / +".number_format($minus_price):" / ".number_format($minus_price))."</option>\n";
						} else if($minus_price>0) {
							$assembletable.= "<option value=\"{$_acdata[$assemble_list_pexp[$k]]->productcode}|{$_acdata[$assemble_list_pexp[$k]]->quantity}|{$_acdata[$assemble_list_pexp[$k]]->sellprice}|{$assemble_state}|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" style=\"color:#FF00FF;\">".$_acdata[$assemble_list_pexp[$k]]->productname.($minus_price>0?" / +".number_format($minus_price):" / ".number_format($minus_price))."</option>\n";
						} else {
							$assembletable.= "<option value=\"{$_acdata[$assemble_list_pexp[$k]]->productcode}|{$_acdata[$assemble_list_pexp[$k]]->quantity}|{$_acdata[$assemble_list_pexp[$k]]->sellprice}|{$assemble_state}|".htmlspecialchars($_acdata[$assemble_list_pexp[$k]]->tinyimage)."\" style=\"color:#003399;\">".$_acdata[$assemble_list_pexp[$k]]->productname.($minus_price>0?" / +".number_format($minus_price):" / ".number_format($minus_price))."</option>\n";
						}
					}
				}
			}
			$assembletable.= "			</select></td>\n";
			$assembletable.= "		</tr>\n";
			$assembletable.= "		</table>\n";
			$assembletable.= "		</td>\n";
			$assembletable.= "	</tr>\n";
		}

		$assembletable.= "	</table>\n";
		$assembletable.= "	</td>\n";
		$assembletable.= "</tr>\n";
		$assembletable.= "<tr>\n";
		$assembletable.= "	<td style=\"padding-top:20px;padding-left:5px;padding-right:5px;padding-bottom:10px;\"><TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0><tr><td height=\"1\" bgcolor=\"#DADADA\"></td></tr></table></td>\n";
		$assembletable.= "</tr>\n";
		$assembletable.= "<tr>\n";
		$assembletable.= "	<td style=\"padding:5px;\">\n";
		$assembletable.= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$assembletable.= "	<tr>\n";
		$assembletable.= "		<td align=\"center\" bgcolor=\"#FFFFFF\" style=\"padding:10px;border:1px #DADADA solid;\">\n";
		$assembletable.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$assembletable.= "		<tr>\n";
		$assembletable.= "			<td>\n";
		$assembletable.= "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$assembletable.= "			<tr>\n";
		$assembletable.= "				<td><span style=\"font-size:16px;color:#000000;line-height:18px;\"><b>구매수량&nbsp;:&nbsp;</b></span></td>\n";
		$assembletable.= "				<td>\n";
		$assembletable.= "				<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$assembletable.= "				<tr>\n";
		$assembletable.= "					<td><input type=text name=\"assemblequantity\" value=\"".($miniq>1?$miniq:"1")."\" size=\"4\" style=\"height:24px;text-align:center;font-weight:bold;font-size:14px;BORDER:#DFDFDF 1px solid;BACKGROUND-COLOR:#FFFFFF;padding-top:4pt;padding-bottom:1pt;\" readonly></td>\n";
		$assembletable.= "					<td style=\"padding-left:4px;padding-right:4px;\">\n";
		$assembletable.= "					<table cellpadding=\"0\" cellspacing=\"0\">\n";
		$assembletable.= "					<tr>\n";
		$assembletable.= "						<td valign=\"top\" style=\"padding-bottom:1px;\"><a href=\"javascript:change_quantity('up')\"><img src=\"{$Dir}images/assemble_neroup2.gif\" border=\"0\"></a></td>\n";
		$assembletable.= "					</tr>\n";
		$assembletable.= "					<tr>\n";
		$assembletable.= "						<td valign=\"bottom\" style=\"padding-top:1px;\"><a href=\"javascript:change_quantity('dn')\"><img src=\"{$Dir}images/assemble_nerodown2.gif\" border=\"0\"></a></td>\n";
		$assembletable.= "					</tr>\n";
		$assembletable.= "					</table>\n";
		$assembletable.= "					</td>\n";
		$assembletable.= "				</tr>\n";
		$assembletable.= "				</table>\n";
		$assembletable.= "				</td>\n";
		$assembletable.= "			</tr>\n";
		$assembletable.= "			</table>\n";
		$assembletable.= "			</td>\n";
		if(ord($dicker)==0) {
			$assembletable.= "			<td style=\"padding-left:20px;\">\n";
			$assembletable.= "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$assembletable.= "			<tr>\n";
			$assembletable.= "				<td><span style=\"font-size:16px;color:#000000;line-height:18px;\"><b>합계금액&nbsp;:&nbsp;</b></span></td>\n";
			$assembletable.= "				<td>\n";
			$assembletable.= "				<table cellpadding=\"0\" cellspacing=\"0\">\n";
			$assembletable.= "				<tr>\n";
			$assembletable.= "					<td><input type=text name=\"assembleprice\" id=\"idx_assembleprice\" value=\"".number_format($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)."\" size=\"12\" style=\"height:24px;text-align:right;font-weight:bold;font-size:14px;BORDER:#DFDFDF 1px solid;BACKGROUND-COLOR:#FFFFFF;padding-top:4pt;padding-bottom:1pt;padding-right:2pt;\" readonly></td>\n";
			$assembletable.= "					<td style=\"padding-left:20px;\"><a href=\"javascript:CheckForm('','')\" onMouseOver=\"window.status='장바구니담기';return true;\"><IMG SRC=\"{$Dir}images/assemble_basket.gif\" hspace=\"3\" border=\"0\" align=middle></a></td>\n";
			$assembletable.= "				</tr>\n";
			$assembletable.= "				</table>\n";
			$assembletable.= "				</td>\n";
			$assembletable.= "			</tr>\n";
			$assembletable.= "			</table>\n";
			$assembletable.= "			</td>\n";
		 }
		$assembletable.= "		</tr>\n";
		$assembletable.= "		</table>\n";
		$assembletable.= "		</td>\n";
		$assembletable.= "	</tr>\n";
		$assembletable.= "	</table>\n";
		$assembletable.= "	</td>\n";
		$assembletable.= "</tr>\n";
		$assembletable.= "</table>\n";
	}
}

//태그목록 관련
$taglist="";
$tagreginput="";
$tagregok="";

$taglist.="<div id=\"prtaglist\">\n";
$arrtaglist=explode(",",$_pdata->tag);
$jj=0;
for($i=0;$i<count($arrtaglist);$i++) {
	$arrtaglist[$i]=preg_replace("/<|>/","",$arrtaglist[$i]);
	if(ord($arrtaglist[$i])) {
		if($jj>0) $taglist.=",&nbsp;&nbsp;";
		$taglist.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$i])."\" onmouseover=\"window.status='{$arrtaglist[$i]}';return true;\" onmouseout=\"window.status='';return true;\">{$arrtaglist[$i]}</a>";
		$jj++;
	}
}
$taglist.="</div>\n";

if($num=strpos($body,"[TAGREGINPUT_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$tagreginput_style=$s_tmp[1];
}
if(ord($tagreginput_style)==0) $tagreginput_style="width:160px";
$tagreginput = "<input type=\"text\" name=\"searchtagname\" maxlength=\"50\" style=\"{$tagreginput_style}\" autocomplete=\"off\" onkeyup=\"check_tagvalidate(event, this);\">";
$tagregok="\"javascript:void(0)\" onclick=\"tagCheck('{$productcode}')\"";
