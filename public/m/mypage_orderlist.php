<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);


#####날짜 셋팅 부분
$s_year=(int)$_GET["s_year"];
$s_month=(int)$_GET["s_month"];
$s_day=(int)$_GET["s_day"];

$e_year=(int)$_GET["e_year"];
$e_month=(int)$_GET["e_month"];
$e_day=(int)$_GET["e_day"];

$day_division = $_GET['day_division'];
if ($day_division == '') {
	$day_division = '1MONTH';
}

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);

?>
<script>
<!--
var NowTime=parseInt(<?=time()?>);

function GoSearch(gbn) {

	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "12MONTH":
			s_date.setMonth(s_date.getMonth()-12);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));

	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form1.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//

	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;

	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//

	CheckForm();
}

function str_pad_right(num){

	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;

}

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}

function CheckForm() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form1.date1.value;
	if(!isNull(document.form1.date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.s_year.value = sdatearr[0];
			document.form2.s_month.value = sdatearr[1];
			document.form2.s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));

	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form1.date2.value;
	if(!isNull(document.form1.date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.e_year.value = edatearr[0];
			document.form2.e_month.value = edatearr[1];
			document.form2.e_day.value = edatearr[2];
		}
	}

	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}


	document.form2.submit();
}


function OrderDetail(ordercode) {
	document.detailform.ordercode.value=ordercode;
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
-->
</script>
<form name=form2 method=GET action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
</form>

<form name=detailform method=GET action="<?=$Dir.MDir?>mypage_orderlist_view.php">
<input type=hidden name=ordercode>
</form>

<?
	
	$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
	$result=pmysql_query($sql,get_db_conn());
	$delicomlist=array();
	while($row=pmysql_fetch_object($result)) {
		$delicomlist[$row->code]=$row;
	}
	pmysql_free_result($result);

	$s_curtime=strtotime("$s_year-$s_month-$s_day");
	$s_curdate=date("Ymd",$s_curtime);
	$e_curtime=strtotime("$e_year-$e_month-$e_day");
	$e_curdate=date("Ymd",$e_curtime)."999999999999";

	$add_sql	= " AND a.oi_step1 in (0,1,2,3,4) ";
	
	$sql = "SELECT
			a.ordercode,
			min(a.id) as id,
			min(a.price) as price,
			min(a.deli_price) as deli_price,
			min(a.dc_price) as dc_price,
			min(a.reserve) as reserve,
			min(a.point) as point,
			min(a.paymethod) as paymethod,
			min(a.oi_step1) as oi_step1,
			min(a.oi_step2) as oi_step2,
			min(b.productname) as productname,
			min(b.productcode) as productcode,
			min(a.redelivery_type) as redelivery_type,
			min(a.order_conf) as order_conf,
			(Select maximage from tblproduct where productcode = min(b.productcode)) as maximage,
			(Select brandname from tblproduct p left join tblproductbrand pb on p.brand=pb.bridx where p.productcode = min(b.productcode)) as brandname,
			(select sum(option_quantity) from tblorderproduct op where op.ordercode = a.ordercode) quantity,
			(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt,
			(select count(*) from tblorderproduct op2 where op2.store_stock_yn = 'N' and op2.ordercode = a.ordercode) stock_yn_cnt
			FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender
			WHERE a.id='".$_ShopInfo->getMemid()."'
			AND b.option_type = 0 ".$add_sql." And a.oi_step2 in (0,40,41,42,44)
			AND a.ordercode >= '".$s_curdate."' AND a.ordercode <= '".$e_curdate."'
			GROUP BY a.ordercode
			ORDER BY a.ordercode DESC ";

		//echo $sql;
	
	$paging = new New_Templet_mobile_paging($sql, 3,  5, 'GoPage', true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	#$result3=pmysql_query($sql,get_db_conn());
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());

		//exdebug("##");
 ?>


<!-- 내용 -->
<main id="content" class="subpage">
	
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>주문/배송조회</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_orderlist">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="date1" id="" value="<?=$strDate1?>">
		<input type="hidden" name="date2" id="" value="<?=$strDate2?>">

		<div class="check_period">
			<ul>
				<li <?if($day_division == '1MONTH'){?>class="on"<?}?>><a href="javascript:GoSearch('1MONTH');">1개월</a></li><!-- [D] 해당 조회기간일때 .on 클래스 추가 -->
				<li <?if($day_division == '3MONTH'){?>class="on"<?}?>><a href="javascript:GoSearch('3MONTH');">3개월</a></li>
				<li <?if($day_division == '6MONTH'){?>class="on"<?}?>><a href="javascript:GoSearch('6MONTH');">6개월</a></li>
				<li <?if($day_division == '12MONTH'){?>class="on"<?}?>><a href="javascript:GoSearch('12MONTH');">12개월</a></li>
			</ul>
		</div><!-- //.check_period -->
		
		</form>

		<p class="info_msg">※ 취소, 교환, 반품은 주문상세보기 페이지에서 가능합니다.</p>

		<div class="list_myorder">
<?
if ($t_count > 0) {
?>
		<div class="list_myorder">
<?
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

			$ord_date	= substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2);

			$ord_title	= $row->productname;
			$ord_brand	= $row->brandname;
			if ($row->prod_cnt > 1) {
				$ord_title	.= " 외 ".($row->prod_cnt - 1)."건";
			}

			$file = getProductImage($Dir.DataDir.'shopimages/product/', $row->maximage);

            $stock_status = "";
            if($row->stock_yn_cnt > 0) $stock_status = " (재고부족)";
?>
			<!-- 주문별 반복 -->
			<div class="with_deli_info">
				<h3 class="order_title">
					<span>주문번호: <?=$row->ordercode?></span>
					<a href="javascript:OrderDetail('<?=$row->ordercode?>')">상세보기</a>
				</h3>
				<ul class="cart_goods">
					<li>
						<div class="cart_wrap">
							<div class="clear">
								<div class="goods_area">
									<div class="img"><a href="javascript:OrderDetail('<?=$row->ordercode?>')"><img src="<?=$file?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$ord_brand?></p>
										<p class="name"><?=$ord_title?></strong></p>
										<p class="price">￦ <?=number_format($row->price-$row->dc_price-$row->reserve-$row->point+$row->deli_price)?></p>
										<span class="status_tag btn-point h-small"><?=GetStatusOrder("o", $row->oi_step1, $row->oi_step2, "", $row->redelivery_type, $row->order_conf)?></span>
									</div>
								</div>
							</div>
						</div><!-- //.cart_wrap -->
					</li>
				</ul><!-- //.cart_goods -->
			</div>
			<!-- //주문별 반복 -->
<?
		$cnt++;
		}
?>
		<!-- </ul> -->
		</div>
<?
	}
?>
		</div><!-- //.list_myorder -->

		<div class="list-paginate mt-15">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>

		<div class="attention mt-35">
			<h3 class="tit">유의사항</h3>
			<ul class="list">
				<li>[주문상세보기]를 클릭하시면 주문/취소/교환/반품을 하실 수 있습니다.</li>
				<li>결제 전 상태에서는 모든 주문건 취소가 가능하며, 출고 완료된 상품은 반품메뉴를 이용하시기 바랍니다.</li>
				<li>상품 일부만 취소/교환/반품을 원하시는 경우 1:1 문의 또는 고객센터(1661-2585)로 문의 부탁드립니다.</li>
				<li>배송처리 이후 14일이 경과되면 자동 구매확정 처리 되며 교환/반품이 불가능합니다. </li>
				<li>상품하자 또는 오배송으로 인한 교환/반품 신청은 1:1 문의 또는 고객센터(1661-2585)로 문의 부탁 드립니다.</li>
				<li>무통장입금 또는 가상계좌 결제주문의 경우, 환불금액 입금이 3-4일정도 소요됩니다. (영업일기준) </li>
			</ul>
		</div>

	</section><!-- //.my_orderlist -->

</main>
<!-- //내용 -->

<? include_once('outline/footer_m.php'); ?>