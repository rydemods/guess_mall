<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

if($_GET['category_data']){
	$arrCategoryData = explode("|", $_GET['category_data']);
	
	$_REQUEST["code_a"] = $arrCategoryData[0];
	$_REQUEST["code_b"] = $arrCategoryData[1];
	$_REQUEST["code_c"] = $arrCategoryData[2];
	$_REQUEST["code_d"] = $arrCategoryData[3];
}

$mode=$_POST["mode"];
$keyword=$_POST["keyword"];
$s_check=$_POST["s_check"];
$display=$_POST["display"];
$vperiod=$_POST["vperiod"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_POST["search_end"];
$search_start=$_POST["search_start"];
$sellprice_min=$_POST["sellprice_min"];
$sellprice_max=$_POST["sellprice_max"];

if($keyword=="상품명 상품코드")$keyword="";

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$likecodeExchange = $code_a."|".$code_b."|".$code_c."|".$code_d;

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));


$checked["display"][$display] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["check_vperiod"][$vperiod] = "checked";

$imagepath=$Dir.DataDir."shopimages/product/";

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


?>


<link rel="stylesheet" href="style.css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function ProductInfo(prcode,popuptype,category_data) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	document.form_register.category_data.value=category_data;
	if (popup=="YES") {
		document.form_register.action="product_register.add.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

function ProductDel(prcode){
	if(confirm("선택하신 상품을 정말로 삭제하시겠습니까?")){
		document.form1.mode.value="delete";
		document.form1.prcode.value=prcode;
		document.form1.submit();
	}
}

function Productcopy(prcode){
	if(confirm("선택하신 상품을 동일하게 한개 더 생성하시겠습니까?")){
		document.form1.mode.value="copy";
		document.form1.prcode.value=prcode;
		document.form1.submit();
	}
}

function registeradd(){
	document.form_register.code.value='';
	document.form_register.prcode.value='';
	document.form_register.popup.value="NO";
	//document.form_register.code.value="004002000000";
	document.form_register.action="product_register.set.php";
	document.form_register.target="";
	document.form_register.submit();
}


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

$(document).ready(function(){
	$(".CLS_couponAccept").click(function(){
		var thisText = "";
		var thisVal = "";
		var cateA = $("select[name='code_a']").val();
		var cateB = $("select[name='code_b']").val();
		var cateC = $("select[name='code_c']").val();
		var cateD = $("select[name='code_d']").val();
		if (cateA)
		{
			if(cateA){
				thisText = $("select[name='code_a'] option:selected").text();
				thisVal = cateA;
			}
			if(cateB){
				if(cateB != '000') thisText = thisText + " > " + $("select[name='code_b'] option:selected").text();
				if(cateB != '000') thisVal = thisVal+cateB;
			}
			if(cateC){
				if(cateC != '000') thisText = thisText + " > " + $("select[name='code_c'] option:selected").text();
				if(cateC != '000') thisVal = thisVal+cateC;
			}
			if(cateD){
				if(cateD != '000') thisText = thisText + " > " + $("select[name='code_d'] option:selected").text();
				if(cateD != '000') thisVal = thisVal+cateD;
			}

			var set_productcode	= $("#ID_productLayer input[name='set_productcode[]']", opener.document).length;
			var inProduct	= 0;
			if (set_productcode > 0)
			{
				$("#ID_productLayer input[name='set_productcode[]']", opener.document).each(function(){
					var ex_set_alt			= $(this).val();
					var ex_set_productA	= ex_set_alt.substr(0,3);
					var ex_set_productB	= ex_set_alt.substr(3,3);
					var ex_set_productC	= ex_set_alt.substr(6,3);
					var ex_set_productD	= ex_set_alt.substr(9,3);
					//alert(ex_set_product);
					
					if (cateA && !cateB && !cateC && !cateD) {	
						//alert('1');					
						if (ex_set_productA == cateA) {
							//alert('2');					
							if (ex_set_productB == '' || ex_set_productB == '000') {							
								//alert('3');					
								inProduct	= inProduct + 1;
							} else {						
								//alert('4');					
								$(this).parent().remove();
							}
						}
					}
					
					if (cateA && cateB && !cateC && !cateD) {						
						if (ex_set_productA == cateA) {
							if (ex_set_productB == '' || ex_set_productB == '000') {
								inProduct	= inProduct + 1;
							} else {							
								if (ex_set_productB == cateB) {								
									if (ex_set_productC == '' || ex_set_productC == '000') {
										inProduct	= inProduct + 1;
									} else {
										$(this).parent().remove();
									}
								}
							}
						}
					}
					
					if (cateA && cateB && cateC && !cateD) {						
						if (ex_set_productA == cateA) {
							if (ex_set_productB == '' || ex_set_productB == '000') {
								inProduct	= inProduct + 1;
							} else {							
								if (ex_set_productB == cateB) {								
									if (ex_set_productC == '' || ex_set_productC == '000') {
										inProduct	= inProduct + 1;
									} else {															
										if (ex_set_productC == cateC) {								
											if (ex_set_productD == '' || ex_set_productD == '000')
											{
												inProduct	= inProduct + 1;
											} else {
												$(this).parent().remove();
											}
										}
									}
								}
							}
						}
					}
					
					if (cateA && cateB && cateC && cateD) {						
						if (ex_set_productA == cateA) {
							if (ex_set_productB == '' || ex_set_productB == '000') {
								inProduct	= inProduct + 1;
							} else {							
								if (ex_set_productB == cateB) {								
									if (ex_set_productC == '' || ex_set_productC == '000') {
										inProduct	= inProduct + 1;
									} else {															
										if (ex_set_productC == cateC) {								
											if (ex_set_productD == '' || ex_set_productD == '000')
											{
												inProduct	= inProduct + 1;
											} else {																									
												if (ex_set_productD == cateD) {
													inProduct	= inProduct + 1;
												}
											}
										}
									}
								}
							}
						}
					}
				});
			}

			if (inProduct == 0)
			{
				thisVal2	= cateA+"|"+cateB+"|"+cateC+"|"+cateD;
				var tempHtml = $("#ID_productLayer", opener.document).html();
				$("#ID_productLayer", opener.document).html(tempHtml+"<div style='padding:5px 0px;'><a href=\"javascript:;\" onClick=\"javascript:$(this).parent().remove();\"><img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' /></a>&nbsp;&nbsp;"+thisText+"<input type = 'hidden' name ='set_productcode[]' value = '"+thisVal+"'></div>");
			}
			
			$("input[name='productcode']", opener.document).val("CATEGORY");
		}
	})
})
</script>
<!-- 라인맵 -->
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<input type=hidden name=mode>
	<input type=hidden name=prcode>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">			
	<tr>
		<td>
			<div class="title_depth2"></div>
			
			<!-- 테이블스타일01 -->
			<div class="table_style01 pt_20">
				<table cellpadding=0 cellspacing=0 border=0 width=100%>
					<tr>
						<th><span>카테고리 검색</span></th>
						<td>
						<?
							$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
							$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
							$i=0;
							$ii=0;
							$iii=0;
							$iiii=0;
							$strcodelist = "";
							$strcodelist.= "<script>\n";
							$result = pmysql_query($sql,get_db_conn());
							$selcode_name="";

							while($row=pmysql_fetch_object($result)) {
								$strcodelist.= "var clist=new CodeList();\n";
								$strcodelist.= "clist.code_a='{$row->code_a}';\n";
								$strcodelist.= "clist.code_b='{$row->code_b}';\n";
								$strcodelist.= "clist.code_c='{$row->code_c}';\n";
								$strcodelist.= "clist.code_d='{$row->code_d}';\n";
								$strcodelist.= "clist.type='{$row->type}';\n";
								$strcodelist.= "clist.code_name='{$row->code_name}';\n";
								if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
									$strcodelist.= "lista[{$i}]=clist;\n";
									$i++;
								}
								if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
									if ($row->code_c=="000" && $row->code_d=="000") {
										$strcodelist.= "listb[{$ii}]=clist;\n";
										$ii++;
									} else if ($row->code_d=="000") {
										$strcodelist.= "listc[{$iii}]=clist;\n";
										$iii++;
									} else if ($row->code_d!="000") {
										$strcodelist.= "listd[{$iiii}]=clist;\n";
										$iiii++;
									}
								}
								$strcodelist.= "clist=null;\n\n";
							}
							pmysql_free_result($result);
							$strcodelist.= "CodeInit();\n";
							$strcodelist.= "</script>\n";

							echo $strcodelist;


							echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
							echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
							echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
							echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<select name=code_d style=\"width:170px;\">\n";
							echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
							echo "</select>\n";

							echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
						?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr><td height="50" align = 'center'><img src = '../admin/images/botteon_save.gif' class = 'hand CLS_couponAccept'></td></tr>
	</table>
</form>