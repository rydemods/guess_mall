<style type="text/css">
</style>

<?	
	$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' ";
	$qry.= "AND a.productcode=b.productcode ";
//    $qry.= "AND b.display='Y' ";
	$qry.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";

    $tmp_sort=explode("_",$sort);
    $sql = "SELECT to_char(cast(substr(a.date,0,9) as date),'YYYY-MM-DD') as regdt,a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.consumerprice,b.sellprice as realprice, ";
    $sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
    $sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num, (SELECT brandname FROM tblproductbrand WHERE bridx = b.brand) as brandname ";
    $sql.= "FROM tblwishlist a, tblproduct b ";
    $sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
    $sql.= $qry." ";
    if($tmp_sort[0]=="date") $sql.= "ORDER BY a.date ".$tmp_sort[1]." ";
    else if($tmp_sort[0]=="marks") $sql.= "ORDER BY a.marks ".$tmp_sort[1]." ";
    else if($tmp_sort[0]=="price") $sql.= "ORDER BY b.sellprice ".$tmp_sort[1]." ";
    else if($tmp_sort[0]=="name") $sql.= "ORDER BY b.productname ".$tmp_sort[1]." ";
    else $sql.= "ORDER BY a.date DESC ";

    if ( $isMobile ) {
        $listnum = 5;
        $paging = new New_Templet_mobile_paging($sql, 5,$listnum, 'GoPage', true);
    } else {
        $paging = new New_Templet_paging($sql,10,$listnum);
    }

    $t_count = $paging->t_count;
    $gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql);
    $result=pmysql_query($sql,get_db_conn());

    $cnt=0;
    $prod_list_html = '';
    while($row=pmysql_fetch_object($result)) {
        $row->quantity=1;

        $assembleuse =$row->assembleuse;
        $optvalue="";

        if (strlen($row->option_price)==0) {
            $price = $row->realprice;
            $tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
            $sellprice=$row->sellprice;
        } else if (strlen($row->opt1_idx)>0) {
            $option_price = $row->option_price;
            $pricetok=explode(",",$option_price);
            $priceindex = count($pricetok);
            $price = $pricetok[$row->opt1_idx-1]*$row->quantity;
            $tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
            $sellprice=$pricetok[$row->opt1_idx-1];
        }
        $bankonly_html = ""; $setquota_html = "";
        if (strlen($row->etctype)>0) {
            $etctemp = explode("",$row->etctype);
            for ($i=0;$i<count($etctemp);$i++) {
                switch ($etctemp[$i]) {
                    case "BANKONLY": $bankonly = "Y";
                        $bankonly_html = " <img src=\"".$Dir."images/common/bankonly.gif\" border=\"0\"> ";
                        break;
                    case "SETQUOTA":
                        if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
                            $setquotacnt++;
                            $setquota_html = " <img src=\"".$Dir."images/common/setquota.gif\" border=\"0\">";
                            $setquota_html.= "</b><font color=\"#000000\" size=\"1\">(";
                            $setquota_html.="3~";
                            $setquota_html.= $_data->card_splitmonth.")</font>";
                        }
                        break;
                }
            }
        }

        $number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
        if($row->package_num || $row->assembleuse=='Y')	$cart_ok='';
        else $cart_ok='cart_ok';

        // ===========================================================================
        // 상품 옵션 정보
        // ===========================================================================
        $arrTmpOption = array();
        if ( !empty($row->opt1_idx) ) { array_push($arrTmpOption, $row->opt1_idx); }
        if ( !empty($row->opt2_idx) ) { array_push($arrTmpOption, $row->opt2_idx); }

        $option = implode(" - ", $arrTmpOption);
        $optionArr = implode(chr(30), $arrTmpOption);   // 장바구니에 담기 위한 옵션 정보

        if ( $isMobile ) {
            $consumer_class = "";
            if ( $row->consumerprice <= 0 || $row->consumerprice == $row->sellprice ){
                $consumer_class = "hide";
            }

            $prod_list_html .= '
                <li>
                    <div class="item-info-wrap vm"><!-- 상하 중앙정렬시 vm 클래스 추가 -->
                        <div class="inner">
                            <p class="thumb"><a href="/m/productdetail.php?productcode=' . $row->productcode . '"><img src="' . getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage) . '" alt=""></a></p>
                            <div class="price-info">
                                <input type="checkbox" name="idx[]" value="' . $row->wish_idx . '">
                                <span class="brand-nm">' . $row->brandname . '</span>
                                <span class="goods-nm">' . $row->productname . '</span>
                                <span class="price"><del class="' . $consumer_class . '">' . number_format($row->consumerprice) . '</del><strong>' . number_format($row->sellprice) . '</strong></span>
                                <span class="date">' . $row->regdt . '</span>
                            </div>
                        </div>
                    </div>
                </li>
            ';
        } else {
            $prod_list_html .= '
                <tr>
                    <td><input type="checkbox" class="checkbox-def" name="idx[]" value="' . $row->wish_idx . '" class="' . $cart_ok . '"></td>
                    <td ><A HREF="' . $Dir.FrontDir . 'productdetail.php?productcode=' . $row->productcode . '" >';

            if(strlen($row->tinyimage)!=0) {
                $prod_list_html .= '<img class="img-size-mypage" src="' . getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage) . '" style="max-width:80px" border="0">';
            }else {
                $prod_list_html .= '<img class="thumb-mypage" src="' . $Dir. 'images/no_img.gif" width="" border="0">';
            }
                
            $prod_list_html .= '
                    </a></td>
                    <td class="ta_l">
                        <A HREF="' . $Dir.FrontDir . 'productdetail.php?productcode=' . $row->productcode . '" >
                        <span class="brand-color">' . $row->brandname . '</span><br>
                        ' . $row->productname . '<br>
                        ' . $option . '
                        </a>
                    </td>
                    <!-- <td>' . $row->regdt . '</td> -->
                    <td>' . number_format($row->sellprice) . '</td>
                    <td>' . $row->regdt . '</td>
                    <td>
                        <!--a href="javascript:;" onClick="javascript:insert_basket(\'' . $row->productcode . '\',\'' . $optionArr . '\');" class="btn-dib-line"><span>장바구니</span></a><br-->
                        <a href="javascript:;" onClick="javascript:delete_wishlist(\'' . $row->wish_idx . '\');" class="btn-dib-line"><span>삭제</span></a>
                    </td>
                </tr>';
        }

	    $cnt++;
    } ; # end of while

    if ( $cnt == 0 ) {
        if ( !$isMobile ) {
            $brand_list_html .= '
                <div class="brand-one">
                    <p class="no-brand">등록된 관심상품이 없습니다.</p>
                </div>';
        }
    }


    if ( $isMobile ) {
?>
    <div class="sub-title">
        <h2>MY WISHLIST</h2>
        <a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
    </div>

    <div class="mypage-wrap">
        <p class="att-title star">상품 <?=number_format($t_count)?>개를 찜했습니다.</p>
		
		<?if ( $t_count == 0 ) { ?>
		<div class="none-ment margin"> 
			<p>관심상품이 없습니다.</p> 
		</div>
		<?} else {?>
        <ul class="my-thumb-list">
            <?=$prod_list_html?>
        </ul>
        <div class="paginate">
            <div class="box">
                <?php 
                    if( $paging->pagecount > 1 ){ 
                        echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
                    } 
                ?>
            </div>
        </div>
        <div class="btn-place">
            <a href="javascript:;" onClick="javascript:GoDelete();" class="btn-function">선택삭제</a>
            <a href="javascript:;" onClick="javascript:AllDelete();" class="btn-def">전체삭제</a>
        </div>
		<?php } ?>
    </div><!-- //.mypage-wrap -->

<?php
    } else {
?>

	<div class="containerBody sub-page">
	
	<div class="breadcrumb">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="mypage.php">MY PAGE</a></li>
			<li class="on"><a>관심상품</a></li>
		</ul>
	</div>
	
		<!-- LNB -->
	<div class="left_lnb">
		<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
		<!---->
	</div><!-- //LNB -->

		<div class="right_section mypage-content-wrap">

			<!-- 테이블 -->
			<div class="table_wrap">
				<!-- <div class="right_area">
					<div class="select_type open ta_l" style="width:150px; z-index:70">
						<span class="ctrl"><span class="arrow"></span></span>
						<button type="button" class="myValue">10개씩 보기</button>
						<ul class="aList">
							<li><a href="javascript:ChangeListnum('10')">10개씩 보기</a></li>
							<li><a href="javascript:ChangeListnum('20')">20개씩 보기</a></li>
							<li><a href="javascript:ChangeListnum('30')">30개씩 보기</a></li>
						</ul>
					</div>
				</div> -->

				<h4 class="mypage-title align-top">
					관심상품
					<div class="function">
						<div class="goods-sort-wrap">
							<div class="view-ea">
								<button <?if ( $listnum == 25 ) { echo 'class="on"'; }?> type="button" onClick="javascript:ChangeListnum('25')";>25</button>
								<button <?if ( $listnum == 50 ) { echo 'class="on"'; }?> type="button" onClick="javascript:ChangeListnum('50');">50</button>
								<button <?if ( $listnum == 75 ) { echo 'class="on"'; }?> type="button" onClick="javascript:ChangeListnum('75');">75</button>
							</div>
						</div>
					</div>
				</h4>
					
				<table class="th-top util">
					<colgroup>
						<col style="width:55px" ><col style="width:100px" ><col style="width:auto" ><col style="width:150px" ><col style="width:120px" ><col style="width:150px" >
					</colgroup>
					<thead>
					<tr>
						<th scope="col"><input type="checkbox" class="checkbox-def" name="" id="all_check" onclick="javascript:CheckBoxAll()"></th>
						<th scope="col" colspan=2>상품정보</th>
						<th scope="col">상품금액</th>
						<th scope="col">담은날짜</th>
						<th scope="col">삭제</th>
					</tr>
					</thead>

                    <?=$prod_list_html?>
					
<?php
	if ($cnt == 0) {
?>
					<tr>
						<td colspan=6>등록된 관심상품이 없습니다.</td>
					</tr>
<?
	}
?>
				</table>
				<div class="btn-function-place">
					<a href="javascript:;" onClick="javascript:GoDelete();" class="btn-dib-line"><span>선택상품 삭제</span></a>
					<a href="javascript:;" onClick="javascript:AllDelete();" class="btn-dib-line"><span>전체상품 삭제</span></a>
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


			</div><!-- //테이블 -->
		</div>
	</div>

<?php
    }
?>

