<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
$a_poption=$_REQUEST['sort_word']; //productlist에서 받아온 정렬문
//exdebug($a_poption);
/*
switch($a_poption){
	case "new" : $sort_word2= " ORDER BY selldate DESC "; break;
	case "favorite" : $sort_word2 = " ORDER BY sellcount DESC "; break;
	case "highprice" : $sort_word2 = " ORDER BY sellprice DESC "; break;
	case "lowprice" : $sort_word2 = " ORDER BY sellprice "; break;
}
*/
$list_type=$_POST['list_type'];
$offset = $_POST['offsetLine']*4;
$dispaly = $_POST['dispaly']*4;
//echo $dispaly;
$code=$_REQUEST["code"];
//$sort=$_REQUEST["sort"];
//$spe = ($_REQUEST["spe_code"]=="spe")?$_REQUEST["spe_code"]:"";



$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$code=$codeA.$codeB.$codeC.$codeD;

$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;


$sql = "SELECT code_a, code_b, code_c, code_d FROM tblproductcode ";

if(strlen($_MShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
	//$sql.= "WHERE group_code='' ";
} else {
	$sql.= "WHERE group_code!='".$_MShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
	//$sql.= "WHERE group_code='".$_MShopInfo->getMemgroup()."' or group_code='ALL' or group_code='' ";
}

$result=pmysql_query($sql,get_mdb_conn());


$not_qry="";
$lineCnt=0;
while($row=pmysql_fetch_object($result)) {

	$tmpcode=$row->code_a;
	if($row->code_b!="000") $tmpcode.=$row->code_b;
	if($row->code_c!="000") $tmpcode.=$row->code_c;
	if($row->code_d!="000") $tmpcode.=$row->code_d;
	$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
}
pmysql_free_result($result);


$qry = "WHERE 1=1 ";
if(strstr($_cdata->type,"T")) {	//가상분류
	$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
	if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
		$sql.= "ORDER BY date DESC ";
	}
	$result=pmysql_query($sql,get_mdb_conn());
	$t_prcode="";
	while($row=pmysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	pmysql_free_result($result);
	$t_prcode=substr($t_prcode,0,-1);
	$t_prcode=str_replace(',','\',\'',$t_prcode);
	$qry.= "AND a.productcode IN ('".$t_prcode."') ";

	$add_query="&code=".$code;
} else {	//일반분류
	$link_qry="select c_productcode, c_date from tblproductlink where c_category like '{$likecode}%' group by c_productcode, c_date order by c_date desc";
	$link_result=pmysql_query($link_qry);
	$lik=1;
	$lk_casewhen="";
	while($link_data=pmysql_fetch_object($link_result)){
		$linkcode[]=$link_data->c_productcode;
		$lk_casewhen[]=" '".$link_data->c_productcode."' then ".$lik;
		$lik++;
	}

	$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";
}
$qry.="AND a.display='Y' ";

$category_name="";
$_cateselectlist ="<select name=\"type\">\n";
$sql = "SELECT code_a as code, type, code_name FROM tblproductcode ";
$sql.= "WHERE group_code!='NO' ";
$sql.= "AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY cate_sort";
$result=pmysql_query($sql,get_mdb_conn());

while($row=pmysql_fetch_object($result)) {
	if($_cdata->code_a==$row->code) {

		$category_name=$row->code_name;
	}
	$_cateselectlist.="<option value=\"productlist.php?code=".$row->code."\" ".($_cdata->code_a==$row->code?"selected":"")." class=\"{ code: '".$row->code."' }\">".strip_tags($row->code_name)."</option>\n";
}
pmysql_free_result($result);
$_cateselectlist.="</select>\n";

$sql = "SELECT COUNT(*) as t_count FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= $qry." ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
if(strlen($not_qry)>0) {
	$sql.= $not_qry." ";
}
$result=pmysql_query($sql,get_mdb_conn());
$row=pmysql_fetch_object($result);
$t_count = (int)$row->t_count;
pmysql_free_result($result);

if ($t_count>0) {

	$tmp_sort=explode("_",$sort);
	if($tmp_sort[0]=="reserve") {
			$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
		}
	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, ";
	if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
	$sql.= "a.tinyimage, a.maximage, a.etctype, a.mdcomment, a.option_price, a.consumerprice, a.tag, a.selfcode, ";
	$sql.= " c.sellprice as group_sellprice, c.consumerprice as group_consumerprice  ";
	//$sql.= "FROM tblproduct AS a ";
	$sql.= "FROM view_tblproduct AS a ";
	$sql.= "JOIN (select c_productcode as c_productcode from tblproductlink group by c_productcode) AS link ON a.productcode=link.c_productcode ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price where group_code = '{$_ShopInfo->memgroup}') c ON a.productcode = c.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= " AND staff_product != '1' ";
	$sql.=  $a_poption;
	//$sql.= " ORDER BY sellprice desc";
	$sql.= " LIMIT ".$dispaly." OFFSET ".$offset;
	//exdebug($sql);
	$result=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($result);
} else {
    $list_cnt = 0;
}

pmysql_free_result($deli_result);
?>

	<?php if ($list_cnt) : ?>
	<ul>
<?		while ($row = pmysql_fetch_object($result)) : 
		
			##### 쿠폰에 의한 가격 할인
			$cou_data = couponDisPrice($row->productcode);
			if($cou_data['coumoney']){
				$nomalprice=$row->sellprice;
				$row->sellprice = $row->sellprice-$cou_data['coumoney'];
			}
			
			##### 쿠폰에 의한 가격 할인

			##### 오늘의 특가, 타임세일에 의한 가격
			$spesell = getSpeDcPrice($row->productcode);
			if($spesell){
				$nomalprice=$row->sellprice;
				$row->sellprice = $spesell;
			}
			##### //오늘의 특가, 타임세일에 의한 가격

			$dc_rate = getDcRate($row->consumerprice,$row->sellprice);
			$dc_type = $cou_data["goods_sale_type"];
			$image = getMaxImageForXn($row->productcode);

	?>

			<li>
				<div class="goods_wrap">
					<a href="productdetail.php?pridx=<?=$row->pridx?>">
					<img src="<?=$image?>"  alt="" />
					<div class="infobox">
						<span class="name"><?=$row->productname?></span>
						<div class="pricebox">
							<strong>
<?php 						if($row->consumerprice){ ?>
								<del><?=number_format($row->consumerprice)?></del><br>
<?php 						} ?>
								<?=number_format($row->sellprice)?>
							
							</strong>
						</div>
					</div>
					</a>
				</div>

			</li>


		<?php endwhile; ?>
		</ul>
	<?php endif; ?>
