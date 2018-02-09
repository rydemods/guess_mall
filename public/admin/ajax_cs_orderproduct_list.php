<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

# 배송업체를 불러온다.
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=pmysql_query($sql,get_db_conn());
$delicomlist=array();
while($row=pmysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
pmysql_free_result($result);

$ordercode		= $_POST['ordercode'];
?>
				
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<col width=100></col>
	<col width=200></col>
	<col width=80></col>
	<col width=></col>
	<col width=150></col>
	<col width=40></col>
	<col width=90></col>
	<col width=90></col>
	<col width=90></col>
	<col width=150></col>
	<col width=170></col>			
	<tr bgcolor="#EFEFEF">
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>접수번호</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>주문번호</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>상품</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>옵션</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>수량</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>판매가</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>구입금액</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>주문상태</td>
		<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>배송정보</td>
		<td style='border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>CS처리</td>
	</tr>
<?

	list($paymethod, $oi_step1, $oi_step2)=pmysql_fetch_array(pmysql_query("select paymethod, oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".$ordercode."' "));

	#주문상품
	$sql = "SELECT 
					a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
					a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
					a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
					b.minimage, a.option_type, a.option_price, a.option_quantity, 
					a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
					a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
					a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, b.option1_tf, option2_tf, option2_maxlen, 
					a.delivery_type, a.store_code, a.reservation_date, a.oc_no 
				FROM 
					tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
				WHERE 
					a.ordercode='".$ordercode."' 
				ORDER BY a.vender, a.idx ";

	$result	= pmysql_query($sql,get_db_conn());

	while($row=pmysql_fetch_object($result)) {

		$file = getProductImage($Dir.DataDir.'shopimages/product/', $row->tinyimage);

		$optStr	= "";
		$option1	 = $row->opt1_name;
		$option2	 = $row->opt2_name;

		if( strlen( trim( $row->opt1_name ) ) > 0 ) {
			$opt1_name_arr	= explode("@#", $row->opt1_name);
			$opt2_name_arr	= explode(chr(30), $row->opt2_name);
			for($g=0;$g < sizeof($opt1_name_arr);$g++) {
				if ($g > 0) $optStr	.= " / ";
				$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
			}
		}

		if( strlen( trim( $row->text_opt_subject ) ) > 0 ) {
			$text_opt_subject_arr	= explode("@#", $row->text_opt_subject);
			$text_opt_content_arr	= explode("@#", $row->text_opt_content);

			for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
				if ($text_opt_content_arr[$s]) {
					if ($optStr != '') $optStr	.= " / ";
					$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
				}
			}
		}
?>
	<tr>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$row->oc_no?$row->oc_no:'-'?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$ordercode?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><img src="<?=$file?>" style="width:70px" border="1" alt="<?=$row->productname?>"></td>
		<td style='padding:5px;border-bottom:1px solid #cbcbcb;text-align:left'>[<?=$row->brandname?>]<br><?=$row->productname?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:left'><?=$optStr?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row->option_quantity)?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($row->price)?>원</td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($row->price)?>원</td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=GetStatusOrder("p", $oi_step1, $oi_step2, $row->op_step, $row->redelivery_type, $row->order_conf)?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$row->deli_num?$delicomlist[$row->deli_com]->company_name."<br><font color='blue'>".$row->deli_num."</font>":"-"?></td>
		<td style='padding:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
		
	<? if ($row->op_step < 40) { //주문취소 신청및 완료상태가 아닌경우
			if( $row->op_step == 1 || $row->op_step == 2 ){ // 입금완료, 배송 준비중일 경우
	?>
		<input type='button' value='취소' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$ordercode?>" idx = "<?=$row->idx?>" pc_type="PART" can_type="refund">&nbsp;<input type='button' value='CS 처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$ordercode?>" idx = "<?=$row->idx?>" pc_type="PART" can_type="cscancel">
	<?
			} else if( $row->op_step == 3 || $row->op_step == 4){ // 배송중일 경우
	?>
		<input type='button' value='반품' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$ordercode?>" idx = "<?=$row->idx?>" pc_type="PART" can_type="regoods">&nbsp;<input type='button' value='교환' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$ordercode?>" idx = "<?=$row->idx?>" pc_type="PART" can_type="rechange">&nbsp;<input type='button' value='CS 처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' ordercode = "<?=$ordercode?>" idx = "<?=$row->idx?>" pc_type="PART" can_type="cscancel">
	<?
			} else {
				echo "-";
			}
		} else {
			echo "-";
		}
	?>
		</td>
	</tr>
<?
	}
?>
	</table>