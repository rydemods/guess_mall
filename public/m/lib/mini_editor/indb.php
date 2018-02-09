<?

switch ($_POST[mode]){

	case "imgUpload":

		if (!preg_match("/^image/",$_FILES[mini_file][type])){
			echo "<script>alert('이미지 파일만 업로드가 가능합니다');</script>";
			exit;
		}

		if (is_uploaded_file($_FILES[mini_file][tmp_name])){
			$div = explode(".",$_FILES[mini_file][name]);
			$filename = time().".".$div[count($div)-1];
			move_uploaded_file($_FILES[mini_file][tmp_name],"../../data/editor/".$filename);
			chmod("../../img/editor/".$filename,0707);
		}
		$src = "http://".$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])."/../../data/editor/".$filename;
		if ($imgWidth && $imgHeight) $size = " width='$imgWidth' height='$imgHeight'";

?>

		<script>
		<? if ($filename){ ?>
		parent.opener.mini_insertHTML("<img src='<?=$src?>'<?=$size?>>");
		<? } ?>
		window.close();
		</script>

<?

		break;

	case "insertTable":

		$tmp = "<table border=1 width=100%>";
		for ($i=0;$i<$_POST[rows];$i++){
			$tmp .= "<tr>";
			for ($j=0;$j<$_POST[cols];$j++) $tmp .= "<td></td>";
			$tmp .= "</tr>";
		}
		$tmp .= "</table>";

?>

		<script>
		parent.opener.mini_insertHTML("<?=$tmp?>");
		window.close();
		</script>

<?

		break;

}

?>