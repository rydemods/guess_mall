<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/cscenter_ascode.php");
include("access.php");

$ordercode=$_REQUEST["ordercode"];
$idx=$_REQUEST["idx"];
/*
$ordercode="2016093011275423282AX";
$productcode="002001001000000747";
$idx="4466";
*/
#주문정보, 회원정보 가져오기
$sql="select 
o.ordercode, o.receiver_name, o.receiver_tel2, o.receiver_addr, o.oi_step2, o.oi_step1, o.sender_name as name, o.sender_email as email, o.sender_tel as mobile,
op.deli_gbn,  op.opt2_name, op.op_step, op.redelivery_type, op.order_conf, op.price, op.quantity, op.deli_num, op.deli_com,
d.postcode_new, d.addr1, d.addr2, 
p.consumerprice, p.tinyimage, p.productname, p.productcode, p.prodcode, p.colorcode,
pb.brandname,
s.name as storename
from tblorderinfo o 
left join tblorderproduct op on (o.ordercode=op.ordercode) 
left join tblmember m on (o.id=m.id) 
left join tbldestination d on (m.id=d.mem_id and base_chk='Y') 
left join tblproduct p on(op.productcode=p.productcode)
left join tblproductbrand pb on (p.brand=pb.bridx)
left join tblstore s on (op.store_code=s.store_code)
where o.ordercode='".$ordercode."' and op.idx='".$idx."'";
$result=pmysql_query($sql);
$data=pmysql_fetch_array($result);


#매장정보 가져오기
$store_sql="select * from tblstore order by name";
$store_result=pmysql_query($store_sql);

# 배송업체를 불러온다.
$del_sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$del_result=pmysql_query($del_sql,get_db_conn());
$delicomlist=array();
while($del_data=pmysql_fetch_object($del_result)) {
	$delicomlist[trim($del_data->code)]=$del_data;
}

pmysql_free_result($del_result);

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

#주문일
$order_date=substr($data['ordercode'],'0','4').'-'.substr($data['ordercode'],'4','2').'-'.substr($data['ordercode'],'6','2');

#상품이미지
$product_img = getProductImage($Dir.DataDir.'shopimages/product/', $data['tinyimage']);

#라디오박스 체크
$checked["gubun"]["1"]="checked";
$checked["receipt"]["1"]="checked";
$checked["repair"]["F"]="checked";
$checked["depreciation"]["Y"]="checked";
$checked["cash"]["Y"]="checked";
$checked["cashcheck"]["1"]="checked";


?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>온라인 AS</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="styleSheet" href="/css/common.css" type="text/css">
<link rel="stylesheet" href="/admin/static/css/crm.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script src="../js/jquery.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<div class="pop_top_title"><p>온라인 AS 등록</p></div>

<script language="JavaScript">
$(document).ready(function(){
	
	$(".rowon").click(function() { $(".receipt_none").show(function(){
		cachtype=$(':radio[name="cash_type"]:checked').val();
		if(cachtype=="Y"){
			$(".cash_none").show(function(){});
		}else{
			$(".cash_none").hide(function(){});
		}
	}); });
	$(".rowoff").click(function() { $(".receipt_none").hide(function(){}); $(".cash_none").hide(function(){}); });
	
	$(".cashon").click(function() { $(".cash_none").show(function(){}); });
	$(".cashoff").click(function() { $(".cash_none").hide(function(){}); });

	

});

function zip_change(){
	$(".redisplay").toggle(
		function(){
		
			if($("#place_type").val()=="0"){
				$("#place_type").val("1");
			}else{
				
				$("#place_type").val("0");
			}
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

	$("#onlinecsform").submit();
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

function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
</script>
<form name="onlinecsform" id="onlinecsform" method="post" action="./cscenter_online_as_indb.php" enctype="multipart/form-data">
<input type="hidden" name="place_type" id="place_type" value="0">
<input type="hidden" name="mode" id="mode" value="insert">
<input type="hidden" name="ordercode" id="ordercode" value="<?=$ordercode?>">
<input type="hidden" name="productcode" id="productcode" value="<?=$data["productcode"]?>">
<input type="hidden" name="productidx" id="productidx" value="<?=$idx?>">

<section class="online-as">
	<div class="order-info">
		<h3>A/S 구분</h3>
		<table class="table-th-left">
			<caption>A/S 구분</caption>
			<colgroup>
				<col style="width:120px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">선택</th>
					<td>
						<div class="radio-set">
						<?foreach($as_gubun as $ag=>$agv){?>
							<input type="radio" name="as_type" value="<?=$ag?>" <?=$checked["gubun"][$ag]?>><?=$agv?>&nbsp;&nbsp;
						<?}?>
						</div>
					</td>
				</tr>
				
			</tbody>
		</table>
	</div>

	<div class="order-info">
		<h3>사용자 정보</h3>
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
			<caption>수령지</caption>
			<thead>
				<tr>
					<th scope="col" colspan="2">수령지 <span class="btn-small"><a href="javascript:zip_change()" class="btn-type c2">수령지 변경</a></span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<p>[받는 분] <?=$data['receiver_name']?> / <?=$data['receiver_tel2']?></p>
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
					<td><input type="text" style="width:125px" class="input" name="place_name" value="<?=$data['receiver_name']?>" title="주문자 이름" ></td>
				</tr>
				
				<tr>
					<th scope="row">휴대전화</th>
					<td><input type="text" style="width:125px" class="input" name="place_mobile" value="<?=$data['receiver_tel2']?>" title="휴대전화" ></td>
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
		<h3>A/S 신청 상품</h3>
		<table class="table-th-top02">
			<caption>A/S 신청 상품</caption>
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
					<th scope="col">주문상태</th>
					<th scope="col">매장</th>
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
					<td><?=$data['storename']?$data['storename']:"-"?></td>
					<td>
						<?=$data['deli_num']?"<strong>".$delicomlist[trim($data['deli_com'])]->company_name."</strong><p>".$data['deli_num']."</p>":"-"?>
						
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="mt_40">
		<h3>A/S 신청 정보</h3>
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
								<?while($store_data=pmysql_fetch_array($store_result)){?>
									<option value="<?=$store_data["sno"]?>"><?=$store_data["name"]?></option>
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
				
				<tr class=receipt_none>
					<th scope="row">유상수선비</th>
					<td>
						<div class="radio-set">
							<?foreach($as_repair as $ae=>$aev){?>
								<input type="radio" id="repair-a<?=$ae?>" name="repairs_type" value="<?=$ae?>" <?=$checked["repair"][$ae]?>> <label for="repair-a<?=$ae?>"><?=$aev?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr class=receipt_none>
					<th scope="row">현금영수증</th>
					<td>
						<div class="radio-set">
							<?foreach($as_cash as $ac=>$acv){?>
								<input type="radio" id="cash-a<?=$ac?>" name="cash_type" value="<?=$ac?>" class=<?=$as_cash_class[$ac]?> <?=$checked["cash"][$ac]?>> <label for="cash-a<?=$ac?>"><?=$acv?></label>
							<?}?>
						</div>
					</td>
				</tr>

				<tr class=cash_none>
					<th scope="row">현금영수증<br>발행정보</th>
					<td>
						<div class="radio-set">
							<div>
							<input type="radio" id="cashcheck-a01" name="cash_detail_type" value="1" onclick="cach_disabled('1')" checked> <label for="cashcheck-a01">소득공제용</label>
							<select class="cashcheck_1" name="cash_detail_tel1" id="cash_detail_tel1">
								<option value='010'>010</option>
								<option value='011'>011</option>
								<option value='016'>016</option>
								<option value='017'>017</option>
								<option value='018'>018</option>
								<option value='019'>019</option>
							</select>
							- <input type="text" name="cash_detail_tel2" id="cash_detail_tel2" class="cashcheck_1" style="width:50px">
							- <input type="text" name="cash_detail_tel3" id="cash_detail_tel3"class="cashcheck_1" style="width:50px">
							</div>
							<div class="mt_5">
							<input type="radio" id="cashcheck-a02" name="cash_detail_type" value="2" onclick="cach_disabled('2')"> <label for="cashcheck-a02">지출증빙용(사업자등록번호)</label>
							<input type="text" style="width:50px" name="cash_detail_num1" id="cash_detail_num1" class="cashcheck_2" disabled="disabled">
							- <input type="text" style="width:50px" name="cash_detail_num2" id="cash_detail_num2" class="cashcheck_2" disabled="disabled">
							- <input type="text" style="width:50px" name="cash_detail_num3" id="cash_detail_num3" class="cashcheck_2" disabled="disabled">
							</div>
						</div>
					</td>
				</tr>


				<tr>
					<th scope="row">요쳥사항</th>
					<td>
						<textarea name="requests_text" style="width:100%; height:80px"></textarea>
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
					<td><input type="text" name="delivery_cost" id="delivery_cost" title="고객부담 택배비" value="" style="width:100px;"></td>
					<th scope="row">택배비 수령</th>
					<td><input type="text" name="delivery_receipt" id="delivery_receipt" title="택배비 수령" value="" style="width:100px;"></td>
				</tr>
			</tbody>
		</table>
	</div>

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
						<textarea style="width:100%; height:300px" name="cs_memo"></textarea>
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

	<div class="mt_40 btn-set">
		<a href="javascript:as_submit();" class="btn-type c1">저장</a>
		<a href="javascript:window.close();" class="btn-type c2">닫기</a>
	</div>

</section> <!-- // .online-as -->
</form>


<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type=hidden name=ordercode>
</form>

<?=$onload?>
</body>
</html>