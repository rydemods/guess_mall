<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

//게시판 디렉토리 생성 및 삭제
function ProcessBoardDir($board,$mode) {
	if(strlen($board)==0 || strlen($mode)==0) {
		$resdata="FAIL";
	} else {
		if($mode=="create") {
			if(!is_dir(DirPath.DataDir."shopimages/board")) {
				mkdir(DirPath.DataDir."shopimages/board");
				chmod(DirPath.DataDir."shopimages/board",0777);
			}
			if(!is_dir(DirPath.DataDir."shopimages/board/".$board)) {
				mkdir(DirPath.DataDir."shopimages/board/".$board);
				chmod(DirPath.DataDir."shopimages/board/".$board,0777);
			}
			$resdata="OK";
		} else if($mode=="delete") {
			if(!is_dir(DirPath.DataDir."shopimages/board/".$board)) {
				$resdata="OK";
			} else {
				proc_rmdir(DirPath.DataDir."shopimages/board/".$board);
				$resdata="OK";
			}
		} else {
			$resdata="FAIL";
		}
	}
}

// 파일등록
function ProcessBoardFileIn($board,$file_name) {
	$resdata="SUCCESS";

	if (empty($board) || empty($file_name)) {
		$resdata="FAIL";
	}

	$file       = DirPath.DataDir."cache/board/".$board.".".$file_name;
	$file_mini  = DirPath.DataDir."cache/board/".$board.".thumbnail.".$file_name;
	$dirpath    = DirPath.DataDir."shopimages/board/".$board;
	$file2      = DirPath.DataDir."shopimages/board/".$board."/".$file_name;
	$file2_mini = DirPath.DataDir."shopimages/board/".$board."/thumbnail.".$file_name;

	if (!file_exists($file)) {
		$resdata="FAIL";
	} else if ($resdata!="FAIL") {
		if (!is_dir("$dirpath")) {
			mkdir($dirpath);
			chmod($dirpath,0777);
		}
		copy ($file,$file2);
		unlink($file);

		if (file_exists($file_mini)) {
			copy ($file_mini,$file2_mini);
			unlink($file_mini);
		}
	}
	return $resdata;
}

//파일삭제
function ProcessBoardFileDel($board,$file_name) {
	$resdata="SUCCESS";

	if (empty($board) || empty($file_name)) {
		$mess="FAIL";
	}

	$file      = DirPath.DataDir."shopimages/board/".$board."/".$file_name;
	$file_mini = DirPath.DataDir."shopimages/board/".$board."/thumbnail.".$file_name;

	if (!file_exists($file)) {
		$resdata="FAIL";
	} else {
		unlink($file);
		if (file_exists("$file_mini")) {
			unlink($file_mini);
		}
	}
	return $resdata;
}

//파일수정
function ProcessBoardFileModify($board,$file_name,$oldfile_name) {
	$resdata="SUCCESS";

	if (empty($board) || empty($file_name)) {
		$resdata="FAIL";
	}

	$file       = DirPath.DataDir."cache/board/".$board.".".$file_name;
	$file_mini  = DirPath.DataDir."cache/board/".$board.".thumbnail.".$file_name;
	$dirpath    = DirPath.DataDir."shopimages/board/".$board;
	$file2      = DirPath.DataDir."shopimages/board/".$board."/".$file_name;
	$file2_mini = DirPath.DataDir."shopimages/board/".$board."/thumbnail.".$file_name;

	$file3 = DirPath.DataDir."shopimages/board/".$board."/".$oldfile_name;
	$file3_mini = DirPath.DataDir."shopimages/board/".$board."/thumbnail.".$oldfile_name ;

	if (!file_exists($file)) {
		$resdata="FAIL";
	} else if ($resdata!="FAIL") {
		if (!is_dir("$dirpath")) {
			mkdir($dirpath);
			chmod($dirpath,0777);
		}
		copy ($file,$file2);
		unlink($file);

		if (file_exists($file_mini)) {
			copy ($file_mini,$file2_mini);
			unlink($file_mini);
		}
	}

	//기존파일은 삭제
	if ($resdata=="SUCCESS" && strlen($oldfile_name)>0) {
		@unlink($file3);
		@unlink($file3_mini);
	}
	return $resdata;
}

//파일 용량 구한다.
function ProcessBoardFileSize($board,$file_name) {
	$size = "0";

	if (empty($BOARD) || empty($file_name)) {
		$size="0";
	}

	$file=DirPath.DataDir."shopimages/board/".$board."/".$file_name;

	if (!file_exists($file)) {
		$size="0";
	} else {
		$size = filesize($file);
		if ($size>=1024 && $size<=1024000) {
			$size = (int)($size/1024);
			$size = "$size KB";
		} else if ($size>=1024000) {
			$size = (int)($size/102400);
			$size /= 10;
			$size = "$size M";
		}
	}
	return $size;
}

// 파일의 가로크기를 구한다.
function ProcessBoardFileWidth($board,$file_name) {
	$resdata="0";

	if (empty($board) || empty($file_name)) {
		$resdata="0";
	}

	$file=DirPath.DataDir."shopimages/board/".$board."/".$file_name;

	if (!file_exists("$file")) {
		$resdata="0";
	} else {
		$size=getimagesize($file);
		$resdata=$size[0];
	}
	return $resdata;
}

//첨부파일 다운로드 링크
function FileDownload($board,$file_name,$r_file) {
	return "<a href=\"".DirPath.BoardDir."download.php?board=".$board."&file_name=".urlencode($file_name)."\" target=_top>".$r_file."</a>";
}

//이미지 첨부파일 url
function ImageAttachUrl($board,$file_name) {
	$ext = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
	if(in_array($ext,array('gif','jpg','png'))) {
		return DirPath.DataDir."shopimages/board/".$board."/".urlencode($file_name);
	} else {
		return "";
	}
}

function ImageMiniUrl($board,$file_name) {
	if($file_name) {
		return DirPath.DataDir."shopimages/board/".$board."/".urlencode($file_name);
	} else {
		return "";
	}
}
