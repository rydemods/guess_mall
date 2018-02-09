<?php 
Header('Content-Type: text/html; charset=utf-8');
$Dir = "../";

include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/order.class.php");

$_POST['ordercode'];
$_POST['productcode'];
$count = $_POST['quantity'];

$sql="SELECT product_serial FROM tblorderproduct where ordercode ='".$_POST['ordercode']."' and productcode='".$_POST['productcode']."'" ;
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_array($result); 
$serial_arr = explode("|",$row[0]);
?>
<link rel="stylesheet" type="text/css" href="<?=$Dir?>css/admin.css">
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html; charset=utf-8">
<!--<style>
	.crmView{
		cursor:pointer;
		border:1px solid #eeeeee;
		background:#FBFBFC;		
	}
</style>-->
</head>
<body>
<form action="order_chg_deli_indb_serial.php?ordcode=<?=$_POST['ordercode']?>"  method=POST > 
	<input type=hidden value="<?=$_POST['ordercode']?>"		name=ordercode  ></input>
	<input type=hidden value="<?=$_POST['productcode']?>"	name=productcode ></input>

 
	<div class="title_depth3_sub">시리얼 번호 입력</div>

	<div class="table_style01">
		<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th> <!-- class = 'crmView' -->
						<span> 주문 번호 
						</span>
					</th>
					<td>
						<span> <?=$_POST['ordercode']?>
						</span>
					</td>
				</tr>		
		</table>
		<div style="padding-top:4pt;"></div>

		<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th> <!-- class = 'crmView' -->
						<span> 상품 번호 
						</span>
					</th>
					<td>
						<span> <?=$_POST['productcode']?>
						</span>
					</td>
				</tr>		
		</table>
	</div>
 
<div style="padding-top:4pt;"></div>
<div class="table_style01">
	<table cellpadding="0" cellspacing="0" width="100%">
		<?for($i=0 ; $i<$count ; $i++){?>
			<tr>
				<th> <!-- class = 'crmView' -->
					<span> serial <?=($i+1)?> 
					</span>
				</th>
				<td>
					<input type=text value="<?=$serial_arr[$i]?>"  
					name="product_serial[<?=$_POST['ordercode']?>][<?=$_POST['productcode']?>][<?=$i?>]" class="input"
					style="width:100%;" ></input>
				</td>
			</tr>
		<?}?>
	</table>
	<div style="padding-top:4pt;"></div>
	<div  style='text-align:center;'>
		<img style="cursor:pointer;" src="images/botteon_save.gif" onclick=submit() />
	</div>
</div>

</form>
</body>
</html>