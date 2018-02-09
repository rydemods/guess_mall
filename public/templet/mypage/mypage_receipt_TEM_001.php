<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);
?>
<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

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


		$sql = "SELECT
				a.ordercode,
				min(a.id) as id,
				min(a.price) as price,
				min(a.deli_price) as deli_price,
				min(a.dc_price) as dc_price,
				min(a.reserve) as reserve,
				min(a.paymethod) as paymethod,
				min(a.oi_step1) as oi_step1,
				min(a.oi_step2) as oi_step2,
				min(b.productname) as productname,
				min(b.productcode) as productcode,
				min(a.redelivery_type) as redelivery_type,
				min(a.order_conf) as order_conf,
				(Select tinyimage from tblproduct where productcode = min(b.productcode)) as tinyimage,
				(Select brandname from tblproduct p left join tblproductbrand pb on p.brand=pb.bridx where p.productcode = min(b.productcode)) as brandname,
				(select sum(option_quantity) from tblorderproduct op where op.ordercode = a.ordercode) quantity,
				(select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt
				FROM tblorderinfo a join tblorderproduct b on a.ordercode = b.ordercode join tblvenderinfo v on b.vender = v.vender
				WHERE a.id='".$_ShopInfo->getMemid()."'
				AND b.option_type = 0 AND a.oi_step1 in (4) And a.oi_step2 in (0) AND a.order_conf = '1'
				AND a.ordercode >= '".$s_curdate."' AND a.ordercode <= '".$e_curdate."'
				GROUP BY a.ordercode
				ORDER BY a.ordercode DESC ";


		$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
 ?>

<!-- 현금영수증 팝업-->
<div class="layer-dimm-wrap pop-cash-receipt layer-receipt01"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<form action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_cash'>
	<input type = 'hidden' name = 'ordercode'>
	<div class="dimm-bg"></div>
	<div class="layer-inner w500">
		<h3 class="layer-title"><span class="type_txt1">현금영수증</span> 신청</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="receipt_box">
				<p>
					<input type="radio" name="up_tr_code" value="0" id="type1" class="radio-def" checked>
					<label for="type1">개인</label>
					<input type="radio" name="up_tr_code" value="1" id="type2" class="radio-def ml-40">
					<label for="type2">사업자</label>
				</p>
				<p class="mobile">
					<label for="text1">휴대폰 번호</label>
					<input type="text" placeholder="하이픈(-) 없이 입력" name="up_mobile" title="휴대폰번호 입력자리" class="ml-10" style="width:228px;">
				</p>
				<p class="license">
					<label for="text2">사업자 번호</label>
					<input type="text" placeholder="하이픈(-) 없이 입력" name="up_comnum" title="휴대폰번호 입력자리" class="ml-10" style="width:228px;">
				</p>
			</div>
			<div class="btn_wrap mt-40"><a href="javascript:;" class="btn-type1 c1 CLS_submitCash">신청</a></div>
		</div>
	</div>
	</form>
</div>
<!-- // 현금영수증 팝업 -->

<!-- 세금계산서 팝업-->
<div class="layer-dimm-wrap pop-tax-invoice layer-receipt02"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<form action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_tax'>
	<input type = 'hidden' name = 'ordercode'>
	<div class="dimm-bg"></div>
	<div class="layer-inner w500">
		<h3 class="layer-title"><span class="type_txt1">세금계산서</span> 신청</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<table class="th_left">
				<caption></caption>
				<colgroup>
					<col style="width:100px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="">회사명 <span class="required">*</span></label></th>
						<td>
							<input type="text" name="up_company" placeholder="회사명" title="회사명 입력자리" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row">사업자 번호 <span class="required">*</span></th>
						<td>
							<ul class="int_license">
								<li><input type="text" name="up_comnum1" title="사업자 번호 앞자리" maxlength = "3"></li>
								<li><input type="text" name="up_comnum2" title="사업자 번호 중간자리" maxlength = "2"></li>
								<li><input type="text" name="up_comnum3" title="사업자 번호 마지막자리" maxlength = "5"></li>
							</ul>
						</td>
					</tr>
					<tr>
						<th scope="row">대표자명 <span class="required">*</span></th>
						<td><input type="text" name="up_name" placeholder="대표자명" title="대표자명 입력자리" style="width:100%;"></td>
					</tr>
					<tr>
						<th scope="row">업태 <span class="required">*</span></th>
						<td><input type="text" name="up_service" placeholder="업태" title="업태 입력자리" style="width:100%;"></td>
					</tr>
					<tr>
						<th scope="row">종목 <span class="required">*</span></th>
						<td><input type="text" name="up_item" placeholder="종목" title="종목 입력자리" style="width:100%;"></td>
					</tr>
					<tr>
						<th scope="row">사업장 주소 <span class="required">*</span></th>
						<td><input type="text" name="up_address" placeholder="사업장 주소" title="사업장 주소 입력자리" style="width:100%;"></td>
					</tr>
				</tbody>
			</table>
			<p class="s_txt">ㆍ세금계산서는 법인카드만 신청가능</p>
			<div class="btn_wrap mt-40"><a href="javascript:;" class="btn-type1 c1 CLS_submitTax">신청</a></div>
		</div>
	</div>
	</form>
</div>
<!-- // 세금계산서 팝업 -->

<div id="contents">
	 <!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">증명서류 발급</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<section class="mypage_main">
					<div class="title_box_border">
						<h3>증빙서류 발급</h3>
					</div>
					<!-- 증빙서류 리스트 -->
					<div class="order_list_wrap voucher mt-50">
						<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
						<div class="order_right">
							<div class="total">총 <?=number_format($t_count)?>건</div>
							<div class="date-sort clear">
								<div class="type month">
									<p class="title">기간별 조회</p>
								<?
									if(!$day_division) $day_division = '1MONTH';

								?>
								<?foreach($arrSearchDate as $kk => $vv){?>
									<?
										$dayClassName = "";
										if($day_division != $kk){
											$dayClassName = '';
										}else{
											$dayClassName = 'on';
										}
									?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
								<?}?>
								</div>
								<div class="type calendar">
									<p class="title">일자별 조회</p>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$strDate1?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
									<span>-</span>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$strDate2?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
								</div>
								<button type="button" class="btn-go" onClick="javascript:CheckForm();"><span>검색</span></button>
							</div>
						</div>
						</form>
						<table class="th_top">
							<caption></caption>
							<colgroup>
								<col style="width:20%">
								<col style="width:10%">
								<col style="width:auto">
								<col style="width:12%">
								<col style="width:12%">
								<col style="width:12%">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">주문번호</th>
									<th scope="col">주문날짜</th>
									<th scope="col">상품정보</th>
									<th scope="col">결제수단</th>
									<th scope="col">결제금액</th>
									<th scope="col">영수증</th>
								</tr>
							</thead>
							<tbody>
<?
		$cnt=0;
		if ($t_count > 0) {
			while($row=pmysql_fetch_object($result)) {

				list($cash_receipt_count) = pmysql_fetch("select count(ordercode) from tbltaxsavelist where ordercode='".$row->ordercode."'");
				list($tax_receipt_count) = pmysql_fetch("select count(ordercode) from tbltaxcalclist where ordercode='".$row->ordercode."'");
				list($authno) = pmysql_fetch("select authno from tbltaxsavelist where ordercode='".$row->ordercode."'");

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

				$ord_date	= substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2);

				$ord_title	= $row->productname;
				$ord_brand	= $row->brandname;
				if ($row->prod_cnt > 1) {
					$ord_title	.= " 외 ".($row->prod_cnt - 1)."건";
				}

				$file = getProductImage($Dir.DataDir.'shopimages/product/', $row->tinyimage);

?>
								<tr class="bold">
									<td class="order_num"><a href="javascript:OrderDetail('<?=$row->ordercode?>')"><?=$row->ordercode?></a></td>
									<td class="date"><?=$ord_date?></td>
									<td class="goods_info">
										<a href="javascript:OrderDetail('<?=$row->ordercode?>')">
											<img src="<?=$file?>" alt="<?=$ord_title?>">
											<ul>
												<li>[<?=$ord_brand?>]</li>
												<li><?=$ord_title?></li>
											</ul>
										</a>
									</td>
									<td><?=$arpm[$row->paymethod[0]]?></td>
									<td class="payment"><?=number_format($row->price-$row->dc_price-$row->reserve+$row->deli_price)?>원</td>
									<td>
										<div class="btn_voucher">
							<?php
									if($row->paymethod == 'B' || $row->paymethod[0] == 'V' || $row->paymethod[0] == 'O' || $row->paymethod[0] == 'Q'){
										if(!$cash_receipt_count){
							?>
											<p>
											<a href="javascript:;" class="btn-line btn-cash-receipt btn-receipt01" ordercode = '<?=$row->ordercode?>' >
												현금영수증 신청
											</a>
											</p>
							<?php
										}else{
							?>
											<p>
											<a href="javascript:;" class="btn-line  pop_receiptView" ordercode = '<?=$row->ordercode?>||<?=$authno?>' >
												<span>영수증확인</span>
											</a>
											</p>
							<?php
										} // cash_receipt_count if
										if(!$tax_receipt_count){
							?>
											<p>
											<a href="javascript:;" class="btn-line btn-tax-invoice btn-receipt02" ordercode = '<?=$row->ordercode?>' >
												<span>세금계산서 신청</span>
											</a>
											</p>
							<?php
										} // tax_receipt_count if
									}else if( $row->paymethod[0] == 'C' || $row->paymethod[0] == 'P' ){
							?>
											<p>
											<a href="javascript:;" class="btn-line  pop_receiptCardView" ordercode = '<?=$row->ordercode?>' >
												<span>신용카드 매출전표</span>
											</a>
											</p>
							<?php
										if(!$tax_receipt_count){
							?>
											<p>
											<a href="javascript:;" class="btn-line btn-tax-invoice btn-receipt02" ordercode = '<?=$row->ordercode?>' >
												<span>세금계산서 신청</span>
											</a>
											</p>
							<?php
										} // tax_receipt_count if
									} else {
							?>
											-
							<?php
									}
							?>
										</div>
									</td>
								</tr>
<?
		$cnt++;
		}
	} else {
?>
								<tr>
									<td colspan="6">내역이 없습니다.</td>
								</tr>
<?
	}
?>
							</tbody>
						</table>
						<div class="list-paginate mt-30">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
						</div>
					</div>
					<!-- // 증빙서류 리스트 -->

					<!-- 안내 -->
					<div class="list_text">
						<h3>유의사항</h3>
						<ul>
							<li>ㆍ 구매확정 후 48시간 이내에 현금영수증 정보가 국세청으로 이관된 후 증빙서류로 출력이 가능합니다</li>
							<li>ㆍ 이벤트 성격으로 지급된 Action 포인트의 경우 일부 현금영수증 발행 대상 금액에서 제외되며 결제금액과 상이할 수 있습니다</li>
							<li>ㆍ 구매확정 이후 현금영수증 발행 정보를 전달하므로 국세청 사이트에서는 즉시 확인이 되지 않을 수 있습니다</li>
							<li>ㆍ 휴대폰 결제금액은 증빙서류 발급에서 제외됩니다 (현금영수증은 휴대폰 요금을 현금납부하는 경우에만 해당 이동통신사에서 발급합니다)</li>
							<li>ㆍ 부분취소 발생 시 취소금액이 적용되어 증빙 금액이 변경될 수 있습니다</li>
						</ul>
					</div>
					<!-- // 안내 -->

				</section>
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<? include($Dir."admin/calendar_join.php");?>