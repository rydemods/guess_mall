<html>
<head>
<script language = 'javascript'>
	function goResult()
	{
		document.pay_info.submit();
	}
</script>
</head>
<body onload="goResult();">
<form name="pay_info" method="post" action="./result.php">
	<input type="hidden" name="req_tx"            value="<?=$req_tx?>">            <!-- 요청 구분 -->
	<input type="hidden" name="bSucc"             value="<?=$bSucc?>">             <!-- 쇼핑몰 DB 처리 성공 여부 -->

	<input type="hidden" name="res_cd"            value="<?=$res_cd?>">            <!-- 결과 코드 -->
	<input type="hidden" name="res_msg"           value="<?=$res_msg?>">           <!-- 결과 메세지 -->
	<input type="hidden" name="ordr_idxx"         value="<?=$ordr_idxx?>">         <!-- 주문번호 -->
	<input type="hidden" name="good_name"         value="<?=$good_name?>">         <!-- 상품명 -->
	<input type="hidden" name="buyr_name"         value="<?=$buyr_name?>">         <!-- 주문자명 -->
	<input type="hidden" name="buyr_tel1"         value="<?=$buyr_tel1?>">         <!-- 주문자 전화번호 -->
	<input type="hidden" name="buyr_mail"         value="<?=$buyr_mail?>">         <!-- 주문자 E-mail -->
	<input type="hidden" name="comment"           value="<?=$comment?>">           <!-- 비고 -->

	<input type="hidden" name="corp_type"         value="<?=$corp_type?>">         <!-- 사업장 구분 -->
	<input type="hidden" name="corp_tax_type"     value="<?=$corp_tax_type?>">     <!-- 과세/면세 구분 -->
	<input type="hidden" name="corp_tax_no"       value="<?=$corp_tax_no?>">       <!-- 발행 사업자 번호 -->
	<input type="hidden" name="corp_nm"           value="<?=$corp_nm?>">           <!-- 상호 -->
	<input type="hidden" name="corp_owner_nm"     value="<?=$corp_owner_nm?>">     <!-- 대표자명 -->
	<input type="hidden" name="corp_addr"         value="<?=$corp_addr?>">         <!-- 사업장주소 -->
	<input type="hidden" name="corp_telno"        value="<?=$corp_telno?>">        <!-- 사업장 대표 연락처 -->

	<input type="hidden" name="tr_code"           value="<?=$tr_code?>">           <!-- 발행용도 -->
	<input type="hidden" name="id_info"           value="<?=$id_info?>">           <!-- 신분확인 ID -->
	<input type="hidden" name="amt_tot"           value="<?=$amt_tot?>">           <!-- 거래금액 총 합 -->
	<input type="hidden" name="amt_sub"           value="<?=$amt_sup?>">           <!-- 공급가액 -->
	<input type="hidden" name="amt_svc"           value="<?=$amt_svc?>">           <!-- 봉사료 -->
	<input type="hidden" name="amt_tax"           value="<?=$amt_tax?>">           <!-- 부가가치세 -->
	<input type="hidden" name="pay_type"          value="<?=$pay_type?>">          <!-- 결제 서비스 구분 -->
	<input type="hidden" name="pay_trade_no"      value="<?=$pay_trade_no?>">      <!-- 결제 거래번호 -->

	<input type="hidden" name="mod_type"          value="<?=$mod_type?>">          <!-- 변경 타입 -->
	<input type="hidden" name="mod_value"         value="<?=$mod_value?>">         <!-- 변경 요청 거래번호 -->
	<input type="hidden" name="mod_gubn"          value="<?=$mod_gubn?>">          <!-- 변경 요청 거래번호 구분 -->
	<input type="hidden" name="mod_mny"           value="<?=$mod_mny?>">           <!-- 변경 요청 금액 -->
	<input type="hidden" name="rem_mny"           value="<?=$rem_mny?>">           <!-- 변경처리 이전 금액 -->

	<input type="hidden" name="cash_no"           value="<?=$cash_no?>">           <!-- 현금영수증 거래번호 -->
	<input type="hidden" name="receipt_no"        value="<?=$receipt_no?>">        <!-- 현금영수증 승인번호 -->
	<input type="hidden" name="app_time"          value="<?=$app_time?>">          <!-- 승인시간(YYYYMMDDhhmmss) -->
	<input type="hidden" name="reg_stat"          value="<?=$reg_stat?>">          <!-- 등록 상태 코드 -->
	<input type="hidden" name="reg_desc"          value="<?=$reg_desc?>">          <!-- 등록 상태 설명 -->

</form>
</body>
</html>