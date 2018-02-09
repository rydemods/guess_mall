<?
if(substr(getenv("SCRIPT_NAME"),-12)=="/lib.inc.php") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

class _MShopInfo {
	var $id				= "";
	var $authkey		= "";

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
	var $gifttempkey	= "";	//사은품 관련 키
	var $oldtempkey		= "";	//결제창 띄울경우 기존 장바구니 인증키
	var $okpayment		= "";	//결제시 새로고침 방지 쿠키

	var $searchkey		= "";	//검색인증 구분키

	function _MShopInfo($_sinfo) {
		if ($_sinfo) {
			$savedata=unserialize(decrypt_md5($_sinfo));

			$this->id			= $savedata["id"];
			$this->authkey		= $savedata["authkey"];

			$this->shopurl		= $savedata["shopurl"];
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
		}
	}

	function Save() {
		$savedata["id"]			= $this->getId();
		$savedata["authkey"]	= $this->getAuthkey();
		$savedata["shopurl"]	= $this->getShopurl();
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
	}

	function setId($id)					{$this->id = $id;}
	function setAuthkey($authkey)		{$this->authkey = $authkey;}
	function setShopurl($shopurl)		{$this->shopurl = $shopurl;}
	function setRefurl($refurl)			{$this->refurl = $refurl;}
	function setAuthidkey($authidkey)	{$this->authidkey = $authidkey;}
	function setMemid($memid)			{$this->memid = $memid;}
	function setMemgroup($memgroup)		{$this->memgroup = $memgroup;}
	function setMemname($memname)		{$this->memname = $memname;}
	function setMemreserve($memreserve)	{$this->memreserve = $memreserve;}
	function setMememail($mememail)		{$this->mememail = $mememail;}
	function setBoardadmin($boardadmin)	{$this->boardadmin = $boardadmin;}
	function setGifttempkey($gifttempkey){$this->gifttempkey = $gifttempkey;}
	function setOldtempkey($oldtempkey){$this->oldtempkey = $oldtempkey;}
	function setOkpayment($okpayment)	{$this->okpayment = $okpayment;}


	function getId()			{return $this->id;}
	function getAuthkey()		{return $this->authkey;}
	function getShopurl()		{return $this->shopurl;}
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



function loginprocess($id, $passwd) {
	global $_MShopInfo;

	unset($passwd_type);
	$sql = "SELECT passwd FROM tblmember WHERE id='".$id."' ";
	$result=pmysql_query($sql,get_mdb_conn());
	if($row=pmysql_fetch_object($result)) {
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
		} else {
			$passwd_type="md5";
		}
	} else {
		return "NO";
	}
	pmysql_free_result($result);



	$sql = "SELECT * FROM tblmember WHERE id='".$id."' ";
	if($passwd_type=="hash") {
		$sql.= "AND passwd='".crypt($passwd, $hashdata)."' ";
	} else if($passwd_type=="password") {
		$sql.= "AND passwd=password('".$passwd."')";
	} else if($passwd_type=="old_password") {
		$sql.= "AND passwd=old_password('".$passwd."')";
	} else if($passwd_type=="md5") {
		$sql.= "AND passwd=md5('".$passwd."')";
	}

	$result = pmysql_query($sql,get_mdb_conn());
	if($row=pmysql_fetch_object($result)) {
		$memid=$row->id;
		$memname=$row->name;
		$mememail=$row->email;
		$memgroup=$row->group_code;
		$memreserve=$row->reserve;

		if($row->member_out=="Y") {	//탈퇴한 회원
			return "OUT";
		}
		if($row->confirm_yn=="N") { //관리자인증기능여부 및 회원인증 검사
			return "CONFIRM";
		}
	} else {
		return "NO";
	}
	pmysql_free_result($result);

	$authidkey = md5(uniqid(""));
	$_MShopInfo->setMemid($memid);
	$_MShopInfo->setAuthidkey($authidkey);
	$_MShopInfo->setMemgroup($memgroup);
	$_MShopInfo->setMemname($memname);
	$_MShopInfo->setMemreserve($memreserve);
	$_MShopInfo->setMememail($mememail);
	$_MShopInfo->Save();


	$sql = "UPDATE tblmember SET ";
	$sql.= "authidkey		= '".$authidkey."', ";
	if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
		$sql.= "passwd		= '".md5($passwd)."', ";
	}
	$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
	$sql.= "logindate		= '".date("YmdHis")."', ";
	$sql.= "logincnt		= logincnt+1 ";
	$sql.= "WHERE id='".$_MShopInfo->getMemid()."'";
	pmysql_query($sql,get_mdb_conn());

	$loginday = date("Ymd");
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


	return "OK";
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
