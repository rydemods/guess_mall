
function bluring(){
if(event.srcElement.tagName=="A"||event.srcElement.tagName=="IMG") document.body.focus();
}

function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
 
}
// 장바구니 삭제 2015 11 06 유동혁
function basket_del( productcode ){
	if( confirm('해당 장바구니를 삭제하시겠습니까?\n( 해당 상품은 전체 삭제가 됩니다. )') ){
		$.post(
			'../main/ajax_basketpop.php',
			{
				mode : 'delete',
				productcode : productcode
			},
			function( data ){
				//console.log( data );
				if( data == 'success' ){
					$('#tab_2 .con_size2').html('');
					$.post(
						'../main/ajax_basketpop.php',
						function( data ){
							if( data.length > 0 ){
								$('#tab_2 .con_size2').html( data );
							}
						}
					);
					alert('삭제가 완료되었습니다.');
				} else {
					alert('잠시후에 다시시도해 주세요.');
					//오류처리
				}
			}
		);
	}
}

//장바구니 구매하기 2015 11 06 유동혁

function goOrder(){
	if( $("input[name='topbasket[]']").length > 0 ){
		/*
		var strBasket = "";
		$("input[name='topbasket[]']").each(function(index){
			strBasket += $(this).val() + "|";
		})
		*/
		//var orderHref = "/front/order.php?"+"&allcheck="+strBasket;
		var orderHref = "/front/order.php?"+"&allcheck=1";
		location.href = orderHref;
	} else {
		alert('한개 이상의 상품이 있어야 구매가 가능합니다.');
	}
}
