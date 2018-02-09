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
		copy($upfile['tmp_name'],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
	} else {
		alert_go("파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 텍스트(TXT) 파일만 등록 가능합니다.");
	}	

	$i=0;
	$filepath=$imagepath.$filename;
	$fp=fopen($filepath,"r");
	$yy=0;
	while($field=@fgetcsv($fp, 4096, ",")) {
		if($yy++==0) continue;

		if(ord($field[0])==0) {
			continue;
		}

		$arrayPropVal = array();
		for($pNum=28; $pNum<41; $pNum++){
			$arrayPropVal[] = $field[$pNum];
		}
		$strPropVal = implode("||", $arrayPropVal);
		
		if(strlen($strPropVal)>40){
			$sql = "UPDATE tblproduct SET sabangnet_prop_val = '".$_POST['prop_type']."||".$strPropVal."' WHERE productname = '".$field[6]."'";
			$result = pmysql_query($sql,get_db_conn());
		}
	}
	@fclose($fp);
	
	alert_go('제휴몰 정보고시 정보 수정이 완료되었습니다.');
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
						<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
							<input type=hidden name=mode>
							<input type="hidden" name="code" value="">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td height="8"></td>
								</tr>
								<tr>
									<td>
										<!-- 페이지 타이틀 -->
										<div class="title_depth3">제휴몰 상품정보 일괄 등록</div>
									</td>
								</tr>
								<tr>
									<td>
										<!-- 소제목 -->
										<div class="title_depth3_sub"><span>다수 상품정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</span></div>
										<div class="title_depth3_sub"><span style = 'color:red;'>엑셀(CSV) 파일만 등록 가능합니다.</span></div>
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
								<tr>
									<td>
									<div class="table_style01">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>				
									<TR>
										<th>
											<span>엑셀파일(CSV) 등록</span>
											<select name = 'prop_type'>
												<option value = '002'>신발</option>
												<option value = '003'>가방</option>
											</select>
										</th>
										<TD class="td_con1">
											<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
											<div class="file_input_div">
												<input type="button" value="찾아보기" class="file_input_button" />
												<input type=file name=upfile style="width:54%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
											</div>
										</TD>
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
								<tr>
									<td height="50"></td>
								</tr>
							</table>
						</form>
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
