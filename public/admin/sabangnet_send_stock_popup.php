<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	
	if($_POST['mode'] == 'register'){
		if($_POST['option_type'] == '1'){
			$sabang_stock = ",".implode(",", $_POST[sabang_stock]);
			$sql = "UPDATE tblproduct SET sabangnet_option_quantity = '".$sabang_stock."' WHERE productcode = '".$_POST['productcode']."' ";
			pmysql_query($sql,get_db_conn());
		}else if($_POST['option_type'] == '3'){
			//sabang_opt1 sabang_opt2 sabang_stock
			foreach($_POST['sabang_opt1'] as $sk => $sv){
				$sql = "UPDATE tblproduct_sabangnet SET quantity = '".$_POST['sabang_stock'][$sk]."' WHERE option1 = '".$sv."' AND option2 = '".$_POST['sabang_opt2'][$sk]."' AND productcode = '".$_POST['productcode']."' ";
				pmysql_query($sql,get_db_conn());
			}
		}else{
			$optnumvalue = $_POST['sabang_stock'];
			for($i=0;$i<5;$i++){
				for($j=0;$j<10;$j++){
					if(ord(trim($optnumvalue[$i][$j]))) {
						$optnumvalue[$i][$j]=(int)$optnumvalue[$i][$j];
						$tempcnt++;
					}
					$optcnt.=",".$optnumvalue[$i][$j];
				}
			}
			$sabang_stock = $optcnt;
			$sql = "UPDATE tblproduct SET sabangnet_option_quantity = '".$sabang_stock."' WHERE productcode = '".$_POST['productcode']."' ";
			pmysql_query($sql,get_db_conn());
		}
		alert_go('사방넷 옵션이 수정되었습니다.', 'c');
		exit;
	}
	
	$sql = "SELECT * FROM tblproduct WHERE productcode = '".$_GET['productcode']."' ";
	$result = pmysql_query($sql,get_db_conn());
	$_data = pmysql_fetch_object($result);
	
	$optionarray1=explode(",",$_data->option1);
	$option_price=explode(",",$_data->option_price);
	$option_ea=explode(",",$_data->option_ea);
	$sabang_option_ea=explode(",",$_data->sabangnet_option_quantity);
	$option_consumer=explode(",",$_data->option_consumer);
	$optcode=explode(",",$_data->optcode);
	$optreserve=explode(",",$_data->option_reserve);
	$optionarray2=explode(",",$_data->option2);
	$option_quantity_array=explode(",",$_data->option_quantity);
	$optnum1=count($optionarray1)-1; 
	$optnum2=count($optionarray2)-1;


	$optionover="NO";
	if($optnum1>10){
		$optnum1=10;
		$optionover="YES";
	}
	if($optnum2>5){
		$optnum2=5;
		$optionover="YES";
	}
	if($optnum1>0 && ord($_data->option_quantity)==0) $optionover="YES";
	if($optnum2<=1) $optnum2=1;
	
	list($sabangSetCount)=pmysql_fetch_array(pmysql_query("SELECT count(no) FROM tblproduct_sabangnet WHERE productcode = '".$_data->productcode."'"));
?>

<html>
<head>
	<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
	<title>사방넷 재고 입력</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="styleSheet" href="/css/admin.css" type="text/css">
</head>

<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 160;

	//window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
		<div class="pop_top_title"><p>사방넷 상품 옵션 수정</p></div>
		<div class="table_style02">
			<form action = '../admin/sabangnet_send_stock_popup.php' method = 'POST'>
				<input type = 'hidden' name = 'mode' value = 'register'>
				<input type = 'hidden' name = 'productcode' value = '<?=$_GET['productcode']?>'>
				<table width = '100%' border = '0' cellpadding = '0' cellspacing = '0' style = 'table-layout:fixed;' id = 'table_body'>
					<?if(count($optionarray1) > 1 && count($optionarray2) == 1){?>
						<tr>
							<th>옵션명<input type = 'hidden' name = 'option_type' value = '1'></th>
							<th>쇼핑몰 재고</th>
							<th>사방넷 재고</th>
						</tr>
						<?for($i=1;$i<count($optionarray1);$i++){?>
							<?if(!$sabang_option_ea[$i]) $sabang_option_ea[$i] = 0;?>
							<tr>
								<td align = 'center'><?=trim(htmlspecialchars($optionarray1[$i]))?></td>
								<td align = 'center'><?=$option_quantity_array[$i]?></td>
								<td align = 'center'><input type = 'text' name = 'sabang_stock[]' value = '<?=$sabang_option_ea[$i]?>' size = '4' style = 'text-align:right'></td>
							</tr>
						<?}?>
						<?if($_GET[chk]){?>
						<tr>
							<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size" colspan = '3'><span style="letter-spacing:-0.5pt;">※ 수정 후 사방넷과 재연동이 필요합니다.</span></td>
						</tr>
						<?}?>
					<?}else if(count($optionarray2) > 1){?>
					<?
						$option2SabangQuantity = array_chunk($sabang_option_ea, 10);
					?>
						<tr>
							<th>옵션명<input type = 'hidden' name = 'option_type' value = '2'></th>
							<th>쇼핑몰 재고</th>
							<?for($z=1;$z<count($optionarray2);$z++){?>
							<td><b><?=$optionarray2[$z]?></b></td>
							<?}?>
						</tr>
						<?for($i=1;$i<count($optionarray1);$i++){?>
							<tr>
								<td align = 'center'><?=trim(htmlspecialchars($optionarray1[$i]))?></td>
								<td align = 'center'><?=$option_quantity_array[$i]?></td>
								<?for($z=1;$z<count($optionarray2);$z++){?>
								<?if(!$option2SabangQuantity[$z-1][$i]) $option2SabangQuantity[$z-1][$i] = 0;?>
								<td align = 'center'><input type = 'text' name = 'sabang_stock[<?=$z-1?>][<?=$i-1?>]' value = '<?=$option2SabangQuantity[$z-1][$i]?>' size = '4' style = 'text-align:right'></td>
								<?}?>
							</tr>
						<?}?>
						<?if($_GET[chk]){?>
						<tr>
							<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size" colspan = '<?=count($optionarray2)+1?>'><span style="letter-spacing:-0.5pt;">※ 수정 후 사방넷과 재연동이 필요합니다.</span></td>
						</tr>
						<?}?>
					<?}else if($sabangSetCount > 0){?>
					<?
						$sqlSet = "SELECT * FROM tblproduct_sabangnet WHERE productcode = '".$_data->productcode."'";
						$resultSet = pmysql_query($sqlSet,get_db_conn());
					?>
						<tr>
							<th>옵션명<input type = 'hidden' name = 'option_type' value = '3'></th>
							<th>사방넷 재고</th>
						</tr>
						<?while($rowSet = pmysql_fetch_object($resultSet)){?>
							<tr>
								<td align = 'center'>
									<?=$rowSet->option1?><?if($rowSet->option2){?>/<?=$rowSet->option2?><?}?>
									<input type = 'hidden' name = 'sabang_opt1[]' value = '<?=$rowSet->option1?>'>
									<?if($rowSet->option2){?><input type = 'hidden' name = 'sabang_opt2[]' value = '<?=$rowSet->option2?>'><?}?>
								</td>
								<td align = 'center'><input type = 'text' name = 'sabang_stock[]' value = '<?=$rowSet->quantity?>' size = '4' style = 'text-align:right'></td>
							</tr>
						<?}?>
						<?if($_GET[chk]){?>
						<tr>
							<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size" colspan = '2'><span style="letter-spacing:-0.5pt;">※ 수정 후 사방넷과 재연동이 필요합니다.</span></td>
						</tr>
						<?}?>
					<?}else{?>
						<?if(!$sabang_option_ea[1]) $sabang_option_ea[1] = 0;?>
						<tr>
							<th>옵션명<input type = 'hidden' name = 'option_type' value = '1'></th>
							<th></th>
							<th>사방넷 재고</th>
						</tr>
						<tr>
							<td align = 'center'>단품</td>
							<td align = 'center'></td>
							<td align = 'center'><input type = 'text' name = 'sabang_stock[]' value = '<?=$sabang_option_ea[1]?>' size = '4' style = 'text-align:right'></td>
						</tr>
						<?if($_GET[chk]){?>
						<tr>
							<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size" colspan = '3'><span style="letter-spacing:-0.5pt;">※ 수정 후 사방넷과 재연동이 필요합니다.</span></td>
						</tr>
						<?}?>
					<?}?>

				</table>
				<div style = 'padding:4px 0px 8px 0px;text-align:center;'>
					<a href="javascript:;"><input type = 'image' src="images/btn_add2.gif" border="0" hspace="1"></a>
				</div>
			</form>
		</div>
	</body>
</html>