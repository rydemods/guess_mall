<?
/********************************************************************* 
// 파 일 명 : daum.php 
// 설    명 : daum ep_list
// 상세설명 : 다음 쇼핑하우 EP 리스트
// 작 성 자 : 유동혁 2017-04-25
// 수 정 자 : 
// 
// 필수 목록 : 이미지, 상품디스플레이 = Y, 상품 수량 > 0 
*********************************************************************/ 

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");

    @set_time_limit(0);

    // 아래 헤더를 막으면 time out 이 나버린다. 왜 주석처리 한거쥐? 일단 해제..2016-06-21 jhjeong
	header("Cache-Control: no-cache, must-revalidate");
	header("Content-Type: text/plain; charset=UTF-8");
    
    # 벤더별 배송료 $vender -> 벤더번호, $sellprice -> 상품가격
    function VenderDeli( $vender, $sellprice ){
        global $_data;

        $delivery = array();
        $deli_price = 0;
        
        if( $vender != '0' ){
            $sql = "SELECT deli_pricetype, deli_price, deli_mini, deli_select FROM tblvenderinfo WHERE vender ='".$vender."' ";
            $result = pmysql_query( $sql, get_db_conn() );
            $row = pmysql_fetch_object( $result );
            pmysql_free_result( $result );
            $delivery = array (
                'deli_type'      => $row->deli_pricetype,   //배송료 무료(0:무료, 1:유료)
                'deli_price'     => $row->deli_price,       //배송료
                'deli_price_min' => $row->deli_mini,        //최소 구매금액( 0일 경우 무조건 배송료 발생)
                'deli_select'    => $row->deli_select       //배송방식 선택
            );
            
        } else {
            $delivery = array(
                'deli_type'      => $_data->deli_basefeetype, // 배송료 유료/무료
                'deli_price'     => $_data->deli_basefee,     // 배송료
                'deli_price_min' => $_data->deli_miniprice,   // 최소 구매금액
                'deli_select'    => $_data->deli_select       // 선/착불 
            );
        }
        
        if( $delivery['deli_type'] == '1' ){
            if( $delivery['deli_price_min'] == "0") {
                $deli_price = $delivery['deli_price'];
            } else if( $delivery['deli_price_min'] > $sellprice ){
                $deli_price = $delivery['deli_price'];
            }
        }

        if( $delivery['deli_select'] == '1' || $delivery['deli_select'] == '2' ){
            $deli_price = -1;
        }

        return $deli_price;

    }

    #이미지 URL
    function ProductImgUrl( $img_name ){

        global $Dir;

        $imgUrl = "http://".$_SERVER['HTTP_HOST']."/".DataDir."shopimages/product/";
        $img_path = $Dir.ImageDir."product/";
        $igurl = '';
        
        if(strpos($img_name, "http://") === false) { 
            if(strlen($img_name)!=0 && file_exists($img_path.$img_name) ){ 
                $igurl = $imgUrl.$img_name; 
            } 
            /*else { 
                $igurl = "../images/common/noimage.gif"; 
            }
            */
        } else {
            $igurl = $img_name;
        }

        return $igurl;
    }


	$url = "http://".$_SERVER['HTTP_HOST']."/".FrontDir;
	//$imagepath = "http://".$_SERVER['HTTP_HOST']."/".DataDir."shopimages/product/";
	
	// 다음 쇼핑하우 tocnt 한번만 실행함 일반적으로 전체 count 를 노출함
	$cnt_qry = "SELECT count(*) as cnt FROM tblproduct WHERE daum_display = 'Y' AND display = 'Y' AND soldout = 'N' AND quantity > 0";
	$cnt_result = pmysql_query($cnt_qry,get_db_conn());
	if( $cnt_row = pmysql_fetch_object( $cnt_result ) ) {
		$cnt = $cnt_row->cnt;
	}
?>
<<<tocnt>>><?=$cnt."\n" ?>
<?php
	$sql = "SELECT p.vender, p.productcode, ( CASE WHEN CHAR_LENGTH( p.productname ) > 100 THEN SUBSTR( p.productname, 1, 100 ) ELSE p.productname END ) AS productname, p.sellprice, p.maximage, p.deli, p.deli_price, p.deli_qty, p.deli_select, v.brandname, p.production, p.prodcode, p.colorcode, p.productname_kor 
            FROM tblproduct p 
            JOIN tblproductbrand v on p.vender = v.vender 
            WHERE p.display = 'Y' 
            AND p.soldout = 'N' 
            AND p.quantity > 0 
            AND p.hotdealyn = 'N' 
           	AND p.daum_display = 'Y'
            ORDER BY p.regdate DESC 
            ";
	$result = pmysql_query($sql,get_db_conn());
	while( $row = pmysql_fetch_object( $result ) ) {

        # 상품 이미지
		if ( ord( $row->maximage ) ){
			$imgUrl = ProductImgUrl( $row->maximage );
            if( $imgUrl == '' ) continue;
		} else {
			continue;
		}
		# 카테고리
		$cate_query = "SELECT pc.code_a||pc.code_b||pc.code_c||pc.code_d AS catecode, pc.code_name ";
        $cate_query.= "FROM tblproductlink pl ";
        $cate_query.= "JOIN tblproductcode pc ON 
                            ( 
                              SUBSTR( pl.c_category, 1, 3 )||'000000000' = pc.code_a||pc.code_b||pc.code_c||pc.code_d 
                              OR SUBSTR( pl.c_category, 1, 6 )||'000000' = pc.code_a||pc.code_b||pc.code_c||pc.code_d 
                              OR SUBSTR( pl.c_category, 1, 9 )||'000' = pc.code_a||pc.code_b||pc.code_c||pc.code_d 
                              OR pl.c_category = pc.code_a||pc.code_b||pc.code_c||pc.code_d 
                            )
                       ";
        $cate_query.= "WHERE pl.c_productcode = '".$row->productcode."' AND pl.c_maincate = '1' ";
        $cate_query.= "ORDER BY code_a, code_b, code_c, code_d ASC ";
        
		$cate_result = pmysql_query( $cate_query );
        $i = 0;
		while( $cate_row = pmysql_fetch_object( $cate_result ) ){
			$cate_code[$i] = array( 'cate_code'=>$cate_row->catecode, 'code_name'=>$cate_row->code_name );
            $i++;
		}
        pmysql_free_result( $cate_result );

        #배송료 0 - 벤더별, 2 - 상품 개별 배송료
        if( $row->deli == '0' ){
            $deli_price = VenderDeli( $row->vender, $row->sellprice );
        } else if( $row->deli == '2' ) {
            $deli_price = $row->deli_price;
        } else {
            $deli_price = 0;
        }

		#사용 가능한 쿠폰 정보
		$dpc = DownPossibleCoupon( $row->productcode );
		//exdebug($dpc);
		if ($dpc) {
			foreach($dpc as $dpcKey => $dpcVal) {
				if ($dpcVal->sale_type == 2) { // % 할인
					$coupon_use['per']	= $dpcVal->sale_money;
					$coupon_use['price']	= round( ( (100 - $dpcVal->sale_money) / 100 ) * $row->sellprice );
				} else if ($dpcVal->sale_type == 4) { // 금액 할인
					$coupon_use['per']	= round( ( ( $row->sellprice - ($row->sellprice - $dpcVal->sale_money) ) / $row->sellprice ) * 100 );
					$coupon_use['price']	= $row->sellprice - $dpcVal->sale_money;
				}
				$coupon_use['name']		= $dpcVal->coupon_name;
				$coupon_use['code']		= $dpcVal->coupon_code;
				$coupon_use['type']		= $dpcVal->coupon_type;
				$coupon_use['dn']			= $dpcVal->take_dn;
				$coupon_use['btn_yn']	= $dpcVal->detail_auto;
			}
			$fix_price	= $coupon_use['price'];
		} else {
			$fix_price	= $row->sellprice;
		}
		
		//한글상품명이 있으면 한글 없으면 영문
// 		if (empty( $row->productname_kor )) {
// 			$pname = $row->productname;
// 		} else {
// 			$pname = $row->productname_kor;
// 		}
		$pname = $row->productname;
		$pcard = "[현대/KB국민/삼성/신한/롯데/비씨/NH농협/하나카드  5개월 무이자]";

?>
<<<begin>>>
<<<mapid>>><?=$row->productcode."\n"?>
<<<price>>><?=$fix_price."\n"?>
<<<pname>>>[<?=$row->brandname?>] <?=$pname." (".$row->prodcode."-".$row->colorcode.")"."\n"?>
<<<pgurl>>><?=$url?>productdetail.php?productcode=<?=$row->productcode."\n"?>
<<<igurl>>><?=$imgUrl."\n"?>
<?php 
        for ( $j = 0; $j < 4; $j++ ){ 
            if( strlen( $cate_code[$j]['code_name'] ) > 0 ) $codename = $cate_code[$j]['code_name'];
            else $codename = '';
?>
<<<cate<?=( $j + 1 )?>>>><?=$codename."\n"?>
<?php
        }
?>
<?php
        for ( $j = 0; $j < 4; $j++ ){ 
            if( strlen( $cate_code[$j]['cate_code'] ) > 0 ) $catecode = $cate_code[$j]['cate_code'];
            else $catecode = '';
?>
<<<caid<?=( $j + 1 )?>>>><?=$catecode."\n"?>
<?php
        }
?>
<<<pcard>>><?=$pcard."\n"?>
<<<coupon>>><?=$coupon_use['name']."\n" ?>
<<<brand>>><?=$row->brandname."\n"?>
<<<maker>>><?=$row->production."\n"?>
<<<deliv>>><?=$deli_price."\n"?>
<<<ftend>>>
<?
        unset( $cate_code );
	}
    pmysql_free_result( $result );
?>