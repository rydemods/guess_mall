<?
$listnum = $_REQUEST ['listnum'] ?: "10";
$imagepath = $Dir . DataDir . "shopimages/product/";


$sort = "recent";
$soldout = $_REQUEST ['soldout'];
$brand = $_REQUEST['brand'];

// 조건
$qry = "WHERE 1=1 AND a.display = 'Y' AND a.hotdealyn='N' ";

//브랜드별 검색
$arrBrand = explode(",",$brand);
if(!empty($brand)){
	foreach($arrBrand as $i => $v){
		$checked ['brand'] [$v] = "checked";
		if($i == 0){
			$qry.= " AND (a.brand = '".$v."'";
		}else{
			$qry.= " OR a.brand = '".$v."'";
		}
	}
	$qry.=")";
}

// 품절상품제외 2016-10-10
if($soldout == "1") {
	$qry.= " AND a.quantity > 0 ";
}

// 상품리스트
$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.color_code, ";
$sql.= "a.maximage, a.minimage,a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, a.brand, a.icon, a.soldout, a.prodcode, a.colorcode, a.sizecd, COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
$sql.= "TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt ";
$sql.= "FROM (select *, case when (consumerprice - sellprice) <= 0 then 0 else (consumerprice - sellprice) end as saleprice from tblproduct) AS a  ";
$sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
								count(productcode) as marks_total_cnt
					FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '" . $_ShopInfo->getMemid () . "' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";

$sql .= $qry . " ";

$sql.= " ORDER BY a.modifydate desc, a.pridx desc ";

$paging = new New_Templet_mobile_paging ( $sql, 5, $listnum, 'GoPage', true );
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql ( $sql );
// exdebug($sql);

$list_array = productlist_print ( $sql, $type = 'W_015', array (), $listnum );
?>
<main id="content" class="subpage">
    <section>
        <h2 class="page_local">
            <a href="<?=$Dir.MDir ?>" class="prev"></a>
            <span>NEW</span>
        </h2>
    </section>
    <section class="goods_list_wrap">
        <div class="inner">
            <!-- 상품검색정렬 -->
            <div class="list_sort mt-10">
                <ul>
                    <li>
                        <div><input type="checkbox" id="sold-out" name="sold-out" class="chk_agree checkbox_custom" value="" > <label for="sold-out">품절상품제외</label></div>
                    </li>
                    <li><a href="#" class="btn-brand-search">브랜드</a></li>
                </ul>
            </div>
            <!-- // 상품검색정렬 -->
        </div>
        <!-- // .inner -->

        <!-- 상품 리스트 영역 -->
        <div class="product-list">
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
            <!-- // 상품 리스트 영역 -->

            <!-- 페이징 -->
            <div class="list-paginate mt-10 mb-30">
                <?echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;?>
            </div>
            <!-- // 페이징 -->
        </div>

        <!-- 브랜드 검색 팝업 -->
        <div class="layer-dimm-wrap pop-brand-search">
            <div class="dimm-bg"></div>
            <div class="layer-inner">
                <h3 class="layer-title">브랜드</h3>
                <button type="button" class="btn-close">창 닫기 버튼</button>
                <div class="layer-content">
                    <div class="sorting">
                        <section class="brand-wrap">
                            <ul>
                                <li>
                                    <div>
                                        <label for="brand_check01">브랜드 전체</label> <input
                                            type="checkbox" id="brandCheckAll" name="brandCheckAll"
                                            class="checkbox_custom">
                                    </div>
                                </li>
                                <?
                                $t_getBrandList = getAllBrandList ();
                                $brandAllCnt = count($t_getBrandList);
                                foreach ( $t_getBrandList as $t_brandKey => $t_brandVal ) {
                                    ?>
                                <li>
                                    <div>
                                        <label for="brand_check01"><?=$t_brandVal->brandname?></label> <input
                                            type="checkbox" class="CLS_brandchk checkbox_custom" name="brandchk"
                                            id="<?=$t_brandVal->bridx?>" ids="<?=$t_brandVal->bridx?>" <?=$checked ['brand'] [$t_brandVal->bridx] ?>>

                                    </div>
                                </li>
                            <?}?>

                            </ul>
                        </section>

                        <div class="btn-wrap">
                            <button class="btn-submit" type="submit">
                                <span>적용하기</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- // 브랜드 검색 팝업 -->

        <form name="formSearch" id="formSearch" method="GET"
            action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return submitForm(this);">
            <input type=hidden name=block value="<?=$block?>">
            <input type=hidden	name=gotopage value="<?=$gotopage?>">
            <input type=hidden name=listnum	value="<?=$listnum?>">
            <input type=hidden name=brand	value="<?=$brand?>">
            <input type=hidden name=addwhere value="<?=$strAddQuery?>">
            <input type=hidden name="s_search_brand[]" id="s_search_brand" value="<?=$searchBrand[0]?>">
            <input type="hidden" name="thr"	value="sw" />
            <input type="hidden" name="sort" id="sort" value="<?=$sort?>">
            <input type="hidden" name="soldout" id="soldout" value="<?=$soldout ?>" />
        </form>
    </section>
</main>


<script type="text/javascript">
function GoPage(block,gotopage) {
	document.formSearch.block.value=block;
	document.formSearch.gotopage.value=gotopage;
	document.formSearch.submit();
}

$(document).ready(function() {
	oParams = getUrlParams();
	var brandCode = [];
	var selectBrand = "<?=$brand?>";
	var soldout = "<?=$soldout ?>";
	var brandChecked = $('input[name=brandchk]:checkbox:checked').length;
	var brandAllCnt = "<?=$brandAllCnt ?>";

	if(soldout == "1"){
		$("#sold-out").prop("checked", true);
	}

	//전체선택 체크
	if(brandChecked == brandAllCnt){
		$("#brandCheckAll").prop("checked",true);
	}

    //브랜드 전체 클릭
    $("#brandCheckAll").click(function(){
    if($("input[name=brandchk]").prop("checked")){
        $("input[name=brandchk]").prop("checked",false);
    }else{
        $("input[name=brandchk]").prop("checked",true);
    }
    });

	//조건 적용
	$(".btn-submit").click(function() {
		//브랜드 value
		if(brandCode != ""){
			//배열에 code가 있는 경우 삭제
			var codeSize = brandCode.length;
			brandCode.splice(0,codeSize);
		}

		if($("input[name=brandchk]:checked").val() == "undefined"){
			brandCode.push("");
			$("input[name=brand]").val("");
		}else{
			$("input[name=brandchk]:checked").each(function() {
				brandCode.push($(this).attr("ids"));
				$("input[name=brand]").val(brandCode);
			});
		}

		//var param = {"code":oParams.code, "color":color_name, "sort":sort, "brand":brandCode, "size":sizeCode};
        var param = {"brand":brandCode, "soldout":soldout};
		$.ajax({
			type: "POST",
			url: "../m/ajax_productnew_search.php",
			data: param,
			dataType:"HTML",
		    error:function(request,status,error){
		       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		    }
		}).done(function(html){
			$(".product-list").html(html);
			$(".btn-close").trigger("click");
		});

	});

	//품정상품 제외
	$("#sold-out").change(function() {
	    var soldout = "";
	    if($("#sold-out").prop('checked')) soldout = "1";
	    else soldout = "0";
	    document.formSearch.soldout.value = soldout;
		document.formSearch.block.value="";
		document.formSearch.gotopage.value="";
		document.formSearch.submit();

	});
});
</script>

<?php include_once('./outline/footer_m.php'); ?>
