<?
#날씨 코드
//전국 108
//서울,경기 109
//강원 105
//충북 131
//충남 133
//전북 146
//전남 156
//경북 143
//경남 159
//제주 184

$weather_code = array("109"=>"서울,경기","105"=>"강원","131"=>"충북","133"=>"충남","146"=>"전북","156"=>"전남","143"=>"경북","159"=>"경남","184"=>"제주");

#이미지 코드
$brand_img = array("nike"=>"@logo_nike.png","adidas"=>"@logo_adidas.png","teva"=>"@logo_teva.png","converse"=>"@logo_converse.png","diadora"=>"@logo_diadora.png", "keds"=>"@logo_keds.png","puma"=>"@logo_puma.png","reebok"=>"@logo_reebok.png","asicstiger"=>"@logo_asics.png");



/*이미지 코드
01 : 맑음
02 : 구름조금
03 : 구름많음
04 : 구름많고 비
05 : 구름많고 눈
06 : 구름많고 비 또는 눈
07 : 흐림
08 : 흐리고 비
09 : 흐리고 눈
10 : 흐리고 비 또는 눈
11 : 흐리고 낙뢰
12 : 뇌우, 비
13 : 뇌우, 눈
14 : 뇌우, 비 또는 눈
15 : 안개
*/
#지금 예보 아이콘 이미지
$now_img = array("01"=>"ico_weather_01.png","02"=>"ico_weather_02.png","03"=>"ico_weather_03.png","04"=>"ico_weather_07.png","07"=>"ico_weather_04.png","08"=>"ico_weather_08.png","11"=>"ico_weather_09.png","12"=>"ico_weather_08.png","13"=>"ico_weather_09.png","14"=>"ico_weather_12.png","15"=>"ico_weather_15.png","16"=>"ico_weather_15.png","17"=>"ico_weather_15.png","18"=>"ico_weather_15.png","20"=>"ico_weather_08.png","21"=>"ico_weather_09.png","22"=>"ico_weather_10.png","23"=>"ico_weather_10.png");

#동네예보 아이콘
$day_img =  array("맑음"=>"ico_weather_01.png","구름 조금"=>"ico_weather_02.png","구름 많음"=>"ico_weather_03.png","흐림"=>"ico_weather_07.png","비"=>"ico_weather_08.png","눈/비"=>"ico_weather_10.png","눈"=>"ico_weather_09.png");

#주간예보 아이콘
$ju_img = array("맑음"=>"ico_weather_01.png","구름조금"=>"ico_weather_02.png","구름많음"=>"ico_weather_03.png","구름많고 비"=>"ico_weather_04.png","구름많고 비/눈"=>"ico_weather_06.png","구름많고 눈/비"=>"ico_weather_06.png","구름많고 눈"=>"ico_weather_05.png","흐림"=>"ico_weather_07.png","흐리고 비"=>"ico_weather_08.png","흐리고 비/눈"=>"ico_weather_10.png","흐리고 눈/비"=>"ico_weather_10.png","흐리고 눈"=>"ico_weather_09.png");

#요일번호
$yoil = array("0"=>"일","1"=>"월","2"=>"화","3"=>"수","4"=>"목","5"=>"금","6"=>"토");



?>