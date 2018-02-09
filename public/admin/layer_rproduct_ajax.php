<?php
/********************************************************************* 
// 파 일 명		: layer_prlistPost.php
// 설     명		: 데코앤이 카테고리별 상품검색 및 선택시 추가
// 상세설명	: 데코앤이 카테고리별 상품검색 및 선택시 추가
// 작 성 자		: 2016.01.18 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$code_a         = $_POST['code_a'];
$code_b         = $_POST['code_b'];
$code_c         = $_POST['code_c'];
$code_d         = $_POST['code_d'];
$sel_vender     = $_POST['sel_vender'];
$block          = $_POST['block'];
$gotopage       = $_POST['gotopage'];
$prlistMode     = $_POST['prlistMode'];
$s_keyword      = $_POST['s_keyword'];
$s_prod_keyword = $_POST['s_prod_keyword'];
$box_no = $_POST['box_no'];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;
$imagepath = $Dir.DataDir."shopimages/product/";

$qry = "";
if ($likecode){
	$qry.= "AND b.c_category LIKE '{$likecode}%' ";
}

if($sel_vender) { 
    // 브랜드를 선택한 경우
    $qry.= "AND a.brand = {$sel_vender} ";
} else {
    // 특정브랜드를 선택하지 않은 경우
    // 검색어에 해당하는 브랜드 리스트를 구한다.

    if ($s_keyword) {
        $arrVender = array();

        $subqry  = "SELECT bridx FROM tblproductbrand ";
        $subqry .= "WHERE lower(brandname) like '%".strtolower($s_keyword)."%' OR lower(brandname2) like '%".strtolower($s_keyword)."%' ";
        $subresult = pmysql_query($subqry);
        while ( $subrow = pmysql_fetch_object($subresult) ) {
            array_push($arrVender, $subrow->bridx);
        }
        pmysql_free_result($subresult);
//        echo $subqry . "<br>";

        if ( count($arrVender) > 0 ) {
            $qry.= "AND a.brand in ( " . implode(",", $arrVender) . " ) ";
        }
    }
}

if ($s_prod_keyword) $qry.= "AND lower(productname || productcode) LIKE lower('%{$s_prod_keyword}%') ";

## jhjeong 2015-06-11
$sql = "select distinct on (productcode,regdate, pridx) * 
		from
		(
			SELECT	option_price,productcode,productname,production,sellprice,consumerprice, vip_product, option_quantity, option1, staff_product, 
					buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, modifydate,sabangnet_flag, sewon_option_no, sabangnet_prop_val,model, regdate, pridx
			FROM	tblproduct a 
			left join tblproductlink b on(a.productcode=b.c_productcode) 
			WHERE 1=1 
			".$qry."
		) v
		";
## jhjeong 2015-06-11
$sql0 = "SELECT COUNT(*) as t_count FROM (".$sql.") a  WHERE 1=1 ";
if(!$listnum){
	$listnum = 20;
}
$paging = new newPaging($sql0,10,$listnum,$link='T_GoPage',$block,$gotopage);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql.= "ORDER BY regdate DESC, pridx ASC ";
$sql = $paging->getSql($sql);
//echo $sql . "<br/>";

$result = pmysql_query($sql,get_db_conn());
$cnt=0;
while($row=pmysql_fetch_object($result)) {
	$repProduct[] = $row;
}
pmysql_free_result($result);

?>
<div class="table_style02">
	<table width=100% cellpadding=0 cellspacing=0>
		<colgroup>
			<col width="50" />
			<col width="80" />
			<col width="" />
			<!-- <col width="120" /> -->
			<col width="200" />
			<col width="200" />
			<!-- <col width="80" /> -->
			<!-- <col width="80" /> -->
			<col width="80" />
		</colgroup>
		<tr>
			<th>No</th>
			<th>이미지</th>
			<th width="400">상품명</th>
			<!-- <th>등록일</th> -->
			<th>시중가</th>
			<th>판매가</th>
			<!-- <th>상태</th> -->
			<!-- <th>재고</th> -->
			<th>진열유무</th>
		</tr>
<?
	$page_numberic_type=1;
	foreach($repProduct as $repPrKey=>$row){
		$number = ($t_count-(20 * ($gotopage-1))-$cnt);
		//$row->productname = iconv("EUC-KR","UTF-8",$row->productname);

        $tinyimage = getProductImage($imagepath, $row->tinyimage );
?>
		<tr>
			<td><?=$number?></td>
			<!--이미지-->
			<td>
			<input type="hidden" name="product_code" value="<?=$row->productcode?>">
		<!-- <?	if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){ ?>
			<a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
			<img src="<?=$imagepath.$row->tinyimage."?v".date("His")?>" style="width:70px" border=1></a>
		<?} else if($tinyimage) { ?>
			<a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
			<img src="<?=$tinyimage."?v".date("His")?>" style="width:70px" border=1></a>
		<?} else { ?>
			<img src=images/space01.gif>
		<?} ?> -->
        <?	if($tinyimage) { ?>
			<a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
			<img src="<?=$tinyimage."?v".date("His")?>" style="width:60px" border=1></a>
		<?} ?>
			</td>
			<!--상품명-->
			<td height="50">
				<p class="ta_l" >
				<?if($row->vip_product == 1){ ?>
					<img src="img/icon/icon_vip.gif" border="0" style="margin-right:2px;">
				<?}?>
				<?if($row->staff_product == "1"){ ?>
					<img src="img/icon/icon_staff.gif" border="0" style="margin-right:2px;">
				<?}?>				
					<!-- <a href="javascript:T_onProductcode('<?=$prlistMode?>','<?=$row->productname?>','<?=$row->productcode?>','<?=$imagepath.$row->tinyimage?>');">
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>
					</a> -->
				<?if($box_no){?>
					&nbsp;&nbsp;<a href="javascript:T_onProductcode_pop('<?=$prlistMode?>','<?=addslashes($row->productname)?>','<?=$row->productcode?>','<?=$tinyimage?>','<?=$box_no?>');">
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>
				<?}else{?>
                    &nbsp;&nbsp;<a href="javascript:T_onProductcode('<?=$prlistMode?>','<?=addslashes($row->productname)?>','<?=$row->productcode?>','<?=$tinyimage?>');">
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?><?=$box_no?>
				<?}?>
					</a>
				</p>
			</td>
			<!--등록일-->
			<!-- <td><?=substr($row->modifydate,0,10)?></td> -->
			<!--판매가-->
			<td style="text-align:right; padding-right:10px">기본가 : <img src="images/won_icon.gif" border="0" style="margin-right:2px;">
				<span class="font_orange"><?=number_format($row->consumerprice)?></span><br>
			</td>
			<!--시중가-->
			<td style="text-align:right; padding-right:10px">
				기본가 : <img src="images/won_icon.gif" border="0" style="margin-right:2px;">
			<span class="font_orange"><?=number_format($row->sellprice)?></span><br>
			</td>
			<!--상태-->
			<!-- <td><?=($row->quantity=="0"?"품절":"재고")?></td> -->
			<!--재고-->
			<!-- <TD>
			<?if ($row->quantity=="0")
				{ echo "품절";
			}else if($row->quantity=="") {
				echo "무제한";
			}else echo $row->quantity;
			?>
			</td> -->
			<td>
				<?if($row->display=="Y") echo "판매중";?>
				<?if($row->display=="N") echo "보류중";?>
			</td>
		</tr>
<?
		$cnt++;
	}
	if ($cnt==0) {
		$colspan='9';
		$page_numberic_type="";
		echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
	}
?>
	</table>
</div>
<div id="page_navi01" style="height:'40px'">
	<div class="page_navi">
	<?if($page_numberic_type){?>
		<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
	<?}?>
	</div>
</div>
