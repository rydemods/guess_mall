<?php
//KCAPTCHA 설정 파일
$alphabet = "0123456789abcdefghijklmnopqrstuvwxyz"; //절대변경하지 말것

//심볼로 그릴 대상
$allowed_symbols = "0123456789"; #숫자
//$allowed_symbols = "23456789abcdeghkmnpqsuvxyz"; #혼동 가능 문자뺀 알파벳과 숫자(o=0, 1=l, i=j, t=f)

//폰트 폴더 이름
$fontsdir = 'fonts';

//캡차 문자열 길이
$length = mt_rand(5,6); //5~6개
//$length = 5;

//캡차이미지 크기
$width = 120;
$height = 60;

//symbol's vertical fluctuation amplitude divided by 2
$fluctuation_amplitude = 5;

//문자열 사이의 빈공간 여부
$no_spaces = true;

//그레딧 보임
$show_credits = false; //크레딧을 보임으로 하면 세로 12PX 이미지 크기증가
$credits = 'www.captcha.ru'; //비워놓으면 사용중인 도메인으로 보임

//캡차 이미지 색상 (RGB, 0-255)
$foreground_color = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
$background_color = array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));

//캡차이미지 품질(JPEG이미지로 높을수록 고품질)
$jpeg_quality = 90;

//웨브 왜곡사용
$wave = false;
?>