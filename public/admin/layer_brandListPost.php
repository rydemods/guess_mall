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

$s_keyword          = $_POST['s_keyword'];
$s_keyword_lower    = strtolower($s_keyword);


$sql  = "SELECT *, tvia.s_img ";
$sql .= "FROM tblproductbrand tpb LEFT JOIN tblvenderinfo_add tvia ON tpb.vender = tvia.vender ";
$sql .= "WHERE tpb.display_yn = 1 ";
if ( !empty($s_keyword) ) {
    $sql .= "AND lower(tpb.brandname) like '%{$s_keyword_lower}%' OR lower(tpb.brandname2) like '%{$s_keyword_lower}%' ";
}
$sql .= "ORDER BY lower(tpb.brandname) asc ";

if(!$listnum){
	$listnum = 20;
}

$sql0 = "SELECT COUNT(*) as t_count FROM (".$sql.") a  WHERE 1=1 ";

$paging = new newPaging($sql0,10,$listnum,$link='T_Brand_GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);

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
		</colgroup>
		<tr>
			<th>No</th>
			<th>이미지</th>
			<th width="400">브랜드명</th>
		</tr>
<?
	$page_numberic_type=1;
	foreach($repProduct as $repPrKey=>$row){
		$number = ($t_count-(20 * ($gotopage-1))-$cnt);
        $s_img  = getProductImage($Dir.DataDir.'shopimages/vender/', $row->s_img);
?>
		<tr>
			<td><?=$number?></td>
			<!--이미지-->
			<td>
			<input type="hidden" name="product_code" value="<?=$row->productcode?>">
			<img src="<?=$s_img?>" style="width:60px" border=1></a>
			</td>
			<!--상품명-->
			<td height="50">
				<p>
                    <a href="javascript:T_onBrandcode('<?=addslashes($row->brandname)?>','<?=$row->bridx?>','<?=$s_img?>');">
						<?=$row->brandname?>
					</a>
				</p>
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
