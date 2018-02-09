<?php
// default redirection
$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
echo "<script>alert('1');</script>";
if(bSuccessUpload) {
	$tmp_name = $_FILES['Filedata']['tmp_name'];
	//$name = time().$_FILES['Filedata']['name'];
	$temp_var= explode(".", rawurldecode($_FILES['Filedata']['name']));
	$name = str_replace("\0", "", time().'-'.rand(0,100).'.'.$temp_var[1]);
	
	$filename_ext = strtolower(array_pop(explode('.',$name)));
	$allow_file = array("jpg", "png", "bmp", "gif");
	
	if(!in_array($filename_ext, $allow_file)) {
		$url .= '&errstr='.$name;
	} else {
		$uploadDir = '../../upload/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}
		
		//$newPath = $uploadDir.urlencode($_FILES['Filedata']['name']);
        $newPath = $uploadDir.urlencode($name);
		
		@move_uploaded_file($tmp_name, $newPath);
		
		$url .= "&bNewLine=true";
		$url .= "&sFileName=".urlencode(urlencode($name));
		$url .= "&sFileURL=/SE2/upload/".urlencode(urlencode($name));
	}
}
// FAILED
else {
	$url .= '&errstr=error';
}
	
header('Location: '. $url);
?>