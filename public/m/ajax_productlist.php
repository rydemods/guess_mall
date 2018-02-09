<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$a_poption=$_REQUEST['sort_word']; //productlist에서 받아온 정렬문
$list_type=$_REQUEST['list_type'];
$offset = $_REQUEST['offsetLine']*2;
$display = $_REQUEST['display']*2;
$code=$_REQUEST["code"];

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

################그룹코드로 하는것 같은데 쓰지는 않음(2015.12.11 - 김재수 확인)#####################
$sql = "SELECT code_a, code_b, code_c, code_d FROM tblproductcode ";

if(strlen($_MShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	$sql.= "WHERE group_code!='".$_MShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
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
################그룹코드로 하는것 같은데 쓰지는 않음(2015.12.11 - 김재수 확인)#####################

$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE code_a='".$codeA."' AND code_b='".$codeB."' ";
$sql.= "AND code_c='".$codeC."' AND code_d='".$codeD."' ";
$result=pmysql_query($sql,get_mdb_conn());

if($row=pmysql_fetch_object($result)) {
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {
		exit;
	}
	if(strlen($_MShopInfo->getMemid())==0) {
		if(strlen($row->group_code)>0) {
			exit;
		}
	} else {
		if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_MShopInfo->getMemgroup()) {
			exit;
		}
	}
	$_cdata=$row;
} else {
	exit;
}
//exdebug($_cdata);
pmysql_free_result($result);



## 가상분류 -> 일반분류 교체 ( 2015 10 28 유동혁 )
## 해외직구 상품 (카테고리 003) 추가 overseas_type -> 1 해외직구, 0 일반상품
if( $code_a == '003' ){
	$qry = 'WHERE a.overseas_type = 1 ';
} else {
	$qry = "WHERE link.c_category LIKE '".$likecode."%' AND a.overseas_type = 0 ";
}
$qry.="AND (  a.mall_type = 0 OR a.mall_type = '".$_MShopInfo->getAffiliateType()."' ) "; // 해당 몰관련 상품만 보여줌 (2015.11.10 - 김재수)

$qry.="AND a.display='Y' ";

//아이템별 검색
$item_cate = $_REQUEST['item_cate'];
if($item_cate){
	$qry.="AND a.itemcate={$item_cate} ";
}
//브랜드별 검색
$brand = $_REQUEST['brand'];

if($brand){
	$sql_brand = "SELECT c_productcode FROM tblproductlink ";
	$sql_brand.= "WHERE c_category like '{$brand}%'";
	$qry.="AND a.productcode in ({$sql_brand}) ";
	}
//검색어
if($likestr){
	$qry.="AND a.productname LIKE '%{$likestr}%' ";
}

if($_data->ETCTYPE["CODEYES"]!="N") {
	$cateList_sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode WHERE code_a='{$code_a}' AND code_b!='000' AND group_code!='NO' ORDER BY cate_sort ASC";
	$cateList_res = pmysql_query($cateList_sql , get_mdb_conn());
	while($cateList_row = pmysql_fetch_array($cateList_res)){
		$cateList[$cateList_row[code_b]][] = $cateList_row;
	}
	pmysql_free_result($cateList_res);
}

if($_cdata->islist=="Y"){
	//상품 리스트 불러오기 시작
	$sql = "SELECT DISTINCT(a.productcode) AS dis, * FROM tblproduct AS a ";
	$sql.= "JOIN tblproductlink link on(a.productcode=link.c_productcode AND c_maincate=1) ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";

	if($likeCate){
		$sql.=$likeCate;
	} else {
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') AND staff_product != '1' ";
	}
	if($likeBrand){
		$sql.=$likeBrand;
	}
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	//번호, 사진, 상품명, 제조사, 가격
	$tmp_sort=explode("_",$a_poption);
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}

	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, ";
	if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
	$sql.= "a.maximage, a.minimage,a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
	$sql.= $addsortsql;

	$sql.= "FROM (select *, case when (buyprice - sellprice) <= 0 then 0 else (buyprice - sellprice) end as saleprice from tblproduct) AS a  ";
	$sql.= "JOIN tblproductlink link on(a.productcode=link.c_productcode AND c_maincate=1) ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";

	$sql.= $qry." ";

	if($likeCate){
		$sql.=$likeCate;
	}
	if($likeBrand){
		$sql.=$likeBrand;
	}
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="rcnt") $sql.= "ORDER BY a.review_cnt ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="saleprice") $sql.= "ORDER BY a.saleprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	elseif($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
	// 등록일순으로 변경함. 만약 수정일순으로 변경한다면 modifydate desc 로 변경..2015-11-30 jhjeong
	elseif($tmp_sort[0]=="opendate") $sql.= "ORDER BY a.regdate DESC, pridx ASC ";
	elseif($tmp_sort[0]=="dcprice") $sql.= "ORDER BY case when consumerprice>0 then  100 - cast((cast(sellprice as float)/cast(consumerprice as float))*100 as integer) else 0 end desc ";
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
		$sql.= "ORDER BY a.start_no asc, a.date desc, modifydate desc";
		//$sql.= "ORDER BY a.start_no asc";
	}else if($tmp_sort[0]=="best"){

		$sql.= "ORDER BY a.start_no desc ";
		if(count($lk_casewhen)>0) $sql.= " ,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";
	}else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(strstr($_cdata->type,"T") && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
			} else {
				$sql.= "ORDER BY opendate DESC ";
			}
		} elseif($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.start_no desc,a.productname ";
		} elseif($_cdata->sort=="production") {
			$sql.= "ORDER BY a.start_no desc,a.production ";
		} elseif($_cdata->sort=="price") {
			$sql.= "ORDER BY a.start_no desc,a.sellprice ";
		}
	}

	$sql.= " LIMIT ".$display." OFFSET ".$offset;

	$result=pmysql_query($sql,get_mdb_conn());
	$list_cnt = pmysql_num_rows($result);

?>
	
	<?php if ($list_cnt) : ?>
	<ul>
		<?php while ($row = pmysql_fetch_object($result)) : ?>
<?php
		$pro_icon			= "";
		$pro_url				= "";
		$pro_img			= "";
		$pro_name			= "";
		$pro_comment	= "";
		$pro_otype			= "";
		$pro_bprice		= "";
		$pro_cprice		= "";
		$pro_sprice		= "";


		$sellprice = number_format($row->sellprice);
		$consumerprice = number_format($row->consumerprice);
		$temp = $row->option1;
		$tok = explode(",",$temp);
		$goods_count=count($tok);

		$check_optea='0';
		if($goods_count>"1"){
			$check_optea="1";
		}

		$optioncnt = explode(",",ltrim($row->option_quantity,','));
		$check_optout=array();
		$check_optin=array();
		for($gi=1;$gi<$goods_count;$gi++) {

			if(strlen($row->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
			else{  $check_optin[]='1';}
		}

		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
        // 이미지 tinyimage => minimage로 변경 2015 11 09 유동혁
		if (strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
			// 이미지 오류로 변경 2015 11 09 유동혁
			$imgsrc = $Dir.DataDir."shopimages/product/".$row->minimage;
		}else{
			$imgsrc = $Dir."images/common/noimage.gif";
		}

		#장바구니를 위한 것들
		//장바구니를 위한 옵션

		if($row->option1){
			$opt1arr = explode(",",$row->option1);
			$opt1 = $opt1arr[1];
		}
		if($row->option2){
			$opt2arr = explode(",",$row->option2);
			$opt2 = $opt2arr[1];
		}

		## 품절 안된옵션중에 하나 가져와서 담아야 하기때문에 품절 안된것 조회 하여 옵션인덱스 셋팅 2014-08-25 12:12
		$calOpt1 = $calOpt2 = $resultOption1Val = $resultOption2Val = $resultOptionKey = 0;
		foreach($opt1arr as $opt1Key => $opt1Val){
			if($resultOption1Val) continue;
			if($opt1Key == 0) continue;
			$calOpt1 = $opt1Key-1;
			foreach($opt2arr as $opt2Key =>  $opt2Val){
				if($opt2Key == 0) continue;
				$calOpt2 = ($opt2Key-1)*10;
			}
			$resultOptionKey = $calOpt2 + $calOpt1;
			if($optioncnt[$resultOptionKey] > 0){
				$resultOption1Val = $opt1Key;
				$resultOption2Val = $opt2Key;
			}
		}

		//상품의 카테고리 코드
		$prd_cate_code = substr($row->productcode,0,12);

		$buy_price = ($row->buyprice)?$row->buyprice:"0";
		$sell_price = ($row->sellprice)?$row->sellprice:"0";
		$consumerprice = ($row->consumerprice)?$row->consumerprice:"0";
		$option_reserve = ($row->option_reserve)?$row->option_reserve:"0";

        if ($row->keyword) {
            $tags_tmp	=explode(",",$row->keyword);
            for($t=0;$t < sizeof($tags_tmp);$t++) {
                if ($tags_tmp[$t]) $tags[]	= $tags_tmp[$t];
            }
        }


		//리스트에 쓰이는 변수들만 정리한다.
		$pro_icon			= viewicon($row->etctype);
		$pro_url				= "productdetail.php?productcode=".$row->productcode;
		$pro_img			= $imgsrc;
		$pro_name			= $row->productname;
		$pro_comment	= $row->mdcomment;
		$pro_otype			= $row->overseas_type;
		$pro_bprice		= number_format($buy_price);
		$pro_cprice		= number_format($consumerprice);
		$pro_sprice		= number_format($sell_price);

		?>
			<?if($list_type == "ul"){?>
			<li>
				<div class="goods_ico">
				<?=$pro_icon?>
				</div>
				<a href="<?=$pro_url?>">
					<p class="goods"><img src="<?=$pro_img?>" alt=""></p>
					<div class="infobox">
						<span class="name"><?=$pro_name?></span>
						<span class="sub-ment"><?if ($pro_otype == '1') echo "<img src='../images/main/ico_outdely.png' alt='해외배송' > ";?><?=$pro_comment?></span>
						<div class="pricebox" id="cupon_price" style="">
							<p><label>정상가</label><del><?=$pro_bprice?>원</del></p>
							<p><label>최저가</label><del class="color"><?=$pro_cprice?>원</del></p>
							<p class="last-price"><label>교육할인가</label><strong class="member-only"><?if(strlen($_MShopInfo->getMemid())>0) {?><?=$pro_sprice?>원<?} else {?><img src="../images/common/ico_memberonly_sub.gif" alt="members only" ><?}?></strong></p>
						</div>									
					</div>
				</a>
			</li>
			<?}else{?>
			<li>
				<div class="goods_ico">
				<?=$pro_icon?>
				</div>
				<div class="goods_wrap">
					<a href="<?=$pro_url?>">
						<p class="goods"><img src="<?=$pro_img?>" alt=""></p>
						<div class="infobox">
							<span class="name"><?=$pro_name?></span>
							<div class="pricebox" id="cupon_price" style="">
								<p><label>정상가</label><del><?=$pro_bprice?>원</del></p>
								<p><label>최저가</label><del class="color"><?=$pro_cprice?>원</del></p>
								<p class="last-price"><label>교육할인가</label><strong class="member-only"><?if(strlen($_MShopInfo->getMemid())>0) {?><?=$pro_sprice?>원<?} else {?><img src="../images/common/ico_memberonly_sub.gif" alt="members only"><?}?></strong></p>
							</div>
						</div>
					</a>
				</div>
			</li>
		<?}?>
		<?	//if($lineCnt == 1 || $lineCnt == 3 || $lineCnt == 5 || $lineCnt == 7 ||$lineCnt == 9) echo "</ul>";
			//$lineCnt++;?>

		<?php endwhile; ?>
		</ul>
	<?php endif; ?>
<?}?>	