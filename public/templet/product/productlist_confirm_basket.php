<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/timesale.class.php");
?>

<?
$sql=" select * from tblproduct where productcode='{$productcode}' ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
?>

<main>
test dd
</main>

<?/*


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
									if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
									else $onchange_opt1.="''";
									$onchange_opt1.=")";
									$onchange_opt1.="\"";
								} else {
									$onchange_opt1="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
									if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
									else $onchange_opt1.="''";
									$onchange_opt1.=")";
									$onchange_opt1.="\"";
								}
								$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
								if (sizeof($optioncnt) > 1) {
									for ($i=0; $i<sizeof($optioncnt);$i++) {
										if ($optioncnt[$i] == "") {
											$optioncnt[$i] = "0";
										}
									}
								}
						?>
						<!--<tr>
							<td colspan="3" class="line_1px" ><em></em></td>
						</tr>-->
						<tr>
							<th><?=$tok[0]?></th>
							<td>
								<div class="select_type" style="width:180px;z-index:0;">
									<select name="option1" id="option1" style="width: 225px;" alt='<?=$tok[0]?>'>
									<option value="">옵션을 선택해주세요.</option>
										<?for($i=1;$i<$count;$i++) {?>
											<?if(strlen($tok[$i]) > 0) {?>
												<option value="<?=$i?>">
												<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
													<span class='option_strike'><?=$tok[$i]." [품절]"?></span>
												<?}else{
													$tempopt = $optprice[$i-1] == "" ? "0": $optprice[$i-1];
												?>
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
						<?
							}
						?>

						<?
							$onchange_opt2="";

							if(strlen($_pdata->option2)>0) {
								$temp = $_pdata->option2;
								$option2Arr = explode(",",$temp);
								$tok = explode(",",$temp);
								$count2=count($tok);
								$onchange_opt2.="onchange=\"change_price(0,";
								if(strlen($_pdata->option1)>0) $onchange_opt2.="document.form1.option1.selectedIndex-1";
								else $onchange_opt2.="''";
								$onchange_opt2.=",document.form1.option2.selectedIndex-1)\"";
						?>
						<tr>
							<th><?=$tok[0]?></th>
							<td>
								<div class="select_type" style="width:180px;z-index:0;">
									<select name="option2" id="option2" style="width: 225px;" alt='<?=$tok[0]?>'>
									<option value="">옵션을 선택해주세요.</option>
										<?for($i=1;$i<$count2;$i++) {?>
											<?if(strlen($tok[$i]) > 0) {?>
												<option value="<?=$i?>">
												<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
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
						<?
							}
						?>
						<?if( strlen($_pdata->option1) == 0 ){?>
						<tr class="line">
							<th class="ea">주문수량</th>
							<td>
								<div class="ea_select">
									<input type="text" readonly="true" name="quantity" id="quantity" value="1" onkeyup="strnumkeyup(this)" class="amount" size="2">
									<a href="javascript:change_quantity('up')" class="btn_plus"></a>
									<a href="javascript:change_quantity('dn')" class="btn_minus"></a>
								</div>
							</td>
						</tr>
						<?}*/?>