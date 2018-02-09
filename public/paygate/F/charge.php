<?php
	//-----------------------------------------------------------------------------
	// PAYCO 주문 페이지 샘플  ( PHP EASYPAY / PAY1 )
	// 2016-02-16	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------
    header('Content-Type: text/html; charset=utf-8');
	include("./payco/payco_config.php");

	//-----------------------------------------------------------------------------
	// 테스트용 고객 주문 번호 생성
	//-----------------------------------------------------------------------------
	//$customerOrderNumber = "TEST2016".str_replace("-","",date("Y-m-d")).substr("000000".mt_rand(0,999999), -6);
	// 결제완료 후 에도 주문예약번호 자동생성 가능하도록 임시 로 function callPaycoUrl()에 구현되어 주석처리.

	//echo "isMobile >>> ".$isMobile;
?>

<!--<script type="text/javascript" src="/static/js/jquery-1.12.0.min.js" ></script>-->
<script type="text/javascript" src="/js/jquery-1.12.1.min.js" ></script>
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

    var mobile_path = '<?=$_REQUEST['mobile_path']?>';

    if ( mobile_path != "/m/order.php" ) {
        $("#paybuttonlayer", opener.parent.document).css('display','none');
        $("#payinglayer", opener.parent.document).css('display','block');
    }
	
	var randomStr = "";
	var randomStrCart = ""; 
	
	for(var i=0;i<10;i++){
		randomStr += Math.ceil(Math.random() * 9 + 1);
	}
	
	//var customerOrderNumber = "TEST2016" + randomStr;               // ( 결제완료 후 에도 주문예약번호 자동생성 가능하도록 임시 추가 ) 가맹점 고객 주문번호 입력
	var customerOrderNumber = "<?=$_REQUEST['ordercode']?>";               // ( 결제완료 후 에도 주문예약번호 자동생성 가능하도록 임시 추가 ) 가맹점 고객 주문번호 입력

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

		data : {
            "customerOrderNumber" : '<?=$_REQUEST['ordercode']?>', 
            "cartNo" : cartNo,
            "totalProductPaymentAmt" : '<?=$_REQUEST['price']?>',
            "orderTitle" : '<?=$_REQUEST['goodname']?>',
            "mobile_path" : '<?=$_REQUEST['mobile_path']?>',
            "paycode" : '<?=$_REQUEST['paycode']?>',
            "basketidxs" : '<?=$_REQUEST['basketidxs']?>'
        },
		
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		dataType:"json",
		success:function(data){
			if(data.code == '0') {
				//console.log(data.result.reserveOrderNo);	 // 주석 해제시, 일부 웹브라우저 에서 PAYCO 결제창이 뜨지 않습니다. 			
				$('#order_num').val(data.result.reserveOrderNo);
				$('#order_url').val(data.result.orderSheetUrl);	
				$("#reserveOrderNo_detail").val(data.result.reserveOrderNo);    // 결제상세 조회 (검증용) 입력창에 삽입_PAYCO 주문예약번호 ( reserveOrderNo )
				$("#sellerOrderReferenceKey_detail").val(customerOrderNumber);	// 결제상세 조회 (검증용) 입력창에 삽입_외부가맹점에서 발급하는 주문 연동 Key ( sellerOrderReferenceKey )

                order();
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
	location.href = order_Url;
	
/*
	var isMobile = <?=$isMobile?>;

	if(isMobile == 0){
		location.href = order_Url;
	}else{
		window.open(order_Url, 'popupPayco', 'top=100, left=300, width=727px, height=512px, resizble=no, scrollbars=yes'); 
	}
*/
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

<script type="text/javascript">
    $(document).ready(function() {
        callPaycoUrl();
    });
</script>

<form>
    <input type="hidden" id="order_num"/>
    <input type="hidden" id="order_url"/>
    <input type="hidden" id="reserveOrderNo_detail"/>
    <input type="hidden" id="sellerOrderReferenceKey_detail"/>
</form>

