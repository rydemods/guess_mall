
<div class="containerBody sub_skin">
	
		<!-- LNB -->
	<div class="left_lnb">
		<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
		<!---->
	</div><!-- //LNB -->

	<div class="right_section">
		<h3 class="title mb_20 ">
			장바구니
			<p class="line_map"><a>홈</a> &gt; <a>주문정보</a>  &gt;  <a class="on">장바구니</a></p>
		</h3>
		<table class="th_top">
			<colgroup>
				<col width="40px" /><col width="90px" /><col width="*px" /><col width="80px" /><col width="200px" /><col width="100px" /><col width="100px" />
			</colgroup>
			<tr>
				<th><input type="checkbox" id="allCehck" onclick='javascript:checkAll();' /></th>
				<th colspan='2'>상품정보</th>
				<th>수량</th>
				<th>총 상품금액</th>
				<th>비고</th>
			</tr>
<?php
if( count($basket) > 0 ) {
	foreach( $basket as $bKey=>$bVal ){
	$optPrice = 0;
	$basket_idx = '';
	$quantity = 0;
	$bkQuantity = $bVal->quantity;
?>
			<tr>
				<td>
					<input type="checkbox" name="select_basket" value='<?=$bVal->productcode?>' />
				</td>
				<td>
					<img src="<?=$bVal->tinyimage?>" alt=" " style='max-width : 85px;'  class="mini_pro">
				</td>
				<td class='ta_l'>
					<?=$bVal->productname?><br>
<?php
	foreach( $option[$bVal->productcode] as $optKey=>$optVal ) {
		// 옵션이 존재하는 경우
		if( $optVal->op_type == '0' && strlen( trim($optVal->optionarr) ) > 0 ){
			$optStr = '';
			$tmpOpt1 = explode( ',', $bVal->option1 );
			$tmpOpt2 = explode( ',', $bVal->option2 );
			$optStr = '옵션 : '.$tmpOpt1[0].' '.$optVal->opt1_idx;
			if( strlen( trim($optVal->opt2_idx) ) > 0 ){
				$optStr.= ' / '.$tmpOpt2[0].' '.$optVal->opt2_idx;
			}
?>
					<?=$optStr?> - <?=$optVal->quantityarr?>개
					<span class="opt-price" style='color: #ff8585;' >+<?=number_format($optVal->option_price * $optVal->quantityarr )?>원</span><br>
<?php
			unset($tmpOpt1);
			unset($tmpOpt2);
			$quantity += $optVal->quantityarr;
		// 추가 옵션이 존재하는 경우
		} else if( $optVal->op_type == '1' && strlen( trim($optVal->optionarr) ) > 0 ) {
			$tmpsupply = explode( chr(30) , $optVal->optionarr);
?>
					<?='옵션 : '.$tmpsupply[0].' '.$tmpsupply[1]?> - <?=$optVal->quantityarr?>개
					<span class="opt-price" style='color: #ff8585;' >+<?=number_format($optVal->option_price * $optVal->quantityarr )?>원</span><br>
<?php
			unset($tmpsupply);
		//상품만 있는 경우
		} else {
			$quantity = $optVal->quantity;
		}
		$optPrice += (int) $optVal->pricearr;
	}
?>					
				</td>
				<td>
					<?=$bkQuantity?>
				</td>
				<td>
					<span class="price" style='color: #eb0e1d; text-align: right; font-size: 13px; font-weight: bold;'><?=number_format( ( $bVal->sellprice * $quantity ) + $optPrice )?>원</span>
				</td>
				<td>
					<a href="javascript:delbasket('<?=$bVal->productcode?>')"><img src="../img/button/cart_remove_btn.gif" alt="삭제"></a>
				</td>
			</tr>	
<?php
	}
} else {
?>    
			<tr>
				<td colspan='6'> 장바구니에 상품이 존재하지 않습니다. </td>
			</tr>
<?php
}
?>
		</table>
<?php
if( count($basket) > 0 ) {
?>
		<div class="button_left" style='margin-top:25px; float: left;' >
			<a href="javascript:checkAll( '1' );" target="_self"><img src="../img/button/cart_select_all_btn.gif" alt="전체선택" class=""></a>
			<a href="javascript:checkAll( '0' );" target="_self"><img src="../img/button/cart_select_cancel_btn.gif" alt="선택해제" class=""></a>
			<a href="javascript:basket_clear();" target="_self"><img src="../img/button/cart_delete_all_btn.gif" alt="전체삭제" class=""></a>
		</div>
		<div class="button_right" style='margin-top:25px; float: right;'>
			<a href="javascript:select_order()" class="btn_B wide">선택상품 주문</a>
			<a href="<?=$Dir?>/front/order.php?&allcheck=1" class="btn_A wide">전체상품 주문</a>
		</div>
	</div>
<?php
}
?>
</div>
<form id='basketForm' name='basketForm' method='POST' action='<?=$_SERVER['PHP_SELF']?>' >
<input type='hidden' id='basketMode' name='mode' value=''>
<input type='hidden' id='basketProductCode' name='productcode' value=''>
</form>
