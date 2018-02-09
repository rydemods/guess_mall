<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mode=$_POST["mode"];
$id = $_ShopInfo->getMemid();
$bank_code = $_POST["bank_code"];
$account_num = $_POST["account_num"];
$depositor = $_POST["depositor"];
$home_tel = $_POST["home_tel"];
$mobile = $_POST["mobile"];

#DB 처리
if($mode == "save"){
	#해당 id 정보에 추가 업데이트
	$where[]="bank_code='".$bank_code."'";
	$where[]="account_num='".$account_num."'";
	$where[]="depositor='".$depositor."'";
	$where[]="home_tel='".$home_tel."'";
	$where[]="mobile='".$mobile."'";

	$sql = "UPDATE tblmember SET ";
	$sql.= implode(", ",$where);
	$sql.=" WHERE id = '".$id."'";
	
	pmysql_query($sql, get_db_conn());
	if(!pmysql_error()){
		alert_go('저장되었습니다.', $_SERVER['REQUEST_URI']);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
}

#환불계좌정보 조회
$view_sql ="SELECT id, bank_code, account_num, depositor, home_tel , mobile  FROM tblmember
			WHERE id = '".$id."'";
$result = pmysql_query($view_sql, get_db_conn());
$row = pmysql_fetch_object($result);
$rBankCode = $row->bank_code;
$rAccountNum = $row->account_num;
$rDepositor = $row->depositor;
$rhome_tel = $row->home_tel;
$rmobile = $row->mobile;

$temp_tels = explode ("-", $rhome_tel);
$temp_mobiles = explode ("-", $rmobile);s

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">환불계좌 관리</h2>

		<div class="inner-align page-frm clear">
			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->
			<article class="my-content">
				<form name='refund_form' id='refund_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
				<input type='hidden' id='mode' name='mode'>
				<input type='hidden' id='home_tel' name='home_tel'>
				<input type='hidden' id='mobile' name='mobile'>
				<fieldset>
					<legend>환불계좌 입력</legend>
					<table class="th-left mt-10">
						<caption>환불계좌 입력</caption>
						<colgroup>
							<col style="width:178px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label class="essential" for="bank_name">은행명</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select id="refund-type-bankcode" <?=$account_disabled?> name="bank_code" style="width:270px" class="required_value" label="은행명">
												<option value="">은행명 선택</option>
<?php
										foreach($oc_bankcode as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val?></option>
<?php
										}
?>
											</select>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label class="essential" for="bank_account">계좌번호</label></th>
								<td>
									<div class="input-cover"><input class="required_value" type="text" name="account_num" id="account_num" title="계좌번호 입력" placeholder="하이픈(-)없이 입력" style="width:270px" value="<?=$rAccountNum?>" label = "계좌번호"></div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label class="essential" for="account_name">예금주</label></th>
								<td>
									<div class="input-cover"><input type="text" class="required_value" name="depositor" id="depositor" value="<?=$rDepositor?>" title="예금주 이름 입력" placeholder="이름 입력" style="width:270px" label = "예금주"></div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="account_tel">연락처</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select id="account_tel" style="width:110px">
												<option value="02" selected="">02</option>
												<option value="031">031</option>
												<option value="032">032</option>
												<option value="033">033</option>
												<option value="041">041</option>
												<option value="042">042</option>
												<option value="043">043</option>
												<option value="044">044</option>
												<option value="051">051</option>
												<option value="052">052</option>
												<option value="053">053</option>
												<option value="054">054</option>
												<option value="055">055</option>
												<option value="061">061</option>
												<option value="062">062</option>
												<option value="063">063</option>
												<option value="064">064</option>
											</select>
										</div>
										<span class="txt">-</span>
										<input type="text" title="선택 전화번호 가운데 입력자리" style="width:110px" id="tel2" value="<?=$temp_tels[1]?>">
										<span class="txt">-</span>
										<input type="text" title="선택 전화번호 마지막 입력자리" style="width:110px" id="tel3" value="<?=$temp_tels[2]?>">
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="account_phone">핸드폰 </label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select id="account_phone" style="width:110px">
												<option value="010">010</option>
												<option value="011">011</option>
												<option value="016">016</option>
												<option value="017">017</option>
												<option value="018">018</option>
												<option value="019">019</option>
											</select>
										</div>
										<span class="txt">-</span>
										<input type="text" title="선택 전화번호 가운데 입력자리" style="width:110px" id="phone2" value="<?=$temp_mobiles[1]?>">
										<span class="txt">-</span>
										<input type="text" title="선택 전화번호 마지막 입력자리" style="width:110px" id="phone3" value="<?=$temp_mobiles[2]?>">
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="btnPlace mt-40">
						<button type="button" class="btn-point h-large" style="width:220px" id="btnSubmit"><span>저장</span></button>
					</div>
				</fieldset>
				</form>
			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<script Language="JavaScript">
$(document).ready(function (){
	//저장된 은행 선택
	var rBank='<?=$rBankCode?>';
	if(rBank) $('#refund-type-bankcode option[value=<?=$rBankCode?>]').prop('selected', 'selected');

	$("#btnSubmit").click(function(){
		if(check_form()){
			$("#mode").val("save");
			$("#refund_form").submit();
		}
	});
});

function check_form() {
	var procSubmit = true;

	$(".required_value").each(function(){
		if(!$(this).val()){
			if($(this).attr('label') == "은행명"){
				alert($(this).attr('label')+"을 정확히 입력해 주세요");
			}else{
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
			}
			$(this).focus();
			procSubmit = false;
			return false;
		}
	})

	$("#home_tel").val($("#account_tel").val() + "-" + $("#tel2").val() + "-" + $("#tel3").val());
	$("#mobile").val($("#account_phone").val() + "-" + $("#phone2").val() + "-" + $("#phone3").val());
	
	if(procSubmit){
		return true;
	}else{
		return false;
	}
}
</script>
<?php  include ($Dir."lib/bottom.php") ?>
