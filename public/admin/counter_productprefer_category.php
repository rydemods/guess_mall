<?php

/*

에코먼트 용으로 제작함. 아자샵 공통적으로 적용 가능함.
혹시 쓸일이 있을지도...
원본 소스는 http://www.soapschool.co.kr/admin/counter_productprefer2.php
여기가면 있습니다 ㅇㅇ. 원재 야캐요

*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("calendar.php");//달력관련스크립트
include("access.php");//접근권한
include("header.php");//헤↗더↘
####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################절취선##########################

#####정렬기준 시작######
$sort  = $_POST['sort']?$_POST['sort']:'Q';
$chk_asc_desc = "desc ";
$sort_array = array('Q'=>'sell_count','B'=>'order_count','P'=>'total_price');
$query_sort = " ORDER BY ".$sort_array[$sort]." ".$chk_asc_desc;
//exdebug($query_sort);
#####정렬기준 끝#######

#####검색기간 관련 시작######
$search_start = $_POST['search_start'];
$search_end = $_POST['search_end'];
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime-(60*60*24));
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$search_start = $search_start?$search_start:$period[0];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start):str_replace("-","",$period[0]);
$search_e=$search_end?str_replace("-","",$search_end):date("Ymd",$CurrentTime);

if($search_s > $search_e){//검색기간이잘못되면은심각한쿼리오류가생길수있으므로 미리 막음
	echo "<script>alert('검색기간 오류! 처음부터 다시 시작해주세요');</script>";
	exit;
}
if($search_s == date("Ymd")){
	$search_s = date("Ymd",strtotime('-1 day'));//시작날짜가 오늘일 경우 조회 안됨 하루전 날짜로 다시계산
}
if($search_s == $search_e){//검색시작날짜,종료날짜 같을경우 검색날짜 쿼리
	$query_date = " AND substr(a.ordercode,1,8) ='".$search_s."'";
}else{
	$query_date = " AND substr(a.ordercode,1,8) >='".$search_s."'";
	$query_date .= " AND substr(a.ordercode,1,8) <='".$search_e."'";
}//검색기간은 WITH 절에 들어감
//exdebug($search_s);
//exdebug($search_e);
//exdebug($query_date);
#####검색기간 관련 끝#######

#####카테고리 코드 관련 시작#####
$code_a=$_REQUEST["code_a"];
$code_b = $_REQUEST["code_b"];
$code_c = $_REQUEST["code_c"];
$code_d = $_REQUEST["code_d"];
$code = $code_a.$code_b.$code_c.$code_d; //exdebug($code);
$query_cate = " AND b.c_category like '".$code."%' "; //카테고리 조건
#####카테고리 코드 관련 끝#######

#####쿼리문 및 페이징 시작#####
if($code_a){//카테고리가 하나도 선택되지 않으면 쿼리 실행 안함
//-------WITH STRAT-----
$sql = " WITH v1 AS ( ";
$sql .= "select 
	a.productcode, 
	c.productname,
	c.tinyimage,
	sum(a.quantity) as sell_count,
	count(a.ordercode) as order_count,
	sum(a.price * a.quantity) as total_price";
$sql .= " from tblorderproduct a
	join tblproduct c on a.productcode=c.productcode";//table 조인
$sql .= " where 1=1 AND a.deli_gbn='Y' "; //기본 where절
$sql .= $query_date; //검색기간 where절에 추가
$sql .= " group by a.productcode, c.productname, c.tinyimage";
$sql .= " ) ";
//-------WITH END-----//

//-------실제 데이터 불러올 sql-----//
$sql .= "select 
	productcode,
	max(b.c_category) as category,
	max(productname) as productname,
	max(tinyimage) as tinyimage,
	max(sell_count) as sell_count,
	max(order_count) as order_count, 
	max(total_price) as total_price ";
$sql .= " from tblproductlink b ";
$sql .= " join v1 on v1.productcode=b.c_productcode ";
$sql .= $query_cate ;//조건에 맞는 카테고리 상품만 가져옴 ㅇㅇ
$sql .= " group by productcode  ";
$sql .= $query_sort; //정렬조건 판매수량,구매자수,총판매액
//-------실제 데이터 불러올 sql 끝-----//

//-------페이징-------//
$sql_count = "select count(*) from (" ;//페이징을 위해 전체 레코드 수를 구하는 쿼리
$sql_count .= $sql;
$sql_count .= " ) as count ";
$paging = new Paging($sql_count,10,20);
/*아자샵 기본 페이징 메소드가 일반 쿼리를 바로 넣어주면 안되고 반드시 count()를 [첫번째 select]로 지정해서 전체 레코드 수를 구해서 넣어줘야만 한다(ex.select count(*) from..)
페이징 함수내부에서 첫번째 컬럼의 값을 총 레코드로 인식해서 페이징을 생성해주기 때문이다. 
이페이지에 쓰이는 실쿼리문($sql)은 group by절이 중복으로 묶여서 일반적인 count(*)로 총 레코드를 구 할 수가 없기 때문에 전체 쿼리($sql_count)로 묶어서 count(*)를 강제로 잡아줌*/
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
$sql = $paging->getSql($sql);
$result = pmysql_query($sql,get_db_conn());
//-------페이징 끝------//
}//if($code_a)끝
#####쿼리문 및 페이징 끝#####
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

function OnChangePeriod(val) {//검색기간 오늘 7일 14일 한달 잡아주는 함수
	var pForm = document.form2;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoSearch(){//검색버튼 누르면 조건 처리 및 submit
	var s_date = document.form2.search_start.value;
	var e_date = document.form2.search_end.value;
	s_date = s_date.replace(/-/g,"");
	e_date = e_date.replace(/-/g,"");
	if(s_date > e_date){
		alert("검색시작날짜가 검색종료날짜 보다 큽니다");
		return 0;
	}
	var chk_code = document.form2.code_a.value;
	if(!chk_code){
		alert("카테고리를 지정해 주세요");
		return 0;
	}
	document.form2.submit();
}

function GoPage(block,gotopage) {//페이징함수
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}

$(function(){//정렬해주는 함수 [판매수량][구매자수][매출액] defaul는 판매수량 ㅇㅇ
	$("#sort").change(function(){
		$("#sort_form").val($(this).val());
		document.form2.submit();
	});
});
</script>

<link rel="styleSheet" href="/css/admin.css" type="text/css">
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 고객 선호도 분석 &gt;<span>인기상품 분석</span></p></div></div>
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
<?php } else {?>
			<table cellpadding="5" cellspacing="0" width="100%" style="table-layout:fixed">
<?php }?>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">인기상품 분석인데 사용 안함 ㅇㅇ</div>
				</td>
			</tr>
			<tr>
				<td height="20">
				<!--카테고리 삽입할 영역 -->
			
				<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden id="sort_form" name=sort>
				<input type=hidden name=block value="">
				<input type=hidden name=gotopage value="">
					<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<TR>
							<th><span>검색기간</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" readonly  OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" readonly  OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql_cate = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								$sql_cate.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
								$i=0;
								$ii=0;
								$iii=0;
								$iiii=0;
								$strcodelist = "";
								$strcodelist.= "<script>\n";
								$result_cate = pmysql_query($sql_cate,get_db_conn());
								$selcode_name="";
								//exdebug($sql_cate);
								$call_cate_name="";
								while($row_cate=pmysql_fetch_object($result_cate)) {
									###카테고리 이름 배열 만들긔###
									$call_cate_name[$row_cate->code_a.$row_cate->code_b.$row_cate->code_c.$row_cate->code_d] = $row_cate->code_name;
									#########################
									$strcodelist.= "var clist=new CodeList();\n";
									$strcodelist.= "clist.code_a='{$row_cate->code_a}';\n";
									$strcodelist.= "clist.code_b='{$row_cate->code_b}';\n";
									$strcodelist.= "clist.code_c='{$row_cate->code_c}';\n";
									$strcodelist.= "clist.code_d='{$row_cate->code_d}';\n";
									$strcodelist.= "clist.type='{$row_cate->type}';\n";
									$strcodelist.= "clist.code_name='{$row_cate->code_name}';\n";
									if($row_cate->type=="L" || $row_cate->type=="T" || $row_cate->type=="LX" || $row_cate->type=="TX") {
										$strcodelist.= "lista[{$i}]=clist;\n";
										$i++;
									}
									if($row_cate->type=="LM" || $row_cate->type=="TM" || $row_cate->type=="LMX" || $row_cate->type=="TMX") {
										if ($row_cate->code_c=="000" && $row_cate->code_d=="000") {
											$strcodelist.= "listb[{$ii}]=clist;\n";
											$ii++;
										} else if ($row_cate->code_d=="000") {
											$strcodelist.= "listc[{$iii}]=clist;\n";
											$iii++;
										} else if ($row_cate->code_d!="000") {
											$strcodelist.= "listd[{$iiii}]=clist;\n";
											$iiii++;
										}
									}
									$strcodelist.= "clist=null;\n\n";
								}
								pmysql_free_result($result_cate);
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
				</form>
				<p class="ta_c"><a href="#" onClick="GoSearch();"><img src="img/btn/btn_search01.gif"></a></p>
				</td>
			</tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
						<?
						echo "검색기간: <b><font color=\"#FF6633\">".substr($search_s,0,4)."년 ".substr($search_s,4,2)."월 ".substr($search_s,6,2)."일"."</font></b>";
						echo "~ <b><font color=\"#FF6633\">".substr($search_e,0,4)."년 ".substr($search_e,4,2)."월 ".substr($search_e,6,2)."일"."</font></b>";
						?>
						</td>
						<td align="right">
							<span>정렬기준:</span>
							<select id="sort">
								<option value="Q" <?if($sort=='Q')echo 'selected'?>>판매수량</option>
								<option value="B" <?if($sort=='B')echo 'selected'?>>구매자수</option>
								<option value="P" <?if($sort=='P')echo 'selected'?>>매출액</option>
							</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
                    <TR>
                    	<th>NO</th>
						<th></th>
						<th>상품명</th>
						<th>세부카테고리</th>
                        <th>총 구매수량</th>
                        <th>구매자수</th>
						<th>매출액</th>
					</TR>
				<?$count_no=1;?>
				<?if(!$result){?>
					<tr>
						<td>검색된 데이터가 없습니다</td>
					</tr>
				<?}?>
				<?while($row=pmysql_fetch_object($result)){?>
					
					<tr>
						<td><?=$count_no?></td>
						<td><img src="http://<?=$shopurl.DataDir?>shopimages/product/<?=$row->tinyimage?>" width=40 border=0></td>
						<td><A HREF="http://<?=$shopurl?>front/productdetail.php?productcode=<?=$row->productcode?>" target="_blank"><?=$row->productname?></a></td>
						<td><?=$call_cate_name[$row->category]?></td>
						<td><?=$row->sell_count?>개</td>
						<td><?=$row->order_count?>명</td>
						<td><?=number_format($row->total_price)?>원</td>
					</tr>
				<?$count_no++;?>
				<?}?>
					</table>
                    </div>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=30 align=center><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
				</tr>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="font_white" align=right>
						
							<TR>
								<td align=right style="padding:20,20,0,5">
								<!--
								<A HREF="javascript:print()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0">
								</A>
								-->
								</td>
							</TR>
					</table>
					</td>
				</tr>
			
			<tr><td height="20"></td></tr>
<?php if($print!="Y"){?>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>어떤 상품이 가장 많이 판매되었는지 알 수 있습니다.</span></dt>
                    </dl>
					
                    <dl>
                    	<dt><span>판매된 상품들 중, 판매량이 높은 상품들의 개별적인 정보를 조회 할 수 있습니다.</span></dt>
                    </dl>
					 <dl>
                    	<dt><span>당일날짜 데이터는 조회 하실 수 없습니다(하루가 지나야 상품판매데이터가 집계됩니다).당일 날짜로 검색하시면 자동으로 전날 데이터가 조회됩니다.</span></dt>
                    </dl>
					<dl>
                    	<dt><span></span></dt>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr><td height="50"></td></tr>
<?php }?>
			</table>
<?php if($print!="Y"){?>
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
}