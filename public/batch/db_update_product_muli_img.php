<?php
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata2.php");

//exit;

echo "Start ".date("Y-m-d H:i:s")."<br>";
echo "<hr>";



update tblmultiimages set 
primg01 = replace(primg01, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1 
and strpos(primg01, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg02 = replace(primg02, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg02, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg03 = replace(primg03, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg03, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg04 = replace(primg04, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg04, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg05 = replace(primg05, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg05, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg06 = replace(primg06, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg06, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg07 = replace(primg07, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg07, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg08 = replace(primg08, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg08, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg09 = replace(primg09, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg09, 'http://deconc.cafe24.com/web/') > 0
;

update tblmultiimages set 
primg10 = replace(primg10, 'http://deconc.cafe24.com/web/', 'http://test-deco.ajashop.co.kr/data/shopimages/cafe24/') 
where 1=1
and strpos(primg10, 'http://deconc.cafe24.com/web/') > 0
;





echo "End ".date("Y-m-d H:i:s")."<br>";
?>
