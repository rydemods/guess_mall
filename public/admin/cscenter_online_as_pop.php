<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/cscenter_ascode.php");
include("calendar.php");
include("access.php");

$ordercode=$_REQUEST["ordercode"];
$idx=$_REQUEST["idx"];
$no=$_REQUEST["no"];

//경로
$filepath = $Dir.DataDir."shopimages/cscenter/";

$sql="select
c.*,
o.id, o.paymethod, o.ordercode, o.receiver_name, o.receiver_tel2, o.receiver_addr, o.oi_step2, o.oi_step1, o.sender_name as name, o.sender_email as email, o.sender_tel as mobile, o.is_mobile, o.pg_ordercode,
op.deli_gbn,  op.opt2_name, op.op_step, op.redelivery_type, op.order_conf, op.price, op.quantity, op.option_price, op.option_quantity, op.deli_num, op.deli_com, op.coupon_price, op.use_point, op.deli_price,
p.consumerprice, p.tinyimage, op.option_type, p.productname, p.productcode, p.prodcode, p.colorcode, 
pb.brandname, op.opt1_name, op.opt2_name, op.text_opt_subject, op.text_opt_content, op.option_price_text, p.option1, p.option2, p.option1_tf, p.option2_tf, p.option2_maxlen, op.store_code,
s.name as storename
from tblcsasreceiptinfo c
left join tblorderinfo o on(c.as_ordercode=o.ordercode)
left join tblorderproduct op on (o.ordercode=op.ordercode and op.idx=c.as_idx)
left join tblmember m on (o.id=m.id)
left join tbldestination d on (m.id=d.mem_id and base_chk='Y')
left join tblproduct p on(op.productcode=p.productcode)
left join tblproductbrand pb on (p.brand=pb.bridx)
left join tblstore s on (c.receipt_store=s.sno::varchar)
where c.no='".$no."'";

$result=pmysql_query($sql);
$data=pmysql_fetch_array($result);

$pgid_info="";
$pg_type="";
switch ($data['paymethod'][0]) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_shopdata->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_shopdata->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_shopdata->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_shopdata->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);

list($prod_total)=pmysql_fetch_array(pmysql_query("select count(*) as prod_total from tblorderproduct WHERE ordercode='".$data['ordercode']."' "));
$pc_type	= ($prod_total==1)?"ALL":"PART";

//재주문건 생성이후 환불요청시 부분취소로 pg사에 넘겨준다.
$cancel_pc_type=$pc_type;
if($data['ordercode'] != $data['pg_ordercode']) $cancel_pc_type="PART";

$op_idxs	= $data['as_idx'];
$prodcd		= $data['prodcode'];
$colorcd	= $data['colorcode'];

$t_op_total_price	= 0;
//배송비로 인한 보여지는 가격 재조정
$can_total_price	= (($data['price'] + $data['option_price']) * $data['option_quantity']) - ($data['coupon_price'] + $data['use_point']) + $data['deli_price'];

list($od_deli_price, $prod_code)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($data['ordercode'])."' and product LIKE '%".$data['productcode']."%'"));
//echo $od_deli_price;
if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
	// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
	list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($data['ordercode'])."' and productcode in ('".str_replace(",","','", $prod_code)."') and idx != '".$data['as_idx']."' and op_step < 40 limit 1"));
	if ($op_idx) { // 상품이 있으면
		if ($data['deli_price'] > 0) $can_total_price	= $can_total_price - $od_deli_price;
	}
}

if ($pc_type == 'ALL') {
	$t_op_total_price	+=  (($data['price'] + $data['option_price']) * $data['option_quantity']) - ($data['coupon_price'] + $data['use_point']) + $data['deli_price'];
} else if ($pc_type == 'PART') {
	$t_op_total_price	+=  $can_total_price;
}

$op_store_code	= $data['store_code'];

$option1	 = $data['opt1_name'];
$option2	 = $data['opt2_name'];

if( strlen( trim( $data['opt1_name'] ) ) > 0 ) {
	$opt1_name_arr	= explode("@#", $data['opt1_name']);
	$opt2_name_arr	= explode(chr(30), $data['opt2_name']);
}

if( strlen( trim( $data['text_opt_subject'] ) ) > 0 ) {
	$text_opt_subject_arr	= explode("@#", $data['text_opt_subject']);
	$text_opt_content_arr	= explode("@#", $data['text_opt_content']);
}

$op_option1		= $data['opt1_name'];
$op_option2		= $data['opt2_name'];
$op_text_opt_s	= $data['text_opt_subject'];
$op_text_opt_c	= $data['text_opt_content'];

if ($data['option1'] !='' || $data['option2'] != '') {

	//변경옵션 관련
	$change_option_html	= '';
	if ($data['option1']) {
		$option1_arr	= explode("@#", $data['option1']);
		$option1_tf_arr	= explode("@#", $data['option1_tf']);
		$option1_cnt	= count($option1_arr);
		if ($data['option_type'] == '0') {							// 조합형
			//$option_arr		= get_option( $data['productcode );
		} else if ($data['option_type'] == '1') {					// 독립형
			$option_arr		= get_alone_option( $data['productcode'] );
		}
		
		for($s=0;$s < sizeof($option1_arr);$s++) {
			$sel_est			= "essential";
			//$sel_est_text	= ' <span class="point-color">*</span>'; // 필수
			if ($data['option_type'] == '1' && $option1_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
				$sel_est			= "";
				$sel_est_text	= "";
			}
			$change_option_html	.= '
				<tr>
					<th scope="row">'.($option1_arr[$s]=='SIZE'?'사이즈':$option1_arr[$s]).$sel_est_text.'</th>
					<td>
			';

			if ($data['option_type'] == '0') {							// 조합형
				if (($s + 1) != $option1_cnt){
					$add_opt_onChange	= 'onChange="javascript:option_change(\''.$data['productcode'].'\',\''.($s+1).'\', \''.$option1_cnt.'\', this.value)"';
				} else {
					$add_opt_onChange	= "";
				}
				/*$change_option_html	.= '
						<select name="sel_option'.$s.'" style="min-width:143px;" class="select opt_chk opt_sel"'.$add_opt_onChange.' alt="'.$sel_est.'">
							<option value="">'.($option1_arr[$s]=='SIZE'?'사이즈':$option1_arr[$s]).' 선택</option>
				';*/
				$change_option_html	.= '
						<select name="sel_option'.$s.'" style="min-width:143px;" class="select opt_chk opt_sel"'.$add_opt_onChange.' alt="'.$sel_est.'">
							<option value="">=========선택=========</option>
				';
	
				if ($s == 0) {
					$option_arr		= get_option( $data['productcode'] );
				} else{ 
					$option_arr		= get_option( $data['productcode'] , $opt2_name_arr[$s-1], $s);
				}

				foreach($option_arr as $key => $val) {
					$disabled_on	= "";

					if ($option1_arr[$s] == 'SIZE') {
						if($val['qty'] < 1) {
							$disabled_on = ' disabled';
						} else {
							$disabled_on = '';
						}
						$soldout = "&nbsp;[재고 : ".number_format($val['qty'])."개]";
					} else {

						if ($val['price'] > 0) {
							$option_price		= "(+".number_format($val['price'])."원)";
						} else {
							$option_price		= "";
						}

						if($val['soldout'] == 1) {
							$disabled_on = ' disabled';
							$soldout = '&nbsp;[품절]';
						} else {
							$disabled_on = '';
							$soldout = '';
						}
					}

					$change_option_html	.= '
							<option value="'.$val['code'].'|!@#|'.$val['price'].'"'.$disabled_on.($opt2_name_arr[$s] == $val['code']?" selected":"").'>'.$val['code'].$option_price.$soldout.'</option>
					';
				}

				$change_option_html	.= '			
						</select>
				';

			} else if ($data['option_type'] == '1') {					// 독립형

				/*$change_option_html	.= '		
						<select name="sel_option'.$s.'" style="min-width:143px;" class="select opt_chk opt_sel" alt="'.$sel_est.'">
							<option value="">'.$option1_arr[$s].' 선택</option>
				';*/
				
				$change_option_html	.= '		
						<select name="sel_option'.$s.'" style="min-width:143px;" class="select opt_chk opt_sel" alt="'.$sel_est.'">
							<option value="">=========선택=========</option>
				';
				
				$oa_cnt	= 0;
				foreach($option_arr[$option1_arr[$s]] as $key => $val) {	
					$option_code_arr		= explode( chr(30), $val->option_code);
					$option_code			= $option_code_arr[1];
					if ($val->option_price > 0) {
						$option_price		= " (+".number_format($val->option_price)."원)";
					} else {
						$option_price		= "";
					}

					$change_option_html	.= '		
							<option value="'.$option_code.'|!@#|'.$val->option_price.'"'.($opt2_name_arr[$s] == $option_code?" selected":"").'>'.$option_code.$option_price.'</option>
					';

					$oa_cnt++;
				}	

				$change_option_html	.= '			
						</select>
				';
			}

			$change_option_html	.= '
					</td>
				</tr>
			';
		}
	}

	if ($data['option2']) {
		$option2_arr				= explode("@#", $data['option2']);
		$option2_cnt				= count($option2_arr);

		$option2_tf_arr				= explode("@#", $data['option2_tf']);
		$option2_maxlen_arr	= explode("@#", $data['option2_maxlen']);

		$text_opt_content_arr	= explode("@#", $data['text_opt_content']);

		for($s=0;$s < sizeof($option2_arr);$s++) {
			$sel_est			= "essential";
			//$sel_est_text	= " *필수";
			if ($option2_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
				$sel_est			= "";
				$sel_est_text	= "";
			}

			$change_option_html	.= '
				<tr>
					<th>'.$option2_arr[$s].'</th>
					<td><input name="text_option'.$s.'" style="width:500px;" value="'.$text_opt_content_arr[$s].'" size="45" maxlength="'.$option2_maxlen_arr[$s].'" type="text" class="opt_chk opt_text" alt="'.$sel_est.'"></td>
				</tr>
			';
		}
	}
}










#로그 정보 쿼리
$log_sql="select * from tblcsaslog where receipt_no='".$no."' order by regdt";
$log_result=pmysql_query($log_sql);

#매장정보 가져오기
$store_sql="SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc";
$store_result=pmysql_query($store_sql);
$storelist=array();
while($store_data=pmysql_fetch_array($store_result)){
	$store_array[]=$store_data;
	$storelist[$store_data['store_code']]=$store_data;
}

# 배송업체를 불러온다.
$del_sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$del_result=pmysql_query($del_sql,get_db_conn());
$delicomlist=array();
while($del_data=pmysql_fetch_object($del_result)) {
	$delicomlist[trim($del_data->code)]=$del_data;
}

#메모 정보 쿼리
$memo_sql="select * from tblcscentermemo where receipt_no='".$no."' and route_type='onlineas' order by regdt";
$memo_result=pmysql_query($memo_sql);
while($memo_data=pmysql_fetch_array($memo_result)){
	$memo_while[$memo_data["no"]]=$memo_data;

	$file_sql="select * from tblcscenterfile where receipt_no='".$no."' and memo_no='".$memo_data["no"]."' and route_type='onlineas' order by no";
	$file_result=pmysql_query($file_sql);
	while($file_data=pmysql_fetch_array($file_result)){
		$memo_while[$memo_data["no"]]["filename"][$file_data["no"]]=$file_data["filename"];
	}
}

pmysql_free_result($del_result);

#수령지 변경 유무의 따른 주소 정보 변경
if($data["place_type"]=="1"){
	$place_name=$data['place_name'];
	$place_tel=	$data['place_mobile'];
	$address = $data['place_addr'];
	$zonecode	= $data['place_zipcode'];

}else{
	$place_name=$data['receiver_name'];
	$place_tel=	$data['receiver_tel2'];
	$address = str_replace("\n"," ",trim($data['receiver_addr']));
	$address = str_replace("\r"," ",$address);
	$pos=strpos($address,"주소");
	if ($pos>0) {
		$post = trim(substr($address,0,$pos));
		$address = substr($address,$pos+9);
	}
	$post = str_replace("우편번호 : ","",$post);
	$arpost = explode("-",$post);
	$zonecode	= $post;
}

#주문일
$order_date=substr($data['ordercode'],'0','4').'-'.substr($data['ordercode'],'4','2').'-'.substr($data['ordercode'],'6','2');

#접수일
$reci_date=substr($data['regdt'],'0','4').'-'.substr($data['regdt'],'4','2').'-'.substr($data['regdt'],'6','2').' '.substr($data['regdt'],'8','2').':'.substr($data['regdt'],'10','2').':'.substr($data['regdt'],'12','2');

#상품이미지
$product_img = getProductImage($Dir.DataDir.'shopimages/product/', $data['tinyimage']);

#상담가능한 연락처 분리
$cut_astel=explode("-",$data["as_tel"]);

#현금영수증 발행정보에 따른 번호 분리
//소득공제
$so_num="";
$ji_num="";
$ji_disabled="";
$so_disabled="";
if($data["cash_detail_type"]=="1"){
	$so_num=explode("-",$data["cash_detail_num"]);
	$selected["cash_detail_num"][$so_num[0]]="selected";
	$ji_disabled="disabled='disabled'";
//지출증빙용
}else if($data["cash_detail_type"]=="2"){
	$ji_num=explode("-",$data["cash_detail_num"]);
	$so_disabled="disabled='disabled'";
}


#라디오박스 체크
$checked["receipt"][$data["receipt_type"]]="checked"; //접수유형
$checked["repair"][$data["repairs_type"]]="checked"; // 유상 수선비
$checked["depreciation"][$data["depreciation_type"]]="checked"; //감가적용
$checked["cash"][$data["cash_type"]]="checked"; // 현금영수증
$checked["cashcheck"][$data["cash_detail_type"]]="checked"; // 현금영수증 발행정보
$checked["progress"][$data["step_code"]]="checked"; //진행상태
$checked["complete"][$data["complete_type"]]="checked"; //처리내용
$checked["complete_detail"][$data["complete_detail"]]="checked"; //처리내용 상세
$checked["creturn"][$data["c_return"]]="checked";//회성 처리내용
$checked["reviewreturn"][$data["c_reviewreturn"]]="checked"; //심의 회송 처리내용
$checked["returngoods2"][$data["as_return_type"]]="checked"; //as 반품 처리내용

#진행상태에 따른 체크
$detail_qry="select * from tblcsasreceiptdetail where receipt_no='".$no."'";
$detail_result=pmysql_query($detail_qry);
while($detail_data=pmysql_fetch_array($detail_result)){
	if($detail_data["as_code"]!="process_text"){
		$checked[$detail_data["as_code"]]="checked";
		$process_price_num[$detail_data["as_code"]]=$detail_data["process_price"];
	}else{
		$as_process_title=$detail_data["process_title"];
		$as_process_text=$detail_data["process_price"];
	}
}

$selected["receipt_store"][$data["receipt_store"]]="selected";
$selected["as_tel"][$cut_astel[0]]="selected";
$selected["company_name"][$data["complete_delicode"]]="selected";

$chk_mb["0"]="PC";
$chk_mb["1"]="MO";
$chk_mb["2"]="AP";

$color["step_color"][$data["step_code"]]="point-txt bold";

################################노출상태 체크####################################

$display["receipt_type"]="style='display:none'";
$display["cash_type"]="style='display:none'";
$display["complete_type"]="style='display:none'";
$display["return"]="style='display:none'";
$display["repair"]="style='display:none'";
$display["returngoods"]="style='display:none'";
$display["reviewreturn"]="style='display:none'";
$display["outreviewgoods"]="style='display:none'";
$display["outreviewreturn"]="style='display:none'";

//as 진행상태별 활성화 체크
if($as_progress_class[$data["step_code"]]){
	$display[$as_progress_class[$data["step_code"]]]="style='display:block'";
}

//as접수정보 유상수선일 수선비, 현금영수증 창활성화
if($as_receipt_class[$data["receipt_type"]]=="rowon"){
	$display["receipt_type"]="style='display:'";
	//현금영수증 발행일 경우 현금영수증 발행정보 활성화
	if($as_cash_class[$data["cash_type"]]=="cashon"){
		$display["cash_type"]="style='display:'";
	}
}
//as 처리정보에서 기타일 경우 기타 상세처리 활성화
if($as_complete_class[$data["complete_type"]]=="completeon"){
	$display["complete_type"]="style='display:'";
}

#################################################################################
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>온라인 AS 요청서</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="styleSheet" href="/css/common.css" type="text/css">
<link rel="stylesheet" href="/admin/static/css/crm.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script src="../js/jquery.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize();">

<div class="pop_top_title"><p>온라인 AS 요청서</p></div>

<script language="JavaScript">
$(document).ready(function(){
	//접수유형 클릭시 이벤트
	$(".rowon").click(function() { $(".receipt_none").show(function(){
		cachtype=$(':radio[name="cash_type"]:checked').val();
		if(cachtype=="Y"){
			$(".cash_none").show(function(){});
		}else{
			$(".cash_none").hide(function(){});
		}
	}); });
	$(".rowoff").click(function() { $(".receipt_none").hide(function(){}); $(".cash_none").hide(function(){}); });

	//현금영수증 클릭시 이벤트
	$(".cashon").click(function() { $(".cash_none").show(function(){}); });
	$(".cashoff").click(function() { $(".cash_none").hide(function(){}); });

	//처리내용 클릭시이벤트
	$(".completeon").click(function() { $(".complete_none").show(function(){}); });
	$(".completeoff").click(function() { $(".complete_none").hide(function(){}); });

	//진행상태 클릭시 이벤트
	//회송
	$(".return").click(function() {
		$(".return_none").show(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});
	//수선중, 수선완료, 고객발송
	$(".repair").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").show(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});

	//as/반품, 반품처리
	$(".returngoods").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").show(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});

	//심의회송
	$(".reviewreturn").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").show(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});

	//외부심의반품, 반품처리, 반품등록, 로케이션이동
	$(".outreviewgoods").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").show(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});

	//외부심의회송
	$(".outreviewreturn").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").show(function(){});
	});

	$(".progressoff").click(function() {
		$(".return_none").hide(function(){});
		$(".repair_none").hide(function(){});
		$(".returngoods_none").hide(function(){});
		$(".reviewreturn_none").hide(function(){});
		$(".outreviewgoods_none").hide(function(){});
		$(".outreviewreturn_none").hide(function(){});
	});

	//처리결과에따른 display
	$(".endgoods").change(function() {
		var endgoods=$(".endgoods").val();

		if(endgoods=="1" || endgoods=="2"){
			$(".cancelgoods_display").show(function(){});
			$(".changegoods_display").hide(function(){});
		}else if(endgoods=="3"){
			$(".cancelgoods_display").hide(function(){});
			$(".changegoods_display").show(function(){});
		}else{
			$(".cancelgoods_display").hide(function(){});
			$(".changegoods_display").hide(function(){});
		}
	});

	//교환상품 검색방법 선택시
	//$("#changegoods1").click(function() { $(".size_select").removeAttr("disabled"); });
	//$("#changegoods2").click(function() { $(".size_select").attr("disabled",true); });
});

function zip_change(){
	$(".redisplay").toggle(
		function(){
			if($("#place_type").val()=="0") $("#place_type").val("1");
			else $("#place_type").val("0");
		}
	);
}


function filenamein(obj, num){
	$("#filename"+num).html(obj.value);
}

var count=1;
function add(){
	if (count == 5) {
		alert("5개까지만 첨부가 가능합니다.");
	} else {
		var html = "<div>";
		html += "<div class='add-file-cover'>";
		html += "<div id='filename"+count+"'></div>";
		html += "<input type='file' id='add_file' name='file[]' onchange='filenamein(this,"+count+")'>";
		html += "</div>";
		html += "</div>";
		count++;
		$("#add_file_div").append(html);
	}
}

function cach_disabled(num){
	if(num=="1"){
		$(".cashcheck_2").attr("disabled",true);
		$(".cashcheck_1").removeAttr("disabled");
	}else{
		$(".cashcheck_1").attr("disabled",true);
		$(".cashcheck_2").removeAttr("disabled");
	}
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.submit();
}

function as_submit(){
	receiptcheck=$(':radio[name="receipt_type"]:checked').val();
	cachchaeck=$(':radio[name="cash_type"]:checked').val();
	cashdetailcheck=$(':radio[name="cash_detail_type"]:checked').val();

	var sHTML = oEditors.getById["ir1"].getIR();
	$("textarea[name=cs_memo]").val(sHTML);

	if(!$("#receipt_store").val()){
		alert("접수매장을 선택해주세요.");
		return;
	}

	if(receiptcheck=="1" && cachchaeck=="Y"){
		if(cashdetailcheck=="1"){
			if(!$("#cash_detail_tel2").val() || !$("#cash_detail_tel3").val()){
				alert("소득공제용 전화번호를 입력해주세요.");
				return;
			}
		}else{
			if(!$("#cash_detail_num1").val() || !$("#cash_detail_num2").val() || !$("#cash_detail_num3").val()){
				alert("지출증빙용 사업자등록번호를 입력해주세요.");
				return;
			}
		}
	}
	var endgoods					= $(".endgoods").val();
	var now_end_step			= $('input[name=now_end_step]').val();

	$('input[name=re_type]').val("");	
	var re_type_val	= "";

	$('input[name=option2]').val("");
	$('input[name=text_opt_s]').val("");
	$('input[name=text_opt_c]').val("");
	
	if (endgoods =='1') { // 반품요청
		re_type_val	= "";
	} else if (endgoods =='2') { // 반품완료
		re_type_val	= "B";
	} else if (endgoods =='3') { // 교환완료
		re_type_val	= "C";
	}

	$('input[name=re_type]').val(re_type_val);	

	if (endgoods == now_end_step) {
		if(confirm("저장 하시겠습니까?")){
			$("#onlinecsform").submit();
		}
	} else {
		if (endgoods =='2' || endgoods =='3') { // 반품완료

			var re_type				= $('input[name=re_type]').val();
			var ordercode			= $('input[name=ordercode]').val();
			var pg_ordercode			= $('input[name=pg_ordercode]').val();
			var idxs					= $('input[name=idxs]').val();
			var pc_type			= $('input[name=pc_type]').val();
			var cancel_pc_type			= $('input[name=cancel_pc_type]').val();
			var paymethod		= $('input[name=paymethod]').val();
			var each_price		= $('input[name=each_price]').val();
			var sel_code			= "";

			var sel_option1		= "";
			var sel_option2		= "";

			var sel_option_price_text		= '';
			var sel_text_opt_s				= '';
			var sel_text_opt_c				= '';

			var bankcode				= 0;
			var bankuser				= "";
			var bankaccount			= "";
			
			var receipt_name			= $('input[name=place_name]').val();
			var receipt_tel					= $('input[name=receipt_tel]').val();
			var receipt_mobile			= $('input[name=place_mobile]').val();
			var receipt_addr				= $('input[name=place_addr]').val();
			var receipt_post5				= $('input[name=place_zipcode]').val();

			var sel_sub_code			= "";

			if (re_type == 'C') { // 교환일 경우
				sel_code				= '11';
				sel_sub_code			= '0';

				sel_option1		= $('input[name=option1]').val();
				sel_option2		= "";
				sel_option_price_text		= "";
				sel_text_opt_s		= $('input[name=text_opt_s]').val();
				sel_text_opt_c		= "";
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
			} else if (re_type == 'B') { // 반품일 경우
				sel_code				= '4';
				sel_sub_code			= '0';
			}
			
			var rechange_type			= 0;
			if (re_type == 'C') { // 교환일 경우
				rechange_type		= $('input[name=rechange_type]:checked').val();
			}

			var return_store_code		= "";
			if (re_type == 'B') { // 반품일 경우
				return_store_code		= $('select[name=sel_return_store_code1]').val();
			} else if (re_type == 'C') { // 교환일 경우
				return_store_code		= $('select[name=sel_return_store_code2]').val();
			}

			if (typeof sel_option_price_text == "undefined")
				sel_option_price_text	= '';
			if (typeof sel_text_opt_s == "undefined")
				sel_text_opt_s				= '';
			if (typeof sel_text_opt_c == "undefined")
				sel_text_opt_c				= '';

			$('input[name=option2]').val(sel_option2);
			$('input[name=text_opt_s]').val(sel_text_opt_s);
			$('input[name=text_opt_c]').val(sel_text_opt_c);
			
			if (re_type == 'B') { // 반품일 경우
				bankcode				= $('select[name=bankcode]').val();
				bankuser				= $('input[name=bankuser]').val();
				bankaccount			= $('input[name=bankaccount]').val();

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
				}
			}

			/*alert(
				"re_type : "+re_type+"\n"
				+"ordercode : "+ordercode+"\n"
				+"idxs : "+idxs+"\n"
				+"paymethod : "+paymethod+"\n"
				+"return_store_code : "+return_store_code+"\n"
				+"rechange_type : "+rechange_type+"\n"
				+"sel_code : "+sel_code+"\n"
				+"sel_sub_code : "+sel_sub_code+"\n"
				+"bankcode : "+bankcode+"\n"
				+"bankaccount : "+bankaccount+"\n"
				+"bankuser : "+bankuser+"\n"
				+"each_price : "+each_price+"\n"
				+"sel_option1 : "+sel_option1+"\n"
				+"sel_option2 : "+sel_option2+"\n"
				+"sel_option_price_text : "+sel_option_price_text+"\n"
				+"sel_text_opt_s : "+sel_text_opt_s+"\n"
				+"sel_text_opt_c : "+sel_text_opt_c+"\n"
				+"receipt_name : "+receipt_name+"\n"
				+"receipt_tel : "+receipt_tel+"\n"
				+"receipt_mobile : "+receipt_mobile+"\n"
				+"receipt_addr : "+receipt_addr+"\n"
				+"receipt_post5 : "+receipt_post5);
				return;*/

			var fd = new FormData();
			
			fd.append('mode','fin_proc');
			fd.append('re_type',re_type);
			fd.append('ordercode',ordercode);
			fd.append('idxs',idxs);
			fd.append('paymethod',paymethod);
			fd.append('return_store_code',return_store_code);
			fd.append('rechange_type',rechange_type);
			fd.append('sel_code',sel_code);
			fd.append('sel_sub_code',sel_sub_code);
			fd.append('opt1_changes',sel_option1);
			fd.append('opt2_changes',sel_option2);
			fd.append('opt2_pt_changes',sel_option_price_text);
			fd.append('opt_text_s_changes',sel_text_opt_s);
			fd.append('opt_text_c_changes',sel_text_opt_c);

			fd.append('receipt_name',receipt_name);
			fd.append('receipt_tel',receipt_tel);
			fd.append('receipt_mobile',receipt_mobile);
			fd.append('receipt_addr',receipt_addr);
			fd.append('receipt_post5',receipt_post5);
			fd.append('pg_ordercode',pg_ordercode);
			fd.append('cancel_pc_type',cancel_pc_type);

			if(confirm("저장 하시겠습니까?")){
				if (re_type == 'B' && (paymethod =='C' || paymethod =='V')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
					<?php if($pg_type=="A"){?>
					var sitecd = '<?=$pgid_info["ID"]?>';
					var sitekey = '<?=$pgid_info["KEY"]?>';
					var sitepw = "<?=$pgid_info['PW']?>";
					$(".button_open").hide();
					$(".button_close").show();
					$.post("<?=$Dir?>paygate/<?=$pg_type?>/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, sitepw:sitepw, ordercode:pg_ordercode, real_ordercode:ordercode, pc_type:cancel_pc_type,mod_mny:each_price},function(data){
						if(data.res_code !='N'){
							var cancel_check="OK";
							if (data.type != '1') {
								var cancel_check="NO";
								
								/*
								if(confirm("정상 처리가 되지않았습니다.\\n환불계좌로 처리하시겠습니까?")){
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
								} else {
									return;
								}*/
								if(confirm("!!정상 처리가 되지않았습니다.!!\n(같은현상이 지속적으로 발생할경우 PG관리자에서 수동처리후 확인 버튼 클릭)")){
									if(confirm("취소처리 하시겠습니까?")){
										var cancel_check="OK";
									}
								}
							} else {
								bankcode		= 0;
								bankaccount	= '';
								bankuser		= '';
							}
							fd.append('bankcode',bankcode);
							fd.append('bankaccount',bankaccount);
							fd.append('bankuser',bankuser);

							fd.append('pgcancel_type',data.type);
							fd.append('pgcancel_res_code',data.res_code);
							fd.append('pgcancel_res_msg',data.res_msg);
							
							if(cancel_check=="OK"){
								$.ajax({
									url:"cscenter_online_as_indb.php",
									type:'POST',
									data:fd,
									dataType: "json",
									async:false,
									cache:false,
									contentType:false,
									processData:false,
									success: function(data){
										if(data.type == 1){
											$("#onlinecsform").submit();
										} else {
											alert(data.msg);
										}
									}
								});
							}else{
								$(".button_open").show();
								$(".button_close").hide();
							}
						} else {
							alert(data.msg);
							$(".button_open").show();
							$(".button_close").hide();
						}
					},"json");
					<?}?>
				} else {

					fd.append('bankcode',bankcode);
					fd.append('bankaccount',bankaccount);
					fd.append('bankuser',bankuser);

					$.ajax({
						url:"cscenter_online_as_indb.php",
						type:'POST',
						data:fd,
						dataType: "json",
						async:false,
						cache:false,
						contentType:false,
						processData:false,
						success: function(data){
							if(data.type == 1){
								$("#onlinecsform").submit();
							} else {
								alert(data.msg);
							}
						}
					});
				}
			}
		} else {
			if(confirm("저장 하시겠습니까?")){
				$("#onlinecsform").submit();
			}
		}
	}
}

function disbled_on(num){
	$(".text_disbled").attr("disabled",true);
	$("#text_disbled_"+num).attr("disabled",false);
}

function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('as_zipcode').value = data.zonecode;
			document.getElementById('as_addr').value = data.address;
			document.getElementById('as_addr').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;
		}
	}).open();
}

function ajaxValue(mode, no){

	var receipt_no=$("#receipt_no").val();

	if(mode=="log"){
		var logno = [];
		var logtime_h = [];
		var logtime_i = [];
		var logtime_s = [];
		var logday = [];

		$("input[name='log_no[]']").each(function(idx, elem){ logno.push($(elem).val()); });
		$("input[name='log_day[]']").each(function(idx, elem){ logday.push($(elem).val()); });
		$("select[name='log_time_h[]']").each(function(idx, elem){ logtime_h.push($(elem).val()); });
		$("select[name='log_time_i[]']").each(function(idx, elem){ logtime_i.push($(elem).val()); });
		$("select[name='log_time_s[]']").each(function(idx, elem){ logtime_s.push($(elem).val()); });

		var allData = { "logno" : logno, "logtime_h" : logtime_h, "logtime_i" : logtime_i, "logtime_s" : logtime_s, "logday" : logday, "mode" : mode, "receipt_no" : receipt_no };
	}else if(mode=="tel"){
		var as_name=$("#as_name").val();
		var as_tel_1=$("#as_tel_1").val();
		var as_tel_2=$("#as_tel_2").val();
		var as_tel_3=$("#as_tel_3").val();

		if(!as_name){
			alert("이름을 입력해주세요.");
			return;
		}else if(!as_tel_2 || !as_tel_3){
			alert("전화번호를 입력해주세요.");
			return;
		}

		var allData = { "as_name" : as_name, "as_tel_1" : as_tel_1, "as_tel_2" : as_tel_2, "as_tel_3" : as_tel_3,  "mode" : mode, "receipt_no" : receipt_no };
	}else if(mode=="memo"){

		var allData = { "memo_no" : no, "mode" : mode, "receipt_no" : receipt_no };

	}else if(mode=="img"){

		var allData = { "img_no" : no, "mode" : mode, "receipt_no" : receipt_no };

	}

	//jQuery.ajaxSettings.traditional = true;

	$.ajax({
	type: "POST",
	url: "./cscenter_online_as_ajax.php",
	data: allData
	}).done(function(msg) {
		cout_msg=msg.split("||");
		if(cout_msg[0]=="html"){
			$(".txt-box").html(cout_msg[1]);
			alert("삭제되었습니다.");
		}else{
			alert(msg);
		}
	});
}

function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
</script>
<form name="onlinecsform" id="onlinecsform" method="post" action="./cscenter_online_as_indb.php" enctype="multipart/form-data">
<input type="hidden" name="place_type" id="place_type" value="0">
<input type="hidden" name="mode" id="mode" value="request">
<input type="hidden" name="receipt_no" id="receipt_no" value="<?=$data["no"]?>">
<input type=hidden name=re_type>
<input type=hidden name=ordercode value="<?=$data['ordercode']?>">
<input type=hidden name=pg_ordercode value="<?=$data['pg_ordercode']?>">
<input type=hidden name=paymethod value="<?=$data['paymethod'][0]?>">
<input type=hidden name=receipt_name value="<?=$place_name?>">
<input type=hidden name=receipt_tel value="<?=$place_tel?>">
<input type=hidden name=receipt_mobile value="">
<input type=hidden name=receipt_addr value="<?=$address?>">
<input type=hidden name=receipt_post5 value="<?=$zonecode?>">

<section class="online-as">
	<div class="title">
		<h3><span class="point-txt"><?=$data["ordercode"]?></span>온라인 AS 요청정보</h3>
		<p><a href="javascript:window.print()" class="btn-type">온라인 AS 요청서 출력</a></p>
	</div>
	<div class="clear">
		<div class="content-l">
			<table class="table-th-left">
				<caption></caption>
				<colgroup>
					<col style="width:120px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">접수번호</th>
						<td><?=$data["as_code"]?></td>
					</tr>
					<tr>
						<th scope="row">접수일</th>
						<td><?=$reci_date?></td>
					</tr>
					<tr>
						<th scope="row">구분</th>
						<td><?=$as_gubun[$data["as_type"]]?></td>
					</tr>
					<tr>
						<th scope="row">주문채널/<br>대표주문번호</th>
						<td><strong>[<?=$chk_mb[$data["is_mobile"]]?>] <span class="point-txt"><?=$data["ordercode"]?></span></strong></td>
					</tr>
					<tr>
						<th scope="row">PG사 주문번호</th>
						<td><?=$data["ordercode"]?></td>
					</tr>
					<tr>
						<th scope="row">주문일</th>
						<td><?=$order_date.' '.substr($data['ordercode'],'8','2').':'.substr($data['ordercode'],'10','2').':'.substr($data['ordercode'],'12','2')?></td>
					</tr>
					<tr>
						<th scope="row">AS 요청자</th>
						<td><?=$data["name"]?><?if($data["id"]){?>(<?=$data["id"]?>)<?}?></td>
					</tr>
					<tr>
						<th scope="row">결제</th>
						<td>
							<div>
								<p>- <?=$arpm[$data["paymethod"][0]]?></p>
								<p>- 실결제금액 : <strong class="point-txt"><?=number_format($data["price"]-$data["coupon_price"])?>원</strong></p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="content-r">
			<div class="cont-box">
				<table class="table-th-top">
				<caption></caption>
				<thead>
					<tr class="bg">
						<th scope="col"><strong>처리이력</strong></th>
						<th scope="col" class="ta_r">온라인 AS 요청상품 :<span class="point-txt"><?=$data["productcode"]?></span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="scroll">
								<div> <!-- [D] 리스트 반복 -->
									<strong>상태</strong>
									<?while($log_data=pmysql_fetch_array($log_result)){
										$selected["logh"]="";
										$selected["logi"]="";
										$selected["logs"]="";
										#a/s진행상태 텍스트 컬러변경
										if($log_data["step_code"]!=$data["step_code"]){
											$color["step_color"][$log_data["step_code"]]="point-txt2 bold";
										}

										#등록일
										$log_date=substr($log_data['regdt'],'0','4').'-'.substr($log_data['regdt'],'4','2').'-'.substr($log_data['regdt'],'6','2');
										$log_H=substr($log_data['regdt'],'8','2');
										$log_I=substr($log_data['regdt'],'10','2');
										$log_S=substr($log_data['regdt'],'12','2');

										$selected["logh"][$log_H]="selected";
										$selected["logi"][$log_I]="selected";
										$selected["logs"][$log_S]="selected";

										?>
									<input type="hidden" name="log_no[]" value=<?=$log_data["no"]?>>

									<p class="name"><?=$as_progress[$log_data["step_code"]]?> <?=$log_data["admin_name"]?>(<?=$log_data["admin_id"]?>)</p>
									<div class="date-sort clear">
										<div class="type calendar">
											<div class="box">
												<input type="text" name="log_day[]" title="일자별 시작날짜" value="<?=$log_date?>" OnClick="Calendar(event)" readonly>
												<!--<button type="button" OnClick="Calendar(event)">달력 열기</button>-->
											</div>
											<select name="log_time_h[]" class="ml_5 select">
												<?for($h=0;$h<=23;$h++){
													$mH=str_pad($h, 2, "0", STR_PAD_LEFT);
												?>
												<option value="<?=$mH?>" <?=$selected["logh"][$mH]?>><?=$mH?></option>
												<?}?>
											</select>
											<span>시</span>
											<select name="log_time_i[]" class="select">
												<?for($i=0;$i<=59;$i++){
													$mI=str_pad($i, 2, "0", STR_PAD_LEFT);
												?>
												<option value="<?=$mI?>" <?=$selected["logi"][$mI]?>><?=$mI?></option>
												<?}?>
											</select>
											<span>분</span>
											<select name="log_time_s[]" class="select">
												<?for($s=0;$s<=59;$s++){
													$mS=str_pad($s, 2, "0", STR_PAD_LEFT);
												?>
												<option value="<?=$mS?>" <?=$selected["logs"][$mS]?>><?=$mS?></option>
												<?}?>
											</select>
											<span>초</span>
										</div>
									</div>
									<?}?>
								</div><!-- // [D] 리스트 반복 -->
							</div>
							<div class="btn-bottom"><a href="javascript:ajaxValue('log')" class="btn-type c1">처리이력 저장</a></div>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="mt_40 btn-set button_open">
		<a href="javascript:as_submit();" class="btn-type c1">저장</a>
		<a href="javascript:window.close();" class="btn-type c2">닫기</a>
	</div>
	<div class="mt_40 btn-set button_close hide">
		========== 처리중입니다 ==========
	</div>

	<div class="order-info">
		<h3>주문자 정보</h3>
		<table class="table-th-left">
			<caption>사용자 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">주문자이름</th>
					<td><input type="text" style="width:125px" class="input" value="<?=$data['name']?>" title="주문자 이름" readonly></td>
				</tr>

				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" style="width:125px" class="input" value="<?=$data['mobile']?>" title="휴대전화" readonly></td>
				</tr>
				<tr>
					<th scope="row">이메일</th>
					<td><input type="text" style="width:200px" class="input" value="<?=$data['email']?>" title="주소" readonly></td>
				</tr>
				<tr>
					<th scope="row">주소</th>
					<td>
						<div>
							<input type="text" title="우편번호" value="<?=$data['postcode_new']?>" style="width:80px;" readonly>

						</div>
						<div class="input-wrap">
							<input type="text" title="주소" value="<?=$data['addr1']?>" style="width:350px;" readonly>
							<input type="text" title="상세주소"  value="<?=$data['addr2']?>" style="width:350px;" readonly>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<table class="table-th-top">
			<caption>상담 가능한 연락처</caption>
			<colgroup>
				<col style="width:210px">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col" colspan="2">상담 가능한 연락처 <span class="btn-small"><a href="javascript:ajaxValue('tel')" class="btn-type c2">수정</a></span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						· 이름:
						<input type="text" name="as_name" id="as_name" title="이름" value="<?=$data["as_name"]?>" style="width:125px;">
					</td>
					<td>
						· 전화번호:
						<select name="as_tel_1" id="as_tel_1" class="select" style="width:60px;">
							<option value="010" <?=$selected["as_tel"]["010"]?>>010</option>
							<option value="011" <?=$selected["as_tel"]["011"]?>>011</option>
							<option value="016" <?=$selected["as_tel"]["016"]?>>016</option>
							<option value="017" <?=$selected["as_tel"]["017"]?>>017</option>
							<option value="018" <?=$selected["as_tel"]["018"]?>>018</option>
							<option value="019" <?=$selected["as_tel"]["019"]?>>019</option>
						</select>
						<span class="dash">-</span>
						<input type="text" name="as_tel_2" id="as_tel_2" title="전화번호" value="<?=$cut_astel[1]?>" style="width:40px;">
						<span class="dash">-</span>
						<input type="text" name="as_tel_3" id="as_tel_3" title="전화번호" value="<?=$cut_astel[2]?>" style="width:40px;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<table class="table-th-top">
			<caption>교환신청 상품 수령지</caption>
			<thead>
				<tr>
					<th scope="col" colspan="2">교환신청 상품 수령지 <span class="btn-small"><a href="javascript:zip_change()" class="btn-type c2">수령지 변경</a></span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<p>[받는 분] <?=$place_name?> / <?=$place_tel?></p>
						<p><?=$address?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="order-info redisplay hide">
		<h3>수령지 변경</h3>
		<table class="table-th-left">
			<caption>수령지 변경</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">받는분</th>
					<td><input type="text" style="width:125px" class="input" name="place_name" value="<?=$place_name?>" title="주문자 이름" ></td>
				</tr>

				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" style="width:125px" class="input" name="place_mobile" value="<?=$place_tel?>" title="휴대전화" ></td>
				</tr>
				<tr>
					<th scope="row">주소</th>
					<td>
						<div>
							<input type="text" title="우편번호" name="place_zipcode" id="as_zipcode" value="<?=$zonecode?>" style="width:80px;" >  <span class="btn-small"><a href="javascript:openDaumPostcode();" class="btn-type c2">우편번호 찾기</a></span>

						</div>
						<div class="input-wrap">
							<input type="text" title="주소" name="place_addr"  id="as_addr" value="<?=$address?>" style="width:500px;" >

						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>
			A/S 진행 상태
			<!--
			<div class="btn-wrap">
				<input type="radio" name="" value="" checked="">
				<span class="btn-small"><a href="#" class="btn-type c2">제품도착</a></span>
				<span>
					<a href="#" class="btn-line02 open">모든 상태 열림</a>
					<a href="#" class="btn-line02 close">모든 상태 닫힘</a>
				</span>
			</div>-->
		</h3>
		<table class="table-th-left">
			<caption>A/S 진행 상태</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<?foreach($as_progress_sort as $ap=>$apv){?>
				<tr>
					<th scope="row"><?=$ap?></th>
					<td>
						<div class="radio-set">

						<?foreach($apv as $app=>$appv){?>
							<input type="radio" name="step_code" id="radio<?=$appv?>" class="<?if($as_progress_class[$appv]){echo $as_progress_class[$appv];}else{echo "progressoff";}?>" value="<?=$appv?>"  <?=$checked["progress"][$appv]?>> <label for="radio<?=$appv?>" class='<?=$color["step_color"][$appv]?>'><?=$as_progress[$appv]?></label>&nbsp;
						<?}?>
						</div>
					</td>
				</tr>
				<?}?>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>A/S 신청 상품</h3>
		<table class="table-th-top02">
			<caption>A/S 신청 상품</caption>
			<colgroup>
				<col style="width:8%">
				<col style="width:19%">
				<col style="width:8%">
				<col style="width:9%">
				<col style="width:9%">
				<col style="width:5%">
				<col style="width:9%">
				<col style="width:8%">
				<col style="width:8%">
				<col style="width:auto">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">상품<br>주문번호</th>
					<th scope="col">상품명</th>
					<th scope="col">옵션<br>(사이즈)</th>
					<th scope="col">정상가</th>
					<th scope="col">판매가</th>
					<th scope="col">주문<br>수량</th>
					<th scope="col">총금액</th>
					<th scope="col">상태</th>
					<th scope="col">출고매장</th>
					<th scope="col">배송정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?=$order_date?><br><ins><A HREF="javascript:OrderDetailView('<?=$data['ordercode']?>')"><?=$data['ordercode']?></a></ins></td>
					<td class="ta_l">
						<div class="product-info clear">
							<a href="javascript:ProductDetail('<?=$data['productcode']?>')">
							<img src="<?=$product_img?>" alt="">
							</a>
							<div class="pro-title">
								<a href="javascript:ProductDetail('<?=$data['productcode']?>')">
								<strong><?=$data['brandname']?></strong>
								<p><?=$data['productname']?></p>
								<p><?="[".$data['prodcode']."-".$data['colorcode']."]"?></p>
								</a>
							</div>
						</div>
					</td>
					<td><?=$data['opt2_name']?></td>
					<td><?=number_format($data['consumerprice'])?>원</td>
					<td><?=number_format($data['price'])?>원</td>
					<td><?=$data['quantity']?></td>
					<td><?=number_format($data['price']*$data['quantity'])?>원</td>
					<td><?=GetStatusOrder("p", $data['oi_step1'], $data['oi_step2'], $data['op_step'], $data['redelivery_type'], $data['order_conf'])?></td>
					<td><?=($storelist[$data['store_code']]['name'] !='')?$storelist[$data['store_code']]['name']:'-'?></td>
					<td>
						<?=$data['deli_num']?"<strong>".$delicomlist[trim($data['deli_com'])]->company_name."</strong><p>".$data['deli_num']."</p>":"-"?>

					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	$disable['end_step']['0']	= " disabled";
	$disable['end_step']['1']	= " disabled";
	$disable['end_step']['2']	= " disabled";
	$disable['end_step']['3']	= " disabled";
	$data_end_step	= $data['end_step']==''?'0':$data['end_step'];
	$selected['end_step'][$data_end_step]	= " selected";

	if ($data['end_step'] == '' || $data['end_step'] == '1') {
		$disable['end_step']['0']	= "";
		$disable['end_step']['1']	= "";
		$disable['end_step']['2']	= "";
		$disable['end_step']['3']	= "";
	} else if ($data['end_step'] == '2') {
		$disable['end_step']['2']	= "";
	} else if ($data['end_step'] == '3') {
		$disable['end_step']['3']	= "";
	}
?>
	<!--##########################################################주문상태변경처리 작업필요###########################################################-->
	<div class="mt_40">
		<h3>처리결과</h3>
		<table class="table-th-left">
			<caption>처리결과</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">현재 주문</th>
					<td>
						<!--tblcsasreceiptinfo -> end_step 상태값 저장필요.-->
						<select name="end_step" class="select endgoods" style="width:143px;">
							<option value=""<?=$selected['end_step']['0'].$disable['end_step']['0']?>>선택하세요</option>
							<option value="1"<?=$selected['end_step']['1'].$disable['end_step']['1']?>>반품요청</option>
							<option value="2"<?=$selected['end_step']['2'].$disable['end_step']['2']?>>반품완료</option>
							<option value="3"<?=$selected['end_step']['3'].$disable['end_step']['3']?>>교환완료</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!--반품요청, 반품완료시-->
	<div class="mt_40 cancelgoods_display" <?if($data['end_step']=='' || $data['end_step']=='3'){?>style="display:none"<?}?>>
		<h3>환불계좌</h3>
		<table class="table-th-left">
			<caption>환불계좌</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">환불계좌</th>
					<td>
						<!--tblcsasreceiptinfo -> end_step 상태값 저장필요.-->
						<select name="bankcode" class="select" style="width:143px;">
							<option value="">==== 은행선택 ====</option>
							<?php
								foreach($oc_bankcode as $key => $val) {
									echo "<option value=\"{$key}\"";
									if($data['end_bankcode']==$key) echo " selected";
									echo ">{$val}</option>\n";
								}
							?>
						</select>
						<input type="text" name="bankaccount" id="account-num" style="width:300px;" value="<?=$data['end_bankaccount']?>">
					</td>
					<th scope="row">예금주</th>
					<td>
						<input type="text" name="bankuser" id="account-nm" style="width:100px;" value="<?=$data['end_bankuser']?>">
					</td>
				</tr>
				<tr>
					<th scope="row">회송매장</th>
					<td colspan=3>
						<select name="sel_return_store_code1" class="select" style="width:143px;">
							<option>매장 선택</option>
							<?
								$end_return_store_code	= $data['end_return_store_code']?$data['end_return_store_code']:$op_store_code;
								foreach($store_array as $sa=>$sav){
									if ($sav["store_code"] == $end_return_store_code) {
										$b_store_code_sel	= " selected";
									} else {
										$b_store_code_sel	= "";
									}
							
							?>
								<option value="<?=$sav["store_code"]?>"<?=$b_store_code_sel?>><?=$sav["name"]?></option>
							<?}?>
						</select>
					</td>

				</tr>
			</tbody>
		</table>
	</div>

	<!--교환완료시-->
	<div class="mt_40 changegoods_display" <?if($data['end_step']!='3'){?>style="display:none"<?}?>>
		<h3>교환상품</h3>
		<table class="table-th-left">
			<caption>교환상품</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">

			</colgroup>
			<tbody>
				<tr>
					<th scope="row">검색방법</th>
					<td>
						<div class="radio-set">
						<input type="radio" name="rechange_type" value="1" id="changegoods1" checked> <label for="changegoods1">동일 상품으로 교환</label>&nbsp;&nbsp;
						<input type="radio" name="rechange_type" value="2" id="changegoods2"> <label for="changegoods2">다른 사이즈로 교환</label>
						</div>
					</td>

				</tr>
				<?=$change_option_html?>
				<!-- <tr>
					<th scope="row">사이즈선택</th>
					<td>
						<select name="" class="select size_select" style="width:143px;">
							<option>사이즈 선택</option>
						</select>
					</td>

				</tr> -->
				<tr>
					<th scope="row">회송매장</th>
					<td>
						<select name="sel_return_store_code2" class="select" style="width:143px;">
							<option>매장 선택</option>
							<?
								$end_return_store_code	= $data['end_return_store_code']?$data['end_return_store_code']:$op_store_code;
								foreach($store_array as $sa=>$sav){
									if ($sav["store_code"] == $end_return_store_code) {
										$b_store_code_sel	= " selected";
									} else {
										$b_store_code_sel	= "";
									}
							
							?>
								<option value="<?=$sav["store_code"]?>"<?=$b_store_code_sel?>><?=$sav["name"]?></option>
							<?}?>
						</select>
					</td>

				</tr>
			</tbody>
		</table>
	</div>

	<!------------####################################################################################################################----------------->

	<div class="mt_40 return_none" <?=$display["return"]?>>
		<h3>회송 처리내용</h3>
		<table class="table-th-left">
			<caption>회송 처리내용</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수유형</th>
					<td>
						<div class="radio-set">
							<?foreach($as_return as $asr=>$arrv){?>
								<input type="radio" id="creturn-a<?=$asr?>" name="c_return" value="<?=$asr?>" <?=$checked["creturn"][$asr]?>> <label for="creturn-a<?=$asr?>"><?=$arrv?></label>
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 returngoods_none" <?=$display["returngoods"]?>>
		<h3>AS 반품 처리내용</h3>
		<table class="table-th-left">
			<caption>AS 반품 처리내용</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">AS 반품</th>
					<td>
						<div class="radio-set">
							<?foreach($as_returngoods as $asg=>$argv){?>
								<input type="checkbox" id="areturng-a<?=$asg?>" name="as_returngoods[]" value="<?=$asg?>" <?=$checked[$asg]?>> <label for="areturng-a<?=$asg?>"><?=$argv?></label>
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 returngoods_none" <?=$display["returngoods"]?>>
		<h3>AS 반품 처리</h3>
		<table class="table-th-left">
			<caption>AS 반품 처리</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">AS 반품</th>
					<td>
						<div class="radio-set">
							<?foreach($as_returngoods2 as $asg2=>$argv2){
								$value_check="";
								if($asg2!=$data["as_return_type"]){
									$disabled["as_return_type"][$asg2]="disabled='disabled'";
									$value_check="none";
								}

								?>
								<input type="radio" id="areturng2-a<?=$asg2?>" name="as_return_type" value="<?=$asg2?>" onclick="javascript:disbled_on('<?=$asg2?>')" <?=$checked["returngoods2"][$asg2]?>> <label for="areturng2-a<?=$asg2?>" style="margin-right:3px;"><?=$argv2?></label><input type="text" name="as_return_text[<?=$asg2?>]" class="text_disbled" id="text_disbled_<?=$asg2?>" value="<?if(!$value_check){ echo $data["as_return_text"]; }?>" <?=$disabled["as_return_type"][$asg2]?> style="width:50px; margin-right:15px;">
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 reviewreturn_none" <?=$display["reviewreturn"]?>>
		<h3>심의회송 처리내용</h3>
		<table class="table-th-left">
			<caption>심의회송 처리내용</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수유형</th>
					<td>
						<div class="radio-set">
							<?foreach($as_reviewreturn as $are=>$arev){?>
								<input type="radio" id="reviewreturn-a<?=$are?>" name="c_reviewreturn" value="<?=$are?>" <?=$checked["reviewreturn"][$are]?>> <label for="reviewreturn-a<?=$are?>"><?=$arev?></label>
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 outreviewgoods_none" <?=$display["outreviewgoods"]?>>
		<h3>외부심의 반품 처리내용</h3>
		<table class="table-th-left">
			<caption>외부심의 반품 처리내용</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">구분</th>
					<td>
						<div class="radio-set">
							<?foreach($as_outreviewgoods_1 as $aog=>$aogv){?>
								<input type="checkbox" id="outreviewgoods-a<?=$aog?>" name="as_outreviewgoods_1[]" value="<?=$aog?>" <?=$checked[$aog]?>> <label for="outreviewgoods-a<?=$aog?>"><?=$aogv?></label>
							<?}?>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">상세</th>
					<td>
						<div class="radio-set">
							<?foreach($as_outreviewgoods_2 as $aog2=>$aogv2){?>
								<input type="checkbox" id="outreviewgoods2-a<?=$aog2?>" name="as_outreviewgoods_2[]" value="<?=$aog2?>" <?=$checked[$aog2]?>> <label for="outreviewgoods2-a<?=$aog2?>"><?=$aogv2?></label>
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 outreviewreturn_none" <?=$display["outreviewreturn"]?>>
		<h3>외부심의 회송 처리내용</h3>
		<table class="table-th-left">
			<caption>외부심의 회송 처리내용</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">외부심의 회송</th>
					<td>
						<div class="radio-set">
							<?foreach($as_outreviewreturn as $aor=>$aorv){?>
								<input type="checkbox" id="outreturn-a<?=$aor?>" name="as_outreviewreturn[]" value="<?=$aor?>" <?=$checked[$aor]?>> <label for="outreturn-a<?=$aor?>"><?=$aorv?></label>
							<?}?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>A/S접수 정보</h3>
		<table class="table-th-left">
			<caption>A/S 접수 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수매장</th>
					<td>
						<select name="receipt_store" id="receipt_store" class="select" style="width:143px;">
								<option value="">==== 매장선택 ====</option>
								<?foreach($store_array as $sa=>$sav){?>
									<option value="<?=$sav["sno"]?>" <?=$selected["receipt_store"][$sav["sno"]]?>><?=$sav["name"]?></option>
								<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">접수유형</th>
					<td>
						<div class="radio-set">
							<?foreach($as_receipt as $ar=>$arv){?>
								<input type="radio" id="receipt-a<?=$ar?>" name="receipt_type" value="<?=$ar?>" class=<?=$as_receipt_class[$ar]?> <?=$checked["receipt"][$ar]?>> <label for="receipt-a<?=$ar?>"><?=$arv?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr>
					<th scope="row">감가적용</th>
					<td>
						<div class="radio-set">
							<?foreach($as_depreciation as $ad=>$adv){?>
								<input type="radio" id="depreciation-a<?=$ad?>" name="depreciation_type" value="<?=$ad?>" <?=$checked["depreciation"][$ad]?>> <label for="depreciation-a<?=$ad?>"><?=$adv?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr class=receipt_none <?=$display["receipt_type"]?>>
					<th scope="row">유상수선비</th>
					<td>
						<div class="radio-set">
							<?foreach($as_repair as $ae=>$aev){?>
								<input type="radio" id="repair-a<?=$ae?>" name="repairs_type" value="<?=$ae?>" <?=$checked["repair"][$ae]?>> <label for="repair-a<?=$ae?>"><?=$aev?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr class=receipt_none <?=$display["receipt_type"]?>>
					<th scope="row">현금영수증</th>
					<td>
						<div class="radio-set">
							<?foreach($as_cash as $ac=>$acv){?>
								<input type="radio" id="cash-a<?=$ac?>" name="cash_type" value="<?=$ac?>" class=<?=$as_cash_class[$ac]?> <?=$checked["cash"][$ac]?>> <label for="cash-a<?=$ac?>"><?=$acv?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr class=cash_none <?=$display["cash_type"]?>>
					<th scope="row">현금영수증<br>발행정보</th>
					<td>
						<div class="radio-set">
							<div>
							<input type="radio" id="cashcheck-a01" name="cash_detail_type" value="1" onclick="cach_disabled('1')" <?=$checked["cashcheck"]["1"]?>> <label for="cashcheck-a01">소득공제용</label>
							<select class="cashcheck_1" name="cash_detail_tel1" id="cash_detail_tel1" <?=$so_disabled?>>
								<option value='010' <?=$selected["cash_detail_num"]["010"]?>>010</option>
								<option value='011' <?=$selected["cash_detail_num"]["011"]?>>011</option>
								<option value='016' <?=$selected["cash_detail_num"]["016"]?>>016</option>
								<option value='017' <?=$selected["cash_detail_num"]["017"]?>>017</option>
								<option value='018' <?=$selected["cash_detail_num"]["018"]?>>018</option>
								<option value='019' <?=$selected["cash_detail_num"]["019"]?>>019</option>
							</select>
							- <input type="text" name="cash_detail_tel2" id="cash_detail_tel2" value="<?=$so_num[1]?>" class="cashcheck_1" style="width:50px" <?=$so_disabled?>>
							- <input type="text" name="cash_detail_tel3" id="cash_detail_tel3" value="<?=$so_num[2]?>" class="cashcheck_1" style="width:50px" <?=$so_disabled?>>
							</div>
							<div class="mt_5">
							<input type="radio" id="cashcheck-a02" name="cash_detail_type" value="2" onclick="cach_disabled('2')" <?=$checked["cashcheck"]["2"]?>> <label for="cashcheck-a02">지출증빙용(사업자등록번호)</label>
							<input type="text" style="width:50px" name="cash_detail_num1" id="cash_detail_num1" value="<?=$ji_num[0]?>" class="cashcheck_2" <?=$ji_disabled?>>
							- <input type="text" style="width:50px" name="cash_detail_num2" id="cash_detail_num2" value="<?=$ji_num[1]?>" class="cashcheck_2" <?=$ji_disabled?>>
							- <input type="text" style="width:50px" name="cash_detail_num3" id="cash_detail_num3" value="<?=$ji_num[2]?>" class="cashcheck_2" <?=$ji_disabled?>>
							</div>
						</div>
					</td>
				</tr>

				<tr>
					<th scope="row">요쳥사항</th>
					<td>
						<textarea name="requests_text" style="width:100%; height:80px"><?=$data["requests_text"]?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>AS 처리 정보</h3>
		<table class="table-th-left">
			<caption>AS 처리 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:360px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">접수유형</th>
					<td><?=$as_receipt[$data["receipt_type"]]?></td>
					<th scope="row">수선비</th>
					<td><input type="text" name="complete_cost" id="complete_cost" title="수선비" value="<?=$data["complete_cost"]?>" style="width:100px;"> 원</td>
				</tr>
				<tr>
					<th scope="row">처리내용</th>
					<td colspan="3">
						<div class="radio-set">
							<?foreach($as_complete as $asc=>$ascv){?>
								<input type="radio" id="com-a<?=$asc?>" name="complete_type" value="<?=$asc?>" class=<?=$as_complete_class[$asc]?> <?=$checked["complete"][$asc]?>> <label for="com-a<?=$asc?>"><?=$ascv?></label>
							<?}?>

						</div>
					</td>
				</tr>
				<tr class=complete_none <?=$display["complete_type"]?>>
					<th scope="row">기타 상세처리</th>
					<td colspan="3">
						<div class="radio-set">
							<?foreach($as_complete_detail as $asd=>$asdv){?>
								<input type="radio" id="comd-a<?=$asd?>" name="complete_detail" value="<?=$asd?>" <?=$checked["complete_detail"][$asd]?>> <label for="comd-a<?=$asd?>"><?=$asdv?></label>
							<?}?>

						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">업체명 (밴더사)</th>
					<td colspan="3"><input type="text" name="complete_store" id="complete_store" title="수선비" value="<?=$data["complete_store"]?>" style="width:510px;"></td>
				</tr>
				<tr>
					<th scope="row">발송 운송장</th>
					<td colspan="3">
						<select name="complete_delicode" class="select" style="width:113px;">
							<option value="">택배사선택</option>
							<?foreach($delicomlist as $dc=>$dcv){?>
								<option value="<?=$dc?>" <?=$selected["company_name"][$dc]?>><?=$dcv->company_name?></option>
							<?}?>


						</select>
						<input type="text" name="complete_delinumber" id="complete_delinumber" title="수선비" value="<?=$data["complete_delinumber"]?>" style="width:393px;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>택배비</h3>
		<table class="table-th-left">
			<caption>택배비</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:360px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">고객부담 택배비</th>
					<td><input type="text" name="delivery_cost" id="delivery_cost" title="고객부담 택배비" value="<?=$data["delivery_cost"]?>" style="width:100px;"></td>
					<th scope="row">택배비 수령</th>
					<td><input type="text" name="delivery_receipt" id="delivery_receipt" title="택배비 수령" value="<?=$data["delivery_receipt"]?>" style="width:100px;"></td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- [D] 20161006 AS 처리 상세 -->
	<div class="mt_40 as-detail repair_none" <?=$display["repair"]?>>
		<h3>AS 처리 상세</h3>
		<table class="table-th-left">
			<colgroup>
				<col width="100px">
				<col width="*">
			</colgroup>
			<tbody>
			<tr>
				<th scope="row">접착수선</th>
				<td>
					<?foreach($as_process_1 as $aps1=>$apsv1){?>
						<input type="checkbox" id="adhesion_repair_cd_<?=$aps1?>" name="as_process[<?=$aps1?>]" value="<?=$aps1?>" <?=$checked[$aps1]?>><label for="adhesion_repair_cd_<?=$aps1?>"><?=$apsv1?></label>&nbsp;&nbsp;
					<?}?>
				</td>
			</tr>
			<tr>
				<th scope="row"><p>재봉수선</p></th>
				<td>
					<?foreach($as_process_2 as $aps2=>$apsv2){?>
						<input type="checkbox" id="sewing_repair_cd_<?=$aps2?>" name="as_process[<?=$aps2?>]" value="<?=$aps2?>" <?=$checked[$aps2]?>><label for="sewing_repair_cd_<?=$aps2?>"><?=$apsv2?></label>&nbsp;&nbsp;
					<?}?>
				</td>
			</tr>
			<tr>
				<th scope="row">덧댐수선</th>
				<td>
					<?foreach($as_process_3 as $aps3=>$apsv3){?>
						<input type="checkbox" id="add_repair_cd_<?=$aps3?>" name="as_process[<?=$aps3?>]" value="<?=$aps3?>" <?=$checked[$aps3]?>><label for="add_repair_cd_<?=$aps3?>"><?=$apsv3?></label>&nbsp;&nbsp;
					<?}?>
				</td>
			</tr>
			<tr>
				<th scope="row">작업성수선</th>
				<td>
					<?foreach($as_process_4 as $aps4=>$apsv4){?>
						<input type="checkbox" id="work_repair_cd_<?=$aps4?>" name="as_process[<?=$aps4?>]" value="<?=$aps4?>" <?=$checked[$aps4]?>><label for="work_repair_cd_<?=$aps4?>"><?=$apsv4?></label>&nbsp;&nbsp;
					<?}?>
				</td>
			</tr>
		</tbody>
		</table>

		<table width="100%" cellspacing="0" cellpadding="0"  border="0">
			<colgroup>
				<col style="width:33.33%"/>
				<col style="width:33.34%"/>
				<col style="width:33.33%"/>
			</colgroup>
			<tr>
				<td valign="top">

					<table cellspacing="0" cellpadding="0" class="table-th-left" style="border-top:0;">
						<colgroup>
							<col style="width:100px;" />
							<col style="width:auto;" />
							<col style="width:auto;" />
						</colgroup>
						<tbody>
							<?$prono=0;
							foreach($as_process_5 as $aps5=>$apsv5){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_5)?>" scope="row">덧댐수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_A<?=$aps5?>" name="as_process[<?=$aps5?>]" value="<?=$aps5?>" <?=$checked[$aps5]?>><label for="ascode_A<?=$aps5?>"><?=$apsv5?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps5?>]" value="<?=$process_price_num[$aps5]?$process_price_num[$aps5]:$as_process_5_price[$aps5];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>
							<?$prono=0;
							foreach($as_process_6 as $aps6=>$apsv6){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_6)?>" scope="row">아웃솔 수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_B<?=$aps6?>" name="as_process_6[<?=$aps6?>]" value="<?=$aps6?>" <?=$checked[$aps6]?>><label for="ascode_B<?=$aps6?>"><?=$apsv6?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps6?>]" value="<?=$process_price_num[$aps6]?$process_price_num[$aps6]:$as_process_6_price[$aps6];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_7 as $aps7=>$apsv7){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_7)?>" scope="row">뒤축수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_C<?=$aps7?>" name="as_process[<?=$aps7?>]" value="<?=$aps7?>" <?=$checked[$aps7]?>><label for="ascode_C<?=$aps7?>"><?=$apsv7?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps7?>]" value="<?=$process_price_num[$aps7]?$process_price_num[$aps7]:$as_process_7_price[$aps7];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_8 as $aps8=>$apsv8){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_8)?>" scope="row">갑피수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_D<?=$aps8?>" name="as_process[<?=$aps8?>]" value="<?=$aps8?>" <?=$checked[$aps8]?>><label for="ascode_D<?=$aps8?>"><?=$apsv8?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps8?>]" value="<?=$process_price_num[$aps8]?$process_price_num[$aps8]:$as_process_8_price[$aps8];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_9 as $aps9=>$apsv9){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_9)?>" scope="row">작업성 수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_E<?=$aps9?>" name="as_process[<?=$aps9?>]" value="<?=$aps9?>" <?=$checked[$aps9]?>><label for="ascode_E<?=$aps9?>"><?=$apsv9?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps9?>]" value="<?=$process_price_num[$aps9]?$process_price_num[$aps9]:$as_process_9_price[$aps9];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>

						</tbody>
					</table>

				</td>
				<td  valign="top">

					<table cellspacing="0" cellpadding="0" class="table-th-left" style="border-top:0; border-left:0; border-right:0;">
						<colgroup>
							<col style="width:100px;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<?$prono=0;
							foreach($as_process_10 as $aps10=>$apsv10){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_10)?>" scope="row">접착수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_F<?=$aps10?>" name="as_process[<?=$aps10?>]" value="<?=$aps10?>" <?=$checked[$aps10]?>><label for="ascode_F<?=$aps10?>"><?=$apsv10?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps10?>]" value="<?=$process_price_num[$aps10]?$process_price_num[$aps10]:$as_process_10_price[$aps10];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_11 as $aps11=>$apsv11){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_11)?>" scope="row">재봉수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_G<?=$aps11?>" name="as_process[<?=$aps11?>]" value="<?=$aps11?>" <?=$checked[$aps11]?>><label for="ascode_G<?=$aps11?>"><?=$apsv11?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps11?>]" value="<?=$process_price_num[$aps11]?$process_price_num[$aps11]:$as_process_11_price[$aps11];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_12 as $aps12=>$apsv12){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_12)?>" scope="row">작업성수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_H<?=$aps12?>" name="as_process[<?=$aps12?>]" value="<?=$aps12?>" <?=$checked[$aps12]?>><label for="ascode_H<?=$aps12?>"><?=$apsv12?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps12?>]" value="<?=$process_price_num[$aps12]?$process_price_num[$aps12]:$as_process_12_price[$aps12];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_13 as $aps13=>$apsv13){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_13)?>" scope="row">세탁,염색</th>
									<?}?>
									<td><input type="checkbox" id="ascode_I<?=$aps13?>" name="as_process[<?=$aps13?>]" value="<?=$aps13?>" <?=$checked[$aps13]?>><label for="ascode_I<?=$aps13?>"><?=$apsv13?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps13?>]" value="<?=$process_price_num[$aps13]?$process_price_num[$aps13]:$as_process_13_price[$aps13];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>
						</tbody>
					</table>

				</td>
				<td valign="top">

					<table cellspacing="0" cellpadding="0" class="table-th-left" style="border-top:0;">
						<colgroup>
							<col style="width:100px;">
							<col style="width:auto;">
						</colgroup>
						<tbody>
							<?$prono=0;
							foreach($as_process_14 as $aps14=>$apsv14){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_14)?>" scope="row">벨크로,밴드수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_J<?=$aps14?>" name="as_process[<?=$aps14?>]" value="<?=$aps14?>" <?=$checked[$aps14]?>><label for="ascode_J<?=$aps14?>"><?=$apsv14?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps14?>]" value="<?=$process_price_num[$aps14]?$process_price_num[$aps14]:$as_process_14_price[$aps14];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_15 as $aps15=>$apsv15){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_15)?>" scope="row">지퍼수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_K<?=$aps15?>" name="as_process[<?=$aps15?>]" value="<?=$aps15?>" <?=$checked[$aps15]?>><label for="ascode_K<?=$aps15?>"><?=$apsv15?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps15?>]" value="<?=$process_price_num[$aps15]?$process_price_num[$aps15]:$as_process_15_price[$aps15];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_16 as $aps16=>$apsv16){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_16)?>" scope="row">부자재수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_L<?=$aps16?>" name="as_process[<?=$aps16?>]" value="<?=$aps16?>" <?=$checked[$aps16]?>><label for="ascode_L<?=$aps16?>"><?=$apsv16?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps16?>]" value="<?=$process_price_num[$aps16]?$process_price_num[$aps16]:$as_process_16_price[$aps16];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_17 as $aps17=>$apsv17){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_17)?>" scope="row">시즌제품 창기모</th>
									<?}?>
									<td><input type="checkbox" id="ascode_M<?=$aps17?>" name="as_process[<?=$aps17?>]" value="<?=$aps17?>" <?=$checked[$aps17]?>><label for="ascode_M<?=$aps17?>"><?=$apsv17?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps17?>]" value="<?=$process_price_num[$aps17]?$process_price_num[$aps17]:$as_process_17_price[$aps17];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>

							<?$prono=0;
							foreach($as_process_18 as $aps18=>$apsv18){?>
								<tr>
									<?if(!$prono){?>
									<th rowspan="<?=count($as_process_18)?>" scope="row">부자재수선</th>
									<?}?>
									<td><input type="checkbox" id="ascode_N<?=$aps18?>" name="as_process[<?=$aps18?>]" value="<?=$aps18?>" <?=$checked[$aps18]?>><label for="ascode_N<?=$aps18?>"><?=$apsv18?></label></td>
									<td><input type="text" name="as_process_cost[<?=$aps18?>]" value="<?=$process_price_num[$aps18]?$process_price_num[$aps18]:$as_process_18_price[$aps18];?>" class="input" style="float:right;width:60px;"></td>
								</tr>

							<?$prono++;}?>
								<tr>
									<th scope="row">기타</th>
									<td><input type="text" name="as_process_title" value="<?=$as_process_title?>" class="input"></td>
									<td><input type="text" name="as_process_text" value="<?=$as_process_text?>" class="input" style="float:right;width:60px;"></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>

						</tbody>
					</table>

				</td>
			</tr>
		</table>

	</div>
	</div>
	<!-- // [D] 20161006 AS 처리 상세 -->

	<div class="mt_40">
		<h3>기타</h3>
		<table class="table-th-left">
			<caption>기타</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">CS메모</th>
					<td>
						<textarea style="width:100%; height:300px" id="ir1" name="cs_memo"></textarea>
						<div class="add-file-cover">
							<div id="filename0"></div> <!-- 파일 업로드시 파일 주소 출력 -->
							<input type="file" id="add_file" name="file[]" onchange="filenamein(this,'0')">
						</div>
						<div class="btn-wrap1"><span><a href="javascript:add()" class="btn-type1">이미지추가</a></span></div>

						<div id="add_file_div"></div> <!-- 이미지 추가 -->


						<div class="txt-box">
							<?if($memo_while){
								foreach($memo_while as $mw=>$mwv){
									#접수일
									$memo_date=substr($mwv['regdt'],'0','4').'-'.substr($mwv['regdt'],'4','2').'-'.substr($mwv['regdt'],'6','2').' '.substr($mwv['regdt'],'8','2').':'.substr($mwv['regdt'],'10','2').':'.substr($mwv['regdt'],'12','2');

								?>
							<h4>[<?=$as_progress[$mwv["step_code"]]?>] <strong><?=$mwv["admin_name"]?>(<?=$mwv["admin_id"]?>)</strong> <?=$memo_date?> <?if($mwv["admin_id"]==$_ShopInfo->id){?>&nbsp;<div class="btn-wrap1"><span><a href="javascript:ajaxValue('memo', '<?=$mw?>')" class="btn-type1" style="width:50px;">삭제</a></span></div><?}?></h4>
							<div class="cont">
								<?if($mwv["filename"]){
									foreach($mwv["filename"] as $mwf=>$mwfv){
									?>
									<img src="<?=$filepath.$mwfv?>">
									<?if($mwv["admin_id"]==$_ShopInfo->id){?>&nbsp;<div class="btn-wrap1"><span><a href="javascript:ajaxValue('img', '<?=$mwf?>')" class="btn-type1" style="width:50px;">삭제</a></span></div><?}?></span><br><br>
								<?
									}
								}?>
								<?=$mwv["cs_memo"]?>
							</div>
								<?
								}
							}?>
						</div>

					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 btn-set button_open">
		<a href="javascript:as_submit();" class="btn-type c1">저장</a>
		<a href="javascript:window.close();" class="btn-type c2">닫기</a>
	</div>
	<div class="mt_40 btn-set button_close hide">
		========== 처리중입니다 ==========
	</div>

</section> <!-- // .online-as -->
<input type=hidden name=idxs value="<?=$op_idxs?>">
<input type=hidden name=pc_type value="<?=$pc_type?>">
<input type=hidden name=cancel_pc_type value="<?=$cancel_pc_type?>">
<input type=hidden name=each_price value="<?=$t_op_total_price?>">
<input type=hidden name=option1 value="<?=$op_option1?>">
<input type=hidden name=option2 value="<?=$op_option2?>">
<input type=hidden name=text_opt_s value="<?=$op_text_opt_s?>">
<input type=hidden name=text_opt_c value="<?=$op_text_opt_c?>">
<input type=hidden name=now_end_step value="<?=$data['end_step']?>">
</form>
<script type="text/javascript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		},
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>

<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type=hidden name=ordercode>
</form>

<?=$onload?>
</body>
</html>