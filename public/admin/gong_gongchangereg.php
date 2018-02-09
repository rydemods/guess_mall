<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-3";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/gonggu/";

$mode=$_POST["mode"];
$gong_seq=$_POST["gong_seq"];

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

$gong_name=$_POST["gong_name"];
$production=$_POST["production"];
$specialadd=$_POST["specialadd"];
$receipt_end=$_POST["receipt_end"];
$origin_price=$_POST["origin_price"];
$start_price=$_POST["start_price"];
$quantity=$_POST["quantity"];
$down_price=$_POST["down_price"];
$mini_price=$_POST["mini_price"];
$sel_count=$_POST["sel_count"];
$count=$_POST["count"];
$sel_deli_money=$_POST["sel_deli_money"];
$deli_money=$_POST["deli_money"];
$content=$_POST["content"];
$image1=$_FILES["image1"];
$image2=$_FILES["image2"];
$image3=$_FILES["image3"];

if($sel_count==0) $count=1;
if(ord($count)==0 || $count==0) $count=1;

if(ord($sel_deli_money)==0) {
	$deli_money="NULL";
} else if(ord($sel_deli_money) && $sel_deli_money==0) {
	$deli_money="0";
}

if(ord($gong_seq)) {
	$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='{$gong_seq}' ";
	$result=pmysql_query($sql,get_db_conn());
	$data=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$data) {
		$onload="<script>window.onload=function(){ alert(\"해당 공동구매 정보가 존재하지 않습니다.\"); }</script>";
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
		$onload="<script>window.onload=function(){ alert(\"공동구매 시작가격 입력이 잘못되었습니다.\"); }</script>";
	}
	if(!$onload) {
		if($end_date<date("YmdHis")) {
			$onload="<script>window.onload=function(){ alert(\"공동구매 종료일 설정이 잘못되었습니다.\\n\\n공동구매 종료일은 현재시각보다 커야합니다.\"); }</script>";
		}
	}
	if(!$onload) {
		$filesize=$image1['size']+$image2['size']+$image3['size'];
		if($filesize>307200) {
			$onload="<script>window.onload=function(){ alert(\"공동구매 총 이미지 용량은 300KB이하로 등록이 가능합니다\"); }</script>";
		}
	}
	if(!$onload) {
		$files=array(&$image1,&$image2,&$image3);
		$oldfiles=array(&$data->image1,&$data->image2,&$data->image3);
		$in_image=array();
		for($i=0;$i<3;$i++) {
			if($mode=="modify") {
				if(ord($oldfiles[$i]) && file_exists($imagepath.$oldfiles[$i]) && ord($files[$i]['name'])) {
					unlink($imagepath.$oldfiles[$i]);
				}
			}
			if(ord($files[$i]['name']) && file_exists($files[$i]['tmp_name'])) {
				$ext = strtolower(pathinfo($files[$i]['name'],PATHINFO_EXTENSION));
				if(in_array($ext,array('gif','jpg'))) {
					$in_image[$i] = ($i+1)."_".date("YmdHis",$CurrentTime).".".$ext;
					if($i==2) {
						$size=getimageSize($files[$i]['tmp_name']);
						$imgwidth=$size[0];
						$imgheight=$size[1];
						$imgtype=$size[2];
						$maxsize=120;
						$makesize=100;
						if($imgwidth>$maxsize || $imgheight>$maxsize) {
							if($imgtype==1)			$img = ImageCreateFromGif($files[$i]['tmp_name']);
							else if($imgtype==2)	$img = ImageCreateFromJpeg($files[$i]['tmp_name']);
							else if($imgtype==3)	$img = ImageCreateFromPng($files[$i]['tmp_name']);
							if($imgwidth>=$imgheight) {
								$small_width=$makesize; 
								$small_height=($imgheight*$makesize)/$imgwidth;
							} else if($imgwidth<$imgheight) {
								$small_width=($imgwidth*$makesize)/$imgheight; $small_height=$makesize;
							}

							if($imgtype==1) {
								$img2=ImageCreate($small_width,$small_height); // GIF일경우
								ImageCopyResized($img2,$img,0,0,0,0,$small_width,$small_height,$imgwidth,$imgheight);
								imageGIF($img2,$files[$i]['tmp_name']);
							} else if($imgtype==2) {
								$img2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
								imagecopyresampled($img2,$img,0,0,0,0,$small_width,$small_height,$imgwidth,$imgheight);
								imageJPEG($img2,$files[$i]['tmp_name'],90);
							} else {
								$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
								imagecopyresampled($img2,$img,0,0,0,0,$small_width,$small_height,$imgwidth,$imgheight);
								imagePNG($img2,$files[$i]['tmp_name']);
							}
							ImageDestroy($img);
							ImageDestroy($img2);
						}
					}
					move_uploaded_file($files[$i]['tmp_name'],$imagepath.$in_image[$i]);
					chmod($imagepath.$in_image[$i],0666);
				} else {
					$in_image[$i]=$oldfiles[$i];
				}
			} else {
				$in_image[$i]=$oldfiles[$i];
			}
		}
		$qry='';
		$qry2='';
		if($mode=="insert" && ord($gong_name)) {
			$sql = "SELECT COALESCE(MAX(gong_seq),0)+1 as seq FROM tblgonginfo ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->seq>0) {
				$in_gong_seq=$row->seq;
			} else {
				$in_gong_seq=1;
			}
			$qry = "INSERT INTO tblgonginfo(gong_seq) VALUES('{$in_gong_seq}')";
			pmysql_query($qry,get_db_conn());
			$qry = "UPDATE tblgonginfo SET ";
			$qry2 = "WHERE gong_seq='{$in_gong_seq}' ";
			$msg="공동구매 등록이 완료되었습니다.";
		} else if($mode=="modify" && ord($gong_seq)) {
			$qry = "UPDATE tblgonginfo SET ";
			$qry2 = "WHERE gong_seq='{$gong_seq}' ";
			$msg="공동구매 수정이 완료되었습니다.";
			$data->image1=$in_image[0];
			$data->image2=$in_image[1];
			$data->image3=$in_image[2];
		}
		$data->gong_name=$gong_name;
		$data->production=$production;
		$data->specialadd=$specialadd;
		$data->receipt_end=$receipt_end;
		$data->origin_price=$origin_price;
		$data->start_price=$start_price;
		$data->quantity=$quantity;
		$data->down_price=$down_price;
		$data->mini_price=$mini_price;
		$data->count=$count;
		$data->deli_money=($deli_money=="NULL"?"":$deli_money);
		$data->content=$content;
		$sql = $qry." ";
		$sql.= "start_date		= '{$start_date}', ";
		$sql.= "end_date		= '{$end_date}', ";
		$sql.= "gong_name		= '{$gong_name}', ";
		$sql.= "production		= '{$production}', ";
		$sql.= "specialadd		= '{$specialadd}', ";
		$sql.= "receipt_end		= '{$receipt_end}', ";
		$sql.= "origin_price	= '{$origin_price}', ";
		$sql.= "start_price		= '{$start_price}', ";
		$sql.= "quantity		= '{$quantity}', ";
		$sql.= "down_price		= '{$down_price}', ";
		$sql.= "mini_price		= '{$mini_price}', ";
		$sql.= "count			= '{$count}', ";
		$sql.= "deli_money		= {$deli_money}, ";
		$sql.= "image1			= '{$in_image[0]}', ";
		$sql.= "image2			= '{$in_image[1]}', ";
		$sql.= "image3			= '{$in_image[2]}', ";
		$sql.= "content			= '{$content}' ";
		$sql.= $qry2;

		$update=pmysql_query($sql,get_db_conn());
		if($update) {
			$start_date1=date("Y-m-d",$CurrentTime);
			$start_date2=date("H",$CurrentTime);
			$start_date3=date("i",$CurrentTime);
			$end_date1=date("Y-m-d",($CurrentTime+(60*60*24)));
			$end_date2=date("H",$CurrentTime);
			$end_date3=date("i",$CurrentTime);

			$onload="<script>window.onload=function(){ alert(\"{$msg}\"); }</script>";
		} else {
			$onload="<script>window.onload=function(){ alert(\"공동구매 등록중 오류가 발생하였습니다.\"); }</script>";
			for($i=0;$i<3;$i++) {
				if(ord($in_image[$i]) && file_exists($imagepath.$in_image[$i])) {
					unlink($imagepath.$in_image[$i]);
				}
			}
		}
	} else {
		$data->gong_name=$gong_name;
		$data->production=$production;
		$data->specialadd=$specialadd;
		$data->receipt_end=$receipt_end;
		$data->origin_price=$origin_price;
		$data->start_price=$start_price;
		$data->quantity=$quantity;
		$data->down_price=$down_price;
		$data->mini_price=$mini_price;
		$data->count=$count;
		$data->deli_money=$deli_money;
		$data->content=$content;
	}
}

$mode="insert";
if(ord($data->gong_seq)) $mode="modify";

if(ord($data->count)==0) $data->count=1;

/*
if(ord($deli_money)==0 || $deli_money=="NULL") {
	$data->deli_money="";
} else if(ord($deli_money) && $deli_money==0) {
	$data->deli_money=0;
} else {
	$data->deli_money=$deli_money;
}
*/
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script language="JavaScript">
function CheckForm(form) {
	if(form.gong_name.value.length==0) {
		alert("공동구매 상품 제목을 입력하세요.");
		form.gong_name.focus();
		return;
	}
	if(!IsNumeric(form.receipt_end.value)) {
		alert("입금 마감일은 숫자만 입력 가능합니다.");
		form.receipt_end.focus();
		return;
	}
	if(form.start_price.value.length==0) {
		alert("공동구매 시작가격을 입력하세요.");
		form.start_price.focus();
		return;
	}
	if(!IsNumeric(form.start_price.value)) {
		alert("공동구매 시작가격은 숫자만 입력 가능합니다.");
		form.start_price.focus();
		return;
	}
	if(form.start_price.value<100) {
		alert("공동구매 시작가격은 100원 이상이어야 합니다.");
		form.start_price.focus();
		return;
	}
	if(form.start_price.value.substring(form.start_price.value.length-2,form.start_price.value.length)!="00") {
		alert("공동구매 시작가격은 100원 단위로 입력하세요.\n\n예) 9100,10500,1100,11800");
		form.start_price.focus();
		return;
	}
	if(form.quantity.value.length==0) {
		alert("공동구매 한정수량을 입력하세요.");
		form.quantity.focus();
		return;
	}
	if(!IsNumeric(form.quantity.value)) {
		alert("공동구매 한정수량은 숫자만 입력 가능합니다.");
		form.quantity.focus();
		return;
	}
	if(form.down_price.value.length==0) {
		alert("공동구매 참여시 감소가격을 입력하세요.");
		form.down_price.focus();
		return;
	}
	if(!IsNumeric(form.down_price.value)) {
		alert("공동구매 참여시 감소가격은 숫자만 입력 가능합니다.");
		form.down_price.focus();
		return;
	}
	if(form.down_price.value<=0) {
		alert("공동구매 참여시 감소가격은 0원 이상이어야 합니다.");
		form.down_price.focus();
		return;
	}
	if(!IsNumeric(form.mini_price.value)) {
		alert("공동구매 하한가격은 숫자만 입력 가능합니다.");
		form.mini_price.focus();
		return;
	}
	if(form.mini_price.value.length>0 && form.mini_price.value<=0) {
		alert("공동구매 하한가격은 0원 이상이어야 합니다.");
		form.mini_price.focus();
		return;
	}
	//공동구매형식 처리
	if(form.sel_count[0].checked) {
		form.count.value=1;
	} else if(form.count.value==0 || form.count.value.length==0) {
		alert("감소단위을 입력하세요.");
		form.count.focus();
		return;
	} else if(!IsNumeric(form.count.value)) {
		alert("감소단위은 숫자만 입력 가능합니다.");
		form.count.focus();
		return;
	} else if(form.count.value<2) {
		alert("감소단위은 2이상 입력하셔야 합니다.");
		form.count.focus();
		return;
	}

	if(form.sel_deli_money[2].checked) {
		if(form.deli_money.value.length==0) {
			alert("배송료를 입력하세요.");
			form.deli_money.focus();
			return;
		}
		if(!IsNumeric(form.deli_money.value)) {
			alert("배송료는 숫자만 입력 가능합니다.");
			form.deli_money.focus();
			return;
		}
		if(form.deli_money.value<=0) {
			alert("배송료는 0원 이상이어야 합니다.");
			form.deli_money.focus();
			return;
		}
	}
	
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.content.value=sHTML;
	
	
	if(form.content.value.length==0) {
		alert("공동구매 상세내용을 입력하세요.");
		form.content.focus();
		return;
	}
	temp=form.down_price.value*(form.quantity.value/form.count.value);
	temp=form.start_price.value-temp;
	if(temp<0) temp=0;
	if(form.mini_price.value.length==0 || temp>form.mini_price.value) {
		if(!confirm("공동구매 하한가격을 자동으로 계산하시겠습니까?\n\n자동 계산된 하한가격 : "+temp+"원")) {
			form.mini_price.focus();
			return;
		} else {
			document.form1.mini_price.value=temp;
		}
	}
	if(form.receipt_end.value.length==0) {
		if(confirm("입금 마감일 설정을 기본 3일로 설정하시겠습니까?")) {
			form.receipt_end.value=3;
		} else {
			form.receipt_end.focus();
			return;
		}
	}
	

	document.form1.submit();
}

function chk_deli_money(disabled,color) {
	document.form1.deli_money.disabled=disabled;
	document.form1.deli_money.style.background=color;
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 공동구매관리 &gt;<span>가격변동형 공구 등록/수정</span></p></div></div>
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
			<input type=hidden name=gong_seq value="<?=$gong_seq?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">가격변동형 공구 등록/수정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>공동구매 상품을 등록/수정하실 수 있습니다.(등록된 공구구매 상품 수정은 등록공구 관리에서 상품명을 클릭하시면 수정 가능합니다.)</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">공동구매 물품 등록 및 수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>상품명</span></th>
					<TD><INPUT class=input maxLength=100 size=70 name=gong_name value="<?=$data->gong_name?>"></TD>
				</TR>
				<TR>
					<th><span>제조사</span></th>
					<TD><INPUT class=input maxLength=20 size=30 name=production value="<?=$data->production?>"></TD>
				</TR>
				<TR>
					<th><span>특수표시</span></th>
					<TD><INPUT class=input maxLength=100 size=50 name=specialadd value="<?=$data->specialadd?>">  <FONT class=font_orange>ex) 색상 BLUE, 사이즈 XL</FONT></TD>
				</TR>
				<tr>
					<th><span>공동구매 시작일</span></th>
					<TD>
					<INPUT class="input_bd_st01" style="text-align:center;" onfocus=this.blur(); onclick=Calendar(event) size=15 value="<?=$start_date1?>" name=start_date1> 
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
				</tr>
				<tr>
					<th><span>공동구매 종료일</span></th>
					<TD>
					<INPUT class="input_bd_st01" style="text-align:center;" onfocus=this.blur(); onclick=Calendar(event) size=15 value="<?=$end_date1?>" name=end_date1> 
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
				</tr>
                
				<tr>
					<th><span>입금 마감일</span></th>
					<TD>공동구매 마감 후 <INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength="10" size="10" id="receipt_end" name=receipt_end value="<?=($data->receipt_end!=0?$data->receipt_end:"")?>"> 일 까지 입금  <FONT class=font_orange>＊미입력시 기본 3일로 설정</FONT></TD>
				</tr>
                
				<tr>
					<th><span>시중가격</span></th>
					<TD><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength=10 size=10 name=origin_price value="<?=$data->origin_price?>"> 원  <FONT class=font_orange>ex) 60000</FONT></TD>
				</tr>

				<tr>
					<th><span>시작가격</span></th>
					<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength=10 size=10 name=start_price value="<?=$data->start_price?>"> 원  <FONT class=font_orange>ex) 30000</FONT></TD>
				</tr>

				<tr>
					<th><span>한정수량</span></th>
					<TD><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength="10" size="10" name=quantity value="<?=$data->quantity?>"> 개 <FONT class=font_orange>ex) 100</FONT></TD>
				</tr>

				<TR>
					<th><span>공구 참여시 감소가격</span></th>
					<TD><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength="10" size=10 name=down_price value="<?=$data->down_price?>"> 원  <FONT class=font_orange>ex) 1000</FONT></TD>
				</TR>

				<tr>
					<th><span>최저가격</span></th>
					<TD><INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right" maxLength=10 size=10 name=mini_price value="<?=($data->mini_price!=0?$data->mini_price:"")?>"> 원&nbsp;&nbsp;<span class="font_ornage">* 미입력시 자동 계산되어 입력됩니다.</span></TD>
				</tr>

				<tr>
					<th><span>공동구매 형식</span></th>
					<TD>
					<INPUT class=input id=idx_count0 onclick="this.form.count.disabled=true;this.form.count.style.background='#f4f4f4'" type=radio value=0 name=sel_count <?php if($data->count==1)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_count0>슬라이드식</LABEL><br>
					<INPUT class=input id=idx_count1 onclick="this.form.count.disabled=false;this.form.count.style.background='#ffffff'" type=radio value=1 name=sel_count <?php if($data->count>1)echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_count1>계단식 =&gt; 감소단위</LABEL> : <INPUT class=input onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right;<?php if($data->count==1)echo"BACKGROUND:#f4f4f4";?>" maxLength="10" size="10" name=count <?php if($data->count>1)echo"value=\"{$data->count}\""; else echo"disabled";?>> 개&nbsp;&nbsp;&nbsp;&nbsp;<FONT class=font_orange><span class="font_orange">* 감소단위에 입력한 수량 단위로 가격이 변동합니다.</span></FONT>
					</TD>
				</tr>

				<tr>
					<th><span>배송료</span></th>
					<TD>
						
						<INPUT class=input id=idx_deli_money0 type=radio value="" name=sel_deli_money <?php if(ord($data->deli_money)==0) echo " checked";?> onclick="chk_deli_money('disabled','#f4f4f4')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_deli_money0>무료</LABEL> &nbsp;&nbsp;
						<INPUT class=input id=idx_deli_money1 type=radio value=0 name=sel_deli_money <?php if(ord($data->deli_money) && $data->deli_money==0) echo " checked";?> onclick="chk_deli_money('disabled','#f4f4f4')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_deli_money1>착불</LABEL> &nbsp;&nbsp;
						<INPUT class=input id=idx_deli_money2 type=radio value=1 name=sel_deli_money <?php if($data->deli_money>0) echo " checked";?> onclick="chk_deli_money('','#ffffff')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_deli_money2>배송료</LABEL> : 
						<INPUT class=input maxLength="10" size="10" name=deli_money <?php if($data->deli_money>0)echo"value={$data->deli_money}";else echo"disabled";?> onkeyup=strnumkeyup(this) style="TEXT-ALIGN: right;<?php if($data->deli_money<=0)echo"BACKGROUND:#f4f4f4;";?>"> 원
					</TD>
				</tr>

				<tr>
					<th><span>실제이미지</span></th>
					<TD>             
						<input type=file name=image1 size=50><br>
					<!--
                		<input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly">
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" />
						<input type=file name=image1 value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName1').value = this.value" ><br />	</div>   
						-->
                    <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* GIF, JPG만 가능</span>
<?php
					if(ord($data->image1)) {
						if(file_exists($imagepath.$data->image1)) {
							echo "<br><img src=\"".$imagepath.$data->image1."\" border=0 style=\"border:#e1e1e1 solid 1px\">";
						} else {
							echo "<br>등록된 이미지가 없습니다.";
						}
					}
?>
					</TD>
				</tr>

				<tr>
					<th><span>중간이미지</span></th>
					<TD>                    
						<input type=file name=image2 size=50><br>
					<!--
                        <input type="text" id="fileName2" class="file_input_textbox w400" readonly="readonly">
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" />
						<input type=file name=image2 value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName2').value = this.value" ><br />	</div>   
					-->
                    <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* GIF, JPG만 가능</span>
<?php
					if(ord($data->image2)) {
						if(file_exists($imagepath.$data->image2)) {
							echo "<br><img src=\"".$imagepath.$data->image2."\" border=0 style=\"border:#e1e1e1 solid 1px\">";
						} else {
							echo "<br>등록된 이미지가 없습니다.";
						}
					}
?>
					</TD>
				</tr>

				<tr>
					<th><span>작은이미지</span></th>
					<TD>
						<input type=file name=image3 size=50><br>
						<!--
						<input type="text" id="fileName3" class="file_input_textbox w400" readonly="readonly">
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" />
						<input type=file name=image3 value="" style="width:100%;" class="file_input_hidden" onchange="javascript: document.getElementById('fileName3').value = this.value" ><br />	</div>   
						-->
                        <span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* GIF, JPG만 가능</span>
<?php
					if(ord($data->image3)) {
						if(file_exists($imagepath.$data->image3)) {
							echo "<br><img src=\"".$imagepath.$data->image3."\" border=0 style=\"border:#e1e1e1 solid 1px\">";
						} else {
							echo "<br>등록된 이미지가 없습니다.";
						}
					}
?>
					</TD>
				</tr>

				<tr>
					<th><span>경매 상세내용<br>&nbsp;&nbsp;&nbsp;(제품설명,배송,반품등)</span></th>
					<TD class="td_con1"><TEXTAREA style="WIDTH: 100%; HEIGHT: 150px" name=content wrap=off class="textarea" id="ir1"><?=$data->content?></TEXTAREA></TD>
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
							<dt><span>등록 경매 관리</span></dt>
							<dd>
								- 공동구매 시작가격은 100원 이상부터 가능합니다.<br>
								- 공동구매 상품 가격은 <b>시작가격</b>으로 시작해서 공동구매 참여자가 늘때마다 <b>참여시 감소가격</b> 단위로 가격이 떨어집니다.<br>
								- 공동구매형식에서 <b>슬라이드식</b>은 상품수량 1개마다, <b>계단식</b>은 지정된 상품수 단위로 가격이 떨어집니다.<br>
								- 이미지(실제이미지+중간이미지+작은이미지) 업로드 용량은 최대 300KB 이하만 가능합니다.<br>
								- 가격 및 수량 입력시 콤마(,)를 제외하고 입력해 주세요.<br>
								- 등록된 공동구매 상품 중 참여자가 있는 공동구매 상품은 수정할 수 없습니다.
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
