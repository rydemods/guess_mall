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
$up_agreement = addslashes($_POST["up_agreement"]);
if ($type == "up") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$flag = $row->cnt;
	pmysql_free_result($result);

	if ($flag) {
		//$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.'); }</script>";
		$onload = "<script>alert('정보 수정이 완료되었습니다.'); </script>";
		$sql = "UPDATE tbldesign SET agreement = '{$up_agreement}' ";
	} else {
		//$onload = "<script>window.onload=function(){ alert('정보 등록이 완료되었습니다.'); }</script>";
		$onload = "<script>alert('정보 등록이 완료되었습니다.');</script>";
		$sql = "INSERT INTO tbldesign(agreement) VALUES('{$up_agreement}')";
	}
	pmysql_query($sql,get_db_conn());
}

$sql = "SELECT agreement FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$flag = true;
	$agreement = ($row->agreement=="<P>&nbsp;</P>"?"":$row->agreement);
	$agreement = str_replace('\\','',$agreement);
}
pmysql_free_result($result);
if(ord($agreement)==0 && file_exists($Dir.AdminDir."agreement.txt")) {
	$agreement = file_get_contents($Dir.AdminDir."agreement.txt");
}


include("header.php"); 
echo $onload;
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
_editor_url = "htmlarea/";
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	form1.up_agreement.value=sHTML;
	
	form1.type.value="up";
	form1.submit();
}

function BasicTerms(){
	var str = '';
		str += '<p class="MsoNormal" align="center" style="text-align:center"><b><span lang="EN-US">[SHOP] </span>사이버 몰 회원 약관<span lang="EN-US"><o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">1</span>조<span lang="EN-US">(</span>목적<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">이 약관은 <span lang="EN-US">[COMPANY] </span>회사<span lang="EN-US">(</span>전자상거래';
		str += '사업자<span lang="EN-US">)</span>가 운영하는 <span lang="EN-US">[SHOP] </span>사이버 몰<span lang="EN-US">(</span>이하<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이라 한다<span lang="EN-US">)</span>에서 제공하는 인터넷 관련 서비스<span lang="EN-US">(</span>이하<span lang="EN-US"> “</span>서비스<span lang="EN-US">”</span>라 한다<span lang="EN-US">)</span>를 이용함에';
		str += '있어 사이버 몰과 이용자의 권리<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:';
		str += '&quot;MS Gothic&quot;">․</span>의무 및 책임사항을 규정함을 목적으로 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;※</span>「<span lang="EN-US">PC</span>통신<span lang="EN-US">, </span>무선 등을 이용하는 전자상거래에 대해서도 그 성질에 반하지';
		str += '않는 한 이 약관을 준용합니다<span lang="EN-US">.</span>」<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">2</span>조<span lang="EN-US">(</span>정의<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>이란 <span lang="EN-US">[COMPANY] </span>회사가 재화 또는 용역<span lang="EN-US">(</span>이하<span lang="EN-US"> “</span>재화 등<span lang="EN-US">”</span>이라 함<span lang="EN-US">)</span>을';
		str += '이용자에게 제공하기 위하여 컴퓨터 등 정보통신설비를 이용하여 재화 등을 거래할 수 있도록 설정한 가상의 영업장을 말하며<span lang="EN-US">, </span>아울러 사이버몰을 운영하는 사업자의 의미로도 사용합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>이용자<span lang="EN-US">”</span>란<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에 접속하여 이 약관에 따라<span lang="EN-US">';
		str += '“</span>몰<span lang="EN-US">”</span>이 제공하는 서비스를 받는 회원 및 비회원을 말합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ ‘</span>회원<span lang="EN-US">’</span>이라 함은<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에 회원등록을 한 자로서<span lang="EN-US">, </span>계속적으로<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 제공하는 서비스를 이용할 수 있는 자를 말합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ ‘</span>비회원<span lang="EN-US">’</span>이라 함은';
		str += '회원에 가입하지 않고<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 제공하는 서비스를 이용하는';
		str += '자를 말합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">3</span>조<span lang="EN-US"> (</span>약관 등의 명시와 설명 및 개정<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이 약관의';
		str += '내용과 상호 및 대표자 성명<span lang="EN-US">, </span>영업소 소재지 주소<span lang="EN-US">(</span>소비자의';
		str += '불만을 처리할 수 있는 곳의 주소를 포함<span lang="EN-US">), </span>전화번호<span style="font-family:';
		str += '&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>모사전송번호<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>전자우편주소<span lang="EN-US">, </span>사업자등록번호<span lang="EN-US">, </span>통신판매업 신고번호<span lang="EN-US">, </span>개인정보관리책임자 등을 이용자가 쉽게 알 수 있도록<span lang="EN-US"> 00 </span>사이버몰의';
		str += '초기 서비스화면<span lang="EN-US">(</span>전면<span lang="EN-US">)</span>에 게시합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>약관의 내용은 이용자가 연결화면을 통하여 볼 수 있도록 할';
		str += '수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰은 이용자가 약관에 동의하기에 앞서 약관에 정하여져 있는 내용';
		str += '중 청약철회<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>배송책임<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>환불조건 등과';
		str += '같은 중요한 내용을 이용자가 이해할 수 있도록 별도의 연결화면 또는 팝업화면 등을 제공하여 이용자의 확인을 구하여야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>은 「전자상거래';
		str += '등에서의 소비자보호에 관한 법률」<span lang="EN-US">, </span>「약관의 규제에 관한 법률」<span lang="EN-US">, </span>「전자문서';
		str += '및 전자거래기본법」<span lang="EN-US">, </span>「전자금융거래법」<span lang="EN-US">, </span>「전자서명법」<span lang="EN-US">, </span>「정보통신망 이용촉진 및 정보보호 등에 관한 법률」<span lang="EN-US">, </span>「방문판매';
		str += '등에 관한 법률」<span lang="EN-US">, </span>「소비자기본법」 등 관련 법을 위배하지 않는 범위에서 이 약관을 개정할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ “</span>몰<span lang="EN-US">”</span>이 약관을 개정할';
		str += '경우에는 적용일자 및 개정사유를 명시하여 현행약관과 함께 몰의 초기화면에 그 적용일자<span lang="EN-US"> 7</span>일 이전부터';
		str += '적용일자 전일까지 공지합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>이용자에게 불리하게';
		str += '약관내용을 변경하는 경우에는 최소한<span lang="EN-US"> 30</span>일 이상의 사전 유예기간을 두고 공지합니다<span lang="EN-US">.&nbsp; </span>이 경우<span lang="EN-US"> "</span>몰<span lang="EN-US">“</span>은 개정 전 내용과 개정 후 내용을 명확하게 비교하여';
		str += '이용자가 알기 쉽도록 표시합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑤ “</span>몰<span lang="EN-US">”</span>이 약관을 개정할';
		str += '경우에는 그 개정약관은 그 적용일자 이후에 체결되는 계약에만 적용되고 그 이전에 이미 체결된 계약에 대해서는 개정 전의 약관조항이 그대로 적용됩니다<span lang="EN-US">. </span>다만 이미 계약을 체결한 이용자가 개정약관 조항의 적용을 받기를 원하는 뜻을 제<span lang="EN-US">3</span>항에 의한 개정약관의 공지기간 내에<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에 송신하여<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 동의를';
		str += '받은 경우에는 개정약관 조항이 적용됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑥ </span>이 약관에서 정하지 아니한 사항과 이 약관의 해석에 관하여는 전자상거래';
		str += '등에서의 소비자보호에 관한 법률<span lang="EN-US">, </span>약관의 규제 등에 관한 법률<span lang="EN-US">, </span>공정거래위원회가';
		str += '정하는 전자상거래 등에서의 소비자 보호지침 및 관계법령 또는 상관례에 따릅니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">4</span>조<span lang="EN-US">(</span>서비스의 제공 및 변경<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 다음과 같은';
		str += '업무를 수행합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>재화 또는 용역에 대한 정보 제공 및 구매계약의 체결<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>구매계약이 체결된 재화 또는 용역의 배송<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>기타<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 정하는';
		str += '업무<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 재화 또는';
		str += '용역의 품절 또는 기술적 사양의 변경 등의 경우에는 장차 체결되는 계약에 의해 제공할 재화 또는 용역의 내용을 변경할 수 있습니다<span lang="EN-US">. </span>이 경우에는 변경된 재화 또는 용역의 내용 및 제공일자를 명시하여 현재의 재화 또는 용역의 내용을 게시한 곳에';
		str += '즉시 공지합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>이 제공하기로';
		str += '이용자와 계약을 체결한 서비스의 내용을 재화등의 품절 또는 기술적 사양의 변경 등의 사유로 변경할 경우에는 그 사유를 이용자에게 통지 가능한 주소로';
		str += '즉시 통지합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ </span>전항의 경우<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 이로 인하여 이용자가 입은 손해를 배상합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>이 고의 또는 과실이 없음을 입증하는 경우에는 그러하지 아니합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">5</span>조<span lang="EN-US">(</span>서비스의 중단<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 컴퓨터 등';
		str += '정보통신설비의 보수점검<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>교체';
		str += '및 고장<span lang="EN-US">, </span>통신의 두절 등의 사유가 발생한 경우에는 서비스의 제공을 일시적으로 중단할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 제<span lang="EN-US">1</span>항의 사유로 서비스의 제공이 일시적으로 중단됨으로 인하여 이용자 또는 제<span lang="EN-US">3</span>자가';
		str += '입은 손해에 대하여 배상합니다<span lang="EN-US">. </span>단<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>이 고의 또는 과실이 없음을 입증하는 경우에는 그러하지 아니합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ </span>사업종목의 전환<span lang="EN-US">, </span>사업의';
		str += '포기<span lang="EN-US">, </span>업체 간의 통합 등의 이유로 서비스를 제공할 수 없게 되는 경우에는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 제<span lang="EN-US">8</span>조에 정한';
		str += '방법으로 이용자에게 통지하고 당초<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에서 제시한 조건에';
		str += '따라 소비자에게 보상합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>이 보상기준 등을 고지하지 아니한 경우에는 이용자들의 마일리지 또는 적립금 등을<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에서 통용되는 통화가치에 상응하는 현물 또는 현금으로 이용자에게';
		str += '지급합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">6</span>조<span lang="EN-US">(</span>회원가입<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">① </span>이용자는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 정한 가입 양식에 따라 회원정보를 기입한 후 이 약관에 동의한다는 의사표시를 함으로서 회원가입을 신청합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 제<span lang="EN-US">1</span>항과 같이 회원으로 가입할 것을 신청한 이용자 중 다음 각 호에 해당하지 않는 한 회원으로 등록합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>가입신청자가 이 약관 제<span lang="EN-US">7</span>조제<span lang="EN-US">3</span>항에';
		str += '의하여 이전에 회원자격을 상실한 적이 있는 경우<span lang="EN-US">, </span>다만 제<span lang="EN-US">7</span>조제<span lang="EN-US">3</span>항에 의한 회원자격 상실 후<span lang="EN-US"> 3</span>년이 경과한 자로서<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 회원재가입 승낙을 얻은 경우에는 예외로 한다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>등록 내용에 허위<span lang="EN-US">, </span>기재누락<span lang="EN-US">, </span>오기가';
		str += '있는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>기타 회원으로 등록하는 것이<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 기술상 현저히 지장이 있다고 판단되는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ </span>회원가입계약의 성립 시기는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 승낙이 회원에게 도달한 시점으로 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ </span>회원은 회원가입 시 등록한 사항에 변경이 있는 경우<span lang="EN-US">, </span>상당한 기간 이내에<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에';
		str += '대하여 회원정보 수정 등의 방법으로 그 변경사항을 알려야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">7</span>조<span lang="EN-US">(</span>회원 탈퇴 및 자격 상실 등<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① </span>회원은<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에 언제든지 탈퇴를 요청할 수 있으며<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 즉시 회원탈퇴를 처리합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>회원이 다음 각 호의 사유에 해당하는 경우<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>은 회원자격을 제한 및 정지시킬 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>가입 신청 시에 허위 내용을 등록한 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. “</span>몰<span lang="EN-US">”</span>을 이용하여 구입한 재화 등의 대금<span lang="EN-US">, </span>기타<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이용에 관련하여';
		str += '회원이 부담하는 채무를 기일에 지급하지 않는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>다른 사람의<span lang="EN-US"> “</span>몰<span lang="EN-US">” </span>이용을';
		str += '방해하거나 그 정보를 도용하는 등 전자상거래 질서를 위협하는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">4. “</span>몰<span lang="EN-US">”</span>을 이용하여 법령 또는 이 약관이 금지하거나 공서양속에 반하는';
		str += '행위를 하는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>이 회원 자격을';
		str += '제한<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>정지';
		str += '시킨 후<span lang="EN-US">, </span>동일한 행위가<span lang="EN-US"> 2</span>회 이상 반복되거나<span lang="EN-US"> 30</span>일 이내에 그 사유가 시정되지 아니하는 경우<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 회원자격을 상실시킬 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ “</span>몰<span lang="EN-US">”</span>이 회원자격을';
		str += '상실시키는 경우에는 회원등록을 말소합니다<span lang="EN-US">. </span>이 경우 회원에게 이를 통지하고<span lang="EN-US">, </span>회원등록 말소 전에 최소한<span lang="EN-US"> 30</span>일 이상의 기간을 정하여 소명할 기회를';
		str += '부여합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">8</span>조<span lang="EN-US">(</span>회원에 대한 통지<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>이 회원에 대한';
		str += '통지를 하는 경우<span lang="EN-US">, </span>회원이<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>과 미리 약정하여 지정한 전자우편 주소로 할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 불특정다수';
		str += '회원에 대한 통지의 경우<span lang="EN-US"> 1</span>주일이상<span lang="EN-US"> “</span>몰<span lang="EN-US">” </span>게시판에 게시함으로서 개별 통지에 갈음할 수 있습니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>회원 본인의 거래와 관련하여 중대한 영향을 미치는 사항에 대하여는 개별통지를 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">9</span>조<span lang="EN-US">(</span>구매신청 및 개인정보 제공 동의 등<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>이용자는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>상에서 다음 또는 이와 유사한 방법에 의하여 구매를 신청하며<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>은 이용자가 구매신청을 함에 있어서 다음의 각 내용을 알기';
		str += '쉽게 제공하여야 합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>재화 등의 검색 및 선택<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>받는 사람의 성명<span lang="EN-US">, </span>주소<span lang="EN-US">, </span>전화번호<span lang="EN-US">, </span>전자우편주소<span lang="EN-US">(</span>또는 이동전화번호<span lang="EN-US">) </span>등의';
		str += '입력<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>약관내용<span lang="EN-US">, </span>청약철회권이 제한되는 서비스<span lang="EN-US">, </span>배송료<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:';
		str += '&quot;MS Gothic&quot;">․</span>설치비 등의 비용부담과 관련한 내용에 대한 확인<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">4. </span>이 약관에 동의하고 위<span lang="EN-US"> 3.</span>호의 사항을 확인하거나 거부하는 표시<span lang="EN-US"> (</span>예<span lang="EN-US">, </span>마우스 클릭<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">5. </span>재화등의 구매신청 및 이에 관한 확인 또는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 확인에 대한 동의<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">6. </span>결제방법의 선택<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>이 제<span lang="EN-US">3</span>자에게 구매자 개인정보를 제공할 필요가 있는 경우<span lang="EN-US"> 1) </span>개인정보를';
		str += '제공받는 자<span lang="EN-US">, 2)</span>개인정보를 제공받는 자의 개인정보 이용목적<span lang="EN-US">, 3) </span>제공하는';
		str += '개인정보의 항목<span lang="EN-US">, 4) </span>개인정보를 제공받는 자의 개인정보 보유 및 이용기간을 구매자에게 알리고 동의를';
		str += '받아야 합니다<span lang="EN-US">. (</span>동의를 받은 사항이 변경되는 경우에도 같습니다<span lang="EN-US">.)<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>이 제<span lang="EN-US">3</span>자에게 구매자의 개인정보를 취급할 수 있도록 업무를 위탁하는 경우에는<span lang="EN-US"> 1) </span>개인정보';
		str += '취급위탁을 받는 자<span lang="EN-US">, 2) </span>개인정보 취급위탁을 하는 업무의 내용을 구매자에게 알리고 동의를 받아야 합니다<span lang="EN-US">. (</span>동의를 받은 사항이 변경되는 경우에도 같습니다<span lang="EN-US">.) </span>다만<span lang="EN-US">, </span>서비스제공에 관한 계약이행을 위해 필요하고 구매자의 편의증진과 관련된 경우에는 「정보통신망 이용촉진 및 정보보호';
		str += '등에 관한 법률」에서 정하고 있는 방법으로 개인정보 취급방침을 통해 알림으로써 고지절차와 동의절차를 거치지 않아도 됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">10</span>조<span lang="EN-US"> (</span>계약의 성립<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">①&nbsp; “</span>몰<span lang="EN-US">”</span>은 제<span lang="EN-US">9</span>조와 같은 구매신청에 대하여 다음 각 호에 해당하면 승낙하지';
		str += '않을 수 있습니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>미성년자와 계약을 체결하는 경우에는';
		str += '법정대리인의 동의를 얻지 못하면 미성년자 본인 또는 법정대리인이 계약을 취소할 수 있다는 내용을 고지하여야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>신청 내용에 허위<span lang="EN-US">, </span>기재누락<span lang="EN-US">, </span>오기가';
		str += '있는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>미성년자가 담배<span lang="EN-US">, </span>주류 등 청소년보호법에서 금지하는 재화 및 용역을';
		str += '구매하는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>기타 구매신청에 승낙하는 것이<span lang="EN-US"> “</span>몰<span lang="EN-US">” </span>기술상 현저히 지장이 있다고 판단하는 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>의 승낙이 제<span lang="EN-US">12</span>조제<span lang="EN-US">1</span>항의 수신확인통지형태로 이용자에게 도달한 시점에 계약이 성립한';
		str += '것으로 봅니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>의 승낙의 의사표시에는';
		str += '이용자의 구매 신청에 대한 확인 및 판매가능 여부<span lang="EN-US">, </span>구매신청의 정정 취소 등에 관한 정보 등을 포함하여야';
		str += '합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">11</span>조<span lang="EN-US">(</span>지급방법<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">“</span>몰<span lang="EN-US">”</span>에서 구매한 재화';
		str += '또는 용역에 대한 대금지급방법은 다음 각 호의 방법 중 가용한 방법으로 할 수 있습니다<span lang="EN-US">. </span>단<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>은 이용자의 지급방법에 대하여 재화 등의 대금에 어떠한 명목의';
		str += '수수료도 추가하여 징수할 수 없습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>폰뱅킹<span lang="EN-US">, </span>인터넷뱅킹<span lang="EN-US">, </span>메일';
		str += '뱅킹 등의 각종 계좌이체 <span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>선불카드<span lang="EN-US">, </span>직불카드<span lang="EN-US">, </span>신용카드';
		str += '등의 각종 카드 결제<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>온라인무통장입금<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">4. </span>전자화폐에 의한 결제<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">5. </span>수령 시 대금지급<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">6. </span>마일리지 등<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이';
		str += '지급한 포인트에 의한 결제<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">7. “</span>몰<span lang="EN-US">”</span>과 계약을 맺었거나<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 인정한 상품권에 의한 결제<span lang="EN-US">&nbsp; <o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">8. </span>기타 전자적 지급 방법에 의한 대금 지급 등<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">12</span>조<span lang="EN-US">(</span>수신확인통지</b><b><span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>구매신청 변경';
		str += '및 취소<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이용자의 구매신청이';
		str += '있는 경우 이용자에게 수신확인통지를 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>수신확인통지를 받은 이용자는 의사표시의 불일치 등이 있는 경우에는';
		str += '수신확인통지를 받은 후 즉시 구매신청 변경 및 취소를 요청할 수 있고<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 배송 전에 이용자의 요청이 있는 경우에는 지체 없이 그 요청에 따라 처리하여야 합니다<span lang="EN-US">. </span>다만 이미 대금을 지불한 경우에는 제<span lang="EN-US">15</span>조의 청약철회 등에 관한';
		str += '규정에 따릅니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">13</span>조<span lang="EN-US">(</span>재화 등의 공급<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이용자와 재화';
		str += '등의 공급시기에 관하여 별도의 약정이 없는 이상<span lang="EN-US">, </span>이용자가 청약을 한 날부터<span lang="EN-US"> 7</span>일 이내에 재화 등을 배송할 수 있도록 주문제작<span lang="EN-US">, </span>포장 등 기타의';
		str += '필요한 조치를 취합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, “</span>몰<span lang="EN-US">”</span>이 이미 재화 등의 대금의 전부 또는 일부를 받은 경우에는 대금의 전부 또는 일부를 받은 날부터<span lang="EN-US"> 3</span>영업일 이내에 조치를 취합니다<span lang="EN-US">.&nbsp; </span>이때<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 이용자가 재화 등의 공급 절차 및 진행 사항을 확인할 수 있도록 적절한 조치를 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 이용자가 구매한';
		str += '재화에 대해 배송수단<span lang="EN-US">, </span>수단별 배송비용 부담자<span lang="EN-US">, </span>수단별 배송기간';
		str += '등을 명시합니다<span lang="EN-US">. </span>만약<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 약정 배송기간을 초과한 경우에는 그로 인한 이용자의 손해를 배상하여야 합니다<span lang="EN-US">. </span>다만<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 고의<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>과실이 없음을';
		str += '입증한 경우에는 그러하지 아니합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">14</span>조<span lang="EN-US">(</span>환급<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">“</span>몰<span lang="EN-US">”</span>은 이용자가 구매신청한';
		str += '재화 등이 품절 등의 사유로 인도 또는 제공을 할 수 없을 때에는 지체 없이 그 사유를 이용자에게 통지하고 사전에 재화 등의 대금을 받은 경우에는';
		str += '대금을 받은 날부터<span lang="EN-US"> 3</span>영업일 이내에 환급하거나 환급에 필요한 조치를 취합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">15</span>조<span lang="EN-US">(</span>청약철회 등<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>과 재화등의 구매에';
		str += '관한 계약을 체결한 이용자는 「전자상거래 등에서의 소비자보호에 관한 법률」 제<span lang="EN-US">13</span>조 제<span lang="EN-US">2</span>항에 따른 계약내용에 관한 서면을 받은 날<span lang="EN-US">(</span>그 서면을 받은 때보다';
		str += '재화 등의 공급이 늦게 이루어진 경우에는 재화 등을 공급받거나 재화 등의 공급이 시작된 날을 말합니다<span lang="EN-US">)</span>부터<span lang="EN-US"> 7</span>일 이내에는 청약의 철회를 할 수 있습니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>청약철회에 관하여 「전자상거래 등에서의 소비자보호에 관한 법률」에 달리 정함이 있는 경우에는 동 법 규정에';
		str += '따릅니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>이용자는 재화 등을 배송 받은 경우 다음 각 호의<span lang="EN-US"> 1</span>에 해당하는 경우에는 반품 및 교환을 할 수 없습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>이용자에게 책임 있는 사유로 재화 등이 멸실 또는 훼손된 경우<span lang="EN-US">(</span>다만<span lang="EN-US">, </span>재화 등의 내용을 확인하기 위하여 포장 등을 훼손한 경우에는 청약철회를 할 수 있습니다<span lang="EN-US">)<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>이용자의 사용 또는 일부 소비에 의하여 재화 등의 가치가 현저히 감소한 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. </span>시간의 경과에 의하여 재판매가 곤란할 정도로 재화등의 가치가 현저히 감소한 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">4. </span>같은 성능을 지닌 재화 등으로 복제가 가능한 경우 그 원본인 재화 등의 포장을 훼손한 경우<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ </span>제<span lang="EN-US">2</span>항제<span lang="EN-US">2</span>호 내지 제<span lang="EN-US">4</span>호의 경우에<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 사전에 청약철회 등이 제한되는 사실을 소비자가 쉽게 알 수 있는 곳에 명기하거나 시용상품을 제공하는 등의';
		str += '조치를 하지 않았다면 이용자의 청약철회 등이 제한되지 않습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ </span>이용자는 제<span lang="EN-US">1</span>항 및';
		str += '제<span lang="EN-US">2</span>항의 규정에 불구하고 재화 등의 내용이 표시<span lang="EN-US">·</span>광고 내용과';
		str += '다르거나 계약내용과 다르게 이행된 때에는 당해 재화 등을 공급받은 날부터<span lang="EN-US"> 3</span>월 이내<span lang="EN-US">, </span>그 사실을 안 날 또는 알 수 있었던 날부터<span lang="EN-US"> 30</span>일 이내에 청약철회';
		str += '등을 할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">16</span>조<span lang="EN-US">(</span>청약철회 등의 효과<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이용자로부터';
		str += '재화 등을 반환받은 경우<span lang="EN-US"> 3</span>영업일 이내에 이미 지급받은 재화 등의 대금을 환급합니다<span lang="EN-US">. </span>이 경우<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 이용자에게';
		str += '재화 등의 환급을 지연한 때에는 그 지연기간에 대하여 「전자상거래 등에서의 소비자보호에 관한 법률 시행령」제<span lang="EN-US">21</span>조의<span lang="EN-US">2</span>에서 정하는 지연이자율을 곱하여 산정한 지연이자를 지급합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 위 대금을';
		str += '환급함에 있어서 이용자가 신용카드 또는 전자화폐 등의 결제수단으로 재화 등의 대금을 지급한 때에는 지체 없이 당해 결제수단을 제공한 사업자로 하여금';
		str += '재화 등의 대금의 청구를 정지 또는 취소하도록 요청합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ </span>청약철회 등의 경우 공급받은 재화 등의 반환에 필요한 비용은 이용자가';
		str += '부담합니다<span lang="EN-US">. “</span>몰<span lang="EN-US">”</span>은 이용자에게 청약철회 등을 이유로 위약금';
		str += '또는 손해배상을 청구하지 않습니다<span lang="EN-US">. </span>다만 재화 등의 내용이 표시<span lang="EN-US">·</span>광고';
		str += '내용과 다르거나 계약내용과 다르게 이행되어 청약철회 등을 하는 경우 재화 등의 반환에 필요한 비용은<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 부담합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;④ </span>이용자가';
		str += '재화 등을 제공받을 때 발송비를 부담한 경우에<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 청약철회';
		str += '시 그 비용을<span lang="EN-US">&nbsp; </span>누가 부담하는지를';
		str += '이용자가 알기 쉽도록 명확하게 표시합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">17</span>조<span lang="EN-US">(</span>개인정보보호<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이용자의 개인정보';
		str += '수집시 서비스제공을 위하여 필요한 범위에서 최소한의 개인정보를 수집합니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 회원가입시';
		str += '구매계약이행에 필요한 정보를 미리 수집하지 않습니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>관련';
		str += '법령상 의무이행을 위하여 구매계약 이전에 본인확인이 필요한 경우로서 최소한의 특정 개인정보를 수집하는 경우에는 그러하지 아니합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>은 이용자의 개인정보를';
		str += '수집<span lang="EN-US">·</span>이용하는 때에는 당해 이용자에게 그 목적을 고지하고 동의를 받습니다<span lang="EN-US">. <o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ “</span>몰<span lang="EN-US">”</span>은 수집된 개인정보를';
		str += '목적외의 용도로 이용할 수 없으며<span lang="EN-US">, </span>새로운 이용목적이 발생한 경우 또는 제<span lang="EN-US">3</span>자에게 제공하는 경우에는 이용<span lang="EN-US">·</span>제공단계에서 당해 이용자에게 그 목적을';
		str += '고지하고 동의를 받습니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>관련 법령에 달리 정함이';
		str += '있는 경우에는 예외로 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑤ “</span>몰<span lang="EN-US">”</span>이 제<span lang="EN-US">2</span>항과 제<span lang="EN-US">3</span>항에 의해 이용자의 동의를 받아야 하는 경우에는 개인정보관리';
		str += '책임자의 신원<span lang="EN-US">(</span>소속<span lang="EN-US">, </span>성명 및 전화번호<span lang="EN-US">, </span>기타 연락처<span lang="EN-US">), </span>정보의 수집목적 및 이용목적<span lang="EN-US">, </span>제<span lang="EN-US">3</span>자에 대한 정보제공 관련사항<span lang="EN-US">(</span>제공받은자<span lang="EN-US">, </span>제공목적 및 제공할 정보의 내용<span lang="EN-US">) </span>등 「정보통신망 이용촉진 및 정보보호';
		str += '등에 관한 법률」 제<span lang="EN-US">22</span>조제<span lang="EN-US">2</span>항이 규정한 사항을 미리 명시하거나';
		str += '고지해야 하며 이용자는 언제든지 이 동의를 철회할 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑥ </span>이용자는 언제든지<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 가지고 있는 자신의 개인정보에 대해 열람 및 오류정정을 요구할 수 있으며<span lang="EN-US">';
		str += '“</span>몰<span lang="EN-US">”</span>은 이에 대해 지체 없이 필요한 조치를 취할 의무를 집니다<span lang="EN-US">. </span>이용자가 오류의 정정을 요구한 경우에는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>은 그 오류를 정정할 때까지 당해 개인정보를 이용하지 않습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑦ “</span>몰<span lang="EN-US">”</span>은 개인정보 보호를';
		str += '위하여 이용자의 개인정보를 취급하는 자를<span lang="EN-US">&nbsp; </span>최소한으로';
		str += '제한하여야 하며 신용카드<span lang="EN-US">, </span>은행계좌 등을 포함한 이용자의 개인정보의 분실<span lang="EN-US">, </span>도난<span lang="EN-US">, </span>유출<span lang="EN-US">, </span>동의 없는';
		str += '제<span lang="EN-US">3</span>자 제공<span lang="EN-US">, </span>변조 등으로 인한 이용자의 손해에 대하여 모든';
		str += '책임을 집니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑧ “</span>몰<span lang="EN-US">” </span>또는 그로부터';
		str += '개인정보를 제공받은 제<span lang="EN-US">3</span>자는 개인정보의 수집목적 또는 제공받은 목적을 달성한 때에는 당해 개인정보를';
		str += '지체 없이 파기합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">⑨ “</span>몰<span lang="EN-US">”</span>은 개인정보의';
		str += '수집<span lang="EN-US">·</span>이용<span lang="EN-US">·</span>제공에 관한 동의 란을 미리 선택한 것으로 설정해두지';
		str += '않습니다<span lang="EN-US">. </span>또한 개인정보의 수집<span lang="EN-US">·</span>이용<span lang="EN-US">·</span>제공에 관한 이용자의 동의거절시 제한되는 서비스를 구체적으로 명시하고<span lang="EN-US">, </span>필수수집항목이';
		str += '아닌 개인정보의 수집<span lang="EN-US">·</span>이용<span lang="EN-US">·</span>제공에 관한 이용자의 동의 거절을';
		str += '이유로 회원가입 등 서비스 제공을 제한하거나 거절하지 않습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">18</span>조<span lang="EN-US">(“</span>몰<span lang="EN-US">“</span>의 의무<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 법령과 이';
		str += '약관이 금지하거나 공서양속에 반하는 행위를 하지 않으며 이 약관이 정하는 바에 따라 지속적이고<span lang="EN-US">, </span>안정적으로';
		str += '재화<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>용역을';
		str += '제공하는데 최선을 다하여야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 이용자가 안전하게';
		str += '인터넷 서비스를 이용할 수 있도록 이용자의 개인정보<span lang="EN-US">(</span>신용정보 포함<span lang="EN-US">)</span>보호를';
		str += '위한 보안 시스템을 갖추어야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>이 상품이나 용역에';
		str += '대하여 「표시<span style="font-family:&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>광고의';
		str += '공정화에 관한 법률」 제<span lang="EN-US">3</span>조 소정의 부당한 표시<span style="font-family:';
		str += '&quot;MS Gothic&quot;;mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>광고행위를 함으로써 이용자가 손해를 입은 때에는';
		str += '이를 배상할 책임을 집니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">④ “</span>몰<span lang="EN-US">”</span>은 이용자가 원하지';
		str += '않는 영리목적의 광고성 전자우편을 발송하지 않습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">19</span>조<span lang="EN-US">(</span>회원의<span lang="EN-US"> ID </span>및 비밀번호에 대한 의무<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① </span>제<span lang="EN-US">17</span>조의 경우를 제외한<span lang="EN-US"> ID</span>와 비밀번호에 관한 관리책임은 회원에게 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>회원은 자신의<span lang="EN-US"> ID </span>및';
		str += '비밀번호를 제<span lang="EN-US">3</span>자에게 이용하게 해서는 안됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ </span>회원이 자신의<span lang="EN-US"> ID </span>및';
		str += '비밀번호를 도난당하거나 제<span lang="EN-US">3</span>자가 사용하고 있음을 인지한 경우에는 바로<span lang="EN-US">';
		str += '“</span>몰<span lang="EN-US">”</span>에 통보하고<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 안내가 있는 경우에는 그에 따라야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">20</span>조<span lang="EN-US">(</span>이용자의 의무<span lang="EN-US">) <o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">이용자는 다음 행위를 하여서는 안 됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">1. </span>신청 또는 변경시 허위 내용의 등록<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">2. </span>타인의 정보 도용<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">3. “</span>몰<span lang="EN-US">”</span>에 게시된 정보의 변경<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">4. “</span>몰<span lang="EN-US">”</span>이 정한 정보 이외의 정보<span lang="EN-US">(</span>컴퓨터';
		str += '프로그램 등<span lang="EN-US">) </span>등의 송신 또는 게시<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">5. “</span>몰<span lang="EN-US">” </span>기타 제<span lang="EN-US">3</span>자의';
		str += '저작권 등 지적재산권에 대한 침해<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">6. “</span>몰<span lang="EN-US">” </span>기타 제<span lang="EN-US">3</span>자의';
		str += '명예를 손상시키거나 업무를 방해하는 행위<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal" style="text-indent:10.0pt;mso-char-indent-count:1.0"><span lang="EN-US">7. </span>외설 또는 폭력적인 메시지<span lang="EN-US">, </span>화상<span lang="EN-US">, </span>음성<span lang="EN-US">, </span>기타 공서양속에 반하는 정보를 몰에 공개 또는 게시하는 행위<span lang="EN-US"><o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">21</span>조<span lang="EN-US">(</span>연결<span lang="EN-US">“</span>몰<span lang="EN-US">”</span>과 피연결<span lang="EN-US">“</span>몰<span lang="EN-US">” </span>간의 관계<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① </span>상위<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>과 하위<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>이 하이퍼링크<span lang="EN-US">(</span>예<span lang="EN-US">: </span>하이퍼링크의 대상에는 문자<span lang="EN-US">, </span>그림';
		str += '및 동화상 등이 포함됨<span lang="EN-US">)</span>방식 등으로 연결된 경우<span lang="EN-US">, </span>전자를';
		str += '연결<span lang="EN-US"> “</span>몰<span lang="EN-US">”(</span>웹 사이트<span lang="EN-US">)</span>이라고';
		str += '하고 후자를 피연결<span lang="EN-US"> “</span>몰<span lang="EN-US">”(</span>웹사이트<span lang="EN-US">)</span>이라고 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>연결<span lang="EN-US">“</span>몰<span lang="EN-US">”</span>은 피연결<span lang="EN-US">“</span>몰<span lang="EN-US">”</span>이 독자적으로';
		str += '제공하는 재화 등에 의하여 이용자와 행하는 거래에 대해서 보증 책임을 지지 않는다는 뜻을 연결<span lang="EN-US">“</span>몰<span lang="EN-US">”</span>의 초기화면 또는 연결되는 시점의 팝업화면으로 명시한 경우에는 그 거래에 대한 보증 책임을 지지 않습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">22</span>조<span lang="EN-US">(</span>저작권의 귀속 및 이용제한<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">“</span>이 작성한 저작물에';
		str += '대한 저작권 기타 지적재산권은<span lang="EN-US"> ”</span>몰<span lang="EN-US">“</span>에 귀속합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② </span>이용자는<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>을 이용함으로써 얻은 정보 중<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>에게';
		str += '지적재산권이 귀속된 정보를<span lang="EN-US"> “</span>몰<span lang="EN-US">”</span>의 사전 승낙 없이 복제<span lang="EN-US">, </span>송신<span lang="EN-US">, </span>출판<span lang="EN-US">, </span>배포<span lang="EN-US">, </span>방송 기타 방법에 의하여 영리목적으로 이용하거나 제<span lang="EN-US">3</span>자에게 이용하게';
		str += '하여서는 안됩니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>은 약정에 따라';
		str += '이용자에게 귀속된 저작권을 사용하는 경우 당해 이용자에게 통보하여야 합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">23</span>조<span lang="EN-US">(</span>분쟁해결<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>은 이용자가 제기하는';
		str += '정당한 의견이나 불만을 반영하고 그 피해를 보상처리하기 위하여 피해보상처리기구를 설치<span style="font-family:&quot;MS Gothic&quot;;';
		str += 'mso-bidi-font-family:&quot;MS Gothic&quot;">․</span>운영합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>은 이용자로부터';
		str += '제출되는 불만사항 및 의견은 우선적으로 그 사항을 처리합니다<span lang="EN-US">. </span>다만<span lang="EN-US">,';
		str += '</span>신속한 처리가 곤란한 경우에는 이용자에게 그 사유와 처리일정을 즉시 통보해 드립니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">③ “</span>몰<span lang="EN-US">”</span>과 이용자 간에';
		str += '발생한 전자상거래 분쟁과 관련하여 이용자의 피해구제신청이 있는 경우에는 공정거래위원회 또는 시<span lang="EN-US">·</span>도지사가';
		str += '의뢰하는 분쟁조정기관의 조정에 따를 수 있습니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><b>제<span lang="EN-US">24</span>조<span lang="EN-US">(</span>재판권 및 준거법<span lang="EN-US">)<o:p></o:p></span></b></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal"><span lang="EN-US">① “</span>몰<span lang="EN-US">”</span>과 이용자 간에';
		str += '발생한 전자상거래 분쟁에 관한 소송은 제소 당시의 이용자의 주소에 의하고<span lang="EN-US">, </span>주소가 없는 경우에는 거소를';
		str += '관할하는 지방법원의 전속관할로 합니다<span lang="EN-US">. </span>다만<span lang="EN-US">, </span>제소 당시';
		str += '이용자의 주소 또는 거소가 분명하지 않거나 외국 거주자의 경우에는 민사소송법상의 관할법원에 제기합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">② “</span>몰<span lang="EN-US">”</span>과 이용자 간에';
		str += '제기된 전자상거래 소송에는 한국법을 적용합니다<span lang="EN-US">.<o:p></o:p></span></p><p class="MsoNormal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal">부칙<span lang="EN-US"><o:p></o:p></span></p><p>';
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
		str += '</p><p class="MsoNormal"><span lang="EN-US">1. </span>이 약관은<span lang="EN-US"> 2017</span>년';
		str += '<span lang="EN-US">1</span>월 <span lang="EN-US">1</span>일부터 적용됩니다<span lang="EN-US">.<o:p></o:p></span></p>';
	oEditors.getById["ir1"].setIR(str);
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 기본정보 설정 &gt;<span>쇼핑몰 이용약관</span></p></div></div>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">쇼핑몰 이용약관</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 이용약관 <span>쇼핑몰 이용약관을 설정합니다.</span></div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><textarea name=up_agreement id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$agreement?></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li><B>[COMPANY]</B>, <B>[SHOP]</B>은 회사명과 상점명이 자동 입력됩니다.</li>
                            <li>2) 공정거래위원회 표준약관 준수를 권합니다.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center">
					<a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a>
					<a href="javascript:BasicTerms();"><span class="btn-basic">표준약관 적용</span></a>
				</td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>[COMPANY]는 회사명, [SHOP]은 쇼핑몰명이 자동 입력됩니다.</li>
							<li>공정거래위원회 표준약관을 수정 없이 그대로 사용하시는 것을 권합니다.</li>
							<li>표준약관은 청약철회, 환불약관에 대해서 구체적으로 명시되어 있습니다.</li>
							<li>공정거래위원회 약관을 사용하지 않거나 수정한 경우 공정거래위원회의 로고를 표시할 수 없으며, 이를 위반할 경우 공정위로부터 제재를 받을 수 있습니다.</li>
							<li><b>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</b></li>
						</ul>
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

<?php 
include("copyright.php");
