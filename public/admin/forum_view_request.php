<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/forum.class.php");
$forum = new FORUM('view_request');

$forum_detail = $forum->forum_detail;
$reply_list = $forum->reply_list;
$imagepath = $Dir.DataDir."shopimages/forum/";


if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}
?>

<html>
<head>
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function PageResize() {
	var oWidth = 1000;
	if (document.all.table_body.clientHeight > 600)
	{
		var oHeight = 1000;
	} else {
		var oHeight = document.all.table_body.clientHeight + 120;
	}

	window.resizeTo(oWidth,oHeight);
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<title>포럼 글 수정</title>
<div class="pop_top_title"><p>포럼 글 수정<!--/답변--></p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id="table_body">
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18"></td>
		<td></td>
		<td width="18" height=10></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

			<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
		
			<TR>
				<th><span>작성자</span></th>
				<TD class="td_con1"><B><?=$forum_detail->id?></B></TD>
			</TR>
			<TR>
				<th><span>날짜</span></th>
				<TD class="td_con1"><B><?=date("Y-m-d h:i",strtotime($forum_detail->writetime) )?></B></TD>
			</TR>

			<TR>
				<th><span>신청 카테고리</span></th>
				<TD class="td_con1"><?=$forum_detail->code_a?>-><?=$forum_detail->code_b?>-><?=$forum_detail->code_c?></TD>
			</TR>
		
			<TR>
				<th><span>제목</span></th>
				<TD class="td_con1"><?=$forum_detail->title?></TD>
			</TR>
			<TR>
				<th><span>내용</span></th>
				<TD class="td_con1"><?=$forum_detail->content?></TD>
			</TR>

			<TR>
				<th><span>썸네일</span></th>
				<TD class="td_con1">
				<?if(is_file($Dir.DataDir.$imagepath.$forum_detail->img) ){?>
					<img src='<?=$Dir.DataDir.$imagepath.$forum_detail->img?>' style='max-width:320px'/>
				<?}?>
				</td>
			</TR>

			<TR>
				<th><span>댓글</span></th>
				<td>
					<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td colspan=5 style="width:100%">
							<textarea style="width:85%" id="review_comment"></textarea>
							<a class="write_reply" data-degree="1">[댓글입력]</a>
						</td>
					</tr>
					<TR align=center>
						<th colspan=2>내용</th>
						<th>작성자</th>
						<th>등록일</th>
						<th>삭제</th>
					</TR>
					<?if($reply_list['1']){?>
						<?foreach($reply_list['1'] as $val){?>
						<TR>
							<td colspan=2>
							<?if($val->check_delete =='Y'){?>
								삭제된 댓글 입니다
							<?}else{?>
								<?=$val->content?>&nbsp;&nbsp; <a class="open_reply">[댓글]</a>
							<?}?>
							</td>
							<td><?=$val->id?></td>
							<td><?=$val->writetime?></td>
							<td><a data-no="<?=$val->index?>" class="delete_reply"><img src="img/btn/btn_cate_del01.gif" alt="삭제"><a/></td>
						</TR>

						<tr style="display:none;">
							<td colspan=5 style="width:100%;">
								<textarea style="width:85%"></textarea>
								<a data-degree="2" data-no="<?=$val->index?>" class="write_reply_2">[댓글입력]</a>
								<a class="close_reply">[취소]</a>
							</td>
						</tr>

							<?if($reply_list['2'][$val->index]){?>
								<?foreach($reply_list['2'][$val->index] as $val2){?>
									<TR>
										<td>ㄴ</td>
										<td>
										<?if($val2->check_delete =='Y'){?>
											삭제된 댓글 입니다
										<?}else{?>
											<?=$val2->content?>
										<?}?>
										</td>
										<td><?=$val2->id?></td>
										<td><?=$val2->writetime?></td>
										<td><a data-no="<?=$val2->index?>" class="delete_reply"><img src="img/btn/btn_cate_del01.gif" alt="삭제"><a/></td>
									</TR>
								<?}?>
							<?}?>

						<?}?>
					<?}?>
					</table>
					</div><!-- reply_comment-->
				</td>
			</TR>

			</TABLE>
			</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td align="center"><a id="forum_submit">;<a href="javascript:window.close();"><img src="images/btn_close.gif"  border="0" vspace="5" border=0 hspace="2"></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	
	</form>
	</table>
	</TD>
</TR>
</TABLE>

<script language="javascript">

var forum_code = "<?=$forum_detail->code?>";
var list_no = "<?=$forum_detail->index?>";

function open_reply()
{
	$(this).parent().parent().next().fadeIn('fast');
}

function close_reply()
{
	$(this).parent().parent().fadeOut('fast');
}

function write_reply()
{
	if( confirm('댓글을 등록 하시겠습니까?') ){
		var reply_no = null;
		var degree = $(this).data('degree');

		if(degree =="1"){//일반 댓글
			var comment = $("#review_comment").val();
		}else{//대댓글
			var reply_no = $(this).data('no');
			var comment = $(this).prev().val();
		}
		
		var event_reply = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,            
			data: {
				mode : 'write_reply_request',
				degree : degree,
				list_no : list_no,
				reply_no : reply_no,
				comment : comment,
				admin : 'admin'
			}            
		});
		event_reply.done(resultHandler_REPLY);
	}
}

function resultHandler_REPLY(r_data)
{
	if(r_data=="WRITE_OK"){
		alert('댓글이 등록되었습니다');
		location.reload();
	}else if(r_data=="DELETE_OK"){
		alert('댓글이 삭제되었습니다');
		location.reload();
	}
}

function delete_reply()
{
	if( confirm('댓글을 삭제 하시겠습니까?') ){
		var reply_no = $(this).data('no');
		alert(reply_no);
		var event_reply = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,             
			data: {
				mode : 'delete_reply_request',
				reply_no : reply_no
			}            
		});
		event_reply.done(resultHandler_REPLY);
	}
}



$(document).on("click","#forum_submit",forum_submit);

$(document).on("click",".open_reply",open_reply);

$(document).on("click",".close_reply",close_reply);

$(document).on("click",".write_reply",write_reply);

$(document).on("click",".write_reply_2",write_reply);

$(document).on("click",".delete_reply",delete_reply);

</script>

</body>
</html>
