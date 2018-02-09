<?
if(substr(getenv("SCRIPT_NAME"),-12)=="/lib.inc.php") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

class _MShopInfo {
	var $id				= "";
	var $name			= "";
	var $email			= "";
	var $authkey		= "";
	var $nickname		= "";

	var $memlevel		= "";
	var $wsmember		= "";

	var $counterid		= "";
	var $counterauthkey	= "";

	var $shopurl		= "";
	var $refurl			= "";
	var $authidkey		= "";
	var $memid			= "";
	var $memgroup		= "";
	var $memname		= "";
	var $mememail		= "";
	var $memreserve		= 0;
	var $boardadmin		= "";	//array serialize data

	var $tempkey		= "";	//장바구니 인증키
	var $tempkeySelect		= "";	//장바구니 인증키2
	var $gifttempkey	= "";	//사은품 관련 키
	var $oldtempkey		= "";	//결제창 띄울경우 기존 장바구니 인증키
	var $okpayment		= "";	//결제시 새로고침 방지 쿠키
	var $staff_type		= "";		# 스태프 유무
	var $staff_yn		= "";		# 임직원 유무
	var $cooper_yn		= "";		# 협력업체 유무
	var $staffcardno		= "";		# ERP 임직원 STAFFCARDNO
	var $checksns		= "";		# sns 구분
	var $checksnslogin		= "";		# sns 로그인 구분
	var $checksnsaccess		= "";		# sns 접속타입 (PC, MOBILE) 구분
	var $checksnschurl		= "";		# sns 모바일에서 로그인시 이동 URL

	var $searchkey		= "";	//검색인증 구분키

	// 저장 정보 추가 (2015.10.29 - 김재수)
	var $referrerurl		= "";	// 이전URL 정보
	var $affiliatetype		= "";	// 제휴업체 구분
	var $affiliateno		= "";	// 제휴업체 번호
	var $affiliatename	= "";	// 제휴업체 이름
	var $affiliateimg		= "";	// 제휴업체 이미지

	function _MShopInfo($_sinfo) {
		if ($_sinfo) {
			$savedata=unserialize(decrypt_md5($_sinfo));

			$this->id			= $savedata["id"];
			$this->name			= $savedata["name"];
			$this->email		= $savedata["email"];
			$this->authkey		= $savedata["authkey"];
			$this->nickname		= $savedata["nickname"];

			$this->shopurl		= $savedata["shopurl"];
			$this->shopurl2		= $savedata["shopurl2"];
			$this->refurl		= $savedata["refurl"];
			$this->authidkey	= $savedata["authidkey"];
			$this->memid		= $savedata["memid"];
			$this->memgroup		= $savedata["memgroup"];
			$this->memname		= $savedata["memname"];
			$this->memreserve	= $savedata["memreserve"];
			$this->mememail		= $savedata["mememail"];
			$this->boardadmin	= $savedata["boardadmin"];
			$this->gifttempkey	= $savedata["gifttempkey"];
			$this->oldtempkey	= $savedata["oldtempkey"];
			$this->okpayment	= $savedata["okpayment"];
			$this->memlevel		= $savedata["memlevel"];
			$this->wsmember		= $savedata["wsmember"];
			$this->staff_type	= $savedata["staff_type"];
			$this->staff_yn	= $savedata["staff_yn"];
			$this->cooper_yn	= $savedata["cooper_yn"];
			$this->staffcardno	= $savedata["staffcardno"];
			$this->checksns	= $savedata["checksns"];
			$this->checksnslogin	= $savedata["checksnslogin"];
			$this->checksnsaccess	= $savedata["checksnsaccess"];
			$this->checksnschurl	= $savedata["checksnschurl"];

			// 저장 정보 추가 (2015.10.29 - 김재수)
			$this->referrerurl		= $savedata["referrerurl"];
			$this->affiliatetype	= $savedata["affiliatetype"];
			$this->affiliateno		= $savedata["affiliateno"];
			$this->affiliatename	= $savedata["affiliatename"];
			$this->affiliateimg	= $savedata["affiliateimg"];
		}
	}

	function Save() {
		$savedata["id"]			= $this->getId();
		$savedata["name"]		= $this->getName();
		$savedata["email"]		= $this->getEmail();
		$savedata["authkey"]	= $this->getAuthkey();
		$savedata["nickname"]	= $this->getNickName();
		$savedata["shopurl"]	= $this->getShopurl();
		$savedata["shopurl2"]	= $this->getShopurl2();
		$savedata["refurl"]		= $this->getRefurl();
		$savedata["authidkey"]	= $this->getAuthidkey();
		$savedata["memid"]		= $this->getMemid();
		$savedata["memgroup"]	= $this->getMemgroup();
		$savedata["memname"]	= $this->getMemname();
		$savedata["memreserve"]	= $this->getMemreserve();
		$savedata["mememail"]	= $this->getMememail();
		$savedata["boardadmin"]	= $this->getBoardadmin();
		$savedata["gifttempkey"]= $this->getGifttempkey();
		$savedata["oldtempkey"]	= $this->getOldtempkey();
		$savedata["okpayment"]	= $this->getOkpayment();

		$savedata["memlevel"]	= $this->getMemlevel();
		$savedata["wsmember"]	= $this->getWsmember();
		$savedata["staff_type"]	= $this->getStaffType();
		$savedata["staff_yn"]	= $this->getStaffYn();
		$savedata["cooper_yn"]	= $this->getCooperYn();
		$savedata["staffcardno"]	= $this->getStaffCardNo();
		$savedata["checksns"]	= $this->getCheckSns();
		$savedata["checksnslogin"]	= $this->getCheckSnsLogin();
		$savedata["checksnsaccess"]	= $this->getCheckSnsAccess();
		$savedata["checksnschurl"]	= $this->getCheckSnsChurl();

		// 저장 정보 추가 (2015.10.29 - 김재수)
		$savedata["referrerurl"]		= $this->getReferrerUrl();
		$savedata["affiliatetype"]		= $this->getAffiliateType();
		$savedata["affiliateno"]		= $this->getAffiliateNo();
		$savedata["affiliatename"]	= $this->getAffiliateName();
		$savedata["affiliateimg"]		= $this->getAffiliateImg();
		$_sinfo = encrypt_md5(serialize($savedata));
		setcookie("_sinfo", $_sinfo, 0, "/".RootPath, getCookieDomain());
	}

	function SetMemNULL() {
		$this->setAuthidkey("");
		$this->setMemid("");
		$this->setMemgroup("");
		$this->setMemname("");
		$this->setMemreserve("");
		$this->setMememail("");
		$this->setMemlevel("");
		$this->setStaffType("");
		$this->setStaffYn("");
		$this->setCooperYn("");
		$this->setStaffCardNo("");
		$this->setCheckSns("");
		$this->setCheckSnsLogin("");
		$this->setCheckSnsAccess("");
		$this->setCheckSnsChurl("");

		// 저장 정보 추가 (2015.11.02 - 김재수)
		$this->setReferrerUrl("");
		$this->setAffiliateType("1");
		$this->setAffiliateNo("");
		$this->setAffiliateName("");
		$this->setAffiliateImg("");
	}

	function setId($id)					{$this->id = $id;}
	function setName($name)				{$this->name = $name;}
	function setEmail($email)			{$this->email = $email;}
	function setNickName($nickname)		{$this->nickname = $nickname;}
	function setAuthkey($authkey)		{$this->authkey = $authkey;}
	function setShopurl($shopurl)		{$this->shopurl = $shopurl;}
	function setShopurl2($shopurl2)		{$this->shopurl2 = $shopurl2;}
	function setRefurl($refurl)			{$this->refurl = $refurl;}
	function setAuthidkey($authidkey)	{$this->authidkey = $authidkey;}
	function setMemid($memid)			{$this->memid = $memid;}
	function setMemgroup($memgroup)		{$this->memgroup = $memgroup;}
	function setMemname($memname)		{$this->memname = $memname;}
	function setMemreserve($memreserve)	{$this->memreserve = $memreserve;}
	function setMememail($mememail)		{$this->mememail = $mememail;}
	function setBoardadmin($boardadmin)	{$this->boardadmin = $boardadmin;}
	function setGifttempkey($gifttempkey){$this->gifttempkey = $gifttempkey;}
	function setOldtempkey($oldtempkey) {$this->oldtempkey = $oldtempkey;}
	function setOkpayment($okpayment)	{$this->okpayment = $okpayment;}
	function setMemlevel($memlevel)		{$this->memlevel = $memlevel;}
	function setWsmember($wsmember)		{$this->wsmember = $wsmember;}
	function setStaffType($staff_type)		{$this->staff_type = $staff_type;}
	function setStaffYn($staff_yn)		{$this->staff_yn = $staff_yn;}
	function setCooperYn($cooper_yn)		{$this->cooper_yn = $cooper_yn;}
	function setStaffCardNo($staffcardno)		{$this->staffcardno = $staffcardno;}
	function setCheckSns($checksns)		{$this->checksns = $checksns;}
	function setCheckSnsLogin($checksnslogin)		{$this->checksnslogin = $checksnslogin;}
	function setCheckSnsAccess($checksnsaccess)		{$this->checksnsaccess = $checksnsaccess;}
	function setCheckSnsChurl($checksnschurl)		{$this->checksnschurl = $checksnschurl;}
	
	// 저장 정보 추가 (2015.10.29 - 김재수)
	function setReferrerUrl($referrerurl)		{$this->referrerurl = $referrerurl;}
	function setAffiliateType($affiliatetype)		{$this->affiliatetype = $affiliatetype;}
	function setAffiliateNo($affiliateno)		{$this->affiliateno = $affiliateno;}
	function setAffiliateName($affiliatename)		{$this->affiliatename = $affiliatename;}
	function setAffiliateImg($affiliateimg)		{$this->affiliateimg = $affiliateimg;}


	function getId()			{return $this->id;}
	function getName()			{return $this->name;}
	function getEmail()			{return $this->email;}
	function getAuthkey()		{return $this->authkey;}
	function getNickName()		{return $this->nickname;}
	function getShopurl()		{return $this->shopurl;}
	function getShopurl2()		{return $this->shopurl2;}
	function getRefurl()		{return $this->refurl;}
	function getAuthidkey()		{return $this->authidkey;}
	function getMemid()			{return $this->memid;}
	function getMemgroup()		{return $this->memgroup;}
	function getMemname()		{return $this->memname;}
	function getMemreserve()	{return $this->memreserve;}
	function getMememail()		{return $this->mememail;}
	function getBoardadmin()	{return $this->boardadmin;}
	function getGifttempkey()	{return $this->gifttempkey;}
	function getOldtempkey()	{return $this->oldtempkey;}
	function getOkpayment()		{return $this->okpayment;}
	function getMemlevel()		{return $this->memlevel;}
	function getWsmember()		{return $this->wsmember;}
	function getStaffType()		{return $this->staff_type;}
	function getStaffYn()		{return $this->staff_yn;}
	function getCooperYn()		{return $this->cooper_yn;}
	function getStaffCardNo()		{return $this->staffcardno;}
	function getCheckSns()		{return $this->checksns;}
	function getCheckSnsLogin()		{return $this->checksnslogin;}
	function getCheckSnsAccess()		{return $this->checksnsaccess;}
	function getCheckSnsChurl()		{return $this->checksnschurl;}

	// 저장 정보 추가 (2015.10.29 - 김재수)
	function getReferrerUrl()		{return $this->referrerurl;}
	function getAffiliateType()		{return $this->affiliatetype;}
	function getAffiliateNo()		{return $this->affiliateno;}
	function getAffiliateName()		{return $this->affiliatename;}
	function getAffiliateImg()		{return $this->affiliateimg;}

	//쇼핑몰 방문자수 확인
	function getShopCount() {
		$sql = "SELECT * FROM tblshopcount ";
		$result=pmysql_query($sql,get_mdb_conn());
		if($row=pmysql_fetch_object($result)) {
			$count=(int)$row->count;
		} else {
			$count=0;
		}
		pmysql_free_result($result);
		return $count;
	}

	function getTempkey() {
		if(strlen($this->tempkey)!=32) {
			$basketauthkey=$_COOKIE["basketauthkey"];
			$this->tempkey=$basketauthkey;
		}
		return $this->tempkey;
	}
}

$_MShopInfo = new _MShopInfo($_COOKIE["_sinfo"]);

class _MShopData extends _MShopInfo {
	var $shopdata		= "";

	function _MShopData($_MShopInfo) {
		//$this=$_MShopInfo;

		$sql = "SELECT * FROM tblshopinfo ";
		$result=pmysql_query($sql,get_mdb_conn());
		if($row=pmysql_fetch_object($result)) {
			pmysql_free_result($result);
			$this->shopdata=$row;

			$this->shopdata->onetop_type=$row->top_type;
			if ($row->frame_type=="Y") $this->shopdata->top_type="top";

			$this->shopdata->deli_basefee=$this->shopdata->deli_basefee+0;
			if($row->deli_setperiod<2) $this->shopdata->deli_setperiod=1;
			if($row->deli_basefee==-9) {
				$this->shopdata->deli_basefee=0;
				$this->shopdata->deli_after="Y";
			}
			if ($row->deli_miniprice==0) $this->shopdata->deli_miniprice=1000000000;
			else $this->shopdata->deli_miniprice = $row->deli_miniprice;
			if (strlen($row->deli_type)==0) $this->shopdata->deli_type=0;
			if (strlen($row->reserve_join)==0) $this->shopdata->reserve_join=0;

			$this->shopdata->primg_minisize2 = 250;

			unset($ETCTYPE);
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				$etccnt = count($etctemp);
				for ($etci=0;$etci<$etccnt;$etci++) {
					$etctemp2 = explode("=",$etctemp[$etci]);
					if(isset($etctemp2[1])) {
						$ETCTYPE[$etctemp2[0]]=$etctemp2[1];
					} else {
						$ETCTYPE[$etctemp2[0]]="";
					}
				}
			}
			$this->shopdata->ETCTYPE=$ETCTYPE;
			$this->shopdata->count=$this->getShopCount();
			$this->shopdata->visitor=$this->shopdata->count;

			$this->shopdata->primg_minisize2 = 250;

			if(strlen($row->search_info)>0) {
				$temp=explode("=",$row->search_info);
				$cnt = count($temp);
			}

			$this->shopdata->search_info=array();
			if($cnt>0) {
				$this->shopdata->search_info["autosearch"]="";
				$this->shopdata->search_info["bestkeyword"]="";
				$this->shopdata->search_info["bestauto"]="";
				$this->shopdata->search_info["keyword"]="";
				for ($i=0;$i<$cnt;$i++) {
					if (strpos($temp[$i],"AUTOSEARCH=")===0) $this->shopdata->search_info["autosearch"]=substr($temp[$i],11);	#자동완성기능 사용여부(Y/N)
					elseif (strpos($temp[$i],"BESTKEYWORD=")===0) $this->shopdata->search_info["bestkeyword"]=substr($temp[$i],12);	#인기검색어기능 사용여부(Y/N)
					elseif (strpos($temp[$i],"BESTAUTO=")===0) $this->shopdata->search_info["bestauto"]=substr($temp[$i],9);	#인기검색어 자동추출인지 수동등록인지(Y/N)
					elseif (strpos($temp[$i],"KEYWORD=")===0) $this->shopdata->search_info["keyword"]=substr($temp[$i],8);	#인기검색어 수동등록 리스트
				}
			}
			if(ord($this->shopdata->search_info["autosearch"])==0) $this->shopdata->search_info["autosearch"]="N";
			if(ord($this->shopdata->search_info["bestkeyword"])==0) $this->shopdata->search_info["bestkeyword"]="Y";
			if(ord($this->shopdata->search_info["bestauto"])==0) $this->shopdata->search_info["bestauto"]="Y";

			if(strlen($this->shopdata->ETCTYPE["SELFCODEVIEW"])>0) {
				$this->shopdata->ETCTYPE["SELFCODELOCAT"]="";
				$this->shopdata->ETCTYPE["SELFCODEBR"]="";

				if($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Z") {
					$this->shopdata->ETCTYPE["SELFCODELOCAT"]="Y";
				} else if($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="N" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="M") {
					$this->shopdata->ETCTYPE["SELFCODELOCAT"]="N";
				}

				if($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="N") {
					$this->shopdata->ETCTYPE["SELFCODEBR"]="<br>";
				}

				if(strlen($this->shopdata->ETCTYPE["SELFCODEF"])>0) {
					$this->shopdata->ETCTYPE["SELFCODEF"] = str_replace(" ", "&nbsp;", @htmlspecialchars($this->shopdata->ETCTYPE["SELFCODEF"]));
				}

				if(strlen($this->shopdata->ETCTYPE["SELFCODEB"])>0) {
					$this->shopdata->ETCTYPE["SELFCODEB"] = str_replace(" ", "&nbsp;", @htmlspecialchars($this->shopdata->ETCTYPE["SELFCODEB"]));
				}
			}

			//스마트폰용 (모바일웹 사용여부, 모바일웹 로고 등.....)
			$sql="select * from tblmobileShopInfo";
			$result=pmysql_query($sql,get_mdb_conn());
			$row_mobile=pmysql_fetch_object($result);
			pmysql_free_result($result);

			$this->shopdata->smart_use=$row_mobile->useyn;

			if($row_mobile->logo_img){
				$this->shopdata->smart_logo=$row_mobile->logo_img;
				//$this->shopdata->smart_logo="..//images/".$this->shopdata->icon_type."logo.gif";
			}



		} else {
			pmysql_free_result($result);

			header("Content-Type: text/html; charset=euc-kr");
			echo "<script>alert('쇼핑몰 정보 등록이 안되었습니다.');</script>\n"; exit;
		}
	}
}

$_MShopInfo = new _MShopInfo($_COOKIE["_sinfo"]);

function get_mdb_conn() {
	global $DB_CONN, $Dir;
	if (!$DB_CONN) {
		$f=@file($Dir.DataDir."config.php") or die("config.php파일이 없습니다.<br />DB설정을 먼저 하십시요");
		for($i=1;$i<=4;$i++) $f[$i]=trim(str_replace("\n","",$f[$i]));
		$DB_CONN = @pmysql_connect($f[1],$f[2],$f[3]) or die("DB 접속 에러가 발생하였습니다.");
		$status = @pmysql_select_db($f[4],$DB_CONN) or die("DB Select 에러가 발생하였습니다.");

		if (!$status) {
		   die("DB Select 에러가 발생하였습니다.");
		}
	}

	return $DB_CONN;
}


function getMainproductlist($special,$limit) {
	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage,a.maximage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$sql.= "LIMIT ".$limit;
	$resobj=pmysql_query($sql,get_mdb_conn());

	return $resobj;
}

/**
* add by PTY at 2014.02.21
* @param $special
*
* @return $list_cnt
*/

function getMainproductlistOffset($special,$limit,$offset){
	if($offset==null||$offset==""){
		$offset="0";
	}

	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$sql.= "LIMIT ".$limit." OFFSET ".$offset;
	$resobj=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($resobj);

	return $list_cnt;
}



/**
* add by PTY at 2014.02.21
* @param $special
*
* @return $list_cnt
*/

function getCountMainproductlist($special){
	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmain ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$resobj=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($resobj);

	return $list_cnt;
}

/**
* add by PTY at 2014.03.18
* @param $special
*
* @return $list_cnt
* @comment 모바일용 메인리스트를 불러온다
*/

function getMainproductlistMobile($special,$limit) {
	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmainmobile ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage,a.maximage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$sql.= "LIMIT ".$limit;
	$resobj=pmysql_query($sql,get_mdb_conn());

	return $resobj;
}

/**
* add by PTY at 2014.03.18
* @param $special
*
* @return $list_cnt
*/

function getMainproductlistOffsetMobile($special,$limit,$offset){
	if($offset==null||$offset==""){
		$offset="0";
	}

	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmainmobile ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$sql.= "LIMIT ".$limit." OFFSET ".$offset;
	$resobj=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($resobj);

	return $list_cnt;
}



/**
* add by PTY at 2014.03.18
* @param $special
*
* @return $list_cnt
*/

function getCountMainproductlistMobile($special){
	global $_MShopInfo;

	$sp_prcode="";
	$sql = "SELECT special_list FROM tblspecialmainmobile ";
	$sql.= "WHERE special='".$special."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
	}
		//debug($sp_prcode);
	pmysql_free_result($result);

	$sql = "SELECT a.pridx, a.productcode, a.productname, a.sellprice, a.quantity, ";
	$sql.= "a.tinyimage, a.date, a.etctype, a.consumerprice, a.tag, a.selfcode FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_MShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
	$resobj=pmysql_query($sql,get_mdb_conn());

	$list_cnt = pmysql_num_rows($resobj);

	return $list_cnt;
}



function loginprocess($id='', $passwd='', $push_token ='', $push_os ='', $sns_id ='', $sns_type ='', $sns_login ='', $sns_login_id ='') {
	global $_MShopInfo, $_data, $_ShopInfo, $Dir;

	# SNS 관련 세션값 초기화
	$_ShopInfo->setCheckSns("");
	$_ShopInfo->setCheckSnsLogin("");
	$_ShopInfo->setCheckSnsAccess("");
	$_ShopInfo->setCheckSnsChurl("");
	$_ShopInfo->Save();


	if (!$id && !$sns_login_id) return "NO";
	
	unset($passwd_type);
	if($sns_login){
		$sql = "SELECT passwd, id FROM tblmember WHERE sns_type='".$sns_login_id."' ";
		$alertMsg='SNS_NO';
	}else{
		$sql = "SELECT passwd, id FROM tblmember WHERE id='".$id."' ";
		$alertMsg='NO';
	}
	$result=pmysql_query($sql,get_mdb_conn());
	if($row=pmysql_fetch_object($result)) {

		// 회원등급 변경 상위 이동(2016.10.26 - 김재수 추가)
		//ChangeGrade($row->id);

		if(substr($row->passwd,0,3)=="$1$") {
			$passwd_type="hash";
			$hashdata=$row->passwd;
		} else if(strlen($row->passwd)==16) {
			$passwd_type="password";
			$chksql = "SELECT PASSWORD('1') AS passwordlen ";
			$chkresult=pmysql_query($chksql,get_mdb_conn());
			if($chkrow=pmysql_fetch_object($chkresult)) {
				if(strlen($chkrow->passwordlen)==41 && substr($chkrow->passwordlen,0,1)=="*") {
					$passwd_type="old_password";
				}
			}
			pmysql_free_result($chkresult);
		} else if(substr($row->passwd,0,1) == "*" && strlen($row->passwd) == 41){
			// mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
			$passwd_type = "sha1";
			$shadata = "*".strtoupper(SHA1(unhex(SHA1($passwd))));
		} else {
			$passwd_type="md5";
		}
	} else {
		return $alertMsg;
	}

	pmysql_free_result($result);
	if($sns_login){
		$sql = "SELECT a.*, b.group_level,b.group_wsmember FROM tblmember a  left join tblmembergroup b on a.group_code=b.group_code WHERE a.sns_type='".$sns_login_id."' ";
	}else{
		$sql = "SELECT a.*, b.group_level,b.group_wsmember 
		FROM tblmember a  
		left join tblmembergroup b on a.group_code=b.group_code ";

		$sql .= " WHERE a.id='".$id."' ";

		if($passwd_type=="hash") {
			$sql.= "AND a.passwd='".crypt($passwd, $hashdata)."' ";
		} else if($passwd_type=="password") {
			$sql.= "AND a.passwd=password('".$passwd."')";
		} else if($passwd_type=="old_password") {
			$sql.= "AND a.passwd=old_password('".$passwd."')";
		} else if($passwd_type=="md5") {
			$sql.= "AND a.passwd=md5('".$passwd."')";
		} elseif($passwd_type=="sha1") {
			$sql.= "AND a.passwd = '".$shadata."'";
		}
	}

	$result = pmysql_query($sql,get_mdb_conn());
	if($row=pmysql_fetch_object($result)) {
		$memid=$row->id;
		$memname=$row->name;
		$memnickname=$row->nickname;
		$mememail=$row->email;
		$memgroup=$row->group_code;
		$memreserve=$row->reserve;
		$memlevel=$row->group_level;
		$wsmember=$row->group_wsmember;
		$memgender=$row->gender;
		$memage=$row->age;
		$staff_type=$row->staff_type;	
		$staff_yn=$row->staff_yn;	
		$cooper_yn=$row->cooper_yn;	
		$staffcardno=$row->staffcardno;	

		if($row->member_out=="Y") {	//탈퇴한 회원
			return "OUT";
		}
		if($row->confirm_yn=="N") { //관리자인증기능여부 및 회원인증 검사
			return "CONFIRM";
		}

		if($_data->coupon_ok=="Y") {
			include_once($Dir."lib/coupon.class.php");
			$ci = new CouponInfo();
			# 즉시
			$ci->set_coupon( '1' );
			$ci->search_coupon( '', $memid ); // 쿠폰 확인
			$ci->set_couponissue( $memid ); // 등록 테이블
			$ci->insert_couponissue(); // 발급
			# 무료배송
			$ci->set_coupon( '9' );
			$ci->search_coupon( '', $memid ); // 쿠폰 확인
			$ci->set_couponissue( $memid ); // 등록 테이블
			$ci->insert_couponissue(); // 발급
			# 생일
			$ci->set_coupon( '10' );
			$ci->search_coupon( '', $memid ); // 쿠폰 확인
			$ci->set_couponissue( $memid ); // 등록 테이블
			$ci->insert_couponissue(); // 발급
			# 회원 등급
			$ci->set_coupon( '15' );
			$ci->search_coupon( '', $memid ); // 쿠폰 확인
			$ci->set_couponissue( $memid ); // 등록 테이블
			$ci->insert_couponissue(); // 발급
		}
	} else {
		return "OUT";
	}

	pmysql_free_result($result);
	
	$authidkey = md5(uniqid(""));
	$_MShopInfo->setMemid($memid);
	$_MShopInfo->setNickName($memnickname);
	$_MShopInfo->setAuthidkey($authidkey);
	$_MShopInfo->setMemgroup($memgroup);
	$_MShopInfo->setMemname($memname);
	$_MShopInfo->setMemreserve($memreserve);
	$_MShopInfo->setMememail($mememail);
	$_MShopInfo->setMemlevel($memlevel);
	$_MShopInfo->setWsmember($wsmember);
	$_MShopInfo->setStaffType($staff_type);
	$_MShopInfo->setStaffYn($staff_yn);
	$_MShopInfo->setCooperYn($cooper_yn);
	$_MShopInfo->setStaffCardNo($staffcardno);

	$_MShopInfo->Save();

	$sql = "UPDATE tblmember SET ";
	$sql.= "authidkey		= '".$authidkey."', ";
	if(!$sns_login){
		if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
			$sql.= "passwd		= '".md5($passwd)."', ";
		} else if($passwd_type=="sha1") {
			$sql.= "passwd		= '*".strtoupper(SHA1(unhex(SHA1($passwd))))."', ";
		}
	}

	$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
	$sql.= "logindate		= '".date("YmdHis")."', ";

	## 로그인시 토큰 추가 시작
	if($push_token){
		if($push_os == "Android"){
			$sql.= "push_token		= '".$push_token."', ";
		}else{
			$sql.= "push_token_ios		= '".$push_token."', ";
		}
	}
	## 로그인시 토큰 추가 종료

	$sql.= "logincnt		= logincnt+1 ";
	$sql.= "WHERE id='".$_MShopInfo->getMemid()."'";
	pmysql_query($sql,get_mdb_conn());

	if (get_session('ACCESS') == 'app') {
		$access_type	= "app";
	} else {
		$access_type	= "mobile";
	}

	//--------------------------------- 로그인시 장바구니를 로그인 아이디로 한다. (2016.06.22 - 김재수 추가)-------------------------------//
	$upDelQuery = "DELETE FROM tblbasket where basketidx NOT IN (select MAX(basketidx) basketidx 
	from tblbasket 
	where 
	(tempkey = '".$_ShopInfo->getTempkey()."' AND id='') 
	or id = '".$_ShopInfo->getMemid()."' 
	group by tempkey, productcode, opt1_idx, opt2_idx, optidxs, package_idx, assemble_idx)
	AND ((tempkey = '".$_ShopInfo->getTempkey()."' AND id='') 
	or id = '".$_ShopInfo->getMemid()."') ";
	pmysql_query( $upDelQuery, get_db_conn() );

	$upNewQuery = "UPDATE tblbasket SET id = '".$_MShopInfo->getMemid()."' WHERE tempkey = '".$_MShopInfo->getTempkey()."' AND id='' ";
	pmysql_query( $upNewQuery, get_db_conn() );
	//---------------------------------------------------------------------------------------------------------------------------------//

	//---------------------------------------------------- 로그인시 로그를 등록한다. ----------------------------------------------------//
	$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$_MShopInfo->getMemid()."','login','".$access_type."','".date("YmdHis")."')";
	pmysql_query($memLogSql,get_db_conn());
	//---------------------------------------------------------------------------------------------------------------------------------//

	//로그인시 포인트 소멸체크 및 업데이트(2015.11.25 - 김재수 추가)
	/*if($_data->reserve_maxuse >= 0) { 
		$sum_point = get_point_sum($_MShopInfo->getMemid()); 
		$sql= " update tblmember set reserve = '{$sum_point}' where id = '".$_MShopInfo->getMemid()."' "; 
		pmysql_query($sql); 
		//echo "sql1 = ".$sql."<br>"; 
	}*/

	//로그인시 포인트 소멸체크 및 업데이트(2015.11.25 - 김재수 추가)
	if($_data->reserve_maxuse >= 0) { 
		$sum_act_point = get_point_act_sum($_MShopInfo->getMemid()); 
		$sql= " update tblmember set act_point = '{$sum_act_point}' where id = '".$_MShopInfo->getMemid()."' "; 
		pmysql_query($sql); 
		//echo "sql1 = ".$sql."<br>"; 
	}

	//로그인시 이전주문체크 정보 삭제(2016.09.06 - 김재수 추가)
	$sql= " delete from tblorder_check where id = '".$_MShopInfo->getMemid()."' "; 
	pmysql_query($sql); 

	$loginday = date("Ymd");

	//로그인시 포인트 지급(2015.02.28 - 김재수 추가)
	include_once($Dir."conf/config.ap_point.php");
	$tp_login_cnt		= $pointSet['login']['count']; 
	$tp_login_point	= $pointSet['login']['point']; 

	// 오늘 로그인시 적립받은 갯수를 체크한다.
	list($lp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) lp_cnt from tblpoint_act WHERE rel_flag='@login_point' and regdt >= '{$loginday}000000' AND regdt <= '{$loginday}999999' AND mem_id = '".$_MShopInfo->getMemid()."' "));
	if ($lp_cnt < $tp_login_cnt) { // 로그인시 적립받은 갯수가 설정수보다 작으면
		insert_point_act($_MShopInfo->getMemid(), $tp_login_point, '로그인 포인트', '@login_point', '', date("YmdHis"), 0);
	}

	// 회원등급 변경(2016.09.30 - 김재수 추가)
	//ChangeGrade($_MShopInfo->getMemid());
	
	// 회원 온오프라인통합포인트내역 가져오기(2017.04.13 - 김재수 추가)
	getErpOnOffPoint($_MShopInfo->getMemid());
	getErpMeberPoint($_MShopInfo->getMemid());

	$app_login_point = 'N';
	//앱 첫 로그인시 10,000P 적립
	if (get_session('ACCESS') == 'app') {
		//앱 첫 로그인인지 체크한다.
		list($ap_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) ap_cnt from tblmemberlog WHERE id = '".$_MShopInfo->getMemid()."' and type='login' and access_type = 'app'"));
		if ($ap_cnt == 1) {//앱 첫 로그인이면
			insert_point_act($_MShopInfo->getMemid(), '10000', 'App 첫 로그인 포인트', '@app_login_point', '', date("Ymd"), 0);
			$app_login_point = "Y";
		}
	}


	$sql = "SELECT id_list FROM tblshopcountday ";
	$sql.= "WHERE date='".$loginday."'";
	$result = pmysql_query($sql,get_mdb_conn());
	if($row3 = pmysql_fetch_object($result)){
		if(!strpos(" ".$row3->id_list,"".$_MShopInfo->getMemid()."")){
			$id_list=$row3->id_list.$_MShopInfo->getMemid()."";
			$sql = "UPDATE tblshopcountday SET id_list='".$id_list."',login_cnt=login_cnt+1 ";
			$sql.= "WHERE date='".$loginday."'";
			pmysql_query($sql,get_mdb_conn());
		}
	} else {
		$id_list="".$_MShopInfo->getMemid()."";
		$sql = "INSERT INTO tblshopcountday (date,count,login_cnt,id_list) VALUES ('".$loginday."',1,1,'".$id_list."')";
		pmysql_query($sql,get_mdb_conn());
	}

	if ($app_login_point == 'Y') {
		return "AOK";
	} else {
		return "OK";
	}
}

function getReserveConvert($reserve,$reservetype,$sellprice,$reservshow) {
	global $_MShopInfo, $_data;

	$_data->ETCTYPE["MEM"]=(isset($_data->ETCTYPE["MEM"])?$_data->ETCTYPE["MEM"]:"");

	if($_data->ETCTYPE["MEM"]=="Y" && strlen($_MShopInfo->getMemid())==0 && $reservshow=="Y") {
		return 0;
	} else {
		$sellprice = (int)$sellprice;
		if($reservetype=="Y") {
			if($sellprice>0 && $reserve>0) {
				return @round($sellprice*$reserve*0.01);
			} else {
				return 0;
			}
		} else {
			return $reserve;
		}
	}
}

function replace_content($filter, $content) {
	if(strlen($filter)>0) {
		$arr_filter=explode("#",$filter);
		$detail_filter=$arr_filter[0];
		$filters=explode("=",$detail_filter);
		$filtercnt=count($filters)/2;

		for($i=0;$i<$filtercnt;$i++){
			$filterpattern[$i]="/".str_replace("\0","\\0",preg_quote($filters[$i*2]))."/";
			$filterreplace[$i]=$filters[$i*2+1];
			if(strlen($filterreplace[$i])==0) $filterreplace[$i]="***";
		}
	}
	if(strlen($detail_filter)>0) {
		$content=preg_replace($filterpattern,$filterreplace,$content);
	}

    if (strpos($content, "table>") != false || strpos($content, "TABLE>") != false) {
		$content="<pre>".$content."</pre>";
    } else if (strpos($content, "</") != false) {
		$content=str_replace("\n", "<br>", $content);
	} else if (strpos($content,"img") != false || strpos($content, "IMG") != false) {
		$content=str_replace("\n", "<br>", $content);
	} else{
		$content=str_replace("  ", " &nbsp;", str_replace("\n", "<br>", $content));
	}
    $content=str_replace('\\','\\\\',$content);
    $content=str_replace('$','\$',$content);
    return $content;
}

function ajax_convert($string) {
	return mb_convert_encoding($string, "UTF-8", "EUC-KR");
}
function data_convert($string) {
	return mb_convert_encoding($string, "EUC-KR", "UTF-8");
}
function response($status, $msg=NULL, $data=NULL) {
	if (strlen($msg)>0) {
		$msg=rawurlencode(ajax_convert($msg));
	}

	$json=new Services_JSON();

	echo $json->encode(array(
				"success" => $status,
				"msg" => $msg,
				"data" => $data,
				));
	exit;
}

include "json.inc.php";
?>
