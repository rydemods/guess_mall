<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$origin_productcode=$_REQUEST["op"];	// 조합상품아이디

if(ord($code)==0) {
	$code=substr($origin_productcode,0,12);
}
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

// 조합상품 권한 체크(조합상품 권한이 없을 경우 개별 상품 정보도 열람 불가)
if(strlen($origin_productcode)==18) {
	$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');self.close();\"></body></html>";exit;
		} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');self.close();\"></body></html>";exit;
		} else if(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');self.close();\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');self.close();\"></body></html>";exit;
	}
	pmysql_free_result($result);
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
}

$productcode=$_REQUEST["np"];	// 개별상품아이디

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

if(strlen($productcode)==18) {
	$sql = "SELECT a.* ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode='{$productcode}' AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_pdata=$row;

		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='{$_pdata->brand}' ";
		$bresult=pmysql_query($sql,get_db_conn());
		$brow=pmysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;

		pmysql_free_result($result);

	} else {
		echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
}

//상품단어 필터링
if(ord($_data->filter)) {
	$arr_filter=explode("#",$_data->filter);
	$detail_filter=$arr_filter[0];
	$filters=explode("=",$detail_filter);
	$filtercnt=count($filters)/2;

	for($i=0;$i<$filtercnt;$i++){
		$filterpattern[$i]="/".str_replace("\0","\\0",preg_quote($filters[$i*2]))."/";
		$filterreplace[$i]=$filters[$i*2+1];
		if(ord($filterreplace[$i])==0) $filterreplace[$i]="***";
	}
}

// 제조회사, 모델명, 브랜드, 출시일, 진열코드, 특이사항 사용자 정의 스펙
$arproduct=array(&$prproduction,&$prmodel,&$prbrand,&$propendate,&$prselfcode,&$praddcode,&$pruserspec0,&$pruserspec1,&$pruserspec2,&$pruserspec3,&$pruserspec4);
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [{$_pdata->productname}]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding-left:5px;padding-right:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td style="padding-left:5px;padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="46%"></col>
				<col width="1%"></col>
				<col width=></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
<?php 
					echo "<tr><td align=\"center\">";
					if(ord($_pdata->maximage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
						$imgsize=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
						if(($imgsize[1]>550 || $imgsize[0]>750) && $multi_img!="I") $imagetype=1;
						else $imagetype=0;
					}
					if(ord($_pdata->minimage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->minimage)) {
						$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->minimage);
						if($width[0]>=300) $width[0]=300;
						else if (ord($width[0])==0) $width[0]=300;
						echo "<img src=\"".$Dir.DataDir."shopimages/product/{$_pdata->minimage}\" border=\"0\" width=\"{$width[0]}\"></td>\n";
					} else {
						echo "<img src=\"{$Dir}images/no_img.gif\" border=\"0\"></td>\n";
					}
					echo "</tr>\n";
					echo "<tr><td height=\"10\"></td></tr><tr><td align=\"center\">";
					echo "</tr><tr><td height=\"5\"></td></tr>\n";
?>
					</table>
					</td>
					<td></td>
					<td valign="top">
					<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
					<tr>
						<td style="padding:8px;" bgcolor="FFFFFF">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td><font color="#FF4C00" style="font-size:15px;letter-spacing:-0.5pt;word-break:break-all;"><b><?=viewproductname($_pdata->productname,$_pdata->etctype,"")?></b></font></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td background="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_titleline.gif" HEIGHT="3"></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td width="100%">
							<table cellpadding="0" cellspacing="0" width="100%">
							<col width="14" align="center"></col>
							<col width="64"></col>
							<col width="13"></col>
							<col width=></col>
<?php 
							if(ord($_pdata->production)) {
								$prproduction ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prproduction.="<td>제조회사</td>\n";
								$prproduction.="<td></td>";
								$prproduction.="<td>{$_pdata->production}</td>\n";
							}
							if(ord($_pdata->model)) {
								$prmodel ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prmodel.="<td>모델명</td>\n";
								$prmodel.="<td></td>";
								$prmodel.="<td>{$_pdata->model}</td>\n";
							}
							if(ord($_pdata->brand)) {
								$prbrand ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prbrand.="<td>브랜드</td>\n";
								$prbrand.="<td></td>";
								if($_data->ETCTYPE["BRANDPRO"]=="Y") {
									$prbrand.="<td>{$_pdata->brand}</td>\n";
								} else {
									$prbrand.="<td>{$_pdata->brand}</td>\n";
								}
							}
							if(ord($_pdata->userspec)) {
								$specarray= explode("=",$_pdata->userspec);
								for($i=0; $i<count($specarray); $i++) {
									$specarray_exp = explode("", $specarray[$i]);
									if(ord($specarray_exp[0]) || ord($specarray_exp[1])) {
										${"pruserspec".$i} ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
										${"pruserspec".$i}.="<td>{$specarray_exp[0]}</td>\n";
										${"pruserspec".$i}.="<td></td>";
										${"pruserspec".$i}.="<td>{$specarray_exp[1]}</td>\n";
									} else {
										${"pruserspec".$i} = "";
									}
								}
							}
							if(ord($_pdata->selfcode)) {
								$prselfcode ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prselfcode.="<td>진열코드</td>\n";
								$prselfcode.="<td></td>";
								$prselfcode.="<td>".$selfcodefont_start.$_pdata->selfcode.$selfcodefont_end."</td>\n";
							}
							if(ord($_pdata->opendate)) {
								$propendate ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$propendate.="<td>출시일</td>\n";
								$propendate.="<td></td>";
								$propendate.="<td>".@substr($_pdata->opendate,0,4).(@substr($_pdata->opendate,4,2)?"-".@substr($_pdata->opendate,4,2):"").(@substr($_pdata->opendate,6,2)?"-".@substr($_pdata->opendate,6,2):"")."</td>\n";
							}
							if(ord($_pdata->addcode)) {
								$praddcode ="<td><IMG SRC=\"{$Dir}images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$praddcode.="<td>특이사항</td>\n";
								$praddcode.="<td></td>";
								$praddcode.="<td>{$_pdata->addcode}</td>\n";
							}

							for($i=0;$i<count($arproduct);$i++) {
								if(ord($arproduct[$i]))
									echo "<tr height=\"22\">{$arproduct[$i]}</tr>\n";
							}
?>	
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_top.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_bg.gif"></td>
					<td><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_bottom.gif" border="0"></td>
				</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding:5px;">
<?php 
						if(ord($detail_filter)) {
							$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
						}

						if (stripos($_pdata->content,"table>")!==false)
							echo "<pre>{$_pdata->content}</pre>";
						else if(strpos($_pdata->content,"</")!==false)
							echo nl2br($_pdata->content);
						else if(stripos($_pdata->content,"img")!==false)
							echo nl2br($_pdata->content);
						else
							echo str_replace(" ","&nbsp;",nl2br($_pdata->content));
?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="50" align="center"><a href="javascript:self.close();"><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_close.gif" border="0"></a></td>
</tr>
</table>
<?=$onload?>
</BODY>
</HTML>
