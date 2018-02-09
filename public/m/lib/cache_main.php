<?php
function urlsafe_b64encode($string) {


    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}

if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$_REQUEST['bridx']=false;
$_SESSION['brand_session_no']=false;
$_SESSION['brand_outlet']=false;


if($_SERVER['PHP_SELF']=="/front/outlet.php"){
      $_SESSION['brand_outlet']="Y";
      $_SESSION['brand_session_no']="";
}else if($_SERVER['PHP_SELF']=="/index.htm"){
      unset($_SESSION['brand_outlet']);
      $_SESSION['brand_session_no']="";
}else if($_REQUEST['bridx']>0) {
      unset($_SESSION['brand_outlet']);
        $_SESSION['brand_session_no']=$_REQUEST['bridx'];
}
$b_idx = $_SESSION['brand_session_no'];
if($_SESSION['brand_outlet'])
$b_idx = $_SESSION['brand_outlet'];
$TEMP_SCRIPTNM=$_SERVER['SCRIPT_NAME'];

$cache_file_name = $_SERVER['DOCUMENT_ROOT'].'/'.DataDir.'cache/'.urlsafe_b64encode($_SERVER['REQUEST_URI']).'.'.$b_idx;

$HTML_ERROR_EVENT="NO";
$HTML_CACHE_EVENT="NO";

function html_cache($buffer) {
        global $cache_file_name,$HTML_ERROR_EVENT;
    if(strlen($buffer)>10000) {
        file_put_contents($cache_file_name,$buffer.pack("L",strlen($buffer)+4));
    }
        return $buffer;
}

function html_cache_out() {
        global $cache_file_name;

    $buffer = file_get_contents($cache_file_name);
    list(,$len) = unpack("L",substr($buffer,-4));
    if($len==strlen($buffer)) {
        echo(substr($buffer,0,-4)); exit;
    }
}


function error_cache($errno, $errstr, $errfile, $errline) {
        global $HTML_ERROR_EVENT;
        if (strpos($errstr,"mysql")!==FALSE) $HTML_ERROR_EVENT = $errstr;
}

//$error_handler = set_error_handler("error_cache");

$urls = array('/','/index2.htm','/front/productlist.php2','/front/brand_main.php','/front/brand_detail.php','/front/outlet.php','/front/lookbook_list.php','/front/ecatalog_list.php','/front/brand_store.php',
'/front/promotion_detail2.php','/front/promotion.php','/m/index.htm','/front/storeList.php','/front/instagramlist.php','/m/movie_list.php',
        '/m/productlist.php','/m/brand_main.php','/m/brand_detail.php','/m/ecatalog_list.php','/m/promotion.php','/m/lookbook_list.php','/m/promotion_detail2.php','/front/productdetail2.php','/m/productdetail2.php');
if (in_array($TEMP_SCRIPTNM,$urls) && $_SERVER['REQUEST_METHOD']=="GET" && strlen($_ShopInfo->getMemid())==0 && $_REQUEST['hashkeyword']=='') {

// 紐⑤컮?쇱~]몄? pc?몄? 泥댄~A|,
$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
if(preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) && !$_GET[pc]) {

        $m_referrer_tmp = parse_url($_SERVER['HTTP_REFERER']);
        $m_referrer_url = $m_referrer_tmp['host'];
        if ((strpos($_SERVER["REQUEST_URI"],'/front/') !== false || strpos($_SERVER["REQUEST_URI"],'/board/') !== false || strpos($_SERVER["REQUEST_URI"],'/index.htm') === 0 || $_SERVER["REQUEST_URI"]=='/') ) {
                if ($_GET['board']) {
                        $mainurl= str_replace('/board/','/m/',$_SERVER["REQUEST_URI"]);
                        if ($_GET['pagetype'] == 'view') { // ?~A?몃낫湲???寃쎌~Z|0
                                if ($_GET['board'] == 'event') { // ?대깽???~A??蹂닿린??寃쎌~Z|0
                                        $mainurl= "/m/event_view.php";
                                } else {
                                        $mainurl= "/m/board_view.php";
                                }
                                $mainurl .= "?board=".$_GET['board']."&boardnum=".$_GET['num'];
                        }
                } else {
                        $mainurl= str_replace('/front/','',$_SERVER["REQUEST_URI"]);
                        $mainurl= '/m/'.$mainurl;
                        $mainurl= str_replace(array('//','csfaq.php'),array('/','customer_faq.php'),$mainurl); // FAQ 寃쎈줈 ?ъ~Dㅼ젙
                }
                //echo $mainurl;
                Header("Location: ".$mainurl);
                exit;
        }
        //}
}
	if(strpos($_SERVER["REQUEST_URI"],'/index2.htm')===0) {
		$coos = array();
		foreach ($_COOKIE as $key=>$val) {
			if(strpos($key,'layerNotOpen')===0) {
				$coos[] = substr($key,12);	
			}
		}
		asort($coos);
		$cache_file_name .= '~'.implode('.',$coos);
        }

	if(strpos($_SERVER["REQUEST_URI"],'/m/')!==false) {
		$cache_file_name .= '@'.get_session("ACCESS").'@'.$checkApp;
	}

	if(strpos($TEMP_SCRIPTNM,'productdetail.php')>0) $ctime = 60*10;
	else $ctime = 60*30;

        if (file_exists($cache_file_name) && time()-filemtime($cache_file_name)<$ctime) {
                html_cache_out();
        } else {
                $HTML_CACHE_EVENT="OK";
                ob_start("html_cache");
        }
}
