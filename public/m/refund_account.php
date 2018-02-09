<?
include_once('outline/header_m.php');

if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}

$mode=$_POST["mode"];
$id = $_ShopInfo->getMemid();
$bank_code = $_POST["bank_code"];
$account_num = $_POST["account_num"];
$depositor = $_POST["depositor"];
$home_tel = $_POST["home_tel"];

#DB 처리
if($mode == "save"){
	#해당 id 정보에 추가 업데이트
	$where[]="bank_code='".$bank_code."'";
	$where[]="account_num='".$account_num."'";
	$where[]="depositor='".$depositor."'";
	$where[]="home_tel='".$home_tel."'";

	$sql = "UPDATE tblmember SET ";
	$sql.= implode(", ",$where);
	$sql.=" WHERE id = '".$id."'";
// echo $home_tel;
// echo "-------------<br>";
// echo $sql;
// exit();
	pmysql_query($sql, get_db_conn());
	if(!pmysql_error()){
		alert_go('저장되었습니다.', $_SERVER['REQUEST_URI']);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}
}

#환불계좌정보 조회
$view_sql ="SELECT id, bank_code, account_num, depositor, home_tel  FROM tblmember
			WHERE id = '".$id."'";
$result = pmysql_query($view_sql, get_db_conn());
$row = pmysql_fetch_object($result);
$rBankCode = $row->bank_code;
$rAccountNum = $row->account_num;
$rDepositor = $row->depositor;
$rhome_tel = $row->home_tel;

$temp_tels = explode ("-", $rhome_tel);

?>

<!-- 내용 -->
<main id="content" class="subpage">

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>환불계좌 관리</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_refund_account sub_bdtop">
		<form name='refund_form' id='refund_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
		<input type='hidden' id='mode' name='mode'>
		<input type='hidden' id='home_tel' name='home_tel'>
		<div class="board_type_write">
			<dl>
				<dt><span class="required">은행명</span></dt>
				<dd>
					<select class="select_line w100-per required_value" name="bank_code" id="refund-type-bankcode" <?=$account_disabled?> label="은행명">
						<option value="">은행명 선택</option>
				<?php 
					foreach($oc_bankcode as $key => $val) {
						echo "<option value=\"".$key."\">".$val."</option>";
					}
				?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><span class="required">계좌번호</span></dt>
				<dd><input type="tel" class="w100-per required_value" placeholder="하이픈(-)없이 입력" name="account_num" id="account_num" value="<?=$rAccountNum?>" label = "계좌번호"></dd>
			</dl>
			<dl>
				<dt><span class="required">예금주</span></dt>
				<dd><input type="text" class="w100-per required_value" label = "예금주" name="depositor" id="depositor" value="<?=$rDepositor?>"></dd>
			</dl>
			<dl>
				<dt><span class="required">연락처</span></dt>
				<dd>
					<div class="input_tel">
						<select class="select_line required_value" id="phone_num1" label="전화번호 첫자리">
							<option value="010" selected="">010</option>
							<option value="011">011</option>
							<option value="016">016</option>
							<option value="017">017</option>
							<option value="018">018</option>
							<option value="019">019</option>
						</select>
						<span class="dash"></span>
						<input type="tel" maxlength="4" class="required_value" id="phone_num2" label="전화번호 중간자리" value="<?=$temp_tels[1] ?>">
						<span class="dash"></span>
						<input type="tel" maxlength="4" class="required_value" id="phone_num3" label="전화번호 끝 자리" value="<?=$temp_tels[2] ?>">
					</div>
				</dd>
			</dl>
			<div class="btn_area mt-20">
				<ul>
					<li><a href="javascript:;" class="btn-point h-input" id="btnSubmit">저장</a></li>
				</ul>
			</div>
		</div><!-- //.board_type_write -->
		</form>
	</section><!-- //.my_refund_account -->

</main>
<!-- //내용 -->

<script Language="JavaScript">
$(document).ready(function (){
	//저장된 은행 선택
	var rBank='<?=$rBankCode?>';
	if(rBank) $('#refund-type-bankcode option[value=<?=$rBankCode?>]').prop('selected', 'selected');
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			$("#mode").val("save");
			$("#home_tel").val($("#phone_num1").val()+"-"+$("#phone_num2").val()+"-"+$("#phone_num3").val());
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

	if(procSubmit){
		return true;
	}else{
		return false;
	}
}
</script>
<? include_once('outline/footer_m.php'); ?>
