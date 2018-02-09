<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
//include_once($Dir."lib/paging.php");
//include("calendar.php");


$display_type = $_POST['display_type'];
$keyword = $_POST['keyword'];
$where[]="vender='".$_VenderInfo->getVidx()."' ";
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


$sql = "SELECT COUNT(*) as t_count FROM tblpromo ".$where." ";
$paging = new Paging($sql,10,10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

include("header.php"); 
?>


<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function event_pop(mode,idx){

	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	document.location.href="promotion_reg.php?mode="+mode+"&pidx="+idx;
	//window.open( "promotion_reg.php?mode="+mode+"&pidx="+idx, "기획전 관리", "" );
}

function event_ins(mode,idx,seq){
	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="promotion_reg.php?mode="+mode+"&pidx="+idx+"&seq="+seq;
			//window.open( "promotion_reg.php?mode="+mode+"&pidx="+idx+"&seq="+seq, "기획전 관리", "" );
		}
	}else{
		document.location.href="promotion_reg.php?mode="+mode+"&pidx="+idx;
		//window.open( "promotion_reg.php?mode="+mode+"&pidx="+idx, "기획전 관리", "" );
	}
}

function evnet_reg(idx){
	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	//document.location.href="promotion_product.php?pidx="+idx;
	window.open( "promotion_product.php?pidx="+idx, 'promotion_product','height=700,width=1000,scrollbars=yes,resizable=no');
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

function SearchPrd() {
	document.form1.submit();
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
					<FONT COLOR="#ffffff"><B>기획전 관리<B></FONT>
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
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>기획전 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 해당기간동안 기획전을 관리합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 기획전 등록후 관리자의 승인 필요합니다.</td>
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
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>						
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
						<input type="hidden" id="type" name="type" />
						<input type="hidden" id="num" name="num" value="<?=$num?>" />
						<input type="hidden" id="htmlmode" name="htmlmode" value='wysiwyg' />
						<input type="hidden" id="block" name="block" value="<?=$_REQUEST['block']?>" />
						<input type="hidden" id="gotopage" name="gotopage" value="<?=$gotopage?>" />
						<input type="hidden" id="board" name="board" value=<?=$board?> />
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=155></col>
							<col width=></col>
							<col width=325></col>
							<col width=></col>
							<col width=155></col>
							<tr>
								<td>
								<select class="option" name="skey" style="width:100%">
								<option value="title" <?=$selected['skey']['title']?>>타이틀</option>
								</select>
								</td>

								<td></td>

								<td><input type="text" name="sword" value="<?=$_POST['sword']?>" style="width:100%"></td>

								<td></td>

								<td><A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0></A></td>
							</tr>
							</table>
							</td>
						</tr>

						</form>

						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=50>
					<col width=>
					<col width=80>
					<col width=60>
					<col width=60>
					<col width=60>
					<col width=60>
					<col width=60>
					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><b>번호</b></td>
						<td align=center><b>제목</b></td>
						<td align=center><b>기간</b></td>
						<td align=center><b>미리보기</b></td>
						<td align=center><b>진열상태</b></td>
						<td align=center><b>상품등록</b></td>
						<td align=center><b>수정</b></td>
						<td align=center><b>삭제</b></td>
					</tr>

<?php
					$colspan=8;
					$cnt=0;
					if($t_count>0) {

						$sql = "select * from tblpromo ".$where." order by idx desc ";
						$sql = $paging->getSql($sql);
						$res=pmysql_query($sql,get_db_conn());
						$i=0;
						while($row=pmysql_fetch_object($res)) {							
							$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
							echo "<tr height=30 bgcolor=#FFFFFF>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$number."</td>\n";
							echo "	<td align=left style=\"font-size:8pt\">".$row->title."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$row->start_date."<br>~".$row->end_date."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">";							
							switch($row->display_type){
								case 'M' : echo "<a target=\"_balnk\" href=\"/m/promotions.php?pidx=".$row->idx."\">"; break;
								default : echo "<a target=\"_balnk\" href=\"/front/promotion.php?pidx=".$row->idx."\">"; break;
							} 				
							echo "미리보기</a></td>\n";
							echo "	<td align=center style=\"font-size:8pt\">";
							switch($row->display_type){
								case 'A' : echo "ALL"; break;
								case 'P' : echo "PC"; break;
								case 'M' : echo "모바일"; break;
								case 'N' : echo "보류"; break;
							}							
							echo "</td>\n";
							echo "	<td align=center><a href=\"javascript:evnet_reg(".$row->idx.");\"><img src=\"images/btn_regist05.gif\" border=0></a></td>\n";
							echo "	<td align=center><a href=\"javascript:event_pop('mod','".$row->idx."');\"><img src=\"images/btn_modify03.gif\" border=0></a></td>\n";
							echo "	<td align=center><a href=\"javascript:event_ins('del','".$row->idx."','".$row->display_seq."');\"><img src=\"images/btn_delete.gif\" border=0></a></td>\n";
							echo "</tr>\n";
							$i++;
						}
						pmysql_free_result($result);
						$cnt=$i;

						if($i>0) {
							$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
						}
					} else {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
					}
?>
					<input type=hidden name=tot value="<?=$cnt?>">
					</form>

					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>
					<?=$pageing?>
					</td>
				</tr>
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
</table>
	
<script language="javascript">

</script>
<?=$onload?>
<?php
include("copyright.php");
