<?php
/********************************************************************* 
// 파 일 명		: cscenter_order_cancel_request.php
// 설     명		: 취소/반품/교환 요청
// 상세설명	: 취소/반품/교환 요청
// 작 성 자		: 2016.10.05 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

# 배송업체를 불러온다.
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);

# 전체 매장을 불러온다.
$sql="SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result=pmysql_query($sql,get_db_conn());
$storelist=array();
while($row=pmysql_fetch_object($result)) {
	$storelist[$row->store_code]=$row;
}
pmysql_free_result($result);

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
//exdebug($_POST);
//exdebug($_GET);
//exdebug($_FILES);
//exit;

$ordercode		= $_REQUEST["ordercode"];
$type				= $_REQUEST["type"];
$idx				= $_REQUEST["idx"];

//경로
$filepath = $Dir.DataDir."shopimages/cscenter/";
//파일
$csfile = new FILE($filepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$mode = $_POST["mode"];

#---------------------------------------------------------------
# 주문상품을 가져온다.
#---------------------------------------------------------------
$sql="SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);
//exdebug($_ord);
pmysql_free_result($result);
if(!$_ord) {
	alert_go("해당 주문내역이 존재하지 않습니다.",'c');
}
$isupdate=false;

$pgid_info="";
$pg_type="";
switch ($_ord->paymethod[0]) {
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

$re_type	= "";
/*if ($type == "cancel") {					// 주문취소
	$re_type	= "";
	$type_text	= "취소";
} else */if ($type == "refund") {		// 주문취소환불
	$re_type	= "";
	$type_text	= "취소";
} else if ($type == "regoods") {		// 반품
	$re_type	= "B";
	$type_text	= "반품";
} else if ($type == "rechange") {	// 교환
	$re_type	= "C";
	$type_text	= "교환";
}

$deli_view_type	= "old";

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$type_text?>요청서</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="styleSheet" href="/css/common.css" type="text/css">
<link rel="stylesheet" href="/admin/static/css/crm.css" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript">
var now_sub_code	= "";
function subCodeView(sub_code) {
	$("input[name=b_sel_sub_code]").attr("checked", false);
	if($('.CLS_sel_sub_code').hasClass('chk_sub_code_'+now_sub_code))
		$('.chk_sub_code_'+now_sub_code).hide();	
	if($('.CLS_sel_sub_code').hasClass('chk_sub_code_'+sub_code))
		$('.chk_sub_code_'+sub_code).show();
	now_sub_code	= sub_code;
<?if ($deli_view_type	== "") {?>
	if (
		now_sub_code =='4' || 
		now_sub_code =='8' || 
		now_sub_code =='10' || 
		now_sub_code =='11' || 
		now_sub_code =='14') {
		$("input[name=return_deli_type][value='11']").prop("checked", true);
	} else {
		$("input[name=return_deli_type][value='13']").prop("checked", true);
	}
<?}?>
}

function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
}

//2차 옵션
function option_change(productcode, option_depth, option_totalDepth, option_code) {	

	var sel_option	="<option value=\"\">=========선택=========</option>\n";

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

$(document).ready(function(){

	// 취소/반품/교환 요청시
	$('.refundClose').click(function(){
		self.close();
	});

	// 취소/반품/교환 요청시
	$('.refundSubmit').click(function(){
		var re_type				= $('input[name=re_type]').val();
		var ordercode			= $('input[name=ordercode]').val();
		var pg_ordercode			= $('input[name=pg_ordercode]').val();
		var idx					= $('input[name=idx]').val();
		var idxs					= $('input[name=idxs]').val();
		var pc_type			= $('input[name=pc_type]').val();
		var cancel_pc_type			= $('input[name=cancel_pc_type]').val();
		var paymethod		= $('input[name=paymethod]').val();
		var each_price		= "";
		var sel_code			= "";
		var memo				= "";
		var admin_memo	= "";
		var sel_option1		= "";
		var sel_option2		= "";

		var sel_option_price_text		= '';
		var sel_text_opt_s				= '';
		var sel_text_opt_c				= '';

		var bankcode				= 0;
		var bankuser				= "";
		var bankaccount			= "";
		var bankusertel			= "";
		
		if (re_type == '') {
		var alert_text		= "취소";
		} else if (re_type == 'B') {// 반품
			alert_text		= "반품접수";
		} else if (re_type == 'C') {//교환
			alert_text		= "교환접수";
		}
		if (re_type == '' || re_type == 'B') { // 취소, 반품일 경우
			each_price			= $('input[name=each_price]').val();
			sel_code			= $('select[name=b_sel_code]').val();
			memo				= $('textarea[name=memo]').val();
			admin_memo		= $('input[name=admin_memo]').val();

			if(sel_code == 0 || sel_code == ''){
				alert("사유를 선택해 주세요.");
				return;
			}

			/*if(memo == ''){
				alert("상세사유를 입력해 주세요.");
				$('textarea[name=memo]').focus();
				return;
			}*/

		} else if (re_type == 'C') { // 교환일 경우
			sel_code				= $('select[name=b_sel_code]').val();
			memo					= $('textarea[name=memo]').val();
			admin_memo		= $('input[name=admin_memo]').val();

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
		}
		
		var rechange_type			= 0;
		if (re_type == 'C') { // 교환일 경우
			rechange_type		= $('input[name=rechange_type]:checked').val();
		}

		var return_store_code		= "";
		if (re_type == 'B' || re_type == 'C') { // 반품, 교환일 경우
			return_store_code		= $('select[name=sel_return_store_code]').val();
		}

		if(sel_code == 0 || sel_code == ''){
			alert("사유를 선택해 주세요.");
			return;
		}

		/*if(memo == ''){
			alert("상세사유를 입력해 주세요.");
			$('textarea[name=memo]').focus();
			return;
		}*/
		
		if (typeof sel_option_price_text == "undefined")
			sel_option_price_text	= '';
		if (typeof sel_text_opt_s == "undefined")
			sel_text_opt_s				= '';
		if (typeof sel_text_opt_c == "undefined")
			sel_text_opt_c				= '';

		
		var receipt_name			= "";
		var receipt_tel					= "";
		var receipt_mobile			= "";
		var receipt_addr				= "";
		var receipt_post5				= "";

		var sel_sub_code			= "";
		var return_deli_price		= 0;
		var return_deli_receipt		= "";
		var return_deli_type			= "";
		var return_deli_memo		= "";
		if (re_type == 'B' || re_type == 'C') { // 반품, 교환일 경우
			receipt_name				= $('input[name=receipt_name]').val();
			receipt_tel					= $('input[name=receipt_tel]').val();
			receipt_mobile			= $('input[name=receipt_mobile]').val();
			receipt_addr				= $('input[name=receipt_addr]').val();
			receipt_post5				= $('input[name=receipt_post5]').val();

			$(".b_sel_sub_code:checked").each(function(index){
				if(sel_sub_code == '')
					sel_sub_code = $(this).val();
				else
					sel_sub_code += "|" + $(this).val();
			});

			return_deli_price				= $('input[name=return_deli_price]').val();
			if(return_deli_price == '') return_deli_price = 0;
			return_deli_receipt			= $('input[name=return_deli_receipt]').val();
			return_deli_type				= $('input[name=return_deli_type]:checked').val();
			return_deli_memo			= $('input[name=return_deli_memo]').val();
		}

		if (re_type == '' || re_type == 'B') { // 취소, 반품일 경우
			bankcode				= $('select[name=bankcode]').val();
			bankuser				= $('input[name=bankuser]').val();
			bankaccount			= $('input[name=bankaccount]').val();
			bankusertel			= $('input[name=bankusertel]').val();

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
		
		var sHTML = oEditors.getById["ir1"].getIR();
		$("textarea[name=cs_memo]").val(sHTML);
		var cs_memo			= $('textarea[name=cs_memo]').val();

		/*alert(
			"re_type : "+re_type+"\n"
			+"ordercode : "+ordercode+"\n"
			+"idx : "+idx+"\n"
			+"idxs : "+idxs+"\n"
			+"paymethod : "+paymethod+"\n"
			+"return_store_code : "+return_store_code+"\n"
			+"rechange_type : "+rechange_type+"\n"
			+"sel_code : "+sel_code+"\n"
			+"sel_sub_code : "+sel_sub_code+"\n"
			+"memo : "+memo+"\n"
			+"admin_memo : "+admin_memo+"\n"
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
			+"receipt_post5 : "+receipt_post5+"\n"
			+"return_deli_price : "+return_deli_price+"\n"
			+"return_deli_receipt : "+return_deli_receipt+"\n"
			+"return_deli_type : "+return_deli_type+"\n"
			+"return_deli_memo : "+return_deli_memo+"\n"
			+"cs_memo : "+cs_memo);
			return;*/

		var fd = new FormData();
		
		fd.append('mode',"redelivery");
		fd.append('re_type',re_type);
		fd.append('ordercode',ordercode);
		fd.append('idx',idx);
		fd.append('idxs',idxs);
		fd.append('paymethod',paymethod);
		fd.append('return_store_code',return_store_code);
		fd.append('rechange_type',rechange_type);
		fd.append('sel_code',sel_code);
		fd.append('sel_sub_code',sel_sub_code);
		fd.append('memo',memo);
		fd.append('admin_memo',admin_memo);
		fd.append('bankcode',bankcode);
		fd.append('bankaccount',bankaccount);
		fd.append('bankuser',bankuser);
		fd.append('bankusertel',bankusertel);
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

		fd.append('return_deli_price',return_deli_price);
		fd.append('return_deli_receipt',return_deli_receipt);
		fd.append('return_deli_type',return_deli_type);
		fd.append('return_deli_memo',return_deli_memo);
		fd.append('cs_memo',cs_memo);
		fd.append('pg_ordercode',pg_ordercode);
		fd.append('cancel_pc_type',cancel_pc_type);

		
		
		
		$.each($("input[type='file']"), function(i, tag) {
			$.each($(tag)[0].files, function(i, file) {
				fd.append(tag.name, file);
			});
		});
		/*for (var pair of fd.entries()) {
			console.log(pair[0]+ ', ' + pair[1]); 
		}*/
		//return;

		if(confirm(alert_text+'를 하시겠습니까?')){
			if (re_type == '' && (paymethod =='C' || paymethod =='V' || paymethod =='M')) { // 카드, 계좌이체 결제일 경우에는 취소로 보낸이후에 취소로직으로 이동한다.
				<?php if($pg_type=="A"){?>
				var sitecd = '<?=$pgid_info["ID"]?>';
				var sitekey = '<?=$pgid_info["KEY"]?>';
				var sitepw = "<?=$pgid_info['PW']?>";	
				$(".button_open").hide();
				$(".button_close").show();
				$.post("<?=$Dir?>paygate/<?=$pg_type?>/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, sitepw:sitepw, ordercode:pg_ordercode, real_ordercode:ordercode, pc_type:cancel_pc_type,mod_mny:each_price},function(data){
					if(data.res_code !='N'){

						fd.append('pgcancel_type',data.type);
						fd.append('pgcancel_res_code',data.res_code);
						fd.append('pgcancel_res_msg',data.res_msg);
						
						$.ajax({
							url:"cscenter_order_cancel_indb.php",
							type:'POST',
							data:fd,
							dataType: "json",
							async:false,
							cache:false,
							contentType:false,
							processData:false,
							success: function(data){
								alert(data.msg);
								if(data.type == 1){ 
									window.opener.location.reload();
									//window.close();
									window.location.replace('cscenter_order_cancel_detail.php?type=<?=$type?>&oc_no='+data.oc_no);
								}
							}
						});
					} else {
						$(".button_open").show();
						$(".button_close").hide();
						alert(data.msg);
					}
				},"json");
				<?}?>
			} else {
				$.ajax({
					url:"cscenter_order_cancel_indb.php",
					type:'POST',
					data:fd,
					dataType: "json",
					async:false,
					cache:false,
					contentType:false,
					processData:false,
					success: function(data){
						alert(data.msg);
						if(data.type == 1){ 
							window.opener.location.reload();
							//window.close();
							window.location.replace('cscenter_order_cancel_detail.php?type=<?=$type?>&oc_no='+data.oc_no);
						}
					}
				});
			}
		}
	});

});
function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>

<div class="pop_top_title"><p><?=$type_text?>요청서</p></div>

<section class="online-as">
	<form name=orderCancelForm id='orderCancelForm' action="<?=$_SERVER['PHP_SELF']?>" method=post enctype='multipart/form-data'>
	<input type=hidden name=re_type value="<?=$re_type?>">
	<input type=hidden name=ordercode value="<?=$ordercode?>">
	<!--교환후 재생성된 주문 취소시 기존에 pg사로 넘겨줬던 주문번호를 넘겨준다-->
	<input type=hidden name=pg_ordercode value="<?=$_ord->pg_ordercode?>">
	<input type=hidden name=paymethod value="<?=$_ord->paymethod[0]?>">
<?
	if (strstr('CB', $re_type)) {
		$address = str_replace("\n"," ",trim($_ord->receiver_addr));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$post5 = str_replace("우편번호 : ","",$post);

		$receipt_name	= $_ord->receiver_name;
		$receipt_tel			= $_ord->receiver_tel1;
		$receipt_mobile	= $_ord->receiver_tel2;
		$receipt_addr		= $address;
		$receipt_post5	= $post5;
	} else {
		$receipt_name	= "";
		$receipt_tel			= "";
		$receipt_mobile	= "";
		$receipt_addr		= "";
		$receipt_post5	= "";
	}
?>
	<input type=hidden name=receipt_name value="<?=$receipt_name?>">
	<input type=hidden name=receipt_tel value="<?=$receipt_tel?>">
	<input type=hidden name=receipt_mobile value="<?=$receipt_mobile?>">
	<input type=hidden name=receipt_addr value="<?=$receipt_addr?>">
	<input type=hidden name=receipt_post5 value="<?=$receipt_post5?>">
	<div class="title">
		<h3><span class="point-txt"><?=$ordercode?></span> <?=$type_text?>요청서</h3>
	</div>
	<div class="clear">
		<table class="table-th-left">
			<caption>주문자 정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">주문자 이름</th>
					<td><?=$_ord->sender_name?></td>
				</tr>
				<tr>
					<th scope="row">주문자 아이디</th>
					<td><?=$_ord->id?></td>
				</tr>
				<tr>
					<th scope="row">일반전화번호</th>
					<td><?=$_ord->sender_tel2?></td>
				</tr>
				<tr>
					<th scope="row">휴대전화번호</th>
					<td><?=$_ord->sender_tel?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	if (strstr('CB', $re_type)) {
?>
	<div class="mt_40">
		<h3>수령지</h3>
		<table class="table-th-left">
			<caption>수령지</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">받는사람 이름</th>
					<td><?=$_ord->receiver_name?></td>
				</tr>
				<tr>
					<th scope="row">전화번호</th>
					<td><?=$_ord->receiver_tel1?></td>
				</tr>
				<tr>
					<th scope="row">휴대전화번호</th>
					<td><?=$_ord->receiver_tel2?></td>
				</tr>
				<tr>
					<th scope="row">주소</th>
					<td><?="[".$receipt_post5."] ".$receipt_addr?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	}
?>
	<div class="mt_40">
		<h3><?=$type_text?>요청상품</h3>
		<table class="table-th-top02">
			<caption><?=$type_text?>요청상품</caption>
			<colgroup>
				<col style="width:8%">
				<col style="width:19%">
				<col style="width:8%">
				<col style="width:10%">
				<col style="width:10%">
				<col style="width:5%">
				<col style="width:10%">
				<col style="width:8%">
				<col style="width:8%">
				<col style="width:auto">
			<?if($re_type !=''){?>
				<col style="width:8%">
				<col style="width:8%">
			<?}?>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">주문번호</th>
					<th scope="col">상품명</th>
					<th scope="col">옵션</th>
					<th scope="col">정상가</th>
					<th scope="col">판매가</th>
					<th scope="col">수량</th>
					<th scope="col">총금액</th>
					<th scope="col">상태</th>
				<?if($re_type !=''){?>
					<th scope="col">매장</th>
					<th scope="col">배송정보</th>
				<?}?>
				</tr>
			</thead>
			<tbody>
<?
	list($prod_total)=pmysql_fetch_array(pmysql_query("select count(*) as prod_total from tblorderproduct WHERE ordercode='".$ordercode."' "));

	if (ord($idx)) $add_qry	= " AND a.idx='".$idx."' ";

	#주문상품
	$sql = "SELECT 
					a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
					a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
					a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
					b.minimage, a.option_type, a.option_price, a.option_quantity, 
					a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
					a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
					a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, a.use_epoint, b.option1_tf, option2_tf, option2_maxlen, 
					a.delivery_type, a.store_code, a.reservation_date, a.oc_no, b.prodcode, b.colorcode 
				FROM 
					tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
				WHERE 
					a.ordercode='".$ordercode."' {$add_qry} 
				ORDER BY a.vender, a.idx ";


	if (strstr('C', $re_type)) {

		$erp_result=pmysql_query($sql,get_db_conn());
		while($erp_row=pmysql_fetch_object($erp_result)) {
			if ($erp_row->prodcode !='' && $erp_row->colorcode !='') {
				//ERP 상품의 사이즈 수량정보를 쇼핑몰에 업데이트한다.
				getUpErpSizeStockUpdate($erp_row->productcode, $erp_row->prodcode, $erp_row->colorcode);
			}
		}
		pmysql_free_result($erp_result);
	}

	$result	= pmysql_query($sql,get_db_conn());
	$total		= pmysql_num_rows($result);

	$pc_type	= ($total==$prod_total)?"ALL":"PART";

	//재주문건 생성이후 환불요청시 부분취소로 pg사에 넘겨준다.
	$cancel_pc_type=$pc_type;
	if($_ord->ordercode != $_ord->pg_ordercode) $cancel_pc_type="PART";

	$op_idxs						=  "";
	$op_reorderidx				=  "";
	$op_store_code				=  "";
	$t_op_price						=  0;
	$t_op_dc_coupon_price	=  0;
	$t_op_dc_use_point			=  0;
	$t_op_dc_use_epoint			=  0;
	$t_op_dc_price				=  0;
	$t_op_deli_price				=  0;
	$t_op_total_price				=  0;
	$t_op_total_quantity			=  0;

	$prodcd							= "";
	$colorcd						= "";
	
	while($row=pmysql_fetch_object($result)) {

		$op_idxs	= $op_idxs?$op_idxs."|".$row->idx:$row->idx;
		
		//if($re_type	== "C") {
			$prodcd		= $row->prodcode;
			$colorcd	= $row->colorcode;
		//}

		//배송비로 인한 보여지는 가격 재조정
		$can_deli_price	= 0;
		$can_total_price	= (($row->price + $row->option_price) * $row->option_quantity) - ($row->coupon_price + $row->use_point + $row->use_epoint) + $row->deli_price;

		list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$row->productcode."%'"));
		//echo $od_deli_price;
		if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
			// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
			list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$row->idx."' and op_step < 40 limit 1"));
			if ($op_idx) { // 상품이 있으면
				if ($row->deli_price > 0) $can_total_price	= $can_total_price - $od_deli_price;
			} else {
				$can_deli_price	= $od_deli_price;
			}
		}

		$t_op_price			+=  ($row->price + $row->option_price) * $row->option_quantity;
		$t_op_dc_coupon_price	+=  $row->coupon_price;
		$t_op_dc_use_point			+=  $row->use_point;
		$t_op_dc_use_epoint			+=  $row->use_epoint;
		$t_op_dc_price	+=  $row->coupon_price + $row->use_point + $row->use_epoint;
		if ($pc_type == 'ALL') {
			$t_op_deli_price	+=  $row->deli_price;
			$t_op_total_price	+=  (($row->price + $row->option_price) * $row->option_quantity) - ($row->coupon_price + $row->use_point + $row->use_epoint) + $row->deli_price;
		} else if ($pc_type == 'PART') {
			$t_op_deli_price	+=  $can_deli_price;
			$t_op_total_price	+=  $can_total_price;
		}
		$t_op_total_quantity	+=  $row->option_quantity;
		
		if($_ord->oldordno) {

			$reorderidx_sql	= "select op.idx as reorderidx 
				from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode 
				WHERE oi.ordercode='".$_ord->oldordno."' 
				and op.productcode='".$row->productcode."' 
				AND op.op_step='44' 
				AND op.redelivery_type='G' ";
			if ($row->opt1_name) $reorderidx_sql	.= "AND op.opt1_change='".$row->opt1_name."' ";
			if ($row->opt2_name) $reorderidx_sql	.= "AND op.opt2_change='".$row->opt2_name."' ";
			if ($row->text_opt_subject) $reorderidx_sql	.= "AND op.text_opt_subject_change='".$row->text_opt_subject."' ";
			if ($row->text_opt_content) $reorderidx_sql	.= "AND op.text_opt_content_change='".$row->text_opt_content."' ";
			if ($row->option_price_text) $reorderidx_sql	.= "AND op.option_price_text_change='".$row->option_price_text."' ";

			//echo $reorderidx_sql;

			list($reorderidx)=pmysql_fetch_array(pmysql_query($reorderidx_sql));
			$op_reorderidx	= $reorderidx;
		}

		$op_store_code	= $row->store_code;

		$file = getProductImage($Dir.DataDir.'shopimages/product/', $row->tinyimage);

		$optStr	= "";
		$option1	 = $row->opt1_name;
		$option2	 = $row->opt2_name;

		if( strlen( trim( $row->opt1_name ) ) > 0 ) {
			$opt1_name_arr	= explode("@#", $row->opt1_name);
			$opt2_name_arr	= explode(chr(30), $row->opt2_name);
			for($g=0;$g < sizeof($opt1_name_arr);$g++) {
				if ($g > 0) $optStr	.= " / ";
				$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
			}
		}

		if( strlen( trim( $row->text_opt_subject ) ) > 0 ) {
			$text_opt_subject_arr	= explode("@#", $row->text_opt_subject);
			$text_opt_content_arr	= explode("@#", $row->text_opt_content);

			for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
				if ($text_opt_content_arr[$s]) {
					if ($optStr != '') $optStr	.= " / ";
					$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
				}
			}
		}
		if (strstr('C', $re_type)) {

			$op_option1		= $row->opt1_name;
			$op_text_opt_s	= $row->text_opt_subject;

			if ($row->option1 !='' || $row->option2 != '') {

				//변경옵션 관련
				$change_option_html	= '';
				if ($row->option1) {
					$option1_arr	= explode("@#", $row->option1);
					$option1_tf_arr	= explode("@#", $row->option1_tf);
					$option1_cnt	= count($option1_arr);
					if ($row->option_type == '0') {							// 조합형
						//$option_arr		= get_option( $row->productcode );
					} else if ($row->option_type == '1') {					// 독립형
						$option_arr		= get_alone_option( $row->productcode );
					}
					
					for($s=0;$s < sizeof($option1_arr);$s++) {
						$sel_est			= "essential";
						//$sel_est_text	= ' <span class="point-color">*</span>'; // 필수
						if ($row->option_type == '1' && $option1_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
							$sel_est			= "";
							$sel_est_text	= "";
						}
						$change_option_html	.= '
							<tr>
								<th scope="row">'.($option1_arr[$s]=='SIZE'?'사이즈':$option1_arr[$s]).$sel_est_text.'</th>
								<td>
						';

						if ($row->option_type == '0') {							// 조합형
							if (($s + 1) != $option1_cnt){
								$add_opt_onChange	= 'onChange="javascript:option_change(\''.$row->productcode.'\',\''.($s+1).'\', \''.$option1_cnt.'\', this.value)"';
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
								$option_arr		= get_option( $row->productcode );
							} else{ 
								$option_arr		= get_option( $row->productcode , $opt2_name_arr[$s-1], $s);
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

						} else if ($row->option_type == '1') {					// 독립형

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

				if ($row->option2) {
					$option2_arr				= explode("@#", $row->option2);
					$option2_cnt				= count($option2_arr);

					$option2_tf_arr				= explode("@#", $row->option2_tf);
					$option2_maxlen_arr	= explode("@#", $row->option2_maxlen);

					$text_opt_content_arr	= explode("@#", $row->text_opt_content);

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
		}

		$erp_pc_code	= "[".$row->prodcode."-".$row->colorcode."]";
?>
				<tr>
					<td><?=substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)?><br><ins><?=$ordercode?></ins></td>
					<td class="ta_l">
						<div class="product-info clear">
							<a href="javascript:ProductDetail('<?=$row->productcode?>')">
							<img src="<?=$file?>" alt="">
							</a>
							<div class="pro-title ta_l">
								<a href="javascript:ProductDetail('<?=$row->productcode?>')">
								<strong><?=$row->brandname?></strong>
								<p><?=$row->productname?></p>
								<p><?=$erp_pc_code?></p>
								</a>
							</div>
						</div>
					</td>
					<td><?=$optStr?><?if ($row->option_price > 0) {?>(+ <?=number_format($row->option_price)?>원)<?}?></td>
					<td><?=number_format($row->consumerprice)?>원</td>
					<td><?=number_format($row->price)?>원</td>
					<td><?=number_format($row->option_quantity)?></td>
					<td><?=number_format(($row->price + $row->option_price) * $row->option_quantity)?>원</td>
					<td><?=GetStatusOrder("p", $_ord->oi_step1, $_ord->oi_step2, $row->op_step, $row->redelivery_type, $row->order_conf)?></td>
				<?if($re_type !=''){?>
					<td><?=($storelist[$row->store_code]->name !='')?$storelist[$row->store_code]->name:'-'?></td>
					<td><?=$row->deli_num?$delicomlist[$row->deli_com]->company_name."<br>".$row->deli_num:"-"?></td>
				<?}?>
				</tr>
<?
	}
?>
				<tr>
					<td colspan="<?=$re_type !=''?'10':'8'?>" class="ta_r">
					<strong>
					 · 총 수량 : <?=number_format($t_op_total_quantity)?>개&nbsp;&nbsp;
					 · 총 구매금액 : <?=number_format($t_op_price)?>원
					</strong>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?
		if (strstr('CB', $re_type)) {
?>
<?if(strstr('C', $re_type) || count($storelist)){?>
	<div class="mt_40">
		<h3><?=$type_text?>상품</h3>
		<table class="table-th-left">
			<caption><?=$type_text?>상품</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
<?
			if (strstr('C', $re_type)) {
?>
				<tr>
					<th scope="row">교환방법</th>
					<td>
						<div class="radio-set">
							<input id="radio-a01" type="radio" name="rechange_type" value="1" checked>
							<label for="radio-a01">동일 상품 교환</label>
							<input id="radio-a02" type="radio" name="rechange_type" value="2">
							<label for="radio-a02">다른 사이즈로 교환</label>
						</div>
					</td>
				</tr>
				<?=$change_option_html?>
<?
			}
?>
				<?if(count($storelist)){?>
				<tr style='display:none;'>
					<th scope="row">회송매장</th>
					<td>
						<select name="sel_return_store_code" class="select" style="width:143px;">
<?php
						foreach($storelist as $key => $val) {
							if ($key == $op_store_code) {
								$b_store_code_sel	= " selected";
							} else {
								$b_store_code_sel	= "";
							}
?>
							<option value="<?=$key?>"<?=$b_store_code_sel?>><?=$val->name?></option>
<?php
						}
?>
						</select>
					</td>
				</tr>
				<?}?>
			</tbody>
		</table>
	</div>
<?
		}
}
?>
	<div class="mt_40">
		<h3><?=$type_text?>사유</h3>
		<table class="table-th-left">
			<caption><?=$type_text?>사유</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">사유</th>
					<td>
						<select name="b_sel_code" class="select" style="width:143px;"<?if($re_type!=''){?> onChange="javascript:subCodeView(this.value);"<?}?>>
<?php
						$oc_reason_code_cnt = 0;
						//$oc_reason_code_type = $type=='refund'?'cancel':$type;
						$oc_reason_code_type = $type;
						$oc_reason_sub_code_html	= '';
						foreach($oc_reason_code[$oc_reason_code_type] as $key => $val) {
							if ($oc_reason_code_cnt == 0) {
								$oc_reason_code_sel	= " selected";
							} else {
								$oc_reason_code_sel	= "";
							}
?>
							<option value="<?=$key?>"<?=$oc_reason_code_sel?>><?=$val['name']?></option>
<?php
								$oc_reason_code_cnt++;
								if($val['detail_code']) {
									$oc_reason_sub_code_html	.= '
										<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.' hide">
									';
									foreach($val['detail_code'] as $c2key => $c2val) {
										$oc_reason_sub_code_html	.= '
											<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
											<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
										';
									}
									$oc_reason_sub_code_html	.= '
										</div>
									';
								}
							}
?>
						</select>
						<?=$oc_reason_sub_code_html?>
					</td>
				</tr>
				<tr>
					<th scope="row">상세사유</th>
					<td>
						<textarea name="memo" id="exchange-info" placeholder="상세사유를 입력해 주시기 바랍니다." style="width:100%; height:80px"></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">메모</th>
					<td>
						<input type="text" name="admin_memo" id="" title="관리자 메모" value="" style="width:100%;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php
$delivery_fee_type		= array(
				'old'	=> array(
						1	=> "동봉(5천원)",
						2	=> "선불+2500원 동봉",
						3	=> "계좌이체 (5천원)",
						4	=> "신원부담",
						5	=> "기타면제",
						6	=> "고객동봉"
						),
				'plus'	=> array(
						11	=> "왕복택배비(5천원)",
						12	=> "선불+편도택배비(2천5백원)"
						),
				'exc'	=> array(
						13	=> "신원 부담",
						14	=> "고객동봉",
						15	=> "별도계좌이체",
						16	=> "기타면제"
						)
				);
?>
<?
	if (strstr('CB', $re_type)) {
?>
	<div class="mt_40">
		<h3>택배비</h3>
		<table class="table-th-left">
			<caption>택배비</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:400px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">고객부담 택배비</th>
					<td><input type="text" name="return_deli_price" id="return_deli_price" title="고객부담 택배비" value="5000" style="width:100px;">원</td>
					<th scope="row">택배비 수령</th>
					<td><input type="text" name="return_deli_receipt" id="return_deli_receipt" title="택배비 수령" value="" style="width:250px;"></td>
				</tr>
<?
		if ($deli_view_type	== "old") {
?>
				<tr>
					<th scope="row" rowspan=2>택배비</th>
					<td colspan=3>
						<div class="radio-set">
<?php
					$oc_delivery_fee_type_cnt = 0;
					foreach($delivery_fee_type['old'] as $key => $val) {
						if ($oc_delivery_fee_type_cnt == 0) {
							$oc_delivery_fee_type_chk	= " checked";
						} else {
							$oc_delivery_fee_type_chk	= "";
						}
?>
						<input id="radio-delivery-fee<?=$key?>" type="radio" name="return_deli_type" value="<?=$key?>"<?=$oc_delivery_fee_type_chk?>>
						<label for="radio-delivery-fee<?=$key?>"><?=$val?></label>
<?php
						$oc_delivery_fee_type_cnt++;
					}
?>
					</div>
					</td>
				</tr>
				<tr>
					<td colspan=3>
					<input type="text" name="return_deli_memo" id="" title="택배비 메모" value="" style="width:100%;">
					</td>
				</tr>
<?
		} else {
?>
				<tr>
					<th scope="row" rowspan=2>택배비</th>
					<td colspan=3>				
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<caption>택배비</caption>
					<colgroup>
						<col style="width:120px">
						<col style="width:auto">
					</colgroup>
					<tr>
						<th scope="row">택배비별도결제</th>
						<td>
						<div class="radio-set">
<?php
						$oc_delivery_fee_type_cnt = 0;
						foreach($delivery_fee_type['plus'] as $key => $val) {
							if ($oc_delivery_fee_type_cnt == 0) {
								$oc_delivery_fee_type_chk	= " checked";
							} else {
								$oc_delivery_fee_type_chk	= "";
							}
?>
							<input id="radio-delivery-fee<?=$key?>" type="radio" name="return_deli_type" value="<?=$key?>"<?=$oc_delivery_fee_type_chk?>>
							<label for="radio-delivery-fee<?=$key?>"><?=$val?></label>
<?php
							$oc_delivery_fee_type_cnt++;
						}
?>
						</div>
						<!--div class='mt-10'>
						- 결제상태 : 미결제 (가상계좌 국민 1005201486523)
						</div-->
						</td>
					</tr>
					<tr>
						<th scope="row">제외</th>
						<td>
						<div class="radio-set">
<?php
						foreach($delivery_fee_type['exc'] as $key => $val) {
?>
							<input id="radio-delivery-fee<?=$key?>" type="radio" name="return_deli_type" value="<?=$key?>"<?=$oc_delivery_fee_type_chk?>>
							<label for="radio-delivery-fee<?=$key?>"><?=$val?></label>
<?php
						}
?>
						</div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
<?php
		}
?>
			</tbody>
		</table>
	</div>
<?
	}
?>
<?
	if (!strstr('C', $re_type)) {
?>
<?php
		if($_ord->oldordno) {
				$reordercode	= $_ord->oldordno;
				if (ord($op_reorderidx)) $coupon_add_qry	= " AND op.idx='".$op_reorderidx."' ";
		} else {
				$reordercode	= $ordercode;
				if (ord($idx)) $coupon_add_qry	= " AND op.idx='".$idx."' ";
		}
		# 쿠폰
		$couponSql = "SELECT op.productname, op.opt1_name, op.opt2_name, op.text_opt_subject, op.text_opt_content, ci.coupon_name, co.dc_price, ci.coupon_type FROM tblcouponinfo ci ";
		$couponSql.= "JOIN tblcoupon_order co ON co.coupon_code = ci.coupon_code ";
		$couponSql.= "left join tblorderproduct op ON op.idx = co.op_idx ";
		$couponSql.= "WHERE co.ordercode = '".$reordercode."' {$coupon_add_qry} order by op.vender, op.idx";

		//echo $couponSql;
		$coupon_use_html	= "";

		$couponRes =  pmysql_query( $couponSql, get_db_conn() );
		$couponTotal	= pmysql_num_rows($couponRes);
		if ($couponTotal > 0) {
			while( $couponRow = pmysql_fetch_object( $couponRes ) ) {
			# 상품 옵션 정보 저장 및 출력
			

				$cp_opt_name	= "";
				if( strlen( trim( $couponRow->opt1_name ) ) > 0 ) {
					$cp_opt1_name_arr	= explode("@#", $couponRow->opt1_name);
					$cp_opt2_name_arr	= explode(chr(30), $couponRow->opt2_name);
					$s_cnt	= 0;
					for($s=0;$s < sizeof($cp_opt1_name_arr);$s++) {
						if ($cp_opt2_name_arr[$s]) {
							if ($s_cnt > 0) $cp_opt_name	.= " / ";
							$cp_opt_name	.= $cp_opt1_name_arr[$s].' : '.$cp_opt2_name_arr[$s];
							$s_cnt++;
						}
					}
				}
															
				if( strlen( trim( $couponRow->text_opt_subject ) ) > 0 ) {
					$cp_text_opt_subject_arr	= explode("@#", $couponRow->text_opt_subject);
					$cp_text_opt_content_arr	= explode("@#", $couponRow->text_opt_content);

					for($s=0;$s < sizeof($cp_text_opt_subject_arr);$s++) {
						if ($cp_text_opt_content_arr[$s]) {
							if ($cp_opt_name != '') $cp_opt_name	.= " / ";
							$cp_opt_name	.= $cp_text_opt_subject_arr[$s].' : '.$cp_text_opt_content_arr[$s];
						}
					}
				}	
				$coupon_use_html	.= "
					<tr>
						<td>".$couponRow->coupon_name."</td>
						<td>".$couponRow->productname."</td>
						<td>".$cp_opt_name."</td>
						<td>".($couponRow->coupon_type == '9'?"-":number_format( $couponRow->dc_price )."원")."</td>";
				if($_ord->oldordno) {
					$coupon_use_html	.= "
							<td>불가 (재주문건)</td>
						</tr>";
				} else {
					$coupon_use_html	.= "
							<td>가능</td>
						</tr>";
				}
			}
			pmysql_free_result( $couponRes );
		}
?>

	<div class="mt_40">
		<h3>결제정보</h3>
		<table class="table-th-left">
			<caption>결제정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">결제수단</th>
					<td><?=$arpm[$_ord->paymethod[0]]?><?=$t_op_dc_coupon_price>0?" + 쿠폰":""?><?=$t_op_dc_use_point>0?" + 포인트":""?><?=$t_op_dc_use_epoint>0?" + E포인트":""?></td>
				</tr>
				<tr>
					<th scope="row">실결제금액<br>/총주문금액</th>
					<td>· 실결제 금액 : <strong class="point-txt"><?=number_format($t_op_total_price)?>원</strong> <span class="point-txt"><?if($t_op_dc_price>0 || $t_op_deli_price>0) {?> (<?=$t_op_dc_price>0?number_format($t_op_dc_price)."원 할인":""?><?=$t_op_dc_price>0&&$t_op_deli_price>0?" / ":""?><?=$t_op_deli_price>0?number_format($t_op_deli_price)."원 배송비":""?>)<?}?></span> · 총구매 금액 : <strong class="point-txt2"><?=number_format($t_op_price)?>원</strong></td>
				</tr>
				<tr>
					<th scope="row">환불금액</th>
					<td><strong class="point-txt"><?=number_format($t_op_total_price)?>원</strong></td>
				</tr>
				<tr>
					<th scope="row">할인내역 복원</th>
					<td>
			<?
					if ($coupon_use_html) {
			?>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table-th-top02 border-t">
						<tbody>
							<tr>
								<th scope="col">쿠폰명</th>
								<th scope="col">상품</th>
								<th scope="col">옵션</th>
								<th scope="col">할인액</th>
								<th scope="col">복원</th>
							</tr>
							<?=$coupon_use_html?>
						</tbody>
					</table>
			<?
					} else {
						echo "-";
					}
			?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?
	if (strstr("CMYVG", $_ord->paymethod[0])) {
		$bank_class	= " hide";
	} else {
		$bank_class	= "";
	}
?>

	<div class="mt_40<?=$bank_class?>">
		<h3>환불정보</h3>
		<table class="table-th-left">
			<caption>환불정보</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:400px">
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">환불계좌</th>
					<td>
						<select name="bankcode" class="select" style="width:110px;">
<?php
						foreach($oc_bankcode as $key => $val) {
?>
							<option value='<?=$key?>'><?=$val?></option>
<?php
						}
?>
						</select>
						<input type="text" name="bankaccount" id="account-num" title="환불계좌" style="width:250px;">
					</td>
					<th scope="row">예금주</th>
					<td><input type="text" name="bankuser" id="account-nm" title="예금주" style="width:100px;"></td>
				</tr>
				<tr>
					<th scope="row">연락처</th>
					<td colspan=3><input type="text" name="bankusertel" id="account-tel" title="연락처" style="width:125px;"></td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	}
?>
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
						<textarea wrap=off  id="ir1" id="cs_memo" name="cs_memo" label="문의내용" style="width:100%; height:300px"></textarea>
						<div class="add-file-cover">
							<div id="filename0"></div> <!-- 파일 업로드시 파일 주소 출력 -->
							<input type="file" id="add_file" name="file[]" onchange="filenamein(this,'0')">
						</div>
						<div class="btn-wrap1"><span><a href="javascript:add()" class="btn-type1">이미지추가</a></span></div>

						<div id="add_file_div"></div> <!-- 이미지 추가 -->
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40 btn-set button_open">
		<a href="javascript:;" class="btn-type c1 refundSubmit">저장</a>
		<a href="javascript:;" class="btn-type c2 refundClose">닫기</a>
	</div>
	<div class="mt_40 btn-set button_close hide">
		========== 처리중입니다 ==========
	</div>

	<input type=hidden name=idx value="<?=$idx?>">
	<input type=hidden name=idxs value="<?=$op_idxs?>">
	<input type=hidden name=pc_type value="<?=$pc_type?>">
	<!--교환후 재생성된 주문 취소시 기존에 pg사로 넘겨줬던 주문번호를 넘겨준다-->
	<input type=hidden name=cancel_pc_type value="<?=$cancel_pc_type?>">
	<input type=hidden name=each_price value="<?=$t_op_total_price?>">
	<input type=hidden name=option1 value="<?=$op_option1?>">
	<input type=hidden name=text_opt_s value="<?=$op_text_opt_s?>">
	</form>
</section>

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

</script>





<?=$onload?>
</body>
</html>