<?php

$lbno = $_GET['id'];
if ( empty($lbno) ) {
    $sql  = "SELECT * FROM tbllookbook WHERE hidden = 1 ORDER BY no desc LIMIT 1";
    list($lbno) = pmysql_fetch($sql);
}

$sql  = "SELECT * FROM tbllookbook_content WHERE lbno = ${lbno} ";
$sql .= "ORDER BY sort asc ";
$result = pmysql_query($sql);

$idx = 0;
$content_rolling_html = '';
$bottom_rolling_html = '';

while ($row = pmysql_fetch_array($result)) {

    if ( $isMobile ) {
        $content_rolling_html .= '<li onClick="javascript:show_lookbook_prodlist(\'' . $row['lbno'] . '\', \'' . $row['no'] . '\');"><a href="javascript:;"><img src="/data/shopimages/lookbook/' . $row['img'] . '" alt=""></a></li>';
    } else {
	
        if($row['total_num']){
            $lr_ex=explode("|",$row["lr_coordinates"]);
            $ud_ex=explode("|",$row["ud_coordinates"]);
            $vi_ex=explode("|",$row["view_lr"]);
            $vi_ex2=explode("|",$row["view_ud"]);
            $vi_ex3=explode("|",$row["view_img"]);
            $pr_ex=explode("|",$row["productcodes"]);

            $content_rolling_html .= '<li>';
            for($i=0;$i<$row['total_num'];$i++){
                $vi_lr=$vi_ex[$i]=="L"?"left":"right";
                $vi_ud=$vi_ex2[$i]=="U"?"top":"bottom";
                $vi_class=$vi_ud."-".$vi_lr;
                $ud_px=$ud_ex[$i]-15;
                $lr_px=$lr_ex[$i]-15;
                //이미지
                $tinyimage=pmysql_fetch_array(pmysql_query("select tinyimage from tblproduct where productcode='".$pr_ex[$i]."'"));
                /*$img_check=stripos($tinyimage['tinyimage'], "http");
                
                if(empty($img_check)){
                    $p_img=$tinyimage['tinyimage'];
                }else{
                    $p_img="http://".$shopurl."/data/shopimages/product/".$tinyimage['tinyimage'];
                }*/

                $p_img= getProductImage($Dir.DataDir.'shopimages/product/', $tinyimage['tinyimage']);

                $content_rolling_html .= '  <div class="star-link '.$vi_class.'" style="top:'.$ud_px.'px; left:'.$lr_px.'px">';
                $content_rolling_html .= '      <a href="javascript:;">';
                
                if($vi_ex3[$i]=="B"){
                    $content_rolling_html .= '          <img src="../static/img/icon/icon_lookbook_star_b.png">';
                }else{
                    $content_rolling_html .= '          <img src="../static/img/icon/icon_lookbook_star_w.png">';
                }

                $content_rolling_html .= '      </a>';
                $content_rolling_html .= '      <div class="thumb ">';
                $content_rolling_html .= '      <a href="/front/productdetail.php?productcode='.$pr_ex[$i].'">';
                $content_rolling_html .= '          <img src="'.$p_img.'" alt="썸네일">';
                $content_rolling_html .= '      </a>';
                $content_rolling_html .= '      </div>';
                $content_rolling_html .= '  </div>';
            
            }
        }

        $content_rolling_html .= '  <img src="/data/shopimages/lookbook/' . $row['img'] . '" alt="" >';
        $content_rolling_html .= '</li>';
    }

    if ( $isMobile ) {
        $bottom_rolling_html .= '<li class="" onClick="javascript:show_lookbook_prodlist(\'' . $row['lbno'] . '\', \'' . $row['no'] . '\');"><a href="javascript:;"><img src="/data/shopimages/lookbook/' . $row['img'] . '" alt=""></a></li>';
    } else {
        $bottom_rolling_html .= '<a data-slide-index="' . $idx . '" href=""><img src="/data/shopimages/lookbook/' . $row['img'] . '" alt=""></a>';
    }

    $idx++;
}

$sql  = "SELECT * FROM tbllookbook ";
$sql .= "WHERE hidden = 1 ";
$sql .= "ORDER BY no desc ";
$result = pmysql_query($sql);

$lookbook_list      = '';
$firstItemName      = '';
$lookbook_title     = "";
$lookbook_subtitle  = "";
while ($row = pmysql_fetch_array($result)) {
    if ( empty($firstItemName) && $row['no'] === $lbno ) {
        $firstItemName = $row['title'];

        $lookbook_title = $row['title'];
        $lookbook_subtitle = $row['subtitle'];
    }

    $folderName = "front";
    if ( $isMobile ) {
        $folderName = "m";
    }
    $lookbook_list .= '<li><a href="/' . $folderName . '/lookbook_view.php?id=' . $row['no'] . '">' . $row['title'] . '</a></li>';
}

$lookbook_select = '<div class="select dark">
                    <span class="ctrl"><span class="arrow"></span></span>
                    <button type="button" class="my_value"><span>' . $firstItemName . '</span></button>
                    <ul class="a_list">' 
                    . $lookbook_list . 
                    '</ul>
                </div>';

if ( $isMobile ) {
    include ($Dir.TempletDir."studio/mobile/studio_lookbook_detail_TEM001.php"); 
} else {
?>

<div id="contents">
        <div class="containerBody sub-page">
            
            <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>
			<input type="hidden" name="countcheck" id="countcheck" value=0>
            <div class="lookbook-wrap">
                <?=$lookbook_select?>               
                <div class="lookbook-rolling-wrap with-btn-rolling-big">
                    <p class="title"><?=$lookbook_title?><span><?=$lookbook_subtitle?></span></p>
                    <ul class="lookbook-rolling "><?=$content_rolling_html?></ul>
                    <div class="lookbook-thumb"><?=$bottom_rolling_html?></div>
                </div><!-- //.lookbook-rolling-wrap -->
                <div class="ta-c mt-30">
                    <button class="btn-dib-function" type="button" id="list_btn" onClick="javascript:location.href='/front/lookbook_list.php';"><span>LIST</span></button>
                </div>
            </div><!-- //.lookbook-wrap -->

        </div><!-- //공통 container -->
    </div><!-- //contents -->
<?php } ?>
