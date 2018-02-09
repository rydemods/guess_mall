<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

### 회원등급 SELECT ###
### 회원등급 SELECT ###
$member_sql = "SELECT group_code, group_name FROM tblmembergroup WHERE group_level != '100' ORDER BY group_code ASC ";
$member_res = pmysql_query($member_sql,get_db_conn());
while($member_row = pmysql_fetch_array($member_res)){
	$member_code[] = $member_row;
}
pmysql_free_result($member_res);
?>
<tr>   
	<td align = 'center'>
		<?=mb_convert_encoding("그룹명 : ",'UTF-8','EUC-KR')?>
		<select name="s_group[]">
		<?php foreach($member_code as $k=>$v){ ?>
			<option value="<?=$v['group_code']?>"> <?=mb_convert_encoding($v['group_name'],'UTF-8','EUC-KR')?> </option>
		<? } ?>
		</select>
	</td>
	<td align = 'center'> <?=mb_convert_encoding("수량",'UTF-8','EUC-KR')?> 
		<input type='text' name="s_min_num[]" value = '' class = '' maxlength='10' class='input' style = 'width:70px;text-align:right;'>~
		<input type='text' name="s_max_num[]" value = '' class = '' maxlength='10' class='input' style = 'width:70px;text-align:right;'>
	</td>
	<td align = 'center'>  
		<input type='text' name="s_price[]" value = '' class = '' maxlength='50' class='input' style = 'width:144px;text-align:right;'>
	</td>
	<td align = 'center'><a href = 'javascript:;' class = 'CLS_groupSaleDel'><?=mb_convert_encoding("[삭제]",'UTF-8','EUC-KR')?></a></td>
</tr>