<?
/********************************************************************* 
// 파 일 명 : naver.php 
// 설    명 : naver ep_list
// 상세설명 : 네이버 EP 리스트
// 작 성 자 : 유동혁 2016-03-16
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
	header("Content-Type: text/html; charset=UTF-8");
	
// 	echo "<header>id\ttitle\tprice_pc\tlink\timage_link\tcategory_name1\tcategory_name2\tcategory_name3\tcategory_name4\tinterest_free_event\tcoupon\tbrand\tmaker\tshipping\tclass\tupdate_time</header>";
// 	echo "<header>id\ttitle\tprice_pc\tlink\timage_link\tcategory_name1\tcategory_name2\tcategory_name3\tcategory_name4\tshipping\tclass\tupdate_time</header>";
	echo "id	title	price_pc	link	image_link	category_name1	category_name2	category_name3	category_name4	interest_free_event	coupon	brand	maker	shipping	class	update_time\n";
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

	$sql = "SELECT to_char( p.regdate, 'YYYY-MM-DD' ) AS REGDT,p.vender, p.productcode, ( CASE WHEN CHAR_LENGTH( p.productname ) > 100 THEN SUBSTR( p.productname, 1, 100 ) ELSE p.productname END ) AS productname, p.sellprice, p.maximage, p.deli, p.deli_price, p.deli_qty, p.deli_select, v.brandname, p.production, p.prodcode, p.colorcode, p.productname_kor 
            FROM tblproduct p 
            JOIN tblproductbrand v on p.vender = v.vender 
            WHERE p.display = 'Y' 
            AND p.soldout = 'N' 
            AND p.quantity > 0 
            AND p.hotdealyn = 'N' 
           	AND p.naver_display = 'Y'
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
        
//         echo $cate_query;
//         exit();
        
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
	// echo "001004005003000367	[SIEG]블루 베이직 코튼 셔츠 (PQBAB5081)(PQB50810-BL)	59000	http://www.shinwonmall.com/front/productdetail.php?productcode=001004005003000367	http://img.shinwonmall.com/images/PQ/PQBAB5081/PQBAB5081_BL_B.jpg	신원	남성	탑	셔츠	[현대/KB국민/삼성/신한/롯데/비씨/NH농협/하나카드 5개월 무이자]	NO	SIEG	SIEG	0	I	2017-06-02\n";
	
?>
<?=$row->productcode."\t"?>
[<?=$row->brandname?>]<?=$pname."(".$row->prodcode."-".$row->colorcode.")"."\t"?>
<?=$fix_price."\t"?>
<?=$url?>productdetail.php?productcode=<?=$row->productcode."\t"?>
<?=$imgUrl."\t"?>
<?php 
        for ( $j = 0; $j < 4; $j++ ){ 
            if( strlen( $cate_code[$j]['code_name'] ) > 0 ) $codename = $cate_code[$j]['code_name'];
            else $codename = '';
?>
<?=$codename."\t"?>
<?php
        }
?>
<?=$pcard."\t"?>
<?php 
	if($coupon_use['name']){
		echo $coupon_use['name']."\t";
	} else {
		echo "NO"."\t";
	}
?>
<?=$row->brandname."\t"?>
<?=$row->production."\t"?>
<?=$deli_price."\t"?>
<?="I\t"?>
<?=$row->regdt."\n"?>
<?
	}
    pmysql_free_result( $result );
?>