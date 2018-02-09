<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	exit;
}

/*
쇼핑몰 태그 목록
*/

header("Cache-Control: no-cache, must-revalidate"); 
header("Content-Type: text/xml; charset=utf-8");

$type=$_GET["type"];
$prcode=$_GET["prcode"];
$org_tagname=$_GET["tagname"];

$sql = "SELECT tag FROM tblproduct WHERE productcode='{$prcode}' ";
$result=pmysql_query($sql,get_db_conn());
$taglist="";
$cnt=0;
if($row=pmysql_fetch_object($result)) {
	if($type=="del" && ord($org_tagname) && strlen($prcode)==18) {
		if(_DEMOSHOP=="OK" && $_SERVER['REMOTE_ADDR']!=_ALLOWIP) {

		} else {
			$deltagname="<{$org_tagname}>,";
			$row->tag=str_replace($deltagname,"",$row->tag);
			$sql = "UPDATE tblproduct SET tag='{$row->tag}' WHERE productcode='{$prcode}' ";
			pmysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tbltagproduct WHERE productcode='{$prcode}' AND tagname='{$org_tagname}' ";
			pmysql_query($sql,get_db_conn());

			$sql = "SELECT COUNT(*) as count FROM tbltagproduct WHERE tagname='{$org_tagname}' ";
			$result2=pmysql_query($sql,get_db_conn());
			$row2=pmysql_fetch_object($result2);
			pmysql_free_result($result2);
			if($row2->count==0) {
				$sql = "DELETE FROM tbltagsearch WHERE tagname='{$org_tagname}' ";
				pmysql_query($sql,get_db_conn());
				$sql = "DELETE FROM tbltagsearchall WHERE tagname='{$org_tagname}' ";
				pmysql_query($sql,get_db_conn());

				DeleteCache("tbltagsearch".date("Ymd").".cache");
			}
		}
	}

	if(ord($row->tag)) {
		$tag=explode(">,",$row->tag);
		for($i=0;$i<count($tag);$i++) {
			$cnt++;
			if($cnt==count($tag)) {
				$tagname=trim($tag[$i],'<>,');
			} else {
				$tagname=ltrim($tag[$i],'<>,');
			}

			if(ord($tagname)) {
				$taglist.="<a href=\"javascript:delTagName('{$prcode}','{$tagname}');\"><img src=images/x.gif border=0 hspace=2></a>{$tagname}, ";
			}
		}
	}
}
pmysql_free_result($result);

if(ord($taglist)==0) {
	$taglist="등록된 태그가 없습니다.";
}
echo "<div style=\"line-height:13pt\">\n";
echo "	{$taglist}\n";
echo "</div>";
