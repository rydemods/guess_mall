<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

include ($Dir.BoardDir."file.inc.php");

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
if(strlen($prqnaboard)>0) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$qnasetup->btype=$qnasetup->board_skin[0];
	$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
	if($qnasetup->use_hidden=="Y") $qnasetup=NULL;
}

if(strlen($qnasetup->board)<=0) {
	alert_go("쇼핑몰 Q&A게시판 오픈이 안되었습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",'order_qna.php');
}







if($_REQUEST["mode"]=="comment_result") { //코멘트 등록


	$code=$_REQUEST["code"];
	$num=$_REQUEST["num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$up_name=$_REQUEST["up_name"];
	$up_comment=$_REQUEST["up_comment"];
		
	$sql = "SELECT * FROM tblboard WHERE num = {$num} ";

	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);

		$setup = @pmysql_fetch_array(@pmysql_query("SELECT * FROM tblboardadmin WHERE board ='{$row->board}'",get_db_conn()));
		$setup['max_filesize'] = $setup['max_filesize']*(1024*100);
		$setup['btype']=$setup['board_skin'][0];
		if(ord($setup['board'])==0) {
			alert_go('해당 게시판이 존재하지 않습니다.',-1);
		}
	} else {
		$errmsg="댓글 달 게시글이 없습니다.";
		alert_go($errmsg,-1);
	}

	if ($setup['use_comment'] != "Y") {
		$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
		alert_go($errmsg,-1);
	}

	if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
		$errmsg="잘못된 경로로 접근하셨습니다.";
		alert_go($errmsg,-1);
	}

	if(isNull($up_comment)) {
		$errmsg="내용을 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}

	if(isNull($up_name)) {
		$errmsg="이름을 입력하셔야 합니다.";
		alert_go($errmsg,-1);
	}


	$up_name = pg_escape_string($up_name);
	$up_comment = autoLink($up_comment);
	$up_comment = pg_escape_string($up_comment);

	$sql = "INSERT INTO tblboardcomment DEFAULT VALUES RETURNING num";
	$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
	$sql  = "UPDATE tblboardcomment SET ";
	$sql.= "board		= '{$row->board}', ";
	$sql.= "parent		= '{$row->num}', ";
	$sql.= "name		= '{$up_name}', ";
	$sql.= "passwd		= '{$setup['passwd']}', ";
	$sql.= "ip			= '{$_SERVER['REMOTE_ADDR']}', ";
	$sql.= "writetime	= '".time()."', ";
	$sql.= "comment		= '{$up_comment}' WHERE num={$row2[0]}";
	$insert = pmysql_query($sql,get_db_conn());

	// 코멘트 갯수를 구해서 정리
	$total=pmysql_fetch_array(pmysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='{$row->board}' AND parent='{$row->num}'",get_db_conn()));
	pmysql_query("UPDATE tblboard SET total_comment='{$total[0]}' WHERE board='{$row->board}' AND num='{$row->num}'",get_db_conn());

	header("Location:order_qna.php?code=$code&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
	exit;
} elseif($_REQUEST["mode"]=="comment_del") {//코멘트 삭제
	$code=$_REQUEST["code"];
	$num=$_REQUEST["num"];
	$c_num=$_REQUEST["c_num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$sql = "SELECT * FROM tblboardcomment WHERE parent='{$num}' AND num = {$c_num} ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblboardcomment WHERE board='{$row->board}' AND parent='{$num}' AND num = '{$c_num}'";
		$delete = pmysql_query($sql,get_db_conn());

		if ($delete) {
			@pmysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='{$row->board}' AND num='{$num}'",get_db_conn());
		}
	}
	header("Location:order_qna.php?code=$code&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");
	exit;
}





$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$num=$_REQUEST["num"];
$code=$_REQUEST["code"];
$s_check=$_REQUEST["s_check"];
$search=$_REQUEST["search"];
$block=$_REQUEST["block"];
$search_start=$_REQUEST["search_start"];
$search_end=$_REQUEST["search_end"];
$vperiod=(int)$_REQUEST["vperiod"];
$gotopage=$_REQUEST["gotopage"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?$search_start." 00:00:00":$period[0]." 00:00:00";
$search_e=$search_end?$search_end." 23:59:59":date("Ymd",$CurrentTime)." 23:59:59";

$search_s=strtotime($search_s);
$search_e=strtotime($search_e);

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry = "WHERE a.board='".$qnasetup->board."' ";
$qry.= "AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' ";

$qry2="";
if(strlen($code)>=3) {
	$qry2.= "AND b.productcode LIKE '".$code."%' ";
}
if(date("Ymd",$search_s)==date("Ymd",$search_e)) {
	$qry2.= "AND to_char(to_timestamp(a.writetime),'YYYY-MM-DD')='".date("Y-m-d",$search_s)."' ";
} else {
	$qry2.= "AND a.writetime>='".$search_s."' AND a.writetime <='".$search_e."' ";
}
if(strlen($search)>0) {
	if($s_check=="t") $qry2.= "AND (a.title LIKE '%".$search."%' OR a.content LIKE '%".$search."%') ";
	else if($s_check=="n") $qry2.= "AND a.name='".$search."' ";
}

$sql = "SELECT a.*, b.productcode,b.productname,b.tinyimage,b.sellprice,b.selfcode ";
$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
$sql.= "AND a.num='".$num."' ";
$result=pmysql_query($sql,get_db_conn());
if(!$qnadata=pmysql_fetch_object($result)) {
	alert_go('해당 게시글이 존재하지 않습니다.','order_qna.php');
}
pmysql_free_result($result);

if(strlen($qnadata->filename)>0) {
	$file_name1='';	//다운로드 링크
	$upload_file1='';	//이미지 태그

	$attachfileurl=$filepath."/".$qnadata->filename;
	if(remote_file_exists($attachfileurl)) {
		$file_name1=FileDownload($qnasetup->board,$qnadata->filename)." (".ProcessBoardFileSize($qnasetup->board,$qnadata->filename).")";

		$ext = strtolower(pathinfo($qnadata->filename,PATHINFO_EXTENSION));
		if(in_array($ext,array('gif','jpg','png'))) {
			$imgmaxwidth=ProcessBoardFileWidth($qnasetup->board,$qnadata->filename);
			if($imgmaxwidth>600) {
				$imgmaxwidth=600;
			}
			$upload_file1="<img src=\"".ImageAttachUrl($qnasetup->board,$qnadata->filename)."\" border=0 width=\"".$imgmaxwidth."\">";
		}
	}
}


//이전글
$sql = "SELECT a.num,a.name,a.title,a.writetime FROM tblboard a, tblproduct b ".$qry." ".$qry2." ";
$sql.= "AND a.pos = 0 AND a.thread < '".$qnadata->thread."' AND a.deleted != '1' ";
$sql.= "ORDER BY a.thread DESC LIMIT 1 ";
$result=pmysql_query($sql,get_db_conn());
$prevdata=pmysql_fetch_object($result);
pmysql_free_result($result);

//다음글
$sql = "SELECT a.num,a.name,a.title,a.writetime FROM tblboard a, tblproduct b ".$qry." ".$qry2." ";
$sql.= "AND a.pos = 0 AND a.thread > '".$qnadata->thread."' AND a.deleted != '1' ";
$sql.= "ORDER BY a.thread LIMIT 1 ";
$result=pmysql_query($sql,get_db_conn());
$nextdata=pmysql_fetch_object($result);
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "order.ctgr.php?code="+code;
	BCodeCtgr.location.href = murl;
}

function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.sForm.submit();
}

function listArticle() {
	document.procForm.exec.value="";
	document.procForm.num.value="";
	document.procForm.target="";
	document.procForm.action="order_qna.php";
	document.procForm.submit();
}

function viewArticle(num) {
	document.procForm.exec.value="";
	document.procForm.num.value=num;
	document.procForm.target="";
	document.procForm.action="order_qnaview.php";
	document.procForm.submit();
}

function replyArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="reply";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnawriteopen.php";
	document.procForm.submit();
}

function modifyArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="modify";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnapassconfirm.php";
	document.procForm.submit();
}

function deleteArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="delete";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnapassconfirm.php";
	document.procForm.submit();
}

</script>

<script>
function check_del(url) {
	if(confirm("삭제 하시겠습니까?")) {
		document.location.href=url;
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
					<FONT COLOR="#ffffff"><B>상품 Q&A 관리</B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 Q&A 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사에서 등록한 상품에 대해서만 Q&A 게시물을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사는 등록된 Q&A 게시물의 관리[답변/수정/삭제]를 할 수 있습니다.</td>
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

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="order_qna.php" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=></col>
						<col width=130></col>
						<tr>
							<td>
							<U>접수일</U>&nbsp; <input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;font-size:8pt">
							&nbsp;
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<U>분류</U>&nbsp;
							<select name="code1" style=width:130; onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
							<option value="">--- 선택하세요 ---</option>
<?
							$sql = "SELECT SUBSTR(b.c_category,1,3) as prcode FROM tblproduct a left join tblproductlink b on a.productcode=b.c_productcode ";
							$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
							$sql.= "GROUP BY prcode ";
							$result=pmysql_query($sql,get_db_conn());
							$codes="";
							while($row=pmysql_fetch_object($result)) {
								$codes.=$row->prcode.",";
							}
							pmysql_free_result($result);
							if(strlen($codes)>0) {
								$codes=rtrim($codes,',');
								$prcodelist=str_replace(',','\',\'',$codes);
							}
							if(strlen($prcodelist)>0) {
								$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
								$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
								$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									echo "<option value=\"".$row->code_a."\"";
									if($row->code_a==substr($code,0,3)) echo " selected";
									echo ">".$row->code_name."</option>\n";
								}
								pmysql_free_result($result);
							}
?>
							</select>
							</td>
							<td><iframe name="BCodeCtgr" src="order.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>" width="130" height="21" scrolling=no frameborder=no></iframe></td>
						</tr>
						<tr><td colspan=2 height=8></td></tr>
						<tr>
							<td colspan=2>
							<U>검색어</U>&nbsp;
							<select name=s_check style="font-size:8pt">
							<option value="n" <?if($s_check=="n")echo"selected";?>>작성자</option>
							<option value="t" <?if($s_check=="t")echo"selected";?>>제목+내용</option>
							</select>
							<input type=text name=search value="<?=$search?>" size=30>
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr><td height="10"></td></tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
					<tr>
						<td bgcolor="#FFFFFF" style="padding:8px;">
						<table cellpadding="0" cellspacing="0" width="100%" align="center" style="table-layout:fixed">
						<col width="70"></col>
						<col width="15"></col>
						<col></col>
						<tr>
							<td>
<?
							echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$qnadata->productcode."\" target='_blank' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
							if (strlen($qnadata->tinyimage)>0) {
								echo "<img src=\"".getProductImage($Dir.DataDir.'shopimages/product/',$qnadata->tinyimage)."\" border=\"0\" width=\"70\">";
							} else {
								echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\" width=\"70\">";
							}
							echo "</A></td>";
?>
							<td></td>
							<td>
							<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
							<col width="60">
							<col width="10">
							<tr>
								<td>상품명</td>
								<td align="center">:</td>
								<td><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$qnadata->productcode?>" target="_blank" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($qnadata->productname,$qnadata->etctype,"").(strlen($qnadata->selfcode)>0?" - ".$qnadata->selfcode:"")?></FONT></A></td>
							</tr>
							<tr>
								<td>상품가격</td>
								<td align="center">:</td>
								<td><font class="prprice">
<?
							if($dicker=dickerview($qnadata->etctype,number_format($qnadata->sellprice)."원",1)) {
								echo $dicker;
							} else if(strlen($_data->optiontitle)==0) {
								echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\">".number_format($qnadata->sellprice)."원";
								if (strlen($qnadata->option_price)!=0) echo "(기본가)";
							} else {
								if (strlen($qnadata->optionprice)==0) echo number_format($qnadata->sellprice)."원";
								else echo str_replace("[PRICE]",number_format($qnadata->sellprice),$_data->optiontitle);
							}
							if ($qnadata->quantity=="0") echo soldout();
?>
								</font></td>
							</tr>
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

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=20></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td valign=top style=background-repeat:repeat-x bgcolor="e7e7e7">
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td bgcolor=F5F5F5>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style=background-repeat:repeat-y;background-position:right;padding:9 width="88%">
							<B>제 목 : <?=$qnadata->title?></B>
							</td>
							<td align="left"><?=date("Y/m/d",$qnadata->writetime)?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<?if(strlen($file_name1)>0) {?>
				<TR>
					<TD align="right" style="padding:3;"><font color="#FF6600">첨부파일 : <?=$file_name1?></font></TD>
				</TR>
				<?}?>
				<tr>
					<td bgcolor=ffffff style=background-repeat:repeat-y;background-position:right;padding:9>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
						<TD style="word-break:break-all;" valign="top">
						<span style="width:100%;line-height:160%;"> 
						<?=nl2br($qnadata->content)?>
						</span>
						</TD>
					</TR>
					<TR>
						<TD style="word-break:break-all;" valign="top">
						<?if ($upload_file1) {?>
						<span style="width:100%;line-height:160%;text-align:center"> 
						<?=$upload_file1?>
						</span>
						<?}?>
						</td>
					</tr>
<?
					//관련답변
						echo "<tr><td height=5></td></tr>\n";
						echo "<tr>\n";
						echo "	<td bgcolor=#E7E7E7>\n";
						echo "	<table border=0 cellpadding=5 cellspacing=4 width=100%>\n";
						echo "	<tr>\n";
						echo "		<td bgcolor=#FFFFFF>\n";
						echo "		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";				
							echo "	<tr bgcolor=#FFFFFF>\n";
							echo "		<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
							
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
							echo "	document.comment_form.mode.value='comment_result';\n";

					//		if(isdev()) echo "	alert(document.comment_form.action);\n";
					//		if(isdev()) echo "	alert(document.comment_form.target);\n";
					//		if(isdev()) echo "	alert(document.comment_form.mode.value);\n";
							echo "	document.comment_form.submit();\n";
							echo "}\n";
							echo "</script>\n";

							echo "<form method='post' name='comment_form' action='order_qnaview.php'>\n";
							echo "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\">\n";

							
							echo "<input type=hidden name=code value=\"{$code}\">\n";
							echo "<input type=hidden name=s_check value=\"{$s_check}\">\n";
							echo "<input type=hidden name=search value=\"{$search}\">\n";
							echo "<input type=hidden name=block value=\"{$block}\">\n";
							echo "<input type=hidden name=gotopage value=\"{$gotopage}\">\n";
							echo "<input type=hidden name=num value=\"{$num}\">\n";
							echo "<input type=hidden name=mode value='comment_result'>\n";
							echo "<TR>\n";
							echo "	<TD>\n";

							echo "	<TABLE cellSpacing=0 cellPadding=4 width=\"100%\">\n";
							echo "<TR>\n";
							echo "	<TD class=board_cell1 width=581 bgColor=#fafafa colSpan=2>\n";
							 echo "	&nbsp;▣ <b>문의 답변</b><INPUT type=hidden name=up_name value='vender_{$_venderdata->id}'>";
							echo "	</TD>\n";
							echo "</TR>\n";
							echo "	<TR align=middle>\n";
							echo "		<TD align=left width=\"100%\" bgColor=#fafafa><TEXTAREA class=input style=\"PADDING-RIGHT: 5pt; PADDING-LEFT: 5pt; PADDING-BOTTOM: 5pt; WIDTH: 100%; PADDING-TOP: 5pt; HEIGHT: 70px\" name=up_comment></TEXTAREA></TD>\n";
							echo "		<TD align=right width=\"72\" bgColor=#fafafa><A href=\"javascript:chkCommentForm();\"><IMG height=69 src=\"images/comment.gif\" width=72 border=0></A></TD>\n";
							echo "	</TR>\n";
							echo "	</TABLE>\n";

							echo "	</TD>\n";
							echo "</TR>\n";

							$com_query = "SELECT * FROM tblboardcomment WHERE board='".$qnasetup->board."' 
							AND parent = $num ORDER BY num DESC ";
							$com_result = @pmysql_query($com_query,get_db_conn());
							$com_rows = @pmysql_num_rows($com_result);

							if ($com_rows <= 0) {
								@pmysql_query("UPDATE tblboard SET total_comment='0' WHERE board='".$qnasetup->board."' AND num='{$num}'");
							} else {
								$com_list=array();
								while($com_row = pmysql_fetch_array($com_result)) {
									$com_row[name] .= $com_row[c_mem_id]?"({$com_row[c_mem_id]})":"";
									$com_list[count($com_list)] = $com_row;
								}
								pmysql_free_result($com_result);
							}

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
								echo "	<TABLE cellSpacing=0 cellPadding=0 width=\"100%\">\n";
								echo "	<TR>\n";
								echo "		<TD width=\"100%\" background=\"images/bbs_line1.gif\"></TD>\n";
								echo "	</TR>\n";
								echo "	<TR>\n";
								echo "		<TD width=\"100%\" height=5></TD>\n";
								echo "	</TR>\n";
								echo "	<TR>\n";
								echo "		<TD class=tk1 width=\"100%\" height=22><B><span class=\"font_blue\">{$c_name}</span></B> / <span class=\"board_con1s\">{$c_writetime} ({$c_ip})</span>";
								if ($c_name == "vender_".$_venderdata->id) {
									echo "<A style=\"CURSOR:hand;\" onclick=\"check_del('{$_SERVER['PHP_SELF']}?mode=comment_del&num={$num}&c_num={$c_num}&s_check={$s_check}&search={$search}&block={$block}&gotopage={$gotopage}')\"><IMG SRC=\"images/del_x.gif\" border=0 align=\"absmiddle\" vspace=\"4\" alt=\"삭제\"></A>";
								}
								echo "</TD>\n";
								echo "	</TR>\n";
								echo "	<TR>\n";
								echo "		<TD style='word-break:break-all;' class=tk1 width=\"100%\" height=22>{$c_comment}</TD>\n";
								echo "	</TR>\n";
								echo "	<TR>\n";
								echo "		<TD width=\"100%\" height=5></TD>\n";
								echo "	</TR>\n";
								echo "	</TABLE>\n";
								echo "	</TD>\n";
								echo "</TR>\n";
							}
							echo "<TR>\n";
							echo "	<TD width=\"100%\" background=\"images/bbs_line1.gif\"></TD>\n";
							echo "</TR>\n";
							echo "<TR>\n";
							echo "	<td></td>\n";
							echo "</TR>\n";
							echo "</TABLE>\n";
							echo "</form>\n";
			


							echo "		</td>\n";
							echo "	</tr>\n";
						echo "		</table>\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</td>\n";
						echo "</tr>\n";
?>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr><td height=12></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:listArticle()"><img src="images/btn_list.gif" border=0></A>
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
					<col width=60></col>
					<col width=></col>
					<col width=80></col>
					<col width=80></col>
					<tr height=28 align=center bgcolor=F5F5F5>
						<td><B>번호</B></td>
						<td><B>제목</B></td>
						<td><B>글쓴이</B></td>
						<td><B>등록일</B></td>
					</tr>
					<?if(is_object($prevdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>이전글</td>
						<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>
						<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>
						<A HREF="javascript:viewArticle(<?=$prevdata->num?>)"><?=strip_tags($prevdata->title)?></A>
						</span>
						</td>
						<td align=center><?=$prevdata->name?></td>
						<td align=center><?=date("Y-m-d",$prevdata->writetime)?></td>
					</tr>
					<?}?>
					<?if(is_object($nextdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>다음글</td>
						<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>
						<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>
						<A HREF="javascript:viewArticle(<?=$nextdata->num?>)"><?=strip_tags($nextdata->title)?></A>
						</span>
						</td>
						<td align=center><?=$nextdata->name?></td>
						<td align=center><?=date("Y-m-d",$nextdata->writetime)?></td>
					</tr>
					<?}?>
					</table>
					</td>
				</tr>

				<?}?>

				</table>

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

<form name=procForm method=post>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=exec>
<input type=hidden name=num>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
