<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$iconImgPath = $Dir."img/icon/";
$index = $_GET["index"];
?>
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript">
function iconUp(number){
	if($("input[name=icon_select]:checked").length < 1){
		alert("아이콘을 선택해 주세요");
		return;
	}
	var chkIcon = $("input[name=icon_select]:checked").val();
	$("#icon_"+number,opener.document).val(chkIcon);
	$("input[name=mode]",opener.document).val("update");
	$("#frm1",opener.document).submit();
	window.close();
}

</script>

<div class="table_main_setup"  id="divscroll" style="height:400px;overflow-x:hidden;overflow-y:auto;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<th>아이콘 선택</th>
			<td><input type="radio" name="icon_select" value="icon_new.gif" /><img src="<?=$iconImgPath?>icon_new.gif"/></td>
			<td><input type="radio" name="icon_select" value="icon_best.gif" /><img src="<?=$iconImgPath?>icon_best.gif"/></td>
			<td><input type="radio" name="icon_select" value="" />삭제</td>
		</tr>
		<tr>
			<td colspan="4">
				<a href="javascript:iconUp('<?=$index?>');"><img src="images/btn_save.gif" /></a>
				<a href="javascript:window.close();"><img src="images/btn_close.gif" /></a>		
			</td>
			
		</tr>
	</table>
</div>