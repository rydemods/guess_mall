<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-2";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/auction/";

$mode=$_POST["mode"];
$auction_seq=$_POST["auction_seq"];
$auction_date=$_POST["auction_date"];

$CurrentTime = time();
$start_date1=$_POST["start_date1"];
$start_date2=$_POST["start_date2"];
$start_date3=$_POST["start_date3"];

$end_date1=$_POST["end_date1"];
$end_date2=$_POST["end_date2"];
$end_date3=$_POST["end_date3"];

$start_date1=$start_date1?$start_date1:date("Y-m-d",$CurrentTime);
$start_date2=$start_date2?$start_date2:date("H",$CurrentTime);
$start_date3=$start_date3?$start_date3:date("i",$CurrentTime);

$end_date1=$end_date1?$end_date1:date("Y-m-d",($CurrentTime+(60*60*24)));
$end_date2=$end_date2?$end_date2:date("H",$CurrentTime);
$end_date3=$end_date3?$end_date3:date("i",$CurrentTime);

$start_date=str_replace("-","",$start_date1).$start_date2.$start_date3."00";
$end_date=str_replace("-","",$end_date1).$end_date2.$end_date3."59";

$auction_name=$_POST["auction_name"];
$start_price=(int)$_POST["start_price"];
$sel_mini_unit=(int)$_POST["sel_mini_unit"];
$mini_unit=(int)$_POST["mini_unit"];
$deli_area=$_POST["deli_area"];
$used_period=$_POST["used_period"];
$content=$_POST["content"];
$product_image=$_FILES["product_image"];

if($sel_mini_unit==0) {
	$mini_unit=0;
}

if(ord($auction_seq) && ord($auction_date)) {
	$sql = "SELECT * FROM tblauctioninfo ";
	$sql.= "WHERE auction_seq='{$auction_seq}' AND start_date='{$auction_date}' ";
	$result=pmysql_query($sql,get_db_conn());
	$data=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$data) {
		$onload="<script>window.onload=function(){ alert(\"해당 경매상품 정보가 존재하지 않습니다.\"); }</script>";
		$mode="";
	} else {
		if($mode!="modify") {
			$start_date1=substr($data->start_date,0,4)."-".substr($data->start_date,4,2)."-".substr($data->start_date,6,2);
			$start_date2=substr($data->start_date,8,2);
			$start_date3=substr($data->start_date,10,2);
			$end_date1=substr($data->end_date,0,4)."-".substr($data->end_date,4,2)."-".substr($data->end_date,6,2);
			$end_date2=substr($data->end_date,8,2);
			$end_date3=substr($data->end_date,10,2);
		}
	}
}

if($mode=="insert" || $mode=="modify") {
	if($start_price<100 || substr($start_price,-2)!="00") {
		$onload="<script>window.onload=function(){ alert(\"경매 시작가격 입력이 잘못되었습니다.\"); }</script>";
	}
	if($mini_unit>0 && ord($onload)==0) {
		if($mini_unit<100 || substr($mini_unit,-2)!="00") {
			$onload="<script>window.onload=function(){ alert(\"입찰 최소단위 가격 입력이 잘못되었습니다.\"); }</script>";
		}
	}
	if(ord($onload)==0) {
//	    if(ord($product_name['name']))
//		 	$ext = strtolower(pathinfo($product_name['name'],PATHINFO_EXTENSION));
 		if(ord($product_image['name']))
		 	$ext = strtolower(pathinfo($product_image['name'],PATHINFO_EXTENSION));
		if(ord($product_image['name']) && file_exists($product_image['tmp_name']) && in_array($ext,array('gif','jpg'))) {
			$imagename=date("YmdHis",$CurrentTime).".".$ext;
			$filesize = $product_image['size'];
		} else {
			$imagename=$data->product_image;
		}
		if($filesize<102400) {
			if($end_date>date("YmdHis")) {
				if($mode=="insert") {
					$sql = "SELECT MAX(auction_seq)+1 as seq FROM tblauctioninfo ";
					$sql.= "WHERE start_date LIKE '".substr($start_date,0,8)."%' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->seq>0) {
						$in_auction_seq=$row->seq;
					} else {
						$in_auction_seq=1;
					}
					$sql = "INSERT INTO tblauctioninfo(
					auction_seq	,
					start_date	,
					end_date	,
					auction_name	,
					start_price	,
					last_price	,
					mini_unit	,
					deli_area	,
					used_period	,
					product_image	,
					content) VALUES (					
					'{$in_auction_seq}', 
					'{$start_date}', 
					'{$end_date}', 
					'{$auction_name}', 
					'{$start_price}', 
					'{$start_price}', 
					'{$mini_unit}', 
					'{$deli_area}', 
					'{$used_period}', 
					'{$imagename}', 
					'{$content}')";
					pmysql_query($sql,get_db_conn());
					$onload="<script>window.onload=function(){ alert(\"경매상품 등록이 완료되었습니다.\"); }</script>";
					$start_date1=date("Y-m-d",$CurrentTime);
					$start_date2=date("H",$CurrentTime);
					$start_date3=date("i",$CurrentTime);
					$end_date1=date("Y-m-d",($CurrentTime+(60*60*24)));
					$end_date2=date("H",$CurrentTime);
					$end_date3=date("i",$CurrentTime);
				} else if($mode=="modify") {
					if($start_date==$auction_date) {
						$in_auction_seq=$auction_seq;
					} else {
						$sql = "SELECT MAX(auction_seq)+1 as seq FROM tblauctioninfo ";
						$sql.= "WHERE start_date LIKE '".substr($start_date,0,8)."%' ";
						$result=pmysql_query($sql,get_db_conn());
						$row=pmysql_fetch_object($result);
						pmysql_free_result($result);
						if($row->seq>0) {
							$in_auction_seq=$row->seq;
						} else {
							$in_auction_seq=1;
						}
					}
					$sql = "UPDATE tblauctioninfo SET 
					auction_seq		= '{$in_auction_seq}', 
					start_date		= '{$start_date}', 
					end_date		= '{$end_date}', 
					auction_name	= '{$auction_name}', 
					start_price		= '{$start_price}', 
					mini_unit		= '{$mini_unit}', 
					deli_area		= '{$deli_area}', 
					used_period		= '{$used_period}', 
					product_image	= '{$imagename}', 
					content			= '{$content}' 
					WHERE auction_seq	= '{$auction_seq}' AND start_date='{$auction_date}' ";
					pmysql_query($sql,get_db_conn());
					if(ord($data->product_image) && file_exists($product_image['tmp_name']) && file_exists($imagepath.$data->product_image)) {
						unlink($imagepath.$data->product_image);
					}
					$auction_seq=$in_auction_seq;
					$auction_date=$start_date;
					$data->auction_name=$auction_name;
					$data->start_price=$start_price;
					$data->mini_unit=$mini_unit;
					$data->deli_area=$deli_area;
					$data->used_period=$used_period;
					$data->content=$content;
					$data->product_image = $imagename;

					$onload="<script>window.onload=function(){ alert(\"경매상품 수정이 완료되었습니다.\"); }</script>";
				}
				$ext = strtolower(pathinfo($imagename,PATHINFO_EXTENSION));
				if(file_exists($product_image['tmp_name']) && in_array($ext,array('gif','jpg'))) {
					move_uploaded_file($product_image['tmp_name'],$imagepath.$imagename);
					chmod($imagepath.$imagename,0666);
				}
			} else {
				$onload="<script>window.onload=function(){ alert(\"경매 종료일 설정이 잘못되었습니다.\\n\\n경매 종료일은 현재시각보다 커야합니다.\"); }</script>";
				$data->auction_name=$auction_name;
				$data->start_price=$start_price;
				$data->mini_unit=$mini_unit;
				$data->deli_area=$deli_area;
				$data->used_period=$used_period;
				$data->content=$content;
			}
		} else {
			$onload="<script>window.onload=function(){ alert(\"경매상품 사진 파일 용량은 150KB이하로 등록이 가능합니다.\"); }</script>";
			$data->auction_name=$auction_name;
			$data->start_price=$start_price;
			$data->mini_unit=$mini_unit;
			$data->deli_area=$deli_area;
			$data->used_period=$used_period;
			$data->content=$content;
		}
	} else {
		$data->auction_name=$auction_name;
		$data->start_price=$start_price;
		$data->mini_unit=$mini_unit;
		$data->deli_area=$deli_area;
		$data->used_period=$used_period;
		$data->content=$content;
	}
}

$mode="insert";
if(ord($data->auction_seq)) $mode="modify";
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script language="JavaScript">
function CheckForm(form) {
	if(form.auction_name.value.length==0) {
		alert("경매상품 제목을 입력하세요.");
		form.auction_name.focus();
		return;
	}
	if(form.start_price.value.length==0) {
		alert("경매 시작가격을 입력하세요.");
		form.start_price.focus();
		return;
	}
	if(!IsNumeric(form.start_price.value)) {
		alert("경매 시작가격은 숫자만 입력 가능합니다.");
		form.start_price.focus();
		return;
	}
	if(form.start_price.value<100) {
		alert("경매 시작가격은 100원 이상이어야 합니다.");
		form.start_price.focus();
		return;
	}
	if(form.start_price.value.substring(form.start_price.value.length-2,form.start_price.value.length)!="00") {
		alert("경매 시작가격은 100원 단위로 입력하세요.\n\n예) 100,500,1100,1800");
		form.start_price.focus();
		return;
	}
	if(form.sel_mini_unit[1].checked) {
		if(form.mini_unit.value.length==0) {
			alert("입찰 최소단위 가격을 입력하세요.");
			form.mini_unit.focus();
			return;
		}
		if(!IsNumeric(form.mini_unit.value)) {
			alert("입찰 최소단위 가격은 숫자만 입력 가능합니다.");
			form.mini_unit.focus();
			return;
		}
		if(form.mini_unit.value<100) {
			alert("입찰 최소단위 가격은 100원 이상이어야 합니다.");
			form.mini_unit.focus();
			return;
		}
		if(form.mini_unit.value.substring(form.mini_unit.value.length-2,form.mini_unit.value.length)!="00") {
			alert("입찰 최소단위 가격은 100원 단위로 입력하세요.\n\n예) 100,500,1000,1500");
			form.mini_unit.focus();
			return;
		}
	}
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.content.value=sHTML;
	
	if(form.content.value.length==0) {
		alert("경매 상세내용을 입력하세요.");
		form.content.focus();
		return;
	}
	

	document.form1.submit();
}

function chk_mini_unit(gbn) {
	if (gbn=="0") {
		document.form1.mini_unit.value="";
		document.form1.mini_unit.disabled=true;
	} else if (gbn=="1") {
		document.form1.mini_unit.disabled=false;
		document.form1.mini_unit.focus();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 쇼핑몰 경매 관리 &gt;<span>경매상품 등록/수정</span></p></div></div>
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
			<?php include("menu_gong.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=auction_seq value="<?=$auction_seq?>">
			<input type=hidden name=auction_date value="<?=$auction_date?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">경매물품 등록 및 수정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>경매 상품의 등록 및 수정을 하실 수 있습니다. 수정의 경우 경매상품 관리에서 상품명을 클릭하시면 수정이 가능합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">경매물품 등록 및 수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>경매 상품명</span></th>
					<TD><INPUT class=input style=width:100% maxLength=100 size=70 name=auction_name value="<?=$data->auction_name?>"></TD>
				</TR>
				<TR>
					<th><span>경매 시작일</span></th>
					<TD>
					<INPUT class="input_bd_st01"  style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 value="<?=$start_date1?>" name=start_date1> 
					<SELECT name=start_date2 class="select">
<?php
					for($i=0;$i<=23;$i++) {
						$val = sprintf("%02d",$i);
						if($i<=5) {
							echo "<option value=\"{$val}\"";
							if($val==$start_date2) {
								echo "selected";
							}
							echo " >새벽 {$i}시</option>";
						} else if($i<=11) {
							echo "<option value=\"{$val}\"";
							if($val==$start_date2) {
								echo "selected";
							}
							echo " >오전 {$i}시</option>";
						} else {
							echo "<option value=\"{$val}\"";
							if($val==$start_date2) {
								echo "selected";
							}
							echo " >오후 {$i}시</option>";
						}
					}
?>
					</SELECT>
					 <SELECT name=start_date3 class="select">
<?php
					for($i=0;$i<=59;$i++) {
						$val = sprintf("%02d",$i);
						echo "<option value=\"{$val}\"";
						if($val==$start_date3) {
							echo "selected";
						}
						echo " >{$val}분</option>";
					}
?>
					</SELECT>
					
					</TD>
				</TR>
				<TR>
					<th><span>경매 종료일</span></th>
					<TD>
					<INPUT class="input_bd_st01" style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 value="<?=$end_date1?>" name=end_date1> 
					<SELECT name=end_date2 class="select">
<?php
					for($i=0;$i<=23;$i++) {
						$val = sprintf("%02d",$i);
						if($i<=5) {
							echo "<option value=\"{$val}\"";
							if($val==$end_date2) {
								echo "selected";
							}
							echo " >새벽 {$i}시</option>";
						} else if($i<=11) {
							echo "<option value=\"{$val}\"";
							if($val==$end_date2) {
								echo "selected";
							}
							echo " >오전 {$i}시</option>";
						} else {
							echo "<option value=\"{$val}\"";
							if($val==$end_date2) {
								echo "selected";
							}
							echo " >오후 {$i}시</option>";
						}
					}
?>
					</SELECT>					 
					<SELECT name=end_date3 class="select">
<?php
					for($i=0;$i<=59;$i++) {
						$val = sprintf("%02d",$i);
						echo "<option value=\"{$val}\"";
						if($val==$end_date3) {
							echo "selected";
						}
						echo " >{$val}분</option>";
					}
?>
					</SELECT>
					
					</TD>
				</TR>
				<tr>
					<th><span>경매 시작가격</span></th>
					<TD><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength=10 name=start_price value="<?=$data->start_price?>"> 원  &nbsp;<FONT class=font_orange>ex) 20000 (콤마를 입력하지 마세요)</FONT></TD>
				</tr>
				<tr>
					<th><span>입찰 최소단위 가격</span></th>
					<TD>
					<INPUT class=input id=idx_mini_unit0 onclick="chk_mini_unit('0')" type=radio value=0 name=sel_mini_unit <?php if($data->mini_unit==0) echo " checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_mini_unit0>자동</LABEL> &nbsp;
					<INPUT class=input id=idx_mini_unit1 onclick="chk_mini_unit('1')" type=radio value=1 name=sel_mini_unit <?php if($data->mini_unit>0) echo " checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_mini_unit1>직접입력</LABEL>
					<INPUT class="input" style="TEXT-ALIGN: right" maxLength=5 size=5 name=mini_unit <?php if($data->mini_unit>0)echo"value={$data->mini_unit}";else echo"disabled";?>> 원 &nbsp; <FONT class=font_orange>ex) 1000 (콤마를 입력하지 마세요)</FONT></TD>
				</tr>
				<tr>
					<th><span>배송 가능지역 선택</span></th>
					<TD>
					
					<SELECT name=deli_area size="1" class="select">
<?php
					$arealist=array("전국","서울","수도권","경기도","강원도","경상도","전라도","충청도","제주도");
					for($i=0;$i<count($arealist);$i++) {
						if($data->deli_area==$arealist[$i]) {
							echo "<option value=\"{$arealist[$i]}\" selected>{$arealist[$i]}</option>\n";
						} else {
							echo "<option value=\"{$arealist[$i]}\">{$arealist[$i]}</option>\n";
						}
					}
?>
					</SELECT>
					<FONT class=font_orange>＊배송 가능지역을 선택하세요.</FONT>
					</TD>
				</tr>
				<tr>
					<th><span>사용기간</span></th>
					<TD><INPUT class=input maxLength=30 size=30 name=used_period value="<?=$data->used_period?>">  <FONT class=font_orange>＊경매상품의 사용기간을 입력하세요.</FONT></TD>
				</tr>
				<tr>
					<th><span>경매상품 사진</span></th>
					<TD>
						<input type=file name=product_image size=50><br>
					<!--
						<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" />
						<input type=file name=product_image value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
						</div>                      
						-->
                   	 	<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span>
<?php
					if(ord($data->product_image)) {
						if(file_exists($imagepath.$data->product_image)) {
							echo "<br><img src=\"".$imagepath.$data->product_image."\" border=0 style=\"border:#e1e1e1 solid 1px\">";
						} else {
							echo "<br>등록된 경매상품 사진이 없습니다.";
						}
					}
?>
					</TD>
				</tr>
				<tr>
					<th>
						<span>경매 상세내용<br>&nbsp;&nbsp;&nbsp;(제품설명,배송,반품등)</span>
					</th>
					<TD><TEXTAREA style="WIDTH: 100%; HEIGHT: 150px" name=content wrap=off class="textarea" id="ir1"><?=$data->content?></TEXTAREA></TD>
				</tr>				
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>경매상품 등록/수정</span></dt>
							<dd>
								- 경매 시작가격은 100원 이상부터 가능합니다.<br>
								- 입찰 최소단위 가격 직접입력은 100원 이상부터 가능합니다.<br>
								- 입찰 최소단위를 자동으로 선택시 아래와 같이 설정 됩니다.<br>
								<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;5,000원 <span class="font_blue">미만</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;100원<br>
								<b>&nbsp;&nbsp;</b><b>&nbsp;&nbsp;</b>50,000원 <span class="font_blue">미만</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;500원<br>
								<b>&nbsp;&nbsp;</b>&nbsp;100,000원 <span class="font_blue">미만</span><b>&nbsp;&nbsp;</b>1,000원<br>
								<b>&nbsp;&nbsp;</b>&nbsp;100,000원 <span class="font_orange">이상</span><b>&nbsp;&nbsp;</b>2,000원<br>
								- 배송 가능지역 선택은 경매입찰에 직접적인 제한을 두지 않으며, 상품 설명에 참고사항으로 표기됩니다.<br>
								- 가격 및 수량 입력시 콤마(,)를 제외하고 입력해 주세요.<br>
								- 등록된 경매 상품 중 입찰자가 있는 경매 상품은 수정할 수 없습니다.<br>
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<script type="text/javascript">
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
<?=$onload?>
<?php 
include("copyright.php");
