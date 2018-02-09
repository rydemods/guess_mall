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
$display_type = $_POST['display_type'];
$keyword = $_POST['keyword'];
//exdebug($display_type);exdebug($keyword);
/*
if($_POST['sword']){
	if($_POST['skey']=='title'){
		$where[]="title like '%".$_POST['sword']."%' ";
	}
}
*/
if($keyword){
	$where[]="lower(title) like lower('%".$keyword."%') ";
}
if($display_type != 'ALL' && $display_type!=""){
	$where[]="display_type = '".$display_type."' ";
}

if(count($where)>0){
$where=" where ".implode(' and ',$where);
}

$selected[skey][$_POST['skey']]='selected';

$imagepath = $cfg_img_path[timesale];


$sql = "select * from tblfamily_list ".$where." order by title asc";
$res = pmysql_query($sql,get_db_conn());
$total = pmysql_num_rows($res);
$paging = new newPaging((int)$total,10,20);
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
$res = pmysql_query($sql,get_db_conn());

?>



<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function event_pop(mode,idx){

	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="market_family_reg.php?mode="+mode+"&pidx="+idx;
}

function event_ins(mode,idx,seq){
	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="market_family_reg.php?mode="+mode+"&pidx="+idx+"&seq="+seq;
		}
	}else{
		document.location.href="market_family_reg.php?mode="+mode+"&pidx="+idx;
	}
}

function evnet_reg(idx){
	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="market_family_product.php?pidx="+idx;
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>패밀리세일 관리</span></p></div></div>
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
					<div class="title_depth3">패밀리세일 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>패밀리세일 검색</span></div>
					<div class="table_style01 pt_20">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>타이틀 검색</span></th>
								<td>
									<input class="w200" type="text" name="keyword" value=""/>
								</td>
								<th><span>진열상태</span></th>
								<?
								/*
								if($display_type=='ALL') echo "selected";
								if($display_type=='A') echo "selected";
								if($display_type=='P') echo "selected";
								if($display_type=='M') echo "selected";
								if($display_type=='N') echo "selected";
								if($display_type=='S') echo "selected";
								if($display_type=='D') echo "selected";
								if($display_type=='B') echo "selected";
								if($display_type=='C') echo "selected";
								*/
								?>
								<td>
									<select name="display_type" id="display_type">
										<option value="ALL">선택</option>
										<option value="A" >모두</option>
										<option value="P" >PC만</option>
										<option value="M" >모바일만</option>
										<option value="N" >보류</option>
										<option value="S" >PC 비전시</option>
										<option value="D" >모바일 비전시</option>
										<option value="B" >fitflop 모바일만</option>
										<option value="C" >fitflop 모바일 비전시</option>
									</select>
								</td>
							</tr>
						</table>
						<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색"></a></p>
					</div>
				</td>
			</tr>

			<tr>
				<td>
				<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=70><col width=><col width=200><col width=200><col width=100><col width=100><col width=100><col width=100><col width=100><col width=100>
					<TR align=center>
						<th>No</th>
						<th>기획전 타이틀</th>
						<th>기간</th>
						<th>Url</th>
						<th>진열상태</th>
						<th>쿠폰, 적립금<br/> 사용금지</th>
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
					<TD style="text-align:left;"><?=$row->title?></TD>
					<TD><?=$row->start_date?>&nbsp;~&nbsp;<?=$row->end_date?></TD>
					<TD>
					<?
						switch($row->display_type){
							case 'S' : echo "/front/promotions.php?pidx=".$row->idx; break;
							case 'M' : echo "/m/promotions.php?pidx=".$row->idx; break;
							case 'D' : echo "/m/promotions.php?pidx=".$row->idx; break;
							case 'B' : echo "/fitflop_m/promotions.php?pidx=".$row->idx; break;
							case 'C' : echo "/fitflop_m/promotions.php?pidx=".$row->idx; break;
							default :?><a target="_balnk" href="/front/promotion.php?pidx=<?=$row->idx?>"> <?echo "/front/promotion.php?pidx=".$row->idx;?></a> <?break;
						}
					?>
					</TD>
					<td><?
						switch($row->display_type){
							case 'A' : echo "ALL"; break;
							case 'P' : echo "PC"; break;
							case 'M' : echo "모바일"; break;
							case 'N' : echo "보류"; break;
							case 'S' : echo "PC 비전시"; break;
							case 'D' : echo "모바일 비전시"; break;
							case 'B' : echo "fitflop 모바일만"; break;
							case 'C' : echo "fitflop 모바일 비전시"; break;
						}
					?></td>
					<TD><?=$row->no_coupon?></TD>
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
