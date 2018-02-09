<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$num=$_GET["num"];

$qna_reply_sql = "SELECT * FROM tblboardcomment WHERE board = 'qna' and parent = '".$num."' order by num desc";
$qna_reply_res = pmysql_query($qna_reply_sql,get_db_conn());


$sql = "SELECT * FROM tblboard WHERE mem_id='".$_ShopInfo->getMemid()."' AND board = 'qna' AND num='{$num}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_pdata=$row;
	$reg_date = date("Y-m-d", $row->writetime);
} else {
	alert_go('해당 문의내역이 없습니다.','c');
}

pmysql_free_result($result);
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}

//-->
</SCRIPT>


<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<tr>
	<td align="center"> 
	<div class="containerBody sub_skin">
	<!-- LNB -->
	<?	include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	<!-- #LNB -->
		<div class="right_section">
			<h3 class="title mb_20">
				Q&A
				<p class="line_map"><a>홈</a> &gt; <a>나의메모</a>  &gt;  <a>질문과답변</a></p>
			</h3>
		
			<div class="customer_inquiry_wrap">
				<table class="write_table" summary="">
					<colgroup>
						<col style="width:121px" />
						<col style="width:auto" />
					</colgroup>
					<tbody>
						<tr height="40">
							<th scope="row">제목</th>
							<td>
								<?=$_pdata->title?>
							</td>
						</tr>
						<tr height="40">
							<th scope="row">날짜</th>
							<td>						
								<?=$reg_date?>
							</td>
						</tr>
						<tr height="40">
							<th scope="row">내용</th>
							<td>
								<?=nl2br($_pdata->content)?>
							</td>
						</tr>
						<tr height="40">
							<th scope="row">답변</th>
							<td>
								<?while($qna_reply_row = pmysql_fetch_object($qna_reply_res)){?>
										<?=nl2br($qna_reply_row->comment)?>
								<?}?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ta_c mt_40"><a href="javascript:history.back();" class="btn_D">목록</a></div>
		</div>
	</div>
	</td> 
</tr>
</table>
		
<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
		