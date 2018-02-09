<?php
$listnum = 9;

$sql  = "SELECT * FROM tbllookbook ";
$sql .= "WHERE hidden = 1 ";
$sql .= "ORDER BY no desc ";

$paging = new New_Templet_paging($sql, 10, $listnum, 'GoPage', true);
$t_count = $paging->t_count; 
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$result = pmysql_query($sql);

$list_html = '';
while ($row = pmysql_fetch_array($result)) {
    $list_html .= '
            <li>
                <a href="/front/lookbook_view.php?id=' . $row['no'] . '">
                    <figure>
                        <img src="/data/shopimages/lookbook/' . $row['img'] . '" alt="" width="366" height="247">
                        <figcaption>' . $row['title'] . '</figcaption>
                    </figure>
                </a>
            </li>';
}

if ( $isMobile ) {
    //include ($Dir.TempletDir."studio/mobile/studio_lookbook_list_TEM001.php"); 
} else {
?>

<div id="contents">
    <div class="containerBody sub-page">
        <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>

        <div class="lookbook-wrap">
            <ul class="lookbook-list">
                <?=$list_html?>
            </ul><!-- //.lookbook-list -->
            <div class="list-paginate">
                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
            </div>
        </div><!-- //.lookbook-wrap -->

        

    </div><!-- //공통 container -->
</div><!-- //contents -->
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>" >
    <input type=hidden name=idx value="<?=$idx?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<script>
function GoPage(block,gotopage) {
    document.form2.block.value=block;
    document.form2.gotopage.value=gotopage;
    document.form2.submit();
}
</script>
<?php } ?>
