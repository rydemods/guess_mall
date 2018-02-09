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


$sort=$_POST["sort"];
$mode=$_POST["mode"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$codesum="";
$codesum.=strlen($code_a)?$code_a:"000";
$codesum.=strlen($code_b)?$code_b:"000";
$codesum.=strlen($code_c)?$code_c:"000";
$codesum.=strlen($code_d)?$code_d:"000";

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


$imagepath=$Dir.DataDir."shopimages/product/";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
function CodeProcessFun(_code) {
	
	if(_code.length==12 && _code!="000000000000") {
		document.form2.mode.value="";
		document.form2.code.value=_code;
		
		document.form2.target="MainPrdtFrame";
		document.form2.action="product_startlist.main.php";
		if(document.MainPrdtFrame.form1)
			document.form2.Scrolltype.value = document.MainPrdtFrame.form1.Scrolltype.value;
		document.form2.submit();
	} else {
		document.form2.mode.value="";
		document.form2.code.value="";
		
		document.form2.target="MainPrdtFrame";
		document.form2.action="product_startlist.main.php";
		if(document.MainPrdtFrame.form1)
			document.form2.Scrolltype.value = document.MainPrdtFrame.form1.Scrolltype.value;
		document.form2.submit();
	}
}

function MainInsert(prcode) {
	max=50;
	if(document.MainPrdtFrame.form1.num.value>=max){
		alert("카테고리 진열상품은 최대 "+max+"개의 상품을 등록하실 수 있습니다.\n\n다른 상품을 삭제후 등록하세요.");
		return;
	}
	
	var is_start = false;
	for(i=0;i<document.MainPrdtFrame.form1.start.length;i++) {
		if (document.MainPrdtFrame.form1.start[i].checked) {
			is_start=true;
			break;
		}
	}
	if(!is_start){
		alert("이동할 진열상품카테고리를 선택하세요.");
		document.MainPrdtFrame.form1.start[0].focus();
		return;
	}
	//if (confirm("해당 상품을 카테고리 진열상품으로 이동하시겠습니까?")){
		document.MainPrdtFrame.form1.prcode.value=prcode;
		document.MainPrdtFrame.Insertstart();
	//}
} 


function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}


function ProductInfo(prcode,popuptype) {
	code=prcode.substring(0,12);
	popup=popuptype;
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	if (popup=="YES") {
		document.form_register.action="product_register.add.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.add.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

var ProductInfoStop="";
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	ProductInfoStop = "1";
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>


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

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}


</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>카테고리 상품 진열관리</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=prcode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=sort value="<?=$sort?>">			
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">카테고리 상품 진열관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰 카테고리페이지에 신상품, 인기상품, 추천상품에 진열할 상품을 등록할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->								
					<div class="table_style01">						
						<table width=100% cellpadding=0 cellspacing=0 border=0>
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
						
						</table>
					</div>
				
				</td>
			</tr>
			<tr>
				<td align=center><a href="#"><input type="image" src="images/botteon_search.gif" alt="" /></a></td>
			</tr>
			<tr>
				<td>
					
					<table  width=100% cellpadding=0 cellspacing=0 border=0>
						<colgroup>
							<col width="" /><col width="50" /><col width="480" />
						</colgroup>
						<tr>
							<td valign=top>
								<div class="title_depth3_sub">상품리스트</div>
								<div class="sort_list"><span>정렬방법 :</span> <a href="javascript:GoSort('modifydate');">등록순</a> l <a href="javascript:GoSort('productname');">상품명순</a> l <a href="javascript:GoSort('price');">가격순</a></div>
								<div class="table_style02" style="min-height:450px;">
								<table width=100% cellpadding=0 cellspacing=0 border=0>
									<colgroup>
										<?
										$colspan=7;
										if($vendercnt>0) $colspan++;
										?>
										<col width="50" />
										<?php if($vendercnt>0){?>
										<col width=70></col>
										<?php }?>
										<col width="50" />
										<col width="" />
										<col width="80" />
										<col width="50" />
										<col width="50" />
										<col width="50" />
										<col width="50" />
									</colgroup>
									<tr>
										<th>No</th>
										<?php if($vendercnt>0){?>
										<Th>입점업체</Th>
										<?php }?>
										<th colspan="2">상품명/진열코드/특이사항</th>
										<th>판매가격</th>
										<th>수량</th>
										<th>상태</th>
										<th>수정</th>
										<th>진열</th>
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
									}// $qry= "AND b.c_category LIKE '{$likecode}%' ";
																		
									$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
									$sql0.= $qry;
									$paging = new newPaging($sql0,10,8);
									$t_count = $paging->t_count;
									$gotopage = $paging->gotopage;

									$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
									$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date, selfcode, modifydate ";
									$sql.= "FROM tblproduct a WHERE 1=1 ";
									$sql.= $qry." ";
									if ($sort=="price")				$sql.= "ORDER BY sellprice ";
									elseif ($sort=="productname")	$sql.= "ORDER BY productname ";
									else							$sql.= "ORDER BY modifydate DESC ";
									$sql = $paging->getSql($sql);
									$result = pmysql_query($sql,get_db_conn());
									$cnt=0;

									if($likecode)while($row=pmysql_fetch_object($result)) {
									$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
										
									echo "<tr>";
									echo "<td>".$number."</td>";
									if($vendercnt>0) {
										echo "	<TD><B>".(ord($venderlist[$row->vender]->vender)?"<span onclick=\"viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</span>":"-")."</B></td>\n";
									}
									echo "	<TD>";
									if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
										echo "<a href=\"javascript:ProductInfo('".$row->productcode."','YES');\"><img src='".$imagepath.$row->tinyimage."'  style=\"width:100px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\"></a>";
									} else {
										echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
									}
									echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
									echo "		<tr bgcolor=\"#FFFFFF\">\n";
									if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
										echo "		<td align=\"center\" width=\"100%\"  style=\"width:100px\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
									} else {
										echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
									}
									echo "		</tr>\n";
									echo "		</table>\n";
									echo "		</div>\n";
									echo "	</td>\n";
									echo "<td height=\"50\"><a href=\"javascript:ProductInfo('".$row->productcode."','YES');\"><p class=\"ta_l\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</p></a></td>";
									echo "	<TD style=\"text-align:right; padding-right:10px\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
									
									echo "	<TD>";
									if (ord($row->quantity)==0) echo "무제한";
									elseif ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
									else echo $row->quantity;
									echo "	<TD>".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
									echo "	</TD>\n";
									echo "<td><a href=\"javascript:ProductInfo('".$row->productcode."','YES');\"><img src=\"images/icon_newwin1.gif\" alt=\"수정\" /></a></td>";
									echo "<td><img src=\"images/btn_show01.gif\" onclick=\"javascript:MainInsert('".$row->productcode."');\"></td>";
									
									echo "</tr>";
									$cnt++;	
									}
									if ($cnt==0) {
										$colspan='11';
										$page_numberic_type="";
										echo "<tr><td colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
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
						
		?>
							</td>
							<td align=center><img src="images/btn_next.gif" alt="" /></td>
							<td valign=top>
								<!-- 소제목 -->
								<div class="title_depth3_sub">카테고리별 진열 리스트</div>
								
								<IFRAME name="MainPrdtFrame" src="product_startlist.main.php" width=100% height=100% frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
								
							</td>
						</tr>
					</table>

				</td>
			</tr>
			
			<tr>
				<td height="20"></td>
			</tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					
					<dl>
						<dt><span>카테고리 섹션별 진열상품 등록하기</span></dt>
						<dd>
							① 카테고리 진열섹션에 등록할 카테고리 선택<br>
							② 상품목록에서 진열섹션에 등록할 상품 선택<br>
							③ 카테고리에 선택된 상품을 등록할 진열섹션 선택 후, [상품진열] 버튼 클릭
						</dd>				
					</dl>
					<dl>
						<dt><span>카테고리 섹션별 진열시 주의사항</span></dt>
						<dd>
							- 진열상품 순서조정을 위해 우측 버튼(삭제버튼제외)을 사용할 경우 [진열상품 순서 저장하기] 를 클릭해야만 적용됩니다.<br>
							- 진열상품 순서조정을 위해 "선택상품 순서변경"을 사용할 경우 [적용하기] 를 클릭해야만 적용됩니다.<br>
							- 카테고리 진열상품 삭제(우측삭제버튼)시 상품은 삭제되지 않으며 단지 해당 카테고리 상품진열 섹션에서 제외됩니다.

						</dd>				
					</dl>
					<dl>
						<dt><span>카테고리 상품진열 섹션 순서 입니다.</span></dt>
						<dd>
							<img src="images/product_codelist_1_img.gif" border="0">
						</dd>				
					</dl>
				</div>
				</td>
			</tr>
			</form>
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
<form name=form2 action="" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
<input type=hidden name=Scrolltype>
</form>

<form name=form_register action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?php
echo "<script>CodeProcessFun('$codesum')</script>";
include("copyright.php");
