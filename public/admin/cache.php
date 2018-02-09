<?php // hspark
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}


function groupmail_tempfile_delete($path)
{
    $endtime = time() - (60 * 60 * 7);    //1주일 지난파일 삭제
    $i       = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile() && $endtime > $f->getMTime()) {
            unlink($f->getRealPath());
        }else        if (!$f->isDot() && $f->isDir()) {
            groupmail_tempfile_delete($f->getRealPath());
        }
    }	
}

function ssl_tempfile_delete($path)
{
    $endtime = time() - (60 * 60 * 1);    //1일 지난파일 삭제
    $i       = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile() && $endtime > $f->getMTime()) {
            unlink($f->getRealPath());
        }else        if (!$f->isDot() && $f->isDir()) {
            ssl_tempfile_delete($f->getRealPath());
        }
    }
}

function product_tempfile_delete($path,$type)
{
    $match = '';
    if ($type == "main") {
        if (is_dir($path."main")) {
            $match = $path."main/*_main.php_";
        }
    }elseif ($type == "product") {
        if (is_dir($path."product")) {
            $match = $path."product/*_product*";
        }
    }elseif ($type == "productb") {
        if (is_dir($path."product")) {
            $match = $path."product/*_productb*";
        }
    }
    if (ord($match) > 0) {
        $match = str_replace(array(".."," "),"",$match);
        $matches = glob($match);
        if (is_array($matches)) {
            foreach ($matches as $cachefile) {
                $filetime = filemtime($cachefile) + (60 * 10);
                if ($filetime <= time()) {
                    @unlink($cachefile);
                }
            }
        }
    }
}

function rss_tempfile_delete($path)
{
    $match = '';
    if (is_dir($path)) {
        $match = $path."*_rss.*";
    }
    if (ord($match) > 0) {
        $match = str_replace(array(".."," "),"",$match);
        $matches = glob($match);
        if (is_array($matches)) {
            foreach ($matches as $cachefile) {
                $filetime = filemtime($cachefile) + (60 * 60);
                if ($filetime <= time()) {
                    @unlink($cachefile);
                }
            }
        }
    }
}

function board_tempfile_delete($path)
{
    $endtime = time() - (60 * 60 * 24);
    $i       = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile() && $endtime > $f->getMTime()) {
            unlink($f->getRealPath());
        }else        if (!$f->isDot() && $f->isDir()) {
            board_tempfile_delete($f->getRealPath());
        }
    }
}

function name_tempfile_delete($path)
{
    $endtime = time() - (60 * 60 * 24 * 7);
    $i       = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile() && $endtime > $f->getMTime()) {
            unlink($f->getRealPath());
        }else        if (!$f->isDot() && $f->isDir()) {
            name_tempfile_delete($f->getRealPath());
        }
    }
}

function order_backupfile_delete($path)
{
    $enddate = date("Ymd00",strtotime("-7 day"));
    $i       = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile()) {
            $filename = str_replace(array('.tar','.gz','_'),'',$f->getFilename());
            if ($enddate > $filename)                unlink($f->getRealPath());
        }else        if (!$f->isDot() && $f->isDir()) {
            order_backupfile_delete($f->getRealPath());
        }
    }
}

// 입점/미니샵의 경우 방문자수 템프파일인듯(어제 방문자수 삭제) - 벤더기능 체크중 확인됨. 2015/10/22 막아놓음
function vender_visittemp_delete()
{
    /*if (setUseVender()) {
        $date = date("Ymd",strtotime('-1 day'));
        $sql  = "DELETE FROM tblvenderstorevisittmp WHERE date<'{$date}' ";
        @mysql_query($sql,get_db_conn());
    }*/
}

function order_restore()
{
    $sdate = date("YmdHi",strtotime('-1 hour -5 min'));
    $edate = date("YmdHi",strtotime('-2 hour -5 min'));
    $sql   = "SELECT * FROM tblorderinfotemp WHERE (ordercode>='{$edate}' AND ordercode<='{$sdate}') ";
    $sql .= "AND (del_gbn='' OR del_gbn is NULL) ";
    $result = @mysql_query($sql,get_db_conn());
    while ($data = @mysql_fetch_object($result)) {
        $ordercode = $data->ordercode;
        $sql       = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity,
        b.option_quantity,b.option1,b.option2 FROM tblorderproducttemp a, tblproduct b
        WHERE a.productcode=b.productcode AND a.ordercode='{$ordercode}' ";
        $result2   = @mysql_query($sql,get_db_conn());
        while ($row = @mysql_fetch_object($result2)) {
            $tmpoptq = "";
            if (ord($artmpoptq[$row->productcode]) > 0)                $optq = $artmpoptq[$row->productcode];
            else                $optq = $row->option_quantity;

            if (strlen($optq) > 51 && substr($row->opt1_name,0,5) != "[OPTG") {
                $tmpoptname1 = explode(" : ",$row->opt1_name);
                $tmpoptname2 = explode(" : ",$row->opt2_name);
                $tmpoption1  = explode(",",$row->option1);
                $tmpoption2  = explode(",",$row->option2);
                $cnt         = 1;
                $maxoptq     = count($tmpoption1);
                while ($tmpoption1[$cnt] != $tmpoptname1[1] && $cnt < $maxoptq) {
                    $cnt++;
                }
                $opt_no1 = $cnt;
                $cnt     = 1;
                $maxoptq2= count($tmpoption2);
                while ($tmpoption2[$cnt] != $tmpoptname2[1] && $cnt < $maxoptq2) {
                    $cnt++;
                }
                $opt_no2   = $cnt;
                $optioncnt = explode(",",substr($optq,1));
                if ($optioncnt[($opt_no2 - 1) * 10 + ($opt_no1 - 1)] != "") $optioncnt[($opt_no2 - 1) * 10 + ($opt_no1 - 1)] += $row->quantity;
                for ($j = 0;$j < 5;$j++) {
                    for ($i = 0;$i < 10;$i++) {
                        $tmpoptq .= ",".$optioncnt[$j * 10 + $i];
                    }
                }
                if (strlen($tmpoptq) > 0 && $tmpoptq."," != $optq) {
                    $artmpoptq[$row->productcode] = $tmpoptq;
                    $tmpoptq = ",option_quantity='{$tmpoptq},'";
                }else {
                    $tmpoptq = "";
                }
            }
            $sql = "UPDATE tblproduct SET quantity=quantity+{$row->quantity}{$tmpoptq}
            WHERE productcode='{$row->productcode}'";
            @mysql_query($sql,get_db_conn());
        }
        @mysql_free_result($result2);

        $sql     = "SELECT productcode FROM tblorderproducttemp WHERE ordercode='{$ordercode}' AND productcode LIKE 'COU%' ";
        $result3 = @mysql_query($sql,get_db_conn());
        $rowcou  = @mysql_fetch_object($result3);
        @mysql_free_result($result3);
        if ($rowcou) {
            $coupon_code = substr($row->productcode,3, - 1);
            @mysql_query("UPDATE tblcouponissue SET used='N' WHERE id='{$data->id}' AND coupon_code='{$coupon_code}'",get_db_conn());
        }
        if ($data->reserve > 0) {
            @mysql_query("UPDATE tblmember SET reserve=reserve+{$data->reserve} WHERE id='{$data->id}'",get_db_conn());
        }
        @mysql_query("UPDATE tblorderinfotemp SET del_gbn='R' WHERE ordercode='{$ordercode}'",get_db_conn());
    }
    @mysql_free_result($result);
}

function old_data_delete()
{
    $edate1 = date("Ymd00",strtotime('-30 day'));
    $edate2 = date("Ymd00",strtotime('-7 day'));
    $edate3 = date("Ymd00",strtotime('-7 day'));

    $sql    = "DELETE FROM tblsecurityadminlog WHERE date<'{$edate1}' ";
    @mysql_query($sql,get_db_conn());

    $sql    = "DELETE FROM tblbasket WHERE date<'{$edate2}' ";
    @mysql_query($sql,get_db_conn());

    if (setUseVender()) {
        $sql = "DELETE FROM tblvenderlog WHERE date<'{$edate1}' ";
        @mysql_query($sql,get_db_conn());
    }
}



//data 폴더의 쓰레기 파일들 일정시간 지나면 삭제
if (file_exists($Dir.DataDir."temp")) {
    $filecreatetime = (time() - filemtime($Dir.DataDir."temp")) / 60;
    if ($filecreatetime > 10) {
        //쓰레기 파일 삭제 후 파일 생성시간 업데이트
        groupmail_tempfile_delete($Dir.DataDir."groupmail/");
        ssl_tempfile_delete($Dir.DataDir."ssl/");
        product_tempfile_delete($Dir.DataDir."cache/","main");
        product_tempfile_delete($Dir.DataDir."cache/","product");
        board_tempfile_delete($Dir.DataDir."cache/board/");
        name_tempfile_delete($Dir.DataDir."cache/name/");
        order_backupfile_delete($Dir.DataDir."backup/");
        vender_visittemp_delete();
        //order_restore();
        //old_data_delete();

        @unlink($Dir.DataDir."temp");
    }
}else {
	file_put_contents($Dir.DataDir."temp","OK");
}

$_mscriptname = basename($_SERVER["SCRIPT_NAME"]);
switch ($_mscriptname) {
    case "shop_basicinfo.php":    #$type == "up", main, product
    case "shop_layout.php":    #$type == "up", main, product
    case "shop_keyword.php":    #$type == "up", main, product
    case "shop_displaytype.php":#$type == "up", main, product
    case "shop_mainproduct.php":#$type == "up", main, product
    case "shop_mainleftinform.php":#$type == "up", main, product
    case "shop_search.php":#$type == "up", main, product
    case "shop_review.php":#$type == "up", main, product
    case "shop_estimate.php":#$type == "up", main, product
    if ($type == "up") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "shop_productshow.php":#$type == "up", main, product
    if ($type == "up" || $type == "del" || $type == "icondel") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "shop_logobanner.php":#$type == "up", main, product
    if (strlen($type) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "shop_mainintro.php":    #$type == "up", main,
    if ($type == "up") {
        delete_cache_file("main");
    }
    break;

    case "design_option.php":    #$type == "modify", main, product
    if ($type == "modify") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "design_main.php":    #$type == "update", main, product
    case "design_bottom.php":    #$type == "update", main, product
    case "design_easytop.php":    #$type == "update", main, product
    if ($type == "update") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "design_plist.list.php":    #$mode == "update", product
    if ($mode == "update") {
        delete_cache_file("product");
    }
    break;
    case "design_section.php":    #$type == "update"
    if ($type == "update") {
        delete_cache_file("product");
    }
    break;
    case "design_eachtitleimage.php":    #$type == "color" || $type == "titleimage" || $type == "delete", main
    if ($type == "color" || $type == "titleimage" || $type == "delete") {
        delete_cache_file("main");
    }
    break;
    case "design_eachtopmenu.php":    #$type == "update" || $type == "delete", main, product
    case "design_eachleftmenu.php":    #$type == "update" || $type == "delete", main, product
    case "design_eachbottom.php":    #$type == "update" || $type == "delete", main, product
    case "design_eachbottomtools.php":    #$type == "update" || $type == "delete", main, product
    if ($type == "update" || $type == "delete") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "design_eachmain.php":    #$type == "update" || $type == "delete", main
    if ($type == "update" || $type == "delete") {
        delete_cache_file("main");
    }
    break;
    case "design_eachsection.php":    #$type == "update" || $type == "delete", product
    if ($type == "update" || $type == "delete") {
        delete_cache_file("product");
    }
    break;
    case "design_eachplist.list.php":    #$type == "update" || $type == "delete", product
    if ($mode == "update" || $mode == "delete") {
        delete_cache_file("product");
    }
    break;
    case "design_eachpdetail.list.php":    #$type == "update" || $type == "delete", product
    if ($mode == "update" || $mode == "delete") {
        delete_cache_file("product");
    }
    break;
    case "design_easyleft.php": #strlen($type) > 0, main, product
    case "design_easycss.php": #strlen($type) > 0, main, product
    if (ord($type) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "community_list.php":#$type == "up", main, product
    if ($type == "up") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "community_basicinfo_pop.php":#$mode == "modify", main, product
    case "community_specialinfo_pop.php":#$mode == "modify", main, product
    if ($mode == "modify") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "community_register.php":#$mode == "insert", main, product
    if ($mode == "insert") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "community_order_pop.php":#$mode == "sequence", main, product
    if ($mode == "sequence") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;

    case "market_notice.php":#strlen($type) > 0, main, product
    case "market_quickmenu.php":#strlen($type) > 0, main, product
    case "market_newproductview.php":#strlen($type) > 0, main, product
    if (ord($type) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "market_contentinfo.php":#strlen($type) > 0, main
    case "market_survey.php":#strlen($type) > 0, main
    case "market_eventpopup.php":#strlen($type) > 0, main
    if (ord($type) > 0) {
        delete_cache_file("main");
    }
    break;
    case "market_eventcode.add.php":#strlen($type) > 0, product
    if (ord($mode) > 0) {
        delete_cache_file("product");
    }
    break;
    case "product_code.property.php": #strlen($mode) > 0, main, product
    case "product_code.process.php": #strlen($mode) > 0, main, product
    if (ord($mode) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "product_mainlist.main.php": #strlen($mode) > 0, main
    if (ord($mode) > 0) {
        delete_cache_file("main");
    }
    break;
    case "product_register.add.php": #strlen($mode) > 0, main, product
    case "product_codelist.main.php": #strlen($mode) > 0, main, product
    if (ord($mode) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "product_sort.list.php": #strlen($mode) > 0, product
    case "product_copy.list.php": #strlen($mode) > 0, product
    case "product_theme.T.list.php": #strlen($mode) > 0, product
    case "product_theme.L.list.php": #strlen($mode) > 0, product
    if (ord($mode) > 0) {
        delete_cache_file("product");
    }
    break;
    case "product_allupdate.list.php":    #$mode == "update", main, product
    case "product_reserve.php":    #$mode == "update", main, product
    case "product_price.php":        #$mode == "update", main, product
    if ($mode == "update") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "product_allsoldout.list.php":    #strlen($mode) > 0, main, product
    if (ord($mode) > 0) {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "order_tempinfo.php":    #$type == "restore" && strlen($ordercode) >= 12
    if ($type == "restore" && strlen($ordercode) >= 12) {
        delete_cache_file("main");
        delete_cache_file("product", "code=".substr($ordercode,0,3));
    }
    break;
    case "product_brand.php":    #$type == "save"
    if ($type == "save" || $type == "up") {
        delete_cache_file("main");
        delete_cache_file("product");
    }
    break;
    case "design_blist.php":    #$mode == "update"
    case "design_blist.list.php":    #$mode == "update"
    case "design_eachblist.php":    #$mode == "update,delete,clear"
    case "design_eachblist.list.php":    #$mode == "update,delete,clear"
    if ($mode == "update" || $mode == "delete" || $mode == "clear") {
        delete_cache_file("productb");
    }
    break;
    default :
    break;
}
