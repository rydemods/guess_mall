<?php
/********************************************************************* 
// 파 일 명		: community_store_story_comment_list.php
// 설     명		: 스토어 스토리 댓글 리스트 관리
// 작 성 자		: 2016.09.09 - 김재수
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
?>

<?
$index = $_REQUEST['index'];
$r_sql = " select list.*, ";
$r_sql .="
	(select count(feeling_type) from tblgood_feeling where section ='forum_reply' AND feeling_type ='good' AND code = 'forum_reply_'||list.index ) as good_count ,
	(select count(feeling_type) from tblgood_feeling where section ='forum_reply' AND feeling_type ='bad' AND code = 'forum_reply_'||list.index ) as bad_count 
	from tblforumreply list 
	where list.list_no = {$index} 
	order by list.degree asc, list.sort asc 
";
$r_result = pmysql_query($r_sql);
$r_list = array();
while( $r_row = pmysql_fetch_object($r_result) ){
	$r_row->writetime = date("Y-m-d", strtotime($r_row->writetime) );
	
	if($r_row->degree >1 ){//대댓글 조립
		$r_list[$r_row->degree][$r_row->reply_no][] = $r_row;
	}else{//일반 댓글 조립
		$r_list['1'][] = $r_row;
	}
}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>포럼글 댓글 정보</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function commentDelete(cno) {
	if(confirm("삭제하시겠습니까?")) {
		document.form_del.mode.value="delete_exe";
        document.form_del.cno.value=cno;
		document.form_del.target="processFrame";
		document.form_del.submit();
	}
}

function GoPage(block,gotopage) {
	document.pageForm.block.value = block;
	document.pageForm.gotopage.value = gotopage;
	document.pageForm.submit();
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>포럼 댓글 정보</p></div>
<TABLE WIDTH="800" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-bottom:3pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr style="display:none;">
			<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])==0?'1':ceil($t_count/$setup['list_num'])?></b> 페이지</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<form name="pageForm" method="post">
			<input type=hidden name=mode>
			<input type=hidden name=sno value="<?=$sno?>">
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
		</form>
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
			<th>작성자</th>
			<th>등록일</th>
			<th>삭제</th>
		</TR>
	<?if($r_list['1']){?>
		<?foreach($r_list['1'] as $val){?>
		<TR>
			<td>1</td>
			<td><?=$val->content?></td>
			<td>1</td>
			<td><?=$val->writetime?></td>
			<td>1</td>
		</TR>
			<?if($r_list['2'][$val->index]){?>
				<?foreach($r_list['2'][$val->index] as $val2){?>
					<TR>
						<td></td>
						<td><?=$val->id?><br>RE:<?=$val2->content?></td>
						<td>1</td>
						<td><?=$val2->writetime?></td>
						<td>1</td>
					</TR>
				<?}?>
			<?}?>

		<?}?>
	<?}?>

		</TABLE>
        </div>
		</td>
		
		
	</tr>
	<tr style="display:none;">
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
<input type=hidden name="cno">
</form>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

</body>
</html>
