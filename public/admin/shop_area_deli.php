<?php
/********************************************************************* 
// 파 일 명		: shop_area_deli.php 
// 설     명		: 지역별 배송비 설정
// 상세설명	: 지역별로 추가 배송비를 설정한다.
// 작 성 자		: 
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "me-4";
	$MenuCode = "shop";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################
//exdebug($_POST);
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST["mode"];
	$area_no=$_POST["area_no"];
	$del_no=$_POST["del_no"];
	$search= strtolower(trim($_POST["search"]));

#---------------------------------------------------------------
# 지역리스트를 삭제한다.
#---------------------------------------------------------------
	if($mode=="area_alldel") {				// DB를 삭제한다.
		foreach($area_no as $an=>$anv){
			pmysql_query("delete from tbldeliarea where no='".$anv."'");
		}
		$onload="<script>window.onload=function(){alert('삭제되었습니다.');location.href='shop_area_deli.php'; }</script>\n";
	}else if($mode=="area_del"){
		pmysql_query("delete from tbldeliarea where no='".$del_no."'");
		$onload="<script>window.onload=function(){alert('삭제되었습니다.');location.href='shop_area_deli.php'; }</script>\n";
	}
#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	//var_dump($_POST);
	$listnum    = $_POST["listnum"] ?: "10";

	if(ord($search)) {
		$qry.= "where lower(area_name) LIKE '%{$search}%' ";
	}

	include("header.php");  // 상단부분을 불러온다.

#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$sql = "SELECT COUNT(*) as t_count FROM tbldeliarea {$qry} ";
	$paging = new Paging($sql,10,$listnum);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;			
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function Searchdeil() {
    document.formSearch.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function CheckAllDelete() {

	if($("input:checkbox[id='area_no']").is(":checked")==false){
		alert("선택하신 리스트가 없습니다.");
		return;
	}else{
		if (confirm("삭제하시겠습니끼?")) {
			$("#mode").val("area_alldel");
			document.form1.submit();
		}
	}
}
function CheckDelete(no) {

	if (confirm("삭제하시겠습니끼?")) {
		$("#mode").val("area_del");
		$("#del_no").val(no);
		document.form1.submit();
	}

}

function Area_ins() {
	window.open("./shop_area_deli_pop.php","areaopop","width=567,height=650,scrollbars=yes");

}

function excel_download() {
	document.form1.action="shop_area_deli_excel.php";
	document.form1.submit();
	document.form1.action="";
}

</script>
<style>
a.btn_blue {display:block;color:#507291;background-color:#FFFFFF;font-size:8pt;border:1px solid #507291;padding:0px 0px;text-align:center;text-decoration:none;}
a.btn_blue:hover {color:#FFFFFF;background-color:#507291;text-decoration:none;}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 배송관리 &gt;<span>지역별 배송비 설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">지역별 배송비 설정</div>
				</td>
			</tr>
			<!-- <form name="sForm" method="post">	 -->
            <form name="formSearch" method="post">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>특수지역명 검색</span></th>
					<td>
						<input type=text name=search value="<?=$search?>" class="input" style="width:300px;">
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center><a href="javascript:Searchdeil();"><span class="btn-point">검색</span></a></td>
			</tr>
			</form>
			<form name="form1" method="post">
			<input type="hidden" name="mode" id="mode">
			<input type="hidden" name="del_no" id="del_no">
			<tr><td height=20></td></tr>
			
			<tr>
				<td>
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td>
								<!-- 소제목 -->
								<div class="title_depth3_sub">추가지역 리스트</div>
							</td>
							<td align='right'>
								<a href="javascript:Area_ins()"><span class="btn-point">등록</span></a>
								<a href="javascript:excel_download()"><span class="btn-point blk">엑셀 다운로드</span></a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="40"></col>
				<col width="45"></col>
				<col width="180"></col>
				<col width=""></col>
				<col width="150"></col>
				<col width="45"></col>
				<TR align=center>
					<th>선택</th>
					<th>번호</th>
					<th>특수지역명</th>
					<th>우편번호 범위</th>
                    <th>배송비</th>
                    <th>삭제</th>
				</TR>
	
<?php
#---------------------------------------------------------------
# 벤더 정보 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {
           	$sql = "SELECT * FROM tbldeliarea {$qry} order by no desc";

			$sql = $paging->getSql($sql);

			$result=pmysql_query($sql,get_db_conn());
            //echo $sql;
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
?>
				<tr bgcolor=#FFFFFF onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#FFFFFF'">
					<TD><p align="center"><input type=checkbox name="area_no[]" id="area_no" value="<?=$row->no?>" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></td>
					<td align=center><?=$number?></td>
					<td align=center><?=$row->area_name?></td>
					<td style='text-align:center'>[<?=$row->st_zipcode?>] 부터 [<?=$row->en_zipcode?>] 까지</td>
					<td style='text-align:center'><?=number_format($row->deli_price)?>원</td>
					<td style='text-align:left'><a href="javascript:CheckDelete(<?=$row->no?>)"><img src="images/btn_del.gif"></a></td>			
				</tr>
<?				$i++;
			}
			pmysql_free_result($result);
		} else {
?>
				<tr><td colspan=14 align=center>검색된 정보가 존재하지 않습니다.</td></tr>
<?
		}
?>
				</TABLE>
				</form>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=250></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left'>
					<a href="javascript:CheckAllDelete();"><span class="btn-basic">삭제하기</span></a>
					</td>
					<td align='center'>
					<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
					</table></td>
					
				<tr>
				</table>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>지역별 배송비는 도서지역, 산간지역 등의 배송비가 추가로 과금되는 지역을 설정할 수 있습니다.</li>
							<li>
								<dl>
									<dt>엑셀파일로 등록 시 <b>우편번호 형식</b>을 지켜주셔야 적용됩니다.</dt>
									<dd> - 시작 우편번호가 끝 우편번호보다 클 경우 등록되지 않습니다.<br>
									- 우편번호 자리수를 지켜주셔야 합니다. (5자리)</dd>
								</dl>
							</li>
							<li>우편번호 범위가 겹치는 구간의 배송비를 다르게 설정한 경우 더 높은 배송비가 부과됩니다.</li>
							<li>네이버페이 주문 건은 지역별 배송비를 설정하여도 부과되지 않습니다. 네이버 페이 주문으로 인해 발생하는 추가 배송비는 고객에게 별도 결제를 받으시기 바랍니다.</li>
						</ul>
						<!-- <dl>
							<dt><span>Vender 정보관리</span></dt>
							<dd>- 등록된 Vender 리스트와 기본적인 정보사항을 확인할 수 있습니다.<br>
							- 입점사 정보변경은 [관리]를 이용하여 변경할 수 있습니다.<br>
							- 입점사 관리자 URL은 <B><font class=font_orange><A HREF="http://<?=$_ShopInfo->getShopurl()?>vender/" target="_blank">http://<?=$_ShopInfo->getShopurl()?>vender/</A></font></B> 입니다.
							</dd>	
						</dl> -->

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			
			<form name="pageForm" method="post">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			<input type=hidden name='listnum' value='<?=$listnum?>'>
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php"); // 하단부분을 불러온다. 
