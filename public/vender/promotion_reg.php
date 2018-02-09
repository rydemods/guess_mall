<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");


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
$errmsg = $filedata->chkExt();
if($errmsg==''){
	$up_file = $filedata->upFiles();
}
$content = $_REQUEST["content"];
if(ord($_REQUEST["mode2"])>0){
	$ppidx_ = $_REQUEST["ppidx"];
	$pidx_ = $_REQUEST["pidx"];
	$sql = "DELETE FROM tblpromotion WHERE idx = '{$ppidx_}' AND promo_idx = '{$pidx_}' ";
	pmysql_query($sql);
	echo "<script>alert('삭제되었습니다.')</script>";
}

$cqry="select count(*) from tblpromotion WHERE promo_idx='{$pidx}'"; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$count=$crow['count'];

$cqry="select count(*) from tblpromo "; 
$cres=pmysql_query($cqry);
$crow=pmysql_fetch_array($cres);
pmysql_free_result($cres);
$mcount=$crow['count'];

switch($mode){
	case "del" : 	$seq=$_REQUEST['seq']; /*삭제할때 삭제할 로우보다 진열 순위가 낮은 로우를 한개씩 위로 올림*/
				$dcsql = "SELECT count(*) FROM tblpromo WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
				$dcres = pmysql_query($dcsql,get_db_conn());
				$dcrow=pmysql_fetch_array($dcres);
				if($dcrow[0]!=0){
					$dusql = "UPDATE tblpromo SET display_seq = display_seq-1 
						WHERE idx = ( select * from (select idx where display_seq > {$seq}) as a)";
					pmysql_query($dusql,get_db_conn());
				}
				/*메인 타이틀 삭제*/
				$dsql = "DELETE FROM tblpromo WHERE idx='{$pidx}'";
				pmysql_query($dsql);	
				
				/*상품 삭제*/
				$ddsql = "SELECT idx FROM tblpromotion WHERE promo_idx='{$pidx}'";
				$ddres = pmysql_query($ddsql);
				$ddrow= pmysql_fetch_object($ddres);
				for($i=0;$i<count($ddrow);$i++){	
					$dsql2 = "DELETE FROM tblspecialpromo WHERE special='".$ddrow->idx."'";
					pmysql_query($dsql2);
				}		
				/*서브 타이틀 삭제*/	 
				$dsql3 = "DELETE FROM tblpromotion WHERE promo_idx='{$pidx}' "; 
				pmysql_query($dsql3);		
				
				echo "<script>alert('삭제되었습니다.');</script>";
				echo "<script>document.location.href='promotion.php';</script>";
				break; 
				
	case "ins" : $count=$count+1; $mcount= $mcount+1; break;	 
				
	case "ins_submit" : $ptitle = $_POST["ptitle"]; $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = $_POST["mtitle"]; $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$mcount++;
						
						$mnsql = "select idx from tblpromo order by idx desc";
						$mnres = pmysql_query($mnsql);
						$tempx = 1;
						while($mnrow = pmysql_fetch_object($mnres)){
							if($tempx <= $mnrow->idx){
								$tempx = $mnrow->idx+1;								
							}							
						}
						
						$misql = "insert into tblpromo (idx, title, banner_img, display_type, display_seq, rdate, start_date, end_date, no_coupon,image_type, content,title_banner, vender) ";
						$misql.= "values('".$tempx."', '{$mt}', '{$up_file['banner_img'][0]['v_file']}', '{$mdt}', '{$mds}', current_date, '{$start_date}', '{$end_date}', '{$no_coupon}','{$image_type}', '{$content}','{$up_file['title_banner'][0]['v_file']}', '".$_VenderInfo->getVidx()."') ";
						pmysql_query($misql);
						
						for($aa=0;count($pt)>$aa;$aa++){						
							$csql = "SELECT count(*) FROM tblpromotion where  promo_idx='{$tempx}'  ";
							$cres = pmysql_query($csql,get_db_conn());
							$crow=pmysql_fetch_array($cres);
							if($crow[0]!=$ps[$aa]+1){ /*새로 등록할때 지정한 진열순위가 맨 뒤가 아니라면 지정한 순위부터 뒤에 로우를 한칸씩 뒤로 민다.*/
								$usql = "UPDATE tblpromotion SET display_seq = display_seq+1 
										WHERE idx = ( select * from (select idx where  promo_idx='{$tempx}' AND display_seq >= {$ps[$aa]}) as a)";
								pmysql_query($usql,get_db_conn());					
							}
							 
							$isql = "INSERT INTO tblpromotion (	idx,
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
						//echo "<script>document.location.href='promotion.php';</script>";
						break;
					
	case "mod_submit" :  $ptitle = $_POST["ptitle"]; $pinfo = $_POST["pinfo"]; $pseq = $_POST["pseq"]; $ptem = $_POST["ptem"]; $pppidx = $_POST["pppidx"];
						$pt = explode(",", $ptitle); $pi = explode(",", $pinfo); $ps = explode(",", $pseq); $pte = explode(",", $ptem); $pidxs = explode(",", $pppidx);
						$mt = $_POST["mtitle"]; $mdt = $_POST["display_type"]; $mds = $_POST["mdisplay_seq"];
						
						$arrPromoSeq = explode(",", $_POST["ppromo_seq"]);
						$promo_code = $_POST["promo_code"];
						$promo_view = $_POST["promo_view"];


						$musql = "SELECT display_seq FROM tblpromo WHERE idx='{$pidx}' ";
						$mures = pmysql_query($musql);	
						$murow = pmysql_fetch_array($mures);
						
						if($murow[0]!=$mds){ /*수정할때 지정한 진열 순위에 따라 나머지 로우들도 진열 순위를 수정함*/
							if($murow[0]<$mds){
								$usql = "UPDATE tblpromo SET display_seq = display_seq-1 
										WHERE idx = ( select * from (select idx where display_seq between {$murow[0]} and {$mds}) as a)";
								pmysql_query($usql,get_db_conn());
							} 
							if($murow[0]>$mds){
								$usql = "UPDATE tblpromo SET display_seq = display_seq+1 
										WHERE idx = ( select * from (select idx where display_seq between {$mds} and {$murow[0]}) as a)";
								pmysql_query($usql,get_db_conn());	
							}
						}
						 /*메인테이블 업데이트*/
						$musql = "update tblpromo set title = '{$mt}', display_type = '{$mdt}', display_seq =  '{$mds}', promo_code =  '{$promo_code}', promo_view =  '{$promo_view}', 
								start_date = '{$start_date}', end_date = '{$end_date}', no_coupon = '{$no_coupon}', image_type = '{$image_type}', content = '{$content}' ";						
						if($up_file['banner_img'][0]['v_file']){
							$musql.=", banner_img = '{$up_file['banner_img'][0]['v_file']}' ";

							list($temp_banner_img)=pmysql_fetch("select banner_img from tblpromo where idx='{$pidx}'");
							if($temp_banner_img) @unlink($imagepath.$temp_banner_img);
						}
						// 핏플랍 모바일 타이틀 베너
						if($up_file['title_banner'][0]['v_file']){
							$musql.=", title_banner = '{$up_file['title_banner'][0]['v_file']}' ";

							list($temp_tbanner_img)=pmysql_fetch("select title_banner from tblpromo where idx='{$pidx}'");
							if($temp_tbanner_img) @unlink($imagepath.$temp_tbanner_img);
						}
						
						$musql .= " where idx='{$pidx}' ";						
						pmysql_query($musql);





						$promotion_sql = "SELECT seq FROM tblpromotion WHERE promo_idx='{$pidx}'";
						$promotion_result = pmysql_query($promotion_sql,get_db_conn());
						$arrTempSeq = array();
						while($promotion_row=pmysql_fetch_object($promotion_result)) {
							$arrTempSeq[] = $promotion_row->seq;
						}
						$arrDeletePromotion = array_diff($arrTempSeq, $arrPromoSeq);
						foreach($arrDeletePromotion as $kk => $vv){
							$mdsql = "DELETE FROM tblpromotion WHERE seq='{$vv}'";
							pmysql_query($mdsql);
							$mdsql = "DELETE FROM tblspecialpromo WHERE special='{$vv}'";
							pmysql_query($mdsql);
						}

						for($aa=0;count($pt)>$aa;$aa++){
							if($arrPromoSeq[$aa] != 'undefined' && $arrPromoSeq[$aa]){	//$arrPromoSeq[$aa] 조건 추가 by PTY - 2014.10.14
								$isql = "UPDATE tblpromotion SET idx = {$pidxs[$aa]}, title = '{$pt[$aa]}', info = '{$pi[$aa]}', display_seq = {$ps[$aa]}, display_tem = {$pte[$aa]}, rdate = current_date, promo_idx = '{$pidx}' WHERE seq = '".$arrPromoSeq[$aa]."'"; 
							}else{
								
								$isql = "INSERT INTO tblpromotion 
											(idx, title, info, display_seq, display_tem, rdate, promo_idx) ";
								$isql.= "values 
											({$pidxs[$aa]}, '{$pt[$aa]}', '{$pi[$aa]}', {$ps[$aa]}, {$pte[$aa]}, current_date, '{$pidx}')"; 
							}
							pmysql_query($isql);
						}
						
						echo "<script>alert('수정되었습니다.');</script>";
						echo "<script>document.location.href='promotion.php';</script>";
						break;
}

include("header.php"); 
?>
<script src="../js/jquery-1.11.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
 
<script language="JavaScript">
function tr_remove(){
	var itemCount = $(".ts01 [name=promotable]:last").attr("class").replace("item", "");
	document.eventform.itemCount.value = itemCount;
	$(".ts01 [name=promotable]:last").remove();
	
}

function formSubmit() {
	var itemCount = $(".ts01 [name=promotable]:last").attr("class").replace("item", "");
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

	if(confirm("저장하시겠습니까?")) {
		document.eventform.submit();
	}

}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>

<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>기획전 <?if($mode=="ins"){echo "등록";}else{echo "수정";} ?><B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>기획전 <?if($mode=="ins"){echo "등록";}else{echo "수정";} ?></B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 기획전을 등록/수정 할수 있습니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15px">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<form name="eventform" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="formSubmit();">
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
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>기획전 기본정보</B></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=140></col>
					<col width=></col>

<?php
					$msql = "SELECT * FROM tblpromo WHERE idx = '{$pidx}'";
					$mres = pmysql_query($msql);
					$mrow=pmysql_fetch_array($mres);
?>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><font color=FF4800>*</font> 메인 타이틀</td>
						<td style="padding:7px 7px"><input name=mtitle id="mtitle" value="<?=$mrow['title']?>" maxlength=250 style="width:388" alt="타이틀"></td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 메인 카테고리</td>
						<td style="padding:7px 7px">
						<select name = 'promo_code'>
							<option value = ''>--카테고리 선택--</option>
<?php
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
							<option value = '<?=$first_cate_row->code_a?>' <?=$selected['promo_code'][$first_cate_row->code_a]?> >
								<?=$first_cate_row->code_name?>
							</option>
<?php
	}
?>
						</select>
						<!--
						<input type = 'checkbox' name = 'promo_view' value = 'Y' <?=$checked['promo_view'][$mrow['promo_view']]?>> 메인 노출
						-->
						</td>
						</tr>
						<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 메인 이미지 타입 선택</td>
						<td style="padding:7px 7px" >
							<input type="radio" name="image_type" value="F" <?if($mrow['image_type']=="F" || $mrow['image_type']=="") echo "checked";?> />파일 업로드 &nbsp;
							<input type="radio" name="image_type" value="E" <?if($mrow['image_type']=="E") echo "checked";?> />에디터 사용
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr id="img_F">
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 메인 이미지</td>
						<td style="padding:7px 7px" >
						<input type="file" name="banner_img[]" alt="본문 이미지" class="button"/> <font style="color:#2A97A7;font-size:8pt">(권장이미지 : 1100X580)</font>
<?php
	if($mrow['banner_img']){
?>
						<br><img src="<?=$imagepath?><?=$mrow['banner_img']?>" style="height:30px;" class="img_view_sizeset">
<?
	}
?>
						</td>
					</tr>
					<tr id="img_E" style="display:none;">
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 메인 이미지 에디터</td>
						<td style="padding:7px 7px">
							<textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 220px; min-width:220px;" name=content><?=stripslashes($mrow['content'])?></textarea>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 전시 상태</td>
<?php
	if( $mrow['disabled'] == '0' ){
?>
						<td style="padding:7px 7px" >
							<select name="display_type" id="display_type">
								<option value="A" <?if($mrow['display_type']=='A') echo "selected";?>>모두</option>
								<option value="P" <?if($mrow['display_type']=='P') echo "selected";?>>PC만</option>
								<option value="M" <?if($mrow['display_type']=='M') echo "selected";?>>모바일만</option>
								<option value="N" <?if($mrow['display_type']=='N') echo "selected";?>>보류</option>
								<!-- <option value="S" <?if($mrow['display_type']=='S') echo "selected";?>>PC 비전시</option>
								<option value="D" <?if($mrow['display_type']=='D') echo "selected";?>>모바일 비전시</option>
								<option value="B" <?if($mrow['display_type']=='B') echo "selected";?>>fitflop 모바일만</option>
								<option value="C" <?if($mrow['display_type']=='C') echo "selected";?>>fitflop 모바일 비전시</option> -->
							</select>
						</td>
<?php
	} else {
?>
						<td  style="padding:7px 7px">
							<input type='hidden' name='display_type' value='N' /> 보류
						</td>
<?php
	}
?>
					</tr>
					<tr <?if($mrow['display_type']!='B') echo " style='display: none'";?> ><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr id="fmobile" <?if($mrow['display_type']!='B') echo " style='display: none'";?>>
						<th><span> </span></th>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 모바일 타이틀 배너</td>
						<td style="padding:7px 7px" >
							<input type="file" name="title_banner[]" alt="본문 이미지" />
<?php
	if($mrow['title_banner']){
?>
						<br><img src="<?=$imagepath?><?=$mrow['title_banner']?>" style="height:30px;" class="img_view_sizeset">
<?php
	}
?>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 영역 우선순위</td>
						<td style="padding:7px 7px" >
							<select name="mdisplay_seq" id="mdisplay_seq">
<?php
	if ( $count==0 ) { 
		$count=1; 
	} 
	for($i=1; $i<=$mcount; $i++) {
?>
								<option value="<?=$i?>" <?if($mrow['display_seq']== $i) echo "selected";?>><?=$i?></option>
<?php
	}
?>
							</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style="display:none;">
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 쿠폰, 적립금 사용금지</td>
						<td style="padding:7px 7px" >
							<input type="checkbox" name="no_coupon" value="Y" <?if($mrow['no_coupon'] == 'Y') echo checked;?> />
						</td>
					</tr>
					<tr style="display:none;"><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<TR>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 노출 기간</td>
						<TD style="padding:7px 7px"><INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=start_date value="<?=$mrow['start_date']?>" class="input_bd_st01">부터  <INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=end_date value="<?=$mrow['end_date']?>" class="input_bd_st01">까지</span></TD>
					</TR>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					</table>
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> <B>기획전 상품영역 정보</B></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td>
					<div class="ts01" style="position:relative">
			<!--기획전들-->
<?php
	if($mode=="ins"){
?>
					<table name="promotable" border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" class="item1">	
					<col width=140></col>
					<col width=></col>		
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 기획전 타이틀</td>
						<td style="padding:7px 7px"><input type="text" name="title" id="title" maxlength=250 style="width:388" value="" alt="타이틀" /></td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;'>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 타이틀 설명</td>
						<td style="padding:7px 7px"><textarea name="info" style="width:500;height:100;"></textarea> </td> 
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 영역 우선순위</td>
						<td style="padding:7px 7px">
							<select name="display_seq"class="display_seq">
<?
		if( $count==0 ) {
			$count=1;
		} else { 
			for($i=1; $i<=$count; $i++) {
?>
							<option value="<?=$i?>"><?=$i?></option>
<?
			}
		}
?>
							</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9>상품 리스팅 템플릿</td>
						<td  style="padding:7px 7px">
							<select name="display_tem">
								<option value="1" >강조형 상품 템플릿</option>
								<option value="2" >일반형 상품 템플릿</option>
							</select>
						</td>
					</tr>
					<tr><td height=2 colspan=2 bgcolor=acacac></td></tr>
					<input type="hidden" name="ppidx" value="1"/>
					</table> 
<?php 
	}else if ($mode=="mod") { 
		$qry = "select * from tblpromotion where promo_idx='".$pidx."' ORDER by idx ASC "; 
		$res = pmysql_query( $qry );
		$cnt = 0;
		while( $row=pmysql_fetch_array( $res ) ) { 
			$cnt++;
?>
					<!-- img align="left" class="tr_remove" src="../admin/images/del_arrow.gif" align="right" alt="삭제하기" onclick="javascript:del_prmo(<?=$row['idx']?>)" -->
					<table name="promotable" border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" class="item1<?=$cnt?>">	
					<col width=140></col>
					<col width=></col>		
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 기획전 타이틀</td>
						<td  style="padding:7px 7px">
							<input type="text" name="title" id="title" maxlength=250 style="width:388" value="<?=$row['title']?>" alt="타이틀" />
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 타이틀 설명</td>
						<td style="padding:7px 7px"><textarea name="info" style="width:500;height:100;"><?=$row['info']?></textarea> </td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 영역 우선순위</td>
						<td  style="padding:7px 7px">
							<select name="display_seq" class="display_seq">
<?php
			if($count==0){
				$count=1;
			} 
			for($i=1; $i<=$count; $i++){
?>
							<option value="<?=$i?>" <?if($row['display_seq']== $i) echo "selected";?>><?=$i?></option>
<?php
			}
?>
							</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 상품 리스팅 템플릿</td>
						<td  style="padding:7px 7px">
							<select name="display_tem">
								<option value="1" <?if($row['display_tem']=='1') echo "selected";?>>강조형 상품 템플릿</option>
								<option value="2" <?if($row['display_tem']=='2') echo "selected";?>>일반형 상품 템플릿</option>
							</select>
						</td>
					</tr>
					<tr><td height=2 colspan=2 bgcolor=acacac></td></tr>
					<input type="hidden" name="ppidx" value="<?=$row['idx']?>"> 
					<input type="hidden" name="promo_seq" value="<?=$row['seq']?>"/>
					</table> 
<?php
		}
	} 
	if($cnt == 0  and $mode != "ins" ) { 
?> 
					<table name="promotable" border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" class="item1">	
					<col width=140></col>
					<col width=></col>		
					<tr>  
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 기획전 타이틀</td>
						<td style="padding:7px 7px"><input type="text" name="title" id="title" maxlength=250 style="width:388" value="" alt="타이틀" /></td>
					</tr>		
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 타이틀 설명</td>
						<td style="padding:7px 7px"><textarea name="info" style="width:500;height:100;"></textarea> </td> 
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 영역 우선순위</td>
						<td style="padding:7px 7px">
							<select name="display_seq"class="display_seq">
							<?if($count==0){$count=1;}else{ for($i=1; $i<=$count; $i++){?>
								<option value="<?=$i?>"><?=$i?></option>
							<?}}?>
							</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr style='display:none;' >
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9> 상품 리스팅 템플릿</td>
						<td style="padding:7px 7px">
							<select name="display_tem">
								<option value="1" >강조형 상품 템플릿</option>
								<option value="2" >일반형 상품 템플릿</option>
							</select>
						</td>
					</tr>
					<tr><td height=2 colspan=2 bgcolor=acacac></td></tr>
					<input type="hidden" name="ppidx" value="1"/>
					</table> 
<?php
	}
?>
					<div id="add_div"></div>
					</div>
					</form>
					
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">	
					<col width=></col>
					<tr>  
						<td style="padding:7px 7px 7px 7px">
						<a href="javascript:;"><img align="right" class="tr_remove" src="images/btn_delete.gif" align="right" alt="삭제하기" onclick="javascript:tr_remove()"></a>
						<a href="javascript:;"><img align="right" id="tr_add" src="images/btn_add.gif" alt="추가하기"></a>
					</tr>
					<tr><td height=1 bgcolor=E7E7E7></td></tr>
					</table> 
					</td>
				</tr>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<?if($mode=="mod"){?>
					<A HREF="javascript:formSubmit()"><img src="images/btn_modify06.gif" border=0></A>
					<a href="javascript:;" onClick="window.open('promotion_product.php?pidx=<?=$pidx?>','promotion_product','height=700,width=1000,scrollbars=yes,resizable=no');"><img src="images/btn_registered.gif"  border=0></a>
					<a href="/front/promotion.php?pidx=<?=$pidx?>" target="_blank"><img src="images/btn_preview01.gif" border=0></a>
					<?} else {?>
					<A HREF="javascript:formSubmit()"><img src="images/btn_regist01.gif" border=0></A>
					<?}?>
					</td>
				</tr>

				</table>
				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
		
<form name="delform" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<input type="hidden" name="ppidx" />
<input type="hidden" name="mode" value="mod" />
<input type="hidden" name="mode2" value="!!!" />
<input type="hidden" name="pidx" value="<?=$pidx?>" />
</form>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="javascript">

var oEditors = [];

$(document).ready(function(){
	$("#tr_add").click(function(){
		var lastItemNo = $(".ts01 [name=promotable]:last").attr("class").replace("item", "");
		document.eventform.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			var newItem = $(".ts01 [name=promotable]:last").clone();
			newItem.removeClass();
			var xxx = $(".ts01 [name=promotable]:last [name=ppidx]").val();
			newItem.addClass("item"+(parseInt(lastItemNo)+1));
			newItem.appendTo('.ts01'); 			
			$(".ts01 [name=promotable]:last [name=ppidx]").attr('value', parseInt(xxx)+1);	
			$(".ts01 [name=promotable]:last [name=promo_seq]").val('');		
			
			var optemp = "<option value='"+(parseInt(lastItemNo)+1)+"'>"+(parseInt(lastItemNo)+1)+"</option>";
			$(".ts01").find(".display_seq").append(optemp);
			
			$(".ts01 [name=promotable]:last [name=title]").val(""); 
			$(".ts01 [name=promotable]:last [name=info]").val(""); 
			$(".ts01 [name=promotable]:last [name=display_seq]:last option:last").attr("selected", "selected"); 
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
	//스마트 에디터
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
