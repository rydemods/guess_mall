<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

$mode=$_POST["mode"];
$prcodes=$_POST["prcodes"];
$display=$_POST["display"];
if($mode=="display" && strlen($prcodes)>0 && ($display=="Y" || $display=="N")) {
	if($_venderdata->grant_product[1]!="Y") {
		echo "<html></head><body onload=\"alert('상품정보 수정 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	} else if($_venderdata->grant_product[3]!="N") {
		echo "<html></head><body onload=\"alert('쇼핑몰 운영자만 상품진열 수정이 가능합니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	}
	$prcodes=rtrim($prcodes,',');
	$prcodelist=str_replace(',','\',\'',$prcodes);
	$sql = "UPDATE tblproduct SET display='".$display."' ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		pmysql_free_result($result);

		$sql = "UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		pmysql_query($sql,get_db_conn());

		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
} else if($mode=="delete" && strlen($prcodes)>0) {
	if($_venderdata->grant_product[2]!="Y") {
		echo "<html></head><body onload=\"alert('상품 삭제권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	}
	
	$_deldata=array();
	$prcodes=rtrim($prcodes,',');
	$prcodelist=str_replace(',','\',\'',$prcodes);

	$prcodes="";
	$sql = "SELECT productcode, productname, maximage, minimage, tinyimage, display,pridx,assembleuse,assembleproduct FROM tblproduct ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$_deldata[]=$row;
		$prcodes.=$row->productcode.",";
	}
	pmysql_free_result($result);
	
	if(count($_deldata)>0) {
		$prcodes=rtrim($prcodes,',');
		$prcodelist=str_replace(',','\',\'',$prcodes);

		$sql = "DELETE FROM tblproduct WHERE productcode IN ('".$prcodelist."') ";
		$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
		if(pmysql_query($sql,get_db_conn())) {
			//상품 삭제로 인한 관련 데이터 삭제처리

			#태그관련 지우기
			$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());

			#리뷰 지우기
			$sql = "DELETE FROM tblproductreview WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());

			#위시리스트 지우기
			$sql = "DELETE FROM tblwishlist WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());

			#관련상품 지우기
			$sql = "DELETE FROM tblcollection WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());
			
			#테마상품 지우기
			$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());
			
			#카테고리 삭제
			$sql = "DELETE FROM tblproductlink WHERE c_productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());

			#옵션 삭제
			$sql = "DELETE FROM tblproduct_option WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query( $sql, get_db_conn() );
			
			#상품접근권한 지우기
			$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('".$prcodelist."')";
			pmysql_query($sql,get_db_conn());

			#상품별 노출 브랜드 삭제(2016.01.22 - 김재수)
			$sql = "DELETE FROM tblbrandproduct WHERE productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());	

			//미니샵 테마코드에 등록된 상품 삭제
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND productcode IN ('".$prcodelist."') ";
			pmysql_query($sql,get_db_conn());

			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			$sql = "UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			pmysql_query($sql,get_db_conn());

			/*$tmpcode_a=array();
			$arrprcode=explode(",",$prcodes);
			for($j=0;$j<count($arrprcode);$j++) {
				$tmpcode_a[substr($arrprcode[$j],0,3)]=true;
			}

			if(count($tmpcode_a)>0) {
				$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
				$sql.= "WHERE ( ";
				$arr_code_a=$tmpcode_a;
				$i=0;
				while(list($key,$val)=each($arr_code_a)) {
					if(strlen($key)==3) {
						if($i>0) $sql.= "OR ";
						$sql.= "productcode LIKE '".$key."%' ";
						$i++;
					}
				}
				$sql.= ") ";
				$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "GROUP BY code_a ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					unset($tmpcode_a[$row->code_a]);
				}
				pmysql_free_result($result);

				if(count($tmpcode_a)>0) {
					$str_code_a="";
					while(list($key,$val)=each($tmpcode_a)) {
						$str_code_a.=$key.",";

						$imagename=$Dir.DataDir."shopimages/vender/".$_VenderInfo->getVidx()."_CODE10_".$key.".gif";
						@unlink($imagename);
					}
					$str_code_a=rtrim($str_code_a,',');
					$str_code_a=str_replace(',','\',\'',$str_code_a);
					$sql = "DELETE FROM tblvendercodedesign WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND code IN ('".$str_code_a."') AND tgbn='10' ";
					pmysql_query($sql,get_db_conn());
				}
			}*/

			#상품이미지 삭제
			$imagepath=$Dir.DataDir."shopimages/product/";
			$update_ymd = date("YmdH");
			$update_ymd2 = date("is");
			for($i=0;$i<count($_deldata);$i++) {
				if(strlen($_deldata[$i]->assembleproduct)>0) {
					$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
					$sql.= "WHERE productcode IN ('".str_replace(",","','",$_deldata[$i]->assembleproduct)."') ";
					$result = pmysql_query($sql,get_db_conn());
					while($row = @pmysql_fetch_object($result)) {
						$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
						$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
						$sql.= "AND display ='Y' ";
						$sql.= "AND assembleuse!='Y' ";
						$result2 = pmysql_query($sql,get_db_conn());
						if($row2 = @pmysql_fetch_object($result2)) {
							$sql = "UPDATE tblproduct SET sellprice='".$row2->sumprice."' ";
							$sql.= "WHERE productcode = '".$row->productcode."' ";
							$sql.= "AND assembleuse='Y' ";
							pmysql_query($sql,get_db_conn());
						}
						pmysql_free_result($result2);
					}
				}

				$sql = "UPDATE tblassembleproduct SET ";
				$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$_deldata[$i]->pridx."',''), ";
				$sql.= "assemble_list=REPLACE(assemble_list,',".$_deldata[$i]->pridx."','') ";
				pmysql_query($sql,get_db_conn());

				$vimagear=array(&$vimage,&$vimage2,&$vimage3);
				$vimage=$_deldata[$i]->maximage;
				$vimage2=$_deldata[$i]->minimage;
				$vimage3=$_deldata[$i]->tinyimage;

				for($y=0;$y<3;$y++){
					if(strlen($vimagear[$y])>0 && file_exists($imagepath.$vimagear[$y]))
						unlink($imagepath.$vimagear[$y]);
				}
				@delProductMultiImg("prdelete","",$_deldata[$i]->productcode);

				$log_content = "## 상품삭제 ## - 상품코드 ".$_deldata[$i]->productcode." - 상품명 : ".urldecode($_deldata[$i]->productname)." ".$_deldata[$i]->display."";
				$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content,$update_date);
				$update_ymd2++;
			}

			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('삭제할 상품이 존재하지 않습니다.');parent.pageForm.submit();\"></body></html>";exit;
	}
} else if($mode=="copy") {
	$prcode=$_POST["prcode"];
	if ( strlen( $prcode ) > 0 ) {
		$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
		$result = pmysql_query($sql,get_db_conn());
		if ( $row = pmysql_fetch_object( $result ) ) {
            $copy_vender = $row->vender;
            #해당 상품의 카테고리
            $cate_sql = "SELECT pl.c_category , pl.c_date_1, pl.c_date_2, pl.c_date_3, pl.c_date_4, pl.c_date, pc.code_a||pc.code_b||pc.code_c||pc.code_d AS catecode 
                    FROM tblproductlink pl 
                    JOIN tblproductcode pc ON pl.c_category = pc.code_a||pc.code_b||pc.code_c||pc.code_d 
                    WHERE pl.c_productcode = '{$prcode}' 
                    AND pl.c_maincate = '1' 
                    LIMIT 1
            ";
            $cate_res  = pmysql_query( $cate_sql, get_db_conn() );
            $cate_row  = pmysql_fetch_object( $cate_res );
            $main_cate = $cate_row;
            pmysql_free_result( $cate_res );

			$copycode = $main_cate->catecode;
            
            #카테고리 코드 + 6자리 숫자코드로 새 코드 발급
			$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$copycode}%' ";
			$sql.= "ORDER BY productcode DESC LIMIT 1 ";
			$result = pmysql_query( $sql, get_db_conn() );
			if ( $rows = pmysql_fetch_object( $result ) ) {
				$newproductcode = substr( $rows->productcode, 12 ) + 1;
				$newproductcode = substr( "000000".$newproductcode, strlen( $newproductcode ) );
			} else {
				$newproductcode = "000001";
			}
			pmysql_free_result($result);

			$path = $Dir.DataDir."shopimages/product/";

			if ( ord( $row->maximage ) ) {
				$ext = strtolower( pathinfo( $row->maximage, PATHINFO_EXTENSION ) );
				$maximage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum1_500X500.".$ext;
				if ( file_exists( $path.$row->maximage ) ) {
                    if( !is_dir( $path.$copycode.$newproductcode ) ){
                        mkdir( $path.$copycode.$newproductcode, 0700 );
                        chmod( $path.$copycode.$newproductcode, 0777 );
                    }
					copy( $path.$row->maximage, $path.$maximage );
				}
			} else {
                $maximage = "";
            }

			if ( ord( $row->minimage ) ) {
				$ext = strtolower( pathinfo( $row->minimage, PATHINFO_EXTENSION ) );
				$minimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum2_500X500.".$ext;
				if ( file_exists( $path.$row->minimage ) ) {
					copy( $path.$row->minimage, $path.$minimage );
				}
			} else {
                $minimage = "";
            }

			if ( ord( $row->tinyimage ) ) {
				$ext = strtolower( pathinfo( $row->tinyimage, PATHINFO_EXTENSION ) );
				$tinyimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum3_500X500.".$ext;
				if ( file_exists( $path.$row->tinyimage ) ) {
					copy( $path.$row->tinyimage, $path.$tinyimage );
				}
			} else {
                $tinyimage="";
            }

            if (ord($row->over_minimage)) {
				$ext = strtolower( pathinfo( $row->over_minimage, PATHINFO_EXTENSION ) );
				$over_minimage = $copycode.$newproductcode.'/'.$copycode.$newproductcode."_thum4_500X500.".$ext;
				if ( file_exists( $path.$row->over_minimage ) ) {
					copy( $path.$row->over_minimage, $path.$over_minimage );
				}
			} else {
                $over_minimage="";
            }

            $quantity = $row->quantity;
            # 브랜드 추가 필요
			if( ord( $row->brand ) == 0 ) $row->brand = 'NULL';

			$productname     = pmysql_escape_string( $row->productname );
			$production      = pmysql_escape_string( $row->production );
			$madein          = pmysql_escape_string( $row->madein );
			$model           = pmysql_escape_string( $row->model );
			$tempkeyword     = pmysql_escape_string( $row->keyword );
			$addcode         = pmysql_escape_string( $row->addcode );
			$userspec        = pmysql_escape_string( $row->userspec );
			$option1         = pmysql_escape_string( $row->option1 );
			$option2         = pmysql_escape_string( $row->option2 );
			$content         = pmysql_escape_string( $row->content );
			$selfcode        = pmysql_escape_string( $row->selfcode );
			$assembleproduct = pmysql_escape_string( $row->assembleproduct );

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
			brand		,
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
			option1		,
			option2		,
			sabangnet_flag,
			etctype		,
			deli		,
			package_num	,
			display		,
			date		,
			vender		,
			regdate		,
			modifydate	,
			content,
			sabangnet_prop_val,
            icon,
            dicker ,
            min_quantity,
            max_quantity,
            setquota,
            prkeyword,
            deli_qty,
            deli_select,
            mdcommentcolor,
            option2_tf,
            option2_maxlen,
            option_type,
            soldout,
            option1_tf,
            deliinfono,
            deli_price,
            over_minimage
			) VALUES (
			'".$copycode.$newproductcode."',
			'{$productname}',
			'{$row->assembleuse}',
			'{$row->assembleproduct}',
			{$row->sellprice},
			{$row->consumerprice},
			{$row->buyprice},
			'{$row->reserve}',
			'{$row->reservetype}',
			'{$production}',
			'{$madein}',
			'{$model}',
			{$row->brand},
			'{$row->opendate}',
			'{$row->selfcode}',
			'{$row->bisinesscode}',
			{$quantity},
			'{$row->group_check}',
			'{$tempkeyword}',
			'{$addcode}',
			'{$userspec}',
			'{$maximage}',
			'{$minimage}',
			'{$tinyimage}',
			'{$option1}',
			'{$option2}',
			'{$copy_type}',
			'{$row->etctype}',
			'{$row->deli}',
			'".(int)$row->package_num."',
			'N',
			'".(($newtime=="Y")?date("YmdHis"):$row->date)."',
			'{$row->vender}',
			now(),
			now(),
			'{$content}',
			'{$row->sabangnet_prop_val}',
            '{$row->icon}',
            '{$row->dicker }',
            '{$row->min_quantity}',
            '{$row->max_quantity}',
            '{$row->setquota}',
            '{$row->prkeyword}',
            '{$row->deli_qty}',
            '{$row->deli_select}',
            '{$row->mdcommentcolor}',
            '{$row->option2_tf}',
            '{$row->option2_maxlen}',
            '{$row->option_type}',
            '{$row->soldout}',
            '{$row->option1_tf}',
            '{$row->deliinfono}',
            '{$row->deli_price}',
            '{$over_minimage}'

            ) RETURNING pridx";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));

			$fromproductcodes.= "|".$prcode;
			$copyproductcodes.= "|".$copycode.$newproductcode;

			$copy_cate_insert_sql = "INSERT INTO tblproductlink
    			(c_productcode, c_category, c_maincate, c_date, c_date_1, c_date_2, c_date_3, c_date_4 )
				VALUES
				('".$copycode.$newproductcode."', '".$main_cate->c_category."', '1', '".$main_cate->c_date."', '".$main_cate->c_date_1."', '".$main_cate->c_date_2."', '".$main_cate->c_date_3."', '".$main_cate->c_date_4."'  )";
			pmysql_query($copy_cate_insert_sql,get_db_conn());

			$log_content = "## 상품복사입력 ## - 상품코드 {$prcode} => ".$copycode.$newproductcode." - 상품명 : ".$productname;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		}
        # 멀티이미지는 가져오지 못한다
		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		#옵션복사
		$optionSelectSql = " SELECT option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf ";
		$optionSelectSql .= "FROM tblproduct_option WHERE productcode = '".$prcode."' ";
		$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
		while( $optionSelect = pmysql_fetch_object( $optionSelectRes ) ){
			$optionCopySql = "INSERT INTO tblproduct_option ( option_code, productcode, option_price, option_quantity, ";
			$optionCopySql.= "option_quantity_noti, option_type, option_use, option_tf ) ";
			$optionCopySql.= "VALUES ( '".$optionSelect->option_code."','".$copycode.$newproductcode."', '".$optionSelect->option_price."', '".$optionSelect->option_quantity."', ";
			$optionCopySql.= "'".$optionSelect->option_quantity_noti."', '".$optionSelect->option_type."', '".$optionSelect->option_use."', '".$optionSelect->option_tf."' ) ";
			pmysql_query( $optionCopySql, get_db_conn() );
		}
		pmysql_free_result( $optionSelectRes );
        

        # 브랜드 처리
        if ( $copy_vender ) {
            list( $bridx ) = pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='{$copy_vender}'");
            if ( $bridx > 0 ) {
                @pmysql_query("UPDATE tblproduct SET brand = '{$bridx}' WHERE productcode = '".$copycode.$newproductcode."'",get_db_conn());
                $bpSql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$copycode.$newproductcode."','".$bridx."','1')";			
                pmysql_query($bpSql,get_db_conn());
            }			
        }

		$onload="<script>window.onload=function(){ alert(\"상품 복사가 완료되었습니다.\");}</script>";
		$prcode="";
	}
}

//exdebug($_POST);
//exdebug($_GET);


$_ShopData=new ShopData($_ShopInfo);
$_ShopData=$_ShopData->shopdata;
$regdate = $_ShopData->regdate;

$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

$code=$_POST["code"];
$listnum=$_POST["listnum"]?$_POST["listnum"]:'20';
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
$soldout_yn=$_POST["soldout_yn"];
$sellprice_min=$_POST["sellprice_min"];
$sellprice_max=$_POST["sellprice_max"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search=ltrim($_POST["search"]);
$sort=$_POST["sort"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

if(strlen($s_check)==0) $s_check="name";

if($sort!="order by productname asc" && $sort!="order by productname desc" && $sort!="order by productcode asc" && $sort!="order by productcode desc" && $sort!="order by sellprice asc" && $sort!="order by sellprice desc" && $sort!="order by regdate asc" && $sort!="order by regdate desc") {
	$sort="order by regdate desc";
}

$qry_form	= 'tblproduct';
$qry = "WHERE 1=1 ";

$qry.= "AND vender='".$_VenderInfo->getVidx()."' ";

if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','') ";

if(strlen($code)>=3) {
	$qry.= "AND c_category LIKE '".$code."%' ";
}

if(!isnull($sellprice_min) > 0) $qry.="AND sellprice >= '{$sellprice_min}' ";
if(!isnull($sellprice_max) > 0) $qry.="AND sellprice <= '{$sellprice_max}' ";

if($disptype=="Y") $qry.= "AND display='Y' ";
else if($disptype=="N") $qry.= "AND display='N' ";

if($soldout_yn==1)	{
	$qry.="AND (quantity > 0 and soldout = 'N') ";
} elseif($soldout_yn==2){
	$qry.="AND ( (quantity < 999999999 and soldout = 'Y') OR (quantity <= 0) ) ";
}

if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND UPPER( productname ) LIKE UPPER( '%".$search."%' ) ";
	else if($s_check=="code") $qry.= "AND UPPER( productcode ) LIKE UPPER( '%".$search."%' ) ";
	
	if($s_check=="self_goods_code") {	
		$qry_form	= "( SELECT pd.* FROM (
		SELECT 
		prd.productcode
		FROM tblproduct prd LEFT JOIN tblproduct_option opt ON prd.productcode=opt.productcode where UPPER(prd.self_goods_code) LIKE UPPER('%".$search."%') OR UPPER(opt.self_goods_code) LIKE UPPER('%".$search."%') group by prd.productcode ) as prdc LEFT JOIN tblproduct pd ON prdc.productcode=pd.productcode ) ";
	}
}

$sql = "SELECT COUNT(*) as t_count FROM (select a.*, b.c_category FROM {$qry_form} a left join tblproductlink b on ( a.productcode=b.c_productcode  AND b.c_maincate = '1' ) ) tpd ".$qry." ";
//echo $sql;

$paging = new Paging($sql,10,$listnum);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//exdebug( $_REQUEST );
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "product_myprd.ctgr.php?code="+code+"&depth=2";
	surl = "product_myprd.ctgr.php?depth=3";
	durl = "product_myprd.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

//엑셀파일 다운로드
function excelDown() {
	document.downexcelform.productcodes.value="";
	for(i=1;i<document.form2.chkprcode.length;i++) {
		if(document.form2.chkprcode[i].checked) {
			if(document.downexcelform.productcodes.value!='') document.downexcelform.productcodes.value +=",";
			document.downexcelform.productcodes.value+=document.form2.chkprcode[i].value;
		}
	}

	if(document.downexcelform.productcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=255,height=160,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

<?if($_venderdata->grant_product[1]=="Y" && $_venderdata->grant_product[3]=="N") {?>
function setPrdDisplaytype(prcode,display) {
	if(display!="Y" && display!="N") {
		alert("ON/OFF 설정이 잘못되었습니다.");
		return;
	}
	document.etcform.prcodes.value="";
	if(prcode.length==18) {
		document.etcform.prcodes.value+=prcode+",";
	} else {
		for(i=1;i<document.form2.chkprcode.length;i++) {
			if(document.form2.chkprcode[i].checked) {
				document.etcform.prcodes.value+=document.form2.chkprcode[i].value+",";
			}
		}
	}
	if(document.etcform.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택하신 상품의 상품진열을 ["+(display=="Y"?"ON":"OFF")+"] 하시겠습니까?")) {
		document.etcform.mode.value="display";
		document.etcform.display.value=display;
		document.etcform.action="<?=$_SERVER[PHP_SELF]?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}
<?}?>

<?if($_venderdata->grant_product[2]=="Y") {?>
function DeletePrd(prcode) {
	document.etcform.prcodes.value="";
	if(prcode.length==18) {
		document.etcform.prcodes.value+=prcode+",";
	} else {
		for(i=1;i<document.form2.chkprcode.length;i++) {
			if(document.form2.chkprcode[i].checked) {
				document.etcform.prcodes.value+=document.form2.chkprcode[i].value+",";
			}
		}
	}
	if(document.etcform.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택하신 상품을 삭제할 경우 복구가 불가능합니다.\n\선택하신 상품을 완전히 삭제하시겠습니까?")) {
		document.etcform.mode.value="delete";
		document.etcform.display.value="";
		document.etcform.action="<?=$_SERVER[PHP_SELF]?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}
<?}?>

function SearchPrd() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function OrderSort(sort) {
	document.pageForm.block.value="";
	document.pageForm.gotopage.value="";
	document.pageForm.sort.value=sort;
	document.pageForm.submit();
}

function GoPrdinfo(prcode,target) {
	document.form3.target="";
	document.form3.prcode.value=prcode;
	if(target.length>0) {
		document.form3.target=target;
	}
	document.form3.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkprcode[i].checked=chkval;
   }
}

function go_copy( prcode ){
    if( confirm('해당 상품을 복사하시겠습니까?') ){
        document.copy_form.mode.value = 'copy';
        document.copy_form.prcode.value = prcode;
        document.copy_form.submit();
    }
}

function listnumSet(listnum){
	document.sForm.listnum.value=listnum.value;
	document.sForm.submit();
}

</script>

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
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
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
					<FONT COLOR="#ffffff"><B>내 상품 관리<B></FONT>
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
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>내 상품 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 카테고리 분류/상품명 검색으로 등록된 상품을 관리합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 상품명 클릭시 해당 상품 열람/수정이 가능합니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 상품 체크 후 ON/OFF 상태를 일괄 변경할 수 있습니다.</td>
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
				<td style="padding:15">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<form name="sForm" method="post">
						<input type="hidden" name="code" value="<?=$code?>">
						<input type="hidden" name="listnum" value="<?=$listnum?>">
						<tr>
							<td>							
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>&nbsp;<U>분류</U></td>
								<td>
								<select name="code1" onchange="ACodeSendIt(this.options[this.selectedIndex].value)" style="width:155px;height:19px;vertical-align:middle;font-family:돋음;color:333333;font-size:9pt">
								<option value="">------ 대 분 류 ------</option>
<?
								$sql = "SELECT SUBSTR(b.c_category,1,3) as prcode FROM tblproduct a left join tblproductlink b on a.productcode=b.c_productcode ";
								$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "GROUP BY prcode ";
								$result=pmysql_query($sql,get_db_conn());
								$codes="";
								while($row=pmysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								pmysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=rtrim($codes,',');
									$prcodelist=str_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
									$sql.= "WHERE code_a IN ('".$prcodelist."') AND code_b='000' AND code_c='000' ";
									$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									echo $sql;
									$result=pmysql_query($sql,get_db_conn());
									while($row=pmysql_fetch_object($result)) {
										echo "<option value=\"".$row->code_a."\"";
										if($row->code_a==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									pmysql_free_result($result);
								}
?>
								</select>
								<iframe name="BCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no marginheight="0" marginwidth="0" style="vertical-align:middle"></iframe>
								<iframe name="CCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no marginheight="0" marginwidth="0" style="vertical-align:middle"></iframe>
								<iframe name="DCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no marginheight="0" marginwidth="0" style="vertical-align:middle"></iframe></td>
							</tr>
							<tr><td height=5 colspan=2></td></tr>
							<tr>
								<td>&nbsp;<U>기간선택</U></td>
								<td>
								<input type=text name=search_start value="<?=$search_start?>" size=13 onclick=Calendar(event) style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onclick=Calendar(event) style="text-align:center;font-size:8pt">
								&nbsp;
								<img src=images/btn_dayall.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
								</td>
							</tr>
							<tr><td height=5 colspan=2></td></tr>
							<tr>
								<td>&nbsp;<U>상품금액</U></td>
								<td style="font-size:8pt">
								<input type=text name=sellprice_min value="<?=$sellprice_min?>" size=13 style="text-align:right;font-size:8pt"> 원 ~ <input type=text name=sellprice_max value="<?=$sellprice_max?>" size=13 style="text-align:right;font-size:8pt"> 원
								</td>
							</tr>
							<tr><td height=5 colspan=2></td></tr>
							<tr>
								<td>&nbsp;<U>품절유무</U></td>
								<td>
								<select name=soldout_yn style="font-size:8pt">
								<option value="">전체</option>
								<option value="1" <?if($soldout_yn=="1")echo"selected";?>>미품절</option>
								<option value="2" <?if($soldout_yn=="2")echo"selected";?>>품절</option>
								</select>
								&nbsp;&nbsp;&nbsp;
								<U>진열여부</U>&nbsp;
								<select name=disptype  style="font-size:8pt">
								<option value="">전체</option>
								<option value="Y" <?if($disptype=="Y")echo"selected";?>>진열</option>
								<option value="N" <?if($disptype=="N")echo"selected";?>>미진열</option>
								</select>
								&nbsp;&nbsp;&nbsp;
								<U>검색어</U>&nbsp;
								<select name=s_check style="font-size:8pt;">
								<option value="name" <?if($s_check=="name")echo"selected";?>>상품명</option>
								<option value="code" <?if($s_check=="code")echo"selected";?>>상품코드</option>
								<option value="self_goods_code" <?if($s_check=="self_goods_code")echo"selected";?>>자체품목코드</option>
								</select>
								<input type=text name=search value="<?=$search?>" style="width:183">
								&nbsp;<A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
								</td>
							</tr>
							</table>
							</td>
						</tr>

						</form>

						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=150></col>
					<col width=></col>
					<col width=100></col>
					<tr>
						<td valign=top><img src=images/btn_exceldown.gif border=0 style="cursor:hand" onclick="excelDown()"></td>
						<td align=right valign=top>
						<?if($_venderdata->grant_product[1]=="Y" && $_venderdata->grant_product[3]=="N") {?>
						<img src=images/btn_prddispon.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','Y')">
						<img src=images/btn_prddispoff.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','N')">
						<?}?>
						<?if($_venderdata->grant_product[2]=="Y") {?>
						<img src=images/btn_prddel.gif border=0 style="cursor:hand" onclick="DeletePrd('')">
						<?}?>
						</td>
						<td align=right valign=top>
						<select name="listnum_select" onchange="javascript:listnumSet(this)">
							<option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
							<option value="47" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
							<option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
							<option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
							<option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
							<option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
							<option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
							<option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
							<option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
							<option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
						</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=3></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=30></col>
					<col width=40></col>
					<col width=47></col>
					<col width=></col>
					<col width=60></col>
					<col width=70></col>
					<col width=60></col>
					<col width=60></col>
					<col width=60></col>
					<col width=70></col>
                    <col width=60></col>
					<col width=60></col>

					<form name=form2 method=post>
					<input type=hidden name=chkprcode>

					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><input type=checkbox name=allcheck onclick="CheckAll()"></td>
						<td align=center><B>번호</B></td>
						<td align=center><B>이미지</B></td>
						<td align=center><B>상품명</B></td>
						<td align=center><B>품절여부</B></td>
						<td align=center><B>등록일</B></td>
						<td align=center><B>구입원가</B></td>
						<td align=center><B>소비자가</B></td>
						<td align=center><B>판매가</B></td>
						<td align=center><B>재고</B></td>
                        <td align=center><B>비고</B></td>
						<td align=center><B>상품진열</B></td>
					</tr>
<?php
					$colspan=12;
					$cnt=0;
					if($t_count>0) {
						$sql = "SELECT * FROM (select a.*, b.c_category FROM {$qry_form} a left join tblproductlink b on ( a.productcode=b.c_productcode  AND b.c_maincate = '1' )  ) tpd ".$qry." ".$sort." ";
						$sql = $paging->getSql($sql);

						// 엑셀용 쿼리를 저장한다.(2016.06.23 - 김재수 추가)
						$excel_sql = "
							SELECT * FROM ( SELECT a.*, b.*
							FROM	{$qry_form} a 
							left join tblproductlink b on (a.productcode=b.c_productcode AND b.c_maincate=1 ) 
							left join tblproductbrand c on (a.brand = c.bridx)   ) tpd
							".$qry."
						";						
                        $excel_sql_orderby =" ".$sort." "; // 엑셀용 쿼리를 저장한다.(2016.06.23 - 김재수 추가)

						//exdebug($sql);
						$result=pmysql_query($sql,get_db_conn());
						$i=0;
						while($row=pmysql_fetch_object($result)) {
							$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
							$imagepath=$Dir.DataDir."shopimages/product/";
							$min_img = getProductImage($imagepath, $row->minimage );
							
                            if (ord($row->minimage) && file_exists($imagepath.$row->minimage)){
                            $prd_img	= $imagepath.$row->minimage;
                            } else if(ord($row->minimage) && file_exists($Dir.$row->minimage)) {
                             $prd_img	= $Dir.$row->minimage;
                            } else if($min_img) {
                             $prd_img	= $min_img;
                            } else { 
                             $prd_img	= "images/space01.gif";
                            }

							echo "<tr height=47 bgcolor=#FFFFFF>\n";
							echo "	<td align=center><input type=checkbox name=chkprcode value=\"".$row->productcode."\"></td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$number."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\"><a href=\"/front/productdetail.php?productcode=".$row->productcode."\" target=\"_blank\"><img src='".$prd_img."?v".date("His")."' style=\"width:35px;margin:5px\" border=0></a></td>\n";
							echo "	<td style='font-size:8pt;line-height:13pt;padding-left:5;padding-right:5'><A HREF=\"javascript:GoPrdinfo('".$row->productcode."','')\">".$row->productname.($row->selfcode?"-".$row->selfcode:"")."<br>".$row->productcode."</A> <A HREF=\"javascript:GoPrdinfo('".$row->productcode."','_blank')\"><img src=images/newwindow.gif border=0 align=absmiddle></A></td>\n";
							echo "	<td align=center style=font-size:8pt;padding-right:5>".($row->soldout=='Y'?'품절':'미품절')."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".substr($row->regdate,0,10)."</td>\n";
							echo "	<td align=right style=font-size:8pt;padding-right:5>".number_format($row->buyprice)."</td>\n";
							echo "	<td align=right style=font-size:8pt;padding-right:5>".number_format($row->consumerprice)."</td>\n";
							echo "	<td align=right style=font-size:8pt;padding-right:5>".number_format($row->sellprice)."</td>\n";
							echo "	<td align=center style=font-size:8pt;'>".($row->quantity=="999999999"?"무제한":$row->quantity)."</td>\n";
                            echo "  <td align=center><a href=\"javascript:go_copy('".$row->productcode."');\"><img src='images/btn_cate_copy.gif'></a></td>\n";
							echo "	<td align=center>";
							if($_venderdata->grant_product[1]=="Y" && $_venderdata->grant_product[3]=="N") {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','N')\">";
								} else {
									echo "<img src=images/icon_off.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','Y')\">";
								}
							} else {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0>";
								} else {
									echo "<img src=images/icon_off.gif border=0>";
								}
							}
							echo "	</td>\n";
                           
							echo "</tr>\n";
							$i++;
						}
						pmysql_free_result($result);
						$cnt=$i;
						if($i>0) {
							$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
						}
					} else {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
					}
?>
					<input type=hidden name=tot value="<?=$cnt?>">
					</form>

					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>
					<form name="pageForm" method="post">
					<input type=hidden name='code' value='<?=$code?>'>
					<input type=hidden name='listnum' value='<?=$listnum?>'>
					<input type=hidden name='search_start' value="<?=$search_start?>">
					<input type=hidden name='search_end' value="<?=$search_end?>">
					<input type=hidden name='sellprice_min' value="<?=$sellprice_min?>">
					<input type=hidden name='sellprice_max' value="<?=$sellprice_max?>">
					<input type=hidden name='soldout_yn' value='<?=$soldout_yn?>'>
					<input type=hidden name='disptype' value='<?=$disptype?>'>
					<input type=hidden name='s_check' value='<?=$s_check?>'>
					<input type=hidden name='search' value='<?=$search?>'>
					<input type=hidden name='sort' value='<?=$sort?>'>
					<input type=hidden name='block' value='<?=$block?>'>
					<input type=hidden name='gotopage' value='<?=$gotopage?>'>
					</form>

					<?=$pageing?>

					</td>
				</tr>
				</table>

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

<form name=etcform method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=prcodes>
<input type=hidden name=display>
</form>

<form name=form3 method=post action="product_prdmodify.php">
<input type=hidden name=prcode>

<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name='search_start' value="<?=$search_start?>">
<input type=hidden name='search_end' value="<?=$search_end?>">
<input type=hidden name='sellprice_min' value="<?=$sellprice_min?>">
<input type=hidden name='sellprice_max' value="<?=$sellprice_max?>">
<input type=hidden name='soldout_yn' value='<?=$soldout_yn?>'>
<input type=hidden name=code1 value="<?=$code1?>">
<input type=hidden name=disptype value="<?=$disptype?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
</form>

<form name='copy_form' method='POST' action='<?=$_SERVER[PHP_SELF]?>' >
<input type='hidden' name='prcode' value='' >
<input type='hidden' name='mode' value='' >
</form>

<form name=downexcelform action="product_excel_sel_popup.php" method="post">
	<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
	<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
	<input type=hidden name="productcodes"/>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
