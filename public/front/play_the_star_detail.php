<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<?php include($Dir.MainDir.$_data->menu_type.".php") ?>
<?php include($Dir.TempletDir."studio/play_the_star_detail_TEM001.php"); ?>

<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
