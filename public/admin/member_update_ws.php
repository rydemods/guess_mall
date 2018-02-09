<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$id = $_POST[id];


$query="UPDATE tblmember SET resetday = now() WHERE id='".$id."'";
pmysql_query($query);


$query="INSERT INTO tblwsmoneylog (wsmoney, group_level, id, regdt) VALUES ('".$_POST[wsmoney]."', '".$_POST[group_level]."', '".$id."', now())";
pmysql_query($query);


/* tblmemberchange 연장*/
/*
IF($_SERVER[REMOTE_ADDR]!='218.234.32.11'){
$sel_qry="select * from tblmember a left join tblmembergroup b on(a.group_code=b.group_code) where a.id='{$id}'";
$sel_result=pmysql_query($sel_qry);
$sel_data=pmysql_fetch_object($sel_result);

$sum_sql = "SELECT sum(price) as sumprice FROM {$SHOP_SCHEMAS}tblorderinfo ";
$sum_sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y'";
$sum_result = pmysql_query($sum_sql,get_db_conn());
$sum_data=pmysql_fetch_object($sum_result);
$sumprice="0";
$sumprice=$sum_data->sumprice+$sel_data->sumprice;

$insert_query = "INSERT INTO tblmemberchange (mem_id, before_group, after_group, accrue_price, change_date) ";
$insert_query.= "VALUES ('".$id."', '".$sel_data->group_name."', '".$sel_data->group_name."', ".$sumprice.", now())";
pmysql_query($insert_query);

msg('연장 되었습니다');
go($_POST[returnUrl]);
}else{
	*/
	echo"
		<script language='javascript'>
		   alert('연장 되었습니다.');
	       history.back();
		   
		</script>
		";
//}



?>