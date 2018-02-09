<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");

$productcode=$_REQUEST["productcode2"];
?>

<?
$sql=" select * from tblproduct where productcode='{$productcode}' ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
?>
<link rel="stylesheet" href="../css/oryany.css">
<script>
function gobasket(){
	var optionprice="<?=$row->option_price?>";
	var pricearr="<?=$row->sellprice?>";
	var optionarr;
	var op1;
	var op2;
	optionprice=optionprice.split(",");
	if(!"<?=$row->option2?>"){
		op2=0;
		//alert("옵션2없음");
	}

	if(document.form1.option1.value==0){
		alert("옵션을 선택 해야 합니다");
	}else{
		op1=document.form1.option1.value;

		if(op2==0){
			//document.form1.option2.value=0;
		}else{
			if(document.form1.option2.value==0){
				alert("두번째 옵션을 선택 해야 합니다");
				return;
			}
			op2=document.form1.option2.value;
		}
		optionarr=op1+"_"+op2;
		pricearr=Number(pricearr)+Number(optionprice[op1-1]);
		document.form1.priceArr.value=pricearr;
		document.form1.productcode.value="<?=$productcode?>";
		document.form1.optionArr.value=optionarr;
		document.form1.quantity.value=0;
		document.form1.action="../front/confirm_basket.php";
		document.form1.target="go";
		window.open("","go","width=440,height=350,scrollbars=no,resizable=no, status=no,");
		document.form1.submit();
		window.open("about:blank","_self").close();
	}
};
</script>

<div class="popup_def_wrap">
	<div class="title_wrap">
		<P class="title">장바구니 담기</p>
	</div>

<div class="popup_cart_go">
		<img src="../img/icon/icon_pop_cart.gif" alt="">
		<p class="txt">
			장바구니에 넣기전 필수 옵션을 선택해야 합니다<br>
			옵션 선택후 해당 상품을 장바구니에 넣으시겠습니까?
		</p>
		<br><br>

<form name="form1">
<table border="0" align="center">
<?
if(strlen($row->option1)>0) {
	$temp = $row->option1;
	$option1Arr = explode(",",$temp);
	$tok = explode(",",$temp);
	$optprice = explode(",", $row->option_price);

	$optcode = "";
	if($row->optcode){
		$optcode = explode(",", $row->optcode);
	}
	if (sizeof($optprice)!= sizeof($option1Arr) ) {
		for($i=0; $i<sizeof($option1Arr); $i++){
			$optprice[$i] = $optprice[$i]=="" ? "0":$optprice[$i];
		}
	}

	$count=count($tok);

	if ($priceindex!=0) {
		$onchange_opt1="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
		if(strlen($row->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
		else $onchange_opt1.="''";
		$onchange_opt1.=")";
									$onchange_opt1.="\"";
	}else{
		$onchange_opt1="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
		if(strlen($row->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
		else $onchange_opt1.="''";
		$onchange_opt1.=")";
		$onchange_opt1.="\"";
	}
	$optioncnt = explode(",",ltrim($row->option_quantity,','));
	if (sizeof($optioncnt) > 1) {
		for ($i=0; $i<sizeof($optioncnt);$i++) {
			if ($optioncnt[$i] == "") {
				$optioncnt[$i] = "0";
			}
		}
	}
?>
	<tr>
		<td>
			<span><?=$tok[0]?> 선택 : </span>
			<div class="select_type" style="width:180px;z-index:0;margin-left: 5px">
				<select name="option1" id="option1" style="width: 180px;" alt='<?=$tok[0]?>'>
				<option value="">옵션을 선택해주세요.</option>
				<?for($i=1;$i<$count;$i++) {?>
					<?if(strlen($tok[$i]) > 0) {?>
					<option value="<?=$i?>">
						<?if(strlen($row->option2) == 0 && $optioncnt[$i-1] == "0"){?>
							<span class='option_strike'><?=$tok[$i]." [품절]"?></span>
						<?}else{
							$tempopt = $optprice[$i-1] == "" ? "0": $optprice[$i-1];?>
							<? if($tempopt == 0){?>
									<span><?=$tok[$i]?></span>&nbsp;
							<? }else{ ?>
									<span><?=$tok[$i]?></span>&nbsp;(<?=number_format($tempopt)?>원)

							<? } ?>
						<?}?>
					</option>
					<?}?>
				<?}?>
				</select>
			</div>
		</td>
	</tr>
<?}?>

<?
$onchange_opt2="";
if(strlen($row->option2)>0){
	$temp = $row->option2;
	$option2Arr = explode(",",$temp);
	$tok = explode(",",$temp);
	$count2=count($tok);
	$onchange_opt2.="onchange=\"change_price(0,";
	if(strlen($row->option1)>0) $onchange_opt2.="document.form1.option1.selectedIndex-1";
								else $onchange_opt2.="''";
								$onchange_opt2.=",document.form1.option2.selectedIndex-1)\"";
?>
	<tr>
		<td><?=$tok[0]?></td>
	</tr>

	<tr>
		<td>
			<div class="select_type" style="width:180px;z-index:0;">
				<select name="option2" id="option2" style="width: 225px;" alt='<?=$tok[0]?>'>
				<option value="">옵션을 선택해주세요.</option>
					<?for($i=1;$i<$count2;$i++) {?>
						<?if(strlen($tok[$i]) > 0) {?>
							<option value="<?=$i?>">
								<?if(strlen($row->option2) == 0 && $optioncnt[$i-1] == "0"){?>
									<span class='option_strike'><?=$tok[$i]." [품절]"?></span>
								<?}else{?>
								<!-- (<?=number_format($optprice[$i-1])?>원) -->
									<?=$tok[$i]?>
								<?}?>
						<?}?>
							</option>
					<?}?>
				</select>
			</div>
		</td>
	</tr>

<?}?>
	<input type="hidden" name="quantity">
	<input type="hidden" name="productcode">
	<input type="hidden" name="quantityArr" value=1>
	<input type="hidden" name="optionArr">
	<input type="hidden" name="priceArr">
</table>
</form>

	<div class="btn_area">
		<a href="javascript:gobasket()" class="go_cart">장바구니에 담기</a>
		<a href="javascript:window.close();" class="gray">계속 쇼핑하기</a>
	</div>
</div><!--div class="popup_cart_go" end-->

</div><!--div class="popup_def_wrap" end -->

