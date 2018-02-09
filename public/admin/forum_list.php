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

function getCodeLoc_forum($code) {
	$code_loc = "";
	$sql = "SELECT code_name FROM tblforumcode WHERE code_a='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
			} else {
				$sql.= "AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row->code_name;
	}
	$code_loc = implode(" > ",$_);
	pmysql_free_result($result);
	return $code_loc;
}

if($mode=='set_notice'){
	//
}

	$code_a = $_REQUEST['code_a'];
	$code_b = $_REQUEST['code_b'];
	$code_c = $_REQUEST['code_c'];
	$code = $code_a.$code_b.$code_c;

	$listnum = 15;
	$sql = "
		select count(*) as t_count
		from tblforumlist list
		join tblforumcode code
		on list.code = code.code_a||code.code_b||code.code_c 
		";
	$sql .= " where list.code like '{$code}%' ";

	$paging = new newPaging($sql,10,$listnum,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;	

	$sql = "
		select code.code_name,list.* ,
		(select count(*)from tblforumreply where list_no = list.index AND degree=1 ) as re,
		(select count(*) from tblhott_like where section='forum_list' AND hott_code = list.index::varchar ) as like
		from tblforumlist list
		join tblforumcode code
		on list.code = code.code_a||code.code_b||code.code_c 
		";
	$sql .= " where list.code like '{$code}%' ";
	//$sql .= " order by list.index desc ";
    $sql .= " order by list.writetime desc ";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql);
	$i = 0;
	while( $row = pmysql_fetch_object($result) ){
		$row->number= ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
		$forum_list[] = $row;
		$i++;
	}


?>

<?

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblforumcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
$i=0;
$ii=0;
$iii=0;

$strcodelist = "";
$strcodelist.= "<script>\n";
$result = pmysql_query($sql,get_db_conn());
$selcode_name="";

while($row=pmysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code_a='{$row->code_a}';\n";
	$strcodelist.= "clist.code_b='{$row->code_b}';\n";
	$strcodelist.= "clist.code_c='{$row->code_c}';\n";
	$strcodelist.= "clist.code_d='{$row->code_d}';\n";
	$strcodelist.= "clist.type='{$row->type}';\n";
	$strcodelist.= "clist.code_name='{$row->code_name}';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[{$i}]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->code_c=="000" && $row->code_d=="000") {
			$strcodelist.= "listb[{$ii}]=clist;\n";
			$ii++;
		} else if ($row->code_d=="000") {
			$strcodelist.= "listc[{$iii}]=clist;\n";
			$iii++;
		} 
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px;\" onchange=\"SearchChangeCate(this,1)\" {$disabled} >\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px;\" onchange=\"SearchChangeCate(this,2)\" {$disabled}>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px;\" onchange=\"SearchChangeCate(this,3)\" {$disabled} >\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 스크립트 작성완료

?>

<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> 

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; 포럼관리 &gt; <span>포럼 리스트</span></p></div></div>
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
					<div class="title_depth3">포럼 글 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 포럼 글을 확인할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">포럼 글 리스트 </span></div>
				</td>
			</tr>
	

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
                   
				</td>
			</tr>
			
			<tr>
				<td height="20"></td>
			</tr>

			<!-- 포럼 카테고리 선택하기-->
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name="mode">
			<input type=hidden name="index">
			<input type=hidden name="block" value="<?=$block?>">
			<input type=hidden name="gotopage" value="<?=$gotopage?>">
			<tr>
				<td>
					<div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<tr>
							<th><span>카테고리 선택</span> <font color='#FF0000' > *필수 </font> </th>
							<td colspan="3">
							<?php
								//카테고리 SELECT BOX를 불러온다
								echo $codeA_list;
								echo $codeB_list;
								echo $codeC_list;
								//카테고리 SELECT 버튼을 불러온다
								//echo $codeSelect;
								//카테고리 스크립트 실행
								echo $strcodelist;
								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
							?>
							</td>
						</tr>
						</table>
						</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:20px;"><center><a style="cursor:pointer;" id="view_list"><img src="images/botteon_search.gif" border="0"></a></center></td>
			</tr>
			
			<!-- //포럼 카테고리 선택하기-->

			
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
					<th>공지</th>
					<th>번호</th>
					<th>포럼명</th>
					<th>제목</th>
					<th>댓글</th>
					<th>글쓴이</th>
					<th>날짜</th>
					<th>좋아요</th>
					<th>조회</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
				<?foreach($forum_list as $list){;?>
				 <tr>
					<td>
						<?if($list->notice=='Y'){echo "공지글";}?>
					</td>
                    <td><?=$list->number?></td>
					<td style="text-align:left;"><?//=$list->code_name?><?=getCodeLoc_forum($list->code."000")?></td>
                    <td><a class="view_forum" data-index="<?=$list->index?>" data-code="<?=$list->code?>" style="cursor:pointer;"><?=$list->title?></a></td>
					<td><?=$list->re?>건 
					<!-- <a style="cursor:pointer;" class="view_reply" data-index="<?=$list->index?>"><img src="images/btn_viewbbs.gif" align="middle"></a> -->
					</td>
                    <td><?=$list->id?></td>
                    <td><?=date("Y-m-d H:i",strtotime($list->writetime) )?></td>
					<td><?=$list->like?></td>
					<td><?=$list->view?></td>
                    <td><a class="modify_forum" data-index="<?=$list->index?>" data-code="<?=$list->code?>" style="cursor:pointer;"><img src="img/btn/btn_cate_modify.gif" alt="수정" /></a></td>
                    <td><a class="delete_forum" data-index="<?=$list->index?>"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a></td>
                </tr>
				<?}?>
				<!-- <tr height=28 bgcolor=#FFFFFF><td colspan=8 align=center>조회된 내용이 없습니다.</td></tr> -->

				</TABLE>
				</div>
				</td>
			</tr>

			<tr>
				<td><a style="cursor:pointer;" id="set_notice"><img src="images/botteon_save.gif"></a></td>
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

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

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
	document.view_form.action="forum_modify.php";
	document.view_form.code.value=code;
	document.view_form.index.value=index;
	document.view_form.submit();
}

function view_forum()
{
	var index = $(this).data("index");
	window.open("about:blank","view","width=1000,height=1000,scrollbars=yes");
	document.view_form.target="view";
	document.view_form.action="forum_view.php";
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

function set_notice()
{
	document.form1.mode=''
	document.form1.submit();
}

$(document).on("click","#view_list",view_list);

$(document).on("click",".modify_forum",modify_forum);

$(document).on("click",".view_forum",view_forum);

$(document).on("click",".delete_forum",delete_forum);

$(document).on("click",".view_reply",view_reply);

$(document).on("click","#set_notice",set_notice);

</script>

<?=$onload?>
<?php 
include("copyright.php");
?>
