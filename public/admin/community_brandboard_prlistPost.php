<?php
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
$code_a = $_POST['code_a'];
$code_b = $_POST['code_b'];
$code_c = $_POST['code_c'];
$code_d = $_POST['code_d'];
$block = $_POST['block'];
$gotopage = $_POST['gotopage'];
$listMode = $_POST['listMode'];
$s_keyword = $_POST['s_keyword'];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;
$imagepath = $Dir.DataDir."shopimages/product/";
if($listMode == 'repProduct' || $listMode == 'relationProduct'){
	if ($likecode){
		$qry= "AND b.c_category LIKE '{$likecode}%' ";
	}
	if ($s_keyword) $qry.= "AND lower(productname || productcode) LIKE lower('%{$s_keyword}%') ";
	
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
	$paging = new newPaging($sql0,10,$listnum,$link='GoPage',$block,$gotopage);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	$sql.= "ORDER BY regdate DESC, pridx ASC ";
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=pmysql_fetch_object($result)) {
		$repProduct[] = $row;
	}
	pmysql_free_result($result);
}
?>
<div class="table_style02">
	<table width=100% cellpadding=0 cellspacing=0>
		<colgroup>
			<col width="50" />
			<col width="50" />
			<col width="80" />
			<col width="" />
			<col width="120" />
			<col width="200" />
			<col width="200" />
			<col width="80" />
			<col width="80" />
			<col width="80" />
		</colgroup>
		<tr>
			<th><input type="checkbox" name="check-all" class="check-all"></th>
			<th>No</th>
			<th>이미지</th>
			<th width="300">상품명</th>
			<th>등록일</th>
			<th>시중가</th>
			<th>판매가</th>
			<th>상태</th>
			<th>재고</th>
			<th>진열유무</th>
		</tr>
<?
	$page_numberic_type=1;
	foreach($repProduct as $repPrKey=>$row){
		$number = ($t_count-(20 * ($gotopage-1))-$cnt);
		//$row->productname = iconv("EUC-KR","UTF-8",$row->productname)
?>
		<tr>
			<td><input type="checkbox" name="code_check"  value="<?=$row->productcode?>" class="code_check"></td>
			<td><?=$number?></td>
			<!--이미지-->
			<td>
			<input type="hidden" name="product_code" value="<?=$row->productcode?>">
		<?	if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){ ?>
			<a href="/front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank">
			<img src="<?=$imagepath.$row->tinyimage."?v".date("His")?>" style="width:100px" border=1></a>
		<?} else { ?>
			<img src=images/space01.gif>
		<?} ?>
			</td>
			<!--상품명-->
			<td height="50">
				<p class="ta_l" style="text-align:center">
				<?if($row->vip_product == 1){ ?>
					<img src="img/icon/icon_vip.gif" border="0" style="margin-right:2px;">
				<?}?>
				<?if($row->staff_product == "1"){ ?>
					<img src="img/icon/icon_staff.gif" border="0" style="margin-right:2px;">
				<?}?>
				
				<?if($listMode == 'relationProduct'){?>
					<a href="javascript:relationProducts('<?=$row->productname?>','<?=$row->productcode?>','<?=$imagepath.$row->tinyimage?>');">
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>
					</a>
				<?}else{?>
					<a href="javascript:onProductcode('<?=$row->productname?>','<?=$row->productcode?>','<?=$imagepath.$row->tinyimage?>');">
						<?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>
					</a>
				<?}?>
				</p>
			</td>
			<!--등록일-->
			<td><?=substr($row->modifydate,0,10)?></td>
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
			<td><?=($row->quantity=="0"?"품절":"재고")?></td>
			<!--재고-->
			<TD>
			<?if ($row->quantity=="0")
				{ echo "품절";
			}else if($row->quantity=="") {
				echo "무제한";
			}else echo $row->quantity;
			?>
			</td>
			<td>
				<?if($row->display=="Y") echo "판매중";?>
				<?if($row->display=="N") echo "보류중";?>
			</td>
		</tr>
<?
		$cnt++;
	}
	if ($cnt==0) {
		$colspan='10';
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