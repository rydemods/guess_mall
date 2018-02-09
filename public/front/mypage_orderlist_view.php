<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once ($Dir."lib/product.class.php");

$product = new PRODUCT();
#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################
function dateDiff($nowDate, $oldDate) { 
	$nowDate = date_parse($nowDate); 
	$oldDate = date_parse($oldDate); 
	return ((gmmktime(0, 0, 0, $nowDate['month'], $nowDate['day'], $nowDate['year']) - gmmktime(0, 0, 0, $oldDate['month'], $oldDate['day'], $oldDate['year']))/3600/24); 
}

$ordercode = $_POST["ordercode"] ? $_POST["ordercode"] : $_GET["ordercode"];				//로그인한 회원이 조회시

$ordername = ($_POST["ordername"]) ? $_POST["ordername"] : $_GET["ordername"];			//비회원 조회시 주문자명
$ordercodeid = ($_POST["ordercodeid"]) ? $_POST["ordercodeid"] : $_GET["ordercodeid"];		//비회원 조회시 주문번호 6자리

if(ord($ordercodeid) && strlen($ordercodeid)!=6) {
	alert_go('주문번호 6자리를 정확히 입력하시기 바랍니다.','c');
}

$tempkey = ($_POST["tempkey"]) ? $_POST["tempkey"] : $_GET["tempkey"];

# order product 불러오기

# 주문 세팅

$i=0;
$_ord = '';
$orProduct = '';
$orOption = '';

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";

$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_ord=$row;
	$gift_price=$_ord->price-$row->deli_price;
} else {
	echo "<html></head><body onload=\"alert(".$sql."); location.href='/'\"></body></html>";
// 	exit;
}
pmysql_free_result($result);

$pg_ordercode=$_ord->pg_ordercode;
$pgid_info="";
$pg_type="";
switch ($_ord->paymethod[0]) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_data->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_data->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_data->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_data->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_data->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_data->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);

# 반송가능상품(주문)
$redaliveryArray = array();
$redelivery_sql = "SELECT redelivery_type,redelivery_date,redelivery_reason,deli_date FROM tblorderinfo ";
$redelivery_sql.= "WHERE ordercode = '{$ordercode}' ";
$redelivery_sql.= "AND deli_date != '' ";
$redelivery_res = pmysql_query($redelivery_sql,get_db_conn());
if($redelivery_row = pmysql_fetch_object($redelivery_res)){
	if(dateDiff(date("YmdHis"),$redelivery_row->deli_date) < 11){
		$redaliveryArray = $redelivery_row; 
	}
}
pmysql_free_result($redelivery_res);

#주문상품
$sql = "SELECT 
				a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
				a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
				a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
				b.minimage, a.option_type, a.option_price, a.option_quantity, 
				a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
				a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
				a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, b.option1_tf, option2_tf, option2_maxlen, 
				a.delivery_type, a.store_code, a.reservation_date, b.prodcode, b.colorcode, b.colorcode, b.prodcode,
                a.store_stock_yn, a.oc_no, a.use_epoint, a.deli_closed
			FROM 
				tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
			WHERE 
				a.ordercode='".$ordercode."' 
			ORDER BY vender ASC, productcode ASC ";

$erp_result=pmysql_query($sql,get_db_conn());
//exdebug($sql);
while($erp_row=pmysql_fetch_object($erp_result)) {
	if ($erp_row->prodcode !='' && $erp_row->colorcode !='') {
		//ERP 상품의 사이즈 수량정보를 쇼핑몰에 업데이트한다.
		getUpErpSizeStockUpdate($erp_row->productcode, $erp_row->prodcode, $erp_row->colorcode);
	}
}
pmysql_free_result($erp_result);

$result=pmysql_query($sql,get_db_conn());

# 물류 정보를 확인해서 값이 변경될 경우 reload를 시킨다 2016-11-28 유동혁
$reloadFlag = false;

//에스크로 결제건일때 모든 상품이 배송중이면 전체취소를 노출시킨다.
list($step_count)=pmysql_fetch("select count(idx) from tblorderproduct where op_step!='3' and ordercode='".$ordercode."'");

while($row=pmysql_fetch_object($result)) {
	$i++;
	$isnot=false;
	$tmpPrice = 0;
	$tmpQuantity = 0;

	# 상품정보
	$orProduct[$row->idx] = (object) array(
		'vender' => $row->vender,
		'brand' => $row->brand,
		'brandname' => $row->brandname,
		'productcode' => $row->productcode,
		'productname' => $row->productname,
		'sellprice' => $row->sellprice,
		'consumerprice' => $row->consumerprice,
		'tinyimage' => $row->tinyimage,
		'minimage' => $row->minimage,
		'option1' => $row->option1,
		'option2' => $row->option2,
		'price' => $row->price,
		'reserve' => $row->reserve,
		'opt1_name' => $row->opt1_name,
		'opt2_name' => $row->opt2_name,
		'text_opt_subject' => $row->text_opt_subject,
		'text_opt_content' => $row->text_opt_content,
		'option_price_text' => $row->option_price_text,
		'option1_tf' => $row->option1_tf,
		'option2_tf' => $row->option2_tf,
		'option2_maxlen' => $row->option2_maxlen,
		'op_step' => $row->op_step,
		'tempkey' => $row->tempkey,
		'addcode' => $row->addcode,
		'quantity' => $row->quantity,
		'order_prmsg' => $row->order_prmsg,
		'selfcode' => $row->selfcode,
		'package_idx' => $row->package_idx,
		'assemble_idx' => $row->assemble_idx,
		'assemble_info' => $row->assemble_info,
		'option_type' => $row->option_type,
		'option_price' => $row->option_price,
		'option_quantity' => $row->option_quantity,
		'coupon_price' => $row->coupon_price,
		'deli_price' => $row->deli_price,
		'use_point' => $row->use_point,
		'deli_gbn' => $row->deli_gbn,
		'deli_com' => $row->deli_com,
		'deli_num' => $row->deli_num,
		'deli_date' => $row->deli_date,
		'receive_ok' => $row->receive_ok,
		'order_conf' => $row->order_conf,
		'redelivery_type' => $row->redelivery_type,
		'redelivery_date' => $row->redelivery_date,
		'redelivery_reason' => $row->redelivery_reason,
		'delivery_type' => $row->delivery_type,
		'store_code' => $row->store_code,
		'reservation_date' => $row->reservation_date, 
        'oc_no' => $row->oc_no,
		'use_epoint' => $row->use_epoint,
		'prodcode' => $row->prodcode,
		'colorcode' => $row->colorcode,
		'store_code' => $row->store_code,
		'deli_closed' => $row->deli_closed
	);
	if ($orvender[$row->brand]['t_pro_count'] == '') {
		$orvender[$row->brand]['t_pro_count']	= 1; // 브랜드별 상품수
		$orvender[$row->brand]['t_pro_price']	= ($row->price + $row->option_price) * $row->option_quantity; // 브랜드별 총 주문금액
		$orvender[$row->brand]['t_deli_price']	= $row->deli_price; // 브랜드별 총 배송비
	} else {
		$orvender[$row->brand]['t_pro_count']	= $orvender[$row->brand]['t_pro_count'] + 1; // 브랜드별 상품수
		$orvender[$row->brand]['t_pro_price']	= $orvender[$row->brand]['t_pro_price'] + (($row->price + $row->option_price) * $row->option_quantity); // 브랜드별 총 주문금액
		$orvender[$row->brand]['t_deli_price']	= $orvender[$row->brand]['t_deli_price'] + $row->deli_price; // 브랜드별 총 배송비
	}

    # 물류정보일 경우 ERP 정보를 가져와 UPDATE한다 2016-11-28 유동혁
    if( $row->op_step == '1' ){
        if( array_search( $row->store_code, $mStoreCode ) !== false ){
            $status = getErpWmsStatus( $ordercode, $row->idx ); // wms 상태값 가져오기
            if( $status == '2' ){
                $deliOpts = array(
                    'ordercode'     => $ordercode,      // 주문코드
                    'op_idx'        => $row->idx,      // 상세 idx
                    'step'          => $status,     // 주문 step
                    'sync_status'   => 'M'      // 물류 또는 싱크커머스에서 넘겼는지 체크해주는 값 M 물류 S 싱크커머스
                );
                $rtn = deliveryStatusUp( $deliOpts );
                if( $rtn == 1 ){
                    $reloadFlag = true;
                }
            }
        }
    }

}
pmysql_free_result($result);

# ERP 정보 UPDATE가 성공할경우 reload 2016-11-28 유동혁
if( $reloadFlag ){
    echo '<script>';
    echo '  location.reload();';
    echo '</script>';
    exit;
}

//상품할인
list($t_product_sale)=pmysql_fetch_array(pmysql_query("select SUM(tco.dc_price) dc_price from tblcoupon_order tco LEFT JOIN tblcouponinfo tci ON tco.coupon_code = tci.coupon_code WHERE tco.ordercode='{$ordercode}' AND tci.coupon_use_type !='1' GROUP BY ordercode"));

//쿠폰할인
list($t_coupon_sale)=pmysql_fetch_array(pmysql_query("select SUM(tco.dc_price) dc_price from tblcoupon_order tco LEFT JOIN tblcouponinfo tci ON tco.coupon_code = tci.coupon_code WHERE tco.ordercode='{$ordercode}' AND tci.coupon_use_type ='1' GROUP BY ordercode"));

//환불 계좌 정보
if(strlen($_ShopInfo->getMemid()) > 0) {
	list($refund_bankcode, $refund_bankaccount, $refund_bankuser, $refund_bankusertel)=pmysql_fetch_array(pmysql_query("select bank_code, account_num, depositor, home_tel from tblmember where id='".$_ShopInfo->getMemid()."'"));
}

# 배송업체를 불러온다.
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);

#배송지 관리 리스트
if(strlen($_ShopInfo->getMemid()) > 0) {
	$sql ="SELECT *, case when base_chk='Y' then '(기본)' else '' end as base_chk_text  FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."' ORDER BY NO DESC";
	$result=pmysql_query($sql,get_db_conn());
	$deliAddressList=array();
	while($row=pmysql_fetch_object($result)) {
		$deliAddressList[]=$row;
	}
	pmysql_free_result($result);
}


?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

$(document).ready(function(){
	// 매장 정보 레이어 오픈
	$(document).on("mouseover", ".CLS_store_layer_open", function(){
		//$(this).next().css("margin-left", "-"+ (parseInt($(this).prev().width(), 10)+25) + "px");
		if($(this).data('delivery_type') != '0'){
			// 상단 높이가 별도로 계산되어 180 추가 차감
			$(this).next().css({top: $(this).offset().top - 180, left: ($(this).offset().left - $(this).next().width() - 40)});
			$(this).next().show();
		}
	})
	$(document).on("mouseout", ".CLS_store_layer_open", function(){
		if($(this).data('delivery_type') != '0'){
			$(this).next().hide();
		}
	})

	//배송완료
	$(".deli_ok").click(function(){
		if(confirm('구매확정을 하시겠습니까?')){
			ordcode = $(this).attr('ordercode');
			idx = $(this).attr('idx');

			$.post("mypage_orderlist.ajax.php",{mode:"deli_ok",ordercode:ordcode,idx:idx},function(data){
				alert(data.msg);
				if(data.type == 1){ 
					window.location.reload();
				}
			},"json");
		}
		
	});

	//구매확정
	$(".ord_conf").click(function(){
		if(confirm('구매확정을 하시겠습니까?')){
			ordcode = $(this).attr('ordercode');
			idx = $(this).attr('idx');

			$.post("mypage_orderlist.ajax.php",{mode:"ord_conf",ordercode:ordcode,idx:idx},function(data){
				alert(data.msg);
				if(data.type == 1){ 
					window.location.reload();
				}
			},"json");
		}
		
	});

	//교환/반품신청 철회
	$(".ord_req_cancel").click(function(){
		if(confirm('신청철회를 하시겠습니까?')){
			ordcode = $(this).attr('ordercode');
			idx = $(this).attr('idx');
            oc_no = $(this).attr('oc_no');

			$.post("mypage_orderlist.ajax.php",{mode:"ord_req_cancel",ordercode:ordcode,idx:idx,oc_no:oc_no},function(data){
				alert(data.msg);
				if(data.type == 1){ 
					window.location.reload();
				}
			},"json");
		}
		
	});


	$(".CLS_delivery_tracking").click(function(){
		//window.open($(this).attr('urls'),"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=560,height=550");
		window.open( $(this).attr('urls'), "배송추적");
	});
	
	// 취소 - 주문접수상태일 경우
	$('.ord_receive_cancel').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var idxs				= $(this).attr("idxs");
		var paymethod	= $(this).attr("paymethod");
		// 구글연동 취소필수 값전달
		var price	= $(this).attr("price");
		var dcprice	= $(this).attr("dcprice");
		var deliprice	= $(this).attr("deliprice");
		var productname	= $(this).attr("productname");
		var productcode	= $(this).attr("productcode");
		var option	= $(this).attr("option");
		var quantity	= $(this).attr("quantity");

		if(confirm('취소를 하시겠습니까?')){
			//console.log('ordercode=['+ordercode+'],idxs=['+idxs+'],paymethod=['+paymethod+'],price=['+price+'],productname=['+productname+'],option=['+option+'],quantity=['+quantity+'],');
			$.post("mypage_orderlist.ajax.php",{mode:"receive_cancel",ordercode:ordercode,idxs:idxs,paymethod:paymethod},function(data){
				alert(data.msg);
				if(data.type == 1){

					// 취소시 구글 통계연동 -제품개수 , - 제품가격 등을 차감한다 (마이너스로 설정한다)
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
						
					ga('create', 'UA-98275559-1', 'auto');
					ga('send', 'pageview');
					
					ga('require', 'ecommerce', 'ecommerce.js');
					ga('ecommerce:addTransaction', { 
					  'id': ordercode, 								// 시스템에서 생성된 주문번호. 필수. 
					  'affiliation': paymethod, 					// 제휴사이름. 선택사항. 
					  'revenue': -(price - dcprice - deliprice), 	// 구매총액. 필수. 
					  'shipping': -deliprice, 						// 배송비. 선택사항. 
					  'tax': '0' 									// 세금. 선택사항.
					});
					ga('ecommerce:addItem', { 
					  'id': ordercode, 								// 시스템에서 생성된 주문번호. 필수. 
					  'name': productname, 							// 제품명. 필수. 
					  'sku': productcode, 							// SKU 또는 제품고유번호. 선택사항. 
					  'category': option, 							// 제품 분류. 
					  'price': -(price - dcprice - deliprice), 		// 제품 단가. 
					  'quantity': -quantity 						// 제품 수량.
					});
					ga('ecommerce:send');
					 
					window.location.reload();
				}
			},"json");
		}
	});
	// 취소(환불) 레이어 오픈시
	$('.ord_cancel').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pc_type		= $(this).attr("pc_type");
		if (pc_type == 'ALL') {
			var idxs				= $(this).attr("idxs");
		} else if (pc_type == 'PART') {
			var idxs				= $(this).attr("idx");
		}
		var pg_ordercode		= $(this).attr("pg_ordercode");

		var paymethod	= $(this).attr("paymethod");
		var idxs_arr		= idxs.split("|");
		var t_op_price				= 0;
		var t_op_dc_price		= 0;
		var t_op_deli_price		= 0;
		var t_op_total_price		= 0;

		$(".button_open").show();
		$(".button_close").hide();

        // wms 상태값 가져오기 추가..2016-11-29
        // 결제완료 상태에서 취소(환불) 요청시, 그 사이에 배송준비중으로 바뀌었을수 있으므로 체크.
		/*
        $.ajax({
            url : 'mypage_order_check.ajax.php',
            method : 'post',
            data : { ordercode : ordercode, idxs : idxs },
            dataType : 'json'
        }).done(function( data ){
            //alert(data.code);
            //alert(data.msg);
            if( data.code != '1' ){
                alert( data.msg );
                $('.popDelivery-return.refund').fadeOut();
                window.location.reload();
            }
        });
*/
		$("#rg_list > tbody").html("");
		$("#rg_list > tfoot").html("");
		$(".refund-way").removeClass('hide');
		$(".refund-way2").removeClass('hide');
		$(".refund-way2").addClass('hide');
		$(".account-info").removeClass('hide');
		
		// 초기화		
		$(".reason-reason-info").find("select[name=b_sel_code]").val("");
		$(".reason-reason-info").find("textarea[name=memo]").val("");
		$(".account-info").find("select[name=bankcode]").val($("input[name=refund_bankcode]").val());
		$(".account-info").find("input[name=bankaccount]").val($("input[name=refund_bankaccount]").val());
		$(".account-info").find("input[name=bankuser]").val($("input[name=refund_bankuser]").val());
		$(".account-info").find("input[name=bankusertel]").val($("input[name=refund_bankusertel]").val());

		if( paymethod == 'C' || paymethod == 'M' || paymethod == 'V' || paymethod == 'Y'){
            // 신용카드 결제, 휴대폰 결제, 계좌이체, PAYCO 결제인 경우
			//$(".account-info").addClass('hide');
			$(".account-info").find("select[name=bankcode]").attr('disabled','disabled');
			$(".account-info").find("input[name=bankaccount]").attr('disabled','disabled');
			$(".account-info").find("input[name=bankuser]").attr('disabled','disabled');
			$(".account-info").find("input[name=bankusertel]").attr('disabled','disabled');
		}
		//$(".re_addr").removeClass('hide');
		//$(".re_addr").addClass('hide');

		var appHtml	= "";
		for (var s=0;s < idxs_arr.length ; s++)
		{
			var idx				= idxs_arr[s];
			var info				= $("#idx_"+idx).attr("info");
			var info_arr			= info.split("!@#");

			var productcode			= info_arr[0]; // 상품코드
			var op_regdt				= info_arr[1]; // 주문일
			var op_img					= info_arr[2]; // 상품 이미지
			var op_brand				= info_arr[3]; // 상품 브랜드
			var op_name				= info_arr[4]; // 상품 이름
			var op_opt_qy				= info_arr[5]; // 옵션 / 수량
			var op_opt1				= info_arr[6]; // 옵션1
			var op_opt2				= info_arr[7]; // 옵션2
			var op_text_opt_s		= info_arr[8]; // 텍스트 옵션 옵션명
			var op_text_opt_c		= info_arr[9]; // 텍스트 옵션 옵션값
			var op_option_price_t	= info_arr[10]; // 옵션별 가격  구분자 ||
			var op_csprice			= info_arr[11]; // 정가
			var op_sellprice			= info_arr[12]; // 판매가
			if (pc_type == 'ALL') {
				var op_deli_price			= info_arr[13]; // 배송비
				var op_total_price		= info_arr[16]; // 총 주문금액
			} else if (pc_type == 'PART') {
				var op_deli_price			= info_arr[22]; // 배송비
				var op_total_price		= info_arr[23]; // 총 주문금액
			}
			var op_coupon_price	= info_arr[14]; // 쿠폰할인금액
			var op_use_point			= info_arr[15]; // 사용적립금
			var option_type			= info_arr[17]; // 옵션타입 (0 : 조합형 / 1 : 독립형 / 2 : 옵션없음)
			var re_addr					= info_arr[21]; // 반품주소
			var op_quantity			= info_arr[24]; // 수량
			var op_delivery_type_str		= info_arr[25]; // 배송 타입 문자열
			var op_delivery_type			= info_arr[26]; // 배송 타입
			var op_reservation_date			= info_arr[27]; // 예약일
			var op_store_name		= info_arr[28]; // 당일배송 주소
			var op_use_epoint		= info_arr[29]; // e포인트

			t_op_price			+=  parseInt(op_sellprice) * parseInt(op_quantity);
			t_op_dc_price		+=  parseInt(op_coupon_price) + parseInt(op_use_point) + parseInt(op_use_epoint);
			t_op_deli_price	+=  parseInt(op_deli_price);
			t_op_total_price	+=  parseInt(op_total_price);

			if (op_deli_price == 0)
				op_deli_price = "무료";
			else
				op_deli_price = comma(op_deli_price)+"원";

			var op_dc_price = parseInt(op_coupon_price) + parseInt(op_use_point) + parseInt(op_use_epoint);

			if (op_dc_price == 0)
				op_dc_price = "없음";
			else
				op_dc_price = comma(op_dc_price)+"원";


			appHtml	+= "<tr>\n";
			appHtml	+= "	<td class=\"txt-toneA\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"pl-5\">\n";
			appHtml	+= "	<div class=\"goods-in-td\">\n";
			appHtml	+= "	<div class=\"thumb-img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "		<div class=\"info\">\n";
			appHtml	+= "			<p class=\"brand-nm\">"+op_brand+"</p>\n";
			appHtml	+= "			<p class=\"goods-nm\">"+op_name+"</p>\n";
			appHtml	+= "			<p class=\"opt\">"+op_opt_qy+"</p>\n";
			appHtml	+= "		</div>\n";
			appHtml	+= "	</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"txt-toneB\">"+op_quantity+"</td>\n";
			appHtml	+= "	<td class=\"txt-toneA fw-bold\">\ "+comma(op_sellprice)+"</td>\n";		
			appHtml	+= "	<td class=\"flexible-delivery\">\ "+op_deli_price+"</td>\n";		
			appHtml	+= "	<td class=\"point-color\">\ "+op_dc_price+"</td>\n";		
			appHtml	+= "</tr>\n";
/*
			appHtml	+= "<tr class=\"bold\">\n";
			appHtml	+= "	<td class=\"date\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"goods_info\">\n";
			appHtml	+= "		<a href=\"productdetail.php?productcode="+productcode+"\">\n";
			appHtml	+= "			<img src=\""+op_img+"\" alt=\""+op_name+"\">\n";
			appHtml	+= "			<ul>\n";
			appHtml	+= "				<li>["+op_brand+"]</li>\n";
			appHtml	+= "				<li>"+op_name+"</li>\n";
			appHtml	+= "				<li>["+op_delivery_type_str+"] "+op_store_name+"</li>\n";
			appHtml	+= "				<li>"+op_opt_qy+"</li>\n";
			appHtml	+= "			</ul>\n";
			appHtml	+= "		</a>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td>"+op_quantity+"</td>\n";
			appHtml	+= "	<td>"+comma(op_sellprice)+"</td>\n";		
			appHtml	+= "	<td>"+op_deli_price+"</td>\n";
			appHtml	+= "	<td>"+op_dc_price+"</td>\n";
			appHtml	+= "	<td class=\"payment\">"+comma(op_total_price)+"원</td>\n";
			appHtml	+= "</tr>\n";
*/
		}

		$("#rg_list > tbody").append(appHtml);

		appHtml	= "";
		if(paymethod=="Q"){
		appHtml	+= "<input type=hidden name=re_type value=\"B\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\"\"><input type=hidden name=idxs value=\""+idxs+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=each_price value=\""+t_op_total_price+"\">";
		}else{
		appHtml	+= "<input type=hidden name=re_type value=\"\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\"\"><input type=hidden name=idxs value=\""+idxs+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=each_price value=\""+t_op_total_price+"\">";
		}
		appHtml	+= "<tr>\n";
		appHtml	+= "	<td colspan=\"6\" class=\"reset\">\n";
		appHtml	+= "		<div class=\"cart-total-price clear\">\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>상품합계</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<span class=\"txt point-color\">-</span>\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>할인</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_dc_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<span class=\"txt\">+</span>\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>배송비</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_deli_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<dl class=\"sum\">\n";
		appHtml	+= "				<dt>환불금액</dt>\n";
		appHtml	+= "				<dd class=\"point-color fz-18\">\ "+comma(t_op_total_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "		</div>\n";
		appHtml	+= "	</td>\n";
		appHtml	+= "</tr>\n";

		/*
		appHtml	= "";
		appHtml	+= "<input type=hidden name=re_type value=\"\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=idx value=\"\"><input type=hidden name=idxs value=\""+idxs+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=each_price value=\""+t_op_total_price+"\">";
		appHtml	+= "<span>총 결제금액</span>\n";
		appHtml	+= "<ul class=\"clear\">\n";
		appHtml	+= "	<li><div>결제금액 <em>"+comma(t_op_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div>쿠폰 <em>"+comma(t_op_dc_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div>배송비 <em>"+comma(t_op_deli_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div><p>환불금액</p> <em>"+comma(t_op_total_price)+"원</em></div></li>\n";
		appHtml	+= "</ul>\n";
*/	
		$("#rg_list > tfoot").html(appHtml);
//		$("#rg_total .refund-money").html(appHtml);
		$("#tr_return").hide();
		$("#tr_refund").show();
		if(paymethod!="Q"){
			$("#parcel_pay").hide();
		}
		$('.popDelivery-return.refund').fadeIn();

	});

	// 반품 레이어 오픈시
	$('.ord_regoods').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pg_ordercode		= $(this).attr("pg_ordercode");
		var idx				= $(this).attr("idx");
		var pc_type		= $(this).attr("pc_type");
		var paymethod	= $(this).attr("paymethod");
		var op_step		= $(this).attr("op_step");
		var info				= $("#idx_"+idx).attr("info");
		var info_arr			= info.split("!@#");

		var t_op_price				= 0;
		var t_op_dc_price		= 0;
		var t_op_deli_price		= 0;
		var t_op_total_price		= 0;
		
		// 초기화		
		$(".reason-reason-info").find("select[name=b_sel_code]").val("");
		$(".reason-reason-info").find("textarea[name=memo]").val("");
		$(".account-info").find("select[name=bankcode]").val($("input[name=refund_bankcode]").val());
		$(".account-info").find("input[name=bankaccount]").val($("input[name=refund_bankaccount]").val());
		$(".account-info").find("input[name=bankuser]").val($("input[name=refund_bankuser]").val());
		$(".account-info").find("input[name=bankusertel]").val($("input[name=refund_bankusertel]").val());

		$(".button_open").show();
		$(".button_close").hide();
		
		//$(".re_addr").removeClass('hide');
		//$(".re_addr").addClass('hide');
		
		var productcode			= info_arr[0]; // 상품코드
		var op_regdt				= info_arr[1]; // 주문일
		var op_img					= info_arr[2]; // 상품 이미지
		var op_brand				= info_arr[3]; // 상품 브랜드
		var op_name				= info_arr[4]; // 상품 이름
		var op_opt_qy				= info_arr[5]; // 옵션 / 수량
		var op_opt1				= info_arr[6]; // 옵션1
		var op_opt2				= info_arr[7]; // 옵션2
		var op_text_opt_s		= info_arr[8]; // 텍스트 옵션 옵션명
		var op_text_opt_c		= info_arr[9]; // 텍스트 옵션 옵션값
		var op_option_price_t	= info_arr[10]; // 옵션별 가격  구분자 ||
		var op_csprice			= info_arr[11]; // 정가
		var op_sellprice			= info_arr[12]; // 판매가
		var op_deli_price			= info_arr[22]; // 배송비
		var op_coupon_price	= info_arr[14]; // 쿠폰할인금액
		var op_use_point			= info_arr[15]; // 사용적립금
		var op_total_price		= info_arr[23]; // 총 주문금액
		var option_type			= info_arr[17]; // 옵션타입 (0 : 조합형 / 1 : 독립형 / 2 : 옵션없음)
		var re_addr					= info_arr[21]; // 반품주소
		var op_quantity			= info_arr[24]; // 수량
		var op_delivery_type_str		= info_arr[25]; // 배송 타입 문자열
		var op_delivery_type			= info_arr[26]; // 배송 타입
		var op_reservation_date			= info_arr[27]; // 예약일
		var op_store_name		= info_arr[28]; // 당일배송 주소
		var op_use_epoint		= info_arr[29]; // e포인트

		/*if (re_addr)
		{
			$(".re_addr .re_addr_in").html(re_addr);
			$(".re_addr").removeClass('hide');
		}*/

		t_op_price			+=  parseInt(op_sellprice) * parseInt(op_quantity);
		t_op_dc_price		+=  parseInt(op_coupon_price) + parseInt(op_use_point) + parseInt(op_use_epoint);
		t_op_deli_price	+=  parseInt(op_deli_price);
		t_op_total_price	+=  parseInt(op_total_price);

		if (op_deli_price == 0)
			op_deli_price = "무료";
		else
			op_deli_price = comma(op_deli_price)+"원";

		var op_dc_price = parseInt(op_coupon_price) + parseInt(op_use_point);

		if (op_dc_price == 0)
			op_dc_price = "없음";
		else
			op_dc_price = comma(op_dc_price)+"원";
		
		$("#rg_list > tbody").html("");
		$("#rg_list > tfoot").html("");
		
		var appHtml	= "";
		appHtml	+= "<tr>\n";
		appHtml	+= "	<td class=\"txt-toneA\">"+op_regdt+"</td>\n";
		appHtml	+= "	<td class=\"pl-5\">\n";
		appHtml	+= "	<div class=\"goods-in-td\">\n";
		appHtml	+= "	<div class=\"thumb-img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
		appHtml	+= "		<div class=\"info\">\n";
		appHtml	+= "			<p class=\"brand-nm\">"+op_brand+"</p>\n";
		appHtml	+= "			<p class=\"goods-nm\">"+op_name+"</p>\n";
		appHtml	+= "			<p class=\"opt\">"+op_opt_qy+"</p>\n";
		appHtml	+= "		</div>\n";
		appHtml	+= "	</div>\n";
		appHtml	+= "	</td>\n";
		appHtml	+= "	<td class=\"txt-toneB\">"+op_quantity+"</td>\n";
		appHtml	+= "	<td class=\"txt-toneA fw-bold\">\ "+comma(op_sellprice)+"</td>\n";		
		appHtml	+= "	<td class=\"flexible-delivery\">\ "+op_deli_price+"</td>\n";		
		appHtml	+= "	<td class=\"point-color\">\ "+op_dc_price+"</td>\n";		
		appHtml	+= "</tr>\n";
/*
		var appHtml	= "";
		appHtml	+= "<tr class=\"bold\">\n";
		appHtml	+= "	<td class=\"date\">"+op_regdt+"</td>\n";
		appHtml	+= "	<td class=\"goods_info\">\n";
		appHtml	+= "		<a href=\"productdetail.php?productcode="+productcode+"\">\n";
		appHtml	+= "			<img src=\""+op_img+"\" alt=\""+op_name+"\">\n";
		appHtml	+= "			<ul>\n";
		appHtml	+= "				<li>["+op_brand+"]</li>\n";
		appHtml	+= "				<li>"+op_name+"</li>\n";
		appHtml	+= "				<li>["+op_delivery_type_str+"] "+op_store_name+"</li>\n";
		appHtml	+= "				<li>"+op_opt_qy+"</li>\n";
		appHtml	+= "			</ul>\n";
		appHtml	+= "		</a>\n";
		appHtml	+= "	</td>\n";
		appHtml	+= "	<td>"+op_quantity+"</td>\n";
		appHtml	+= "	<td>"+comma(op_sellprice)+"</td>\n";		
		appHtml	+= "	<td>"+op_deli_price+"</td>\n";
		appHtml	+= "	<td>"+op_dc_price+"</td>\n";
		appHtml	+= "	<td class=\"payment\">"+comma(op_total_price)+"원</td>\n";
		appHtml	+= "</tr>\n";
*/
		$("#rg_list > tbody").append(appHtml);
		
		appHtml	= "";
		appHtml	+= "<input type=hidden name=re_type value=\"B\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\""+idx+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=each_price value=\""+op_total_price+"\">";
		appHtml	+= "<tr>\n";
		appHtml	+= "	<td colspan=\"6\" class=\"reset\">\n";
		appHtml	+= "		<div class=\"cart-total-price clear\">\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>상품합계</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<span class=\"txt point-color\">-</span>\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>할인</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_dc_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<span class=\"txt\">+</span>\n";
		appHtml	+= "			<dl>\n";
		appHtml	+= "				<dt>배송비</dt>\n";
		appHtml	+= "				<dd>\ "+comma(t_op_deli_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "			<dl class=\"sum\">\n";
		appHtml	+= "				<dt>환불금액</dt>\n";
		appHtml	+= "				<dd class=\"point-color fz-18\">\ "+comma(t_op_total_price)+"</dd>\n";
		appHtml	+= "			</dl>\n";
		appHtml	+= "		</div>\n";
		appHtml	+= "	</td>\n";
		appHtml	+= "</tr>\n";
		/*
		appHtml	+= "<span>총 결제금액</span>\n";
		appHtml	+= "<ul class=\"clear\">\n";
		appHtml	+= "	<li><div>결제금액 <em>"+comma(t_op_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div>쿠폰 <em>"+comma(t_op_dc_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div>배송비 <em>"+comma(t_op_deli_price)+"원</em></div></li>\n";
		appHtml	+= "	<li><div><p>환불금액</p> <em>"+comma(t_op_total_price)+"원</em></div></li>\n";
		appHtml	+= "</ul>\n";
*/
		$("#rg_list > tfoot").html(appHtml);
		$("#tr_refund").hide();
		$("#tr_return").show();
		$("#parcel_pay").show();
		$('.popDelivery-return.refund').fadeIn();
//		$('.layer-refund.regoods').fadeIn();
	});

	// 교환 레이어 오픈시
	$('.ord_change').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pg_ordercode		= $(this).attr("pg_ordercode");
		var idx				= $(this).attr("idx");
		var pc_type		= $(this).attr("pc_type");
		var paymethod	= $(this).attr("paymethod");
		var info				= $("#idx_"+idx).attr("info");
		var info_arr			= info.split("!@#");

		var t_op_price				= 0;
		var t_op_dc_price		= 0;
		var t_op_deli_price		= 0;
		var t_op_total_price		= 0;

		// 초기화		
		$(".exchange-reason-info").find("select[name=c_sel_code]").val("");
		$(".exchange-reason-info").find("textarea[name=memo]").val("");

		$(".button_open").show();
		$(".button_close").hide();

		//$(".re_addr").removeClass('hide');
		//$(".re_addr").addClass('hide');
		
		var productcode			= info_arr[0]; // 상품코드
		var op_regdt				= info_arr[1]; // 주문일
		var op_img					= info_arr[2]; // 상품 이미지
		var op_brand				= info_arr[3]; // 상품 브랜드
		var op_name				= info_arr[4]; // 상품 이름
		var op_opt_qy				= info_arr[5]; // 옵션 / 수량
		var op_opt1				= info_arr[6]; // 옵션1
		var op_opt2				= info_arr[7]; // 옵션2
		var op_text_opt_s		= info_arr[8]; // 텍스트 옵션 옵션명
		var op_text_opt_c		= info_arr[9]; // 텍스트 옵션 옵션값
		var op_option_price_t	= info_arr[10]; // 옵션별 가격  구분자 ||
		var op_csprice			= info_arr[11]; // 정가
		var op_sellprice			= info_arr[12]; // 판매가
		var op_deli_price			= info_arr[22]; // 배송비
		var op_coupon_price	= info_arr[14]; // 쿠폰할인금액
		var op_use_point			= info_arr[15]; // 사용적립금
		var op_total_price		= info_arr[23]; // 총 주문금액
		var op_option_type		= info_arr[17]; // 옵션타입 (0 : 조합형 / 1 : 독립형 / 2 : 옵션없음)
		var op_option1_tf			= info_arr[18]; // 옵션1 필수여부(T:필수,F:선택)
		var op_option2_tf			= info_arr[19]; // 추가문구옵션필수여부(T:필수,F:선택)
		var op_option2_maxlen= info_arr[20]; // 추가옵션문구_글자수제한
		var op_quantity			= info_arr[24]; // 수량
		var re_addr					= info_arr[21]; // 반품주소
		var op_delivery_type_str		= info_arr[25]; // 배송 타입 문자열
		var op_delivery_type			= info_arr[26]; // 배송 타입
		var op_reservation_date			= info_arr[27]; // 예약일
		var op_store_name		= info_arr[28]; // 당일배송 주소
		var op_use_epoint		= info_arr[29]; // e포인트

		/*if (re_addr)
		{
			$(".re_addr .re_addr_in").html(re_addr);
			$(".re_addr").removeClass('hide');
		}*/

		t_op_price			+=  parseInt(op_sellprice) * parseInt(op_quantity);
		t_op_dc_price		+=  parseInt(op_coupon_price) + parseInt(op_use_point) + parseInt(op_use_epoint);
		t_op_deli_price	+=  parseInt(op_deli_price);
		t_op_total_price	+=  parseInt(op_total_price);

		var appHtml	= "";
		var sel_option		="";

		$("#cg_list > tbody").html("<input type=hidden name=re_type value=\"C\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\""+idx+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=option1 value=\""+op_opt1+"\"><input type=hidden name=text_opt_s value=\""+op_text_opt_s+"\">");
		if (op_option_type !='2' && (op_opt1 != '' || op_text_opt_s != ''))
		{
			var op_opt1_arr	= "";
			var op_opt1_total	= "";
			var op_opt2_arr	= "";
			var op_option_price_t_arr	= "";
			var op_text_opt_s_arr	= "";
			var op_text_opt_s_total	= "";
			var op_text_opt_c_arr	= "";
			var sel_est			= "";
			var sel_est_text	= "";
			var op_option1_tf_arr	= "";
			var op_option2_tf_arr	= "";

			if (op_opt1 != "")
			{

				op_opt1_arr	= op_opt1.split("@#");
				op_opt1_total	= op_opt1_arr.length;
				op_opt2_arr	= op_opt1.split(chr(30));
				op_option_price_t_arr	= op_option_price_t.split("||");
				op_option1_tf_arr	= op_option1_tf.split("@#");

				for(var s=0;s < op_opt1_total;s++) {
					
					sel_est			= "essential";
					sel_est_text	= " *필수";
					if (op_option_type == '1' && op_option1_tf_arr[s] == 'F') {// 독립형 옵션이 필수가 아닐경우
						$sel_est			= "";
						$sel_est_text	= "";
					}
					var on_change	= "";
					if (op_option_type == '0' && ((s+1) != op_opt1_total)) {
						on_change	= " onChange=\"javascript:option_change(this, '"+productcode+"', '"+(s+1)+"', '"+op_opt1_total+"', this.value);\"";
					}
					sel_option += "	<div class=\"select opt"+s+"\">\n";
					sel_option += "		<select class=\"sel_option\" name=\"sel_option"+s+"\" est=\""+sel_est+"\""+on_change+">\n";
					sel_option += "		<option value=\"\">"+op_opt1_arr[s]+"</option>\n";
					sel_option += "		</select>\n";
					sel_option += "	</div>\n";
					/*
					sel_option += "	<div class=\"opt"+s+"\" style=\"z-index:"+(30-s)+"\">\n";
					sel_option += "		<span>"+op_opt1_arr[s]+"</span>\n";
					sel_option += "		<select class=\"sel_option\" name=\"sel_option"+s+"\" est=\""+sel_est+"\""+on_change+">\n";
					sel_option += "		<option value=\"\">===선택===</option>\n";
					sel_option += "		</select>"+sel_est_text+"\n";
					sel_option += "	</div>\n";*/
				}
			}
			
			if (op_text_opt_s != "")
			{
				op_text_opt_s_arr	= op_text_opt_s.split("@#");
				op_text_opt_s_total	= op_text_opt_s_arr.length;
				op_text_opt_c_arr	= op_text_opt_c.split("@#");
				op_option2_tf_arr	= op_option2_tf.split("@#");

				for(var s=0;s < op_text_opt_s_total;s++) {
					sel_est			= "essential";
					sel_est_text	= " *필수";
					if (op_option2_tf_arr[s] == 'F') {// 독립형 옵션이 필수가 아닐경우
						sel_est			= "";
						sel_est_text	= "";
					}
					sel_option += "	<div>\n";
					//sel_option += "		<span>"+op_text_opt_s_arr[s]+"</span>\n";
					sel_option += "		<input type=\"text\" class=\"opt_text\" name=\"opt_text"+s+"\" est=\""+sel_est+"\" class=\"input-def w200\">\n";
					sel_option += "	</div>\n";
				}
			}

			appHtml	+= "<tr>\n";
			appHtml	+= "	<td class=\"txt-toneA\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"pl-5\">\n";
			appHtml	+= "		<div class=\"goods-in-td\">\n";
			appHtml	+= "			<div class=\"thumb-img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "			<div class=\"info\">\n";
			appHtml	+= "				<p class=\"brand-nm\">"+op_brand+"</p>\n";
			appHtml	+= "				<p class=\"goods-nm\">"+op_name+"</p>\n";
//			appHtml	+= "				<li>["+op_delivery_type_str+"] "+op_store_name+"</li>\n";
			appHtml	+= "				<p class=\"opt\">"+op_opt_qy+"</p>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "		</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"txt-toneB\">"+op_quantity+"</td>\n";
			appHtml	+= "	<td>\n";
			appHtml	+= "		<div class=\"select-multi\">"+sel_option+"</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"txt-toneA fw-bold\">\ "+comma(op_total_price)+"원</td>\n";
			appHtml	+= "</tr>\n";

/*
			appHtml	+= "<tr class=\"bold\">\n";
			appHtml	+= "	<td class=\"date\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"goods_info\">\n";
			appHtml	+= "		<a href=\"productdetail.php?productcode="+productcode+"\">\n";
			appHtml	+= "			<img src=\""+op_img+"\" alt=\""+op_name+"\">\n";
			appHtml	+= "			<ul>\n";
			appHtml	+= "				<li>["+op_brand+"]</li>\n";
			appHtml	+= "				<li>"+op_name+"</li>\n";
			appHtml	+= "				<li>["+op_delivery_type_str+"] "+op_store_name+"</li>\n";
			appHtml	+= "				<li>"+op_opt_qy+"</li>\n";
			appHtml	+= "			</ul>\n";
			appHtml	+= "		</a>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"ta-l opt-change date\">"+sel_option+"</td>\n";
			appHtml	+= "	<td class=\"payment\">"+comma(op_total_price)+"원</td>\n";
			appHtml	+= "</tr>\n";
*/
			$("#cg_list > tbody").append(appHtml);

			if (op_opt1 != "")
			{
				$.ajax({
					type: "POST",
					url: "ajax_product_option.php",
					data: "productcode="+productcode+"&option_type="+op_option_type,
					dataType:"JSON",
					success: function(data){
						var sel_option		="";
						var soldout			="";
						var option_price	="";
						var disabled_on	= "";
						if (data)
						{
							if (op_option_type == '0')
							{
								$.each(data, function(){
									if (this.price > 0) {
										var opt_price = this.price;
										var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
										opt_price += '';  // 숫자를 문자열로 변환
										while (reg.test(opt_price)){
											opt_price = opt_price.replace(reg, '$1' + ',' + '$2');
										}
										option_price		= "(+"+opt_price+"원)";
									} else {
										option_price		= "";
									}
									if (this.soldout == 1)
									{
										disabled_on = ' disabled';
										soldout = '&nbsp;[품절]';
									} else {
										disabled_on = '';
										soldout = '';
									}

									sel_option += "				<option value='"+this.code+"' opt='"+this.code+"' opt_name=\""+this.code+option_price+soldout+"\" opt_p='"+this.price+"' "+disabled_on+">"+this.code+option_price+soldout+"</option>\n";
								});

								$("#cg_list > tbody").find("select[name=sel_option0]").append(sel_option);
							} else if (op_option_type == '1') {
								for(var s=0;s < op_opt1_total;s++) {
								//for(var s=0;s < 1;s++) {
									$.each(data[op_opt1_arr[s]], function(code, n){
										sel_option		="";
										//alert (code+"/"+n.option_price);

										if (n.option_price > 0) {
											var opt_price = n.option_price;
											var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
											opt_price += '';  // 숫자를 문자열로 변환
											while (reg.test(opt_price)){
												opt_price = opt_price.replace(reg, '$1' + ',' + '$2');
											}
											option_price		= "(+"+opt_price+"원)";
										} else {
											option_price		= "";
										}

										sel_option += "				<option value='"+code+"' opt='"+code+"' opt_name=\""+code+option_price+"\" opt_p='"+n.option_price+"' "+disabled_on+">"+code+option_price+"</option>\n";
										$("#cg_list > tbody").find("ul.sel_option"+s+"_list").append(sel_option);
									});
								}
							}
						}						
					},
					complete: function(data){
					},
					error:function(xhr, status , error){
						alert("에러발생");
					}
				});
			}
			appHtml	="";
			appHtml	+= "<tr>\n";
			appHtml	+= "	<td colspan=\"6\" class=\"reset\">\n";
			appHtml	+= "		<div class=\"cart-total-price clear\">\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>상품합계</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<span class=\"txt point-color\">-</span>\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>할인</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_dc_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<span class=\"txt\">+</span>\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>배송비</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_deli_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<dl class=\"sum\">\n";
			appHtml	+= "				<dt>합계</dt>\n";
			appHtml	+= "				<dd class=\"point-color fz-18\">\ "+comma(t_op_total_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "		</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "</tr>\n";

			$("#cg_list > tfoot").html(appHtml);

			$("#exchange-call-type").val("3").prop("selected", true);
			$('.popDelivery-return.exchange').fadeIn();
		} else {
			
			appHtml	+= "<tr>\n";
			appHtml	+= "	<td class=\"txt-toneA\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"pl-5\">\n";
			appHtml	+= "		<div class=\"thumb-img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "			<div class=\"info\">\n";
			appHtml	+= "				<p class=\"brand-nm\">"+op_brand+"</p>\n";
			appHtml	+= "				<p class=\"goods-nm\">"+op_name+"</p>\n";
//			appHtml	+= "				<li>["+op_delivery_type_str+"] "+op_store_name+"</li>\n";
			appHtml	+= "				<p class=\"opt\">"+op_opt_qy+"</p>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "		</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"txt-toneB\">"+op_quantity+"</td>\n";
			appHtml	+= "	<td>\n";
			appHtml	+= "		<div class=\"select-multi\">-</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"txt-toneA fw-bold\">\ "+comma(op_total_price)+"원</td>\n";
			appHtml	+= "</tr>\n";
/*
			appHtml	+= "<tr class=\"bold\">\n";
			appHtml	+= "	<td class=\"date\">"+op_regdt+"</td>\n";
			appHtml	+= "	<td class=\"goods_info\">\n";
			appHtml	+= "		<a href=\"productdetail.php?productcode="+productcode+"\">\n";
			appHtml	+= "			<img src=\""+op_img+"\" alt=\""+op_name+"\">\n";
			appHtml	+= "			<ul>\n";
			appHtml	+= "				<li>["+op_brand+"]</li>\n";
			appHtml	+= "				<li>"+op_name+"</li>\n";
			appHtml	+= "				<li>"+op_opt_qy+"</li>\n";
			appHtml	+= "			</ul>\n";
			appHtml	+= "		</a>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "	<td class=\"ta-l\">-</td>\n";
			appHtml	+= "	<td class=\"payment\">"+comma(op_total_price)+"원</td>\n";
			appHtml	+= "</tr>\n";
*/
			
			$("#cg_list > tbody").append(appHtml);

			appHtml	="";
			appHtml	+= "<tr>\n";
			appHtml	+= "	<td colspan=\"6\" class=\"reset\">\n";
			appHtml	+= "		<div class=\"cart-total-price clear\">\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>상품합계</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<span class=\"txt point-color\">-</span>\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>할인</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_dc_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<span class=\"txt\">+</span>\n";
			appHtml	+= "			<dl>\n";
			appHtml	+= "				<dt>배송비</dt>\n";
			appHtml	+= "				<dd>\ "+comma(t_op_deli_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "			<dl class=\"sum\">\n";
			appHtml	+= "				<dt>합계</dt>\n";
			appHtml	+= "				<dd class=\"point-color fz-18\">\ "+comma(t_op_total_price)+"</dd>\n";
			appHtml	+= "			</dl>\n";
			appHtml	+= "		</div>\n";
			appHtml	+= "	</td>\n";
			appHtml	+= "</tr>\n";

			$("#cg_list > tfoot").html(appHtml);

			$("#exchange-call-type").val("3").prop("selected", true);
			$('.popDelivery-return.exchange').fadeIn();
		}		
	});

	// 취소/반품/교환 요청시
	$('.refundSubmit').click(function(){
		
		var re_type			= $(this).parents(".layer-inner").find('input[name=re_type]').val();
		var ordercode		= $(this).parents(".layer-inner").find('input[name=ordercode]').val();
		var pg_ordercode		= $(this).parents(".layer-inner").find('input[name=pg_ordercode]').val();
		var idx				= $(this).parents(".layer-inner").find('input[name=idx]').val();
		var pc_type		= $(this).parents(".layer-inner").find('input[name=pc_type]').val();
		var paymethod	= $(this).parents(".layer-inner").find('input[name=paymethod]').val();

		//배송지 정보
		var receipt_name = $("#td_receiver_name").text();
		var receipt_tel = $("#receiver_tel1").val();
		var receipt_mobile = $("#td_receiver_tel2").text();
		var receipt_addr = $("#td_receiver_addr").text();
		
		if (re_type == '') {
		var alert_text		= "취소";
		} else if (re_type == 'B') {// 반품
			alert_text		= "반품접수";
		} else if (re_type == 'C') {//교환
			alert_text		= "교환접수";
		}
		if (re_type == '' || re_type == 'B') { // 취소, 반품일 경우
			var idxs				= $(this).parents(".layer-inner").find('input[name=idxs]').val();
			var each_price	= $(this).parents(".layer-inner").find('input[name=each_price]').val();
			if($(this).parents(".layer-inner").find('select[name=b_sel_code]').val() == ""){
				var sel_code		= $(this).parents(".layer-inner").find('select[name=b_sel_code2]').val();
			}else{
				var sel_code		= $(this).parents(".layer-inner").find('select[name=b_sel_code]').val();
			}		
			var memo			= $(this).parents(".layer-inner").find('textarea[name=memo]').val();
			var bankcode		= $(this).parents(".layer-inner").find('select[name=bankcode]').val();
			var bankuser		= $(this).parents(".layer-inner").find('input[name=bankuser]').val();
			var bankaccount	= $(this).parents(".layer-inner").find('input[name=bankaccount]').val();
			var bankusertel	= $(this).parents(".layer-inner").find('input[name=bankusertel]').val();

			var sel_sub_code			= "";
			var return_deli_price		= 0;
			var return_deli_receipt		= "";
			var return_deli_type			= "";
			var return_deli_memo		= "";

			$("input[name=b_sel_sub_code]:checked").each(function(index){
				if(sel_sub_code == '')
					sel_sub_code = $(this).val();
				else
					sel_sub_code += "|" + $(this).val();
			});

			return_deli_price				= $('input[name=return_deli_price]').val();
			if(return_deli_price == '') return_deli_price = 0;
			return_deli_receipt			= $('input[name=return_deli_receipt]').val();
			return_deli_type				= $('input[name=return_deli_type]:checked').val();
			return_deli_memo			= $('#return_deli_memo').val();

			if(sel_code == 0 || sel_code == ''){
				alert("사유를 선택해 주세요.");
				$(this).parents(".layer-inner").find('select[name=b_sel_code]').focus();
				return;
			}

			if(memo == ''){
				alert("상세사유를 입력해 주세요.");
				$(this).parents(".layer-inner").find('textarea[name=memo]').focus();
				return;
			}

			if(re_type == 'B'){
				if(typeof return_deli_type == "undefined"){
					alert("택배비 발송 종류를 선택해 주세요.");
					 $('input[name=return_deli_type]').focus();
					return
				}
			}	
			//if ((re_type == '' && paymethod != 'C') || re_type == 'B' ) // 반품시 결제방식이 카드가 아닌경우
			if (paymethod != 'C' && paymethod != 'M' && paymethod != 'V' && paymethod != 'Y' && paymethod != 'G') // 반품시 결제방식이 카드, 핸드폰, 계좌이체, PAYCO결제, 임직원 포인트가 아닌경우
			{
				if(bankcode==0 || bankcode=='') {
					alert("환불받으실 은행을 선택해 주세요.");
					$(this).parents(".layer-inner").find('select[name=bankcode]').focus();
					return;
				}
				
				if(bankaccount=='') {
					alert("환불받으실 계좌번호를 입력해 주세요.");
					$(this).parents(".layer-inner").find('input[name=bankaccount]').focus();
					return;
				}
				
				if(bankuser=='') {
					alert("환불받으실 예금주를 입력해 주세요.");
					$(this).parents(".layer-inner").find('input[name=bankuser]').focus();
					return;
				}

				if(bankusertel=='') {
					alert("연락처를 입력해 주세요.");
					$(this).parents(".layer-inner").find('input[name=bankusertel]').focus();
					return;
				}
			}
				

			var sel_option1				= "";
			var sel_option2				= "";
			var sel_option_price_text	= "";
			var sel_text_opt_s			= "";
			var sel_text_opt_c			= "";

		} else if (re_type == 'C') { // 교환일 경우
			var sel_code		= $(this).parents(".layer-inner").find('select[name=c_sel_code]').val();
			var memo			= $(this).parents(".layer-inner").find('textarea[name=memo]').val();
			var bankcode		= 0;
			var bankuser				= "";
			var bankaccount			= "";
			var bankusertel			= "";
			var sel_sub_code			= "";
			var return_deli_price		= 0;
			var return_deli_receipt		= "";
			var return_deli_type			= "";
			var return_deli_memo		= "";

			$("input[name=b_sel_sub_code]:checked").each(function(index){
				if(sel_sub_code == '')
					sel_sub_code = $(this).val();
				else
					sel_sub_code += "|" + $(this).val();
			});

			return_deli_price				= $('input[name=return_deli_price]').val();
			if(return_deli_price == '') return_deli_price = 0;
			return_deli_receipt			= $('input[name=return_deli_receipt]').val();
			return_deli_type				= $('input[name=return_deli_type]:checked').val();
			return_deli_memo			= $('#return_deli_memo2').val();

			var sel_option1		= $(this).parents(".layer-inner").find('input[name=option1]').val();
			var sel_option2		= "";
			var sel_option_price_text		= "";
			var sel_text_opt_s		= $(this).parents(".layer-inner").find('input[name=text_opt_s]').val();
			var sel_text_opt_c		= "";
			var sel_option_chk	= "Y";
			if ($(this).parents(".layer-inner").find('.sel_option').length > 0) {
				$(this).parents(".layer-inner").find('.sel_option').each(function(index) {
					var sel_option_val	= $(this).val();
					var sel_option_est	= $(this).attr('est');

					if (sel_option2 == '') {
						sel_option2 +=  sel_option_val;
						sel_option_price_text +=  $(this).find("option[value='"+sel_option_val+"']").attr("opt_p");
					} else {
						sel_option2 +=  chr(30)+sel_option_val;
						sel_option_price_text +=  "||"+$(this).find("option[value='"+sel_option_val+"']").attr("opt_p");
					}
					
					if (sel_option_est == "essential" && sel_option_val == "") { // 필수일 경우
						sel_option_chk	= "N";
					}

				});
			}

			if ($(this).parents(".layer-inner").find('.opt_text').length > 0) {
				var sel_chk_cnt			= 0;
				$(this).parents(".layer-inner").find('.opt_text').each(function(index) {
					var sel_option_est		= $(this).attr('est');
					if ($(this).val() != '') {
						if (sel_text_opt_c == '') {
							sel_text_opt_c += $(this).val();
						} else {
							sel_text_opt_c += "@#"+$(this).val();
						}
						sel_chk_cnt++;
					}
				
					if (sel_option_est == "essential" && sel_chk_cnt == 0) { // 필수일 경우
						sel_option_chk	= "N";
					}
				});
			}
			if (sel_option_chk == "N") {
				alert("옵션을 선택해 주세요.");
				return;
			}

			if(sel_code == 0 || sel_code == ''){
				alert("사유를 선택해 주세요.");
				$(this).parents(".layer-inner").find('select[name=c_sel_code]').focus();
				return;
			}

			if(memo == ''){
				alert("상세사유를 입력해 주세요.");
				$(this).parents(".layer-inner").find('textarea[name=memo]').focus();
				return;
			}
/*
			if(typeof return_deli_type == "undefined"){
				alert("택배비 발송 종류를 선택해 주세요.");
				 $('input[name=return_deli_type]').focus();
				return
			}*/
		}
		//alert(re_type+"\n"+ordercode+"\n"+idx+"\n"+paymethod+"\n"+sel_code+"\n"+memo+"\n"+bankcode+"\n"+bankaccount+"\n"+bankuser+"\n"+bankusertel+"\n"+sel_option1+"\n"+sel_option2+"\n"+sel_option_price_text+"\n"+sel_text_opt_s+"\n"+sel_text_opt_c);return;
		

		if (re_type == '' && (paymethod =='C' || paymethod =='M' || paymethod =='V' || paymethod =='G')) { // 카드, 휴대폰, 계좌이체, 임직원 적립금 결제일 경우
			bankcode					= '0';
			bankaccount				= '';
			bankuser					= '';
			bankusertel				= '';
			sel_option1					= '';
			sel_option2					= '';
			sel_option_price_text	= '';
			sel_text_opt_s				= '';
			sel_text_opt_c				= '';
		}

        /* 취소(환불) 요청시..*/ 
			/*
        var reload = 0;
        if(re_type == '') {
            //alert("re_type = "+re_type);
            //alert("idxs = "+idxs);

            // wms 상태값 가져오기 추가..2016-11-29
            // 결제완료 상태에서 취소(환불) 요청시, 그 사이에 배송준비중으로 바뀌었을수 있으므로 체크.
            $.ajax({
                url : 'mypage_order_check.ajax.php',
                method : 'post',
                async : false,
                data : { ordercode : ordercode, idxs : idxs },
                dataType : 'json'
            }).done(function( data ){
                //alert(data.code);
                //alert(data.msg);
                if( data.code != '1' ){
                    reload = 1;
                    alert( data.msg );
                    $('.layer-refund').fadeOut();
                    window.location.reload();
                }
            });
        }
        if(reload) return;
		*/
        /**/

        if(confirm(alert_text+'를 하시겠습니까?')){
            if (re_type == '' && (paymethod =='C' || paymethod =='V' || paymethod =='M')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
				$.ajax({
					url:"mypage_orderlist.ajax.php",
					type:'POST',
					data:{mode:'cancel_check', ordercode:ordercode, idx:idx, idxs:idxs},
					dataType: "json",
					async:false,
					cache:false,
					//contentType:false,
					//processData:false,
					success: function(data){
						if(data.type == 1){ 
							<?php if($pg_type=="A"){?>
							var sitecd = '<?=$pgid_info["ID"]?>';
							var sitekey = '<?=$pgid_info["KEY"]?>';
							var sitepw = "<?=$pgid_info['PW']?>";	
							$(".button_open").hide();
							$(".button_close").show();
							$.post("<?=$Dir?>paygate/<?=$pg_type?>/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, sitepw:sitepw, ordercode:pg_ordercode, real_ordercode:ordercode, pc_type:pc_type,mod_mny:each_price},function(data){
								if(data.res_code !='N'){
									$.post("mypage_orderlist.ajax.php",{
										mode:"redelivery",
										re_type:re_type,
										ordercode:ordercode,
										idx:idx,
										idxs:idxs,
										paymethod:paymethod,
										sel_code:sel_code,
										sel_sub_code:sel_sub_code,
										memo:memo,
										bankcode:bankcode,
										bankaccount:bankaccount,
										bankuser:bankuser,
										bankusertel:bankusertel,
										opt1_changes:sel_option1,
										opt2_changes:sel_option2,
										opt2_pt_changes:sel_option_price_text,
										opt_text_s_changes:sel_text_opt_s,
										opt_text_c_changes:sel_text_opt_c,
										pgcancel_type:data.type,
										pgcancel_res_code:data.res_code,
										pgcancel_res_msg:data.res_msg,
										receipt_name:receipt_name,
										receipt_tel:receipt_tel,
										receipt_mobile:receipt_mobile,
										receipt_addr:receipt_addr,
										return_deli_price:return_deli_price,
										return_deli_receipt:return_deli_receipt,
										return_deli_type:return_deli_type,
										return_deli_memo:return_deli_memo
									},function(data){
										alert(data.msg);
										if(data.type == 1){ 
											window.location.reload();
										}
									},"json");
								} else {
									alert(data.msg);
									$(".button_open").show();
									$(".button_close").hide();
								}
							},"json");
							<?}?>
						} else {
							alert(data.msg);
						}
					}
				});
            } else {
				$(".button_open").hide();
				$(".button_close").show();
                $.post("mypage_orderlist.ajax.php",{
                    mode:"redelivery",
                    re_type:re_type,
                    ordercode:ordercode,
                    idx:idx,
                    idxs:idxs,
                    paymethod:paymethod,
                    sel_code:sel_code,
                    sel_sub_code:sel_sub_code,
                    memo:memo,
                    bankcode:bankcode,
                    bankaccount:bankaccount,
                    bankuser:bankuser,
                    bankusertel:bankusertel,
                    opt1_changes:sel_option1,
                    opt2_changes:sel_option2,
                    opt2_pt_changes:sel_option_price_text,
                    opt_text_s_changes:sel_text_opt_s,
                    opt_text_c_changes:sel_text_opt_c,
                    receipt_name:receipt_name,
                    receipt_tel:receipt_tel,
                    receipt_mobile:receipt_mobile,
                    receipt_addr:receipt_addr,
                    return_deli_price:return_deli_price,
                    return_deli_receipt:return_deli_receipt,
                    return_deli_type:return_deli_type,
                    return_deli_memo:return_deli_memo
                },function(data){
                    alert(data.msg);
                    if(data.type == 1){ 
                        window.location.reload();
                    }
                },"json");
            }
        }
	});

	$('.layer_close').click(function(){
		$('.layer-refund').fadeOut();
	});
	
	//신용카드 매출 전표
	$('button.pop_receiptCardView').click( function() {
		var orderCode = $(this).attr('ordercode');
		var receiptWin = "mypage_receipt.pop.php?orderid="+orderCode+"&mode=02";
		window.open(receiptWin , "receipt_pop" , "width=360, height=647");
	});
	
	//배송지 선택 변경시
	$("div.pop-address-add").find("select[name=destination_sel]").on("change", function(){
		//alert($(this).val());
		if($(this).val() == "") {
			$(this).parents(".layer-inner").find('input[name=destination_name]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=get_name]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=mobile]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=postcode]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=postcode_new]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=addr1]').val("").attr("disabled",false);
			$(this).parents(".layer-inner").find('input[name=addr2]').val("").attr("disabled",false);

			$(this).parents(".layer-inner").find('.base_chk').prop("checked", false);
			$(this).parents(".layer-inner").find('.base_chk_area').show();
			$(this).parents(".layer-inner").find('.btn_sh_zip').show();
		} else {
			var destination_sel_option	= $(this).find("option:selected");
			var desinfo			= destination_sel_option.attr("desinfo");
			var sp_desinfo	= desinfo.split("|@|");
			$(this).parents(".layer-inner").find('input[name=destination_name]').val(sp_desinfo[0]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=get_name]').val(sp_desinfo[1]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=mobile]').val(sp_desinfo[2]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=postcode]').val(sp_desinfo[3]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=postcode_new]').val(sp_desinfo[4]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=addr1]').val(sp_desinfo[5]).attr("disabled",true);
			$(this).parents(".layer-inner").find('input[name=addr2]').val(sp_desinfo[6]).attr("disabled",true);
			
			$(this).parents(".layer-inner").find('.base_chk').prop("checked", false);
			$(this).parents(".layer-inner").find('.base_chk_area').hide();
			$(this).parents(".layer-inner").find('.btn_sh_zip').hide();
		}
	});	

	//배송지 변경 저장시
	$("div.pop-address-add").find(".btnAddressSubmit").click(function(){

		var mode				= "receiver_change";
		var base_chk			= 'N';
		var dn_ins				= "N";
		var procSubmit		= true;

		var ordercode				= $(this).attr('ordercode');

		var destination_sel		= $(this).parents(".layer-inner").find('select[name=destination_sel]').val();
		var destination_name	= $(this).parents(".layer-inner").find('input[name=destination_name]').val();
		var get_name				= $(this).parents(".layer-inner").find('input[name=get_name]').val();
		var mobile					= $(this).parents(".layer-inner").find('input[name=mobile]').val();
		var postcode				= $(this).parents(".layer-inner").find('input[name=postcode]').val();
		var postcode_new		= $(this).parents(".layer-inner").find('input[name=postcode_new]').val();
		var addr1					= $(this).parents(".layer-inner").find('input[name=addr1]').val();
		var addr2					= $(this).parents(".layer-inner").find('input[name=addr2]').val();
		
		if ($(this).parents(".layer-inner").find('input[name=base_chk]').is(':checked') == true)  base_chk				= 'Y';
		if(destination_sel =="") dn_ins	= "Y";

		//alert(destination_sel+"\n"+destination_name+"\n"+get_name+"\n"+mobile+"\n"+postcode+"\n"+postcode_new+"\n"+addr1+"\n"+addr2+"\n"+base_chk);return false;

		$(this).parents(".layer-inner").find(".required_value").each(function(){
			if(!$(this).val()){
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
				$(this).focus();
				procSubmit = false;
				return false;
			}
		})

		var destination_field		= $(this).parents(".layer-inner").find('input[name=destination_name]');

		if(procSubmit){
			if(confirm('배송지를 변경하시겠습니까?')){
				if(dn_ins == 'Y') {
					if(!confirm('배송지 관리에 등록하시겠습니까?')){
						dn_ins	= "N";
					}
				}
				$.post("mypage_orderlist.ajax.php",{
					mode:mode,
					dn_ins:dn_ins,
					ordercode:ordercode,
					destination_sel:destination_sel,
					destination_name:destination_name,
					get_name:get_name,
					mobile:mobile,
					postcode:postcode,
					postcode_new:postcode_new,
					addr1:addr1,
					addr2:addr2,
					base_chk:base_chk
				},function(data){
					alert(data.msg);
					if(data.type == 1){ 
						window.location.reload();
					} else if(data.type == 2){ 
						destination_field.focus();
					}
				},"json");
			}
		}else{
			return false;
		}
	});	
});

function opt_jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
}

//chr(30)처리를 위한 함수
 function chr(code) 
{ 
    return String.fromCharCode(code); 
}

function option_change(obj, productcode, option_depth, option_totalDepth, option_code) {	

	var def_option	="<option value=\"\">===선택===</option>\n";

	for (var i=option_depth; i < option_totalDepth; i++)
	{
		$(obj).parents(".opt-change").find("select[name=sel_option"+i+"]").find("option").remove();
		$(obj).parents(".opt-change").find("select[name=sel_option"+i+"]").html(def_option);
	}

	$.ajax({
		type: "POST",
		url: "ajax_product_option.php",
		data: "productcode="+productcode+"&option_code="+option_code+"&option_depth="+option_depth,
		dataType:"JSON",
		success: function(data){
			var sel_option	="";
			var soldout	="";
			var disabled_on = '';
			var option_price = '';
			if (data)
			{
				$.each(data, function(){
					if (this.price > 0) {
						var opt_price = this.price;
						var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
						opt_price += '';  // 숫자를 문자열로 변환
						while (reg.test(opt_price)){
							opt_price = opt_price.replace(reg, '$1' + ',' + '$2');
						}
						option_price		= "(+"+opt_price+"원)";
					} else {
						option_price		= "";
					}
					if( this.soldout == 1 ) {
						disabled_on = ' disabled';
						soldout = '&nbsp;[품절]';
					} else {
						disabled_on = '';
						soldout = '';
					}
					sel_option += "				<option value='"+this.code+"' opt='"+this.code+"' opt_name=\""+this.code+option_price+soldout+"\" opt_p='"+this.price+"' "+disabled_on+">"+this.code+option_price+soldout+"</option>\n";
				});
				$(obj).parents(".opt-change").find("select[name=sel_option"+option_depth+"]").append(sel_option);
			}
		},
		complete: function(data){
		},
		error:function(xhr, status , error){
			alert("에러발생");
		}
	});
}

function layer_open(el){

	var temp = $('#' + el);
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수

	if(bg){
		$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
	}else{
		temp.fadeIn();
	}

	// 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
	else temp.css('top', '0px');
	if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
	else temp.css('left', '0px');

	temp.find('a.cbtn').click(function(e){
		if(bg){
			$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
		}else{
			temp.fadeOut();
		}
		e.preventDefault();
	});

	$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
		$('.layer').fadeOut();
		e.preventDefault();
	});

}

function search_zip(text){
	daum.postcode.load(function(){
		new daum.Postcode({
			oncomplete: function(data) {
				var postcode = data.zonecode; //2015-08-01 시행 새 우편번호
				var zipCode1 = data.postcode1; //구 우편번호1
				var zipCode2 = data.postcode2; //구 우편번호2

				if(data.userSelectedType == 'R'){ //도로명
					var address = data.roadAddress;
				}else{//지번
					var address = data.jibunAddress;
				}
				
				$("#postcode_new").val(postcode);
				$("#postcode").val(zipCode1+"-"+zipCode2);
				$("#addr1").val(address);

			}
		}).open();
	});
}

</SCRIPT>

<?php 
	if (ord($ordercodeid) && ord($ordername)) {	//비회원 주문조회
		$curdate = date("Ymd00000",strtotime('-90 day'));
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode > '{$curdate}' AND id LIKE 'X{$ordercodeid}%' ";
		$sql.= "AND sender_name='{$ordername}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_ord=$row;
			$ordercode=$row->ordercode;
			$gift_price=$row->price-$row->deli_price;
		} else {

			echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.<br><br>회원주문이 아닌경우 주문후 90일이 경과하였다면 상점에 문의바랍니다.</td></tr>\n";
			echo "<tr><td align=center></td></tr>\n";
			echo "</table>";
			exit;
		}
		pmysql_free_result($result);
	} else {
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_ord=$row;
			$gift_price=$row->price-$row->deli_price;
		} else {
			echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.</td></tr>\n";
			echo "<tr><td align=center></td></tr>\n";
			echo "</table>";
			exit;
		}
		pmysql_free_result($result);
	}

	if($_ord){
		$addr_tmp = explode("주소 :",$_ord->receiver_addr);
		$_ord->zipcode=str_replace("우편번호 :","",$addr_tmp[0]);
		$_ord->address=$addr_tmp[1];
	}
?>

<?php 
include ($Dir.TempletDir."orderlist/orderlist_view_TEM_001.php");
?>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>

</HTML>
