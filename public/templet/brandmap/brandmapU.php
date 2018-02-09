<?
if(strlen($searchValue)==0 && strpos($body,"[IFBRANDMAP]")!=0) {
	$ifbrandmap=strpos($body,"[IFBRANDMAP]");
	$endbrandmap=strpos($body,"[IFENDBRANDMAP]");
	$body=substr($body,0,$ifbrandmap).substr($body,$endbrandmap+15);
}

include("productbmap_text.php");

$pattern=array(
	"[SEARCHBRAND]",
	"[KORBRAND]",
	"[ENGBRAND]",
	"[KORSEARCHBAR]",
	"[ENGSEARCHBAR]",
	"[SEARCHBAR]",
	"[IFBRANDMAP]",
	"[IFENDBRANDMAP]"
);
$replace=array($searchresult,$brandhangul,$brandalphabet,$korsearchbar,$engsearchbar,$searchbar,"","");

$body=str_replace($pattern,$replace,$body);
echo $body;
