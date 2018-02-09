<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-8";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
$mode = $_REQUEST['mode'];



	$listnum = 20;
	$sql = "
		select count(*) as t_count
		from tblforumlist_request list
		";

	$paging = new newPaging($sql,10,$listnum,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;	

	$sql = "
		select list.* ,
		(select count(*)from tblforumreply_request where list_no = list.index AND degree=1 ) as re,
		(select count(*) from tblhott_like where section='forum_list_request' AND hott_code = list.index::character ) as like
		from tblforumlist_request list
		";
	$sql .= " order by list.index desc ";

	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql);
	$i = 0;
	while( $row = pmysql_fetch_object($result) ){
		$row->number= ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
		$forum_list[] = $row;
		$i++;
	}


?>

<script>

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; 포럼관리 &gt; <span>포럼 신청 리스트</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">포럼 신청 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 포럼 신청 리스트를 확인할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">포럼 신청 리스트 </span></div>
				</td>
			</tr>
		
            <input type=hidden name=mode>
	

			<tr style="display:none;">
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <TR>
                            <th><span>배가고픔</span></th>
                            <TD><input name=store_name size=47 value="" class="input"></TD>
                        </TR>
						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr style="display:none;">
				<td style="padding-top:4pt;" align="center">
                    <a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>
                </td>
			</tr>
			
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
			
				<TR >
					<th>번호</th>
					<th>제목</th>
					<th>댓글</th>
					<th>글쓴이</th>
					<th>날짜</th>
					<th>좋아요</th>
					<th>조회</th>
					<th>삭제</th>
				</TR>
				<?foreach($forum_list as $list){;?>
				 <tr>
                    <td><?=$list->number?></td>
                    <td><a class="view_forum" data-index="<?=$list->index?>" data-code="<?=$list->code?>" style="cursor:pointer;"><?=$list->title?></a></td>
					<td><?=$list->re?>건 
					<!-- <a style="cursor:pointer;" class="view_reply" data-index="<?=$list->index?>"><img src="images/btn_viewbbs.gif" align="middle"></a> -->
					</td>
                    <td><?=$list->id?></td>
                    <td><?=date("Y-m-d h:i",strtotime($list->writetime) )?></td>
					<td><?=$list->like?></td>
					<td><?=$list->view?></td>
                    <td><a class="delete_forum" data-index="<?=$list->index?>"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a></td>
                </tr>
				<?}?>
				<!-- <tr height=28 bgcolor=#FFFFFF><td colspan=8 align=center>조회된 내용이 없습니다.</td></tr> -->

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
	
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
		
?>
				</table>
				</td>
			</tr>
			<!-- <input type=hidden name=tot value="<?=$cnt?>"> -->
			</form>

	

           

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>-</span></dt>
							<dd>-</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
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

<form name="view_form" method=post>
<input type=hidden name=mode value='modify_form'>
<input type=hidden name=type value='modify'>
<input type=hidden name=code value=''>
<input type=hidden name=index value=''>
</form>

<script>

function view_list()
{
	document.form1.submit();
}

function modify_forum()
{
	var index = $(this).data("index");
	var code = $(this).data("code");
	window.open("about:blank","view","width=1000,height=1000,scrollbars=yes");
	document.view_form.target="view";
	document.view_form.action="forum_modify_request.php";
	document.view_form.code.value=code;
	document.view_form.index.value=index;
	document.view_form.submit();
}

function view_forum()
{
	var index = $(this).data("index");
	window.open("about:blank","view","width=1000,height=1000,scrollbars=yes");
	document.view_form.target="view";
	document.view_form.action="forum_view_request.php";
	document.view_form.index.value=index;
	document.view_form.submit();
}

function resultHandler_DELETE(r_data)
{
	if(r_data =='S'){
		alert('글이 삭제 되었습니다');
		location.reload();
	}else if(r_data=='F'){
		alert('삭제실패. 관리자에게 문의하세요');
	}
}

function delete_forum()
{
	if( confirm('글을 삭제 하시겠습니까?') ){
		var list_no = $(this).data('index');
		var event_delete = $.ajax({
			url: '/front/forum_process.php',
			type: 'POST',           
			cache: true,             
			data: {
				mode : 'delete',
				forum_index : list_no
			}
		});
		event_delete.done(resultHandler_DELETE);
	}
}

function view_reply()
{
	var index = $(this).data("index");
	window.open("forum_reply.php?index="+index,"_blank","width=1000,height=1000,scrollbars=yes");
}

$(document).on("click","#view_list",view_list);

$(document).on("click",".modify_forum",modify_forum);

$(document).on("click",".view_forum",view_forum);

$(document).on("click",".delete_forum",delete_forum);

$(document).on("click",".view_reply",view_reply);

</script>

<?=$onload?>
<?php 
include("copyright.php");
?>
