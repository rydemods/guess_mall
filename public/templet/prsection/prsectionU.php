<?php
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

include("productsection_text.php");
$pattern=array(
	"/(\[PRLIST1([0-9LNY_]{2,14})\])/",
	"/(\[PRLIST2([0-9LNY_]{2,14})\])/",
	"/(\[PRLIST3([0-9NY]{2,10})\])/",
	"/(\[PRLIST4([1-8]{2}(\_){0,1}[0-9]{0,2})\])/",
	"/(\[TOTAL\])/",
	"/(\[SORTPRODUCTUP\])/",
	"/(\[SORTPRODUCTDN\])/",
	"/(\[SORTNAMEUP\])/",
	"/(\[SORTNAMEDN\])/",
	"/(\[SORTPRICEUP\])/",
	"/(\[SORTPRICEDN\])/",
	"/(\[SORTRESERVEUP\])/",
	"/(\[SORTRESERVEDN\])/",
	"/(\[PAGE\])/"
);
$replace=array($prlist1,$prlist2,$prlist3,$prlist4,$t_count,"javascript:ChangeSort('production')","javascript:ChangeSort('production_desc')","javascript:ChangeSort('name')","javascript:ChangeSort('name_desc')","javascript:ChangeSort('price')","javascript:ChangeSort('price_desc')","javascript:ChangeSort('reserve')","javascript:ChangeSort('reserve_desc')",$list_page);

$body=preg_replace($pattern,$replace,$body);

echo $body;
