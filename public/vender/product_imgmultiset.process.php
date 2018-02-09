<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

if($_venderdata->grant_product[1]!="Y") {
	alert_go("상품정보 수정 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",-1);
}

$type=$_POST["type"];
$mulimgno=$_POST["mulimgno"];
$productcode=$_POST["productcode"];

for($i=1;$i<=10;$i++){
	$img_new=sprintf("mulimg%02d",$i);
	$img_old=sprintf("oldimg%02d",$i);
	${$img_new} = $_FILES[$img_new];
	${$img_old} = $_POST[$img_old];
}

$imagepath=$Dir.DataDir."shopimages/multi/";

if ($type=="delete") {
	if (strlen($productcode)==18) {
		if (strlen($mulimgno)>0) {	//부분삭제
			proc_matchfiledel($imagepath."*".$mulimgno."_".$productcode."*");
			$oldfile = array ("01"=>$oldimg01,"02"=>$oldimg02,"03"=>$oldimg03,"04"=>$oldimg04,"05"=>$oldimg05,"06"=>$oldimg06,"07"=>$oldimg07,"08"=>$oldimg08,"09"=>$oldimg09,"10"=>$oldimg10);

			$num=0;
			for($i=1;$i<=10;$i++) {
				$gbn = sprintf("%02d",$i);
				if(strlen($oldfile[$gbn])>0) $num++;
			}
			if($num<=0) {
				$sql = "DELETE FROM tblmultiimages WHERE productcode = '".$productcode."' ";
			} else {
				$sql = "SELECT size FROM tblmultiimages WHERE productcode = '".$productcode."' ";
				$result = pmysql_query($sql,get_db_conn());
				if($row = pmysql_fetch_object($result)){
					if(strlen($row->size)!=0){
						$tmpsize=explode("",$row->size);
						$delsize = array("01"=>$tmpsize[0],"02"=>$tmpsize[1],"03"=>$tmpsize[2],"04"=>$tmpsize[3],"05"=>$tmpsize[4],"06"=>$tmpsize[5],"07"=>$tmpsize[6],"08"=>$tmpsize[7],"09"=>$tmpsize[8],"10"=>$tmpsize[9]);
						for($i=1;$i<=10;$i++){
							$gbn = sprintf("%02d",$i);
							if($gbn==$mulimgno) $delimgsize.="";
							else $delimgsize.="".$delsize[$gbn];
						}
					}
				}
				if(strlen($delimgsize)>0) $delimgsize=",size='".ltrim($delimgsize,'')."'";
				else $delimgsize=",size=NULL";
				$sql = "UPDATE tblmultiimages SET primg".$mulimgno."='' ".$delimgsize." ";
				$sql.= "WHERE productcode = '".$productcode."' ";
			}
		} else {	//전체삭제
			proc_matchfiledel($imagepath."*".$productcode."*");
			$sql = "DELETE FROM tblmultiimages WHERE productcode = '".$productcode."' ";
		}
		pmysql_query($sql,get_db_conn());
	}
	echo "<html></head><body onload=\"parent.document.form1.submit();\"></body></html>";exit;
} else if ($type=="insert" || $type=="update") {
	$oldfile = array ("01"=>$oldimg01,"02"=>$oldimg02,"03"=>$oldimg03,"04"=>$oldimg04,"05"=>$oldimg05,"06"=>$oldimg06,"07"=>$oldimg07,"08"=>$oldimg08,"09"=>$oldimg09,"10"=>$oldimg10);
	$filearray = array ("01"=>$mulimg01["name"],"02"=>$mulimg02["name"],"03"=>$mulimg03["name"],"04"=>$mulimg04["name"],"05"=>$mulimg05["name"],"06"=>$mulimg06["name"],"07"=>$mulimg07["name"],"08"=>$mulimg08["name"],"09"=>$mulimg09["name"],"10"=>$mulimg10["name"]);
	$filen = array ("01"=>$mulimg01["tmp_name"],"02"=>$mulimg02["tmp_name"],"03"=>$mulimg03["tmp_name"],"04"=>$mulimg04["tmp_name"],"05"=>$mulimg05["tmp_name"],"06"=>$mulimg06["tmp_name"],"07"=>$mulimg07["tmp_name"],"08"=>$mulimg08["tmp_name"],"09"=>$mulimg09["tmp_name"],"10"=>$mulimg10["tmp_name"]);
	if ($type=="insert") {
		$sql = "INSERT INTO tblmultiimages(productcode) VALUES ('{$productcode}')";		
		$result = pmysql_query($sql,get_db_conn());
	} else {
		$sql = "SELECT size FROM tblmultiimages WHERE productcode = '".$productcode."' ";
		$result = pmysql_query($sql,get_db_conn());
		if ($row = pmysql_fetch_object($result)){
			if (strlen($row->size)!=0){
				$tmpsize=explode("",$row->size);
				$delsize = array("01"=>$tmpsize[0],"02"=>$tmpsize[1],"03"=>$tmpsize[2],"04"=>$tmpsize[3],"05"=>$tmpsize[4],"06"=>$tmpsize[5],"07"=>$tmpsize[6],"08"=>$tmpsize[7],"09"=>$tmpsize[8],"10"=>$tmpsize[9]);
			}
		}
	}
	$sql = "UPDATE tblmultiimages SET ";
	$file_size=0;
	for($i=1;$i<=10;$i++){
		$gbn = sprintf("%02d",$i);
		$image="";
		if (strlen($filearray[$gbn])>0) {
			if (strlen($filearray[$gbn])>0 && file_exists($filen[$gbn])) {
				$image = $gbn."_".$productcode.".".strtolower(pathinfo($filearray[$gbn],PATHINFO_EXTENSION));
				$imgname=$imagepath."s".$image;
				$file_size += filesize($filen[$gbn]);
				if($type=="update" && strlen($oldfile[$gbn])>0) {
					proc_matchfiledel($imagepath."*".$oldfile[$gbn]);
				}
				move_uploaded_file($filen[$gbn],$imagepath.$image);
				chmod($imagepath.$image, 0664);
				copy($imagepath.$image,$imgname);
				chmod($imgname, 0664);
				$size=getimageSize($imgname);
				$width=$size[0];
				$height=$size[1];
				$imgtype=$size[2];
				$maxsize=80;
				if ($width>$maxsize || $height>$maxsize) {
					if ($imgtype==1) $im = ImageCreateFromGif($imgname);
					else if ($imgtype==2) $im = ImageCreateFromJpeg($imgname);
					else if( $imgtype==3) $im = ImageCreateFromPng($imgname);
					if ($width>=$height) {
						$small_width=$maxsize;
						$small_height=($height*$maxsize)/$width;
					} else if ($width<$height) {
						$small_width=($width*$maxsize)/$height;
						$small_height=$maxsize;
					}

					// GIF일경우
					if ($imgtype==1) $im2=ImageCreate($small_width,$small_height);
					// JPG일경우
					else $im2=ImageCreateTrueColor($small_width,$small_height);

					// 홀수픽셀의 경우 검은줄을 흰색으로 바꾸기위해.
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);

					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);

					if($imgtype==1) imageGIF($im2,$imgname);
					else if($imgtype==2) imageJPEG($im2,$imgname);
					else if($imgtype==3) imagePNG($im2,$imgname);
					ImageDestroy($im);
					ImageDestroy($im2);
				}
			}
		} else if (strlen($oldfile[$gbn])>0) {
			$image=$oldfile[$gbn];
		}
		if (strlen($image)>0) {
			if ($type=="insert") {
				$sql.= "primg".$gbn." = '".$image."',";
				$imgsize.="".$width."X".$height;
			} else {
				$sql.= "primg".$gbn." = '".$image."',";
				if (strlen($filearray[$gbn])>0) $imgsize.="".$width."X".$height;
				else $imgsize.="".$delsize[$gbn];
			}
		} else {
			if ($type=="insert") {
			   $sql.= "primg".$gbn." = '',";
			   $imgsize.="";
			} else {
			   $sql.= "primg".$gbn." = '',";
			   $imgsize.="";
			}
		}
	}
	$imgsize=ltrim($imgsize,'');
	$sql.= "size = '".$imgsize."' ";
	$sql.= " WHERE productcode = '".$productcode."' ";
	pmysql_query($sql,get_db_conn());
	if (!pmysql_errno()) {
		if($type=="insert") {
			echo "<html></head><body onload=\"alert('상품 이미지가 등록되었습니다.');parent.document.form1.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('상품 이미지가 수정되었습니다.');parent.document.form1.submit();\"></body></html>";exit;
		}
	} else {
		sleep(1);
		pmysql_query($sql,get_db_conn());
		if (!pmysql_errno()) {
			if($type=="insert") {
				echo "<html></head><body onload=\"alert('상품 이미지가 등록되었습니다.');parent.document.form1.submit();\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('상품 이미지가 수정되었습니다.');parent.document.form1.submit();\"></body></html>";exit;
			}
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}
}
