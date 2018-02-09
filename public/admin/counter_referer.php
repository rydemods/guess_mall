<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###########
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//print_r($_POST);
################## 가입경로 쿼리 ########################
$referer1 = '';
$ref_qry="select idx,name from tblaffiliatesinfo order by name";
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
$referer1 = $_POST["referer1"];
$selected[referer1][$referer1]='selected';

$search_start = $search_start?$search_start:$period[3];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

//유입경로
if($referer1) {
    $qry = " AND    c.idx = {$referer1} ";
}

$qry_from = "
                FROM 
                (
                    SELECT  count(a.id) tot_cnt,  sum(case b.mb_type when 'web' then 1 end) tot_cnt_b_web, (count(a.id) - sum(case b.mb_type when 'web' then 1 end)) tot_cnt_b_fb , 0 tot_cnt_e_web, 0 tot_cnt_e_fb,
                            c.type, case c.type when 1 then '학교' else '기업' end as gubun,
                            c.area, c.name as ref_name1 
                    FROM    tblmember_rf a 
                    JOIN    tblmember b ON a.id = b.id 
                    JOIN    tblaffiliatesinfo c ON b.mb_referrer1 = c.idx::varchar 
                    WHERE   1=1 
                    AND     a.date >= '{$search_s}' AND a.date <= '{$search_e}' 
                    ".$qry." 
                    AND     a.rf_type = 'B' 
                    GROUP BY c.type, c.area, c.name 
                    UNION ALL
                    SELECT  count(a.id) tot_cnt,  0 tot_cnt_b_web, 0 tot_cnt_b_fb, sum(case b.mb_type when 'web' then 1 end) tot_cnt_e_web, (count(a.id) - sum(case b.mb_type when 'web' then 1 end)) tot_cnt_e_fb ,
                            c.type, case c.type when 1 then '학교' else '기업' end as gubun,
                            c.area, c.name as ref_name1 
                    FROM    tblmember_rf a 
                    JOIN    tblmember b ON a.id = b.id 
                    JOIN    tblaffiliatesinfo c ON b.mb_referrer1 = c.idx::varchar 
                    WHERE   1=1 
                    AND     a.date >= '{$search_s}' AND a.date <= '{$search_e}' 
                    ".$qry." 
                    AND     a.rf_type = 'E' 
                    GROUP BY c.type, c.area, c.name 
                ) z 
        ";


include("header.php"); 


$query = "SELECT COUNT(*) 
            FROM (
                SELECT  sum(z.tot_cnt) tot_cnt, sum(z.tot_cnt_b_web) tot_cnt_b_web, sum(z.tot_cnt_b_fb) tot_cnt_b_fb, sum(z.tot_cnt_e_web) tot_cnt_e_web, sum(z.tot_cnt_e_fb) tot_cnt_e_fb, 
                        z.gubun, z.area, z.ref_name1 
                ".$qry_from."
                GROUP BY z.gubun, z.area, z.ref_name1 
            ) a 
        ";
$paging = new Paging($query,10,30);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT  sum(z.tot_cnt) tot_cnt, sum(z.tot_cnt_b_web) tot_cnt_b_web, sum(z.tot_cnt_b_fb) tot_cnt_b_fb, sum(z.tot_cnt_e_web) tot_cnt_e_web, sum(z.tot_cnt_e_fb) tot_cnt_e_fb, 
                z.gubun, z.area, z.ref_name1 
        ".$qry_from."
        GROUP BY z.gubun, z.area, z.ref_name1 
        ORDER BY z.ref_name1 
        ";
$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//echo "sql = ".$sql."<br>";
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="counter_referer.php";
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

function OrderExcel() {
    //document.form1.target = "_blank";
	document.form1.action="counter_referer_excel.php";
	document.form1.submit();
	document.form1.action="";
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석  &gt; 회원가입분석 &gt;<span>회원가입 분석 통계</span></p></div></div>
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
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">회원가입 분석 통계</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>일별 회원가입 경로를 확인 할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">회원가입현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td>
				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<!-- <img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)"> -->
                                <img src=images/orderlist_1month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/orderlist_3month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
                                <img src=images/orderlist_6month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(5)">
							</td>

                        <TR>
                            <th><span>유입경로</span></th>
                            <TD>
                                <select name=referer1 class="select">
                                    <option value="">==== 전체 ====</option>
<?
                                while($ref2_data=pmysql_fetch_object($ref2_result)){?>
                                    <option value="<?=$ref2_data->idx?>" <?=$selected[referer1][$ref2_data->idx]?>><?=$ref2_data->name?></option>
<?}?>
                                </select>&nbsp;
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
				<td style="padding-top:4pt;" align="center"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a></td>
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
				<!-- <col width=40></col>
				<col width=80></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<input type=hidden name=chkordercode> -->
			
				<TR >
					<th rowspan=2>번호</th>
					<th rowspan=2>구분</th>
					<th rowspan=2>지역</th>
					<th rowspan=2>학교/기업명</th>
					<th colspan=2>배너가입</th>
					<th colspan=2>이메일가입</th>
				</TR>
                <TR >
					<th>일반</th>
					<th>facebook</th>
					<th>일반</th>
					<th>facebook</th>
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
?>
                <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=number_format($number)?></td>
                    <td><?=$row->gubun?></td>
                    <td><?=$row->area?></td>
                    <td><?=$row->ref_name1?></td>
                    <td><?=number_format($row->tot_cnt_b_web)?></td>
                    <td><?=number_format($row->tot_cnt_b_fb)?></td>
                    <td><?=number_format($row->tot_cnt_e_web)?></td>
                    <td><?=number_format($row->tot_cnt_e_fb)?></td>
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
            <input type=hidden name=referer1 value="<?=$referer1?>">
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
