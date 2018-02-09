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
$sel_vender = $_POST['sel_vender'];
$s_brand_keyword = $_POST['s_brand_keyword'];

if($sel_vender) {
    // 브랜드를 선택한 경우
    $qry.= "AND a.brand = {$sel_vender} ";
} else { 
    // 특정브랜드를 선택하지 않은 경우
    // 검색어에 해당하는 브랜드 리스트를 구한다.
            
    if ($s_brand_keyword) {
        $arrVender = array();
            
        $subqry  = "SELECT bridx FROM tblproductbrand ";
        $subqry .= "WHERE lower(brandname) like '%".strtolower($s_brand_keyword)."%' OR lower(brandname2) like '%".strtolower($s_brand_keyword)."%' ";
        $subresult = pmysql_query($subqry);
        while ( $subrow = pmysql_fetch_object($subresult) ) {
            array_push($arrVender, $subrow->bridx);
        }
        pmysql_free_result($subresult);

        if ( count($arrVender) > 0 ) {
            $qry.= "AND a.brand in ( " . implode(",", $arrVender) . " ) ";
        }
    }
}

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

/*
if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}
*/

$sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname
        FROM    tblvenderinfo a
        JOIN    tblproductbrand b ON a.vender = b.vender
        ORDER BY lower(b.brandname) ASC";
$ret_brand=pmysql_query($sql,get_db_conn());
while($row_brand=pmysql_fetch_object($ret_brand)) {
    $venderlist[$row_brand->vender]=$row_brand;
}
pmysql_free_result($ret_brand);

include_once("../lib/adminlib.php");
include_once("../conf/config.php");

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

$(document).ready(function(){

	$("#chk_confirm").click(function(){
		var input_r_prodcut = "";
		var new_count=0;
		var current_count = $("#r_product_list",opener.document).children().length;

		$("input[name='productcode']").each(function(i){
			if($(this).attr('checked')){
				
				var arrThisValue = $(this).val().split("||||");
				
				var inProduct	= 0;

				input_r_prodcut += "<li style='height:22px;'><input type='hidden' name='r_product[]' value='"+arrThisValue+"'>"+arrThisValue[1]+"<img src='images/icon_del1.gif' border='0' style='vertical-align:middle;' class='del_rproduct'></li>";
				$(this).removeAttr('checked');
				new_count = i+1;
			}
		})
		
		if( (new_count+current_count) >10 ){
			alert("관련상품은 10개까지만 등록 가능 합니다(현재 선택된 관련상품갯수:"+current_count);
		}else{
			$("#r_product_list", opener.document).append(input_r_prodcut);
			window.close();
		}
	})
	$(".CLS_allCheck").click(function(){
		if($(this).prop("checked")){
			$("input[name='productcode']").prop("checked", true);
		}else{
			$("input[name='productcode']").prop("checked", false);
		}
	})
})

function resetBrandSearchWord(obj) {
    if ( $(obj).val() == "" ) {
        $("#s_brand_keyword").attr("disabled", false).val("").focus();
    } else {
        $("#s_brand_keyword").attr("disabled", true);
    }
}

function ProductMouseOver(Obj) {
	//
}

function ProductMouseOut(Obj) {
	//
}

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
						<th><span>상품검색</span></th>
						<td><input class="w200" type="text" name="keyword" onfocus="this.value=''; this.style.color='#000000'; this.style.textAlign='left';" <?=$keyword?"value=".$keyword:"style=\"color:'#bdbdbd';text-align:center;\" value=\"상품명 상품코드\""?>></td>
					</tr>
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
					<tr>
						<th><span>등록일</span></th>
						<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
							<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
						</td>
					</tr>
					<tr>
						<th><span>상품금액별 검색</span></th>
						<td><input class="input_bd_st01" type="text" name="sellprice_min" value="<?=$sellprice_min?>"/> 원 ~ <input class="input_bd_st01" type="text" name="sellprice_max" value="<?=$sellprice_max?>"/> 원</td>
					</tr>
					<tr>
						<th><span>품절 유무</span></th>
						<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>미품절 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
					</tr>
					<tr>
						<th><span>진열 유무</span></th>
						<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열</td>
					</tr>
					<tr>
						<th><span>브랜드검색</span></th>
						<td>

                        <select name="sel_vender" id="sel_vender" onChange="javascript:resetBrandSearchWord(this);">
                            <option value="">========== 브랜드 선택============</option>
                            <?php
                                foreach($venderlist as $key => $val) {
                                        echo "\t\t\t\t\t\t\t<option value=\"{$val->bridx}\"";
                                        if($sel_vender==$val->bridx) echo " selected";
                                        echo ">{$val->brandname}</option>\n";
                                }
                            ?>
                        </select>
                        <input type="text" name="s_brand_keyword" id="s_brand_keyword" value="<?=$s_brand_keyword?>" style="width: 180px;" <?php if ($sel_vender) echo "disabled"; ?>/>
    
                        </td>
					</tr>
					<tr>
						<th><span>리스트 갯수</span></th>
						<td>
							<?
								$selected['page_limit'][$_POST['page_limit']] = "selected";
							?>
							<select name = 'page_limit'>
								<option value = '10' <?=$selected['page_limit']['10']?>>10개씩 보기</option>
								<option value = '20' <?=$selected['page_limit']['20']?>>20개씩 보기</option>
								<option value = '40' <?=$selected['page_limit']['40']?>>40개씩 보기</option>
								<option value = '60' <?=$selected['page_limit']['60']?>>60개씩 보기</option>
								<option value = '80' <?=$selected['page_limit']['80']?>>80개씩 보기</option>
								<option value = '100' <?=$selected['page_limit']['100']?>>100개씩 보기</option>
							</select>
                        </td>
					</tr>
				</table>
				<p class="ta_c">
					<a href="javascript:;"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a>
				</p>
			</div>

			<div class="table_style02">
				<table width=100% cellpadding=0 cellspacing=0 border=0>
					<colgroup>
						<col width="50" />
						<col width="50" />
						<col width="50" />
						<col width="" />
						<col width="120" />
						<col width="120" />
						<col width="70" />
						<col width="70" />
					</colgroup>
					<tr>
						<th><input type = 'checkbox' class = 'CLS_allCheck'></th>
						<th>No</th>
						<th>이미지</th>
						<th>상품명</th>
						<th>시중가</th>
						<th>판매가</th>
						<th>품절여부</th>
						<th>진열유무</th>

<!--
						<th colspan=2>
							상품명/진열코드/특이사항
							<?
								$selected['page_limit'][$_POST['page_limit']] = "selected";
							?>
							<select name = 'page_limit'>
								<option value = '10' <?=$selected['page_limit']['10']?>>10개씩 보기</option>
								<option value = '20' <?=$selected['page_limit']['20']?>>20개씩 보기</option>
								<option value = '40' <?=$selected['page_limit']['40']?>>40개씩 보기</option>
								<option value = '60' <?=$selected['page_limit']['60']?>>60개씩 보기</option>
								<option value = '80' <?=$selected['page_limit']['80']?>>80개씩 보기</option>
								<option value = '100' <?=$selected['page_limit']['100']?>>100개씩 보기</option>
							</select>
						</th>
-->
					</tr>
					<?
						$page_numberic_type=1;

						if ($likecode){
						//$qry= "AND b.c_category LIKE '{$likecode}%' ";
						$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
						$link_result=pmysql_query($link_qry);
						while($link_data=pmysql_fetch_object($link_result)){
							$linkcode[]=$link_data->c_productcode;
						}

						$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";

						}
                        
						if ($keyword) $qry.= "AND lower(productname || productcode) LIKE lower('%{$keyword}%') ";

						if($s_check==1)	{
//                            $qry.="AND (quantity is NULL OR quantity > 0) ";
                            $qry .= "AND (quantity > 0 and soldout = 'N') ";
                        } elseif($s_check==2){
//							$qry.=" AND ( quantity <= 0 or option_quantity like '%,0,%' )";
							$qry.=" AND ( (quantity < 999999999 and soldout = 'Y') OR (quantity <= 0) ) ";
						}

						if($display==1)	$qry.="AND display='Y' ";
						elseif($display==2)	$qry.="AND display='N'";
						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

						$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 "; //and sabangnet_flag='N'
						$sql0.= $qry;
						$pageLimit = $_POST['page_limit'];
						if(!$pageLimit) $pageLimit = "10";
						$paging = new newPaging($sql0,10,$pageLimit);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
						$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, modifydate ";
						$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode and c_maincate=1) WHERE 1=1  ";
						//$sql.= "and sabangnet_flag='N' "; 신규상품 검색 안되서 따로 주석처리 해서 조건 뺌 0623원재
						$sql.= $qry." ";
						$sql.= "ORDER BY regdate DESC ";

						$sql = $paging->getSql($sql);
//echo $sql . "<br/>";

						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
						while($row=pmysql_fetch_object($result)) {

						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

						echo "<tr>";
						echo "<td><input type = 'checkbox' name = 'productcode' value = '".$row->productcode."||||".$row->productname."'></td>";
						echo "<td>".$number."</td>";
						echo "<TD>";

                        $prodImgUrl = getProductImage($imagepath, $row->tinyimage);

                        if ( ord($row->tinyimage) ) {
							echo "<img src='".$prodImgUrl."' style=\"width:50px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
						} else {
							echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
						}
						echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
						echo "		<tr bgcolor=\"#FFFFFF\">\n";
						if ( ord($row->tinyimage) )  {
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$prodImgUrl."\" style=\"width:100px\" border=\"0\"></td>\n";
						} else {
							echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
						}
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</div>\n";
						echo "	</td>\n";
						echo "<td height=\"50\"><p class=\"ta_l\"><!--img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"--> &nbsp; ".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</p></td>";
                        echo '<td style="text-align:right; padding-right:10px">기본가 : <img src="images/won_icon.gif" border="0" style="margin-right:2px;">
                                <span class="font_orange">' . number_format($row->consumerprice) . '</span><br>
                            </td>';
                        echo '<td style="text-align:right; padding-right:10px">
                                기본가 : <img src="images/won_icon.gif" border="0" style="margin-right:2px;">
                                <span class="font_orange">' . number_format($row->sellprice) . '</span><br>
                              </td>';
                        echo '<td>' . ( $row->quantity=="0"?"Y":"N" ) . '</td>';
                        echo '<td>'; 
                                if($row->display=="Y") echo "판매중";
                                if($row->display=="N") echo "보류중";
                        echo '</td>';
						echo "</tr>";
						$cnt++;
						}
						if ($cnt==0) {
							$colspan='11';
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}
					?>
				</table>
			</div>
			<?
				//페이징
				echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
					echo "<div class=\"page_navi\">";
						echo "<ul>";
							if($page_numberic_type) echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
						echo "</ul>";
					echo "</div>";
				echo "</div>";
			?>
		</td>
	</tr>
	<tr><td height="50" align = 'center'><img src = '../admin/images/botteon_save.gif' class = 'hand' id="chk_confirm"></td></tr>
	</table>
</form>
