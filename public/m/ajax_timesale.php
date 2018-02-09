<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
//Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
include_once($Dir."lib/product.class.php");

function merror($msg) {
    $msg = str_replace("'", "\'", $msg);
    header("Content-Type: text/html; charset=euc-kr");
    echo <<<HTML
        <script type="text/javascript">
        alert('{$msg}');
        history.back(-1);
        </script>
HTML;
    exit;
}

$pridx=$_REQUEST["pridx"];
$mode=$_POST["mode"];

##### 타임세일 정보
$today_date = date("Y-m-d H:i:s");
$sql_timesale = "SELECT *,to_char(sdate,'YYYY-MM-DD-HH24-MI-SS') as s_date, to_char(edate,'YYYY-MM-DD-HH24-MI-SS') as e_date FROM tbl_timesale_list ";
$sql_timesale.= "WHERE 1=1 ";
if($sno){
	$sql_timesale.= "AND sno='{$sno}' ";
}else{
	$sql_timesale.= "AND sdate<='{$today_date}' AND edate>='{$today_date}' ";
	$sql_timesale.= "AND view_type='1' ";
}
$sql_timesale.= "ORDER BY edate ";
$res_timesale = pmysql_query($sql_timesale);
$_odata = pmysql_fetch_array($res_timesale);
$enddate_arr = explode("-",$_odata['e_date']);
##### //타임세일 정보

$productcode=$_odata["productcode"];
if(ord($productcode)==0) {
	//alert_go("등록된 타임세일 상품이 없습니다.","index.php");
	//Header("Location:".$Dir.MainDir."main.php");
	exit;
}

$sql = "SELECT a.* ";
$sql.= "FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= "WHERE a.productcode='".$productcode."' ";
//$sql.= "AND a.display='Y' ";
$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
$result=pmysql_query($sql,get_mdb_conn());
if (!$result) {
    //merror("시스템 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
    exit;
}
$_pdata=pmysql_fetch_object($result);
pmysql_free_result($result);

if (!$_pdata) {
    //merror("상품이 삭제되었거나 진열중인 상품이 아닙니다.");
    exit;
}

$productcode=$_pdata->productcode;
$pridx = $_pdata->pridx;
if($_odata[s_price]){
	$_pdata->sellprice = $_odata[s_price];
}

list($review_ordercode_cnt)=pmysql_fetch("select count(*) from tblorderproduct a JOIN tblorderinfo b on a.ordercode = b.ordercode where a.productcode='".$productcode."' AND b.id = '".$_ShopInfo->memid."'");
if(!$_ShopInfo->memid) $review_ordercode_cnt = 0;

$code=substr($productcode,0,12);

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

$sql = "SELECT * FROM tblproductcode WHERE code_a='{$codeA}' AND code_b='{$codeB}' AND code_c='{$codeC}' AND code_d='{$codeD}' ";
$result=pmysql_query($sql,get_mdb_conn());

if($row=pmysql_fetch_object($result)) {
	$_cdata=$row;
	if($row->group_code=="NO") {	//숨김 분류
		//merror("판매가 종료된 상품입니다.");
		exit;
	} else if($row->group_code=="ALL" && strlen($_MShopInfo->getMemid())==0) {	//회원만 접근가능
		//Header("Location:login.php?chUrl=".getUrl());
		exit;
	} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_MShopInfo->getMemgroup()) {	//그룹회원만 접근
		//merror("해당 분류의 접근 권한이 없습니다.");
		exit;
	}
} else {
	//merror("해당 분류가 존재하지 않습니다.");
	exit;
}
pmysql_free_result($result);


$_pdata->reserve=getReserveConvert($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");

if(preg_match("/^\[OPTG\d{4}\]$/",$_pdata->option1)){
	$optcode = substr($_pdata->option1,5,4);
	$_pdata->option1="";
	$_pdata->option_price="";
}

$opt_list=array();
if(strlen($_pdata->option1)>0) {
	$values = explode(",", $_pdata->option1);
	$opt_price=explode(",", $_pdata->option_price);
	$opt_c_price=explode(",", $_pdata->option_c_price);
	$option["name"] = array_shift($values);
	$option["values"] = $values;
	$option["price"]=$opt_price;
	$option["consumer"]=$opt_c_price;

	$opt_list[] = $option;

	if(strlen($_pdata->option2)>0) {
		$values = explode(",", $_pdata->option2);
		$option["name"] = array_shift($values);
		$option["values"] = $values;
		$opt_list[] = $option;
	}

} else if(strlen($optcode)>0) {
	$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
	$result = pmysql_query($sql,get_mdb_conn());
	if($row = pmysql_fetch_object($result)) {
		$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
		$opti=0;
		$option_choice = $row->option_choice;
		$exoption_choice = explode("",$option_choice);
		while(strlen($optionadd[$opti])>0) {
			$option=array();
			$opval = str_replace('"','',explode("",$optionadd[$opti]));
			$option["values"][]="--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
			$opcnt=count($opval);
/*			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				if($exop[1]>0) $option["values"][]=$exop[0]."(+".$exop[1]."원)";
				else if($exop[1]==0) $option["values"][]=$exop[0];
				else $option["values"][]=$exop[0]."(".$exop[1]."원)";
			}*/
			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				if($exop[1]>0){ $option["values"][]=$exop[0]."(+".$exop[1]."원)"; $option["p"][]=$exop[1];}
				else if($exop[1]==0){ $option["values"][]=$exop[0]; $option["p"][]=$exop[0];}
				else {$option["values"][]=$exop[0]."(".$exop[1]."원)"; $option["p"][]=$exop[1];}
			}
			$opti++;

			$opt_list[] = $option;
		}
	}
	pmysql_free_result($result);
}


//입점업체 정보 관련
if($_pdata->vender>0) {
	$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt ";
	$sql.= "FROM tblvenderstore a, tblvenderstorecount b ";
	$sql.= "WHERE a.vender='{$_pdata->vender}' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		$_pdata->vender=0;
	}
	pmysql_free_result($result);
}



//상품다중이미지 확인
$multi_img="N";
$sql2 ="SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
$result2=pmysql_query($sql2,get_db_conn());
if($row2=pmysql_fetch_object($result2)) {
	if($_data->multi_distype=="0") {
		$multi_img="I";
	} else if($_data->multi_distype=="1") {
		$multi_img="Y";
		$multi_imgs=array(&$row2->primg01,&$row2->primg02,&$row2->primg03,&$row2->primg04,&$row2->primg05,&$row2->primg06,&$row2->primg07,&$row2->primg08,&$row2->primg09,&$row2->primg10);
		$thumbcnt=0;
		for($j=0;$j<10;$j++) {
			if(ord($multi_imgs[$j])) {
				$thumbcnt++;
			}
		}
		$multi_height=430;
		$thumbtype=1;
		if($thumbcnt>5) {
			$multi_height=490;
			$thumbtype=2;
		}
	}
}
pmysql_free_result($result2);

if($multi_img=="Y") {

	$imagepath=$Dir.DataDir."shopimages/multi/";
	//$dispos=$row->multi_dispos;
	$changetype=$_data->multi_changetype;
	$bgcolor=$_data->multi_bgcolor;

	$sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$multi_imgs=array(&$row->primg01,&$row->primg02,&$row->primg03,&$row->primg04,&$row->primg05,&$row->primg06,&$row->primg07,&$row->primg08,&$row->primg09,&$row->primg10);

		$tmpsize=explode("",$row->size);
		$insize="";
		$updategbn="N";

		$y=0;
		for($i=0;$i<10;$i++) {
			if(ord($multi_imgs[$i])) {
				$yesimage[$y]=$multi_imgs[$i];
				if(ord($tmpsize[$i])==0) {
					$size=getimagesize($Dir.DataDir."shopimages/multi/".$multi_imgs[$i]);
					$xsize[$y]=$size[0];
					$ysize[$y]=$size[1];
					$insize.="{$size[0]}X".$size[1];
					$updategbn="Y";
				} else {
					$insize.="".$tmpsize[$i];
					$tmp=explode("X",$tmpsize[$i]);
					$xsize[$y]=$tmp[0];
					$ysize[$y]=$tmp[1];
				}
				$y++;
			} else {
				$insize.="";
			}
		}

		$makesize=$maxsize;
		for($i=0;$i<$y;$i++){
			if($xsize[$i]>$makesize || $ysize[$i]>$makesize) {
				if($xsize[$i]>=$ysize[$i]) {
					$tempxsize=$makesize;
					$tempysize=($ysize[$i]*$makesize)/$xsize[$i];
				} else {
					$tempxsize=($xsize[$i]*$makesize)/$ysize[$i];
					$tempysize=$makesize;
				}
				$xsize[$i]=$tempxsize;
				$ysize[$i]=$tempysize;
			}
		}

		pmysql_free_result($result);
	}
}

if(strlen($productcode)==18) {
	$viewproduct=$_COOKIE["ViewProduct"];
	if(ord($viewproduct)==0 || strpos($viewproduct,",{$productcode},")===FALSE) {
		if(ord($viewproduct)==0) {
			$viewproduct=",{$productcode},";
		} else {
			$viewproduct=",".$productcode.$viewproduct;
		}
	} else {
		$viewproduct=str_replace(",{$productcode}","",$viewproduct);
		$viewproduct=",".$productcode.$viewproduct;
	}
	$viewproduct=substr($viewproduct,0,571);
	setcookie("ViewProduct",$viewproduct,0,"/".RootPath);
}

// 패키지 선택 출력
$arrpackage_title=array();
$arrpackage_list=array();
$arrpackage_price=array();
$arrpackage_pricevalue=array();
if((int)$_pdata->package_num>0) {
	$sql = "SELECT * FROM tblproductpackage WHERE num='".(int)$_pdata->package_num."' ";
	$result = pmysql_query($sql,get_db_conn());
	$package_count=0;
	if($row = @pmysql_fetch_object($result)) {
		$package_type=$row->package_type;
		pmysql_free_result($result);
		if(strlen($row->package_title)>0) {
			$arrpackage_title = explode("",$row->package_title);
			$arrpackage_list = explode("",$row->package_list);
			$arrpackage_price = explode("",$row->package_price);

			$package_listrep = str_replace("","",$row->package_list);

			if(strlen($package_listrep)>0) {
				$sql = "SELECT pridx,productcode,productname,sellprice,maximage,quantity,etctype FROM tblproduct ";
				$sql.= "WHERE pridx IN ('".str_replace(",","','",trim($package_listrep,','))."') ";
				$sql.= "AND assembleuse!='Y' ";
				$sql.= "AND display='Y' ";
				$result2 = pmysql_query($sql,get_db_conn());
				while($row2 = @pmysql_fetch_object($result2)) {
					$arrpackage_proinfo[productcode][$row2->pridx] = $row2->productcode;
					$arrpackage_proinfo[productname][$row2->pridx] = $row2->productname;
					$arrpackage_proinfo[sellprice][$row2->pridx] = $row2->sellprice;
					$arrpackage_proinfo[maximage][$row2->pridx] = $row2->maximage;
					$arrpackage_proinfo[quantity][$row2->pridx] = $row2->quantity;
					$arrpackage_proinfo[etctype][$row2->pridx] = $row2->etctype;
				}
				@pmysql_free_result($result2);
			}

			for($t=1; $t<count($arrpackage_list); $t++) {
				$arrpackage_pricevalue[0]=0;
				$arrpackage_pricevalue[$t]=0;
				if(strlen($arrpackage_list[$t])>0) {
					$arrpackage_list_exp = explode(",",$arrpackage_list[$t]);
					$sumsellprice=0;
					for($tt=0; $tt<count($arrpackage_list_exp); $tt++) {
						$sumsellprice += (int)$arrpackage_proinfo[sellprice][$arrpackage_list_exp[$tt]];
					}

					if((int)$sumsellprice>0) {
						$arrpackage_pricevalue[$t]=(int)$sumsellprice;
						if(strlen($arrpackage_price[$t])>0) {
							$arrpackage_price_exp = explode(",",$arrpackage_price[$t]);
							if(strlen($arrpackage_price_exp[0])>0 && $arrpackage_price_exp[0]>0) {
								$sumsellpricecal=0;
								if($arrpackage_price_exp[1]=="Y") {
									$sumsellpricecal = ((int)$sumsellprice*$arrpackage_price_exp[0])/100;
								} else {
									$sumsellpricecal = $arrpackage_price_exp[0];
								}
								if($sumsellpricecal>0) {
									if($arrpackage_price_exp[2]=="Y") {
										$sumsellpricecal = $sumsellprice-$sumsellpricecal;
									} else {
										$sumsellpricecal = $sumsellprice+$sumsellpricecal;
									}
									if($sumsellpricecal>0) {
										if($arrpackage_price_exp[4]=="F") {
											$sumsellpricecal = floor($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else if($arrpackage_price_exp[4]=="R") {
											$sumsellpricecal = round($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										} else {
											$sumsellpricecal = ceil($sumsellpricecal/($arrpackage_price_exp[3]*10))*($arrpackage_price_exp[3]*10);
										}
										$arrpackage_pricevalue[$t]=$sumsellpricecal;
									}
								}
							}
						}
					}
				}
				$propackage_option.= "<option value=\"".$t."\">".$arrpackage_title[$t]."</option>\n";
				$package_count++;
			}
		}
	}
}

$miniq = 1;
if (ord($_pdata->etctype)) {
	$etctemp = explode("",$_pdata->etctype);
	for ($i=0;$i<count($etctemp);$i++) {

		if (strpos($etctemp[$i],"MINIQ=")===0)			$miniq=substr($etctemp[$i],6);
		if (strpos($etctemp[$i],"MAXQ=")===0)			$maxq=substr($etctemp[$i],5);
		if (strpos($etctemp[$i],"DELIINFONO=")===0)	$deliinfono=substr($etctemp[$i],11);
	}
}

//[saveheels] [FITFLOP] 핏플랍 14AW/ 팝 발레리나_퓨터 http://www.saveheels.com/m/goods/view.php?goodsno=1834
//http://twitter.com/home?status=%5Bsaveheels%5D+%5BFITFLOP%5D+%ED%95%8F%ED%94%8C%EB%9E%8D+13AW%2F%EB%AC%B5%EB%A3%A9+%EB%AA%A9+%EB%A0%88%EB%8D%94_%ED%84%B0%ED%8B%80%EA%B7%B8%EB%A6%B0%0Dhttp%3A%2F%2Fnexolve.ajashop.co.kr%2Fm%2Fproductdetail.php%3Fpridx%3D14627
/*
$encodedMsg = urlencode(@iconv('EUC-KR', 'UTF-8//IGNORE', "[saveheels] [FITFLOP] 핏플랍 14AW/ 팝 발레리나_퓨터 http://www.saveheels.com/m/goods/view.php?goodsno=1834"));
$twitterurl = 'http://twitter.com/home?status='.$encodedMsg;
*/


if($_REQUEST["pridx"]){
	list($sns_productname, $sns_maximage)=pmysql_fetch("SELECT productname, maximage FROM tblproduct WHERE pridx='".$_REQUEST["pridx"]."'");


	if($multi_img=="Y") {

		$facebook_imagepath = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/multi/";

		$facebook_sql = "SELECT * FROM tblmultiimages WHERE productcode='{$productcode}' ";
		$facebook_result=pmysql_query($facebook_sql,get_db_conn());
		if($facebook_row=pmysql_fetch_object($facebook_result)) {
			$facebook_multi_imgs=array(&$facebook_row->primg01,&$facebook_row->primg02,&$facebook_row->primg03,&$facebook_row->primg04,&$facebook_row->primg05,&$facebook_row->primg06,&$facebook_row->primg07,&$facebook_row->primg08,&$facebook_row->primg09,&$facebook_row->primg10);
			$facebook_y=0;
			$facebook_insize="";
			for($facebook_i=0;$facebook_i<10;$facebook_i++) {
				if(ord($facebook_multi_imgs[$facebook_i])) {
					$facebook_image[$facebook_y] = $facebook_imagepath.$facebook_multi_imgs[$facebook_i];
					$facebook_y++;
				} else {
					$facebook_insize.="";
				}
			}
			pmysql_free_result($facebook_result);
		}
	}else{
		$facebook_image[0] = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$sns_maximage;
	}
	$facebook_msg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
	$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($facebook_msg.'&time='.time());
	$facebook_meta1 = "<meta property='og:title' content='[".$_data->shopname."] ".$sns_productname."]'/>";
	$facebook_meta2 = "<meta property='og:description' content='[".$_data->shopname."] ".$sns_productname."]'/>";
	$facebook_meta3 = "<meta property='og:image' content='".$facebook_image[0]."' />";


	$tw_msg = "[".$_data->shopname."] {productname} ".'http://'.$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
	$tw_goodsnm = $sns_productname;
	if ($tw_msg_length <= 140) $tw_goodsnm = mb_substr($_pdata->productname, 0, 140 - $tw_msg_length);
	$tw_msg = preg_replace('/{productname}/i', $tw_goodsnm, $tw_msg);
	$tw_encodedMsg = urlencode(iconv('EUC-KR', 'UTF-8//IGNORE', $tw_msg));
	$tw_twitterurl = 'http://twitter.com/home?status='.$tw_encodedMsg;
}


# 카카오
$Port = ($_SERVER[SERVER_PORT] == 80)? "":$_SERVER[SERVER_PORT];
if (strlen($Port)>0) $Port = ":".$Port;
$linkURL = 'http://'.$_SERVER[HTTP_HOST].$Port.$_SERVER[REQUEST_URI];
$msg_kakao1 = $_pdata->productname;
$msg_kakao2 = $linkURL;
$msg_kakao3 = 'fitflop';
$server_host = $_SERVER[HTTP_HOST];


# 페이스북
$facebookButtonUrl = $facebookurl;


# 트위터
$twitterButtobUrl = $tw_twitterurl;


$ardollar=explode(",",$_data->ETCTYPE["DOLLAR"]);

if(ord($ardollar[1])==0 || $ardollar[1]<=0) $ardollar[1]=1;






# 쿠폰 다운로드 최근 날짜 1장 노출
$couponDownLoadFlag = false;
$goods_sale_type = "";
$goods_sale_money = "";
$goods_amount_floor = "";
$goods_sale_max_money = "";
if($_data->coupon_ok=="Y") {
	$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$productcode."'";
	$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
	$categorycode = array();
	while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
		list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
		$categorycode[] = $cate_a;
		$categorycode[] = $cate_a.$cate_b;
		$categorycode[] = $cate_a.$cate_b.$cate_c;
		$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
	}
	if(count($categorycode) > 0){
		$addCategoryQuery = "('".implode("', '", $categorycode)."')";
	}else{
		$addCategoryQuery = "('')";
	}

	$sql = "SELECT a.* FROM tblcouponinfo a ";
	$sql .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
	$sql .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
	if($_pdata->vender>0) {
		$sql .= "WHERE (a.vender='0' OR a.vender='{$_pdata->vender}') ";
	} else {
		$sql .= "WHERE a.vender='0' ";
	}
	$sql .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' AND a.coupon_type='1' ";
	$sql .= "AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
	$sql .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
	$sql .= "AND mod(sale_type::int , 2) = '0' ";
	$sql .= "ORDER BY date DESC ";
	$sql .= "LIMIT 1 OFFSET 0";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$goods_sale_type = $row->sale_type;
		$goods_sale_money = $row->sale_money;
		$goods_amount_floor = $row->amount_floor;
		$goods_sale_max_money = $row->sale_max_money;
		$goods_coupon_code = $row->coupon_code;

		$couponDownLoadFlag = true;
	}
	pmysql_free_result($result);
}

$mainbanner = mainBannerList();

?>
<script src="<?=$Dir?>js/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="<?=$Dir?>js/jquery.sudoSlider.js" ></script>
	<script src="js2/libs/TweenMax-1.15.0.min.js"></script>
	<script src="js2/libs/hammer-2.0.4.min.js"></script>
	<script src="js2/breadcrumb.js"></script>
	<script src="js2/common.js"></script>
	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../js/jcarousellite_1.0.js"></script>
	<script type="text/javascript" src="js/jquery.slides.js"></script>
	<script type="text/javascript" src="js/jquery.metadata.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="js/common.js"></script>

<article class="goodview pos_r">
		<div class="good_img">
				<img src="<?=$Dir.DataDir?>shopimages/timesale/<?=$_odata['rolling_v_img']?>" border="0">
		</div>

</article>

<!-- 코딩 2014-08-08 -->

<form name=frmView method=post onsubmit="return false">
<input type="hidden" name="miniq" value="<?=$miniq?$miniq:"1";?>">
<input type="hidden" name="maxq" value="<?=$maxq?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="package_count" value="<?=$package_count?>">
<input type="hidden" name="consumerprice" value="<?=$_pdata->consumerprice?>">
<input type="hidden" name="dan_price" id="dan_price" value="<?=$_pdata->sellprice?>">
<input type="hidden" name="ea" id="ea" value="<?=$_pdata->quantity?>">
<input type="hidden" name="productcode" value="<?=$productcode?>">
<input type="hidden" name="returnUrl" value="<?=$_SERVER["REQUEST_URI"]?>">
<input type="hidden" name="package_type" value="<?=$package_type?>">
<input type="hidden" name="optionArr" id="optionArr" />
<input type="hidden" name="quantityArr" id="quantityArr" />

<section class="time_sale_wrap">

	<div class="sale_info">
			<h3><?=$_odata['title']?></h3>
			<div class="sale_zone">
				<p class="sale_price">
				<?php if($_pdata->consumerprice){ ?>
				<span><?=number_format($_pdata->consumerprice)?></span>
				<?php } ?>
				<span>
					<em><?=number_format($_pdata->sellprice)?></em>원
				</span>
			</p>
			<p class="sale_icon">
				<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
				<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
				<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
				<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">

			<?php
				$_pdata->rate_discount = ceil(100*($_pdata->consumerprice-$_pdata->sellprice)/$_pdata->consumerprice);

			?>
				<?php if($_pdata->rate_discount){ ?>
                <span class="discount"><em><?=$_pdata->rate_discount?>%</em> 할인</span>
                <?php } ?>
			</p>
			</div>
	</div>
	<div class="sale_amount">
		<ul>
			<li><span>남은시간</span><p id="deadline">00:00:00</p></li>
			<!--<li><span>- 구매자수 ｜</span> <p><?=$_odata['sale_cnt']+$_odata['add_ea']?><em>명</em></p></li>-->

			<?php if ($opt_list) : ?>

				<?php $i=0; foreach ($opt_list as $option) : ?>
				<li>
				<input type="hidden" id="opt_choice<?=$i?>" value="<?=$exoption_choice[$i]?>">
				<span><?=$option["name"]?></span>
				<p>
					<select name="optidxs[]" style="width:100%" onchange="javascript:option_change(this,'<?=$i?>')">
						<option value=""> &nbsp;제품선택하세요</option>
					<?php $v=1; foreach ($option["values"] as $value) :?>
						<?if(strlen($_pdata->option2)==0 && $optioncnt[$v-1]=="0"){?>
							<option value="<?=$v?>" disabled><?=$value?> [품절]</option>
						<?}else{?>
							<option value="<?=$v?>"><?=$value?></option>
						<?}?>
					<?php $v++; endforeach; ?>
					</select>
				</p>
				</li>
				<?php $i++; endforeach; ?>

			<?php endif; ?>

			<li>
				<span>구매수량</span>
				<div class=" " style="">

					<a href="javascript:change_quantity('dn')">
					<input type="button" value="-" class="m"  style="width:30px;height:30px;"/>
					</a>
					<input type='text' pattern="[0-9]*" id = 'quantity' name="quantity" value="<?=($miniq>1?$miniq:"1")?>" <?if($_pdata->assembleuse=="Y"){echo " readonly";}else{ echo "onkeyup='strnumkeyup(this)'";}?> class="amount" size = '2' style="width:100px;height:30px;">
					<a href="javascript:change_quantity('up')">
					<input type="button" value="+" class="p" style="width:30px;height:30px;"/>
					</a>
				</div>

			</li>
		</ul>
	</div>
</section>




<section class="goods_info_total" >
	<article class="local2" style="padding:0px">
		<ul class="view_buy_btn" style=";">
			<li class="spesale"><a href="javascript:;" id="buy" class="timesale_btn">바로구매하기</a></li>
			<!--<li><a href="javascript:;" class="cart"><img src="img/btn_cart.png" alt="" /></a></li>
			<li><a href="javascript:;" class="favorite"><img src="img/btn_wishlist.png" alt="" /></a></li>-->
		</ul>
	</article>
	<div class="social_icons">
		<?if($twitterButtobUrl){?><input type = 'hidden' value = '<?=$twitterButtobUrl?>'><a href="javascript:;" class="t CLS_btn_twitter">twitter</a><?}?>
		<?if($facebookButtonUrl){?><input type = 'hidden' value = '<?=$facebookButtonUrl?>'><a href="javascript:;" class="f CLS_btn_facebook">facebook</a><?}?>
		<!--a href="#" class="m">me2day</a-->
		<!--<a href="javascript:;" class="k CLS_btn_kakao">kakao talk</a>-->
	</div>


	<?if($mainbanner['productdetail_banner']['1']['banner_img']){?>
	<div class="goods_view_banner">
		<div class="img100">
			<a href="<?=$mainbanner['productdetail_banner']['1']['banner_link']?$mainbanner['productdetail_banner']['1']['banner_link']:"javascript:;";?>">
				<img src="<?=$mainbanner['productdetail_banner']['1']['banner_img']?>" alt="" />
			</a>
		</div>
	</div>
	<?}?>

</section>
</form>








<!-- //코딩 2014-08-08 -->
<!--
<article class="goodinfo hide">
<form name=frmView2 method=post onsubmit="return false">
<input type="hidden" name="miniq" value="<?=$miniq?$miniq:"1";?>">
<input type="hidden" name="maxq" value="<?=$maxq?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="package_count" value="<?=$package_count?>">
<input type="hidden" name="consumerprice" value="<?=$_pdata->consumerprice?>">
<input type="hidden" name="dan_price" id="dan_price" value="<?=$_pdata->sellprice?>">
<input type="hidden" name="ea" id="ea" value="<?=$_pdata->quantity?>">
<input type="hidden" name="productcode" value="<?=$productcode?>">
<input type="hidden" name="returnUrl" value="<?=$_SERVER["REQUEST_URI"]?>">
<input type="hidden" name="package_type" value="<?=$package_type?>">
<section class="important_info">
	<p></p>
	<span class="price"><em><?=number_format($_pdata->sellprice*$miniq)?></em> 원</span>
	<div class="size">
		<?if ($package_count) :?>
		<li>
			<dl>
				<dd class="option_list<?=$i?> option-list" style="padding-bottom:2px">
				패키지
				<select name="package_idx" onchange="javascript:packagecal()">
					<option value="">패키지를 선택하세요</option>
					<?=$propackage_option?>
				</select>
				</dd>


			</dl>
		</li>
		<?php endif; ?>

		<?php if ($opt_list) : ?>
		<li>
			<dl>
			<?php $i=0; foreach ($opt_list as $option) : ?>
			<input type="hidden" id="opt_choice<?=$i?>" value="<?=$exoption_choice[$i]?>">

				<dd class="option_list<?=$i?> option-list" style="padding-bottom:2px">
				<?=$option["name"]?>
				<select name="optidxs[]" onchange="javascript:option_change(this,'<?=$i?>')">
					<option value="">옵션을 선택하세요.</option>
				<?php $v=1; foreach ($option["values"] as $value) :?>

					<option value="<?=$v?>">
					<?if(strlen($_pdata->option2)==0 && $optioncnt[$v-1]=="0")echo "품절";?>
						<?=$value?>
					</option>
				<?php $v++; endforeach; ?>
				</select>
				</dd>
			<?php $i++; endforeach; ?>
			</dl>
		</li>
		<?php endif; ?>


	</div>
</section>
<?
$i=1;
foreach($option['price'] as $v){?>
	<input type="hidden" id="option_price<?=$i?>" value="<?=$v?>">
<?$i++;
}
$i=1;
foreach($option['consumer'] as $v){?>
	<input type="hidden" id="option_consumer<?=$i?>" value="<?=$v?>">
<?$i++;
}
$i=0;

foreach($arrpackage_pricevalue as $v){?>
	<input type="hidden" id="package_price<?=$i?>" value="<?=$v?>">
<?$i++;
}
?>
<section class="important_info2">
<?php if($_pdata->consumerprice>0 && $_pdata->sellprice<$_pdata->consumerprice) : ?>
	<dl>
		<dt class="title">· &nbsp;정가</dt>
		<dd class="content"><span id="consumer"><?=number_format($_pdata->consumerprice)?></span>원</dd>
	</dl>
<?php endif; ?>
	<dl>
		<dt class="title">· 판매가</dt>
		<dd class="content"><span id="sellprice"><?=number_format($_pdata->sellprice)?></span>원</dd>
	</dl>
<?php if($reserveconv>0) : ?>

	<dl>
		<dt class="title">· &nbsp;적립금</dt>
		<dd class="content"><span id=reserve><?=number_format($_pdata->reserve)?></span>원</dd>
	</dl>
<?php endif; ?>
	<dl>
		<dt class="title">· &nbsp;수량</dt>
		<dd class="content">
			<input type="button" value="-" onclick="change_quantity('dn')" /><input type="text" name=quantity id=quantity size=2 value="<?=$miniq>1?$miniq:"1";?>" onkeyup="strnumkeyup(this);" onchange="change_quantity('key');"><input type="button" value="+" onclick="change_quantity('up')" />
		</dd>
	</dl>
</section>
<section class="important_info3">
	<ul class="btn_zone">
		<li><a class="buy">바로구매</a></li>
		<li><a class="cart">장바구니</a></li>
		<li><a class="favorite">관심상품</a></li>
	</ul>

</section>
</form>
</article>
-->


<!-- 상품상단 기본정보 섹션들 -->
<?php
	if($package_count>0) { //패키지 상품 출력
		$SellpriceValue=$_pdata->sellprice;
?>
<!-- 패키지 상품 출력 시작 //-->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td bgcolor="#FFFFFF" style="border:1px #d4d4d4 solid;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="100"></col>
			<col width=""></col>
			<?php
			$packagecoll=2;

			for($j=1; $j<count($arrpackage_title); $j++) {
			$arrpackage_list_exp = explode(",", $arrpackage_list[$j]);
			?>
			<tr>
				<td align="center" bgcolor="#F8F8F8" style="padding:5px;border-right:1px #EDEDED solid;border-bottom:1px #EDEDED solid;">

				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center"><b><?=$arrpackage_title[$j]?></b></td>
				</tr>
				<tr>
					<td align="center" style="padding:3px;"><?=(strlen($dicker)>0?$dicker:"<b><FONT color=\"#F02800\" id=\"idx_price".$j."\">".number_format($SellpriceValue+$arrpackage_pricevalue[$j])."원</font></b>")?></td>
				</tr>
				</table>

				</td>
				<td style="border-bottom:1px #EDEDED solid;">

				<table border="0" cellpadding="0" cellspacing="0" width=100%>
				<tr>
					<td width=100% style="padding:5">

					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="<?=ceil(100/$packagecoll)?>%" valign="top" align="center" style="padding:5px;">

						<table border="0" cellpadding="0" cellspacing="0" width="110">
						<tr>
							<td align="center" valign=middle style="border:1px #EAEAEA solid;padding:10px;" bgcolor="#EDEDED">
							<?
								if (strlen($_pdata->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
									echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($_pdata->maximage)."\" border=\"0\" ";
									$width = getimagesize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
									if($width[0]>$width[1]) echo "width=\"80\"> ";
									else echo "height=\"80\">";
								} else {
									echo "<img src=\"".$Dir."images/no_img.gif\" width=\"80\" border=\"0\">";
								}
							?>
						</td>
						</tr>
						<tr>
							<td height="3"></td>
						</tr>
						<tr>
							<td align="center" style="word-break:break-all;padding:10px;padding-top:0px;color:#BEBEBE;"><b>기본상품</b></td>
						</tr>
						</table>

						</td>
						<?
						for($ttt=1; $ttt<count($arrpackage_list_exp); $ttt++) {
						if(strlen($arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]])>0) {
						?>
						<?=($ttt%$packagecoll==0?"</tr><tr>":"")?>
						<td width="<?=ceil(100/$packagecoll)?>%" valign="top" align="center" style="padding:5px;">

						<table border="0" cellpadding="0" cellspacing="0" width="110">
						<tr>
							<td valign="top">
							<table border="0" cellpadding="0" cellspacing="0" id="P<?=$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]?>" >
							<tr>
								<td align="center" valign=middle style="border:1px #EAEAEA solid;padding:10px;" bgcolor="#EDEDED"><A HREF="productdetail.php?pridx=<?=$arrpackage_list_exp[$ttt]?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;">

							<?
							if (strlen($arrpackage_proinfo[maximage][$arrpackage_list_exp[$ttt]])>0 && file_exists($Dir.DataDir."shopimages/product/".$arrpackage_proinfo[maximage][$arrpackage_list_exp[$ttt]])) {
								echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($arrpackage_proinfo[maximage][$arrpackage_list_exp[$ttt]])."\" border=\"0\" ";
								$width = getimagesize($Dir.DataDir."shopimages/product/".$arrpackage_proinfo[maximage][$arrpackage_list_exp[$ttt]]);
								if($width[0]>$width[1]) echo "width=\"80\"> ";
								else echo "height=\"80\">";
							} else {
								echo "<img src=\"".$Dir."images/no_img.gif\" width=\"80\" border=\"0\" align=\"center\">";
							}
							?>
							</A></td>
							</tr>
							<tr>
								<td height="3" style="position:relative;"><?=($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','P','".$arrpackage_proinfo[productcode][$arrpackage_list_exp[$ttt]]."','".($arrpackage_proinfo[quantity][$arrpackage_list_exp[$ttt]]=="0"?"":"1")."')</script>":"")?></td>
							</tr>

							<tr>
								<td align="center" style="word-break:break-all;padding:10px;padding-top:0px;"><A HREF="productdetail.php?pridx=<?=$arrpackage_list_exp[$ttt]?>" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($arrpackage_proinfo[productname][$arrpackage_list_exp[$ttt]],$arrpackage_proinfo[etctype][$arrpackage_list_exp[$ttt]],"")?></FONT></A></td>
							</tr>
						</table>

						</td>
						</tr>
						</table>

					</td>
					<?
						}
					}

					if($ttt<$packagecoll) {
						$empty_count = $packagecoll-$ttt;
						for($ttt=0; $ttt<$empty_count; $ttt++) {
					?>
						<td width="<?=ceil(100/$packagecoll)?>%"></td>
					<?
						}
					}
					?>
					</tr>
					</table>

					</td>
				</tr>
				</table>

				</td>
			</tr>

			<?
					}
			?>
			</table>
			</td>
		</tr>
	</table>
	<br />
<!-- 패키지 상품 출력 끝 //-->
<?
	} //패키지 상품 출력 끝
?>
<input type = 'hidden' id = 'ID_msg_kakao1' value = '<?=$msg_kakao1?>'>
<input type = 'hidden' id = 'ID_msg_kakao2' value = '<?=$msg_kakao2?>'>
<input type = 'hidden' id = 'ID_msg_kakao3' value = '<?=$msg_kakao3?>'>
<input type = 'hidden' id = 'ID_server_host' value = '<?=$server_host?>'>

<script>
setInterval(function(){nowtime()},1000) ;

// 장바구니 담기
$('.cart').click(function(){

	var optidx = document.getElementsByName('optidxs[]');
	var optidxs = '';
	var ea=$('#ea').val();
	var quantity = $('#quantity').val();
	var miniq=document.frmView.miniq.value;
	var maxq=document.frmView.maxq.value;
	var tmp=document.frmView.quantity.value;
	var package_type=document.frmView.package_type.value;
	var packagecount=document.frmView.package_count.value;


	for (i=0;i<optidx.length;i++){
		if(optidx[i].value==''){
			alert('옵션을 선택해주세요.');
			return;
		}else{
			optidxs += optidx[i].value+",";
		}
	}

	if(parseInt(ea)<parseInt(tmp) && ea){
		if(ea=="0"){
			alert("품절인 상품입니다.");
		}else{
			if(parseInt(ea)<parseInt(miniq)){
				alert('선택하신 상품의 재고량이 최소구매수량 보다 적습니다.');
			}else{
				alert('선택하신 상품의 재고량이 '+ea+'개입니다.');
			}
		}
		return;
	}else if(parseInt(miniq)>parseInt(tmp)){
		alert("최소구매수량 이하로 주문하실수 없습니다.");
		return;
	}else if(parseInt(maxq)<parseInt(tmp) && maxq){
		alert("최대구매수량 이상으로 주문하실수 없습니다.");
		return;
	}

	if(packagecount!=""){
		var packagenum=document.frmView.package_idx.selectedIndex;
		if(package_type=="Y" && packagenum=="0") {
			alert('해당 상품의 패키지를 선택하세요.');
			return;
		}
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'productdetail.process.php',
		data: { action_mode: 'cart_add', productcode: '<?=$_pdata->productcode?>', quantity: quantity, optidxs: optidxs, packagenum:packagenum },
		success: function(response){
			if (response.success){
				if(confirm("장바구니를 확인하시겠습니까?")){
					location.replace('basket.php');
				}
			}else{
				if(response.data=="package"){
					alert(decodeURIComponent(response.msg));
				}else{
					if(confirm("이미 장바구니에 상품이 담겨있습니다. 장바구니를 확인하시겠습니까?")){
						location.replace('basket.php');
					}
				}

			}
			return;
		}
	});
	return;
});


//바로구매
$('#buy').click(function(){

	var optidx = document.getElementsByName('optidxs[]');
	var optidxs = '';
	var ea=$('#ea').val();
	var quantity = $('#quantity').val();
	$("#quantityArr").val(quantity);
	var miniq=document.frmView.miniq.value;
	var maxq=document.frmView.maxq.value;
	var tmp=document.frmView.quantity.value;
	var package_type=document.frmView.package_type.value;
	var packagecount=document.frmView.package_count.value;

	if(optidx.length == 1){
		if(optidx[i].value==''){
				alert('옵션을 선택해주세요.');
				return;
			}else{
				optidxs += optidx[i].value;
			}
	}else if(optidx.length == 2){
		for (i=0;i<optidx.length;i++){
			if(optidx[i].value==''){
				alert('옵션을 선택해주세요.');
				return;
			}else if(i == 0){
				optidxs += optidx[i].value+"_";
			}else{
				optidxs += optidx[i].value;
			}
		}
	}else{
		optidxs += "0_0";
	}
	
	if(parseInt(ea)<parseInt(tmp) && ea){
		if(ea=="0"){
			alert("품절인 상품입니다.");
		}else{
			if(parseInt(ea)<parseInt(miniq)){
				alert('선택하신 상품의 재고량이 최소구매수량 보다 적습니다.');
			}else{
				alert('선택하신 상품의 재고량이 '+ea+'개입니다.');
			}
		}
		return;
	}else if(parseInt(miniq)>parseInt(tmp)){
		alert("최소구매수량 이하로 주문하실수 없습니다.");
		return;
	}else if(parseInt(maxq)<parseInt(tmp) && maxq){
		alert("최대구매수량 이상으로 주문하실수 없습니다.");
		return;
	}

	if(packagecount!=""){
		var packagenum=document.frmView.package_idx.selectedIndex;
		if(package_type=="Y" && packagenum=="0") {
			alert('해당 상품의 패키지를 선택하세요.');
			return;
		}
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'productdetail.process.php',
		data: { action_mode: 'order_add', productcode: '<?=$_pdata->productcode?>', quantity: quantity,quantityArr:quantity, optionArr: optidxs, packagenum:packagenum },
		success: function(response){
			if (response.success){
				//location.replace('order.php');
				//location.replace('login.php?chUrl=order.php');
				location.replace('login.php?chUrl=order.php?productcode=<?=$productcode?>');

			}else{
				if(response.data=="package"){
					alert(decodeURIComponent(response.msg));
				}else{
					if(confirm("이미 장바구니에 상품이 담겨있습니다. 장바구니를 확인하시겠습니까?")){
						location.replace('basket.php');
					}
				}

			}
			return;
		}
	});
	return;
});


// 관심 상품 담기
$('.favorite').click(function() {
	var optidx = document.getElementsByName('optidxs[]');
	var optidxs = '';

	for (i=0;i<optidx.length;i++){
		if(optidx[i].value==''){
			alert('옵션을 선택해주세요.');
			return;
		}else{
			optidxs += optidx[i].value+",";
		}
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'productdetail.process.php',
		data: { action_mode: 'wishlist_add', pridx: '<?=$_pdata->pridx?>', optidxs: optidxs },
		success: function(response) {
			if (response.success) {
				if(confirm('관심상품 등록 하였습니다. \n이동하시겠습니까?')){
					location.replace('wishlist.php');
				}
			} else if (response.data == 'LOGIN') {
				alert(decodeURIComponent(response.msg));
				location.replace('login.php?chUrl=<?=getUrl()?>');
			} else {
				alert(decodeURIComponent(response.msg));
				$("#qtest").val(decodeURIComponent(response.msg));

			}
			return false;
		}
	});
	return false;
});

//바로구매
function directbuy(){
	alert('바로구매 미구현!');
}

//옵션설정
function optionlist(){
	var optidx = document.getElementsByName('optidxs[]');
	var optidxs = '';
	for (i=0;i<optidx.length;i++){
		if(optidx[i].value=='0'){
			alert(i+'옵션을 선택해주세요.');
			exit;
		}else{
			optidxs += optidx[i].value+",";
		}
	}

	alert(optidxs);
}

function option_change(sel,type){
	packagecount=document.frmView.package_count.value;
	consumerprice=document.frmView.consumerprice.value;
	if(type=="0"){
		num=sel.value;
		if(packagecount==''){
			ea=$("#quantity").val();

			if($('#option_consumer'+num).val()!=''){
				$("#consumer").html(numberComma($('#option_consumer'+num).val()));
			}else{
				$("#consumer").html(numberComma(consumerprice));
			}

			$("#sellprice").html(numberComma($('#option_price'+num).val()));
			if($('#option_price'+num).val()>"0")$("#dan_price").val($('#option_price'+num).val());


			if(sel.value!=""){
				sumprice=$('#option_price'+num).val()*ea;

				$(".price").html(numberComma(String(sumprice))+" 원");
			}
		}else{
			if($('#option_consumer'+num).val()!=''){
				$("#consumer").html(numberComma($('#option_consumer'+num).val()));
			}else{
				$("#consumer").html(numberComma(consumerprice));
			}
			if($('#option_price'+num).val()>"0")$("#dan_price").val($('#option_price'+num).val());
		}
	}


	var optidx = document.getElementsByName('optidxs[]');
	var optidxs = '';

	for (i=0;i<optidx.length;i++){
		optidxs += optidx[i].value+",";
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'productdetail.check.php',
		data: { action_mode: 'check_ea', optidxs: optidxs, productcode: '<?=$_pdata->productcode?>' },
		success: function(response){
			if (response.success){
				alert(decodeURIComponent(response.msg));
				$("#ea").val("0");
			}else{
				$("#ea").val(response.msg);
			}
			change_quantity();
			return false;
		}
	});
	return false;

}

function numberComma(_number) {
	if (isNaN(_number))
	return;

	var _regExp = new RegExp("(-?[0-9]+)([0-9]{3})");
	while (_regExp.test(_number)) {
		_number = _number.replace(_regExp, "$1,$2");
	}
	return _number;
}

function packagecal(){
	num=document.frmView.package_idx.selectedIndex;
	package_length=document.frmView.package_idx.length;

	var result = "";
	var intgetValue = document.frmView.dan_price.value;
	var temppricevalue = "0";

	for(var j=1; j<package_length; j++) {

		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number($('#package_price'+j).val())).toString();
			$("#idx_price"+j).html(numberComma(temppricevalue)+"원");
		}

	}

	if(num=="0") {
		dan_price=document.frmView.dan_price.value;
		tmp=document.frmView.quantity.value;

		$(".price").html(numberComma(String(dan_price*tmp))+" 원");

		$("#sellprice").html(numberComma(String(dan_price)));

	}else{

		price=$('#package_price'+num).val();
		tmp=document.frmView.quantity.value;
		sumprice=(Number(intgetValue)+Number(price));
		$(".price").html(numberComma(String(sumprice*tmp))+" 원");
		$("#sellprice").html(numberComma(String(sumprice)));



		/*
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);

		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) {
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
		*/
	}


}


//수량 조절
function change_quantity(gbn) {

	tmp=document.frmView.quantity.value;
	miniq=document.frmView.miniq.value;
	maxq=document.frmView.maxq.value;
	ea=document.frmView.ea.value;
	dan_price=document.frmView.dan_price.value;
	packagecount=document.frmView.package_count.value;
	if(packagecount!='')num=document.frmView.package_idx.selectedIndex;
	else num="0";

	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}

	if(parseInt(ea)<parseInt(tmp) && ea){
		if(num>0)dan_price=(Number($('#package_price'+num).val())+Number(dan_price));
		if(ea=="0"){
			document.frmView.quantity.value=miniq;
			sumprice=dan_price*ea;
			$(".price").html("품절");
		}else{

			if(parseInt(ea)<parseInt(miniq)){
				alert('선택하신 상품의 재고량이 최소구매수량 보다 적습니다.');
				document.frmView.quantity.value=miniq;
				sumprice=dan_price*miniq;

				$(".price").html("구매불가");
			}else{
				alert('선택하신 상품의 재고량이 '+ea+'개입니다.');
				document.frmView.quantity.value=ea;
				sumprice=dan_price*ea;

				$(".price").html(numberComma(String(sumprice))+" 원");
			}
		}
		return;
	}else if(parseInt(miniq)>parseInt(tmp)){
		if(num>0)dan_price=(Number($('#package_price'+num).val())+Number(dan_price));
		alert("최소구매수량 이하로 주문하실수 없습니다.");
		document.frmView.quantity.value=miniq;
		sumprice=dan_price*miniq;
		$(".price").html(numberComma(String(sumprice))+" 원");
		return;
	}else if(parseInt(maxq)<parseInt(tmp) && maxq){
		if(num>0)dan_price=(Number($('#package_price'+num).val())+Number(dan_price));
		alert("최대구매수량 이상으로 주문하실수 없습니다.");
		document.frmView.quantity.value=maxq;
		sumprice=dan_price*maxq;
		$(".price").html(numberComma(String(sumprice))+" 원");
		return;
	}

	sumprice=dan_price*tmp;
	//$(".price").html(numberComma(String(sumprice))+" 원");

	document.frmView.quantity.value=tmp;


	if(packagecount!='')packagecal();

}

function CheckForm(gbn,temp2) {

}

function nowtime(){
	var today = new Date();
	var year = today.getFullYear();
	var month = today.getMonth();
	var date = today.getDate();
	var endyear = '<?=$enddate_arr[0]?>';
	var endmonth = '<?=$enddate_arr[1]-1?>';
	var enddate = '<?=$enddate_arr[2]?>';
	var endhour = '<?=$enddate_arr[3]?>';
	var endmin = '<?=$enddate_arr[4]?>';
	var endsec = '<?=$enddate_arr[5]?>';
	var enddate = new Date(endyear,endmonth,enddate,endhour,endmin,endsec);
	var gaptime = (enddate.getTime()-today.getTime())/1000;
	var deadline = "";

	if(gaptime>0){
		var gapS = Math.floor(gaptime%60);
		var gapM = Math.floor((gaptime/60)%60);
		var gapH = Math.floor((gaptime/3600)%24);
		var gapDate = Math.floor(gaptime/86400);

		if(gapH<10){
			gapH = "0"+gapH;
		}
		if(gapM<10){
			gapM = "0"+gapM;
		}
		if(gapS<10){
			gapS = "0"+gapS;
		}

		deadline = gapH+":"+gapM+":"+gapS;

		if(gapDate>0){
			deadline = gapDate+"일 "+deadline;
		}
	}else{
		deadline = "타임세일 마감";
	}
	$("#deadline").html(deadline);
}




$(window).ready(function(){
/*
	var msg = $('#ID_msg_kakao2').val();
	var url = $('#ID_msg_kakao3').val();
	var appname = $('#ID_msg_kakao1').val();
	var link = new com.kakao.talk.KakaoLink($('#ID_server_host').val(), "1.0", url, msg, appname);

	$(".CLS_btn_kakao").click(function() {
		link.execute();
	});
	$(".CLS_btn_facebook").click(function() {
		window.open($(this).prev().val());
	});
	$(".CLS_btn_twitter").click(function() {
		window.open($(this).prev().val());
	});




	$('#slides').slidesjs({
		width: 1000,
		height: 1000,
		play: {
			active: true,
			auto: true,
			interval: 7000,
			swap: true
		}
	});
	$(".CLS_main_next").click(function(){
		$('#slides').find('.slidesjs-next').trigger('click');
	})
	$(".CLS_main_back").click(function(){
		$('#slides').find('.slidesjs-previous').trigger('click');
	})
	$('.slidesjs-navigation').hide();
	$('.slidesjs-pagination').hide();
*/

/*
	$(".jCarouselLite_main_image").jCarouselLite({
		visible: 1,
		btnNext: ".next_black",
		btnPrev: ".prev_black",
		auto: 6000,
		speed: 1000
	}).delay(500);
	*/
	setTimeout(function() {
		$(".jCarouselLite_recommendation").jCarouselLite({
			visible: 3,
			btnNext: ".recommend_product_right",
			btnPrev: ".recommend_product_left",
			auto: 6000,
			speed: 1000
		});
	}, 500);
	//<a class="tap_title tap_title_on"><em class="on">상품 상세설명</em><span class="on">▲</span></a>
	$(".tap_title").click(function(){
		if($(this).attr('idx') == '1'){
			$(".tap_title").removeClass('tap_title_on');
			$(".tap_title").next('div').hide();
			$(".tap_title").find('em').attr('class', 'off');
			$(".tap_title").find('span').attr('class', 'off');
			$(".tap_title").find('span').html('▲');
			$(".tap_title").attr('idx', '1');

			$(this).addClass('tap_title_on');
			$(this).next('div').show();
			$(this).find('em').attr('class', 'on');
			$(this).find('span').attr('class', 'on');
			$(this).find('span').html('▼');
			$(this).attr('idx', '2');
		}else{
			$(this).removeClass('tap_title_on');
			$(this).next('div').hide();
			$(this).find('em').attr('class', 'off');
			$(this).find('span').attr('class', 'off');
			$(this).find('span').html('▲');
			$(this).attr('idx', '1');
		}
	})
	$(".tap_title").removeClass('tap_title_on');
	$(".tap_title").attr('idx', '1');
	$(".tap_title").next('div').hide();
	$(".tap_title").find('em').attr('class', 'off');
	$(".tap_title").find('span').attr('class', 'off');
	$(".tap_title").find('span').html('▲');


	$(document).on("click", ".reviewContentsDeleteAjax", function(){
		if(confirm('해당 리뷰를 삭제하시겠습니까?')){
			var reviewNum = $(this).prev().val();
			$.ajax({
				type: "POST",
				url: "../m/productdetail_ajax_review_proc.php",
				data: "num="+reviewNum+"&productcode="+$("input[name='productcode']").val()+"&mode=deleteReview"
			}).done(function ( data ) {
				if(data == '1' && reviewNum){
					alert('해당 리뷰를 정상적으로 삭제했습니다.');
					$.get("productdetail_ajax_review.php?page=1&productcode="+$("input[name='productcode']").val(),function(data){
						$(".CLS_detail_review").html(data);
					});
				}else{
					alert('리뷰 삭제에 실패 했습니다.');
				}
			});
		}
	});


	$(document).on("click", ".reviewCommentDeleteAjax", function(){
		if(confirm('해당 리플을 삭제하시겠습니까?')){
			var objComment = $(this).parent().parent().parent().parent();
			$.ajax({
				type: "POST",
				url: "../m/productdetail_ajax_review_proc.php",
				data: "no="+$(this).prev().val()+"&num="+$(this).prev().prev().val()+"&mode=deleteReviewContents"
			}).done(function ( data ) {
				$(objComment).html(data);
			});
		}
	});


	$(document).on("click", ".reviewCommentAjax", function(){
		var objText = $(this).prev();
		var objComment = $(this).parent().next();
		$.ajax({
			type: "POST",
			url: "../m/productdetail_ajax_review_proc.php",
			data: "num="+$(this).prev().prev().val()+"&contents="+$(this).prev().val()+"&mode=writeReviewContents"
		}).done(function ( data ) {
			$(objComment).html(data);
			$(objText).val('');
		});
	});


});
</script>

