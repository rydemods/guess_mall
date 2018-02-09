<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once('outline/header_m.php');


$ordercode = $_POST['ordercode'];

?>

<script>
	$(document).on("click", ".CLS_submitTax", function(){
		
		if( $("input[name='up_company']").val() == '' ){
			alert('회사명을 입력하세요.');
			return;
		}

		if( $("input[name='up_comnum1']").val() == '' ){
			alert('사업자번호를 입력하세요.');
			return;
		}

		if( $("input[name='up_comnum2']").val() == '' ){
			alert('사업자번호를 입력하세요.');
			return;
		}

		if( $("input[name='up_comnum3']").val() == '' ){
			alert('사업자번호를 입력하세요.');
			return;
		}

		if( $("input[name='up_name']").val() == '' ){
			alert('대표자명을 입력하세요.');
			return;
		}

		if(confirm('세금계산서를 신청 하시겠습니까?')){
			$('#receipt_tax_frm').submit();  
		}
	});
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<main id="content" class="subpage">
<form name='receipt_tax_frm' id='receipt_tax_frm' action = "mypage_receipt.indb.php" method = "POST">
	<input type = 'hidden' name = 'mode' value = 'receipt_tax'>
	<input type = 'hidden' name = 'ordercode' value='<?=$ordercode?>' >
	<article class="mypage">
<?php include("myp_sub_header.php"); ?>
		<section class="mypage_tb2">
			<h3 class="mypage_tit" style="border-bottom:1px solid #000">세금계산서 신청</h3>
			<div class="form">
				<table class="receipt" width="100%">
					<colgroup>
						<col style="width:100px" /><col style="width:auto" />
					</colgroup>
					<tr>
						<th>주문번호</th>
						<td><?=$ordercode?></td>
					</tr>
					<tr>
						<th>회사명</th>
						<td><input class="w140" type="text" name="up_company"/></td>
					</tr>
					<tr>
						<th>사업자번호</th>
						<td>
							<input class="ta_c" type="text" name="up_comnum1" maxlength = "3"/> - 
							<input class="ta_c" type="text" name="up_comnum2" maxlength = "2"/> - 
							<input class="ta_c" type="text" name="up_comnum3" maxlength = "5"/>
						</td>
					</tr>
					<tr>
						<th>대표자명</th>
						<td><input class="w100" type="text" name="up_name"/></td>
					</tr>
					<tr>
						<th>업태</th>
						<td><input class="w100" type="text" name="up_service"/></td>
					</tr>
					<tr>
						<th>종목</th>
						<td><input class="w100" type="text" name="up_item"/></td>
					</tr>
					<tr>
						<th>사업장 주소</th>
						<td><input class="w100per" type="text" name="up_address"/></td>
					</tr>
				</table>
				<div class="btn">
					<input type="button" class="btn-point CLS_submitTax" style='width: 110px;' value="세금계산서 신청" >
				</div>
				<!-- <div class="ta_c"><a href="javascript:;" class = 'btn_pop_gray CLS_submitTax'>세금계산서 신청</a></div> -->
			</div>
		</section>
<?php include("myp_sub_footer.php");?>
	</article>
</form>
</main>
<? include_once('outline/footer_m.php'); ?>