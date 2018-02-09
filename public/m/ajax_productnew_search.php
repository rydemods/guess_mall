<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//exdebug($_POST);
//exdebug($_GET);

$imagepath=$Dir.DataDir."shopimages/product/";
$pr_link = $Dir.'m/productdetail.php?productcode=';
$sort = "recent";
$soldout = $_POST['soldout'];
$brand = $_POST['brand'];
$listnum = $_POST['listnum'] ?: "10";

//조건
$qry = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N' ";

$strAddQuery = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N' ";

//브랜드별 검색
// exdebug($brand);
if(!empty($brand)){
    foreach($brand as $i => $v){
        if($i == 0){
            $strAddQuery.= " AND (a.brand = '".$v."'";
        }else{
            $strAddQuery.= " OR a.brand = '".$v."'";
        }
    }
    $strAddQuery.=")";
}

// 품절상품제외 2016-10-10
if($soldout == "1") {
    $strAddQuery.= " AND a.quantity > 0 ";
}


//상품리스트
$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.color_code, ";
$sql.= "a.maximage, a.minimage,a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, a.brand, a.icon, a.soldout, a.prodcode, a.colorcode, a.sizecd, COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$sql.= "TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt ";
$sql.= "FROM (select *, case when (consumerprice - sellprice) <= 0 then 0 else (consumerprice - sellprice) end as saleprice from tblproduct) AS a  ";
$sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
								count(productcode) as marks_total_cnt
					FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$sql.= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";

$sql.= $strAddQuery." ";
$sql.= " ORDER BY a.start_no desc, a.pridx desc ";

$paging = new New_Templet_mobile_paging($sql,5,$listnum,'GoPage',true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
//exdebug($sql);
//exdebug($t_count);
$sql = $paging->getSql($sql);

$list_array = productlist_print ( $sql, $type = 'W_015', array (), $listnum );
?>

            <div class="goods-list">
                <div class="goods-list-item">
                    <!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
                    <ul>
                            <?
                            foreach ( $list_array as $listKey => $listVal ) {
                                echo $listVal;
                            }
                            ?>
                    </ul>
                </div>
            </div>
            <input type="hidden" id="t_count" value="<?=$t_count ?>" />
                    
            <!-- 페이징 -->
            <div class="list-paginate mt-10 mb-30">
                <?echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;?>
            </div>
            <!-- // 페이징 -->