
<?	
	$qry = "WHERE a.bridx = b.bridx and a.id = '" . $_ShopInfo->getMemid() . "' "; 
    $tmp_sort=explode("_",$sort);

    $sql  = "SELECT tblResult.wish_idx, tblResult.bridx, tblResult.regdt, tblResult.brandname, tvia.s_img ";
    $sql .= "FROM ( ";
    $sql .= "SELECT a.wish_idx, a.bridx, to_char(cast(substr(a.date,0,9) as date),'YYYY-MM-DD') as regdt, b.brandname, b.vender ";
    $sql .= "FROM tblbrandwishlist a, tblproductbrand b " . $qry . " ";

    if($tmp_sort[0]=="date") 
        $sql.= "ORDER BY a.date ".$tmp_sort[1]." ";
    else if($tmp_sort[0]=="name") 
        $sql.= "ORDER BY b.brandname".$tmp_sort[1]." ";
    else 
        $sql.= "ORDER BY a.date DESC ";

    $sql .= ") AS tblResult LEFT JOIN tblvenderinfo_add tvia ON tblResult.vender = tvia.vender ";

    if ( $isMobile ) {
        $listnum = 8;
        $paging = new New_Templet_mobile_paging($sql, 5,$listnum, 'GoPage', true);
    } else {
        $paging = new New_Templet_paging($sql,10,$listnum);
    }
    $t_count = $paging->t_count;
    $gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql);

    $result=pmysql_query($sql,get_db_conn());
    $cnt=0;

    $brand_list_html = '';
    while ( $row=pmysql_fetch_array($result) ) {

        $sub_sql  = "SELECT a.productcode ";
        $sub_sql .= "FROM tblbrandproduct a, tblproduct b ";
        $sub_sql .= "WHERE a.bridx = " . $row['bridx'] . " and b.display = 'Y' and a.productcode = b.productcode ";
        $sub_sql .= "ORDER BY b.regdate desc limit 3 ";
        $sub_result = pmysql_query($sub_sql);
        
        $arrProdCode = array();
        while ($sub_row = pmysql_fetch_array($sub_result)) {
            array_push($arrProdCode, $sub_row['productcode']);
        }

        $arrProd[0] = "";
        if ( count($arrProdCode) >= 1 ) {
            $productcodes = "'" . implode("','", $arrProdCode) . "'";
            $prod_sql  = "SELECT productcode, productname, sellprice, consumerprice, brand, maximage, minimage, tinyimage, ";
            $prod_sql .= "mdcomment, review_cnt, icon, over_minimage, soldout, quantity ";
            $prod_sql .= "FROM tblproduct WHERE display = 'Y' and productcode in ( {$productcodes} ) ";
            $arrProd = productlist_print($prod_sql, "W_014", $arrProdCode);
        }

        if ( $isMobile ) {
            $imgUrl = getProductImage($Dir.DataDir.'shopimages/vender/', $row['s_img']);
            $brand_list_html .= '
                        <li>
                            <input type="checkbox" name="idx[]" value="' . $row['wish_idx'] . '">
							<a href="/m/brand_detail.php?bridx=' . $row['bridx'] . '">
								<figure>
									<div class="img"><img src="' . $imgUrl . '" alt=""></div>
									<figcaption>' . $row['brandname'] . '</figcaption>
								</figure>
							</a>
                        </li>';

        } else {
            $brand_list_html .= '
				<div class="brand-one">
					<div class="wish-brand">
						<figure>
							<a href="/front/brand_detail.php?bridx=' . $row['bridx'] . '"><p class="thumb" style="background:url(\'/data/shopimages/vender/' . $row['s_img'] . '\') 0 center no-repeat;"></p></a>
							<figcaption>
								<input type="checkbox" id="" name="idx[]" value="' . $row['wish_idx'] . '" class="checkbox-def"><br>
								<span class="name"><a href="/front/brand_detail.php?bridx=' . $row['bridx'] . '"><span class="name">' . $row['brandname'] . '</span></a>
							</figcaption>
							<a href="/front/brand_detail.php?bridx=' . $row['bridx'] . '" class="btn-dib-function"><span>BRAND VIEW</span></a>
						</figure>
					</div>' . $arrProd[0] . '
				</div>';
        }

        $cnt++;
    } ; # end of while

    if ( $cnt == 0 ) {
        if ( !$isMobile ) {
            $brand_list_html .= '
                <div class="brand-one">
                    <p class="no-brand">등록된 관심브랜드가 없습니다.</p>
                </div>';
        }
    }

    if ( $isMobile ) {
?>

<div class="sub-title">
    <h2>MY WISHBRAND</h2>
    <a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
</div>

<div class="mypage-wrap">
    
    <p class="att-title star">관심브랜드 <?=number_format($t_count)?>개를 찜했습니다.</p>

    <?if ( $t_count == 0 ) { ?>
    <div class="none-ment margin"> 
        <p>관심브랜드가 없습니다.</p> 
    </div>
    <?} else {?>
    <div class="wish-brand-wrap">
        <ul class="list">
            <?=$brand_list_html?>
        </ul>
    </div>
    <?php 
        if( $paging->pagecount > 1 ){ 
    ?>
    <div class="paginate">
        <div class="box">
            <?php echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page; ?>
        </div>
    </div>
    <?php
        } 
    ?>
    <div class="btn-place">
        <a href="javascript:;" onClick="javascript:GoDelete();" class="btn-function">선택삭제</a>
        <a href="javascript:;" onClick="javascript:AllDelete();" class="btn-def">전체삭제</a>
    </div>
    <?php } ?>
</div><!-- //.mypage-wrap -->

<?php
    } else {
?>

<style type="text/css">
</style>

	<div class="containerBody sub-page">

	<div class="breadcrumb">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="mypage.php">MY PAGE</a></li>
			<li class="on"><a>관심브랜드</a></li>
		</ul>
	</div>
	
		<!-- LNB -->
	<div class="left_lnb">
		<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
		<!---->
	</div><!-- //LNB -->

		<div class="right_section mypage-content-wrap">

			<div class="wish-brand-wrap">
				<div class="mypage-title">관심브랜드</div>

                <?=$brand_list_html?>

				<div class="btn-place">
					<button class="btn-dib-line" onClick="javascript:GoDelete();">선택브랜드 삭제</button>
					<button class="btn-dib-line" onClick="javascript:AllDelete();">전체브랜드 삭제</button>
				</div>


                <div class="list-paginate-wrap">
                    <div class="list-paginate">
                    <?php
                        if( $paging->pagecount > 1 ){
                            echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
                        }
                    ?>
                    </div>
                </div><!-- //.list-paginate-wrap -->

			</div><!-- //.wish-brand-wrap -->
<?php } ?>
