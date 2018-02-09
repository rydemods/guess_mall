<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
?>

<?
#############################function##############################
function store_list($keyword)
{
	$r_data ="";
	$sql = " select count(*) as t_count from tblsignage_store ";
	if($keyword){
		$qry = " where name like '%{$keyword}%' ";
	}

	$paging = new Paging($sql.$qry,10,10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	$sql = " select * from tblsignage_store ";
	$sql .= $qry;
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql);
	while( $row = pmysql_fetch_object($result) ){
		$store_list[] = $row;
	}
	$r_data['list'] = $store_list;
	$r_data['paging'] = $paging;
	return $r_data;
}

function del_store()
{
	$no = $_POST['num'];
	$sql = " delete from tblsignage_store ";
	$sql .= " where no = {$no} ";
	pmysql_query($sql);
}

#############################//function##############################

$mode = $_POST['mode'];
$keyword = $_POST['keyword'];

if($mode=='del'){
	del_store();
}

$store_list = store_list($keyword);

?>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; 매장관리 &gt; <span>매장관리</span></p></div></div>
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
			<?php include("menu_signage.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">매장관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>매장정보를 확인할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">매장 조회</span></div>
				</td>
			</tr>

			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						
                        <TR>
                            <th><span>매장명 입력</span></th>
                            <TD><input name=store_name size=47 class="input" id="store_keyword" value="<?=$keyword?>"></TD>
                        </TR>
                        </TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center">
                    <a style="cursor:pointer;" id="search_store"><img src="images/botteon_search.gif" border="0"></a>&nbsp;
                </td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

		
			<tr>
				<td style="padding-bottom:3pt;">

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="" align="right"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
			
				<TR >
					<th>매장명</th>
					<th>주소</th>
					<th>전화번호</th>
					<th>주변정보등록</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
			<?if($store_list['list']){?>
				<?foreach($store_list['list'] as $val){?>
                <tr>
                    <td><a style="cursor:pointer;" class="view_sub" data-num="<?=$val->no?>"><?=$val->name?></a></td>
					<td><?=$val->address?></td>
                    <td><?=$val->phone?></td>
					<td><a style="cursor:pointer;" class="view_sub" data-num="<?=$val->no?>"><img src="images/btn_input.gif"></a></td>
                    <td>
						<a style="cursor:pointer;" class="modi_store" data-mode="modi" data-num="<?=$val->no?>">
							<img src="img/btn/btn_cate_modify.gif" alt="수정" />
						</a>
					</td>
                    <td>
						<a style="cursor:pointer;" class="del_store" data-mode="del" data-num="<?=$val->no?>">
							<img src="img/btn/btn_cate_del01.gif" alt="삭제" />
						</a>
					</td>
                </tr>
				<?}?>
			<?}else{?>
				<tr height=28 bgcolor=#FFFFFF><td colspan=6 align=center>조회된 내용이 없습니다.</td></tr>
			<?}?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size">
						<p align="center">
						<?=$a_div_prev_page.$store_list['paging']->a_prev_page.$store_list['paging']->print_page.$store_list['paging']->a_next_page.$a_div_next_page?>
						</p>
						</td>
					</tr>
				</table>
				</td>
			</tr>

			<tr>
				<td height=20></td>
			</tr>

			<tr>
				<td align="center">
					<a style="cursor:pointer" id="add_store" data-mode="add"><img src="images/btn_badd2.gif" border="0"></a>
				</td>
			</tr>

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

<form id="store_form" name="store_form" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=num>
	<input type=hidden name=keyword value="<?=$keyword?>">
	<input type=hidden name=block value="">
	<input type=hidden name=gotopage value="">
</form>

<script>

function GoPage(block,gotopage) {
	document.store_form.block.value = block;
	document.store_form.gotopage.value = gotopage;
	document.store_form.submit();
}

function eventHandler()
{
	//배가 고프다..
}

function set_store()
{
	var mode = $(this).data('mode');
	if(mode == 'add'){
		window.open("signage_store_reg.php","_blank","width=900,height=400,scrollbars=no");
		return;
	}else if(mode =='modi'){
		var num = $(this).data('num');
		window.open("signage_store_reg.php?no="+num,"_blank","width=900,height=400,scrollbars=no");
		return;
	}else{//매장 삭쩨
		if(confirm('매장 정보를 삭제하시겠습니까?')){
			var num = $(this).data('num');
			var form = document.store_form;
			form.mode.value = mode;
			form.num.value = num;
			form.submit();
		}
	}
}

function search_store()
{
	var keyword = $("#store_keyword").val();
	var form = document.store_form;
	form.keyword.value = keyword;
	form.submit();
}

function view_sub()
{
	var store_no = $(this).data('num');
	location.href="signage_store_sub.php?store_no="+store_no;
}

$(document).on("click","#add_store",set_store);

$(document).on("click",".modi_store",set_store);

$(document).on("click",".del_store",set_store);

$(document).on("click","#search_store",search_store);

$(document).on("click",".view_sub",view_sub);

</script>

<?=$onload?>

<?php 
include("copyright.php");
?>
