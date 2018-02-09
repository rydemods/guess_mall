<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir.'lib/basket.class.php');

$Basket = new Basket;

$productcode = $_POST['productcode']; //상품코드
$option_code = $_POST['option_code']; // 옵션명
$quantity = $_POST['quantity']; // 상품수량
$option_type = $_POST['option_type']; // 옵션 type 0 - 조합형, 1 - 독립형,  2 - 옵션없음
$text_opt_subject = $_POST['text_opt_subject']; // 텍스트 옵션 명
$text_opt_content = $_POST['text_opt_content']; // 텍스트 옵션 내용
$spl_option = $_POST['spl_option']; //추가옵션
$mode = $_POST['mode'];

# 배송 타입 추가에 따른 추가 입력 데이터 셋팅
$delivery_type = $_POST['delivery_type'];
$store_code = $_POST['store_code'];
$reservation_date = $_POST['reservation_date'];
$post_code = $_POST['post_code'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$basketArray = array(
							"delivery_type" => $delivery_type, 
							"store_code" => $store_code, 
							"reservation_date" => $reservation_date, 
							"post_code" => $post_code, 
							"address1" => $address1, 
							"address2" => $address2, 
);

$returnArr = array();

if( $mode == 'insert' ){

	$code = $Basket->set_item( $productcode, $quantity, $option_code, $quantity, $option_type, $text_opt_subject, $text_opt_content, $basketArray );
	$returnArr['code'] = $code;
	$returnArr['msg'] = $Basket->return_code[$code];

} else if( $mode == 'order' ){

	$code = $Basket->set_item( $productcode, $quantity, $option_code, $quantity, $option_type, $text_opt_subject, $text_opt_content, $basketArray );
	$returnArr['code'] = $code;
	$returnArr['msg'] = $Basket->return_code[$code];
	$returnArr['basketidx'] = $Basket->return_idx;

} else if ( $mode == 'delete' ){

	$basketidxs = $_POST['basketidxs'];
	$code = $Basket->del_item( $basketidxs );
	$returnArr['code'] = $code;
	$returnArr['msg'] = $Basket->return_code[$code];

} else if( $mode == 'quantity_update' ) {

	$basketidx = $_POST['basketidx'];
	$is_option = $_POST['is_option'];

	$Basket->basket_quantity_update( $basketidx, $quantity );
	if( $is_option == 'true' && $Basket->get_success() ){
		$Basket->basket_quantityarr_update( $basketidx, $quantity );
	}
	$code = $Basket->this_code;
	$returnArr['code'] = $code;
	$returnArr['msg'] = $Basket->return_code[$code];

} else if( $mode == 'modify' ){
	$basketidx = $_POST['basketidx'];
	$option_type = $_POST['option_type'];
	$text_content = $_POST['text_content'];

	$code = $Basket->modify_item( $basketidx, $quantity, $option_code, $quantity, $option_type, $text_content );
	$returnArr['code'] = $code;
	$returnArr['msg'] = $Basket->return_code[$code];
}

echo json_encode( $returnArr );

?>