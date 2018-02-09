<?
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

$r_type = $_GET['r_type'];

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
function GoSearch(gbn, obj) {

	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			s_date.setDate(s_date.getDate()-7);
			break;
		case "2WEEK":
			s_date.setDate(s_date.getDate()-14);
			break;
		case "3WEEK":
			s_date.setDate(s_date.getDate()-21);
			break;
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
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
				AND b.option_type = 0 And a.oi_step1 in (4) And a.oi_step2 in (0) AND a.order_conf = '1'
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


	<!-- 현금영수증 신청 레이어팝업 -->
	<div class="layer-dimm-wrap layer_cash_receipt layer-receipt01">
	<form action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_cash'>
	<input type = 'hidden' name = 'ordercode'>
	<input type = 'hidden' name = 'up_tr_code' value='1'>
	<input type = 'hidden' name = 'up_comnum'>
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">현금영수증 신청</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content wrap_receipt">

				<ul class="tabmenu_cancellist clear">
					<li class="idx-menu on tr_code_change" tr_code='1'>개인</li>
					<li class="idx-menu tr_code_change" tr_code='2'>사업자</li>
				</ul>

				<div class="idx-content on">
					<div class="order_table">
						<table class="my-th-left form_table">
							<colgroup>
								<col style="width:30%;">
								<col style="width:70%;">
							</colgroup>
							<tbody>
								<tr>
									<th>휴대폰번호</th>
									<td><input type="tel" name="up_mobile" class="" placeholder="하이픈(-)없이 입력"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn-point CLS_submitCash">신청</button>
				</div>

				<div class="idx-content">
					<div class="order_table">
						<table class="my-th-left form_table">
							<colgroup>
								<col style="width:30%;">
								<col style="width:70%;">
							</colgroup>
							<tbody>
								<tr>
									<th>사업자번호</th>
									<td class="input_tel"><input type="tel" name="up_comnum1"><span class="dash">-</span><input type="tel" name="up_comnum2"><span class="dash">-</span><input type="tel" name="up_comnum3"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<button type="button" class="btn-point CLS_submitCash">신청</button>
				</div>

			</div>
		</div>
	</form>
	</div><!-- //.layer_cash_receipt -->
	<!-- //현금영수증 신청 레이어팝업 -->

	<!-- 세금계산서 신청 레이어팝업 -->
	<div class="layer-dimm-wrap layer_tax_invoice layer-receipt02">
	<form action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_tax'>
	<input type = 'hidden' name = 'ordercode'>
		<div class="dimm-bg"></div>
		<div class="layer-inner">
			<h3 class="layer-title">세금계산서 신청</h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content wrap_receipt">
				<div class="order_table">
					<table class="my-th-left form_table">
						<colgroup>
							<col style="width:30%;">
							<col style="width:70%;">
						</colgroup>
						<tbody>
							<tr>
								<th>회사명 <span class="point-color">*</span></th>
								<td><input type="text" name="up_company"></td>
							</tr>
							<tr>
								<th>사업자번호 <span class="point-color">*</span></th>
								<td class="input_tel"><input type="tel" name="up_comnum1"><span class="dash">-</span><input type="tel" name="up_comnum2"><span class="dash">-</span><input type="tel" name="up_comnum3"></td>
							</tr>
							<tr>
								<th>대표자명 <span class="point-color">*</span></th>
								<td><input type="text" name="up_name"></td>
							</tr>
							<tr>
								<th>업태 <span class="point-color">*</span></th>
								<td><input type="text" name="up_service"></td>
							</tr>
							<tr>
								<th>종목 <span class="point-color">*</span></th>
								<td><input type="text" name="up_item"></td>
							</tr>
							<tr>
								<th>사업장주소 <span class="point-color">*</span></th>
								<td><input type="text" name="up_address"></td>
							</tr>
						</tbody>
					</table>

					<ul class="list_notice">
						<li>세금계산서는 법인카드만 신청이 가능합니다.</li>
					</ul>
				</div>
				<button type="button" class="btn-point CLS_submitTax">신청</button>
			</div>
		</div>
	</form>
	</div><!-- //.layer_tax_invoice -->
	<!-- //세금계산서 신청 레이어팝업 -->

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>mypage.php" class="prev"></a>
			<span>증빙서류 발급</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
	<div class="mypage_sub">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="date1" id="" value="<?=$strDate1?>">
		<input type="hidden" name="date2" id="" value="<?=$strDate2?>">
		<ul class="category_faq clear">
			<li <?if($day_division == '1MONTH'){?>class="on"<?}?>><button type="button" onClick = "GoSearch('1MONTH', this)">1개월</button></li>
			<li <?if($day_division == '3MONTH'){?>class="on"<?}?>><button type="button" onClick = "GoSearch('3MONTH', this)">3개월</button></li>
			<li <?if($day_division == '6MONTH'){?>class="on"<?}?>><button type="button" onClick = "GoSearch('6MONTH', this)">6개월</button></li>
			<li <?if($day_division == '12MONTH'){?>class="on"<?}?>><button type="button" onClick = "GoSearch('12MONTH', this)">12개월</button></li>
		</ul>
		</form>
<?
if ($t_count > 0) {
?>
		<div class="list_myorder">
<?
		$cnt=0;
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
			<div class="box_mylist">
				<div class="title">
					주문날짜 : <?=$ord_date?> <span class="state point-color"><?=$arpm[$row->paymethod[0]]?></span>
				</div>
				<div class="content">
					<a href="javascript:OrderDetail('<?=$row->ordercode?>')">
						<figure class="mypage_goods">
							<div class="img"><img src="<?=$file?>" alt="<?=$ord_title?>"></div>
							<figcaption>
								<p class="brand">[<?=$ord_brand?>]</p>
								<p class="name"><?=$ord_title?></p>
								<p class="price"><span class="point-color"><?=number_format($row->price-$row->dc_price-$row->reserve+$row->deli_price)?>원</span> / <span class="ea"><?=number_format($row->quantity)?>개</span></p>
							</figcaption>
						</figure>
					</a>
					<div class="btnwrap">
						<ul class="ea<?=!$tax_receipt_count?'2':'1'?>"><!-- [D] 버튼이 두개인 경우 ul에 ea2 클래스 -->
			<?php
					if($row->paymethod == 'B' || $row->paymethod[0] == 'V' || $row->paymethod[0] == 'O' || $row->paymethod[0] == 'Q'){
						if(!$cash_receipt_count){
			?>
							<li><button type="button" class="btn-def btn_cash_receipt btn-receipt01" ordercode = '<?=$row->ordercode?>' >
								현금영수증</button></li>
			<?php
						}else{
			?>
							<li><button type="button" class="btn-def  pop_receiptView" ordercode = '<?=$row->ordercode?>||<?=$authno?>' >
								영수증확인</button></li>
			<?php
						} // cash_receipt_count if
						if(!$tax_receipt_count){
			?>
							<li><button type="button" class="btn-def btn_tax_invoice btn-receipt02" ordercode = '<?=$row->ordercode?>' >
								세금계산서</button></li>
			<?php
						} // tax_receipt_count if
					}else if( $row->paymethod[0] == 'C' || $row->paymethod[0] == 'P' ){
			?>
							<li><button type="button" class="btn-def  pop_receiptCardView" ordercode = '<?=$row->ordercode?>' >
								매출전표</button></li>
			<?php
						if(!$tax_receipt_count){
			?>
							<li><button type="button" class="btn-def btn_tax_invoice btn-receipt02" ordercode = '<?=$row->ordercode?>' >
								세금계산서</button></li>
			<?php
						} // tax_receipt_count if
					} else {
			?>
			<?php
					}
			?>
						</ul>
					</div>
				</div>
			</div>
<?
		$cnt++;
		}
?>
		<!-- </ul> -->
		</div>

		<!-- 페이징 -->
		<div class="list-paginate mt-10 mb-30">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div>
		<!-- //페이징 -->
<?
} else {
?>
		<!-- 내역 없는경우 -->
		<div class="none-ment margin">
			<p>내역이 없습니다.</p>
		</div><!-- //내역 없는경우 -->


<?
}
?>
	</div><!-- //.mypage-wrap -->

<SCRIPT>
$(document).ready(function(){

	$(document).on("click", ".tr_code_change", function(){
		$("input[name=up_tr_code]").val($(this).attr('tr_code'));
	})

	$(document).on("click", ".CLS_submitCash", function(){
		//$('div.layer-receipt01 form').submit();  
		var fd = new FormData($('div.layer-receipt01 form')[0]);
		$.ajax({
			type: "POST",
			url: "../m/mypage_receipt.indb.php",
            data: fd,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
			error:function(request,status,error){
				//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		}).success(function(data){
            var arrTmp = data.split("||");
			var flagResult	= arrTmp[0];
			var msg	= arrTmp[1];

			alert(msg);
			if(flagResult == 'SUCCESS') {
				location.reload();
			}
		});
	})
	$(document).on("click", ".CLS_submitTax", function(){
		//$('div.layer-receipt02 form').submit();  
		var fd = new FormData($('div.layer-receipt02 form')[0]);
		$.ajax({
			type: "POST",
			url: "../m/mypage_receipt.indb.php",
            data: fd,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
			error:function(request,status,error){
				//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		}).success(function(data){
            var arrTmp = data.split("||");
			var flagResult	= arrTmp[0];
			var msg	= arrTmp[1];

			alert(msg);
			if(flagResult == 'SUCCESS') {
				location.reload();
			}
		});
	})
		
	$('button.pop_receiptView').click(function(){
		var arrOrderCode = $(this).attr('ordercode').split("||");
		var receiptWin = "mypage_receipt.pop.php?orderid="+arrOrderCode[0]+"&authno="+arrOrderCode[1]+"&mode=01";
		window.open(receiptWin , "receipt_pop" , "width=360, height=647");
	});

	$('button.btn-receipt01').click(function(){
		$("div.layer-receipt01 form input[name='ordercode']").val( $(this).attr('ordercode') );
	});
	$('button.btn-receipt02').click(function(){
		$("div.layer-receipt02 form input[name='ordercode']").val( $(this).attr('ordercode') );
	});
	$('button.close').click(function(){
		$('div.pop_receipt_cash , div.pop_receipt_tax').css('display','none');
		$("div.layer-receipt01 form input[name='ordercode']").val('');
		$("div.layer-receipt02 form input[name='ordercode']").val('');
	});

	$('button.pop_receiptCardView').click( function() {
		var orderCode = $(this).attr('ordercode');
		var receiptWin = "mypage_receipt.pop.php?orderid="+orderCode+"&mode=02";
		window.open(receiptWin , "receipt_pop" , "width=360, height=647");
	});
});
</SCRIPT>
<? include_once('outline/footer_m.php'); ?>