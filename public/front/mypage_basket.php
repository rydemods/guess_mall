<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
//장바구니 정보
include_once($Dir.'lib/basket.php');
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script LANGUAGE="JavaScript">
	//전체 주문
	function select_order(){
		var selectCnt = 0;
		var selectCode = '';
		$("input[name='select_basket']").each( function() {
			if( $(this).prop('checked') ) {
				selectCnt++;
				selectCode += $(this).val() + '|';
			}
		});
		if( selectCnt == 0 ){
			alert('적어도 하나 이상의 상품을 선택해 주세요.');	
		} else {
			var orderHref = "/front/order.php?"+"&selectProduct=" + selectCode;
			//console.log( orderHref );
			location.href = orderHref;
		}
	}
	//전체 삭제
	function basket_clear(){
		if( confirm('장바구니 전체를 삭제하시겠습니까?') ){
			$('#basketMode').val('delete_all');
			$('#basketForm').submit();
		}
	}
	//석택 삭제
	function delbasket( prcode ){
		if( prcode.length > 0 ){
			if( confirm('해당 장바구니를 삭제하시겠습니까?') ){
				$('#basketProductCode').val( prcode );
				$('#basketMode').val('delete');
				$('#basketForm').submit();			
			}
		} else {
			alert('잘못된 상품입니다. 관리자에 문의해 주세요.');
		}
	}
	//전체선택 ( 버튼클릭시 )
	function checkAll( type ) {
		if( type == '1' ){
			$("input[name='select_basket']").prop('checked', true );
		} else {
			$("input[name='select_basket']").prop('checked', false );
		}
	}
	
	$(document).ready( function() {
		//전체선택 (select box 클릭시 )
		$("#allCehck").on('click',function(){
			$("input[name='select_basket']").prop('checked', $(this).prop('checked') );
		});
	});
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
<?php		include($Dir.TempletDir."mybasket/mybasket_TEM001.php"); ?>
		</td>
	<tr>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>