<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$userspec_cnt=5;
$maxfilesize="512000";
$mode=$_POST["mode"];

$checkmaxq=$_POST["checkmaxq"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$productcode=$_POST["productcode"];
$productname=$_POST["productname"];
$vimage=$_POST["vimage"];
$vimage2=$_POST["vimage2"];
$vimage3=$_POST["vimage3"];
$changecode=$_POST["changecode"];
$code_a=$_REQUEST["code_a"]?$_REQUEST["code_a"]:substr($code,0,3);
$code_b=$_REQUEST["code_b"]?$_REQUEST["code_b"]:substr($code,3,3);
$code_c=$_REQUEST["code_c"]?$_REQUEST["code_c"]:substr($code,6,3);
$code_d=$_REQUEST["code_d"]?$_REQUEST["code_d"]:substr($code,9,3);

if($changecode)$typecode=$changecode;
else $typecode=$code;
if(strlen($typecode)==12) {
	$sql = "SELECT type, list_type FROM tblproductcode WHERE code_a='".substr($typecode,0,3)."' ";
	$sql.= "AND code_b='".substr($typecode,3,3)."' ";
	$sql.= "AND code_c='".substr($typecode,6,3)."' AND code_d='".substr($typecode,9,3)."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row) exit;
	if(strpos($row->type,'X')===FALSE) exit;

	$type = $row->type;
	if ($row->list_type[0]=="B")
		$gongtype="Y";
	else
		$gongtype="N";

	if($gongtype=="Y") {
		$bgcolor="#E9E7F1";
		$list_title = "<B>가격 고정형 공동구매 상품목록</B>";
	} else {
		$bgcolor="#F0F0F0";
		$list_title = "<B>판매상품 목록</B>";
	}

}// else {
//	exit;
//}


if(ord($_POST["setcolor"])==0){
	$setcolor=$_COOKIE["setcolor"];
} else if($_COOKIE["setcolor"]!=$_POST["setcolor"]){
	setcookie("setcolor",$setcolor,0,"/".RootPath.AdminDir);
	$setcolor=$_POST["setcolor"];
} else {
	$setcolor=$_COOKIE["setcolor"];
}

#상품이미지 태두리 쿠키 설정
if ($_POST["imgborder"]=="Y" && $_COOKIE["imgborder"]!="Y") {
	setcookie("imgborder","Y",0,"/".RootPath.AdminDir);
} else if ($_POST["imgborder"]!="Y" && $_COOKIE["imgborder"]=="Y" && ($mode=="insert" || $mode=="modify")) {
	setcookie("imgborder","",time()-3600,"/".RootPath.AdminDir);
	$imgborder="";
} else {
	$imgborder=$_COOKIE["imgborder"];
}

#상품등록날짜 고정 쿠키 설정
if ($_COOKIE["insertdate_cook"]=="Y" && $insertdate!="Y" && $mode=="modify") {
	setcookie("insertdate_cook","",time()-3600,"/".RootPath.AdminDir);
	$insertdate_cook="";
} else if ($_COOKIE["insertdate_cook"]!="Y" && $insertdate=="Y" && $mode=="modify") {
	setcookie("insertdate_cook","Y",time()+2592000,"/".RootPath.AdminDir);
	$insertdate_cook="Y";
}

if(ord($setcolor)==0) $setcolor="000000";
$rcolor=HexDec(substr($setcolor,0,2));
$gcolor=HexDec(substr($setcolor,2,2));
$bcolor=HexDec(substr($setcolor,4,2));
$quality = "90";


$popup=$_POST["popup"];
$option1=$_POST["option1"];
$option1_name=$_POST["option1_name"];
$option2=$_POST["option2"];
$option2_name=$_POST["option2_name"];
$consumerprice=$_POST["consumerprice"];
$buyprice=$_POST["buyprice"];
$sellprice=$_POST["sellprice"];
$assembleuse=$_POST["assembleuse"];
$production=$_POST["production"];
$keyword=$_POST["keyword"];
$quantity=$_POST["quantity"];
$checkquantity=$_POST["checkquantity"];
$reserve=$_POST["reserve"];
$reservetype=$_POST["reservetype"];
$package_num=$_POST["package_num"];
$deli=$_POST["deli"];
$optnumvalue=$_POST["optnumvalue"];

if($deli=="Y")
	$deli_price=(int)$_POST["deli_price_value2"];
else
	$deli_price=(int)$_POST["deli_price_value1"];

if($deli=="H" || $deli=="F" || $deli=="G") $deli_price=0;
if($deli!="Y" && $deli!="F" && $deli!="G") $deli="N";
$display=$_POST["display"];
$addcode=$_POST["addcode"];
$option_price=str_replace(" ","",$_POST["option_price"]);
$option_price=rtrim($option_price,',');
$madein=$_POST["madein"];
$model=$_POST["model"];
$brandname=$_POST["brandname"];
$opendate=$_POST["opendate"];
$selfcode=$_POST["selfcode"];
$bisinesscode=$_POST["bisinesscode"];
$optiongroup=$_POST["optiongroup"];
$imgcheck=$_POST["imgcheck"];
$bankonly=$_POST["bankonly"];
$deliinfono=$_POST["deliinfono"];
$setquota=$_POST["setquota"];
$miniq=$_POST["miniq"];
$maxq=$_POST["maxq"];
$insertdate=$_POST["insertdate"];
$localsave=$_POST["localsave"];
$content=$_POST["content"];
$dicker=$_POST["dicker"];
$dicker_text=$_POST["dicker_text"];
$iconvalue=$_POST["iconvalue"];

$userspec=$_POST["userspec"];
$specname=$_POST["specname"];
$specvalue=$_POST["specvalue"];

$group_check=$_POST["group_check"];
$group_code=$_POST["group_code"];

$delprdtimg=$_POST["delprdtimg"];

if($group_check=="Y" && count($group_code)>0) {
	$group_check="Y";
} else {
	$group_check="N";
	$group_code="";
}

$specarray='';
if($userspec == "Y") {
	for($i=0; $i<$userspec_cnt; $i++) {
		$specarray[$i]=$specname[$i]."".$specvalue[$i];
	}
	$userspec = implode("=",$specarray);
} else {
	$userspec = "";
}

if(ord($display)==0) $display='Y';

if((int)$opendate<1) $opendate="";

$searchtype=$_POST["searchtype"];
if(ord($searchtype)==0) $searchtype=0;

$userfile = $_FILES["userfile"];
$userfile2 = $_FILES["userfile2"];
$userfile3 = $_FILES["userfile3"];

$use_imgurl=$_POST["use_imgurl"];
$userfile_url=$_POST["userfile_url"];
$userfile2_url=$_POST["userfile2_url"];
$userfile3_url=$_POST["userfile3_url"];
if($use_imgurl!="Y") {
	$userfile_url="";
	$userfile2_url="";
	$userfile3_url="";
}

$maxsize=130;
$makesize=130;

$card_splittype = $_shopdata->card_splittype;
$makesize=$_shopdata->primg_minisize;
$predit_type=$_shopdata->predit_type;
$maxsize=$makesize+10;
if(strpos(" ".$_shopdata->etctype,"IMGSERO=Y")) {
	$imgsero="Y";
}

if(substr($prcode,0,12)!=$code && ord($prcode)) {
	$prcode="";
	$maxq="";
}
if (ord($mode)==0) $maxq="";

if ($mode=="insert" || $mode=="modify") {
	$etctype = "";
	if ($bankonly=="Y") $etctype .= "BANKONLY";
	if ($deliinfono=="Y") $etctype .= "DELIINFONO=Y";
	if ($setquota=="Y") $etctype .= "SETQUOTA";
	if (ord(substr($iconvalue,0,3)))       $etctype .= "ICON={$iconvalue}";
	if ($dicker=="Y" && ord($dicker_text)) $etctype .= "DICKER={$dicker_text}";

	if ($miniq>1)       $etctype .= "MINIQ={$miniq}";
	else if ($miniq<1){
		alert_go('최소구매수량 수량은 1개 보다 커야 합니다.',-1);
	}
	if ($checkmaxq=="B" && $maxq>=1)        $etctype .= "MAXQ={$maxq}";
	else if ($checkmaxq=="B" && $maxq<1){
		alert_go('최대구매수량 수량은 1개 보다 커야 합니다.',-1);
	}

	if ($bankonly=="Y" && $setquota=="Y") {
		alert_go('현금전용결제와 무이자상점부담을 동시에 체크하실 수 없습니다.(결제수단이 서로 틀림)',-1);
	}
} else {
	$bankonly="";
	$deliinfono="";
	$setquota="";
	$miniq="";
	$freedeli="N";
}

$imagepath=$Dir.DataDir."shopimages/product/";

if($mode=="delprdtimg"){
	$delarray = array (&$vimage,&$vimage2,&$vimage3);
	$delname = array ("maximage","minimage","tinyimage");
	if(ord($delarray[$delprdtimg]) && file_exists($imagepath.$delarray[$delprdtimg])) {
		unlink($imagepath.$delarray[$delprdtimg]);
	}
	$sql = "UPDATE tblproduct SET $delname='' WHERE productcode = '{$prcode}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품이미지를 삭제하였습니다.');</script>";
}

if (ord($option1) && ord($option1_name)) {
	$option1 = $option1_name.",".rtrim($option1,',');
} else {
	$option1="";
}
if (ord($option2) && ord($option2_name)) {
	$option2 = $option2_name.",".rtrim($option2,',');
} else {
	$option2="";
}

$optcnt="";
$tempcnt=0;
if ($searchtype=="1") {
	for($i=0;$i<5;$i++){
		for($j=0;$j<10;$j++){
			if(ord(trim($optnumvalue[$i][$j]))) {
				$optnumvalue[$i][$j]=(int)$optnumvalue[$i][$j];
				$tempcnt++;
			}
			$optcnt.=",".$optnumvalue[$i][$j];
		}
	}
}
if($tempcnt>0) $optcnt.=",";
else $optcnt="";

if($mode=="insert") {
	$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
	$sql.= "WHERE productcode LIKE '{$code}%' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($rows = pmysql_fetch_object($result)) {
		if (strlen($rows->maxproductcode)==18) {
			$productcode = ((int)substr($rows->maxproductcode,12))+1;
			$productcode = sprintf("%06d",$productcode);
		} else if($rows->maxproductcode==NULL){
			$productcode = "000001";
		} else {
			alert_go('상품코드를 생성하는데 실패했습니다. 잠시후 다시 시도하세요.',-1);
		}
		pmysql_free_result($result);
	} else {
		$productcode = "000001";
	}
}

if ($mode=="insert") {
	$image_name = $code.$productcode;
} elseif ($mode=="modify") {
	$image_name = $prcode;
}

if($use_imgurl!="Y") {
	$file_size = $userfile['size']+$userfile2['size']+$userfile3['size'];
} else {
	$file_size=0;
}

if($file_size < $maxfilesize) {
	if (ord($reserve)==0) {
		$reserve=0;
	}

	if ($reservetype!="Y") {
		$reservetype=="N";
	}

	$curdate = date("YmdHis");

	$productname = str_replace("\\\\'","''",$productname);
	$addcode = str_replace("\\\\'","''",$addcode);
	$content = str_replace("\\\\'","''",$content);

	$message="";

	if($use_imgurl!="Y") {
		if($imgcheck=="Y") $filename = array (&$userfile['name'],&$userfile['name'],&$userfile['name']); 
		else $filename = array (&$userfile['name'],&$userfile2['name'],&$userfile3['name']);
		$file = array (&$userfile['tmp_name'],&$userfile2['tmp_name'],&$userfile3['tmp_name']);
	} else {
		if($imgcheck=="Y") $filename = array (&$userfile_url,&$userfile_url,&$userfile_url); 
		else $filename = array (&$userfile_url,&$userfile2_url,&$userfile3_url);
		$file = array (&$userfile_url,&$userfile2_url,&$userfile3_url);
	}

	$vimagear = array (&$vimage,&$vimage2,&$vimage3);
	$imgnum = array ("","2","3");

	if($mode=="insert" || $mode=="modify"){
		for($i=0;$i<3;$i++){
			if($use_imgurl!="Y") {
				if ($mode=="modify" && ord($vimagear[$i]) && ord($filename[$i]) && file_exists($imagepath.$vimagear[$i])) {
					unlink($imagepath.$vimagear[$i]);
				}
				if (ord($filename[$i]) && file_exists($file[$i])) {
					$ext = strtolower(pathinfo($filename[$i],PATHINFO_EXTENSION));
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name.$imgnum[$i].".".$ext;
						move_uploaded_file($file[$i],$imagepath.$image[$i]);
						chmod($imagepath.$image[$i],0664);
					} else {
						$image[$i]="";
					}
				} else if($imgcheck=="Y" && ord($filename[$i])) {
					$image[$i]=$image_name.$imgnum[$i].".".$ext;
					copy($imagepath.$image[0],$imagepath.$image[$i]);
				} else {
					$image[$i] = $vimagear[$i];
				}
			} else {
				if(ord($filename[$i]) && ord($file[$i])) {
					$image_url=str_replace("http://","",$file[$i]);
					$temp=explode("/",$image_url);
					$host=$temp[0];
					$path=str_replace($host,"",$image_url);

					$ext = strtolower(pathinfo($image_url,PATHINFO_EXTENSION));

					$is_upimage=true;
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name.$imgnum[$i].".".$ext;
						$fdata=getRemoteImageData($host,$path,$ext);

						if(ord($fdata)) {
							file_put_contents($imagepath.$image[$i],$fdata);
							chmod($imagepath.$image[$i],0664);
							$tempsize=@getimagesize($imagepath.$image[$i]);
							if($tempsize[0]>0 && $tempsize[1]>0 && (strstr("12",$tempsize[2]))) {

							} else {
								@unlink($imagepath.$image[$i]);
								$is_upimage=false;
							}
						} else {
							$is_upimage=false;
						}
					} else {
						$is_upimage=false;
					}

					if($is_upimage==false) {
						$image[$i]="";
						$filename[$i]="";
					}
				} else if($imgcheck=="Y" && ord($filename[$i])) {
					$image[$i]=$image_name.$imgnum[$i].".".$ext;
					copy($imagepath.$image[0],$imagepath.$image[$i]);
					chmod($imagepath.$image[$i],0664);
				} else {
					$image[$i] = $vimagear[$i];
				}
			}
		}

		if ($imgcheck=="Y" && ord($filename[1]) && file_exists($imagepath.$image[1])) {
			$imgname=$imagepath.$image[1];
			$size=getimageSize($imgname);
			$width=$size[0];
			$height=$size[1];
			$imgtype=$size[2];
			$makesize1=300;
			if ($width>$makesize1 || $height>$makesize1) {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($width>=$height) {
					$small_width=$makesize1;
					$small_height=($height*$makesize1)/$width;
				} else if($width<$height) {
					$small_width=($width*$makesize1)/$height;
					$small_height=$makesize1;
				}

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
		if (ord($filename[2]) && file_exists($imagepath.$image[2])) {
			$imgname=$imagepath.$image[2];
			$size=getimageSize($imgname);
			$width=$size[0];
			$height=$size[1];
			$imgtype=$size[2];
			$makesize2=250;
			$changefile="Y";
			if($imgsero=="Y") $leftmax=$makesize2;
			else $leftmax=$maxsize;
			if ($width>$maxsize || $height>$leftmax) {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($width>=$height) {
					$small_width=$makesize;
					$small_height=($height*$makesize)/$width;
				} else if ($width<$height) {
					if ($imgsero=="Y") {
						$temwidth=$width;$temheight=$height;
						if ($temwidth>$makesize) {
							$temheight=($temheight*$makesize)/$temwidth;
							$temwidth=$makesize;
						}
						if ($temheight>$makesize2) {
							$temwidth=($temwidth*$makesize2)/$temheight;
							$temheight=$makesize2;
						}
						$small_width=$temwidth; $small_height=$temheight;
					} else {
						$small_width=($width*$makesize)/$height; $small_height=$makesize;
					}
				}

				if ($imgtype==1) {
					$im2=ImageCreate($small_width,$small_height); // GIF일경우
					// 홀수픽셀의 경우 검은줄을 흰색으로 바꾸기위해.
					$white = ImageColorAllocate($im2, 255,255,255); 
					imagefill($im2,1,1,$white);
					//$color = ImageColorAllocate ($im2, 0, 0, 0);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imageGIF($im2,$imgname);
				} else if ($imgtype==2) {
					$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imageJPEG($im2,$imgname,$quality);
				} else {
					$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imagePNG($im2,$imgname);
				}

				ImageDestroy($im);
				ImageDestroy($im2);
			} else if($imgborder=="Y") {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($imgtype==1) {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					//$color = ImageColorAllocate ($im, 0, 0, 0);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imageGIF($im,$imgname);
				} else if ($imgtype==2) {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imageJPEG($im,$imgname,$quality);
				} else {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imagePNG($im,$imgname);
				}
				ImageDestroy($im);
			}
		}
		if($checkquantity=="F") $quantity="NULL";
		else if($checkquantity=="E") $quantity=0;
		else if($checkquantity=="A") $quantity=-9999;
		if ($searchtype=="3") {
			if($optiongroup>0) {
				$option1="[OPTG{$optiongroup}]";
				$option2="";
				$option_price="";
				$optcnt="";
			}
		}
	} else if($mode=="delete"){
		for($i=0;$i<3;$i++){
			if(ord($vimagear[$i]) && file_exists($imagepath.$vimagear[$i]))
				unlink($imagepath.$vimagear[$i]);
		}
	}

	####################### 타서버 이미지 쇼핑몰에 저장 #############################
	$arrimgurl='';
	$arrsavefilename=array();
	$in_content=$content;
	if($mode=="insert" || $mode=="modify") {
		if($localsave=="Y") {
			$imagesavepath=$Dir.DataDir."design/etc/";
			if(is_dir($imagesavepath)==false) {
				mkdir($imagesavepath);
				chmod($imagesavepath,0755);
			}
			$arrimgurl=array();
			$cln=explode("\n", $in_content);
			for($i=0;$i<count($cln);$i++) {
				while(preg_match("/[^= \"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}/i", $cln[$i], $imgval)){
					$imgval[0] = rtrim($imgval[0],$imgval[2]);
					$arrimgurl[$imgval[0]]=$imgval;
					$cln[$i]=str_replace($imgval,"",$cln[$i]);
				}
			}
			if(count($arrimgurl)>0) {
				while(list($key,$val)=each($arrimgurl)) {
					$file_url=urldecode($val);
					if(substr($file_url,0,7)=="http://") {
						$file_url=str_replace("http://","",$file_url);
						$temp=explode("/",$file_url);
						$host=$temp[0];
						$path=str_replace($host,"",$file_url);

						$filename=basename($file_url);
						$j=0;
						while(file_exists($imagesavepath.$filename)){
							$file_ext = pathinfo($filename,PATHINFO_EXTENSION);					
							$file_name = pathinfo($filename,PATHINFO_FILENAME);					
							$file_name=substr($file_name, 0, strlen($file_name) - strlen(strrchr($file_name,"[")));

							$filename=$file_name."[{$j}].".$file_ext;
							$j++ ;
						}

						$ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));

						$fdata=getRemoteImageData($host,$path,$ext);

						if(ord($fdata)) {
							$filepath=$imagesavepath.$filename;
							file_put_contents($filepath,$fdata);
							@chmod($filepath,0604);

							$size=@getimagesize($filepath);

							if($size[0]>0 && $size[1]>0 && (strstr("1236",$size[2]))) {
								$arrsavefilename[]=$filename;
								$in_content=@str_replace($val,"/".RootPath.DataDir."design/etc/".$filename,$in_content);
							} else {
								@unlink($filepath);
							}
						}
					}
				}
			}
		}
	}

	if ($mode=="insert") {
		if(strlen($buyprice) < 1 ) $buyprice = 0 ;
		$result = pmysql_query("SELECT COUNT(*) as cnt FROM tblproduct ",get_db_conn());
		if ($row=pmysql_fetch_object($result)) $cnt = $row->cnt;
		else $cnt=0;
		pmysql_free_result($result);

		if($assembleuse=="Y") {
			$sellprice=0;
			$option1="";
			$option2="";
			$option_price="";
			$optcnt="";
			$package_num="0";
		}

		$sql = "INSERT INTO tblproduct(
		productcode	,
		productname	,
		assembleuse	,
		assembleproduct	,
		sellprice	,
		consumerprice	,
		buyprice	,
		reserve		,
		reservetype	,
		production	,
		madein		,
		model		,
		opendate	,
		selfcode	,
		bisinesscode	,
		quantity	,
		group_check	,
		keyword		,
		addcode		,
		userspec	,
		maximage	,
		minimage	,
		tinyimage	,
		option_price	,
		option_quantity	,
		option1		,
		option2		,
		etctype		,
		deli_price	,
		deli		,
		package_num	,
		display		,
		date		,
		regdate		,
		modifydate	,
		content) VALUES (
		'".$code.$productcode."', 
		'{$productname}', 
		'{$assembleuse}', 
		'', 
		{$sellprice}, 
		{$consumerprice}, 
		{$buyprice}, 
		'{$reserve}', 
		'{$reservetype}', 
		'{$production}', 
		'{$madein}', 
		'{$model}', 
		'{$opendate}', 
		'{$selfcode}', 
		'{$bisinesscode}', 
		{$quantity}, 
		'{$group_check}', 
		'{$keyword}', 
		'{$addcode}', 
		'{$userspec}', 
		'{$image[0]}', 
		'{$image[1]}', 
		'{$image[2]}', 
		'{$option_price}', 
		'{$optcnt}', 
		'{$option1}', 
		'{$option2}', 
		'{$etctype}', 
		'{$deli_price}', 
		'{$deli}', 
		'".(int)$package_num."', 
		'{$display}', 
		'{$curdate}', 
		now(), 
		now(), 
		'{$in_content}')";
		if($insert = pmysql_query($sql,get_db_conn())) {
			if(ord($brandname)) { // 브랜드 관련 처리
				$result = pmysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '{$brandname}' ",get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					@pmysql_query("UPDATE tblproduct SET brand = '{$row->bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());
				} else {
					$sql = "INSERT INTO tblproductbrand(brandname) VALUES ('{$brandname}') RETURNING bridx";
					if($brandinsert = @pmysql_query($sql,get_db_conn())) {
						$row = pmysql_fetch_array($brandinsert);
						$bridx = $row[0];
						if($bridx>0) {
							@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());
						}
					}
				}
				pmysql_free_result($result);
			}

			if($group_check=="Y" && count($group_code)>0) {
				for($i=0; $i<count($group_code); $i++) {
					$sql = "INSERT INTO tblproductgroupcode(productcode,group_code) VALUES (
					'".$code.$productcode."', 
					'{$group_code[$i]}')";
					pmysql_query($sql,get_db_conn());
				}
			}

			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";

			
			$onload="<script>alert(\"상품이 등록이 완료되었습니다.$message\");</script>";
			

			$log_content = "## 상품입력 ## - 코드 $code$productcode - 상품 : $productname 코디/조립 : $assembleuse 패키지그룹 : $package_num 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			
				$onload="<script>alert(\"상품 등록중 오류가 발생하였습니다.\");</script>";
			
		}
		$prcode=$code.$productcode;
	} else if ($mode=="delete") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode = '{$prcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vpridx=$row->pridx;
		$vassembleuse=$row->assembleuse;
		$vassembleproduct=$row->assembleproduct;

		#태그관련 지우기
		$sql = "DELETE FROM tbltagproduct WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#리뷰 지우기
		$sql = "DELETE FROM tblproductreview WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#위시리스트 지우기
		$sql = "DELETE FROM tblwishlist WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		#관련상품 지우기
		$sql = "DELETE FROM tblcollection WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());
		
		$sql = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
		pmysql_query($sql,get_db_conn());

		if($vassembleuse=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '{$prcode}' ";
			$result = pmysql_query($sql,get_db_conn());
			if($row = @pmysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$prcode}' ";
				pmysql_query($sql,get_db_conn());
				
				if(ord(str_replace("","",$row->assemble_pridx))) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					pmysql_query($sql,get_db_conn());
				}
			}
			pmysql_free_result($result);
		} else {
			if(ord($vassembleproduct)) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
				$result = pmysql_query($sql,get_db_conn());
				while($row = @pmysql_fetch_object($result)) {
					$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND display ='Y' ";
					$sql.= "AND assembleuse!='Y' ";
					$result2 = pmysql_query($sql,get_db_conn());
					if($row2 = @pmysql_fetch_object($result2)) {
						$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
						$sql.= "WHERE productcode = '{$row->productcode}' ";
						$sql.= "AND assembleuse='Y' ";
						pmysql_query($sql,get_db_conn());
					}
					pmysql_free_result($result2);
				}
			}

			$sql = "UPDATE tblassembleproduct SET ";
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
			pmysql_query($sql,get_db_conn());
		}

		if($vender>0) {
			//미니샵 테마코드에 등록된 상품 삭제
			setVenderThemeDeleteNor($prcode, $vender);
			setVenderCountUpdateMin($vender, $vdisp);

			$tmpcode_a=substr($prcode,0,3);
			$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$vender}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prcnt=$row->cnt;
			pmysql_free_result($result);

			if($prcnt==0) {
				setVenderDesignDeleteNor($tmpcode_a, $vender);
				$imagename=$Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$tmpcode_a}.gif";
				@unlink($imagename);
			}
		}

		$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("prdelete","",$prcode);

		
		$onload="<script>alert(\"상품 삭제가 완료되었습니다.\");window.close();opener.location.reload()</script>";
		
		$prcode="";
	} else if ($mode=="modify") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,sellprice,assembleproduct FROM tblproduct WHERE productcode = '{$prcode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vassembleuse=$row->assembleuse;
		$vpridx=$row->pridx;
		$vsellprice=$row->sellprice;
		$vassembleproduct=$row->assembleproduct;

		if(strlen($buyprice) < 1 ) $buyprice = 0 ;

		$sql = "UPDATE tblproduct SET ";
		$sql.= "productname		= '{$productname}', ";
		$sql.= "consumerprice	= {$consumerprice}, ";
		$sql.= "buyprice		= {$buyprice}, ";
		$sql.= "reserve			= '{$reserve}', ";
		$sql.= "reservetype		= '{$reservetype}', ";
		$sql.= "production		= '{$production}', ";
		$sql.= "madein			= '{$madein}', ";
		$sql.= "model			= '{$model}', ";
		$sql.= "opendate		= '{$opendate}', ";
		$sql.= "selfcode		= '{$selfcode}', ";
		$sql.= "bisinesscode	= '{$bisinesscode}', ";
		$sql.= "quantity		= {$quantity}, ";
		$sql.= "group_check		= '{$group_check}', ";
		$sql.= "keyword			= '{$keyword}', ";
		$sql.= "addcode			= '{$addcode}', ";
		$sql.= "userspec		= '{$userspec}', ";
		$sql.= "maximage		= '{$image[0]}', ";
		$sql.= "minimage		= '{$image[1]}', ";
		$sql.= "tinyimage		= '{$image[2]}', ";
		
		$sql.= "assembleuse		= '{$assembleuse}', ";
		
		
		if($vassembleuse=="Y") {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "option_price	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= {$sellprice}, ";
				$sql.= "option_price	= '{$option_price}', ";
				$sql.= "option_quantity	= '{$optcnt}', ";
				$sql.= "option1			= '{$option1}', ";
				$sql.= "option2			= '{$option2}', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		} else {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= 0, ";
				$sql.= "option_price	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "sellprice		= {$sellprice}, ";
				$sql.= "option_price	= '{$option_price}', ";
				$sql.= "option_quantity	= '{$optcnt}', ";
				$sql.= "option1			= '{$option1}', ";
				$sql.= "option2			= '{$option2}', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		}

		$sql.= "etctype			= '{$etctype}', ";
		$sql.= "deli_price		= '{$deli_price}', ";
		$sql.= "deli			= '{$deli}', ";
		$sql.= "display			= '{$display}', ";
		if($insertdate!="Y") {
			$sql.= "date			= '{$curdate}', ";
		}
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '{$in_content}' ";
		$sql.= "WHERE productcode = '{$prcode}' ";
		#echo $sql; exit;
		if($update = pmysql_query($sql,get_db_conn())) {
			
			if(ord($brandname)) { // 브랜드 관련 처리
				$result = pmysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '{$brandname}' ",get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					if($brand != $row->bridx) {
						@pmysql_query("UPDATE tblproduct SET brand = '{$row->bridx}' WHERE productcode = '{$prcode}'",get_db_conn());
					}
				} else {
					$sql = "INSERT INTO tblproductbrand(brandname) VALUES ('{$brandname}') RETURNING bridx";
					if($brandinsert = @pmysql_query($sql,get_db_conn())) {
						$row = pmysql_fetch_object($brandinsert);
						$bridx = $row[0];
						if($bridx>0) {
							@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '{$prcode}'",get_db_conn());
						}
					}
				}
				pmysql_free_result($result);
			} else {
				if($brand>0) {
					@pmysql_query("UPDATE tblproduct SET brand = null WHERE productcode = '{$prcode}'",get_db_conn());
				}
			}
			
			$groupdelete = pmysql_query("DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}' ",get_db_conn());
			if($groupdelete) {
				if($group_check=="Y" && count($group_code)>0) {
					for($i=0; $i<count($group_code); $i++) {
						$sql = "INSERT INTO tblproductgroupcode(productcode,group_code) VALUES (
						'{$prcode}', 
						'{$group_code[$i]}')";
						@pmysql_query($sql,get_db_conn());
					}
				}
			}

			if($vassembleuse=="Y") {
				if($assembleuse!="Y") {
					$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
					$sql.= "WHERE productcode = '{$prcode}' ";
					$result = pmysql_query($sql,get_db_conn());
					if($row = @pmysql_fetch_object($result)) {
						$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$prcode}' ";
						pmysql_query($sql,get_db_conn());
						
						if(ord(str_replace("","",$row->assemble_pridx))) {
							$sql = "UPDATE tblproduct SET ";
							$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}','') ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND assembleuse != 'Y' ";
							pmysql_query($sql,get_db_conn());
						}
					}
					pmysql_free_result($result);
				}
			} else {
				if($assembleuse=="Y" || ($assembleuse!="Y" && $vsellprice!=$sellprice)) {
					if(ord($vassembleproduct)) {
						$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
						$result = pmysql_query($sql,get_db_conn());
						while($row = @pmysql_fetch_object($result)) {
							$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND display ='Y' ";
							$sql.= "AND assembleuse!='Y' ";
							$result2 = pmysql_query($sql,get_db_conn());
							if($row2 = @pmysql_fetch_object($result2)) {
								$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
								$sql.= "WHERE productcode = '{$row->productcode}' ";
								$sql.= "AND assembleuse='Y' ";
								pmysql_query($sql,get_db_conn());
							}
							pmysql_free_result($result2);
						}
					}

					if($assembleuse=="Y") {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$vpridx}',''), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',{$vpridx}','') ";
						pmysql_query($sql,get_db_conn());
					}
				}
			}

			if($vender>0) {
				if($vdisp!=$display) {
					setVenderCountUpdateRan($vender, $display);
				}
			}
			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";
			
			if($changecode){
				
				$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
				$result = pmysql_query($sql,get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$changecode}%' ";
					$sql.= "ORDER BY productcode DESC LIMIT 1 ";
					$result = pmysql_query($sql,get_db_conn());
					if ($rows = pmysql_fetch_object($result)) {
						$newproductcode = substr($rows->productcode,12)+1;
						$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
					} else {
						$newproductcode = "000001";
					}
					pmysql_free_result($result);

					$path = $Dir.DataDir."shopimages/product/";
					if (ord($row->maximage)) {
						$ext = strtolower(pathinfo($row->maximage,PATHINFO_EXTENSION));
						$maximage=$changecode.$newproductcode.".".$ext;

						if (file_exists("$path$row->maximage")) {
							
							rename("$path$row->maximage","$path$maximage");
							
						}
					} else $maximage="";
					if (ord($row->minimage)) {
						$ext = strtolower(pathinfo($row->minimage,PATHINFO_EXTENSION));
						$minimage=$changecode.$newproductcode."2.".$ext;
						if (file_exists("$path$row->minimage")) {
							rename("$path$row->minimage","$path$minimage");
							
						}
					} else $minimage="";
					if (ord($row->tinyimage)) {
						$ext = strtolower(pathinfo($row->tinyimage,PATHINFO_EXTENSION));
						$tinyimage=$changecode.$newproductcode."3.".$ext;
						if (file_exists("$path$row->tinyimage")) {
							rename("$path$row->tinyimage","$path$tinyimage");
							
						}
					} else $tinyimage="";
				
					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["IN"][]=$changecode.$newproductcode;
					}

					
					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["OUT"][]=$row->productcode;
					}

					$sql = "UPDATE tblproduct SET productcode = '".$changecode.$newproductcode."' ";
					if($tinyimage)$sql.=", tinyimage='".$tinyimage."'";
					if($minimage)$sql.=", minimage='".$minimage."'";
					if($maximage)$sql.=", maximage='".$maximage."'";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					#태그관련 지우기
					$sql = "UPDATE tbltagproduct SET productcode = '".$changecode.$newproductcode."'";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductgroupcode SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductreview SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproducttheme SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblwishlist SET productcode = '".$changecode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$prcode}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET ";
					$sql.= "collection_list = replace(collection_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialcode SET ";
					$sql.= "special_list = replace(special_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialmain SET ";
					$sql.= "special_list = replace(special_list,'{$prcode}','".$changecode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());


					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "UPDATE tblassembleproduct SET productcode = '".$changecode.$newproductcode."' ";
						$sql.= "WHERE productcode='{$prcode}'";
						pmysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '".$changecode.$newproductcode."' ";
						$result = pmysql_query($sql,get_db_conn());
						if($row = @pmysql_fetch_object($result)) {
							if(ord(str_replace("","",$row->assemble_pridx))) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = REPLACE(assembleproduct,',{$prcode}',',".$changecode.$newproductcode."') ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								pmysql_query($sql,get_db_conn());
							}
						}
						pmysql_free_result($result);
					} else {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$row->pridx}','{$insert_pridx}'), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',{$row->pridx}',',{$insert_pridx}') ";
						pmysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품이동입력 ## - 상품코드 {$prcode} => ".$changecode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
					
					$prcode=$changecode.$newproductcode;
					$code=$changecode;
					$changecode="";
				}
			}
		}

		
		$onload="<script>alert(\"상품이 수정되었습니다.$message\");</script>";
		
		$log_content = "## 상품수정 ## - 코드 $prcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금 : $reserve 날짜고정 : ".(($insertdate=="Y")?"Y":"N")." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}
} else {
	alert_go("상품이미지의 총 용량이 ".ceil($file_size/1024).
	"Kbyte로 500K가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 500K입니다.\\n\\n".
	"이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.",-1);
}

//################# 500K가 넘는 이미지 체크
if ((ord($userfile['name']) && $userfile['size']==0) || (ord($userfile2['name']) && $userfile2['size']==0) || (ord($userfile3['name']) && $userfile3['size']==0)) {
	 $onload="<script>alert(\"상품이미지중 용량이 500K가 넘는 이미지가 있습니다.\\n\\n500K가 넘는 이미지는 등록되지 않습니다..\\n\\n"
		."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");</script>\n";
}
//###############################################

?>

<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function PrdtDelete() {
	if (confirm("해당 상품을 삭제하시겠습니까?")) {
		document.cForm.mode.value="delete";
		document.cForm.submit();
	}
}

function NewPrdtInsert(){
	document.cForm.prcode.value="";
	document.cForm.submit();
}

function IconMy(){
	window.open("","icon","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	document.icon.submit();
}

function IconList(){
	alert("서비스 준비중 입니다.");
	//window.open("","iconlist","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	//document.iconlist.submit();
}

function DeletePrdtImg(temp){
	if(confirm('해당 이미지를 삭제하시겠습니까?')){
		document.cForm.mode.value="delprdtimg";
		document.cForm.delprdtimg.value=temp-1;
		document.cForm.submit();
	}
}

function CheckChoiceIcon(no){
	num = document.form1.iconnum.value;
	iconnum=0;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked) iconnum++;
	}
	if(iconnum>3){
		alert('한 상품에 3개까지 아이콘을 사용할 수 있습니다.');
		document.form1.icon[no].checked=false;
	}
}

function PrdtAutoImgMsg(){
	if(document.form1.imgcheck.checked) alert('상품 중간/작은 이미지가 대 이미지에서 자동 생성됩니다.\n\n기존의 중간/작은 이미지는 삭제됩니다.');
}

var shop="layer0";
var ArrLayer = new Array ("layer0","layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementById(ArrLayer[i]).style.display="";
			else
				document.getElementById(ArrLayer[i]).style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
	parent_resizeIframe('AddFrame');
}

function SelectColor(){
	setcolor = document.form1.setcolor.value;
	var newcolor = showModalDialog("select_color.php?color="+setcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		document.form1.setcolor.value=newcolor;
		document.all.ColorPreview.style.backgroundColor = '#' + newcolor;
	}
}

function optionhelp(){
	alert("서비스 준비중 입니다.");
}

function DateFixAll(obj) {
	if (obj.checked) {
		document.form1.insertdate.value="Y";
		document.form1.insertdate1.checked=true;
		document.form1.insertdate2.checked=true;
		document.form1.insertdate3.checked=true;
	} else {
		document.form1.insertdate.value="";
		document.form1.insertdate1.checked=false;
		document.form1.insertdate2.checked=false;
		document.form1.insertdate3.checked=false;
	}
}

function change_filetype(obj) {
	if(obj.checked) {	//이미지 링크 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='none';
			document.form1["userfile"+idx+"_url"].style.display='';
			document.form1["userfile"+idx].disabled=true;
			document.form1["userfile"+idx+"_url"].disabled=false;
		}
	} else {				//첨부파일 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='';
			document.form1["userfile"+idx+"_url"].style.display='none';
			document.form1["userfile"+idx].disabled=false;
			document.form1["userfile"+idx+"_url"].disabled=true;
		}
	}
}

function userspec_change(val) {
	if(document.getElementById("userspecidx")) {
		if(val == "Y") {
			document.getElementById("userspecidx").style.display ="";
		} else {
			document.getElementById("userspecidx").style.display ="none";
		}
	}
}

function GroupCode_Change(val) {
	if(document.getElementById("group_checkidx")) {
		if(val == "Y") {
			document.getElementById("group_checkidx").style.display ="";
		} else {
			document.getElementById("group_checkidx").style.display ="none";
		}
	}
}

function GroupCodeAll(checkval,checkcount) {
	for(var i=0; i<checkcount; i++) {
		if(document.getElementById("group_code_idx"+i)) {
			document.getElementById("group_code_idx"+i).checked = checkval;
		}
	}
}

/*################################### 태그관련 시작 #####################################*/
var IE = false ;
if (window.navigator.appName.indexOf("Explorer") !=-1) {
	IE = true;
}

function getXmlHttpRequest() {
	var xmlhttp = false
	if(window.XMLHttpRequest){//Mozila
		xmlhttp = new XMLHttpRequest()
	}else {//IE
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP")
	}
	return xmlhttp;
}
function loadData(path, successFunc, msg){ 
	var xmlhttp = getXmlHttpRequest();
	xmlhttp.open("GET",path,true);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				var data = xmlhttp.responseText;
				successFunc(data);
			}else{
				alert(msg);
			}
		}
	}
	xmlhttp.send(null);
	return false;
}


function loadProductTagList (prcode) {
	loadData("product_taglist.xml.php?prcode="+prcode, setProductTagList);
}
function setProductTagList(data) {
  	try {
  		var tagElem = document.getElementById("ProductTagList");
  		if(data=='') {
  			data = "일시적으로 태그 정보를 불러올 수 없습니다.\n\n태그 관리 기능은 잠시 후에 이용해 주십시오. \n\n상품수정은  정상적으로 수정하실  수 있습니다.";
  		}	
  		tagElem.innerHTML = data;
		tagElem.style.height = "68";
		tagElem.style.overflowY = "auto";  		
  	}catch(e) {}
}

function delTagName(prcode,tagname) {
<?php if(_DEMOSHOP=="OK" && $_SERVER['REMOTE_ADDR']!=_ALLOWIP) { ?>
	alert("데모버전에서는 삭제가 불가능 합니다.");
<?php } else { ?>
	if(confirm("\""+tagname+"\" 태그를 삭제하시겠습니까?")) {
		loadData("product_taglist.xml.php?type=del&prcode="+prcode+"&tagname="+tagname, setProductTagList);
	}
<?php } ?>
}

function BrandSelect() {
	window.open("product_brandselect.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}

function FiledSelect(pagetype) {
	window.open("product_select.php?type="+pagetype,pagetype,"height=400,width=420,scrollbars=no,resizable=no");
}

/*################################### 태그관련 끝   #####################################*/


function deli_helpshow() {
	if(document.getElementById('deli_helpshow_idx')) {
		if(document.getElementById('deli_helpshow_idx').style.display=="none") {
			document.getElementById('deli_helpshow_idx').style.display="";
		} else {
			document.getElementById('deli_helpshow_idx').style.display="none";
		}
	}
}

function chkFieldMaxLenFunc(thisForm,reserveType) {
	if (reserveType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }
	if (thisForm.reserve.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.reserve.value = thisForm.reserve.value.cut(max);
		thisForm.reserve.focus();
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

<?php if($_data->vender<=0) { ?>
function assembleuse_change() {
	if(document.form1.assembleuse[0].checked) {
		document.form1.sellprice.disabled=false;
		document.form1.sellprice.style.backgroundColor = "#FFFFFF";
		if(document.form1.searchtype) {
			if(document.form1.searchtype.length && document.form1.searchtype.length>0) {
				for(var i=0; i<document.form1.searchtype.length; i++) {
					document.form1.searchtype[i].disabled=false;
					if(document.form1.searchtype[i].checked) {
						ViewLayer("layer"+i);
					}
				}
			} else {
				document.form1.searchtype.disabled=false;
				if(document.form1.searchtype.checked) {
					ViewLayer("layer");
				}
			}
			if(document.getElementById("assemblealertidx")) {
				document.getElementById("assemblealertidx").style.display="none";
			}
		}

		if(document.getElementById("packagealertidx")) {
			document.getElementById("packagealertidx").style.display="none";
		}
		if(document.getElementById("packageselectidx")) {
			document.getElementById("packageselectidx").style.display="";
		}
		document.form1.package_num.disabled=false;
	} else {
		document.form1.sellprice.disabled=true;
		document.form1.sellprice.style.backgroundColor = "#C0C0C0";
		if(document.form1.searchtype) {
			ViewLayer("layer0");
			if(document.form1.searchtype.length && document.form1.searchtype.length>0) {
				for(var i=0; i<document.form1.searchtype.length; i++) {
					document.form1.searchtype[i].disabled=true;
				}
			} else {
				document.form1.searchtype.disabled=true;
			}
			if(document.getElementById("assemblealertidx")) {
				document.getElementById("assemblealertidx").style.display="";
			}
		}
		if(document.getElementById("packagealertidx")) {
			document.getElementById("packagealertidx").style.display="";
		}
		if(document.getElementById("packageselectidx")) {
			document.getElementById("packageselectidx").style.display="none";
		}
		document.form1.package_num.disabled=true;
	}
	parent_resizeIframe('AddFrame');
	alert("한번 등록된 상품 판매가격 타입은 변경이 불가능하므로 신중히 선택해 주세요.");
}

<?php } ?>

function catenmchange(prcode){
	
	var code_a=document.form1.code_a.value;
	var code_b=document.form1.code_b.value;
	var code_c=document.form1.code_c.value;
	var code_d=document.form1.code_d.value;
	if(!code_a) code_a="000";
	if(!code_b) code_b="000";
	if(!code_c) code_c="000";
	if(!code_d) code_d="000";
	sumcode=code_a+code_b+code_c+code_d;
	$.ajax({ 
		type: "POST", 
		url: "product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
	if(msg=='nocate'){
		alert("상품카테고리 선택이 잘못되었습니다.");
//		$("#catenm").html(msg);
		
	}else if(msg=='nolowcate'){
		alert("하위카테고리가 존재합니다.");
	//	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
	}else{
		
		var code=document.getElementById("code").value;
		if(code){
			
			if(sumcode!=code){
				document.form1.changecode.value=sumcode;
			}else{
				document.form1.changecode.value="";
			}
		}else{
			document.form1.code.value=sumcode;
		}
			
		document.form1.submit();	
	}
	});
	
	
}

function disabledon(){
	document.getElementById("code_a").disabled=false;
	document.getElementById("code_b").disabled=false;
	document.getElementById("code_c").disabled=false;
	document.getElementById("code_d").disabled=false;
}


function isNumberKey(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode < 48 || charCode > 57){
		return false;	
	}
	// Textbox value        
	
	return true;
}

function isNumberKey2(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;	
	}
	// Textbox value        

	var _value = event.srcElement.value;        

	// 소수점(.)이 두번 이상 나오지 못하게
	var _pattern0 = /^\d*[.]\d*$/; // 현재 value값에 소수점(.) 이 있으면 . 입력불가
	if (_pattern0.test(_value)) {
		if (charCode == 46) {
		return false;
		}
	}

	// 소수점 둘째자리까지만 입력가능
	var _pattern2 = /^\d*[.]\d{2}$/; // 현재 value값이 소수점 둘째짜리 숫자이면 더이상 입력 불가
		if (_pattern2.test(_value)) {
		alert("소수점 둘째자리까지만 입력가능합니다.");
		return false;
	}
	
	return true;
}

//-->
</SCRIPT>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
<td valign="top">
<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
<tr>
<td>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">

<tr>
	
	
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	
	
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
	<input type=hidden name=mode>
	<input type=hidden name=code id=code value="<?=$code?>">
	<input type=hidden name=changecode value="<?=$changecode?>">
	<input type=hidden name=prcode value="<?=$prcode?>">
	<input type=hidden name=htmlmode value='wysiwyg'>
	<input type=hidden name=delprdtimg>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	<input type=hidden name=option_price>
	<input type=hidden name=insertdate>
	<input type=hidden name=popup value="<?=$popup?>">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3" style="margin-top:-5px;">기본정보 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>필수표시 항목</span></div>
				</td>
			</tr>
			
			
			<tr><td height=3></td></tr>
				
			<tr>
				<td>
				<?
					if(!$code && !$changecode){
						 $classname="class=\"graybg_wrap\"";
						 $graybgdisplay="style=\"display:block\"";
					}else{
						$graybgdisplay="style=\"display:none\"";
					}
					
				?>
				<div <?=$classname?>>
				<div class="graybg" <?=$graybgdisplay?>><div class="ment01">상단의 카테고리를 선택하면 작성하실 수 있습니다.</div></div>

				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<th style="width:140px;"><span>카테고리 선택</span></th>
					<td colspan="3">
		<?php
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
						$i=0;
						$ii=0;
						$iii=0;
						$iiii=0;
						$strcodelist = "";
						$strcodelist.= "<script>\n";
						$result = pmysql_query($sql,get_db_conn());
						$selcode_name="";

						while($row=pmysql_fetch_object($result)) {
							$strcodelist.= "var clist=new CodeList();\n";
							$strcodelist.= "clist.code_a='{$row->code_a}';\n";
							$strcodelist.= "clist.code_b='{$row->code_b}';\n";
							$strcodelist.= "clist.code_c='{$row->code_c}';\n";
							$strcodelist.= "clist.code_d='{$row->code_d}';\n";
							$strcodelist.= "clist.type='{$row->type}';\n";
							$strcodelist.= "clist.code_name='{$row->code_name}';\n";
							if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
								$strcodelist.= "lista[{$i}]=clist;\n";
								$i++;
							}
							if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
								if ($row->code_c=="000" && $row->code_d=="000") {
									$strcodelist.= "listb[{$ii}]=clist;\n";
									$ii++;
								} else if ($row->code_d=="000") {
									$strcodelist.= "listc[{$iii}]=clist;\n";
									$iii++;
								} else if ($row->code_d!="000") {
									$strcodelist.= "listd[{$iiii}]=clist;\n";
									$iiii++;
								}
							}
							$strcodelist.= "clist=null;\n\n";
						}
						pmysql_free_result($result);
						$strcodelist.= "CodeInit();\n";
						$strcodelist.= "</script>\n";

						echo $strcodelist;
						
						if($code && !$prcode){
							$disabled="disabled";
						}
							
						

						echo "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<select name=code_d id=code_d style=\"width:150px; height:150px\" {$disabled} Multiple>\n";
						echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
						echo "</select>\n";

						echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
						
						
						echo "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" onclick=\"javascript:catenmchange('{$prcode}')\"></span>";
//						echo "<span style=\"display:\" name=\"cateonbutton\"><input type=\"button\" value=\"수정\" onclick=\"javascript:disabledon()\"></span>";
		?>
					</td>
				</table>
				</div>
				</div><!-- 그레이배경 div -->
				</td>
			</tr>
			</table>
				
		</td>
	</tr>
	<tr>
		<td>
			
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center">
		<?php
		
			if ($code != substr($prcode,0,12)) $prcode = "";

		//	if (strlen($code)==12) {
				if (ord($prcode)) {
					$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}' ";
					$result = pmysql_query($sql,get_db_conn());
					if ($_data = pmysql_fetch_object($result)) {
						$productname = $_data->productname;
						if(ord($_data->option_quantity)) $searchtype=1;
						else if(preg_match("/^\[OPTG\d{4}\]$/",$_data->option1)) $searchtype=3;
						
						$specname=array();
						$specvalue=array();
						$specarray=array();
						if(ord($_data->userspec)) {
							$userspec = "Y";
							$specarray= explode("=",$_data->userspec);
							for($i=0; $i<$userspec_cnt; $i++) {
								$specarray_exp = explode("", $specarray[$i]);
								$specname[] = $specarray_exp[0];
								$specvalue[] = $specarray_exp[1];
							}

						} else {
							$userspec = "N";
						}

						// 특수옵션값을 체크한다.
						$dicker = $dicker_text="";
						if (ord($_data->etctype)) {
							$etctemp = explode("",$_data->etctype); 
							$miniq = 1;          // 최소주문수량 기본값 넣는다.
							$maxq = "";
							for ($i=0;$i<count($etctemp);$i++) {
								if ($etctemp[$i]=="BANKONLY")                    $bankonly="Y";        // 현금전용
								else if (substr($etctemp[$i],0,11)=="DELIINFONO=")     $deliinfono=substr($etctemp[$i],11);  // 배송/교환/환불정보 노출안함 정보
								else if ($etctemp[$i]=="SETQUOTA")               $setquota="Y";        // 무이자상품
								else if (substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);  // 최소주문수량
								else if (substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);  // 최대주문수량
								else if (substr($etctemp[$i],0,5)=="ICON=")      $iconvalue=substr($etctemp[$i],5);  // 최대주문수량
								else if (substr($etctemp[$i],0,9)=="FREEDELI=")  $freedeli=substr($etctemp[$i],9);  // 무료배송상품
								else if (substr($etctemp[$i],0,7)=="DICKER=") {  $dicker=Y; $dicker_text=str_replace("DICKER=","",$etctemp[$i]); }  // 가격대체문구

								/*switch ($etctemp[$i]) {
									case "BANKONLY": $bankonly = "Y";break;
									case "SETQUOTA": $setquota = "Y";break;
								}*/
							}
						}
						if(ord($iconvalue)) {
							for($i=0;$i<strlen($iconvalue);$i=$i+2) {
								$iconvalue2[substr($iconvalue,$i,2)]="Y";
								//echo "<br>>>>>".substr($iconvalue,$i,2);
							}
						}
						if($_data->brand>0) {
							$sql = "SELECT brandname FROM tblproductbrand WHERE bridx = '{$_data->brand}' ";
							$result = pmysql_query($sql,get_db_conn());
							$_data2 = pmysql_fetch_object($result);
							$_data->brandname = $_data2->brandname;
							pmysql_free_result($result);
						}
						
						if($_data->group_check=="Y") {
							$sql = "SELECT group_code FROM tblproductgroupcode WHERE productcode = '{$prcode}' ";
							$result = pmysql_query($sql,get_db_conn());
							while($row = pmysql_fetch_object($result)) {
								$group_code[$row->group_code] = "Y";
							}
							pmysql_free_result($result);
						}
					} else {
//						alert_go('해당 상품이 존재하지 않습니다.');
						echo "<script>alert('해당 상품이 존재하지 않습니다.');window.close();</script>";
					}
				}

				if(preg_match("/^\[OPTG\d{4}\]$/",$_data->option1)){
					$optcode = substr($_data->option1,5,4);
					$_data->option1="";
					$_data->option_price="";
				}
		?>
			
				<table cellpadding="0" cellspacing="0" width="100%">
				
				
				<tr>
					<td>
		            <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					
					<?php if($_data->vender>0){?>
					<tr>
						<th><span>등록업체</span></th>
						<td class="" colspan="3">
						<?php
						$sql = "SELECT vender,id,brand_name FROM tblvenderstore WHERE vender='{$_data->vender}' ";
						$result=pmysql_query($sql,get_db_conn());
						if($row=pmysql_fetch_object($result)) {
							echo "<A HREF=\"javascript:viewVenderInfo({$row->vender})\"><B>{$row->brand_name} ({$row->id})</B></A>";
						}
						pmysql_free_result($result);
						?>
						</td>
					</tr>
					<?php }?>
					<TR>
						<th style="width:140px;"><span>현재 카테고리</span></th>
						<TD class="" colspan="3" style="word-break:break-all;">
		<?php
						$code_loc = getCodeLoc($code);
						if (ord($prcode)) {
							echo $code_loc." > <B><span class=\"font_orange\" id=\"catenm\">{$productname}</B></span>";
						} else if(!$code) {
							echo "<B><span id=\"catenm\" class=\"font_orange\">카테고리를 선택하여 주십시요.</span></B>";
						} else {
							echo $code_loc." > <B><span class=\"font_orange\" id=\"catenm\">".($gongtype=="Y"?"공동구매 신규입력":"신규입력")."</B></span>";
						}
						/*
						if(!$prcode) {
							echo "<B><span class=\"font_orange\" id=\"catenm\">카테고리를 선택하여 주십시요.</B></span>";
						} else {
							echo "<B><span class=\"font_orange\" id=\"catenm\">".$code_loc."</B></span>";
						}*/
		?>
						</TD>
					</TR>
					<?if($changecode){?>
					<TR>
						<th><span>변경될 카테고리</span></th>
						<TD class="" colspan="3" style="word-break:break-all;">
		<?php
						$code_loc = getCodeLoc($changecode);
						echo "<B><span class=\"font_orange\" id=\"catenm\">$code_loc</span></B>";
						
		?>
						</TD>
					</TR>
					<?}?>
					<TR>
						<th><span>상품명</span></th>
						<TD class="" colspan="3"><input name=productname value="<?=str_replace("\"","&quot",$_data->productname)?>" size=80 maxlength=250 onKeyDown="chkFieldMaxLen(250)" class="input" style="width:100%"></TD>
					</TR>
					<TR>
						<th><span>등록/수정일</span></th>
						<TD class="" colspan="3">
		<?php
					if (ord($prcode)==0) {
						echo "자동입력";
					} else { 
						if ($_data) {
							echo " ".str_replace("-","/",substr($_data->modifydate,0,16))."\n";
							echo "(상품코드 : <span class=\"font_orange\">{$_data->productcode}</span>)";
							echo "&nbsp;&nbsp;&nbsp;<a href=\"http://{$shopurl}/front/productdetail.php?productcode={$_data->productcode}\" target=_blank><img src=\"images/productregister_goproduct.gif\" align=absmiddle border=0></font></a>";
						}
						echo "<input type=hidden name=productcode value=\"{$_data->productcode}\">\n";
					}
		?>
						</TD>
					</TR>
					<TR>
						<th><span>상품 등록날짜</span></th>
						<TD class="" colspan="3"><input type=checkbox id="idx_insertdate10" name=insertdate1 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate10>등록 일자 고정</label>&nbsp;<span class="font_orange">(* 상품수정시 등록날짜가 변경되지 않습니다.)</span></TD>
					</TR>
					<?php if($_data->vender>0){ ?>
					<input type=hidden name="assembleuse" value="N">
					<TR>
						<th><span class="font_orange"><b><?=($gongtype=="Y"?"공구가":"판매가격")?></b></span></th>
						<TD class=""><input name=sellprice onkeypress="return isNumberKey(event)" value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98%></TD>
						<td><span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"시중가격")?></b></span></TD>
						<TD class=""><input name=consumerprice onkeypress="return isNumberKey(event)" value="<?=(int)(ord($_data->consumerprice)?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%><br><span class="font_orange">* <strike>5,000</strike>로 표기됨, 0 입력시 표기안됨&nbsp;</span></TD>
					</tr>
					<?php } else { ?>
					
					<TR>
						<?	if(ord($prcode)==0) { ?>
						<th>
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td width="100%"><input type="radio" name="assembleuse" value="N" <?=($_data->assembleuse=="Y"?"":"checked")?> id="idx_assembleuseY" style="border:none" onClick="assembleuse_change();"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_assembleuseY"><span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"단일 판매가격")?></b></span></label></td>
						</tr>
						<tr>
							<td width="100%"><input type="radio" name="assembleuse" value="Y" <?=($_data->assembleuse=="Y"?"checked":"")?> id="idx_assembleuseN" style="border:none" onClick="assembleuse_change();"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_assembleuseN"><span class="font_orange"><b><?=($gongtype=="Y"?"코디/조립 판매가":"코디/조립 판매가")?></b></span></label></td>
						</tr>
						<tr>
							<td width="100%">&nbsp;&nbsp;&nbsp;<span class="font_orange" style="font-size:8pt;">* 한번 등록후 변경불가</span></td>
						</tr>
						</table>
		                </div>
		                
		                </th>
						<?php } else { ?>
						<th>
							
								<div class="table_none">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<input type=hidden name="assembleuse" value="<?=$_data->assembleuse?>">
								<tr>
									<td width="100%" height="30">
									<?	if($_data->assembleuse=="Y") { ?>
										<span class="font_orange"><b><?=($gongtype=="Y"?"코디/조립 판매가":"코디/조립 판매가")?></b></span>
									<?php } else { ?>
										<span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"단일 판매가격")?></b></span>
									<?php } ?>
									</td>
								</tr>
								</table>
								</div>
							
		                </th>
						<?php } ?>
						<TD><input name=sellprice onkeypress="return isNumberKey(event)" value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98% <?=($_data->assembleuse=="Y"?"disabled style='background:#C0C0C0'":"")?>><br><span class="font_orange" style="font-size:8pt;">* 코디/조립 사용시 판매가격 등록불가<br><b>&nbsp;&nbsp;</b>및 상품옵션, 패키지그룹 사용불가</span></TD>
						<th>
							<span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"시중가격")?></b></span>
						</th>
						<TD class="">
							<input name=consumerprice onkeypress="return isNumberKey(event)" value="<?=(int)(ord($_data->consumerprice)?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%><br><span class="font_orange">* <strike>5,000</strike>로 표기됨, 0 입력시 표기안됨&nbsp;</span><br><br>
						</TD>
					</tr>
					<?php } ?>
					<tr>
						<th><span>적립금(률)</span></th>
						<TD class=""><input name=reserve onkeypress="return isNumberKey2(event)" value="<?=$_data->reserve?>" size=16 maxlength=6 class="input" style="width:60%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.reservetype.value);"> <select name="reservetype" class="select" onChange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N"<?=($_data->reservetype!="Y"?" selected":"")?>>적립금(￦)</option><option value="Y"<?=($_data->reservetype!="Y"?"":" selected")?>>적립률(%)</option></select><br><span class="font_orange" style="font-size:8pt;letter-spacing:-0.5pt">* 적립률은 소수점 둘째자리까지 입력 가능합니다.<br>* 적립률에 대한 적립 금액 소수점 자리는 반올림.</span>
						</TD>
						<th><span>구입원가</span></th>
						<TD class=""><input name=buyprice onkeypress="return isNumberKey(event)" value="<?=$_data->buyprice?>" size=16 maxlength=10 class="input" style="width:100%"></TD>
					</tr>
					<tr>
						<th><span>제조사</span></th>
						<TD class=""><input name=production value="<?=$_data->production?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:FiledSelect('PR');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
						<th><span>원산지</span></th>
						<TD class=""><input name=madein value="<?=$_data->madein?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(30)" class="input"><a href="javascript:FiledSelect('MA');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
					</tr>
					<tr>
						<th><span>브랜드</span></th>
						<TD class=""><input type=text name=brandname value="<?=$_data->brandname?>" size=23 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:BrandSelect();"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a><br>
						<span class="font_orange">* 브랜드를 직접 입력시에도 등록됩니다.</span></TD>
						<th><span>모델명</span></th>
						<TD class=""><input name=model value="<?=$_data->model?>" size=23 maxlength=40 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:FiledSelect('MO');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
					</tr>
					<tr>
						<th><span>진열코드</span></th>
						<TD class="" colspan="3"><input name=selfcode value="<?=$_data->selfcode?>" size=35 maxlength=20 onKeyDown="chkFieldMaxLen(20)" class="input" style="width:100%"></td>
					</tr>
					<tr>
						<TD class="td_con_orange" colspan="4" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid; border-left:1px solid #b9b9b9;">
						<span class="font_orange">* 쇼핑몰에서 자동으로 발급되는 상품코드와는 별개로 운영상 필요한 자체상품코드를 입력해 주세요.<br>
						* 진열코드 관련 설정은 <a href="javascript:opener.parent.topframe.GoMenu(1,'shop_productshow.php');"><span class="font_blue">상점관리 > 쇼핑몰 환경 설정 > 상품 진열 기타 설정</a></span> 변경할 수 있습니다.
						</span></TD>
					</tr>
					<tr>
						<th><span>출시일</span></th>
						<TD class="" colspan="3"><input name=opendate value="<?=$_data->opendate?>" size=20 maxlength=8 class="input">&nbsp;&nbsp;예) <?=DATE("Ymd")?>(출시년월일)<br>
						<span class="font_orange">* 가격비교 페이지 등 제휴업체 관련 노출시 사용됩니다.<br>* 잘못된 출시일 지정으로 인한 문제는 상점에서 책임지셔야 됩니다.</span></TD>
					</tr>
					<tr>
						<th><span>수량</span></th>
						<TD class="" colspan="3">
		<?php
					if ($gongtype=="Y") {
						if ($_data) {
							$quantity=$_data->quantity;
							if($_data->quantity<=0) $checkquantity="E";
							else $checkquantity="C";
						}else {
							$checkquantity="C";
						}

						$arrayname= array("마감","수량");
						$arrayprice=array("E","C");
						$arraydisable=array("true","false");
						$arraybg=array("silver","white");
						$arrayquantity=array("","$quantity");
						
						for($i=0;$i<2;$i++){
							echo "<input type=radio id=\"idx_checkquantity{$i}\" name=checkquantity value=\"{$arrayprice[$i]}\" ";
							if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled={$arraydisable[$i]};document.form1.quantity.style.background='{$arraybg[$i]}';document.form1.quantity.value='{$arrayquantity[$i]}';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity{$i}>{$arrayname[$i]}</label>&nbsp;";
						}
						echo ": <input type=text onkeypress=\"return isNumberKey(event)\" name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개";
						
					} else {
						if ($_data) {
							$quantity=$_data->quantity;
							if($_data->quantity==NULL) $checkquantity="F";
							else if($_data->quantity<=0) $checkquantity="E";
							else $checkquantity="C";
							if($quantity<0) $quantity="";
						} else {
							$checkquantity="F";
						}

						$arrayname= array("품절","무제한","수량");
						$arrayprice=array("E","F","C");
						$arraydisable=array("true","true","false");
						$arraybg=array("silver","silver","white");
						$arrayquantity=array("","","$quantity");
						$cnt = count($arrayprice);
					
						for($i=0;$i<$cnt;$i++){
							echo "<input type=radio id=\"idx_checkquantity{$i}\" name=checkquantity value=\"{$arrayprice[$i]}\" "; 
							if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled={$arraydisable[$i]};document.form1.quantity.style.background='{$arraybg[$i]}';document.form1.quantity.value='{$arrayquantity[$i]}';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity{$i}>{$arrayname[$i]}</label>&nbsp;&nbsp;";
						}
						echo ": <input type=text onkeypress=\"return isNumberKey(event)\" name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개";
						
						
					}
					if($checkquantity=="C"){
						echo "<script>document.form1.quantity.disabled=false;document.form1.quantity.style.background='white';</script>\n";
					}else{
						echo "<script>document.form1.quantity.disabled=true;document.form1.quantity.style.background='silver';document.form1.checkquantity.value='';</script>\n";
					}
		?>
						</TD>
					</tr>
					<tr>
						<th><span>최소구매수량</span></th>
						<TD class=""><input type=text onkeypress="return isNumberKey(event)" name=miniq value="<?=($miniq>0?$miniq:"1")?>" size=5 maxlength=5 class="input"> 개 이상</TD>
						<th><span>최대구매수량</span></th>
						<TD class=""><input type=radio id="idx_checkmaxq1" name=checkmaxq value="A" <?php if (ord($maxq)==0 || $maxq=="?") echo "checked ";?> onClick="document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkmaxq1>무제한</label><br><input type=radio id="idx_checkmaxq2" name=checkmaxq value="B" <?php if ($maxq!="?" && $maxq>0) echo "checked"; ?> onClick="document.form1.maxq.disabled=false;document.form1.maxq.style.background='white';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkmaxq2>수량</label> : <input name=maxq onkeypress="return isNumberKey(event)" size=5 maxlength=5 value="<?=$maxq?>" class="input"> 개 이하
						<script>
						if (document.form1.checkmaxq[0].checked) { document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver'; }
						else if (document.form1.checkmaxq[1].checked) { document.form1.maxq.disabled=false;document.form1.maxq.style.background='white'; }
						</script>
						</TD>
					</tr>
					<tr>
						<th><span>개별배송비</span></th>
						<td class="" colspan="3">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td><input type=radio id="idx_deliprtype0" name=deli value="H" <?php if($_data->deli_price<=0 && $_data->deli=="N") echo "checked";?> onClick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliprtype0>기본 배송비 <b>유지</b></label>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio id="idx_deliprtype2" name=deli value="F" <?php if($_data->deli_price<=0 && $_data->deli=="F") echo "checked";?> onClick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliprtype2>개별 배송비 <b><font color="#0000FF">무료</font></b></label>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio id="idx_deliprtype1" name=deli value="G" <?php if($_data->deli_price<=0 && $_data->deli=="G") echo "checked";?> onClick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliprtype1>개별 배송비 <b><font color="#38A422">착불</font></b></label>
							</td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td><input type=radio id="idx_deliprtype3" name=deli value="N" <?php if($_data->deli_price>0 && $_data->deli=="N") echo "checked";?> onClick="document.form1.deli_price_value1.disabled=false;document.form1.deli_price_value1.style.background='';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliprtype3>개별 배송비 <b><font color="#FF0000">유료</font></b> <input type=text onkeypress="return isNumberKey(event)" name=deli_price_value1 value="<?php if($_data->deli_price>0 && $_data->deli=="N") echo $_data->deli_price;?>" size=6 maxlength=6 <?php if($_data->deli_price<=0 || $_data->deli=="Y") echo "disabled style='background:silver'";?> class="input">원</label>&nbsp;<a href="javascript:deli_helpshow();"><img src="images/product_optionhelp3.gif" border="0" align="absmiddle"></a>
								<br>
								<input type=radio id="idx_deliprtype4" name=deli value="Y" <?php if($_data->deli_price>0 && $_data->deli=="Y") echo "checked";?> onClick="document.form1.deli_price_value2.disabled=false;document.form1.deli_price_value2.style.background='';document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliprtype4>개별 배송비 <b><font color="#FF0000">유료</font></b> <input type=text onkeypress="return isNumberKey(event)" name=deli_price_value2 value="<?php if($_data->deli_price>0 && $_data->deli=="Y") echo $_data->deli_price;?>" size=6 maxlength=6 <?php if($_data->deli_price<=0 || $_data->deli=="N") echo "disabled style='background:silver'";?> class="input">원 (구매수 대비 개별 배송비 증가 : <FONT COLOR="#FF0000"><B>상품구매수×개별 배송비</B></font>)</label>&nbsp;<a href="javascript:deli_helpshow();"><img src="images/product_optionhelp3.gif" border="0" align="absmiddle"></a>
							</td>
						</tr>
						<tr id="deli_helpshow_idx" style="display:none;">
		                <td style="padding-top:3pt; padding-bottom:3pt;">
		                    
		                    <!-- 도움말 -->
		                    <div class="help_info01_wrap">
		                        <ul>
		                            <li><span class=font_blue>&nbsp;&nbsp;&nbsp;&nbsp;<b>'개별배송비' 입력 후 '배송비 타입 상품수 대비 배송비 증가' <font color='#0000FF'>체크</font> 예)</b></li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;구매가격&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 10,000원 × 2개구매 = 상품가격 20,000원</li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;개별배송비&nbsp;&nbsp;: 3,000원 일때 × 2개구매= 총배송비 6,000원</li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;총 결제금액 : 26,000원<br></li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;<b>'개별배송비' 입력 후 '배송비 타입 상품수 대비 배송비 증가' <font color='#FF0000'>미체크</font> 예)</b></li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;구매가격&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 10,000원 × 2개구매 = 상품가격 20,000원</li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;개별배송비&nbsp;&nbsp;: 3,000원(구매수가 2개라도 3,000원 한번만 적용)</li>
		                            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;총 결제금액 : 23,000원</li>
		                        </ul>
		                    </div>
		                    
		            		</td>                   	              
						</tr>
						</table>
		                </div>
						</td>
					</tr>
					<tr>
						<th><span>상품노출등급</span></th>
						<td class="" colspan="3">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td><input type=radio id="idx_group_check1" name="group_check" value="N" onClick="GroupCode_Change('N');" <?php if($_data->group_check!="Y") echo "checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_group_check1">상품노출등급 미지정</label>&nbsp;&nbsp;<span class="font_orange">* 상품노출등급 미지정할 경우 모든 비회원, 회원에게 노출됩니다.</span><br><input type=radio id="idx_group_check2" name="group_check" value="Y" onClick="GroupCode_Change('Y');" <?php if($_data->group_check=="Y") echo "checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for="idx_group_check2">상품노출등급 지정</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원등급은 <a href="javascript:opener.parent.topframe.GoMenu(3,'member_groupnew.php');"><span class="font_blue">회원관리 > 회원등급 설정 > 회원등급 등록/수정/삭제</span></a>에서 관리하세요.</span></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr id="group_checkidx" <?php if($_data->group_check!="Y") echo "style=\"display:none;\"";?>>
							<td>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td bgcolor="#FFF7F0" style="border:2px #FF7100 solid;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
		<?php
								$sqlgrp = "SELECT group_code,group_name FROM tblmembergroup ";
								$resultgrp = pmysql_query($sqlgrp,get_db_conn());
								$grpcnt=0;
								while($rowgrp = pmysql_fetch_object($resultgrp)){
									if($grpcnt!=0 && $grpcnt%4==0) {
										echo "</tr>\n<tr>\n";
									}
									echo "<td width=\"25%\" style=\"padding:3px;\"><input type=checkbox id=\"group_code_idx{$grpcnt}\" name=\"group_code[]\" value=\"{$rowgrp->group_code}\" ".(ord($group_code[$rowgrp->group_code])?"checked":"")."> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=\"group_code_idx{$grpcnt}\">{$rowgrp->group_name}</label></td>\n";
									$grpcnt++;
								}
								pmysql_free_result($resultgrp);

								if($grpcnt==0) {
									echo "<td style=\"padding:3px;\">* 회원등급이 존재하지 않습니다.<br>* 회원등급은 <a href=\"javascript:opener.parent.topframe.GoMenu(3,'member_groupnew.php');\"><span class=\"font_blue\">상품관리 > 카테고리/상품관리 > 상품 거래처 관리</span></a>에서 등록하세요.</span></td>\n";
								}
		?>
								</tr>
								</table>
								</td>
							</tr>
		<?php
							if($grpcnt!=0) {
								echo "<tr><td align=\"right\"><input type=checkbox id=\"group_codeall_idx\" onclick=\"GroupCodeAll(this.checked,$grpcnt);\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=\"group_codeall_idx\">일괄선택/해제</label></td></tr>\n";
							}
		?>
							</table>
							</td>
						</tr>
						</table>
		                </div>
						</td>
					</tr>
					<tr>
						<th><span>사용자 정의 스펙</span></th>
						<TD class="" colspan="3" style="padding:5px;">
		                <div class="table_none">
		                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width="180"></col>
						<col width=""></col>
						<tr>
							<td colspan="2"><input type=radio id="idx_userspec1" name=userspec onClick="userspec_change('N');" value="N" <?php if($userspec!="Y") echo "checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_userspec1>사용자 정의 스펙 사용안함</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<input type=radio id="idx_userspec0" name=userspec onClick="userspec_change('Y');" value="Y" <?php if($userspec=="Y") echo "checked";?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_userspec0>사용자 정의 스펙 사용함</label></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr id="userspecidx" <?=($userspec=="Y"?"":"style='display:none;'")?>>
							<td valign="top" bgcolor="#FFF7F0" style="border:2px #FF7100 solid;border-right:1px #FF7100 solide;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td height="7"></td>
							</tr>
							<tr>
								<td align="center" height="30"><b>스<img width="20" height="0">펙<img width="20" height="0">명</b></td>
							</tr>
							<tr>
								<td height="3"></td>
							</tr>
							<tr>
								<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
							</tr>
							<tr>
								<td height="5"></td>
							</tr>
							<tr>
								<td>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width="20"></col>
								<col width=""></col>
								<?for($i=0; $i<$userspec_cnt; $i++) {?>
								<tr>
									<td style="padding:5px;padding-bottom:0px;padding-left:7px;padding-right:2px;" align="center"><?=str_pad(($i+1), 2, "0", STR_PAD_LEFT);?>.</td>
									<td style="padding:5px;padding-bottom:0px;padding-left:0px;"><input name=specname[] value="<?=htmlspecialchars($specname[$i])?>" size=30 maxlength=30 class="input" style="width:100%;"></td>
								</tr>
								<?php }?>
								</table>
								</td>
							</tr>
							<tr>
								<td height="10"></td>
							</tr>
							</table>
							</td>
							<td valign="top" bgcolor="#F1FFEF" style="border:2px #57B54A solid;border-left:1px #57B54A solide;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td height="7"></td>
							</tr>
							<tr>
								<td align="center" height="30"><b>스<img width="20" height="0">펙<img width="20" height="0">내<img width="20" height="0">용</b></td>
							</tr>
							<tr>
								<td height="3"></td>
							</tr>
							<tr>
								<td style="padding-left:5px;padding-right:5px;"><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><tr><td height="1" bgcolor="#DADADA"></td></tr></table></td>
							</tr>
							<tr>
								<td height="5"></td>
							</tr>
							<?for($i=0; $i<$userspec_cnt; $i++) {?>
							<tr>
								<td style="padding:5px;padding-bottom:0px;"><input name=specvalue[] value="<?=htmlspecialchars($specvalue[$i])?>" size=50 maxlength=100 class="input" style="width:100%;"></td>
							</tr>
							<?php }?>
							<tr>
								<td height="10"></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
		                </div>
						</TD>
					</tr>
		<?php
						if($_data->vender<=0) {
							$sql = "SELECT num, package_name, package_type FROM tblproductpackage ";
							$sql.= "ORDER BY num DESC ";
							$result=pmysql_query($sql,get_db_conn());
		?>
					<tr>
						<th><span>패키지 그룹 선택</span></th>
						<td class="" colspan="3">
		                <div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<TR id="packagealertidx"<?=($_data->assembleuse=="Y"?"":" style='display:none;'")?>>
							<TD bgcolor="#FF7100" style="padding:2px;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td align="center" style="padding:10px;" bgcolor="#FFF7F0" height="40"><span class="font_orange"><b>**** 코디/조립 판매가격 사용시 패키지 그룹선택은 사용불가 ****</b></span></td>
							</tr>
							</table>
							</TD>
						</TR>
		<?php
			if($_data->assembleuse!="Y") {
		?>
						<TR id="packageselectidx"<?=($_data->assembleuse=="Y"?" style='display:none;'":"")?>>
							<TD bgcolor="#FF7100" style="padding:2px;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<tr>
								<td align="center" style="padding:10px;" bgcolor="#FFF7F0"><select name="package_num" class="input" style="width:70%;"<?=($_data->assembleuse=="Y"?" disabled":"")?>>
								<option value=""> ---------------- 패키지 그룹을 선택해 주세요. ----------------- </option>
		<?php
								while($row=pmysql_fetch_object($result)) {
									echo "<option value=\"{$row->num}\"".($row->num==(int)$_data->package_num?" selected":"").">필수(".($row->package_type=="Y"?"Y":"N").") : {$row->package_name}</option>\n";
								}
								pmysql_free_result($result);
		?>
								</select> <?php if($popup!="YES"){?><A HREF="product_package.php"><B><img src="images/btn_package.gif" border="0" hspace="2" align=absmiddle><?}?></A>
								</td>
							</tr>
							</table>
							</td>
						</tr>
		<?php
			}
		?>
						</table>
		                </div>
						</td>
					</tr>
		<?php
						}
		?>
					<tr>
						<th><span>검색어</span></th>
						<TD class="" colspan="3"><input name=keyword value="<?php if ($_data) echo $_data->keyword; ?>" size=80 maxlength=100 onKeyDown="chkFieldMaxLen(100)" class="input" style=width:100%></TD>
					</tr>
					<?php if($gongtype=="N"){?>
					<TR>
						<th><span>특이사항</span></th>
						<TD class="" colspan="3"><input name=addcode value="<?php if ($_data) echo str_replace("\"","&quot;",$_data->addcode); ?>" size=43 maxlength=200 onKeyDown="chkFieldMaxLen(200)" class="input">&nbsp;<span class="font_orange">* 상품의 특이사항을 입력해 주세요.</span></TD>
					</TR>
					<?php } else { ?>
					<TR>
						<th><span>공구 판매수량 표시</span></th>
						<TD class="" colspan="3"><input name=addcode value="<?php if ($_data) echo str_replace("\"","&quot;",$_data->addcode); ?>" size=35 maxlength=200 class="input">&nbsp;<span class="font_orange">(예: 한정판매 : 50개, 판매수량 : 100개)</span></TD>
					</TR>
					<?php } ?>
					<?php if(strlen($_data->productcode)==18){?>
					<tr>
						<th><span>태그 관리</span></th>
						<TD class="" colspan="3">
						<DIV id="ProductTagList" name="ProductTagList" style="padding:5px;width:600px;height:68px;word-spacing:7px;background:#fafafa">
							태그를 불러오고 있습니다.
						</DIV>
						</TD>
					</tr>
					
					<script>loadProductTagList('<?=$_data->productcode?>');</script>
					<?php }?>

					<TR>
						<TD class="td_con_orange" colspan="4" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid; border-left:1px solid #b9b9b9;"><b><span class="font_orange">상품이미지등록</span></b><br><font color="black">상품 다중 이미지 등록은 <B>[상품관리 부가기능 =&gt; 상품 다중이미지 등록]</B> 에서 하실 수 있습니다.</font>
						<br>
						<input type=checkbox id="idx_use_imgurl" name=use_imgurl value="Y" <?=($use_imgurl=="Y"?"checked":"")?> onClick="change_filetype(this)"> <label style='cursor:hand;' onMouseOver="style.textDecoration=''" onMouseOut="style.textDecoration='none'" for=idx_use_imgurl><span class="font_orange"><B>상품이미지 첨부 방식을 URL로 입력합니다.</B> (예 : http://www.abc.com/images/abcd.gif)</font></label>
						</TD>
					</TR>
					<TR>
						<th><span>대 이미지</span></th>
						<TD class="" colspan="3">
						<input type=file name="userfile" onChange="document.getElementById('size_checker').src=this.value;" style="WIDTH: 400px">
						<input type=text name="userfile_url" value="<?=$userfile_url?>" style="WIDTH: 400px; display:none" class="input">
						<span class="font_orange">(권장이미지 : 550X550)</span>
						<input type=hidden name="vimage" value="<?=$_data->maximage?>">
		<?php
		
					if ($_data) {
						if (ord($_data->maximage) && file_exists($imagepath.$_data->maximage)) {
							echo "<br><img src='".$imagepath.$_data->maximage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->maximage}'>";
							echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
						} else {
							echo "<br><img src=\"images/space01.gif\">";
						}
					}
		?>
						<br><input type=checkbox id="idx_imgcheck1" name=imgcheck value="Y"<?php if (ord($_data->minimage) || ord($row->tinyimage)) echo "onclick=PrdtAutoImgMsg()"?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_imgcheck1><font color=#003399>대 이미지로 중, 소 이미지 자동생성(중, 소 권장 사이즈로 생성)</font></label>
						</TD>
					</TR>
					<TR>
						<th><span>중 이미지</span></th>
						<TD class="" colspan="3">
						<input type=file name="userfile2" style="WIDTH: 400px" onChange="document.getElementById('size_checker2').src = this.value;">
						<input type=text name="userfile2_url" value="<?=$userfile2_url?>" style="WIDTH: 400px; display:none" class="input">
						<span class="font_orange">(권장이미지 : 300X300)</span>
						<input type=hidden name="vimage2" value="<?=$_data->minimage?>">
		<?php
					if ($_data) {
						if (ord($_data->minimage) && file_exists($imagepath.$_data->minimage)){
							echo "<br><img src='".$imagepath.$_data->minimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->minimage}'>";
							echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
						} else {
							echo "<br><img src=images/space01.gif>";
						}
					}
		?>
						</TD>
					</TR>
					<TR>
						<th><span>소 이미지</span></th>
						<TD class="" colspan="3" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;">
						<input type=file name="userfile3" style="WIDTH: 400px" onChange="document.getElementById('size_checker3').src = this.value;">
						<input type=text name="userfile3_url" value="<?=$userfile3_url?>" style="WIDTH: 400px; display:none" class="input">
						<span class="font_orange">(권장이미지 : 130X130)</span>

						<input type=hidden name=setcolor value="<?=$setcolor?>">
						<input type=hidden name="vimage3" value="<?=$_data->tinyimage?>">
		<?php
					if ($_data) {
						if (ord($_data->tinyimage) && file_exists($imagepath.$_data->tinyimage)){
							echo "<br><img src='".$imagepath.$_data->tinyimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$_data->tinyimage}'>";
							echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('3')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
						} else {
							echo "<br><img src=images/space01.gif>";
						}
					}
		?>
						<BR><input type=checkbox name=imgborder value="Y" <?=(($imgborder)=="Y"?"checked":"")?>>신규 상품등록시 외곽 테두리선 생성 &nbsp; <font class=font_orange>테두리 색상</font> <span id="ColorPreview" style="width:15px;font-size:12pt;background: #<?=$setcolor?>;"></span> &nbsp;<a href="javascript:SelectColor();"><img src="images/btn_color.gif" border="0" align=absmiddle></a>
						</TD>
					</TR>

					<script>change_filetype(document.form1.use_imgurl);</script>

					<tr>
						<TD class="td_con_orange" colspan="4" style="border-left:1px solid #b9b9b9;">
		                <div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=160></col>
						<col width=></col>
						<tr>
							<td><B><span class="font_orange">상품 상세내역 입력</span></B></td>
							<!--<td><?php if($predit_type=="Y"){?><input type=radio id="idx_checkedit1" name=checkedit checked onClick="JavaScript:htmlsetmode('wysiwyg',this)"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkedit1>웹편집기로 입력하기(권장)</label> &nbsp;&nbsp; <input type=radio id="idx_checkedit2" name=checkedit onClick="JavaScript:htmlsetmode('textedit',this);"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_checkedit2>직접 HTML로 입력하기</label><?php } ?>&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox id="idx_localsave" name=localsave value="Y" <?=($localsave=="Y"?"checked":"")?> onClick="alert('상품 상세내역에 링크된 타서버 이미지를 본 쇼핑몰에 저장 후 링크를 변경하는 기능입니다.')"> <label style='cursor:hand;' onMouseOver="style.textDecoration='none'" onMouseOut="style.textDecoration='none'" for=idx_localsave><span class="font_orange"><B>타서버 이미지 쇼핑몰에 저장</B></span></label></td>-->
						</tr>
						</table>
		                </div>
						</TD>
					</tr>
					<tr>
						<TD colspan="4" style="border-left:1px solid #b9b9b9;">
			<textarea wrap=off style="WIDTH: 100%; HEIGHT: 300px" name=content id=ir1><?=htmlspecialchars($_data->content)?></textarea>
						</TD>
					</tr>
					<tr>
						<td colspan="4"><img id="size_checker" style="display:none;"><img id="size_checker2" style="display:none;"><img id="size_checker3" style="display:none;"></td>
					</tr>
					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td colspan="2"><input type=checkbox id="idx_insertdate20" name=insertdate2 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate20><span class="font_orange">상품등록날짜 고정</span></label></td>
					</tr>
					<tr>
						<td align="center" width="100%">
						<?php if (ord($prcode)==0) { ?>
								<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle border="0" vspace="5"></a>
						<?php } else {?>
								<a href="javascript:CheckForm('modify');"><B><img src="images/btn_infoedit.gif" align=absmiddle border="0" vspace="5"></B></a>
								&nbsp;
								<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle border="0" vspace="5"></B></a>
						<?php }?>
									</td>
									<td align="right">
						<?php if (ord($prcode)) { ?>
								<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" vspace="5"></a>
						<?php } ?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<!-- 페이지 타이틀 -->
						<div class="title_depth3">추가정보 등록/수정</div>
					</td>
				</tr>
				<tr>
					<td height=3></td>
				</tr>
				<tr>
					<td>
		            <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR id="assemblealertidx"<?=($_data->assembleuse=="Y"?"":" style='display:none;'")?>>
						<TD colspan="2" bgcolor="#FF7100" style="padding:2px;">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td align="center" style="padding:10px;" bgcolor="#FFF7F0"><span class="font_orange"><b>**** 코디/조립 판매가격 사용시 상품옵션은 사용불가 ****</b></span></td>
						</tr>
						</table>
		                </div>
						</TD>
					</TR>
					<tr>
						<th><span>옵션 타입 선택</span></th>
						<TD class="">
						<input type=radio id="idx_searchtype0" name=searchtype style="border:none" onClick="ViewLayer('layer0')" value="0" <?php if($searchtype=="0") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_searchtype0>옵션정보 없음</label>
						<img width=10 height=0>
						<input type=radio id="idx_searchtype1" name=searchtype style="border:none" onClick="ViewLayer('layer1');alert('옵션1은 최대 10개, 옵션2는 최대 5개로\n각 옵션별 수량조절이 가능하게 됩니다.\n수정시 기존의 그이상의 옵션들은 삭제됩니다.');" value="1" <?php if($searchtype=="1") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_searchtype1>상품 옵션 + <font color=#FF0000>재고관리</font></label> <a href="JavaScript:optionhelp()"><img src="images/product_optionhelp3.gif" align=absmiddle border=0></a>
						<img width=10 height=0>
						<input type=radio id="idx_searchtype2" name=searchtype style="border:none" onClick="ViewLayer('layer2')" value="2" <?php if($searchtype=="2") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_searchtype2>상품 옵션 무제한 등록</label>
						<?php if($gongtype=="N" && (int)$_data->vender==0){?>
						<img width=10 height=0>
						<input type=radio id="idx_searchtype3" name=searchtype style="border:none" onClick="ViewLayer('layer3')" value="3" <?php if($searchtype=="3") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_searchtype3>옵션그룹</label>
						<?php }?>
						
						</td>
					</tr>
					</table>
		            </div>
					<div id=layer0 style="margin-left:0;display:hide; display:block ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
		            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

					</TABLE>
					</div>		
					<div id=layer1 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0; padding-bottom: 5px" class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<?php
					$optionarray1=explode(",",$_data->option1);
					$option_price=explode(",",$_data->option_price);
					$optionarray2=explode(",",$_data->option2);
					$option_quantity_array=explode(",",$_data->option_quantity);
					$optnum1=count($optionarray1)-1; 
					$optnum2=count($optionarray2)-1;

					$optionover="NO";
					if($optnum1>10){
						$optnum1=10;
						$optionover="YES";
					}
					if($optnum2>5){
						$optnum2=5;
						$optionover="YES";
					}
					if($optnum1>0 && ord($_data->option_quantity)==0) $optionover="YES";
					if($optnum2<=1) $optnum2=1;
		?>
					<tr>
						<th><span>상품옵션 속성명</span></th>
						<TD class=""><b>옵션1 속성명</b><B> :<FONT color=#ff6000></FONT> </B><input name=option1_name value="<?php if (ord($_data->option1)) echo htmlspecialchars($optionarray1[0]); ?>" size=20 maxlength=20 class="input">&nbsp;&nbsp;&nbsp;&nbsp;<b>옵션2 속성명</b><B> :<FONT color=#128c02></FONT> </B><input name=option2_name value="<?php if (ord($_data->option2)) echo htmlspecialchars($optionarray2[0]); ?>" size=20 maxlength=20 class="input"></TD>
					</tr>
					<TR>
		 				<td colspan="2"  style="border-left:1px solid #b9b9b9;">
		                    
		                    <!-- 도움말 -->
		                    <div class="help_info01_wrap">
		                        <ul>
		                            <li>1) 옵션가격 입력시 판매가격은 무시되고 옵션가격으로 구매가 진행됩니다.</li>
		                            <li>2) 판매상품 품절일 경우 옵션 재고수량이 남아 있더라도 상품구매는 진행되지 않습니다.<br>
		   옵션 재고수량으로만 상품 관리를 할 경우 판매상품 재고수량을 무제한으로 설정해 주세요.</li>
		                            <li>3) 옵션 재고수량 미입력시 옵션 재고수량은 무제한 상태가 되며 "0" 입력시 옵션 재고수량은 품절 상태가 됩니다.</li>
		                        </ul>
		                    </div>
		                    
		            		</td>          
					</TR>
					<TR>
						<TD colspan="2"  style="border-left:1px solid #b9b9b9;">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
						<TR>
							<TD width="14%" bgColor="#F9F9F9">
							<TABLE cellSpacing=0 cellPadding=0 border=0>
							<TR bgColor=#FF7100 height=2>
								<TD noWrap width=2></TD>
								<TD noWrap width=2></TD>
								<TD width="100%"></TD>
								<TD noWrap width=2></TD>
								<TD noWrap width=2></TD>
							</TR>
							<TR height=50>
								<TD bgColor=#FF7100 rowSpan=25></TD>
								<TD rowSpan=25></TD>
								<TD align=middle><B>옵션1 속성</B></TD>
								<TD rowSpan=25></TD>
								<TD bgColor=#FF7100 rowSpan=25></TD>
							</TR>
							<TR bgColor=#dadada height=1>
								<TD></TD>
							</TR>
							<TR height=1>
								<TD></TD>
							</TR>
		<?php
						for($i=1;$i<=10;$i++){
							if($i==6) echo "<tr height=5><td></td></tr>";
							echo "<tr height=7><td></td></tr>";
							echo "<tr height=19><TD align=middle><input type=text name=optname1 value=\"".trim(htmlspecialchars($optionarray1[$i]))."\" size=12 class=\"input\"></td></tr>";
						}
						echo "<tr height=2><td></td></tr>";
						echo "<tr height=2><td colspan=5 bgcolor=#FF7100></td></tr>";
		?>
							</TABLE>
							</TD>
							<TD noWrap width=2>&nbsp;</TD>
							<TD width="14%" bgColor="#F9F9F9">
							<TABLE cellSpacing=0 cellPadding=0 border=0>
							<TR bgColor=#0071C3 height=2>
								<TD noWrap width=2></TD>
								<TD noWrap width=2></TD>
								<TD width="100%"></TD>
								<TD noWrap width=2></TD>
								<TD noWrap width=2></TD>
							</TR>
							<TR height=50>
								<TD bgColor=#0071C3 rowSpan=25></TD>
								<TD rowSpan=25></TD>
								<TD align=middle><B>가격</B></TD>
								<TD rowSpan=25></TD>
								<TD bgColor=#0071C3 rowSpan=25></TD>
							</TR>
							<TR bgColor=#dadada height=1>
								<TD></TD>
							</TR>
							<TR height=1>
								<TD></TD>
							</TR>
		<?php
						for($i=0;$i<10;$i++){
							if($i==5) echo "<tr height=5><td></td></tr>";
							echo "<tr height=7><td></td></tr>";
							echo "<tr height=21><td align=center><input type=text name=optprice size=12 ";
							echo " value=\"{$option_price[$i]}\" ";
							echo "onkeypress=\"return isNumberKey(event)\" class=\"input\"></td></tr>";
						}
						echo "<tr height=2><td></td></tr>";
						echo "<tr height=2><td colspan=5 bgcolor=#0071C3></td></tr>";
		?>
							</TABLE>
							</TD>
							<TD noWrap width=2>&nbsp;</TD>
							<TD vAlign=top width="72%" bgColor=#ffffff>
							<TABLE cellSpacing=0 cellPadding=0 border=0>
							<TR bgColor=#57B54A height=2>
								<TD width=2 rowSpan=4></TD>
								<TD width=2></TD>
								<TD width=90></TD>
								<TD width=90></TD>
								<TD width=90></TD>
								<TD width=90></TD>
								<TD width=90></TD>
								<TD width=2></TD>
								<TD width=2 rowSpan=4></TD>
							</TR>
							<TR bgColor=#f1ffef height=27>
								<TD width=2 rowspan="2"></TD>
								<TD align=middle colSpan=5 bgcolor="#F9F9F9"><b>옵션2 속성</b></TD>
								<TD width=2 rowspan="2"></TD>
							</TR>
							<TR bgColor=#f1ffef height=19>
		<?php 
							for($i=1;$i<=5;$i++){
								echo "<TD align=middle width=\"20%\" bgcolor=\"#F9F9F9\"><input type=text name=optname2 value=\"".htmlspecialchars($optionarray2[$i])."\" size=12 class=\"input\"></td>";
							}
		?>
							</TR>
							<TR bgColor=#F9F9F9 height=4>
								<TD colSpan=7></TD>
							</TR>
							<TR bgColor=#57B54A height=2>
								<TD colSpan=9></TD>
							</TR>
							<TR height=6>
								<TD colSpan=2 rowSpan="22"></TD>
								<TD colSpan=5></TD>
								<TD colSpan=2 rowSpan="22"></TD>
							</TR>
		<?php
						for($i=0;$i<10;$i++){
							if($i!=0 && $i!=5) echo "<tr><td colspan=5 height=7></td></tr>";
							else if($i==5) echo "<tr><td colspan=5 height=6></td></tr>
												<tr><td colspan=5 height=1 bgcolor=#DADADA></td></tr>
												<tr><td colspan=5 height=5></td></tr>";
							echo "<tr height=19>";
							for($j=0;$j<5;$j++){
								echo "<TD align=middle><input type=text name=optnumvalue[{$j}][{$i}] value=\"".$option_quantity_array[$j*10+$i+1]."\" size=12 maxlength=3 onkeyup=\"strnumkeyup(this)\" class=\"input\"></TD>\n";
							}
							echo "</tr>";
						}
		?>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
		                </div>
						</TD>
					</TR>
					
					</table>
					</div>
					
					<div id=layer2 style="margin-left:0;display:hide; display:none ;background:#FFFFFF;padding:0; padding-bottom: 5px" class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>옵션1</span></th>
						<TD class="">
		<?php
						$option1="";
						$optname1="";
						if ($_data) {
							if (ord($_data->option1)) {
								$tok = strtok($_data->option1,",");
								$optname1=$tok;
								$tok = strtok("");
								$option1=$tok;
							}
						}
		?>
						<div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
						<TR>
							<TD>1)속성명</TD>
							<TD style="PADDING-LEFT: 5px"><input name=toptname1 value="<?php if ($_data && ord($_data->option1)) echo $optname1; ?>" size=50 maxlength=20 class="input"></TD>
						</TR>
						<TR>
							<TD>2)속성</TD>
							<TD style="PADDING-LEFT: 5px"><input name=toption1 value="<?php if ($_data && ord($_data->option1)) echo htmlspecialchars($option1); ?>" size=50 maxlength=230 class="input"></TD>
						</TR>
						<TR>
							<TD style="PADDING-LEFT: 3px" colSpan=2>* 옵션의 속성명으로 색상 또는 사이즈 또는 용량 등을 입력해서 사용하세요.<br>* 속성은 속성명에 대한 세부내용을 입력합니다.<br>&nbsp;&nbsp;&nbsp;예)빨강,파랑,노랑 또는 95,100,105 와 같이 컴마(,)로 구분하여 공백없이 입력합니다.</TD>
						</TR>
						</TABLE>
		                </div>
						</TD>
					</TR>
					<TR>
						<th><span>옵션1 가격</span></th>
						<TD class="">
						<?php if($gongtype=="N"){?>
							<input name=toption_price value="<?php if ($_data) echo $_data->option_price; ?>" size=50 maxlength=250 class="input">&nbsp;<span class="font_orange"><b>예) 1000,2000,3000</b></span><br>
							* 옵션1 가격 입력시 판매가격은 무시됩니다.<br>
							* 옵션1 가격 입력시 판매가격 대신 첫번째 가격이 판매가격으로 사용됩니다.<br>
							* 카테고리내 상품 출력시 "판매가격 (기본가)"로 표기 됩니다.<br>
							* 메세지 변경은 <?=($popup=="YES"?"":"<A HREF=\"javascript:opener.parent.topframe.GoMenu(1,'shop_mainproduct.php');\">")?><span class="font_blue">상점관리 > 쇼핑몰 환경설정 > 상품 진열 기타 설정</span></A> 에서 변경 가능.
						<?php } else { ?>
							가격 고정형 공동구매의 경우 옵션1 가격은 지원하지 않습니다.<input type=hidden name=toption_price>
						<?php } ?>
						</TD>
					</TR>
					<tr>
						<th><span>옵션2</span></th>
						<TD class="">
		<?php
					$option2="";
					$optname2="";
					if ($_data) {
						if (ord($_data->option2)) {
							$tok = strtok($_data->option2,",");
							$optname2=$tok;
							$tok = strtok("");
							$option2=$tok;
						}
					}
		?>				
						<div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
						<TR>
							<TD>1)속성명</TD>
							<TD style="PADDING-LEFT: 5px"><input name=toptname2 value="<?php if ($_data && ord($_data->option2)) echo $optname2; ?>" size=50 maxlength=20 class="input"></TD>
						</TR>
						<TR>
							<TD>2)속성</TD>
							<TD style="PADDING-LEFT: 5px"><input name=toption2 value="<?php if ($_data && ord($_data->option2)) echo htmlspecialchars($option2); ?>" size=50 maxlength=230 class="input"></TD>
						</TR>
						<TR>
							<TD style="PADDING-LEFT: 3px" colSpan=2>* 옵션1 등록 방법과 같으나 "<B>옵션1 가격</B>"과는 무관합니다.</TD>
						</TR>
						</TABLE>
		                </div>
						</TD>
					</tr>
					
					</table>
					</div>
					
					<div id=layer3 style="margin-left:0;display:hide; display:none ;background:#FFFFFF;padding:0; padding-bottom: 5px" class="table_style01">
					<?php if($gongtype=="N" && (int)$_data->vender==0){?>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>옵션그룹 선택</span></th>
						<TD class="">
						<select name=optiongroup style="width: 70%" class="select">
		<?php
					$sqlopt = "SELECT option_code,description FROM tblproductoption ";
					$resultopt = pmysql_query($sqlopt,get_db_conn());
					$optcnt=0;
					while($rowopt = pmysql_fetch_object($resultopt)){
						if($optcnt++==0) echo "<option value=0>옵션그룹을 선택하세요.";
						echo "<option value=\"{$rowopt->option_code}\"";
						if($optcode==$rowopt->option_code) echo " selected";
						echo ">{$rowopt->description}</option>";
					}
					pmysql_free_result($resultopt);
					if($optcnt==0) echo "<option value=0>등록하신 옵션그룹이 없습니다.</option>";
		?>
						</select>
						<?php if($popup!="YES"){?><A HREF="javascript:parent.location='product_package.php';"><B><img src="images/btn_option.gif" border="0" hspace="2" align=absmiddle></B></A><?php }?>
						<?php if($optcnt==0) echo "<script>document.form1.optiongroup.disabled=true;</script>";?>
						
						<br>* (상품가격+옵션) 변경가격 사용시 옵션그룹을 이용해 주세요.
						<br>* 옵션그룹 사용시 옵션1과 옵션2는 자동 삭제됩니다.
						<br>* 옵션그룹 선택시 해당 옵션그룹에 등록된 상품옵션을 확인할 수 있습니다.
						</TD>
					</TR>
					
					</TABLE>
					<?php }?>
					</div>
					
		            <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<th><span>아이콘 꾸미기</span></th>
						<TD class="">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<?php
					$iconarray = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28");
					$totaliconnum = 0;
					for($i=0;$i<count($iconarray);$i++) {
						if($i%7==0) echo "<TR height=25>";
						echo "<TD width=\"14%\"><input type=checkbox name=icon onclick=CheckChoiceIcon('{$totaliconnum}') value=\"{$iconarray[$i]}\" ";
						if($iconvalue2[$iconarray[$i]]=="Y") echo "checked";
						echo "><img src=\"{$Dir}images/common/icon{$iconarray[$i]}.gif\" border=0 align=absmiddle></td>\n";
						if($i%7==6) echo "</tr>";
						$totaliconnum++;
					}
		?>
						<TR>
							<TD colSpan=7 height=5></TD>
						</TR>
						<TR>
							<TD colSpan=7>
							<table cellpadding="1" cellspacing="1" width="100%" bgcolor="#FF9933">
							<tr>
								<td width="585" bgcolor="#FFFCF6">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="167" align=center style="padding-top:5pt; padding-bottom:5pt;"><b><span class="font_orange">내 아이콘</span></b></td>
									<td width="424" style="padding-top:5pt; padding-bottom:5pt;">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<?php 
								$iconpath=$Dir.DataDir."shopimages/etc/";
								$usericon = array("U1","U2","U3","U4","U5","U6");
								$cnt=0;
								for($i=0;$i<count($usericon);$i++){
									if(file_exists($iconpath."icon{$usericon[$i]}.gif")){
										$cnt++;
										if($cnt%3==1) echo "<TR height=25>";
										echo "<td width=33%><input type=checkbox name=icon onclick=CheckChoiceIcon('{$totaliconnum}') value=\"{$usericon[$i]}\" ";
										if($iconvalue2[$usericon[$i]]=="Y") echo "checked";
										echo "> <img src=\"{$iconpath}icon{$usericon[$i]}.gif\" border=0 align=absmiddle></td>\n";
										if($cnt%3==0) echo "</tr>";
										$totaliconnum++; 
									}
								}
								if($cnt==0) {
									echo "<tr><td align=center><font color=red>등록된 내 아이콘이 없습니다.</font></td></tr>";
								} else {
									for($i=$cnt;$i<6;$i++){
										echo "<td width=33%>&nbsp;</td>";
										if($i%3==2) echo "</tr><tr>";
									}
									if($cnt<6) echo "</tr>";
								}
		?>
									</TABLE>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#FF9933" style="padding-left:5pt;"><font color="white"><span style="letter-spacing:-0.5;">* 한 상품에 3개까지 아이콘을 사용할 수 있습니다.<br>* <b>아이콘 등록은 6개 까지 등록</b> 가능합니다.</span></font></td>
							</tr>
							<tr>
								<td bgcolor="#FF9933"><A href="JavaScript:IconMy()"><IMG src="images/productregister_iconinsert.gif" align=absMiddle border=0 width="120" height="20"></A><!--&nbsp;<A href="JavaScript:IconList()"><IMG src="images/productregister_icondown.gif" align=absMiddle border=0 width="98" height="20"></A>--></td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
		                </div>
						</TD>
					</tr>
					<TR>
						<th><span>상품진열여부</span></th>
						<TD class=""><input type=radio id="idx_display1" name=display value="Y" <?php if ($_data) { if ($_data->display=="Y") echo "checked"; } else echo "checked";  ?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display1>진열함</label> &nbsp; <input type=radio id="idx_display2" name=display value="N" <?php if ($_data) { if ($_data->display=="N") echo "checked"; } ?> onClick="JavaScript:alert('메인 화면의 상품 특징이 없음으로 변경됩니다.')"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_display2>진열안함</label></TD>
					</TR>
					<TR>
						<th><span>거래 업체 선택</span></th>
						<TD class=""><select name=bisinesscode class="select">
						<option value="0"> -------- 거래업체를 선택하세요. -------- </option>
		<?php
					$sqlbiz = "SELECT companycode,companyviewval FROM tblproductbisiness ";
					$resultbiz = pmysql_query($sqlbiz,get_db_conn());
					$bizcnt=0;
					while($rowbiz = pmysql_fetch_object($resultbiz)){
						echo "<option value=\"{$rowbiz->companycode}\"";
						if($_data->bisinesscode==$rowbiz->companycode) echo " selected";
						echo ">{$rowbiz->companyviewval}</option>\n";
						$bizcnt++;
					}
					pmysql_free_result($resultbiz);
					if($bizcnt==0) echo "<option value=\"0\">등록하신 거래업체가 없습니다.</option>";
		?>
						</select><br>
						<span class="font_orange">* 거래 업체 등록은 <a href="javascript:opener.parent.topframe.GoMenu(4,'product_business.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 상품 거래처 관리</span></a>에서 등록하세요.</span>
						</TD>
					</TR>
					<input type=hidden name=old_display value="<?=$_data->display?>">
					<TR>
						<th><span>상품기타설정</span></th>
						<TD class="">
		                <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD><input type=checkbox id="idx_bankonly1" name=bankonly value="Y" <?php if ($_data) { if ($bankonly=="Y") echo "checked";}?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_bankonly1>현금결제만 사용하기</label> <span class="font_orange">(여러 상품과 함께 구매시 결제는 현금결제로만 진행됩니다.)</span></TD>
							<td></td>
						</TR>
						<?php if ($card_splittype=="O") { ?>
						<tr>
							<td><input type=checkbox id="idx_setquota1" name=setquota value="Y" <?php if ($_data) { if ($setquota=="Y") echo "checked";}?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_setquota1>상점부담 무이자</label> <span class="font_orange">(결제금액/무이자할부개월은 <a  href="shop_payment.php"><b>결제관련기능설정</b></a>과 동일)</span></td>
							<td></td>
						</tr>
						<?php } ?>
						<?php if ($gongtype=="N") { ?>
						<TR>
							<TD style="PADDING-TOP: 5px"><input type=checkbox id="idx_dicker1" name=dicker value="Y" <?php if ($_data) { if ($dicker=="Y") echo "checked";}?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_dicker1><b>판매가격 대체문구</b></label> &nbsp;<input type=text name=dicker_text value="<?=$dicker_text?>" size=20 maxlength=20 onKeyDown="chkFieldMaxLen(20)" class="input"> <span class="font_orange">* 예) 판매대기상품, 상담문의(000-000-000)</span></TD>
							<td></td>
						</TR>
						<TR>
							<TD colSpan=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* <b>판매가격 대체문구</b>는 상품 판매가격 대신 원하는 문구를 출력시키는 기능입니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* <b>판매가격 대체문구</b> 입력가능 글자 수는 한글 10자, 영문 20자로 제한되어 있습니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* <b>판매가격 대체문구</b> 사용시 주문은 진행되지 않습니다.</TD>
						</TR>
						<?php } ?>

						<TR>
							<TD style="PADDING-TOP: 5px"><input type=checkbox id="idx_deliinfono1" name=deliinfono value="Y" <?php if ($_data) { if ($deliinfono=="Y") echo "checked";}?>><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_deliinfono1>배송/교환/환불정보 노출안함</label> <font color=#AA0000>(상품상세화면 하단에 배송/교환/환불정보가 노출안됨)</font></TD>
							<td></td>
						</TR>

						</TABLE>
		                </div>
						</TD>
					</TR>
					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td colspan="2"><input type=checkbox id="idx_insertdate30" name=insertdate3 value="Y" onClick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_insertdate30><span class="font_orange">상품등록날짜 고정</span></label></td>
					</tr>
					<tr>
						<td align="center" width="100%">
						<?php if (ord($prcode)==0) { ?>
								<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle border="0" vspace="5"></a>
						<?php } else {?>
								<a href="javascript:CheckForm('modify');"><B><img src="images/btn_infoedit.gif" align=absmiddle border="0" vspace="5"></B></a>
								&nbsp;
								<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle border="0" vspace="5"></B></a>
						<?php }?>
									</td>
									<td align="right">
						<?php if (ord($prcode)) { ?>
								<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" vspace="5"></a>
						<?php } ?>
						</td>
					</tr>
					</table>
		            </td>
				</tr>
				<tr><td height=50></td></tr>
				
				</table>
		<?php
		//	}
		?>
				</td>
			</tr>
			</table>
			
		</td>
	</tr>
	<tr>
			<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>메뉴얼</p></div>
					<dl>
						<dt><span>제목제목제목</span></dt>
						<dd>
							  - 내용내용내용  <br />
							  - 내용내용내용 <br />
							  - 내용내용내용
						</dd>
					</dl>
				</div>
			</td>
		</tr>
		
	<input type=hidden name=iconnum value='<?=$totaliconnum?>'>
	<input type=hidden name=iconvalue>
	<input type=hidden name=optnum1 value=<?=$optnum1?>>
	<input type=hidden name=optnum2 value=<?=$optnum2?>>
	</form>
	
	</table>

	</td>
</tr>

</table>

</td>
</tr>

</table>
</td>
</tr>
</table>

<form name=cForm action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=popup value="<?=$popup?>">
<input type=hidden name=code value=<?=$code?>>
<input type=hidden name=prcode value=<?=$prcode?>>
<input type=hidden name=delprdtimg>
<input type=hidden name="vimage" value="<?php if ($_data) echo $_data->maximage; ?>">
<input type=hidden name="vimage2" value="<?php if ($_data) echo $_data->minimage; ?>">
<input type=hidden name="vimage3" value="<?php if ($_data) echo $_data->tinyimage; ?>">
</form>
<form name=icon action="product_iconmy.php" method=post target=icon>
</form>
<form name=iconlist action="product_iconlist.php" method=post target=iconlist>
</form>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
	
<?=$onload?>

<?php
if (strlen($code)==12 && $predit_type=="Y") {
?>
<!--
<script language="Javascript1.2" src="htmlarea/editor.js"></script>

<script language="JavaScript">
function htmlsetmode(mode,i){
	if(mode==document.form1.htmlmode.value) {
		return;
	} else {
		i.checked=true;
		editor_setmode('content',mode);
	}
	document.form1.htmlmode.value=mode;
} 
_editor_url = "htmlarea/";
editor_generate('content');
</script>
-->
<?php
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
<?php if ($gongtype=="Y") {?>
	gongname="시작가";
	gongname2="공구가";
<?php } else {?>
     gongname="소비자가격";
     gongname2="판매가격";
<?php }?>

var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.content.value=sHTML;
	if (document.form1.productname.value.length==0) {
		alert("상품명을 입력하세요.");
		document.form1.productname.focus();
		return;
	}
	if (CheckLength(document.form1.productname)>100) {
		alert('총 입력가능한 길이가 한글 50자까지입니다. 다시한번 확인하시기 바랍니다.');
		document.form1.productname.focus();
		return;
	}
	if (document.form1.consumerprice.value.length==0) {
		alert(gongname+"을 입력하세요.");
		document.form1.consumerprice.focus();
		return;
	}
	if (isNaN(document.form1.consumerprice.value)) {
		alert(gongname+"을 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
<?php if($_data->vender<=0){?>
	if(document.form1.sellprice.disabled==false) {
		if (document.form1.sellprice.value.length==0) {
			alert(gongname2+"을 입력하세요.");
			document.form1.sellprice.focus();
			return;
		}
		if (isNaN(document.form1.sellprice.value)) {
			alert(gongname2+"을 숫자로만 입력하세요.(콤마제외)");
			document.form1.consumerprice.focus();
			return;
		}
	}
<?php }?>
	if (document.form1.reserve.value.length>0) {
		if(document.form1.reservetype.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
			
			if(getSplitCount(document.form1.reserve.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(getPointCount(document.form1.reserve.value,".",2)) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(Number(document.form1.reserve.value)>100 || Number(document.form1.reserve.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.reserve.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.reserve.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
		}
	}
<?php if ($gongtype=="N") {?>
	if (document.form1.checkquantity[2].checked) {
<?php } else {?>
	if (document.form1.checkquantity[1].checked) {
<?php }?>
		if (document.form1.quantity.value.length==0) {
			alert("수량을 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (isNaN(document.form1.quantity.value)) {
			alert("수량을 숫자로만 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (parseInt(document.form1.quantity.value)<=0) {
			alert("수량은 0개이상이여야 합니다.");
			document.form1.quantity.focus();
			return;
		}
	}
	miniq_obj=document.form1.miniq;
	maxq_obj=document.form1.maxq;
	if (miniq_obj.value.length>0) {
		if (isNaN(miniq_obj.value)) {
			alert ("최소주문한도는 숫자로만 입력해 주세요.");
			miniq_obj.focus();
			return;
		}
	}
	if (document.form1.checkmaxq[1].checked) {
		if (maxq_obj.value.length==0) {
			alert ("최대주문한도의 수량을 입력해 주세요.");
			maxq_obj.focus();
			return;
		} else if (isNaN(maxq_obj.value)) {
			alert ("최대주문한도의 수량을 숫자로만 입력해 주세요.");
			maxq_obj.focus();
			return;
		}
	}
	if (miniq_obj.value.length>0 && document.form1.checkmaxq[1].checked && maxq_obj.value.length>0) {
		if (parseInt(miniq_obj.value) > parseInt(maxq_obj.value)) {
			alert ("최소주문한도는 최대주문한도 보다 작아야 합니다.");
			miniq_obj.focus();
			return;
		}
	}
	if(document.form1.deli[3].checked || document.form1.deli[4].checked) {
		if(document.form1.deli[3].checked)
		{
			if (document.form1.deli_price_value1.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value1.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value1.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value1.focus();
				return;
			}
		}
		else
		{
			if (document.form1.deli_price_value2.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value2.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value2.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value2.focus();
				return;
			}
		}
	}

	searchtype=false;
	for(i=0;i<document.form1.searchtype.length;i++) {
		if(document.form1.searchtype[i].checked) {
			searchtype=true;
			shop="layer"+i;
			break;
		}
	}

	if(searchtype==false) {
		alert("옵션 타입을 선택하세요.\n\n공동구매 타입의 상품일 경우 옵션그룹 사용이 불가하오니\n잘 확인하시기 바랍니다.");
		document.form1.searchtype[0].focus();
		return;
	}
	
	if(document.form1.sellprice.disabled==false) {
		if(shop=="layer0") {

		} else if(shop=="layer1"){
			optnum1=0;
			optnum2=0;

			//옵션1 항목
			document.form1.option1.value="";
			for(i=0;i<10;i++){
				if(document.form1.optname1[i].value.length>0) {
					document.form1.option1.value+=document.form1.optname1[i].value+",";
					optnum1++;
				}
			}

			//옵션1 제목 검사 (옵션1 항목이 NULL이 아니면)
			if((document.form1.option1.value.length!=0 && document.form1.option1_name.value.length==0)
			|| (document.form1.option1.value.length==0 && document.form1.option1_name.value.length!=0)){
				alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
				if(document.form1.option1_name.value.length==0) {
					document.form1.option1_name.focus();
				} else {
					document.form1.optname1[0].focus();
				}
				return;
			}

			//옵션2 항목
			document.form1.option2.value="";
			for(i=0;i<5;i++){
				if(document.form1.optname2[i].value.length>0) {
					document.form1.option2.value+=document.form1.optname2[i].value+",";
					optnum2++;
				}
			}

			//옵션2 제목 검사 (옵션2 항목이 NULL이 아니면)
			if((document.form1.option2.value.length!=0 && document.form1.option2_name.value.length==0)
			|| (document.form1.option2.value.length==0 && document.form1.option2_name.value.length!=0)){
				alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
				if(document.form1.option2_name.value.length==0) {
					document.form1.option2_name.focus();
				} else {
					document.form1.optname2[0].focus();
				}
				return;
			}

			//옵션2만 입력했는지 검사
			if(document.form1.option1.value.length==0 && document.form1.option2.value.length>0) {
				alert('옵션2는 옵션1 입력후 입력가능합니다.');
				document.form1.option1_name.focus();
				return;
			}

			//옵션1에 따른 가격 검사
			document.form1.option_price.value="";
			pricecnt=0;
			for(i=0;i<optnum1;i++){
				if(document.form1.optprice[i].value.length==0){
					pricecnt++;
				}else{
					document.form1.option_price.value+=document.form1.optprice[i].value+",";
				}
			}
			if(optnum1>0 && pricecnt!=0 && pricecnt!=optnum1){
				alert('옵션별 가격은 모두 입력하거나 모두 입력하지 않아야 합니다.');
				document.form1.optprice[0].focus();
				return;
			}

			if(document.form1.option_price.value.length!=0) temp=0;
			else temp=-1;
			temp2=document.form1.option_price.value;
			while(temp!=-1){
				temp=temp2.indexOf(",");
				if(temp!=-1) temp3=(temp2.substring(0,temp));
				else temp3=temp2;
				if(isNaN(temp3)){
					alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
					document.form1.option_price.focus();
					return;
				}
				temp2=temp2.substring(temp+1);
			}

			//재고수량 및 숫자검사
			isquan=false;
			quanobj="";
			for(i=0;i<10;i++) {
				isgbn1=false;
				if(i<optnum1) isgbn1=true;

				for(j=0;j<5;j++) {
					isgbn2=false;
					if(optnum2>0) {
						if(j<optnum2 && isgbn1) isgbn2=true;
					} else {
						if(j==0 && isgbn1) isgbn2=true;
					}

					if(isgbn2) {
						if(isquan==false && document.form1["optnumvalue["+j+"]["+i+"]"].value.length==0) {
							isquan=true;
							quanobj=document.form1["optnumvalue["+j+"]["+i+"]"];
						}
					} else {
						if(document.form1["optnumvalue["+j+"]["+i+"]"].value.length>0) {
							alert("입력하신 수량이 옵션정보의 범위를 넘었습니다. ("+(i+1)+" 째줄 "+(j+1)+" 째칸)");
							document.form1["optnumvalue["+j+"]["+i+"]"]. focus();
							return;
						}
					}
				}
			}
			if(isquan) {
				if(!confirm("수량 입력이 안된 옵션정보는 무제한 수량으로 등록됩니다.\n\n계속 하시겠습니까?")) {
					quanobj.focus();
					return;
				}
			}

		} else if(shop=="layer2"){
			if (document.form1.toption_price.value.length!=0 && document.form1.toption1.value.length==0) {
				alert("특수코드별가격을 입력하면 반드시 특수코드입력1에도 내용을 입력해야 합니다.");
				document.form1.toption1.focus();
				return;
			}
			if(document.form1.toption_price.value.length!=0) temp=0;
			else temp=-1;
			temp2=document.form1.toption_price.value;
			while(temp!=-1){
				temp=temp2.indexOf(",");
				if(temp!=-1) temp3=(temp2.substring(0,temp));
				else temp3=temp2;
				temp4=" "+temp3;
				if(isNaN(temp3) || temp4.indexOf('.')>0){
					alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
					document.form1.toption_price.focus();
					return;
				}
				temp2=temp2.substring(temp+1);
			}
			document.form1.option_price.value=document.form1.toption_price.value+",";
			document.form1.option1_name.value=document.form1.toptname1.value;
			document.form1.option1.value=document.form1.toption1.value+",";
			document.form1.option2_name.value=document.form1.toptname2.value;
			document.form1.option2.value=document.form1.toption2.value+",";
<?	if($gongtype=="N" && (int)$_data->vender==0) { ?>
		} else if(shop=="layer3") {
			if(document.form1.optiongroup.selectedIndex==0) {
				alert("옵션그룹을 선택하세요.");
				document.form1.optiongroup.focus();
				return;
			}
<?php } ?>
		}
	}

	if(document.form1.use_imgurl.checked!=true) {
		filesize = Number(document.form1.size_checker.fileSize) + Number(document.form1.size_checker2.fileSize) + Number(document.form1.size_checker3.fileSize) ;
		if(filesize><?=$maxfilesize?>) { 
			alert('올리시려고 하는 파일용량이 500K이상입니다.\n파일용량을 체크하신후에 다시 이미지를 올려주세요');
			return;
		}
	}
	tempcontent = document.form1.content.value;
<?php if ($predit_type=="Y"){ ?>
	if(mode=="modify" && tempcontent.length>0 && tempcontent.indexOf("<")==-1 && tempcontent.indexOf(">")==-1 && !confirm("웹편집기 기능추가로 텍스트로만 입력하신 상세설명은\n줄바꾸기가 해제되어 쇼핑몰에서 다르게 보여질 수 있습니다.\n\n재입력하시거나 현재 쇼핑몰에서 해당 상품의 상세설명을\n그대로 마우스로 드래그하여 붙여넣기를 해서 재입력하셔야 합니다.\n\n위와 같이 수정하지 않고 저장하시려면 [확인]을 누르세요.")){
		return;
	}
<?php }?>
	document.form1.iconvalue.value="";
	num = document.form1.iconnum.value;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked) document.form1.iconvalue.value+=document.form1.icon[i].value;
	}
	if (document.form1.insertdate1.checked) document.form1.insertdate.value="Y";
	document.form1.mode.value=mode;
	document.form1.submit();
}

//-->
</SCRIPT>
<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	}, 
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>
<?php
if($searchtype==2 || $optionover=="YES") {
	echo "<script>document.form1.searchtype[2].checked=true;\nViewLayer('layer2');</script>";
} else if($searchtype==1) {
	echo "<script>document.form1.searchtype[1].checked=true;\nViewLayer('layer1');</script>";
} else if($searchtype==3 && $gongtype=="N" && (int)$_data->vender==0) {
	echo "<script>document.form1.searchtype[3].checked=true;\nViewLayer('layer3');</script>";
}
?>
<?php
include("copyright.php");
?>
</body>
</html>
