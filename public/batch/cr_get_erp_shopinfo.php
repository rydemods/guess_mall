#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_shopinfo.php
# Desc              : 매일 자정에 실행되어 ERP로부터 매장정보 가져오기
# Last Updated      : 2016-09-01
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_shopinfo.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@set_time_limit(0);

 $conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII");

 $erp_category_arr	= array("D"=>"1", "G"=>"2", "K"=>"3");

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT PART_DIV,
            PART_NO,
            BRAND AS BRANDCD,
            PART_NAME,
			ADDR1,
			ADDR2,
			TEL_NO,
			CLOSE_YN 
			FROM TA_OM006 
			WHERE 1=1
			AND RCV_DATE is NULL
			ORDER BY PART_DIV, PART_NO, BRAND
        ";
$smt = oci_parse($conn, $sql);
oci_execute($smt);
//echo $sql."\r\n";
//exit;

$brand_vender	= getAllBrand();			// 쇼핑몰 전체 EPR 브랜드코드별 쇼핑몰 브랜드코드, 벤더코드

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = utf8encode($v);
    }

	if ($data[CLOSE_YN]=='N') {
		$view			= 1;
		$display_yn	= "Y";
		$delivery_yn	= "Y";
	} else {
		$view	= 0;
		$display_yn	= "N";
		$delivery_yn	= "N";
	}

	//$view	= 0;
	//$display_yn	= "N";
	
	$address	= $data[ADDR2]!=''?$data[ADDR1]." ".$data[ADDR2]:$data[ADDR1];

	$addr_sido		= explode(" ",$data[ADDR1]);
	if ($addr_sido[0] == '서울' || $addr_sido[0] == '서울시' || $addr_sido[0] == '서울특별시') {
		$area_code	= 1;
	} else if ($addr_sido[0] == '경기' || $addr_sido[0] == '경기도' || $addr_sido[0] == '안양시' || $addr_sido[0] == '성남시') {
		$area_code	= 2;
	} else if ($addr_sido[0] == '인천' || $addr_sido[0] == '인천광역시' || $addr_sido[0] == '인천시') {
		$area_code	= 3;
	} else if ($addr_sido[0] == '강원' || $addr_sido[0] == '강원도') {
		$area_code	= 4;
	} else if ($addr_sido[0] == '충남' || $addr_sido[0] == '충청남도') {
		$area_code	= 5;
	} else if ($addr_sido[0] == '대전' || $addr_sido[0] == '대전광역시' || $addr_sido[0] == '대전시') {
		$area_code	= 6;
	} else if ($addr_sido[0] == '충북' || $addr_sido[0] == '충청북도') {
		$area_code	= 7;
	} else if ($addr_sido[0] == '부산' || $addr_sido[0] == '부산광역시' || $addr_sido[0] == '부산시') {
		$area_code	= 8;
	} else if ($addr_sido[0] == '울산' || $addr_sido[0] == '울산광역시' || $addr_sido[0] == '울산시') {
		$area_code	= 9;
	} else if ($addr_sido[0] == '대구' || $addr_sido[0] == '대구광역시' || $addr_sido[0] == '대구시') {
		$area_code	= 10;
	} else if ($addr_sido[0] == '경북' || $addr_sido[0] == '경상북도') {
		$area_code	= 11;
	} else if ($addr_sido[0] == '경남' || $addr_sido[0] == '경상남도') {
		$area_code	= 12;
	} else if ($addr_sido[0] == '전남' || $addr_sido[0] == '전라남도') {
		$area_code	= 13;
	} else if ($addr_sido[0] == '광주' || $addr_sido[0] == '광주광역시' || $addr_sido[0] == '광주시') {
		$area_code	= 14;
	} else if ($addr_sido[0] == '전북' || $addr_sido[0] == '전라북도' || $addr_sido[0] == '전주시') {
		$area_code	= 15;
	} else if ($addr_sido[0] == '제주' || $addr_sido[0] == '제주특별자치도') {
		$area_code	= 16;
	} else {
		$area_code	= 0;
	}
	
	$brandcd			= $data[BRANDCD]=='Q'?'P':$data[BRANDCD];
	$vender		= $brand_vender[$brandcd][VENDER];// 벤더
	$stime		= "10:00";
	$etime		= "20:00";
	$store_code	= $data[PART_DIV].$data[PART_NO].$data[BRANDCD];
	$regdt	= date("YmdHis");

	$addr1=getAddr1($data[ADDR1]);
	$addr_CCC=getAddr1($data[ADDR1]);
	$addr1=str_replace(" (","(",$addr1);
	$addr1=explode("(",$addr1);

    //echo "part_div = ".$data[PART_DIV]." / part_no = ".$data[PART_NO]." / brand = ".$data[BRANDCD]."\r\n";
    //echo "ADDR1 = ".$addr1[0]." / vender = ".$vender."\r\n";

	$addr1=urlencode($addr1[0]);

	if ($vender != '') {
			
		list($coordinate, $old_pickup_yn, $old_delivery_yn, $old_day_delivery_yn) = pmysql_fetch("Select coordinate, pickup_yn, delivery_yn, day_delivery_yn From tblstore where part_div = '".$data[PART_DIV]."' AND part_no = '".$data[PART_NO]."' AND brandcd = '".$data[BRANDCD]."' ");
		$coordinate_arr	= explode("|", $coordinate);
		$lat	= $coordinate_arr[0];
		$lng	= $coordinate_arr[1];
		$coordinate_yn	= "N";
		if ($lat =='' && $lng == '') {
			if ($data[ADDR1]) {
				$sGoogleMapApi = "https://maps.googleapis.com/maps/api/geocode/xml?address={$addr1}&sensor=true_or_false&key=AIzaSyArxWfVeCl-4GuL5rTNgTwGQjxUDGOX_2o" ;
				$budget74 = simplexml_load_file($sGoogleMapApi);
				print($addr_CCC);
				if($budget74->status!='OK') {
					$lat = "";
					$lng = "";
				} else {
					$lat = $budget74->result->geometry->location->lat;
					$lng = $budget74->result->geometry->location->lng;
					$coordinate_yn	= "Y";
				}
				sleep(1);
			} else {
				$lat = "";
				$lng = "";
			}
		}

		$coordinate	= $lat."|".$lng;
		//echo "lat:{$lat}, lng:{$lng}"."\r\n";
		//exit;

		$data[PART_NAME]	= str_replace("(변경)", "", $data[PART_NAME]);
		$data[PART_NAME]	= str_replace("변경/", "", $data[PART_NAME]);
		$data[PART_NAME]	= str_replace("신/", "", $data[PART_NAME]);
		$data[PART_NAME]	= str_replace("(교체)", "", $data[PART_NAME]);
		$data[PART_NAME]	= str_replace("교체/", "", $data[PART_NAME]);

		if ($delivery_yn == 'N') {			
			$pickup_yn				= "N";
			$delivery_yn			= "N";
			$day_delivery_yn	= "N";
		} else {
			$pickup_yn				= $old_pickup_yn;
			$delivery_yn			= $old_delivery_yn;
			$day_delivery_yn	= $old_day_delivery_yn;
		}

		$sql = "
				WITH upsert as (
					update  tblstore 
					set 	view      = '".$view."', 
							pickup_yn       = '".$pickup_yn."', 
							delivery_yn       = '".$delivery_yn."', 
							day_delivery_yn       = '".$day_delivery_yn."', 
							display_yn       = '".$display_yn."'
					where part_div = '".$data[PART_DIV]."' AND part_no = '".$data[PART_NO]."' AND brandcd = '".$data[BRANDCD]."' 
					RETURNING * 
				)
				insert into tblstore 
				(name, address, phone, view, area_code, category, vendor, stime, etime, 
				 coordinate, store_code, regdt, pickup_yn, delivery_yn, day_delivery_yn, display_yn, part_div, part_no, 
				 brandcd  )
				Select  '".trim($data[PART_NAME])."','".$address."', '".$data[TEL_NO]."', '".$view."', '".$area_code."', '".$erp_category_arr[$data[PART_DIV]]."', '".$vender."', '".$stime."', '".$etime."', 
						'".$coordinate."','".$store_code."','".$regdt."','N','N','N', 'Y', '".$data[PART_DIV]."','".$data[PART_NO]."','".$data[BRANDCD]."'
				WHERE NOT EXISTS ( SELECT * FROM upsert )
				";
		$ret = pmysql_query($sql, get_db_conn());
		if ($coordinate_yn == 'Y') print_r($sql);
		if($err=pmysql_error()) echo $err."\r\n";

		// 실서버에는 주석풀자..
		$sql = "update TA_OM006 set RCV_DATE=SYSDATE where RCV_DATE is NULL and PART_DIV = '".$data[PART_DIV]."' AND PART_NO = '".$data[PART_NO]."' AND BRAND = '".$data[BRANDCD]."'  ";
		$smt_rec = oci_parse($conn, $sql);
		oci_execute($smt_rec);
	}


    $cnt++;

    if( ($cnt%1000) == 0) echo "cnt = ".$cnt."\r\n";
}

oci_free_statement($smt);
oci_close($conn);

echo "END = ".date("Y-m-d H:i:s")."\r\n";

function getAddr1($addr1) {
	$old_addr1	= array(
		'',
		'강원 동해시 천곡동 1072 삼양골든 106호',
		'강원도 동해시 대학로 24 ,104',
		'강원도 원주시 서원대로 500 프리미엄아울렛2층',
		'경기 구리시 인창동 419-7 외4필지',
		'경기 수원시 권선구 권선동 매산로1 18',
		'경기 수원시 장안구 조원동 893 아일렛쇼핑타운 168호',
		'경기 수원시 장안구 조원동 893 아일렛타운1층 116호',
		'경기 안산시 고잔동 541 안산종합상가 2-133',
		'경기 안산시 상록구 사동 1530 안산의류상설타운 A-11',
		'경기 안성시 창전동 152-5외 1필지',
		'경기 이천 호법면 단천리 869 이천패션유통물류단지 내 C1-2가든워크',
		'경기 평택시 평택동 50-3외 2필지',
		'경기 포천시 소흘읍 이동교리 84-2 용상상가1층',
		'경기도 김포시  김포대로 1623 (모아패션아울렛)',
		'경기도 성남시 분당구 판교로 242, 에이동 8층(삼평동, 판교디지털센터 에이동8층)',
		'경기도 시흥시 대은로 80, 120호(은행동,하이런빌딩)',
		'경기도 여주시 명품1로 22-2',
		'경기도 여주시 상거동 375-64 375ST 여주-2 1층 102',
		'경기도 의정부시 민락동 804-6 민락2지구 상업용지 7-2,3블럭',
		'경남 김해시 장유면 신문리 김해관광유통단지 1B4L번',
		'경남 진주시 정촌면 삼일로95번길 46',
		'경남 진주시 진양호로 274, 제나호(신안동)',
		'경남 창원시 두대동 333 더시티세븐 E210',
		'경남 창원시 중앙동 98-4 성원 106',
		'경남 창원시 중앙동 98-4 성원오피스텔 1층 123호',
		'경남 창원시 팔용동 30-1 외3필지',
		'경상남도 김해시 활천로267번길 19, 1층 1-8호(삼방동)',
		'광주광역시 광산구 사암로395',
		'광주시 북구 설죽로 325 외 1필지 청룡빌딩 1층',
		'광주시 서구 치평동 1326 세정아울렛 247번지 2층 247호',
		'대전 서구 탄방동 746 로데오타운빌딩 133호',
		'대구 수성구 대흥동 504 스타디움몰 38호',
		'대구 중구 덕산동 53-3 외1필지',
		'대구 중구 동문동 20-4 외6필지',
		'대구광역시 동구 팔공로49길 33 A-110(봉무동,이시아봉무아울렛)',
		'대전광역시 유성구 테크노1로 12-21',
		'부산 북구 화명동 2272-1 로얄펠리스 104호',
		'부산광역시 부산진구 백양대로 1192',
		'부산광역시 사하구 장림동 1055 세정아울렛부산점2층',
		'서울 강남구 도산대로 327, 502(신사동)',
		'서울 강남구 신사동 664-24 희용빌딩 5층',
		'서울 관악구 봉천동 862-1 에그엘로우지하2층 B2081',
		'서울 구로구 고척동 76-41 일이상전자타운제3동',
		'서울 구로구 구로동로 119, 1층,2층(구로동)',
		'서울 금천구 디지털로10길 9 2001(가산동20층)',
		'서울 성동구 행당동 155-1 서울숲더些怜鑿므卵퓜걱퓔탐체',
		'서울 은평구 역촌동 85-41 리더스파크 103호',
		'서울 중구 장충동1가 48-24 금화빌딩2층 202',
		'서울 중랑구 상봉동 73-10 이노시티AB 1층,2층,3층',
		'서울시 성동구 용답동 233-5 예림빌딩 5층',
		'울산 남구 달동 1360-3 외 4필지',
		'울산 북구 진장동 진장명촌지구54B1N',
		'울산시 북구 진장유통로 78-6(진장동)',
		'인천 남동구 논현동 20 논현2지구 20',
		'인천 서구 마전동 검단2지구 33BL2LT외 1필지 영진프',
		'인천 서구 북항로 32번안길 50, 381-70(원창동)',
		'인천 연수구 연수동 606-3 1층우측',
		'전남 목포시 상동 987-2 2층건물중 1층일부',
		'전라남도 목포시 장미로 38',
		'전라남도 순천시 연향중앙상가길 12, 나동 103호(연향동, 1층)',
		'전라북도 전주시 덕진구 동부대로 1215 메가월드 A동 278호',
		'전라북도 전주시 완산구 백제대로 334',
		'전라북도 전주시 완산구 유연로 34',
		'전라북도 전주시 완산구 홍산남로 28. 1층 102호',
		'전북 군산시 나운동 97-3 예스트쇼핑몰 6동 105호',
		'전북 전주시 완산구 서신동 971번지',
		'제주특별자치도 제주시 박성내서길 16-7,1층(이도이동,모꼬지빌)',
		'제주특별자치도 제주시 탑동로 38,301',
		'충남 당진군 당진읍 읍내리 546-9 546-81층 상가',
		'충남 당진시 신평면 서해안고속도로 276',
		'충남 천안시 서북구 공원로 196, 1,2,4층(불당동,펜타포트)',
		'충남 천안시 서북구 성정동 1123 탑존빌딩 104호',
		'충청남도 예산군 예산읍 예산로194번길 28',
		'충청북도 청주시 흥덕구 분평동 1383 조은주차빌딩 102',
		'충청북도 충주시 번영대로 200, 가동 102(연수동)'
	);

	$new_addr1	= array(
		'',
		'강원 동해시 천곡동 1072',
		'강원도 동해시 대학로 24',
		'강원도 원주시 서원대로 500',
		'경기 구리시 인창동 419-7',
		'경기 수원시 권선구 매산로1가 18',
		'경기 수원시 장안구 조원동 893',
		'경기 수원시 장안구 조원동 893',
		'경기 안산시 고잔동 541',
		'경기 안산시 상록구 사동 1530',
		'경기 안성시 창전동 152-5',
		'경기 이천 호법면 단천리 869',
		'경기 평택시 평택동 50',
		'경기 포천시 소흘읍 이동교리 84-2',
		'경기도 김포시 양촌읍 누산리 420',
		'경기도 성남시 분당구 판교로 242',
		'경기도 시흥시 대은로 80',
		'경기도 여주시 상거동 375-54 ',
		'경기도 여주시 상거동 375-54 ',
		'경기도 의정부시 천보로 44',
		'경상남도 김해시 장유로 469 김해관광유통단지 1B4L번지',
		'경상남도 진주시 정촌면 화개리 1596-3',
		'경남 진주시 진양호로 274',
		'경남 창원시 두대동 333',
		'경남 창원시 중앙동 98-4',
		'경남 창원시 중앙동 98-4',
		'경남 창원시 팔용동 30-1',
		'경상남도 김해시 활천로267번길 19',
		'광주광역시 광산구 하남동 804 ',
		'광주시 북구 설죽로 325',
		'광주시 서구 치평동 1326',
		'대전 서구 탄방동 746',
		'대구 수성구 대흥동 504',
		'대구 중구 덕산동 53-3',
		'대구 중구 동문동 20-4',
		'대구광역시 동구 팔공로49길 33',
		'대전광역시 유성구 관평동 1355 ',
		'부산 북구 화명동 2272-1',
		'부산 북구 백양대로 1192',
		'부산광역시 사하구 장림동 1055',
		'서울 강남구 도산대로 327',
		'서울 강남구 신사동 664-24',
		'서울 관악구 봉천동 862-1',
		'서울 구로구 고척동 76-41',
		'서울 구로구 구로동로 119',
		'서울 금천구 디지털로10길 9',
		'서울 성동구 행당동 155-1',
		'서울 은평구 역촌동 85-41',
		'서울 중구 장충동1가 48-24',
		'서울 중랑구 상봉동 73-10',
		'서울시 성동구 용답동 233-5',
		'울산 남구 달동 1360-3',
		'울산광역시 북구 진장17길 10',
		'울산광역시 북구 진장동 283-4',
		'인천 남동구 논현동 20',
		'인천광역시 서구 마전동 582-4',
		'인천 서구 북항로 32번안길 50',
		'인천 연수구 연수동 606-3',
		'전남 목포시 상동 987-2',
		'전라남도 목포시 상동 994-2',
		'전라남도 순천시 연향중앙상가길 12',
		'전라북도 전주시 덕진구 동부대로 1215',
		'전라북도 전주시 완산구 중화산2동 265-6',
		'전라북도 전주시 완산구 효자동3가 1019-5 ',
		'전라북도 전주시 완산구 효자동2가 1242-7 ',
		'전북 군산시 나운동 97-3',
		'전북 전주시 완산구 서신동 971',
		'제주특별자치도 제주시 이도2동 1978-4',
		'제주특별자치도 제주시 탑동로 38',
		'충청남도 당진시 읍내동 546-9',
		'충청남도 당진시 신평면 매산리 516-40',
		'충청남도 천안시 서북구 공원로 196',
		'충남 천안시 서북구 성정동 1123',
		'충청남도 예산군 예산읍 예산리 501-5',
		'충청북도 청주시 흥덕구 분평동 1383',
		'충청북도 충주시 번영대로 200'
	);

	$key    = array_search($addr1, $old_addr1);
	if ($key)
		$res_addr1	= $new_addr1[$key];
	else
		$res_addr1	= $addr1;

	return $res_addr1;
}
?>
