<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

function create_coupon_number($sno, $num){
	$key = rand(11,55).sprintf('%010d',$sno).rand(55,99).sprintf('%010d',$num);
	$key = str_split($key,12);
	$key = strtoupper(base_convert($key[0], 10, 34).base_convert($key[1], 10, 34));
	$key = str_split($key,4);
	return $key;
}

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$date_start=$_POST["date_start"];
$date_end=$_POST["date_end"];
$date_start=$date_start?$date_start:date("Y-m-d",$CurrentTime);
$date_end=$date_end?$date_end:date("Y-m-d",$CurrentTime);

$type=$_POST["type"];
$productcode=$_POST["productcode"];
$set_productcode=$_POST["set_productcode"];
$coupon_use_type=$_POST["coupon_use_type"];
$coupon_type=$_POST["coupon_type"];
$coupon_name=$_POST["coupon_name"];
$time=$_POST["time"];
$peorid=$_POST["peorid"];
$sale_type=$_POST["sale_type"];
$sale2=$_POST["sale2"];
$sale_money=$_POST["sale_money"];
$amount_floor=$_POST["amount_floor"];
$mini_price=$_POST["mini_price"];
$sale_max_money=$_POST["sale_max_money"];
$bank_only=$_POST["bank_only"];
$use_con_type1=$_POST["use_con_type1"];
$use_con_type2=$_POST["use_con_type2"];
$issue_type=$_POST["issue_type"];
$detail_auto=$_POST["detail_auto"];
$issue_tot_no=$_POST["issue_tot_no"];
$issue_member_no=$_POST["issue_member_no"];
$repeat_id=$_POST["repeat_id"];
$description=$_POST["description"];
$use_point=$_POST["use_point"];
$couponimg=$_FILES["couponimg"];
$delivery_type=$_POST["delivery_type"];
$use_card = $_POST['use_card'];
$coupon_is_mobile=$_POST["coupon_is_mobile"];
$imagepath=$Dir.DataDir."shopimages/etc/";
if ($type=="insert") {
	$coupon_code=substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
	if($couponimg['size'] < 153600) {
		if (ord($couponimg['name']) && file_exists($couponimg['tmp_name'])) {
			$ext = strtolower(pathinfo($couponimg['name'],PATHINFO_EXTENSION));
			if ($ext=="gif") {
				$imagename = "COUPON{$coupon_code}.gif";
				move_uploaded_file($couponimg['tmp_name'],$imagepath.$imagename);
				chmod($imagepath.$imagename,0666);
			} else {
				alert_go('쿠폰 이미지 파일은 GIF 파일만 등록 가능합니다.',-1);
			}
		}
	} else {
		alert_go("쿠폰 이미지 파일 용량이 초과되었습니다.\\n\\nGIF 파일 150KB 이하로 올려주시기 바랍니다.",-1);
	}

	if(ord($mini_price)==0) $mini_price=0;
	if(ord($use_con_type1)==0 || $productcode=="ALL") $use_con_type1="N";
	if(ord($use_con_type2)==0 || $productcode=="ALL") $use_con_type2="Y";
	if(ord($repeat_id)==0) $repeat_id="N";
	if(ord($issue_tot_no)==0) $issue_tot_no=0;
	if(ord($sale_money)==0) $sale_money=0;
	if(ord($issue_member_no)==0) $issue_member_no=1;

	if($sale_type=="+" && $sale2=="%") $realsale=1;
	else if($sale_type=="-" && $sale2=="%") $realsale=2;
	else if($sale_type=="+" && $sale2=="원") $realsale=3;
	else if($sale_type=="-" && $sale2=="원") $realsale=4;
	if ($time=="D") {
		$date_start = str_replace("-","",$date_start)."00";
		$date_end = str_replace("-","",$date_end)."23";
	} else {
		$date_start = "-".$peorid;
		$date_end = str_replace("-","",$date_end)."23";
	}

	$sql = "INSERT INTO tblcouponinfo(
	coupon_code	,
	coupon_name	,
	coupon_use_type ,
	coupon_type ,
	date_start	,
	date_end	,
	sale_type	,
	sale_money	,
	amount_floor	,
	mini_price	,
	sale_max_money,
	bank_only	,
	productcode	,
	use_con_type1	,
	use_con_type2	,
	issue_type	,
	detail_auto	,
	issue_tot_no	,
	issue_member_no,
	repeat_id	,
	description	,
	use_point	,
	member		,
	display		,
	date,
	delivery_type,
	use_card,
	coupon_is_mobile,
	time_type) VALUES (
	'{$coupon_code}',
	'{$coupon_name}',
	'{$coupon_use_type}',
	'{$coupon_type}',
	'{$date_start}',
	'{$date_end}',
	'{$realsale}',
	'{$sale_money}',
	'{$amount_floor}',
	{$mini_price},
	'{$sale_max_money}',
	'{$bank_only}',
	'{$productcode}',
	'{$use_con_type1}',
	'{$use_con_type2}',
	'{$issue_type}',
	'{$detail_auto}',
	{$issue_tot_no},
	{$issue_member_no},
	'{$repeat_id}',
	'{$description}',
	'{$use_point}',
	'".($issue_type!="N"?"ALL":"")."',
	'".($issue_type!="N"?"Y":"N")."',
	'".date("YmdHis")."',
	'{$delivery_type}',
	'{$use_card}',
	'{$coupon_is_mobile}',
	'{$time}')";
	pmysql_query($sql,get_db_conn());

	if($issue_type!="N") $url = "market_couponlist_v2.php";
	else $url = "market_couponsupply_v2.php";

	if(is_array($set_productcode)) $set_productcode = array_unique($set_productcode);
	if($productcode == 'CATEGORY'){
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponcategory (coupon_code, categorycode) VALUES ('{$coupon_code}', '{$v}')", get_db_conn());
		}
	}else if($productcode == 'GOODS'){
		foreach($set_productcode as $v){
			pmysql_query("INSERT INTO tblcouponproduct (coupon_code, productcode) VALUES ('{$coupon_code}', '{$v}')", get_db_conn());
		}
	}

	if($_POST['coupon_type'] == '7'){
		for($i=1;$i<=$_POST['issue_max_no'];$i++){
			$paperNum = create_coupon_number($coupon_code, $i.rand(0,1000));
			$paperNum = implode('-',$paperNum);
			pmysql_query("INSERT INTO tblcouponpaper (coupon_code, papercode) VALUES ('{$coupon_code}', '{$paperNum}')", get_db_conn());
		}
	}


	echo "<body onload=\"location.href='$url';\"></body>";
	exit;
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<!--<script type="text/javascript" src="calendar.js.php"></script>-->
<script language="JavaScript">

function Deliver_type(form){
	$("input[name='sale_max_money']").val(0);
	if(form.sale_type.value=='-'){
		document.getElementById("delivery_dis").style.display="block";
		if($("select[name='sale2']").val() == '원'){
			$('#ID_maxPrice').hide();
		}else{
			$('#ID_maxPrice').show();
		}
	}else{
		document.getElementById("delivery_dis").style.display="none";
		$('#ID_maxPrice').hide();
		form.delivery_type[1].checked=true;
	}
}

function CheckForm(form) {
	if(form.coupon_name.value.length==0) {
		alert("쿠폰 이름을 입력하세요.");
		form.coupon_name.focus();
		return;
	}
	if(CheckLength(form.coupon_name)>40) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 20자 이내 혹은 영문/숫자/기호 40자 이내로 입력이 가능합니다.");
		form.coupon_name.focus();
		return;
	}
	content ="아래의 사항을 확인하시고, 등록하시면 됩니다.\n\n"
			 +"--------------------------------------------\n\n"
			 +"* 쿠폰 이름 : "+form.coupon_name.value+"\n\n";

	if (form.time[0].checked) {
		date = "<?=date("Y-m-d");?>";
		if (form.date_start.value<date || form.date_end.value<date || form.date_start.value>form.date_end.value) {
			alert("쿠폰 유효기간 설정이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			form.date_start.focus();
			return;
		}
		content+="* 쿠폰 유효기간 : "+form.date_start.value+" ~ "+form.date_end.value+" 까지\n\n";
	} else {
		if (form.peorid.value.length==0) {
			alert("쿠폰 사용기간을 입력하세요.");
			form.peorid.focus();
			return;
		} else if (!IsNumeric(document.form1.peorid.value)) {
			alert("쿠폰 사용기간은 숫자만 입력 가능합니다.");
			form.peorid.focus();
			return;
		}
		content+="* 쿠폰 사용기간 : "+form.peorid.value+"일 동안\n\n";
	}
	if (form.sale_money.value.length==0) {
		alert("쿠폰 할인 금액/할인률을 입력하세요.");
		form.sale_money.focus();
		return;
	} else if (!IsNumeric(form.sale_money.value)) {
		alert("쿠폰 할인 금액/할인률은 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
		form.sale_money.focus();
		return;
	}
	if(form.sale2.selectedIndex==1 && form.sale_money.value>=100){
		alert("쿠폰 할인률은 100보다 작아야 합니다.");
		form.sale_money.focus();
		return;
	}
	if( $("input[name='coupon_type']").val() == '8' && $("select[name='use_card']").val() == '' ){
		alert("카드 종류를 선택하셔야 합니다.");
		//form.coupon_type.focus();
		return;
	}
	content+="* 쿠폰종류 : "+form.sale_type.options[form.sale_type.selectedIndex].text+"\n\n";
	content+="* 쿠폰 금액/할인률 : "+form.sale_money.value+form.sale2.options[form.sale2.selectedIndex].value+"\n\n";
	if(form.bank_only[0].checked) content+="* 쿠폰 사용가능 결제방법 : 제한 없음\n\n";
	else content+="* 쿠폰 사용가능 결제방법 : 현금 결제만 가능(실시간 계좌이체 포함)\n\n";

	if(form.sale_type.value=="-"){
		if(form.delivery_type[0].checked){
			content+="* 배송포함 여부 : 배송비 포함\n\n";
		}else if(form.delivery_type[1].checked){
			content+="* 배송포함 여부 : 배송비 미포함\n\n";
		}

	}

	/*
	if(form.productcode.value.length==18 && form.checksale[1].checked && form.use_con_type2.checked!=true) {
		alert("쿠폰이 한상품에 적용될경우 구매금액에 제한이 없습니다.");
		nomoney(1);
	}
	*/
	if(form.checksale[1].checked){
		if(form.mini_price.value.length==0){
			alert("쿠폰 결제 금액을 입력하세요.");
			document.form1.mini_price.focus();
			return;
		}else if(!IsNumeric(form.mini_price.value)){
			alert("쿠폰 결제 금액은 숫자만 입력 가능합니다.");
			form.mini_price.focus();
			return;
		}
		content+="* 쿠폰 결제 금액 : "+form.mini_price.value+"원 이상 구매시\n\n";
	} else {
		content+="* 쿠폰 결제 금액 : 제한없음\n\n";
	}
	//content+="* 적용상품군 : "+form.productname.value+"\n\n";

	if(form.detail_auto[0].checked && form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출함\n\n";
	} else if(form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출안함\n\n";
	}

	if(form.description.value.length==0) {
		alert("쿠폰 설명을 입력하세요.");
		form.description.focus();
		return;
	}
	if(CheckLength(form.description)>100) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 50자 이내 혹은 영문/숫자/기호 100자 이내로 입력이 가능합니다.");
		form.description.focus();
		return;
	}
	if((form.issue_type[0].checked || form.issue_type[2].checked) && form.checknum[1].checked){
		alert("즉시 발급시,회원 가입시 쿠폰 발행의 경우 발행 쿠폰수에 제한이 없습니다.");
		nonum(1);
	}
	if(form.checknum[1].checked){
		if(form.issue_tot_no.value.length==0){
			alert("쿠폰 발행수를 입력하세요.");
			form.issue_tot_no.focus();
			return;
		}else if(!IsNumeric(form.issue_tot_no.value)){
			alert("쿠폰 발행수는 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
			form.issue_tot_no.focus();
			return;
		}else if(form.issue_tot_no.value<=0) {
			alert("쿠폰 발행 매수를 입력하세요.");
			form.issue_tot_no.focus();
			return;
		}
		content+="* 발행 쿠폰수 : "+form.issue_tot_no.value+"개\n\n";
	} else {
		content+="* 발행 쿠폰수 : 무제한\n\n";
	}

	if(form.issue_type[0].checked) tempmsg ="즉시 발급용 쿠폰";
	else if(form.issue_type[1].checked) tempmsg ="쿠폰 클릭시 발급";
	else if(form.issue_type[2].checked) tempmsg ="회원 가입시 발급";
	else if(form.issue_type[3].checked) tempmsg ="자동 발급";
	else if(form.issue_type[4].checked) tempmsg ="직접 입력";
	content+="* 발급조건 : "+tempmsg+"\n\n";
	//content+="* 제한사항 : 등급할인혜택과 동시사용 "+form.use_point.options[form.use_point.selectedIndex].text+"\n\n";
	if(form.useimg[0].checked){
		form.couponimg.value="";
		content+="* 쿠폰이미지 : 기본이미지\n\n";
	} else if(form.useimg[1].checked && form.couponimg.value.length==0){
		alert("쿠폰 이미지를 등록하세요.");
		form.couponimg.focus();
		return;
	} else {
		content+="* 쿠폰이미지 : 선택 이미지 등록\n\n";
	}
	content+="--------------------------------------------";
	if(confirm(content)){
		form.type.value="insert";
		form.submit();
	}

}
function changerate(rate){
	$("input[name='sale_max_money']").val(0);
	document.form1.rate.value=rate;
	if(rate=="%") {
		if($("select[name='sale_type']").val() == '-') $('#ID_maxPrice').show();
		document.form1.amount_floor.disabled=false;
	} else {
		if($("select[name='sale_type']").val() == '-') $('#ID_maxPrice').hide();
		document.form1.amount_floor.disabled=true;
	}
}
function nomoney(temp){
	if(temp==1){
		document.form1.mini_price.value="";
		document.form1.mini_price.disabled=true;
		document.form1.mini_price.style.background='#F0F0F0';
		document.form1.checksale[0].checked=true;
	} else {
		document.form1.mini_price.value="0";
		document.form1.mini_price.disabled=false;
		document.form1.mini_price.style.background='white';
		document.form1.checksale[1].checked=true;
	}
}
function nonum(temp){
	if(temp==1){
		document.form1.issue_tot_no.value="";
		document.form1.issue_tot_no.disabled=true;
		document.form1.issue_tot_no.style.background='#F0F0F0';
		document.form1.checknum[0].checked=true;
	} else {
		document.form1.issue_tot_no.value="0";
		document.form1.issue_tot_no.disabled=false;
		document.form1.issue_tot_no.style.background='white';
		document.form1.checknum[1].checked=true;
	}
}
function ViewLayer(layer,display){
	if(document.all){
		document.all[layer].style.display=display;
	} else if(document.getElementById){
		document.getElementById(layer).style.display=display;
	} else if(document.layers){
		document.layers[layer].display=display;
	}
}
function ChoiceProduct(){
	if($(".CLS_coupon:checked").val() == '2'){
		window.open("about:blank","coupon_product","width=600,height=150,scrollbars=no");
		document.form2.action = "coupon_productchoice2.php";
	}else if($(".CLS_coupon:checked").val() == '3'){
		window.open("about:blank","coupon_product","width=700,height=800,scrollbars=yes");
		document.form2.action = "coupon_productchoice3.php";
	}
	document.form2.submit();
}
$(document).ready(function(){
	$(".CLS_coupon").click(function(){
		if($(this).val() == '1'){
			$("#ID_coupon_all").show();
			$("#ID_coupon_goods").hide();
			$("input[name='productcode']").val("ALL");
			$("#layer1").hide();
			$("#ID_productLayer").html('');
		}else{
			$("#ID_coupon_all").hide();
			$("#ID_coupon_goods").show();
			$("input[name='productcode']").val("");
			$("#layer1").show();
			$("#ID_productLayer").html('');
		}
	})
	$(".CLS_coupon_use_type").click(function(){
		if($(this).val() == '2'){
			$('#ID_coupon1').attr("disabled", "disabled");
			$('#ID_coupon2').removeAttr('disabled');
			$('#ID_coupon3').removeAttr('disabled');
			$('#ID_coupon2').trigger('click');
		}else{
			$('#ID_coupon1').removeAttr('disabled')
			$('#ID_coupon2').attr("disabled", "disabled");
			$('#ID_coupon3').attr("disabled", "disabled");
			$('#ID_coupon1').trigger('click');
		}
	})
    /* 동일인 재발급 여부 Y 고정
	$("input[name='repeat_id']").click(function(){
		if($(this).val() == 'Y'){
			$("input[name='issue_member_no']").val('1');
			$("input[name='issue_member_no']").removeAttr('readonly');
		}else{
			$("input[name='issue_member_no']").val('1');
			$("input[name='issue_member_no']").attr('readonly', 'readonly');
		}
	})
    */

	$(".CLS_coupon_type").click(function(){
		$("#ID_issue_type1").removeAttr("disabled");
		$("#ID_issue_type2").removeAttr("disabled");
		$("#ID_issue_type3").removeAttr("disabled");
		$("#ID_issue_type4").removeAttr("disabled");
		$("#ID_issue_type5").removeAttr("disabled");
        $(".CLS_coupon_use_type").each( function( ) {
            $(this).removeAttr("disabled");
        });
        $(".CLS_coupon_use_type").eq(0).trigger('click');
		//coupon_setting( $(this).val() )
		if($(this).val() == '1'){
			$('#ID_issue_type1').trigger('click');

			$("#ID_issue_type1").removeAttr("disabled");
			$("#ID_issue_type2").removeAttr("disabled");
			$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");
		}else if($(this).val() == '2'){
			$('#ID_issue_type3').trigger('click');

			$("#ID_issue_type1").attr("disabled", "disabled");
			$("#ID_issue_type2").attr("disabled", "disabled");
			$("#ID_issue_type3").removeAttr("disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");
		}else if($(this).val() == '3' || $(this).val() == '4' || $(this).val() == '5'){
			$('#ID_issue_type4').trigger('click');

			$("#ID_issue_type1").attr("disabled", "disabled");
			$("#ID_issue_type2").attr("disabled", "disabled");
			$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").removeAttr("disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");
		}else if($(this).val() == '6'){
			$('#ID_issue_type2').trigger('click');

			$("#ID_issue_type1").attr("disabled", "disabled");
			$("#ID_issue_type2").removeAttr("disabled");
			$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");
		}else if($(this).val() == '7'){
			$('#ID_issue_type5').trigger('click');

			$("#ID_issue_type1").attr("disabled", "disabled");
			$("#ID_issue_type2").attr("disabled", "disabled");
			$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").removeAttr("disabled");
		/*}else if($(this).val() == '8'){
			$('#ID_issue_type1').trigger('click');

			$("#ID_issue_type1").removeAttr("disabled");
			$("#ID_issue_type2").removeAttr("disabled");
			$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");*/
//			console.log(  );
		}else if($(this).val() == '9'){
			$('#ID_issue_type1').trigger('click');
            // 무료배송 쿠폰은 장바구니만 가능함
            $(".CLS_coupon_use_type").eq(0).trigger('click'); // 2016-03-13 유동혁 추가
            $(".CLS_coupon_use_type").eq(1).attr("disabled", "disabled"); // 2016-03-13 유동혁 추가
			$("#ID_issue_type1").removeAttr("disabled");
			//$("#ID_issue_type2").removeAttr("disabled");
			//$("#ID_issue_type3").removeAttr("disabled");
            $("#ID_issue_type2").attr("disabled", "disabled");
			//$("#ID_issue_type3").attr("disabled", "disabled");
			$("#ID_issue_type4").attr("disabled", "disabled");
			$("#ID_issue_type5").attr("disabled", "disabled");
		}
	})

    $('input[name="issue_type"]').click( function(){
        $('input[name="issue_member_no"]').val(1);
        if( $(this).val() == 'Y' ) $('#ID_issue_no').show();
        else $('#ID_issue_no').hide();
    });

	$(".CLS_coupon_time").click(function(){
		if($(this).val() == 'D'){
			$("#ID_coupon_timeD").show();
			$("#ID_coupon_timeP").hide();
		}else{
			$("#ID_coupon_timeP").show();
			$("#ID_coupon_timeD").hide();
		}
	})
	//ID_issue_type4
	//issue_type N Y M A CLS_coupon_type
});

/*
function coupon_setting( type ){
	if( type == '8' ){
		$("#ID_floor_select").hide();
		$("#ID_rate_select").hide();
		$("#ID_card_select").show();
		$("input[name='delivery_type']").eq(0).trigger('click');
		$("input[name='delivery_type']").eq(1).attr("disabled", "disabled");
		$("select[name='sale2'] option").eq(1).prop('selected', true);
		$("select[name='sale2'] option").eq(0).prop('disabled', true);
		$("input[name='rate']").val('%');
		$("input[name='sale_money']").val(0).attr('readonly','readonly');
		$("input[name='bank_only']").eq(0).prop("checked", true);
		$("input[name='bank_only']").eq(1).attr("disabled", "disabled");
	} else {
		$("input[name='bank_only']").eq(1).removeAttr("disabled");
		$("input[name='sale_money']").val(0).removeAttr('readonly');
		$("input[name='rate']").val('원');
		$("select[name='sale2'] option").eq(0).prop('disabled', false);
		$("select[name='sale2'] option").eq(0).prop('selected', true);
		$("input[name='delivery_type']").eq(1).removeAttr("disabled");
		$("input[name='delivery_type']").eq(1).trigger('click');
		$("#ID_card_select").hide();
		$("#ID_rate_select").show();
		$("#ID_floor_select").show();
	}

}*/

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt;<span>새로운 쿠폰 생성하기</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name='productcode' value="ALL">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">새로운 쿠폰 생성하기</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원들에게 자유롭게 쿠폰발행 서비스를 진행할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰 기본정보 입력 <span>쿠폰 사용은 한 주문건에 대해서 한개의 쿠폰만 사용이 가능합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<tr>
					<th><span>쿠폰 이름</span></th>
					<td><INPUT maxLength=40 size=70 name=coupon_name class="input"><br><span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~ [한글 20자를 넘길수 없습니다]</b></span></td>
				</tr>
				<tr>
					<th><span>쿠폰 타입</span></th>
					<td>
						<INPUT type='radio' value='1' name='coupon_type' class = 'CLS_coupon_type' CHECKED> 상품 쿠폰 &nbsp;
						<INPUT type='radio' value='2' name='coupon_type' class = 'CLS_coupon_type'> 신규가입 쿠폰 &nbsp;
						<INPUT type='radio' value='3' name='coupon_type' class = 'CLS_coupon_type'> 기념일 쿠폰 &nbsp;
						<INPUT type='radio' value='4' name='coupon_type' class = 'CLS_coupon_type'> 첫구매 쿠폰 &nbsp;
						<INPUT type='radio' value='5' name='coupon_type' class = 'CLS_coupon_type'> 등급업 쿠폰 &nbsp;
						<INPUT type='radio' value='6' name='coupon_type' class = 'CLS_coupon_type'> 기타 쿠폰 &nbsp;
						<INPUT type='radio' value='7' name='coupon_type' class = 'CLS_coupon_type'> 페이퍼 쿠폰 &nbsp;
						<!--<INPUT type='radio' value='8' name='coupon_type' class = 'CLS_coupon_type'> 카드 쿠폰 &nbsp;-->
                        <INPUT type='radio' value='9' name='coupon_type' class = 'CLS_coupon_type'> 무료 배송 쿠폰 &nbsp;
					</td>
				</tr>
				<tr>
					<th><span>유효 설정</span></th>
					<td>
					<div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td style="padding:5px 0px">
							<INPUT type=radio value=D name=time class = 'CLS_coupon_time'> 기간으로 설정 &nbsp;
							<INPUT type=radio CHECKED value=P name=time class = 'CLS_coupon_time'> 일로 설정 &nbsp;
						</td>
					</tr>
					<tr>
						<td style="padding:5px 0px" height=30><span id = 'ID_coupon_timeD' style='display:none;'>&nbsp;기간설정 : <INPUT onfocus=this.blur(); onclick=Calendar(event) size=15 name=date_start value="<?=$date_start?>" class="input_selected"> 부터 </span><span id = 'ID_coupon_timeP'>&nbsp;일설정 : 발행 후 <INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid class="input">일 동안, </span><INPUT  onfocus=this.blur(); onclick=Calendar(event) size=15 name=date_end value="<?=$date_end?>" class="input_selected"> 까지 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </td>
					</tr>
					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<th><span>쿠폰종류 선택</span></th>
					<td>
                    	<div class="table_none">
                   		<table width="100%">
                        <tr>
                            <td>
                            <SELECT style="WIDTH: 100px" name=sale_type class="select" onchange="javascript:Deliver_type(document.form1);">
                            <OPTION value=- selected>할인 쿠폰</OPTION>
                            <!--OPTION value=+>적립 쿠폰</OPTION-->
                            </SELECT>
                            <span class="font_orange"> * 할인쿠폰은 구매시 즉시 할인됩니다.<!--할인쿠폰은 구매시 즉시 할인되며, 적립쿠폰은 구매시 추가 적립금이 지급됩니다.--></span></td>
                        </tr>
                        <tr style="display:none" id="delivery_dis">
                            <td class="td_con1">
                            <INPUT type=radio value=Y name=delivery_type>배송비 포함 &nbsp;
                            <INPUT type=radio value=N name=delivery_type CHECKED>배송비 미포함
                            </td>
                        </tr>
                    	</table>
                        </div>
                    </td>
                </tr>
				<tr style='display: none;' id='ID_card_select'>
					<th><span>사용카드 선택</span></th>
					<td>
						<SELECT name='use_card' >
							<option value='' >==선택==</option>
<?php
					foreach( $KCP_CARD as $card_name=>$card_code ){
?>
							<option value='<?=$card_code?>'><?=$card_name?></option>
<?php
					}
?>
						</SELECT>
					</td>
				</tr>
				<tr id='ID_rate_select'>
					<th><span>금액/할인율 선택</span></th>
					<td>
					<SELECT style="WIDTH: 100px" onchange="changerate(this.value)" name=sale2 class="select">
						<OPTION value='원' selected>금액</OPTION> <OPTION value='%'>할인(적립)율</OPTION>
					</SELECT>
					→
					<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money class="input">
					<INPUT class="input_hide1" readOnly size=1 value=원 name=rate>
                    <span class="font_orange"> * 배송비 쿠폰은 금액과 상관없이 무료배송됩니다.</span>
					</td>
				</tr>

				<tr id='ID_floor_select'>
					<th><span>금액절삭</span></th>
					<td>
					<SELECT disabled name=amount_floor class="select">
<?php
					$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
					$arcnt = count($arfloor);
					for($i=1;$i<$arcnt;$i++){
						echo "<option value=\"{$i}\"";
						if($amount_floor==$i) echo " selected";
						echo ">{$arfloor[$i]}</option>";
					}
?>
					</SELECT>
					</td>
				</tr>
				<tr style = 'display:none;' id = 'ID_maxPrice'>
					<th><span>할인 상한 금액</span></th>
					<td>
						<INPUT maxLength='10' size='10' name='sale_max_money' class="input" value = '0' onkeyup='strnumkeyup(this);' style="PADDING-RIGHT: 5px; TEXT-ALIGN: right"> 원
						 <span class="font_orange"> * %할인의 경우 최대로 할인 받을수 있는 금액(0원일 경우 제한 없음).</span>
					</td>
				</tr>
				<tr>
					<th><span>쿠폰 결제금액 사용제한</span></th>
					<td>
					<INPUT onclick=nomoney(1) type=radio CHECKED name=checksale>제한 없음  &nbsp;
					<INPUT onclick=nomoney(0) type=radio name=checksale><INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=mini_price class="input_disabled">원 이상 주문시 가능
					<SCRIPT>nomoney(1);</SCRIPT>
					</td>
				</tr>
				<tr>
					<th><span>쿠폰사용가능 결제방법</span></th>
					<td>
						<INPUT type=radio CHECKED value=N name=bank_only>제한 없음  &nbsp;
						<INPUT type=radio value=Y name=bank_only><B>현금 결제</B>만 가능(실시간 계좌이체 포함)
					</td>
				</tr>
				<tr>
					<th><span>쿠폰사용 방법</span></th>
					<td>
						<INPUT type='radio' value='1' name='coupon_use_type' class = 'CLS_coupon_use_type' checked>장바구니 쿠폰
						<INPUT type='radio' value='2' name='coupon_use_type' class = 'CLS_coupon_use_type'>상품별 쿠폰
					</td>
				</tr>
				<tr>
					<th><span>쿠폰노출 타입</span></th>
					<td>
						<INPUT type='radio' value='A' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile' checked>ALL
						<INPUT type='radio' value='P' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile'>PC
						<INPUT type='radio' value='M' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile'>MOBILE
						<INPUT type='radio' value='T' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile'>APP
						<INPUT type='radio' value='B' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile'>PC + MOBILE
						<INPUT type='radio' value='C' name='coupon_is_mobile' class = 'CLS_coupon_is_mobile'>PC + APP
						<div class="font_orange">접속 방법에 따라 선택할 수 있는 쿠폰이 달라집니다.</div>
					</td>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰 부가정보 입력</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=200></col>
				<col width=></col>
				<tr>
					<th><span>쿠폰 적용 대상 선택</span></th>
					<td class="td_con1">
						<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon1' value = '1' checked> 전체상품
						<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon2' value = '2' disabled> 카테고리
						<input type = 'radio' name = 'couponaccept' class = 'CLS_coupon' id = 'ID_coupon3' value = '3' disabled> 개별상품
						<div class="font_orange">* 전체상품 : 장바구니 쿠폰</div>
						<div class="font_orange">* 카테고리, 개별상품 : 상품별 쿠폰</div>
					</td>
				</tr>
				<tr>
					<th><span>쿠폰 적용 상품군 선택</span></th>
					<td class="td_con1">
						<div class="table_none" id = 'ID_coupon_all'>
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="80">적용 상품군  :  </td>
									<td>전체상품
										<INPUT type = 'hidden' name='productname' class="input" value='전체상품'>
									</td>
								</tr>
							</table>
						</div>
						<div class="table_none" id = 'ID_coupon_goods' style = 'display:none;'>
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100"><a href="javascript:ChoiceProduct();"><img src="images/btn_select2.gif" border="0" hspace="2"></a></td>
									<td width="80">적용 상품군  :  </td>
									<td>
										<INPUT type = 'hidden' name='productname' class="input" value='-'>
										<div id = 'ID_productLayer'></div>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<th colspan="2">
					<div id=layer1 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<col width=200></col>
					<col width=></col>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" border="0">쿠폰 사용조건</td>
						<td class="td_con1">
						<!--INPUT type=checkbox CHECKED value=Y name=use_con_type1-->다른 상품과 함께 구매시에도, 해당 쿠폰을 사용합니다.
						<!--INPUT type=checkbox value=N name=use_con_type2>선택된 카테고리(상품)을 제외하고 적용합니다.-->
						<INPUT type='hidden' value='Y' name='use_con_type1'>
						<INPUT type='hidden' value='Y' name='use_con_type2'>
						</td>
					</tr>
					</TABLE>
					</div>
					</th>
				</tr>
				<tr>
					<th><span>쿠폰 발급조건</span></th>
					<td class="td_con1">
						<INPUT onclick="ViewLayer('layer2','none')" type=radio CHECKED value=N name=issue_type id = 'ID_issue_type1'>
						쿠폰 발급용 쿠폰&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* [생성된 쿠폰 즉시 발급] 에서 발급 가능합니다.</span><BR>

						<INPUT onclick="ViewLayer('layer2','block')" type=radio value=Y name=issue_type id = 'ID_issue_type2'>
						쿠폰 클릭시 발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원이 쿠폰 클릭시 자동 발급됩니다. </span><BR>

						<INPUT onclick="ViewLayer('layer2','none')" type=radio value=M name=issue_type id = 'ID_issue_type3'>
						회원 가입시 발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원가입하면 자동 발급됩니다.</span><BR>

						<INPUT onclick="ViewLayer('layer2','none')" type=radio value=A name=issue_type id = 'ID_issue_type4' disabled>
						자동 발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 조건에 맞을시 자동으로 발급됩니다.</span><BR>

						<INPUT onclick="ViewLayer('layer2','none')" type=radio value=P name=issue_type id = 'ID_issue_type5' disabled>
						직접 입력 발급&nbsp;&nbsp;&nbsp;&nbsp;
						<INPUT onkeyup=strnumkeyup(this); maxLength=5 size=5 name=issue_max_no value = '100' class="input">장
						<span class="font_orange">* 쿠폰 번호를 직접 입력하면 발급됩니다.</span>
					</td>
				</tr>
                 <!-- 재발급 가능 쿠폰 수 => 보유가능 쿠폰 수 -->
                <tr id='ID_issue_no' style='display: none;' >
                    <th><span>보유가능 쿠폰 수</span></th>
                    <td class="td_con1">
                        <INPUT name='issue_member_no' value = '1' maxLength='4' size='4' class="input" onkeyup='strnumkeyup(this);'>매 한정
                    </td>
                </tr>
				<tr>
					<th colspan="2">
					<div id=layer2 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
							<col width=200></col>
							<col width=></col>
							<tr>
								<td class="table_cell"><img src="images/icon_point2.gif" border="0">쿠폰 자동노출 여부</td>
								<td class="td_con1">
								상품 상세페이지 상세설명 상단에 쿠폰을 자동
								<SELECT name=detail_auto class="select">
								<OPTION value=Y selected>노출함</OPTION>
								<OPTION value=N>노출안함</OPTION>
								</SELECT>
								<IMG height=5 width=0><BR><span class="font_orange"> * 회원이 직접 쿠폰을 클릭함으로서 발급받을 수 있는 서비스입니다.</span>
								</td>
							</tr>
							<tr>
								<td class="table_cell"><img src="images/icon_point2.gif" border="0">총 발행 쿠폰 수</td>
								<td class="td_con1">
								<INPUT onclick=nonum(1) type=radio CHECKED name=checknum>무제한 &nbsp;
								<INPUT onclick=nonum(0) type=radio name=checknum><INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=issue_tot_no class="input">매 한정
								<SCRIPT>nonum(1);</SCRIPT>
								</td>
							</tr>
							<!-- <tr>
								<td class="table_cell"><img src="images/icon_point2.gif" border="0">동일인 재발급 가능여부</td>
								<td class="td_con1">
								<INPUT type=radio value=Y name=repeat_id checked>가능  &nbsp;
								<INPUT type=radio value=N name=repeat_id>불가능
								</td>
								</div>
							</tr> -->
                            <INPUT type=hidden value="Y" name="repeat_id">
                            <!-- 재발급 가능 쿠폰 수 발급조건 하단으로 위치 변경 -->
                            <!-- <tr>
                                <td class="table_cell"><img src="images/icon_point2.gif" border="0">동일인 재발급 쿠폰 수</td>
                                <td class="td_con1">
                                    <INPUT name='issue_member_no' value = '1' maxLength='4' size='4' class="input" onkeyup='strnumkeyup(this);'>매 한정
                                </td>
                                </div>
                            </tr> -->
						</TABLE>
					</div>
					</th>
				</tr>
				<tr>
					<th>
						<span>쿠폰 설명</span>
					</th>
					<td class="td_con1">
						<INPUT maxLength=200 size=91 name=description style=width:99% class="input"> <!-- <span class="font_orange"> * 입력한 쿠폰설명은 쿠폰이미지 상단에 출력됩니다.</span> -->
						<INPUT type = 'hidden' name='use_point' value = 'Y'>
					</td>
				</tr>
				<!--tr>
					<th><span>쿠폰 적용 제한 사항</span>
					<td class="td_con1">
					쿠폰과 등급회원 할인/적립 혜택 동시
					<SELECT name=use_point class="select">
					<OPTION value=Y selected>적용함</OPTION>
					<OPTION value=A>적용안함</OPTION>
					</SELECT>
					</td>
				</tr-->
				<tr style="display:none;">
					<th><span>쿠폰 이미지 설정</span>
					<td class="td_con1">
					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><INPUT type=radio CHECKED name=useimg>기본 이미지 사용<br></td>
						</tr>
						<tr>
							<td><IMG src="images/sample/market_couponsampleimg.gif"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><IMG height=3 width=0><INPUT type=radio name=useimg>자유제작 이미지 등록<span class="font_orange">(*GIF 파일 150KB 이하로 올려주시고, 권장 사이즈는 350*150 입니다.)</span></td>
						</tr>
						<tr>
							<td>
							<input type=file name=couponimg size="50">
							<!--
                            <input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
                            <div class="file_input_div">
                            <input type="button" value="찾아보기" class="file_input_button" />
                            <input type=file name=couponimg value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ></div>
							-->
                            </td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm(document.form1);"><img src="images/btn_cupon.gif"  border="0"></a></td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>

						<dl>
							<dt><span>쿠폰 사용은 한번의 주문건에서만 사용할 수 있습니다.</span></dt>
						</dl>
						<dl>
							<dt><span>쿠폰사용 선택  </span></dt>
							<dd>① 할인쿠폰은 구매시 즉시 할인됩니다.<!-- <br>② 적립쿠폰은 구매시 추가 적립금이 지급됩니다. --></dd>
						</dl>
						<dl>
							<dt><span>쿠폰상품 선택 :모든상품,일부카테고리,일부상품 으로 구분 됩니다.</span></dt>
						</dl>
						<dl>
							<dt><span>발생한 쿠폰은 로그인 후 마이페이지 정보에서 확인 할 수 있습니다.</span></dt>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			<form name=form2 action="coupon_productchoice2.php" method=post target=coupon_product>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php include("copyright.php"); ?>
