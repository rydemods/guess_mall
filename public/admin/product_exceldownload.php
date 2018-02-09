<?php // hspark
//header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";

if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"', $pos = false) {
	$str = '';
	$escape_char = '\\';
	$count = 0;
	//exdebug($pos);
	foreach ($fields as $value) {
		
		if (strpos($value, $delimiter) !== false ||
		strpos($value, $enclosure) !== false ||
		strpos($value, "\n") !== false ||
		strpos($value, "\r") !== false ||
		strpos($value, "\t") !== false ||
		strpos($value, ' ') !== false ) {
			$str2 = $enclosure;
			$escaped = 0;
			$len = strlen($value);
			for ($i=0;$i<$len;$i++) {
				if ($value[$i] == $escape_char) {
					$escaped = 1;
				} else if (!$escaped && $value[$i] == $enclosure) {
					$str2 .= $enclosure;
				} else {
					$escaped = 0;
				}
				$str2 .= $value[$i];
			}
			$str2 .= $enclosure;
			$str .= $str2.$delimiter;
		} else {
			$str .= $value.$delimiter;
		}
		/*if ($count == 21){
			exdebug($count);
			exdebug($str);
		}*/
		$count++;
	}
	$str = rtrim($str,$delimiter);
	$str .= "\n";
	return $str;
}

@set_time_limit(300);

###################################### 입점기능 사용권한 체크 #######################################
$usevender=setUseVender();

$venderlist=array();
if($usevender) {
	//$sql = "SELECT vender,id,com_name FROM tblvenderinfo WHERE disabled=0 AND delflag='N' ";
    $sql = "SELECT  a.vender,a.id,a.com_name, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
			WHERE  a.disabled=0 AND a.delflag='N' 
            ORDER BY b.brandname
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}
#####################################################################################################

$mode=$_POST["mode"];
$vender=$_POST["vender"];
$code=$_POST["code"];

if($mode=="download") {

	if($usevender) {
		//입점업체 확인
		if(ord($vender) && $vender != "0") {
			if($vender == "shop") {
				$qry .= "AND vender = '0' ";
			} else if(ord($venderlist[$vender]->vender)){
			 	$qry .= "AND vender = '{$vender}' ";
			}
		}
	}

	if($code>0) {
		$code_aBCD = str_pad($code, 12, "0", STR_PAD_RIGHT);

		//분류 확인
		$sql = "SELECT type FROM tblproductcode ";
		$sql.= "WHERE code_a='".substr($code_aBCD,0,3)."' AND code_b='".substr($code_aBCD,3,3)."' ";
		$sql.= "AND code_c='".substr($code_aBCD,6,3)."' AND code_d='".substr($code_aBCD,9,3)."' ";
		$result=pmysql_query($sql,get_db_conn());
		if(@pmysql_num_rows($result)!=1) {
			alert('상품을 다운로드할 분류 선택이 잘못되었습니다.');
			exit;
		}
		pmysql_free_result($result);
		$qry .= "AND b.c_category LIKE '{$code}%' ";
	}

	if(ord($qry)) {
		$qry = "WHERE".substr($qry,3);
	}

	$connect_ip = $_SERVER['REMOTE_ADDR'];
	$curdate = date("YmdHis");

	$sql = "SELECT COUNT(*) as cnt FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
	$sql.= $qry;
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	if ($row->cnt>=5000) {
		$temp = "삼일";
		$okdate = date("Ymd",strtotime('+3 day'));
	} else {
		$temp = "하루";
		$okdate=date("Ymd");
	}
	pmysql_free_result($result);

	$log_content = "## 상품 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	Header("Content-Disposition: attachment; filename=product_".date("Ymd").".csv");
	Header("Content-type: application/x-msexcel;");
	header("Content-Description: PHP4 Generated Data" );
	
	$sql = "SELECT code_a||code_b||code_c||code_d as code, type,code_name FROM tblproductcode ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$code_name[$row->code] = iconv( 'utf-8', 'euc-kr', $row->code_name);
	}
	pmysql_free_result($result);

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
		$brandname[$row->bridx] = $row->brandname;
	}
	pmysql_free_result($result);

	$patten = array ("\r");
	$replace = array ("");

	$sql = "SELECT 
	*,
	a.maximage as maximageurl,
	a.minimage as minimageurl,
	a.tinyimage as tinyimageurl
	FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
	$sql.= $qry;
	$sql.= " ORDER BY productcode "; 
	//$sql.= " and a.productcode = '015002000000000006' ORDER BY productcode ";
	//exdebug($sql);
	$result = pmysql_query($sql,get_db_conn());

	$field=array(
		iconv( 'utf-8', 'euc-kr', "1차카테고리" ),
		iconv( 'utf-8', 'euc-kr',"2차카테고리" ),
		iconv( 'utf-8', 'euc-kr',"3차카테고리" ),
		//iconv( 'utf-8', 'euc-kr',"4차카테고리" ),
		iconv( 'utf-8', 'euc-kr',"상품코드" ),
		iconv( 'utf-8', 'euc-kr',"상품명" ),
		iconv( 'utf-8', 'euc-kr',"매입처" ),
		iconv( 'utf-8', 'euc-kr',"원산지" ),
		iconv( 'utf-8', 'euc-kr',"브랜드" ),
		iconv( 'utf-8', 'euc-kr',"매입처" ),
		iconv( 'utf-8', 'euc-kr',"출시일" ),
		iconv( 'utf-8', 'euc-kr',"진열코드" ),
		iconv( 'utf-8', 'euc-kr',"적립금(률)" ),
		iconv( 'utf-8', 'euc-kr',"재고" ),
		iconv( 'utf-8', 'euc-kr',"옵션1" ),
		iconv( 'utf-8', 'euc-kr',"옵션2" ),
		iconv( 'utf-8', 'euc-kr',"구분" ),
		iconv( 'utf-8', 'euc-kr',"등록일" ),
		iconv( 'utf-8', 'euc-kr',"상품진열여부" ),
		iconv( 'utf-8', 'euc-kr',"설명" ),
		iconv( 'utf-8', 'euc-kr',"정가" ),
		iconv( 'utf-8', 'euc-kr',"판매가" ),
		iconv( 'utf-8', 'euc-kr',"구매가" )
	);

	echo getcsvdata($field);

	while ($row=pmysql_fetch_object($result)) {
		$field=array();

		$code_a = substr($row->c_category,0,3);
		$code_b = substr($row->c_category,3,3);
		$code_c = substr($row->c_category,6,3);
		$code_d = substr($row->c_category,9,3);
		$code = substr($row->productcode,0,12);
		if($code_b=="000") $code_b="";
		if($code_c=="000") $code_c="";
		if($code_d=="000") $code_d="";
		$field[]=$code_name[$code_a."000000000"];
		if(ord($code_name[$code_a.$code_b."000000"])==0) $field[]=iconv( 'utf-8', 'euc-kr', "2차카테고리없음" );
		else $field[]=$code_name[$code_a.$code_b."000000"];
		if(ord($code_name[$code_a.$code_b.$code_c."000"])==0) $field[]=iconv( 'utf-8', 'euc-kr', "3차카테고리없음" );
		else $field[]=$code_name[$code_a.$code_b.$code_c."000"];
		//if(ord($code_name[$code_a.$code_b.$code_c.$code_d])==0) $field[]=iconv( 'utf-8', 'euc-kr', "4차카테고리없음" );
		//else $field[]=$code_name[$code_a.$code_b.$code_c.$code_d];
		$option1 = '';
		$option2 = '';
		$option1 = explode( ',', $row->option1 );
		$option2 = explode( ',', $row->option2 );

		$field[]="=\"$row->productcode\"";
		$field[]=iconv( 'utf-8', 'euc-kr', $row->productname);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->production);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->madein);
		$field[]=iconv( 'utf-8', 'euc-kr', $brandname[$row->brand]);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->model);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->opendate);
		$field[]=iconv( 'utf-8', 'euc-kr', $row->selfcode);
		$field[]=($row->reservetype!="Y"?$row->reserve:$row->reserve."%");
		$field[]=$row->quantity;
		$field[]=iconv( 'utf-8', 'euc-kr', $option1[0] );
		$field[]=iconv( 'utf-8', 'euc-kr', $option2[0] );
		$field[]=iconv( 'utf-8', 'euc-kr', str_replace(",","",$row->addcode));
		$field[]=substr($row->date,0,8);
		$field[]=$row->display;
		$content = str_replace($patten,$replace,$row->content);
		$field[]=iconv( 'utf-8', 'euc-kr', $content);
		$field[]=$row->consumerprice;
		$field[]=$row->sellprice;
		$field[]=$row->buyprice;
		
		echo(getcsvdata($field,',','"',true));
		flush();
	}
	pmysql_free_result($result);
	exit;
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_exceldownload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_exceldownload.ctgr.php?depth=3";
	durl = "product_exceldownload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}
function CheckForm() {
	document.form1.mode.value="download";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>상품 엑셀 다운로드</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 엑셀 다운로드</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품정보 Excel(.csv)형식으로 다운로드할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">카테고리별 상품 엑셀 다운로드</div>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<?php if($usevender) {?>

				<TR>
					<th><span>등록상품 브랜드 선택</span></th>
					<TD>
					<select name=vender>
						<option value="0">쇼핑몰 전체</option>
						<option value="shop">쇼핑몰 본사</option>
						<?
						while(list($key,$val)=each($venderlist)) {
							echo "<option value=\"{$val->vender}\">{$val->brandname}</option>\n";
						}
						?>
					</select>
					<span class="font_orange">＊다운로드할 상품 브랜드를 선택하세요.</span>
					</TD>
				</TR>
				<?php }?>

				<TR>
					<th><span>상품 카테고리 선택</span></th>
					<TD>
                    <div class="table_none">
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=></col>
					<tr>
						<td>
						<select name="code1" style=width:145 onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
						<option value="">--- 대 분 류 전 체 ---</option>
<?
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_b='000' AND code_c='000' ";
						$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$ctype=substr($row->type,-1);
							if($ctype!="X") $ctype="";
							echo "<option value=\"{$row->code_a}\" ctype='{$ctype}'>{$row->code_name}";
							if($ctype=="X") echo " (단일분류)";
							echo "</option>\n";
						}
						pmysql_free_result($result);
?>
						</select>
						</td>
						<td></td>
						<td>
						<iframe name="BCodeCtgr" src="product_exceldownload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
						</td>
						<td></td>
						<td><iframe name="CCodeCtgr" src="product_exceldownload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
						<td></td>
						<td><iframe name="DCodeCtgr" src="product_exceldownload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no style="display:none;"></iframe></td>
					</tr>
					</table>
                    </div>
					</td>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_filedown.gif" id="downloadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>

			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품정보 엑셀 다운로드</span></dt>
							<dd>
							- 상품정보 엑셀 다운로드 파일은 확장자 CSV 로 저장됩니다.<Br>
							- 상품정보 엑셀 다운로드할 경우 등록상품 브랜드 및 상품 카테고리 별로 선택하여 다운로드 가능합니다.
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
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
</table>
<?=$onload?>
<?php
include("copyright.php");
