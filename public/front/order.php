<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/order.class.php");
include_once($Dir."lib/delivery.class.php");
include_once($Dir."lib/coupon.class.php");

## 비로긴상태에서 로긴한 후 retrun url 로 주문으로 들어오면 준회원도 주문이 가능해진다..ㅠㅠ 2016-12-02
$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
//exdebug($mem_auth_type);
//exdebug("ismobile = ".$isMobile);
if($mem_auth_type == 'sns') {
    echo "<script>";
    echo "if(confirm('정회원으로 전환시 구매가능합니다.\\n정회원 전환 페이지로 이동하시겠습니까?')) { ";
	echo "		location.href='member_agree.php' ";
    echo "} else { ";
	echo "		location.href='/' ";
    echo "} ";
    echo "</script>";
    exit;
}

$basketidxs = $_REQUEST['basketidxs'];

# 임직원 구매기능 추가
$staff_order = $_REQUEST['staff_order']; // 임직원 구매 type
if( $staff_order == '' ) $staff_order = 'N'; // 값이 없을때 예외처리
if( chk_staff_order( $staff_order ) == 0 ) { // 0 - 오류처리 1 - 일반구매 2 - 임직원 구매
    echo "<script>";
    echo "  alert('잘못된 구매 입니다.'); ";
    echo "  window.location.replace('basket.php')";
    echo "</script>";
    exit;
}

# 협력사 구매기능 추가
$cooper_order = $_REQUEST['cooper_order']; // 임직원 구매 type
if( $cooper_order == '' ) $cooper_order = 'N'; // 값이 없을때 예외처리
if( chk_cooper_order( $cooper_order ) == 0 ) { // 0 - 오류처리 1 - 일반구매 2 - 임직원 구매
    echo "<script>";
    echo "  alert('잘못된 구매 입니다.'); ";
    echo "  window.location.replace('basket.php')";
    echo "</script>";
    exit;
}


# 매장 픽업 / 당일 수령 장바구니 상품 삭제
delDeliveryTypeData();


$Order = new Order();

$Delivery = new Delivery();

if( $_ShopInfo->getStaffYn() == 'Y' && $staff_order == 'Y' ) { // 임직원 구매이면
	$staff_order = 'Y';
	$cooper_order = 'N';
} else if($_ShopInfo->getCooperYn() == 'Y'  && $cooper_order == 'Y' ) { // 협력사 구매이면
	$staff_order = 'N';
	$cooper_order = 'Y';
}else{
	$staff_order = 'N';
	$cooper_order = 'N';
}
$Order->set_staff_order( $staff_order); // 임직원 구분

$Order->set_cooper_order( $cooper_order); // 협력사 구분

$Order->order_setting( $basketidxs ); //주문할 장바구니 정보




$_odata = $Order->get_order_object(); //주문에 들어가는 상품정보

//$Order->market_stock_check(  ); //주문할 장바구니 정보


// 2016-06-22 중복주문 방지를 위한 주문 check용 추가
$paycode = unique_id();
$orderChkQry = array();
$delitype_check="";
$staff_pr_price="0";
$cooper_pr_price="0";
foreach( $_odata as $dataKey => $dataVal ){
    $orderChkQry[] .= "( '".$paycode."', '".$dataVal['productcode']."', '".$dataVal['basketidx']."', '".$_ShopInfo->getMemid()."', '".date('YmdHis')."' )";
	
	$staff_pr_price+=($dataVal["ori_price"]-$dataVal["price"])*$dataVal["quantity"];
	if($cooper_order == 'Y'){
		list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
		$c_productcode = $dataVal['productcode'];
		list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
		list($consumerprice) = pmysql_fetch("select consumerprice from tblproduct where productcode= '".$c_productcode."' ");

		if($consumerprice == $dataVal["price"] || $dataVal["price"] > $company_price){
			$cooper_pr_price+=($consumerprice-$company_price)*$dataVal["quantity"];
		}else{
			$cooper_pr_price+=($consumerprice-$dataVal["price"])*$dataVal["quantity"];
		}
	}

	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//echo "ori_price : ";
		//echo $dataVal["ori_price"];
		//echo "<br/>";
		//echo "price : ";
		//echo $dataVal["price"];
		//echo "<br/>";
		//echo "cooper_pr_price : ";
		//echo $company_price;
		//exit;
	}
	$delitype_check[$dataVal['delivery_type']]++;
}


//o2o주문과 택배주문은 동시에 같이 주문할수가없다.
if($delitype_check[0] && ( $delitype_check[1] > 0 || $delitype_check[3]) > 0 ){
	alert_go("택배주문과 O2O주문은 함께 주문하실수 없습니다.", "../front/basket.php");
	exit;
}
/*
foreach( $_odata as $_proData =>$_proObj ){
	$brandVenderArr[$_proObj['brand']]	=  $_proObj['vender'];
}
*/

$brandArr = ProductToBrand_Sort( $_odata );

$Delivery->get_product( $_odata ); //배송비를 세팅해줌
$Delivery->set_deli_item();
$vender_info    = $Delivery->get_vender();
$vender_deli    = $Delivery->get_vender_deli();
$free_deli    = $Delivery->get_free_deli();
$product_deli = $Delivery->get_product_deli();
//상품 이미지 경로
$productImgPath = $Dir.DataDir."shopimages/product/";

//결제관련
#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
$escrow_info = GetEscrowType($_data->escrow_info);
if(ord($escrow_info["escrow_limit"])==0) $escrow_info["escrow_limit"]=100000;
if(ord($_data->escrow_id) && ($escrow_info["escrowcash"]=="Y" || $escrow_info["escrowcash"]=="A")) {
	$escrowok="Y";
} else {
	$escrowok="N";
	$escrow_info["escrowcash"]="";
	if($escrow_info["onlycash"]!="Y" && (ord($escrow_info["onlycard"])==0 && ord($escrow_info["nopayment"])==0)) $escrow_info["onlycash"]="Y";
}

$arrpayinfo=explode("=",$_data->bank_account);
$bank_payinfo = explode(",", $arrpayinfo[0]);
$cardid_info=GetEscrowType($_data->card_id);
//결제관련 종료

#회원 정보 설정
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row = pmysql_fetch_object($result)) {
		$reserve_chk= $row->reserve_chk;
		
		//$user_reserve = $row->reserve;
		//통합포인트를 erp에서 가져온다.
		$user_erp_reserve=getErpMeberPoint($_ShopInfo->getMemid());
		$user_reserve = $user_erp_reserve[p_data]?$user_erp_reserve[p_data]:"0";
		
		//if( $staff_order == 'N' ) $user_reserve = $row->reserve;
		//if( $staff_order == 'Y' ) $user_reserve = $row->staff_reserve;
		$user_point = $row->act_point;
		if($user_reserve>$reserve_limit) {
			$okreserve=$reserve_limit;
			$remainreserve=$user_reserve-$reserve_limit;
		} else {
			$okreserve=$user_reserve;
			$remainreserve=0;
		}
		if($user_point>$reserve_limit) {
			$okpoint=$reserve_limit;
			$remainpoint=$user_point-$reserve_limit;
		} else {
			$okpoint=$user_point;
			$remainpoint=0;
		}

		//배송지 정보
		$dn_sql ="SELECT * FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."' ORDER BY NO DESC";
		$dn_result = pmysql_query( $dn_sql, get_db_conn() );
		while( $dn_row = pmysql_fetch_object( $dn_result ) ){
			$dn_info[] = $dn_row;
			if ($dn_row->base_chk == 'Y') {
				$dn_name					= $dn_row->get_name;
				$dn_mobile				= $dn_row->mobile;
				$dn_post_code			= $dn_row->postcode;
				$dn_post_zonecode	= $dn_row->postcode_new;
				$dn_addr1					= $dn_row->addr1;
				$dn_addr2					= $dn_row->addr2;
			}
		}
		pmysql_free_result($dn_result);

		$home_addr="";
		
		if($dn_post_zonecode) {
			$home_zonecode	= $dn_post_zonecode;
			$home_post_ep		= explode("-", $dn_post_code);
			$home_post1			= $home_post_ep[0];
			$home_post2			= $home_post_ep[1];
			$home_post			= $dn_post_zonecode;
			$home_addr1			= $dn_addr1;
			$home_addr2			= $dn_addr2;
		} else {
			$home_zonecode	= $row->home_post;
			$home_post1			= "";
			$home_post2			= "";
			$home_post			= $row->home_post;
			$home_addr = explode("↑=↑",$row->home_addr);
			$home_addr1 = $home_addr[0];
			$home_addr2 = $home_addr[1];
		}

		$office_addr="";
		if(strlen($row->office_post)==6) {
			$office_post1=substr($row->office_post,0,3);
			$office_post2=substr($row->office_post,3,3);
		}
		$row->office_addr = str_replace("\"","",$row->office_addr);
		$office_addr = explode("=",$row->office_addr);
		$office_addr1 = $office_addr[0];
		$office_addr2 = $office_addr[1];

		$name = $row->name;
		$userName = $row->name;
		$email = explode("@",$row->email);
		if (ord($row->mobile)) $mobile = $row->mobile;
		else if (ord($row->home_tel)) $mobile = $row->home_tel;
		else if (ord($row->office_tel)) $mobile = $row->office_tel;
		$mobile=explode("-",replace_tel(check_num($mobile)));
		$home_tel=explode("-",replace_tel(check_num($row->home_tel)));

		if($_ShopInfo->getStaffType()){
			$staff_limit_max = $row->staff_limit_max;
			$staff_limit = $row->staff_limit;
		}

		$group_code=$row->group_code;
		pmysql_free_result($result);
		if(ord($group_code) && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='{$group_code}' AND SUBSTR(group_code,1,1)!='M' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)){
				$group_code = $row->group_code;
				$group_level=$row->group_level;
				$group_deli_free=$row->group_deli_free;
				$org_group_name=$row->group_name;  //그룹정보로 인해 추가
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2);
				$group_usemoney=$row->group_usemoney;
				$group_addmoney=$row->group_addmoney;
				$group_payment=$row->group_payment;
				if($group_payment=="B") $group_name.=" (현금결제시)";
				else if($group_payment=="C") $group_name.=" (카드결제시)";
			}
			pmysql_free_result($result);
		}
	} else {

		$_ShopInfo->setMemid("");
	}
}

//관리자 주문설정
$etcmessage=explode("=",$_data->order_msg);

#회원 쿠폰정보

#쿠폰 설정
$_CouponInfo = new CouponInfo();
# 회원쿠폰
$_CouponInfo->search_member_coupon( $_ShopInfo->memid, 1, 1 );
$memCoupon      = $_CouponInfo->mem_coupon;


#상품쿠폰과 장바구니 쿠폰을 나눈다
$basket_coupon = array();
$product_coupon = array();
$deliver_coupon = array();
$chk_coupon     = array();
foreach( $memCoupon as $couponVal ){
	if( $couponVal->coupon_use_type == '1' && $couponVal->coupon_type != '9' ){
        if( array_search( $couponVal->coupon_code, $chk_coupon) === false ){
            $basket_coupon[] = $couponVal;
            $chk_coupon[]    = $couponVal->coupon_code;
        }
	} else if( $couponVal->coupon_type == '9' ) {
        $deliver_coupon[] = $couponVal;
    }else {
		$product_coupon[] = $couponVal;
	}
}
$coupon_cnt = 0;
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
	$coupon_cnt = count( $memCoupon );
	$memsql = "SELECT reserve FROM tblmember WHERE id = '".$_ShopInfo->getMemid()."'";
	$memres = pmysql_query( $memsql, get_db_conn() );
	$mem_reserve = pmysql_fetch_object( $memres );
	pmysql_free_result( $memres );
}
# 상품 토탈가  + 토탈 수량
if( strlen( $basketidxs ) > 0 ){

	$chk_total_pro_price	= 0; // 상품금액
	$chk_total_deli_price	= 0; // 배송료
	$chk_total_quantity		= 0; // 상품수량

	foreach( $brandArr as $brand=>$brandObj ){
		//$vender	=$brandVenderArr[$brand];
		$vender	=0;
		$product_price = 0;
		foreach( $brandObj as $product ) {
			$opt_price = 0; // 상품별 옵션가
			if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
				if( count( $product['option'] ) > 0 ){
					if( $product['option_type'] == 0 ){ // 조합형 옵션
						$tmp_option = $product['option'][0];
						$opt_price += $tmp_option['option_price'] * $product['option_quantity'];
					}
					if( $product['option_type'] == 1 ){ // 독립형 옵션
						foreach( $product['option'] as $optKey=>$optVal ){
							$opt_price += $optVal['option_price'] * $product['option_quantity'];
						}// option foreach
					}
				} // count option
			}// count option
			$product_price += ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
			$chk_total_quantity += $product['quantity'];
		}

		$vender_deli_price = 0;

		if( $vender_info[$vender] ){

			if( $product_deli[$vender] ){
				foreach( $product_deli[$vender] as $prDeliKey => $prDeliVal ){
					$vender_deli_price += $prDeliVal['deli_price'];
				}
			}
			$vender_deli_price += $vender_deli[$vender]['deli_price'];
		}

		if( $vender_info[$vender]['deli_select'] == '0' || $vender_info[$vender]['deli_select'] == '2' ) $chk_total_deli_price += $vender_deli_price;
		$chk_total_pro_price += $product_price;
	}

	$total_price_sum	= $chk_total_pro_price+$chk_total_deli_price;
	$total_qty			= $chk_total_quantity;

	//echo $total_price_sum."/".$total_qty;
	if($staff_order == 'Y') { // 임직원 구매이면
		// 임직원 적립금을 가져온다.
		list($staff_reserve)=pmysql_fetch("select staff_reserve from tblmember where id='".$_ShopInfo->getMemid()."'");// 임직원 포인트
		if($staff_pr_price > $staff_reserve) { // 포인트가 부족하면
			echo "<script>";
			echo "  alert('보유하신 임직원 포인트가 부족합니다.'); ";
			echo "  window.location.replace('basket.php')";
			echo "</script>";
			exit;
		}
	}
/*
if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
}else{
	if($cooper_order == 'Y') { // 제휴사 구매이면
		// 제휴사 적립금을 가져온다.
		list($cooper_reserve)=pmysql_fetch("select cooper_reserve from tblmember where id='".$_ShopInfo->getMemid()."'");// 임직원 포인트
		if($cooper_pr_price < 0){$cooper_pr_price=0;}
		if($cooper_pr_price > $cooper_reserve) { // 포인트가 부족하면
			echo "<script>";
			echo "  alert('보유하신 제휴사 적립금이 부족합니다.'); ";
			echo "  window.location.replace('basket.php')";
			echo "</script>";
			exit;
		}
	}
}
*/
}

# 상품별 재고 체크를 위해 상품 재정렬 같은 옵션의 상품의 수량을 더한 후 비교 하기 위해 배열 셋팅
# 옵션은 조합형만 존재 한다고 하여 조합형에 대한 내용만 작업
$stockArrayCheck = array();
foreach( $brandArr as $brand=>$brandObj ){
	foreach( $brandObj as $product ) {
		if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
			if( count( $product['option'] ) > 0 ){
				$tmp_opt_subject = explode( '@#', $product['option_subject'] );
				if( $product['option_type'] == 0 ){ // 조합형 옵션
					$tmp_option = $product['option'][0];
					$tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
					foreach( $tmp_opt_subject as $optKey=>$optVal ){
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['productname'] = $product['productname'];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['prodcode'] = $product['prodcode'];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['colorcode'] = $product['colorcode'];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['size'] = $tmp_opt_contetnt[$optKey];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['store_code'] = $product['store_code'];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['delivery_type'] = $product['delivery_type'];	//2016-10-07 libe90 발송구분 변수 추가
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['chk_key'] = $tmp_opt_contetnt[$optKey];
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['quantity'] += $product['quantity'];
						# 매장 코드가 있을때만 매장 코드 없이 수량을 더해 놓는다. 매장 코드없는 상품은 같은 옵션의 전체 재고를 비교해야 하기 때문
						if($product['store_code']) $stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey]]['quantity'] += $product['quantity'];
					}
				}
			}
		}
	}
}
if(count($stockArrayCheck) > 0){
	foreach($stockArrayCheck as $k => $v){
		# 상품별 재고 체크
		if($v['prodcode'] && $v['colorcode']) {
/*	20170824 수정
			if ($v['delivery_type']=='0' && $staff_order=="Y"){
				$shopRealtimeStock=getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $sync_bon_code);
			}else 
*/
			if ($v['delivery_type']=='0') {	//2016-10-07 libe90 매장발송일경우 재고체크 분기
				$shopRealtimeStock = getErpProdShopStock_Type($v['prodcode'], $v['colorcode'], $v['size'], 'delivery');
				$shopRealtimeStock['sumqty']=$shopRealtimeStock['availqty'];
				$stockArrayCheck[$v['prodcode'].$v['chk_key']]['store_code'] = $shopRealtimeStock['shopcd'];
			}else{
				$shopRealtimeStock = getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $v['store_code']);
			}
			if($v['quantity'] > $shopRealtimeStock['sumqty']){
				alert_go("[".$v['productname']."]재고가 부족합니다.\\r해당상품의 최대주문가능수량은 ".$shopRealtimeStock['sumqty']." 개 입니다.\\r장바구니 페이지로 이동합니다.", "../front/basket.php");	//2016-10-07 libe90 문구변경
				exit;
			}
		}
	}
}

?>

<?include ($Dir.MainDir.$_data->menu_type.".php");?>

<SCRIPT LANGUAGE="JavaScript">
<!--
var market_pic="<?=$delitype_check[1]?>";
function deli_area_check(zipcode){
	if(zipcode && !market_pic){
		$.ajax({
			method : "POST",
			url : "product_deli_area.php",
			data : { zipcode : zipcode },
			dataType : "json"
		}).done( function( data ) {
			$("#total_deli_price_area").val(data);
			$(".area_delivery_price").html(comma(data));
			sumprice_reset();
		});
			
		
	}else{
		$("#total_deli_price_area").val("0");
		$(".area_delivery_price").html("0");
		sumprice_reset();
	}
}

function sumprice_reset(){
	$('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
	$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
}
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			$("#post5").val(data.zonecode);
			$("#rpost1").val(data.postcode1);
			$("#rpost2").val(data.postcode2);
			$("#raddr1").val(data.address);
			$("#raddr2").val('');
			$("#raddr2").focus();
			$("#post").val( data.zonecode );
			
			//산간배송비 확인
			deli_area_check(data.zonecode);
		}
	}).open();
}

$(window).ready(function(){

	var payMethodType = 'C';
	var deli_price = $("#delivery_price").html();

	if(uncomma(deli_price) == 0){
		$('#deli_type1').attr('disabled',true);
	}
	
	$(".dev_payment").each(function(){
		if($(this).val() == payMethodType){
			$(this).prop('checked', true);
			sel_paymethod(this);
		}
	});

});

function SameCheck(checked) {
	if(checked) {
		document.form1.receiver_name.value=document.form1.sender_name.value;
		document.form1.receiver_tel11.value=document.form1.home_tel1.value;
		document.form1.receiver_tel12.value=document.form1.home_tel2.value;
		document.form1.receiver_tel13.value=document.form1.home_tel3.value;
		document.form1.receiver_tel21.value=document.form1.sender_tel1.value;
		document.form1.receiver_tel22.value=document.form1.sender_tel2.value;
		document.form1.receiver_tel23.value=document.form1.sender_tel3.value;

		document.form1.post.value="<?=$home_post?>";
		document.form1.rpost1.value="<?=$home_post1?>";
		document.form1.rpost2.value="<?=$home_post2?>";
		document.form1.raddr1.value="<?=$home_addr1?>";
		document.form1.raddr2.value="<?=$home_addr2?>";
		document.form1.post5.value="<?=$home_zonecode?>";

		//산간배송비 확인
		deli_area_check("<?=$home_zonecode?>");
	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel11.value="02";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";
		document.form1.receiver_tel21.value="010";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";

		document.form1.post.value="";
		document.form1.rpost1.value='';
		document.form1.rpost2.value='';
		document.form1.raddr1.value='';
		document.form1.raddr2.value='';
		document.form1.post5.value='';

		deli_area_check();
	}
	$(".deli_check").prop("checked", false);
}

function Dn_InReceivercheck(dn_data){
	document.form1.dn_inr.value=dn_data;
}
function Dn_InReceiver(in_data) {
	var dn_inr = document.form1.dn_inr.value;
	if(dn_inr && in_data=="in") {
		dn_data_ep		= dn_inr.split("|@|");
		dn_mobile_ep		= dn_data_ep[3].split("-");
		dn_postcode_ep	= dn_data_ep[4].split("-");

		document.form1.receiver_name.value=dn_data_ep[2];
		document.form1.receiver_tel21.value=dn_mobile_ep[0];
		document.form1.receiver_tel22.value=dn_mobile_ep[1];
		document.form1.receiver_tel23.value=dn_mobile_ep[2];
		document.form1.receiver_tel11.value="02";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";

		document.form1.post.value=dn_data_ep[5];
		document.form1.rpost1.value=dn_postcode_ep[0];
		document.form1.rpost2.value=dn_postcode_ep[2];
		document.form1.raddr1.value=dn_data_ep[6];
		document.form1.raddr2.value=dn_data_ep[7];
		document.form1.post5.value=dn_data_ep[5];

		//산간배송비 확인
		deli_area_check(dn_data_ep[5]);

		$("#dev_orderer").prop("checked", false);

	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel21.value="010";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";
		document.form1.receiver_tel11.value="02";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";

		document.form1.post.value="";
		document.form1.rpost1.value='';
		document.form1.rpost2.value='';
		document.form1.raddr1.value='';
		document.form1.raddr2.value='';
		document.form1.post5.value='';
		$(".deli_check").prop("checked", false);
		//산간배송비 확인
		deli_area_check();
	}

	$('div.delivery .btn-close').trigger('click');
}


function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="basket.php";
	} else {
		if (PROCESS_IFRAME.chargepop) {
			if (gbn=="cancel") alert("결제창과 연결중입니다. 취소하시려면 결제창에서 취소하기를 누르세요.");
			PROCESS_IFRAME.chargepop.focus();
		} else {
			PROCESS_IFRAME.PaymentOpen();
			ProcessWait('visible');
		}

	}
}

function ProcessWait(display) {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility=display;
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
	PAYWAIT_LAYER.style.visibility=display;
}

function PaymentOpen() {// 사용 안하는것 같음
	PROCESS_IFRAME.PaymentOpen();
	ProcessWait('visible');
}

function sel_paymethod(obj){

	var frm=document.form1;
	var totp=uncomma(document.getElementById("price_sum").innerHTML);

	$("#pay_txt").html($(obj).next().attr('title'));
	$(".noticeBox").addClass("hide");

	if (obj.value =='O' || obj.value == 'Q') {
		$("#O_notice").removeClass("hide");
	}else{
		$("#"+obj.value+"_notice").removeClass("hide");
	}

	if (obj.value !='C' && obj.value != 'Y' && obj.value != 'K' ) {
		frm.pay_data1.value='';
	}

/* 페이코결제와 무통장입금 조건 제거 2017-02-22 */
	if (obj.value=='B') {
		document.getElementById("card_type").style.display="block";
	//	document.getElementById("payco_notice").style.display="none";
    } else if ( obj.value == 'Y' ) {
	//	document.getElementById("payco_notice").style.display="block";
		document.getElementById("card_type").style.display="none";
	} else {
		document.getElementById("card_type").style.display="none";
	//	document.getElementById("payco_notice").style.display="none";
		frm.pay_data1.value='';
	}

	if(obj.value=='Q'){
		if(frm.escrowcash.value=='Y' && (frm.escrow_limit.value>parseInt(totp))){
			alert('총 결제금액이 '+comma(frm.escrow_limit.value)+'원 이상일때만 에스크로 결제가 가능합니다.');
			frm.paymethod.value='';
			obj.checked=false;
			return;
		}
	}

	frm.paymethod.value=obj.value;
}

function sel_account(obj){
	var frm=document.form1;
	frm.pay_data1.value=obj.value;
}

function go_basket(){
	document.location.href="../../front/basket.php";
}

function email_change(){
	var email_select=$('select[name="email_select"]').val();
		
	if(email_select){
		$("#sender_email2").val(email_select);
		$("#sender_email2").hide();
	}else{
		$("#sender_email2").show();
	}
}
//-->
</SCRIPT>
<form name='form1' action="<?=$Dir.FrontDir?>ordersend.php" method='post'>
<input type="hidden" name="addorder_msg" value="">
<input type="hidden" id="direct_deli" name="direct_deli" value="N">
<input type='hidden' name='overseas_code' value='' > <!-- 통관번호 -->
<input type='hidden' name='basketidxs' value='<?=$basketidxs?>' > <!-- 장바구니 번호 -->
<input type='hidden' name='staff_order' value='<?=$staff_order?>' > <!-- 임직원 구매 -->
<input type='hidden' name='cooper_order' value='<?=$cooper_order?>' > <!-- 임직원 구매 -->
<input type='hidden' name='paycode' value='<?=$paycode?>' > <!-- 주문체크용 코드 -->

<?
	include ($Dir.TempletDir."order/orderTEM01.php");
?>

<?php
if($sumprice<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
} else if($sumprice<=0) {
	echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
}
?>

<input type='hidden' name='process' id='process' value="N">
<input type='hidden' name='escrow_limit' value="<?=$escrow_info["escrow_limit"]?>">
<input type='hidden' name='escrowcash' value="<?=$escrow_info["escrowcash"]?>">
<input type='hidden' name='paymethod'>
<!--input type=text name=bank_sender-->
<input type='hidden' name='pay_data1'>
<input type='hidden' name='pay_data2'>
<input type='hidden' name='sender_resno'>
<input type='hidden' name='sender_tel'>
<input type='hidden' name='home_tel'>
<input type='hidden' name='receiver_tel1'>
<input type='hidden' name='receiver_tel2'>
<input type='hidden' name='receiver_addr'>
<input type='hidden' name='order_msg'>
<input type="hidden" name="sender_email">
<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?php }?>
</form>

<form name=couponform action="<?=$Dir.FrontDir?>coupon.php" method=post target=couponpopup>
<input type=hidden name=sumprice id="sumprice" value="<?=$sumprice?>">
</form>

<form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay.php" target=orderpaypop>
<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?php }?>
<input type=hidden name=coupon_code>
<input type=hidden name=usereserve>
<input type=hidden name=usepoint>
<input type=hidden name=email>
<input type=hidden name=mobile_num1>
<input type=hidden name=mobile_num>
<input type=hidden name=address>
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {

	if($('#dev_agree').prop('checked') == false){
		alert("구매에 동의하지 않으셨습니다.");
		$('#dev_agree').focus();
	}else{
		paymethod=document.form1.paymethod.value.substring(0,1);

		<?php  if(strlen($_ShopInfo->getMemid())==0) { ?>
			/*
		if(document.form1.dongi[0].checked!=true) {
			alert("개인정보보호정책에 동의하셔야 비회원 주문이 가능합니다.");
			document.form1.dongi[0].focus();
			return;
		}
		*/
		if(document.form1.sender_name.type=="text") {
			if(document.form1.sender_name.value.length==0) {
				alert("주문자 성함을 입력하세요.");
				document.form1.sender_name.focus();
				return;
			}
			if(!chkNoChar(document.form1.sender_name.value)) {
				alert("주문자 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표), *(별표)는 입력하실 수 없습니다.");
				document.form1.sender_name.focus();
				return;
			}
		}
		<?php  } ?>

		ispaymentcheck=false;
		for(i=0;i<document.form1.dev_payment.length;i++) {
			if(document.form1.dev_payment[i].checked) {
				ispaymentcheck=true;
				break;
			}
		}
		if(ispaymentcheck==false) {
			alert("결제방법을 선택하세요.");
			document.form1.paymethod.value="";
			return;
		}

/* 무통장 주석처리2017-02-22
		if(document.form1.paymethod.value=='B' && document.form1.bank_sender.value==''){
			alert("입금자명을 입력하세요.");
			document.form1.bank_sender.focus();
			return;
		}
*/

		if(document.form1.paymethod.value=='B' && document.form1.pay_data_sel.value==''){
			alert("입금계좌를 선택하세요.");
			return;
		}
		/*<?if($_ShopInfo->memid && $_ShopInfo->wsmember=="Y"){?>
		if(document.form1.paymethod.value=='B' && $(".receipt_yn:checked").length==0){
			alert("영수증 신청여부를 선택하세요.");
			return;
		}
		<?}?>*/
		document.form1.sender_email.value=document.form1.sender_email1.value+"@"+document.form1.sender_email2.value;

		if(document.form1.sender_email.value.length>0) {
			if(!IsMailCheck(document.form1.sender_email.value)) {
				alert("주문자 이메일 형식이 잘못되었습니다.");
				document.form1.sender_email.focus();
				return;
			}
		}

		if(document.form1.sender_tel1.value.length==0) {
			alert("주문자 휴대전화번호를 입력하세요.");
			document.form1.sender_tel1.focus();
			return;
		}
		if(document.form1.sender_tel2.value.length==0) {
			alert("주문자 휴대전화번호를 입력하세요.");
			document.form1.sender_tel2.focus();
			return;
		}
		if(document.form1.sender_tel3.value.length==0) {
			alert("주문자 휴대전화번호를 입력하세요.");
			document.form1.sender_tel3.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel1.value)) {
			alert("주문자 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form1.sender_tel1.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel2.value)) {
			alert("주문자 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form2.sender_tel2.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel3.value)) {
			alert("주문자 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form3.sender_tel3.focus();
			return;
		}
		document.form1.sender_tel.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value;
		

		//주문자 전화번호 추가
		/*
		if( !IsNumeric( $("#home_tel1").val() ) ) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			$("#home_tel1").focus();
			return;
		}
		if( !IsNumeric( $("#home_tel2").val() ) ) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			$("#home_tel2").focus();
			return;
		}
		if( !IsNumeric( $("#home_tel3").val() ) ) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			$("#home_tel3").focus();
			return;
		}
		*/
		//$("input[name='home_tel']").val( $("#home_tel1").val()+'-'+$("#home_tel2").val()+'-'+$("#home_tel3").val() );

		document.form1.home_tel.value=document.form1.home_tel1.value+"-"+document.form1.home_tel2.value+"-"+document.form1.home_tel3.value;

		if(document.form1.receiver_name.value.length==0) {
			alert("받는분 성함을 입력하세요.");
			document.form1.receiver_name.focus();
			return;
		}
		if(!chkNoChar(document.form1.receiver_name.value)) {
			alert("받는분 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표), *(별표)는 입력하실 수 없습니다.");
			document.form1.receiver_name.focus();
			return;
		}


		if(document.form1.receiver_tel21.value.length==0) {
			alert("받는분 휴대전화번호를 입력하세요.");
			document.form1.receiver_tel21.focus();
			return;
		}
		if(document.form1.receiver_tel22.value.length==0) {
			alert("받는분 휴대전화번호를 입력하세요.");
			document.form1.receiver_tel22.focus();
			return;
		}
		if(document.form1.receiver_tel23.value.length==0) {
			alert("받는분 휴대전화번호를 입력하세요.");
			document.form1.receiver_tel23.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel21.value)) {
			alert("받는분 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel21.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel22.value)) {
			alert("받는분 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel22.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel23.value)) {
			alert("받는분 휴대전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel23.focus();
			return;
		}
		document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;

		document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

		if( document.form1.post.value.length==0 ) {
			alert("우편번호를 선택하세요.");
			openDaumPostcode();
			return;
		}

		if(document.form1.raddr1.value.length==0) {
			alert("주소를 입력하세요.");
			document.form1.raddr1.focus();
			return;
		}
		if(document.form1.raddr2.value.length==0) {
			alert("상세주소를 입력하세요.");
			document.form1.raddr2.focus();
			return;
		}
		if(!chkNoChar(document.form1.raddr2.value)) {
			alert("상세주소에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표), *(별표)는 입력하실 수 없습니다.");
			document.form1.raddr2.focus();
			return;
		}
		//20170911 메모 제한수 설정
		if(document.form1.order_prmsg.value){
			if(document.form1.order_prmsg.value.length>50) {
				alert("메모는 50자 이하로 입력해 주시기 바랍니다.");
				document.form1.order_prmsg.focus();
				return;
			}
		}

		if(paymethod.length==0) {
			alert('결제 수단을 선택해주세요.');
			return;
		}


	<?php  if(strlen($_ShopInfo->getMemid())>0) { ?>
		<?php  if($_data->reserve_maxuse>=0 && ord($okreserve) && $okreserve>0) { ?>
		if(document.form1.usereserve.value > <?=$okreserve?>) {
			alert("포인트 사용가능금액보다 큽니다.");
			document.form1.usereserve.focus();
			return;
		} else if(document.form1.usereserve.value < 0) {
			alert("포인트는 0원보다 크게 사용하셔야 합니다.");
			document.form1.usereserve.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && ord($okpoint) && $okpoint>0) { ?>
		if(document.form1.usepoint.value > <?=$okpoint?>) {
			alert("E포인트 사용가능금액보다 큽니다.");
			document.form1.usepoint.focus();
			return;
		} else if(document.form1.usepoint.value < 0) {
			alert("E포인트는 0원보다 크게 사용하셔야 합니다.");
			document.form1.usepoint.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && ord($okreserve) && $okreserve>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
		if(document.form1.usereserve.value>0 && document.form1.coupon_code.value.length==8){
			alert('포인트와 쿠폰을 동시에 사용이 불가능합니다.\n둘중에 하나만 사용하시기 바랍니다.');
			document.form1.usereserve.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
		if (document.form1.usereserve.value>0) {
			if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
				alert('포인트는 현금결제시에만 사용이 가능합니다.\n현금결제로 선택해 주세요');
				document.form1.paymethod.value="";
				return;
			}
		}
		<?php  } ?>
		<?php  if($_data->reserve_maxuse>=0 && ord($okpoint) && $okpoint>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
		if(document.form1.usepoint.value>0 && document.form1.coupon_code.value.length==8){
			alert('E포인트와 쿠폰을 동시에 사용이 불가능합니다.\n둘중에 하나만 사용하시기 바랍니다.');
			document.form1.usepoint.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
		if (document.form1.usepoint.value>0) {
			if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
				alert('포인트는 현금결제시에만 사용이 가능합니다.\n현금결제로 선택해 주세요');
				document.form1.paymethod.value="";
				return;
			}
		}
		<?php  } ?>
	<?php  } ?>

	<?php  if ($_data->payment_type=="Y" || $_data->payment_type=="N") { ?>
		if(paymethod=="B" && document.form1.pay_data1.value.length==0) {
			if(typeof(document.form1.usereserve)!="undefined" && typeof(document.form1.usepoint)!="undefined") {
				if((document.form1.usereserve.value+document.form1.usepoint.value)<<?=$sumprice-$salemoney?>) {
					alert("은행을 선택하세요.");
					return;
				}
			} else if(typeof(document.form1.usereserve)!="undefined") {
				if(document.form1.usereserve.value<<?=$sumprice-$salemoney?>) {
					alert("은행을 선택하세요.");
					return;
				}
			} else if(typeof(document.form1.usepoint)!="undefined") {
				if(document.form1.usepoint.value<<?=$sumprice-$salemoney?>) {
					alert("은행을 선택하세요.");
					return;
				}
			} else {
				alert("은행을 선택하세요.");
				return;
			}
		}
	<?php  } ?>

		prlistcnt="<?=$arr_prlist?>"+0;
		if(document.form1.msg_type.value=="1") {
			message_len = document.form1.order_prmsg.value.length;
			message_end = document.form1.order_prmsg.value.charCodeAt(message_len-1);
			if (message_len>0 && (message_end==39 || message_end==34 || message_end==92) ) {
				document.form1.order_prmsg.value += " ";
			}
		} else if(document.form1.msg_type.value=="2") {
			for(j=0;j<prlistcnt;j++) {
				message_len = document.form1["order_prmsg"+j].value.length;
				message_end = document.form1["order_prmsg"+j].value.charCodeAt(message_len-1);
				if (message_len>0 && (message_end==39 || message_end==34 || message_end==92) ) {
					document.form1["order_prmsg"+j].value += " ";
				}
			}
		}
/*
		document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value + "-" + document.form1.rpost2.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;
*/
		document.form1.receiver_addr.value = "우편번호 : " + document.form1.post.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;
		document.form1.order_msg.value="";

		if(document.form1.process.value=="N") {
		<?php  if(ord($etcmessage[1])) {?>
			if(document.form1.nowdelivery.checked) {
				document.form1.order_msg.value+="<font color=red>희망배송일 : 가능한 빨리배송</font>";
			} else {
				document.form1.order_msg.value+="<font color=red>희망배송일 : "+document.form1.year.value+"년 "+document.form1.mon.value+"월 "+document.form1.day.value+"일";
				<?php  if(strlen($etcmessage[1])==6) { ?>
				document.form1.order_msg.value+=" "+document.form1.time.value;
				<?php  } ?>
				document.form1.order_msg.value+="</font>";
			}
		<?php  } ?>
/* 무통장 주석처리 2017-02-22 */
		<?php  if($etcmessage[2]=="Y") { ?>
			if(document.form1.bank_sender.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
				if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
				document.form1.order_msg.value+="입금자 : "+document.form1.bank_sender.value;
			}
		<?php  } ?>

			if(document.form1.addorder_msg=="[object]") {
				if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
				document.form1.order_msg.value+=document.form1.addorder_msg.value;
			}
			document.form1.process.value="Y";
			document.form1.target = "PROCESS_IFRAME";

	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ORDER"]=="Y") {?>
			document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order.php';
	<?php }?>
			set_coupon_layer(); // 쿠폰 레이어 생성
//			$(".button_open").hide();
//			$(".button_close").show();
			$("#dimm-loading").show();
			document.form1.submit();

            if ( paymethod == "Y" ) {
                // 페이코 결제인 경우
                // 해당 내용을 pg_url에서 해준다.
            } else {
				//$("#paybuttonlayer").removeClass('hide');
				//$("#paybuttonlayer").addClass('hide');
				//$("#payinglayer").removeClass('hide');
                //document.all.paybuttonlayer.style.display="none";
               // document.all.payinglayer.style.display="block";
            }

			if(paymethod!="B") ProcessWait("visible");

		} else {
			ordercancel();
			$("#dimm-loading").hide();
		}
	}
}

var total_deli_price = 0; //선불 배송료
var total_deli_price2 = 0; //착불배송료

$(document).ready(function(){
/*
    total_deli_price = parseInt( $('#total_deli_price').val() ); // 선불 배송료
    total_deli_price2 = parseInt( $('#total_deli_price2').val() ); //착불배송료
	
    //선/착불 선택
    $(document).on( 'change', 'select[name^="deli_select"]', function(){
        var vender = $(this).attr('data-vender');
        var select_type = $(this).val();
        var vender_deli_price = parseInt( $('input[name="select_price[' + vender + ']"]').val() );

        if( $('input[name="dcoupon_ci_no"]').length > 0 ){
            if( $('input[name="dcoupon_ci_no"]').prop('checked') ){
                if( confirm('배송비 무료 쿠폰이 해제 됩니다.') ){
                    $('input[name="dcoupon_ci_no"]').prop( 'checked', false );
                    $('input[name="dcoupon_ci_no"]').trigger('click');
                }
            }
        }

        if( select_type == 1 ){
            total_deli_price = total_deli_price - vender_deli_price;
            total_deli_price2 = total_deli_price2 + vender_deli_price;
        } else {
            total_deli_price = total_deli_price + vender_deli_price;
            total_deli_price2 = total_deli_price2 - vender_deli_price;
        }

        $('#total_deli_price').val( total_deli_price );
        $('#total_deli_price2').val( total_deli_price2 );
        $('#delivery_price').html( comma( total_deli_price ) );
        $('#delivery_price2').html( comma( total_deli_price2 ) );
        $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );

        $('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
		$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
    });
	
    // 배송비 쿠폰
    $('input[name="dcoupon_ci_no"]').click(function(){
        var dcoupon_price = total_deli_price; // 넘어가는 값의 배송료를 빼주기 위함
        if( $(this).prop('checked') ) {
            $('input[name="dcoupon_price"]').val( dcoupon_price );
            $('#delivery_price').html( comma( 0 ) );
            $('#delivery_price2').html( comma( 0 ) );

            $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( 0 ) + parseInt( $('#total_deli_price_area').val() ) ) );

			$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( 0 ) + parseInt( $('#total_deli_price_area').val() ) ) );
			$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
        } else {
            $('input[name="dcoupon_price"]').val( 0 );
            $('#delivery_price').html( comma( total_deli_price ) );
            $('#delivery_price2').html( comma( total_deli_price2 ) );
            $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );

			$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
			$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
        }
    });
*/
	
	//주문 메세지 복사
	$("#prmsg_chg").on('change', function(){
   		$('#order_prmsg').val( $(this).val() );
        if( $(this).val() == '' )
			$('#order_prmsg').focus();
	} );
	
	email_change();
});

//-->
</SCRIPT>

<!-- 쿠폰 스크립트 -->
<script>
    var _prCouponObj       = [];   // 상품쿠폰 내용
   // var _bkCouponObj       = {};   // 장바구니쿠폰 내용
   // var _deliCouponObj     = {};   // 배송비 무료쿠폰 내용
    var _sum_price         = 0; // 상품 결제가
    var _total_prdc        = 0; // 상품쿠폰가
    var _total_bdc         = 0; // 장바구니 쿠폰가
    var _total_mileage     = 0; //마일리지
	var _total_point     = 0; //e포인트
	var _coupon_arr       = [];   // 쿠폰사용정보 배열

    var total_sum = 0;
    var total_deli_price  = 0 // 선불 배송료
    var total_deli_price2 = 0 //착불배송료
    var useand_pc_yn = "<?=$_CouponInfo->coupon['useand_pc_yn']?>";
    var all_type     = "<?=$_CouponInfo->coupon['all_type']?>";

    $(document).ready( function() {
        total_deli_price  = parseInt( $('#total_deli_price').val() ); // 선불 배송료
        total_deli_price2 = parseInt( $('#total_deli_price2').val() ); //착불배송료
        _sum_price        = parseInt( $('#total_sum').val() );  // 총 상품 가격
        _before_deliprice = parseInt( $('#total_deli_price').val() ); // 선불 배송료
        _after_deliprice  = parseInt( $('#total_deli_price2').val() ); // 착불 배송료
		_prCoupon_area    = $('input[name="obj_basketidx"]');
        total_sum         = _sum_price;
		//상품쿠폰 초기화
		
        $.each( _prCoupon_area, function( _i, _obj ) {
		    var tmp_obj = {
                "basketidx"     : _obj.value,
                "ci_no"         : $('input[name="obj_ci_no['+_obj.value+']"]').val(),
                "coupon_code"	: $('input[name="obj_coupon_code['+_obj.value+']"]').val(),
				"dc"			: $('input[name="obj_dc['+_obj.value+']"]').val(),
                "product_price" : $('input[name="obj_product_price['+_obj.value+']"]').val()
            };
				
            _prCouponObj[_i] = { "obj" : tmp_obj };
			
        });
		/*
        // 장바구니 쿠폰 obj를 초기화
        _bkCouponObj = {
            "ci_no"       : "",
            "type"        : "",
            "coupon_code" : "",
            "coupon_type" : "",
            "sellprice"   : _sum_price,
            "dc"          : 0
        };

        // 배송비 무료 쿠폰 obj를 초기화
        _deliCouponObj = {
            "ci_no"       : "",
            "type"        : "",
            "coupon_code" : "",
            "coupon_type" : "",
            "sellprice"   : _sum_price,
            "dc"          : 0
        }
*/
    });
    // 상품쿠폰 세팅
    function set_product_coupon ( checkbasket, checked ){ // 쿠폰 기본세팅
		
		_prCoupon_area    = $('input[name="obj_basketidx"]');
		_prCoupon_radio    = $('input[name="product_coupon"]');
		
		// 상품쿠폰 체크 및 체크해제
        $.each( _prCoupon_radio, function( _e, _obje ) {
			var basketidx   = $(this).data('basketidx');
			var ci_no       = $(this).val();
			var coupon_code = $(this).data('cp-code');
			var price       = $(this).data('pr-price');

			if(checked=="false" && checkbasket==basketidx) $(this).attr("checked", false);

			if($(this).is(":checked")==true){
				$('input[name="obj_ci_no['+basketidx+']"]').val(ci_no);
				$('input[name="obj_coupon_code['+basketidx+']"]').val(coupon_code);
			//	$('input[name="obj_dc['+basketidx+']"]').val(_obje.obj.dc);
				$('input[name="obj_product_price['+basketidx+']"]').val(price);
			}
			
		});
		// 상품쿠폰 셋팅
        $.each( _prCoupon_area, function( _i, _obj ) {
			var tmp_obj = {
                "basketidx"     : _obj.value,
                "ci_no"         : $('input[name="obj_ci_no['+_obj.value+']"]').val(),
                "coupon_code"	: $('input[name="obj_coupon_code['+_obj.value+']"]').val(),
				"dc"			: $('input[name="obj_dc['+_obj.value+']"]').val(),
                "product_price" : $('input[name="obj_product_price['+_obj.value+']"]').val()
            };
            _prCouponObj[_i] = { "obj" : tmp_obj };
			
        });
		pc_price_sum(function back() {
			_total_prdc = product_coupon_dc();
			coupon_update();
			$('div.coupon .btn-close').trigger('click');
		});
        // _prCouponObj = _coupon_arr;   // 상품쿠폰 내용
		
		
    }

	//체크해제 
	function prd_coupon_cancel(basketidx){
		$('input[name="obj_ci_no['+basketidx+']"]').val("");
		$('input[name="obj_coupon_code['+basketidx+']"]').val("");
		$('input[name="obj_dc['+basketidx+']"]').val("");
		set_product_coupon(basketidx, "false");
	}

	 // 상품쿠폰 가격을 계산 및 합산
    function pc_price_sum( callback ){

        $.each( _prCouponObj , function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
				
                $.ajax({
                    method : "POST",
                    url : "../front/ajax_coupon_select.php",
                    data : { mode : 'P01', sellprice : _obj.obj.product_price , ci_no : _obj.obj.ci_no },
                    dataType : "json"
                }).done ( function( data ){
					if( data.mini_price > _sum_price ){
						alert('구매 금액이 ' + comma( data.mini_price ) + '이상 주문시 가능합니다.' );
						prd_coupon_cancel(_obj.obj.basketidx);	
						callback();
                    } else {
						
                        var tmp_obj = {
                            coupon_type : data.coupon_type,
                            dc : data.dc,
                            type : data.type
                        }
                        $.extend( _obj.obj, tmp_obj );
						callback();
                    }
                });
            } else {
                $.extend( _obj.obj, { "dc" : 0 } );
				callback();
            }
        });

		
    }
    // 상품쿠폰 팝업
    function product_coupon_pop(basidx){
        coupon_use_type( 1 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        if( reset_dc( 3 ) ){
			$.ajax({
				cache: false,
				type: 'POST',
				url: 'product_coupon_layer.php',
				data : { basketidxs : basidx, prCouponObj : _prCouponObj },
				success: function(data) {
					$(".coupon_list").html(data);
					$('.popList.coupon').show();
				//	$('html,body').css('position','fixed');
				}
			});
        }
    }

    // 20170902 상품쿠폰 팝업(제휴사)
    function product_ccoupon_pop(basidx,product_price){
        coupon_use_type( 1 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        if( reset_dc( 3 ) ){
			$.ajax({
				cache: false,
				type: 'POST',
				url: 'product_ccoupon_layer.php',
				data : { basketidxs : basidx, price : product_price, prCouponObj : _prCouponObj },
				success: function(data) {
					$(".coupon_list").html(data);
					$('.popList.coupon').show();
				//	$('html,body').css('position','fixed');
				}
			});
        }
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

    // 상품쿠폰 닫기
    function product_coupon_close(){
    	$("#coupon_view").hide();
         $('#coupon_layer').fadeOut('fast');
    }
    // 상품쿠폰 할인 금액 ( 총액 )
    function product_coupon_dc(){
        var prd_dc = 0;
		
        $.each( _prCouponObj, function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
				prd_dc += parseInt(_obj.obj.dc);
            }
        });

        return prd_dc;
    }
    // 상품쿠폰 초기화
    function reset_prdc(){
		_prCoupon_area    = $('input[name="obj_basketidx"]');
		
		// 상품쿠폰 obj를 초기화
        $.each( _prCoupon_area, function( _i, _obj ) {
			$('input[name="obj_ci_no['+_obj.value+']"]').val("");
			$('input[name="obj_coupon_code['+_obj.value+']"]').val("");
			$('input[name="obj_dc['+_obj.value+']"]').val("");
		});
        _total_prdc  = 0;
        _prCouponObj = [];
    }
/*
    // 장바구니 쿠폰
    function default_set_basket_coupon(){
         _total_bdc = 0;
        _bkCouponObj = {
            "ci_no"       : "",
            "type"        : "",
            "coupon_code" : "",
            "coupon_type" : "",
            "sellprice"   : _sum_price,
            "dc"          : 0
        };
        $('.CLS_coupon_value').val('');
    }

    // 장바구니 쿠폰 선택
    function set_basket_coupon( ci_no ){
        var basket_sellprice =  total_sum - parseInt( _total_prdc );
        coupon_use_type( 2 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        if( ci_no != '' && reset_dc( 2 ) ){
            $.ajax({
                method : "POST",
                url : "ajax_coupon_select.php",
                data : { mode : 'B01', ci_no : ci_no, sellprice : basket_sellprice },
                dataType : "json"
            }).done( function( data ) {
                if( data.mini_price > total_sum ){
                    alert('구매 금액이 ' + comma( data.mini_price ) + '이상 주문시 사용이 가능합니다.' );
                    default_set_basket_coupon();
                    coupon_update();
                } else {
                    $.extend( _bkCouponObj, data )
                    _total_bdc = data.dc;
                    coupon_update();
                }
            });
        }
    }
	*/
    // 쿠폰 선택 초기화
    function reset_dc( type ){
        var resetType  = false;
        if( (_total_mileage > 0 ||_total_point > 0) && _total_bdc > 0 && ( type == 2 || type == 3 ) ){
            if( confirm('선택한 쿠폰 / 포인트 / E포인트가 초기화 됩니다. 쿠폰을 다시 선택하시겠습니까?') ){
                mileage_cancel();
				point_cancel();
                reset_prdc();
               // default_set_basket_coupon();
                coupon_update();
                resetType = true;
            }
        } else if( _total_bdc > 0 && type == 3 ){
            if( confirm('선택한 할인쿠폰이 초기화 됩니다. 쿠폰을 다시 선택하시겠습니까?') ){
                //default_set_basket_coupon();
                coupon_update();
                resetType = true;
            }
        } else if( _total_mileage > 0 || _total_point > 0 ){
                if( confirm('포인트 / E포인트가 초기화 됩니다.') ){
                mileage_cancel();
				point_cancel();
                coupon_update();
                resetType = true;
            }
        } else {
            resetType = true;
        }

        return resetType;
    }

    //선/착불 선택
/*
    $(document).on( 'click', 'select[name="dcoupon_ci_no"]', function(){
        var vender = $(this).attr('data-vender');
        var select_type = $(this).val();
        var vender_deli_price = parseInt( $('input[name="select_price[' + vender + ']"]').val() );

        if( $('input[name="dcoupon_ci_no"]').length > 0 ){
            if( $('input[name="dcoupon_ci_no"]').prop('checked') ){
                if( confirm('배송비 무료 쿠폰이 해제 됩니다.') ){
                    $('input[name="dcoupon_ci_no"]').prop( 'checked', false );
                    $('input[name="dcoupon_ci_no"]').trigger('click');
                }
            }
        }

        if( select_type == 1 ){
            total_deli_price = total_deli_price - vender_deli_price;
            total_deli_price2 = total_deli_price2 + vender_deli_price;
        } else {
            total_deli_price = total_deli_price + vender_deli_price;
            total_deli_price2 = total_deli_price2 - vender_deli_price;
        }

        $('#total_deli_price').val( total_deli_price );
        $('#total_deli_price2').val( total_deli_price2 );
        $('#delivery_price').html( comma( total_deli_price ) );
        $('#delivery_price2').html( comma( total_deli_price2 ) );
        $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );

		$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
		$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
    });

    // 배송비 쿠폰
    $('input[name="dcoupon_ci_no"]').click(function(){
        var dcoupon_price = total_deli_price; // 넘어가는 값의 배송료를 빼주기 위함
        if( $(this).prop('checked') ) {
            $('input[name="dcoupon_price"]').val( dcoupon_price );
            $('#delivery_price').html( comma( 0 ) );
            $('#delivery_price2').html( comma( 0 ) );
            $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( 0 ) + parseInt( $('#total_deli_price_area').val() ) ) );

			$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( 0 ) + parseInt( $('#total_deli_price_area').val() ) ) );
			$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
        } else {
            $('input[name="dcoupon_price"]').val( 0 );
            $('#delivery_price').html( comma( total_deli_price ) );
            $('#delivery_price2').html( comma( total_deli_price2 ) );
            $('.price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );

			$('#all_price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
			$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) + parseInt( _total_point ) ) );
        }
    });
*/
    function coupon_update(){
        $('.CLS_prCoupon').html( comma( _total_prdc ) );
        //$('.CLS_bCoupon').html( comma( _total_bdc ) );
        $('.CLS_saleMil').html( comma( _total_mileage ) );
		$('.CLS_salePoi').html( comma( _total_point ) );
		$('.CLS_Tprice').html( comma( total_sum - parseInt( _total_prdc ) + parseInt( $('#total_deli_price').val() ) ) );

		$('.price_sum').html( comma( total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
        $('#total_sumprice').val( total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) );

		$('#all_price_sum').html( comma( total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt( _total_point ) + parseInt( $('#total_deli_price').val() ) + parseInt( $('#total_deli_price_area').val() ) ) );
		$('#all_dc_price_sum').html( comma( parseInt( _total_prdc ) + parseInt( _total_bdc ) + parseInt( _total_mileage ) - parseInt( _total_point ) ) );
    }

    //숫자키 이외의 값은 막는다
    $(document).on( 'keydown', '#mileage-use', function ( event ) {
        if( !isNumKey( event ) ) event.preventDefault();
        if( event.keyCode != 8 && $(this).val().length > 0 ) $(this).val( parseInt( $(this).val() ) );
    });
	 //10원단위 0원처리
    $(document).on( 'change', '#mileage-use', function ( event ) {
        var mileage	= $(this).val();
		var mileagelength=mileage.length;
		var last_cut=mileage.substr(mileagelength-2, mileagelength);
		if(last_cut!='00' && mileagelength>=3){
			alert("10포인트단위 0원 처리됩니다.");
			$("#mileage-use").val(mileage.substr(0, mileagelength-2)*100);
			$("#mileage-use").keyup();
		}else if(mileagelength<3){
			alert("100포인트이상 사용가능하십니다.");
			$("#mileage-use").val("0");
			$("#mileage-use").keyup();
		}

    });

	 
    //마일리지 계산
    $(document).on( 'keyup', '#mileage-use', function ( event ) {
        coupon_use_type( 3 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        var okreserve = parseInt( $('#okreserve').val() );
        var mileage	= parseInt( $(this).val() );
		var deli_area=  $('#total_deli_price_area').val();
		

		//배송비는 마일리지를 사용할수없음
        //var sum_price = total_sum + total_deli_price + parseInt( deli_area ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_point );
		var sum_price = total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_point ) - parseInt("0");

        if( $(this).val().length > 0 ){
            if( okreserve < mileage ) {
                if( okreserve > sum_price ){
                    alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
                    $(this).val( sum_price );
                } else {
                    alert('보유 마일리지보다 큰 값을 입력했습니다.');
                    $(this).val( okreserve );
                }
            } else {
                if( mileage > sum_price ){
                    alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
                    $(this).val( sum_price );
                } else {
                    $(this).val( mileage );
                }
            }
        } else {
            $(this).val( 0 );
        }
        set_total_mileage();
        coupon_update();
    });

	//숫자키 이외의 값은 막는다
    $(document).on( 'keydown', '#point-use', function ( event ) {
        if( !isNumKey( event ) ) event.preventDefault();
        if( event.keyCode != 8 && $(this).val().length > 0 ) $(this).val( parseInt( $(this).val() ) );
    });
	 //10원단위 0원처리
    $(document).on( 'change', '#point-use', function ( event ) {
        var mileage	= $(this).val();
		var mileagelength=mileage.length;
		var last_cut=mileage.substr(mileagelength-2, mileagelength);
		if(last_cut!='00' && mileagelength>=3){
			alert("10포인트단위 0원 처리됩니다.");
			$("#point-use").val(mileage.substr(0, mileagelength-2)*100);
			$("#point-use").keyup();
		}else if(mileagelength<3){
			alert("100포인트이상 사용가능하십니다.");
			$("#point-use").val("0");
			$("#point-use").keyup();
		}

    });
    //포인트 계산
    $(document).on( 'keyup', '#point-use', function ( event ) {
        coupon_use_type( 3 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        var okpoint = parseInt( $('#okpoint').val() );
        var point	= parseInt( $(this).val() );
		var deli_area=  $('#total_deli_price_area').val();
		//배송비는 마일리지를 사용할수없음
        //var sum_price = total_sum + total_deli_price + parseInt( deli_area ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage );
		var sum_price = total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt("0");

        if( $(this).val().length > 0 ){
            if( okpoint < point ) {
                if( okpoint > sum_price ){
                    alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
                    $(this).val( sum_price );
                } else {
                    alert('보유 E포인트보다 큰 값을 입력했습니다.');
                    $(this).val( okpoint );
                }
            } else {
                if( point > sum_price ){
                    alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
                    $(this).val( sum_price );
                } else {
                    $(this).val( point );
                }
            }
        } else {
            $(this).val( 0 );
        }
        set_total_point();
        coupon_update();
    });

    //마일리지 모두사용
    $(document).on( 'change', '#all-mileage-use', function ( event ) {
		coupon_use_type( 3 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
		if($(this).is(":checked")){
			$('#mileage-use').val(parseInt( $('#okreserve').val() ));
		} else {
			$('#mileage-use').val( 0 );
		}
		var okreserve = parseInt( $('#okreserve').val() );
		var mileage	= parseInt( $('#mileage-use').val() );
		var deli_area=  $('#total_deli_price_area').val();
		//var sum_price = total_sum + total_deli_price + parseInt( deli_area ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_point );
		var sum_price = total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_point ) - parseInt("0");

		if( $('#mileage-use').val().length > 0 ){
			if( okreserve < mileage ) {
				if( okreserve > sum_price ){
					alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
					$('#mileage-use').val( sum_price );
				} else {
					alert('보유 마일리지보다 큰 값을 입력했습니다.');
					$('#mileage-use').val( okreserve );
				}
			} else {
				if( mileage > sum_price ){
					alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
					$('#mileage-use').val( sum_price );
				} else {
					$('#mileage-use').val( mileage );
				}
			}
		} else {
			$('#mileage-use').val( 0 );
		}
		set_total_mileage();
		coupon_update();
    });

	//포인트 모두사용
    $(document).on( 'change', '#check-epoint-all', function ( event ) {
		coupon_use_type( 3 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
		if($(this).is(":checked")){
			$('#point-use').val(parseInt( $('#okpoint').val() ));
		} else {
			$('#point-use').val( 0 );
		}
		var okpoint = parseInt( $('#okpoint').val() );
		var point	= parseInt( $('#point-use').val() );
		var deli_area=  $('#total_deli_price_area').val();
		//var sum_price = total_sum + total_deli_price + parseInt( deli_area ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage );
		var sum_price = total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) - parseInt("0");

		if( $('#point-use').val().length > 0 ){
			if( okpoint < point ) {
				if( okpoint > sum_price ){
					alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
					$('#point-use').val( sum_price );
				} else {
					alert('보유 E포인트보다 큰 값을 입력했습니다.');
					$('#point-use').val( okpoint );
				}
			} else {
				if( point > sum_price ){
					alert('최대 사용가능한 포인트는 '+comma(sum_price)+'P 입니다.');
					$('#point-use').val( sum_price );
				} else {
					$('#point-use').val( point );
				}
			}
		} else {
			$('#point-use').val( 0 );
		}
		set_total_point();
		coupon_update();
    });

    function set_total_mileage(){
        _total_mileage = parseInt( $('#mileage-use').val() );
    }

	function set_total_point(){
        _total_point = parseInt( $('#point-use').val() );
    }

    //마일리지 취소
    function mileage_cancel(){
        $("#mileage-use").val( 0 );
        _total_mileage = parseInt( $("#mileage-use").val() );
		$("#all-mileage-use").attr("checked", false);
    }
	//포인트 취소
    function point_cancel(){
        $("#point-use").val( 0 );
        _total_point = parseInt( $("#point-use").val() );
		$("#check-epoint-all").attr("checked", false);
    }
    // 쿠폰 설정 체크
    function coupon_use_type( type ){ // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지

        // 쿠폰 마일리지 동시사용 불가
        if( all_type != 'Y' ){
            if( ( type == 1 || type == 2 ) && (_total_mileage > 0 || _total_point > 0) ){
                if( confirm( "쿠폰과 포인트 및 E포인트는 동시사용이 불가능합니다.\n마일리지와 포인트를 다시 선택하시겠습니까?" ) ){
                    mileage_cancel();
					point_cancel();
                    coupon_update();
                } else {
                    reset_prdc();
                    //default_set_basket_coupon();
                    coupon_update();
                }
            } else if( type == 3 && ( _total_prdc > 0 || _total_bdc > 0 ) ) {
                if( confirm( "쿠폰과 포인트 및 E포인트는 동시사용이 불가능합니다.\n쿠폰을 다시 선택하시겠습니까?" ) ){
                    reset_prdc();
                    //default_set_basket_coupon();
                    coupon_update();
                } else {
                    mileage_cancel();
					point_cancel();
                    coupon_update();
                }
            }
        }

        // 상품 / 장바구니 쿠폰 동시사용 X
        if( useand_pc_yn != 'Y' ){
            if( type == 2 && _total_prdc > 0 ){
                if( confirm( "상품쿠폰과 할인쿠폰은 동시사용이 불가능합니다.\n쿠폰을 다시 선택하시겠습니까?" ) ){
                    reset_prdc();
                    coupon_update();
                } else {
                    //default_set_basket_coupon();
                    coupon_update();
                }
            }
        }

    }
    // 결제로 넘어갈 쿠폰값을 레이어에 넘겨준다
    function set_coupon_layer(){
        var prd_layer        = $('#ID_prd_coupon_layer'); // 상품쿠폰이 담길 레이어 위치
        //var bk_layer         = $('#ID_bk_coupon_layer');  // 장바구니 쿠폰이 담길 레이어 위치
        //var deli_layer       = $('#ID_deli_coupon_layer');  // 장바구니 쿠폰이 담길 레이어 위치
        var pr_coupon_html   = '';
        var bk_coupon_html   = '';
        var deli_coupon_html = '';

        // 상품쿠폰
        $.each( _prCouponObj, function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
                pr_coupon_html += '<input type="hidden" name="prcoupon_bridx[]" value="' + _obj.obj.basketidx + '" >';
                pr_coupon_html += '<input type="hidden" name="prcoupon_ci_no[]" value="' + _obj.obj.ci_no + '" >';
            }
        });
		/*
        // 장바구니 쿠폰
        if( _bkCouponObj.ci_no != '' ){
            bk_coupon_html += '<input type="hidden" name="bcoupon_ci_no[]" value="' + _bkCouponObj.ci_no + '" >';
        }
        //배송비 쿠폰
        if( _deliCouponObj.ci_no != '' ) {
            deli_coupon_html += '<input type="hidden" name="dcoupon_ci_no" value="' + _deliCouponObj.ci_no + '" >';
            deli_coupon_html += '<input type="hidden" name="dcoupon_price" value="' + _deliCouponObj.dc + '" >';
        }
*/
        $( prd_layer ).html( pr_coupon_html );
        //$( bk_layer ).html( bk_coupon_html );
        //$( deli_layer ).html( deli_coupon_html );

    }

	

</script>
<!-- //쿠폰 스크립트 -->
  <div id="coupon_view" style="display:none">
<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; display:none'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; display:none;"></IFRAME>
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME width="100%" height="500"></IFRAME>
<IFRAME id='CHECK_PAYGATE' name='CHECK_PAYGATE' style='display:none'></IFRAME>
<iframe class='layer-iframe' id='coupon_layer'  width="100%" height="800"></iframe>
</div>
<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
