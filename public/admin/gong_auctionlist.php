<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-2";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$auction_seq=$_POST["auction_seq"];
$auction_date=$_POST["auction_date"];

$id=$_POST["id"];
$date=$_POST["date"];

$imagepath=$Dir.DataDir."shopimages/auction/";

if($mode=="delete" && ord($auction_seq) && ord($auction_date)) {
	$sql = "SELECT product_image FROM tblauctioninfo 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if(ord($row->product_image) && file_exists($imagepath.$row->product_image)) {
			unlink($imagepath.$row->product_image);
		}
	}
	pmysql_free_result($result);
	$sql = "DELETE FROM tblauctioninfo 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblauctionresult 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"해당 경매를 삭제하였습니다.\"); }</script>";
} else if($mode=="lastdel" && ord($auction_seq) && ord($auction_date) && ord($id) && ord($date)) {
	$sql = "DELETE FROM tblauctionresult 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' 
	AND id='{$id}' AND date='{$date}' ";
	pmysql_query($sql,get_db_conn());

	$sql = "SELECT start_price, bid_cnt FROM tblauctioninfo 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$last_price=(int)$row->start_price;
	$bid_cnt=(int)$row->bid_cnt;

	if($bid_cnt>0) {
		$sql = "SELECT price FROM tblauctionresult 
		WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' 
		ORDER BY date DESC LIMIT 1 ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$last_price=$row->price;
		}
		pmysql_free_result($result);
	}
	if($bid_cnt>0) $bid_cnt--;
	$sql = "UPDATE tblauctioninfo SET 
	last_price	= '{$last_price}', 
	bid_cnt		= '{$bid_cnt}' 
	WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"최종낙찰자 삭제가 완료되었습니다.\"); }<script>";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function AuctionModify(auction_seq,auction_date) {
	document.modifyform.auction_seq.value=auction_seq;
	document.modifyform.auction_date.value=auction_date;
	document.modifyform.submit();
}

function AuctionDelete(auction_seq,auction_date) {
	if(!confirm("해당 경매를 완전 삭제하시겠습니까?")) return;
	document.form1.mode.value="delete";
	document.form1.auction_seq.value=auction_seq;
	document.form1.auction_date.value=auction_date;
	document.form1.submit();
}

function LastDelete(auction_seq,auction_date,id,date) {
	if(!confirm("최종낙찰자 삭제를 하시겠습니까?")) return;
	document.lastform.auction_seq.value=auction_seq;
	document.lastform.auction_date.value=auction_date;
	document.lastform.id.value=id;
	document.lastform.date.value=date;
	document.lastform.submit();
}

function MemberView(id){
	document.memberform.search.value=id;
	document.memberform.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 쇼핑몰 경매 관리 &gt;<span>등록 경매 관리</span></p></div></div>
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
			<?php include("menu_gong.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=auction_seq>
			<input type=hidden name=auction_date>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">등록 경매 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 경매를 관리할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">경매 관리</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=95></col>
				<col width=></col>
				<col width=120></col>
				<col width=100></col>
				<col width=60></col>
				<col width=40></col>
				<col width=60></col>
				<TR>
					<th>경매 마감일</th>
					<th>경매 상품명</th>
					<th>최종입찰자</th>
					<th>최종입찰가</th>
					<th>입찰수</th>
					<th>조회</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=7;
				$sql = "SELECT COUNT(*) as t_count FROM tblauctioninfo "; 
				$paging = new Paging($sql,10,15);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;						

				$sql = "SELECT a.auction_seq,a.start_date,a.end_date,a.auction_name,a.last_price,a.bid_cnt,a.access, 
				b.id,b.date FROM tblauctioninfo a LEFT JOIN tblauctionresult b 
				ON a.auction_seq=b.auction_seq AND a.start_date=b.start_date AND a.last_price=b.price 
				ORDER BY a.end_date DESC";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$end_date=substr($row->end_date,0,4)."/".substr($row->end_date,4,2)."/".substr($row->end_date,6,2)."(".substr($row->end_date,8,2).":".substr($row->end_date,10,2).")";
					echo "<TR>\n";
					echo "	<TD>";
					if($row->end_date<date("YmdHis")) {
						echo "<img src=\"images/gong_auctionlist_endicon.gif\" border=\"0\">";
					} else {
						echo "<NOBR>{$end_date}</NOBR>";
					}
					echo "	</TD>\n";
					if(ord($row->id)==0) {	//입찰자 없음
						echo "	<TD><div class=\"ta_l\"><A HREF=\"javascript:AuctionModify('{$row->auction_seq}','{$row->start_date}');\">{$row->auction_name}</A></div></TD>\n";
						echo "	<TD>입찰자 없음</TD>\n";
					} else {	//입찰자 있음
						echo "	<TD><div class=\"ta_l\">{$row->auction_name}</div></TD>\n";
						echo "	<TD><A HREF=\"javascript:MemberView('{$row->id}');\"><b>{$row->id}</b></A>";
						if($row->end_date>date("YmdHis")) {
							echo "<BR><A HREF=\"javascript:LastDelete('{$row->auction_seq}','{$row->start_date}','{$row->id}','{$row->date}')\"><img src=\"images/icon_del1.gif\" boder=\"0\"></A>";
						}
						echo "	</TD>\n";
					}
					echo "	<TD><b><span class=\"font_orange\">".number_format($row->last_price)."원</span></b></TD>\n";
					echo "	<TD>".(int)$row->bid_cnt."</TD>\n";
					echo "	<TD>".(int)$row->access."</TD>\n";
					echo "	<TD><a href=\"javascript:AuctionDelete('{$row->auction_seq}','{$row->start_date}')\"><img src=\"images/btn_del.gif\" border=\"0\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>등록 경매 관리</span></dt>
							<dd>
								- 최종입찰자와 최종입찰가만 출력되며, 최종 입찰자 클릭시 해당 회원 정보가 출력됩니다.<br>
								- 경매마감시 <b>최종입찰자</b>가 경매 상품을 구매할 수 있게 별도 처리하셔야 합니다.<br>
								- 등록된 경매 상품 중 입찰자가 없는 상품은 상품명 클릭후 수정이 가능합니다.<Br>
								- 경매 목록 누적시 마감된 경매는 삭제 처리해 주시면 됩니다.
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>

			<form name=lastform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode value="lastdel">
			<input type=hidden name=auction_seq>
			<input type=hidden name=auction_date>
			<input type=hidden name=id>
			<input type=hidden name=date>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>

			<form name=modifyform action="gong_auctionreg.php" method=post>
			<input type=hidden name=auction_seq>
			<input type=hidden name=auction_date>
			</form>
			<form name=memberform action="member_list.php" method=post>
			<input type=hidden name=search>
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
<?=$onload?>
<?php 
include("copyright.php");
