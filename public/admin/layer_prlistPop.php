
<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");


$sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname
        FROM    tblvenderinfo a
        JOIN    tblproductbrand b ON a.vender = b.vender
        ORDER BY lower(b.brandname) ASC";
$ret_brand=pmysql_query($sql,get_db_conn());
while($row_brand=pmysql_fetch_object($ret_brand)) {
	$venderlist[$row_brand->vender]=$row_brand;
}
pmysql_free_result($ret_brand);
?>
<input type="hidden" name="prlistMode" id="prlistMode" value=""/>
<input type="hidden" name="box_no" id="box_no" value="<?=$box_no?>"/>
<div id='layer_product_sel_box' class="layer">
	<div class="bg"></div>
	<div id="layer_product_sel" class="pop-layer">
		<div class="btn-r">
			<a href="#" class="cbtn">Close</a>
		</div>
		<div class="pop-container">
			<div class="pop-conts">
				<!--content //-->
				<p class="ctxt mb20" style="font-size:15px; font-weight: 700;">상품 선택<br>
					<div style="margin-bottom:15px;">
                        
						<select name="sel_vender" id="sel_vender" onChange="javascript:resetBrandSearchWord(this);">
							<option value="">========== 브랜드 선택============</option>
<?php
	foreach($venderlist as $key => $val) {
			echo "\t\t\t\t\t\t\t<option value=\"{$val->bridx}\"";
			if($sel_vender==$val->bridx) echo " selected";
			echo ">{$val->brandname}</option>\n";
	}
?>
						</select>
						<input type="text" name="s_keyword" id="s_keyword" value="" style="width: 250px;"/>
					</div>
					<?=T_codeListScript()?>
                    <div style="margin-top:10px;">
                    상품명 검색 : <input type="text" name="s_prod_keyword" id="s_prod_keyword" value="" style="width: 250px;"/> <a href="javascript:T_productListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
                    </div>
				</p>
				<div id="productList">
				</div>
				<!--// content-->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    function resetBrandSearchWord(obj) {
        if ( $(obj).val() == "" ) {
            $("#s_keyword").attr("disabled", false).val("").focus();
        } else {
            $("#s_keyword").attr("disabled", true);
        }
    }
</script>
