<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$setup['page_num'] = 10;
$setup['list_num'] = 10;

$type=$_REQUEST["type"];
$artid=$_REQUEST["artid"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup['page_num'] + $gotopage;
} else {
	$nowblock = 0;
}

if (empty($gotopage)) {
	$gotopage = 1;
}

if($type!="list" && $type!="view" && $type!="write" && $type!="delete") $type="list";

if($type=="view" || ($type=="write" && strlen($artid)>0) || $type=="delete") {
	$sql = "SELECT * FROM tblvendernotice WHERE vender='".$_VenderInfo->getVidx()."' AND date='".$artid."' ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$noticedata=pmysql_fetch_object($result)) {
		echo "<html></head><body onload=\"alert('해당 공지사항이 존재하지 않습니다.')\"></body></html>";exit;
	}
	pmysql_free_result($result);
	
	if($type=="view") {
		//이전글
		$prevdata=NULL;
		$sql = "SELECT date,subject FROM tblvendernotice WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND date>'".$artid."' ORDER BY date ASC LIMIT 1 ";
		$result=pmysql_query($sql,get_db_conn());
		$prevdata=pmysql_fetch_object($result);
		pmysql_free_result($result);

		//다음글
		$nextdata=NULL;
		$sql = "SELECT date,subject FROM tblvendernotice WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND date<'".$artid."' ORDER BY date DESC LIMIT 1 ";
		$result=pmysql_query($sql,get_db_conn());
		$nextdata=pmysql_fetch_object($result);
		pmysql_free_result($result);
	}
}

if($type=="write") {
	$mode=$_POST["mode"];
	$subject=$_POST["subject"];
	$content=$_POST["content"];
	if($mode=="insert") {
		if(strlen($subject)>0 && strlen($content)>0) {
			$sql = "INSERT INTO tblvendernotice(
			vender		,
			date		,
			ip			,
			subject		,
			content		) VALUES (
			'".$_VenderInfo->getVidx()."', 
			'".date("YmdHis")."', 
			'".$_SERVER['REMOTE_ADDR']."', 
			'".$subject."', 
			'".$content."')";
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.href='".$_SERVER['PHP_SELF']."'\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
			}
		}
	} else if($mode=="modify") {
		if(strlen($artid)>0 && strlen($subject)>0 && strlen($content)>0) {
			$sql = "UPDATE tblvendernotice SET ";
			$sql.= "subject		= '".$subject."', ";
			$sql.= "content		= '".$content."' ";
			$sql.= "WHERE vender = '".$_VenderInfo->getVidx()."' ";
			$sql.= "AND date = '".$artid."' ";
			
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.href='".$_SERVER['PHP_SELF']."?type=view&artid=".$artid."&block=".$block."&gotopage=".$gotopage."'\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
			}
		}
	}
} else if($type=="delete") {
	if(strlen($artid)>0) {
		$sql = "DELETE FROM tblvendernotice ";
		$sql.= "WHERE vender = '".$_VenderInfo->getVidx()."' ";
		$sql.= "AND date = '".$artid."' ";
		
		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.href='".$_SERVER['PHP_SELF']."?block=".$block."&gotopage=".$gotopage."'\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER['PHP_SELF']?>?block="+block+"&gotopage="+gotopage;
}
function GoNoticeList(block,gotopage) {
	url="<?=$_SERVER['PHP_SELF']?>?block="+block+"&gotopage="+gotopage;
	document.location.href=url;
}
function GoNoticeView(artid,block,gotopage) {
	url="<?=$_SERVER['PHP_SELF']?>?type=view&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}
function GoWrite() {
	document.location.href="<?=$_SERVER['PHP_SELF']?>?type=write";
}
function GoModify(artid,block,gotopage) {
	url="<?=$_SERVER['PHP_SELF']?>?type=write&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}
function GoDelete(artid,block,gotopage) {
	if(confirm("해당 글을 정말 삭제하겠습니까?")) {
		url="<?=$_SERVER['PHP_SELF']?>?type=delete&artid="+artid;
		if(typeof block!="undefined") url+="&block="+block;
		if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
		document.location.href=url;
	}
}
function formSubmit() {
	if(document.form1.subject.value.length==0) {
		alert("공지사항 제목을 입력하세요.");
		document.form1.subject.focus();
		return;
	}
	if(document.form1.content.value.length==0) {
		alert("공지사항 내용을 입력하세요.");
		document.form1.content.focus();
		return;
	}
	if(confirm("미니샵 공지사항을 적용하시겠습니까?")) {
		document.form1.mode.value="<?=(strlen($artid)>0?"modify":"insert")?>";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>미니샵 공지사항</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>미니샵 공지사항</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 공지사항으로 등록된 내용은 미니샵 페이지에 출력됩니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<?if($type=="list"){?>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=10%></col>
				<col width=></col>
				<col width=14%></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>번호</B></td>
					<td><B>제목</B></td>
					<td><B>게시일</B></td>
				</tr>
<?
				$sql = "SELECT COUNT(*) as t_count FROM tblvendernotice ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$result = pmysql_query($sql,get_db_conn());
				$row = pmysql_fetch_object($result);
				$t_count = $row->t_count;
				pmysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup['list_num']) + 1;

				$sql = "SELECT date,subject,access FROM tblvendernotice ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "ORDER BY date DESC ";
				$sql.= "LIMIT " . $setup['list_num']." OFFSET ".($setup['list_num'] * ($gotopage - 1));
				$result=pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
					$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					echo "<tr height=28 bgcolor=#FFFFFF>\n";
					echo "	<td align=center>".$number."</td>\n";
					echo "	<td style=\"padding:7,10\"><A HREF=\"javascript:GoNoticeView('".$row->date."','".$block."','".$gotopage."')\">".strip_tags($row->subject)."</A></td>\n";
					echo "	<td align=center>".$date."</td>\n";
					echo "</tr>\n";
					$i++;
				}
				pmysql_free_result($result);
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=3 align=center>등록된 공지사항이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$total_block = intval($pagecount / $setup['page_num']);
					if (($pagecount % $setup['page_num']) > 0) {
						$total_block = $total_block + 1;
					}
					$total_block = $total_block - 1;
					if (ceil($t_count/$setup['list_num']) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
							$prev_page_exists = true;
						}
						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup['page_num']*($block-1)+$setup['page_num']).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup['page_num']." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

							$a_prev_page = $a_first_block.$a_prev_page;
						}
						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup['page_num']; $gopage++) {
								if ((intval($nowblock*$setup['page_num']) + $gopage) == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup['page_num']) + $gopage)."</B></font> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup['page_num']) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock*$setup['page_num']) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup['page_num']) == 0) {
								$lastpage = $setup['page_num'];
							} else {
								$lastpage = $pagecount % $setup['page_num'];
							}
							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup['page_num']) + $gopage == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup['page_num']) + $gopage)."</B></FONT> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup['page_num']) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup['page_num']) + $gopage)."';return true\">[".(intval($nowblock*$setup['page_num']) + $gopage)."]</a> ";
								}
							}
						}
						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup['list_num']*$setup['page_num'])) - 1;
							$last_gotopage = ceil($t_count/$setup['list_num']);
							$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
							$next_page_exists = true;
						}
						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup['page_num']*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup['page_num']." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>1</B>";
					}
					$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				}
?>
				</table>
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td width="100%" align=center style="padding-top:10"><?=$pageing?></td>
					<td style="padding-top:3"><A HREF="javascript:GoWrite()"><img src="images/btn_noticewrite.gif" border=0></A></td>
				</tr>
				</table>

				<?}else if($type=="view"){?>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td valign=top style=background-repeat:repeat-x bgcolor="e7e7e7">
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td bgcolor=F5F5F5>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style="background-repeat:repeat-y;background-position:right;padding:9;word-break:break-all;" width="88%">
							<B>제 목 : <?=$noticedata->subject?></B>
							</td>
							<td align="left"><?=substr($noticedata->date,0,4)."/".substr($noticedata->date,4,2)."/".substr($noticedata->date,6,2)?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr>
					<td valign="top" height="250" bgcolor=ffffff style="background-repeat:repeat-y;background-position:right;padding:9;word-break:break-all;">
					<?=nl2br($noticedata->content)?>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr><td height=5></td></tr>
				<tr>
					<td align=right>
					<A HREF="javascript:GoModify('<?=$noticedata->date?>','<?=$block?>','<?=$gotopage?>');"><img src="images/btn_noticeedit.gif" border=0></A>
					<A HREF="javascript:GoDelete('<?=$noticedata->date?>','<?=$block?>','<?=$gotopage?>');"><img src="images/btn_noticedelete.gif" border=0></A>
					<A HREF="javascript:GoWrite()"><img src="images/btn_noticewrite.gif" border=0></A>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr>
					<td align=center>
					<?php if(is_object($prevdata)){?>
					<A HREF="javascript:GoNoticeView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_prev01.gif" border=0></A>&nbsp;
					<?}?>
					<A HREF="javascript:GoNoticeList('<?=$block?>','<?=$gotopage?>')"><img src="images/btn_list.gif" border=0></A>
					<?php if(is_object($nextdata)){?>
					&nbsp;<A HREF="javascript:GoNoticeView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_next01.gif" border=0></A>
					<?}?>
					</td>
				</tr>
				
				<?if(is_object($prevdata) || is_object($nextdata)){?>

				<tr><td height=25></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr><td height=1 bgcolor=red></td></tr>
					</table>

					<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
					<col width=10%></col>
					<col width=></col>
					<col width=14%></col>
					<tr height=28 align=center bgcolor=F5F5F5>
						<td><B>번호</B></td>
						<td><B>제목</B></td>
						<td><B>게시일</B></td>
					</tr>
					<?if(is_object($prevdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>이전글</td>
						<td style="padding:7,10;word-break:break-all;"><A HREF="javascript:GoNoticeView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($prevdata->subject)?></A></td>
						<td align=center><?=substr($prevdata->date,0,4)."/".substr($prevdata->date,4,2)."/".substr($prevdata->date,6,2)?></td>
					</tr>
					<?}?>
					<?if(is_object($nextdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>다음글</td>
						<td style="padding:7,10;word-break:break-all;"><A HREF="javascript:GoNoticeView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($nextdata->subject)?></A></td>
						<td align=center><?=substr($nextdata->date,0,4)."/".substr($nextdata->date,4,2)."/".substr($nextdata->date,6,2)?></td>
					</tr>
					<?}?>
					</table>
					</td>
				</tr>

				<?}?>

				</table>

				<?}else if($type=="write"){?>

				<table width=100% border=0 cellspacing=0 cellpadding=0>
				<col width="80"></col>
				<col width=""></col>
				<form name=form1 method=post>
				<input type=hidden name=type value="<?=$type?>">
				<input type=hidden name=mode>
				<?=(strlen($noticedata->date)>0?"<input type=hidden name=artid value=\"".$noticedata->date."\">\n":"")?>
				<?=(strlen($block)>0?"<input type=hidden name=block value=\"".$block."\">\n":"")?>
				<?=(strlen($gotopage)>0?"<input type=hidden name=gotopage value=\"".$gotopage."\">\n":"")?>
				<tr> 
					<td align="center" bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>제 목</B></td>
					<td style=padding:7,10 bgcolor="#FFFFFF">
					<input type=text name="subject" value="<?=$noticedata->subject?>" size="60" maxlength=40 required>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr> 
					<td align="center" bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>내 용</B></td>
					<td style=padding:7,10 bgcolor="#FFFFFF">
					<textarea name="content" rows=10 cols="" style="width:100%;word-break:break-all;" maxbyte=10000 required><?=$noticedata->content?></textarea>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr><td colspan=2 height=25></td></tr>
				<tr>
					<td colspan=2 align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_regist05.gif" border=0></A>
					&nbsp;&nbsp;
					<A HREF="javascript:history.go(-1);"><img src="images/btn_cancel05.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>
				<iframe name="processFrame" src="about:blank;" width="0" height="0" scrolling=no frameborder=no></iframe>
				<?}?>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
<?=$onload?>
<? include("copyright.php"); ?>
