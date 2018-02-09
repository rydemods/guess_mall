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

$mode=$_POST["mode"];
$code=$_POST["code"];
$codes=$_POST["codes"];

if($mode=="movesave" && ord($codes)) {	//이동된 분류 순서 저장
	$tok1=explode("!",$codes);
	for($i=0;$i<count($tok1);$i++) {
		if(strlen(trim($tok1[$i]))>=12) {
			$code = substr($tok1[$i],0,12);
			$sequence=9999-$i;
			$sql = "UPDATE tblrecipecode SET sequence='{$sequence}' ";
			$sql.= "WHERE code_a='".substr($code,0,3)."' ";
			$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
			$sql.= "AND code_d='".substr($code,9,3)."' ";
			pmysql_query($sql,get_db_conn());

			if(strcmp("@",substr($tok1[$i],13))) {
				$tok2=explode("@",substr($tok1[$i],13));
				for($ii=0;$ii<count($tok2);$ii++) {
					if(strlen(trim($tok2[$ii]))>=12) {
						$code=substr($tok2[$ii],0,12);
						$sequence=9999-$ii;
						$sql = "UPDATE tblrecipecode SET sequence='{$sequence}' ";
						$sql.= "WHERE code_a='".substr($code,0,3)."' ";
						$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
						$sql.= "AND code_d='".substr($code,9,3)."' ";
						pmysql_query($sql,get_db_conn());

						if(strcmp("#",substr($tok2[$ii],13))) {
							$tok3=explode("#",substr($tok2[$ii],13));
							for($iii=0;$iii<count($tok3);$iii++) {
								if(strlen(trim($tok3[$iii]))>=12) {
									$code=substr($tok3[$iii],0,12);
									$sequence=9999-$iii;
									$sql = "UPDATE tblrecipecode SET sequence='{$sequence}' ";
									$sql.= "WHERE code_a='".substr($code,0,3)."' ";
									$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
									$sql.= "AND code_d='".substr($code,9,3)."' ";
									pmysql_query($sql,get_db_conn());
									
									if(strcmp("$",substr($tok3[$iii],13))) {
										$tok4=explode("$",$tok3[$iii]);
										for($iiii=1;$iiii<count($tok4);$iiii++) {
											$code=$tok4[$iiii];
											$sequence=9999-$iiii;
											$sql = "UPDATE tblrecipecode SET sequence='{$sequence}' ";
											$sql.= "WHERE code_a='".substr($code,0,3)."' ";
											$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
											$sql.= "AND code_d='".substr($code,9,3)."' ";
											pmysql_query($sql,get_db_conn());
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$onload="<script>parent.CodeMoveResult();alert('분류순서 변경 수정이 완료되었습니다.');</script>";
} else if($mode=="delete" && strlen($code)==12) {	//분류 삭제
	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);
	$sql = "SELECT * FROM tblrecipecode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
	$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblrecipecode WHERE code_a = '{$code_a}' ";
		$likecode=$code_a;
		if($code_b!="000") {
			$sql.= "AND code_b='{$code_b}' ";
			$likecode.=$code_b;
			if($code_c!="000") {
				$sql.= "AND code_c='{$code_c}' ";
				$likecode.=$code_c;
				if($code_d!="000") {
					$sql.= "AND code_d='{$code_d}' ";
					$likecode.=$code_d;
				}
			}
		}
		
		pmysql_query($sql,get_db_conn());

		$arrvender=array();
		$arrvenderid=array();
		$arrpridx=array();
		$arrassembleuse=array();
		$arrassembleproduct=array();
		$arrproductcode=array();
		$sql = "SELECT vender,pridx,assembleuse,assembleproduct,productcode FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '{$likecode}%' ";
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

		$sql = "DELETE FROM tblproduct WHERE productcode LIKE '{$likecode}%' ";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblproducttheme WHERE (productcode LIKE '{$likecode}%' OR code LIKE '{$likecode}%') ";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblproductreview WHERE productcode LIKE '{$likecode}%' ";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblwishlist WHERE productcode LIKE '{$likecode}%'";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblspecialcode WHERE code LIKE '{$likecode}%'";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblcollection WHERE productcode LIKE '{$likecode}%'";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tbltagproduct WHERE productcode LIKE '{$likecode}%'";
		pmysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblproductgroupcode WHERE productcode LIKE '{$likecode}%' ";
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
			setVenderThemeDeleteLike($likecode, $arrvender[$yy]);

			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);

			$tmpcode_a=substr($likecode,0,3);
			$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '{$tmpcode_a}%' AND vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prcnt=$row->cnt;
			pmysql_free_result($result);

			if($prcnt==0) {
				setVenderDesignDeleteNor($tmpcode_a, $arrvender[$yy]);

				$imagename=$Dir.DataDir."shopimages/vender/{$arrvender[$yy]}_CODE10_{$tmpcode_a}.gif";
				@unlink($imagename);
			}
		}

		$delshopimage = $Dir.DataDir."shopimages/product/{$likecode}*";
		proc_matchfiledel($delshopimage);

		$delshopimage = $Dir.DataDir."shopimages/etc/CODE{$likecode}*";
		proc_matchfiledel($delshopimage);

		$log_content = "## 분류 삭제 ## - 코드 : {$code} - 코드 : ".str_replace("'","''",$row->code_name)."";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("codedel",$code,"");
		
	}
	
	//상단 카테고리 type 수정
	$cate_len=strlen($likecode)/3;

	if($code_b!='000' && $cate_len>1){
		
		$add_qry='';
		$h_cate=substr($likecode,0,-3);

		if($cate_len=='2'){

			$add_qry=" code_a ";
		}else if($cate_len=='3'){

			$add_qry=" code_a || code_b ";
		}else if($cate_len=='4'){

			$add_qry=" code_a || code_b || code_c ";
		}


		$chk_qry="select count(*) as cnt from tblproductcode where ".$add_qry." like '".$h_cate."%'";
		$chk_res=pmysql_query($chk_qry,get_db_conn());
		$chk_row=pmysql_fetch_object($chk_res);

		if($chk_row->cnt==1){
		
			$qry="update tblproductcode set type=(type||'X') where ".$add_qry."='".$h_cate."'";

			pmysql_query($qry,get_db_conn());
		}
		
	}
	
	pmysql_free_result($result);
	//$onload="<script>parent.CodeDeleteResult('{$code}');alert('선택하신 분류를 삭제하였습니다.');</script>";
	$onload="<script>alert('선택하신 분류를 삭제하였습니다.');parent.document.location.href='recipe_code.php?category=".$likecode."'</script>";
}
echo $onload;
