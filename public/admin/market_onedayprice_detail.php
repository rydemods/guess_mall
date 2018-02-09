<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//날짜를 위한 함수

$todayY = $_POST['todayY'];	//선택된 연도
$todayM = $_POST['todayM'];	//선택된 월
$todayD = $_POST['todayD'];	//선택된 일
$today = "{$todayY}-{$todayM}-{$todayD}";
$nextdayD = $todayD+1;	//선택된 다음날 일 - 쿼리에서 쓰임
$nextday = date("Y-m-d",mktime(0,0,0,$todayM,$nextdayD,$todayY));	//선태된 다음날 - 쿼리에서 쓰임

$type=($_POST['type'])?$_POST['type']:"R";

$typename = array(	
				"R" => "문자예약"
				,"E" =>  "조르기"
			);
$title = $typename[$type]." 목록";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>

<script language="JavaScript">

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function GoType(type){
	document.form2.type.value=type;
	document.form2.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function GoList(){
	document.form2.action="market_onedayprice.php";
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원  &gt; 이벤트/사은품 기능 설정 &gt;<span>오늘의 특가 관리</span></p></div></div>

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
			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">오늘의 특가 상세(<?=$title?>)</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>오늘의 특가의 예약 및 조르기 현황을 알 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type="hidden" name="type" value="">
				<input type="hidden" name="todayY" value="<?=$todayY?>">
				<input type="hidden" name="todayM" value="<?=$todayM?>">
				<input type="hidden" name="todayD" value="<?=$todayD?>">
<?php
			//예약 / 조르기 내역 가져오는 영역
			$where[] = "a.applydate = '{$today}'";
			$where[] = "a.type='{$type}'";

			$whereStr = " WHERE ".implode(" AND ",$where);

			$sql = "SELECT COUNT(a.*) as t_count FROM tblproductonedaydetail as a".$whereStr;

			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			$t_count = (int)$row->t_count;
			pmysql_free_result($result);
			$paging = new Paging($t_count,10,20);
			$gotopage = $paging->gotopage;				

			$sql = "SELECT a.*,to_char(a.regdate,'YYYY-MM-DD') as reg_date, b.productname FROM tblproductonedaydetail as a ";
			$sql.= "LEFT JOIN tblproduct as b ON a.productcode = b.productcode";
			$sql.= $whereStr;
			$sql = $paging->getSql($sql);
			$result = pmysql_query($sql,get_db_conn());
?>
			<!--정렬부분-->
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">
						<p align="left"><img src="images/icon_8a.gif" border="0" />
						<B><FONT class=font_orange>
							<input type="button" value="문자예약 목록 보기" onclick="GoType('R')">
						</FONT></B> / 
						<B><FONT class=font_orange>
							<input type="button" value="조르기 목록 보기" onclick="GoType('E')">
						</FONT></B> 
						(<?=$today?>)
						</p>
						
					</td>
					
					<!--<B>정렬 : 
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoType('0');"><B><FONT class=font_orange>문자예약</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoType('1');"><B><FONT class=font_orange>조르기</FONT></B></A>
					<?php }?>
					</B></td>-->
					<td width=""><p align="right"><img src="images/icon_8a.gif" border="0">현재 <b>1/<?=floor($paging->pagecount)?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<!--정렬부분 끝-->
			<tr>
				<td>
				
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
                <col width="40" />
                <col width="400" />
                <col width="80" />
                <col width="" />
                <col width="200" />
                <col width="200" />
				<TR>
					<th>No</th>
					<th>상품명</th>
					<th>일자</th>
					<th>ID</th>
					<th>전화번호</th>
					<th>예약/조르기</th>
				</TR>
				<?php
				if($result){
					while($row=pmysql_fetch_object($result)){
				?>
				<TR>
					<td><?=$row->idx?></td>
					<td><?=$row->productname?></td>
					<td><?=$row->reg_date?></td>
					<td><?=$row->id?></td>
					<td><?=$row->mobile?></td>
					<td><?=$typename[$row->type]?></td>
				</TR>
				<?php
					}
				}else{
				?>
				<TR>
					<td colspan="6">등록된 예약/조르기 가 없습니다.</td>
				</TR>
				<?php
				}
				?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr> 
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size"><p align="center">
<?php
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
?>
						</td>
						<td width="100%" class="font_size">
							<a href="javascript:GoList();"><img src="images/btn_list_com.gif"/></a>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			</form>
			<form name=detailform method="post" action="order_tempdetail.php" target="ordertempdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=search_date value="<?=$search_date?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
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
						<!--<dt><span>개월별 상품명 주문조회</span></dt>
						<dd>
							- 결제시도 목록이란 구매자가 주문서를 작성하고 최종 결제단계로 넘어가기 전<br />
							&nbsp;&nbsp;&nbsp;고객의 변심, 네트워크 장애, 구매자 PC 장애, 기타 예기치 못한 문제로 인해 최종 결제완료되지 못한 주문서들의 현황입니다.<br />
							- 결제시도 목록에 등록된 주문건은 적용된 <span class="point_c1">1시간 후</span>에 <span class="point_c1">[자동]</span>으로 해당상품으로 적용되었던 수량/적립금/쿠폰이 원상복구가 됩니다.<span class="point_c1">(권장)</span><br />
							- 결제시도 목록에 등록된 주문건은 <span class="point_c1">10분 후 [수동]</span>으로 복구할 수 있지만, 현재 결제중인 주문일 수 있으므로 권장하지 않습니다.<br />
							- 수동 복구시 해당 주문에 적용되었던 수량/적립금/쿠폰이 원상 복구됩니다.
						</dd>-->
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
<?=$onload?>
<?php 
include("copyright.php");
