<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php?chUrl=".getUrl()."');</script>");
	exit;
}

//PG key 값 가져오기
$_ShopInfo->getPgdata();
$pgid_info=GetEscrowType($_data->card_id);

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
if ($day_division == '') $day_division = '1MONTH';

$ord_step = $_GET['ord_step'];
$oc_status = $_GET['oc_status'];
if ($oc_status == '') $oc_status = 'R';

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

function store_map(storecode){
	if( storecode ){
		$.ajax({
			cache: false,
			type: 'POST',
			url: 'ajax_store_map.php',
			data : { storecode : storecode },
			success: function(data) {
				$(".store_view").html(data);
				$('.pop-infoStore').show();
			//	$('html,body').css('position','fixed');
			}
		});
	}
	
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
<input type=hidden name=ord_step value="<?=$ord_step?>">
<input type=hidden name=oc_status value="<?=$oc_status?>">
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
		$s_curdate=date("Ymd",$s_curtime)."000000";
		$e_curtime=strtotime("$e_year-$e_month-$e_day");
		$e_curdate=date("Ymd",$e_curtime)."999999";

		$add_query	= "
					AND b.op_step IN ('44') ";
		# 취소/교환/반품/환불 대기
		$sql = "SELECT 
					oc.oc_no,
					a.ordercode,
					min(a.id) as id,
					min(a.paymethod) as paymethod,
					min(a.oi_step1) as oi_step1,
					min(a.oi_step2) as oi_step2,
					min(b.redelivery_type) as redelivery_type,
					min(b.op_step) as op_step,
					min(oc.regdt) as regdt,
					min(oc.cfindt) as cfindt,
					min(oc.rfee) as rfee,
					min(oc.rprice) as rprice,
					min(a.receiver_addr) as receiver_addr,
					SUM(b.price) as price,
					SUM(b.option_price) as option_price, 
					SUM(b.option_quantity) as option_quantity,   
					SUM(b.deli_price) as deli_price, 
					SUM(((b.price+b.option_price) * b.option_quantity)+b.deli_price) as tot_price
					FROM
					tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode left join tblorder_cancel oc ON b.oc_no=oc.oc_no
					join tblvenderinfo v on b.vender = v.vender
					WHERE 1=1
					AND a.id = '".$_MShopInfo->getMemid()."'
					AND oc.regdt >= '".$s_curdate."' AND oc.regdt <= '".$e_curdate."' {$add_query} 
					GROUP BY oc.oc_no, a.ordercode ORDER BY oc.oc_no DESC";

		//echo $sql;

		$paging = new New_Templet_mobile_paging($sql, 5,  5, 'GoPage', true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		#$result3=pmysql_query($sql,get_db_conn());
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());

		//exdebug("##");
 ?>



<!-- 내용 -->
<main id="content" class="subpage">
	
	<!-- 매장안내 팝업 -->
	<section class="pop_layer layer_store_info">
		<div class="inner">
			<h3 class="title">매장 위치정보 <button type="button" class="btn_close">닫기</button></h3>
			<div class="select_store store_view">
				
			</div><!-- //.select_store -->
		</div>
	</section>
	<!-- //매장안내 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>취소/교환/반품 현황</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_cancellist">
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

		<div class="list_myorder mt-15">
<?
		if($t_count > 0){
?>
			<div class="list_myorder">
<?
			while($row=pmysql_fetch_object($result)) {

					$reg_dt	= substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2);
?>
			<!-- 주문별 반복 -->
			<div class="with_deli_info">
				<h3 class="order_title">
					<span>주문번호: <?=$row->ordercode?></span>
					<a href="javascript:OrderDetail('<?=$row->ordercode?>')">상세보기</a>
				</h3>
				<ul class="cart_goods">
<?
					$orProduct	= null;
					$orvender	= null;

					$sql2		 = "SELECT op.*, p.tinyimage,p.consumerprice,p.prodcode,p.colorcode, pb.brandname FROM tblorderproduct op left join tblproduct p ON op.productcode=p.productcode left join tblproductbrand pb on p.brand=pb.bridx ";
					$sql2		.= "WHERE op.ordercode='".$row->ordercode."' AND op.oc_no='".$row->oc_no."' ";
					$sql2		.= "/*AND op.option_type = 0*/ order by vender, productcode";

					//echo $sql2;

					$result2	=pmysql_query($sql2,get_db_conn());

					while($row2=pmysql_fetch_object($result2)){
						# 상품정보
						$orProduct[$row2->idx] = (object) array(
							'vender' => $row2->vender,
							'brandname' => $row2->brandname,
							'productcode' => $row2->productcode,
							'productname' => $row2->productname,
							'consumerprice' => $row2->consumerprice,
							'price' => $row2->price,
							'tinyimage' => $row2->tinyimage,
							'opt1_name' => $row2->opt1_name,
							'opt2_name' => $row2->opt2_name,
							'text_opt_subject' => $row2->text_opt_subject,
							'text_opt_content' => $row2->text_opt_content,
							'option_quantity' => $row2->option_quantity,
							'redelivery_type' => $row2->redelivery_type,
							'op_step' => $row2->op_step,
							'redelivery_reason' => $row2->redelivery_reason,
							'delivery_type' => $row2->delivery_type,
							'store_code' => $row2->store_code,
							'reservation_date' => $row2->reservation_date,
							'prodcode' => $row2->prodcode,
							'colorcode' => $row2->colorcode
						);
					}
					pmysql_free_result($result2);

					//exdebug($orProduct);
					$pr_cnt=0;

					foreach( $orProduct as $pr_idx=>$prVal ) { // 상품
						
						$file = getProductImage($Dir.DataDir.'shopimages/product/', $prVal->tinyimage);

						$optStr	= "";
						$optPum="";
						$option1	 = $prVal->opt1_name;
						$option2	 = $prVal->opt2_name;
						$tmp_opt_price = $prVal->option_price * $prVal->quantity;

						if($prVal->prodcode){
							$optPum	.= "품번 : ".$prVal->prodcode;
						}
						if($prVal->colorcode){
							$optStr	.= "색상 : ".$prVal->colorcode;
						}

						if( strlen( trim( $prVal->opt1_name ) ) > 0 ) {
							$opt1_name_arr	= explode("@#", $prVal->opt1_name);
							$opt2_name_arr	= explode(chr(30), $prVal->opt2_name);
							for($g=0;$g < sizeof($opt1_name_arr);$g++) {
								if ($g >= 0) $optStr	.= " / ";
								$optStr	.= ''.$opt1_name_arr[$g].' : '.$opt2_name_arr[$g].'';
							}
						}

						if( strlen( trim( $prVal->text_opt_subject ) ) > 0 ) {
							$text_opt_subject_arr	= explode("@#", $prVal->text_opt_subject);
							$text_opt_content_arr	= explode("@#", $prVal->text_opt_content);

							for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
								if ($text_opt_content_arr[$s]) {
									if ($optStr != '') $optStr	.= " / ";
									$optStr	.= ''.$text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s].'';
								}
							}
						}

						if( $tmp_opt_price > 0 ) $optStr	 .= '<span>&nbsp;( + '.number_format( $tmp_opt_price ).'원)</span>';
						if ($optStr !='') $optStr	 .= ' / ';
						$optStr	 .= number_format( $prVal->option_quantity )."개";

						$storeData = getStoreData($prVal->store_code);
	?>
					<!-- 상품 반복 -->
					<li>
						<div class="cart_wrap">
							<div class="clear">
								<div class="goods_area">
									<div class="img"><a href="javascript:OrderDetail('<?=$row->ordercode?>')"><img src="<?=$file?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$prVal->brandname?></p>
										<p class="name"><?=$prVal->productname?></p>
										<p class="option"><?=$optPum?></p>
										<p class="option"><?=$optStr?></p>
										<p class="price">￦ <?=number_format($prVal->price)?></p>
										<span class="status_tag btn-line h-small"><?=GetStatusOrder("p", $row->oi_step1, $row->oi_step2, $row->op_step, $row->redelivery_type)?></span><!-- [D] 관리자에서 [반품완료/교환완료] 전까지 모든 프로세스는 신청페이지에 노출됨 -->
									</div>
								</div>
							</div>
						</div><!-- //.cart_wrap -->
						<?if($prVal->delivery_type == '1' || $prVal->delivery_type == '3'){?>
						<div class="delibox">
							<h4 class="cart_tit">
								<?=$arrDeliveryType[$prVal->delivery_type]?>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($prVal->delivery_type == '1'){?>
											<div class="container"><p><?=$prVal->reservation_date?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </p></div>
											<?}else if($prVal->delivery_type == '3'){?>
											<div class="container"><p>선택하신 상품은 당일수령이 가능한 상품입니다. </p></div>
											<?}?>
											
										</div>
									</div>
								</div><!-- //.wrap_bubble -->
							</h4>
							<div class="change_store">
								<?if($prVal->delivery_type == '1'){?>
								<span class="store_name"><?=$storeData['name']?> (<?=$prVal->reservation_date?>)</span>
								<?}else if($prVal->delivery_type == '3'){?>
								<span class="store_name"><?=$storeData['name']?></span>
								<?}?>
								<a href="javascript:store_map('<?=$prVal->store_code?>');" class="btn_store_info btn-basic">매장안내</a>
							</div>
						</div><!-- //.delibox -->
						<?}else{?>
						<div class="delibox">
							<h4 class="cart_tit">
								택배수령
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div><!-- //.wrap_bubble -->
							</h4>
						</div><!-- //.delibox -->
						<?}?>
					</li>
					<!-- //상품 반복 -->
					<?}?>
				</ul><!-- //.cart_goods -->
			</div>
			<!-- //주문별 반복 -->
			<?
					
				$cnt++;
			}
		} else {
?>
			<div class="with_deli_info">
				<h3 class="order_title" style="text-align:center;">
					<span >내역이 없습니다.</span>
				</h3>
			</div>
<?
		}
?>
		</div><!-- //.list_myorder -->

		<div class="list-paginate mt-15">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>

	</section><!-- //.my_cancellist -->

</main>
<!-- //내용 -->

<? include_once('outline/footer_m.php'); ?>
