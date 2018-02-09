<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
$_data = $_shopdata;

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
@set_time_limit(300);
$imagepath=$Dir.DataDir."shopimages/etc/";
$filename="memexcelupfile.csv";
$filepath2=$imagepath."member_error.csv";
@unlink($imagepath.$filename);

$mode=$_POST["mode"];
$group_code=$_POST["group_code"];
$upfile=$_FILES["upfile"];

$reg_group=$_shopdata->group_code;
//exdebug("group = ".$reg_group);

// 가입금 축하적립금 사용유무 체크 ..
// 일괄등록시는 가입 축하금 지급은 일단 안하는걸로...단, csv 에 등록된 적립금은 지급한다.
// 2016-05-18 by JeongHo,Jeong
//$reserve_join=(int)$_shopdata->reserve_join;
//exdebug("reserve_join = ".$reserve_join);

$group_list=array();
$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_level";
$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)){
	if(ord($group_code)) {
		if($row->group_code==$group_code) {
			$reg_group=$row->group_code;
		}
	}
	$group_list[]=$row;
}

if($mode=="upload" && ord($upfile['name']) && $upfile['size']>0) {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$ext = strtolower(pathinfo($upfile['name'],PATHINFO_EXTENSION));
	if($ext=="csv") {
		copy($upfile['tmp_name'],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
	} else {
		alert_go("파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 엑셀(CSV) 파일만 등록 가능합니다.");
	}

	########################################################################################################
	# 0=>아이디, 1=>비밀번호, 2=>이름, 3=>이메일, 4=>휴대폰, 5=>이메일수신여부, 6=>SMS수신여부
	# 7=>집전화, 8=>집우편번호, 9=>집주소(동/읍/면 이상), 10=>집주소(번지 미만), 11=>회사전화
	# 12=>회사우편번호, 13=>회사주소(동/읍/면 이상), 14=>회사주소(번지 미만), 15=>적립금, 16=>가입일
	########################################################################################################

    //setlocale(LC_CTYPE, 'ko_KR.eucKR'); 

	$query0="INSERT INTO tblmember (id,passwd,name,email,mobile,news_yn,gender,home_post,home_addr,home_tel,office_post,office_addr,office_tel,reserve,joinip,date,group_code,nickname) VALUES ";
    //echo $query0."<br>";

	$query1 = array();
    $point_arr = array();   // 포인트 지급할 명단
	$error_list=array();
	$memcnt=0;
	$filepath=$imagepath.$filename;
	$fp=fopen($filepath,"r");
	$yy=0;

	//while($field=@fgetcsv($fp, 4096, ",")) {
    while($field=fgetcsv($fp, 135000, ",", "'", "\\")) {
		if($yy++==0) continue;

        //$field = recursive_iconv('EUC-KR','UTF-8',$field);
        //print_r($field);

        //echo "1<br>";
		$id=trim($field[0]);
		$passwd=trim($field[1]);
		$name=iconv('EUC-KR','UTF-8', trim($field[2]));
		$email=trim($field[3]);
		$mobile=trim($field[4]);
		$news_mail_yn=trim($field[5]);
		$news_sms_yn=trim($field[6]);
		$home_tel=trim($field[7]);
		$home_post=trim($field[8]);
		$home_post=@str_replace("-","",$home_post);
		$home_addr1=iconv('EUC-KR','UTF-8', trim($field[9]));
		$home_addr2=iconv('EUC-KR','UTF-8', trim($field[10]));
		$office_tel=trim($field[11]);
		$office_post=trim($field[12]);
		$office_post=@str_replace("-","",$office_post);
		$office_addr1=iconv('EUC-KR','UTF-8', trim($field[13]));
		$office_addr2=iconv('EUC-KR','UTF-8', trim($field[14]));
		$reserve=(int)trim(str_replace(",", "", $field[15]));

		$date=trim(@str_replace("/","",$field[16]));
		$date=@str_replace("-","",$date);
		if(strlen($date)!=8) $date=date("Ymd");
		$date.="000000";

        if($nickname == "") $nickname = str_pad(substr($email, 0, 4), 10, '*', STR_PAD_RIGHT);
        if($name == "") $name = str_pad(substr($email, 0, 4), 10, '*', STR_PAD_RIGHT);

		if(!strstr("YN",$news_mail_yn)) {
			$news_mail_yn="Y";
		}
		if(!strstr("YN",$news_sms_yn)) {
			$news_sms_yn="Y";
		}
		if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
			$news_yn="Y";
		} else if($news_mail_yn=="Y") {
			$news_yn="M";
		} else if($news_sms_yn=="Y") {
			$news_yn="S";
		} else {
			$news_yn="N";
		}

        $field[18] = "";
		$joinip="127.0.0.1";

		if(ord($id)==0 || ord($passwd)==0 || ord($name)==0 || ord($email)==0) {
            $field[18] = iconv('UTF-8', 'EUC-KR', 'ID 또는 PASSWD 또는 NAME 또는 EMAIL 값이 없습니다.');
			$error_list[]=$field;
            //echo "1-1<br>";
			continue;
		} /*
        else if(!IsAlphaNumeric($id)) {
			$error_list[]=$field;
            echo "1-2<br>";
			continue;
		} 
        else if(!ismail($email)) {
			$error_list[]=$field;
            echo "1-3<br>";
			continue;
		} */

        //echo "2<br>";

		//아이디 중복 체크
		$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE id='{$id}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row->cnt>=1) {
            $field[18] = iconv('UTF-8', 'EUC-KR', 'ID 중복입니다.');
			$error_list[]=$field;
            //echo "1-4<br>";
			continue;
		}

        //echo "3<br>";

		$gender="";

		//이메일 중복 체크
		$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE email='{$email}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row->cnt>=1) {
            $field[18] = iconv('UTF-8', 'EUC-KR', 'EMAIL 중복입니다.');
			$error_list[]=$field;
            //echo "1-5<br>";
            //exdebug($field);
			continue;
		}

		$home_addr="";
		//if(strlen($home_post)==6) $home_addr=$home_addr1."↑=↑".$home_addr2;
        if(strlen($home_post) >= 5) {
            $home_addr=$home_addr1."↑=↑".$home_addr2;
		    $home_addr = str_replace("'","\'",$home_addr);
        } else {
            $field[18] = iconv('UTF-8', 'EUC-KR', '집 우편번호 오류입니다.');
            $error_list[]=$field;
            //echo "1-1<br>";
			continue;
        }
        //echo "$home_post";
        //echo "$home_addr";

		$office_addr="";
		//if(strlen($office_post)==6) $office_addr=$office_addr1."=".$office_addr2;
        if(strlen($office_post) >= 5) { 
            $office_addr=$office_addr1."↑=↑".$office_addr2;
		    $office_addr = str_replace("'","\'",$office_addr);
        } else {
            $field[18] = iconv('UTF-8', 'EUC-KR', '회사 우편번호 오류입니다.');
            $error_list[]=$field;
            //echo "1-1<br>";
			continue;
        }

		//비밀번호 재조정
        $passwd = "*".strtoupper(SHA1(unhex(SHA1($passwd))));

		$memcnt++;
		$query1[] = "('{$id}','{$passwd}','{$name}','{$email}','{$mobile}','{$news_yn}','{$gender}','{$home_post}','{$home_addr}','{$home_tel}','{$office_post}','{$office_addr}','{$office_tel}','{$reserve}','{$joinip}','{$date}','{$reg_group}','{$nickname}')";

        // insert_point 처리 추가해야됨.2016-05-17
        $point_arr[$memcnt-1]['id'] = $id;
        $point_arr[$memcnt-1]['point'] = $reserve;

		if($memcnt==1000) {
			$query=$query0.implode(',',$query1);
			pmysql_query($query,get_db_conn());

            // insert_point 처리 추가해야됨.2016-05-17
            //exdebug($point_arr);
            for($i = 0; $i < count($point_arr); $i++) {
                insert_point_act($point_arr[$i]['id'], $point_arr[$i]['point'], '관리자 포인트 지급', '@admin', $_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''), 0);
            }
			$memcnt=0;
			$query1 = array();
            $point_arr = array();
		}
	}
	@fclose($fp);
	@unlink($filepath);

	if($memcnt>0) {
		$query=$query0.implode(',',$query1);
		pmysql_query($query,get_db_conn());
        //echo $query."<br><hr>";

        // insert_point 처리 추가해야됨.2016-05-17
        //exdebug($point_arr);
        for($i = 0; $i < count($point_arr); $i++) {
            insert_point_act($point_arr[$i]['id'], $point_arr[$i]['point'], '관리자 포인트 지급', '@admin', $_ShopInfo->id, $_ShopInfo->id.'-'.uniqid(''), 0);
        }
	}

    //exdebug($error_list);
	@unlink($filepath2);
	if(count($error_list)>0) {
		$fp2=fopen($filepath2,"a");
		for($i=0;$i<count($error_list);$i++) {
			if(count($error_list[$i])>0) {
				fputcsv($fp2,$error_list[$i]);
			}
		}
		@fclose($fp2);
	}
	alert_go('회원정보 등록이 완료되었습니다.');
} else if($mode=="error_del") {
	@unlink($filepath2);
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
var isupload=false;
function CheckForm() {
	if(isupload) {
		alert("######### 현재 회원정보를 등록중입니다. #########");
		return;
	}
    /*
	if(document.form1.group_code.value=="") {
		if(!confirm("회원등급을 설정하지않고 등록하시겠습니까?")) {
			return;
		}
	} else {
		temp=document.form1.group_code.options[document.form1.group_code.selectedIndex].text;
		if(!confirm("\""+temp+"\" 회원등급으로 등록하시겠습니까?")) {
			return;
		}
	}
    */

	isupload=true;
	document.all.uploadButton.style.filter = "Alpha(Opacity=60) Gray";
	document.form1.mode.value="upload";
	document.form1.submit();
}

function delete_errfile() {
	if(isupload) {
		alert("######### 현재 회원정보를 등록중입니다. #########");
		return;
	}
	if(confirm("등록 실패한 회원정보 엑셀파일을 서버에서 삭제하시겠습니까?")) {
		document.form1.mode.value="error_del";
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원정보관리 &gt;<span>회원정보 일괄 등록</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">회원정보 일괄 등록</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>다수의 회원정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등급별 회원정보 일괄 등록 처리</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<div class="table_style01">				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>엑셀 등록양식 다운로드</span></th>
					<TD><A HREF="images/sample/member_reg2.csv"><img src="images/btn_down1.gif" border=0 align=absmiddle></A> <span class="font_orange">＊엑셀(CSV)파일을 내려받은 후 예제와 같이 작성합니다.</span></TD>
				</TR>
				<!-- <TR>
					<th><span>회원등급 선택</span></th>
					<TD>
					<select name=group_code>
						<option value="">회원등급을 선택하세요.</option>
<?php
						for($i=0;$i<count($group_list);$i++) {
							echo "<option value=\"{$group_list[$i]->group_code}\">{$group_list[$i]->group_name}</option>\n";
						}
?>
					</select>
					<span class="font_orange">＊등급설정은 <B>"회원관리 -> 회원등급 설정"</B>에서 하시면 됩니다.</span>
					</TD>
				</TR> -->
				<TR>
					<th><span>엑셀파일(CSV) 등록</span></th>
					<TD class="td_con1">
					<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" /> 
					<input type=file name=upfile style="width:54%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ></div><span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
					
				</TR>

				<?php if(file_exists($filepath2)){?>
				<TR>
					<th><span><font color=red>등록실패 엑셀 관리</font></span></th>
					<TD><A HREF="<?=$filepath2?>"><B>[다운로드]</B></A> <img width=10 height=0> <A HREF="javascript:delete_errfile()"><B>[삭제하기]</B></A> &nbsp;&nbsp;&nbsp; <span class="font_orange">＊등록 실패한 데이터만 엑셀(CSV)파일로 다운/삭제 하실 수 있습니다.</span></TD>
				</TR>
				<?php }?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
	
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>회원정보 일괄 등록</span></dt>
<dd>
- 회원정보를 일괄 등록 하거나, 타 쇼핑몰 이용 고객 정보를 이전하는데 유용하게 사용됩니다.
						<br>
						<span class="font_orange" style="padding-left:0px"><B>- 회원데이터를 이전할 경우 회원의 동의가 꼭 필요하오니 회원 동의 후 이전하시기 바랍니다.</B></span>
</dd>
	
						</dl>
<dl>
	<dt><span>엑셀(CSV)파일 작성 순서</span></dt>
<dd>
- 엑셀파일 작성시 두번 째 라인부터 데이터를 입력하시기 바랍니다. (첫 라인은 필드 설명부분)<br>
- 각 항목의 값은 <FONT class=font_orange><B>반드시 ' ' 로 감싸주십시오 (예: '홍길동')</B></font><br>
- 아래 형식대로 <FONT class=font_orange><B>엑셀파일 작성 -> 다른이름으로 저장 -> CSV(쉼표로 분리)</B></font> 순으로 저장하시면 됩니다.
</dd>

</dl>
<dl>
	<dt><span>회원정보 일괄등록 방법</span></dt>
<dd>
- ① 아래의 형식을 참고로 회원정보 엑셀파일을 작성합니다.<br>
						<span class="font_orange" style="padding-left:10px">----------------------------------------------------- 상품정보 엑셀 형식 ------------------------------------------------------</span><br>
						<span class="font_blue" style="padding-left:25px">아이디, 비밀번호, 이름, 이메일, 휴대폰, 이메일수신, SMS수신, 집전화, 집우편번호, 집주소(동/읍/면 이상), </span>
						<br>
						<span class="font_blue" style="padding-left:25px">집주소(번지 미만), 회사전화, 회사우편번호, 회사주소(동/읍/면 이상), 회사주소(번지 미만), 적립금, 가입일, 가입경로</span><br>
						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>

						<div style="padding-left:30">
						<table border=0 cellpadding=0 cellspacing=0 width=600>
						<col width=145></col>
						<col width=></col>
						<tr>
							<td colspan=2 align=center style="padding-bottom:5">
							<B>회원정보 엑셀 작성 예)</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">아이디<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'ajashop' <img width=20 height=0><FONT class=font_orange>(영문/숫자 4~12자) <B>- 아이디 중복시 등록이 않됩니다</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">비밀번호<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'1234' <img width=20 height=0>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이름<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'홍길동'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<!-- <tr>
							<td class=table_cell align=right style="padding-right:15">주민번호

							<?php if($_shopdata->resno_type!="N") {?>
							<FONT class=font_orange>(*)</font>
							<?php }?>

							</td>
							<td class=td_con1 style="padding-left:5;">
							701103-1000000 <FONT class=font_orange>=>일반적인 주민번호</font>
							<br>701103-1[670b14728ad9902aecba32e22fa4f6bd] <FONT class=font_orange>=> 주민번호 뒤6자리 암호화</font>
							<br>
							<FONT class=font_orange><B>(주민번호 중복시 등록이 않됩니다.)</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr> -->
						<tr>
							<td class=table_cell align=right style="padding-right:15">이메일<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'hong@ajashop.co.kr' <img width=20 height=0><FONT class=font_orange><B>(이메일 중복시 등록이 않됩니다.)</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">휴대폰</td>
							<td class=td_con1 style="padding-left:5;">
							'010-000-0000'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이메일 수신여부<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'Y'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">SMS 수신여부<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'Y'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집전화<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'02-00-0000'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집 우편번호<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'137-070'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집주소 (동/읍/면 이상)<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'서울시 서초구 서초동'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집주소 (번지 미만)<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							'1358-18번지 XX빌딩 8층'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사전화</td>
							<td class=td_con1 style="padding-left:5;">
							'02-111-1111'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사 우편번호</td>
							<td class=td_con1 style="padding-left:5;">
							'137-073'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사주소 (동/읍/면 이상)</td>
							<td class=td_con1 style="padding-left:5;">
							'서울시 서초구 방배동'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사주소 (번지 미만)</td>
							<td class=td_con1 style="padding-left:5;">
							'18-18번지 XX빌딩 3층'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">적립금</td>
							<td class=td_con1 style="padding-left:5;">
							'0'
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">가입일</td>
							<td class=td_con1 style="padding-left:5;">
							'2007/05/10' <img width=20 height=0><FONT class=font_orange>(현재 날짜로 등록시 공란)</font>
							</td>
						</tr>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<!-- <tr>
							<td class=table_cell align=right style="padding-right:15">가입경로</td>
							<td class=td_con1 style="padding-left:5;">
							'YTN' <img width=20 height=0><FONT class=font_orange>(등록된 학교명과 일치해야 됩니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr> -->
						</table>
						</div>

						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>
						- ② 엑셀(CSV)파일을 선택합니다.<br>
						- ③ [파일등록] 버튼을 이용하여 업로드 완료 하면 회원정보가 등록됩니다.
					
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
