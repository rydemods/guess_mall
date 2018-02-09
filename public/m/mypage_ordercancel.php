<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	echo ("<script>location.replace('/m/login.php');</script>");
	exit;
}

//PG key 값 가져오기
$_ShopInfo->getPgdata();
$pgid_info=GetEscrowType($_data->card_id);
if(strlen($_MShopInfo->getMemid()) > 0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_mdata=$row;
		if($row->member_out=="Y") {
			$_MShopInfo->SetMemNULL();
			$_MShopInfo->Save();
			alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
		}

		if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
			$_MShopInfo->SetMemNULL();
			$_MShopInfo->Save();
			alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
		}
	}
	pmysql_free_result($result);
}

$mode=$_REQUEST['mode'];
$ordercode=$_REQUEST['ordercode'];
$pc_type=$_REQUEST['pc_type'];
$idx=$_REQUEST['idx'];
$idxs=$_REQUEST['idxs'];
if ($mode == 'cancel') $re_type		= "";
if ($mode == 'regoods') $re_type		= "B";
if ($mode == 'rechange') $re_type		= "C";

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

    if($row->oi_step1 == "2") {
        echo "<html></head><body onload=\"alert('주문이 배송중비중 상태입니다.'); window.location.replace('mypage_orderlist_view.php?ordercode=$ordercode');\"></body></html>";
        exit;
    }
} else {
	echo "<html></head><body onload=\"alert('오류발생,관리자에게 문의해주세요'); location.href='/m/'\"></body></html>";
	exit;
}
pmysql_free_result($result);

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

if($idx != '') $add_qry	= " AND a.idx = '{$idx}' ";
if($idxs != '') $add_qry	= " AND a.idx IN ('".str_replace("|", "','", $idxs)."') ";

#주문상품
$sql = "SELECT 
				a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
				a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
				a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
				b.minimage, a.option_type, a.option_price, a.option_quantity, 
				a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
				a.deli_date, a.receive_ok, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
				a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice, pb.brandname, a.use_point, b.option1_tf, option2_tf, option2_maxlen,
				a.delivery_type, a.store_code, a.reservation_date, b.prodcode, b.colorcode
			FROM 
				tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
			WHERE 
				a.ordercode='".$ordercode."' {$add_qry}
			ORDER BY vender ASC, productcode ASC ";

if ($mode == 'rechange') {
	$erp_result=pmysql_query($sql,get_db_conn());
	while($erp_row=pmysql_fetch_object($erp_result)) {
		if ($erp_row->prodcode !='' && $erp_row->colorcode !='') {
			//ERP 상품의 사이즈 수량정보를 쇼핑몰에 업데이트한다.
			getUpErpSizeStockUpdate($erp_row->productcode, $erp_row->prodcode, $erp_row->colorcode);
		}
	}
	pmysql_free_result($erp_result);
}

$result=pmysql_query($sql,get_db_conn());

while($row=pmysql_fetch_object($result)) {
	$i++;
	$isnot=false;
	$tmpPrice = 0;
	$tmpQuantity = 0;
	$reserve_point = 0; // 총 적립금

	# 상품정보
	$orProduct[$row->idx] = (object) array(
		'vender' => $row->vender,
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
		'redelivery_type' => $row->redelivery_type,
		'redelivery_date' => $row->redelivery_date,
		'redelivery_reason' => $row->redelivery_reason,
		'delivery_type' => $row->delivery_type,
		'store_code' => $row->store_code,
		'reservation_date' => $row->reservation_date
	);	
	
	if ($pc_type == "PART") {
		//배송비로 인한 보여지는 가격 재조정
		$row->deli_price	= 0;
		list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$row->productcode."%'"));

		if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
			// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
			list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$row->idx."' and op_step < 40 limit 1"));
			if (!$op_idx) { // 상품이 없으면
				$row->deli_price	= $od_deli_price;
			}
		}
	}

	if ($orvender[$row->vender]['t_pro_count'] == '') {
		$orvender[$row->vender]['t_pro_count']	= 1; // 벤더 상품수
		$orvender[$row->vender]['t_pro_price']	= ($row->price + $row->option_price) * $row->option_quantity; // 벤더 총 주문금액
		$orvender[$row->vender]['t_deli_price']	= $row->deli_price; // 벤더 총 배송비
	} else {
		$orvender[$row->vender]['t_pro_count']	= $orvender[$row->vender]['t_pro_count'] + 1; // 벤더 상품수
		$orvender[$row->vender]['t_pro_price']	= $orvender[$row->vender]['t_pro_price'] + (($row->price + $row->option_price) * $row->option_quantity); // 벤더 총 주문금액
		$orvender[$row->vender]['t_deli_price']	= $orvender[$row->vender]['t_deli_price'] + $row->deli_price; // 벤더 총 배송비
	}

	$reserve_point += $row->reserve;
}
pmysql_free_result($result);
?>
<SCRIPT LANGUAGE="JavaScript">

$(document).ready(function(){
	var showNum = "";
			//사유 서브 메뉴
			$("#b_sel_code, #c_sel_code").on('change', function(){
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
			$("#return_deli_type").change(function() {	
				var val = $(this).val(); 
				if(val == "1"){
					$("input[name=return_deli_price]").val("5000");
					$("#tr_return_deli_memo").hide();
				}else if(val == "2"){
					$("input[name=return_deli_price]").val("2500");
					$("#tr_return_deli_memo").hide();
				}else if(val == "3"){
					$("input[name=return_deli_price]").val("5000");
					$("#tr_return_deli_memo").show();
				}else{
					$("input[name=return_deli_price]").val("0");
					$("#tr_return_deli_memo").hide();
				}			
			});
	

	// 취소/반품/교환 요청시
	$('.refundSubmit').click(function(){
		var re_type			= $('input[name=re_type]').val();
		var ordercode		= $('input[name=ordercode]').val();
		var idx				= $('input[name=idx]').val();
		var pc_type		= $('input[name=pc_type]').val();
		var paymethod	= $('input[name=paymethod]').val();

		//배송지 정보
		var receipt_name = "<?=$_ord->receiver_name?>";
		var receipt_tel =  "<?=$_ord->receiver_tel1?>";
		var receipt_mobile = "<?=$_ord->receiver_tel2?>";
		var receipt_addr = $("#receipt_addr").val();

		if (re_type == '') {
		var alert_text		= "취소";
		} else if (re_type == 'B') {// 반품
			alert_text		= "반품접수";
		} else if (re_type == 'C') {//교환
			alert_text		= "교환접수";
		}
		if (re_type == '' || re_type == 'B') { // 취소, 반품일 경우
			var idxs				= $('input[name=idxs]').val();
			var each_price	= $('input[name=each_price]').val();
			var sel_code		= $('select[name=b_sel_code]').val();
			var memo			= $('textarea[name=memo]').val();

			var bankcode				= $('select[name=bankcode]').val();
			var bankuser				= $('input[name=bankuser]').val();
			var bankaccount			= $('input[name=bankaccount]').val();
			var bankusertel			= $('input[name=bankusertel]').val();

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
			return_deli_type				= $('#return_deli_type').val();
			return_deli_memo			= $('input[name=return_deli_memo]').val();

			if(sel_code == 0 || sel_code == ''){
				alert("사유를 선택해 주세요.");
				return;
			}

			if(memo == ''){
				alert("상세사유를 입력해 주세요.");
				$('textarea[name=memo]').focus();
				return;
			}

			if(re_type == 'B'){
				if(return_deli_type == ""){
					alert("택배비 발송 종류를 선택해 주세요.");
					 $('#return_deli_type').focus();
					return
				}
			}	

			if (paymethod != 'C' && paymethod != 'M' && paymethod != 'V' && paymethod != 'Y' && paymethod != 'G') // 반품시 결제방식이 카드, 핸드폰, 계좌이체, PAYCO, 임직원 포인트 결제가 아닌경우
			{
				if(bankcode==0 || bankcode=='') {
					alert("환불받으실 은행을 선택해 주세요.");
					return;
				}
				
				if(bankaccount=='') {
					alert("환불받으실 계좌번호를 입력해 주세요.");
					$('input[name=bankaccount]').focus();
					return;
				}
				
				if(bankuser=='') {
					alert("환불받으실 예금주를 입력해 주세요.");
					$('input[name=bankuser]').focus();
					return;
				}

				if(bankusertel=='') {
					alert("연락처를 입력해 주세요.");
					$('input[name=bankusertel]').focus();
					return;
				}
			}

			var sel_option1		= "";
			var sel_option2		= "";

		} else if (re_type == 'C') { // 교환일 경우
			//var sel_code		= $('input[name=c_sel_code]:checked').val();
			var sel_code		= $('select[name=c_sel_code]').val();
			var memo			= $('textarea[name=memo]').val();
			var bankcode		= 0;
			var bankuser				= "";
			var bankaccount			= "";
			var bankusertel			= "";

			var sel_sub_code			= "";
			var return_deli_price		= 0;
			var return_deli_receipt		= "";
			var return_deli_type			= "";
			var return_deli_memo		= "";

			$("input[name=c_sel_sub_code]:checked").each(function(index){
				if(sel_sub_code == '')
					sel_sub_code = $(this).val();
				else
					sel_sub_code += "|" + $(this).val();
			});

			console.log(sel_sub_code);

			return_deli_price				= $('input[name=return_deli_price]').val();
			if(return_deli_price == '') return_deli_price = 0;
			return_deli_receipt			= $('input[name=return_deli_receipt]').val();
			return_deli_type				= $('#return_deli_type').val();
			return_deli_memo			= $('input[name=return_deli_memo]').val();

			var sel_option1		= $('input[name=option1]').val();
			var sel_option2		= "";
			var sel_option_price_text		= "";
			var sel_text_opt_s		= $('input[name=text_opt_s]').val();
			var sel_text_opt_c		= "";
			var sel_option_chk	= "Y";
			
			// 필수 체크
			$(".opt_chk").each(function(){
				if($(this).attr('alt') == 'essential' && $(this).val() == '') {
					sel_option_chk = "N";
				}
			});

			if (sel_option_chk == "N")	{
				alert("옵션을 선택 및 입력해 주세요.");
				return;
			} else {
				if ($('.opt_sel').length > 0) {
					$(".opt_sel").each(function(){
						var option_code		= $(this).val();
						var option_code_arr	= option_code.split("|!@#|");
						if (sel_option2 == '') {
							sel_option2		= option_code_arr[0];
							sel_option_price_text = option_code_arr[1];
						} else {
							sel_option2		+= chr(30)+option_code_arr[0];
							sel_option_price_text += "||"+option_code_arr[1];
						}
					});
				}
				if ($('.opt_text').length > 0) {
					$(".opt_text").each(function(){
						if (sel_text_opt_c == '') {
							sel_text_opt_c	= $(this).val();
						} else {
							sel_text_opt_c	+= "@#"+$(this).val();
						}
					});
				}
			}

			if(sel_code == 0 || sel_code == ''){
				alert("사유를 선택해 주세요.");
				return;
			}

			if(memo == ''){
				alert("상세사유를 입력해 주세요.");
				$('textarea[name=memo]').focus();
				return;
			}

			if(return_deli_type == ""){
				alert("택배비 발송 종류를 선택해 주세요.");
				 $('#return_deli_type').focus();
				return
			}
		}		

		if (re_type == '' && (paymethod =='C' || paymethod =='M' || paymethod =='V' || paymethod =='G')) { // 카드, 휴대폰, 계좌이체, 임직원 포인트 결제일 경우
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

		/*alert(
			"re_type : "+re_type+"\n"
			+"ordercode : "+ordercode+"\n"
			+"idx : "+idx+"\n"
			+"idxs : "+idxs+"\n"
			+"paymethod : "+paymethod+"\n"
			+"sel_code : "+sel_code+"\n"
			+"memo : "+memo+"\n"
			+"bankcode : "+bankcode+"\n"
			+"bankaccount : "+bankaccount+"\n"
			+"bankuser : "+bankuser+"\n"
			+"each_price : "+each_price+"\n"
			+"sel_option1 : "+sel_option1+"\n"
			+"sel_option2 : "+sel_option2+"\n"
			+"sel_option_price_text : "+sel_option_price_text+"\n"
			+"sel_text_opt_s : "+sel_text_opt_s+"\n"
			+"sel_text_opt_c : "+sel_text_opt_c);return;*/

        /* 취소(환불) 요청시..*/ 
        var reload = 0;
        if(re_type == '') {
            //alert("re_type = "+re_type);
            //alert("idxs = "+idxs);

            // wms 상태값 가져오기 추가..2016-11-29
            // 결제완료 상태에서 취소(환불) 요청시, 그 사이에 배송준비중으로 바뀌었을수 있으므로 체크.
            $.ajax({
                url : '<?=$Dir.FrontDir?>mypage_order_check.ajax.php',
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
                    window.location.replace('mypage_orderlist_view.php?ordercode=<?=$ordercode?>');
                }
            });
        }
        if(reload) return;
        /**/

        if(confirm(alert_text+'를 하시겠습니까?')){
            if (re_type == '' && (paymethod =='C' || paymethod =='V')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
                <?php if($pg_type=="G"){?>
                var sitecd = '<?=$pgid_info["ID"]?>';
                var sitekey = '<?=$pgid_info["KEY"]?>';
                var sitepw = "<?=$pgid_info['PW']?>";	
                $.post("<?=$Dir?>paygate/<?=$pg_type?>/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, sitepw:sitepw, ordercode:ordercode, pc_type:pc_type,mod_mny:each_price},function(data){
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
                                window.location.replace('mypage_orderlist_view.php?ordercode=<?=$ordercode?>');
                            }
                        },"json");
                    } else {
                        alert(data.msg);
                    }
                },"json");
                <?}?>
            } else {
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
                        window.location.replace('mypage_orderlist_view.php?ordercode=<?=$ordercode?>');
                    }
                },"json");
            }
        }
	});

});

function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
}
//2차 옵션
function option_change(productcode, option_depth, option_totalDepth, option_code) {	

	var sel_option	="<option value=''>============선택============</option>";

	for (var i=option_depth; i < option_totalDepth; i++)
	{
		$("select[name='sel_option"+i+"']").find("option").remove();
		$("select[name='sel_option"+i+"']").append(sel_option);
	}
	if (option_code != '')
	{
		option_code_arr	= option_code.split("|!@#|");
		$.ajax({
			type: "POST",
			url: "ajax.product_option.php",
			data: "productcode="+productcode+"&option_code="+option_code_arr[0]+"&option_depth="+option_depth,
			dataType:"JSON",
			success: function(data){
				var sel_option	="";
				var soldout	="";
				var disabled_on = '';
				if (data)
				{
					$.each(data, function(){
						if (this.price > 0) {
							var option_price		= "(+"+jsSetComa(this.price)+"원)";
						} else {
							var option_price		= "";
						}
						if (this.soldout == 1)
						{
							disabled_on = ' disabled';
							soldout = '&nbsp;[품절]';
						} else {
							disabled_on = '';
							soldout = '';
						}
						sel_option += "<option value='"+this.code+"'"+disabled_on+">"+this.code+option_price+soldout+"</option>";
					});
					$("select[name='sel_option"+option_depth+"']").append(sel_option);
				}
			},
			complete: function(data){
			},
			error:function(xhr, status , error){
				alert("에러발생");
			}
		});
	}	
}

//chr(30)처리를 위한 함수
 function chr(code) 
{ 
    return String.fromCharCode(code); 
}
</script>
<?
if ($mode == 'cancel' || $mode == 'regoods') { // 취소/반품일 경우 
	include 'mypage_ordercancel_regoods.php';
} else if ($mode == 'rechange') { // 교환일 경우
	include 'mypage_ordercancel_rechange.php';
}
?>
<? include_once('outline/footer_m.php'); ?>
