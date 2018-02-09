<?
include_once('outline/header_m.php');

$Dir="../";
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
	echo "		location.href='/m/' ";
    echo "} ";
    echo "</script>";
    exit;
}


$basketidxs = $_REQUEST['basketidxs'];

# 임직원 구매기능 추가
$staff_order = $_REQUEST['staff_order']; // 임직원 구매 type
if( $staff_order == '' || $staff_order == 'undefined' ) $staff_order = 'N'; // 값이 없을때 예외처리
if( chk_staff_order( $staff_order ) == 0 ) { // 0 - 오류처리 1 - 일반구매 2 - 임직원 구매
    echo "<script>";
    echo "  alert('잘못된 구매 입니다.'); ";
    echo "  window.location.replace('basket.php')";
    echo "</script>";
    exit;
}

# 협력사 구매기능 추가
$cooper_order = $_REQUEST['cooper_order']; // 임직원 구매 type
if( $cooper_order == '' || $cooper_order == 'undefined' ) $cooper_order = 'N'; // 값이 없을때 예외처리
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
} else if($_ShopInfo->getCooperYn() == 'Y'  && $cooper_order == 'Y') { // 협력사 구매이면
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

$delitype_check="";
$staff_pr_price="0";

foreach( $_odata as $_proData =>$_proObj ){
	//exdebug($_proObj['vender']);
	$brandVenderArr[$_proObj['brand']]	=  $_proObj['vender'];

	$staff_pr_price+=($_proObj["ori_price"]-$_proObj["price"])*$_proObj["quantity"];
	if($cooper_order == 'Y'){
		list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
		$c_productcode = $_proObj['productcode'];
		list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
		list($consumerprice) = pmysql_fetch("select consumerprice from tblproduct where productcode= '".$c_productcode."' ");

		if($consumerprice == $_proObj["price"] || $_proObj["price"] > $company_price){
			$cooper_pr_price+=($consumerprice-$company_price)*$_proObj["quantity"];
		}else{
			$cooper_pr_price+=($consumerprice-$_proObj["price"])*$_proObj["quantity"];
		}
	}
	
	$delitype_check[$_proObj['delivery_type']]++;
}

//o2o주문과 택배주문은 동시에 같이 주문할수가없다.
if($delitype_check[0] && ( $delitype_check[1] > 0 || $delitype_check[2]) > 0 ){
	alert_go("택배주문과 O2O주문은 함께 주문하실수 없습니다.", "../front/basket.php");
	exit;
}

$brandArr = ProductToBrand_Sort( $_odata );

// 2016-04-21
// 장바구니의 상품을 가져오는 기능의 변경으로 인하여 상품을 불러오지 못할 경우 예외처리
if( count( $brandArr ) == 0 ){

    $sendReferer = parse_url( $_SERVER['HTTP_REFERER'] );

    if( strpos( $sendReferer['query'], 'basketidxs' ) === false ){

        echo "<script>";
        echo " alert( '상품이 존재하지 않습니다.' );";
        echo " window.location.replace('/m/basket.php'); ";
        echo "</script>";

    } else {

        $basketidx = substr( $sendReferer['query'], strpos( $sendReferer['query'], 'basketidxs=' ) + 11 );
        $basketidx_arr = explode( '|', $basketidx );

        if( strlen( $basketidx_arr[0] ) > 0 ){

            $b_sql = "SELECT productcode FROM tblbasket WHERE basketidx = '".$basketidx_arr[0]."' ";
            $b_res = pmysql_query( $b_sql, get_db_conn() );
            $b_row = pmysql_fetch_object( $b_res );
            pmysql_free_result( $b_res );

            echo "<script>";
            echo " alert( '다시 주문해주세요.' );";
            echo " window.location.replace('/m/productdetail.php?productcode=".$b_row->productcode."'); ";
            echo "</script>";

        } else {

            echo "<script>";
            echo " alert( '상품이 존재하지 않습니다.' );";
            echo " window.location.replace('/m/basket.php'); ";
            echo "</script>";

        }

    }
    exit;
}

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
		//$email = $row->email;
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

#쿠폰 설정
$_CouponInfo = new CouponInfo();
# 회원쿠폰
$_CouponInfo->search_member_coupon( $_ShopInfo->memid, 1, 1 );
$memCoupon      = $_CouponInfo->mem_coupon;

//$member_coupon = MemberCoupon( 1, 'P', 'BC' );

#상품쿠폰과 장바구니 쿠폰을 나눈다
$basket_coupon = array();
$product_coupon = array();
$deliver_coupon = array();
$chk_coupon     = array();
$product_coupon_cnt = 0;
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
			$opt_price = 0; // 상품별 옶션가
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
		// 임직원 포인트을 가져온다.
		list($staff_reserve)=pmysql_fetch("select staff_reserve from tblmember where id='".$_ShopInfo->getMemid()."'");// 임직원 포인트
		if($staff_pr_price > $staff_reserve) { // 포인트가 부족하면
			echo "<script>";
			echo "  alert('보유하신 임직원 적립금이 부족합니다.'); ";
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
		list($cooper_reserve)=pmysql_fetch("select cooper_reserve from tblmember where id='".$_ShopInfo->getMemid()."'");// 제휴사 적립금
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
						$stockArrayCheck[$product['prodcode'].$tmp_opt_contetnt[$optKey].$product['store_code']]['delivery_type'] = $product['delivery_type'];	//2016-10-07 libe90 발송구분 변수할당
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
		if($v['prodcode'] && $v['colorcode']){
			/*if ($v['delivery_type']=='0' && $staff_order=="Y"){
				$shopRealtimeStock=getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $sync_bon_code);
			}else */
			if ($v['delivery_type']=='0') {	//2016-10-07 libe90 매장발송 재고체크 분기
				$shopRealtimeStock = getErpProdShopStock_Type($v['prodcode'], $v['colorcode'], $v['size'], 'delivery');
				$shopRealtimeStock['sumqty']=$shopRealtimeStock['availqty'];
				$stockArrayCheck[$v['prodcode'].$v['chk_key']]['store_code'] = $shopRealtimeStock['shopcd'];
			}else{
				$shopRealtimeStock = getErpPriceNStock($v['prodcode'], $v['colorcode'], $v['size'], $v['store_code']);
			}
			if($v['quantity'] > $shopRealtimeStock['sumqty']){
				alert_go("[".$v['productname']."]재고가 부족합니다.\\r해당상품의 최대주문가능수량은 ".$shopRealtimeStock['sumqty']." 개 입니다. \\r장바구니 페이지로 이동합니다.", "../m/basket.php");	//2016-10-07 libe90 문구변경
				exit;
			}
		}
	}
}

?>

<script>
var market_pic="<?=$delitype_check[1]?>";
function deli_area_check(zipcode){

	if(zipcode && !market_pic){
		$.ajax({
			method : "POST",
			url : "../front/product_deli_area.php",
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


$(document).ready(function(){
	
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

	//주문 메세지 복사
	$("#prmsg_chg").on('change', function(){
   		$('#order_prmsg').val( $(this).val() );
        if( $(this).val() == '' )
			$('#order_prmsg').focus();
	} );
	
	email_change();
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

	$('.delivery .btn_close').trigger('click');
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
		}

	}
}

function PaymentOpen() {// 사용 안하는것 같음
	PROCESS_IFRAME.PaymentOpen();
}

function sel_paymethod(obj){

//    console.log(obj.value);

    var frm=document.form1;
    var	totp=uncomma(document.getElementById("price_sum").innerHTML);

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

/*
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
*/
    if(obj.value=='Q'){

        if(frm.escrowcash.value=='Y' && (frm.escrow_limit.value>parseInt(totp))){

            alert('총 결제금액이'+frm.escrowcash.value+'이상일때만 에스크로 결제가 가능합니다.');
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


function email_change(){

	var email_select=$('select[name="email_select"]').val();
	if(email_select){
		$("#sender_email2").val(email_select);
		$("#sender_email2").hide();
	}else{
		$("#sender_email2").show();
	}
}


</script>


<!-- 내용 -->
<main id="content" class="subpage">
	
	<!-- 쿠폰사용 팝업 -->
	<section class="pop_layer layer_use_coupon">
		<div class="inner">
			<h3 class="title">쿠폰사용<button type="button" class="btn_close">닫기</button></h3>
			<div class="coupon_list">
				
			</div>
		</div>
	</section>
	<!-- //쿠폰사용 팝업 -->

	<!-- 배송지 목록 팝업 -->
	<section class="pop_layer layer_deli_site delivery">
		
		<div class="inner">
			<h3 class="title">배송지 목록<button type="button" class="btn_close">닫기</button></h3>
			<div class="list_type">
				<table class="list_with_radio">
					<colgroup>
						<col style="width:40px;">
						<col style="width:auto;">
					</colgroup>
					<tbody>
<?
foreach( $dn_info as $dn_vkey=>$dn_val ){
	//exdebug($dn_val);
?>
						<tr>
							<th><input type="radio" class="radio_def deli_check"  name="my_deliveryList" id="deliver_list<?=$dn_vkey?>" onClick="javascript:Dn_InReceivercheck('<?=$dn_val->no.'|@|'.$dn_val->destination_name.'|@|'.$dn_val->get_name.'|@|'.addMobile($dn_val->mobile).'|@|'.$dn_val->postcode.'|@|'.$dn_val->postcode_new.'|@|'.$dn_val->addr1.'|@|'.$dn_val->addr2?>')"></th>
							<td>
								<label for="deliver_list<?=$dn_vkey?>">
									<p class="name"><?=$dn_val->destination_name?></p>
									<p class="mt-5"><?=$dn_val->addr1?> <?=$dn_val->addr2?></p>
								</label>
							</td>
						</tr>
<?
}
?>
					</tbody>
				</table>

				<div class="btn_area">
					<ul class="ea2">
						<li><a href="javascript:;" class="btn-line h-large" onclick="javascript:Dn_InReceiver('cancel')">취소</a></li>
						<li><a href="javascript:;" class="btn-point h-large" onclick="javascript:Dn_InReceiver('in')">적용</a></li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<!-- //배송지 목록 팝업 -->

	<!-- 매장안내 팝업 -->
	<section class="pop_layer layer_store_info pop-infoStore">
		<div class="inner">
			<h3 class="title">매장 위치정보 <button type="button" class="btn_close">닫기</button></h3>
			<div class="select_store">
				<div class="store_view"></div>					
			</div><!-- //.select_store -->
		</div>
	</section>
	<!-- //매장안내 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>주문/결제</span>
		</h2>
		<div class="page_step">
			<ul class="clear">
				<li><span class="icon_order_step01"></span>장바구니</li>
				<li class="on"><span class="icon_order_step02"></span>주문하기</li>
				<li><span class="icon_order_step03"></span>주문완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->
<form id="form" name="form1" action="<?=$Dir.FrontDir?>ordersend.php" method="post">
	<input type="hidden" name="dn_inr" id="dn_inr">

	<input type="hidden" name="addorder_msg" value="">
	<input type="hidden" id="direct_deli" name="direct_deli" value="N">
	<input type='hidden' name='basketidxs' value='<?=$basketidxs?>' > <!-- 장바구니 번호 -->
	<input type='hidden' name='staff_order' value='<?=$staff_order?>' > <!-- 임직원 구매 -->
	<input type='hidden' name='cooper_order' value='<?=$cooper_order?>' > <!-- 임직원 구매 -->
	<input type='hidden' name='paycode' value='<?=$paycode?>' > <!-- 주문체크용 코드 -->
	<input type='hidden' name='process' id='process' value="N">
	<input type='hidden' name='escrow_limit' value="<?=$escrow_info["escrow_limit"]?>">
	<input type='hidden' name='escrowcash' value="<?=$escrow_info["escrowcash"]?>">
	<input type='hidden' name='paymethod'>
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
	<input type='hidden' name='shopurl' value="<?=$_SERVER['HTTP_HOST']?>">
	<?php }?>
	<input type='hidden' name='coupon_code'>
	<input type='hidden' name='usereserve'>
	<input type='hidden' name='usepoint'>
	<input type='hidden' name='email'>
	<input type='hidden' name='mobile_num1'>
	<input type='hidden' name='mobile_num'>
	<input type='hidden' name='address'>




<script>



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


        if(document.form1.paymethod.value=='B' && document.form1.pay_data_sel.value==''){
            alert("입금계좌를 선택하세요.");
            return;
        }
*/
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
/*
        if(document.form1.sender_email.value.length>0) {
            if(!IsMailCheck(document.form1.sender_email.value)) {
                alert("주문자 이메일 형식이 잘못되었습니다.");
                document.form1.sender_email.focus();
                return;
            }
        }
*/
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

		document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

        document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;

         //$("input[name='home_tel']").val( $("#home_tel1").val()+'-'+$("#home_tel2").val()+'-'+$("#home_tel3").val() );

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


        if(paymethod.length==0) {
            alert('결제 수단을 선택해주세요.');
            //orderpaypop();
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

/*
    <?php  if ($_data->payment_type=="Y" || $_data->payment_type=="N") { ?>
        if(paymethod=="B" && document.form1.pay_data1.value.length==0) {
            if(typeof(document.form1.usereserve)!="undefined") {
                if(document.form1.usereserve.value<<?=$sumprice-$salemoney?>) {
                    alert("은행을 선택하세요.");
                    //orderpaypop();
                    return;
                }
            } else {
                alert("은행을 선택하세요.");
                //orderpaypop();
                return;
            }
        }
    <?php  } ?>
*/
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
/*
        <?php  if($etcmessage[2]=="Y") { ?>
            if(document.form1.bank_sender.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
                if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
                document.form1.order_msg.value+="입금자 : "+document.form1.bank_sender.value;
            }
        <?php  } ?>
*/
            if(document.form1.addorder_msg=="[object]") {
                if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
                document.form1.order_msg.value+=document.form1.addorder_msg.value;
            }
            document.form1.process.value="Y";
            //document.form1.target = "PROCESS_IFRAME";

    <?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ORDER"]=="Y") {?>
            document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order.php';
    <?php }?>
			set_coupon_layer(); // 쿠폰 레이어 생성
			$(".button_open").hide();
			$(".button_close").show();

            document.form1.submit();
            //document.all.paybuttonlayer.style.display="none";
            //$('div[name="paybuttonlayer"]').hide();
            //document.all.payinglayer.style.display="block";


            //if(paymethod!="B") ProcessWait("visible");

        } else {
            ordercancel();
        }
    }
}


var total_deli_price = 0; //선불 배송료
var total_deli_price2 = 0; //착불배송료

</script>


<!-- 쿠폰 -->
<script>
    var _prCouponObj       = [];   // 상품쿠폰 내용
    //var _bkCouponObj       = {};   // 장바구니쿠폰 내용
    //var _deliCouponObj     = {};   // 배송비 무료쿠폰 내용

    var _sum_price         = 0; // 상품 결제가
	var _total_prdc        = 0; // 상품쿠폰가
	var _total_bdc         = 0; // 장바구니 쿠폰가
    var _total_mileage     = 0; //마일리지
	var _total_point     = 0; //e포인트

    var _before_deli_dc    = 0; // 선불 배송비 무료
    var _after_deli_dc     = 0; // 후불 배송비 무료

    var _prCoupon_area     = null; // 상품별 영역

    var total_sum = 0;
    var useand_pc_yn = "<?=$_CouponInfo->coupon['useand_pc_yn']?>";
    var all_type     = "<?=$_CouponInfo->coupon['all_type']?>";
    // 쿠폰 내용 초기화
    $(document).ready( function() {
        total_deli_price  = parseInt( $('#total_deli_price').val() ); // 선불 배송료
        total_deli_price2 = parseInt( $('#total_deli_price2').val() ); //착불배송료
        _sum_price        = parseInt( $('#total_sum').val() );  // 총 상품 가격
        _before_deliprice = parseInt( $('#total_deli_price').val() ); // 선불 배송료
        _after_deliprice  = parseInt( $('#total_deli_price2').val() ); // 착불 배송료
		_prCoupon_area    = $('input[name="obj_basketidx"]');
        total_sum         = _sum_price;
        // 상품쿠폰 obj를 초기화
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
			$('.layer_use_coupon .btn_close').trigger('click');
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
					$('.layer_use_coupon').show();
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
					$('.layer_use_coupon').show();
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

	function coupon_use_type( type ){ // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
		// 쿠폰 마일리지 동시사용 불가
		if( all_type != 'Y' ){
			if( ( type == 1 || type == 2 ) &&  (_total_mileage > 0 || _total_point > 0) ){
				if( confirm( "쿠폰과 포인트 및 E포인트는 동시사용이 불가능합니다.\n마일리지와 포인트를 다시 선택하시겠습니까?" ) ){
					mileage_cancel();
					point_cancel();
					coupon_update();
				} else {
					reset_prdc();
					coupon_update();
				}
			} else if( type == 3 && ( prc_leng > 0 || bdc_leng > 0 ) ) {
				if( confirm( "쿠폰과 포인트 및 E포인트는 동시사용이 불가능합니다.\n쿠폰을 다시 선택하시겠습니까?" ) ){
					reset_prdc();
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
					coupon_update();
				}
			}
		}
	}

    // 결제로 넘어갈 쿠폰값을 레이어에 넘겨준다
    function set_coupon_layer(){
        var prd_layer        = $('#ID_prd_coupon_layer'); // 상품쿠폰이 담길 레이어 위치
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
        $( prd_layer ).html( pr_coupon_html );
    }

</script>
<!-- //쿠폰 -->
	
	<section class="orderpage">

		<div class="list_cart">
			<!-- 브랜드별 반복 -->
<!-- 브랜드별 -->
<?php
$sumprice = 0;
$deli_price = 0; // 선불 배송료
$deli_price2 = 0; //착불 배송료
$sum_product_reserve	= 0; // 총 예상 적립금
$checkTodayDelivery = false; // 당일 배송이 있는지 여부
$checkMarketDelivery = false; // 매장픽업이 있는지 여부
$arrDeliveryTodayAddress = array(); // 당일 배송이 있으면 해당 주소 저장 배열
$vender	=0; //밴더 기본배송비로 고정2017-02-27
$o2o_check=0;
foreach( $brandArr as $brand=>$brandObj ){
	foreach($brandObj as $bo=>$bos){
		if($bos[delivery_type]=='0') $o2o_check++;
	}
	$brand_name = get_brand_name( $brand );
	
	//$vender	=$brandVenderArr[$brand];
	$vender_price = 0;
	$product_reserve = 0;
	$product_price = 0;
	if($o2o_check){
?>
			<div class="list_brand">
				<h3 class="cart_tit"><?=$brand_name?> 주문상품</h3>
				<ul class="cart_goods">
					<!-- 상품 반복 -->
<?php
	$product_count="0";
	foreach( $brandObj as $product ) {
		if($product['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송일 경우 재고 가장 많은 매장으로 매장정보 표시
			$shop_code_set = getErpProdShopStock_Type($product['prodcode'], $product['colorcode'], $product['option'][0]['option_code'], 'delivery');
			$product['store_code'] = $shop_code_set['shopcd'];
		}else{
			continue;
		}
		$brand_product_name = get_brand_name( $product[brand] );
//		$storeData = getStoreData($product['store_code']);
		$opt_price = 0; // 상품별 옶션가
		$pr_reserve = 0; //상품별 마일리지
        $tmp_opt_price = 0;
		if($product['delivery_type'] == '3'){
			$checkTodayDelivery = true;
			$arrDeliveryTodayAddress = array('post'=>$product['post_code'], 'address1'=>$product['address1'], 'address2'=>$product['address2']);
		}
		if($product['delivery_type'] == '1'){
			$checkMarketDelivery = true;
		}

		$vender_deli_price = 0;

		if( $product_deli[$vender][$product['productcode']] ){
			$vender_deli_price += $product_deli[$vender][$product['productcode']]['deli_price'];
			/*
			foreach( $product_deli[$vender][$product['productcode']] as $prDeliKey => $prDeliVal ){
				exdebug($prDeliKey);
				$vender_deli_price += $prDeliVal['deli_price'];
			}*/
		}
		
		$vender_deli_price += $vender_deli[$vender]['deli_price'];
?>
					<input type="hidden" name="obj_basketidx" value="<?=$product[basketidx]?>">
					<input type="hidden" name="obj_ci_no[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_coupon_code[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_dc[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_product_price[<?=$product[basketidx]?>]" value="<?=$product['price']?>">

					<li>
						<div class="cart_wrap">
							<div class="clear">
								<div class="goods_area">
									<div class="img"><a href="<?=$Dir?>m/productdetail.php?productcode=<?=$product['productcode']?>"><img src="<?=getProductImage( $productImgPath, $product['tinyimage'] )?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$brand_product_name?></p>
										<p class="name"><?=$product['productname']?></p><!-- [D] 상품명은 최대 2줄까지 노출 -->
										<?if($product[prodcode]){?>
										<p class="option">품번: <?=$product[prodcode]?></p>
										<?}?>
							<?php
									if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
							?>
										<p class="option">
											<?php
											if($product[colorcode]){
												
												echo "색상 : ".$product[colorcode];
											}
											if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
												if( count( $product['option'] ) > 0 ){
													$tmp_opt_subject = explode( '@#', $product['option_subject'] );
													if( $product['option_type'] == 0 ){ // 조합형 옵션
														$tmp_option = $product['option'][0];
														$tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
														if($tmp_opt_subject){
															echo " / ";
															foreach( $tmp_opt_subject as $optKey=>$optVal ){
																echo $optVal.' : '.$tmp_opt_contetnt[$optKey];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
														$opt_price += $tmp_option['option_price'] * $product['option_quantity'];
													}
													if( $product['option_type'] == 1 ){ // 독립형 옵션
														
														if($product['option']){
															echo " / ";
															foreach( $product['option'] as $optKey=>$optVal ){
																$tmp_opt_content = explode( chr(30), $optVal['option_code'] );
																echo $tmp_opt_subject[$optKey].' : '.$tmp_opt_content[1];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
																$opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
													}
												} // count option

												if( $product['text_opt_content'] ){ // 추가문구 옵션
													$tmp_text_subejct = explode( '@#', $product['text_opt_subject'] );
													$text_opt_content = explode( '@#', $product['text_opt_content'] );
													if($text_opt_content){
														echo " / ";
														foreach( $text_opt_content as $textKey=>$textVal ){
															if( $textVal != '' ) {
																echo $tmp_text_subejct[$textKey].' : '.$textVal;
															}
														}
													}
												}  // text_opt_content if

												if( $tmp_opt_price > 0 ){
													echo '(추가금액 : '.number_format( $tmp_opt_price ).')';
												}
											} else {
												echo "-";
											}// count option || text_opt_subject if

											if($product['option_quantity']){
												echo " / ".$product['option_quantity']."개";
											}
										?>
										</p>
									<?
									}
									//$pr_reserve = getReserveConversion( $product['reserve'], $product['reservetype'], ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
									$pr_reserve = getReserveConversion( $product['reserve'], "Y", ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
									if( strlen( $_ShopInfo->getMemid() ) == 0 ) $pr_reserve	= 0;
									$product_reserve += $pr_reserve; // 벤더별 상품 예상 적립금

									if($cooper_order == 'Y'){
										// order.class.php 옴겨야함 20170828 제휴사 수정
										list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
										$c_productcode = $product['productcode'];

										list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
										
										$t_product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
										$c_product_price = ( $company_price  * $product['quantity'] ) ;

										if($c_product_price >= $t_product_price || $c_product_price == 0){
											$product_price = $t_product_price;
										}else{
											$product_price = $c_product_price;
										}
									}else{
										$product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
									}

									$vender_price += $product_price; // 벤더별 상품가격

									$sum_product_reserve += $pr_reserve; // 총 예상 적립금
									?>
										<p class="price">￦ <?=number_format($product_price)?> <?if($pr_reserve){?><span class="point-color">(적립 <?=number_format($pr_reserve)?>P)</span><?}?></p>
									</div>
								</div>
							</div>
							<?php
//								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N') {
									if($cooper_order == 'Y'  ) {
							?>
							<div class="coupon_area">
								<table>
									<colgroup>
										<col style="width:75px;">
										<col style="width:auto;">
									</colgroup>
									<tbody>
										<tr>
											<th><a href="javascript:;" class="btn_use_coupon btn-basic"  onclick="javascript:product_ccoupon_pop(<?=$product[basketidx]?>,<?=$product_price?>)">쿠폰사용</a></th>
											<td><span class="coupon_name"></span></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?php
									}else{
							?>
							<div class="coupon_area">
								<table>
									<colgroup>
										<col style="width:75px;">
										<col style="width:auto;">
									</colgroup>
									<tbody>
										<tr>
											<th><a href="javascript:;" class="btn_use_coupon btn-basic"  onclick="javascript:product_coupon_pop(<?=$product[basketidx]?>)">쿠폰사용</a></th>
											<td><span class="coupon_name"></span></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?	}}?>
						</div><!-- //.cart_wrap -->
					</li>
					<!-- //상품 반복 -->
<?php
		# 장바구니 쿠폰 제외
		foreach( $basket_coupon as $basketKey=>$basketVal ){
			if( !$_CouponInfo->check_coupon_product( $product['productcode'], 2, $basketVal ) ){
				unset( $basket_coupon[$basketKey] );
			}
		}
		$product_count++;
	} //foreach
?>
					
				</ul><!-- //.cart_goods -->
<?php
	
	if( $vender_info[$vender] ){
		
?>
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span>￦ <?=number_format( $vender_price )?></span>
						</li>
<!--
						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>
-->
						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
							<label>쿠폰할인</label>
							<span class="point-color">- ￦ <em class="CLS_prCoupon" style="vertical-align: baseline;">0</em></span>
						</li>
						<div id = "ID_coupon_code_layer">
							<div id = "ID_prd_coupon_layer" ></div>
							<!--
							<div id = "ID_bk_coupon_layer" ></div>
							<div id = "ID_deli_coupon_layer" ></div>-->
						</div>
						<tr>
						<li>
							<label>배송비</label>
							<span>￦ <?=number_format( $vender_deli_price )?></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span>￦ <em class="CLS_Tprice" style="vertical-align: baseline;"><?=number_format( $vender_price + $vender_deli_price )?></em></span>
						</li>
					</ul>
				</div>
<?php
	}
?>
			</div>
			<!-- //브랜드별 반복 -->
<?php
	if( $vender_info[$vender]['deli_select'] == '0' || $vender_info[$vender]['deli_select'] == '2' ) $deli_price += $vender_deli_price;
    if( $vender_info[$vender]['deli_select'] == '1' ) $deli_price2 += $vender_deli_price;
	$sumprice += $vender_price;
	}
} // foreach


$o2o_check=0;
foreach( $brandArr as $brand=>$brandObj ){
	foreach($brandObj as $bo=>$bos){
		if($bos[delivery_type]!='0') $o2o_check++;
	}
	$brand_name = get_brand_name( $brand );
	//$vender	=$brandVenderArr[$brand];
	$vender_price = 0;
	$product_reserve = 0;
	$product_price = 0;

	if($o2o_check){
?>

			<!-- O2O 상품 -->
			<div class="list_brand  with_deli_info">
				<h3 class="cart_tit">O2O 상품</h3>
				<ul class="cart_goods">
<?php
	$product_count="0";
	foreach( $brandObj as $product ) {
		if($product['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송일 경우 재고 가장 많은 매장으로 매장정보 표시
			continue;
		}
		$brand_product_name = get_brand_name( $product[brand] );
		$storeData = getStoreData($product['store_code']);
		$opt_price = 0; // 상품별 옶션가
		$pr_reserve = 0; //상품별 마일리지
        $tmp_opt_price = 0;
		if($product['delivery_type'] == '3'){
			$checkTodayDelivery = true;
			$arrDeliveryTodayAddress = array('post'=>$product['post_code'], 'address1'=>$product['address1'], 'address2'=>$product['address2']);
		}
		if($product['delivery_type'] == '1'){
			$checkMarketDelivery = true;
		}

		$vender_deli_price = 0;

		if( $product_deli[$vender][$product['productcode']] ){
			$vender_deli_price += $product_deli[$vender][$product['productcode']]['deli_price'];
			
		}
	
?>
					<input type="hidden" name="obj_basketidx" value="<?=$product[basketidx]?>">
					<input type="hidden" name="obj_ci_no[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_coupon_code[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_dc[<?=$product[basketidx]?>]" value="">
					<input type="hidden" name="obj_product_price[<?=$product[basketidx]?>]" value="<?=$product['price']?>">

					<!-- 상품 반복 -->
					<li>
						<div class="cart_wrap">
							<div class="clear">
								<div class="goods_area">
									<div class="img"><a href="<?=$Dir?>m/productdetail.php?productcode=<?=$product['productcode']?>"><img src="<?=getProductImage( $productImgPath, $product['tinyimage'] )?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$brand_product_name?></p>
										<p class="name"><?=$product['productname']?></p>
										<?if($product[prodcode]){?>
										<p class="option">품번: <?=$product[prodcode]?></p>
										<?}?>
										<p class="option">
											<?php
									if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
							?>
										<p class="option">
											<?php
											if($product[colorcode]){
												
												echo "색상 : ".$product[colorcode];
											}
											if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
												if( count( $product['option'] ) > 0 ){
													$tmp_opt_subject = explode( '@#', $product['option_subject'] );
													if( $product['option_type'] == 0 ){ // 조합형 옵션
														$tmp_option = $product['option'][0];
														$tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
														if($tmp_opt_subject){
															echo " / ";
															foreach( $tmp_opt_subject as $optKey=>$optVal ){
																echo $optVal.' : '.$tmp_opt_contetnt[$optKey];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
														$opt_price += $tmp_option['option_price'] * $product['option_quantity'];
													}
													if( $product['option_type'] == 1 ){ // 독립형 옵션
														
														if($product['option']){
															echo " / ";
															foreach( $product['option'] as $optKey=>$optVal ){
																$tmp_opt_content = explode( chr(30), $optVal['option_code'] );
																echo $tmp_opt_subject[$optKey].' : '.$tmp_opt_content[1];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
																$opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
													}
												} // count option

												if( $product['text_opt_content'] ){ // 추가문구 옵션
													$tmp_text_subejct = explode( '@#', $product['text_opt_subject'] );
													$text_opt_content = explode( '@#', $product['text_opt_content'] );
													if($text_opt_content){
														echo " / ";
														foreach( $text_opt_content as $textKey=>$textVal ){
															if( $textVal != '' ) {
																echo $tmp_text_subejct[$textKey].' : '.$textVal;
															}
														}
													}
												}  // text_opt_content if

												if( $tmp_opt_price > 0 ){
													echo '(추가금액 : '.number_format( $tmp_opt_price ).')';
												}
											} else {
												echo "-";
											}// count option || text_opt_subject if

											if($product['option_quantity']){
												echo " / ".$product['option_quantity']."개";
											}
										?>
										</p>
									<?
									}
									//$pr_reserve = getReserveConversion( $product['reserve'], $product['reservetype'], ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
									$pr_reserve = getReserveConversion( $product['reserve'], "Y", ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
									if( strlen( $_ShopInfo->getMemid() ) == 0 ) $pr_reserve	= 0;
									$product_reserve += $pr_reserve; // 벤더별 상품 예상 적립금

									if($cooper_order == 'Y'){
										// order.class.php 옴겨야함 20170828 제휴사 수정
										list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
										$c_productcode = $product['productcode'];

										list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
										
										$t_product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
										$c_product_price = ( $company_price  * $product['quantity'] ) ;

										if($c_product_price >= $t_product_price || $c_product_price == 0){
											$product_price = $t_product_price;
										}else{
											$product_price = $c_product_price;
										}
									}else{
										$product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
									}

									$vender_price += $product_price; // 벤더별 상품가격

									$sum_product_reserve += $pr_reserve; // 총 예상 적립금
									?>
										<p class="price">￦ <?=number_format($product_price)?> <?if($pr_reserve){?><span class="point-color">(적립 <?=number_format($pr_reserve)?>P)</span><?}?></p>
									</div>
								</div>
							</div>
							<?php
								//if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N') {
							?>
							<div class="coupon_area">
								<table>
									<colgroup>
										<col style="width:75px;">
										<col style="width:auto;">
									</colgroup>
									<tbody>
										<tr>
											<th><a href="javascript:;" class="btn_use_coupon btn-basic"  onclick="javascript:product_ccoupon_pop(<?=$product[basketidx]?>,<?=$product_price?>)">쿠폰사용</a></th>
											<td><span class="coupon_name"></span></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?php
									}else{
							?>
							<div class="coupon_area">
								<table>
									<colgroup>
										<col style="width:75px;">
										<col style="width:auto;">
									</colgroup>
									<tbody>
										<tr>
											<th><a href="javascript:;" class="btn_use_coupon btn-basic"  onclick="javascript:product_coupon_pop(<?=$product[basketidx]?>)">쿠폰사용</a></th>
											<td><span class="coupon_name"></span></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?	}?>
						</div><!-- //.cart_wrap -->
						<div class="delibox">
							<h4 class="cart_tit">
								<?if($product['delivery_type'] == '1'){?>매장픽업<?}else if($product['delivery_type'] == '3'){?>당일수령<?}?>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($product['delivery_type'] == '1'){?>
											<div class="container">
												<p><?=$product['reservation_date']?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </p>
											</div>
											<?}else if($product['delivery_type'] == '3'){?>
											<div class="container">
												<p>선택하신 상품은 당일수령이 가능한 상품입니다.</p>
											</div>
											<?}?>
										</div>
									</div>
								</div><!-- //.wrap_bubble -->
							</h4>
							<div class="change_store">
								<?if($product['delivery_type'] == '1'){?>
								<span class="store_name">예약일 : <?=$product['reservation_date']?> / <?=$storeData['name']?></span>
								<?}else if($product['delivery_type'] == '3'){?>
								<span class="store_name">￦ <?=number_format($product_deli[$vender][$product['productcode']][deli_price])?> / <?=$storeData['name']?></span>
								<?}?>

								<a href="javascript:;" class="btn_store_info btn-basic" onclick="javascript:store_map('<?=$product['store_code']?>')">매장안내</a>
							</div>
						</div><!-- //.delibox -->
					</li>
					<!-- //상품 반복 -->
<?php
		# 장바구니 쿠폰 제외
		foreach( $basket_coupon as $basketKey=>$basketVal ){
			if( !$_CouponInfo->check_coupon_product( $product['productcode'], 2, $basketVal ) ){
				unset( $basket_coupon[$basketKey] );
			}
		}
		$product_count++;
	} //foreach
?>
				</ul><!-- //.cart_goods -->
<?php
	
	if( $vender_info[$vender] ){
		
?>
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span>￦ <?=number_format( $vender_price )?></span>
						</li>
<!--
						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>
-->
						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
							<label>쿠폰할인</label>
							<span class="point-color">- ￦ <em class="CLS_prCoupon" style="vertical-align: baseline;">0</em></span>
						</li>
						<div id = "ID_coupon_code_layer">
							<div id = "ID_prd_coupon_layer" ></div>
							<!--
							<div id = "ID_bk_coupon_layer" ></div>
							<div id = "ID_deli_coupon_layer" ></div>-->
						</div>
						<tr>
						<li>
							<label>배송비</label>
							<span>￦ <?=number_format( $vender_deli_price )?></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span>￦ <em class="CLS_Tprice" style="vertical-align: baseline;"><?=number_format( $vender_price + $vender_deli_price )?></em></span>
						</li>
					</ul>
				</div>
<!--				
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span>￦ <?=number_format( $vender_price )?></span>
						</li>
<!--						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>--><!--
						<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N') {}else {?> class='hide'<?}?>>
							<label>쿠폰할인</label>
							<span class="point-color">- ￦ <em class="CLS_prCoupon" style="vertical-align: baseline;">0</em></span>
						</li>
						<li>
							<label>배송비</label>
							<span>￦ <?=number_format( $vender_deli_price )?></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span>￦ <em class="CLS_Tprice" style="vertical-align: baseline;"><?=number_format( $vender_price + $vender_deli_price )?></em></span>
						</li>
					</ul>
				</div>
			</div>-->
<?php
	}
?>
			<!-- //O2O 상품 -->
		</div><!-- //.list_cart -->
<?php
	
	if( $vender_info[$vender]['deli_select'] == '0' || $vender_info[$vender]['deli_select'] == '2' ) $deli_price += $vender_deli_price;
    if( $vender_info[$vender]['deli_select'] == '1' ) $deli_price2 += $vender_deli_price;
	$sumprice += $vender_price;
	}
} // foreach
?>

		<!-- 할인 및 결제정보 -->
		<div class="order_table">
			<h3 class="cart_tit">할인 및 결제정보</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>총 상품금액</th>
						<td>￦ <?=number_format($sumprice)?></td>
					</tr>
<?php

	if ( strlen( $_ShopInfo->getMemid() ) > 0 && $user_reserve != 0 ){

?>
<?php
	if($okreserve<0){
		$okreserve=(int)($sumprice*abs($okreserve)/100);
		if($reserve_maxprice>$sumprice) {
			$okreserve=$user_reserve;
			$remainreserve=0;
		} else if($okreserve>$user_reserve) {
			$okreserve=$user_reserve;
			$remainreserve=0;
		} else {
			$remainreserve=$user_reserve-$okreserve;
		}
	}
?>
<!--					<tr <?if($staff_order == 'Y' || $cooper_order == 'Y' ) {?> class='hide'<?}?>>-->
						<tr <?if($staff_order == 'Y') {?> class='hide'<?}?>>
						<th>포인트 사용</th>
						<td class="use_point">
							<input type="hidden" name="okreserve" id='okreserve' value="<?=$user_reserve?>">
<?php
		if( $_data->reserve_maxprice > $sumprice ) {
?>
							<input type="hidden" name="usereserve" id="mileage-use" value='0'><span class="disabled"> 상품금액 <?=number_format($_data->reserve_maxprice)?>원 이상 사용가능</span>
							
<?php
		}else if( $_data->reserve_maxuse > $user_reserve ) {
?>
							<input type="hidden" name="usereserve" id="mileage-use" value='0'><span class="disabled"> 누적포인트가 <?=number_format($_data->reserve_maxuse)?>포인트 이상 사용가능</span>
<?php
		}else if( $user_reserve >= $_data->reserve_maxuse ){
?>
							<span><input type="text" class="w70" name="usereserve" id="mileage-use" value="0"> P</span>
							<label class="ml-20"><input type="checkbox" class="check_def" id="all-mileage-use"> <span>모두사용</span></label>
							<p class="mt-5">(사용가능 포인트 <?=number_format( $user_reserve )?> P)</p>
<?php
		}else{
?>
							<input type="hidden" name="usereserve" id="mileage-use" value='0'><span class="disabled"> 사용불가</span>
<?php
		}
?>
						</td>
					</tr>
<?php
	} else {
?>
							<input type="hidden" name="usereserve" id="mileage-use" value='0'>
							<input type="hidden" name="okreserve" id='okreserve' value="0">
<?php
	}
?>

<?php
	if ( strlen( $_ShopInfo->getMemid() ) > 0 && $user_point != 0 ){
?>
<?php
	if($okpoint<0){
		$okpoint=(int)($sumprice*abs($okpoint)/100);
		if($e_reserve_maxprice>$sumprice) {
			$okpoint=$user_point;
			$remainpoint=0;
		} else if($okpoint>$user_point) {
			$okpoint=$user_point;
			$remainpoint=0;
		} else {
			$remainpoint=$user_point-$okpoint;
		}
	}
?>
<!--					<tr <?if($staff_order == 'Y' || $cooper_order == 'Y' ) {?> class='hide'<?}?>>-->
					<tr <?if($staff_order == 'Y' ) {?> class='hide'<?}?>>
						<th>E포인트 사용</th>
						<td class="use_point">
						<input type="hidden" name="okpoint" id='okpoint' value="<?=$user_point?>">
<?php
		if( $_data->e_reserve_maxprice > $sumprice ) {
?>
						<input type="hidden" name="usepoint" id="point-use" value='0'><span class="disabled"> 상품금액 <?=number_format($_data->e_reserve_maxprice)?>원 이상 사용가능</span>
<?php
		}else if( $_data->e_reserve_maxuse > $user_point ) {
?>
						<input type="hidden" name="usepoint" id="point-use" value='0'><span class="disabled"> 누적포인트가 <?=number_format($_data->e_reserve_maxuse)?>포인트 이상 사용가능</span>
<?php
		}else if( $user_point >= $_data->e_reserve_maxuse ){
?>
						<span><input type="text" class="w70" name="usepoint" id="point-use" value="0"> P</span>
						<label class="ml-20"><input type="checkbox" class="check_def" id="check-epoint-all"> <span>모두사용</span></label>
						<p class="mt-5">(사용가능 포인트 <?=number_format( $user_point )?> P)</p>
<?php
		}else{
?>
						<input type="hidden" name="usepoint" id="point-use" value='0'><span class="disabled"> 사용불가</span>
<?php
		}
?>
						</td>
					</tr>
<?php
	} else {
?>
							<input type="hidden" name="usepoint" id="point-use" value='0'>
							<input type="hidden" name="okpoint" id='okpoint' value="0">
<?php
	}
?>
<!--					<tr <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>-->
					<tr <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
						<th>쿠폰할인</th>
						<td><span class="point-color">- ￦ <em class="CLS_prCoupon">0</em></span></td>
					</tr>
					<tr>
						<th>배송비</th>
						<td>￦ <em id='delivery_price'><?=number_format($deli_price)?></em></td>
					</tr>
					<tr>
						<th>도서산간 배송비</th>
						<td>￦ <em class='area_delivery_price'>0</em></td>
					</tr>
					<tr>
						<th>실 결제금액</th>
						<td><strong class="point-color">￦ <em class="price_sum" id="price_sum"><?=number_format($sumprice+$deli_price)?></em></strong></td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //할인 및 결제정보 -->

		<!-- 주문고객 정보 -->
		<div class="order_table">
			<h3 class="cart_tit">주문고객 정보</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th><span class="required">주문자</span></th>
						<td>
									<?php
if(strlen( $_ShopInfo->getMemid() ) > 0 ) {
?>
						<input type="text" name="sender_name" id="order_name" value="<?=$userName?>" required class="w100-per" msgR="주문하시는분의 이름을 적어주세요">
<?php
} else {
?>
						<input type='text'   name="sender_name" id="order_name" value="" required msgR="주문하시는분의 이름을 적어주세요" >
<?php
} // else
?>
						</td>
					</tr>
					<tr>
						<th>이메일</th>
						<td>
							<div class="input_mail">
								<input type="text" id="user-email" name='sender_email1' value='<?=$email[0]?>'><span class="at">&#64;</span>
								<select class="select_line" name="email_select" name="email_select" onchange="javascript:email_change()">
									<option value="">직접입력</option>
									<option value="naver.com" <?=$email[1]=='naver.com'?' selected':''?>>naver.com</option>
									<option value="gmail.com" <?=$email[1]=='gmail.com'?' selected':''?>>gmail.com</option>
									<option value="daum.net" <?=$email[1]=='daum.net'?' selected':''?>>daum.net</option>
									<option value="nate.com" <?=$email[1]=='nate.com'?' selected':''?>>nate.com</option>
									<option value="hanmail.net" <?=$email[1]=='hanmail.net'?' selected':''?>>hanmail.net</option>
									<option value="yahoo.com" <?=$email[1]=='yahoo.com'?' selected':''?>>yahoo.com</option>
									<option value="dreamwiz.com" <?=$email[1]=='dreamwiz.com'?' selected':''?>>dreamwiz.com</option>
								</select>
							</div>
							<input type="text" class="w100-per mt-5" placeholder="직접입력" name="sender_email2" id="sender_email2" value="<?=$email[1]?>" style="display:none;">
						</td>
					</tr>
					<tr>
						<th><span class="required">휴대전화</span></th>
						<td>
							<div class="input_tel">
								<select class="select_line" name="sender_tel1" id="sender_tel1">
									<option value="010"<?=$mobile[0]=='010'?' selected':''?>>010</option>
									<option value="011"<?=$mobile[0]=='011'?' selected':''?>>011</option>
									<option value="016"<?=$mobile[0]=='016'?' selected':''?>>016</option>
									<option value="017"<?=$mobile[0]=='017'?' selected':''?>>017</option>
									<option value="018"<?=$mobile[0]=='018'?' selected':''?>>018</option>
									<option value="019"<?=$mobile[0]=='019'?' selected':''?>>019</option>
								</select>
								<span class="dash"></span>
								<input type="tel" id="user-phone" name="sender_tel2" value="<?=$mobile[1] ?>" maxlength='4' title="휴대전화번호 가운데 입력자리">
								<span class="dash"></span>
								<input type="tel"  name="sender_tel3" value="<?=$mobile[2] ?>" maxlength='4' title="휴대전화번호 마지막 입력자리">
							</div>
						</td>
					</tr>
					<tr>
						<th>전화번호(선택)</th>
						<td>
							<div class="input_tel">
								<select class="select_line" id="home_tel1" name="home_tel1">
									<option value="02" selected>02</option>
									<option value="031">031</option>
									<option value="032">032</option>
									<option value="033">033</option>
									<option value="041">041</option>
									<option value="042">042</option>
									<option value="043">043</option>
									<option value="044">044</option>
									<option value="051">051</option>
									<option value="052">052</option>
									<option value="053">053</option>
									<option value="054">054</option>
									<option value="055">055</option>
									<option value="061">061</option>
									<option value="062">062</option>
									<option value="063">063</option>
									<option value="064">064</option>
								</select>
								<span class="dash"></span>
								<input type="tel" id="home_tel2" name="home_tel2" maxlength='4' title="전화번호 가운데 입력자리">
								<span class="dash"></span>
								<input type="tel" name="home_tel3" id='home_tel3' maxlength='4' title="전화번호 마지막 입력자리">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //주문고객 정보 -->

		<!-- 배송지 정보 -->
		<div class="order_table">
			<h3 class="cart_tit">배송지 정보</h3>
			<div class="table_top clear">
				<?if($checkTodayDelivery == false){?>
				<label><input type="checkbox" class="check_def checkbox_custom" name="same" id="dev_orderer" value="Y" onclick="SameCheck(this.checked)"> <span>주문고객과 동일한 주소 사용</span></label>
				<?if( $_ShopInfo->getMemid()){?>
				<div class="btn_area"><a href="javascript:;" class="btn_deli_site btn-basic" id="btn_shipping_list">배송지목록</a></div>
				<?}?>
				<?}?>
			</div>
			<table class="th-left mt-5">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th><span class="required">받는사람</span></th>
						<td>
							<?if($checkTodayDelivery == false && $checkMarketDelivery==true && $_ShopInfo->getMemid()){?>
							<input type="text" class="w100-per" placeholder="이름 입력" id="receiver_name" value="<?=$userName?>" name = 'receiver_name' required msgR="받으시는분 이름을 입력하세요.">
							<?}else{?>
							<input type="text" class="w100-per" placeholder="이름 입력" id="receiver_name" name = 'receiver_name' required msgR="받으시는분 이름을 입력하세요.">
							<?}?>
							<?if($_ShopInfo->getMemid()){?>
							<div class="mt-5"><label><input type="checkbox" class="check_def" name="destinationt_type" value="Y" id="delivery_default_save"> <span>기본 배송지로 저장</span></label></div>
							<?}?>
						</td>
					</tr>
					<tr>
						<th><span class="required">휴대전화</span></th>
						<td>
							<?if($checkTodayDelivery == false && $checkMarketDelivery==true && $_ShopInfo->getMemid()){?>
							<div class="input_tel">
								<select class="select_line" id="receiver_tel21" name="receiver_tel21">
									<option value="010"<?=$mobile[0]=='010'?' selected':''?>>010</option>
									<option value="011"<?=$mobile[0]=='011'?' selected':''?>>011</option>
									<option value="016"<?=$mobile[0]=='016'?' selected':''?>>016</option>
									<option value="017"<?=$mobile[0]=='017'?' selected':''?>>017</option>
									<option value="018"<?=$mobile[0]=='018'?' selected':''?>>018</option>
									<option value="019"<?=$mobile[0]=='019'?' selected':''?>>019</option>
								</select>
								<span class="dash"></span>
								<input type="tel" id="receiver_tel22" name="receiver_tel22" maxlength='4' value="<?=$mobile[1] ?>" onKeyUp="strnumkeyup(this)" required title="휴대전화번호 가운데 입력자리">
								<span class="dash"></span>
								<input type="tel" id="receiver_tel23" name="receiver_tel23" maxlength='4' value="<?=$mobile[2] ?>" onKeyUp="strnumkeyup(this)" required title="휴대전화번호 마지막 입력자리">
							</div>
							<?}else{?>
							<div class="input_tel">
								<select class="select_line" id="receiver_tel21" name="receiver_tel21">
									<option value="010" selected>010</option>
									<option value="011">011</option>
									<option value="016">016</option>
									<option value="017">017</option>
									<option value="018">018</option>
									<option value="019">019</option>
								</select>
								<span class="dash"></span>
								<input type="tel" id="receiver_tel22" name="receiver_tel22" maxlength='4' onKeyUp="strnumkeyup(this)" required title="휴대전화번호 가운데 입력자리">
								<span class="dash"></span>
								<input type="tel" id="receiver_tel23" name="receiver_tel23" maxlength='4' onKeyUp="strnumkeyup(this)" required title="휴대전화번호 마지막 입력자리">
							</div>
							<?}?>
						</td>
					</tr>
					<tr>
						<th>전화번호(선택)</th>
						<td>
							<div class="input_tel">
								<select class="select_line" id="receiver_tel11" name="receiver_tel11">
									<option value="02" selected>02</option>
									<option value="031">031</option>
									<option value="032">032</option>
									<option value="033">033</option>
									<option value="041">041</option>
									<option value="042">042</option>
									<option value="043">043</option>
									<option value="044">044</option>
									<option value="051">051</option>
									<option value="052">052</option>
									<option value="053">053</option>
									<option value="054">054</option>
									<option value="055">055</option>
									<option value="061">061</option>
									<option value="062">062</option>
									<option value="063">063</option>
									<option value="064">064</option>
								</select>
								<span class="dash"></span>
								<input type="tel" id="receiver_tel12" name="receiver_tel12" maxlength='4' onKeyUp="strnumkeyup(this)" title="전화번호 가운데 입력자리">
								<span class="dash"></span>
								<input type="tel" id="receiver_tel13" name="receiver_tel13" maxlength='4' onKeyUp="strnumkeyup(this)" title="전화번호 마지막 입력자리">
							</div>
						</td>
					</tr>
					<tr>
						<th><span class="required">주소</span></th>
						<td>
							<?if($checkTodayDelivery == false){?>
							<input type='hidden' id='post5' name='post5' value='' >
							<input type="hidden" id="rpost1" name = 'rpost1'>
							<input type="hidden" id='rpost2' name = 'rpost2'>
							<div class="input_addr">
								<input type="text" placeholder="우편번호" name = 'post' id = 'post' title="우편번호 입력자리" readonly>
								<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="javascript:openDaumPostcode();">주소찾기</a></div>
								<div id="addressWrap" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
							</div>
							<input type="text" class="w100-per mt-5" placeholder="기본주소" name = 'raddr1' id = 'raddr1' title="검색된 주소" class="w100-per" readonly>
							<input type="text" class="w100-per mt-5" placeholder="상세주소" name = 'raddr2' id = 'raddr2' title="상세주소 입력" class="w100-per">
							<?}else{?>
							<input type='hidden' id='post5' name='post5' value = '<?=$arrDeliveryTodayAddress['post']?>' readonly >
							<input type="hidden" id="rpost1" name = 'rpost1'>
							<input type="hidden" id='rpost2' name = 'rpost2'>
							<div class="input_addr">
								<input type="text" placeholder="우편번호" name = 'post' id = 'post' value = '<?=$arrDeliveryTodayAddress['post']?>' title="우편번호 입력자리" readonly>
								<div class="btn_addr"><a href="javascript:;" class="btn-basic h-input" onclick="javascript:openDaumPostcode();">주소찾기</a></div>
								<div id="addressWrap" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
							</div>
							<input type="text" class="w100-per mt-5" placeholder="기본주소" name = 'raddr1' id = 'raddr1' value = '<?=$arrDeliveryTodayAddress['address1']?>' title="검색된 주소" class="w100-per" readonly>
							<input type="text" class="w100-per mt-5" placeholder="상세주소" name = 'raddr2' id = 'raddr2' value = '<?=$arrDeliveryTodayAddress['address2']?>' title="상세주소 입력" class="w100-per" readonly>
							<?}?>
						</td>
					</tr>
					<tr>
						<th>배송 요청사항</th>
						<td>
							<input type="hidden" name="msg_type" value="1">
							<select class="select_line w100-per" id="prmsg_chg" name='prmsg_chg'>
								<option value="" selected>직접입력</option>
								<option value="부재시 경비실에 맡겨 주세요">부재시 경비실에 맡겨 주세요</option>
								<option value="부재시 문앞에 놓아주세요">부재시 문앞에 놓아주세요</option>
								<option value="배송전에 연락주세요">배송전에 연락주세요</option>
								<option value="빠른배송 부탁드려요">빠른배송 부탁드려요</option>
								<option value="소화전에 넣어주세요">소화전에 넣어주세요</option>
								<option value="배관함에 넣어주세요">배관함에 넣어주세요</option>
							</select>
							<input type="text" class="w100-per mt-5" title="배송 요청사항 입력" name = 'order_prmsg' id="order_prmsg">
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //배송지 정보 -->

		<!-- 결제방식 선택 -->
		<div class="order_table">
			<h3 class="cart_tit">결제방식 선택</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>신용카드</th>
						<td>
							<?if(strstr("YC", $_data->payment_type) && ord($_data->card_id)) {?>
							<label><input type="radio" id="dev_payment2" name="dev_payment" value="C" class="radio_def dev_payment" onclick="sel_paymethod(this);" checked> <span>신용카드(일반)</span></label>
							<?}?>
						</td>
					</tr>
					<tr>
						<th>현금결제</th>
						<td>
							<?if($escrow_info["onlycard"]!="Y" && ord($_data->trans_id)){?>
							<label><input type="radio" id="dev_payment3" name="dev_payment" value="V" class="radio_def dev_payment" onclick="sel_paymethod(this);"> <span>실시간 계좌이체</span></label>
							<?}?>
							<?if($escrow_info["onlycard"]!="Y" && ord($_data->virtual_id)){?>
							<label class="ml-10"><input type="radio" id="dev_payment4" name="dev_payment" value="O" class="radio_def dev_payment" onclick="sel_paymethod(this);"> <span>가상계좌</span></label>
							<?}?>
							
							<?if(( $escrow_info["escrowcash"]=="A" || ($escrow_info["escrowcash"]=="Y" && (int)($sumprice+$deli_price)>=$escrow_info["escrow_limit"])) ){?>
							<?
								$pgid_info="";
								$pg_type="";
								$pgid_info=GetEscrowType($_data->escrow_id);
								$pg_type=trim($pgid_info["PG"]);
							?>
								<?if(strstr("ABCDG",$pg_type)){?>
							<br><label><input type="radio" id="dev_payment5" name="dev_payment" value="Q" class="radio_def dev_payment" onclick="sel_paymethod(this);"> <span>에스크로(가상계좌)</span></label>
								<?}?>
							<?}?>
							<label class="ml-10"><input type="radio" id="dev_payment6" name="dev_payment" value="M" class="radio_def dev_payment" onclick="sel_paymethod(this);"> <span>휴대폰</span></label>
						</td>
					</tr>
					<?//if($_ShopInfo->getMemid() == "kyung424" || $_ShopInfo->getMemid() == "yiseoyi" ||  $_ShopInfo->getMemid() == "jjus0827" || $_ShopInfo->getMemid() == "sw160071"  || $_ShopInfo->getMemid() == "sw149010" || $_ShopInfo->getMemid() == "for0319"){?>
					<?//if($_ShopInfo->getMemid() == "kyung424" || $_ShopInfo->getMemid() == "yiseoyi" || $_ShopInfo->getMemid() == "for0319"){?>
					<tr>
						<th>간편결제</th>
						<td>
							<label><input type="radio" id="dev_payment7" name="dev_payment" value="Y" class="radio_def dev_payment" onclick="sel_paymethod(this);" checked> <span style="color: red;">PAYCO</span></label>
						</td>
					</tr>
					<td colspan="2" class="info">
							<!-- 신용카드 안내 -->
							<div id="C_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ신용카드 결제시 '카드사혜택’ 버튼을 클릭하시면 무이자할부/청구할인/즉시할인에 대한 정보를 보실 수 있습니다.</li>
									<li>ㆍ체크카드, 법인카드의 경우 무이자 할부행사에서 제외됩니다.</li>
									<li>ㆍ신용카드로 결제하시는 최종 결제 금액이 기준금액 미만이거나, 그 외 무이자 할부가 되지 않는 기타 신용카드를 사용하시는 경우는 유이자 할부로 결제되오니 반드시 참고하시기 바랍니다.</li>
								</ul>
								<?if(strlen($cb_nointerest_info_pc) > 10){?>
								<a href="#modalCard" class="btn btn-default" data-toggle="modal">카드사 혜택</a>
								<?}?>
							</div>
							<!-- // 신용카드 안내 -->


							<!-- 실시간 계좌이체 안내 -->
							<div id="V_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ주문확인 후 NHN KCP 결제창에서 현금영수증 신청이 가능합니다.</li>
									<li>ㆍ결제와 동시에 ㈜신원몰에 입금 처리되며, 10분 이내에 입금확인이 가능합니다.</li>
								</ul>
<!--
								<p>
									고객님의 안전거래를 위해 현금등으로 모든 거래 결제시 저희 쇼핑몰에서<br>
									가입한 KCP 전자결제의 매매보호(에스크로) 서비스를 이용하실 수 있습니다.<br>
									결제대금예치업 등록번호 : 02-006-00001
								</p>
-->
							</div>
							<!-- // 실시간 계좌이체 안내 -->


							<!-- 무통장입금(가상계좌) 안내 -->
							<div id="O_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ가상계좌(무통장) 이용 시 포인트, 쿠폰을 사용했을 경우, 유효기간이 지나기 전에 입금해 주셔야 하며, 유효기간 이후 입금할 경우 주문이 취소됩니다. 가상계좌(무통장) 입금의 경우 입금 확인 후부터 배송이 진행됩니다.</li>
									<li>ㆍ가상계좌(무통장) 결제 시 주문일로 부터 익일 이내 입금을 하지 않을 경우 자동 취소됩니다.</li>
									<li>ㆍ입금 시 주문자 성함과 동일하게 기재해 주시기 바랍니다. 다를 경우 고객센터 (<?=$_data->info_tel?>)로 연락 주시기 바랍니다.</li>
									<li>ㆍ결제 금액과 계좌번호를 SMS로 발송하므로 휴대폰 번호를 정확히 입력해 주시기 바랍니다.</li>
									<li>ㆍ현금영수증 신청은 NHN KCP 결제창에서 제공됩니다.</li>
								</ul>
<!--
								<p>
									고객님의 안전거래를 위해 현금등으로 모든 거래 결제시 저희 쇼핑몰에서<br>
									가입한 KCP 전자결제의 매매보호(에스크로) 서비스를 이용하실 수 있습니다.<br>
									결제대금예치업 등록번호 : 02-006-00001
								</p>
-->
							</div>
							<!-- // 무통장입금(가상계좌) 안내 -->

							<!--핸드폰 안내 -->
							<div id="M_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ신원몰에서 휴대폰으로 결제 가능한 최대 금액은 월 30만원이나, 개인별 한도금액은 통신사 및 개인 설정에 따라 다를 수 있습니다.</li>
									<li>ㆍ휴대폰으로 결제하신 금액은 익월 휴대폰 요금에 함께 청구되며 별도의 수수료는 부과되지 않습니다.</li>
									<li>ㆍ휴대폰 소액결제로 구매하실 경우 현금영수증이 발급되지 않습니다.</li>
									<li><br/></li>
									<li>ㆍ휴대폰 결제로 구매하신 상품의 취소/반품은 처리완료 시점에 따라 다음과 같이 이루어집니다.</li>
									<li>-결제하신 당월에 취소/반품 처리가 완료되는 경우 휴대폰 이용요금에 부과예정이던 금액이 취소됩니다.</li>
									<li>-결제하신 당월이 지난 후 취소/반품처리가 완료되는 경우, 환불액이 고객님의 계좌로 현금 입금해 드립니다.</li>
									<li><br/></li>
									<li>ㆍ휴대폰결제관련 문의사항은 NHN KCP 고객센터 02-2108-1000 또는 신원몰 고객센터 1661-2585으로 연락주시기 바랍니다.</li>
								</ul>
							</div>
							<!-- // !--핸드폰 안내 -->


							<!-- 페이코 안내 -->
							<div id="Y_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍPAYCO는 온/오프라인 쇼핑은 물론 송금, 멤버십 적립까지 가능한 통합 서비스입니다</li>
									<li>ㆍ휴대폰과 카드 명의자가 동일해야 결제 가능하며, 결제금액 제한은 없습니다.</li>
									<li>ㆍ지원카드 : 모든 국내 신용/체크카드</li>
								</ul>
							</div>
							<!-- // 페이코 안내 -->
						</td>
					<?//}else{?>
					<!--<tr>
						<td colspan="2" class="info">실행되는 보안 플러그인에 카드정보를 입력해주세요. 결제는 암호화 처리를 통해 안전합니다. 결제 후 재고가 없거나 본인이 요청이 있을 경우 배송전 결제를 취소할 수 있습니다. </td>
					</tr>-->
					<?//}?>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //결제방식 선택 -->

		<!-- 결제금액 -->
		<div class="calc_area">
			<?$p_price=$sumprice+$sumpricevat;?>
			<input type="hidden" name="total_sum" id='total_sum' value="<?=$p_price?>">
			<input type="hidden" name="total_sumprice" id='total_sumprice' value="<?=$p_price?>">
			<input type='hidden' name='total_deli_price' id='total_deli_price' value="<?=$deli_price?>" >
			<input type='hidden' name='total_deli_price2' id='total_deli_price2' value="<?=$deli_price2?>" >
			<input type='hidden' name='total_deli_price_area' id='total_deli_price_area' value="0" >
			<h3 class="cart_tit">결제금액</h3>
			<div class="cart_calc">
				<ul>
					<li>
						<label>총 상품금액</label>
						<span>￦ <em id="paper_goodsprice" ><?=number_format($sumprice)?></em></span>
					</li>
					<li>
						<label>배송비</label>
						<span>￦ <em id='delivery_price'><?=number_format($deli_price)?></em></span>
					</li>
					<li>
						<label>도서산간 배송비</label>
						<span>￦ <em class='area_delivery_price'>0</em></span>
					</li>
					<hr>
					<li>
						<label>포인트 사용</label>
						<span class="point-color">- <em class="CLS_saleMil">0</em> P</span>
					</li>
					<li>
						<label>E포인트 사용</label>
						<span class="point-color">- <em class="CLS_salePoi">0</em> P</span>
					</li>
<!--					<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>-->
					<li <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
						<label>쿠폰 사용</label>
						<span class="point-color">- ￦ <em class="CLS_prCoupon">0</em></span>
					</li>
					
				</ul>
			</div>

			<div class="cart_calc">
				<ul>
					<li class="all_total">
						<label>실 결제금액</label>
						<span class="point-color">￦ <em class="price_sum" id="price_sum"><?=number_format($sumprice+$deli_price)?></em></span>
					</li>
				</ul>
			</div>
			<?if($staff_order == 'Y') { // 임직원 구매이면?>
			<div class="cart_calc">
				<h4 class="calc_tit">임직원 적립금</h4>
				<ul>
					<li>
						<label>보유 적립금</label>
						<span><?=number_format($staff_reserve)?> P</span>
					</li>
					<li>
						<label>사용예정 적립금</label>
						<span class="point-color">- <?=number_format($staff_pr_price)?> P</span>
					</li>
					
				</ul>
			</div>
			<?}else if($cooper_order == 'Y'){?>
<!--
			<div class="cart_calc">
				<h4 class="calc_tit">제휴사 적립금</h4>
				<ul>
					<li>
						<label>보유 적립금</label>
						<span><?=number_format($cooper_reserve)?> P</span>
					</li>
					<li>
						<label>사용예정 적립금</label>
						<span class="point-color">- <?=number_format($cooper_pr_price)?> P</span>
					</li>
					
				</ul>
			</div>
-->
			<div class="cart_calc">
				<h4 class="calc_tit">총 적립예정 포인트</h4>
				<ul>
					<li>
						<label>포인트</label>
						<span><?=number_format($sum_product_reserve)?> P</span>
					</li>
					
				</ul>
			</div>
			<?}else{?>
			<div class="cart_calc">
				<h4 class="calc_tit">총 적립예정 포인트</h4>
				<ul>
					<li>
						<label>포인트</label>
						<span><?=number_format($sum_product_reserve)?> P</span>
					</li>
					
				</ul>
			</div>
			<?}?>
		</div><!-- //.calc_area -->
		<!-- //결제금액 -->

		<div class="order_agree mt-15">
			<label><input type="checkbox" class="check_def" id='dev_agree'> <span>동의합니다. (전자상거래법 제 8조 제 2항)</span></label>
			<p class="mt-10">주문하실 상품,가격,배송정보,할인내역 등을 최종 확인하였으며, 구매에 동의하시겠습니까?</p>
		</div>
		
		<div class="btn_area mt-20 mr-10 ml-10 button_open">
			<ul>
				<li><a href="javascript:;" class="btn-point h-input" onclick="javascript:CheckForm()">결제하기</a></li>
			</ul>
		</div>
		<div class="btn_area mt-20 mr-10 ml-10 button_close" style="text-align:center; display:none;">
			<ul>
				<li>	========== 처리중 입니다 ==========</li>
			</ul>

		
		</div>

		<div class="order_agree mt-15">
			<p class="mt-10"> 고객님께 다양한 제품을 선보이고자 본사에 재고가 없는 
			제품은 전국 오프라인  매장에서  <br> 발송을 진행하고 있습니다. <br>
			다만 오프라인 매장에서 발송하는 주문건은 매장재고의 변동으로 인해  
			발송지연 및 주문취소가 될 수 있는점 넓은 마음으로 양해를 부탁드립니다.</p>
		</div>

	</section><!-- //.orderpage -->

</main>
<!-- //내용 -->
</form>


<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; display:none'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; display:none"></IFRAME>
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:''" width=100% height=300></IFRAME-->
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME width="100%" height="500" <?if(!isdev()){?>style="display:none"<?}?>></IFRAME-->
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME width="100%" height="500" style="display:none"></IFRAME>
<IFRAME id='CHECK_PAYGATE' name='CHECK_PAYGATE' style='display:none'></IFRAME>





















<?php if( $_SERVER['HTTPS'] == 'on' ){ ?>
    <script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<?php }else{ ?>
    <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php } ?>
<script>
    // 우편번호 찾기 찾기 화면을 넣을 element
    var element_layer = document.getElementById('addressWrap');

    function foldDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function openDaumPostcode() {
        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
				$("#post5").val(data.zonecode);
				$("#rpost1").val(data.postcode1);
				$("#rpost2").val(data.postcode2);
				$("#raddr1").val(data.address);
				$("#raddr2").val('');
				$("#raddr2").focus();
				$("#post").val( data.zonecode );

				//산간배송비 확인
				deli_area_check(data.zonecode);
/*
                document.getElementById('post').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('raddr1').value = fullAddr;
                document.getElementById('raddr2').value = "";
	 			document.getElementById('raddr2').focus();
*/
                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';

                // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                document.body.scrollTop = currentScroll;
            },
            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
            onresize : function(size) {
            		//console.log("Size:", size, element_layer)
                //element_layer.style.height = size.height+'px';
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = (window.innerWidth || document.documentElement.clientWidth)-20; //우편번호서비스가 들어갈 element의 width
        var height = (window.innerHeight || document.documentElement.clientHeight)-200; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 1; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>

<? include_once('outline/footer_m.php'); ?>
