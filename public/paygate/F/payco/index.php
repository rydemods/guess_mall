<?php
	//-----------------------------------------------------------------------------
	// PAYCO 주문 페이지 샘플  ( PHP EASYPAY / PAY1 )
	// 2016-02-16	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------
	include("payco_config.php");

	//-----------------------------------------------------------------------------
	// 테스트용 고객 주문 번호 생성
	//-----------------------------------------------------------------------------
	//$customerOrderNumber = "TEST2016".str_replace("-","",date("Y-m-d")).substr("000000".mt_rand(0,999999), -6);
	// 결제완료 후 에도 주문예약번호 자동생성 가능하도록 임시 로 function callPaycoUrl()에 구현되어 주석처리.

	//echo "isMobile >>> ".$isMobile;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">

<meta name="keyword" content="컨텐츠">

<title>PAYCO_DEMOWEB (PHP EasyPay PAY1)</title>

<link href="css/common.css" rel="stylesheet" type="text/css">

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<!--
<script src="/share/js/requirejs/require.js"></script>
<script src="/share/js/requirejs/require.config.js"></script>
-->
<script type="text/javascript" src="https://static-bill.nhnent.com/payco/checkout/js/payco.js" charset="UTF-8"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">

function order_chk(){
	if($(".left input:radio[name=sort]:checked").val() == null){
		alert("결제방식을 선택하세요.");		
		return;
	}else{
		if($(".left input:radio[name=sort]:checked").val() == "payco"){
			callPaycoUrl();
			return;
		}else{
			alert($(".left input:radio[name=sort]:checked").val());
			return;
		}	
	}
}

function callPaycoUrl(){

	var randomStr = "";
	var randomStrCart = ""; 
	
	for(var i=0;i<10;i++){
		randomStr += Math.ceil(Math.random() * 9 + 1);
	}
	
	var customerOrderNumber = "TEST2016" + randomStr;               // ( 결제완료 후 에도 주문예약번호 자동생성 가능하도록 임시 추가 ) 가맹점 고객 주문번호 입력
	//var Params = "customerOrderNumber=<?=$customerOrderNumber?>";	// 가맹점 고객 주문번호 입력

	for(var j=0;j<5;j++){
		randomStrCart += Math.ceil(Math.random() * 9 + 1);
	}
	
	var cartNo = "CartNo_" + randomStrCart;                         // 장바구니 번호
	
    // localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류 
    $.support.cors = true;

	/* + "&" + $('order_product_delivery_info').serialize() ); */
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>/payco_reserve.php",

		//data: Params,		// JSON 으로 보낼때는 JSON.stringify(customerOrderNumber)
		data:{"customerOrderNumber":customerOrderNumber, "cartNo":cartNo},
		
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				//console.log(data.result.reserveOrderNo);	 // 주석 해제시, 일부 웹브라우저 에서 PAYCO 결제창이 뜨지 않습니다. 			
				$('#order_num').val(data.result.reserveOrderNo);
				$('#order_url').val(data.result.orderSheetUrl);	
				$("#reserveOrderNo_detail").val(data.result.reserveOrderNo);    // 결제상세 조회 (검증용) 입력창에 삽입_PAYCO 주문예약번호 ( reserveOrderNo )
				$("#sellerOrderReferenceKey_detail").val(customerOrderNumber);	// 결제상세 조회 (검증용) 입력창에 삽입_외부가맹점에서 발급하는 주문 연동 Key ( sellerOrderReferenceKey )		
			}
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}

function order(){
	
	var order_Url = $('#order_url').val();

	var isMobile = <?=$isMobile?>;
	
	if(isMobile == 0){
		location.href = order_Url;
	}else{
		window.open(order_Url, 'popupPayco', 'top=100, left=300, width=727px, height=512px, resizble=no, scrollbars=yes'); 
	}
}

function order_state_modify(){
    // 선택박스 필수 옵션을 체크 함
	if( $('#orderNo').val() == "" ) {
        alert('주문번호를 입력해주세요.');
        return false;
	}

	if( $('#sellerOrderProductReferenceKey').val() == "" ) {
        alert('주문상품연동키를 입력해주세요.');
        return false;
	}

	if( $('#orderProductStatus option:selected').val() == "" ) {
        alert('상태값을 선택해주세요.');
        return false;
    }

    // 선택박스 필수 옵션을 체크 함
    var Params = "sellerOrderProductReferenceKey="
			   + $('#sellerOrderProductReferenceKey').val()
			   + "&orderProductStatus="
			   + $('#orderProductStatus option:selected').val()
			   + "&orderNo="
			   + $('#orderNo').val();
    
	//alert($('#sellerOrderProductReferenceKey').val());
	//alert(Params);

	// localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류
    $.support.cors = true;

	/* + "&" + $('order_product_delivery_info').serialize() ); */
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>payco_upstatus.php",
		data: Params,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				alert("변경되었습니다.");
			} else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			}
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}

function cancel_order_all_test(){
    // 선택박스 필수 옵션을 체크 함
	if( $('#orderNo_all').val() == "" ) {
        alert('주문번호를 입력해주세요.');
        return false;
	}

	if( $('#cancelTotalAmt_all').val() == "" ) {
        alert('취소할 주문서의 총 취소 금액을 입력해주세요.');
        return false;
	}

    // 선택박스 필수 옵션을 체크 함
    var Params = "cancelType=ALL" 
			   + "&orderCertifyKey="
			   + encodeURIComponent($('#orderCertifyKey_all').val())	
			   + "&cancelTotalAmt="
			   + $('#cancelAmt_all').val()
			   + "&orderNo="
			   + $('#orderNo_all').val()
			   + "&totalCancelTaxfreeAmt="
			   + $('#totalCancelTaxfreeAmt_all').val()
			   + "&totalCancelTaxableAmt="
			   + $('#totalCancelTaxableAmt_all').val()
			   + "&totalCancelVatAmt="
			   + $('#totalCancelVatAmt_all').val()
               + "&requestMemo="
			   + $('#requestMemo_all').val()
			   + "&totalCancelPossibleAmt="
			   + $('#totalCancelPossibleAmt_all').val();

	//alert(Params);

	// localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류
    $.support.cors = true;

	/* + "&" + $('order_product_delivery_info').serialize() ); */
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>payco_cancel.php",
		data: Params,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				if(data.result.cancelPossibleYn == "N"){
					alert(data.result.cancelImpossibleReason+"\n이미 취소되었는지 확인하세요.");
				} else {
					alert("주문이 정상적으로 취소되었습니다.\n( 주문취소번호 : "+data.result.cancelTradeSeq+" )");
				}
			} else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			}
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}

function cancel_order_part_test(){
    // 선택박스 필수 옵션을 체크 함
	if( $('#orderNo_part').val() == "" ) {
        alert('주문번호를 입력해주세요.');
        return false;
	}

	if( $('#cancelTotalAmt_part').val() == "" ) {
        alert('취소할 상품이 포함된 주문서의 총 주문금액을 입력해주세요.');
        return false;
	}

	if( $('#sellerOrderProductReferenceKey_part').val() == "" ) {
        alert('주문서에서 취소할 상품의 ID를 입력해주세요.');
        return false;
	}

	if( $('#cancelTotalAmt_part').val() == "" ) {
        alert('주문서에서 취소할 상품의 금액을 입력해주세요.');
        return false;
	}
// 

    // 선택박스 필수 옵션을 체크 함
   var Params = "cancelType=PART" 
			   + "&orderCertifyKey="
			   + encodeURIComponent($('#orderCertifyKey_part').val())				   
			   + "&cancelTotalAmt="
			   + $('#cancelTotalAmt_part').val()
			   + "&sellerOrderProductReferenceKey="
			   + $('#sellerOrderProductReferenceKey_part').val()
			   + "&cancelAmt="
			   + $('#cancelAmt_part').val()
			   + "&orderNo="
			   + $('#orderNo_part').val()
			   + "&totalCancelTaxfreeAmt="
			   + $('#totalCancelTaxfreeAmt_part').val()
			   + "&totalCancelTaxableAmt="
			   + $('#totalCancelTaxableAmt_part').val()
			   + "&totalCancelVatAmt="
			   + $('#totalCancelVatAmt_part').val()
			   + "&totalCancelPossibleAmt="
			   + $('#totalCancelPossibleAmt_part').val()
               + "&requestMemo="
			   + $('#requestMemo_part').val()
			   + "&cancelDetailContent="
			   + $('#cancelDetailContent_part').val();	

	//alert(Params);

	// localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류
    $.support.cors = true;

	/* + "&" + $('order_product_delivery_info').serialize() ); */
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>payco_cancel.php",
		data: Params,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				//{"result":{"cancelPossibleYn":"N","partCancelPossibleYn":"N","pgCancelPossibleAmt":0.0,"cancelImpossibleReason":"취소 결제금액은 0보다 커야합니다.","orderNo":"201503172000160701"},"code":0,"message":"success"}
				if ( data.result.partCancelPossibleYn == "N" ){
					alert(data.result.cancelImpossibleReason);
				} else {
					alert("주문이 정상적으로 취소되었습니다.\n( 주문취소번호 : "+data.result.cancelTradeSeq+" / 취소상품금액 : "+data.result.totalCancelPaymentAmt+" )");
				}
			} else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			}
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}

function mileage_cancel_test(){
    // 선택박스 필수 옵션을 체크 함
	if( $('#orderNo_mile').val() == "" ) {
        alert('주문번호를 입력해주세요.');
        return false;
	}

	if( $('#cancelPaymentAmount_mile').val() == "" ) {
        alert('취소할 주문서의 총 취소 금액을 입력해주세요.\n(마일리지 적립율을 곱한 금액이 취소됩니다.)');
        return false;
	}

    // 선택박스 필수 옵션을 체크 함
    var Params = "orderNo="
			   + $('#orderNo_mile').val()
			   + "&cancelPaymentAmount="
			   + $('#cancelPaymentAmount_mile').val();

	//alert(Params);

	// localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류
    $.support.cors = true;

	/* + "&" + $('order_product_delivery_info').serialize() ); */
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>payco_mileage_cancel.php",
		data: Params,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				if(data.result.cancelPossibleYn == "N"){
					alert(data.result.cancelImpossibleReason);
				} else {
					alert("주문이 정상적으로 취소되었습니다.\n( 취소 마일리지 : "+data.result.canceledMileageAcmAmount+", 잔여 마일리지 : "+data.result.remainingMileageAcmAmount+" )");
				}
			} else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			}
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}


function receipt_go(){
	if($(".payco input:radio[name=receipt]:checked").val() == null){
		alert("출력할 영수증을 선택하세요.");		
		return;
	}
	var orderurl = "https://alpha-bill.payco.com/outseller/receipt/"+$('#orderNo_receipt').val()+"?receiptKind="+$(".payco input:radio[name=receipt]:checked").val();
	window.open(orderurl, 'payco_receipt');
}

//결제상세 조회(검증용)API 호출
function detailForVerify(){
    // 선택박스 필수 옵션을 체크 함
	if( $('#sellerKey_detail').val() == "" ) {
        alert('가맹점키 를 입력해주세요.');
        return false;
	}

	if( $('#reserveOrderNo_detail').val() == "" ) {
        alert('PAYCO 주문예약번호 를 입력해주세요.');
        return false;
	}

	if( $('#sellerOrderReferenceKey_detail').val() == "" ) {
        alert('외부가맹점에서 발급하는 주문 연동 Key 를 입력해주세요.');
        return false;
	}
	

    // 선택박스 필수 옵션을 체크 함
   var Params = "sellerKey="
			   + $('#sellerKey_detail').val()
			   + "&reserveOrderNo="
			   + $('#reserveOrderNo_detail').val()
			   + "&sellerOrderReferenceKey="
			   + $('#sellerOrderReferenceKey_detail').val();
			   

	//alert(Params);

	// localhost 로 테스트 시 크로스 도메인 문제로 발생하는 오류
    $.support.cors = true;
	
	$.ajax({
		type: "POST",
		url: "<?=$AppWebPath?>payco_detailForVerify.php",
		data: Params,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			   		 			     
			 if(data.code == '0') {				

				alert("결제상세(검증용) 내용이 정상적으로 조회 되었습니다.");

				detail_write_info('조회 결과', JSON.stringify(data, null, 4));
								
			} else {
				alert("code:"+data.code+"\n"+"message:"+data.message);
			} 			
		},
        error: function(request,status,error) {
            //에러코드
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			return false;
        }
	});
}


//예약 정보 조회 결과 화면에 출력
function detail_write_info(title, content) {
	$("#list_detail_info").empty();                      // 화면 초기화
    $("#list_detail_info").append("<div class='header'>" + title + ":</div><div><pre>" + content + "</pre></div>");
}

</script>

</head>
<body>

<div id="header">
	<div class="gnb" id="gognb">
		<div class="wrap">
			<ul class="gognb" >
				<li><h3>PHP 간편결제(EASYPAY - PAY1)</h3></li>
			</ul>
		</div>
	</div>
</div>

<div id="container" class="clearfix">
	<div class="main_fix_wrap easyPay_wrap">

		<table cellspacing="0" cellpadding="0" class="tbl_std">
			<colgroup>
				<col width="9%">
				<col width="46%">
				<col width="10%">
				<col width="10%">
				<col width="10%">
				<col width="15%">
			</colgroup>
			<thead>
				<tr>
					<th colspan="2" class="fst left">상품정보</th>
					<th>수량</th>
					<th>상품금액</th>
					<th>적립금</th>
					<th>주문금액</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="fst"><img src="http://image.popshoes.co.kr/images/goods_img/20150127/115312/115312_a_500.jpg?20150213114102"	alt="나이키 우먼스 덩크 스카이 하이 에센셜 (NIKE WMNS DUNK SKY HI ESSENTIAL) 644877 010" width="80" height="80"></td>
					<td class="left">
						<p>아디다스 위네오 슈퍼 웨지 (ADIDAS  WENEO SUPER WEDGE) F38577</p>
						<p>옵션 : 245</p>
					</td>
					<td>1</td>
					<td>
						<p>100,000 원</p>
					</td>
					<td class="bg_sum">0원</td>
					<td class="bg_sum txt_sum text_bold">100,000 원</td>
				</tr>
				<tr>
					<td class="fst left" colspan="4"></td>
					<td colspan="2" class="bg_total left">
						<ul class="total_wrap">
							<li><p>총상품금액</p>
								<strong>100,000원</strong></li>
							<li><p>총적립금</p>
								<strong>0원</strong></li>
							<li><p>배송비</p>
								<strong>2,500원</strong></li>
							<li><p>결제금액</p>
								<strong class="point">102,500원</strong></li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>

	<div style="height:30px;"></div>

	<table cellspacing="0" cellpadding="0" class="save_point_wrap">
		<colgroup>
			<col width="78%">
			<col width="22%">
		</colgroup>
		<tbody>
			<tr>
				<td>
					<!-- s:안내 -->
					<table cellspacing="0" cellpadding="0" class="save_point">
						<colgroup>
							<col width="20%">
							<col width="80%">
						</colgroup>
						<tbody>
							<tr>
								<th class="underline">결제방식</th>
								<td class="left underline">
									<input id="paym_01" type="radio" name="sort" value="card" disabled> <label for="paym_01">신용카드</label>&nbsp;
									<input id="paym_05" type="radio" name="sort" value="virtual" disabled> <label for="paym_05">무통장(가상계좌)</label>&nbsp; 
									<input id="paym_03" type="radio" name="sort" value="transfer" disabled> <label for="paym_03">실시간계좌이체</label>&nbsp; 
									<input id="paym_04" type="radio" name="sort" value="mobile" disabled> <label for="paym_04">휴대폰결제</label>&nbsp; 
									<input id="paym_07" type="radio" name="sort" value="payco" checked="checked"> <label for="paym_07" id="payco_type1">
										<div class="payco">
											<div id="payco_btn_type_A1"></div>
										</div> 
								</td>
							</tr>

							<!-- PAYCO 안내 -->
							<tr id="div_toastpay" class="pay_detail"
								style="height: 148px">
								<th>PAYCO</th>
								<td class="left">
									<ul>
										<li><font color="red"><strong>PAYCO 간편결제 안내</strong></font></li>
										<li>PAYCO는 NHN엔터테인먼트가 만든 안전한 간편결제 서비스입니다.</li>
										<li>휴대폰과 카드 명의자가 동일해야 결제 가능하며, 결제금액 제한은 없습니다.</li>
										<li>- 지원카드: 모든 국내 신용/체크카드</li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	
	<form name="frm"></form>
	
	<div class="easyPay_div"><button type="button" class="btn easyPay_btn"  onclick="order_chk();" >주문예약실행</button> </div>
	<div class="easyPay_div">
		<li style="margin:20px 0;"><em>예약주문번호 </em><input type="text" class="form-control input_text" name="order_num" id="order_num" value=""  ></li>
		<li><em>주문창URL </em><input type="text" class="form-control input_text" name="order_url" id="order_url" value=""  ></li>		
	</div>
	<div class="easyPay_div"><button type="button" class="btn easyPay_btn"  onclick="order();" >결제하기</button> </div>
	
		<div class="detail_area">
			<div class="payco">
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">주문 상태 변경 테스트</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">					
						<em>주문상품연동키 ( sellerOrderProductReferenceKey )</em>
						<input type="text" class="form-control input_text" name="sellerOrderProductReferenceKey" id="sellerOrderProductReferenceKey" value="">
						<em>주문번호 ( orderNo )</em>
						<input type="text" class="form-control input_text" name="orderNo" id="orderNo" value="">
						<em>상태값 </em>
						<div class="input-group">
							<select id="orderProductStatus" name="orderProductStatus" class="fs12 gray_03" style="width: 220px">
									<option value="">선택하세요</option>
									<option value="PAYMENT_WAITNG">입금대기</option>
									<option value="PAYED">결제완료 (빌링 결제완료)</option>
									<option value="DELIVERY_READY">배송 준비 중 [deprecated]</option>
									<option value="DELIVERING">배송 중 [deprecated]</option>
									<option value="DELIVERY_COMPLETE">배송 완료 [deprecated]</option>
									<option value="DELIVERY_START">배송 시작(출고지시)</option>
									<option value="PURCHASE_DECISION">구매확정</option>
									<option value="CANCELED">취소</option>
							</select>
							<span class="input-group-btn">
							 <button id="order_modify_btn" class="btn btn-default" type="button" onclick="order_state_modify();">GO</button>
							</span>
						</div>
					</li>
				</ul>
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">주문 취소 테스트 (전체)</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">
						<em>주문인증키 ( orderCertifyKey ) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="orderCertifyKey_all" id="orderCertifyKey_all" value="">
						<em>주문번호 ( orderNo ) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="orderNo_all" id="orderNo_all" value="">
						<em>총 취소할 면세금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelTaxfreeAmt_all" id="totalCancelTaxfreeAmt_all" value="">
						<em>총 취소할 과세금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelTaxableAmt_all" id="totalCancelTaxableAmt_all" value="">
						<em>총 취소할 부가세(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelVatAmt_all" id="totalCancelVatAmt_all" value="">
						<em>총 취소가능금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelPossibleAmt_all" id="totalCancelPossibleAmt_all" value="">
						<em>취소처리 요청메모(선택)</em>
						<input type="text" class="form-control input_text" name="requestMemo_all" id="requestMemo_all" value="">
						<em>취소 총 금액 <font color="red">[ 필수 ]</font></em>
						<div class="input-group">
							<input type="text" class="form-control input_text" name="cancelAmt_all" id="cancelAmt_all" value="">
							<span class="input-group-btn">
								<button id="order_cancel_btn" class="btn btn-default" type="button" onclick="cancel_order_all_test();">GO</button>
							</span>
						</div>
					</li>
				</ul>
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">주문 취소 테스트 (부분 - 상품 1개만)</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">						
						<em>주문인증키 ( orderCertifyKey ) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="orderCertifyKey_part" id="orderCertifyKey_part" value="">		
						<em>상품ID ( sellerOrderProductReferenceKey ) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="sellerOrderProductReferenceKey_part" id="sellerOrderProductReferenceKey_part" value="">
						<em>주문번호 ( orderNo ) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="orderNo_part" id="orderNo_part" value="">
						<em>취소할 총 금액 (면세금액+과세공급가액+과세부가세액) <font color="red">[ 필수 ]</font></em>
						<input type="text" class="form-control input_text" name="cancelTotalAmt_part" id="cancelTotalAmt_part" value="">
						<em>총 취소할 면세금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelTaxfreeAmt_part" id="totalCancelTaxfreeAmt_part" value="">
						<em>총 취소할 과세금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelTaxableAmt_part" id="totalCancelTaxableAmt_part" value="">
						<em>총 취소할 부가세(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelVatAmt_part" id="totalCancelVatAmt_part" value="">
						<em>총 취소가능금액(선택)</em>
						<input type="text" class="form-control input_text" name="totalCancelPossibleAmt_part" id="totalCancelPossibleAmt_part" value="">						
						<em>취소사유(선택)</em>
						<input type="text" class="form-control input_text" name="cancelDetailContent_part" id="cancelDetailContent_part" value="">	
						<em>취소처리 요청메모(선택)</em>
						<input type="text" class="form-control input_text" name="requestMemo_part" id="requestMemo_part" value="">
						<em>취소 할 주문상품 금액 ( 부분취소금액 cancelAmt ) <font color="red">[ 필수 ]</font></em>
						<div class="input-group">
							<input type="text" class="form-control input_text" name="cancelAmt_part" id="cancelAmt_part" value="">
							<span class="input-group-btn">
								<button id="order_cancel_btn" class="btn btn-default" type="button" onclick="cancel_order_part_test();">GO</button>
							</span>
						</div>
					</li>
				</ul>
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">마일리지 적립 취소 테스트</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">
						<em>주문번호</em>
						<input type="text" class="form-control input_text" name="orderNo_mile" id="orderNo_mile" value="">
						<em>취소 총 금액</em>
						<div class="input-group">
							<input type="text" class="form-control input_text" name="cancelPaymentAmount_mile" id="cancelPaymentAmount_mile" value="">
							<span class="input-group-btn">
								<button id="order_cancel_btn" class="btn btn-default" type="button" onclick="mileage_cancel_test();">GO</button>
							</span>
						</div>
					</li>
				</ul>
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">영수증 확인</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">
						<em>주문번호</em>
						<input type="text" class="form-control input_text" name="orderNo_receipt" id="orderNo_receipt" value="">
						<em>결제수단</em>
						<div class="input-group">
							<span style= "margin-right: 3px;"><input type="radio"  value="cash" name="receipt"> <label for="pay01">현금영수증</label></span>
							<span style= "margin-right: 3px;"><input type="radio"  value="online" name="receipt"><label for="pay02">온라인영수증</label></span>	
							<span style= "margin-right: 3px;"><input type="radio"  value="card" name="receipt" checked><label for="pay03">신용카드매출전표</label></span>	
							<span class="input-group-btn">
								<button id="order_mile_cancel_btn" class="btn btn-default" type="button" onclick="receipt_go();">GO</button>
							</span>
						</div>
					</li>
				</ul>
				
				<span class="glyphicon glyphicon-menu-down" aria-hidden="true">결제상세 조회 (검증용)</span>
				<ul style="border-bottom:none;">
					<li style="margin:20px 0;">						
						<em>가맹점키 ( sellerKey )</em>
						<input type="text" class="form-control input_text" name="sellerKey_detail" id="sellerKey_detail" value="S0FSJE">		
						<em>PAYCO 주문예약번호 ( reserveOrderNo )</em>
						<input type="text" class="form-control input_text" name="reserveOrderNo_detail" id="reserveOrderNo_detail" value="">
						<em>외부가맹점에서 발급하는 주문 연동 Key ( sellerOrderReferenceKey )</em>						
						<div class="input-group">
							<input type="text" class="form-control input_text" name="sellerOrderReferenceKey_detail" id="sellerOrderReferenceKey_detail" value="">
							<span class="input-group-btn">
								<button id="order_detailForVerify_btn" class="btn btn-default" type="button" onclick="detailForVerify();">GO</button>
							</span>
						</div>
						
						<div id="list_detail_info"></div>									
						
					</li>
				</ul>
				
			</div>
		</div>
	</div>
	
	<button type="button" class="btn btn-default btn-lg" id="more_btn" style="margin-bottom :20px; display:none;">
	  <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span> 주문 예약 API 정보
	</button>
	
</div>

<script type="text/javascript">
	  Payco.Button.register({
		SELLER_KEY:'1111',
		ORDER_METHOD:"EASYPAY",
		BUTTON_TYPE:"A1",
		BUTTON_HANDLER:order,
		DISPLAY_PROMOTION:"Y",
		DISPLAY_ELEMENT_ID:"payco_btn_type_A1",
		"":""
	  });
</script>
</body>
</html>
