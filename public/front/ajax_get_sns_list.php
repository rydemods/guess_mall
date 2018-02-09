<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page			= $_GET['gotopage'];
$pagePerCount   = 10;
$isMobile       = $_GET['im'];

// 모바일인경우 한 화면에 5개씩
if ( $isMobile ) {
    $pagePerCount = 5;
}

$sql  = "SELECT im.*, iml.productcode, iml.sns_link FROM tblsnsinstamedialink iml RIGHT JOIN  tblsnsinstamedia im ON iml.media_id=im.media_id ";
$sql .= "ORDER BY im.media_id DESC";
$paging = new Tem001_Paging($sql, 10, $pagePerCount, 'GoPage', false);

$htmlResult = "";
if ( $page >= floor($paging->pagecount) ) {
    // 현재 페이지가 마지막 페이지인 경우
    $htmlResult = "END||";   
} else {
    $htmlResult = "||";   
}

// 다음 페이지가 있는 경우
if ( $gotopage < $paging->pagecount ) {
    $sql = $paging->getSql($sql);

    $snsResult = pmysql_query($sql);

    while( $row = pmysql_fetch_array( $snsResult ) ){
        // 이미지
        $s_img = $row['image_std'];

        $pro_link	= "javascript:;";

        $pro_link_prefix = "front";
        if ( $isMobile ) {
            $pro_link_prefix    = "m";
        }

        if ($row['productcode'])  {
            $pro_link	= "javascript:location.href='/{$pro_link_prefix}/productdetail.php?productcode=".$row['productcode']."';";
        } else if ($row['sns_link']) {
            // 어드민을 통해서 직접 링크를 입력한 경우

            if ( $isMobile ) {
                // 모바일의 경우 입력한 링크를 모바일에 맞게 변경
                $sns_link = trim($row['sns_link']);    
                $sns_link = str_replace(array("http://" . $_data->shopurl, "http://www." . $_data->shopurl), "/", $sns_link);   // 쇼핑몰 도메인 제거
                $sns_link = str_replace("/front/", "/m/", $sns_link);   // 모바일 링크 구조로 변경
                
                if ( empty($sns_link) ) $sns_link = "javascript:;";     // 아무 값도 없는 경우 처리
            }

            $pro_link = "javascript:location.href='" . $sns_link . "';";
        }

        if ( $isMobile ) {

            $htmlResult .= '
                <li class="showLayerFadein" >
                    <a class="btn-detail" href="javascript:;">
                        <figure>
                            <img src="' . $s_img . '" alt="">
                            <figcaption>' . nl2br($row['text']) . '</figcaption>
                        </figure>
                    </a>
                    <div class="btnset">
                        <a class="btn-def" href="' . $pro_link . '">PRODUCT</a>
                        <a class="btn-def" href="' . $row['link'] . '" target="_blank">INSTAGRAM</a>
                    </div>
                </li>▒▒';
        } else {
            $htmlResult .= "<li class= 'showLayerFadein'><span class=\"img\"><img src=\"".$s_img."\" alt=\"\"></span>";
            $htmlResult .= "	<p>".nl2br($row['text'])."</p>";
            $htmlResult .= "	<div class=\"btn\">";
            $htmlResult .= "	<button class=\"btn-dib-function\" type=\"button\" onClick=\"{$pro_link}\"><span>PRODUCT</span></button>";
            $htmlResult .= "	<button class=\"btn-dib-function\" type=\"button\" onClick=\"javascript:window.open('".$row['link']."','instagram','');\"><span>INSTAGRAM</span></button>";
            $htmlResult .= "	</div>";
            $htmlResult .= "</li>▒▒";
        }
    }
}

$htmlResult .= "|||" . $isMobile;

echo $htmlResult;
?>
