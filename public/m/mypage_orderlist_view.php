<?
include_once('outline/header_m.php');

$product = new PRODUCT();

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

function dateDiff($nowDate, $oldDate) {
	$nowDate = date_parse($nowDate);
	$oldDate = date_parse($oldDate);
	return ((gmmktime(0, 0, 0, $nowDate['month'], $nowDate['day'], $nowDate['year']) - gmmktime(0, 0, 0, $oldDate['month'], $oldDate['day'], $oldDate['year']))/3600/24);
}

$imgpath_gift=$cfg_img_path['gift'];

$ordgbn = ($_POST["ordgbn"]) ? $_POST["ordgbn"] : $_GET["ordgbn"];				// 주문타입
$ordercode = ($_POST["ordercode"]) ? $_POST["ordercode"] : $_GET["ordercode"];				//로그인한 회원이 조회시
$ordername = ($_POST["ordername"]) ? $_POST["ordername"] : $_GET["ordername"];			//비회원 조회시 주문자명
$ordercodeid = ($_POST["ordercodeid"]) ? $_POST["ordercodeid"] : $_GET["ordercodeid"];		//비회원 조회시 주문번호 6자리
$print = ($_POST["print"]) ? $_POST["print"] : $_GET["print"];													//OK일 경우 프린트

if(ord($ordercodeid) && strlen($ordercodeid)!=6) {
	alert_go('주문번호 6자리를 정확히 입력하시기 바랍니다.','c');
}

$gift_type=explode("|",$_data->gift_type);

$type = ($_POST["type"]) ? $_POST["type"] : $_GET["type"];
$tempkey = ($_POST["tempkey"]) ? $_POST["tempkey"] : $_GET["tempkey"];
$rescode = ($_POST["rescode"]) ? $_POST["rescode"] : $_GET["rescode"];

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
	echo "<html></head><body onload=\"alert('오류발생,관리자에게 문의해주세요'); location.href='/m/'\"></body></html>";
	exit;
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

#주문상품
$sql = "SELECT
				a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text,
				a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
				a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage,
				b.minimage, a.option_type, a.option_price, a.option_quantity,
				a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num,
				a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
				a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, b.option1_tf, option2_tf, option2_maxlen, 
				a.delivery_type, a.store_code, a.reservation_date ,
                a.store_stock_yn, a.oc_no, b.prodcode, b.colorcode, a.use_epoint, a.deli_closed
			FROM
				tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx
			WHERE
				a.ordercode='".$ordercode."'
			ORDER BY vender ASC, productcode ASC ";

$result=pmysql_query($sql,get_db_conn());

# 물류 정보를 확인해서 값이 변경될 경우 reload를 시킨다 2016-11-28 유동혁
$reloadFlag = false;
$reserve_point = 0; // 총 적립금
while($row=pmysql_fetch_object($result)) {
	$i++;
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
		'prodcode' => $row->prodcode,
		'colorcode' => $row->colorcode,
		'use_epoint' => $row->use_epoint,
		'deli_closed' => $row->deli_closed
	);

	$reserve_point += $row->reserve;
/*
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
    }*/
}
pmysql_free_result($result);


# ERP 정보 UPDATE가 성공할경우 reload 2016-11-28 유동혁
if( $reloadFlag ){
    echo '<script>';
    echo '  location.reload();';
    echo '</script>';
    exit;
}


//환불 계좌 정보
if(strlen($_MShopInfo->getMemid()) > 0) {
	list($refund_bankcode, $refund_bankaccount, $refund_bankuser, $refund_bankusertel)=pmysql_fetch_array(pmysql_query("select bank_code, account_num, depositor, home_tel from tblmember where id='".$_MShopInfo->getMemid()."'"));
}

# 배송업체를 불러온다.
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);


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

if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
	$history_back_link	= "mypage_orderlist.php";
} else {
	$history_back_link	= "login.php";
}

?>
<SCRIPT LANGUAGE="JavaScript">

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

function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
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
		url: "../front/ajax_product_option.php",
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

//chr(30)처리를 위한 함수
 function chr(code) 
{ 
    return String.fromCharCode(code); 
}


$(document).ready(function(){
	//구매확정
	$(".deli_ok").click(function(){
		if(confirm('구매확정을 하시겠습니까?')){
			ordcode = $(this).attr('ordercode');
			idx = $(this).attr('idx');

			$.post("<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",{mode:"deli_ok",ordercode:ordcode,idx:idx},function(data){
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

			$.post("<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",{mode:"ord_req_cancel",ordercode:ordcode,idx:idx,oc_no:oc_no},function(data){
				alert(data.msg);
				if(data.type == 1){ 
					window.location.reload();
				}
			},"json");
		}
		
	});

	// 취소 - 주문접수상태일 경우
	$(".ord_receive_cancel").click(function(){
		var ordercode		= $(this).attr("ordercode");
		var idxs				= $(this).attr("idxs");
		var paymethod	= $(this).attr("paymethod");

		if(confirm('취소를 하시겠습니까??')){
			$.post("<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",{mode:"receive_cancel",ordercode:ordercode,idxs:idxs,paymethod:paymethod},function(data){
				//alert(data.msg);
				if(data.type == 1){
					//window.location.reload();
					//window.location.href = "mypage_orderlist.php";
				}
			},"json");
			window.location.href = "mypage_orderlist.php";
			//window.location.reload();
		}
	});
	
	$(".CLS_delivery_tracking").click(function(){
		window.open( $(this).attr('urls'), "배송추적");
	});

	// 취소
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

		$("#rg_list").html("");
		$("#rg_list_t").html("");

		$(".refund-way").removeClass('hide');
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

			appHtml	+= "		<li>\n";
			appHtml	+= "			<div class=\"cart_wrap\">\n";
			appHtml	+= "				<div class=\"clear\">\n";
			appHtml	+= "					<div class=\"goods_area\">\n";
			appHtml	+= "						<div class=\"img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "						<div class=\"info\">\n";
			appHtml	+= "							<p class=\"brand\">"+op_brand+"</p>\n";
			appHtml	+= "							<p class=\"name\">"+op_name+"</p>\n";
			appHtml	+= "							<p class=\"option\">"+op_opt_qy+" / "+op_quantity+"개</p>\n";
			appHtml	+= "							<p class=\"price\">￦ "+comma(op_sellprice)+" </p>\n";
			appHtml	+= "						</div>\n";
			appHtml	+= "					</div>\n";
			appHtml	+= "				</div>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "		</li>\n";

		}

		$("#rg_list").append(appHtml);

		appHtml	= "";
		if(paymethod=="Q"){
		appHtml	+= "<input type=hidden name=re_type value=\"B\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\"\"><input type=hidden name=idxs value=\""+idxs+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=each_price value=\""+t_op_total_price+"\">";
		}else{
		appHtml	+= "<input type=hidden name=re_type value=\"\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\"\"><input type=hidden name=idxs value=\""+idxs+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=each_price value=\""+t_op_total_price+"\">";
		}
		
		appHtml	+= "			<ul>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>상품합계</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>할인</label>\n";
		appHtml	+= "					<span class=\"point-color\">- ￦ "+comma(t_op_dc_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>배송비</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_deli_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li class=\"total\">\n";
		appHtml	+= "					<label>합계금액</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_total_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "			</ul>\n";

		$("#rg_list_t").html(appHtml);
		$("#tr_return").hide();
		$("#tr_refund").show();

		if(paymethod!="Q"){
			$("#parcel_pay").hide();
		}
		$('.popDelivery-return.refund').fadeIn();

	});

	// 반품
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
		
		$("#rg_list").html("");
		$("#rg_list_t").html("");

		var appHtml	= "";
		appHtml	+= "		<li>\n";
		appHtml	+= "			<div class=\"cart_wrap\">\n";
		appHtml	+= "				<div class=\"clear\">\n";
		appHtml	+= "					<div class=\"goods_area\">\n";
		appHtml	+= "						<div class=\"img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
		appHtml	+= "						<div class=\"info\">\n";
		appHtml	+= "							<p class=\"brand\">"+op_brand+"</p>\n";
		appHtml	+= "							<p class=\"name\">"+op_name+"</p>\n";
		appHtml	+= "							<p class=\"option\">"+op_opt_qy+" / "+op_quantity+"개</p>\n";
		appHtml	+= "							<p class=\"price\">￦ "+comma(op_sellprice)+" </p>\n";
		appHtml	+= "						</div>\n";
		appHtml	+= "					</div>\n";
		appHtml	+= "				</div>\n";
		appHtml	+= "			</div>\n";
		appHtml	+= "		</li>\n";

		$("#rg_list").append(appHtml);
		
		appHtml	= "";
		appHtml	+= "<input type=hidden name=re_type value=\"B\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\""+idx+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=each_price value=\""+op_total_price+"\">";
		appHtml	+= "			<ul>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>상품합계</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>할인</label>\n";
		appHtml	+= "					<span class=\"point-color\">- ￦ "+comma(t_op_dc_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li>\n";
		appHtml	+= "					<label>배송비</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_deli_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "				<li class=\"total\">\n";
		appHtml	+= "					<label>합계금액</label>\n";
		appHtml	+= "					<span>￦ "+comma(t_op_total_price)+"</span>\n";
		appHtml	+= "				</li>\n";
		appHtml	+= "			</ul>\n";
		
		$("#rg_list_t").html(appHtml);
		$("#tr_refund").hide();
		$("#tr_return").show();
		$("#parcel_pay").show();
		$('.popDelivery-return.refund').fadeIn();

	});

	// 교환
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
		
		$("#cg_list").html("<input type=hidden name=re_type value=\"C\"><input type=hidden name=ordercode value=\""+ordercode+"\"><input type=hidden name=pg_ordercode value=\""+pg_ordercode+"\"><input type=hidden name=idx value=\""+idx+"\"><input type=hidden name=pc_type value=\""+pc_type+"\"><input type=hidden name=paymethod value=\""+paymethod+"\"><input type=hidden name=option1 value=\""+op_opt1+"\"><input type=hidden name=text_opt_s value=\""+op_text_opt_s+"\">");
		$("#cg_list_t").html("");
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
					sel_option += "	<dd class=\"select opt"+s+"\">\n";
					sel_option += "		<select class=\"sel_option select_line\" name=\"sel_option"+s+"\" est=\""+sel_est+"\""+on_change+">\n";
					sel_option += "		<option value=\"\">"+op_opt1_arr[s]+"</option>\n";
					sel_option += "		</select>\n";
					sel_option += "	</dd>\n";
					
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
					sel_option += "	<dd>\n";
					//sel_option += "		<span>"+op_text_opt_s_arr[s]+"</span>\n";
					sel_option += "		<input type=\"text\" class=\"opt_text\" name=\"opt_text"+s+"\" est=\""+sel_est+"\" class=\"input-def w200\">\n";
					sel_option += "	</dd>\n";
				}
			}

			appHtml	+= "		<li>\n";
			appHtml	+= "			<div class=\"cart_wrap\" >\n";
			appHtml	+= "				<div class=\"clear\">\n";
			appHtml	+= "					<div class=\"goods_area\">\n";
			appHtml	+= "						<div class=\"img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "						<div class=\"info\">\n";
			appHtml	+= "							<p class=\"brand\">"+op_brand+"</p>\n";
			appHtml	+= "							<p class=\"name\">"+op_name+"</p>\n";
			appHtml	+= "							<p class=\"option\">"+op_opt_qy+" / "+op_quantity+"개</p>\n";
			appHtml	+= "							<p class=\"price\">￦ "+comma(op_total_price)+" </p>\n";
			appHtml	+= "						</div>\n";
			appHtml	+= "					</div>\n";
			appHtml	+= "				</div>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "			<div class=\"optbox\">\n";
			appHtml	+= "				<dl>\n";
			appHtml	+= "					<dt>옵션</dt>\n";
			appHtml	+= "					<dd class=\"opt_name\">"+sel_option+"</dd>\n";
			appHtml	+= "				</dl>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "		</li>\n";

			$("#cg_list").append(appHtml);

			if (op_opt1 != "")
			{
				$.ajax({
					type: "POST",
					url: "../front/ajax_product_option.php",
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

								$("#cg_list").find("select[name=sel_option0]").append(sel_option);
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
			appHtml	+= "		<ul>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>상품합계</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>할인</label>\n";
			appHtml	+= "				<span class=\"point-color\">- ￦ "+comma(t_op_dc_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>배송비</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_deli_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li class=\"total\">\n";
			appHtml	+= "				<label>합계금액</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_total_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "		</ul>\n";

			$("#cg_list_t").html(appHtml);

			$('.popDelivery-return.exchange').fadeIn();
		} else {
			
			appHtml	+= "		<li>\n";
			appHtml	+= "			<div class=\"cart_wrap\" >\n";
			appHtml	+= "				<div class=\"clear\">\n";
			appHtml	+= "					<div class=\"goods_area\">\n";
			appHtml	+= "						<div class=\"img\"><a href=\"productdetail.php?productcode="+productcode+"\"><img src=\""+op_img+"\" alt=\""+op_name+"\"></a></div>\n";
			appHtml	+= "						<div class=\"info\">\n";
			appHtml	+= "							<p class=\"brand\">"+op_brand+"</p>\n";
			appHtml	+= "							<p class=\"name\">"+op_name+"</p>\n";
			appHtml	+= "							<p class=\"option\">"+op_opt_qy+" / "+op_quantity+"개</p>\n";
			appHtml	+= "							<p class=\"price\">￦ "+comma(op_total_price)+" </p>\n";
			appHtml	+= "						</div>\n";
			appHtml	+= "					</div>\n";
			appHtml	+= "				</div>\n";
			appHtml	+= "			</div>\n";
			appHtml	+= "		</li>\n";

			$("#cg_list").append(appHtml);

			appHtml	="";
			appHtml	+= "		<ul>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>상품합계</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>할인</label>\n";
			appHtml	+= "				<span class=\"point-color\">- ￦ "+comma(t_op_dc_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li>\n";
			appHtml	+= "				<label>배송비</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_deli_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "			<li class=\"total\">\n";
			appHtml	+= "				<label>합계금액</label>\n";
			appHtml	+= "				<span>￦ "+comma(t_op_total_price)+"</span>\n";
			appHtml	+= "			</li>\n";
			appHtml	+= "		</ul>\n";

			$("#cg_list_t").html(appHtml);

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

        if(confirm(alert_text+'를 하시겠습니까?')){
            if (re_type == '' && (paymethod =='C' || paymethod =='V')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
				$.ajax({
					url:"<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",
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
									$.post("<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",{
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
                $.post("<?=$Dir.FrontDir?>mypage_orderlist.ajax.php",{
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


/*
	// 취소
	$('.ord_cancel').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pc_type		= $(this).attr("pc_type");
		if (pc_type == 'ALL') {
			var idxs				= $(this).attr("idxs");
		} else if (pc_type == 'PART') {
			var idxs				= $(this).attr("idx");
		}

		document.location.href="mypage_ordercancel.php?mode=cancel&ordercode="+ordercode+"&pc_type="+pc_type+"&idxs="+idxs;

	});

	// 반품
	$('.ord_regoods').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pc_type		= $(this).attr("pc_type");
		var idx				= $(this).attr("idx");
		document.location.href="mypage_ordercancel.php?mode=regoods&ordercode="+ordercode+"&pc_type="+pc_type+"&idx="+idx;
	});

	// 교환
	$('.ord_change').click(function(){
		var ordercode		= $(this).attr("ordercode");
		var pc_type		= $(this).attr("pc_type");
		var idx				= $(this).attr("idx");
		document.location.href="mypage_ordercancel.php?mode=rechange&ordercode="+ordercode+"&pc_type="+pc_type+"&idx="+idx;
	});
*/
});
</SCRIPT>

<!-- 내용 -->
<main id="content" class="subpage">
	
	<!-- 매장안내 팝업 -->
	<section class="pop_layer layer_store_info">
		<div class="inner">
			<h3 class="title">매장 위치정보 <button type="button" class="btn_close">닫기</button></h3>
			<div class="select_store store_view">
				
			</div><!-- //.select_store -->
		</div>
	</section>
	<!-- //매장안내 팝업 -->

	<!-- 교환신청 팝업 -->
	<section class="pop_layer layer_exchange popDelivery-return exchange">
		<div class="inner layer-inner">
			<h3 class="title">교환신청 <button type="button" class="btn_close">닫기</button></h3>
			<div class="pb-30">
				<ul class="cart_goods" id="cg_list"></ul><!-- //.cart_goods -->
				<div class="cart_calc mt-10" id="cg_list_t"></div><!-- //.cart_calc -->
				
				<!-- 교환사유 -->
				<div class="order_table">
					<h3 class="cart_tit">교환사유</h3>
					<table class="th-left">
						<colgroup>
							<col style="width:29.37%;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<tr>
								<th><span class="required">교환사유</span></th>
								<td>
									<select class="select_line w100-per tab-select" name="c_sel_code" id="exchane_reason" title="교환사유 선택">
										<option value="">선택</option>
<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($exchange_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div >
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																						</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
									</select>
									<?=$oc_reason_sub_code_html?>
								</td>
								
							</tr>
							<tr>
								<th><span class="required">상세사유</span></th>
								<td><textarea class="w100-per" id="detail_reason" name="memo" title="상세사유 입력" placeholder="교환내용을 자세하게 작성해주세요."></textarea></td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.order_table -->
				<!-- //교환사유 -->

				<!-- 택배비 발송 -->
				<div class="order_table">
					<h3 class="cart_tit">택배비 발송</h3>
					<table class="th-left">
						<colgroup>
							<col style="width:29.37%;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<tr>
								<td class="pl-10">
									<?
									$oc_delivery_fee_type_cnt = 0;
									foreach($delivery_fee_type as $key => $val) {
									?>
									
										<?if($key  == "3"){ ?>
										</td></tr>
										<tr><td class="pl-10">
										<label class="radio_with_input">
											<input type="radio" class="radio_def" value="<?=$key ?>" name="return_deli_type">
											<span><?=$val ?></span>
											<input type="text" class="with_input" name="return_deli_memo" id="return_deli_memo2" placeholder="입금자명">
										</label>
										<?}else{ ?>
										<label>
											<input type="radio" class="radio_def" value="<?=$key ?>" name="return_deli_type">
											<span><?=$val ?></span>
										</label>
										<?} ?>
									<?} ?>
								</td>
							</tr>
							
						</tbody>
					</table>
				</div><!-- //.order_table -->
				<!-- //택배비 발송 -->

				<div class="attention mt-20">
					<h3 class="tit">유의사항</h3>
					<ul class="list">
						<li>교환은 같은 옵션상품만 가능합니다. 다른 옵션의 상품으로 교환을 원하실 경우, 반품 후 재구매를 해주세요.</li>
						<li>상품이 손상/훼손되었거나 이미 사용하셨다면 교환이 불가능합니다. </li>
						<li>교환 사유가 구매자 사유일 경우 왕복 교환 배송비를 상품과 함께 박스에 동봉해 주세요.</li>
						<li>교환 왕복 배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다. </li>
						<li>교환 사유가 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다. </li>
						<li>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다.</li>
					</ul>
				</div>
				<div class="btn_area mt-20 mr-10 ml-10 button_open">
					<ul>
						
						<li><a href="javascript:;" class="btn-point h-input refundSubmit">교환신청</a></li>
					</ul>
				</div>
				<div class="btn_area mt-20 mr-10 ml-10 button_close" style="text-align:center; display:none;">
					========== 처리중 입니다 ==========
				</div>

			</div>
		</div>
	</section><!-- //.layer_exchange -->
	<!-- //교환신청 팝업 -->

	<!-- 반품신청 팝업 -->
	<section class="pop_layer layer_refund popDelivery-return refund">
		<div class="inner layer-inner">
			<h3 class="title">환불/반품신청 <button type="button" class="btn_close">닫기</button></h3>
			<div class="pb-30">
				<ul class="cart_goods" id="rg_list">	</ul><!-- //.cart_goods -->
				<div class="cart_calc" id="rg_list_t">	</div><!-- //.cart_calc -->

				<ul class="list_notice">
					<li>* 할인금액, 배송비를 제외한 금액으로 환불됩니다.</li>
					<li>* 결제 수단별 환불방법과 환불소요기간에 차이가 있습니다. </li>
				</ul>

				<!-- 반품사유 -->
				<div class="order_table">
					<h3 class="cart_tit">환불/반품사유</h3>
					<table class="th-left">
						<colgroup>
							<col style="width:29.37%;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<tr id="tr_refund">
								<th><span class="required">환불사유</span></th>
								<td>
									<select class="select_line w100-per tab-select" name="b_sel_code" id="refund_reason">
										<option value="">선택</option>
										<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($cancel_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div>
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
									</select>
								</td>
								<?=$oc_reason_sub_code_html?>
							</tr>
							<tr id="tr_return">
								<th><span class="required">반품사유</span></th>
								<td>
									<select class="select_line w100-per tab-select" name="b_sel_code2" id="refund_reason">
										<option value="">선택</option>
<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($return_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div>
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
									</select>
									<?=$oc_reason_sub_code_html?>
								</td>
								
							</tr>
							<tr>
								<th><span class="required">상세사유</span></th>
								<td><textarea class="w100-per"id="detail_reason2" name="memo" title="상세사유 입력" placeholder=""></textarea></td>
							</tr>
<?
				if ($_ord->paymethod[0] == 'C') { // 카드결제일 경우
					$refund_text	= "신용카드 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'M') { // 휴대폰결제일 경우
					$refund_text	= "휴대폰결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'Y') { // 페이코결제일 경우
					$refund_text	= "PAYCO결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'V') { // 계좌이체결제일 경우
					$refund_text	= "계좌이체결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'G') { // 임직원 포인트결제일 경우
					$refund_text	= "임직원 포인트 환원";
					$account_disabled	= " disabled";
				} else {
					$refund_text	= "계좌입금(가상계좌 입금의 경우는 계좌입금만 가능)";
					$account_disabled	= "";
				}
?>
							<tr>
								<th><span class="required">환불방법</span></th>
								<td class='refund-way'><?=$refund_text?></td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.order_table -->
				<!-- //반품사유 -->

				<!-- 환불계좌 -->
				<div class="order_table account-info">
					<h3 class="cart_tit">환불계좌</h3>
					<table class="th-left">
						<colgroup>
							<col style="width:29.37%;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<tr>
								<th><span class="required">은행명</span></th>
								<td>
									<select class="select_line w100-per" name="bankcode" id="refund_bank" <?=$account_disabled?>>
										<option value="">선택</option>
<?php
										foreach($oc_bankcode as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val?></option>
<?php
										}
?>
									</select>
								</td>
							</tr>
							<tr>
								<th><span class="required">계좌번호</span></th>
								<td><input type="text" class="w100-per"  id="account-number" name="bankaccount" placeholder="하이픈(-) 없이 입력" maxlength="20" title="환불받을 계좌번호 입력" style="ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" <?=$account_disabled?>></td>
							</tr>
							<tr>
								<th><span class="required">예금주</span></th>
								<td><input type="text" class="w100-per" placeholder="이름" id="account-name" name="bankuser" maxlength="20" title="환불받을 계좌 예금주" placeholder="이름"<?=$account_disabled?>></td>
							</tr>
							<tr>
								<th><span class="required">연락처</span></th>
								<td>
									<div class="input_tel">
										
										<input type="text" class="w100-per" id="account-tel" name="bankusertel" maxlength="20" title="환불받는 분 연락처" placeholder="하이픈(-) 없이 입력" style="ime-mode:disabled;"
onKeypress="if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" <?=$account_disabled?>>

									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.order_table -->
				<!-- //환불계좌 -->

				<!-- 택배비 발송 -->
				<div class="order_table"  id="parcel_pay">
					<h3 class="cart_tit">택배비 발송</h3>
					<table class="th-left">
						<colgroup>
							<col style="width:29.37%;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<tr>
								<td class="pl-10">
						<?
						$oc_delivery_fee_type_cnt = 0;
						foreach($delivery_fee_type as $key => $val) {
						?>
						
							<?if($key  == "3"){ ?>
							</td></tr>
							<tr><td class="pl-10">
							<label class="radio_with_input">
								<input type="radio" class="radio_def" value="<?=$key ?>" name="return_deli_type">
								<span><?=$val ?></span>
								<input type="text" class="with_input" name="return_deli_memo" id="return_deli_memo" placeholder="입금자명">
							</label>
							<?}else{ ?>
							<label>
								<input type="radio" class="radio_def" value="<?=$key ?>" name="return_deli_type">
								<span><?=$val ?></span>
							</label>
							<?} ?>
						<?} ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.order_table -->
				<!-- //택배비 발송 -->

				<input type=hidden name="return_deli_price" id="return_deli_price" value=""  >
				<input type="hidden" name="return_deli_receipt" id="return_deli_receipt" title="택배비 수령" value=""></td>
				<input type="hidden" name="receiver_tel1" id="receiver_tel1" value="<?=$_ord->receiver_tel1?>">

				<div class="attention mt-20">
					<h3 class="tit">유의사항</h3>
					<ul class="list">
						<li>상품이 손상/훼손 되었거나 이미 사용하셨다면 반품이 불가능합니다.  </li>
						<li>반품 사유가 단순변심, 구매자 사유일 경우반품 배송비를 상품과 함께 박스에 동봉해 주세요 </li>
						<li>배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다.  </li>
						<li>반품 사유가 상품불량/파손, 배송누락/오배송 등 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다.  </li>
						<li>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다.</li>
						<li>가상계좌로 결제하신 경우에는 환불이 영업일 기준으로 1~2일정도 소요될 수 있습니다.</li>
					</ul>
				</div>
				<div class="btn_area mt-20 mr-10 ml-10 button_open">
					<ul>
						<li><a href="javascript:;" class="btn-point h-input refundSubmit">신청</a></li>
					</ul>
				</div>
				<div class="btn_area mt-20 mr-10 ml-10 button_close" style="text-align:center; display:none;">
					========== 처리중 입니다 ==========
				</div>

			</div>
		</div>
	</section><!-- //.layer_refund -->
	<!-- //반품신청 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>주문/배송조회</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="orderlist_view sub_bdtop">
		<!-- 주문상세 테이블 -->
		<div class="order_table mt-15">
			<table class="th-left">
				<colgroup>
					<col style="width:32.8%;">
					<col style="width:auto;">
				</colgroup>
				<thead>
					<tr>
						<th colspan="2">
							<span class="ordnum">주문번호: <?=$ordercode?></span>
							<span class="date"><? echo substr($ordercode,0,4).".".substr($ordercode,4,2).".".substr($ordercode,6,2); ?></span>
						</th>
					</tr>
				</thead>
				

			</table>
		</div><!-- //.order_table -->
		<!-- //주문상세 테이블 -->
<?php
		$can_cnt	= 0;
		$pr_idxs		= "";
		$op_cnt	= count($orProduct);
		$op_step_chk	= "";
		$op_step_cnt	= 0;
		$chkDeliveryType = true;
		foreach( $orProduct as $pr_idx=>$prVal ) { // 상품
			if($prVal->delivery_type == '3'){
				# 당일 배송이 있는지 체크
				# 당일 배송이 있을 경우 배송지 정보수정을 막기 위해.
				$chkDeliveryType = false;
			}
			if ($pr_idxs == '') {
				$pr_idxs		.= $pr_idx;
			} else {
				$pr_idxs		.= "|".$pr_idx;
			}

			if ($op_step_chk == "") {
				$op_step_chk = $prVal->op_step;
			} else {
				if ($op_step_chk != $prVal->op_step) {
					$op_step_cnt++;
				}
			}

			$file = getProductImage($Dir.DataDir.'shopimages/product/', $prVal->tinyimage);


			$optStr	= "";
			$optPum="";
			$option1	 = $prVal->opt1_name;
			$option2	 = $prVal->opt2_name;
			$tmp_opt_price = $prVal->option_price * $prVal->quantity;

			
			if($prVal->prodcode){
				$optPum	.= "품번 : ".$prVal->prodcode;
			}
			if($prVal->colorcode){
				$optStr	.= "색상 : ".$prVal->colorcode;
			}



			if( strlen( trim( $prVal->opt1_name ) ) > 0 ) {
				$opt1_name_arr	= explode("@#", $prVal->opt1_name);
				$opt2_name_arr	= explode(chr(30), $prVal->opt2_name);
				for($g=0;$g < sizeof($opt1_name_arr);$g++) {
					if ($g >= 0) $optStr	.= " / ";
					$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
				}
			}

			if( strlen( trim( $prVal->text_opt_subject ) ) > 0 ) {
				$text_opt_subject_arr	= explode("@#", $prVal->text_opt_subject);
				$text_opt_content_arr	= explode("@#", $prVal->text_opt_content);

				for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
					if ($text_opt_content_arr[$s]) {
						if ($optStr != '') $optStr	.= " / ";
						$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
					}
				}
			}

			if( $tmp_opt_price > 0 ) $optStr	 .= '&nbsp;( + '.number_format( $tmp_opt_price ).'원)';
			//if ($optStr !='') $optStr	 .= ' / ';
            //$optStr	 .= '<span>수량 : '.number_format( $prVal->quantity )."개</span>";

		
			//배송비로 인한 보여지는 가격 재조정
			$can_deli_price	= 0;
			$can_total_price	= (($prVal->price + $prVal->option_price) * $prVal->option_quantity) - ($prVal->coupon_price + $prVal->use_point + $prVal->use_epoint) + $prVal->deli_price;

			list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$prVal->productcode."%'"));
			//echo $od_deli_price;
			if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
				// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
				list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$pr_idx."' and op_step < 40 limit 1"));
				//echo "SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$pr_idx."' and op_step < 40 limit 1<br>";
				if ($op_idx) { // 상품이 있으면
					if ($prVal->deli_price > 0) $can_total_price	= $can_total_price - $od_deli_price;
				} else {
					$can_deli_price	= $od_deli_price;
				}
			}
			
			$pro_info	 = $prVal->productcode."!@#";
			$pro_info	.= substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)."!@#";
			$pro_info	.= $file."!@#";
			$pro_info	.= $prVal->brandname."!@#";
			$pro_info	.= $prVal->productname."!@#";
			$pro_info	.= $optStr."!@#";
			$pro_info	.= $option1."!@#";
			$pro_info	.= $option2."!@#";
			$pro_info	.= $prVal->text_opt_subject."!@#";
			$pro_info	.= $prVal->text_opt_content."!@#";
			$pro_info	.= $prVal->option_price_text."!@#";
			$pro_info	.= $prVal->consumerprice."!@#";
			$pro_info	.= $prVal->sellprice."!@#";
			$pro_info	.= $prVal->deli_price."!@#";
			$pro_info	.= $prVal->coupon_price."!@#";
			$pro_info	.= $prVal->use_point."!@#";
			$pro_info	.= (($prVal->price + $prVal->option_price) * $prVal->option_quantity) - ($prVal->coupon_price + $prVal->use_point + $prVal->use_epoint) + $prVal->deli_price."!@#";
			$pro_info	.= $prVal->option_type."!@#";
			$pro_info	.= $prVal->option1_tf."!@#";
			$pro_info	.= $prVal->option2_tf."!@#";
			$pro_info	.= $prVal->option2_maxlen."!@#";

			//입점업체 정보 관련
			if($prVal->vender>0) {
				$sql = "SELECT deli_info, re_addrinfo ";
				$sql.= "FROM tblvenderstore ";
				$sql.= "WHERE vender='{$prVal->vender}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($_vdata=pmysql_fetch_object($result)) {
					$tempvdeli_info=explode("=", stripslashes($_vdata->deli_info));
					if ($_vdata->deli_info && $tempvdeli_info[0]=="Y") {
						$tempaddr_info=explode("|@|",$_vdata->re_addrinfo);
						$pro_info	.=  "(".$tempaddr_info[0].") ".$tempaddr_info[3];
					} else {
						$pro_info	.=  "!@#";
					}
				} else {
					$pro_info	.=  "!@#";
				}
				pmysql_free_result($result);
			} else {
				$pro_info	.=  "!@#";
			}

			$storeData = getStoreData($prVal->store_code);

            list($stock_yn) = pmysql_fetch("Select store_stock_yn from tblorderproduct Where idx = ".$pr_idx."");
            if($stock_yn == "N") $stock_status = " (재고부족)";
            else $stock_status = "";
			
			$pro_info	.= $can_deli_price."!@#";
			$pro_info	.= $can_total_price."!@#";
			$pro_info	.= ($prVal->option_quantity)."!@#";
			$pro_info	.= $arrDeliveryType[$prVal->delivery_type]."!@#";
			$pro_info	.= $prVal->delivery_type."!@#";
			$pro_info	.= $prVal->reservation_date."!@#";
			$pro_info	.= $storeData['name']."!@#";
			$pro_info	.= $prVal->use_epoint;


?>
		<!-- 주문상세 테이블 -->
		<div class="order_table mt-15" id="idx_<?=$pr_idx?>" info = "<?=$pro_info?>">
			<table class="th-left">
				<colgroup>
					<col style="width:32.8%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="ordered_goods">
								<a href="#">
									<div class="img"><img src="<?=$file?>" alt=""></div>
									<div class="info">
										<p class="brand"><?=$prVal->brandname?></p>
										<p class="name"><?=$prVal->productname?></p>
									</div>
								</a>
							</div>
						</td>
					</tr>
					<tr>
						<th>품번</th>
						<td><?=$prVal->prodcode?></td>
					</tr>
					<tr>
						<th>옵션</th>
						<td><?=$optStr?></td>
					</tr>
					<tr>
						<th>수량</th>
						<td><?=number_format( $prVal->quantity )?>개</td>
					</tr>
					<tr>
						<th>판매가</th>
						<td>￦ <?=number_format($prVal->price)?></td>
					</tr>
					<tr>
						<th>배송정보</th>

						<td>
							<?if($prVal->delivery_type == '0'){	?>
							<div class="delivery_info">
								<span class="tit">[택배수령]</span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>본사물류에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?} else if($prVal->delivery_type == '2'){?>
							<div class="delivery_info">
								<span class="tit"><?=$arrDeliveryType[$prVal->delivery_type]?></span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?} else {?>
							<div class="delivery_info">
								<span class="tit"><?=$arrDeliveryType[$prVal->delivery_type]?></span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($prVal->delivery_type == '1'){?>
											<div class="container"><p><?=$prVal->reservation_date?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </p></div>
											<?}else if($prVal->delivery_type == '3'){?>
											<div class="container"><p>선택하신 매장을 방문하여 입어보고 수령하시면 됩니다. <br>(재고가 있을 경우 : 당일~3일 이내 방문수령 / 재고가 없을 경우 : 3일~5일 이내 방문수령)</p></div>
											<?}?>
											
										</div>
									</div>
								</div>
								<p class="name"><?=$storeData['name']?></p>

								<?if($prVal->delivery_type == '1'){?>
								<p class="price"><?=$prVal->reservation_date?></p>
								<?}?>
								<a href="javascript:store_map('<?=$prVal->store_code?>');" class="btn_store_info btn-basic">매장안내</a>
							</div>
							<?} ?>
						</td>


						<!--  
						<td>
							<?if($prVal->delivery_type == '1' || $prVal->delivery_type == '3'){	?>
							<div class="delivery_info">
								<span class="tit"><?=$arrDeliveryType[$prVal->delivery_type]?></span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($prVal->delivery_type == '1'){?>
											<div class="container"><p><?=$prVal->reservation_date?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </p></div>
											<?}else if($prVal->delivery_type == '3'){?>
											<div class="container"><p>선택하신 매장을 방문하여 입어보고 수령하시면 됩니다. <br>(재고가 있을 경우 : 당일~3일 이내 방문수령 / 재고가 없을 경우 : 3일~5일 이내 방문수령)</p></div>
											<?}?>
											
										</div>
									</div>
								</div>
								<p class="name"><?=$storeData['name']?></p>

								<?if($prVal->delivery_type == '1'){?>
								<p class="price"><?=$prVal->reservation_date?></p>
								<?}?>
								<a href="javascript:store_map('<?=$prVal->store_code?>');" class="btn_store_info btn-basic">매장안내</a>
							</div>
							<?}else if($prVal->delivery_type == '2'){?>
							<div class="delivery_info">
								<span class="tit">[매장발송]</span>
								
							</div>
							<?}else{?>
							<div class="delivery_info">
								<span class="tit">[택배수령]</span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?}?>
						</td>
///////////////////////////////////////////////////////////////////////////////// 2차 수정
						
						<td>
							<?if($prVal->delivery_type == '0' || $prVal->delivery_type == '2'){	?>
							<div class="delivery_info">
								<span class="tit">[택배수령]</span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?} else {?>
							<div class="delivery_info">
								<span class="tit"><?=$arrDeliveryType[$prVal->delivery_type]?></span>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($prVal->delivery_type == '1'){?>
											<div class="container"><p><?=$prVal->reservation_date?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </p></div>
											<?}else if($prVal->delivery_type == '3'){?>
											<div class="container"><p>선택하신 매장을 방문하여 입어보고 수령하시면 됩니다. <br>(재고가 있을 경우 : 당일~3일 이내 방문수령 / 재고가 없을 경우 : 3일~5일 이내 방문수령)</p></div>
											<?}?>
											
										</div>
									</div>
								</div>
								<p class="name"><?=$storeData['name']?></p>

								<?if($prVal->delivery_type == '1'){?>
								<p class="price"><?=$prVal->reservation_date?></p>
								<?}?>
								<a href="javascript:store_map('<?=$prVal->store_code?>');" class="btn_store_info btn-basic">매장안내</a>
							</div>
							<?} ?>
						</td>
						-->
					</tr>
					<tr>
						<th>상태</th>
						<td>
							<?
								$_ord_oi_step1	= $_ord->oi_step1;
							
								if($prVal->op_step=="3" && $prVal->deli_closed){
									$status_name="배송완료";
								}else{
									$status_name=GetStatusOrder("p", $_ord_oi_step1, $_ord->oi_step2, $prVal->op_step, $prVal->redelivery_type, $prVal->order_conf);
								}
								$status_qry="";
								if($status_name=="환불접수" || $status_name=="환불완료"){
									list($status_sold)=pmysql_fetch("select count(*) from tblorderproduct_log where ordercode='".$ordercode."' and idx='".$pr_idx."' and step_next in ('41','44') and reg_type in ('api','admin')");

								}
								if ($_ord->oi_step1 > 2 && $prVal->op_step >=40 && $prVal->deli_num =='') $_ord_oi_step1 =2;
							?>
							<?=$status_name?></span><?if($status_sold){echo "(품절)";}?><br>
							<?if( $prVal->op_step == 3 ){ // 배송중일 경우?>
							<a href="javascript:;" class="btn-line ml-5 CLS_delivery_tracking"  urls = "<?=$delicomlist[$prVal->deli_com]->deli_url.$prVal->deli_num?>">배송추적</a>
							<?}?>						
						</td>
					</tr>
					<tr>
						<th>취소/확정/리뷰</th>
						<td>
							<div class="decision">
								<? if ($prVal->op_step < 40  && $_ord->paymethod[0]!='Q') { //주문취소 신청및 완료상태가 아닌경우
									if( $prVal->op_step == 1/*  || $prVal->op_step == 2*/){ // 입금완료, 배송준비일 경우
										if ($op_cnt == 1 || ( $_ord->paymethod[0] == "M" && $_ord->paymethod[1] == "E" ) ) {
											// 주문상품이 한개일경우 전체 취소로 한다.
											// 또는 다날 휴대폰 결제인 경우도 전체 취소로 한다. (부분취소 방법을 아직 알 수 없어서 이렇게 함)
											echo "-";
										} else {
							?>
										<a href="javascript:;" class="btn-basic basic2 ord_cancel" ordercode = "<?=$ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" pg_ordercode = "<?=$pg_ordercode?>" paymethod="<?=$_ord->paymethod[0]?>">주문취소</a>

							<?
										}
									} else if( $prVal->op_step == 3 ){ // 배송중일 경우
							?>
										<a href="javascript:;" class="btn-basic ord_regoods" ordercode = "<?=$ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" pg_ordercode = "<?=$pg_ordercode?>" paymethod="<?=$_ord->paymethod[0]?>">반품</a>
										<a href="javascript:;" class="btn_exchange btn-line ord_change" ordercode = "<?=$ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" pg_ordercode = "<?=$pg_ordercode?>" paymethod="<?=$_ord->paymethod[0]?>">교환</a>
										<a href="javascript:;" class="btn-point deli_ok" ordercode = "<?=$ordercode?>" idx = "<?=$pr_idx?>" pg_ordercode = "<?=$pg_ordercode?>"  pc_type="PART" paymethod="<?=$_ord->paymethod[0]?>">구매확정</a>
							<?
									} else if(  $prVal->op_step == 4) { //배송완료일 경우
										//if ($prVal->order_conf =='1') { // 구매확정인 경우
							?>
										<a href="javascript:;" class="btn-line"  onClick="javascript:document.location.href='<?=$Dir.MDir?>productdetail.php?productcode=<?=$prVal->productcode?>'">리뷰작성</a>
							<?
									} else {
										echo "-";
									}
								} else if($_ord->paymethod[0]!='Q') {
									//echo "...";
									if($prVal->op_step == "40" && $_ord->oi_step1 == "3") {
										//echo "-"."/".$_ord->oi_step1."/".$_ord->oi_step2."/".$prVal->op_step."/".$prVal->redelivery_type."/".$prVal->order_conf;
							?>
										<a href="javascript:;" class="btn_review_write btn-line ord_req_cancel"  ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>"  idx = "<?=$pr_idx?>" oc_no ="<?=$prVal->oc_no?>">신청철회</a>
							<?
									} else {
										echo "-";
									}

								} else if( $_ord->paymethod[0]=='Q' && $prVal->op_step == 4){
							?>
									<a href="javascript:;" class="btn-line"  onClick="javascript:document.location.href='<?=$Dir.MDir?>productdetail.php?productcode=<?=$prVal->productcode?>'">리뷰작성</a>
							<?
								}else{
									echo "-";
								}
							?>

							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //주문상세 테이블 -->
		<?php
				if ($prVal->op_step >= 40) $can_cnt++;
		}
?>
		<div class="btn_area mt-20 mr-10 ml-10">
			<span class="point-color">※ 매장발송 주문건의 경우 매장재고의 변동으로 인해 발송지연 및 주문취소가 될수 있는 점 넓은 마음으로 양해를 부탁드립니다.</span>
		</div>
		<div class="btn_area mt-20 mr-10 ml-10">
			<ul class="ea2 dib_type">
<?
		if ($_ord->oi_step1 < 2 && $can_cnt == 0 && $op_step_cnt == 0) {
			if ($_ord->oi_step1 == 0) {
				$add_text			= "취소";
				$add_class		= " ord_receive_cancel";
			} else {
				$add_text			= "환불";
				$add_class		= " ord_cancel";
			}
?>

				<!-- [D] 주문 상태: 주문접수, 결제완료인 경우에만 노출 -->
				<li><a href="javascript:;" class="btn-line h-input
				btn-def<?=$add_class?>"
				ordercode = "<?=$ordercode?>"
				pg_ordercode = "<?=$pg_ordercode?>" 
				idxs = "<?=$pr_idxs?>"
				pc_type="ALL"
				paymethod="<?=$_ord->paymethod[0]?>"
				price="<?=number_format($_ord->price)?>" 
				dcprice="<?=number_format($_ord->dc_price-$_ord->reserve )?>" 
				deliprice="<?=number_format($_ord->point +$_ord->deli_price)?>" 
				productname="<?=$prVal->productname?>" 
				option="<?=$optStr?>" 
				quantity="<?=number_format($prVal->option_quantity)?>" 
				prodcutcode="<?=$prVal->productcode?>" 
				>전체주문<?=$add_text?></a></li>
				<!-- //[D] 주문 상태: 주문접수, 결제완료인 경우에만 노출 -->
<?
		}
?>
	
				<li><a href="javascript:history.back();" class="btn-point h-input">목록</a></li>
			</ul>
		</div>

		<!-- 할인 및 결제정보 -->
		<div class="order_table mt-25">
			<h3 class="cart_tit">할인 및 결제정보</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:32.8%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>총 상품금액</th>
						<td>￦ <?=number_format($_ord->price)?></td>
					</tr>
					<tr>
						<th>포인트 사용</th>
						<td><span class="point-color">- <?=number_format($_ord->reserve)?> P</span></td>
					</tr>
					<tr>
						<th>E포인트 사용</th>
						<td><span class="point-color">- <?=number_format($_ord->point)?> P</span></td>
					</tr>
					<tr>
						<th>쿠폰할인</th>
						<td><span class="point-color">- ￦ <?=number_format($_ord->dc_price)?></span></td>
					</tr>
					<tr>
						<th>배송비</th>
						<td><?=($_ord->deli_price > 0)?"￦ ".number_format($_ord->deli_price):"무료"?></td>
					</tr>
					<tr>
						<th>실 결제금액</th>
						<td><strong class="point-color">￦ <?=number_format($_ord->price-$_ord->dc_price-$_ord->reserve-$_ord->point+$_ord->deli_price)?></strong></td>
					</tr>
					<tr>
						<th>적립예정포인트</th>
						<td><?=number_format($reserve_point)?>P<p class="msg_sm mt-5">(구매확정 시 적립예정 포인트가 지급됩니다.)</p></td>
					</tr>
					<tr>
						<th>결제방법</th>
						<td>
<?
	if(strstr("VCPMY", $_ord->paymethod[0])) {
		$subject = "결제일자";
		$o_year = substr($ordercode, 0, 4);
		$o_month = substr($ordercode, 4, 2);
		$o_day = substr($ordercode, 6, 2);
		$o_hour = substr($ordercode, 8, 2);
		$o_min = substr($ordercode, 10, 2);
		$o_sec = substr($ordercode, 12, 2);

		$msg = $o_year."-".$o_month."-".$o_day." ".$o_hour.":".$o_min.":".$o_sec;
	} else if (strstr("BOQ", $_ord->paymethod[0])) {
		$_ord_pay_data = explode(" ", $_ord->pay_data);
		if ($_ord->bank_date >= 14) {
			$o_year = substr($_ord->bank_date, 0, 4);
			$o_month = substr($_ord->bank_date, 4, 2);
			$o_day = substr($_ord->bank_date, 6, 2);
			$o_hour = substr($_ord->bank_date, 8, 2);
			$o_min = substr($_ord->bank_date, 10, 2);
			$o_sec = substr($_ord->bank_date, 12, 2);

			$bank_date_msg = $o_year."-".$o_month."-".$o_day." ".$o_hour.":".$o_min.":".$o_sec;
		}
		if(strstr("B", $_ord->paymethod[0])){
			$subject=$_ord->pay_data;
			/*
			$subject = "입금자명";
			$subject2 = "입금은행";
			$subject3 = "입금계좌";
			$msg = $_ord->bank_sender;
			$msg2 = $_ord_pay_data[0];
			$msg3 = $_ord_pay_data[1].' '.$_ord_pay_data[2];
			if ($bank_date_msg) {
				$subject4	= "입금확인";
				$msg4		= $bank_date_msg;
			}*/
		}else{
			$subject = "입금은행";
			$subject2 = "입금계좌";
			$msg = $_ord_pay_data[0];
			$msg2 = $_ord_pay_data[1].' '.$_ord_pay_data[2];
			if ($bank_date_msg) {
				$subject3	= "입금확인";
				$msg3		= $bank_date_msg;
			} else {
				if($_ord->pay_flag=="0000"){
					$subject3 = "입금확인";
					$msg3 = "입금 대기중";
				}
			}
		}
	}

	if ($_ord->receiver_addr) {
		$_ord_receiver_addr	= $_ord->receiver_addr;
		$_ord_receiver_addr	= str_replace("우편번호 :","(",$_ord_receiver_addr);
		$_ord_receiver_addr	= str_replace("주소 :",")",$_ord_receiver_addr);
	}
?>
						<?if($_ord->paymethod[0]!="B"){echo $arpm[$_ord->paymethod[0]]."<br>";}?>
						<?if($subject){?>
						<?=$subject?>: <?=$msg?>
						<?}?>
						<?if($subject2){?>
						<br> <?=$subject2?>: <?=$msg2?>
						<?}?>
						<?if($subject3){?>
						<br> <?=$subject3?>: <?=$msg3?>
						<?}?>
						<?if($subject4){?>
						<br> <?=$subject4?>: <?=$msg4?>
						<?}?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //할인 및 결제정보 -->

		<!-- 배송지 정보 -->
		<div class="order_table">
			<h3 class="cart_tit">
				배송지 정보
				<!-- [D] 주문 상태: 주문접수, 결제완료인 경우에만 노출 -->
				<!--<a href="javascript:;" class="btn_change_addr btn-line">배송지변경</a>-->
				<!-- //[D] 주문 상태: 주문접수, 결제완료인 경우에만 노출 -->
			</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:32.8%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>받는사람</th>
						<td><?=$_ord->receiver_name?></td>
					</tr>
					<tr>
						<th>휴대전화</th>
						<td><?=$_ord->receiver_tel2?></td>
					</tr>
					<tr>
						<th>전화번호(선택)</th>
						<td><?=$_ord->receiver_tel1?></td>
					</tr>
					<tr>
						<th>주소</th>
						<td>
							<?if($_ord->deli_type == "2"){ echo "해당 주문은 고객 [직접수령] 입니다"; } else { echo $_ord_receiver_addr; }?>
						</td>
					</tr>
					<tr>
						<th>배송 요청사항</th>
						<td>
							<?if($_ord->order_msg2){?>
								<?=$_ord->order_msg2?>
							<?}else{?>
								-
							<?}?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //배송지 정보 -->
	</section><!-- //.orderlist_view -->

</main>
<!-- //내용 -->

<script>
// 셀렉트 탭
$(window).ready(function(){

	var blockNum = 4;
	var showNum = "";
	$('.tab-select').on('change', function(){
/*			if($(this).children('option:selected').index() == blockNum)
		{
			$('.parcel-wrap').addClass('on');
		}else{
			$('.parcel-wrap').removeClass('on');
		}
*/
		var val = $(this).val();

		if(showNum == ""){
			showNum = val;
			$('.chk_sub_code_'+val).show();
		}else{
			$('.chk_sub_code_'+val).show();
			$('.chk_sub_code_'+showNum).hide();
			showNum = val;
		}

	});

	//택배비 셋팅
	$("input[name=return_deli_type]").change(function() {
		var val = $(this).val();
		if(val == "1" || val == "3"){
			$("input[name=return_deli_price]").val("5000");
		}else if(val == "2"){
			$("input[name=return_deli_price]").val("2500");
		}else{
			$("input[name=return_deli_price]").val("0");
		}
	});

});
</script>

<? include_once('outline/footer_m.php'); ?>
