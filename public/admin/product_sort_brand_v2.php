<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
//exdebug($_POST);


$mode=$_POST["mode"];
$display=$_POST["display"];
$vender = $_POST["vender"];

$listnum=(int)$_POST["listnum"];
if(!$listnum){
	$listnum = 20;
}
$selected[listnum][$_POST[listnum]] = "selected";
$checked["display"][$display] = "checked";
//print_r($start_no);

//$code=$code_a.$code_b.$code_c.$code_d;

//$prcode=$_POST["prcode"];
//$change=$_POST["change"];
//$prcodes=$_POST["prcodes"];

//exdebug($type);
if ($mode=="sequence") {

	//$date1=date("Ym");
	//$date=date("dHis");
    $productcode = $_POST['productcode'];
	$cnt = count($productcode);

	for($i=0;$i<$cnt;$i++){
		//$date=$date-1;
		//$date = sprintf("%08d",$date);
		if(strpos($type,'T')===FALSE) {
			$sql = "UPDATE tblbrandproduct SET  ";
			$sql.= " start_no=".$i." ";
			$sql.= "WHERE bridx = $vender ";
            $sql.= "AND productcode='{$productcode[$i]}' ";
		} 
		//exdebug($sql);
		pmysql_query($sql,get_db_conn());
	}
	$onload="<script>window.onload=function(){ alert('상품순서 변경이 완료되었습니다.');}</script>\n";

	$log_content = "[브랜드 상품 진열순서 조정] 브랜드코드 : $vender";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
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
            ORDER BY b.brandname ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";

?>

<?php include("header.php"); ?>

<script src="../js/jquery-1.12.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>

<script language="JavaScript">


var iciRow, preRow;
var objArray = new Array();
var objidxArray = new Array();

$(document).ready(function(){
	$(".spoitClass").click(function(){
		if ($(this).hasClass('selected') === true) {			
			$(this).removeClass('selected');  
			spoit(this, 'non');
			console.log("non");
		} else if ($(this).hasClass('selected') === false) {			
			$(this).addClass('selected');
			console.log("sel");
			spoit(this, 'sel');
		}
	});
});

function spoit(obj, chk){
	iciRow = obj;
	iciHighlight(chk);
}

function array_sort(){
	objArray = new Array();
	objidxArray = new Array();
	$("#spoitTable .selected").each(function(index) {
		objArray.push(this);
		objidxArray.push($(".spoitClass").index(this));
	});
}

function iciHighlight(chk){
	if (chk == 'non'){
		iciRow.style.backgroundColor = "";
	}else{
		iciRow.style.backgroundColor = "#FFF4E6";
	}
	array_sort();	
}

function moveTree1(idx){
	if (objArray.length > 0) {	
		var idx			= 0;
		var chkFirst	= objidxArray[idx];
		if (chkFirst > 0) {
			for(var k = 0; k < objArray.length; k++){
				$(objArray[k]).insertBefore($(objArray[k]).prev());
			}
			array_sort();
		}
	}
}
function moveTree2(idx){	
	if (objArray.length > 0) {	
		var idx		= objArray.length - 1;
		var chkEnd= objidxArray[idx];
		if ((chkEnd+1) < $(".spoitClass").length) {
			objidxArray = new Array();
			for(var k = objArray.length-1; k >= 0; k--){
				$(objArray[k]).insertAfter($(objArray[k]).next());
				objidxArray.push($(".spoitClass").index(objArray[k]));
			}
			array_sort();
		}
	}
}


$(function() {
	$('body').keydown(function( event ) {
		event.preventDefault();
		$("body").trigger('focus');
		if (iciRow==null) return;
		switch (event.keyCode){
			case 38: moveTree1(-1); break;
			case 40: moveTree2(1); break;
		}
		return false;
	});
});

function move_save()
{
	if (!confirm("저장하시겠습니까?")) return;

		document.form1.mode.value = "sequence";
		document.form1.submit();
}

function ProductInfo(prcode,popuptype) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	if (popup=="YES") {
		document.form_register.action="product_register.set.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span>상품 진열순서 설정 (브랜드)</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">

			<input type=hidden name=mode>
			<!-- <input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>"> -->
			<tr>
				<td>
				<div class="title_depth3">상품 진열순서 설정 (브랜드)</div>
				<div class="title_depth3_sub"><span>각각의 브랜드에 등록된 상품의 진열 순서를 변경할 수 있습니다.</span></div>
				</td>
            </tr>
            <tr>
            	<td>
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
                        <tr>
                            <th><span>브랜드 검색</span></th>
                            <td><select name=vender class="select">
                                <option value="">==== 전체 ====</option>
        <?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->bridx}\"";
                            if($vender==$val->bridx) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }
        ?>
                                </select> 
                            </td>
                        </tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열</td>
						</tr>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>

<div align=right style="margin-bottom:3px;">
<select name=listnum onchange="this.form.submit()">
<?
$r_pagenum = array(20,40,60,80,100,200,300,400,500,10000);
foreach ($r_pagenum as $v){
?>
<option value="<?=$v?>" <?=$selected[listnum][$v]?>><?=$v?>개 출력
<? } ?>
</select>
</div>

				<table id="ListTTableId" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed;">
					<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
					<tr>
						<td width="100%" height="100%" valign="top" bgcolor="#FFFFFF" style="padding-left:5px;padding-right:5px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="text-align:right">
									</td>
								</tr>
								<TR>
									<TD width="100%">



									<table width=100% border=1 bordercolor=#dfdfdf style="border-collapse:collapse" frame=hsides rules=rows id='spoitTable'>
									<colgroup>
							<?php
									$colspan=8;
									if($vendercnt>0) $colspan++;
							?>

									<col width=40></col>
							<?php if($vendercnt>0){?>
									<col width=200></col>
							<?php }?>
									<col width=></col>
									<col width=70></col>
									<col width=70></col>
									<col width=70></col>
									<col width=70></col>

									</colgroup>
									<TR align="center">
										<th height=25>No</th>
										<?php if($vendercnt>0){?>
										<th>브랜드</th>
										<?php }?>
										<th>상품명/진열코드/특이사항</th>
										<th>판매가격</th>
										<th>수량</th>
										<th>상태</th>
										<th>수정</th>
									</TR>
							<?php
									$image_i=0;
									if($vender) {
										$page_numberic_type=1;

										if($display==1)	$qry.="AND display='Y' ";
										elseif($display==2)	$qry.="AND display='N' ";

                                        if($vender) $qry.="AND a.bridx = $vender ";

										$sql = "SELECT a.productcode, b.productname, b.sellprice, b.quantity, b.reserve, b.reservetype, b.addcode, b.display, b.vender, b.tinyimage, b.date, b.modifydate, a.start_no ";
										$sql.= "FROM 	tblbrandproduct a ";
                                        $sql.= "JOIN	tblproduct b ON a.productcode = b.productcode ";
										$sql.= $qry." ";
										//$sql.= "ORDER BY a.start_no asc, b.pridx desc  ";
                                        // 프론트 브랜드 상품리스트 기준으로 변경 2016-06-23 jhjeong
                                        $sql.= "ORDER BY a.start_no asc, b.regdate desc, b.modifydate desc  ";
                                        if($listnum) $sql.= "Limit ".$listnum." OFFSET 0 ";
										$result = pmysql_query($sql,get_db_conn());
										$cnt = @pmysql_num_rows($result);
										//exdebug($sql);
                                    } else {
                                        $cnt = 0;
                                    }

                                    if($cnt>0)
                                    {
                                        $j=0;
                                        while($row=pmysql_fetch_object($result)) {
        
                                            $tinyimage = getProductImage($imagepath, $row->tinyimage );
?>


<!--  -->
    <tr id = "spoit<?=++$idx?>" class = 'spoitClass' id2 = '<?=$idx?>'>
        <td align=center bgcolor=#f7f7f7 width=40 nowrap><font class=small1 color=444444><?=$idx?></font></td>
        <?php if($vendercnt>0){?>
        <TD><B><?=(ord($venderlist[$row->vender]->vender)?$venderlist[$row->vender]->brandname:"-")?></B></td>
        <?php }?>
        <TD><img src="<?=$tinyimage?>" style="width:25px" border="1"><?=$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")?>&nbsp;/ <?=$row->productcode?></td>
        <TD style="text-align:center; padding-right:20px">
            <img src="images/won_icon.gif" border="0" style="margin-right:2px;"><span class="font_orange"><?=number_format($row->sellprice)?></span><br>
            <img src="images/reserve_icon.gif" border="0" style="margin-right:2px;"><?=($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")?>
        </TD>
        <TD >
        <?
        if ($row->quantity=="999999999") echo "무제한";
        else if ($row->quantity == "0") echo "<span class=\"font_orange\"><b>품절</b></span>";
        else echo $row->quantity;
        ?>
        </TD>
        <TD  style="text-align:center;">
        <?=($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")?>
        </td>		
        <TD><img src="images/icon_newwin1.gif" border="0" onclick="ProductInfo('<?=$row->productcode?>','YES');" style="cursor:hand;" id="spoit<?=$idx?>_pop"></td>
        <input type=hidden name=productcode[] value="<?=$row->productcode?>">
        <input type=hidden name=sort[] value="<?=$idx?>">
    </tr>
<? 
                                        }
                                    } 
?>
</table>
<!--  -->

									</td>
								</TR>

						<? if($cnt>0){?>
								<tr>
									<TD colspan="<?=$colspan?>" align=center><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></TD>

								</tr>
								<TR>
									<TD align=center>
										<a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a>
									</TD>
								</TR>
						<?}?>

							</table>
						</td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td>
							<!-- 매뉴얼 -->
							<div class="sub_manual_wrap">
								<div class="title"><p>매뉴얼</p></div>

								<dl>
									<dt><span>상품 진열순서 설정시 주의사항</span></dt>
									<dd>
										- 카테고리의 상품정렬이 [상품 등록/수정날짜 순서], [상품 등록/수정날짜 순서+품절상품 뒤로] 일때만 상품 진열순서 설정에 따라 출력됩니다.<br>
									<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span> 에서 카테고리의 상품정렬을 확인할 수 있습니다.</a><br>
										- 진열순서 조정을 위해 우측 버튼을 사용할 경우 [저장하기] 를 클릭해야만 적용됩니다.<br>
										- 진열순서 조정을 위해 "진열상품 순서 저장하기"을 사용할 경우 [적용하기] 를 클릭해야만 적용됩니다.<br>
										- <b>하위카테고리가 있는 카테고리의 경우</b> 하위카테고리의 상품 순서를 변경하시면 해당 상품이 맨 위에 위치합니다.
									</dd>
								</dl>

							</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>

			</table>
			</form>
			</td>
		</tr>
		</TABLE>
		</td>
		</tr>
	</table>
	</td>
</tr>
</table>


<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>

<?php
include("copyright.php");
?>
<?=$onload?>