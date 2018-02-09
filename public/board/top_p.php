<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?php $left_name=end(explode('_',$setup['board_skin'])); ?>
