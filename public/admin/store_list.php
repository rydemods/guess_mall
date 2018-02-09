<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "br-1";
$MenuCode = "brand";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 

//print_r($_POST);

if($_POST['mode'] == "delete") {
    $sql = "Delete from tblstore Where sno = ".$_POST['sno']."";
    pmysql_query($sql,get_db_conn());
}

################## 브랜드(벤더) 쿼리 ########################
$referer1 = '';
//$ref_qry="SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC";
$ref_qry = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
				FROM    tblvenderinfo a 
				JOIN    tblproductbrand b on a.vender = b.vender 
				ORDER BY b.brandname ASC
				";
$ref2_result=pmysql_query($ref_qry);
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-3 month'));
$period[5] = date("Y-m-d",strtotime('-6 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];
$store_name = $_POST["store_name"];
$sel_vender = $_POST["sel_vender"];
$selected[sel_vender][$sel_vender]='selected';
$sel_category = $_POST["sel_category"];
$selected[sel_category][$sel_category]='selected';

$search_start = $search_start?$search_start:$period[0];
$search_end = $search_end?$search_end:$period[0];
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

//매장명
if($store_name) {
    $where .= " AND    name like '%".$store_name."%' ";
}

//벤더
if($sel_vender) {
    $where .= " AND    vendor = {$sel_vender} ";
}

//매장구분
if($sel_category) {
    $where .= " AND    category = '{$sel_category}' ";
}

$query = "  SELECT  COUNT(*) 
            FROM    tblstore 
            where   1=1 
            ".$where."
        ";
$paging = new Paging($query,10,20);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT  sno, name, location, address, phone, view, area_code, category, vendor, stime, etime, 
                coordinate, store_code, regdt, com_name , b.brandname
        FROM    tblstore 
        join tblvenderinfo on tblstore.vendor = tblvenderinfo.vender LEFT JOIN    tblproductbrand b on tblvenderinfo.vender = b.vender 
        where   1=1 
        ".$where."
        order by name asc 
        ";
$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//echo "sql = ".$sql."<br>";
//exit();

?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="store_list.php";
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
	period[5] = "<?=$period[5]?>";


	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function StoreExcel() {
    //document.form1.target = "_blank";
	document.form1.action="store_list_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function StoreInfo(id){
	window.open("about:blank","store_set","width=800,height=750,scrollbars=no");
	document.storeform.target="store_set";

    if(id > 0) {
	    document.storeform.sno.value=id;
    }
	document.storeform.submit();
}

function StoreDel(id){

    if(confirm("정말 삭제하시겠습니까?")) {

        document.form1.mode.value="delete";
        document.form1.sno.value=id;
        document.form1.action="store_list.php";
        document.form1.submit();
    }
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 브랜드 관리 &gt; 브랜드 관리 &gt; <span>매장관리</span></p></div></div>
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
			<?php include("menu_brand.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">매장관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>매장정보를 확인할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">매장 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
            <input type=hidden name=mode>
			<input type=hidden name=sno>
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<!-- <TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
                                <img src=images/orderlist_1month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/orderlist_3month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
                                <img src=images/orderlist_6month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(5)">
							</td>
                        </TR> -->
                        <TR>
                            <th><span>매장명 입력</span></th>
                            <TD><input name=store_name size=47 value="<?=$store_name?>" class="input"></TD>
                        </TR>
                        <!-- <TR>
                            <th><span>브랜드</span></th>
                            <TD>
                                <select name=sel_vender class="select">
                                    <option value="">==== 전체 ====</option>
<?
                                while($ref2_data=pmysql_fetch_object($ref2_result)){
                                    if ( trim($ref2_data->com_name) == "" ) { continue; }
?>
                                    <option value="<?=$ref2_data->vender?>" <?=$selected[sel_vender][$ref2_data->vender]?>><?=$ref2_data->brandname?></option>
<?}?>
                                </select>&nbsp;
                            </TD>
					    </TR> -->
                        <TR>
                            <th><span>매장구분</span></th>
                            <TD>
                                <select name="sel_category">
                                    <option value="">==== 전체 ====</option>
                                <? foreach ($store_category as $k=>$v){ ?><option value="<?=$k?>" <?=$selected[sel_category][$k]?>><?=$v?><? } ?>
                                </select>
                            </TD>
					    </TR>

						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center">
                    <a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;
                    <a href="javascript:StoreInfo()"><img src="images/btn_badd2.gif" border="0"></a>&nbsp;
                    <a href="javascript:StoreExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a>
                </td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?php


		$colspan=10;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
			
				<TR >
					<th>번호</th>
					<th>브랜드</th>
					<th>지역</th>
					<th>매장구분</th>
					<th>매장명</th>
					<th>전화번호</th>
					<th>영업시간</th>
					<th>작성일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
		$colspan=10;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

            if( ($number%2)==0 ) $thiscolor="#FEF8ED";
            else $thiscolor="#FFFFFF";

            $regdt = substr($row->regdt, 0, 4)."-".substr($row->regdt, 4, 2)."-".substr($row->regdt, 6, 2)." ".substr($row->regdt, 8, 2).":".substr($row->regdt, 10, 2).":".substr($row->regdt, 12, 2);
?>
                <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=number_format($number)?></td>
                    <td><?=$row->brandname?></td>
                    <td><?=$store_area[$row->area_code]?></td>
                    <td><?=$store_category[$row->category]?></td>
                    <td><?=$row->name?> (<?=$row->store_code?>)</td>
                    <td><?=$row->phone?></td>
                    <td><?=$row->stime." ~ ".$row->etime?></td>
                    <td><?=$regdt?></td>
                    <td><a href="javascript:StoreInfo('<?=$row->sno?>');"><img src="img/btn/btn_cate_modify.gif" alt="수정" /></a></td>
                    <td><a href="javascript:StoreDel('<?=$row->sno?>')"><img src="img/btn/btn_cate_del01.gif" alt="삭제" /></a></td>
                </tr>
<?
			$cnt++;
		}
		pmysql_free_result($result);
		if($t_count==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			<!-- <input type=hidden name=tot value="<?=$cnt?>"> -->
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
            <input type=hidden name=store_name value="<?=$store_name?>">
            <input type=hidden name=sel_vender value="<?=$sel_vender?>">
            <input type=hidden name=sel_category value="<?=$sel_category?>">
			</form>

            <!-- 매장 추가 -->
            <form name=storeform action="store_modify.php" method=post>
			<input type=hidden name=type>
            <input type=hidden name=mode>
			<input type=hidden name=sno>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>-</span></dt>
							<dd>-</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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
?>
