<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<!-- ShoppingMall Version <?=_IncomuShopVersionNo?>(<?=_IncomuShopVersionDate?>) //-->
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?=$onload?>
//-->
</SCRIPT>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function poll_result(type,code) {
	if(type=="result") {
		k=0;
		try {
			for (i=0;i<document.poll_form.poll_sel.length;i++) {
				if(document.poll_form.poll_sel[i].checked) {
					url="<?=$Dir.FrontDir?>survey.php?type=result&survey_code="+code+"&val="+document.poll_form.poll_sel[i].value;
					k=1;
				}
			}
		} catch (e) {}
		if (k==1) {
			window.open(url,"survey","width=450,height=400,scrollbars=yes");
		} else {
			alert ("투표하실 항목을 선택해 주세요");return;
		}
	} else {
		window.open ("<?=$Dir.FrontDir?>survey.php?type=view&survey_code="+code,"survey","width=450,height=400,scrollbars=yes"); 
	}
}

//-->
</SCRIPT>
</HEAD>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?php 
include ($Dir.MainDir."nomenu.php") 
?>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center nowrap>
<?php
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='mainpage' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$main_body=$row->body;
		$main_body=str_replace("[DIR]",$Dir,$main_body);
	}
	pmysql_free_result($result);

	if(strlen($main_body)>0) {
		//추천상품 관련
		$hotitem_type="";
		if($num=strpos($main_body,"[HOTITEM")) {
			$hotitem_type=substr($main_body,$num+8,1);
			if($hotitem_type=="1") {	//이미지A형
				$hotitem_type="";
				$match=array();
				$default_hotitem1=array("4","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[HOTITEM1([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_hotitem1[$i]=$match_array[0][$i];
					}
					$hotitem_type="1";
				}

				$hotitem1_cols=(int)$default_hotitem1[0];
				$hotitem1_rows=(int)$default_hotitem1[1];
				$hotitem1_title=$default_hotitem1[2];
				$hotitem1_rowline=$default_hotitem1[3];		// 상품세로라인여부
				$hotitem1_colline=$default_hotitem1[4];		// 상품가로라인여부
				$hotitem1_price=$default_hotitem1[5];		// 소비자가 표시여부
				$hotitem1_reserve=$default_hotitem1[6];		// 적립금 표시여부
				$hotitem1_tag=(int)$default_hotitem1[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$hotitem1_production=$default_hotitem1[8];	// 제조사 표시여부
				$hotitem1_madein=$default_hotitem1[9];	// 원산지 표시여부
				$hotitem1_model=$default_hotitem1[10];	// 모델명 표시여부
				$hotitem1_brand=$default_hotitem1[11];	// 브랜드 표시여부
				if($hotitem1_cols==0 || $hotitem1_cols==9) $hotitem1_cols=4;
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
				$default_hotitem2=array("2","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[HOTITEM2([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_hotitem2[$i]=$match_array[0][$i];
					}
					$hotitem_type="2";
				}

				$hotitem2_cols=(int)$default_hotitem2[0];
				$hotitem2_rows=(int)$default_hotitem2[1];
				$hotitem2_title=$default_hotitem2[2];
				$hotitem2_rowline=$default_hotitem2[3];		// 상품세로라인여부
				$hotitem2_colline=$default_hotitem2[4];		// 상품가로라인여부
				$hotitem2_price=$default_hotitem2[5];		// 소비자가 표시여부
				$hotitem2_reserve=$default_hotitem2[6];		// 적립금 표시여부
				$hotitem2_tag=(int)$default_hotitem2[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$hotitem2_production=$default_hotitem2[8];	// 제조사 표시여부
				$hotitem2_madein=$default_hotitem2[9];	// 원산지 표시여부
				$hotitem2_model=$default_hotitem2[10];	// 모델명 표시여부
				$hotitem2_brand=$default_hotitem2[11];	// 브랜드 표시여부
				if($hotitem2_cols==0 || $hotitem2_cols==9) $hotitem2_cols=4;
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
				$default_hotitem3=array("5","Y","N","Y","Y","0","N","N","N");
				if (preg_match("/\[HOTITEM3([\dNY]{2,10})\]/",$main_body,$match)) {
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
				$hotitem3_title=$default_hotitem3[1];
				$hotitem3_production=$default_hotitem3[2];	// 제조사 표시여부
				$hotitem3_price=$default_hotitem3[3];		// 소비자가 표시여부
				$hotitem3_reserve=$default_hotitem3[4];		// 적립금 표시여부
				$hotitem3_tag=(int)$default_hotitem3[5];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$hotitem3_madein=$default_hotitem3[6];	// 원산지 표시여부
				$hotitem3_model=$default_hotitem3[7];	// 모델명 표시여부
				$hotitem3_brand=$default_hotitem3[8];	// 브랜드 표시여부
				if($hotitem3_product_num<0 || $hotitem3_product_num>20) $hotitem3_product_num=5;

				$hotitem3_imgsize=$_data->primg_minisize;

				$hotitem_product_num=$hotitem3_product_num;
			}
		}

		//신규상품 관련
		$newitem_type="";
		if($num=strpos($main_body,"[NEWITEM")) {
			$newitem_type=substr($main_body,$num+8,1);
			if($newitem_type=="1") {	//이미지A형
				$newitem_type="";
				$match=array();
				$default_newitem1=array("4","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[NEWITEM1([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_newitem1[$i]=$match_array[0][$i];
					}
					$newitem_type="1";
				}

				$newitem1_cols=(int)$default_newitem1[0];
				$newitem1_rows=(int)$default_newitem1[1];
				$newitem1_title=$default_newitem1[2];
				$newitem1_rowline=$default_newitem1[3];		// 상품세로라인여부
				$newitem1_colline=$default_newitem1[4];		// 상품가로라인여부
				$newitem1_price=$default_newitem1[5];		// 소비자가 표시여부
				$newitem1_reserve=$default_newitem1[6];		// 적립금 표시여부
				$newitem1_tag=(int)$default_newitem1[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$newitem1_production=$default_newitem1[8];	// 제조사 표시여부
				$newitem1_madein=$default_newitem1[9];	// 원산지 표시여부
				$newitem1_model=$default_newitem1[10];	// 모델명 표시여부
				$newitem1_brand=$default_newitem1[11];	// 브랜드 표시여부
				if($newitem1_cols==0 || $newitem1_cols==9) $newitem1_cols=4;
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
				$default_newitem2=array("2","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[NEWITEM2([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_newitem2[$i]=$match_array[0][$i];
					}
					$newitem_type="2";
				}

				$newitem2_cols=(int)$default_newitem2[0];
				$newitem2_rows=(int)$default_newitem2[1];
				$newitem2_title=$default_newitem2[2];
				$newitem2_rowline=$default_newitem2[3];		// 상품세로라인여부
				$newitem2_colline=$default_newitem2[4];		// 상품가로라인여부
				$newitem2_price=$default_newitem2[5];		// 소비자가 표시여부
				$newitem2_reserve=$default_newitem2[6];		// 적립금 표시여부
				$newitem2_tag=(int)$default_newitem2[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$newitem2_production=$default_newitem2[8];	// 제조사 표시여부
				$newitem2_madein=$default_newitem2[9];	// 원산지 표시여부
				$newitem2_model=$default_newitem2[10];	// 모델명 표시여부
				$newitem2_brand=$default_newitem2[11];	// 브랜드 표시여부
				if($newitem2_cols==0 || $newitem2_cols==9) $newitem2_cols=4;
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
				$default_newitem3=array("5","Y","N","Y","Y","0","N","N","N");
				if (preg_match("/\[NEWITEM3([\dNY]{2,10})\]/",$main_body,$match)) {
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
				$newitem3_title=$default_newitem3[1];
				$newitem3_production=$default_newitem3[2];	// 제조사 표시여부
				$newitem3_price=$default_newitem3[3];		// 소비자가 표시여부
				$newitem3_reserve=$default_newitem3[4];		// 적립금 표시여부
				$newitem3_tag=(int)$default_newitem3[5];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$newitem3_madein=$default_newitem3[6];	// 원산지 표시여부
				$newitem3_model=$default_newitem3[7];	// 모델명 표시여부
				$newitem3_brand=$default_newitem3[8];	// 브랜드 표시여부
				if($newitem3_product_num<0 || $newitem3_product_num>20) $newitem3_product_num=5;

				$newitem3_imgsize=$_data->primg_minisize;

				$newitem_product_num=$newitem3_product_num;
			}
		}

		//인기상품 관련
		$bestitem_type="";
		if($num=strpos($main_body,"[BESTITEM")) {
			$bestitem_type=substr($main_body,$num+9,1);
			if($bestitem_type=="1") {	//이미지A형
				$bestitem_type="";
				$match=array();
				$default_bestitem1=array("4","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[BESTITEM1([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_bestitem1[$i]=$match_array[0][$i];
					}
					$bestitem_type="1";
				}

				$bestitem1_cols=(int)$default_bestitem1[0];
				$bestitem1_rows=(int)$default_bestitem1[1];
				$bestitem1_title=$default_bestitem1[2];
				$bestitem1_rowline=$default_bestitem1[3];		// 상품세로라인여부
				$bestitem1_colline=$default_bestitem1[4];		// 상품가로라인여부
				$bestitem1_price=$default_bestitem1[5];			// 소비자가 표시여부
				$bestitem1_reserve=$default_bestitem1[6];		// 적립금 표시여부
				$bestitem1_tag=(int)$default_bestitem1[7];		// 태그 표시갯수(0-9) 0일 경우 표시안함
				$bestitem1_production=$default_bestitem1[8];	// 제조사 표시여부
				$bestitem1_madein=$default_bestitem1[9];	// 원산지 표시여부
				$bestitem1_model=$default_bestitem1[10];	// 모델명 표시여부
				$bestitem1_brand=$default_bestitem1[11];	// 브랜드 표시여부
				if($bestitem1_cols==0 || $bestitem1_cols==9) $bestitem1_cols=4;
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
				$default_bestitem2=array("2","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[BESTITEM2([\dLNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_bestitem2[$i]=$match_array[0][$i];
					}
					$bestitem_type="2";
				}

				$bestitem2_cols=(int)$default_bestitem2[0];
				$bestitem2_rows=(int)$default_bestitem2[1];
				$bestitem2_title=$default_bestitem2[2];
				$bestitem2_rowline=$default_bestitem2[3];		// 상품세로라인여부
				$bestitem2_colline=$default_bestitem2[4];		// 상품가로라인여부
				$bestitem2_price=$default_bestitem2[5];			// 소비자가 표시여부
				$bestitem2_reserve=$default_bestitem2[6];		// 적립금 표시여부
				$bestitem2_tag=(int)$default_bestitem2[7];		// 태그 표시갯수(0-9) 0일 경우 표시안함
				$bestitem2_production=$default_bestitem2[8];	// 제조사 표시여부
				$bestitem2_madein=$default_bestitem2[9];	// 원산지 표시여부
				$bestitem2_model=$default_bestitem2[10];	// 모델명 표시여부
				$bestitem2_brand=$default_bestitem2[11];	// 브랜드 표시여부
				if($bestitem2_cols==0 || $bestitem2_cols==9) $bestitem2_cols=4;
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
				$default_bestitem3=array("5","Y","N","Y","Y","0","N","N","N");
				if (preg_match("/\[BESTITEM3([\dNY]{2,10})\]/",$main_body,$match)) {
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
				$bestitem3_title=$default_bestitem3[1];
				$bestitem3_production=$default_bestitem3[2];// 제조사 표시여부
				$bestitem3_price=$default_bestitem3[3];		// 소비자가 표시여부
				$bestitem3_reserve=$default_bestitem3[4];	// 적립금 표시여부
				$bestitem3_tag=(int)$default_bestitem3[5];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$bestitem3_madein=$default_bestitem3[6];	// 원산지 표시여부
				$bestitem3_model=$default_bestitem3[7];	// 모델명 표시여부
				$bestitem3_brand=$default_bestitem3[8];	// 브랜드 표시여부
				if($bestitem3_product_num<0 || $bestitem3_product_num>20) $bestitem3_product_num=5;

				$bestitem3_imgsize=$_data->primg_minisize;

				$bestitem_product_num=$bestitem3_product_num;
			}
		}

		//특별상품 관련
		$speitem_type="";
		if($num=strpos($main_body,"[SPEITEM")) {
			$speitem_type=substr($main_body,$num+8,1);
			if($speitem_type=="0") {	//기존방식
				$speitem_type="";
				$match=array();
				$default_speitem0=array("3","N","N","Y","0","N","N","N","N");
				if (preg_match("/\[SPEITEM0([\dNY]{0,9})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_speitem0[$i]=$match_array[0][$i];
					}
					$speitem_type="0";
				}
				$speitem0_rows=(int)$default_speitem0[0];
				$speitem0_title=$default_speitem0[1];		// 특별 타이틀이미지 여부
				$speitem0_price=$default_speitem0[2];		// 소비자가 표시여부
				$speitem0_reserve=$default_speitem0[3];		// 적립금 표시여부
				$speitem0_tag=(int)$default_speitem0[4];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$speitem0_production=$default_speitem0[5];	// 제조사 표시여부
				$speitem0_madein=$default_speitem0[6];	// 원산지 표시여부
				$speitem0_model=$default_speitem0[7];	// 모델명 표시여부
				$speitem0_brand=$default_speitem0[8];	// 브랜드 표시여부
				$speitem0_product_num=$speitem0_rows;
				$speitem_product_num=$speitem0_product_num;
			} else if($speitem_type=="1") {	//이미지A형
				$speitem_type="";
				$match=array();
				$default_speitem1=array("4","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[SPEITEM1([0-9LNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_speitem1[$i]=$match_array[0][$i];
					}
					$speitem_type="1";
				}

				$speitem1_cols=(int)$default_speitem1[0];
				$speitem1_rows=(int)$default_speitem1[1];
				$speitem1_title=$default_speitem1[2];
				$speitem1_rowline=$default_speitem1[3];		// 상품세로라인여부
				$speitem1_colline=$default_speitem1[4];		// 상품가로라인여부
				$speitem1_price=$default_speitem1[5];		// 소비자가 표시여부
				$speitem1_reserve=$default_speitem1[6];		// 적립금 표시여부
				$speitem1_tag=(int)$default_speitem1[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$speitem1_production=$default_speitem1[8];	// 제조사 표시여부
				$speitem1_madein=$default_speitem1[9];	// 원산지 표시여부
				$speitem1_model=$default_speitem1[10];	// 모델명 표시여부
				$speitem1_brand=$default_speitem1[11];	// 브랜드 표시여부
				if($speitem1_cols==0 || $speitem1_cols==9) $speitem1_cols=4;
				if($speitem1_rows==0 || $speitem1_rows==9) $speitem1_rows=1;
				$speitem1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
				if($speitem1_gan==0) $speitem1_gan=5;

				$speitem1_colnum=$speitem1_cols*2-1;
				$speitem1_product_num=$speitem1_cols*$speitem1_rows;
				if($speitem1_cols==6)		$speitem1_imgsize=$_data->primg_minisize-5;
				else if($speitem1_cols==7)	$speitem1_imgsize=$_data->primg_minisize-10;
				else if($speitem1_cols==8)	$speitem1_imgsize=$_data->primg_minisize-20;
				else						$speitem1_imgsize=$_data->primg_minisize;

				$speitem_product_num=$speitem1_product_num;
			} else if($speitem_type=="2") {	//이미지B형
				$speitem_type="";
				$match=array();
				$default_speitem2=array("2","1","Y","N","N","N","N","0","N","N","N","N");
				if (preg_match("/\[SPEITEM2([0-9LNY_]{2,15})\]/",$main_body,$match)) {
					$match_array=explode("_",$match[1]);
					for ($i=0;$i<strlen($match_array[0]);$i++) {
						$default_speitem2[$i]=$match_array[0][$i];
					}
					$speitem_type="2";
				}

				$speitem2_cols=(int)$default_speitem2[0];
				$speitem2_rows=(int)$default_speitem2[1];
				$speitem2_title=$default_speitem2[2];
				$speitem2_rowline=$default_speitem2[3];		// 상품세로라인여부
				$speitem2_colline=$default_speitem2[4];		// 상품가로라인여부
				$speitem2_price=$default_speitem2[5];		// 소비자가 표시여부
				$speitem2_reserve=$default_speitem2[6];		// 적립금 표시여부
				$speitem2_tag=(int)$default_speitem2[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$speitem2_production=$default_speitem2[8];	// 제조사 표시여부
				$speitem2_madein=$default_speitem2[9];	// 원산지 표시여부
				$speitem2_model=$default_speitem2[10];	// 모델명 표시여부
				$speitem2_brand=$default_speitem2[11];	// 브랜드 표시여부
				if($speitem2_cols==0 || $speitem2_cols==9) $speitem2_cols=4;
				if($speitem2_rows==0 || $speitem2_rows==9) $speitem2_rows=1;
				$speitem2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
				if($speitem2_gan==0) $speitem2_gan=5;

				$speitem2_colnum=$speitem2_cols*2-1;
				$speitem2_product_num=$speitem2_cols*$speitem2_rows;
				if($speitem2_cols==6)		$speitem2_imgsize=$_data->primg_minisize-5;
				else if($speitem2_cols==7)	$speitem2_imgsize=$_data->primg_minisize-10;
				else if($speitem2_cols==8)	$speitem2_imgsize=$_data->primg_minisize-20;
				else						$speitem2_imgsize=$_data->primg_minisize;

				$speitem_product_num=$speitem2_product_num;
			} else if($speitem_type=="3") {	//리스트형
				$speitem_type="";
				$match=array();
				$default_speitem3=array("5","Y","N","Y","Y","0","N","N","N");
				if (preg_match("/\[SPEITEM3([0-9NY]{2,10})\]/",$main_body,$match)) {
					$ii=0;
					for ($i=0;$i<strlen($match[1]);$i++) {
						if($i==0) {
							$default_speitem3[$ii]=$match[1][$i++].$match[1][$i];
						} else {
							$default_speitem3[$ii]=$match[1][$i];
						}
						$ii++;
					}
					$speitem_type="3";
				}

				$speitem3_product_num=(int)$default_speitem3[0];
				$speitem3_title=$default_speitem3[1];
				$speitem3_production=$default_speitem3[2];	// 제조사 표시여부
				$speitem3_price=$default_speitem3[3];		// 소비자가 표시여부
				$speitem3_reserve=$default_speitem3[4];		// 적립금 표시여부
				$speitem3_tag=(int)$default_speitem3[5];	// 태그 표시갯수(0-9) 0일 경우 표시안함
				$speitem3_madein=$default_speitem3[6];	// 원산지 표시여부
				$speitem3_model=$default_speitem3[7];	// 모델명 표시여부
				$speitem3_brand=$default_speitem3[8];	// 브랜드 표시여부
				if($speitem3_product_num<0 || $speitem3_product_num>20) $speitem3_product_num=5;

				$speitem3_imgsize=$_data->primg_minisize;

				$speitem_product_num=$speitem3_product_num;
			}
		}


		//메인 카테고리 상품정보
		$codeitem_body=$main_body;
		$codeitem_array=array();
		$zz=0;
		while(true) {
			if($num=strpos($codeitem_body,"[CODEITEM")) {
				$codeitem_body=substr_replace($codeitem_body,"!C",$num+1,1);
				$tempbody=substr($codeitem_body,$num);
				if($tempnum=strpos($tempbody,"]")) {
					$codeitem=substr($codeitem_body,$num,$tempnum+1);
					$match=array();
					if (preg_match("/\[!CODEITEM([0-9LNY_]{8,29})\]/",$codeitem,$match)) {
						$codeitem=str_replace("!","",$codeitem);
						$codeitem_type=$codeitem[9];

						$match_array=explode("_",$match[1]);
						if($codeitem_type=="1" || $codeitem_type=="2") {	//이미지A형/B형
							if($codeitem_type=="1") {
								$default_codeitem=array("0","4","1","N","N","N","N","0","N","N","N","N");
							} else {
								$default_codeitem=array("0","2","1","N","N","N","N","0","N","N","N","N");
							}
							for ($i=0;$i<strlen($match_array[2]);$i++) {
								$default_codeitem[$i]=$match_array[2][$i];
							}
							$codeitem_code=$match_array[1];

							$codeitem_prget=(int)$default_codeitem[0];
							$codeitem_cols=(int)$default_codeitem[1];
							$codeitem_rows=(int)$default_codeitem[2];
							$codeitem_rowline=$default_codeitem[3];		// 상품세로라인여부
							$codeitem_colline=$default_codeitem[4];		// 상품가로라인여부
							$codeitem_price=$default_codeitem[5];		// 소비자가 표시여부
							$codeitem_reserve=$default_codeitem[6];		// 적립금 표시여부
							$codeitem_tag=(int)$default_codeitem[7];	// 태그 표시갯수(0-9) 0일 경우 표시안함
							$codeitem_production=$default_codeitem[8];	// 제조사 표시여부
							$codeitem_madein=$default_codeitem[9];	// 원산지 표시여부
							$codeitem_model=$default_codeitem[10];	// 모델명 표시여부
							$codeitem_brand=$default_codeitem[11];	// 브랜드 표시여부
							if($codeitem_cols==0 || $codeitem_cols==9) $codeitem_cols=4;
							if($codeitem_rows==0 || $codeitem_rows==9) $codeitem_rows=1;
							$codeitem_gan=(($match_array[3]+0)>99)?"99":($match_array[3]+0);
							if($codeitem_gan==0) $codeitem_gan=5;

							$codeitem_colnum=$codeitem_cols*2-1;
							$codeitem_product_num=$codeitem_cols*$codeitem_rows;
							if($codeitem_cols==6)		$codeitem_imgsize=$_data->primg_minisize-5;
							else if($codeitem_cols==7)	$codeitem_imgsize=$_data->primg_minisize-10;
							else if($codeitem_cols==8)	$codeitem_imgsize=$_data->primg_minisize-20;
							else						$codeitem_imgsize=$_data->primg_minisize;

							$codeitem_array[$zz]["macro"]=$codeitem;
							$codeitem_array[$zz]["type"]=$codeitem_type;
							$codeitem_array[$zz]["code"]=$codeitem_code;
							$codeitem_array[$zz]["prget"]=$codeitem_prget;
							$codeitem_array[$zz]["cols"]=$codeitem_cols;
							$codeitem_array[$zz]["rows"]=$codeitem_rows;
							$codeitem_array[$zz]["rowline"]=$codeitem_rowline;
							$codeitem_array[$zz]["colline"]=$codeitem_colline;
							$codeitem_array[$zz]["price"]=$codeitem_price;
							$codeitem_array[$zz]["reserve"]=$codeitem_reserve;
							$codeitem_array[$zz]["tag"]=$codeitem_tag;
							$codeitem_array[$zz]["gan"]=$codeitem_gan;
							$codeitem_array[$zz]["colnum"]=$codeitem_colnum;
							$codeitem_array[$zz]["product_num"]=$codeitem_product_num;
							$codeitem_array[$zz]["imgsize"]=$codeitem_imgsize;
							$codeitem_array[$zz]["production"]=$codeitem_production;
							$codeitem_array[$zz]["madein"]=$codeitem_madein;
							$codeitem_array[$zz]["model"]=$codeitem_model;
							$codeitem_array[$zz]["brand"]=$codeitem_brand;

							$zz++;
						} else if($codeitem_type=="3") {	//리스트형
							$default_codeitem=array("0","5","N","Y","Y","0","N","N","N");
							$ii=0;
							for ($i=0;$i<strlen($match_array[2]);$i++) {
								if($i==1) {
									$default_codeitem[$ii]=$match_array[2][$i++].$match_array[2][$i];
								} else {
									$default_codeitem[$ii]=$match_array[2][$i];
								}
								$ii++;
							}
							$codeitem_code=$match_array[1];

							$codeitem_prget=(int)$default_codeitem[0];
							$codeitem_product_num=(int)$default_codeitem[1];
							$codeitem_production=$default_codeitem[2];	// 제조사 표시여부
							$codeitem_price=$default_codeitem[3];		// 소비자가 표시여부
							$codeitem_reserve=$default_codeitem[4];		// 적립금 표시여부
							$codeitem_tag=(int)$default_codeitem[5];	// 태그 표시갯수(0-9) 0일 경우 표시안함
							$codeitem_madein=$default_codeitem[6];	// 원산지 표시여부
							$codeitem_model=$default_codeitem[7];	// 모델명 표시여부
							$codeitem_brand=$default_codeitem[8];	// 브랜드 표시여부
							if($codeitem_product_num<0 || $codeitem_product_num>20) $codeitem_product_num=5;

							$codeitem_imgsize=$_data->primg_minisize;
							$newitem_product_num=$codeitem_product_num;

							$codeitem_array[$zz]["macro"]=$codeitem;
							$codeitem_array[$zz]["type"]=$codeitem_type;
							$codeitem_array[$zz]["code"]=$codeitem_code;
							$codeitem_array[$zz]["prget"]=$codeitem_prget;
							$codeitem_array[$zz]["production"]=$codeitem_production;
							$codeitem_array[$zz]["price"]=$codeitem_price;
							$codeitem_array[$zz]["reserve"]=$codeitem_reserve;
							$codeitem_array[$zz]["tag"]=$codeitem_tag;
							$codeitem_array[$zz]["product_num"]=$codeitem_product_num;
							$codeitem_array[$zz]["imgsize"]=$codeitem_imgsize;
							$codeitem_array[$zz]["madein"]=$codeitem_madein;
							$codeitem_array[$zz]["model"]=$codeitem_model;
							$codeitem_array[$zz]["brand"]=$codeitem_brand;

							$zz++;
						}
					}
				}
			} else {
				break;
			}

			//최대 10개까지만 노출 가능
			if($zz>10) break;
		}

		//공동구매 관련 [GONGGU]:공동구매 메인화면 표시, [GONGGUN]:공동구매 메인화면 표시(타이틀 없음)
		if($num=strpos($main_body,"[GONGGU")) {
			$gonggu_type="Y";
			if(substr($main_body,$num+7,1)!="]") {
				$gonggu_title=substr($main_body,$num+7,1);
			} else {
				$gonggu_title="Y";
			}
		}

		//경매 관련 [AUCTION]:경매 메인화면 표시, [AUCTIONN]:경매 메인화면 표시(타이틀 없음)
		if($num=strpos($main_body,"[AUCTION")) {
			$auction_type="Y";
			if(substr($main_body,$num+8,1)!="]") {
				$auction_title=substr($main_body,$num+8,1);
			} else {
				$auction_title="Y";
			}
		}

		//배너 관련 [BANNER1]:현재 배너타입, [BANNER2]:가로로 표시되는 타입
		if($num=strpos($main_body,"[BANNER")) {
			$banner_type=substr($main_body,$num+7,1);
		}

		$match=array();
		$default_notice=array("1","Y","Y","4","N","2");
		if (preg_match("/\[NOTICE([0-9NY_]{1,9})\]/",$main_body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_notice[$i]=$match_array[0][$i];
			}
			$notice_yn="Y";
		}
		$notice_type=$default_notice[0];	// 공지사항 타입
		$notice_title=$default_notice[1];	// 공지사항 타이틀표시여부
		$notice_gan=$default_notice[2];		// 공지사항 사이 간격
		$notice_new=$default_notice[3];		// 공지사항 신규 아이콘 사용여부
		$notice_timegap=$default_notice[4]*24; // 공지사항 신규아이콘 지속 날짜
		$notice_ganyes="YES";
		$notice_titlelen=(($match_array[1]+0)>200)?"200":($match_array[1]+0); // 공지사항 글자의 길이

		$match=array();
		$default_info=array("1","Y","4");
		if (preg_match("/\[INFO([0-9NY_]{1,7})\]/",$main_body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0 ; $i < strlen($match_array[0]) ; $i ++) {
				$default_info[$i]=$match_array[0][$i];
			}
			$info_yn="Y";
		}
		$info_type=$default_info[0];	// 컨텐츠정보 타입
		$info_title=$default_info[1];	// 컨텐츠정보 타이틀표시여부
		$info_gan=$default_info[2];		// 컨텐츠정보 사이 간격
		$info_ganyes="YES";
		$info_titlelen=(($match_array[1]+0)>200)?"200":($match_array[1]+0); // 컨텐츠정보 글자의 길이

		$boardval=array();
		for($i=1;$i<=6;$i++) {
			if($num=strpos($main_body,"[BOARD".$i)) {
				$boardval[$i]->board_type="Y";
				$boardval[$i]->board_datetype=substr($main_body,$num+7,1);
				$boardval[$i]->board_num=(int)substr($main_body,$num+8,1);
				$boardval[$i]->board_gan=(int)substr($main_body,$num+9,1);
				$boardval[$i]->board_reply=substr($main_body,$num+10,1);

				$board_tmp=explode("_",substr($main_body,$num+1,strpos($main_body,"]",$num)-$num-1));

				$boardval[$i]->board_titlelen=$board_tmp[1];
				$boardval[$i]->board_code=substr($main_body,$num+13+strlen($boardval[$i]->board_titlelen),strpos($main_body,"]",$num)-$num-13-strlen($boardval[$i]->board_titlelen));
/*
				if($boardval[$i]->board_code!=$_ShopInfo->getDb() && (substr($boardval[$i]->board_code,0,strlen($_ShopInfo->getDb()))!=$_ShopInfo->getDb() || substr($boardval[$i]->board_code,strlen($_ShopInfo->getDb()),1)!="_")) {
					$boardval[$i]->board_code="";
					$boardval[$i]->board_type="";
				}
*/
				$boardval[$i]->board_titlelen=(int)$boardval[$i]->board_titlelen;
				if($boardval[$i]->board_num==0) $boardval[$i]->board_num=5;
				if(strlen($boardval[$i]->board_code)==0) $boardval[$i]->board_type="";
			}
		}

		if($_data->review_type!="N") {
			if($num=strpos($main_body,"[REVIEW")) {
				$review_ordertype=(int)substr($main_body,$num+7,1);
				$review_displaytype=(int)substr($main_body,$num+8,1);
				$review_datetype=(int)substr($main_body,$num+9,1);
				$review_num=(int)substr($main_body,$num+10,1);
				$review_gan=(int)substr($main_body,$num+11,1);
				$review_marks=substr($main_body,$num+12,1);

				$review_tmp=explode("_",substr($main_body,$num+1,strpos($main_body,"]",$num)-$num-1));
				$review_titlelen=(int)$review_tmp[1];

				if($review_num==0) $review_num=5;
				if($review_titlelen==0) $review_titlelen=40;
				if($review_reply!="Y") $review_reply=="N";
			}
		}

		include($Dir.MainDir."main_text.php");
		$pattern=array(
			"(\[SHOPINTRO\])",
			"(\[HOTITEM1([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[HOTITEM2([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[HOTITEM3([0-9]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})\])",
			"(\[NEWITEM1([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[NEWITEM2([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[NEWITEM3([0-9]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})\])",
			"(\[BESTITEM1([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[BESTITEM2([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[BESTITEM3([0-9]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})\])",
			"(\[SPEITEM0([1-9]{1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})\])",
			"(\[SPEITEM1([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[SPEITEM2([1-8]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})(\_){0,1}([0-9]{0,2})\])",
			"(\[SPEITEM3([0-9]{2})([YN]{0,1})([YNL]{0,1})([YN]{0,1})([YN]{0,1})([0-9]{0,1})([YN]{0,1})([YN]{0,1})([YN]{0,1})\])",

			"(\[GONGGU([N]{0,1})\])",
			"(\[AUCTION([N]{0,1})\])",
			"(\[NOTICE([1-4]{1})([YN]{0,1})([1-9]{0,1})([YN]{0,1})([1-9]{0,1})(\_){0,1}([0-9]{0,3})\])",
			"(\[INFO([1-4]{1})([YN]{0,1})([1-9]{0,1})(\_){0,1}([0-9]{0,3})\])",
			"(\[BANNER([1-2]{1})\])",
			"(\[POLL\])",
			"(\[POLL_TITLE(2){0,1}\])",
			"(\[POLL_CHOICE\])",
			"(\[POLL_BTN1\])",
			"(\[POLL_BTN2\])",
			"(\[BOARD1([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD2([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD3([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD4([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD5([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD6([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[LOGINFORM\])",
			"(\[LOGINFORMU\])",
			"(\[PRODUCTNEW\])",
			"(\[PRODUCTBEST\])",
			"(\[PRODUCTHOT\])",
			"(\[PRODUCTSPECIAL\])",
			"(\[REVIEW([0-1]{1})([0-1]{1})([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})\])"
		);
		$replace=array($shopintro,$hotitem1,$hotitem2,$hotitem3,$newitem1,$newitem2,$newitem3,$bestitem1,$bestitem2,$bestitem3,$speitem0,$speitem1,$speitem2,$speitem3,$gonggu,$auction,$notice,$info,$banner,$poll,$poll_title,$poll_choice,$poll_btn1,$poll_btn2,$board1,$board2,$board3,$board4,$board5,$board6,$main_loginform,$main_loginformu,$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php",$review);

		$main_body=preg_replace($pattern,$replace,$main_body);

		echo $main_body;

	} else {
		echo "메인 디자인 준비중입니다.";
	}
?>
	</td>
</tr>
</table>
<? include ($Dir."lib/bottom.php") ?>
<?//이벤트 팝업창 (main???.php에만 include)?>
<? include($Dir."lib/eventlayer.php") ?>
<div id="create_openwin" style="display:none"></div>
</BODY>
</HTML>