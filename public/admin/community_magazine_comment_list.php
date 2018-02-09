<?php
/********************************************************************* 
// 파 일 명		: community_store_story_comment_list.php
// 설     명		: 매거진 댓글 리스트 관리
// 작 성 자		: 2016.09.20 - 김대엽
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
	include_once($Dir."lib/shopdata.php");
	include("access.php");

	if(ord($_ShopInfo->getId())==0){
		echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
		exit;
	}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mnum			= $_GET["mnum"] ?$_GET["mnum"]:$_POST["mnum"];    //매거진 no
	$mode		= $_POST["mode"];
	$no			= $_POST["no"];          //매거진 댓글 no

	if($mode=="delete_exe" && ord($no)){
		$sql = "DELETE FROM tblmagazine_comment WHERE no = '{$no}' ";
		pmysql_query($sql,get_db_conn());

		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
	}

#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$listnum = 10;

	$sql = "SELECT COUNT(*) as t_count FROM tblmagazine_comment WHERE mnum = '{$mnum}' ";
	$paging = new newPaging($sql,5,$listnum,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>MAGAZINE 댓글 정보</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function commentDelete(no) {
	if(confirm("삭제하시겠습니까?")) {
		document.form_del.mode.value="delete_exe";
        document.form_del.no.value=no;
		document.form_del.target="processFrame";
		document.form_del.submit();
	}
}

function GoPage(block,gotopage) {
	document.pageForm.block.value = block;
	document.pageForm.gotopage.value = gotopage;
	document.pageForm.submit();
}

</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>MAGAZINE 댓글 정보</p></div>
<TABLE WIDTH="800" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-bottom:3pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])==0?'1':ceil($t_count/$setup['list_num'])?></b> 페이지</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<form name="pageForm" method="post">
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<col width="60"></col>
		<col width=""></col>	
		<col width="180"></col>		
		<col width="100"></col>
		<col width="60"></col>
		<TR align=center>
			<th>번호</th>
			<th>내용</th>
			<th>회원ID</th>
			<th>등록일</th>
			<th>삭제</th>
		</TR>
<?php
#---------------------------------------------------------------
# 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {
			$sql = "SELECT * FROM tblmagazine_comment WHERE mnum = '{$mnum}' ORDER BY mnum DESC ";
			$sql = $paging->getSql($sql);
			$result = pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				$regdt	 = substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2);
				echo "<tr>\n";
				echo "	<TD>{$number}</td>\n";
				echo "	<TD style='text-align:left;'>{$row->content}</td>\n";
				echo "	<TD>{$row->id}</td>\n";
				echo "	<TD>{$regdt}</td>\n";
				echo "	<TD><A HREF=\"javascript:commentDelete('{$row->no}');\"><img src=\"images/btn_del.gif\" border=\"0\"></A></td>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
		} else {
			echo "<tr><TD class=\"td_con2\" colspan=5 align=center>댓글 내역이 없습니다.</td></tr>";
		}
?>
		</TABLE>
        </div>
		</td>
		<input type=hidden name=mode>
		<input type=hidden name=mnum value="<?=$mnum?>">
		<input type=hidden name='block' value='<?=$block?>'>
		<input type=hidden name='gotopage' value='<?=$gotopage?>'>
		</form>
	</tr>
	<tr>
		<td>
			<?
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
			?>
			</td>
		</tr>
		</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a></TD>
</TR>
</TABLE>

<form name="form_del" action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name='mode'>
<input type=hidden name="no">
</form>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

</body>
</html>
