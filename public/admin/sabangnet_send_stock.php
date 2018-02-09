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

<?php include("header.php"); ?>

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
	document.form1.action="sabangnet_indb.php";
	document.form1.target="";
	document.form1.submit();
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
function popupStock(pdcode){
	window.open("../admin/sabangnet_send_stock_popup.php?productcode="+pdcode, "registerStock","width=400,height=300,scrollbars=yes,status=no");
	//sabangnet_send_stock_popup.php
}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;제휴몰 상품관리 &gt; <span>사방넷 재고 등록</span></p></div></div>

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
					<input type=hidden name=mode value = 'sabangProductRegister'>
					<input type=hidden name=prcode>
					<input type=hidden name=block value="<?=$block?>">
					<input type=hidden name=gotopage value="<?=$gotopage?>">			
					<tr>
						<td>
						<div class="title_depth3">사방넷 재고 등록</div>
						
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
						<?php
										$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
										$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
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
									<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
								</tr>
								<tr>
									<th><span>진열 유무</span></th>
									<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열</td>
								</tr>
							</table>
							<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
						</div>

						<div class="table_style02">
							<table width=100% cellpadding=0 cellspacing=0>
								<colgroup>
									<col width="50" />
									<col width="50" />
									<col width="" />
									<col width="80" />
									<col width="80" />
									<col width="80" />
								</colgroup>
								<tr>
									<th>No</th>
									<th colspan=2>상품명/진열코드/특이사항</th>
									<th>등록/수정</th>
								</tr>
				<?php
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
								if ($keyword) $qry.= "AND (productname || productcode)LIKE '%{$keyword}%' ";
								if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
								elseif($s_check==2){
									$qry.=" AND ( quantity <= 0 or option_quantity like '%,0,%' )";
		//							$qry.=" AND option_quantity like '%,0,%' ";
								}
								if($display==1)	$qry.="AND display='Y' ";
								elseif($display==2)	$qry.="AND display='N'";
								//if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
								if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
								if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

								$qry.=" AND (sabangnet_flag = 'Y' or sabangnet_flag='H') ";						
								
								$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
								$sql0.= $qry;
								$paging = new newPaging($sql0,10,15);
								$t_count = $paging->t_count;
								$gotopage = $paging->gotopage;

								$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
								$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, modifydate ";
								$sql.= "FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode and c_maincate=1) WHERE 1=1 ";
								$sql.= $qry." ";
								$sql.= "ORDER BY regdate DESC ";
								
								
								$sql = $paging->getSql($sql);
								$result = pmysql_query($sql,get_db_conn());
								$cnt=0;
								while($row=pmysql_fetch_object($result)) {
							
								$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
									
								echo "<tr>";
								echo "<td>".$number."</td>";
								echo "<td>";
								if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
									echo "<a href=\"/front/productdetail.php?productcode=".$row->productcode."\" target=\"_blank\"><img src='".$imagepath.$row->tinyimage."' style=\"width:100px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\"></a>";
								} else {
									echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
								}
								echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
								echo "		<tr bgcolor=\"#FFFFFF\">\n";
								if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
									echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" style=\"width:100px\" border=\"0\"></td>\n";
								} else {
									echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
								}
								echo "		</tr>\n";
								echo "		</table>\n";
								echo "	</div>\n";
								echo "</td>\n";
								echo "<td height=\"50\"><p class=\"ta_l\"><!--img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"--> &nbsp; <a href=\"javascript:ProductInfo('".$row->productcode."','YES', '".$likecodeExchange."');\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</a></p></td>";
								//echo "<td><p class=\"ta_l\"><a href=\"javascript:ProductInfo('".$row->productcode."','YES');\">".$row->productname."</a></p></td>";
								echo "<td><a href='javascript:popupStock(\"".$row->productcode."\");'><img src='images/btn_edit4.gif' border='0'></a></td>";
								echo "</tr>";
								$cnt++;	
								}
								if ($cnt==0) {
									$colspan='11';
									$page_numberic_type="";
									echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
								}
				?>
						</table>
					</div>
				<?php					
								//페이징
								
									echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
									echo "<div class=\"page_navi\">";
									echo "<ul>";
									if($page_numberic_type) echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
									echo "</ul>";
									echo "</div>";
									echo "</div>";
									##echo "<div class=\"btn_right\"><a href=\"javascript:registeradd();\"><img src=\"img/btn/btn_product_reg.gif\" alt=\"상품등록\" /></a></div>";
								
				?>
					<table height="20"><tr><td> </td></tr></table>
						<!-- 매뉴얼 -->
						<div class="sub_manual_wrap">
							<div class="title"><p>매뉴얼</p></div>
							<dl>
								<dt><span>제목</span></dt>
								<dd>
									 
								</dd>
							</dl>
						</div>
						</td>
					</tr>			
					<tr><td height="50"></td></tr>
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

<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
<input type=hidden name=category_data>
</form>

<?php
include("copyright.php");
?>
<?=$onload?>