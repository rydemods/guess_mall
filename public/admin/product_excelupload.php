<?php // hspark
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

@set_time_limit(300);

###################################### 입점기능 사용권한 체크 #######################################
$usevender=setUseVender();

$venderlist=array();
if($usevender) {
	$sql = "SELECT vender,id,com_name FROM tblvenderinfo WHERE disabled=0 AND delflag='N' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}
#####################################################################################################

function CutStringY($str, $start, $end)
{
	$result = substr($str, $start, $end); // 일단 문자열을 자릅니다.
	preg_match('/^([\x00-\x7e]|.{2})*/', $result, $string);	// 뒤에 오는 ?를 없애줍니다..
	return $string[0];
}
		
$imagepath=$Dir.DataDir."shopimages/product/";
$filename="prdtexcelupfile.csv";
@unlink($imagepath.$filename);

if(ord($setcolor)==0) $setcolor="000000";
$rcolor=HexDec(substr($setcolor,0,2));
$gcolor=HexDec(substr($setcolor,2,2));
$bcolor=HexDec(substr($setcolor,4,2));
$quality = "90";

$maxsize=130;
$makesize=130;

$maxsize=$makesize+10;
if(strpos(" ".$_shopdata->etctype,"IMGSERO=Y")) {
	$imgsero="Y";
}

$mode=$_POST["mode"];
$vender=(int)$_POST["vender"];
$code=$_POST["code"];
$upfile=$_FILES["upfile"];

$date1=date("Ym");		// 등록순서데로 순서 저장 필요 변수
$date=date("dHis");		// 등록순서데로 순서 저장 필요 변수

if($mode=="upload" && strlen($upfile['name'])>0 && $upfile['size']>0) {
	########################### TEST 쇼핑몰 확인 ##########################
	//DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	//입점업체 확인
	if($vender>0 && strlen($venderlist[$vender]->vender)<=0) {
		$vender=0;
	}



	$ext = strtolower(pathinfo($upfile['name'],PATHINFO_EXTENSION));
	if($ext=="csv") {
		/*
		$tempfile=@file($upfile['tmp_name']);
		if(count($tempfile)>101) {
			alert_go("1회 등록 가능한 상품수는 100개 까지 입니다.\\n\\n100개 이상일 경우 나누어 등록하시기 바랍니다.");
		}
		*/

		copy($upfile['tmp_name'],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
	} else {
		alert_go("파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 텍스트(TXT) 파일만 등록 가능합니다.");
	}

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = pmysql_query($sql,get_db_conn());
	while ($rows = pmysql_fetch_object($result)) {
		$bridx[$rows->brandname] = $rows->bridx;
	}
	pmysql_free_result($result);

	###################################################################################################
	# 0=>1차 카테고리, 1=>2차 카테고리, 2=>3차 카테고리, 3=>4차 카테고리, 
	# 4=>상품명, 5=>시중가격, 6=>단일판매가격, 7=>구입원가, 8=>제조사, 9=>원산지, 10=>브랜드, 
	# 11=>매입처, 12=>출시일, 13=>적립금(률), 14=>적립률 YN, 15=>재고, 16=>상품진열여부, 17=>설명,
	###################################################################################################

	$i=0;
	$filepath=$imagepath.$filename;
	$fp=fopen($filepath,"r");
	$yy=0;
	while($field=@fgetcsv($fp, 4096, ",")) {
		
		foreach( $field as $fKey=>$fVal ){
			$field[$fKey] = iconv( 'euc-kr', 'utf-8', $fVal );
		}
		
		if($yy++==0) continue;
		if( strlen($field[0])==0 ) {
			continue;
		}
		
		$codes="";
		$count = 0;
		# 상품 카테고리 체크
		$sql = "SELECT code_a, code_b, code_c, code_d, ";
		$sql.= "code_a||code_b||code_c||code_d AS codes, ";
		$sql.= "type, code_name ";
		$sql.= "FROM tblproductcode ";
		$result=pmysql_query($sql,get_db_conn());
		while( $row = pmysql_fetch_object( $result ) ){
			$prCode[] = $row;
		}
		pmysql_free_result( $result );

		foreach($prCode as $v){

			if( strlen( $field[3] ) > 0 ){
				if( $field[3] == $v->code_name ) {
					$codes[] = $v->codes;
				}
			} else if ( strlen( $field[2] ) > 0 ) {
				if( $field[2] == $v->code_name ) {
					$codes[] = $v->codes;
				}
			} else if ( strlen( $field[1] ) > 0 ) {
				if( $field[1] == $v->code_name ) {
					$codes[] = $v->codes;
				}
			} else if ( strlen( $field[0] ) > 0 ) {
				if( $field[0] == $v->code_name ) {
					$codes[] = $v->codes;
				}
			}
		}
		
		if ( count( $codes ) > 0 ) {
			$code=$codes[0];		
		} else {
			$errer_field[$yy]["category"] = 'X';
			$errer_field[$yy]["productname"] = $field[4]."x";
			continue;
		}
		

		$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '{$code}%' ";
		
		$result = pmysql_query($sql,get_db_conn());
		if ($rows = pmysql_fetch_object($result)) {
			if (strlen($rows->maxproductcode)==18) {
				$productcode = ((int)substr($rows->maxproductcode,12))+1;
				$productcode = sprintf("%06d",$productcode);
			} else if($rows->maxproductcode==NULL){
				$productcode = "000001";
			} else {
				$errer_field[$yy]["category"]=$code;
				$errer_field[$yy]["productname"]=$field[0];
				continue;
			}
			pmysql_free_result($result);
		} else {
			$productcode = "000001";
		}
		
		##### 그룹정보 체크
		
		$group_sql = "
		SELECT group_code,group_name FROM tblmembergroup 
		WHERE group_code != '0002'
		ORDER BY group_code 
		";
		$group_result = pmysql_query($group_sql,get_db_conn());
		while($group_row = pmysql_fetch_array($group_result)){
			$group_code[] = $group_row;
		}
		pmysql_free_result($group_result);
		
		#####// 그룹정보 체크

		# 적립금 체크
		if( ( $field[14] == 'Y' || $field[14] == 'N' ) && is_numeric( $field[13] ) ){
			if( $field[14] == 'Y' && ( $field[13] < 100 && $field[13] > 0 ) ){
				$field[13] = (int) $field[13];
			} else if ( $field[14] == 'N' && ( $field[13] > 0 && $field[13] < 999999 ) ){
				$field[13] = sprintf( "%.2f", $field[13] );
			}
		} else {
			$field[13] = 0;
		}
		# 상품코드 체크
		if($i++>0) {
			$productcode = ((int)$productcode)+1;
			$productcode = sprintf("%06d",$productcode);
		}

		//판매가 체크
		if( $field[5]<=0 && !is_numeric( $field[5] ) ) {
			$field[5] = 0;
		}

		//시중가 체크
		if($field[6]<=0 && !is_numeric( $field[6] ) ) {
			$field[6] = 0;
		}

		//구입원가 체크
		if($field[7]<=0 && !is_numeric( $field[7] ) ) {
			$field[7] = 0;
		}

		$date=$date+1;
		$date = sprintf("%08d",$date);

		$curdate=$date1.$date;

		if(ord($field[10])) {
			if($bridx[$field[10]]==0) {
				$sql = "INSERT INTO tblproductbrand(brandname) VALUES('{$field[10]}') RETURNING bridx";
				if($row = @pmysql_fetch_array(pmysql_query($sql,get_db_conn()))) {
					$bridx[$field[10]] = $row[0];
				}
			}
		}
		$in=0;
		foreach($codes as $k){
			if($in==0){
				$maincate="1";
			}else{
				$maincate="0";
			}
			
			$ins_qry="insert into tblproductlink (c_category, c_productcode,c_maincate)values('".$k."','".$code.$productcode."','".$maincate."')";
			pmysql_query($ins_qry);
			$in++;
		}

		$sql = "INSERT INTO tblproduct(
		productcode	,
		productname	,
		sellprice	,
		consumerprice	,
		buyprice	,
		reserve		,
		reservetype	,
		production	,
		madein		,";
		if($bridx[$field[10]])$sql.="	brand		,";
		$sql.="
		model		,
		opendate	,
		quantity	,
		keyword		,
		addcode		,
		maximage	,
		minimage	,
		tinyimage	,
		etctype		,
		deli_price	,
		deli		,
		display		,
		date		,
		vender		,
		regdate		,
		modifydate	,
		content
		) VALUES (
		'".$code.$productcode."', 
		'".str_replace("'","\'",$field[4])."', 
		{$field[5]}, 
		{$field[6]}, 
		{$field[7]}, 
		'{$field[13]}', 
		'{$field[14]}', 
		'".str_replace("'","\'",$field[8])."', 
		'".str_replace("'","\'",$field[9])."', ";
		
		if($bridx[$field[10]]) $sql.="'{$bridx[$field[10]]}', ";
		
		$sql.="
		'".str_replace("'","\'",$field[11])."', 
		'{$field[12]}', 
		{$field[15]}, 
		'', 
		'', 
		'', 
		'', 
		'', 
		'', 
		'0', 
		'N', 
		'{$field[16]}', 
		'{$curdate}', 
		'{$vender}', 
		now(), 
		now(), 
		'".str_replace("'","\'",$field[17])."'
		)";
		@pmysql_query($sql,get_db_conn());		
	}
	
	@fclose($fp);
	
	if($vender>0 && $i>0) {
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='{$vender}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		pmysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);
		setVenderDesignInsert($vender, $code);
	}		

if(count($errer_field)){
	
	function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"') {
		$str = '';
		$escape_char = '\\';
		foreach ($fields as $value) {
			if (strpos($value, $delimiter) !== false ||
			strpos($value, $enclosure) !== false ||
			strpos($value, "\n") !== false ||
			strpos($value, "\r") !== false ||
			strpos($value, "\t") !== false ||
			strpos($value, ' ') !== false) {
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
		}
		$str = rtrim($str,$delimiter);
		$str .= "\n";
		return $str;
	}

	@set_time_limit(300);

	$log_content = "## 상품 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	Header("Content-Disposition: attachment; filename=product_".date("Ymd").".csv");
	Header("Content-type: application/x-msexcel");

	$patten = array ("\r");
	$replace = array ("");

	$field=array("상품명","잘못 등록된 카테고리");
	echo getcsvdata($field);
	
	foreach($errer_field as $y=>$s){
		$field=array();
		$field[]=$errer_field[$y]["productname"];
		$field[]=$errer_field[$y]["category"];
		echo getcsvdata($field);
		flush();
						
	}
	pmysql_free_result($result);
	exit;
	
	//exdebug("error");
}

	alert_go('상품 등록이 완료되었습니다.');
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(f,obj) {
	
	if(obj.getAttribute("ctype")=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_excelupload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_excelupload.ctgr.php?depth=3";
	durl = "product_excelupload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}

var isupload=false;
function CheckForm() {
	if(isupload) {
		alert("######### 현재 상품정보 등록중입니다. #########");
		return;
	}
	/*
	if(document.form1.code.value.length!=12) {
		codelen=document.form1.code.value.length;
		if(codelen==0) {
			alert("상품을 등록할 대분류를 선택하세요.");
			document.form1.code1.focus();
		} else if(codelen==3) {
			alert("상품을 등록할 중분류를 선택하세요.");
			BCodeCtgr.form1.code.focus();
		} else if(codelen==6) {
			alert("상품을 등록할 소분류를 선택하세요.");
			CCodeCtgr.form1.code.focus();
		} else if(codelen==9) {
			alert("상품을 등록할 세부분류를 선택하세요.");
			DCodeCtgr.form1.code.focus();
		} else {
			alert("상품을 등록할 카테고리를 선택하세요.");
			DCodeCtgr.form1.code.focus();
		}
		return;
	}
*/
	isupload=true;
	document.all.uploadButton.style.filter = "Alpha(Opacity=60) Gray";
	document.form1.mode.value="upload";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>상품 엑셀 업로드</span></p></div></div>
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
					<div class="title_depth3">상품정보 일괄 등록</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>다수 상품정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">카테고리별 상품 일괄 등록 처리</div>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>엑셀 등록양식 다운로드</span></th>
					<!--<TD><A HREF="images/sample/product2.csv"><img src="images/btn_down1.gif" border=0 align=absmiddle></A> <span class="font_orange">＊엑셀(CSV)파일을 내려받은 후 예제와 같이 작성합니다.</span></TD>-->
					<TD><A HREF="./product_excelupload_sample.php"><img src="images/btn_down1.gif" border=0 align=absmiddle></A> <span class="font_orange">＊엑셀(CSV)파일을 내려받은 후 예제와 같이 작성합니다.</span></TD>
				</TR>
				<?php if($usevender) {?>

				<TR>
					<th><span>등록상품 입점사 선택</span></th>
					<TD>
					<select name=vender>
						<option value="0">쇼핑몰 본사</option>
						<?php
						while(list($key,$val)=each($venderlist)) {
							echo "<option value=\"{$val->vender}\">{$val->id} ({$val->com_name})</option>\n";
						}
						?>
					</select>
					<span class="font_orange">＊상품이 등록될 입점사를 선택하세요.</span>
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
						<option value="">---- 대 분 류 ----</option>
<?php
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
						$sql.= "WHERE code_b='000' AND code_c='000' ";
						$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$ctype=substr($row->type,-1);
							if($ctype!="X") $ctype="";
							echo "<option value=\"{$row->code_a}\" ctype=\"{$ctype}\">{$row->code_name}";
							if($ctype=="X") echo " (단일분류)";
							echo "</option>\n";
						}
						pmysql_free_result($result);
?>
						</select>
						</td>
						<td></td>
						<td>
						<iframe name="BCodeCtgr" src="product_excelupload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
						</td>
						<td></td>
						<td><iframe name="CCodeCtgr" src="product_excelupload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
						<td></td>
						<td><iframe name="DCodeCtgr" src="product_excelupload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no></iframe></td>
					</tr>
					</table>
                    </div>
					</td>
				</TR>
				<TR>
					<th><span>엑셀파일(CSV) 등록</span></th>
					<TD class="td_con1">
					<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" />
					<input type=file name=upfile style="width:54%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
					</div>
					<span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
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
							<dt><span>상품정보 일괄 등록</span></dt>
							<dd>
							- 상품 등록시 각 상품별 등록처리를 하지 않고, 다수의 상품정보를 엑셀(CSV)파일로 작성하여 일괄 등록하는 기능입니다.<br> <FONT class=font_orange>- 1회 등록 가능한 상품수는 <B>100개 까지 등록 가능</B>하오니 100개 이상일 경우에 나누어 등록하시기 바랍니다.</font>
							</dd>
	
						</dl>
						<dl>
							<dt><span>엑셀(CSV)파일 작성 순서</span></dt>
							<dd>
							- 엑셀파일 작성시 두번 째 라인부터 데이터를 입력하시기 바랍니다. (첫 라인은 필드 설명부분)<br>
							- 아래 형식대로 <FONT class=font_orange><B>엑셀파일 작성 -> 다른이름으로 저장 -> CSV(쉼표로 분리)</B></font> 순으로 저장하시면 됩니다.
							</dd>

						</dl>
						<dl>
							<dt><span>상품정보 일괄등록 방법</span></dt>
							<dd>
							- ① 아래의 형식을 참고로 상품정보 엑셀파일을 작성합니다.<br>
							<span class="font_orange" style="padding-left:10px">----------------------------------------------------- 상품정보 엑셀 형식 ------------------------------------------------------</span><br>
							<span class="font_blue" style="padding-left:25px">상품명, 시중가, 판매가, 구매가, 제조사, 원산지, 브랜드, 모델명, 출시일, 진열코드, 적립금(률), 재고,</span>
							<br>
							<span class="font_blue" style="padding-left:25px">선택사항1, 선택사항1가격, 선택사항2, 진열여부, 큰이미지, 보통이미지, 작은이미지, 이미지자동생성, 상품상세설명</span><br>
							<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>

							<div style="padding-left:30">
							<table border=0 cellpadding=0 cellspacing=0 width=600>
							<col width=100></col>
							<col width=></col>
							<tr>
								<td colspan=2 align=center style="padding-bottom:5">
								<B>상품정보 엑셀 작성 예)</B>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">상품명<FONT class=font_orange>(*)</font></td>
								<td class=td_con1 style="padding-left:5;">
								삼성 코어2 듀오 1.66G 14.1와이드 노트북
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">시중가<FONT class=font_orange>(*)</font></td>
								<td class=td_con1 style="padding-left:5;">
								1580000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">판매가<FONT class=font_orange>(*)</font></td>
								<td class=td_con1 style="padding-left:5;">
								1350000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">구매가</td>
								<td class=td_con1 style="padding-left:5;">
								1290000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">제조사</td>
								<td class=td_con1 style="padding-left:5;">
								삼성전자
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">원산지</td>
								<td class=td_con1 style="padding-left:5;">
								한국
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">모델명</td>
								<td class=td_con1 style="padding-left:5;">
								YP-S3A
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">브랜드</td>
								<td class=td_con1 style="padding-left:5;">
								YEPP
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">출시일</td>
								<td class=td_con1 style="padding-left:5;">
								<?=DATE("Ymd")?> <img width=10 height=0><FONT class=font_orange>(출시년월일, <B>숫자</B>만 입력하세요.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">진열코드</td>
								<td class=td_con1 style="padding-left:5;">
								N123456789
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15" rowspan="3">적립금(률)</td>
								<td class=td_con1 style="padding-left:5;"><b>적립<FONT class=font_orange>금</font></b> : 11000<br>
								<FONT class=font_orange>* 적립금은 0보다 크고 999999 보다 작은 숫자로만 입력해 주세요.<br>
								* 적립금에 맞지 않는 형식일 경우는 숫자 또는 0으로 등록됩니다.</font></td>
							</tr>
							<tr><td height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=td_con1 style="padding-left:5;"><b>적립<FONT color="#0000FF">률</font></b> : 0.01%<br>
								<FONT class=font_orange>* <B>숫자</B>와 특수문자 <B>소수점(.)</B>, <B>페센트(%)</B>만 입력하세요.<br>
								* 적립률 입력시 <B>페센트(%)</B> 필히 입력해 주세요. 미입력시 <B>적립금</B>으로 등록됩니다.<br>
								* 적립률은 0보다 크고 100보다 작은 수로 입력해 주세요.<br>
								* 적립률은 소수점 둘째자리까지만 입력 가능합니다.<br>
								* 적립률에 형식에 일치 하지 않을 경우는 모두 0으로 등록됩니다.<br>
								</font></td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">재고<FONT class=font_orange>(*)</font></td>
								<td class=td_con1 style="padding-left:5;">
								58 <img width=20 height=0><FONT class=font_orange>(<B>공란</B> : 무제한, <B>0</B> : 품절, <B>0이상</B> : 재고수)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">선택사항1</td>
								<td class=td_con1 style="padding-left:5;">
								옵션(RAM)추가 | 512M추가 | 1G추가 <img width=20 height=0><FONT class=font_orange>속성명 | 속성(속성은 "|"로 구분하여 등록)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">선택사항1 가격</td>
								<td class=td_con1 style="padding-left:5;">
								1380000 | 1410000 <img width=20 height=0><FONT class=font_orange>선택사항1 속성에 대한 가격 (판매가는 무시됩니다.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">선택사항2</td>
								<td class=td_con1 style="padding-left:5;">
								색상 | 빨강 | 파랑 | 노랑 <img width=20 height=0><FONT class=font_orange>속성명 | 속성(속성은 "|"로 구분하여 등록)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">상품진열여부<FONT class=font_orange>(*)</font></td>
								<td class=td_con1 style="padding-left:5;">
								Y <img width=20 height=0><FONT class=font_orange>(<B>Y</B> : 상품진열, <B>N</B> : 진열대기)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">큰이미지</td>
								<td class=td_con1 style="padding-left:5;">
								http://www.abc.com/images/product/1000_1.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
								<br>
								<FONT class=font_orange>(상품 이미지가 존재하는 URL을 정확히 입력하시면 됩니다.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">보통이미지</td>
								<td class=td_con1 style="padding-left:5;">
								http://www.abc.com/images/product/1000_2.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
								<br>
								<FONT class=font_orange>(<B>이미지자동생성</B>의 경우 입력하지 않아도 됩니다.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">작은이미지</td>
								<td class=td_con1 style="padding-left:5;">
								http://www.abc.com/images/product/1000_3.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
								<br>
								<FONT class=font_orange>(<B>이미지자동생성</B>의 경우 입력하지 않아도 됩니다.)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">이미지자동생성</td>
								<td class=td_con1 style="padding-left:5;">
								Y <img width=20 height=0><FONT class=font_orange>(<B>Y</B> : 큰이미지로 보통/작은이미지 자동생성, <B>N</B> : 자동생성안함)</font>
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							<tr>
								<td class=table_cell align=right style="padding-right:15">상품상세설명</td>
								<td class=td_con1 style="padding-left:5;">
								상품에 대한 상세설명을 입력하시면 됩니다.
								</td>
							</tr>
							<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
							</table>
							</div>

							<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>
							- ② 상품 등록할 카테고리를 선택 후, 엑셀(CSV)파일을 업로드 합니다.<br>
							- ③ [파일등록] 버튼을 이용하여 업로드 완료 하면 선택된 카테고리에 상품이 등록됩니다.
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
