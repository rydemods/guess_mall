
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

<div id='layer_brand_sel_box' class="layer">
	<div class="bg"></div>
	<div id="layer_brand_sel" class="pop-layer">
		<div class="btn-r">
			<a href="#" class="cbtn">Close</a>
		</div>
		<div class="pop-container">
			<div class="pop-conts">
				<!--content //-->
				<p class="ctxt mb20" style="font-size:15px; font-weight: 700;">브랜드 선택<br>
					<div style="margin-bottom:15px;">
					</div>
                    <div style="margin-top:10px;">
                    브랜드명 검색 : <input type="text" name="s_brand_keyword" id="s_brand_keyword" value="" style="width: 250px;"/> <a href="javascript:T_brandListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
                    </div>
				</p>
				<div id="brandList">
				</div>
				<!--// content-->
			</div>
		</div>
	</div>
</div>
