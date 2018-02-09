<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once('outline/header_m.php');

$ordercode = $_POST['ordercode'];

?>

<script>
	$(document).on("click", "input[name='up_tr_code']", function(){
		if($(this).val()=='0'){
			$(".CLS_handphone").show();
			$(".CLS_biznumber").hide();
		}else{
			$(".CLS_handphone").hide();
			$(".CLS_biznumber").show();
		}
	});

	$(document).on("click", ".CLS_submitCash", function(){
		if( $("input[name='up_tr_code']").val() == '0' ){
			if( $("input[name='up_mobile1']").val() == '' ){
				alert('핸드폰번호를 입력하세요.');
				return;
			}

			if( $("input[name='up_mobile2']").val() == '' ){
				alert('핸드폰번호를 입력하세요.');
				return;
			}

			if( $("input[name='up_mobile3']").val() == '' ){
				alert('핸드폰번호를 입력하세요.');
				return;
			}
		} else {
			if( $("input[name='up_comnum1']").val() == '' ){
				alert('사업자번호를 입력하세요.');
				return;
			}

			if( $("input[name='up_comnum1']").val() == '' ){
				alert('사업자번호를 입력하세요.');
				return;
			}

			if( $("input[name='up_comnum1']").val() == '' ){
				alert('사업자번호를 입력하세요.');
				return;
			}
		}

		if(confirm('현금영수증을 신청 하시겠습니까?')){
			$('#receipt_cash_frm').submit();
		}
	});

</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<main id="content" class="subpage">
<form name='receipt_cash_frm' id='receipt_cash_frm' action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_cash'>
	<input type = 'hidden' name = 'ordercode' value='<?=$ordercode?>' >
	<article class="mypage">
<?php include("myp_sub_header.php"); ?>
		<section class="mypage_tb2">
			<h3 class="mypage_tit" style="border-bottom:1px solid #000">현금영수증 신청</h3>
			<div class="form">
				<table class="receipt" width="100%">
					<colgroup>
						<col style="width:100px" ><col style="width:auto" >
					</colgroup>
					<tr>
						<th>주문번호</th>
						<td><?=$ordercode?></td>
					</tr>
					<tr>
						<th>구분</th>
						<td><input type="radio" name="up_tr_code" value="0" checked>개인 <input type="radio" name="up_tr_code" value="1">사업자</td>
					</tr>
					<tr class = 'CLS_handphone'>
						<th>핸드폰번호</th>
						<td>
							<input type="text" name="up_mobile1" maxlength = '3' class="w50"> - 
							<input type="text" name="up_mobile2" maxlength = '4' class="w60"> - 
							<input type="text" name="up_mobile3" maxlength = '4' class="w60">
						</td>
					</tr>
					<tr class = 'CLS_biznumber' style = 'display:none;'>
						<th>사업자번호</th>
						<td><input type="text" name="up_comnum1"> - <input type="text" name="up_comnum2"> - <input type="text" name="up_comnum3"></td>
					</tr>
				</table>
				<div class="btn">
					<input type="button" class="btn-point CLS_submitCash" style='width: 110px;' value="현금영수증 신청" >
				</div>
			</div>
		</section>
<?php include("myp_sub_footer.php");?>
	</article>
</form>
</main>
<? include_once('outline/footer_m.php'); ?>