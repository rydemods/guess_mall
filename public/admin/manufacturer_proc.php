<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$sql = "SELECT * FROM tblmanufacturer ORDER BY num ASC ";
$result=pmysql_query($sql,get_db_conn());
$arrManufact = array();
while($row=pmysql_fetch_object($result)){
	$arrManufact[$row->num] = $row->name;
}
pmysql_free_result($result);
?>
<tr>
	<td align = 'center'>
		<select name = 'm_manufactor[]'>
			<?if(count($arrManufact) > 0){?>
			<?foreach($arrManufact as $key => $val){?>
			<option value = '<?=$key?>'><?=mb_convert_encoding($val, 'UTF-8', 'EUC-KR')?></option>
			<?}?>
			<?}?>
		</select>
	</td>
	<td align = 'center'><input type='text' name="m_sellprice[]" class = 'CLS_sellprice' maxlength='10' class='input' style = 'text-align:right;'></td>
	<td align = 'center'><input type='text' name="m_supplyprice[]" class = 'CLS_supplyprice' maxlength='10' class='input' style = 'text-align:right;'></td>
	<td align = 'center'><input type='text' name="m_commission[]" class = 'CLS_commission' readonly maxlength='10' class='input' style = 'text-align:right;width:40px;'>%</td>
	<td align = 'center'><a href = 'javascript:;' class = 'CLS_manufactorDel'>[<?=mb_convert_encoding('삭제', 'UTF-8', 'EUC-KR')?>]</a></td>
</tr>