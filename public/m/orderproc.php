<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once('outline/header_m.php');
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");

include_once("order_check.php");


# 주문 금액 최종 ( ordersend에서는 가격을 나누어서 넣는다 )
// 2015 11 19 유동혁
$last_price = $sumprice + $deli_price - $usereserve - $dc_price;

#카드쿠폰 추가
if( count( $tmp_use_card ) == 1 ){
	$use_card = $tmp_use_card[0];
	$used_card_yn = 'Y';
} else if( count( $tmp_use_card ) > 1 ){
	$use_card = implode( ':', $tmp_use_card );
	$used_card_yn = 'Y';
} else {
	$used_card_yn = 'N';
}

?>

<!DOCTYPE html>
<body>
<script type="text/javascript" src="../../js/jquery.js"></script>
<form name=frmSettle method=post action="ordersend.php">

	<input type=hidden name="ordercode" value="<?=substr($ordercode, 0, 20)?>">
	<input type=hidden name="ordr_idxx" value="<?=substr($ordercode, 0, 20)?>">
	<?foreach($_REQUEST as $k => $v){?>
		<?if(is_array($v)){?>
			<?foreach($v as $kk => $vv){?>
				<input type=hidden name="<?=$k?>[]" value="<?=$vv?>">
			<?}?>
		<?}else{?>
			<input type=hidden name="<?=$k?>" value="<?=$v?>">
		<?}?>
	<?}?>
</form>
<script>
	function submitSettleForm()
	{
		var fm = document.frmSettle;
		fm.target="ifrmHidden";
		fm.submit();
	}
	$(document).ready(function(){
		submitSettleForm();
	})
</script>
<IFRAME id='ifrmHidden' name='ifrmHidden' width="100%" height="0" style="display:none"></IFRAME>
<?
	if($paymethod!="B") {
		########### 결제시스템 연결 시작 ##########
		include("paygate/card_gate.php");
		exit;
		########### 결제시스템 연결 끝   ##########
	}
?>
