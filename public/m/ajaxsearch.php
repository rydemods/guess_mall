<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$offset = $_POST['offsetLine']*3;
$dispaly = $_POST['dispaly']*3;

$code=$_REQUEST["code"];
$sort=$_REQUEST["sort"]?$_REQUEST["sort"]:"name";
$spe = ($_REQUEST["spe_code"]=="spe")?$_REQUEST["spe_code"]:"";
$s_check=$_REQUEST["s_check"]="all";
$search=mb_convert_encoding($_REQUEST["search"],"euc-kr","utf-8");

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

$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE code_a='".$codeA."' AND code_b='".$codeB."' ";
$sql.= "AND code_c='".$codeC."' AND code_d='".$codeD."' ";
$result=pmysql_query($sql,get_mdb_conn());

/*
if($row=pmysql_fetch_object($result)) {
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {
		Header("Location:index.php");
		exit;
	}
	if(strlen($_MShopInfo->getMemid())==0) {
		if(strlen($row->group_code)>0) {
			Header("Location:login.php?chUrl=".getUrl());
			exit;
		}
	} else {
		if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_MShopInfo->getMemgroup()) {
			header("Content-Type: text/html; charset=euc-kr");
			echo "<script>alert('해당 카테고리 접근권한이 없습니다.');document.location.href='index.php';</script>";exit;
		}
	}
	$_cdata=$row;
	$code_name=strip_tags($row->code_name);
} else {
	Header("Location:index.php");
	exit;
}
*/
pmysql_free_result($result);

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
while($row=pmysql_fetch_object($result)) {

	$tmpcode=$row->code_a;
	if($row->code_b!="000") $tmpcode.=$row->code_b;
	if($row->code_c!="000") $tmpcode.=$row->code_c;
	if($row->code_d!="000") $tmpcode.=$row->code_d;
	$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
}
pmysql_free_result($result);


$qry = "WHERE 1=1 and a.productcode not like '035%' ";
if($minprice>0) {
	$qry.= "AND a.sellprice >= {$minprice} ";
}
if($maxprice>0) {
	$qry.= "AND a.sellprice <= {$maxprice} ";
}
//검색조건 처리

//검색조건 처리
if(ord($s_check) && ord($search)) {
	$skeys = explode(" ",$search);
	@setlocale(LC_CTYPE , C);
	for($j=0;$j<count($skeys);$j++) {
		$skeys[$j]=strtoupper(trim($skeys[$j]));
		if(ord($skeys[$j])) {
			if($s_check=="keyword") {
				$qry.= "AND (UPPER(a.productname) LIKE '%{$skeys[$j]}%' OR UPPER(a.keyword) LIKE '%{$skeys[$j]}%') ";
			} else if($s_check=="code") {
				$qry.= "AND a.productcode LIKE '{$skeys[$j]}%' ";
			} else if($s_check=="production") {
				$qry.= "AND UPPER(a.production) LIKE '%{$skeys[$j]}%' ";
			} else if($s_check=="model") {
				$qry.= "AND UPPER(a.model) LIKE '%{$skeys[$j]}%' ";
			} else if($s_check=="selfcode") {
				$qry.= "AND UPPER(a.selfcode) LIKE '%{$skeys[$j]}%' ";
			} else if($s_check=="content") {
				$qry.= "AND UPPER(a.content) LIKE '%{$skeys[$j]}%' ";
			} else {
				//	$qry.= "AND (UPPER(a.productname) LIKE '%{$skeys[$j]}%' OR UPPER(a.keyword) LIKE '%{$skeys[$j]}%' OR a.productcode LIKE '{$skeys[$j]}%' OR UPPER(a.production) LIKE '%{$skeys[$j]}%' OR UPPER(a.model) LIKE '%{$skeys[$j]}%' OR UPPER(a.selfcode) LIKE '%{$skeys[$j]}%' OR UPPER(a.content) LIKE '%{$skeys[$j]}%') ";
				$qry.= "AND (UPPER(a.productname) LIKE '%{$skeys[$j]}%' OR UPPER(a.keyword) LIKE '%{$skeys[$j]}%' ) ";
			}
		}
	}
}
$qry.= "AND a.display!='N' ";

$category_name="";
$sql = "SELECT code_a as code, type, code_name FROM tblproductcode ";
$sql.= "WHERE group_code!='NO' ";
$sql.= "AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY cate_sort";
$result=pmysql_query($sql,get_mdb_conn());

while($row=pmysql_fetch_object($result)) {
	if($_cdata->code_a==$row->code) {

		$category_name=$row->code_name;
	}
}
pmysql_free_result($result);

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
	$sql.= "a.tinyimage, a.maximage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";

	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
	//elseif($tmp_sort[0]=="opendate") $sql.= "ORDER BY opendate DESC, date desc ";
	elseif($tmp_sort[0]=="dcprice") $sql.= "ORDER BY case when consumerprice>0 then  100 - cast((cast(sellprice as float)/cast(consumerprice as float))*100 as integer) else 0 end desc ";
	elseif($tmp_sort[0]=="best" || $tmp_sort[0]=="order" ){
			$bestsql="select COALESCE(sum(cnt),0) sumcnt, a.productcode from tblproduct a left join tblcounterproduct b on (a.productcode=b.productcode) where a.productname like '%".$search."%' and a.productcode not like '035%' group by a.productcode order by sumcnt desc LIMIT 100";
			$bestresult=pmysql_query($bestsql);
			
			$count=0;
			$count=0;
		$lk=1;
		$casewhen="";
		while($bestrow=pmysql_fetch_object($bestresult)){
			$productcode[$count]=$bestrow->productcode;
			$casewhen[]=" '".$bestrow->productcode."' then ".$lk;
			$count++;
			$lk++;
		}
		
		
		if(count($casewhen)>0) $sql.= "ORDER BY case a.productcode when ".implode(" when ",$casewhen)." end ";	
	}else{ $sql.= "ORDER BY a.productname ";}
	/*
	elseif($tmp_sort[0]=="order" ){
		$bestsql="select COALESCE(sum(cnt),0) sumcnt, a.productcode from tblproduct a left join tblcounterproduct b on (a.productcode=b.productcode) where a.productcode like '".$likecode."%' group by a.productcode order by sumcnt desc";
		$bestresult=pmysql_query($bestsql);
		
		$count=0;
		$lk=1;
		$casewhen="";
		while($bestrow=pmysql_fetch_object($bestresult)){
			$productcode[$count]=$bestrow->productcode;
			$casewhen[]=" '".$bestrow->productcode."' then ".$lk;
			$count++;
			$lk++;
		}
		
		$sql.= "ORDER BY a.start_no desc ";
		if(count($casewhen)>0) $sql.= " ,case a.productcode when ".implode(" when ",$casewhen)." end ";	
		//$prlist = implode("','",$productcode);
		//$sql.="ORDER BY FIELD(a.productcode,'{$prlist}') ";
	}else if($tmp_sort[0]=="best"){

		$sql.= "ORDER BY a.start_no desc ";
		if(count($lk_casewhen)>0) $sql.= " ,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";
	}else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
			} else {
				$sql.= "ORDER BY opendate DESC ";
				//$sql.= "ORDER BY a.start_no desc,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";
			}
		} elseif($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.start_no desc,a.productname ";
		} elseif($_cdata->sort=="production") {
			$sql.= "ORDER BY a.start_no desc,a.production ";
		} elseif($_cdata->sort=="price") {
			$sql.= "ORDER BY a.start_no desc,a.sellprice ";
		}
	}
*/
/*
	if($sort=="new") $sql.= "ORDER BY a.date DESC ";
	else if($sort=="low_price") $sql.= "ORDER BY a.sellprice ASC ";
	else if($sort=="high_price") $sql.= "ORDER BY a.sellprice DESC ";
	else if($sort=="product_name") $sql.= "ORDER BY a.productname ASC ";
	else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
			} else {
				$sql.= "ORDER BY a.date DESC ";
			}
		} else {
			$sql.= "ORDER BY a.date DESC ";
		}
	}
*/
	$sql.= "LIMIT ".$dispaly." OFFSET ".$offset;
	
	$result=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($result);
} else {
    $list_cnt = 0;
}
?>

	<?php if ($list_cnt) : ?>
		<?php while ($row = pmysql_fetch_object($result)) : ?>
		<?php
		if (!$row->maximage) {
			$row->maximage = $Dir."images/acimage.gif";
		} else {
			$row->maximage = $imagepath.$row->maximage;
		}
		$row->reserve=getReserveConvert($row->reserve,$row->reservetype,$row->sellprice,"Y");

		$r_cnt=0;
		$r_marks=0;
		$r_totscore=0;
		if($_data->review_type=="Y" || $_data->review_type=="A") {
			$sql = "SELECT COUNT(*) as r_cnt, SUM(marks) as r_marks FROM tblproductreview ";
			$sql.= "WHERE productcode='".$row->productcode."' ";
			if($_data->review_type=="A") $sql.= "AND display='Y' ";
			$sql.= "GROUP BY productcode ";
			$result2=pmysql_query($sql,get_mdb_conn());
			$row2=pmysql_fetch_object($result2);
			pmysql_free_result($result2);
			$r_cnt=(int)$row2->r_cnt;
			$r_marks=(int)$row2->r_marks;
			$r_totscore=0;

			if($r_cnt>0) {
				$r_totscore=ceil(($r_marks*20)/$r_cnt);
			}
		}
		?>

		<li><a href="productdetail.php?pridx=<?=$row->pridx?>"><div class="thumb">
		<div><img src="<?=$row->maximage?>" alt="" /></div>
		<span class="name"><?=strip_tags($row->productname)?></span> <span class="price"><?=number_format($row->sellprice);?>원</span>
		</div></a></li>

		
		<?php endwhile; ?>
	<?php endif; ?>