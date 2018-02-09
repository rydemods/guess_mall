<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type = $_POST["type"];
$up_privercyname = $_POST["up_privercyname"];
$up_privercyemail = $_POST["up_privercyemail"];
/*
$up_privercy = addslashes($_POST["up_privercy"]);
$up_privercy2 = addslashes($_POST["up_privercy2"]);
*/
$up_privercy = addslashes($_POST["up_privercy"]);
exdebug($up_privercy);
$up_privercy2 = ($_POST["up_privercy2"]);

$up_file1=$_FILES["up_file1"];
$up_file2=$_FILES["up_file2"];

$filepath = $Dir."w3c/";
$fileurl = "http://".$_ShopInfo->getShopurl()."w3c/";

if ($type == "up") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$flag = $row->cnt;
	pmysql_free_result($result);

	if ($flag) {
		//$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
		$onload="<script>window.onload=function(){alert(\"정보 수정이 완료되었습니다.\");}</script>";
		$sql = "UPDATE tbldesign SET privercy = '".str_replace("<P>&nbsp;</P>", "", $up_privercy).(ord(str_replace("<P>&nbsp;</P>", "", $up_privercy2))?"=".$up_privercy2:"")."' ";
	} else {
		//$onload = "<script> alert('정보 등록이 완료되었습니다.'); </script>";
		$onload="<script>window.onload=function(){alert(\"정보 등록이 완료되었습니다.\");}</script>";
		$sql = "INSERT INTO tbldesign(privercy) VALUES('".$up_privercy.(ord($up_privercy2)?"=".$up_privercy2:"")."')";
	}
	$insert = pmysql_query($sql,get_db_conn());
	if ($insert) {
		$sql = "UPDATE tblshopinfo SET ";
		$sql.= "privercyname	= '{$up_privercyname}', ";
		$sql.= "privercyemail	= '{$up_privercyemail}' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
	}
	
	if (ord($up_file1['name'])) {
		$ext = strtolower(pathinfo($up_file1['name'],PATHINFO_EXTENSION));
		if ($ext!="xml") {
			//$onload="<script> alert(\"전자적표시 파일1 확장자는 xml 만 가능합니다.\"); </script>";
			$onload="<script>window.onload=function(){alert(\"정보 등록이 완료되었습니다.\");} </script>";
		} else if (strtolower($up_file1['name'])!="p3p.xml") {
			//$onload = "<script>alert (\"전자적표시 파일1 이름은 p3p.xml 만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"전자적표시 파일1 이름은 p3p.xml 만 가능합니다.\");} </script>";
		} else {
			$file1_name="p3p.xml";
			if(ord(RootPath)) {
				$p3pdata = file_get_contents($up_file1['tmp_name']);
					if(ord($p3pdata)) {
						$p3pdata = str_replace("/w3c/p3policy.xml", "/".RootPath."w3c/p3policy.xml", $p3pdata);
						file_put_contents($filepath.$file1_name,$p3pdata);
					} else {
						//$onload = "<script>alert (\"전자적표시 파일1 내용이 존재하지 않습니다.\");</script>";
						$onload="<script>window.onload=function(){alert(\"전자적표시 파일1 내용이 존재하지 않습니다.\");} </script>";
					}
			} else {
				@unlink($filepath.$file1_name);
				move_uploaded_file($up_file1['tmp_name'],"$filepath$file1_name");
				chmod("$filepath$file1_name",0644);
			}
		}
	} else if($file1delete=="Y") {
		$file1_name="p3p.xml";
		@unlink($filepath.$file1_name);
	}

	if (ord($up_file2['name'])) {
		$ext = strtolower(pathinfo($up_file2['name'],PATHINFO_EXTENSION));
		if ($ext!="xml") {
			//$onload = "<script>alert (\"전자적표시 파일2 확장자는 xml 만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"전자적표시 파일2 확장자는 xml 만 가능합니다.\");} </script>";
		} else if (strtolower($up_file2['name'])!="p3policy.xml") {
			//$onload = "<script>alert (\"전자적표시 파일2 이름은 p3policy.xml 만 가능합니다.\");</script>";
			$onload="<script>window.onload=function(){alert(\"전자적표시 파일2 이름은 p3policy.xml 만 가능합니다.\");} </script>";
		} else {
			$file2_name="p3policy.xml";
			@unlink($filepath.$file2_name);
			move_uploaded_file($up_file2['tmp_name'],"$filepath$file2_name");
			chmod("$filepath$file2_name",0644);
		}
	} else if($file2delete=="Y") {
		$file2_name="p3policy.xml";
		@unlink($filepath.$file2_name);
	}
}

$sql = "SELECT privercy FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$privercy_exp = @explode("=", $row->privercy);
	$privercy = ($privercy_exp[0] == "<P>&nbsp;</P>"?"":$privercy_exp[0]);
	$privercy2 = ($privercy_exp[1] == "<P>&nbsp;</P>"?"":$privercy_exp[1]);
}
pmysql_free_result($result);
if(ord($privercy)==0 && file_exists($Dir.AdminDir."privercy.txt")) {
	$privercy = file_get_contents($Dir.AdminDir."privercy.txt");
}
if(ord($privercy2)==0 && file_exists($Dir.AdminDir."privercy2.txt")) {
	$privercy2 = file_get_contents($Dir.AdminDir."privercy2.txt");
}
$sql = "SELECT privercyname, privercyemail FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
pmysql_free_result($result);
$privercyname = $row->privercyname;
$privercyemail = $row->privercyemail;

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
_editor_url = "htmlarea/";
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	//alert(sHTML);
	form1.up_privercy.value=sHTML;
// 	var sHTML2 = oEditors2.getById["ir2"].getIR();
// 	form1.up_privercy2.value=sHTML2;
	//var tmpobj1=document.all["_up_privercy_editor"];
	//var tmpobj2=document.all["_up_privercy2_editor"];
	//form1.up_privercy.value=tmpobj1.contentWindow.document.body.innerHTML;
	//form1.up_privercy2.value=tmpobj2.contentWindow.document.body.innerHTML;
	form1.type.value="up";
	form1.submit();
}

function BasicTerms(){
	var str = '';
		str += '<p class="MsoNormal"><span lang="EN-US">[COMPANY] </span>개인정보취급방침<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">[COMPANY](</span>이하<span lang="EN-US"> “</span>회사<span lang="EN-US">”</span>라 함<span lang="EN-US">)</span>는 이용자들의 개인정보보호를 매우 중요시하며<span lang="EN-US">, "</span>정보통신망 이용촉진 및 정보보호에 관한 법률<span lang="EN-US">”</span>을 준수하고';
		str += '있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal">회사는 개인정보취급방침을 통하여 고객님께서 제공하시는 개인정보가 어떠한 용도와 방식으로 이용되고 있으며<span lang="EN-US">, </span>개인정보보호를 위해 어떠한 조치가 취해지고 있는지 알려드립니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">회사는 개인정보취급방침을 개정하는 경우 웹사이트 공지사항<span lang="EN-US">(</span>또는 개별공지<span lang="EN-US">)</span>을 통하여 공지할 것입니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">[COMPANY]</span>의 개인정보 취급방침은 다음과 같은 내용을 담고 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">1. </span>수집하는 개인정보의 항목 및 수집방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">2. </span>개인정보의 수집 및 이용목적<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">3. </span>개인정보의 보유 및 이용기간<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">4. </span>개인정보의 파기 절차 및 방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">5. </span>개인정보의 제<span lang="EN-US">3</span>자';
		str += '제공 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">6. </span>개인정보의 취급위탁<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">7. </span>이용자 및 법정대리인의 권리와 그 행사방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">8. </span>쿠키<span lang="EN-US">(cookie)</span>의';
		str += '운영에 관한 사항<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">9. </span>개인정보관리책임자 및 담당자의 연락처<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">10. </span>고지의 의무<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">1. </span>수집하는 개인정보의 항목 및 수집방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 회원가입<span lang="EN-US">, </span>비회원 구매<span lang="EN-US">, </span>상담<span lang="EN-US">, </span>불량이용의 방지 등을 위해 아래와 같은 개인정보를 수집하고 있습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>필수항목<span lang="EN-US"> : </span>이름<span lang="EN-US">, ID, </span>비밀번호<span lang="EN-US">, </span>주민등록번호<span lang="EN-US">, </span>이메일<span lang="EN-US">, </span>전화번호<span lang="EN-US">, </span>주소<span lang="EN-US">, IP';
		str += 'Address, </span>결제기록<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>선택항목<span lang="EN-US"> : </span>개인맞춤';
		str += '서비스를 제공하기 위하여 회사가 필요로 하는 정보 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">2. </span>개인정보의 수집 및 이용목적<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">가<span lang="EN-US">. </span>서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산<span lang="EN-US">, </span>콘텐츠 제공<span lang="EN-US">, </span>구매 및 요금 결제<span lang="EN-US">,';
		str += '<o:p></o:p></span></p><p class="MsoNormal">물품배송 또는 청구지 등 발송<span lang="EN-US">, </span>금융거래 본인 인증 및 금융서비스';
		str += '<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">나<span lang="EN-US">. </span>회원 관리<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회원제 서비스 이용에 따른 본인확인<span lang="EN-US">, </span>개인 식별<span lang="EN-US">, </span>불량회원의 부정 이용 방지와 비인가 사용 방지<span lang="EN-US">, <o:p></o:p></span></p><p class="MsoNormal">가입 의사 확인<span lang="EN-US">, </span>연령확인<span lang="EN-US">, </span>불만처리';
		str += '등 민원처리<span lang="EN-US">, </span>고지사항 전달 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">다<span lang="EN-US">. </span>마케팅 및 광고에 활용<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">이벤트 등 광고성 정보 전달<span lang="EN-US">, </span>접속 빈도 파악 또는 회원의 서비스';
		str += '이용에 대한 통계 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">3. </span>개인정보의 보유 및 이용기간<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">단<span lang="EN-US">, </span>상법 및<span lang="EN-US"> “</span>전자상거래등에서의';
		str += '소비자보호에 관한 법률<span lang="EN-US">” </span>등 관련 법령의 규정에 의하여 다음과 같이 거래 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">관련 관리 의무 관계의 확인 등을 이유로 일정기간 보유하여야 할 필요가 있을 경우에는 일정기간 보유합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>계약 또는 청약철회 등에 관한 기록<span lang="EN-US"> : 5</span>년<span lang="EN-US"> (</span>전자상거래등에서의 소비자보호에 관한 법률<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>대금결제 및 재화 등의 공급에 관한 기록<span lang="EN-US"> : 5</span>년<span lang="EN-US"> (</span>전자상거래등에서의 소비자보호에 관한 법률<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>소비자의 불만 또는 분쟁처리에 관한 기록<span lang="EN-US"> : 3</span>년<span lang="EN-US"> (</span>전자상거래등에서의 소비자보호에 관한 법률<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>설문조사<span lang="EN-US">, </span>이벤트 등';
		str += '일시적 목적을 위하여 수집한 경우<span lang="EN-US"> : </span>당해 설문조사<span lang="EN-US">, </span>이벤트';
		str += '등의 종료 시점<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>본인확인에 관한 기록<span lang="EN-US"> : 6</span>개월<span lang="EN-US">(</span>정보통신망 이용촉진 및 정보보호 등에 관한 법률<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>방문<span lang="EN-US">(</span>로그<span lang="EN-US">)</span>에 관한 기록<span lang="EN-US"> : 3</span>개월<span lang="EN-US">(</span>통신비밀보호법<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">4. </span>개인정보의 파기 절차 및 방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">파기절차 및 방법은 다음과 같습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">가<span lang="EN-US">. </span>파기절차<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">이용자가 서비스 이용 등을 위해 입력하신 정보는 목적이 달성된 후 별도의<span lang="EN-US"> DB</span>로';
		str += '옮겨져<span lang="EN-US">(</span>종이의 경우 별도의 서류함<span lang="EN-US">) </span>내부 방침 및 기타 관련';
		str += '법령에 의한 정보보호 사유에 따라<span lang="EN-US">(</span>보유 및 이용기간 참조<span lang="EN-US">) </span>일정';
		str += '기간 저장된 후 파기됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal">별도<span lang="EN-US"> DB</span>로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 보유되어지는';
		str += '이외의 다른 목적으로 이용되지 않습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">나<span lang="EN-US">. </span>파기방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>종이에 출력된 개인정보<span lang="EN-US"> : </span>분쇄기로';
		str += '분쇄하거나 소각 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>전자적 파일 형태로 저장된 개인정보<span lang="EN-US"> : </span>기록을 재생할 수 없는 기술적 방법을 사용하여 삭제<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">5. </span>개인정보의 제<span lang="EN-US">3</span>자';
		str += '제공 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">이용자의 개인정보는 개인정보의 수집 및 이용목적에서 동의한 범위 내에서 사용하며<span lang="EN-US">, </span>이용자의 사전 동의 없이는 동 범위를 초과하여 이용하거나 원칙적으로 이용자의 개인정보를 외부에 공개하지 않습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">다만 아래의 경우에는 예외로 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>이용자들이 사전에 동의한 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>법령의 규정에 의거하거나<span lang="EN-US">, </span>수사';
		str += '목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">6. </span>개인정보의 취급위탁<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">원활한 업무 처리를 위해 이용자의 개인정보를 위탁 처리할 경우 반드시 사전에 개인정보 처리위탁을 받는 자<span lang="EN-US">(</span>이하<span lang="EN-US"> ‘</span>수탁자<span lang="EN-US">’</span>라 합니다<span lang="EN-US">)</span>와 개인정보 처리위탁을 하는 업무의 내용을 고지합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">현재 회사의 개인정보처리 수탁자와 그 업무의 내용은 다음과 같습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">위탁 대상자<span lang="EN-US">(</span>수탁자<span lang="EN-US">) : </span>위탁업무';
		str += '내용<span lang="EN-US">(</span>수탁업무<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">[</span>배송업체명 기입<span lang="EN-US">]&nbsp;&nbsp; : </span>상품 배송 업무 및 배송위치<span lang="EN-US"> / </span>도착정보';
		str += '등의 서비스 제공<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">㈜코리아센터닷컴<span lang="EN-US">&nbsp;&nbsp;';
		str += ': </span>고객정보<span lang="EN-US"> DB</span>시스템 위탁운영<span lang="EN-US">(</span>전산아웃소싱<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal">서울신용평가정보㈜<span lang="EN-US"> : </span>본인인증<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">[PG</span>업체명 기입<span lang="EN-US">]&nbsp;&nbsp;&nbsp; :&nbsp; </span>결제관련<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">7. </span>이용자 및 법정대리인의 권리와 그 행사방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">가<span lang="EN-US">. </span>이용자 및 법정 대리인은 언제든지 등록되어 있는 자신 혹은 당해';
		str += '만<span lang="EN-US"> 14</span>세 미만 아동의 개인정보를 조회하거나 수정할 수 있으며 가입해지<span lang="EN-US">(</span>동의철회<span lang="EN-US">)</span>를 요청할 수도 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">나<span lang="EN-US">. </span>이용자 혹은 만<span lang="EN-US"> 14</span>세';
		str += '미만 아동의 개인정보 조회<span lang="EN-US">, </span>수정을 위해서는 “개인정보변경”<span lang="EN-US">(</span>또는<span lang="EN-US"> “</span>회원정보수정<span lang="EN-US">” </span>등<span lang="EN-US">)</span>을<span lang="EN-US">, </span>가입해지<span lang="EN-US">(</span>동의철회<span lang="EN-US">)</span>를';
		str += '위해서는<span lang="EN-US"> "</span>회원탈퇴<span lang="EN-US">"</span>를 클릭하여 본인 확인';
		str += '절차를 거치신 후 직접 열람<span lang="EN-US">, </span>정정 또는 탈퇴가 가능합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">다<span lang="EN-US">. </span>혹은 개인정보관리책임자에게 서면<span lang="EN-US">, </span>전화 또는 이메일로 연락하시면 지체 없이 조치하겠습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">라<span lang="EN-US">. </span>이용자가 개인정보의 오류에 대한 정정을 요청하신 경우에는 정정을';
		str += '완료하기 전까지 당해 개인정보를 이용 또는 제공하지 않습니다<span lang="EN-US">. </span>또한 잘못된 개인정보를 제<span lang="EN-US">3</span>자에게 이미 제공한 경우에는 정정 처리결과를 제<span lang="EN-US">3</span>자에게 지체 없이';
		str += '통지하여 정정이 이루어지도록 하겠습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">마<span lang="EN-US">. </span>회사는 이용자 혹은 법정 대리인의 요청에 의해 해지 또는 삭제된';
		str += '개인정보는<span lang="EN-US"> "3. </span>개인정보의 보유 및 이용기간<span lang="EN-US">"</span>에';
		str += '명시된 바에 따라 처리하고 그 외의 용도로 열람 또는 이용할 수 없도록 처리하고 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">8. </span>쿠키<span lang="EN-US">(cookie)</span>의';
		str += '운영에 관한 사항<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 이용자<span lang="EN-US">(</span>접속자<span lang="EN-US">)</span>의';
		str += '정보를 수시로 저장하고 찾아내는<span lang="EN-US"> </span>쿠키<span lang="EN-US">(cookie) </span>등을';
		str += '운용합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">쿠키란 웹사이트를 운영하는데 이용되는 서버가 귀하의 브라우저에 보내는 아주 작은 텍스트 파일로서 귀하의 컴퓨터';
		str += '하드디스크에 저장됩니다<span lang="EN-US">. </span>회사는 다음과 같은 목적을 위해 쿠키를 사용합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">가<span lang="EN-US">. </span>쿠키 등 사용 목적<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회원과 비회원의 접속빈도나 방문시간 등을 분석<span lang="EN-US">, </span>이용자의 취향과 관심분야를';
		str += '파악 및 자취 추적<span lang="EN-US">, </span>각종 이벤트 참여 정도 및 방문 회수 파악 등을 통한 타겟마케팅 및 개인 맞춤';
		str += '서비스 제공귀하는 쿠키 설치에 대한 선택권을 가지고 있습니다<span lang="EN-US">. </span>따라서<span lang="EN-US">,';
		str += '</span>귀하는 웹 브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용하거나<span lang="EN-US">, </span>쿠키가 저장될 때마다';
		str += '확인을 거치거나<span lang="EN-US">, </span>아니면 모든 쿠키의 저장을 거부할 수도 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">나<span lang="EN-US">. </span>쿠키 설정 거부 방법<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">이용자는 사용하는 웹 브라우저의 옵션을 선택함으로써 모든 쿠키를 허용하거나<span lang="EN-US">, </span>쿠키를';
		str += '저장할 때마다 확인을 거치거나<span lang="EN-US">, </span>모든 쿠키의 저장을 거부하도록 선택하여 설정할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>설정방법 예<span lang="EN-US">(</span>인터넷';
		str += '익스플로어의 경우<span lang="EN-US">) : </span>웹 브라우저 상단의 도구<span lang="EN-US"> &gt; </span>인터넷';
		str += '옵션<span lang="EN-US"> &gt; </span>개인정보<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>단<span lang="EN-US">, </span>귀하께서 쿠키';
		str += '설치를 거부하였을 경우 서비스 제공에 어려움이 있을 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">9. </span>개인정보관리책임자 및 담당자의 연락처<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">회사는 고객의 개인정보를 보호하고 개인정보와 관련한 불만을 처리하기 위하여 아래와 같이 관련 부서 및 개인정보관리책임자를';
		str += '지정하고 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">고객 서비담당 부서<span lang="EN-US"> : [OOO </span>팀<span lang="EN-US">(</span>부<span lang="EN-US">)]<o:p></o:p></span></p><p class="MsoNormal">전화번호<span lang="EN-US"> : [TEL]<o:p></o:p></span></p><p class="MsoNormal">이메일<span lang="EN-US"> : [EMAIL]<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">개인정보관리책임자 성명<span lang="EN-US"> : [NAME]<o:p></o:p></span></p><p class="MsoNormal">전화번호<span lang="EN-US"> : [TEL]<o:p></o:p></span></p><p class="MsoNormal">이메일<span lang="EN-US"> : [EMAIL]<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">가<span lang="EN-US">. </span>귀하께서는 회사의 서비스를 이용하시며 발생하는 모든 개인정보보호';
		str += '관련 민원을 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">개인정보관리책임자 혹은 담당부서로 신고하실 수 있습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">나<span lang="EN-US">. </span>회사는 이용자들의 신고사항에 대해 신속하게 충분한 답변을 드릴';
		str += '것입니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">다<span lang="EN-US">. </span>기타 개인정보침해에 대한 신고나 상담이 필요하신 경우에는 아래';
		str += '기관에 문의하시기 바랍니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>개인분쟁조정위원회<span lang="EN-US">';
		str += '(www.1336.or.kr / 1336)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>정보보호마크인증위원회<span lang="EN-US">';
		str += '(www.eprivacy.or.kr / 02-580-0533~4)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>대검찰청 인터넷범죄수사센터<span lang="EN-US">';
		str += '(http://icic.sppo.go.kr / 02-3480-3600)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">- </span>경찰청 사이버테러대응센터<span lang="EN-US">';
		str += '(www.ctrc.go.kr / 02-392-0330)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">10. </span>고지의 의무<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal">현 개인정보취급방침은<span lang="EN-US"> 2012</span>년<span lang="EN-US">';
		str += '00</span>월<span lang="EN-US"> 00</span>일부터 적용됩니다<span lang="EN-US">. </span>내용의 추가<span lang="EN-US">, </span>삭제 및 수정이 있을 시에는 개정 최소<span lang="EN-US"> 7</span>일전부터 홈페이지의 공지사항을';
		str += '통하여 고지할 것입니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal">또한 개인정보취급방침에 버전번호 및 개정일자 등을 부여하여 개정여부를 쉽게 알 수 있도록 하고 있습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p>';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '';
		str += '</p><p class="MsoNormal">본 방침은<span lang="EN-US"> : 2012</span>년<span lang="EN-US"> 00</span>월<span lang="EN-US"> 00</span>일부터 시행됩니다<span lang="EN-US">.<o:p></o:p></span></p>																																																				';
	oEditors.getById["ir1"].setIR(str);
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 기본정보 설정 &gt;<span>쇼핑몰 개인정보취급방침</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<!-- 페이지 타이틀 -->
				<div class="title_depth3">쇼핑몰 개인정보 취급방침</div>
				</td>
			</tr>
			<tr>
				<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub"><span>개인정보취급방침, 정보책임자 정보를 설정합니다.</span></div>
				</td>
			</tr>
			<tr class='hide'>
				<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub">개인정보 취급 방침 전자적표시</div>
				</td>
			</tr>
			<tr class='hide'><td height=3></td></tr>
            <tr class='hide'>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>개인정보취급방침 전자적표시 파일을 등록합니다.<br><br></li>
                            <li><b>개인정보취급방침 전자적 표시 파일 등록 순서</b></li>
                            <li>1. <a href="http://www.checkprivacy.co.kr" target="_blank"><font color="#FF4C00">http://www.checkprivacy.co.kr</font></a> 에서 개인정보취급방침 전자적 표시를 작성 하세요.</li>
                            <li>2. 개인정보취급방침 전자적 표시 작성 완료 후 전자적 표시 파일을 다운로드 받습니다.</li>
                            <li>3. 받은 압축파일을 해제 후 해당 파일을 항목에 맞게 전자적 표시 파일을 등록합니다.</li>
                            <li>4. 등록이 정상적으로 완료 됐다면 <a href="http://www.checkprivacy.co.kr/user/" target="_blank"><font color="#FF4C00">http://www.checkprivacy.co.kr/user/</font></a> 에서 최종 확인하세요.<br>
				&nbsp;&nbsp;&nbsp;&nbsp;※ 서브디렉토리 사용 쇼핑몰은 최종 확인시 서브디렉토리까지 입력해 주세요.<br>
				&nbsp;&nbsp;&nbsp;&nbsp;※ 서브디렉토리에 변경사항이 있을 경우 p3p.xml 파일을 <a href="http://www.checkprivacy.co.kr" target="_blank"><font color="#FF4C00">http://www.checkprivacy.co.kr</font></a>에서 받아 재등록 해 주세요.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>			
			<tr class='hide'>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TR>
                        <th><span>전자적표시 파일1&nbsp;&nbsp;<font color="#FF4C00">p3p.xml</font></span></Th>
                        <TD class="td_con1" width="600">
						<input type=file name=up_file1 value=""  style="width:100%;" onchange="javascript: document.getElementById('fileName').value = this.value" >
						<span color=orange>엑셀(CSV) 파일만 등록 가능합니다.</span><br />
						<!--
						<?=(file_exists($filepath."p3p.xml")?"업로드 확인 : <A HREF=\"{$fileurl}p3p.xml\" target=_blank>{$fileurl}p3p.xml</a>":"등록된 파일이 없습니다.")?>
						-->
						<?=(file_exists($filepath."p3p.xml")?"업로드 확인 : {$fileurl}p3p.xml":"등록된 파일이 없습니다.")?>
					</TD>
                    </TR>
                    <TR>
                        <th><span>전자적표시 파일2&nbsp;&nbsp;<font color="#FF4C00">p3policy.xml</font></span></th>
                        <TD class="td_con1" width="600">
						<input type=file name=up_file2 value="" style="width:100%;" onchange="javascript: document.getElementById('fileName').value = this.value" >
						<span color=orange>엑셀(CSV) 파일만 등록 가능합니다.</span><br />
						<!--
						<?=(file_exists($filepath."p3policy.xml")?"업로드 확인 : <A HREF=\"{$fileurl}p3policy.xml\" target=_blank>{$fileurl}p3policy.xml</a>":"등록된 파일이 없습니다.")?>
						-->
						<?=(file_exists($filepath."p3policy.xml")?"업로드 확인 : {$fileurl}p3policy.xml":"등록된 파일이 없습니다.")?>
					</TD>
                    </TR>
				</table>
                </div>
				</td>
			</tr>
			<tr class='hide'><td height="20"></td></tr>
			<tr class='hide'>
				<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub">개인정보 취급 방침</div>
				</td>
			</tr>
			<tr class='hide'>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>개인정보취급방침, 정보책임자 정보를 설정합니다.<br><br></li>
                            <li><b>개인정보취급방침 내용 등록 순서</b></li>
                            <li>1. <a href="http://www.checkprivacy.co.kr" target="_blank"><font color="#FF4C00">http://www.checkprivacy.co.kr</font></a> 에서 개인정보취급방침 전자적 표시 등록 후 발급된 개인정보취급방침 내용을 복사합니다.</li>
                            <li>2. 반드시 아래 예제로 등록된 내용은 지우고, <b>[쇼핑몰 메인 개인정보 취급방침]</b>에 복사된 내용을 붙여넣기 해주시기 바랍니다.</li>
                            <li>3. <b>[회원가입 / 비회원 구매시 개인정보 취급방침]</b>은 회원가입/비회원구매시 정보를 수집하는 목적에 대해서 동일하게 넣어주세요.<br>
				&nbsp;&nbsp;&nbsp;&nbsp;또한 기본사항 및 추가적으로 개인정보를 수집에 필요한 정보가 있다면 구체적으로 내용을 추가한 목적을 넣어주시기 바랍니다.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>
			<tr class='hide'>
				<td>
                <div class="table_style01">
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TR>
                        <th><span>개인정보관리 책임자</span></th>
                        <TD class="td_con1" width="600"><input type=text name=up_privercyname value="<?=$privercyname?>" size=15 maxlength=10 onKeyUp="chkFieldMaxLen(10)" class="input"></TD>
                    </TR>
                    <TR>
                        <th><span>책임자 E-mail</span></th>
                        <TD class="td_con1" width="600"><input type=text name=up_privercyemail value="<?=$privercyemail?>" size=35 maxlength=50 onKeyUp="chkFieldMaxLen(50)" class="input"></TD>
                    </TR>
                    </table>
                </div>
				</td>
			</tr>
			<tr class='hide'>
				<td style="padding-top:3pt; padding-bottom:3pt;">                    
                </p><p class="MsoNormal">본 방침은<span lang="EN-US"> : 2012</span>년<span lang="EN-US"> 00</span>월<span lang="EN-US"> 00</span>일부터 시행됩니다<span lang="EN-US">.<o:p></o:p></span></p>																																																				    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) <B>[NAME]</B>, <B>[EMAIL]</B>은 [상정기본정보]의 개인정보관리 책임자명과 E-mail이 자동 입력됩니다.</li>
                            <li>2) <B>[SHOP]</B>은 상점명이 자동 입력됩니다.</li>
                            <li>3) <B>[TEL]</B>은 [상점기본정보]의 고객상담 전화번호가 자동 입력됩니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			
			<tr>
				<td>
					<div class="tab_style1" data-ui="TabMenu">
						<div class="tab-menu clear">
							<a data-content="menu" class="active" title="선택됨">쇼핑몰 메인 개인정보 취급방침</a>
							<a data-content="menu">회원가입 / 비회원 구매시 개인정보 취급방침</a>
						</div>

						<!-- 쇼핑몰 메인 개인정보 취급방침 -->
						<div class="tab-content active" data-content="content">
							<div>
								<textarea name=up_privercy id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$privercy?>
<?php
	if (!$privercy) {
		include("privercy.txt");
	}
?>
								</textarea>
							</div>
						</div>

						<!-- 회원가입 / 비회원 구매시 개인정보 취급방침 -->
						<div class="tab-content" data-content="content">
							<div>
								<textarea name=up_privercy2 id=ir2 rows=15 wrap=off style="width:100%" class="textarea"><?=$privercy2?>
<?php
	if (!$privercy2) {
		include("privercy2.txt");
	}
?>
								</textarea>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<!-- <tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="10" colspan="2"></td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" colspan="2" height="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" colspan="2" align="center">쇼핑몰 메인 개인정보 취급방침</TD>
				</TR>
				<TR>
					<TD width="100%" colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_privercy id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$privercy?>
<?php
	if (!$privercy) {
		include("privercy.txt");
	}
?>
						</textarea></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr> -->

			<!-- <tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="10" colspan="2"></td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" colspan="2" height="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" colspan="2" align="center">회원가입 / 비회원 구매시 개인정보 취급방침</TD>
				</TR>
				<TR>
					<TD width="100%" colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_privercy2 id=ir2 rows=15 wrap=off style="width:100%" class="textarea"><?=$privercy2?>
			<?php
				if (!$privercy2) {
					include("privercy2.txt");
				}
			?>
						</textarea></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr> -->

			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center">
					<a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a>
					<a href="javascript:BasicTerms();"><span class="btn-basic">표준약관 적용</span></a>
				</td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>[COMPANY]는 회사명, [SHOP]은 쇼핑몰명이 자동 입력됩니다.</li>
							<li>[NAME], [EMAIL]은 [기본정보관리]의 개인정보관리 담당자명과 이메일이 자동 입력됩니다.</li>
							<li>[TEL]은 [기본정보관리]의 고객상담 전화번호가 자동 입력됩니다.</li>
							<li><b>쇼핑몰에 적용하시기 전, 쇼핑몰 운영사항을 확인하시고 내용 수정 후 반영하여 사용하시기 바랍니다.</b></li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
							<li>
								<dl>
									<dt>관련법률</dt>
									<dd> - 정보통신망 이용촉진 및 정보보호 등에 관한 법률 [제27조의2(개인정보취급방침의 공개)]항<br>
									- 정보통신망 이용촉진 및 정보보호 등에 관한 법률 시행규칙 [제3조의2 (개인정보취급방침의 공개 방법 등)]항</dd>
								</dl>
							</li>
						</ul>
						<!-- <dl>
							<dt><span>개인정보취급방침 및 전자적표시</span></dt>
							<dd>
								<span class="font_orange"><b>개인정보취급방침 및 전자적표시정보통신망 이용촉진 및 정보보호등에 관한 법률(이하'정보통신망법')에 따라 <br>
								웹사이트에서개인정보를 취급하는 경우 개인정보취급방침을 공개하고 전자적 표시를 하여야 합니다.</b></span><br>
								<br>
								<br>
								<span class="font_black">1. 정보통신망 이용촉진 및 정보보호 등에 관한 법률</span><br>
								<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange"><b>- 제27조의2(개인정보취급방침의 공개)</b></span><br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;① 정보통신서비스제공자등은 이용자의 개인정보를 취급하는 경우에는 개인정보취급방침을 정하여 이를 이용자가 언제든지 <br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;쉽게 확인할 수 있도록 정보통신부령이 정하는 방법에 따라 공개하여야 한다.[본조신설 2007.1.26]<br>
								<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange"><b>- 제67조 (과태료)</b></span><br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;② 다음 각 호의 어느 하나에 해당하는 자는 1천만원 이하의 과태료에 처한다.<개정 2007.1.26> 8의3. 제27조의2제1항<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(제58조의 규정에 따라 준용되는 경우를 포함한다)의 규정을 위반하여 개인정보취급방침을 공개하지 아니한 자<br>
								<br>
								<br>
								<span class="font_black">2. 정보통신망 이용촉진 및 정보보호 등에 관한 법률 시행규칙</span><br>
								<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange"><b>- 제3조의2 (개인정보취급방침의 공개 방법 등)</b></span><br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;① 법 제27조의2제1항에 따라 정보통신서비스제공자등은 개인정보의 수집 장소와 매체 등을 고려하여 다음 각 호 중 <br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;어느 하나 이상의 방법으로 개인정보취급방침을 공개하되, 그 명칭을 '개인정보취급방침'이라고 표시하여야 한다.<br>
								<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;③ 정보통신서비스제공자등이 제1항제1호에 따라 개인정보취급방침을 공개하는 경우에는 이용자가 인터넷을 통하여 <br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;개인정보취급방침의 주요 사항을 언제든지 쉽게 확인할 수 있도록 하기 위하여 정보통신부장관이 정하여 고시하는 방법에 <br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;따른 전자적 표시도 함께 하여야 한다.[본조신설 2007.7.27]<br><br>
							</dd>
						</dl> -->

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
<SCRIPT LANGUAGE="JavaScript">
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
<SCRIPT LANGUAGE="JavaScript">
	var oEditors2 = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors2,
		elPlaceHolder: "ir2",
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
