<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

$this_num=$_REQUEST["num"];
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - <?=$setup[board_name]?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<script>var LH = new LH_create();</script>
<script for=window event=onload>f_onLoad();</script>
<script>LH.add("parent_resizeIframe('list_comment<?=$this_num?>')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var isOnload = false;
var setCnt = 0;
function f_onLoad() {
	isOnload = true;
	LH.exec();
}
function chk_time() {
	if (!isOnload && setCnt < 20) {
		setCnt = setCnt + 1;
		LH.exec();
		setTimeout("chk_time()", 500);
	}
}
chk_time();

function chkCommentDel(userId,url) {
	parent.location.href = url;
}
//-->
</SCRIPT>
</HEAD>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?@include ($dir."/comment_head.php");?>

<?php
$frametype="Y";
$sql = "SELECT * FROM tblboardcomment WHERE board='".$board."' ";
$sql.= "AND parent = $this_num ORDER BY num ASC ";
$result = @pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$c_num = $row->num;
	$c_name = $row->name;

	if($setup[use_comip]=="Y") {
		$c_uip=$row->ip;
	}

	$c_writetime = getTimeFormat($row->writetime);
	//$c_comment = nl2br(stripslashes($row->comment));
	$c_comment = nl2br(htmlspecialchars($row->comment));
	$c_ip = $row->ip;
	$c_comment = getStripHide($c_comment);

	@include ($dir."/comment_list.php");
}
pmysql_free_result($result);

if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
	@include ($dir."/comment_write.php");
}

?>
</body>
</html>