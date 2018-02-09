<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include_once($Dir."lib/adminlib.php");



$sno=$_POST[sno];
$mode=$_POST[mode];
$wsmoney=$_POST[wsmoney];


if($mode=="modmoney"){
	$qry="update tblwsmoneylog set wsmoney='".$wsmoney."' where sno='".$sno."'";
	pmysql_query($qry);
	
	echo"
		<script language='javascript'>
		   alert('변경되었습니다.');
	       opener.location.reload();
		   self.close();
		</script>
		";
		
}
?>

