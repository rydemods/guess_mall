<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


$prcode = $_POST['prcode'];
$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
$result = pmysql_query($sql,get_db_conn());

$row=pmysql_fetch_object($result);

?>
						<table width=100% cellpadding=0 cellspacing=0 border=0>
							<tr>
							<th><span>상품명</span></th>
							<td><?=$row->productname?></td>
							</tr>
							<tr>
							<th><span>원가</span></th>
							<td><?=$row->sellprice?></td>
						</tr>
						<tr>
							<th><span>특가</span></th>
							<td><input type="text" name="" maxlength="25">
							</td>
						</tr>
						<tr>
							<th><span>메모</span></th>
							<td><textarea style="width: 250px;height:100px;"></textarea></td>
						</tr>
						</table>
