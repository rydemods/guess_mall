<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=(int)$_POST["type"];
$s_check=(int)$_POST["s_check"];
$search=$_POST["search"];
$productcode=$_POST["productcode"];
$listnum    = $_POST["listnum"] ?: "20";

if(!$s_check) {	
	$search="";
	$search_style="disabled style=\"background:#f4f4f4\"";
}
${"check_s_check".$s_check} = "checked";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	check_val="";
	for(i=0;i<document.form1.s_check.length;i++) {
		if (document.form1.s_check[i].checked) {
			check_val=document.form1.s_check[i].value;
			break;
		}
	}
	if (check_val!="0") {
		if (document.form1.search.value.length<=2) {
			document.form1.search.focus();
			alert("검색어를 2자 이상 입력하세요.");
			return;
		}
	}
	document.form1.type.value="0";
	document.form1.submit();
}

function GoMemberView(prcode) {
	document.form3.type.value = "1";
	document.form3.block.value = "";
	document.form3.gotopage.value = "";
	document.form3.productcode.value = prcode;
	document.form3.submit();
}

function Searchid(id) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="#FFFFFF";
	document.form1.search.value=id;
	document.form1.s_check[2].checked=true;
	document.form1.submit();
}

function CheckScheck(val) {
	if (val==0) {

		document.form1.search.disabled=true;	
		document.form1.search.style.background="#F4F4F4";
	//	alert("검색어를 입력하실 필요없이 조회하기 버튼을 누르시기 바랍니다.");
	} else {
		document.form1.search.disabled=false;
		document.form1.search.style.background="#FFFFFF";
		document.form1.search.focus();
	}
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.action="product_register.set.php";
		document.form2.target="register";
		window.open("about:blank","register","width=1000,height=700,scrollbars=yes,status=no");
	} else {
		document.form2.action="product_register.set.php";
		document.form2.target="";
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
function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch();
	}
}
function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.form4.search.value=id;
	document.form4.submit();
}

</script>
<form name=form1 method=post>
<input type=hidden name=type value="<?=$type?>">
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
					<div class="title_depth3">Wishlist 상품 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>Wishlist에 한 상품을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">Wishlist 상품 검색</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>검색조건 선택</span></th>
					<TD><input type=radio name=s_check value="0" onClick="CheckScheck(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>상위 Wishlist 상품 100개</label>&nbsp;&nbsp;&nbsp;<input type=radio name=s_check value="1" onClick="CheckScheck(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>상품명으로 검색</label>&nbsp;&nbsp;&nbsp;<input type=radio name=s_check value="2" onClick="CheckScheck(this.value);" id=idx_s_check2 <?=$check_s_check2?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>회원아이디로 검색</label></TD>
				</TR>
				<TR>
					<th><span>검색어 입력</span></th>
					<TD><input name=search size=40 value="<?=$search?>" onKeyDown="CheckKeyPress()" <?=$search_style?> class="input"> <a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle border="0"></a></TD>
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
<?php if($type=="0"){//일반검색?>
				<col width="40"></col>
				<col width=""></col>
				<col width="40"></col>
				<col width="40"></col>
				<col width="40"></col>
				<col width="80"></col>
				<TR align=center>
					<th>No</th>
					<th>상품명</th>
					<th>수량</th>
					<th>보이기</th>
					<th>인원</th>
					<th>회원보기</th>
				</TR>
<?php
				if ($s_check=="0") {
					$sql = "SELECT b.productname, b.productcode, COUNT(a.productcode) as totcnt, b.display, ";
					$sql.= "b.tinyimage, b.quantity, b.selfcode, b.assembleuse FROM tblwishlist a, tblproduct b ";
					$sql.= "WHERE a.productcode = b.productcode GROUP BY b.productname, b.productcode,b.display,b.tinyimage, b.quantity, b.selfcode, b.assembleuse ";
				} else {
					$sql = "SELECT b.productname, b.productcode, COUNT(a.productcode) as totcnt, b.display, ";
					$sql.= "b.tinyimage, b.quantity, b.selfcode, b.assembleuse FROM tblwishlist a, tblproduct b ";
					$sql.= "WHERE a.productcode = b.productcode ";
					if ($s_check=="1") $sql.= "AND b.productname LIKE '%{$search}%' ";
					else if($s_check=="2") $sql.= "AND a.id LIKE '{$search}%' "; 
					$sql.= "GROUP BY b.productname, b.productcode,b.display,b.tinyimage, b.quantity, b.selfcode, b.assembleuse ";
				}
				$result = pmysql_query($sql,get_db_conn());
				$t_count = pmysql_num_rows($result);
				pmysql_free_result($result);
				$paging = new Paging($t_count,10,$listnum);
				$gotopage = $paging->gotopage;
				
				$sql.= "ORDER BY totcnt DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					echo "<tr>";
					echo "	<TD>{$number}</td>\n";
					echo "	<TD><NOBR><div class=\"ta_l\">";
					echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
					echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','')\"><FONT class=mainprname>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</font></a>";
					echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><img src=\"images/icon_newwin.gif\"  border=\"0\" hspace=\"2\" align=absmiddle></a>";
					echo "		</span>\n";
					echo "		<div id=primage{$cnt} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
					echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
					echo "		<tr bgcolor=#FFFFFF>\n";
					//$imgsrc = getTinyImageForXn($row->productcode);
					$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);
					?>
					<td align=center width=100% height=150 style="BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid; padding:5px;">
					<img src="<?=$imgsrc?>" style="max-width:300px"></td>
					<?
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "	</NOBR></td>\n";
					echo "	<TD><NOBR>".($row->quantity==NULL?"<font color=blue>무제한</font>":($row->quantity>0?$row->quantity:"<font color=red>품절</font>"))."</td>\n";
					echo "	<TD><b>{$row->display}</b></td>\n";
					echo "	<TD>{$row->totcnt}</td>\n";
					echo "	<TD><A HREF=\"javascript:GoMemberView('{$row->productcode}');\"><img src=\"images/btn_memberview.gif\" border=\"0\"></A></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				if ($cnt==0) {
					echo "<tr><td>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
				pmysql_free_result($result);
?>
<?php }else if($type=="1"){//등록회원 보기?>
				<TR align=center>
					<th>No</th>
					<th>회원ID</th>
					<th>회원정보 보기</th>
					<th>총 주문건수</th>
					<th>총 주문금액</th>
				</TR>
<?php
				$sql = "SELECT a.id, COUNT(b.price) as totcnt, SUM(b.price) as totprice ";
				$sql.= "FROM tblwishlist a LEFT OUTER JOIN tblorderinfo b ";
				$sql.= "ON (a.id = b.id AND b.deli_gbn = 'Y') ";
				$sql.= "WHERE a.productcode = '{$productcode}' ";
				$sql.= "GROUP BY a.id ";
				$result = pmysql_query($sql,get_db_conn());
				$t_count = pmysql_num_rows($result);
				pmysql_free_result($result);
				$paging = new Paging($t_count,10,20);
				$gotopage = $paging->gotopage;

				$sql.= "ORDER BY a.id ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					echo "<tr>";
					echo "	<TD>{$number}</td>\n";
					echo "	<TD><div class=\"ta_l\"><img src=\"images/icon_id.gif\" border=\"0\" align=absMiddle> <A HREF=\"javascript:Searchid('{$row->id}');\"><FONT class=mainprname>{$row->id}</font></A></div></td>\n";
					echo "	<TD><A HREF=\"javascript:MemberView('{$row->id}');\"><img src=\"images/bnt_memberview.gif\"  border=\"0\"></A></td>\n";
					echo "	<TD>".number_format($row->totcnt)."건</td>\n";
					echo "	<TD><b><span class=\"font_orange\">".number_format($row->totprice)."원</span></b></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				if ($cnt==0) {
					echo "<tr><td>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
				pmysql_free_result($result);
?>
				<?php }?>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
				echo "<tr>\n";
				echo "	<td width=\"100%\" align=center class=\"font_size\">\n";
				echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			</form>

			<form name=form2 action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=productcode value="<?=$productcode?>">
            <input type=hidden name=listnum value="<?=$listnum?>">
			</form>

			<form name=form4 action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>Wishlist 상품 관리</span></dt>
							<dd>
							- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.<br>
							- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.<br>
							- [회원보기] 버튼 클릭시 해당 상품을 Wishlist에 담은 회원리스트 및 총 구매건수, 총 구매금액을 확인할 수 있습니다.<br>
							- [회원아이디] 클릭시 해당 회원아이디로 WishList 상품 검색이 이루어집니다.<br>
							- [회원정보보기] 클릭시 해당 회원의 정보를 확인할 수 있습니다.
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
