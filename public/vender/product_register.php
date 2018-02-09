<?php
/********************************************************************* 
// 파 일 명    : product_register.php 
// 설     명   : 입점업체 상품관리
// 상세설명    : 검증된 입점업체가 상품을 등록
// 작 성 자    : hspark
// 수 정 자    : 2015.10.26 - 유동혁
//
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
/**
*
* 함수명 : ProductThumbnail
* 이미지 썸네일 생성
* parameter :
* 	- string prcode : 상품코드 ( 이미지 폴더 )
*   - string fileName : 업로드 이미지명
*   - string upFile : 파일명
*	- makeWidth : 편집될 넓이
*	- makeHeight : 편집될 높이
* 2015 10 30 유동혁
*/
function ProductThumbnail ( $prcode, $fileName, $upFile, $makeWidth, $makeHeight, $imgborder, $setcolor='' ){
	$imagepath = DirPath.DataDir."shopimages/product/".$prcode."/";
	$quality = "90";
	if(ord($setcolor)==0) $setcolor="000000";
	$rcolor=HexDec(substr($setcolor,0,2));
	$gcolor=HexDec(substr($setcolor,2,2));
	$bcolor=HexDec(substr($setcolor,4,2));
	$quality = "90";
	if ( ord($fileName) && file_exists( $imagepath.$upFile ) ) {
		$imgname=$imagepath.$upFile; // 위치 + 파일명
		$size=getimageSize($imgname); //파일 사이즈 array ( 0=>width, 1=>height, 2=>imgtype )
		$width=$size[0];
		$height=$size[1];
		$imgtype=$size[2];

		if ($width >= $makeWidth || $height >= $makeHeight) {
			# 파일 타입별 이미지생성
			if($imgtype==1)      $im = ImageCreateFromGif($imgname);
			else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
			else if($imgtype==3) $im = ImageCreateFromPng($imgname);
			 # 파일의 넓이나 높이가 큰 부분을 기준으로 파일을 자른다
			 $small_width = $makeWidth;
			 $small_height = $makeHeight;

			 # 타입별로 파일 색상, 크기 리사이즈
			if ($imgtype==1) {
				$im2=ImageCreate($small_width,$small_height); // GIF일경우
				$white = ImageColorAllocate($im2, 255,255,255);
				imagefill($im2,1,1,$white);
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
			chmod($imgname,0777);
		}
	}
}

function product_related($rmode,$prcode,$prname){ //관련 상품 함수 06 29 원재 ㅠㅠ
	
	if($rmode == "update"){
		$r_product = $_POST['relationProduct'];

		if(!$r_product && $prcode){
			$r_sql3 = " delete from tblproduct_related where productcode='{$prcode}' ";
			pmysql_query($r_sql3);

		}
		
		if($r_product && $prcode){
			//기존 관련상품 삭제
			$r_sql1 = " delete from tblproduct_related where productcode='{$prcode}' ";
			pmysql_query($r_sql1);

			//새로 등록할 관련상품 동작
			$r_sql2 = " insert into tblproduct_related (productcode,productname,r_productcode,sort) values ";
			foreach($r_product as $r_sort=>$r_val){
				$r_sql_values[] = " ('{$prcode}','{$prname}','{$r_val}',{$r_sort}) ";
			}
			$r_qry = implode(",",$r_sql_values);
			$r_sql2 .= $r_qry;
			pmysql_query($r_sql2);
		}
	}
}

function product_size($mode,$prcode,$use){ //사이즈 조견표 함수 07 05 원재 ㅠㅠ
	
	if($use=='N'){//사이즈 조견표 사용 안함시 동작
		$sql = " update tblproduct_size set use='N' where productcode='{$prcode}' ";
		pmysql_query($sql);
	}
		
	if($mode=='up' && $use !='N'){//사이즈 조견표 새로 삽입

		$sizex_sub = $_REQUEST['sizex_subj'];
		$sizey_sub = $_REQUEST['sizey_subj'];
		$size_content = $_REQUEST['size_content'];
		
		if($prcode){//기존에 있던 사이즈 조견표 삭제
			$sql = "delete from tblproduct_size where productcode='{$prcode}' ";
			pmysql_query($sql);
		}

		if($size_content && $sizex_sub && $sizey_sub){
			
			$sql = " insert into tblproduct_size ( productcode,type,rows,cols,text,use) values " ;

			foreach($size_content as $index_1=>$val_1){
				foreach($val_1 as $index_2=>$val_2){
					$content_qry[] = "('{$prcode}','C',{$index_1},{$index_2},'{$val_2}' ,'Y' )";
				}
			}

			foreach($sizex_sub as $index_x=>$xval){
				$content_qry[] = "('{$prcode}','X',{$index_x},0,'{$xval}' ,'Y' )";
			}

			foreach($sizey_sub as $index_y=>$yval){
				$content_qry[] = "('{$prcode}','Y',0,{$index_y},'{$yval}' ,'Y')";
			}

			$qry = implode(",",$content_qry);
			$sql .= $qry;
			pmysql_query($sql);
		}
		
	}
}

#---------------------------------------------------------------
# 권한 및 등록갯수를 체크한다
#---------------------------------------------------------------
if($_venderdata->grant_product[0]!="Y") {
	alert_go("상품 등록 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",-1);
}

if($_venderdata->product_max!=0) {
	$sql = "SELECT prdt_allcnt FROM tblvenderstorecount WHERE vender='".$_VenderInfo->getVidx()."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	$prdt_allcnt=$row->prdt_allcnt;

	if($_venderdata->product_max<=$prdt_allcnt) {
		alert_go("해당 미니샵에서 등록할 수 있는 상품갯수는 ".$_venderdata->product_max."개 입니다.\\n\\n다른상품을 삭제 후 등록하시거나 쇼핑몰에 문의하시기 바랍니다.",-1);
	}
}
#---------------------------------------------------------------
# 파일 및 초기설정
#---------------------------------------------------------------

$userspec_cnt=5;
# 파일 용량 3K -> 3M 변경
$maxfilesize="3072000";
$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$maxsize=130;
$makesize=130;

$sql = "SELECT predit_type,etctype FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$predit_type=$row->predit_type;
	if(strpos(" ".$row->etctype,"IMGSERO=Y")) {
		$imgsero="Y";
	}
} 
pmysql_free_result($result);

if(strlen($_POST["setcolor"])==0){
	$setcolor=$_COOKIE["setcolor"];
} else if($_COOKIE["setcolor"]!=$_POST["setcolor"]){
	SetCookie("setcolor",$setcolor,0,"/".RootPath.VenderDir);
	$setcolor=$_POST["setcolor"];
} else {
	$setcolor=$_COOKIE["setcolor"];
}

if(strlen($setcolor)==0) $setcolor="000000";
$rcolor=HexDec(substr($setcolor,0,2));
$gcolor=HexDec(substr($setcolor,2,2));
$bcolor=HexDec(substr($setcolor,4,2));
$quality = "90";

// 테두리 설정에 대한 부분을 쿠키로 고정시킨다.
if ($_POST["imgborder"]=="Y" && $_COOKIE["imgborder"]!="Y") {
	SetCookie("imgborder","Y",0,"/".RootPath.VenderDir);
} else if ($_POST["imgborder"]!="Y" && $_COOKIE["imgborder"]=="Y" && $mode=="insert") {
	SetCookie("imgborder","",time()-3600,"/".RootPath.VenderDir);
	$imgborder="";
} else {
	$imgborder=$_COOKIE["imgborder"];
}
// 쿠키 끝

#---------------------------------------------------------------
# 상품등록 INSERT
#---------------------------------------------------------------

if($mode=="insert") {
	
	# 진열 코드 추가 2015 10 23 유동혁
	list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
	if(strlen($code_a)!=3) $code_a="000";
	if(strlen($code_b)!=3) $code_b="000";
	if(strlen($code_c)!=3) $code_c="000";
	if(strlen($code_d)!=3) $code_d="000";
	$code = $code_a.$code_b.$code_c.$code_d;

	//분류 확인
	$sql = "SELECT type FROM tblproductcode WHERE code_a='".substr($code,0,3)."' AND code_b='".substr($code,3,3)."' ";
	$sql.= "AND code_c='".substr($code,6,3)."' AND code_d='".substr($code,9,3)."' ";
	
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if(substr($row->type,-1)!="X") {
			echo "<html></head><body onload=\"alert('상품을 등록할 분류 선택이 잘못되었습니다.')\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('상품을 등록할 분류 선택이 잘못되었습니다.')\"></body></html>";exit;
	}
	pmysql_free_result($result);

	$productname=$_POST["productname"];
	if($productname){	//2016 09 19 상품이름 DB에 들가는 형태에 맞춤
		$productname = pmysql_escape_string($productname);
	}

	
	# 정보고시 2015 10 23 유동혁
	$sabangnet_prop_val=$_POST["sabangnet_prop_val"];
	$sabangnet_prop_option = $_POST['sabangnet_prop_option'];

	#####사이즈 조견표 사용조건
	$use_prsize = $_POST['use_prsize'];

    ##### 옵션 변경 2016-02-27 유동혁
    $opt_select    = $_POST['opt_select']; // 옵션사용
	$p_goods_code    =  $_POST['p_goods_code']; // 옵션 없을시 자체품목코드(20160610_김재수 추가)
    $opt_type      = $_POST['opt_type']; // 옵션 구성방식 0 - 조합형, 1 - 독립형
    # 조합형 옵션 2016-02-28유동혁
    $opt_num       = $_POST['opt_num']; // 옵션 고유번호
    $opt_subject   = $_POST['opt_subject']; // 옵션명
    $opt_id        = $_POST['opt_id']; // 옵션코드
    $opt_price     = $_POST['opt_price']; // 옵션가
    $opt_stock_qty = $_POST['opt_stock_qty']; // 옵션 제고
    $opt_noti_qty  = $_POST['opt_noti_qty']; // 옵션 안전제고
    $opt_use       = $_POST['opt_use']; //옵션 사용 유무
    # 고정옵션 2016-02-28유동혁
    $spl_num             = $_POST['spl_num']; // 옵션 고유번호
    $spl_option_code     = $_POST['spl_id'];	//옵션코드
    $spl_option_price    = $_POST['spl_price'];	//가격
    $spl_option_quantity = $_POST['spl_stock_qty'];	//수량
    $spl_option_noti_qty = $_POST['spl_noti_qty']; // 안전재고
    $spl_option_use      = $_POST['spl_use'];	//사용 유무
    $spl_option_subject  = $_POST['spl_subject']; // 옵션명
    $spl_option_name     = $_POST['spl'];	//항목명
    $spl_tf              = $_POST['spl_tf']; //필수여부
    $option1_tf          = $_POST['option1_tf']; // 필수여부
    # 추가 옵션 2016-02-28유동혁
    $addopt_select  = $_POST['addopt_select']; // 추가옵션 사용 1, null일경우 미사용
    $addopt_subject = $_POST['addopt_subject']; // 추가옵션명
    $addopt_type    = $_POST['addopt_type']; // 필수항목 선택 T - 필수, F - 선택
    $addopt_maxln   = $_POST['addopt_maxln']; // 글자수 제한
    # 옵션 자체 코드 2016-05-18
    $opt_goods_code = $_POST['opt_goods_code']; //조합형 옵션 자체코드
    $spl_goods_code = $_POST['spl_goods_code']; //고정 옵션 자체코드

    #mdcomment 추가 2016-03-10 유동혁
    $mdcomment = pmysql_escape_string( $_POST["mdcomment"] );
    $up_mdcommentcolor = $_POST['mdcommentcolor'];

	# 카테고리 추가
	$category=$_POST["category"];
    # 마진률 추가 2016-03-21 유동혁
    $rate = $_POST['rate'];
    if( $rate > 100 ) $rate = 100;
    else if( $rate < 0 || $rate == '' ) $rate = 0;

	$consumerprice=$_POST["consumerprice"];
	$buyprice=$_POST["buyprice"];
	$sellprice=$_POST["sellprice"];
	$production=$_POST["production"];
	$keyword=$_POST["keyword"];
	$quantity=$_POST["quantity"];
    if( $quantity == '' ) $quantity = 0;
    #품절 추가 2016-03-09 유동혁
    $soldout = $_POST['soldout'];
    if( $soldout == '' ) $soldout = 'N';
	$checkquantity=$_POST["checkquantity"];
	$reserve=$_POST["reserve"];
	# rserve not-null
	if( is_null($reserve) || $reserve == '' ) $reserve = 0;
	$reservetype=$_POST["reservetype"];

	#배송비 설정 2016-02-17 유동혁
	$deli = $_POST["deli"];
	$deli_qty = (int)$_POST['deli_qty'];
	if( $deli_qty < 0 || is_null($deli_qty) ) $deli_qty = 0;
	$deli_select = $_POST['deli_select'];
	if( $deli_select < 0 || is_null($deli_select) ) $deli_select = 0;
	$deli_price = $_POST['deli_price'];
	if( $deli_price < 0 || is_null($deli_price) ) $deli_price = 0;

	$display=$_POST["display"];
	$addcode=$_POST["addcode"];
	//$option_price=str_replace(" ","",$_POST["option_price"]);
	//$option_price=rtrim($option_price,',');
	$madein=$_POST["madein"];
	$model=$_POST["model"];
	$brandname=$_POST["brandname"];
	$opendate=$_POST["opendate"];
	$selfcode=$_POST["selfcode"];
	$imgcheck=$_POST["imgcheck"];
	$deliinfono=$_POST["deliinfono"];	// 배송/교환/환불정보 노출안함 (Y)
	$checkmaxq=$_POST["checkmaxq"]; // 최대주문수량 무제한 / 수량제한
	$miniq=$_POST["miniq"];			// 최소주문가능
	$maxq=$_POST["maxq"];			// 최대주문가능
	$content=$_POST["content"];
	$content_m=$_POST["content_m"];
	$pr_notice=$_POST["pr_notice"];

	$userspec=$_POST["userspec"];
	$specname=$_POST["specname"];
	$specvalue=$_POST["specvalue"];

	$group_check=$_POST["group_check"];
	$group_code=$_POST["group_code"];

	if($group_check=="Y" && count($group_code)>0) {
		$group_check="Y";
	} else {
		$group_check="N";
		$group_code="";
	}

	$specarray=array();
	if($userspec == "Y") {
		for($i=0; $i<$userspec_cnt; $i++) {
			$specarray[$i]=$specname[$i]."".$specvalue[$i];
		}
		$userspec = implode("=",$specarray);
	} else {
		$userspec = "";
	}

	if(strlen($display)==0) $display="Y";
	
	if((int)$opendate<1) $opendate="";
	/* 옵션 변경 2015 10 23 유동혁
	$searchtype=$_POST["searchtype"];
	if(strlen($searchtype)==0) $searchtype=0;
	*/
	$userfile = $_FILES["userfile"];
	$userfile2 = $_FILES["userfile2"];
	$userfile3 = $_FILES["userfile3"];
    $userfile4 = $_FILES["userfile4"];

    $use_imgurl=$_POST["use_imgurl"];
    $userfile_url=$_POST["userfile_url"];
    $userfile2_url=$_POST["userfile2_url"];
    $userfile3_url=$_POST["userfile3_url"];
    $userfile4_url=$_POST["userfile4_url"];

    if($use_imgurl!="Y") {
        $userfile_url="";
        $userfile2_url="";
        $userfile3_url="";
        $userfile4_url="";
    }

    // ===================================================
    // 리뷰 배너 이미지
    // ===================================================
    $review_banner_img      = $_FILES["review_banner_img"];
    $old_review_banner_img  = $_POST["old_review_banner_img"];

    $review_banner_image = "";
    if ( !empty($review_banner_img['name']) ) {
        $reviewbannerimagepath  = $Dir.DataDir."shopimages/product_review_banner/";

        if ( !empty($old_review_banner_img) && file_exists($reviewbannerimagepath.$old_review_banner_img) ) {
            unlink($reviewbannerimagepath.$old_review_banner_img);
        }

        $ext = strtolower(pathinfo($review_banner_img['name'],PATHINFO_EXTENSION));
        
        if ( !empty($productcode) ) {
            $review_banner_image = "review_banner_{$productcode}_" . date("YmdHis") . ".".$ext;
        } else {
            $review_banner_image = "review_banner_" . date("YmdHis") . ".".$ext;
        }

        $tmp_image  = $review_banner_img['tmp_name'];

        move_uploaded_file($tmp_image,$reviewbannerimagepath.$review_banner_image);
        chmod($reviewbannerimagepath.$review_banner_image, 0604);
    }

	$etctype = "";
	# etctype 초기화
	$up_bankonly = 'N';
	$up_deliinfono = 'N';
	$up_setquota = 'N';
	$up_icon = '';
	$up_dicker = '';
	$up_miniq = 0;
	$up_maxq = 0;
	if ($bankonly=="Y") {
		$etctype .= "BANKONLY";
		$up_bankonly = 'Y';
	}
	if ($deliinfono=="Y") {
		$etctype .= "DELIINFONO=Y";
		$up_deliinfono = 'Y';
	}
	if ($setquota=="Y") {
		$etctype .= "SETQUOTA";
		$up_setquota = 'Y';
	}
	if (strlen(substr($iconvalue,0,3))>0) {
		$etctype .= "ICON=".$iconvalue."";
		$up_icon = $iconvalue;
	}
	if ($dicker=="Y" && strlen($dicker_text)>0) {
		$etctype .= "DICKER=".$dicker_text."";
		$up_dicker = $dicker_text;
	}

	if ($miniq>1) {
		$etctype .= "MINIQ=".$miniq."";
		$up_miniq = $miniq;
	} else if ($miniq<1) {
		echo "<html></head><body onload=\"alert('최소주문한도 수량은 1개 보다 커야 합니다.')\"></body></html>";exit;
	}
	if ($checkmaxq=="B" && $maxq>=1) {
		$etctype .= "MAXQ=".$maxq."";
		$up_maxq = $maxq;
	} else if ($checkmaxq=="B" && $maxq<1) {
		echo "<html></head><body onload=\"alert('최대주문한도 수량은 1개 보다 커야 합니다.')\"></body></html>";exit;
	}

	$imagepath=$Dir.DataDir."shopimages/product/";

	
    // 추가옵션의 이름을 합친다 2015 10 28 유동혁
    if( count($opt_subject) > 0 && $opt_type == '0' ){
        $tf_arr = array();
        $option1 = implode( '@#', $opt_subject  );
        for( $i=0; $i<count($opt_subject); $i++ ){
            $tf_arr[] = 'T';
        }
        $option1_tf = implode( '@#', $tf_arr );
    } else if( count($spl_option_subject) > 0 && $opt_type == '1' ) {
        $option1 = implode( '@#', $spl_option_subject );
    } else {
        $option1 = '';
        $option1_tf = '';
    }

    if ( $addopt_select == '1' && count( $addopt_subject ) > 0 ){
        $option2 = implode( '@#', $addopt_subject );
        $option2_maxlen = implode( '@#', $addopt_maxln );
        $option2_tf = implode( '@#', $addopt_type );
    } else {
        $option2 = '';
        $option2_maxlen = '';
        $option2_tf = '';

    }

	$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
	$sql.= "WHERE productcode LIKE '".$code."%' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($rows = pmysql_fetch_object($result)) {
		if (strlen($rows->maxproductcode)==18) {
			$productcode = ((int)substr($rows->maxproductcode,12))+1;
			$productcode = sprintf("%06d",$productcode);
		} else if($rows->maxproductcode==NULL){
			$productcode = "000001";
		} else {
			echo "<html></head><body onload=\"alert('상품코드를 생성하는데 실패했습니다. 잠시후 다시 시도하세요.')\"></body></html>";exit;
		}
		pmysql_free_result($result);
	}else {
		$productcode = "000001";
	}

//	$image_name = $code.$productcode;

    $image_dir = $code.$productcode;
    $image_name = $code.$productcode."_".date("YmdHis");
	$p_goods_code	= $p_goods_code?$p_goods_code:$code.$productcode; // 옵션 없을시 자체품목코드 - 자체품목코드를 입력안했을경우에는 상품코드로 (20160610_김재수 추가)
	$p_goods_code  = $opt_select!='1'?$p_goods_code:''; // 옵션 없을시 자체품목코드 있을시에는 공백(20160610_김재수 추가)
	list($p_goods_code_cnt)=pmysql_fetch_array(pmysql_query(" select count(*) from ( 
													select '1' as op_type, vender, productcode, self_goods_code, pridx as idx from tblproduct where self_goods_code !='' 
													union select '2' as op_type, tp.vender, tp.productcode, tpo.self_goods_code, tpo.option_num as idx from tblproduct_option as tpo left join tblproduct as tp on tpo.productcode=tp.productcode where tpo.self_goods_code !='' 
												) AS a where vender='".$_VenderInfo->getVidx()."' AND self_goods_code='{$p_goods_code}' "));
	if ($p_goods_code_cnt > 0) {
		echo "<html></head><body onload=\"alert('존재하는 자체품목 코드 입니다.')\"></body></html>";exit;
	}

	if($use_imgurl!="Y") {
        $file_size = $userfile['size']+$userfile2['size']+$userfile3['size']+$userfile4['size'];
    } else {
        $file_size=0;
    }

	if($file_size < $maxfilesize) {
		if (strlen($reserve)==0) {
			$reserve=0;
		} else {
			$reserve=$reserve*1;
		}

		if ($reservetype!="N") {
			$reservetype=="Y";
		}

		$curdate = date("YmdHis");

		$productname = str_replace("\\\\'","''",$productname);
		$addcode = str_replace("\\\\'","''",$addcode);
		$content = str_replace("\\\\'","''",$content);
		$content_m = str_replace("\\\\'","''",$content_m);
		$pr_notice = str_replace("\\\\'","''",$pr_notice);

		$message="";

		if($use_imgurl!="Y") {
            if($imgcheck=="Y") $filename = array (&$userfile['name'],&$userfile['name'],&$userfile['name'],&$userfile['name']);
            else $filename = array (&$userfile['name'],&$userfile2['name'],&$userfile3['name'],&$userfile4['name']);
            $file = array (&$userfile['tmp_name'],&$userfile2['tmp_name'],&$userfile3['tmp_name'],&$userfile4['tmp_name']);
        } else {
            if($imgcheck=="Y") $filename = array (&$userfile_url,&$userfile_url,&$userfile_url,&$userfile_url);
            else $filename = array (&$userfile_url,&$userfile2_url,&$userfile3_url,&$userfile4_url);
            $file = array (&$userfile_url,&$userfile2_url,&$userfile3_url,&$userfile4_url);
        }
		$vimagear = array (&$vimage,&$vimage2,&$vimage3,&$vimage4);
		//$imgnum = array ("","2","3");
		
		#DB에 올릴 이미지 경로
		$up_ImagePath = array ( '', '', '', '', '' );
		# 이미지 지정사이즈 ( 유동혁 2015 10 29 );
		$thumbnailArr = array( 
			1=>array('width'=>500,'height'=>500),
			2=>array('width'=>500,'height'=>500), 
			3=>array('width'=>500,'height'=>500),
            4=>array('width'=>500,'height'=>500)
		);

		# 이미지 폴더 생성
		if( !is_dir( $imagepath.$image_dir) ){
			mkdir( $imagepath.$image_dir, 0700 );
			chmod( $imagepath.$image_dir, 0777 );
		}
		// 경로지정
		$imagepath2=$Dir.DataDir."shopimages/product/".$image_dir."/";

		# 대이미지 수정일경우 원본이미지를 삭제 후 올림
		/*
		if ($mode=="modify" && ord($vimagear[0]) && ord($filename[0]) && file_exists($imagepath.$vimagear[0]) && file_exists() ) {
			unlink( $Dir.DataDir."shopimages/product/".$vimagear[0] );
		}
		*/
		# 원본 이미지 업로드
		if (ord($filename[0]) && file_exists($file[0])) {
			$ext = strtolower(pathinfo($filename[0],PATHINFO_EXTENSION));
			if(in_array($ext,array('gif','jpg'))) {
				$image[0] = $image_name.".".$ext;
//				move_uploaded_file($file[0],$imagepath2.$image[0]);
                copy($file[0],$imagepath2.$image[0]);
				chmod($imagepath2.$image[0],0777);
			} else {
				$image[0]="";
			}
		} else {
			$image[0] = $vimagear[0];
		}
		
		#썸네일 생성
		if( $imgcheck=="Y" && ord( $image[0] ) ){
			for( $i = 1; $i < 5; $i++ ){
                # 기존 이미지가 존재할경우 삭제하고 넣는다
                if ($mode=="modify" && ord($vimagear[($i-1)]) && file_exists($imagepath.$vimagear[($i-1)])) { 
                    unlink( $Dir.DataDir."shopimages/product/".$vimagear[($i-1)] );
                }

				if (ord($filename[($i-1)]) && file_exists($file[($i-1)])) {	//사용자 이미지 넣기
					$ext = strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION));
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name."_thum".$i."_".$thumbnailArr[$i]['width']."X".$thumbnailArr[$i]['height'].".".$ext;
						move_uploaded_file($file[($i-1)],$imagepath2.$image[$i]);
						chmod($imagepath2.$image[$i],0777);
						$up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
					} else {
						$image[$i]="";
					}
				} else { // 썸네일 생성
					$image[$i] = $image_name."_thum".$i."_".$thumbnailArr[$i]['width']."X".$thumbnailArr[$i]['height'].".".$ext;
					copy($imagepath2.$image[0],$imagepath2.$image[$i]);
					# 썸네일 이미지 크기 리사이징
					ProductThumbnail ( $image_name, $filename[0], $image[$i], $thumbnailArr[$i]['width'],  $thumbnailArr[$i]['height'], $imgborder, $setcolor );
					$up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
				}
			}
		} else { // 개별 업로드
			for( $i = 1; $i < 5; $i++ ){
				if (ord($filename[($i-1)]) && file_exists($file[($i-1)])) {	//사용자 이미지 넣기
					# 기존 이미지가 존재할경우 삭제하고 넣는다
					if ($mode=="modify" && ord($vimagear[($i-1)]) && file_exists($imagepath.$vimagear[($i-1)])) { 
						unlink( $Dir.DataDir."shopimages/product/".$vimagear[($i-1)] );
					}
					$ext = strtolower(pathinfo($filename[($i-1)],PATHINFO_EXTENSION));
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name."_thum_".$i.".".$ext;
						move_uploaded_file($file[($i-1)],$imagepath2.$image[$i]);
						chmod($imagepath2.$image[$i],0777);
						$up_ImagePath[$i] = $image_dir."/"; //DB에 업로드한 경로를 같이 넣어준다
					} else {
						$image[$i]="";
					}
				} else {
					$image[$i] = $vimagear[($i-1)];
				}
			}
		}

		# URL 업로드
		if( $use_imgurl == 'Y' ) {
			for( $i = 1; $i < 5; $i++ ){
				if(ord($filename[($i-1)]) && ord($file[($i-1)])) {
					$up_ImagePath[$i]	= $file[($i-1)];
					$image[$i]				= "";
					/*$image_url=str_replace("http://","",$file[($i-1)]);
					$temp=explode("/",$image_url);
					$host=$temp[0];
					$path=str_replace($host,"",$image_url);

					$ext = strtolower(pathinfo($image_url,PATHINFO_EXTENSION));

					$is_upimage=true;
					if(in_array($ext,array('gif','jpg'))) {
						$image[$i] = $image_name."_thum_".$i.".".$ext;
						//$image[$i] = $image_name.$imgnum[$i].".".$ext;
						$fdata=getRemoteImageData($host,$path,$ext);

						if(ord($fdata)) {
							file_put_contents($imagepath2.$image[$i],$fdata);
							chmod($imagepath2.$image[$i],0777);
							$up_ImagePath[$i] = $image_name."/"; //DB에 업로드한 경로를 같이 넣어준다
							$tempsize=@getimagesize($imagepath2.$image[$i]);
							if($tempsize[0]>0 && $tempsize[1]>0 && (strstr("12",$tempsize[2]))) {

							} else {
								@unlink($imagepath2.$image[$i]);
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
						$filename[($i-1)]="";
					}*/
				} else if($imgcheck=="Y" && ord($filename[($i-1)])) {
					$up_ImagePath[$i]	= $file[0];
					$image[$i]				= "";
					/*$image[$i]=$image_name."_thum_".$i.".".$ext;
					copy($imagepath2.$image[0],$imagepath.$image[$i]);
					chmod($imagepath2.$image[$i],0777);
					$up_ImagePath[$i] = $image_name."/"; //DB에 업로드한 경로를 같이 넣어준다*/
				} else {
					$image[$i] = $vimagear[($i-1)];
				}
			}
		}

		if($checkquantity=="F") $quantity=999999999;

		if($optiongroup>0) {
			$option1="[OPTG".$optiongroup."]";
			$option2="";
			$option_price="";
		}

		if(strlen($buyprice) < 1 ) $buyprice = 0 ;
		$result = pmysql_query("SELECT COUNT(*) as cnt FROM tblproduct",get_db_conn());
		if ($row=pmysql_fetch_object($result)) $cnt = $row->cnt;
		else $cnt=0;
		pmysql_free_result($result);

		// productlink 테이블 입력 추가
		$date1=date("Ym");
		$date=date("dHis");

		$in=0;
		foreach($category as $k){
			if($in==0){
				$maincate="1";
			}else{
				$maincate="0";
			}
			$query="insert into tblproductlink (c_productcode,c_category,c_maincate,c_date,c_date_1,c_date_2,c_date_3,c_date_4) values ('".$code.$productcode."','".$k."','".$maincate."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."')";

			pmysql_query($query);
			$in++;
		}

		$sql = "INSERT INTO tblproduct(productcode) VALUES ('".$code.$productcode."')";
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblproduct SET ";
		$sql.= "assembleuse		= 'N', ";
		$sql.= "assembleproduct	= '', ";
		$sql.= "productname		= '".$productname."', ";
		$sql.= "sellprice		= ".$sellprice.", ";
		$sql.= "consumerprice	= ".$consumerprice.", ";
		$sql.= "buyprice		= ".$buyprice.", ";
		$sql.= "reserve			= '".$reserve."', ";
		$sql.= "reservetype		= '".$reservetype."', ";
		$sql.= "production		= '".$production."', ";
		$sql.= "madein			= '".$madein."', ";
		$sql.= "model			= '".$model."', ";
		$sql.= "opendate		= '".$opendate."', ";
		$sql.= "selfcode		= '".$selfcode."', ";
		$sql.= "quantity		= ".$quantity.", ";
        $sql.= "soldout         = '".$soldout."', ";
		$sql.= "group_check		= '".$group_check."', ";
		$sql.= "keyword			= '".$keyword."', ";
		$sql.= "addcode			= '".$addcode."', ";
		$sql.= "userspec		= '".$userspec."', ";
		$sql.= "maximage		= '".$up_ImagePath[1].$image[1]."', ";
		$sql.= "minimage		= '".$up_ImagePath[2].$image[2]."', ";
		$sql.= "tinyimage		= '".$up_ImagePath[3].$image[3]."', ";
        $sql.= "over_minimage	= '".$up_ImagePath[4].$image[4]."', ";
        
        # 리뷰베너 이미지 추가 2016-03-10 유동혁
        if ( empty($review_banner_image) && empty($old_review_banner_img) ) {
            // 리뷰 상단 배너 이미지가 삭제된 경우
            $reviewbannerimagepath  = $Dir.DataDir."shopimages/product_review_banner/";
            $old_img = $row->review_banner_img;

            // 실제로 업로드 된 이미지 삭제
            if ( !empty($old_img) && file_exists($reviewbannerimagepath.$old_img) ) {
                unlink($reviewbannerimagepath.$old_img);
            }

            // 필드 업데이드
            $sql.= "review_banner_img   = '', ";
        } elseif ( !empty($review_banner_image) ) {
            $sql.= "review_banner_img   = '".$review_banner_image."', ";
        }

		# 옵션관련 내용 변경 2015 10 23 유동혁
		$sql.= "option_quantity	= '".$optcnt."', ";
		$sql.= "option1			= '".$option1."', ";
		$sql.= "option2			= '".$option2."', ";
		# 추가옵션 추가 2015 10 28 유동혁
		$sql.= "supply_subject  = '".$supply_subject."', ";
		# 정보고시 추가
		$sql.= "sabangnet_prop_val		= '".$sabangnet_prop_val."', ";
		$sql.= "sabangnet_prop_option	= '".$sabangnet_prop_option."', ";

		$sql.= "etctype			= '".$etctype."', ";
		# etctype 내용 추가 2015 10 28 유동혁
		$sql.= "bankonly	= '{$up_bankonly}', ";
		$sql.= "deliinfono = '{$up_deliinfono}', ";
		$sql.= "icon =  '{$up_icon}', ";
		$sql.= "dicker = '{$up_dicker}', ";
		$sql.= "min_quantity = '{$up_miniq}', ";
		$sql.= "max_quantity = '{$up_maxq}', ";
		$sql.= "setquota = '{$up_setquota}', ";
        #2016-03-10 옵션 추가 유동혁
        $sql.= "option_type = '".$opt_type."', ";
		$sql.= "option1_tf = '".$option1_tf."', ";
		if( $addopt_select == '1' ){
			$sql.= "option2_tf		= '{$option2_tf}', ";
			$sql.= "option2_maxlen			= '{$option2_maxlen}', ";
		}
        #mdcomment 추가 2016-03-10 유동혁
        $sql.= "mdcomment		= '".$mdcomment."',";
		$sql.= "mdcommentcolor	= '".$up_mdcommentcolor."', ";

		$sql.= "deli_price		= '".$deli_price."', ";
		$sql.= "deli			= '".$deli."', ";
		$sql.= "deli_qty		= '".$deli_qty."', ";
		$sql.= "deli_select		= '".$deli_select."', ";
		if($_venderdata->grant_product[3]=="N") {
			$sql.= "display		= '".$display."', ";
		} else {
			$display="N";
			$sql.= "display		= 'N', ";	
		}
		//자체품목코드 추가(20160610_김재수 추가)
		$sql.= "self_goods_code			= '{$p_goods_code}', ";
        # rate 추가 2016-03-21 유동혁
        $sql.= "rate            = '".$rate."', ";
		$sql.= "date			= '".$curdate."', ";
		$sql.= "vender			= '".$_VenderInfo->getVidx()."', ";
		$sql.= "regdate			= now(), ";
		$sql.= "modifydate		= now(), ";
		$sql.= "pr_notice			= '".pmysql_escape_string($pr_notice)."', ";
		$sql.= "content			= '".pmysql_escape_string($content)."', ";
		$sql.= "content_m		= '".pmysql_escape_string($content_m)."' WHERE productcode='".$code.$productcode."' ";
		
		if($insert = pmysql_query($sql,get_db_conn())) {

			product_related('update',$code.$productcode,$productname); //관련상품이 있을경우 관련상품 등록하여줍니다.

			product_size('up',$code.$productcode,$use_prsize); //사이즈 조견표 등록하여줍니다.

			##### 브랜드 관련 처리	
			//---------------------------------------------------//
			// 벤더에 해당하는 브랜드를 등록한다.
			// 그리고 상품별 노출 브랜드에도 등록한다.
			// 2016.01.22 - 김재수

			list($bridx)=pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='".$_VenderInfo->getVidx()."'");
			if ($bridx>0) {
				@pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$code.$productcode."'",get_db_conn());

				$bpSql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$code.$productcode."','".$bridx."','1')";			
				pmysql_query($bpSql,get_db_conn());
			}			

			/*if(strlen($brandname)>0) { // 브랜드 관련 처리
				$result = pmysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '".$brandname."' ",get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					@pmysql_query("UPDATE tblproduct SET brand = '".$row->bridx."' WHERE productcode = '".$code.$productcode."'",get_db_conn());
				} else {
					$sql = "INSERT INTO tblproductbrand(brandname) VALUES ('".$brandname."') RETURNING bridx";
					if($row = @pmysql_fetch_array(pmysql_query($sql,get_db_conn()))) {
						$bridx = $row[0];
						if($bridx>0) {
							@pmysql_query("UPDATE tblproduct SET brand = '".$bridx."' WHERE productcode = '".$code.$productcode."'",get_db_conn());
						}
					}
				}
				pmysql_free_result($result);
			}*/

			if($group_check=="Y" && count($group_code)>0) {
				for($i=0; $i<count($group_code); $i++) {
					$sql = "INSERT INTO tblproductgroupcode(productcode,group_code) VALUES (
					'".$code.$productcode."', 
					'".$group_code[$i]."')";
					pmysql_query($sql,get_db_conn());
				}
			}

			$sql = "UPDATE tblvenderstorecount SET prdt_allcnt=prdt_allcnt+1 ";
			if($display=="Y") {
				$sql.= ",prdt_cnt=prdt_cnt+1 ";
			}
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			pmysql_query($sql,get_db_conn());
/*			필요없는 기능 2015 10 23 유동혁
			$sql = "INSERT INTO tblvendercodedesign(
			vender		,
			code		,
			tgbn		,
			hot_used	,
			hot_dispseq	) VALUES (
			'".$_VenderInfo->getVidx()."', 
			'".substr($code,0,3)."', 
			'10', 
			'1', 
			'118')";
			@pmysql_query($sql,get_db_conn());
*/

			###   옵션 등록 / 수정   ###
			if ( ( $mode=="insert" && $insert ) || ( $mode=="modify" && $update ) ){
				$upOptQty    = 0;
				$optNumArr   = array();
				$opt_success = true;
				$errMsg      = '';
				try {
					BeginTrans();
					if( $opt_select == '1' ){
						if( strlen( $prcode ) == 0 ) $goods_code = $code.$productcode;
						else $goods_code = $prcode;
						
						if( $opt_type == '0' && count( $opt_id ) > 0 ){
							// 조합형
							$update_num           = $opt_num;
							$option_code          = $opt_id;
							$option_price         = $opt_price;
							$option_quantity      = $opt_stock_qty;
							$option_quantity_noti = $opt_noti_qty;
							$option_use           = $opt_use;
							$option_tf            = array();
							$option_type          = $opt_type;
							$self_goods_code      = $opt_goods_code;
						} else if( $opt_type == '1' && count( $spl_option_code ) > 0 ) {
							// 독립형
							$update_num           = $spl_num;
							$option_code          = $spl_option_code;
							$option_price         = $spl_option_price;
							$option_quantity      = $spl_option_quantity;
							$option_quantity_noti = $spl_option_noti_qty;
							$option_use           = $spl_option_use;
							$option_tf            = $spl_tf;
							$option_type          = $opt_type;
							$self_goods_code      = $spl_goods_code;
						} else {
							$opt_success = false;
							throw new Exception( '옵션목록이 존재하지 않습니다. 다시 시도해 주시기 바랍니다.' );
						}
						if( count( $option_code ) > 0 && $opt_success ){
							foreach( $option_code as $optKey=>$optVal ){
								// self_code
								if( $opt_type == '0' ) $option_tf[$optKey] = 'T';
								if( $mode=="modify" && $update_num[$optKey] != '' ) {
									$option_num = $update_num[$optKey];
								} else {
									// 다음 seq의 값을 가져온다
									$seq_sql = "SELECT nextval( 'tblproduct_option_option_num_seq' ) AS seq_num ";
									$seq_res = pmysql_query( $seq_sql, get_db_conn() );
									$seq_row = pmysql_fetch_row( $seq_res );
									pmysql_free_result( $seq_res );
									if( pmysql_error() ){
										$opt_success = false;
										throw new Exception( "옵션목록생성 실패.다시 시도해 주시기 바랍니다." );
										break;
									} else {
										$option_num = $seq_row[0];
									}
								}
								// self_goods_code 확인
								if( strlen( trim( $self_goods_code[$optKey] ) ) > 0 ) {
									$goods_sql = "select count(*) from ( 
															select '1' as op_type, vender, productcode, self_goods_code, pridx as idx from tblproduct where self_goods_code !='' 
															union select '2' as op_type, tp.vender, tp.productcode, tpo.self_goods_code, tpo.option_num as idx from tblproduct_option as tpo left join tblproduct as tp on tpo.productcode=tp.productcode where tpo.self_goods_code !='' 
														) AS a where vender='".$_VenderInfo->getVidx()."' AND self_goods_code='".$self_goods_code[$optKey]."' ";
									if( $update_num[$optKey] != '' ) $goods_sql .= "AND (op_type ='1' OR (op_type ='2' AND idx != '".$option_num."')) ";
									$goods_res = pmysql_query( $goods_sql, get_db_conn() );
									$goods_row = pmysql_fetch_row( $goods_res );
									pmysql_free_result( $goods_res );
									if( $goods_row[0] > 0 ){
										$opt_success = false;
										throw new Exception( "옵션 등록/수정 실패.존재하는 자체품목 코드 입니다." );
										break;
									}
								} else { // 옵션 self코드를 입력 안할시
									$self_goods_code[$optKey] = $goods_code.'_'.$option_num;
								}
								// 옵션가 예외처리
								if( $option_price[$optKey] == "" || is_null($option_price[$optKey]) ) $option_price[$optKey] = 0;
								// 옵션 수량 예외처리
								if( $option_quantity[$optKey] == "" || is_null($option_quantity[$optKey]) ) $option_quantity[$optKey] = 0;
								// 옵션 공지 수량 예외처리
								if( $option_quantity_noti[$splKey] == "" || is_null($option_quantity_noti[$splKey]) ) $option_quantity_noti[$splKey] = 0;

								if ( $mode=="modify" && $update_num[$optKey] != '' ) { // 옵션 내부 값만 수정시
									$optUpdateSql = "UPDATE tblproduct_option SET option_price='".$option_price[$optKey]."',option_quantity='".$option_quantity[$optKey]."', ";
									$optUpdateSql.= "option_quantity_noti='".$option_quantity_noti[$optKey]."',option_use='".$option_use[$optKey]."', ";
									$optUpdateSql.= "option_tf = '".$option_tf[$optKey]."', self_goods_code ='".$self_goods_code[$optKey]."' ";
									$optUpdateSql.= "WHERE option_num='".$update_num[$optKey]."' ";
									$optUpdateSql.= "RETURNING option_num ";
									$returnOption = pmysql_fetch_object( pmysql_query( $optUpdateSql, get_db_conn() ) );
									if( pmysql_error() ){
										$opt_success = false;
										throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
										break;
									} else {
										$optNumArr[] = $returnOption->option_num;
										$upOptQty += $option_quantity[$optKey];
									}
								} else { // 옵션 입력
									$optInsertSql = "INSERT INTO tblproduct_option ";
									$optInsertSql.= "( option_num, option_code, productcode, option_price, ";
									$optInsertSql.= " option_quantity, option_quantity_noti, option_use, option_tf, ";
									$optInsertSql.= " option_type, self_goods_code ) ";
									$optInsertSql.= "VALUES ( '".$option_num."', '".$optVal."', '".$goods_code."', '".$option_price[$optKey]."', ";
									$optInsertSql.= " '".$option_quantity[$optKey]."', '".$option_quantity_noti[$optKey]."', '".$option_use[$optKey]."', '".$option_tf[$optKey]."', ";
									$optInsertSql.= " '".$option_type."', '".$self_goods_code[$optKey]."' ) ";
									pmysql_query($optInsertSql, get_db_conn());
									if( pmysql_error() ){
										$opt_success = false;
										throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
										break;
									} else {
										$optNumArr[] = $option_num;
										$upOptQty += $option_quantity[$optKey];
									}
								}
							}
							// 수정시 옵션 삭제
							if( $mode=="modify" ){
								$tmpNum = implode( ',', $optNumArr );
								$sql = "DELETE FROM tblproduct_option WHERE productcode = '".$goods_code."' ";
								if( $tmpNum != '' ) $sql.= "AND option_num NOT IN ( ".$tmpNum." ) ";
								pmysql_query( $sql, get_db_conn() );
								if( pmysql_error() ){
									$opt_success = false;
									throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
								}
							}
							// 제고 기준을 option quantity에 맞춘다
							if( $checkquantity=="C" && $opt_type == '0' && $opt_success === true ) {
								pmysql_query( "UPDATE tblproduct SET quantity = '".$upOptQty."' WHERE productcode='".$goods_code."'", get_db_conn() );
								if( pmysql_error() ){
									$opt_success = false;
									throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
								}
							}
						} else { // 값이 존재 안함
							$opt_success = false;
							throw new Exception( "옵션 등록/수정 실패.다시 시도해 주세요." );
						}
					}
				} catch( Exception $e ) {
					$errMsg .= $e->getMessage();
					RollbackTrans();
				}
				
				if( $opt_success ){
					CommitTrans();
				}

				if( !$opt_success && $errMsg != '' ) {
					$onload = "<script>alert(' ".$errMsg." ');</script>";
				}
			}
			### 옵션 등록 / 수정 END ###
			#### 기타 파일 업로드 STR ####
            for($i=1;$i<=10;$i++){
                $img_new		= "mulimg".sprintf("%02d",$i);
                $img_url_new	= "mulimg".sprintf("%02d",$i)."_url";
                $img_old		= "oldimg".sprintf("%02d",$i);

                ${$img_new} = $_FILES["$img_new"];
                ${$img_url_new} = $_POST["$img_url_new"];
                ${$img_old} = $_POST["$img_old"];
            }
            $multiimagepath=$Dir.DataDir."shopimages/multi/";
            if ($mode=="insert" || $mode=="modify" ) {
                if(strlen($productcode) < 18 && $mode=="insert" ) $productcode = $code.$productcode;
                $query = " select count(*) from tblmultiimages where productcode='".$productcode."' ";
                $result = pmysql_query($query,get_db_conn());
                list($multiimage_cnt) = pmysql_fetch_array($result);
                $mode=!$multiimage_cnt?"insert":$mode;



                $file_url = array ("01"=>&$mulimg01_url,"02"=>&$mulimg02_url,"03"=>&$mulimg03_url,"04"=>&$mulimg04_url,"05"=>&$mulimg05_url,"06"=>&$mulimg06_url,"07"=>&$mulimg07_url,"08"=>&$mulimg08_url,"09"=>&$mulimg09_url,"10"=>&$mulimg10_url);
                $oldfile = array ("01"=>&$oldimg01,"02"=>&$oldimg02,"03"=>&$oldimg03,"04"=>&$oldimg04,"05"=>&$oldimg05,"06"=>&$oldimg06,"07"=>&$oldimg07,"08"=>&$oldimg08,"09"=>&$oldimg09,"10"=>&$oldimg10);
                $filearray = array ("01"=>&$mulimg01["name"],"02"=>&$mulimg02["name"],"03"=>&$mulimg03["name"],"04"=>&$mulimg04["name"],"05"=>&$mulimg05["name"],"06"=>&$mulimg06["name"],"07"=>&$mulimg07["name"],"08"=>&$mulimg08["name"],"09"=>&$mulimg09["name"],"10"=>&$mulimg10["name"]);
                $filen = array ("01"=>&$mulimg01["tmp_name"],"02"=>&$mulimg02["tmp_name"],"03"=>&$mulimg03["tmp_name"],"04"=>&$mulimg04["tmp_name"],"05"=>&$mulimg05["tmp_name"],"06"=>&$mulimg06["tmp_name"],"07"=>&$mulimg07["tmp_name"],"08"=>&$mulimg08["tmp_name"],"09"=>&$mulimg09["tmp_name"],"10"=>&$mulimg10["tmp_name"]);
                if ($mode=="insert") {
                    $sql = "INSERT INTO tblmultiimages(productcode) VALUES ('{$productcode}')";
                    pmysql_query($sql,get_db_conn());
                } else {
                    $sql = "SELECT size FROM tblmultiimages WHERE productcode = '{$productcode}' ";
                    $result = pmysql_query($sql,get_db_conn());
                    if ($row = pmysql_fetch_object($result)){
                        if (strlen($row->size)!=0){
                            $tmpsize=explode("",$row->size);
                            $delsize = array("01"=>&$tmpsize[0],"02"=>&$tmpsize[1],"03"=>&$tmpsize[2],"04"=>&$tmpsize[3],"05"=>&$tmpsize[4],"06"=>&$tmpsize[5],"07"=>&$tmpsize[6],"08"=>&$tmpsize[7],"09"=>&$tmpsize[8],"10"=>&$tmpsize[9]);
                        }
                    }
                }
                $sql = "UPDATE tblmultiimages SET ";
                $file_size=0;

                for($i=1;$i<=10;$i++){
                    $gbn=sprintf("%02d",$i);
                    $image="";
                    
                    if (ord($file_url[$gbn])) {
                        if (ord($file_url[$gbn])) {
                            $image=$file_url[$gbn];
                        } else if (ord($oldfile[$gbn])) {
                            $image=$oldfile[$gbn];
                        }
                    } else {
                        if (ord($filearray[$gbn])) {
                            if (ord($filearray[$gbn]) && file_exists($filen[$gbn])) {
                                $ext = strtolower(pathinfo($filearray[$gbn],PATHINFO_EXTENSION));
                                $image = $gbn."_{$productcode}.".$ext;
                                $imgname=$multiimagepath."s".$image;
                                $file_size += filesize($filen[$gbn]);
                                if($mode=="modify" && ord($oldfile[$gbn])) {
                                    proc_matchfiledel($multiimagepath."*".$oldfile[$gbn]);
                                }
                                move_uploaded_file($filen[$gbn],$multiimagepath.$image);
                                chmod($multiimagepath.$image, 0604);
                                copy($multiimagepath.$image,$imgname);
                                chmod($imgname, 0604);
                                $size=getimageSize($imgname);
                                $width=$size[0];
                                $height=$size[1];
                                $imgtype=$size[2];
                                $maxsize=90;
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
                        } else if (ord($oldfile[$gbn])) {
                            $image=$oldfile[$gbn];
                        }
                    }
                    if (ord($image)) {
                        if ($mode=="insert") {
                            $sql.= "primg{$gbn} = '{$image}',";
                            if(ord($file_url[$gbn])) {						
                                $imgsize.="";
                            } else {
                                $imgsize.="{$width}X".$height;
                            }
                        } else {
                            $sql.= "primg{$gbn} = '{$image}',";
                            if(ord($file_url[$gbn])) {						
                                $imgsize.="";
                            } else {
                                if (ord($filearray[$gbn])) $imgsize.="{$width}X".$height;
                                else $imgsize.="".$delsize[$gbn];
                            }
                        }
                    } else {
                        $sql.= "primg{$gbn} = '',";
                        $imgsize.="";
                    }
                }
                $imgsize=substr($imgsize,1);
                $sql.= "size = '{$imgsize}' ";
                $sql.= " WHERE productcode = '{$productcode}' ";

                pmysql_query($sql,get_db_conn());
            }
            #### 기타 파일 업로드 END ####


            // 2016-01-04 jhjeong 사방넷 정옥정씨 요청으로 onload 태그 body 사이에 상품코드 추가 처리.
			$onload="<html></head><body onload=\"alert('상품 등록이 완료되었습니다.');parent.location.href='".$_SERVER[PHP_SELF]."'\">".$productcode."</body></html>";

			$log_content = "## 상품입력 ## - 코드 $code$productcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content);
		} else {
			$onload="<html></head><body onload=\"alert('상품 등록중 오류가 발생하였습니다.')\"></body></html>";
		}
		$prcode=$code.$productcode;
	} else {
		$onload="<html></head><body onload=\"alert('상품이미지의 총 용량이 ".ceil($file_size/1024)
		."Mbyte로 3M가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 3M입니다.\\n\\n"
		."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.')\"></body></html>";
	}

	echo $onload; exit;
}

include("header.php"); 

#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
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


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";

$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";

// 스크립트 작성완료

?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="PrdtRegist.js.php"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="<?=$Dir?>js/jscolor.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>
<script language="JavaScript">

// 카테고리 선택 추가 2015 10 26 유동혁
function exec_add()
{

	var ret;
	var str = new Array();
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
		url: "../admin/product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
	if(msg=='nocate'){
		alert("상품카테고리 선택이 잘못되었습니다.");
//		$("#catenm").html(msg);

	}else if(msg=='nolowcate'){
		alert("하위카테고리가 존재합니다.");
	//	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
	}else{
	document.form1.code.value=sumcode;
	var code_a=document.getElementById("code_a");
	var code_b=document.getElementById("code_b");
	var code_c=document.getElementById("code_c");
	var code_d=document.getElementById("code_d");

	if(code_a.value){
		str[0]=code_a.options[code_a.selectedIndex].text;
	}
	if(code_b.value){
		str[1]=code_b.options[code_b.selectedIndex].text;
	}
	if(code_c.value){
		str[2]=code_c.options[code_c.selectedIndex].text;
	}
	if(code_d.value){
		str[3]=code_d.options[code_d.selectedIndex].text;
	}
	var obj = document.getElementById('Category_table');
	oTr = obj.insertRow();

	oTd = oTr.insertCell(0);
	oTd.id = "cate_name";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell(1);
	oTd.innerHTML = "\
	<input type=text name=category[] value='" + sumcode + "' style='display:none'>\
	";
	oTd = oTr.insertCell(2);
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../admin/img/btn/btn_cate_del01.gif' align=absmiddle></a>";


	}

	});
}
// 카테고리 삭제 추가 2015 10 26 유동혁
function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('Category_table');
	obj.deleteRow(idx);
}


function formSubmit(mode) {
	var sHTML = oEditors.getById["ir1"].getIR();
	var sHTML_m = oEditors_m.getById["ir2"].getIR();
	var sHTML_pr_notice = oEditors_pr_notice.getById["ir3"].getIR();
	document.form1.content.value=sHTML;
	document.form1.content_m.value=sHTML_m;
	document.form1.pr_notice.value=sHTML_pr_notice;
	if( document.form1.code.value.length < 3 ) {
		codelen=document.form1.code.value.length;
		if(codelen==0) {
			alert("상품을 등록할 대분류를 선택하세요.");
			document.form1.code1.focus();
		} 
		return;
	}
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
		alert("소비자가격을 입력하세요.");
		document.form1.consumerprice.focus();
		return;
	}
	if (isNaN(document.form1.consumerprice.value)) {
		alert("소비자가격을 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
	if (document.form1.sellprice.value.length==0) {
		alert("판매가격을 입력하세요.");
		document.form1.sellprice.focus();
		return;
	}
	if (isNaN(document.form1.sellprice.value)) {
		alert("판매가격을 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
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
	if( document.form1.checkquantity.value == 'C' ){ //수량 입력
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
	// 배송비 변경
	var deli_err = 0;
	if( $('input[name="deli"]:checked').val() == '2' ){
		$('input[name="deli_select"]').each( function( i, obj ){
			if( $(this).prop('checked') ){
				if( $('input[name=deli_price]').eq(i).val().length == 0 ){
					alert("개별배송비를 입력하세요.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				} else if( isNaN( $('input[name=deli_price]').eq(i).val() ) ){
					alert("개별배송비는 숫자로만 입력하세요.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				} else if( parseInt( $('input[name=deli_price]').eq(i).val() ) <= 0){
					alert("개별배송비는 0원 이상 입력하셔야 합니다.");
					$('input[name=deli_price]').eq(i).focus();
					deli_err++;
				}
			}
		});
		if( deli_err > 0 ) return;
	}

	/*
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
	*/

	//상품군에 따른 변경 2016 01 19 유동혁
	if( jQuery.type( $("input[name='jungbo_prop_option']") ) !== "undefined"  ){
		var prop_opt_val = $("#jungbo_option").val();
		$("input[name='jungbo_prop_option']").each(function( i, obj ){
			prop_opt_val += "||" + $(this).val();
		});
		var prop_val = $("#jungbo_option").val();
		$("input[name='jungbo_prop_val']").each(function( i, obj ){
			prop_val += "||" + $(this).val();
		});
		document.form1.sabangnet_prop_option.value = prop_opt_val;
		document.form1.sabangnet_prop_val.value = prop_val;
	}

    // 독립형 옵션 필수구분
    var tf_text = '';
    var tf_arr = [];
    $('input[name="necessary_tf[]"').each( function( i, obj ) {
        if( $(this).prop( 'checked' )  ){
            tf_arr.push( 'T' );
        } else {
            tf_arr.push( 'F' );
        }
    });
    tf_text = tf_arr.join( '@#' );
    $('#option1_tf').val( tf_text );


	tempcontent = document.form1.content.value;
	document.form1.iconvalue.value="";
	num = document.form1.iconnum.value;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked) document.form1.iconvalue.value+=document.form1.icon[i].value;
	}
	if(mode=="preview") {
		alert("미리보기 준비중....");
	} else {
		if(confirm("상품을 등록하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.action="<?=$_SERVER['PHP_SELF']?>";
			//document.form1.target="processFrame";
			document.form1.submit();
		}
	}
}
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckChoiceIcon(no){
	num = document.form1.iconnum.value;
	iconnum=0;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked) iconnum++;
	}
	if(iconnum>3){
		alert('아이콘 꾸미기는 한상품에 3개까지 등록 가능합니다.');
		document.form1.icon[no].checked=false;
	}
}

function PrdtAutoImgMsg(){
	if(document.form1.imgcheck.checked) alert('상품 중간/작은 이미지가 큰이미지에서 자동 생성됩니다.\n\n기존의 중간/작은 이미지는 삭제됩니다.');
}


function SelectColor(){
	setcolor = document.form1.setcolor.value;
	var newcolor = showModalDialog("select_color.php?color="+setcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		document.form1.setcolor.value=newcolor;
		document.all.ColorPreview.style.backgroundColor = '#' + newcolor;
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

function BrandSelect() {
	window.open("product_brandselect.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}

function FiledSelect(pagetype) {
	window.open("product_select.php?type="+pagetype,pagetype,"height=400,width=420,scrollbars=no,resizable=no");
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
		$(".file_type").hide();
		$(".url_type").show();
	} else {				//첨부파일 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='';
			document.form1["userfile"+idx+"_url"].style.display='none';
			document.form1["userfile"+idx].disabled=false;
			document.form1["userfile"+idx+"_url"].disabled=true;
		}
		$(".url_type").hide();
		$(".file_type").show();
	}
}

//-->
</SCRIPT>

<!-- form 위치변경 2015 10 23 유동혁 -->
<form name=form1 method=post enctype="multipart/form-data">
<input type=hidden name=mode>
<input type=hidden name=code value="">
<input type=hidden name=htmlmode value='wysiwyg'>
<input type=hidden name=delprdtimg>
<input type=hidden name=option1>
<input type=hidden name=option2>
<input type=hidden name=option_price>
<input type='hidden' name='option1_tf' id='option1_tf' value='' >

<!-- 옵션 수정 END -->
<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>상품 신규등록</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15px 15px 5px 15px">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 신규등록</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 카테고리 생성은 본사 쇼핑몰에서만 관리할 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사는 생성된 대분류 카테고리명을 선택하고 중>소>세분류로 구분하여 상품등록 합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 등록한 상품은 [상품진열]기능을 통해 진열할 수 있습니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15px">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">

				<!-- form 위치변경 2015 10 23 유동혁 -->

<!-- 카테고리 링크 선택 추가 2015 10 26 유동혁 -->
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>카테고리 선택</B></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>

					<!-- <table width=100% border=0 cellspacing=0 cellpadding=0> -->
                    <table width=80% border=0 cellspacing=0 cellpadding=0>
					<tr height=22 align=center>
						<td width=150 nowrap>
						<table width="150" cellpadding="0" cellspacing="1" border="0" bgcolor=E7E7E7>
						<tr>
							<td bgcolor=FEFCE2 align="center" height="23"><B>대분류</B></td>
						</tr>
						</table>
						</td>
						<td align=center><img src=images/icon_arrow02.gif border=0></td>
						<td width=150 nowrap>
						<table width="150" cellpadding="0" cellspacing="1" border="0" bgcolor=E7E7E7>
						<tr>
							<td bgcolor=FEFCE2 align="center" height="23"><B>중분류</B></td>
						</tr>
						</table>
						</td>
						<td align=center><img src=images/icon_arrow02.gif border=0></td>
						<td width=150 nowrap>
						<table width="150" cellpadding="0" cellspacing="1" border="0" bgcolor=E7E7E7>
						<tr>
							<td bgcolor=FEFCE2 align="center" height="23"><B>소분류</B></td>
						</tr>
						</table>
						</td>
						<td align=center><img src=images/icon_arrow02.gif border=0></td>
						<td width=150 nowrap>
						<table width="150" cellpadding="0" cellspacing="1" border="0" bgcolor=E7E7E7>
						<tr>
							<td bgcolor=FEFCE2 align="center" height="23"><B>세분류</B></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=6 colspan=7></td>
					</tr>

					<tr>
						<td valign=top>
							<?=$codeA_list?>
						</td>
						<td></td>
						<td valign=top>
							<?=$codeB_list?>
						</td>
						<td></td>
						<td valign=top>
							<?=$codeC_list?>
						</td>
						<td></td>
						<td valign=top>
							<?=$codeD_list?>
						</td>
					</tr>
					<tr>
						<td colspan='7' style='height: 35px; text-align: right;'>
							<?=$codeSelect?>
<?	
	//카테고리 스크립트 실행
	echo $strcodelist;
	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>"; 
?>
						</td>
					</tr>
					<tr>
						<td colspan=7>
						<img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>카테고리 선택결과</B>
						&nbsp;
							<div class="table_none">
								<table width=100% cellpadding=0 cellspacing=1 id=Category_table>
									<col><col width=50 style="padding-right:10"><col width=52 align=right>
<?
	$cate_query="select * from tblproductlink where c_productcode='".$prcode."' and c_productcode!=''";
	$cate_result=pmysql_query($cate_query);
	$i=0;

	while($cate_row=pmysql_fetch_array($cate_result)){

		$cate_array[$i]["c_category"]=$cate_row[c_category];
		$cate_cut="";
		$catename="";
		$cate_cut[]=str_pad(substr($cate_row[c_category],0,3), 12, "0");
		if(substr($cate_row[c_category],3,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,6), 12, "0");
		if(substr($cate_row[c_category],6,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,9), 12, "0");
		if(substr($cate_row[c_category],9,3)!='000')$cate_cut[]=str_pad(substr($cate_row[c_category],0,12), 12, "0");

		foreach($cate_cut as $k){
			$catename_qry="select * from tblproductcode where code_a='".substr($k,0,3)."' and code_b='".substr($k,3,3)."' and code_c='".substr($k,6,3)."' and code_d='".substr($k,9,3)."'";
			$catename_result=pmysql_query($catename_qry);
			$catename_data=pmysql_fetch_array($catename_result);
			$catename[]=$catename_data[code_name];
		}
		$cate_array[$i]["c_codename"]=implode(" > ",$catename);
	$i++;
	}
	if($cate_array){
		foreach($cate_array as $v=>$k){
	?>
									<tr>
										<td id=cate_name><?=$k[c_codename]?></td>
										<td>
										<input type=text name=category[] value="<?=$k[c_category]?>" style="display:none">
										</td>
										<td>
										<!--<img src="../img/i_select.gif" border=0 onClick="cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)" class=hand>-->
										<a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="img/btn/btn_cate_del01.gif" border=0 align=absmiddle></a>
										</td>
									</tr>
	<?
		}
	}
?>

								</table>
							</div>
						</td>
					</tr>
					<tr><td colspan=7 height=5></tr>

					<tr><td height=1 colspan=7 bgcolor=CDCDCD></td></tr>

					</table>

					</td>
				</tr>

				<tr>
<!-- 카테고리 링크 선택 추가 2015 10 26 유동혁 -->

				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>상품정보</B></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=130></col>
					<col width=250></col>
					<col width=95></col>
					<col width=></col>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><font color=FF4800>*</font> 상품명</td>
						<td colspan=3 style="padding:7px 7px"><input name=productname value="" maxlength=250 style="width:388" onKeyDown="chkFieldMaxLen(250)"></td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><font color=FF4800>*</font> 판매가격</td>
						<td style="padding:7px 7px"><input name=sellprice value="" size=18 maxlength=10></td>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><font color=FF4800>*</font> 소비자가격</td>
						<td style="padding:7px 7px"><input name=consumerprice value="" size=18 maxlength=10><br><font style="color:#2A97A7;font-size:8pt">※ 0입력시, 표시안함</font></td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
                    <tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 적립금(률)</td>
						<td style="padding:7px 7px">
                            <input name=reserve value="1" size=18 maxlength=6 colspan="2" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.reservetype.value);">
                            <select name="reservetype" style="width:77;font-size:8pt;margin-left:1px;" onchange="chkFieldMaxLenFunc(this.form,this.value);">
                                <!-- <option value="N" >적립금(￦)</option> -->
                                <option value="Y" selected>적립률(%)</option>
                            </select>
                            <br><font style="color:#2A97A7;font-size:8pt;letter-spacing:-0.5pt;">* 적립률은 소수점 둘째자리까지 입력 가능합니다.<br>* 적립률에 대한 적립 금액 소수점 자리는 반올림.</span></td>
					</tr>
                    <tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor='F5F5F5' background='images/line01.gif' style='background-repeat:repeat-y;background-position:right;padding:9'> 구입원가</td>
						<td style="padding:7px 7px" colspan='2'><input name=buyprice value="" size=18 maxlength=10></td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 제조사</td>
						<td style="padding:7px 7px"><input name=production value="" size=18 maxlength=20 onKeyDown="chkFieldMaxLen(50)">&nbsp;<a href="javascript:FiledSelect('PR');"><img src="images/btn_select.gif" border="0" align="absmiddle"></a></td>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 원산지</td>
						<td style="padding:7px 7px"><input name=madein value="" size=18 maxlength=20 onKeyDown="chkFieldMaxLen(30)">&nbsp;<a href="javascript:FiledSelect('MA');"><img src="images/btn_select.gif" border="0" align="absmiddle"></a></td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
                        <td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9;display:none;> 브랜드</td>
						<td style="padding:7px 7px;display:none;"><input name=brandname value="" size=18 maxlength=40 onKeyDown="chkFieldMaxLen(50)">&nbsp;<a href="javascript:BrandSelect();"><img src="images/btn_select.gif" border="0" align="absmiddle"></a><br>
						<font style="color:#2A97A7;font-size:8pt">※ 브랜드를 직접 입력시에도 등록됩니다.</font></td>
                    </tr>
                    <tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 매입처</td>
						<td style="padding:7px 7px" ><input name=model value="" size=18 maxlength=40 onKeyDown="chkFieldMaxLen(50)">&nbsp;<a href="javascript:FiledSelect('MO');"><img src="images/btn_select.gif" border="0" align="absmiddle"></a></td>
                        <!-- 임시 마진률 -->
                        <td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9 >
                            마진률 ( % )
                        </td>
						<td style="padding:7px 7px;" >
                            <input name='rate' type='text' value="<?=$_venderdata->rate?>" >
                        </td>
                        
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 진열코드</td>
						<td style="padding:7px 7px" colspan="3"><input name=selfcode value="" size=18 maxlength=20 onKeyDown="chkFieldMaxLen(20)"><br><font style="color:#2A97A7;font-size:8pt">* 쇼핑몰에서 자동으로 발급되는 상품코드와는 별개로 운영상 필요한 자체상품코드를 입력해 주세요.</font></td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 출시일</td>
						<td style="padding:7px 7px" colspan="3"><input name=opendate value="" size=18 maxlength=8>&nbsp;&nbsp;예) <?=DATE("Ymd")?>(출시년월일)<br>
						<font style="color:#2A97A7;font-size:8pt">* 가격비교 페이지 등 제휴업체 관련 노출시 사용됩니다.<br>* 잘못된 출시일 지정으로 인한 문제는 상점에서 책임지셔야 됩니다.</font></td>
					</tr>
                    <tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
                    <tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9 >품절</td>
						<td colspan=3 >
							<input type='checkbox' name='soldout' value='Y' > 품절
						</td>
					</tr>
                    <tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9 >수량</td>
						<td colspan=3 >
							<input type='radio' name='checkquantity' value='F' CHECKED > 무제한
							<input type='radio' name='checkquantity' value='C'  > 수량
							<input type='text' name='quantity' size='9' maxlength='9' value='' readonly style="background : silver;" > 개
                            <font color="#FF0000">(*  조합형 옵션이 존재할 경우 옵션수량의 합계가 상품수량이 됩니다. )</font>
							<script>
								//수량처리 script 2016-03-09 유동혁
								$(document).on( 'click', 'input[name="checkquantity"]', function(){
									if( $(this).val() == 'F' ){
										$('input[name="quantity"]').attr( 'readonly', 'true' ).css( 'background', 'silver' );
										
									} else if( $(this).val() == 'C' ){
										$('input[name="quantity"]').removeAttr( 'readonly' ).css( 'background', 'white');
									}
								});
							</script>
						</td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 최소주문한도</td>
						<td style="padding:7px 7px"><input type=text name=miniq value="1" size=5 maxlength=5> 개 이상</td>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 최대주문한도</td>
						<td style="padding:7px 7px">
						<input type=radio id="idx_checkmaxq1" name=checkmaxq value="A" checked onclick="document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq1>무제한</label>&nbsp;<input type=radio id="idx_checkmaxq2" name=checkmaxq value="B" onclick="document.form1.maxq.disabled=false;document.form1.maxq.style.background='white';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq2>수량</label>:<input name=maxq size=5 maxlength=5 value="">개 이하
						<script>
						if (document.form1.checkmaxq[0].checked) { document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver'; }
						else if (document.form1.checkmaxq[1].checked) { document.form1.maxq.disabled=false;document.form1.maxq.style.background='white'; }
						</script>
						</td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>

<!-- 배송비 변경 2016-02-17 유동혁 -->
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>개별배송비</td>
						<td class="td_con1" colspan="3">
							<div class="table_none">
							<table cellSpacing='0' cellPadding='0' width="100%" border='0' >
								<tr>
									<td>
										<input type='radio' name='deli' id='deli0' value='0' CHECKED >
										<label for='deli0' >기본 배송비 <b>유지</b></label>
										<input type='radio' name='deli' id='deli1' value='1' >
										<label for='deli1' >기본 배송비 <b><font color="#0000FF">무료</font></b></label>
										<input type='radio' name='deli' id='deli2' value='2' >
										<label for='deli2' >기본 배송비 <b><font color="#FF0000">유료</font></b></label>
									</td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								<tr id='ID_deli_tr' style='display:none;'>
									<td>
										<table cellSpacing='0' cellPadding='0' width="100%" style='background-color: #f8f8f8;  border: 1px solid #b9b9b9;' >
											<caption>
												<col width='150px;'>
												<col width='*' >
											</caption>
											<tr>
												<td style='padding: 2 2 2 2;' >
													<input type='radio' name='deli_select' id='deliselect0' value='0' CHECKED >
													<label for='deliselect0' >고정배송비</label>
												</td>
												<td style='padding: 2 2 2 2;' >
													배송비
													<input type='text' name='deli_price' value='0' > 원<br>
													( 수량 / 주문금액에 상관없이 <b><font color="#FF0000">배송비 고정</font></b> )
												</td>
											</tr>
											<tr>
												<td style='padding: 2 2 2 2;'>
													<input type='radio' name='deli_select' id='deliselect1' value='1' >
													<label for='deliselect1' >수량별 배송비</label>
												</td>
												<td style='padding: 2 2 2 2;'>
													배송비
													<input type='text' name='deli_price' value='0' > 원<br>
													( 구매수 대비 개별배송비 증가 : <b><font color="#FF0000">상품구매수 X 개별배송비</font></b> )
												</td>
											</tr>
											<tr>
												<td style='padding: 2 2 2 2;'>
													<input type='radio' name='deli_select' id='deliselect2' value='2' >
													<label for='deliselect2' >수량별비례 배송비</label>
												</td>
												<td style='padding: 2 2 2 2;'>
													배송비
													<input type='text' name='deli_price' value='0' > 원
													<input type='text' name='deli_qty' value='0' style='width:50px;' > 개<br>
													마다 기본 배송비 반복 부과 ( <b><font color="#FF0000"> 상품구매수 대비 배송비</font></b> )
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							</div>
						</td>
					</tr>
					<script>
						$(document).ready( function(){
							disabled_deli();
							un_disabled_deli();
							if( $('input[name="deli"]:checked').val() == '2' ){
								$('#ID_deli_tr').show();
							}
						});

						$(document).on( 'click', 'input[name="deli"]', function( event ){
							if( $(this).val() == '2' ){
								$('#ID_deli_tr').show();
								un_disabled_deli();
							} else {
								$('#ID_deli_tr').hide();
								disabled_deli();
							}
						});

						$(document).on( 'click', 'input[name="deli_select"]', function( event ){
							disabled_deli();
							un_disabled_deli();
						});

						function disabled_deli(){
							$('input[name="deli_price"]').each( function(){
								$(this).attr('disabled','true').css( 'background-Color', '#EFEFEF' );
							});
							$('input[name="deli_qty"]').attr('disabled','true').css( 'background-Color', '#EFEFEF' );
						}

						function un_disabled_deli(){
							$('input[name="deli_price"]').each( function( i, obj ){
								if( $('input[name="deli_select"]').eq(i).prop('checked') ){
									$(this).removeAttr('disabled').css( 'background-Color', '' );
								}
							});
							if( $('input[name="deli_select"]').last().prop('checked') ){
								$('input[name="deli_qty"]').removeAttr('disabled').css( 'background-Color', '' );
							}
						}

					</script>
<!-- //배송비 변경 2016-02-17 유동혁 -->

					
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
                    <tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>MD 코멘트</td>
						<td colspan='3' style="padding:7px 7px" >
							<input name='mdcomment' value="" size=35 maxlength=500 onKeyDown="chkFieldMaxLen(500)" class="input" >
							<input name="mdcommentcolor" class="jscolor" value='000000' class='input'>
						</td>
					</tr>
                    <tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> TAG</td>
						<td colspan=3 style="padding:7px 7px">
						<input name=keyword value="" size=80 maxlength=100 onKeyDown="chkFieldMaxLen(100)">
						</td>
					</tr>
					
					<!--관련상품 세팅NEW 06 30 원재-->
					<!-- <script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> -->
					<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
					<script src="js/product_related.js"></script>
					<?include("layer_rproduct.php");?>
					<TR id='ID_RelationProduct'>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>관련상품
						<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="/admin/images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a>
						</td>
						<td align="left" colspan=3>
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
								<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>								
									<colgroup>
										<col width=20></col>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								</table>
							</div>
						</td>
					</TR>
					
					<!--관련상품 기존 대코앤이 스타일과 맞지 않아 폐기처분-->
					<?/*
					####################################################################
					상품별 관심 상품 세팅 06 29 원재 ㅠㅠ
					*관심상품은 상품별 최대 10개까지 세팅됩니다.
					*자동노출의 경우 등록된 관심상품은 삭제되며, 카테고리 조건에 맞는 상품이 자동 노출됩니다
					*수동노출을 선택했을 경우에만 세팅된 관심상품이 노출됩니다
					*수동노출 상품이 10개가 되지 않을경우 자동노출상품도 함께 노출됩니다.
					#####################################################################
				
					
					<script src="/admin/jscript/jquery-ui_won.min.js"></script>
					<script>
						function view_rproduct(){//관련상품 조회 및 등록 창 열기
							window.open("product_related.php","","width=700,height=700,scrollbars=yes","_blank");
						}

						$(function(){//마우스 드래그로 관련상품 노출 순서를 설정 할 수 있습니다
							$( "#r_product_list" ).sortable({
								scroll: false
							});
							$( "#r_product_list" ).disableSelection();
						});

						$(document).on("click",".del_rproduct",function(){//선택된 관련상품 삭제
							var obj = $(this).parent();
							obj.remove();
						});
						
						$(document).on("change","input[name='r_type']",function(){
							var chk_type = $(this).val();
							
							if(chk_type == "1"){
								$("#r_product_table").fadeOut('fast');
							}

							if(chk_type=="2"){
								$("#r_product_table").fadeIn('fast');
							}

						});
					</script>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>관련상품</font></td>
						
						<td class="td_con1" colspan="4">
						<input type="radio" name="r_type"  value="1" checked>자동노출 <input type="radio" name="r_type" value="2" >수동노출
							<table width="100%" id="r_product_table" style="display:none">
								<tr>
									<td colspan="2"><a onclick="view_rproduct();"><img src="/admin/images/btn_select2.gif" border="0" hspace="2"></a></td>
								</tr>
								<tr>
									<td width="30%"><b>관련상품리스트</b><br>마우스 드래그로<br> 순서를 변경 할 수 있습니다</td> 
									<td>
									
										<ul id="r_product_list">
										
										</ul>
										
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<!--//관련상품-->
					*/?>

					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					<tr style='display: none;'>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 특이사항</td>
						<td colspan=3 style="padding:7px 7px">
						<input name=addcode value="" size=43 maxlength=200 onKeyDown="chkFieldMaxLen(200)">&nbsp;&nbsp;<font style="color:#2A97A7;font-size:8pt">(예: 향수는 용량표시, TV는 17인치등)</font>
						</td>
					</tr>
					<tr><td height=1 colspan=4 bgcolor=E7E7E7></td></tr>
					</table>
					</td>
				</tr>

                <tr><td height=15></td></tr>

                <tr>
					<td>
                        <img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>리뷰 상단 배너 이미지 등록</B>
                    </td>
				</tr>

				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
                <tr>
                    <td>
                        <table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
                            <col width=130></col>
        					<col width=></col>
                            <tr>
                                <td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>리뷰 상단 배너 이미지</td>
                                <td class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="50%" bgcolor="#F9F9F9">
                                    <input type=file name="review_banner_img" style="width:100%">
                                    <input type=hidden name="old_review_banner_img" id="old_review_banner_img" value="">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
				<tr><td height=15></td></tr>

				<tr>
					<td>
                        <img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>사진정보</B>
                        <input type=checkbox id="idx_use_imgurl" name=use_imgurl value="Y" onClick="change_filetype(this)">
                        <label style='cursor:hand;' onMouseOver="style.textDecoration=''" onMouseOut="style.textDecoration='none'" for=idx_use_imgurl>
                            <span class="font_orange"><B>상품이미지 첨부 방식을 URL로 입력합니다.</B> (예 : http://www.abc.com/images/abcd.gif)</font>
                        </label>
                    </td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=130></col>
					<col width=></col>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 큰이미지</td>
						<td style="padding:7px 7px">
                            <div class="file_type">
                                <input type=file name="userfile" class=button style="width=300px" > <font style="color:#2A97A7;font-size:8pt">(권장이미지 : 600X600)</font>
                                <br>
        						<input type=checkbox id="idx_imgcheck1" name=imgcheck value="Y">
                                <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_imgcheck1>
                                    <font color=#003399>큰 이미지로 중간/작은 이미지 자동생성 (이미지 권장 사이즈로 변경)</font>
                                </label>
                            </div>
                            <div class="url_type">
                                <input type=text name="userfile_url" value="" style="width:100%;display:none" class="input">
                                <br><img src="../admin/images/space01.gif">
                            </div>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 중간이미지</td>
						<td style="padding:7px 7px">
                            <div class="file_type">
                                <input type=file name="userfile2" class=button style="width=300px"  >
                                <font style="color:#2A97A7;font-size:8pt">(권장이미지 : 600X600)</font>
                            </div>
                            <div class="url_type">
                                <input type=text name="userfile2_url" value="" style="width:100%;display:none" class="input">
                                <br><img src="../admin/images/space01.gif">
                            </div>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 작은이미지</td>
						<td style="padding:7px 7px">
                            <div class="file_type">
                                <input type=file name="userfile3" class=button style="width=300px" >
                                <font style="color:#2A97A7;font-size:8pt">(권장이미지 : 600X600)</font>
                                <table border=0 cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="imgborder" value="Y">
                                            <font color=#003399>신규 등록시,&nbsp;(&nbsp;</font>
                                        </td>
                                        <td><input type="text" id="itemColor" class='jscolor' name='setcolor' value="666666" ></td>
                                        <td><font color=#003399>&nbsp;)&nbsp;로 상품 테두리선 생성</font></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="url_type">
                                <input type=text name="userfile3_url" value="" style="width:100%;display:none" class="input">
                                <br><img src="../admin/images/space01.gif">
                            </div>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
                    <tr>
                        <td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>리스트 롤오버 이미지</td>
                        <td style="padding:7px 7px">
                            <div class="file_type">
                                <input type=file name="userfile4" style="WIDTH: 300px" >
                                 <font style="color:#2A97A7;font-size:8pt">(권장이미지 : 600X600)</font>
                            </div>
                            <div class="url_type">
                                <input type=text name="userfile4_url" value="" style="width:100%;" class="input">
                                <br><img src="../admin/images/space01.gif">
                            </div>
                        </td>
                    </tr>
                    <tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
<!-- 기타 이미지 추가 (멀티이미지) 20151023 유동혁 -->
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 기타 이미지</td>
						<td class="td_con1" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;position:relative;">
						<table width="100%">
<?php
$urlpath=$Dir.DataDir."shopimages/multi/";
for($i=1;$i<=10;$i+=2) {
    $gbn1=sprintf("%02d",$i);
    $gbn2=sprintf("%02d",$i+1);
?>
                        <tr bgColor=#f0f0f0>
                            <td class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left valign=top width="50%" bgcolor="#F9F9F9">
                                <div class="file_type">
                                    <input type=file name=mulimg<?=$gbn1?> style="width:100%">
                                </div>
                                <div class="url_type">
                                <input type=text name="mulimg<?=$gbn1?>_url" value="<?=$mulimg_name[$gbn1]?>"style="width:100%" class="input">
                                </div>
                                <input type=hidden name=oldimg<?=$gbn1?> value="<?=$mulimg_name[$gbn1]?>">
                            </td>
                            <td class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left valign=top width="50%" bgcolor="#F9F9F9">
                                <div class="file_type">
                                <input type=file name=mulimg<?=$gbn2?> style="width:100%">
                                </div>
                                <div class="url_type">
                                <input type=text name="mulimg<?=$gbn2?>_url" value="<?=$mulimg_name[$gbn2]?>" style="width:100%" class="input">
                                </div>
                                <input type=hidden name=oldimg<?=$gbn2?> value="<?=$mulimg_name[$gbn2]?>">
                            </td>
                        </tr>
<?php
}
?>
                         <script>change_filetype(document.form1.use_imgurl);</script>
						</table>
						</td>
					</tr>
<!--// 기타 이미지 추가 (멀티이미지) 20151023 유동혁 -->
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					</table>
					</td>
				</tr>

				<tr><td height=15></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>상품 상세정보(PC)</B>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr>
						<td>
						<textarea wrap=off id="ir1" style="width:100%; height:300" name=content></textarea>
						</td>
					</tr>
					</table>
					<img id="size_checker" style="display:none;">
					<img id="size_checker2" style="display:none;">
					<img id="size_checker3" style="display:none;">
					</td>
				</tr>

				<tr><td height=15></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>상품 상세정보(MOBILE)</B>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr>
						<td>
						<textarea wrap=off id="ir2" style="width:100%; height:300" name=content_m></textarea>
						</td>
					</tr>
					</table>
					</td>
				</tr>

				<tr><td height=15></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>상품 공지사항</B>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr>
						<td>
						<textarea wrap=off id="ir3" style="width:100%; height:300" name=pr_notice></textarea>
						</td>
					</tr>
					</table>
					<img id="size_checker" style="display:none;">
					<img id="size_checker2" style="display:none;">
					<img id="size_checker3" style="display:none;">
					</td>
				</tr>

				<tr><td height=15></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>추가정보</B></td>
				</tr>

				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" class='option_style'>
					<col width=130></col>
					<col width=></col>

                    <!-- -->
                    <style>
                        table.option_style  tr  th {
                            background-color: #F5F5F5;  padding: 9;  border-bottom-style: inset;  border-bottom-width: thin;
                            font-weight: normal;
                        }
                        table.option_style tr td {
                            border-bottom-style: inset;  border-bottom-width: thin;
                        }
                        /*
						tr.option_style td.on-class { 
							background-repeat:repeat-y; background-position:right; padding:9;
							background-color: F5F5F5; background : 'images/line01.gif';
						}
						tr.option_style th {
							background-repeat:repeat-y; background-position:right; padding:9;
							background-color: F5F5F5; background : 'images/line01.gif';
						}
                    */
                    </style>
					 
<!-- 옵션정보 -->
    <!-- 옵션 사용 선택 -->
					<tr>
						<th>옵션사용</th>
						<td>
							<input type='radio' name='opt_select' id='opt_select1' value ='1' > 사용함
							<input type='radio' name='opt_select' id='opt_select2' value ='0' CHECKED > 사용안함
						</td>
					</tr>
    <!-- //옵션 사용 선택 -->
					
					<tr id='ID_p_goods_code_area' >
						<th><span>자체품목코드</span></th>
						<td>
							<input name="p_goods_code" value="<?=$_data->self_goods_code?>" size="50" maxlength="50" class="input">
						</td>
					</tr>

    <!-- 옵션 구성 방식 -->
                    <tr id='ID_option_type_area' >
						<th><span>옵션구성방식</span></th>
						<td>
							<input type='radio' name='opt_type' id='opt_type1' value ='0' CHECKED > 조합형
							<input type='radio' name='opt_type' id='opt_type2' value ='1' > 독립형
						</td>
					</tr>
    <!-- //옵션 구성 방식 -->
    <!-- 조합형 옵션 ( TYPE = 0 ) -->
                    <tr id='ID_opt1_area' >
						<th scope="row"><span>조합형 옵션</span></th>
						<td>
                            <span class="frm_info">
                                옵션항목은 '@#' 로 구분하여 여러개를 입력할 수 있습니다. 옷을 예로 들어 [옵션1 : 사이즈 , 옵션1 항목 : XXL@#XL@#L@#M@#S] , [옵션2 : 색상 , 옵션2 항목 : 빨@#파@#노]<br>
                                <strong>옵션명과 옵션항목에 '@#'을 연결되어 입력할 수 없습니다.</strong>
                            </span>
								<div class="sit_option tbl_frm01" >
                            <table border = '0' width='98%'>
                                <caption>상품선택옵션 입력</caption>
                                <colgroup>
                                    <col class="grid_4">
                                    <col>
                                </colgroup>
                                <tbody id='opt_subject_body' >

                                <tr>
                                    <th scope="row">
                                        <label for="opt1_subject">옵션</label>
                                        <input type="text" name="opt_subject[]" value="" class="frm_input" size="15">
                                    </th>
                                    <td>
                                        <label for="opt"><b>항목</b></label>
                                        <input type="text" name="opt_content[]" value="" class="frm_input" size="50">
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="btn_confirm02 btn_confirm">
                                <button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>
                                <button type="button" id="option_table_create" class="btn_frmline">옵션목록생성</button>
                            </div>
							<div id="sit_option_frm">

							</div>
						</td>
					</tr>
    <!-- 독립형 옵션 -->
                    <tr id='ID_supply_area'>
						<th scope="row"><span>독립형 옵션</span></th>
						<td>
							<div id="sit_supply_frm" class="sit_option tbl_frm01">
								<?php echo help('옵션항목은 "@#" 로 구분하여 여러개를 입력할 수 있습니다. 스마트폰을 예로 들어 [추가1 : 추가구성상품 , 추가1 항목 : 액정보호필름@#케이스@#충전기]<br><strong>옵션명과 옵션항목에 "@#" 는 입력할 수 없습니다.</strong>'); ?>
								<table>
								<caption>상품추가옵션 입력</caption>
								<colgroup>
									<col class="grid_4">
									<col>
								</colgroup>
								<tbody>
								<?php
								$i = 0;
								do {
									$seq = $i + 1;
								?>
								<tr>
									<th scope="row">
										<label for="spl_subject_<?php echo $seq; ?>">옵션</label>
										<input type="text" name="spl_subject[]" id="spl_subject_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="15">
									</th>
									<td>
										<label for="spl_item_<?php echo $seq; ?>"><b>옵션 항목</b></label>
										<input type="text" name="spl[]" id="spl_item_<?php echo $seq; ?>" value="" class="frm_input" size="40">
										<input type='checkbox' name='necessary_tf[]' value='T' > *필수옵션으로 사용
										<?php
										if($i > 0)
											echo '<button type="button" id="del_supply_row" class="btn_frmline">삭제</button>';
										?>
									</td>
								</tr>
								<?php
									$i++;
								} while($i < $spl_count);
								?>
								</tbody>
								</table>
								<div id="sit_option_addfrm_btn"><button type="button" id="add_supply_row" class="btn_frmline">옵션추가</button></div>
								<div class="btn_confirm02 btn_confirm">
									<button type="button" id="supply_table_create">옵션목록생성</button>
								</div>
							</div>
							
							<div id="sit_option_addfrm"><?php include_once($Dir.'admin/ajax_productoption_plus.php'); ?></div>

						</td>
					</tr>
    <!-- //독립형 옵션 -->
					<!-- //추가옵션 -->
					<!-- text 옵션 -->
					<tr>
						<th><span>추가옵션</span></th>
						<td>
							<div>
								<input type='checkbox' name='addopt_select' value ='1' <? if( strlen( $_data->option2 ) > 0 ){ echo 'checked'; } ?> > 사용 <br>
							</div>
							<div id='ID_addopt_content' >
								<ul id='ID_add_content'>
									<li>
										추가옵션명 <input type='text' name='addopt_subject[0]' value='' >
										<input type='radio' name='addopt_type[0]' value = 'T' checked > 필수항목
										<input type='radio' name='addopt_type[0]' value = 'F' > 선택항목
										<select name="addopt_maxln[0]" >
<?php
		for( $i=1; $i<=50; $i++){
?>
											<option value='<?=$i?>' <? if( $i == 10 ){ echo 'selected'; } ?> > <?=$i?>자</option>
<?php
		} //for
?>
								<!-- <option value='100>' > 100자</option>
								<option value='200' > 200자</option> -->
										</select>
										<button type='button' name='addOptDel' >삭제</button>
									</li>
								</ul>
								<div class="btn_confirm02 btn_confirm">
										<button type="button" class="btn_frmline" name="btn_addopt_add" >추가</button>
								</div>
							</div>
							
						</td>
					</tr>
                    <!-- <tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr> -->
<!-- // 옵션정보 -->
					
					<tr style='display:none;'>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 아이콘 꾸미기</td>
						<td style="padding:7px 7px">

						<table border=0 cellpadding=0 cellspacing=0 width=70%>
<?php
						//$iconarray = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28");
						$iconarray = array("01","02","03","04","05","06","07");
						$totaliconnum = 0;
						for($i=0;$i<count($iconarray);$i++) {
							if($i%7==0) echo "<tr height=25>";
							echo "<td align=center><img src=\"".$Dir."images/common/icon".$iconarray[$i].".gif\" border=0 align=absmiddle><br><input type=checkbox name=icon onclick=CheckChoiceIcon('".$totaliconnum."') value=\"".$iconarray[$i]."\" ";
							if($iconvalue2[$iconarray[$i]]=="Y") echo "checked";
							echo "></td>\n";
							if($i%7==6) echo "</tr>";
							$totaliconnum++;
						}
?>
						</table>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 배송/쇼환/환불정보</td>
						<td style="padding:7px 7px">
						<input type=checkbox id="idx_deliinfono1" name=deliinfono value="Y"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfono1>배송/교환/환불정보 노출안함</label> <font style="color:#2A97A7;font-size:8pt">(상세화면 하단에 배송/교환/환불정보가 노출안됨)</font>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>

					<?if($_venderdata->grant_product[3]=="N") {?>

					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 상품진열</td>
						<td style="padding:7px 7px">
						<input type=radio id="idx_display1" name=display value="Y" checked><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display1>보이기 [ON]</label>
						<img width=50 height=0>
						<input type=radio id="idx_display2" name=display value="N"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display2>안보이기 [OFF]</label>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>

					<?} else {?>

					<input type=hidden name=display value="N">
					
					<?}?>
					</table>
					</td>
				</tr>

				<!--사이즈 조견표 -->
				<tr><td height=20></td></tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tbody id="tbody_chk_prsize">
							<tr>
								<td>
									<script src="/admin/jscript/product_size2.js"></script>
									 <img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>사이즈 조견표</B>		
								</td>
							</tr>
							<tr>
								<td>
								<div class="table_style01" id="prop_type001">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tbody>
										<tr>
											<th rowspan=2>
												<span>사이즈 조견표 사용</span> <font color="#0099BF"></font>
											</th>
											<td>
											<input type="radio" name="use_prsize" checked value="N">사용안함
											<input type="radio" name="use_prsize" value="Y" >사용
												 <span class="font_orange">*사이즈 조견표를 작성 할 수 있습니다</span>
											</td>
										</tr>
										<tr id="tbody_product_size" <?if(!$product_size || $product_size['use']['chk']=='N') echo "style='display:none;' ";?>>
											<td>
												<table cellspacing="0" cellpadding="0" width="100%" border="0" class="style_none">
													<tbody >
														<tr>
															<td>
															<input type=button value='리스트추가' id='add-row'>
															<input type=button value='항목추가' id='add-col'>
															<input type=button value='리스트삭제' id='del-row'>
															<input type=button value='항목삭제' id='del-col'>
															</td>
														</tr>
														<tr>
															<td>
																<div id="">
																	<table border=0>
																		<thead id='stock_thead'>
																			<tr>
																				<td>
																					<!-- <input type=hidden name=it_opty_subj size=10 class=ed value='<?=$it[it_opty_subj]?>'> 
																					<input type=hidden name=it_optx_subj size=4 class=ed value='<?=$it[it_optx_subj]?>'> -->
																					<input type=text readonly value="사이즈/항목" size="10" style="text-align: center">
																				</td>
																			
																				<td>
																					<input type=text name='sizey_subj[0]' size=10 class=ed>
																				</td>
																			
																			</tr>
																		</thead>
																		<tbody id='stock_tbody'>
																		
																			<tr>
																				<td>
																					<input type=text name='sizex_subj[]' size=10 class=ed>
																				</td>
																				<td>
																					<input type=text name='size_content[0][]' size=10 class=ed>
																				</td>
																			</tr>
																		
																		</tbody>
																	</table>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
			<!--//사이즈 조견표-->

				<tr><td height=20></td></tr>
                <!-- 정보고시 페이지 수정 2016 01 18 유동혁 -->
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <!-- <tr>
                                <td>
                                    <div class="title_depth3">정보 고시 등록/수정</div>     
                                    <input type="hidden" name="sabangnet_prop_val" value="<?=$_data->sabangnet_prop_val?>">
                                    <input type="hidden" name="sabangnet_prop_option" value="<?=$data->sabangnet_prop_option?>">
                                    <input type="hidden" name="prop_type" value="001" />
                                </td>
                            </tr>
                            <tr><td height="15"></td></tr> -->
                            <tr>
                                <td>
                                    <img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>정보 고시 등록/수정</B>
                                    <input type="hidden" name="sabangnet_prop_val" value="<?=$_data->sabangnet_prop_val?>">
                                    <input type="hidden" name="sabangnet_prop_option" value="<?=$data->sabangnet_prop_option?>">
                                    <input type="hidden" name="prop_type" value="001" />
                                </td>
                            </tr>

                            <tr><td height="15"></td></tr>
                            <tr><td height="1" bgcolor="red"></td></tr>
                            <tr>
                                <td>
                                    <div class="table_style01" id="prop_type001">
                                        <table cellSpacing=0 cellPadding=0 width="100%" border=0>
                                            <tr>
                                                <th>
                                                    <span>상품의 상품군</span>
                                                </th>
                                                <td>
                                                    <select id='jungbo_option' name='jungbo_option' >
    <?php
        echo "<option value='025' selected >INFO</option>";
        /* deco는 info만 볼수엤게 한다
        foreach( $jungbo_code as $codeKey=>$codeVal ){
            $jungbo_selected = '';
            if( $codeKey == $sabangnet_prop_option[0] ) $jungbo_selected = 'selected';
            echo "<option value='".$codeKey."' ".$jungbo_selected." >".$codeVal['title']."</option>";
        }
        */
    ?>
                                                    </select>
                                                    <input type='checkbox' id='jungbo_allchk' value='' > <span class='font_orange'>* 모든 상품정보 '상세정보 별도표기' 선택</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="table_style01" id="prop_type001">
                                        <table cellSpacing=0 cellPadding=0 width="100%" border=0 id='jungbo_options'>
    <?php
            //$incode = $jungbo_code[$sabangnet_prop_option[0]];
            # info 만 사용하도록 변경 2016-03-07
            $incode = $jungbo_code["025"];
            $optionKey = 1;
            if( $incode ){
                foreach( $incode['option'] as $inKey=>$inVal ){

    ?>
                                            <tr>
                                                <th>
                                                    <span><?=$inVal?></span>
                                                    <input type='hidden' name='jungbo_prop_option' id='' value='<?=$inVal?>' >
                                                </th>
                                                <td>
                                                    <input type='text' name='jungbo_prop_val' id='' value='<?=$sabangnet_prop_val[$optionKey]?>' style="width:450px;" >
                                                    <input type='checkbox' name='option_chk' > 상세정보 별도표기
                                                    <br><span class='font_blue' ><?=$incode['comment'][$inKey]?></span>
                                                </td>
                                            </tr>
    <?php
                    $optionKey++;
                }
            }
    ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                    <!-- //정보고시 페이지 수정 2016 01 18 유동혁 -->
				<tr>
					<td align=center>
					<!--A HREF="javascript:formSubmit('preview')"><img src="images/btn_preview01.gif" border=0></A>
					&nbsp;-->
					<A HREF="javascript:formSubmit('insert')"><img src="images/btn_regist01.gif" border=0></A>
					</td>
				</tr>

				<input type=hidden name=iconnum value='<?=$totaliconnum?>'>
				<input type=hidden name=iconvalue>

				<!-- form 위치변경 -->

				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
</form>

<?php
if ($predit_type=="Y") {
?>
<!-- 에디터 변경 -->
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
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
var oEditors_m = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_m,
	elPlaceHolder: "ir2",
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

var oEditors_pr_notice = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors_pr_notice,
	elPlaceHolder: "ir3",
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

<!-- 옵션 스크립트 추가 2015 10 23 유동혁 -->
<script>
$(function() {

	$(document).ready(function(){
		$('input[name="opt_select"]:checked').trigger('click');

		$('input[name="addopt_select"]').prop( 'checked', function( i, v ){
			if( v ) $('#ID_addopt_content').show();
			else $('#ID_addopt_content').hide();
		});

        //정보고지 내용을 불러온다
        $(document).on( 'change', '#jungbo_option', function(){
            var jungbo_code = $(this).val();
            var productcode = $("input[name='prcode']").val();
            $.post(
                '../admin/ajax_jungbo_option.php',
                { code : jungbo_code, prcode : productcode }
            ).done( function( data ){
                if( data ){
                    $("#jungbo_options").html( data );
                } else {
                    alert('잘못된 상품군을 선택하셨습니다.');
                }
            });
        });
        //개별 상세정보 별도표기
        $(document).on( 'click', 'input[name="option_chk"]', function( e ){
            var chk_index = $('input[name="option_chk"]').index( e.target );
            if( $(this).prop( 'checked' ) ){
                $('input[name="jungbo_prop_val"]').eq(chk_index).val( '상세정보 별도표기' );
                $('input[name="jungbo_prop_val"]').eq(chk_index).attr('readOnly','true');
            } else {
                $('input[name="jungbo_prop_val"]').eq(chk_index).val( '' );
                $('input[name="jungbo_prop_val"]').eq(chk_index).removeAttr('readOnly');
            }
        });
        //전체 상세정보 별도표기
        $(document).on( 'click', '#jungbo_allchk', function( e ){
            if( $(this).prop( 'checked' ) ){
                $('input[name="option_chk"]').each( function( i, obj ){
                    $(this).prop( 'checked', true );
                    $('input[name="jungbo_prop_val"]').eq(i).val( '상세정보 별도표기' );
                    $('input[name="jungbo_prop_val"]').eq(i).attr('readOnly','true');
                });
            } else {
                $('input[name="option_chk"]').each( function( i, obj ){
                    $(this).prop( 'checked', false );
                    $('input[name="jungbo_prop_val"]').eq(i).val( '' );
                    $('input[name="jungbo_prop_val"]').eq(i).removeAttr('readOnly');
                });
            }
        });

	});

	//추가옵션 사용 click
	$('input[name="addopt_select"]').click( function(){
		if( $(this).prop( 'checked' ) ){
			$('#ID_addopt_content').show();
		} else {
			$('#ID_addopt_content').hide();
		}
	});


	$('button[name="btn_addopt_add"]').click( function(){
		//$('#ID_add_content > li');
		var idx = $('#ID_add_content > li').length;
		var addoptHtml = "";

		addoptHtml += "<li>";
		addoptHtml += "	추가옵션명 <input type='text' name='addopt_subject[" + idx + "]' value='' >";
		addoptHtml += "	<input type='radio' name='addopt_type[" + idx + "]' value = 'T' checked > 필수항목";
		addoptHtml += "	<input type='radio' name='addopt_type[" + idx + "]' value = 'F' > 선택항목";
		addoptHtml += "	<select name='addopt_maxln[" + idx + "]' >";
		for( var i = 1; i <= 50; i++ ){
			if( i == 10) addoptHtml += "		<option value='" + i + "' selected > " + i + "자</option>";
			else addoptHtml += "		<option value='" + i + "'> " + i + "자</option>";
		}
		addoptHtml += "	</select>";
		addoptHtml += "	<button type='button' name='addOptDel' >삭제</button>";
		addoptHtml += "</li>";

		$('#ID_add_content').append( addoptHtml );

	});

	$(document).on( 'click', 'button[name="addOptDel"]', function(){
		var content_element = $(this).parent();
		var content_element_leng = $('#ID_add_content > li').length;
		var this_idx = $('button[name="addOptDel"]').index( $(this) ) + 1;

		for( var i = ( this_idx + 1 ); i < content_element_leng; i++ ){
			//console.log( $(content_element).eq(i) );
			$('input[name="addopt_subject[' + i + ']"]').attr( 'name', 'addopt_subject[' + ( i - 1 ) + ']' );
			$('input[name="addopt_type[' + i + ']"]').each( function(){
				$(this).attr( 'name', 'addopt_type[' + ( i - 1 ) + ']' );
			});
			$('select[name="addopt_maxln[' + i + ']"]').attr( 'name', 'addopt_maxln[' + ( i - 1 ) + ']' );
		}
		
		$(this).parent().remove();
	});

	//옵션사용 click
	$(document).on( 'click', 'input[name="opt_select"]', function(){
		if( $(this).val() == 1 ){
			$('#ID_p_goods_code_area').hide();
			$('#ID_option_type_area').show();
			$('input[name="opt_type"]:checked').trigger('click');
		} else {
			$('#ID_p_goods_code_area').show();
			$('#ID_option_type_area').hide();
			$('#ID_opt1_area').hide();
			$('#ID_supply_area').hide();
		}
		
	});

	//옵션 구성방식 click
	$(document).on( 'click', 'input[name="opt_type"]', function(){
		if( $(this).val() == 0 ){
			if( $('input[name="spl_subject[]"').eq(0).val().length > 0 ){
				if( !confirm("조합형 옵션이 존재합니다.\n옵션을 변경할경우 기존 내용은 삭제됩니다.") ){
					$(this).prop( 'checked', false );
					$('input[name="opt_type"]').eq(1).prop( 'checked', true );
					return;
				}
			}
			$('#ID_opt1_area').show();
			$('#ID_supply_area').hide();
		} else {
			if( $('input[name="opt_subject[]"').eq(0).val().length > 0 ){
				if( !confirm("조합형 옵션이 존재합니다.\n옵션을 변경할경우 기존 내용은 삭제됩니다.") ){
					$(this).prop( 'checked', false );
					$('input[name="opt_type"]').eq(0).prop( 'checked', true );
					return;
				}
			}
			$('#ID_opt1_area').hide();
			$('#ID_supply_area').show();
		}
	});

	$(document).on( 'click', 'button[name="btn_opt_subject_add"]' , function() {
		var html = '';
		var element = $("input[name^=opt_subject]");
		var element_cnt = $(element).length;
		var $option_table = $("#sit_option_frm");
		
		html += '<tr>';
		html +=	'	<th scope="row">';
		html +=	'		<label for="opt_subject">옵션</label>';
		html +=	'		<input type="text" name="opt_subject[] " value="" class="frm_input" size="15">';
		html +=	'	</th>';
		html +=	'	<td>';
		html +=	'		<label for="opt"><b>항목</b></label>';
		html +=	'		<input type="text" name="opt_content[]" value="" class="frm_input" size="50">';
		html +=	'		<button type="button" class="btn_frmline" name="btn_opt_subject_del" >삭제</button>';
		//html +=	'		<button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>';
		html +=	'	</td>';
		html += '</tr>';

		//$(this).remove();
		$option_table.empty();
		$('#opt_subject_body').append( html );

	});
	//옵션 삭제
	$(document).on( 'click', 'button[name="btn_opt_subject_del"]', function(){
		var $option_table = $("#sit_option_frm");
		$(this).parent().parent().remove();
		/*
		if( $("input[name^=opt_content]").last().parent().find('button[name="btn_opt_subject_add"]').length == 0 ){
			$("input[name^=opt_content]").last().parent().append( ' <button type="button" class="btn_frmline" name="btn_opt_subject_add" >추가</button>' );
		}
		*/
		$option_table.empty();
	});
	
	// 옵션목록생성
	$("#option_table_create").click(function() {
		//var it_id = $.trim($("input[name=it_id]").val()); // 수정
		var it_id = $.trim($("#it_id").val()); // 수정
		var opt_subject = [];
		var opt_content = [];
		var $option_table = $("#sit_option_frm");

		$("input[name^=opt_subject]").each( function( subject_idx, subject_obj ) {
			var content = $("input[name^=opt_content]").eq( subject_idx );
			if( $(this).val().length > 0 && $(content).val().length > 0 ) {
				opt_subject.push( $.trim( $(this).val() ) );
				opt_content.push( $.trim( $(content).val() ) );
			}
		});
/*
		$("input[name^=opt_content]").each( function( content_idx, content_obj ) {
			if( $.type( opt_subject[content_idx] ) != 'undefined' && $(this).val().length > 0 ) {
				opt_content.push( $.trim( $(this).val() ) );
			}
		});
*/
		if( ( opt_subject[0].length == 0 ) || ( opt_content[0].length == 0 ) ) {
			alert("옵션명과 옵션항목을 입력해 주십시오.");
			return false;
		}

		$.post(
			"../admin/ajax_productoption_new.php",
			{ it_id: it_id, w: "u", opt_subject: opt_subject, opt_content: opt_content },
			function(data) {
				$option_table.empty().html(data);
				//console.log( data );
			}
		);
	});

	// 모두선택
	$(document).on("click", "input[name=opt_chk_all]", function() {
		if($(this).is(":checked")) {
			$("input[name='opt_chk[]']").attr("checked", true);
		} else {
			$("input[name='opt_chk[]']").attr("checked", false);
		}
	});

	// 선택삭제
	$(document).on("click", "#sel_option_delete", function() {
		var $el = $("input[name='opt_chk[]']:checked");
		if($el.size() < 1) {
			alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
			return false;
		}

		$el.closest("tr").remove();
	});

	// 일괄적용
	$(document).on("click", "#opt_value_apply", function() {
		if($(".opt_com_chk:checked").size() < 1) {
			alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
			return false;
		}

		var opt_price = $.trim($("#opt_com_price").val());
		var opt_stock = $.trim($("#opt_com_stock").val());
		var opt_noti = $.trim($("#opt_com_noti").val());
		var opt_use = $("#opt_com_use").val();
		var $el = $("input[name='opt_chk[]']:checked");

		// 체크된 옵션이 있으면 체크된 것만 적용
		if($el.size() > 0) {
			var $tr;
			$el.each(function() {
				$tr = $(this).closest("tr");

				if($("#opt_com_price_chk").is(":checked"))
					$tr.find("input[name='opt_price[]']").val(opt_price);

				if($("#opt_com_stock_chk").is(":checked"))
					$tr.find("input[name='opt_stock_qty[]']").val(opt_stock);

				if($("#opt_com_noti_chk").is(":checked"))
					$tr.find("input[name='opt_noti_qty[]']").val(opt_noti);

				if($("#opt_com_use_chk").is(":checked"))
					$tr.find("select[name='opt_use[]']").val(opt_use);
			});
		} else {
			if($("#opt_com_price_chk").is(":checked"))
				$("input[name='opt_price[]']").val(opt_price);

			if($("#opt_com_stock_chk").is(":checked"))
				$("input[name='opt_stock_qty[]']").val(opt_stock);

			if($("#opt_com_noti_chk").is(":checked"))
				$("input[name='opt_noti_qty[]']").val(opt_noti);

			if($("#opt_com_use_chk").is(":checked"))
				$("select[name='opt_use[]']").val(opt_use);
		}
	});
});
</script>

<!-- 독립형 옵션 2015 10 28 유동혁 -->
<script>
$(function() {
	<?php if($it['it_id'] && $ps_run) { ?>
	// 추가옵션의 항목 설정
	var arr_subj = new Array();
	var subj, spl;
	var tf_type = '';

	$("input[name='spl_subject[]']").each(function() {
		subj = $.trim($(this).val());
		if(subj && $.inArray(subj, arr_subj) == -1)
			arr_subj.push(subj);
	});

	for(i=0; i<arr_subj.length; i++) {
		var arr_spl = new Array();
		$(".spl-subject-cell").each(function(index) {
			subj = $(this).text();
			if(subj == arr_subj[i]) {
				spl = $(".spl-cell:eq("+index+")").text();
				arr_spl.push(spl);
				necessary_tf = $("input[name='spl_tf[]']:eq("+index+")").val();
			}
		});
		
		$("input[name='spl[]']:eq("+i+")").val(arr_spl.join('@#'));
		if( necessary_tf == 'T' ) $("input[name='necessary_tf[]']:eq(" + i + ")").prop( 'checked', true );
	}
	//console.log( subj ); console.log( spl );
	<?php } ?>
	// 입력필드추가
	$("#add_supply_row").click(function() {
		var $el = $("#sit_supply_frm tr:last");
		var fld = "<tr>\n";
		fld += "<th scope=\"row\">\n";
		fld += "<label for=\"\">옵션</label>\n";
		fld += "<input type=\"text\" name=\"spl_subject[]\" value=\"\" class=\"frm_input\" size=\"15\">\n";
		fld += "</th>\n";
		fld += "<td>\n";
		fld += "<label for=\"\"><b>항목</b></label>\n";
		fld += "<input type=\"text\" name=\"spl[]\" value=\"\" class=\"frm_input\" size=\"40\">\n";
		fld += "<input type=\"checkbox\" name=\"necessary_tf[]\" value=\"T\" > *필수옵션으로 사용\n";
		fld += "<button type=\"button\" id=\"del_supply_row\" class=\"btn_frmline\">삭제</button>\n";
		fld += "</td>\n";
		fld += "</tr>";

		$el.after(fld);

		supply_sequence();
	});

	// 입력필드삭제
	$(document).on("click", "#del_supply_row", function() {
		$(this).closest("tr").remove();

		supply_sequence();
	});

	// 옵션목록생성
	$("#supply_table_create").click(function() {
		var it_id = $.trim($("#it_id").val());
		//var it_id = $.trim($("input[name=it_id]").val());
		var subject = new Array();
		var supply = new Array();
		var necessary_type = new Array();
		var subj, spl, necessary_tf;
		var count = 0;
		var $el_subj = $("input[name='spl_subject[]']");
		var $el_spl = $("input[name='spl[]']");
		var $el_necessary_tf = $("input[name='necessary_tf[]']");
		var $supply_table = $("#sit_option_addfrm");

		$el_subj.each(function(index) {
			subj = $.trim($(this).val());
			spl = $.trim($el_spl.eq(index).val());
			if( $el_necessary_tf.eq(index).prop( 'checked' ) ) {
				necessary_tf = 'T';
			} else {
				necessary_tf = 'F';
			}
			
			if(subj && spl) {
				subject.push(subj);
				supply.push(spl);
				necessary_type.push(necessary_tf);
				count++;
			}
		});

		if(!count) {
			alert("추가옵션명과 추가옵션항목을 입력해 주십시오.");
			return false;
		}
		
		$.post(
			"../admin/ajax_productoption_plus.php",
			{ it_id: it_id, w: "u", 'subject[]': subject, 'supply[]': supply, 'necessary_tf[]': necessary_type },
			function(data) {
				$supply_table.empty().html(data);
			}
		);
	});

	// 모두선택
	$(document).on("click", "input[name=spl_chk_all]", function() {
		if($(this).is(":checked")) {
			$("input[name='spl_chk[]']").attr("checked", true);
		} else {
			$("input[name='spl_chk[]']").attr("checked", false);
		}
	});

	// 선택삭제
	$(document).on("click", "#sel_supply_delete", function() {
		var $el = $("input[name='spl_chk[]']:checked");
		if($el.size() < 1) {
			alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
			return false;
		}

		$el.closest("tr").remove();
	});

	// 일괄적용
	$(document).on("click", "#spl_value_apply", function() {
		if($(".spl_com_chk:checked").size() < 1) {
			alert("일괄 수정할 항목을 하나이상 체크해 주십시오.");
			return false;
		}

		var spl_price = $.trim($("#spl_com_price").val());
		var spl_stock = $.trim($("#spl_com_stock").val());
		var spl_noti = $.trim($("#spl_com_noti").val());
		var spl_use = $("#spl_com_use").val();
		var $el = $("input[name='spl_chk[]']:checked");

		// 체크된 옵션이 있으면 체크된 것만 적용
		if($el.size() > 0) {
			var $tr;
			$el.each(function() {
				$tr = $(this).closest("tr");

				if($("#spl_com_price_chk").is(":checked"))
					$tr.find("input[name='spl_price[]']").val(spl_price);

				if($("#spl_com_stock_chk").is(":checked"))
					$tr.find("input[name='spl_stock_qty[]']").val(spl_stock);

				if($("#spl_com_noti_chk").is(":checked"))
					$tr.find("input[name='spl_noti_qty[]']").val(spl_noti);

				if($("#spl_com_use_chk").is(":checked"))
					$tr.find("select[name='spl_use[]']").val(spl_use);
			});
		} else {
			if($("#spl_com_price_chk").is(":checked"))
				$("input[name='spl_price[]']").val(spl_price);

			if($("#spl_com_stock_chk").is(":checked"))
				$("input[name='spl_stock_qty[]']").val(spl_stock);

			if($("#spl_com_noti_chk").is(":checked"))
				$("input[name='spl_noti_qty[]']").val(spl_noti);

			if($("#spl_com_use_chk").is(":checked"))
				$("select[name='spl_use[]']").val(spl_use);
		}
	});
    //필수옵션 체크
    $(document).on( 'click', 'input[name="necessary_tf[]"]', function( event ) {
        var this_index = $('input[name="necessary_tf[]"]').index( $(this) );
        var this_object = $(this);
        if( this_index > 0 && $(this).prop('checked') ) {
            $('input[name="necessary_tf[]"]').each( function( i, obj ) { // 체크할 경우
                if( i < this_index && !$(this).prop('checked') ){
                    alert('필수 옵션을 위부터 체크 해주세요.');
                    $(this_object).prop( 'checked', false );
                    return false;
                }
            });
        } else if( !$(this).prop('checked') ){ // 해제할 경우
            for( var i = $('input[name="necessary_tf[]"]').length - 1; i >= 0; i-- ){
                if( i > this_index && $('input[name="necessary_tf[]"]').eq( i ).prop('checked') ){
                    alert('필수 옵션을 아래부터 해제 해주세요.');
                    $(this_object).prop( 'checked', true );
                    return false;
                }
            }
        }
    });

});

function supply_sequence()
{
	var $tr = $("#sit_supply_frm tr");
	var seq;
	var th_label, td_label;

	$tr.each(function(index) {
		seq = index + 1;
		$(this).find("th label").attr("for", "spl_subject_"+seq).text("옵션");
		$(this).find("th input").attr("id", "spl_subject_"+seq);
		$(this).find("td label").attr("for", "spl_item_"+seq);
		$(this).find("td label b").text("항목");
		$(this).find("td input").attr("id", "spl_item_"+seq);
	});
}

<?php if(strpos($_data->maximage, "http://") !== false) { ?>
	document.form1.use_imgurl.checked = true;
	for(var jj=1;jj<=3;jj++) {
		idx=jj;
		if(idx==1) idx="";
		document.form1["userfile"+idx].style.display='none';
		document.form1["userfile"+idx+"_url"].style.display='';
		document.form1["userfile"+idx].disabled=true;
		document.form1["userfile"+idx+"_url"].disabled=false;
	}
	$(".file_type").hide();
	$(".url_type").show();
<?php } ?>
</script>
<!-- //독립형 옵션 -->

<?php
}
include("copyright.php"); 
