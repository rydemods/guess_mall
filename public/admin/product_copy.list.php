<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################


extract($_REQUEST);

$sort=$_REQUEST["sort"];

$mode=$_POST["mode"];
$code=$_POST["code"];
$keyword=$_POST["keyword"];
$searchtype=$_POST["searchtype"];
$copycode=$_POST["copycode"];
if(ord($searchtype)==0) $searchtype=0;

$cproductcodes=$_POST["cproductcodes"];

if ($mode=="copy" || $mode=="move") {
	$cproductcodes=rtrim($cproductcodes,'|');
	$cproductcode=explode("|",$cproductcodes);
	$size = sizeof($cproductcode);

	if ($size>100) {
		alert_go('한번에 100개씩만 바꾸실 수 있습니다.',-1);
	}
	if ($size==0) {
		alert_go('카테고리 이동/복사할 상품을 선택하세요.',-1);
	}

	$sql = "SELECT type FROM tblproductcode WHERE code_a='".substr($copycode,0,3)."' ";
	$sql.= "AND code_b='".substr($copycode,3,3)."' ";
	$sql.= "AND code_c='".substr($copycode,6,3)."' AND code_d='".substr($copycode,9,3)."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row || strpos($row->type,'X')===FALSE) {
		alert_go('상품카테고리 선택이 잘못되었습니다.',-1);
	}

	$copycount=0;
	$vender_prcodelist=array();
	for ($i=0;$i<=$size;$i++) {
		if (strlen($cproductcode[$i])==18) {
			$sql = "SELECT * FROM tblproduct WHERE productcode = '{$cproductcode[$i]}'";
			$result = pmysql_query($sql,get_db_conn());
			if ($row=pmysql_fetch_object($result)) {
				$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '{$copycode}%' ";
				$sql.= "ORDER BY productcode DESC LIMIT 1 ";
				$result = pmysql_query($sql,get_db_conn());
				if ($rows = pmysql_fetch_object($result)) {
					$newproductcode = substr($rows->productcode,12)+1;
					$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
				} else {
					$newproductcode = "000001";
				}
				pmysql_free_result($result);

				$path = $Dir.DataDir."shopimages/product/";
				if (ord($row->maximage)) {
					$ext = strtolower(pathinfo($row->maximage,PATHINFO_EXTENSION));
					$maximage=$copycode.$newproductcode.".".$ext;
					if (file_exists("$path$row->maximage")) {
						if ($mode=="move") rename("$path$row->maximage","$path$maximage");
						else copy("$path$row->maximage","$path$maximage");
					}
				} else $maximage="";
				if (ord($row->minimage)) {
					$ext = strtolower(pathinfo($row->minimage,PATHINFO_EXTENSION));
					$minimage=$copycode.$newproductcode."2.".$ext;
					if (file_exists("$path$row->minimage")) {
						if ($mode=="move") rename("$path$row->minimage","$path$minimage");
						else copy("$path$row->minimage","$path$minimage");
					}
				} else $minimage="";
				if (ord($row->tinyimage)) {
					$ext = strtolower(pathinfo($row->tinyimage,PATHINFO_EXTENSION));
					$tinyimage=$copycode.$newproductcode."3.".$ext;
					if (file_exists("$path$row->tinyimage")) {
						if ($mode=="move") rename("$path$row->tinyimage","$path$tinyimage");
						else copy("$path$row->tinyimage","$path$tinyimage");
					}
				} else $tinyimage="";
				if (ord($row->quantity)==0) $quantity="NULL";
				else $quantity=$row->quantity;

				if(ord($row->brand)==0) $row->brand = 'NULL';
				$productname = pmysql_escape_string($row->productname);
				$production = pmysql_escape_string($row->production);
				$madein = pmysql_escape_string($row->madein);
				$model = pmysql_escape_string($row->model);
				$tempkeyword = pmysql_escape_string($row->keyword);
				$addcode = pmysql_escape_string($row->addcode);
				$userspec = pmysql_escape_string($row->userspec);
				$option1 = pmysql_escape_string($row->option1);
				$option2 = pmysql_escape_string($row->option2);
				$content = pmysql_escape_string($row->content);
				$selfcode = pmysql_escape_string($row->selfcode);
				$assembleproduct = pmysql_escape_string($row->assembleproduct);

				$sql = "INSERT INTO tblproduct(
				productcode	,
				productname	,
				assembleuse	,
				assembleproduct	,
				sellprice	,
				consumerprice	,
				buyprice	,
				reserve		,
				reservetype	,
				production	,
				madein		,
				model		,
				brand		,
				opendate	,
				selfcode	,
				bisinesscode	,
				quantity	,
				group_check	,
				keyword		,
				addcode		,
				userspec	,
				maximage	,
				minimage	,
				tinyimage	,
				option_price	,
				option_quantity	,
				option1		,
				option2		,
				etctype		,
				deli		,
				package_num	,
				display		,
				date		,
				vender		,
				regdate		,
				modifydate	,
				content) VALUES (				
				'".$copycode.$newproductcode."', 
				'{$productname}', 
				'{$row->assembleuse}', 
				'{$row->assembleproduct}', 
				{$row->sellprice}, 
				{$row->consumerprice}, 
				{$row->buyprice}, 
				'{$row->reserve}', 
				'{$row->reservetype}', 
				'{$production}', 
				'{$madein}', 
				'{$model}', 
				{$row->brand}, 
				'{$row->opendate}', 
				'{$row->selfcode}', 
				'{$row->bisinesscode}', 
				{$quantity}, 
				'{$row->group_check}', 
				'{$tempkeyword}', 
				'{$addcode}', 
				'{$userspec}', 
				'{$maximage}', 
				'{$minimage}', 
				'{$tinyimage}', 
				'{$row->option_price}', 
				'{$row->option_quantity}', 
				'{$option1}', 
				'{$option2}', 
				'{$row->etctype}', 
				'{$row->deli}', 
				'".(int)$row->package_num."', 
				'{$row->display}', 
				'".(($newtime=="Y")?date("YmdHis"):$row->date)."', 
				'{$row->vender}', 
				now(), 
				now(), 
				'{$content}') RETURNING pridx";
				$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
				$insert_pridx = $row2[0];
				$fromproductcodes.="|".$cproductcode[$i];
				$copyproductcodes.="|".$copycode.$newproductcode;

				if($row->vender>0) {
					$vender_prcodelist[$row->vender]["IN"][]=$copycode.$newproductcode;
				}

				if ($mode=="move") {
					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["OUT"][]=$row->productcode;
					}

					$sql = "DELETE FROM tblproduct WHERE productcode = '{$cproductcode[$i]}' ";
					pmysql_query($sql,get_db_conn());

					#태그관련 지우기
					$sql = "DELETE FROM tbltagproduct WHERE productcode = '{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductgroupcode SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductreview SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproducttheme SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblwishlist SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='{$cproductcode[$i]}'";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET ";
					$sql.= "collection_list = replace(collection_list,'{$cproductcode[$i]}','".$copycode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialcode SET ";
					$sql.= "special_list = replace(special_list,'{$cproductcode[$i]}','".$copycode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialmain SET ";
					$sql.= "special_list = replace(special_list,'{$cproductcode[$i]}','".$copycode.$newproductcode."') ";
					pmysql_query($sql,get_db_conn());

					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "UPDATE tblassembleproduct SET productcode = '".$copycode.$newproductcode."' ";
						$sql.= "WHERE productcode='{$cproductcode[$i]}'";
						pmysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '".$copycode.$newproductcode."' ";
						$result = pmysql_query($sql,get_db_conn());
						if($row = @pmysql_fetch_object($result)) {
							if(ord(str_replace("","",$row->assemble_pridx))) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = REPLACE(assembleproduct,',{$cproductcode[$i]}',',".$copycode.$newproductcode."') ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								pmysql_query($sql,get_db_conn());
							}
						}
						pmysql_free_result($result);
					} else {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$row->pridx}','{$insert_pridx}'), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',{$row->pridx}',',{$insert_pridx}') ";
						pmysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품이동입력 ## - 상품코드 {$cproductcode[$i]} => ".$copycode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				} else {
					if($row->group_check=="Y") {
						$sql = "INSERT INTO tblproductgroupcode SELECT '".$copycode.$newproductcode."', group_code FROM tblproductgroupcode WHERE productcode = '{$cproductcode[$i]}' ";
						pmysql_query($sql,get_db_conn());
					}
					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "INSERT INTO tblassembleproduct ";
						$sql.= "SELECT '".$copycode.$newproductcode."', assemble_type, assemble_title, assemble_pridx, assemble_list FROM tblassembleproduct ";
						$sql.= "WHERE productcode='{$cproductcode[$i]}' ";
						pmysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '{$cproductcode[$i]}' ";
						$result = pmysql_query($sql,get_db_conn());
						if($row = @pmysql_fetch_object($result)) {
							if(ord(str_replace("","",$row->assemble_pridx))) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = assembleproduct||',".$copycode.$newproductcode."' ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								pmysql_query($sql,get_db_conn());
							}
						}
						pmysql_free_result($result);
					} else {
						$sql = "UPDATE tblproduct SET assembleproduct = '' ";
						$sql.= "WHERE productcode='".$copycode.$newproductcode."'";
						pmysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품복사입력 ## - 상품코드 {$cproductcode[$i]} => ".$copycode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				}
				$copycount++;
			}
		}
	}
	if ($copycount!=0) {
		//입점업체 상품 관련 처리
		if(count($vender_prcodelist)>0) {
			$tmpvender=$vender_prcodelist;
			while(list($vender,$prarr)=each($tmpvender)) {
				$tmpcode_a=array();
				for($kk=0;$kk<count($prarr["IN"]);$kk++) {
					//insert 처리
					setVenderDesignInsert($vender, $prarr["IN"][$kk]);

					if(strlen($prarr["OUT"][$kk])==18) {
						//move 처리
						$tmpcode_a[substr($prarr["OUT"][$kk],0,3)]=true;
						setVenderThemeSpecialUpdate($vender, $prarr["IN"][$kk], $prarr["OUT"][$kk]);
					}
				}
				//미니샵 상품수 업데이트 (진열된 상품만)
				$sql="SELECT COUNT(*) as prdt_allcnt,COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
				$sql.="WHERE vender='{$vender}' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				$prdt_allcnt=(int)$row->prdt_allcnt;
				$prdt_cnt=(int)$row->prdt_cnt;
				pmysql_free_result($result);

				setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);

				if(count($tmpcode_a)>0) {
					$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
					$sql.= "WHERE ( ";
					$arr_code_a=$tmpcode_a;
					$_=array();
					while(list($key,$val)=each($arr_code_a)) {
						if(strlen($key)==3) {
							$_[] = "productcode LIKE '{$key}%' ";
						}
					}
					$sql.= implode("OR ",$_);
					$sql.= ") ";
					$sql.= "AND vender='{$vender}' ";
					$sql.= "GROUP BY code_a ";
					$result=pmysql_query($sql,get_db_conn());
					while($row=pmysql_fetch_object($result)) {
						unset($tmpcode_a[$row->code_a]);
					}
					pmysql_free_result($result);

					if(count($tmpcode_a)>0) {
						while(list($key,$val)=each($tmpcode_a)) {
							$imagename = $Dir.DataDir."shopimages/vender/{$vender}_CODE10_{$key}.gif";
							@unlink($imagename);
						}
						$str_code_a = implode(',',array_keys($tmpcode_a));
						$str_code_a=str_replace(',','\',\'',$str_code_a);
						setVenderDesignDelete($str_code_a, $vender);
					}
				}
			}
		}

		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		if ($mode=="move") $onload="<script>alert('$copycount 건의 데이터가 [".str_replace("\"","",$copycode_name)."]으로 이동되었습니다.');</script>";
		else $onload="<script>alert('{$copycount} 건의 데이터가 [".str_replace("\"","",$copycode_name)."]으로 복사되었습니다.');</script>";
	}

} else if ($mode=="delete") {
	$cproductcodes=rtrim($cproductcodes,'|');
	$allprcode=$cproductcodes;

	$cproductcode=explode("|",$cproductcodes);
	$size = sizeof($cproductcode);

	if ($size>100) {
		alert_go('한번에 100개씩만 삭제하실 수 있습니다.',-1);
	}
	if ($size==0) {
		alert_go('삭제할 상품을 선택하세요.',-1);
	}

	$prcodelist = str_replace("|","','",$allprcode);
	$arrvender=array();
	$arrvenderid=array();
	$arrpridx=array();
	$arrassembleuse=array();
	$arrassembleproduct=array();
	$arrproductcode=array();
	$sql = "SELECT vender,pridx,assembleuse,assembleproduct,productcode FROM tblproduct ";
	$sql.= "WHERE productcode IN ('{$prcodelist}') ";
	$p_result=pmysql_query($sql,get_db_conn());
	while($p_row=pmysql_fetch_object($p_result)) {
		if($p_row->vender>0 && ord($arrvenderid[$p_row->vender])==0) {
			$arrvender[]=$p_row->vender;
			$arrvenderid[$p_row->vender]=$p_row->vender;
		}
		$arrpridx[]=$p_row->pridx;
		$arrassembleuse[]=$p_row->assembleuse;
		$arrassembleproduct[]=$p_row->assembleproduct;
		$arrproductcode[]=$p_row->productcode;
	}
	pmysql_free_result($p_result);

	$sql = "DELETE FROM tblproduct WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());
	
	$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('{$prcodelist}')";
	$result = pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('{$prcodelist}')";
	$result = pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductreview WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblwishlist WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblcollection WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	for($vz=0; $vz<count($arrpridx); $vz++) { // 코디/조립 기본구성상품의 가격 처리		
		if($arrassembleuse[$vz]=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '{$arrproductcode[$vz]}' ";
			$result = pmysql_query($sql,get_db_conn());
			if($row = @pmysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$arrproductcode[$vz]}' ";
				pmysql_query($sql,get_db_conn());
				
				if(ord(str_replace("","",$row->assemble_pridx))) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',{$arrproductcode[$vz]}','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					pmysql_query($sql,get_db_conn());
				}
			}
			pmysql_free_result($result);
		} else {
			if(ord($arrassembleproduct[$vz])) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$arrassembleproduct[$vz])."') ";
				$result = pmysql_query($sql,get_db_conn());
				while($row = @pmysql_fetch_object($result)) {
					$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND display ='Y' ";
					$sql.= "AND assembleuse!='Y' ";
					$result2 = pmysql_query($sql,get_db_conn());
					if($row2 = @pmysql_fetch_object($result2)) {
						$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
						$sql.= "WHERE productcode = '{$row->productcode}' ";
						$sql.= "AND assembleuse='Y' ";
						pmysql_query($sql,get_db_conn());
					}
					pmysql_free_result($result2);
				}
			}

			$sql = "UPDATE tblassembleproduct SET ";
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$arrpridx[$vz]}',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',{$arrpridx[$vz]}','') ";
			pmysql_query($sql,get_db_conn());
		}
	}

	for($yy=0;$yy<count($arrvender);$yy++) {
		//미니샵 테마코드에 등록된 상품 삭제
		setVenderThemeDelete($prcodelist, $arrvender[$yy]);

		//미니샵 상품수 업데이트 (진열된 상품만)
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='{$arrvender[$yy]}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		pmysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);

		//tblvendercodedesign => 해당 대분류 상품 확인 후 없으면 대분류 화면 삭제
		$tmpcode_a=array();
		$arrprcode=explode("|",$allprcode);
		for($j=0;$j<count($arrprcode);$j++) {
			$tmpcode_a[substr($arrprcode[$j],0,3)]=true;
		}

		if(count($tmpcode_a)>0) {
			$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
			$sql.= "WHERE ( ";
			$arr_code_a=$tmpcode_a;
			$_=array();
			while(list($key,$val)=each($arr_code_a)) {
				if(strlen($key)==3) {
					$_[] = "productcode LIKE '{$key}%' ";
				}
			}
			$sql.= implode("OR ",$_);
			$sql.= ") ";
			$sql.= "AND vender='{$arrvender[$yy]}' ";
			$sql.= "GROUP BY code_a ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				unset($tmpcode_a[$row->code_a]);
			}
			pmysql_free_result($result);

			if(count($tmpcode_a)>0) {
				while(list($key,$val)=each($tmpcode_a)) {
					$imagename = $Dir.DataDir."shopimages/vender/{$arrvender[$yy]}_CODE10_{$key}.gif";
					@unlink($imagename);
				}
				$str_code_a = implode(',',array_keys($tmpcode_a));
				$str_code_a=str_replace(',','\',\'',$str_code_a);
				setVenderDesignDelete($str_code_a, $arrvender[$yy]);
			}
		}
	}

	$prcode = explode("|",$allprcode);
	$cnt = count($prcode);

	$log_content = "## 등록상품 이동/복사/삭제에서 {$cnt}건의 상품삭제 ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	for($i=0;$i<$cnt;$i++){
		$delshopimage=$Dir.DataDir."shopimages/product/{$prcode[$i]}*";
		proc_matchfiledel($delshopimage);
		delProductMultiImg("prdelete","",$prcode[$i]);
	}

	$onload="<script>alert(\"{$cnt}건의 상품이 정상적으로 삭제되었습니다.\");</script>";
}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";
include("header.php"); 
?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoPage(block,gotopage,sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function CheckAll(cnt){
	checkvalue=document.form1.allcheck.checked;
	for(i=1;i<=cnt;i++){
		document.form1.cproductcode[i].checked=checkvalue;
		checkActive(document.form1.cproductcode[i],document.form1.cproductcode[i].value);
	}
}

function CopyCodeSelect() {
	window.open("product_copycodeselect.php","","height=300,width=420,scrollbars=no,resizable=no");
}

function Copy(gbn) {
	var gbn_name = "복사";
	if (gbn=="move") gbn_name = "이동";
	if (document.form1.copycode.value.length==0) {
		alert(gbn_name+"할 카테고리를 선택하세요.");
		CopyCodeSelect();
		return;
	}
	if (document.form1.copycode.value==document.form1.oldcode.value) {
		alert(gbn_name+"할 카테고리가 이전카테고리와 같습니다.");
		CopyCodeSelect();
		return;
	}
	if (confirm("선택된 카테고리를 "+gbn_name+"하시겠습니까?")) {
		var checkvalue=false;
		for(i=1;i<document.form1.cproductcode.length;i++){
			if(document.form1.cproductcode[i].checked){
				checkvalue=true;
				document.form1.cproductcodes.value+=document.form1.cproductcode[i].value+"|";
			}
		}

		if(checkvalue!=true){
			alert(gbn_name+"할 상품을 선택하세요");
			return;
		}
		document.form1.mode.value=gbn;
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function Delete() {
	if (confirm("선택한 상품 삭제시 복구가 불가능합니다.\n\n선택된 상품을 삭제하시겠습니까?")) {
		var checkvalue=false;
		for(i=1;i<document.form1.cproductcode.length;i++){
			if(document.form1.cproductcode[i].checked){
				checkvalue=true;
				document.form1.cproductcodes.value+=document.form1.cproductcode[i].value+"|";
			}
		}
		if(checkvalue!=true){
			alert('삭제할 상품이 선택되지 않았습니다.');
			return;
		}
		document.form1.mode.value="delete";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function checkActive(checkObj,checkId)
{
	if(document.getElementById("pidx_"+checkId))
	{
		if(checkObj.checked)
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#EFEFEF";
		else
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#FFFFFF";
	}
}

function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=cproductcodes>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=searchtype value="<?=$searchtype?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<input type=hidden name=sort value="<?=$sort?>">
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?php
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=45></col>
			<?php if($vendercnt>0){?>
			<col width=70></col>
			<?php }?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell">선택</TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
			<input type=hidden name=cproductcode>
<?php
			if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>2)) {
				$page_numberic_type = 1;
				if ($searchtype=="0" && strlen($code)==12) {
					$qry = "AND productcode LIKE '{$code}%' ";
				} else {
					$qry = "AND productname LIKE '%{$keyword}%' ";
				}
				$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
				$sql0.= $qry;
				$paging = new Paging($sql0,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct WHERE 1=1 ";
				$sql.= $qry." ";
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\" id=\"pidx_{$row->productcode}\">\n";
					echo "	<TD class=\"td_con2\"><input type=checkbox name=cproductcode value=\"{$row->productcode}\" onclick=\"checkActive(this,'{$row->productcode}')\"></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
					}
					echo "<TD class=\"td_con1\">";
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
						echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
					} else {
						echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
					}
					echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
					echo "		<tr bgcolor=\"#FFFFFF\">\n";
					if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
					} else {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "	</td>\n";
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (ord($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('{$row->productcode}');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"{$colspan}\" align=\"center\">상품카테고리를 선택하거나 검색을 하세요.</td></tr>";
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td width="100%" background="images/blueline_bg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
<?php
			echo "<tr>\n";
			echo "	<td class=\"font_blue\" style=\"padding-bottom:2px;\"><input type=checkbox id=\"idx_allcheck\" name=allcheck value=\"{$cnt}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\" onclick=\"CheckAll('{$cnt}')\"><label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_allcheck><span style=\"font-size:8pt;\">전체상품 선택</span></label></td>";
			echo "</tr>\n";
			if($page_numberic_type) {				
				echo "<tr>\n";
				echo "	<td height=\"30\" align=center>\n";
				echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
			}
?>
			</table>
			</td>
		</tr>
<?php
		if ($t_count>0) {
?>
		<input type=hidden name=copycode value="<?=$copycode?>">
		<input type=hidden name=oldcode value="<?=$code?>">
		<tr>
			<td width="100%" bgcolor="#0099CC" style="padding-top:3pt; padding-bottom:3pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" class="font_white1">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="110"></col>
				<col width=""></col>
				<col width="95"></col>
				<tr>
					<td class="font_white1">&nbsp;&nbsp;이동/복사할 카테고리 : </td>
					<td><input type=text name=copycode_name size=43 style="width:100%;" onfocus="this.blur();alert('[카테고리 선택] 버튼을 이용하셔서 이동/복사시킬 위치의 카테고리를 선택하시기 바랍니다.');" value="<?=htmlspecialchars(stripslashes($copycode_name))?>" class="input" style="width:100%;"></td>
					<td align=center><a href="javascript:CopyCodeSelect();"><img src="images/btn_cateselect.gif" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%" class="font_white1">&nbsp;<input type=checkbox id="idx_newtime" name=newtime value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_newtime>이동/복사된 상품의 등록날짜를 현재시간으로 재설정합니다.</label></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td width="100%" align=center style="padding-top:6pt; padding-bottom:6pt;"><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">상품 이동/복사는 <b>최하위 또는 마지막 카테고리에서만 적용</b>됩니다.</span><br>
		<a href="javascript:Copy('copy');"><img src="images/btn_copy.gif" width="136" height="38" border="0" vspace="3"></a>&nbsp;
		<a href="javascript:Copy('move');"><img src="images/btn_trans.gif" width="136" height="38" border="0" vspace="3"></a>&nbsp;
		<a href="javascript:Delete();"><img src="images/btn_del4.gif" width="136" height="38" border="0" vspace="3"></a></td>
	</tr>
	</table>
	</td>
</tr>

<?php
		}
?>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
</table>
<?=$onload?>
</body>
</html>
