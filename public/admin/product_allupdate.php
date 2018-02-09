<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$mode=$_POST["mode"];
$gotopage = $_POST["gotopage"];
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

$sel_season         = $_REQUEST["sel_season"];

$sel_vender = $_POST['sel_vender'];             // 브랜드 지정
$s_brand_keyword = $_POST['s_brand_keyword'];   // 브랜드명으로 검색

$listnum=(int)$_REQUEST["listnum"];
if(!$listnum){
	//$listnum = (int)$_REQUEST["listnum_select"];
	$listnum = 20;
}

if($keyword=="상품명 상품코드")$keyword="";

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;


$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$checked["display"][$display] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["check_vperiod"][$vperiod] = "checked";


$aproductcode=(array)$_POST["aproductcode"];

$aassembleproduct=(array)$_POST["aassembleproduct"];
$aassembleuse=(array)$_POST["aassembleuse"];

$aproductname=(array)$_POST["aproductname"];
$aproductname2=(array)$_POST["aproductname2"];
$aproduction=(array)$_POST["aproduction"];
$aproduction2=(array)$_POST["aproduction2"];
$aconsumerprice=(array)$_POST["aconsumerprice"];
$aconsumerprice2=(array)$_POST["aconsumerprice2"];
$abuyprice=(array)$_POST["abuyprice"];
$abuyprice2=(array)$_POST["abuyprice2"];
$asellprice=(array)$_POST["asellprice"];
$asellprice2=(array)$_POST["asellprice2"];
$areserve=(array)$_POST["areserve"];
$areserve2=(array)$_POST["areserve2"];
$areservetype=(array)$_POST["areservetype"];
$areservetype2=(array)$_POST["areservetype2"];
$aquantity=(array)$_POST["aquantity"];
$aquantity2=(array)$_POST["aquantity2"];
$adisplay=(array)$_POST["adisplay"];
$adisplay2=(array)$_POST["adisplay2"];

if ($mode=="update" && count($aproductcode)>0) {

	$movecount=0;
	$update_ymd = date("YmdH");
	$update_ymd2 = date("is");
	$displist=array();
	for($i=0;$i<count($aproductcode);$i++) {

        // 일괄 수정시 기간설정되어있는 상품이 있는지 체크
        $is_check = CheckDupProcut($aproductcode[$i]);
        if(!$is_check) {
            msg($aproductcode[$i]." ".$aproductname[$i]."상품은 \\r\\n\\r\\n기간설정중인 상품이라 일괄수정되지 않습니다.");
            continue;
        }

		if (ord($aproductcode[$i]) && ($aproductname[$i]!=$aproductname2[$i] || $aproduction[$i]!=$aproduction2[$i] || $aconsumerprice[$i]!=$aconsumerprice2[$i] || $abuyprice[$i]!=$abuyprice2[$i] || $asellprice[$i]!=$asellprice2[$i] || $areserve[$i]!=$areserve2[$i] || $areservetype[$i]!=$areservetype2[$i] || $aquantity[$i]!=$aquantity2[$i] || $adisplay[$i]!=$adisplay2[$i]) && ord($asellprice[$i]) && ord($areserve[$i]) && ord($aproductname[$i])) {
			if (is_numeric($asellprice[$i]) && is_numeric($areserve[$i])) {   #숫자인지 검사
			    $aquantity[$i]=trim($aquantity[$i]);
				if (ord($aquantity[$i])==0) 
					$quantity="NULL";
				else if (is_numeric($aquantity[$i]))
					$quantity = $aquantity[$i]+0;
				if (ord($abuyprice[$i])==0) 
					$abuyprice[$i]="0";
				if (ord($areserve[$i])==0) 
					$areserve[$i]=0;
				if($areservetype[$i]!="Y") {
					$areservetype[$i]="N";
				}

//				$productname = str_replace("\\\\'","''",$aproductname[$i]);
//				$production = str_replace("\\\\'","''",$aproduction[$i]);

				$productname = str_replace("'","''",$aproductname[$i]);
				$production = str_replace("'","''",$aproduction[$i]);

				$sql = "UPDATE tblproduct SET ";
				$sql.= "productname			= '{$productname}', ";
				$sql.= "sellprice			= {$asellprice[$i]}, ";
				$sql.= "consumerprice		= {$aconsumerprice[$i]}, ";
				$sql.= "buyprice			= {$abuyprice[$i]}, ";
				$sql.= "reserve				= '{$areserve[$i]}', ";
				$sql.= "reservetype			= '{$areservetype[$i]}', ";
				$sql.= "production			= '{$production}', ";
				$sql.= "quantity			= {$quantity}, ";
				$sql.= "display				= '{$adisplay[$i]}' ";
				$sql.= "WHERE productcode='{$aproductcode[$i]}' ";
				pmysql_query($sql,get_db_conn());

				if($asellprice[$i]!=$asellprice2[$i] && $aassembleuse[$i]!="Y") {
					if(ord($aassembleproduct[$i])) {
						$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode IN ('".str_replace(",","','",$aassembleproduct[$i])."') ";
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
				}

				if($adisplay[$i]!=$adisplay2[$i]) {
					$displist[]=$aproductcode[$i];
				}

				$movecount++;

				$update_date = $update_ymd.$update_ymd2;
				$log_content = "## 상품일괄수정 ## - 상품코드: {$aproductcode[$i]} 가격: {$asellprice[$i]} 소비자가 : {$aconsumerprice[$i]}  구입가 : {$abuyprice} 진열: {$adisplay[$i]} 수량: $quantity 적립금 : ".$areserve[$i];
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content,$update_date);
				$update_ymd2++;
			}
		}
	}

	//진열 업데이트 배열 확인 후 입점업체 상품수 업데이트
	$prcodelist = implode(',',$displist);
	if(ord($prcodelist)) {
		$prcodelist = str_replace(",","','",$prcodelist);

		$arrvender=array();
		$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('{$prcodelist}') AND vender>0 ";
		$sql.= "GROUP BY vender ";
		$p_result=pmysql_query($sql,get_db_conn());
		while($p_row=pmysql_fetch_object($p_result)) {
			$arrvender[]=$p_row->vender;
		}
		pmysql_free_result($p_result);

		for($yy=0;$yy<count($arrvender);$yy++) {
			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);
		}
	}

	if ($movecount!=0) {
		$onload="<script>window.onload=function(){alert('{$movecount} 건의 상품정보가 수정되었습니다.');}</script>";
	}
}

// 즉시 업데이트시, 기간 설정된 내역과 중복되는 상품이 있는지 체크
function CheckDupProcut($productcode) {
    
    $sql = "select  count(*)  
            from    tblbatchapplylog 
            where   productcode = '".$productcode."'  
            and	    pidx > 0 
            and     (start_date <= '".date("Ymd")."' and end_date >= '".date("Ymd")."') 
            ";
    list($cnt) = pmysql_fetch($sql);
    //exdebug($sql);

    if($cnt > 0) return false;
    else return true;
}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function listnumSet(listnum){
	document.form1.listnum.value=listnum.value;
	document.form1.submit();
}

function resetBrandSearchWord(obj) {
    if ( $(obj).val() == "" ) {
        $("#s_brand_keyword").attr("disabled", false).val("").focus();
    } else {
        $("#s_brand_keyword").attr("disabled", true);
    }
}

function CheckBatchForm() {

    // 선택된 상품이 하나 이상인지 체크
    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
        return;
    }

    // 일괄적용대상 선택 체크
    if( $("#sel_mode").val() == "") {
        alert("일괄적용 대상을 선택해 주세요.");
        return;
    }

    // form에 hidden 추가
    var frm = $("#form_batch_apply")[0];

    // 일단 초기화
    $(frm).children().remove();

    var arrSelectedProd = Array();
    $("input[name='idx[]']:checked").each( function() {
        arrSelectedProd.push($(this).val());

        $(frm).append("<input type='hidden' name='prod_list[]' value='" + $(this).val() + "'>");
    });

    var url = "";
    var title = "";
    var status  = "toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no,width=1000,height=800,top=0,left=20"; 

    if ( $("#sel_mode").val() == "1" ) {
        url     = "product_allupdate_popup_consumer.php";
        title   = "정가 일괄적용";
        var status  = "toolbar=no,directories=no,scrollbars=no,resizable=no,status=no,menubar=no,width=600,height=160,top=0,left=20"; 
    }

    if ( $("#sel_mode").val() == "2" ) {
        url     = "product_allupdate_popup.php";
        title   = "판매가 일괄적용";
    }

    if ( $("#sel_mode").val() == "3" ) {
        url     = "product_allupdate_popup_rate.php";
        title   = "마진율 일괄적용";
    }

    if ( $("#sel_mode").val() == "4" ) {
        url     = "product_allupdate_popup_icon.php";
        title   = "아이콘 일괄적용";
    }

    if ( $("#sel_mode").val() == "5" ) {
        url     = "product_allupdate_popup_tag.php";
        title   = "TAG 일괄적용";
        var status  = "toolbar=no,directories=no,scrollbars=yes,resizable=no,status=no,menubar=no,width=1000,height=800,top=0,left=20"; 
    }

    window.open("", title,status); 

    frm.target  = title;     
    frm.action  = url;      
    frm.method  = "post";
    frm.submit();    
}

function CheckForm() {
	try {
		if (typeof(document.form1["aproductcode[]"])!="object") {
			alert("수정할 상품이 존재하지 않습니다.");
			return;
		}
		
		var i=0;
		while(true) {
			if(document.getElementById("areserve"+i) && document.getElementById("areservetype"+i)) {
				if (document.getElementById("areserve"+i).value.length>0) {
					if(document.getElementById("areservetype"+i).value=="Y") {
						if(isDigitSpecial(document.getElementById("areserve"+i).value,".")) {
							alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
						
						if(getSplitCount(document.getElementById("areserve"+i).value,".")>2) {
							alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
							document.getElementById("areserve"+i).focus();
							return;
						}

						if(getPointCount(document.getElementById("areserve"+i).value,".",2)) {
							alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
							document.getElementById("areserve"+i).focus();
							return;
						}

						if(Number(document.getElementById("areserve"+i).value)>100 || Number(document.getElementById("areserve"+i).value)<0) {
							alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
					} else {
						if(isDigitSpecial(document.getElementById("areserve"+i).value,"")) {
							alert("적립금은 숫자로만 입력하세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
					}
				}
				i++;
			} else {
				break;
			}
		}
	} catch (e) {
		return;
	}
	if(confirm("상품정보를 수정 하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
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
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}



function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	if (popup=="YES") {
		document.form_register.action="product_register.set.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=1000,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}



function chkFieldMaxLenFunc(thisId,reserveTypeID) {
	if(document.getElementById(reserveTypeID)) {
		if (document.getElementById(reserveTypeID).value=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }

		if(document.getElementById(thisId)) {
			if (document.getElementById(thisId).value.bytes() > max) {
				alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
				document.getElementById(thisId).value = document.getElementById(thisId).value.cut(max);
				document.getElementById(thisId).focus();
			}
		}
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='idx[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='idx[]']").attr("checked", false);
    }
}

function ProductCheckExcel(){
	// 선택된 상품이 하나 이상인지 체크
    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
        return;
    }
	var productcode="";
	$("input[name='idx[]']:checked").each( function() {
       
	   productcode+=$(this).val()+"||";
	   
    });
	$("input[name='product_code_all']").val(productcode);

	document.product_excel.action = 'product_allupdate_excel.php';
	//document.form1.target = "HiddenFrame";
	document.product_excel.submit();
}
function submit_data() {
    if ( $("#csv_file").val() == "" ) {
        alert("csv파일을 선택해 주세요.");
        return false;
    }
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;상품 일괄관리 &gt; <span>상품 일괄 간편수정</span></p></div></div>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 일괄 간편수정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 등록된 상품의 가격을 포함한 적립금, 수량 등을 일괄 수정할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name=searchtype value="<?=$searchtype?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=listnum value="<?=$listnum?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td>
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품검색</span></th>
							<td>
								<!-- 
								<input class="input_bd_st01" type="text" name="keyword" onfocus="this.value=''; this.style.color='#000000'; this.style.textAlign='left';" <?=$keyword?"value=".$keyword:"style=\"color:'#bdbdbd';text-align:center;\" value=\"상품명 상품코드\""?>>
								 -->
								<textarea rows="2" cols="10" class="w200" name="keyword" onfocus="this.value='';  style="resize:none;vertical-align:middle;"><?=$keyword?></textarea>
							</td>
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
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
						<tr>
							<th><span>등록일</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</tr>
						<tr>
							<th><span>상품금액별 검색</span></th>
							<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						</tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열&nbsp;&nbsp; <input type="radio" name="display" value="3" <?=$checked["display"]['3']?>/> 가등록</td>
						</tr>
                        <TR>
                            <th><span>브랜드검색</span></th>
                            <td><select name=sel_vender class="select" onChange="javascript:resetBrandSearchWord(this);">
                                <option value="">==== 전체 ====</option>
        <?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->bridx}\"";
                            if($sel_vender==$val->bridx) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }
        ?>
                                </select> 
                                <input class="w200" type="text" id="s_brand_keyword" name="s_brand_keyword" value="<?=$s_brand_keyword?>" <?php if($sel_vender) echo "disabled";?>>
                            </td>
                        </TR>
                        <TR>
						<th><span>시즌 검색</span></th>
						<td><select name=sel_season class="select">
							<option value="">==== 전체 ====</option>
	<?php
							// 20170410 시즌검색 추가
							$sql = "SELECT SEASON_YEAR,SEASON,SEASON_KOR_NAME,SEASON_ENG_NAME FROM tblproductseason ORDER BY NO DESC";
							$result = pmysql_query($sql,get_db_conn());
							while($row = pmysql_fetch_object($result)){
								echo "<option value=\"{$row->season_year},{$row->season}\"";
								if($sel_season=="{$row->season_year},{$row->season}") echo " selected";
								echo ">{$row->season_eng_name}</option>\n";
							}
	?>
							</select>
						</td>
					</TR>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
					<div class="table_style02">

                    <div class="btn_right">
                        <select name="listnum_select" onchange="javascript:listnumSet(this)">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
							<option value="1000" <?if($listnum==1000)echo "selected";?>>1000개씩 보기</option>
                        </select>
                    </div>


					<table width=100% cellpadding=0 cellspacing=0>
						<colgroup>
							<?php
							$colspan=14;
							if($vendercnt>0) $colspan++;
							?>
							<col width=40></col>
							<col width=40></col>
							<?php if($vendercnt>0){?>
							<col width=60></col>
							<?php }?>
							<col width=60></col>
							<col width=50></col>
							<col width=></col>
							<col width=105></col>
							<col width=73></col>
							<col width=73></col>
							<col width=73></col>
							<col width=89></col>
							<col width=38></col>
							<col width=43></col>
							<col width=43></col>
						</colgroup>
						<tr>
							<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
							<th>번호</th>
							<?php if($vendercnt>0){?>
							<th>브랜드</th>
							<?php }?>
							<th>상품코드</th>
							<th colspan=2>상품명</th>
							<th>제조사</th>
							<!--<th>달러</th>-->
							<th>정가</th>
							<th>구매가</th>
							<th>판매가</th>
							<!--<th>적립금(률)</th>-->
							<th>수량</th>
							<th>진열</th>
							<th>상세</th>
							
						</tr>
		<?php
						$page_numberic_type=1;
						
						if ($likecode){
							$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
							$link_result=pmysql_query($link_qry);
							while($link_data=pmysql_fetch_object($link_result)){
								$linkcode[]=$link_data->c_productcode;
							}

							$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";
							
							// $qry= "AND b.c_category LIKE '{$likecode}%' ";
						
						}
						// 20170510 복수검색 적용
						if ($keyword) {
							
							$keyword = trim($keyword);
							$temp_search = explode("\r\n", $keyword);
							$cnt = count($temp_search);
							
							$search_arr = array();
							for($i = 0 ; $i < $cnt ; $i++){
								array_push($search_arr, "'%".$temp_search[$i]."%'");
							}
							
							$qry.= "AND productname || productcode LIKE any ( array[".implode(",", $search_arr)."] ) ";
						}
						if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
						elseif($s_check==2)$qry.="AND quantity <= 0 ";
						if($display==1)	$qry.="AND display='Y' ";
						elseif($display==2)	$qry.="AND display='N'";
						elseif($display==3)	$qry.="AND display='R'";
						//if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

                        // ============================================================================================
                        // 브랜드 지정 또는 검색
                        // ============================================================================================
                        if ( $sel_vender ) {
                            $qry.="AND brand = {$sel_vender} ";
                        } elseif ( $s_brand_keyword ) {
                            $arrBrandIdx = array();

                            $tmp_search_keyword = strtolower($s_brand_keyword);
                            $subsql  = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$tmp_search_keyword}%' OR lower(brandname2) like '%{$tmp_search_keyword}%' ";
                            $subresult = pmysql_query($subsql);
                            while ( $subrow = pmysql_fetch_object($subresult) ) {
                                if ( $subrow->bridx != "" ) {
                                    array_push($arrBrandIdx, $subrow->bridx);
                                }
                            }
                            pmysql_free_result($subresult);

                            if ( count($arrBrandIdx) > 0 ) { 
                                $qry.="AND brand in ( " . implode(",", $arrBrandIdx) . " ) ";
                            }
                        }
						
                        if ($sel_season){
                        	$temp = explode (",", $sel_season);
                        	$season_year = $temp[0];
                        	$season = $temp[1];
                        	$qry.="AND a.season_year = '{$season_year}' AND season = '{$season}'";
                        }
                        
						$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
						$sql0.= $qry;
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, self_goods_code, ";
						$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date ";
						$sql.= "FROM tblproduct a  WHERE 1=1 ";
						$sql.= $qry." ";
						//$sql.= "ORDER BY modifydate DESC ";
// 						exdebug( $sql );
						$sql.= "ORDER BY a.pridx desc ";
						$sql = $paging->getSql($sql);

						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;

						while($row=pmysql_fetch_object($result)) {
							//exdebug( $row );
							$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
		?>
						<tr>
							<input type="hidden" name="aproductcode[]" value="<?=$row->productcode?>">
							<input type="hidden" name="aassembleproduct[]" value="<?=$row->assembleproduct?>">
							<input type="hidden" name="aassembleuse[]" value="<?=$row->assembleuse?>">
							
							<td align="center" style="font-size:8pt;padding:2"><input type="checkbox" name="idx[]" value="<?=$row->productcode?>"></td>
							<td align="center" style="font-size:8pt;padding:2"><?=$number?></td>
		<?php
							if($vendercnt>0) {
								echo "	<td align=\"center\" style=\"font-size:8pt\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->brandname}</a>":"-")."</B></td>\n";
							}
							?>
							<td align="center" style="font-size:8pt;padding:2"><?=$row->self_goods_code?></td>
							<?
							echo "	<TD style='position:relative'>";
							
							/*if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
								echo "<img src='".$imagepath.$row->tinyimage."' style=\"width:100px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else if(ord($row->tinyimage) && file_exists("../".$row->tinyimage)){
								echo "<img src='"."../".$row->tinyimage."' style=\"width:100px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else {
								echo "$row->tinyimage<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							}
							echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
							echo "		<tr bgcolor=\"#FFFFFF\">\n";
							if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
								echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
							} else {
								echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
							}*/

							$file = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);

							if (ord($row->tinyimage)){
								echo "<img src='".$file."' style=\"width:30px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else {
								echo "$row->tinyimage<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							}
							echo "<div id=\"primage{$cnt}\" style=\"position:absolute;left:40px; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
							echo "		<tr bgcolor=\"#FFFFFF\">\n";
							if (ord($row->tinyimage)){
								echo "		<td align=\"center\" width=\"100%\" height=\"100%\" style=\"border:#000000 solid 1px;padding:5px;\"><img src=\"".$file."\" border=\"0\" style='max-width:300px'></td>\n";
							}

							echo "		</tr>\n";
							echo "		</table>\n";
							echo "		</div>\n";
							echo "	</td>\n";
		?>							
							<td><input type=text name="aproductname[]" maxlength=250 value="<?=str_replace("\"","&quot",$row->productname) ?>" style="font-size:8pt;width:100%;" onKeyDown="chkFieldMaxLen(250)"></td>
							<td><input type=text name="aproduction[]" maxlength=20 value="<?=str_replace("\"","&quot",$row->production) ?>" style="font-size:8pt;" size=15></td>
								<!-- 달러  -->
							<!--<td><input type=text name="aconsumerprice[]" maxlength=8 value="<?=$row->sellprice?>" style="font-size:8pt;text-align:right" size=10></td>-->
								<!-- 시중가 -->
							<td><input type=text name="aconsumerprice[]" maxlength=8 value="<?=$row->consumerprice?>" style="font-size:8pt;text-align:right" size=10></td>
							
							<td><input type=text name="abuyprice[]" maxlength=8 value="<?=$row->buyprice?>" style="font-size:8pt;text-align:right" size=10></td>	<!-- 구입가 -->

							<?php if($row->assembleuse=="Y") { ?>	<!-- 판매가 -->
							<td align="right" style="font-size:8pt;"><input type=hidden name="asellprice[]" value="<?=$row->sellprice?>"><?=$row->sellprice?></td>
							<?php } else { ?>
							<td><input type=text name="asellprice[]" maxlength=8 value="<?=$row->sellprice?>" style="font-size:8pt;text-align:right" size=10></td>
							<?php } ?>
									<!-- 적립금 -->
							<td style="display: none">
                                <input type=text name="areserve[]" size=6 maxlength=6 value="<?=$row->reserve?>" style="font-size:8pt;text-align:right" id="areserve<?=$cnt?>" onKeyUP="chkFieldMaxLenFunc(this.id,'areservetype<?=$cnt?>');">
                                <select name="areservetype[]" style="width:36px;font-size:8pt;margin-left:1px;" id="areservetype<?=$cnt?>" onchange="chkFieldMaxLenFunc('areserve<?=$cnt?>',this.id);">
                                    <option value="Y"<?=($row->reservetype!="Y"?"":" selected")?>>%
                                    <option value="N"<?=($row->reservetype!="Y"?" selected":"")?>>￦
                                </select>
                            </td>
							<td><input type=text name="aquantity[]" maxlength=3 value="<?=$row->quantity?>" style="font-size:8pt;text-align:right" size=6></td>
							<td><select name="adisplay[]" style="font-size:8pt;"><option value="Y" <?php if ($row->display=="Y") echo "selected" ?>>Y<option value="N" <?php if ($row->display=="N") echo "selected" ?>>N</select></td>
							<TD ><a href="javascript:ProductInfo('<?=$row->productcode?>');"><img src="images/icon_newwin1.gif" border="0"></a></td>
						</tr>
						<input type="hidden" name="aproductname2[]" value="<?=str_replace("\"","&quot",$row->productname)?>">
						<input type="hidden" name="aproduction2[]" value="<?=str_replace("\"","&quot",$row->production)?>">
						<input type="hidden" name="aconsumerprice2[]" value="<?=$row->consumerprice?>">
						<input type="hidden" name="abuyprice2[]" value="<?=$row->buyprice?>">
						<input type="hidden" name="asellprice2[]" value="<?=$row->sellprice?>">
						<input type="hidden" name="areserve2[]" value="<?=$row->reserve?>">
						<input type="hidden" name="areservetype2[]" value="<?=($row->reservetype!="Y"?"N":"Y")?>">
						<input type="hidden" name="aquantity2[]" value="<?=$row->quantity?>">
						<input type="hidden" name="adisplay2[]" value="<?=$row->display?>">
						
		<?
						$cnt++;	
						}
						if ($cnt==0) {
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}
		?>

                        <tr>
                        <?if($page_numberic_type) {?>
                            <td style="text-align:left;" colspan=<?=$colspan?>>
								<a href="javascript:ProductCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a>&nbsp;
                                <a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a>
                            </td>
                        <?}?>
                        </tr>

					</table>
					</div>
					
		<?
					if($page_numberic_type) {
							echo "<div id=\"page_navi01\">";
					//		echo "<p class=\"btn\"><a href=\"javascript:registeradd();\"><img src=\"img/btn/btn_product_reg.gif\" alt=\"상품등록\" /></a></p>";
							echo "<div class=\"page_navi\">";
							echo "<ul>";
							echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</ul>";
							echo "</div>";
							echo "</div>";
					}
		?>		
					</td>
				</tr>
		
                <tr>
                    <td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
                    선택한 상품을
                    <select name=sel_mode id="sel_mode" class="select">
                        <option value="">=======일괄적용선택=======</option>
                        <option value="1">정가 일괄적용</option>
                        <option value="2">판매가 일괄적용</option>
                        <option value="3">마진율 일괄적용</option>
                        <option value="4">아이콘 일괄적용</option>
                        <option value="5">태그/카테고리/정보고시 일괄적용</option>
                    </select> 으로 
                    &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckBatchForm();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
                </tr>
				<?php
                // =======================================================================
                // 송장정보 일괄 업데이트
                // =======================================================================
				?>
				

				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>

			</form>
			<tr>
				<td background="images/counter_blackline_bg.gif" class="font_white" align="center" height="40">
				<!--td align="center" height="40"-->
					<form name="dv_all_form" action="product_allupdate_indb.php" enctype="multipart/form-data" method="post" onSubmit="return submit_data();">
						<input type="hidden" name="mode" value="updatedvcode" >
						판매가 일괄 업데이트 <input type="file" name="csv_file" id="csv_file" alt="csv파일" accept=".csv">
						<input type="submit" value="일괄 업데이트" >
					</form>
				</td>
			</tr>


			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			</form>
			
			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>
			
			<form name=form_register action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

            <!-- 일괄적용용 form -->
            <form name="form_batch_apply" id="form_batch_apply" method="post">
            </form>
			<form name="product_excel" id="product_excel" method="post">
				<input type="hidden" name=product_code_all>
			</form>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품 일괄 간편수정 주의사항</span></dt>
							<dd>
							- 시중가, 구입가, 판매가, 적립금, 수량 입력시 콤마(,)는 입력하지 마세요.
							</dd>
								
						</dl>
						<dl>
							<dt><span>상품 일괄 간편수정 방법</span></dt>
							<dd>
							① 상품보기 선택에 따라 카테고리 선택 또는 상품명으로 검색합니다.<br>
							② 출력된 상품들 중 수정을 원하는 상품만 입력내용을 수정합니다.<Br>
							③ 수정이 완료 됐으며 [적용하기] 버튼을 클릭합니다.
							</dd>

						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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

<?php
include("copyright.php");
?>
<?=$onload?>
