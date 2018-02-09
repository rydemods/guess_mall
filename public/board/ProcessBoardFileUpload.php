<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$mini_size=100;

$board=$_POST["board"];
$max_filesize=$_POST["max_filesize"];
$btype=$_POST["btype"];

if(strlen($max_filesize)==0) $max_filesize="204800";
if ($max_filesize<100000) $max_filesize="102400";


$type=$_POST["type"];
$upfile=$_FILES["upfile"];

if (strlen($upfile["name"])>0 && $upfile["size"]>0 && $type=="upload") {
	if($upfile["size"]>$max_filesize) {
		$errmsg="파일용량이 초과되어 업로드가 실패하였습니다.\\n\\n올리실 수 있는 파일 용량은 ".($max_filesize/1024)."KB 입니다.";
		ErrorMsg($errmsg);
		exit;
	}

	$filepath = $Dir.DataDir."shopimages/board/".$board;

	$upfile["name"] = str_replace(" ","",$upfile["name"]);
	$file_name = pathinfo($upfile["name"],PATHINFO_FILENAME);					
	$file_ext = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
	
	if(!in_array($file_ext,array('jpg','gif','png'))) {
		if($btype=="W" || $btype=="I") {
			$errmsg="앨범형 게시판 및 웹진형 게시판의 첨부파일은 이미지(jpg,gif,png) 파일만 첨부 가능합니다.";
			ErrorMsg($errmsg);
			exit;
		}
	}

	if((strlen($file_name)-(strlen($file_ext)+1))>30) {
		$file_name=date("ymdHis");
	}

	if ($file_ext=="htm"||$file_ext=="html") {
		$file_ext="htms";
	} else if (($file_ext)=="php" || ($file_ext)=="php3" || ($file_ext)=="php4") {
		$file_ext="phps";
	} else if(strlen($file_ext)==0) {
		$file_ext="temp";
	}

	while (!CheckFileDup()) $cnt++;

	if ($cnt==30) {
		$file_name = date("YmdHis");
		$cnt="";
	} else if ($cnt==0) {
		$cnt="";
	} else {
		$cnt = "_".$cnt;
	}

	$file_name = $file_name.$cnt.".".$file_ext;
	$upfile["name"]=$file_name;

	copy($upfile["tmp_name"], $Dir.DataDir."cache/board/".$board.".".$file_name);

	if(in_array($file_ext,array('jpg','gif','png'))) {
		CreateMini($board,$file_name,$mini_size);
	}

	echo "<script>\n";
	echo "	location.href='".$Dir.BoardDir."boardupload.php?file_name=".urlencode($file_name)."';\n";
	echo "</script>\n";
	exit;
}

@include("ProcessBoardFileUpload.inc.php");

function CheckFileDup() {
	global $filepath, $file_name, $file_ext, $cnt;
	if ($cnt==0) $temp=""; 
	else if ($cnt==30) return true;
	else $temp="_".$cnt;
	if (!file_exists($filepath."/".$file_name.$temp.".".$file_ext)) return true; 
	else return false;
} 

function CreateMini($board,$file_name,$mini_size) {
	$file = DirPath.DataDir."cache/board/".$board.".".$file_name;
	$convert_file = DirPath.DataDir."cache/board/".$board.".thumbnail.".$file_name;
	if (file_exists($file)) {
		$size=getimageSize($file);
		$width=$size[0];
		$height=$size[1];
		$imgtype=$size[2];

		if($imgtype==1)      $im = ImageCreateFromGif($file);
		else if($imgtype==2) $im = ImageCreateFromJpeg($file);
		else if($imgtype==3) $im = ImageCreateFromPng($file);
		else {
			$errmsg="앨범형 게시판 및 웹진형 게시판의 첨부파일은 이미지(jpg,gif,png) 파일만 첨부 가능합니다.";
			ErrorMsg($errmsg);
			exit;
		}

		$height_term = $width_term = 0;

		// 가로가 세로보다 클경우
		if ($width>$height) {
			if ($width>$mini_size) {  // mini_size 100 픽셀보다 크면 리사이즈
				$mini_width=$mini_size;$mini_height=(int)(($height*$mini_size)/$width);
				$height_term = (int) (($mini_size - $mini_height)/2);
			} else {                  // 이미지가 mini_size 보다 작으면 리사이트 하면 안된다.
				$mini_width=$width;$mini_height=$height;
				$height_term = (int) (($mini_size-$height)/2);
				$width_term = (int) (($mini_size-$width)/2);
			}
		} else {
			if ($height>$mini_size) {
				$mini_height=$mini_size;$mini_width=(int)(($width*$mini_size)/$height);
				$width_term = (int) (($mini_size - $mini_width)/2);
			} else {
				$mini_width=$width;$mini_height=$height;
				$height_term = (int) (($mini_size-$height)/2);
				$width_term = (int) (($mini_size-$width)/2);
			}
		}

		// GIF
		if ($imgtype==1) $im2=ImageCreate($mini_size,$mini_size);
		else $im2=ImageCreateTrueColor($mini_size,$mini_size);
		$white = ImageColorAllocate($im2, 255,255,255);
		imagefill($im2,1,1,$white);

		imagecopyresampled($im2,$im,$width_term,$height_term,0,0,$mini_width,$mini_height,$width,$height);

		if ($imgtype==1) imageGIF($im2,$convert_file,90);
		else if ($imgtype==2) imageJPEG($im2,$convert_file,90);
		else if ($imgtype==3) imagePNG($im2,$convert_file,90);

		imagedestroy($im2);
	}
}

function ErrorMsg($errmsg) {
	global $board,$max_filesize,$btype;
	echo "
		<html>
		<head><title></title></head>
		<body onload=\"alert('$errmsg');document.form1.submit();\">
		<form name=form1 method=post action=$_SERVER[PHP_SELF]>
		<input type=hidden name=board value=$board>
		<input type=hidden name=max_filesize value=$max_filesize>
		<input type=hidden name=btype value=$btype>
		</form>
		</body>
		</html>
	";
	exit;
}
