<?php
$Dir="../";
include_once($Dir."lib/init.php");

if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) exit;
$file_name=$_REQUEST["file_name"];
?>
<script>
try {
	opener.writeForm.up_filename.value = "<?=$file_name?>";
} catch (e) {}
window.close();
</script>