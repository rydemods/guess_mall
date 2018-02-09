<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/order.class.php");
include_once($Dir."lib/delivery.class.php");
include_once($Dir."lib/coupon.class.php");

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

$Order = new Order();

$Delivery = new Delivery();

$Order->order_setting( $basketidxs ); //주문할 장바구니 정보

//$Order->basket_select_item( $basketidxs ); //주문할 장바구니 정보

$_odata = $Order->get_order_object(); //주문에 들어가는 상품정보

// 2016-06-22 중복주문 방지를 위한 주문 check용 추가
$paycode = unique_id();
$orderChkQry = array();
foreach( $_odata as $dataKey => $dataVal ){
    $orderChkQry[] .= "( '".$paycode."', '".$dataVal['productcode']."', '".$dataVal['basketidx']."', '".$_ShopInfo->getMemid()."', '".date('YmdHis')."' )";
}
if( count( $orderChkQry ) > 0 ){
    $orderChkSql = "INSERT INTO tblorder_check ( paycode, productcode, basketidx, id, reg_date ) VALUES ";
    $orderChkSql.= implode( ',', $orderChkQry );
    pmysql_query( $orderChkSql, get_db_conn() );
}
$venderArr = ProductToVender_Sort( $_odata );

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
		$home_zonecode = $row->home_zonecode;
		$reserve_chk= $row->reserve_chk;
		if( $staff_order == 'N' ) $user_reserve = $row->reserve;
		if( $staff_order == 'Y' ) $user_reserve = $row->staff_reserve;
		if($user_reserve>$reserve_limit) {
			$okreserve=$reserve_limit;
			$remainreserve=$user_reserve-$reserve_limit;
		} else {
			$okreserve=$user_reserve;
			$remainreserve=0;
		}
		$home_addr="";
		//if(strlen($row->home_post)==6) {
        $home_post1=substr($row->home_post,0,3);
        $home_post2=substr($row->home_post,3,3);
        $home_post = $row->home_post;
		//}
		$row->home_addr = str_replace("\"","",$row->home_addr);
		$home_addr = explode("↑=↑",$row->home_addr);
		$home_addr1 = $home_addr[0];
		$home_addr2 = $home_addr[1];

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
		$email = $row->email;
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

//$member_coupon = MemberCoupon( 1, 'P', 'BC' );

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
    $total_sql = "
        SELECT
            SUM( ( sellprice + option_price ) * quantity  ) AS total_price_sum, SUM( quantity )::int AS total_qty
        FROM
            (
                SELECT 
                    p.sellprice, bk.quantity,  COALESCE( po.option_price, 0 ) AS option_price
                FROM 
                    tblbasket AS bk
                JOIN
                    tblproduct AS p ON ( bk.productcode = p.productcode )  
                LEFT JOIN
                    tblproduct_option AS po ON ( bk.productcode = po.productcode AND bk.optionarr = po.option_code )
                WHERE 
                    id = '".$_ShopInfo->getMemid()."' 
                AND
                    basketidx IN ( ".str_replace( '|', ',', $basketidxs )." )
        ) AS basket
    ";
    $total_res = pmysql_query( $total_sql, get_db_conn() );
    $total_row = pmysql_fetch_object( $total_res );
    pmysql_free_result( $total_res );
}

//$iFrameLayrUrl = $Dir.FrontDir.'coupon_order_layer.php?basketidxs='.$basketidxs;
?>
<?include ($Dir.MainDir.$_data->menu_type.".php");?>
<TITLE><?=$_data->shoptitle?> - 주문서 작성</TITLE>

<!-- <script type="text/javascript" src="/js/jquery-1.10.1.min.js"></script> -->
<!-- <script src="http://dmaps.daum.net/map_js_init/postcode.js"></script> -->
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			/*document.getElementById('rpost1').value = data.postcode1;
			document.getElementById('rpost2').value = data.postcode2;
			document.getElementById('raddr1').value = data.address;
			document.getElementById('raddr2').value = '';
			document.getElementById('raddr2').focus();*/
			//$("#rpost1").val(data.postcode1);
			$("#post5").val(data.zonecode);
			$("#post-code").val(data.postcode1);
			$("#rpost2").val(data.postcode2);
			$("#raddr1").val(data.address);
			$("#raddr2").val('');
			$("#raddr2").focus();
			$("#post").val( data.zonecode );
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}

$(window).ready(function(){
	var payMethodType = 'C';
	/*
	var chk_product_staff = $("input[name='chk_product_staff']").val();
	if(chk_product_staff){
		// 스태프 상품이 있는경우 적립금 & 쿠폰 사용 불가
		$(".CLS_useReserve").hide();
		$(".CLS_useCoupon").hide();
		$("input[name='usereserve']").attr("disabled",true);
		payMethodType = 'B';
		// 스태프 상품이 있는경우 무통장만 남기고 숨김
		$(".CLS_paymentArea li:not(:first-child)").hide();
	}else{
		payMethodType = 'C';
	}
	*/
	
	
	var deli_price = $("#delivery_price").html();
	
	if(uncomma(deli_price) == 0){
		$('#deli_type1').attr('disabled',true);
	}
	/*
	$(".deli_type, .dev_payment, .receipt_yn").click(function(){
		var deli_type = $(".deli_type:checked").val();
		if(deli_type=="1"){
			$("#delivery_price").html(0);
			$("#delivery_price2").html(0+"원");
		}else{
			$("#delivery_price").html(deli_price);
			$("#delivery_price2").html(deli_price+"원");
		}
		calcuPayment();
	});
    */
	$(".CLS_deliMsg").click(function(){
		$(this).parent().parent().parent().prev().val($(this).html());
		$('div.delivery_message').remove();
		//$('div.delivery_message').css('display' , 'none');
	});

	$(".dev_payment").each(function(){
		/*
		if($(this).val() == 'C'){
			$(this).prop('checked', true);
			sel_paymethod(this);
		}
		*/
		if($(this).val() == payMethodType){
			$(this).prop('checked', true);
			sel_paymethod(this);
		}
	});
	
});

function calcuPayment(){

	var paymethod = $(".dev_payment:checked").val();
	var receipt_yn = $(".receipt_yn:checked").val();
	var total_reserve = 0;

	$.get("product_dc_price.php?paymethod="+paymethod+"&receipt_yn="+receipt_yn,function(data,status){

		$("#memberdc_price").html(comma(data)); 
		payment_product_price = uncomma($("#paper_goodsprice").html());
		payment_delivery_price = uncomma($("#delivery_price").html());
		payment_memberdc_price = data; 
		//uncomma($("#memberdc_price").html());
		payment_coupondc_price = uncomma($("#coupon_dc").val());
		payment_usereserve_price = uncomma($("#usereserve").val());
		// - payment_memberdc_price
		if($("#beforehand_reserve").prop("checked")){
			total_reserve = parseInt($("#total_reserve").val());
		}else{
			total_reserve = 0;
		}
		
		pament_price = payment_product_price + payment_delivery_price - payment_coupondc_price - payment_usereserve_price - total_reserve;
		//$("#price_sum").html(comma(pament_price));
		//$("#price_sum2").html(comma(pament_price)+"<span>원</span>");

	});

}


function change_message(gbn) {
	if(gbn==1) {
		document.all["msg_idx2"].style.display="none";
		document.all["msg_idx1"].style.display="";
		document.form1.msg_type.value=gbn;
	} else if(gbn==2) {
		document.all["msg_idx2"].style.display="";
		document.all["msg_idx1"].style.display="none";
		document.form1.msg_type.value=gbn;
	}
}

function SameCheck(checked) {
	if(checked) {
		document.form1.receiver_name.value=document.form1.sender_name.value;
		document.form1.receiver_tel11.value="<?=$home_tel[0]?>";
		document.form1.receiver_tel12.value="<?=$home_tel[1]?>";
		document.form1.receiver_tel13.value="<?=$home_tel[2]?>";
		document.form1.receiver_tel21.value=document.form1.sender_tel1.value;
		document.form1.receiver_tel22.value=document.form1.sender_tel2.value;
		document.form1.receiver_tel23.value=document.form1.sender_tel3.value;

		document.form1.rpost1.value="<?=$home_post1?>";
		document.form1.rpost2.value="<?=$home_post2?>";
		document.form1.raddr1.value="<?=$home_addr1?>";
		document.form1.raddr2.value="<?=$home_addr2?>";
		document.form1.post.value="<?=$home_post?>";
	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel11.value="";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";
		document.form1.receiver_tel21.value="";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";
		document.form1.post.value="";
		document.form1.rpost1.value='';
		document.form1.rpost2.value='';
		document.form1.raddr1.value='';
		document.form1.raddr2.value='';
	}
}

<?php if(strlen($_ShopInfo->getMemid())>0){?>
function addrchoice() {
	window.open("<?=$Dir.FrontDir?>addrbygone.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
}

function coupon_cancel(){
	$("#ID_coupon_code_layer").html("");
	document.form1.coupon_code.value=0;
	document.form1.coupon_dc.value=0;
	$(".CLS_saleCoupon").html('0원');
	document.form1.coupon_reserve.value=0;
	$(".CLS_saleMil").html('0원');

	document.getElementById("sumprice").value=parseInt(document.form1.total_sum.value)-(parseInt(document.form1.coupon_dc.value)+parseInt(document.form1.usereserve.value));

	document.getElementById("price_sum").innerHTML=comma(parseInt(document.form1.total_sum.value)-(parseInt(document.form1.coupon_dc.value)+parseInt(document.form1.usereserve.value)));

	payment_reset();
}

function reserve_check(temp) {
	r_type="<?=$rcall_type?>";
	
	var total_reserve = 0;
	
	if($("#beforehand_reserve").prop("checked")){
		total_reserve = parseInt($("#total_reserve").val());
	}else{
		total_reserve = 0;
	}

	if(r_type=="N" && document.form1.coupon_code.value){
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('쿠폰과 적립금은 동시 사용이 불가능합니다.');
		return;
	}
	temp=parseInt(temp);
	if(isNaN(document.form1.usereserve.value)) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('숫자만 입력하셔야 합니다.');
		return;
	}
	if(parseInt(document.form1.usereserve.value)>temp) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('사용가능 적립금 보다 적거나 똑같이 입력하셔야 합니다.');
		return;
	}
	if(parseInt(document.form1.coupon_dc.value)+parseInt(document.form1.usereserve.value)>parseInt(document.form1.total_sum.value)){
		//document.getElementById("dc_price").innerHTML=comma(parseInt(document.form1.coupon_dc.value));
		document.getElementById("price_sum").innerHTML=comma(parseInt(document.form1.total_sum.value)-parseInt(document.form1.coupon_dc.value));
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('총 합계 금액 보다 적거나 똑같이 입력하셔야 합니다.');
		return;
	}

	document.form1.okreserve.value=temp - document.form1.usereserve.value;
	document.form1.usereserve.value=temp - document.form1.okreserve.value;

	//document.getElementById("dc_price").innerHTML=comma(parseInt(document.form1.coupon_dc.value)+parseInt(document.form1.usereserve.value));
	document.getElementById("sumprice").value=parseInt(document.form1.total_sum.value)-(parseInt(document.form1.coupon_dc.value)+parseInt(document.form1.usereserve.value) + parseInt(total_reserve));

	document.getElementById("price_sum").innerHTML=comma(parseInt(document.form1.total_sum.value)-(parseInt(document.form1.coupon_dc.value) + parseInt(document.form1.usereserve.value) + parseInt(total_reserve) ));

	$('.CLS_saleMil').html(comma($("#usereserve").val())+"원");

	//payment_reset();
	calcuPayment();
}
<?php }?>


function payment_reset(){

	for(var i=0;i<document.getElementsByName("dev_payment").length;i++){
		document.getElementsByName("dev_payment")[i].checked=false;
	}
}

function payment_card(){
	for(var i=0;i<document.getElementsByName("dev_payment").length;i++){
		document.getElementsByName("dev_payment")[i].checked=false;
	}
	// 카드 쿠폰선택시 신용카드 외 나머지 결제 방법을 막는다
	$("input[name='dev_payment']").each(function(){
		if( $(this).val() != 'C' ) $(this).prop( 'disabled', true );
		else $(this).prop( 'checked', true );
	});
}

function payment_disabled_off(){
	$("input[name='dev_payment']").each(function(){
		$(this).prop( 'disabled', false );
	});
}

function orderpaypop() {
	if(typeof(document.form1.usereserve)!="undefined") {
		document.orderpayform.usereserve.value=document.form1.usereserve.value;
	}
	if(typeof(document.form1.coupon_code)!="undefined") {
		document.orderpayform.coupon_code.value=document.form1.coupon_code.value;
	}
	document.orderpayform.email.value=document.form1.sender_email.value;
	document.orderpayform.address.value=document.form1.raddr1.value;
	document.orderpayform.mobile_num1.value=document.form1.sender_tel1.value;
	document.orderpayform.mobile_num.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value

	var winpaypop=window.open("about:blank","orderpaypop","width=620,height=550,scrollbars=yes");
	winpaypop.focus();

<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ORDER"]=="Y") {?>
	document.orderpayform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>orderpay.php';
<?php }?>

	document.orderpayform.submit();
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

function ProcessWaitPayment() {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait2.gif";

	$("#PAYWAIT_LAYER").show();

	var t = $(document).scrollTop();
	var w = ($(window).width()-$("#PAYWAIT_LAYER").width())/2;
	var h = ($(window).height()-$("#PAYWAIT_LAYER").height())/2;


//				$("#PAYWAIT_LAYER").css("position","absolute");
	$("#PAYWAIT_IFRAME").css("display","block");
	$("#PAYWAIT_IFRAME").css("left",w);
	$("#PAYWAIT_IFRAME").css("top",t+h);

	$("#PAYWAIT_LAYER").css("display","block");
	$("#PAYWAIT_LAYER").css("left",w);
	$("#PAYWAIT_LAYER").css("top",t+h);
	/*
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.display='block';
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.display='block';
	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
	*/
}

function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
	ProcessWait('visible');
}

function setPackageShow(packageid) {
	if(packageid.length>0 && document.getElementById(packageid)) {
		if(document.getElementById(packageid).style.display=="none") {
			document.getElementById(packageid).style.display="";
		} else {
			document.getElementById(packageid).style.display="none";
		}
	}
}

function sel_paymethod(obj){

	var frm=document.form1;
	var totp=uncomma(document.getElementById("price_sum").innerHTML);

	if (obj.value=='B') {
		document.getElementById("card_type").style.display="block";
		document.getElementById("payco_notice").style.display="none";
    } else if ( obj.value == 'Y' ) {
		document.getElementById("payco_notice").style.display="block";
		document.getElementById("card_type").style.display="none";
	} else {
		document.getElementById("card_type").style.display="none";
		document.getElementById("payco_notice").style.display="none";
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

// 주문자 휴대전화 / 전화번호 앞자리 선택
function sender_tel_select(){
	$("#sender_tel1").val( $("#senderPhon").text() );
	$("#home_tel1").val( $("#senderTel").text() );
}

// 받는이 휴대전화 / 전화번호 앞자리 선택
function receiver_tel_select(){
	$("#receiver_tel21").val( $("#receiverPhon").text() );
	$("#receiver_tel11").val( $("#receiverTel").text() );
}

//-->
</SCRIPT>
<form name='form1' action="<?=$Dir.FrontDir?>ordersend.php" method='post'>
<input type="hidden" name="addorder_msg" value="">
<input type="hidden" id="direct_deli" name="direct_deli" value="N">
<input type='hidden' name='overseas_code' value='' > <!-- 통관번호 -->
<input type='hidden' name='basketidxs' value='<?=$basketidxs?>' > <!-- 장바구니 번호 -->
<input type='hidden' name='staff_order' value='<?=$staff_order?>' > <!-- 임직원 구매 -->
<input type='hidden' name='paycode' value='<?=$paycode?>' > <!-- 주문체크용 코드 -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
	<?	
		include ($Dir.TempletDir."order/orderTEM01_publish.php"); 
	?>
	</td>
</tr>
</table>

<?php
if($sumprice<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
} else if($sumprice<=0) {
	echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
}

//if(strlen($_ShopInfo->getMemid())>0) echo "<script>document.form1.addrtype[0].checked=true;addrchoice();</script>";
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
		
		// 주문자 휴대전화 / 전화번호 앞자리 선택
		sender_tel_select()


		// 받는이 휴대전화 / 전화번호 앞자리 선택
		receiver_tel_select()
		
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
		

		if(document.form1.paymethod.value=='B' && document.form1.bank_sender.value==''){
			alert("입금자명을 입력하세요.");
			document.form1.bank_sender.focus();
			return;
		}
		

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

		if(document.form1.sender_tel1.value.length==0) {
			alert("주문자 전화번호를 입력하세요.");
			document.form1.sender_tel1.focus();
			return;
		}
		if(document.form1.sender_tel2.value.length==0) {
			alert("주문자 전화번호를 입력하세요.");
			document.form1.sender_tel2.focus();
			return;
		}
		if(document.form1.sender_tel3.value.length==0) {
			alert("주문자 전화번호를 입력하세요.");
			document.form1.sender_tel3.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel1.value)) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			document.form1.sender_tel1.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel2.value)) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			document.form2.sender_tel2.focus();
			return;
		}
		if(!IsNumeric(document.form1.sender_tel3.value)) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			document.form3.sender_tel3.focus();
			return;
		}
		document.form1.sender_tel.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value;

		if(document.form1.sender_email.value.length>0) {
			if(!IsMailCheck(document.form1.sender_email.value)) {
				alert("주문자 이메일 형식이 잘못되었습니다.");
				document.form1.sender_email.focus();
				return;
			}
		}

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
		/*
		if(document.form1.receiver_tel11.value.length==0) {
			alert("받는분 전화번호를 입력하세요.");
			document.form1.receiver_tel11.focus();
			return;
		}
		if(document.form1.receiver_tel12.value.length==0) {
			alert("받는분 전화번호를 입력하세요.");
			document.form1.receiver_tel12.focus();
			return;
		}
		if(document.form1.receiver_tel13.value.length==0) {
			alert("받는분 전화번호를 입력하세요.");
			document.form1.receiver_tel13.focus();
			return;
		}
		
		if(!IsNumeric(document.form1.receiver_tel11.value)) {
			alert("받는분 전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel11.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel12.value)) {
			alert("받는분 전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel12.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel13.value)) {
			alert("받는분 전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel13.focus();
			return;
		}
		*/
		document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

		if(document.form1.receiver_tel21.value.length==0) {
			alert("받는분 비상전화번호를 입력하세요.");
			document.form1.receiver_tel21.focus();
			return;
		}
		if(document.form1.receiver_tel22.value.length==0) {
			alert("받는분 비상전화번호를 입력하세요.");
			document.form1.receiver_tel22.focus();
			return;
		}
		if(document.form1.receiver_tel23.value.length==0) {
			alert("받는분 비상전화번호를 입력하세요.");
			document.form1.receiver_tel23.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel21.value)) {
			alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel21.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel22.value)) {
			alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel22.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel23.value)) {
			alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
			document.form1.receiver_tel23.focus();
			return;
		}
		document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;

		//주문자 전화번호 추가
		/*
		if( !IsNumeric( $("#home_tel1").val() ) ) {
			alert("주문자 전화번호 입력은 숫자만 입력하세요.");
			$("#home_tel1").focus();
			return;
		}
		if( !IsNumeric( $("#user-tel").val() ) ) {
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
		 $("input[name='home_tel']").val( $("#home_tel1").val()+'-'+$("#user-tel").val()+'-'+$("#home_tel3").val() );


/*
		if(document.form1.rpost1.value.length==0 || document.form1.rpost2.value.length==0) {
			alert("우편번호를 선택하세요.");
			openDaumPostcode();
			return;
		}
*/
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
			alert("적립금 사용가능금액보다 큽니다.");
			document.form1.usereserve.focus();
			return;
		} else if(document.form1.usereserve.value < 0) {
			alert("적립금은 0원보다 크게 사용하셔야 합니다.");
			document.form1.usereserve.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && ord($okreserve) && $okreserve>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
		if(document.form1.usereserve.value>0 && document.form1.coupon_code.value.length==8){
			alert('적립금과 쿠폰을 동시에 사용이 불가능합니다.\n둘중에 하나만 사용하시기 바랍니다.');
			document.form1.usereserve.focus();
			return;
		}
		<?php  } ?>

		<?php  if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
		if (document.form1.usereserve.value>0) {
			if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
				alert('적립금은 현금결제시에만 사용이 가능합니다.\n현금결제로 선택해 주세요');
				document.form1.paymethod.value="";
				return;
			}
		}
		<?php  } ?>
	<?php  } ?>

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

		<?php  if($etcmessage[2]=="Y") { ?>
			if(document.form1.bank_sender.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
				if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
				document.form1.order_msg.value+="입금자 : "+document.form1.bank_sender.value;
			}
		<?php  } ?>

			//지역별 추가배송료 확인
		<?php
	/*
			echo "address = \" \"+document.form1.raddr1.value;\n";
			$array_deli = explode("|",$_data->deli_area);
			$cnt= floor(count($array_deli)/2);
			for($i=0;$i<$cnt;$i++){
				$subdeli=explode(",",$array_deli[$i*2]);
				$subcnt=count($subdeli);
				echo "if(";
				for($j=0;$j<$subcnt;$j++){
					if($j!=0) echo " || ";
					echo "address.indexOf(\"{$subdeli[$j]}\")>0";
				}
				echo "){ if(!confirm('";
				if($array_deli[$i*2+1]>0) echo "해당 지역은 배송료 ".number_format($array_deli[$i*2+1])."원이 추가됩니다.";
				else echo "해당 지역은 배송료 ".number_format(abs($array_deli[$i*2+1]))."원이 할인됩니다.";
				echo "')) return;}\n";
			}
	*/
		?>


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
			document.form1.submit();

            if ( paymethod == "Y" ) {
                // 페이코 결제인 경우
                // 해당 내용을 pg_url에서 해준다.
            } else {
                document.all.paybuttonlayer.style.display="none";
                document.all.payinglayer.style.display="block";
            }

			if(paymethod!="B") ProcessWait("visible");

		} else {
			ordercancel();
		}
	}
}

var total_deli_price = 0; //선불 배송료
var total_deli_price2 = 0; //착불배송료

$(document).ready(function(){
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
        $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( total_prdc ) - parseInt( total_bdc ) - parseInt( tital_mileage ) + parseInt( $('#total_deli_price').val() ) ) );
    });
    // 배송비 쿠폰
    $('input[name="dcoupon_ci_no"]').click(function(){
        var dcoupon_price = total_deli_price; // 넘어가는 값의 배송료를 빼주기 위함
        if( $(this).prop('checked') ) {
            $('input[name="dcoupon_price"]').val( dcoupon_price );
            $('#delivery_price').html( comma( 0 ) );
            $('#delivery_price2').html( comma( 0 ) );
            $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( total_prdc ) - parseInt( total_bdc ) - parseInt( tital_mileage ) + parseInt( 0 ) ) );
        } else {
            $('input[name="dcoupon_price"]').val( 0 );
            $('#delivery_price').html( comma( total_deli_price ) );
            $('#delivery_price2').html( comma( total_deli_price2 ) );
            $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( total_prdc ) - parseInt( total_bdc ) - parseInt( tital_mileage ) + parseInt( $('#total_deli_price').val() ) ) );
        }
    });

	$("#beforehand_reserve").on("click",function(){
		var total_sum = $("input[name='total_sum']").val();
		if($(this).prop("checked")){
			$("#price_sum").html( comma( parseInt(total_sum) - parseInt($("#total_reserve").val()) - parseInt($("#coupon_dc").val()) - parseInt($("#usereserve").val()) + parseInt( $('#total_deli_price').val() ) ) );
			$(".CLS_beforehand_reserve").html( comma( parseInt($("#total_reserve").val()) )+'원' );
		}else{
			$("#price_sum").html( comma( parseInt(total_sum) - parseInt($("#coupon_dc").val()) - parseInt($("#usereserve").val()) + parseInt( $('#total_deli_price').val() ) ) );
			$(".CLS_beforehand_reserve").html( 0+'원' );
		}
	});

	//주문 메세지 복사
	$("#prmsg_chg > li > a").click( function(){
        if( $("#prmsg_chg > li > a").index( $(this) ) == 0 ){
            $('#delivery-needs').val( '' );
            $('#delivery-needs').focus();
        } else {
    		$('#delivery-needs').val( $(this).html() );
        }
	} );
	//이메일
	$(".domain-list li").mouseover(function(){
		var objIndex = $(".domain-list li").index(this);
		selectDomain = objIndex;
		$(".domain-list li a").css('background', "white");
		$(".domain-list li:eq("+selectDomain+") a").css('background', "#f3f3f3");
	});
	//이메일
	$(".domain-list li").click(function(){
		if(selectDomain > -1){
			$("#user-email").val($(".domain-list li:eq("+selectDomain+") a").html());
			$(".domain-list").hide();
		}
	});
	//이메일
	$("#user-email").focusout(function(){
		window.setTimeout(function() { $(".domain-list").hide(); }, 200);
	});
	// 옵션 z-index 설정
	var option_cnt = $('.select').length;
	if( option_cnt > 0 ){
		$('.select').each( function( idx, obj ) {
			$(this).css( 'z-index', ( option_cnt * 5 ) - ( idx * 5 ) );
		});
	}

});
//이메일
var selectDomain = -1;
function domail_list_up(val) {
	if(event.keyCode==13){
		// 엔터
		$("#user-email").val($(".domain-list li:eq("+selectDomain+") a").html());
		$(".domain-list").hide();
	}else if(event.keyCode==40){
		// ↓
		if(selectDomain >= $(".domain-list li").length -1){
			selectDomain = $(".domain-list li").length -1;
		}else{
			selectDomain++;
		}
		$(".domain-list li a").css('background', "white");
		$(".domain-list li:eq("+selectDomain+") a").css('background', "#f3f3f3");
	}else if(event.keyCode==38){
		// ↑
		if(selectDomain <= 0){
			selectDomain = 0;
		}else{
			selectDomain--;
		}
		$(".domain-list li a").css('background', "white");
		$(".domain-list li:eq("+selectDomain+") a").css('background', "#f3f3f3");
	}else{
		$(".domain-list").show();
		var spc			= val.search(/[@]/g);
		if (spc != -1) {
			val_arr	= val.split("@");
			val		= val_arr[0];
		}

		//alert(val);
		$(".domain-list").find("li").each(function(e) {
			var domain = $(this).find("a").attr("alt");
			$(this).find("a").html(val+domain);
		});
	}
}
//-->
</SCRIPT>

<!-- 쿠폰 스크립트 -->
<script>
    var _prCouponObj       = [];   // 상품쿠폰 내용
    var _bkCouponObj       = {};   // 장바구니쿠폰 내용
    var _deliCouponObj     = {};   // 배송비 무료쿠폰 내용
    var _sum_price         = 0; // 상품 결제가
    var _total_prdc        = 0; // 상품쿠폰가
    var _total_bdc         = 0; // 장바구니 쿠폰가
    var _total_mileage     = 0; //마일리지

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
        total_sum         = _sum_price;
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

    });
    // 상품쿠폰 세팅
    function set_product_coupon( obj ){ // 쿠폰 기본세팅
         _prCouponObj = obj;   // 상품쿠폰 내용 
         _total_prdc = product_coupon_dc();
         coupon_update();
    }
    $(document).on( 'click', '.coupon-use', function(){
        product_coupon_pop();
    });
    // 상품쿠폰 팝업
    function product_coupon_pop(){
        coupon_use_type( 1 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        if( reset_dc( 3 ) ){
            var basketidxs = $('input[name="basketidxs"]').val();
            var src = 'product_coupon_layer.php?basketidxs' + basketidxs;
            $('#coupon_layer').attr( 'src', src ).off().on( 'load', function() { 
                $(this)[0].contentWindow.set_prd_coupon( _prCouponObj );
                $(this)[0].contentWindow.radio_box_set();
            } ).fadeIn();
        }
    }
    // 상품쿠폰 닫기
    function product_coupon_close(){
         $('#coupon_layer').fadeOut('fast');
    }
    // 상품쿠폰 할인 금액 ( 총액 )
    function product_coupon_dc(){
        var prd_dc = 0;
        $.each( _prCouponObj, function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
                prd_dc += _obj.obj.dc;
            }
        });

        return prd_dc;
    }
    // 상품쿠폰 초기화
    function reset_prdc(){
        _total_prdc  = 0;
        _prCouponObj = [];
    }

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
        $('.CLS_coupon_value').html('<span>쿠폰선택</span>');
        $('.CLS_coupon_value').removeClass('selected');
        $('.CLS_coupon_value').parent().find('ul.a_list > li.hover').removeClass('hover');
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
    // 쿠폰 선택 초기화
    function reset_dc( type ){
        var resetType  = false;
        if( _total_mileage > 0 && _total_bdc > 0 && ( type == 2 || type == 3 ) ){
            if( confirm('선택한 쿠폰 / 마일리지가 초기화 됩니다. 쿠폰을 다시 선택하시겠습니까?') ){
                mileage_cancel();
                reset_prdc();
                default_set_basket_coupon();
                coupon_update();
                resetType = true;
            }
        } else if( _total_bdc > 0 && type == 3 ){
            if( confirm('선택한 할인쿠폰이 초기화 됩니다. 쿠폰을 다시 선택하시겠습니까?') ){
                default_set_basket_coupon();
                coupon_update();
                resetType = true;
            }
        } else if( _total_mileage > 0 ){
                if( confirm('마일리지가 초기화 됩니다.') ){
                mileage_cancel();
                coupon_update();
                resetType = true;
            }
        } else {
            resetType = true;
        }

        return resetType;
    }
    
    //선/착불 선택
    
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
        $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) + parseInt( $('#total_deli_price').val() ) ) );
    });
    
    // 배송비 쿠폰
    $('input[name="dcoupon_ci_no"]').click(function(){
        var dcoupon_price = total_deli_price; // 넘어가는 값의 배송료를 빼주기 위함
        if( $(this).prop('checked') ) {
            $('input[name="dcoupon_price"]').val( dcoupon_price );
            $('#delivery_price').html( comma( 0 ) );
            $('#delivery_price2').html( comma( 0 ) );
            $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) + parseInt( 0 ) ) );
        } else {
            $('input[name="dcoupon_price"]').val( 0 );
            $('#delivery_price').html( comma( total_deli_price ) );
            $('#delivery_price2').html( comma( total_deli_price2 ) );
            $('#price_sum').html( comma( parseInt( $('#total_sum').val() ) - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) + parseInt( $('#total_deli_price').val() ) ) );
        }
    });

    function coupon_update(){
        $('.CLS_prCoupon').html( comma( _total_prdc ) );
        $('.CLS_bCoupon').html( comma( _total_bdc ) );
        $('.CLS_saleMil').html( comma( _total_mileage ) );
        $('#price_sum').html( comma( total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) + parseInt( $('#total_deli_price').val() ) ) );
        $('#total_sumprice').val( total_sum - parseInt( _total_prdc ) - parseInt( _total_bdc ) - parseInt( _total_mileage ) );
    }

    //숫자키 이외의 값은 막는다
    $(document).on( 'keydown', '#mileage-use', function ( event ) {
        if( !isNumKey( event ) ) event.preventDefault();
        if( event.keyCode != 8 && $(this).val().length > 0 ) $(this).val( parseInt( $(this).val() ) );
    });
    //마일리지 계산
    $(document).on( 'keyup', '#mileage-use', function ( event ) {
        coupon_use_type( 3 )  // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        var okreserve = parseInt( $('#okreserve').val() );
        var mileage	= parseInt( $(this).val() );
        var sum_price = total_sum + total_deli_price - parseInt( _total_prdc ) - parseInt( _total_bdc );

        if( $(this).val().length > 0 ){
            if( okreserve < mileage ) {
                if( okreserve > sum_price ){
                    alert('구매 금액보다 큰 값을 입력했습니다.');
                    $(this).val( sum_price );
                } else {
                    alert('보유 마일리지보다 큰 값을 입력했습니다.');
                    $(this).val( okreserve );
                }
            } else {
                if( mileage > sum_price ){
                    alert('구매 금액보다 큰 값을 입력했습니다.');
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

    function set_total_mileage(){
        _total_mileage = parseInt( $('#mileage-use').val() );
    }

    //마일리지 취소
    function mileage_cancel(){
        $("#mileage-use").val( 0 );
        _total_mileage = parseInt( $("#mileage-use").val() );
    }
    // 쿠폰 설정 체크
    function coupon_use_type( type ){ // type 1 -> 상품 type 2 -> 장바구니 type 3 -> 마일리지
        
        // 쿠폰 마일리지 동시사용 불가
        if( all_type != 'Y' ){
            if( ( type == 1 || type == 2 ) && _tital_mileage > 0 ){
                if( confirm( "쿠폰과 마일리지는 동시사용이 불가능합니다.\n마일리지을 다시 선택하시겠습니까?" ) ){
                    mileage_cancel();
                    coupon_update();
                } else {
                    reset_prdc();
                    default_set_basket_coupon();
                    coupon_update();
                }
            } else if( type == 3 && ( _total_prdc > 0 || _total_bdc > 0 ) ) {
                if( confirm( "쿠폰과 마일리지는 동시사용이 불가능합니다.\n쿠폰을 다시 선택하시겠습니까?" ) ){
                    reset_prdc();
                    default_set_basket_coupon();
                    coupon_update();
                } else {
                    mileage_cancel();
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
                    default_set_basket_coupon();
                    coupon_update();
                }
            }
        }

    }
    // 결제로 넘어갈 쿠폰값을 레이어에 넘겨준다
    function set_coupon_layer(){
        var prd_layer        = $('#ID_prd_coupon_layer'); // 상품쿠폰이 담길 레이어 위치
        var bk_layer         = $('#ID_bk_coupon_layer');  // 장바구니 쿠폰이 담길 레이어 위치
        var deli_layer       = $('#ID_deli_coupon_layer');  // 장바구니 쿠폰이 담길 레이어 위치
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
        // 장바구니 쿠폰
        if( _bkCouponObj.ci_no != '' ){
            bk_coupon_html += '<input type="hidden" name="bcoupon_ci_no[]" value="' + _bkCouponObj.ci_no + '" >';
        }
        //배송비 쿠폰
        if( _deliCouponObj.ci_no != '' ) {
            deli_coupon_html += '<input type="hidden" name="dcoupon_ci_no" value="' + _deliCouponObj.ci_no + '" >';
            deli_coupon_html += '<input type="hidden" name="dcoupon_price" value="' + _deliCouponObj.dc + '" >';
        }

        $( prd_layer ).html( pr_coupon_html );
        $( bk_layer ).html( bk_coupon_html );
        $( deli_layer ).html( deli_coupon_html );

    }

</script>
<!-- //쿠폰 스크립트 -->

<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; display:none'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; display:none;"></IFRAME>
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:''" width=100% height=300></IFRAME-->
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME width="100%" height="500" <?if(!isdev()){?>style="display:none"<?}?>></IFRAME-->
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME width="100%" height="500" style="display:none;"></IFRAME>
<IFRAME id='CHECK_PAYGATE' name='CHECK_PAYGATE' style='display:none'></IFRAME>
<iframe class='layer-iframe' id='coupon_layer' ></iframe>
<?=$onload?>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
