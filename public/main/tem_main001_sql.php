<?php 

/*********************************************************************
 // 파 일 명		: sql_tem_main001.php
 // 설     명		: 메인 query
 // 작 성 자		: 2017.01.20 - 위민트
 // 수 정 자		:
 //
 *********************************************************************/

// 상품브랜드 정보
$sql_tblproductbrand_list  = "SELECT bridx, brandname, logo_img, brandname2, brandtag FROM tblproductbrand ";
$sql_tblproductbrand_list .= "WHERE 1=1 ";
$sql_tblproductbrand_list .= "AND display_yn = 1 ";
$sql_tblproductbrand_list .= "LIMIT 7";