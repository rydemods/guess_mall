<?php
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/venderlib.php");

include("access.php");

$type=$_REQUEST["type"];
$artid=$_REQUEST["artid"];

if($type!="list" && $type!="view" && $type!="write") $type="list";

if($type=="view") {
	$sql = "SELECT * FROM tblvenderadminqna WHERE vender='".$_VenderInfo->getVidx()."' AND date='".$artid."' ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$qnadata=pmysql_fetch_object($result)) {
		echo "<html></head><body onload=\"alert('해당 상담 게시글이 존재하지 않습니다.')\"></body></html>";exit;
	}
	pmysql_free_result($result);

	$sql = "UPDATE tblvenderadminqna SET access=access+1 WHERE vender='".$_VenderInfo->getVidx()."' AND date='".$artid."' ";
	pmysql_query($sql,get_db_conn());

	//이전글
	$sql = "SELECT date,subject FROM tblvenderadminqna WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND date>'".$artid."' ORDER BY date ASC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	$prevdata=pmysql_fetch_object($result);
	pmysql_free_result($result);

	//다음글
	$sql = "SELECT date,subject FROM tblvenderadminqna WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND date<'".$artid."' ORDER BY date DESC LIMIT 1 ";
	$result=pmysql_query($sql,get_db_conn());
	$nextdata=pmysql_fetch_object($result);
	pmysql_free_result($result);
}

if($type=="write") {
	$mode=$_POST["mode"];
	$subject=$_POST["subject"];
	$content=$_POST["content"];
	if($mode=="insert") {
		if(strlen($subject)>0 && strlen($content)>0) {
			$sql = "INSERT INTO tblvenderadminqna(
			vender		,
			date		,
			subject		,
			content		) VALUES (
			".$_VenderInfo->getVidx()."', 
			".date("YmdHis")."', 
			".$subject."', 
			".$content."')";
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.href='".$_SERVER[PHP_SELF]."'\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
			}
		}
	}
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER[PHP_SELF]?>?block="+block+"&gotopage="+gotopage;
}
function GoCounselList(block,gotopage) {
	url="<?=$_SERVER[PHP_SELF]?>?block="+block+"&gotopage="+gotopage;
	document.location.href=url;
}
function GoCounselView(artid,block,gotopage) {
	url="<?=$_SERVER[PHP_SELF]?>?type=view&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}
function GoWrite() {
	document.location.href="<?=$_SERVER[PHP_SELF]?>?type=write";
}
function formSubmit() {
	if(document.form1.subject.value.length==0) {
		alert("문의 제목을 입력하세요.");
		document.form1.subject.focus();
		return;
	}
	if(document.form1.content.value.length==0) {
		alert("문의 내용을 입력하세요.");
		document.form1.content.focus();
		return;
	}
	if(confirm("상담게시판에 등록하시겠습니까?")) {
		document.form1.mode.value="insert";
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
					<FONT COLOR="#ffffff"><B>본사 상담게시판</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>본사 상담게시판</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 상담게시판은 본사와 입점사간에 1:1게시판 입니다.</td>
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
				<tr>
					<td style="padding-bottom:3"><A HREF="javascript:GoWrite()"><img src="images/btn_qnawrite.gif" border=0></A></td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<?if($type=="list"){?>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=60></col>
				<col width=></col>
				<col width=100></col>
				<col width=80></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>번호</B></td>
					<td><B>제목</B></td>
					<td><B>문의날짜</B></td>
					<td><B>답변여부</B></td>
				</tr>
<?
				$sql = "SELECT COUNT(*) as t_count FROM tblvenderadminqna ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT date,subject,access,re_date FROM tblvenderadminqna ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					$re_icn="";
					if(strlen($row->re_date)==14) {
						$re_icn="<img src=images/icn_counsel_ok.gif border=0>";
					} else {
						$re_icn="<img src=images/icn_counsel_no.gif border=0>";
					}
					echo "<tr height=28 bgcolor=#FFFFFF>\n";
					echo "	<td align=center>".$number."</td>\n";
					echo "	<td style=\"padding:7,10\"><A HREF=\"javascript:GoCounselView('".$row->date."','".$block."','".$gotopage."')\">".strip_tags($row->subject)."</A></td>\n";
					echo "	<td align=center>".$date."</td>\n";
					echo "	<td align=center>".$re_icn."</td>\n";
					echo "</tr>\n";
					$i++;
				}
				pmysql_free_result($result);
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=4 align=center>등록된 게시글이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				}
?>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
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
							<td style=background-repeat:repeat-y;background-position:right;padding:9 width="88%">
							<B>제 목 : <?=$qnadata->subject?></B>
<?
							if(strlen($qnadata->re_date)==14) {
								echo "<img src=images/icn_counsel_ok.gif border=0 align=absmiddle>";
							} else {
								echo "<img src=images/icn_counsel_no.gif border=0 align=absmiddle>";
							}
?>
							</td>
							<td align="left"><?=substr($qnadata->date,0,4)."/".substr($qnadata->date,4,2)."/".substr($qnadata->date,6,2)?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr>
					<td bgcolor=ffffff style=background-repeat:repeat-y;background-position:right;padding:9>
					<?=nl2br($qnadata->content)?>
					</td>
				</tr>
				<?if(strlen($qnadata->re_date)==14) {?>
				<tr>
					<td bgcolor=FCF3E2 style=background-repeat:repeat-y;background-position:right;padding:9>
					<?=nl2br($qnadata->re_content)?>
					</td>
				</tr>
				<?}?>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr><td height=12></td></tr>
				<tr>
					<td align=center>
					<?if(is_object($prevdata)){?>
					<A HREF="javascript:GoCounselView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_prev01.gif" border=0></A>&nbsp;
					<?}?>
					<A HREF="javascript:GoCounselList('<?=$block?>','<?=$gotopage?>')"><img src="images/btn_list.gif" border=0></A>
					<?if(is_object($nextdata)){?>
					&nbsp;<A HREF="javascript:GoCounselView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_next01.gif" border=0></A>
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
						<td style="padding:7,10"><A HREF="javascript:GoCounselView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($prevdata->subject)?></A></td>
						<td align=center><?=substr($prevdata->date,0,4)."/".substr($prevdata->date,4,2)."/".substr($prevdata->date,6,2)?></td>
					</tr>
					<?}?>
					<?if(is_object($nextdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>다음글</td>
						<td style="padding:7,10"><A HREF="javascript:GoCounselView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($nextdata->subject)?></A></td>
						<td align=center><?=substr($nextdata->date,0,4)."/".substr($nextdata->date,4,2)."/".substr($nextdata->date,6,2)?></td>
					</tr>
					<?}?>
					</table>
					</td>
				</tr>

				<?}?>

				<?}else if($type=="write"){?>

				<table width=100% border=0 cellspacing=0 cellpadding=0>
				
				<form name=form1 method=post>
				<input type=hidden name=type value="<?=$type?>">
				<input type=hidden name=mode>

				<tr> 
					<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>이 름</B></td>
					<td width=83% style=padding:7,10 bgcolor="#FFFFFF"><b><?=$_VenderInfo->getId()?></b></td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr> 
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>제 목</B></td>
					<td style=padding:7,10 bgcolor="#FFFFFF">
					<input type=text name="subject" value="" size="60" maxlength=40 required>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr> 
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>내 용</B></td>
					<td style=padding:7,10 bgcolor="#FFFFFF">
					<textarea name="content" rows=10 cols="" style=width:100% maxbyte=10000 required></textarea>
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
				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

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

<?php include("copyright.php"); ?>
