<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-8";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$yesbrand=$_POST["yesbrand"];
$keyword=$_POST["keyword"];
$type=$_POST["type"];
$listnum    = $_POST["listnum"] ?: "20";

// 상품별 무이자설정때문에 넣음, card_splittype가 'O' 이면 상품별 무이자설정임.
$sql = "SELECT card_splittype,card_splitprice FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$card_splittype = $row->card_quotafree;
	$card_splitprice = $row->card_quotaprice;
}
pmysql_free_result($result);

if($card_splittype!="O" && $type=="card"){
	alert_go('[개별상품 무이자 할부 서비스]가 셋팅되어있어야 검색이 가능합니다.',-1);
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	if (document.form1.keyword.value.length<2) {
		if(document.form1.keyword.value.length==0) alert("검색어를 입력하세요.");
		else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
		document.form1.keyword.focus();
		return;
	} else {
		document.form1.submit();
	}
}

function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch()
	}
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.target="register";
		window.open("about:blank","register","width=100,height=700,scrollbars=yes,status=no");
		document.form2.action="product_register.set.php";
	} else {
		document.form2.target="";
		document.form2.action="product_register.set.php";
	}
	document.form2.submit();
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
function GoPage(block,gotopage) {
	document.form3.block.value = block;
	document.form3.gotopage.value = gotopage;
	document.form3.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt;<span>상품 키워드 검색</span></p></div></div>
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
					<div class="title_depth3">상품 키워드검색</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 모든 상품을 상품명 및 키워드로 검색 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품 키워드 검색</div>
				</td>
			</tr>

			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>검색어 입력</span></th>
					<TD><select size=1 name=yesbrand class="select" style="width:80px;height:32px;vertical-align:middle;">
							<option value="YES" <?php if ($yesbrand=="YES") echo "selected"; ?>>상품명
							<option value="NO" <?php if ($yesbrand=="NO") echo "selected"; ?>>키워드
						</select> 
						<!--  
						<input type=text name=keyword value="<?=$keyword?>" onKeyDown="CheckKeyPress()" style="width:250" class="input"> 
						-->
						<textarea rows="2" cols="10" class="w200" name="keyword" onKeyDown="CheckKeyPress()"  style="resize:none;vertical-align:middle;"><?=$keyword?></textarea>
						<select size=1 name=type class="select" style="width:80px;height:32px;vertical-align:middle;">
							<option value="all" <?php if ($type=="all" || empty($type)) echo "selected"; ?>>전체상품
							<option value="empty" <?php if ($type=="empty") echo "selected"; ?>>품절된상품
							<option value="noview" <?php if ($type=="noview") echo "selected"; ?>>미진열상품
							<option value="bank" <?php if ($type=="bank") echo "selected"; ?>>현금결제상품
							<?php if($card_splittype=="O") {?><option value="card" <?php if ($type=="card") echo "selected"; ?>>개별무이자상품<?php }?>
						</select> <a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle  border="0"></a></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
                    <td align=left width="50%">
					    <div class="title_depth3_sub">검색 내역</div>
					</td>
					<td width="" align="right">
                        <div style="margin:20px 0px 5px">
                        <!-- <img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지 -->
                        <select name="listnum" onchange="javascript:document.form1.submit();">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                            <option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
                        </select>
                        </div>
                    </td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="50"></col>
				<col width=""></col>
				<col width="80"></col>
				<col width="80"></col>
				<col width="60"></col>
				<col width="40"></col>
				<col width="40"></col>
				<col width="40"></col>
				<col width="50"></col>
				<TR>
					<th>No</th>
					<th>상품명</th>
					<th>판매가</th>
					<th>수량</th>
					<th>적립금</th>
					<th colspan=3>이미지 대,중,소</th>
					<th>진열</th>
				</TR>
<?php
				$keyword = str_replace("%","",$keyword);
				if (ord(trim($keyword)) || !empty($type)) {
					$page_numberic_type=1;
					$qry = "FROM tblproduct a WHERE 1=1 ";

					$keyword = trim($keyword);
					$temp_search = explode("\r\n", $keyword);
					$cnt = count($temp_search);
					
					$search_arr = array();
					for($i = 0 ; $i < $cnt ; $i++){
						array_push($search_arr, "'%".$temp_search[$i]."%'");
					}
					
					if (ord($keyword) && $yesbrand=="YES"){
						$qry.= "AND a.productname LIKE any ( array[".implode(",", $search_arr)."] ) ";
					} else if(ord($keyword)) {
						$qry.= "AND a.keyword LIKE any ( array[".implode(",", $search_arr)."] ) ";
					}

					if ($type=="empty")
						$qry.= "AND a.quantity<=0 ";
					else if ($type=="noview")
						$qry.= "AND a.display='N' ";
					else if ($type=="card")
						$qry.= "AND a.etctype LIKE '%SETQUOTA%' ";
					else if ($type=="bank")
						$qry.= "AND a.etctype LIKE '%BANKONLY%' ";
					else if ($type=="new")
						$qry.= "AND date LIKE '".date("Ymd")."%' ";

					$sql = "SELECT COUNT(*) as t_count ".$qry;
					$paging = new Paging($sql,10,$listnum);
					$t_count = $paging->t_count;
					$gotopage = $paging->gotopage;

					$sql = "SELECT a.productcode, a.sellprice, a.productname, a.quantity, a.reserve, a.reservetype, ";
					$sql.= "a.addcode, a.maximage, a.minimage, a.tinyimage, a.display, a.selfcode, a.assembleuse {$qry} ";
					$sql = $paging->getSql($sql);
					$result = pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						echo "<tr>";
						echo "	<TD>{$number}</td>\n";
						echo "	<TD><NOBR>";
						echo "	<div class=\"table_none\"> \n";
						echo "	<TABLE cellSpacing=0 cellPadding=0 border=0 width=\"100%\">\n";
						echo "	<tr>\n";
						echo "		<td style=\"word-break:break-all;\">\n";
						echo "		<div class=\"ta_l\"> \n";
						echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
						echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','')\"><font color=#3D3D3D><u>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</u></font></a>";
						echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><IMG src=\"images/icon_newwin.gif\" align=absMiddle border=0 ></a>";
						echo "		</span>\n";
						echo "		<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
						echo "		<div class=\"table_none\"> \n";
						echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
						echo "		<tr bgcolor=#FFFFFF>\n";
						if (ord($row->tinyimage)) {
							echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid;padding:5px;\"><img src='".getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage)."' style='max-width:300px'></td>\n";
						} else {
							echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid;padding:5px;\"><img src={$Dir}images/product_noimg.gif></td>\n";
						}
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</div>\n";
						echo "		</div>\n";
						echo "		</div>\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</div>\n";
						echo "	</td>\n";
						echo "	<TD><b><span class=\"font_orange\">".number_format($row->sellprice)."</span></b></td>\n";
						echo "	<TD>";
						if (ord($row->quantity)==0) echo "무제한";
						else if ($row->quantity<=0) echo "<font color=red>품절</font>";
						else echo $row->quantity;
						echo "	</td>\n";
						echo "	<TD>".($row->reservetype!="N"?number_format(exchageRate($row->reserve)):$row->reserve."%")."</td>\n";
						echo "	<TD>".(ord($row->maximage)?"O":"X")."</td>\n";
						echo "	<TD>".(ord($row->minimage)?"O":"X")."</td>\n";
						echo "	<TD>".(ord($row->tinyimage)?"O":"X")."</td>\n";
						echo "	<TD>{$row->display}</td>\n";
						echo "</tr>\n";
						$cnt++;
					}
					pmysql_free_result($result);
					if ($cnt==0) {
						$page_numberic_type = "";
						echo "<tr><td class=td_con2 colspan=9 align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
					}
				} else {
					$page_numberic_type = "";
					echo "<tr><td class=td_con2 colspan=9 align=center>검색된 상품이 없습니다.</td></tr>";
				}
?>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
				if($page_numberic_type) {
					echo "<tr>\n";
					echo "	<td width=\"100%\" align=center class=\"font_size\">\n";
					echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
					echo "	</td>\n";
					echo "</tr>\n";
				}
?>
				</table>
				</td>
			</tr>
			</form>

			<form name=form2 action="product_register.add.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=yesbrand value="<?=$yesbrand?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<input type=hidden name=type value="<?=$type?>">
            <input type=hidden name=listnum value="<?=$listnum?>">
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품 키워드 검색</span></dt>
							<dd>
							- 검색어는 최소 2글자 이상부터 검색이 가능합니다.<br>
							- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.<br>
							- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.
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
<?=$onload?>
<?php 
include("copyright.php");
