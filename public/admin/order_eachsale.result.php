<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$regdate = $_shopdata->regdate;

//print_r($_POST);

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$date_year=$_POST["date_year"];
$date_month=$_POST["date_month"];
$age1=$_POST["age1"];
$age2=$_POST["age2"];
$loc=$_POST["loc"];
$sex=$_POST["sex"];
$member=$_POST["member"];
$paymethod=$_POST["paymethod"];

if(ord($date_year)==0) $date_year=date("Y");
if(ord($date_month)==0) $date_month=date("m");

include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>

<script type="text/javascript">

	function init(){
		var doc= document.getElementById("divName");
		if(doc.offsetHeight!=0){
			pageheight = doc.offsetHeight;
			//alert(pageheight);
			parent.document.getElementById("AddFrame").height=pageheight+"px";
		}
	}

	//Explorer
	if(window.attachEvent){
		window.attachEvent('onload', function() { init(); } );

	} 
	//FF
	else if(window.addEventListener){
		window.addEventListener('load', function() { init(); }, false);
	}

	/*window.onload=function(){
		init();
	}*/
</script>


<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(parent.selcode.length!=12 || parent.selcode=="000000000000") {
		alert("상품카테고리를 선택하세요.");
		return;
	}
	if(!IsNumeric(document.form1.age1.value) || !IsNumeric(document.form1.age2.value)) {
		alert("연령 입력은 숫자만 입력하셔야 합니다.");
		return;
	}
	age1=0;
	age2=0;
	if(document.form1.age1.value.length>0 && document.form1.age2.value.length>0) {
		age1=document.form1.age1.value;
		age2=document.form1.age2.value;
		if(age1==0 || age2==0 || age1>age2) {
			age1=0;
			age2=0;
		}
	}
	if((age1>0 || document.form1.sex.value!="ALL") && document.form1.member.value!="Y") {
		document.form1.member.options[1].selected=true;
	}
	document.form1.code.value=parent.selcode;
	document.form1.prcode.value=parent.prcode;
	document.form1.age1.value=age1;
	document.form1.age2.value=age2;
	document.form1.submit();
}
//-->
</SCRIPT>
<link rel="stylesheet" href="/css/admin.css" type="text/css" />
<div id="divName"">
<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=mode value="search">
	<input type=hidden name=code>
	<input type=hidden name=prcode>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
				<tr>
					<td width="100%">
									<!-- 소제목 -->
									<div class="title_depth3_sub"><span>연령에 0을 입력하시면 전체 연령이 조회됩니다.</span></div>
									<div class="table_style01">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
										<TR>
											<th><span>기간 선택</span></th>
											<TD class="td_con1" width="35%"><select name=date_year class="select" style="width:70px;">
											<?php
														for($i=substr($regdate,0,4);$i<=date("Y");$i++) {
															echo "<option value=\"{$i}\" ";
															if($i==$date_year) echo "selected";
															echo ">{$i}</option>\n";
														}
											?>
											</select>년 <select name=date_month class="select" style="width:70px;">
												<option value="ALL" <?php if($date_month=="ALL")echo"selected";?>>전체</option>
											<?php
														for($i=1;$i<=12;$i++) {
															$ii=sprintf("%02d",$i);
															echo "<option value=\"{$ii}\" ";
															if($i==$date_month) echo "selected";
															echo ">{$ii}</option>\n";
														}
											?>
											</select>월</TD>
											<th><span>연령별</span></th>
											<TD class="td_con1" width="35%"><input type=text name=age1 value="<?=(int)$age1?>" maxlength=3 style="width:35px;padding-left:5px" onkeyup="strnumkeyup(this);" class="input">살부터 <input type=text name=age2 value="<?=(int)$age2?>" maxlength=3 style="width:35px;padding-left:5px" onkeyup="strnumkeyup(this);" class="input"> 까지</TD>
										</TR>
										<TR>
											<th><span>지역별</span></th>
											<TD class="td_con1"><select name=loc class="select" style="width:70px;">
												<option value="ALL" <?php if($loc=="ALL")echo"selected";?>>전체</option>
											<?php
														$loclist=array("서울","부산","대구","인천","광주","대전","울산","강원","경기","경남","경북","충남","충북","전남","전북","제주","기타");
														for($i=0;$i<count($loclist);$i++) {
															echo "<option value=\"{$loclist[$i]}\" ";
															if($loc==$loclist[$i]) echo "selected";
															echo ">{$loclist[$i]}</option>\n";
														}
											?>
											</select></TD>
											<th><span>성별</span></th>
											<TD class="td_con1"><select name=sex class="select" style="width:70px;">
												<option value="ALL" <?php if($sex=="ALL")echo"selected";?>>전체</option>
												<option value="M" <?php if($sex=="M")echo"selected";?>>남자</option>
												<option value="F" <?php if($sex=="F")echo"selected";?>>여자</option>
											</select></TD>
										</TR>
										<TR>
											<th><span>회원구분</span></th>
											<TD class="td_con1"><select name=member class="select" style="width:70px;">
												<option value="ALL" <?php if($member=="ALL")echo"selected";?>>전체</option>
												<option value="Y" <?php if($member=="Y")echo"selected";?>>회원</option>
												<option value="N" <?php if($member=="N")echo"selected";?>>비회원</option>
											</select></TD>
											<th><span>결제방법</span></th>
											<TD class="td_con1"><select name=paymethod style="WIDTH: 95%" class="select">
												<option value="ALL" <?php if($paymethod=="ALL")echo"selected";?>>전체</option>
												<option value="B" <?php if($paymethod=="B")echo"selected";?>>무통장</option>
												<option value="V" <?php if($paymethod=="V")echo"selected";?>>실시간계좌이체</option>
												<option value="O" <?php if($paymethod=="O")echo"selected";?>>가상계좌</option>
												<option value="C" <?php if($paymethod=="C")echo"selected";?>>신용카드</option>
												<!--option value="P" <?php if($paymethod=="P")echo"selected";?>>매매보호 신용카드</option-->
												<option value="M" <?php if($paymethod=="M")echo"selected";?>>휴대폰</option>
												<option value="Q" <?php if($paymethod=="Q")echo"selected";?>>매매보호 가상계좌</option>
											</select></TD>
										</TR>
									</TABLE>
									</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" height=10></td>
	</tr>
	<tr>
		<td align="center"><p><a href="javascript:CheckForm();"><img src="images/botteon_search.gif" border="0"></a></p></td>
	</tr>
	</form>
	<tr>
		<td align="center">
<?php
		if($mode=="search") {
			list($code_a,$code_b,$code_c,$code_d) = sscanf($code,"%3s%3s%3s%3s");
			$likecode=$code_a;
			if($code_b!="000") {
				$likecode.=$code_b;
				if($code_c!="000") {
					$likecode.=$code_c;
					if($code_d!="000") {
						$likecode.=$code_d;
					}
				}
			}

			if($date_month=="ALL") {
				include "order_eachsale.year.php";
			} else {
				include "order_eachsale.month.php";
			}
		}
?>
		</td>
	</tr>
</table>
</div>
</body>
</html>
