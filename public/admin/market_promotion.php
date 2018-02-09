<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if($_POST['sword']){	
	if($_POST['skey']=='title'){
		$where[]="title like '%".$_POST['sword']."%' ";
	}
}

if(count($where)>0){
$where=" where ".implode(' and ',$where);
}

$selected[skey][$_POST['skey']]='selected';

$imagepath = $cfg_img_path[timesale];

$paging = new newPaging((int)$t_count,10,10);
$gotopage = $paging->gotopage;

$sql = "select *,to_char(rdate,'YYYY-MM-DD') as rdate from tblpromo ".$where." order by display_seq asc";
$sql = $paging->getSql($sql);

$res = pmysql_query($sql,get_db_conn());
$total = pmysql_num_rows($res);

?>



<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function event_pop(mode,idx){

	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="market_promotion_reg.php?mode="+mode+"&pidx="+idx;
}

function event_ins(mode,idx,seq){
	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="market_promotion_reg.php?mode="+mode+"&pidx="+idx+"&seq="+seq;
		}
	}else{
		document.location.href="market_promotion_reg.php?mode="+mode+"&pidx="+idx;
	}	
}

function evnet_reg(idx){	
	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="market_promotion_product.php?pidx="+idx;
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>기획전 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" id="type" name="type" />
			<input type="hidden" id="num" name="num" value="<?=$num?>" />
			<input type="hidden" id="htmlmode" name="htmlmode" value='wysiwyg' />
			<input type="hidden" id="block" name="block" value="<?=$_REQUEST['block']?>" />
			<input type="hidden" id="gotopage" name="gotopage" value="<?=$gotopage?>" />
			<input type="hidden" id="board" name="board" value=<?=$board?> />
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">기획전 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span></span></div>
				</td>
			</tr>

			<tr>
				<td>
				<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=70><col width=><col width=200><col width=100><col width=100><col width=100><col width=100><col width=100>
					<TR align=center>
						<th>No</th>
						<th>기획전 타이틀</th>
						<th>Url</th>
						<th>진열상태</th>
						<th>등록일</th>
						<th>상품 등록</th>
						<th>수정</th>
						<th>삭제</th>
					</TR>
				<?
				while($row=pmysql_fetch_object($res)) {
					$cnt++;					
				?>
					<TR>
					<TD><?=$cnt?></TD>
					<TD><?=$row->title?></TD>
					<TD>/front/promotion.php?pidx=<?=$row->idx?></TD>
					<td><?
						switch($row->display_type){
							case 'A' : echo "ALL"; break;
							case 'P' : echo "PC"; break;
							case 'M' : echo "모바일"; break;
							case 'N' : echo "보류"; break;
						}
					?></td>
					<TD><?=$row->rdate?></TD>
					<TD><a href="javascript:evnet_reg(<?=$row->idx?>);"><img src="images/btn_add2.gif" border="0"></a></TD>
					<TD><a href="javascript:event_pop('mod','<?=$row->idx?>');"><img src="images/btn_edit.gif" border="0"></a></TD>
					<TD><a href="javascript:event_ins('del','<?=$row->idx?>','<?=$row->display_seq?>');"><img src="images/btn_del.gif" border="0"></a></TD>
				    </TR>
				<?
				}
				pmysql_free_result($res);
				if ($cnt==0) {
					echo "<TR><TD colspan=8 align=center>등록된 목록이 없습니다.</TD></TR>";
				}
?>

				</TABLE>

				 <div class="list_search" style="width:100%;text-align:right;padding-top:20px">
					
					<select class="option" name="skey">
						<option value="title" <?=$selected['skey']['title']?>>타이틀</option>
					</select>
					<input type="text" class="bar" name="sword" value="<?=$_POST['sword']?>"/>
					<input type="image" src="../admin/images/btn_search_com.gif" style="vertical-align:middle">
				 </div>
				</div>
<?
							$page_numberic_type=1;
							echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
							echo "<div class=\"page_navi\">";
							echo "<ul>";
							if($page_numberic_type) echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</ul>";
							echo "</div>";
							echo "</div>";
?>

				</td>
			</tr>
			<tr>
				<td><div style="text-align:center;padding-bottom:40px;"><img src="../admin/images/btn_confirm_com.gif" onclick="javascript:event_pop('ins');"/></div></td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

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
<script language="javascript">

</script>
<?=$onload?>
<?php 
include("copyright.php");
