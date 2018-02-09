<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$pidx=$_REQUEST["pidx"];
$idx=$_REQUEST['idx']; 
$mode=$_REQUEST['mode'];
$itemCount=(int)$_REQUEST["itemCount"];
$start_date = $_REQUEST["start_date"];
$end_date = $_REQUEST["end_date"];
$no_coupon = $_REQUEST["no_coupon"]?$_REQUEST["no_coupon"]:"N";
$imagepath = $cfg_img_path['timesale'];
$filedata= new FILE($imagepath);
$image_type = $_REQUEST['image_type'];
$member = $_REQUEST['member'];
$member_type = implode($_REQUEST['member_type'],",");
echo $member_type;
$errmsg = $filedata->chkExt();
if($errmsg==''){
	$up_file = $filedata->upFiles();
}

$content = $_REQUEST["content"];
if(ord($_REQUEST["mode2"])>0){
	$ppidx_ = $_REQUEST["ppidx"];
	$pidx_ = $_REQUEST["pidx"];
	$sql = "DELETE FROM tblfamily WHERE idx = '{$ppidx_}' AND promo_idx = '{$pidx_}' ";
	pmysql_query($sql);
	echo "<script>alert('삭제되었습니다.')</script>";
}

$cqry="select count(*) from tblfamily WHERE promo_idx='{$pidx}'"; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$count=$crow['count'];

$cqry="select count(*) from tblfamily_list "; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$mcount=$crow['count'];

######회원등급리스트 받아오기
$mem_sql = "select group_code,group_name from tblmembergroup";
$mem_res = pmysql_query($mem_sql,get_db_conn());
while($mem_row = pmysql_fetch_object($mem_res)){
	$mem[] = $mem_row;
}

switch($mode){
	case "del" : 	$seq=$_REQUEST['seq']; /*삭제할때 삭제할 로우보다 진열 순위가 낮은 로우를 한개씩 위로 올림*/
				$dcsql = "SELECT count(*) FROM tblfamily_list WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
				$dcres = pmysql_query($dcsql,get_db_conn());
				$dcrow=pmysql_fetch_array($dcres);
				if($dcrow[0]!=0){
					$dusql = "UPDATE tblfamily_list SET display_seq = display_seq-1 
						WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
					pmysql_query($dusql,get_db_conn());
				}
				/*메인 타이틀 삭제*/
				$dsql = "DELETE FROM tblfamily_list WHERE idx='{$pidx}'";
				pmysql_query($dsql);	
				
				/*상품 삭제*/
				$ddsql = "SELECT seq FROM tblfamily WHERE promo_idx='{$pidx}'";//seq조건으로 변경함
				$ddres = pmysql_query($ddsql);
				//$ddrow= pmysql_fetch_object($ddres);
				/*for($i=0;$i<count($ddrow);$i++){	
					$dsql2 = "DELETE FROM tblfamily_product WHERE special='".$ddrow->seq."'";
					pmysql_query($dsql2);
				}*/	
				##상품삭제부분 잘못되서 수정함 150921 원재
				while($ddrow = pmysql_fetch_object($ddres)){
					$dsql2 = "DELETE FROM tblfamily_product WHERE special='".$ddrow->seq."'";
					pmysql_query($dsql2);
				}
				/*서브 타이틀 삭제*/	 
				$dsql3 = "DELETE FROM tblfamily WHERE promo_idx='{$pidx}' "; 
				pmysql_query($dsql3);		
				
				echo "<script>alert('삭제되었습니다.');</script>";
				echo "<script>document.location.href='market_family.php';</script>";
				break; 
				
	case "ins" : $count=$count+1; $mcount= $mcount+1; break;	 
				
	case "ins_submit" : $ptitle = $_POST["ptitle"]; $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = $_POST["mtitle"]; $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$mcount++;
						
						$mnsql = "select idx from tblfamily_list order by idx desc";
						$mnres = pmysql_query($mnsql);
						$tempx = 1;
						while($mnrow = pmysql_fetch_object($mnres)){
							if($tempx <= $mnrow->idx){
								$tempx = $mnrow->idx+1;								
							}							
						}
						
						$misql = "insert into tblfamily_list (idx, title, banner_img, display_type, display_seq, rdate, start_date, end_date, no_coupon,image_type, content,title_banner,member_type,member) ";
						$misql.= "values('".$tempx."', '{$mt}', '{$up_file['banner_img'][0]['v_file']}', '{$mdt}', '{$mds}', current_date, '{$start_date}', '{$end_date}', '{$no_coupon}','{$image_type}', '{$content}','{$up_file['title_banner'][0]['v_file']}','{$member_type}','{$member}')";
						//exdebug($misql);
						
						pmysql_query($misql);
						
						for($aa=0;count($pt)>$aa;$aa++){						
							$csql = "SELECT count(*) FROM tblfamily where  promo_idx='{$tempx}'  ";
							$cres = pmysql_query($csql,get_db_conn());
							$crow=pmysql_fetch_array($cres);
							if($crow[0]!=$ps[$aa]+1){ /*새로 등록할때 지정한 진열순위가 맨 뒤가 아니라면 지정한 순위부터 뒤에 로우를 한칸씩 뒤로 민다.*/
								$usql = "UPDATE tblfamily SET display_seq = display_seq+1 
										WHERE idx = ( select * from (select idx where  promo_idx='{$tempx}' AND display_seq >= {$ps[$aa]}) as a)";
								pmysql_query($usql,get_db_conn());					
							}
							 
							$isql = "INSERT INTO tblfamily (	idx,
																title, 
																info, 
																display_seq, 
																display_tem, 
																rdate,
																promo_idx
																) ";
							$isql.= "values (  {$pidxs[$aa]},
											'{$pt[$aa]}',
											'{$pi[$aa]}',
											{$ps[$aa]},
											{$pte[$aa]},
											current_date,
											'{$tempx}'
											)"; 
							pmysql_query($isql,get_db_conn());
						}
						echo "<script>alert('등록되었습니다.');</script>";
						echo "<script>document.location.href='market_family.php';</script>";
						break;
					
	case "mod_submit" :  $ptitle = $_POST["ptitle"]; $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = $_POST["mtitle"]; $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$arrPromoSeq = explode(",", $_POST["ppromo_seq"]);
						$promo_code = $_POST["promo_code"];
						$promo_view = $_POST["promo_view"];


						$musql = "SELECT display_seq FROM tblfamily_list WHERE idx='{$pidx}' ";
						$mures = pmysql_query($musql);	
						$murow = pmysql_fetch_array($mures);
						
						if($murow[0]!=$mds){ /*수정할때 지정한 진열 순위에 따라 나머지 로우들도 진열 순위를 수정함*/
							if($murow[0]<$mds){
								$usql = "UPDATE tblfamily_list SET display_seq = display_seq-1 
										WHERE idx = ( select * from (select idx where display_seq between {$murow[0]} and {$mds}) as a)";
								pmysql_query($usql,get_db_conn());
							} 
							if($murow[0]>$mds){
								$usql = "UPDATE tblfamily_list SET display_seq = display_seq+1 
										WHERE idx = ( select * from (select idx where display_seq between {$mds} and {$murow[0]}) as a)";
								pmysql_query($usql,get_db_conn());	
							}
						}
						 /*메인테이블 업데이트*/
						$musql = "update tblfamily_list set title = '{$mt}', display_type = '{$mdt}', display_seq =  '{$mds}', promo_code =  '{$promo_code}', promo_view =  '{$promo_view}', 
								start_date = '{$start_date}', end_date = '{$end_date}', no_coupon = '{$no_coupon}', image_type = '{$image_type}', content = '{$content}' ";						
						if($up_file['banner_img'][0]['v_file']){
							$musql.=", banner_img = '{$up_file['banner_img'][0]['v_file']}' ";

							list($temp_banner_img)=pmysql_fetch("select banner_img from tblfamily_list where idx='{$pidx}'");
							if($temp_banner_img) @unlink($imagepath.$temp_banner_img);
						}
						// 핏플랍 모바일 타이틀 베너
						if($up_file['title_banner'][0]['v_file']){
							$musql.=", title_banner = '{$up_file['title_banner'][0]['v_file']}' ";

							list($temp_tbanner_img)=pmysql_fetch("select title_banner from tblfamily_list where idx='{$pidx}'");
							if($temp_tbanner_img) @unlink($imagepath.$temp_tbanner_img);
						}
						$musql .= ", member_type='{$member_type}' ";
						$musql .= ", member = '{$member}' ";
						$musql .= " where idx='{$pidx}' ";						
						pmysql_query($musql);





						$promotion_sql = "SELECT seq FROM tblfamily WHERE promo_idx='{$pidx}'";
						$promotion_result = pmysql_query($promotion_sql,get_db_conn());
						$arrTempSeq = array();
						while($promotion_row=pmysql_fetch_object($promotion_result)) {
							$arrTempSeq[] = $promotion_row->seq;
						}
						$arrDeletePromotion = array_diff($arrTempSeq, $arrPromoSeq);
						foreach($arrDeletePromotion as $kk => $vv){
							$mdsql = "DELETE FROM tblfamily WHERE seq='{$vv}'";
							pmysql_query($mdsql);
							$mdsql = "DELETE FROM tblfamily_product WHERE special='{$vv}'";
							pmysql_query($mdsql);
						}

						for($aa=0;count($pt)>$aa;$aa++){
							if($arrPromoSeq[$aa] != 'undefined' && $arrPromoSeq[$aa]){	//$arrPromoSeq[$aa] 조건 추가 by PTY - 2014.10.14
								$isql = "UPDATE tblfamily SET idx = {$pidxs[$aa]}, title = '{$pt[$aa]}', info = '{$pi[$aa]}', display_seq = {$ps[$aa]}, display_tem = {$pte[$aa]}, rdate = current_date, promo_idx = '{$pidx}' WHERE seq = '".$arrPromoSeq[$aa]."'"; 
							}else{
								
								$isql = "INSERT INTO tblfamily 
											(idx, title, info, display_seq, display_tem, rdate, promo_idx) ";
								$isql.= "values 
											({$pidxs[$aa]}, '{$pt[$aa]}', '{$pi[$aa]}', {$ps[$aa]}, {$pte[$aa]}, current_date, '{$pidx}')"; 
							}
							pmysql_query($isql);
						}
						
						echo "<script>alert('수정되었습니다.');</script>";
						echo "<script>document.location.href='market_family.php';</script>";
						break;
}
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
 
<script language="JavaScript">
function tr_remove(){
	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	document.eventform.itemCount.value = itemCount;
	$(".table_style01 [name=promotable]:last").remove();
	
}
function chkfrm()	{
	var itemCount = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
	var mode = document.eventform.mode.value;
	if(mode=="ins"){  
		document.eventform.mode.value = "ins_submit";
	}else if(mode=="mod"){
		document.eventform.mode.value = "mod_submit";
	} 
	//promo_seq
	for(var i=1;i<=itemCount;i++){ 
		for(var ii=0;ii<6;ii++){
			var itemname
			var hiddenname
			switch(ii){
				case 0 : itemname = ".item"+i+" [name=title]";	
						hiddenname = document.eventform.ptitle;						
						break;
				case 1 : itemname = ".item"+i+" [name=info]";	
						hiddenname = document.eventform.pinfo;
						break;
				case 2 : itemname = ".item"+i+" [name=display_seq]";	
						hiddenname = document.eventform.pseq;
						break;
				case 3 : itemname = ".item"+i+" [name=display_tem]";	
						hiddenname = document.eventform.ptem;
						break;
				case 4 : itemname = ".item"+i+" [name=ppidx]";	
						hiddenname = document.eventform.pppidx;
						break;
				case 5 : itemname = ".item"+i+" [name=promo_seq]";	
						hiddenname = document.eventform.ppromo_seq;
						break;
			}						
			if(hiddenname.value==""){
				hiddenname.value =$(itemname).val();
			}else{ 
				hiddenname.value = hiddenname.value+","+$(itemname).val();
			}	
		}
	}
	var sHTML = oEditors.getById["ir1"].getIR();
	document.eventform.content.value=sHTML;
}
$(document).ready(function(){
	if($("#mem0").attr('checked')){
		$("input[name='member_type[]']").attr('disabled','disabled');
	}
	$("#mem0").click(function(){
		$("input[name='member_type[]']").attr('disabled','disabled');
	});
	$("#mem1").click(function(){
		$("input[name='member_type[]']").removeAttr('disabled');
	});
});
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>패밀리세일 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">

	<div class="title_depth3">패밀리세일 <?if($mode=="ins"){echo "등록";}else{echo "수정";} ?>
		<a href="#">
			<img align="right" class="tr_remove" src="../admin/images/botteon_del.gif" align="right" alt="삭제하기" onclick="javascript:tr_remove()"></a>
		<a href="#">
			<img align="right" id="tr_add" src="../admin/images/btn_badd2.gif" alt="추가하기"></a>
		<?if($mode=="mod"){?>		
		<a href="/admin/market_promotion_product_test.php?pidx=<?=$pidx?>" target="_self">
			<img align="right" src="/admin/images/btn_promo_product.gif" alt="상품등록"/></a>&nbsp;
		<a href="/front/family.php?pidx=<?=$pidx?>" target="_blank">
			<img align="right" src="/admin/images/btn_preview.gif" alt="미리보기"/></a>
		<?}?>	
	</div>



<form name="eventform" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="chkfrm();">
	<input type="hidden" name="ptitle">
	<input type="hidden" name="pinfo">
	<input type="hidden" name="pseq">
	<input type="hidden" name="ptem">
	<input type="hidden" name="pppidx">
	<input type="hidden" name="ppromo_seq">
	<input type="hidden" name="itemCount">
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="idx" value="<?=$idx?>">
	<input type="hidden" name="pidx" value="<?=$pidx?>">
	<input type="hidden" name="test" value=1>

		<!-- 테이블스타일01 -->
		<div class="table_style01 pt_20" style="position:relative">
			<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<?	$msql = "SELECT * FROM tblfamily_list WHERE idx = '{$pidx}'";
				$mres = pmysql_query($msql);
				$mrow=pmysql_fetch_array($mres);
			?>
			<tr> 
				<th><span>메인 타이틀</span></th>
				<td><input type="text" name="mtitle" id="mtitle" style="width:50%" value="<?=$mrow['title']?>" alt="타이틀" /></td>
			</tr>
			<tr> 
				<th><span>메인 카테고리</span></th>
				<td>
					<select name = 'promo_code'>
						<option value = ''>--카테고리 선택--</option>
						<?
							$selected['promo_code'][$mrow['promo_code']] = 'selected';
							$checked['promo_view']['Y'] = 'checked';
							# 1차 카테고리만 출력
							$first_cate_sql = "
										SELECT 
											* 
										FROM 
											tblproductcode 
										WHERE 
											group_code!='NO' 
											AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') 
											AND code_b = '000' 
											AND code_c = '000' 
											AND code_d = '000' 
										ORDER BY 
											sequence 
										DESC";
							$first_cate_result = pmysql_query($first_cate_sql,get_db_conn());
							while($first_cate_row=pmysql_fetch_object($first_cate_result)) {
						?>
						<option value = '<?=$first_cate_row->code_a?>' <?=$selected['promo_code'][$first_cate_row->code_a]?>><?=$first_cate_row->code_name?></option>
						<?
							}
						?>
					</select>
					<input type = 'checkbox' name = 'promo_view' value = 'Y' <?=$checked['promo_view'][$mrow['promo_view']]?>> 메인 노출
				</td>
			</tr>
			<tr>
				<th><span>메인 이미지 타입 선택</span></th>
				<td>
					<input type="radio" name="image_type" value="F" <?if($mrow['image_type']=="F" || $mrow['image_type']=="") echo "checked";?> />파일 업로드 &nbsp;
					<input type="radio" name="image_type" value="E" <?if($mrow['image_type']=="E") echo "checked";?> />에디터 사용
				</td>
			</tr>
			<tr id="img_F">
				<th><span>배너 이미지 ( 1100 * 580 )</span></th>
				<td>
				<input type="file" name="banner_img[]" alt="본문 이미지" />
				<?
					if($mrow['banner_img']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['banner_img']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr id="img_E" style="display:none;">
				<th><span>배너 이미지 에디터</span></th>
				<td><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name=content><?=stripslashes($mrow['content'])?></textarea></td>
			</tr>
			<?//exdebug($mrow['member']);?>
			<tr>
				<th>회원/비회원 선택</th>
				<td>
					<input type="radio" id="mem0" name="member" 
					<?if($mrow['member'] == '0' || !$mrow['member']){echo "checked";}?> value=0>비회원
					<input type="radio" id="mem1" name="member" <?if($mrow['member']=='1'){echo "checked";}?> value=1>회원
				</td>
			</tr>

			<tr >
				<th><span>회원등급 설정</span></th>
				<td>
					<?$member_type=explode(",",$mrow['member_type']);?>
					<?foreach($mem as $key=>$val){?>
						<input type="checkbox" name="member_type[]" value=<?=$val->group_code?> 
						<?foreach($member_type as $val2){
							if($val2 == $val->group_code) echo "checked";
						}?>><?=$val->group_name?>
					<?}?>
				</td>
			</tr>

			<tr>
				<th><span>전시 상태</span></th>
				<td><select name="display_type" id="display_type">
					<option value="A" <?if($mrow['display_type']=='A') echo "selected";?>>모두</option>
					<option value="P" <?if($mrow['display_type']=='P') echo "selected";?>>PC만</option>
					<option value="M" <?if($mrow['display_type']=='M') echo "selected";?>>모바일만</option>
					<option value="N" <?if($mrow['display_type']=='N') echo "selected";?>>보류</option>
					<option value="S" <?if($mrow['display_type']=='S') echo "selected";?>>PC 비전시</option>
					<option value="D" <?if($mrow['display_type']=='D') echo "selected";?>>모바일 비전시</option>
					<option value="B" <?if($mrow['display_type']=='B') echo "selected";?>>fitflop 모바일만</option>
					<option value="C" <?if($mrow['display_type']=='C') echo "selected";?>>fitflop 모바일 비전시</option>
					</select>
				</td>
			</tr>
			<tr id="fmobile" <?if($mrow['display_type']!='B') echo " style='display: none'";?>>
				<th><span>핏플랍 모바일 타이틀 배너</span></th>
				<td>
					<input type="file" name="title_banner[]" alt="본문 이미지" />
				<?
					if($mrow['title_banner']){
				?>
					<br><img src="<?=$imagepath?><?=$mrow['title_banner']?>" style="height:30px;" class="img_view_sizeset">
				<?
					}
				?>
				</td>
			</tr>
			<tr>
				<th><span>영역 우선순위</span></th>
				<td>
					<select name="mdisplay_seq" id="mdisplay_seq">
					<?if($count==0){$count=1;} for($i=1; $i<=$mcount; $i++){?>
						<option value="<?=$i?>" <?if($mrow['display_seq']== $i) echo "selected";?>><?=$i?></option>
					<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<th><span>쿠폰, 적립금 사용금지</span></th>
				<td>
					<input type="checkbox" name="no_coupon" value="Y" <?if($mrow['no_coupon'] == 'Y') echo checked;?> />
				</td>
			</tr>
			<TR>
				<th><span>노출 기간</span></th>
				<TD class="td_con1"><INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=start_date value="<?=$mrow['start_date']?>" class="input_bd_st01">부터  <INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=end_date value="<?=$mrow['end_date']?>" class="input_bd_st01">까지</span></TD>
			</TR>			
			</table>
			&nbsp;
			
			<!--기획전들-->
			<?if($mode=="ins"){?>
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
				<tr>
					<th><span>패밀리세일 타이틀</span></th>
					<td><input type="text" name="title" id="title" style="width:20%" value="" alt="타이틀" /></td>
				</tr>						
				<tr>
					<th><span>타이틀 설명</span></th>
					<td><textarea name="info" style="width:500;height:100;"></textarea> </td> 
				</tr>
				<tr>
					<th><span>영역 우선순위</span></th>
					<td>
						<select name="display_seq"class="display_seq">
						<?if($count==0){$count=1;}else{ for($i=1; $i<=$count; $i++){?>
							<option value="<?=$i?>"><?=$i?></option>
						<?}}?>
						</select>
					</td>
				</tr>
				<tr>
					<th><span>상품 리스팅 템플릿</span></th>
					<td><select name="display_tem">
						<option value="1" >강조형 상품 템플릿</option>
						<option value="2" >일반형 상품 템플릿</option></select>
					</td>
				</tr>				
				<input type="hidden" name="ppidx" value="1"/>
			</table> 
			<?}else if($mode=="mod"){ 
			$qry="select * from tblfamily where promo_idx='".$pidx."' ORDER by idx ASC "; 
			$res=pmysql_query($qry);
			$cnt=0;
			while($row=pmysql_fetch_array($res)){ $cnt++;?>
				<img align="left" class="tr_remove" src="../admin/images/del_arrow.gif" align="right" alt="삭제하기" onclick="javascript:del_prmo(<?=$row['idx']?>)">
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item<?=$cnt?>">			
					<tr>
						<th><span>패밀리세일 타이틀</span></th>
						<td>
							<input type="text" name="title" id="title" style="width:20%" value="<?=$row['title']?>" alt="타이틀" />
						</td>
					</tr>						
					<tr>
						<th><span>타이틀 설명</span></th>
						<td><textarea name="info" style="width:500;height:100;"><?=$row['info']?></textarea> </td>
					</tr>
					<tr>
						<th><span>영역 우선순위</span></th>
						<td>
							<select name="display_seq" class="display_seq">
							<?if($count==0){$count=1;} for($i=1; $i<=$count; $i++){?>
								<option value="<?=$i?>" <?if($row['display_seq']== $i) echo "selected";?>><?=$i?></option>
							<?}?>
							</select>
						</td>
					</tr> 
					<tr>
						<th><span>상품 리스팅 템플릿</span></th>
						<td><select name="display_tem">
							<option value="1" <?if($row['display_tem']=='1') echo "selected";?>>강조형 상품 템플릿</option>
							<option value="2" <?if($row['display_tem']=='2') echo "selected";?>>일반형 상품 템플릿</option></select>
						</td>
					</tr>
					
					<input type="hidden" name="ppidx" value="<?=$row['idx']?>"> 
					<input type="hidden" name="promo_seq" value="<?=$row['seq']?>"/>
				</table> 
				<!--<table>
				<tr>
					<td colspan="2" align="center">
					<img align="left" class="tr_remove" src="../admin/images/botteon_del.gif" align="right" alt="삭제하기" onclick="javascript:del_prmo(this)">
					</td>
				</tr> 
				</table>-->
			<?  }
			} 
			if($cnt == 0  and $mode != "ins" ){ ?> 
				<table name="promotable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">			
					<tr>  
						<th><span>패밀리세일 타이틀</span></th>
						<td><input type="text" name="title" id="title" style="width:20%" value="" alt="타이틀" /></td>
					</tr>						
					<tr>
						<th><span>타이틀 설명</span></th>
						<td><textarea name="info" style="width:500;height:100;"></textarea> </td> 
					</tr>
					<tr>
						<th><span>영역 우선순위</span></th>
						<td>
							<select name="display_seq"class="display_seq">
							<?if($count==0){$count=1;}else{ for($i=1; $i<=$count; $i++){?>
								<option value="<?=$i?>"><?=$i?></option>
							<?}}?>
							</select>
						</td>
					</tr>
					<tr>
						<th><span>상품 리스팅 템플릿</span></th>
						<td><select name="display_tem">
							<option value="1" >강조형 상품 템플릿</option>
							<option value="2" >일반형 상품 템플릿</option></select>
						</td>
					</tr>
					<input type="hidden" name="ppidx" value="1"/>
				</table> 
				<?}?>
			<div id="add_div"></div>
		</div>
		<div style="width:100%;text-align:center">
			<input type="image" src="../admin/images/btn_confirm_com.gif">
			<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='market_promotion_test.php'">
		</div>


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
<form name="delform" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<input type="hidden" name="ppidx" />
<input type="hidden" name="mode" value="mod" />
<input type="hidden" name="mode2" value="!!!" />
<input type="hidden" name="pidx" value="<?=$pidx?>" />
</form>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript">
$(document).ready(function(){
	$("#tr_add").click(function(){
		var lastItemNo = $(".table_style01 [name=promotable]:last").attr("class").replace("item", "");
		document.eventform.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			var newItem = $(".table_style01 [name=promotable]:last").clone();
			newItem.removeClass();
			var xxx = $(".table_style01 [name=promotable]:last [name=ppidx]").val();
			newItem.addClass("item"+(parseInt(lastItemNo)+1));
			newItem.appendTo('.table_style01'); 			
			$(".table_style01 [name=promotable]:last [name=ppidx]").attr('value', parseInt(xxx)+1);	
			$(".table_style01 [name=promotable]:last [name=promo_seq]").val('');		
			
			var optemp = "<option value='"+(parseInt(lastItemNo)+1)+"'>"+(parseInt(lastItemNo)+1)+"</option>";
			$(".table_style01").find(".display_seq").append(optemp);
			
			$(".table_style01 [name=promotable]:last [name=title]").val(""); 
			$(".table_style01 [name=promotable]:last [name=info]").val(""); 
			$(".table_style01 [name=promotable]:last [name=display_seq]:last option:last").attr("selected", "selected"); 
		}else{ 
			alert("20개까지 등록할 수 있습니다.");
			return;   
		}
	}); 
	 
	$(".img_view_sizeset").on('mouseover',function(){
		$("#img_view_div").find('img').attr('src',($(this).attr('src')));
		$("#img_view_div").find('img').css('display','block');
	});

	$(".img_view_sizeset").on('mouseout',function(){
		$("#img_view_div").find('img').css('display','none'); 
	});	
	
	$('input[name=image_type]:checked').trigger('click');
	
	
	//핏플랍 모바일 타이틀 배너 display
	$("#display_type").change(function() {
		if($("#display_type option:selected").val()=="B"){
			$("#fmobile").show();
		}else{
			$("#fmobile").hide();
		}
	});
});

function del_prmo(t){		
	if(confirm("삭제하시겠습니까?")){
		document.delform.ppidx.value=t;
		document.delform.submit();
	}
}

$('input[name=image_type]').click(function(){
	var type = $(this).val();
	if(type == "E"){
		$('#img_E').show();
		$('#img_F').hide();
	}else if(type == "F"){
		$('#img_E').hide();
		$('#img_F').show();		
	}
})



var oEditors = [];
nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
/*
function htmlsetmode(mode,i){
	if(mode==document.eventform.htmlmode.value) {
		return;
	} else {
		i.checked=true;
		editor_setmode('content',mode);
	}
	document.eventform.htmlmode.value=mode;
}
_editor_url = "htmlarea/";
editor_generate('content');
*/
</script>
<?=$onload?>
<?php 
include("copyright.php");
