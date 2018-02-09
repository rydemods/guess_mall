<?php
exdebug('종료');
exit;
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$imagepath = "../data/shopimages/product/";
/*
$sql = 'Select it_id, it_name, it_img1 from g5_shop_item order by it_time asc';
$result = pmysql_query($sql, get_db_conn());
while($row = pmysql_fetch_array($result)) {
	if( !is_dir($imagepath.$row['it_id']) ){
		exdebug( 'no Dir :: pcode = '.$row[it_id].' / pname = '.$row[it_name] );
	} else {
		if( !is_file($imagepath.$row[it_img1]) ){
			exdebug( 'no File :: pcode = '.$row[it_id].' / pname = '.$row[it_name] );
		}
	}
}
exit;
*/
$sql = "SELECT productcode, productname, maximage, minimage, tinyimage FROM tblproduct ORDER BY date DESC "; //LIMIT 100
$result = pmysql_query($sql, get_db_conn());

$errOutPut = '----------------------------------- '.date("Y-m-d H:i:s")."\n";
$successOutPout = '----------------------------------- '.date("Y-m-d H:i:s")."\n";
while($row = pmysql_fetch_array($result)) {
	$pcode = $row['productcode'];
	$pname = $row['productname'];
	$maximage = $row['maximage']; // max image 기준으로 파일 생성
	$minimage = $row['minimage'];
	//$tinyimage = $row['tinyimage'];

	if( is_dir($imagepath.$pcode) ){
		if( !is_file($imagepath.$maximage) ){
			$imgSql = "SELECT it_id, it_name, it_img1 FROM g5_shop_item WHERE it_id = '".$pcode."'";
			$imgRes = pmysql_query( $imgSql, get_db_conn() );
			if( $imgRow = pmysql_fetch_array( $imgRes ) ){
				if( is_file($imagepath.$imgRow['it_img1']) && strlen( $imgRow['it_img1'] ) > 0 ){
					$tmp_img1 = explode("/", $imgRow['it_img1']);
					$tmp_img2 = explode(".", $tmp_img1[1]);
					$large_img = $tmp_img1[0]."/".$tmp_img2[0]."_thum_390X390".".".$tmp_img2[1];
					$middle_img = $tmp_img1[0]."/".$tmp_img2[0]."_thum_255X255".".".$tmp_img2[1];
					$small_img = $tmp_img1[0]."/".$tmp_img2[0]."_thum_85X85".".".$tmp_img2[1];

					$successOutPout.= "\n";
					$successOutPout.= ' code = '.$pcode.' , name = '.$pname."\n";
					$successOutPout.= ' large_img = '.$large_img."\n";
					$successOutPout.= ' middle_img = '.$middle_img."\n";
					$successOutPout.= ' small_img = '.$small_img."\n";
					$successOutPout.= "\n";

					copy($imagepath.$imgRow['it_img1'],$imagepath.$large_img);  //지정폴더로 파일을 올린다
					copy($imagepath.$imgRow['it_img1'],$imagepath.$middle_img);  //지정폴더로 파일을 올린다
					copy($imagepath.$imgRow['it_img1'],$imagepath.$small_img);  //지정폴더로 파일을 올린다

					@chmod($imagepath.$large_img, 0777);
					@chmod($imagepath.$middle_img, 0777);
					@chmod($imagepath.$small_img, 0777);
					
					CreateThumbnail($imgRow[it_id], $large_img, "390", "390");
					CreateThumbnail($imgRow[it_id], $middle_img, "255", "255");
					CreateThumbnail($imgRow[it_id], $small_img, "85", "85");

				}else{
					//$large_img = '';
					//$middle_img = "";
					//$small_img = "";
					$errOutPut .= 'no File : code = '.$pcode.' , name = '.$pname."\n";
				}
			}
			pmysql_free_result( $imgRes );
		}
	} else {
		$errOutPut.= 'no Dir : code = '.$pcode.' , name = '.$pname."\n";
	}
	
}

$errOutPut.= "\n";
$w_f = fopen('img_err_product_'.date("Ymd").'.txt','a');
fwrite($w_f, $errOutPut );
fclose($w_f);

$successOutPout.= "\n";
$s_f = fopen('img_success_product_'.date("Ymd").'.txt','a');
fwrite($s_f, $successOutPout );
fclose($s_f);

exdebug('end');

function CreateThumbnail($pcode, $org_img, $t_width, $t_height) {

    $imagepath = "../data/shopimages/product/";
    $quality = 100;

    $imgname=$imagepath.$org_img; // 위치 + 파일명
    $size=getimageSize($imgname); //파일 사이즈 array ( 0=>width, 1=>height, 2=>imgtype )
    $width=$size[0];  
    $height=$size[1];
    $imgtype=$size[2];
    //$makesize1= $max_size; // 변경할 파일 최대 넓이 또는 높이

    if ($width>$makesize1 || $height>$makesize1) {

        # 파일 타입별 이미지생성
        if($imgtype==1)      $im = ImageCreateFromGif($imgname);
        else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
        else if($imgtype==3) $im = ImageCreateFromPng($imgname);

        # 파일의 넓이나 높이가 큰 부분을 기준으로 파일을 자른다
        /*
        if ($width>=$height) {
            $small_width=$makesize1;
            $small_height=($height*$makesize1)/$width;
        } else if($width<$height) {
            $small_width=($width*$makesize1)/$height;
            $small_height=$makesize1;
        }
        */
        $small_width=$t_width;
        $small_height=$t_height;

        # 타입별로 파일 색상, 크기 리사이즈
        if ($imgtype==1) {
            $im2=ImageCreate($small_width,$small_height); // GIF일경우
            $white = ImageColorAllocate($im2, 255,255,255);
            imagefill($im2,1,1,$white);
            ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
            imageGIF($im2,$imgname);
        } else if ($imgtype==2) {
            $im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
            $white = ImageColorAllocate($im2, 255,255,255);
            imagefill($im2,1,1,$white);
            imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
            imageJPEG($im2,$imgname,$quality);
        } else {
            $im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
            $white = ImageColorAllocate($im2, 255,255,255);
            imagefill($im2,1,1,$white);
            imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
            imagePNG($im2,$imgname);
        }

        ImageDestroy($im);
        ImageDestroy($im2);
    }
}

?>