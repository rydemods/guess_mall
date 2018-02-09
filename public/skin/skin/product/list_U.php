<?php
$codename=$_cdata->code_name;
$clipcopy="\"javascript:ClipCopy('http://".$_ShopInfo->getShopurl2()."?".$_SERVER['QUERY_STRING']."')\"";

$codenavi="";
if($num=strpos($body,"[CODENAVI")) {
	$s_tmp=explode("_",substr($body,$num+9,13));
	$codenavi=getCodeLoc($code,$s_tmp[0],$s_tmp[1]);
}

//추천상품 관련
$hotitem_type="";
if($num=strpos($body,"[HOTITEM")) {
	$hotitem_type=$body[$num+8];
	if($hotitem_type=="1") {	//이미지A형
		$hotitem_type="";
		$match=array();
		$default_hotitem1=array("5","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[HOTITEM1([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_hotitem1[$i]=$match_array[0][$i];
			}
			$hotitem_type="1";
		}

		$hotitem1_cols=(int)$default_hotitem1[0];
		$hotitem1_rows=(int)$default_hotitem1[1];
		$hotitem1_rowline=$default_hotitem1[2];		// 상품세로라인여부
		$hotitem1_colline=$default_hotitem1[3];		// 상품가로라인여부
		$hotitem1_price=$default_hotitem1[4];		// 소비자가 표시여부
		$hotitem1_reserve=$default_hotitem1[5];		// 적립금 표시여부
		$hotitem1_tag=(int)$default_hotitem1[6];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$hotitem1_production=$default_hotitem1[7];	// 제조사 표시여부
		$hotitem1_madein=$default_hotitem1[8];	// 원산지 표시여부
		$hotitem1_model=$default_hotitem1[9];	// 모델명 표시여부
		$hotitem1_brand=$default_hotitem1[10];	// 브랜드 표시여부
		if($hotitem1_cols==0 || $hotitem1_cols==9) $hotitem1_cols=5;
		if($hotitem1_rows==0 || $hotitem1_rows==9) $hotitem1_rows=1;
		$hotitem1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($hotitem1_gan==0) $hotitem1_gan=5;

		$hotitem1_colnum=$hotitem1_cols*2-1;
		$hotitem1_product_num=$hotitem1_cols*$hotitem1_rows;
		if($hotitem1_cols==6)		$hotitem1_imgsize=$_data->primg_minisize-5;
		else if($hotitem1_cols==7)	$hotitem1_imgsize=$_data->primg_minisize-10;
		else if($hotitem1_cols==8)	$hotitem1_imgsize=$_data->primg_minisize-20;
		else						$hotitem1_imgsize=$_data->primg_minisize;

		$hotitem_product_num=$hotitem1_product_num;
	} else if($hotitem_type=="2") {	//이미지B형
		$hotitem_type="";
		$match=array();
		$default_hotitem2=array("2","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[HOTITEM2([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_hotitem2[$i]=$match_array[0][$i];
			}
			$hotitem_type="2";
		}

		$hotitem2_cols=(int)$default_hotitem2[0];
		$hotitem2_rows=(int)$default_hotitem2[1];
		$hotitem2_rowline=$default_hotitem2[2];		// 상품세로라인여부
		$hotitem2_colline=$default_hotitem2[3];		// 상품가로라인여부
		$hotitem2_price=$default_hotitem2[4];		// 소비자가 표시여부
		$hotitem2_reserve=$default_hotitem2[5];		// 적립금 표시여부
		$hotitem2_tag=(int)$default_hotitem2[6];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$hotitem2_production=$default_hotitem2[7];	// 제조사 표시여부
		$hotitem2_madein=$default_hotitem2[8];	// 원산지 표시여부
		$hotitem2_model=$default_hotitem2[9];	// 모델명 표시여부
		$hotitem2_brand=$default_hotitem2[10];	// 브랜드 표시여부
		if($hotitem2_cols==0 || $hotitem2_cols==9) $hotitem2_cols=5;
		if($hotitem2_rows==0 || $hotitem2_rows==9) $hotitem2_rows=1;
		$hotitem2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($hotitem2_gan==0) $hotitem2_gan=5;

		$hotitem2_colnum=$hotitem2_cols*2-1;
		$hotitem2_product_num=$hotitem2_cols*$hotitem2_rows;
		if($hotitem2_cols==6)		$hotitem2_imgsize=$_data->primg_minisize-5;
		else if($hotitem2_cols==7)	$hotitem2_imgsize=$_data->primg_minisize-10;
		else if($hotitem2_cols==8)	$hotitem2_imgsize=$_data->primg_minisize-20;
		else						$hotitem2_imgsize=$_data->primg_minisize;

		$hotitem_product_num=$hotitem2_product_num;
	} else if($hotitem_type=="3") {	//리스트형
		$hotitem_type="";
		$match=array();
		$default_hotitem3=array("5","N","Y","Y","0","N","N","N");
		if (preg_match("/\[HOTITEM3([0-9NY]{2,9})\]/",$body,$match)) {
			$ii=0;
			for ($i=0;$i<strlen($match[1]);$i++) {
				if($i==0) {
					$default_hotitem3[$ii]=$match[1][$i++].$match[1][$i];
				} else {
					$default_hotitem3[$ii]=$match[1][$i];
				}
				$ii++;
			}
			$hotitem_type="3";
		}

		$hotitem3_product_num=(int)$default_hotitem3[0];
		$hotitem3_production=$default_hotitem3[1];	// 제조사 표시여부
		$hotitem3_price=$default_hotitem3[2];		// 소비자가 표시여부
		$hotitem3_reserve=$default_hotitem3[3];		// 적립금 표시여부
		$hotitem3_tag=(int)$default_hotitem3[4];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$hotitem3_madein=$default_hotitem3[5];	// 원산지 표시여부
		$hotitem3_model=$default_hotitem3[6];	// 모델명 표시여부
		$hotitem3_brand=$default_hotitem3[7];	// 브랜드 표시여부
		if($hotitem3_product_num<0 || $hotitem3_product_num>20) $hotitem3_product_num=5;

		$hotitem_product_num=$hotitem3_product_num;
	}
}

//신규상품 관련
$newitem_type="";
if($num=strpos($body,"[NEWITEM")) {
	$newitem_type=$body[$num+8];
	if($newitem_type=="1") {	//이미지A형
		$newitem_type="";
		$match=array();
		$default_newitem1=array("5","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[NEWITEM1([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_newitem1[$i]=$match_array[0][$i];
			}
			$newitem_type="1";
		}

		$newitem1_cols=(int)$default_newitem1[0];
		$newitem1_rows=(int)$default_newitem1[1];
		$newitem1_rowline=$default_newitem1[2];		// 상품세로라인여부
		$newitem1_colline=$default_newitem1[3];		// 상품가로라인여부
		$newitem1_price=$default_newitem1[4];		// 소비자가 표시여부
		$newitem1_reserve=$default_newitem1[5];		// 적립금 표시여부
		$newitem1_tag=(int)$default_newitem1[6];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$newitem1_production=$default_newitem1[7];	// 제조사 표시여부
		$newitem1_madein=$default_newitem1[8];	// 원산지 표시여부
		$newitem1_model=$default_newitem1[9];	// 모델명 표시여부
		$newitem1_brand=$default_newitem1[10];	// 브랜드 표시여부

		if($newitem1_cols==0 || $newitem1_cols==9) $newitem1_cols=5;
		if($newitem1_rows==0 || $newitem1_rows==9) $newitem1_rows=1;
		$newitem1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($newitem1_gan==0) $newitem1_gan=5;

		$newitem1_colnum=$newitem1_cols*2-1;
		$newitem1_product_num=$newitem1_cols*$newitem1_rows;
		if($newitem1_cols==6)		$newitem1_imgsize=$_data->primg_minisize-5;
		else if($newitem1_cols==7)	$newitem1_imgsize=$_data->primg_minisize-10;
		else if($newitem1_cols==8)	$newitem1_imgsize=$_data->primg_minisize-20;
		else						$newitem1_imgsize=$_data->primg_minisize;

		$newitem_product_num=$newitem1_product_num;
	} else if($newitem_type=="2") {	//이미지B형
		$newitem_type="";
		$match=array();
		$default_newitem2=array("2","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[NEWITEM2([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_newitem2[$i]=$match_array[0][$i];
			}
			$newitem_type="2";
		}

		$newitem2_cols=(int)$default_newitem2[0];
		$newitem2_rows=(int)$default_newitem2[1];
		$newitem2_rowline=$default_newitem2[2];		// 상품세로라인여부
		$newitem2_colline=$default_newitem2[3];		// 상품가로라인여부
		$newitem2_price=$default_newitem2[4];		// 소비자가 표시여부
		$newitem2_reserve=$default_newitem2[5];		// 적립금 표시여부
		$newitem2_tag=(int)$default_newitem2[6];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$newitem2_production=$default_newitem2[7];	// 제조사 표시여부
		$newitem2_madein=$default_newitem2[8];	// 원산지 표시여부
		$newitem2_model=$default_newitem2[9];	// 모델명 표시여부
		$newitem2_brand=$default_newitem2[10];	// 브랜드 표시여부
		if($newitem2_cols==0 || $newitem2_cols==9) $newitem2_cols=5;
		if($newitem2_rows==0 || $newitem2_rows==9) $newitem2_rows=1;
		$newitem2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($newitem2_gan==0) $newitem2_gan=5;

		$newitem2_colnum=$newitem2_cols*2-1;
		$newitem2_product_num=$newitem2_cols*$newitem2_rows;
		if($newitem2_cols==6)		$newitem2_imgsize=$_data->primg_minisize-5;
		else if($newitem2_cols==7)	$newitem2_imgsize=$_data->primg_minisize-10;
		else if($newitem2_cols==8)	$newitem2_imgsize=$_data->primg_minisize-20;
		else						$newitem2_imgsize=$_data->primg_minisize;

		$newitem_product_num=$newitem2_product_num;
	} else if($newitem_type=="3") {	//리스트형
		$newitem_type="";
		$match=array();
		$default_newitem3=array("5","N","Y","Y","0","N","N","N");
		if (preg_match("/\[NEWITEM3([0-9NY]{2,9})\]/",$body,$match)) {
			$ii=0;
			for ($i=0;$i<strlen($match[1]);$i++) {
				if($i==0) {
					$default_newitem3[$ii]=$match[1][$i++].$match[1][$i];
				} else {
					$default_newitem3[$ii]=$match[1][$i];
				}
				$ii++;
			}
			$newitem_type="3";
		}

		$newitem3_product_num=(int)$default_newitem3[0];
		$newitem3_production=$default_newitem3[1];	// 제조사 표시여부
		$newitem3_price=$default_newitem3[2];		// 소비자가 표시여부
		$newitem3_reserve=$default_newitem3[3];		// 적립금 표시여부
		$newitem3_tag=(int)$default_newitem3[4];	// 태그 표시갯수(0-9) 0일 경우 표시안함
		$newitem3_madein=$default_newitem3[5];	// 원산지 표시여부
		$newitem3_model=$default_newitem3[6];	// 모델명 표시여부
		$newitem3_brand=$default_newitem3[7];	// 브랜드 표시여부
		if($newitem3_product_num<0 || $newitem3_product_num>20) $newitem3_product_num=5;

		$newitem_product_num=$newitem3_product_num;
	}
}

//인기상품 관련
$bestitem_type="";
if($num=strpos($body,"[BESTITEM")) {
	$bestitem_type=$body[$num+9];
	if($bestitem_type=="1") {	//이미지A형
		$bestitem_type="";
		$match=array();
		$default_bestitem1=array("5","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[BESTITEM1([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_bestitem1[$i]=$match_array[0][$i];
			}
			$bestitem_type="1";
		}

		$bestitem1_cols=(int)$default_bestitem1[0];
		$bestitem1_rows=(int)$default_bestitem1[1];
		$bestitem1_rowline=$default_bestitem1[2];		// 상품세로라인여부
		$bestitem1_colline=$default_bestitem1[3];		// 상품가로라인여부
		$bestitem1_price=$default_bestitem1[4];			// 소비자가 표시여부
		$bestitem1_reserve=$default_bestitem1[5];		// 적립금 표시여부
		$bestitem1_tag=(int)$default_bestitem1[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$bestitem1_production=$default_bestitem1[7];	// 제조사 표시여부
		$bestitem1_madein=$default_bestitem1[8];	// 원산지 표시여부
		$bestitem1_model=$default_bestitem1[9];	// 모델명 표시여부
		$bestitem1_brand=$default_bestitem1[10];	// 브랜드 표시여부
		if($bestitem1_cols==0 || $bestitem1_cols==9) $bestitem1_cols=5;
		if($bestitem1_rows==0 || $bestitem1_rows==9) $bestitem1_rows=1;
		$bestitem1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($bestitem1_gan==0) $bestitem1_gan=5;

		$bestitem1_colnum=$bestitem1_cols*2-1;
		$bestitem1_product_num=$bestitem1_cols*$bestitem1_rows;
		if($bestitem1_cols==6)		$bestitem1_imgsize=$_data->primg_minisize-5;
		else if($bestitem1_cols==7)	$bestitem1_imgsize=$_data->primg_minisize-10;
		else if($bestitem1_cols==8)	$bestitem1_imgsize=$_data->primg_minisize-20;
		else						$bestitem1_imgsize=$_data->primg_minisize;

		$bestitem_product_num=$bestitem1_product_num;
	} else if($bestitem_type=="2") {	//이미지B형
		$bestitem_type="";
		$match=array();
		$default_bestitem2=array("2","1","N","N","N","N","0","N","N","N","N");
		if (preg_match("/\[BESTITEM2([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_bestitem2[$i]=$match_array[0][$i];
			}
			$bestitem_type="2";
		}

		$bestitem2_cols=(int)$default_bestitem2[0];
		$bestitem2_rows=(int)$default_bestitem2[1];
		$bestitem2_rowline=$default_bestitem2[2];		// 상품세로라인여부
		$bestitem2_colline=$default_bestitem2[3];		// 상품가로라인여부
		$bestitem2_price=$default_bestitem2[4];			// 소비자가 표시여부
		$bestitem2_reserve=$default_bestitem2[5];		// 적립금 표시여부
		$bestitem2_tag=(int)$default_bestitem2[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$bestitem2_production=$default_bestitem2[7];	// 제조사 표시여부
		$bestitem2_madein=$default_bestitem2[8];	// 원산지 표시여부
		$bestitem2_model=$default_bestitem2[9];	// 모델명 표시여부
		$bestitem2_brand=$default_bestitem2[10];	// 브랜드 표시여부
		if($bestitem2_cols==0 || $bestitem2_cols==9) $bestitem2_cols=5;
		if($bestitem2_rows==0 || $bestitem2_rows==9) $bestitem2_rows=1;
		$bestitem2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($bestitem2_gan==0) $bestitem2_gan=5;

		$bestitem2_colnum=$bestitem2_cols*2-1;
		$bestitem2_product_num=$bestitem2_cols*$bestitem2_rows;
		if($bestitem2_cols==6)		$bestitem2_imgsize=$_data->primg_minisize-5;
		else if($bestitem2_cols==7)	$bestitem2_imgsize=$_data->primg_minisize-10;
		else if($bestitem2_cols==8)	$bestitem2_imgsize=$_data->primg_minisize-20;
		else						$bestitem2_imgsize=$_data->primg_minisize;

		$bestitem_product_num=$bestitem2_product_num;
	} else if($bestitem_type=="3") {	//리스트형
		$bestitem_type="";
		$match=array();
		$default_bestitem3=array("5","N","Y","Y","0","N","N","N");
		if (preg_match("/\[BESTITEM3([0-9NY]{2,9})\]/",$body,$match)) {
			$ii=0;
			for ($i=0;$i<strlen($match[1]);$i++) {
				if($i==0) {
					$default_bestitem3[$ii]=$match[1][$i++].$match[1][$i];
				} else {
					$default_bestitem3[$ii]=$match[1][$i];
				}
				$ii++;
			}
			$bestitem_type="3";
		}

		$bestitem3_product_num=(int)$default_bestitem3[0];
		$bestitem3_production=$default_bestitem3[1];	// 제조사 표시여부
		$bestitem3_price=$default_bestitem3[2];			// 소비자가 표시여부
		$bestitem3_reserve=$default_bestitem3[3];		// 적립금 표시여부
		$bestitem3_tag=(int)$default_bestitem3[4];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$bestitem3_madein=$default_bestitem3[5];	// 원산지 표시여부
		$bestitem3_model=$default_bestitem3[6];	// 모델명 표시여부
		$bestitem3_brand=$default_bestitem3[7];	// 브랜드 표시여부
		if($bestitem3_product_num<0 || $bestitem3_product_num>20) $bestitem3_product_num=5;

		$bestitem_product_num=$bestitem3_product_num;
	}
}

//상품리스트 (리스트,이미지,공구형)
$prlist_type="";
if($num=strpos($body,"[PRLIST")) {
	$prlist_type=$body[$num+7];
	if($prlist_type=="1") {	//이미지A형
		$prlist_type="";
		$match=array();
		$default_prlist1=array("5","2","N","Y","N","N","0","N","N","N","N");
		if (preg_match("/\[PRLIST1([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_prlist1[$i]=$match_array[0][$i];
			}
			$prlist_type="1";
		}

		$prlist1_cols=(int)$default_prlist1[0];
		$prlist1_rows=(int)$default_prlist1[1];
		$prlist1_rowline=$default_prlist1[2];		// 상품세로라인여부
		$prlist1_colline=$default_prlist1[3];		// 상품가로라인여부
		$prlist1_price=$default_prlist1[4];			// 소비자가 표시여부
		$prlist1_reserve=$default_prlist1[5];		// 적립금 표시여부
		$prlist1_tag=(int)$default_prlist1[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist1_production=$default_prlist1[7];	// 제조사 표시여부
		$prlist1_madein=$default_prlist1[8];	// 원산지 표시여부
		$prlist1_model=$default_prlist1[9];	// 모델명 표시여부
		$prlist1_brand=$default_prlist1[10];	// 브랜드 표시여부
		if($prlist1_cols==0 || $prlist1_cols==9) $prlist1_cols=5;
		if($prlist1_rows==0 || $prlist1_rows==9) $prlist1_rows=2;
		$prlist1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($prlist1_gan==0) $prlist1_gan=5;

		$prlist1_colnum=$prlist1_cols*2-1;
		$prlist1_product_num=$prlist1_cols*$prlist1_rows;
		if($prlist1_cols==6)		$prlist1_imgsize=$_data->primg_minisize-5;
		else if($prlist1_cols==7)	$prlist1_imgsize=$_data->primg_minisize-10;
		else if($prlist1_cols==8)	$prlist1_imgsize=$_data->primg_minisize-20;
		else						$prlist1_imgsize=$_data->primg_minisize;

		$setup[list_num]=$prlist1_product_num;
	} else if($prlist_type=="2") {	//이미지B형
		$prlist_type="";
		$match=array();
		$default_prlist2=array("2","5","N","Y","N","N","0","N","N","N","N");
		if (preg_match("/\[PRLIST2([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_prlist2[$i]=$match_array[0][$i];
			}
			$prlist_type="2";
		}

		$prlist2_cols=(int)$default_prlist2[0];
		$prlist2_rows=(int)$default_prlist2[1];
		$prlist2_rowline=$default_prlist2[2];		// 상품세로라인여부
		$prlist2_colline=$default_prlist2[3];		// 상품가로라인여부
		$prlist2_price=$default_prlist2[4];			// 소비자가 표시여부
		$prlist2_reserve=$default_prlist2[5];		// 적립금 표시여부
		$prlist2_tag=(int)$default_prlist2[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist2_production=$default_prlist2[7];	// 제조사 표시여부
		$prlist2_madein=$default_prlist2[8];	// 원산지 표시여부
		$prlist2_model=$default_prlist2[9];	// 모델명 표시여부
		$prlist2_brand=$default_prlist2[10];	// 브랜드 표시여부
		if($prlist2_cols==0 || $prlist2_cols==9) $prlist2_cols=5;
		if($prlist2_rows==0 || $prlist2_rows==9) $prlist2_rows=2;
		$prlist2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($prlist2_gan==0) $prlist2_gan=5;

		$prlist2_colnum=$prlist2_cols*2-1;
		$prlist2_product_num=$prlist2_cols*$prlist2_rows;
		if($prlist2_cols==6)		$prlist2_imgsize=$_data->primg_minisize-5;
		else if($prlist2_cols==7)	$prlist2_imgsize=$_data->primg_minisize-10;
		else if($prlist2_cols==8)	$prlist2_imgsize=$_data->primg_minisize-20;
		else						$prlist2_imgsize=$_data->primg_minisize;

		$setup[list_num]=$prlist2_product_num;
	} else if($prlist_type=="3") {	//리스트형
		$prlist_type="";
		$match=array();
		$default_prlist3=array("15","Y","N","Y","Y","0","N","N","N");
		if (preg_match("/\[PRLIST3([0-9NY]{2,10})\]/",$body,$match)) {
			$ii=0;
			for ($i=0;$i<strlen($match[1]);$i++) {
				if($i==0) {
					$default_prlist3[$ii]=$match[1][$i++].$match[1][$i];
				} else {
					$default_prlist3[$ii]=$match[1][$i];
				}
				$ii++;
			}
			$prlist_type="3";
		}

		$prlist3_product_num=(int)$default_prlist3[0];
		$prlist3_image_yn=$default_prlist3[1];
		$prlist3_production=$default_prlist3[2];	// 제조사 표시여부
		$prlist3_price=$default_prlist3[3];			// 소비자가 표시여부
		$prlist3_reserve=$default_prlist3[4];		// 적립금 표시여부
		$prlist3_tag=(int)$default_prlist3[5];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist3_madein=$default_prlist3[6];	// 원산지 표시여부
		$prlist3_model=$default_prlist3[7];	// 모델명 표시여부
		$prlist3_brand=$default_prlist3[8];	// 브랜드 표시여부
		if($prlist3_product_num<10 || $prlist3_product_num>50) $prlist3_product_num=15;

		$setup[list_num]=$prlist3_product_num;
	} else if($prlist_type=="4") {	//공동구매형
		$prlist_type="";
		if (preg_match("/\[PRLIST4([1-8]{2}(\_){0,1}[0-9]{0,2})\]/",$body,$match)) {
			$prlist_type="4";
			$match_array=explode("_",$match[1]);
			$prlist4_cols=(int)$match_array[0][0];
			$prlist4_rows=(int)$match_array[0][1];

			if($prlist4_cols==0 || $prlist4_cols==9) $prlist4_cols=3;
			if($prlist4_rows==0 || $prlist4_rows==9) $prlist4_rows=3;
			$prlist4_colnum=$prlist4_cols*2-1;
			$prlist4_product_num=$prlist4_cols*$prlist4_rows;
			$prlist4_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
			if($prlist4_gan==0) $prlist4_gan=5;
		}
		$setup[list_num]=$prlist4_product_num;
	}
}



include("productlist_text.php");
$pattern=array(
	"(\[CODENAME\])",
	"(\[CODENAVI([0-9a-fA-F_]{0,13})\])",
	"(\[CLIPCOPY\])",
	"(\[CODEEVENT\])",
	"(\[CODEGROUP\])",
	"(\[HOTITEM1([0-9LNY_]{2,14})\])",
	"(\[HOTITEM2([0-9LNY_]{2,14})\])",
	"(\[HOTITEM3([0-9NY]{2,9})\])",
	"(\[NEWITEM1([0-9LNY_]{2,14})\])",
	"(\[NEWITEM2([0-9LNY_]{2,14})\])",
	"(\[NEWITEM3([0-9NY]{2,9})\])",
	"(\[BESTITEM1([0-9LNY_]{2,14})\])",
	"(\[BESTITEM2([0-9LNY_]{2,14})\])",
	"(\[BESTITEM3([0-9NY]{2,9})\])",
	"(\[PRLIST1([0-9LNY_]{2,14})\])",
	"(\[PRLIST2([0-9LNY_]{2,14})\])",
	"(\[PRLIST3([0-9NY]{2,10})\])",
	"(\[PRLIST4([1-8]{2}(\_){0,1}[0-9]{0,2})\])",
	"(\[TOTAL\])",
	"(\[SORTPRODUCTUP\])",
	"(\[SORTPRODUCTDN\])",
	"(\[SORTNAMEUP\])",
	"(\[SORTNAMEDN\])",
	"(\[SORTPRICEUP\])",
	"(\[SORTPRICEDN\])",
	"(\[SORTRESERVEUP\])",
	"(\[SORTRESERVEDN\])",
	"(\[PAGE\])"
);
$replace=array($codename,$codenavi,$clipcopy,$codeevent,$codegroup,$hotitem1,$hotitem2,$hotitem3,$newitem1,$newitem2,$newitem3,$bestitem1,$bestitem2,$bestitem3,$prlist1,$prlist2,$prlist3,$prlist4,$t_count,"javascript:ChangeSort('production')","javascript:ChangeSort('production_desc')","javascript:ChangeSort('name')","javascript:ChangeSort('name_desc')","javascript:ChangeSort('price')","javascript:ChangeSort('price_desc')","javascript:ChangeSort('reserve')","javascript:ChangeSort('reserve_desc')",$list_page);

$body=preg_replace($pattern,$replace,$body);

echo $body;
