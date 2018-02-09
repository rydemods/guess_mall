<style>
#tag1 {color:#Ffffff;font-weight:bold;font-size:20px;letter-spacing:-1}
a#tag1:link {background-color:#12D763;font-weight:bold;color:#Ffffff;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}
a#tag1:visited {background-color:#12D763;font-weight:bold;color:#Ffffff;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}
a#tag1:hover {background-color:#1E4F55;font-weight:bold;color:#D9FFE5;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag2 {color:#3D7B66;font-weight:bold;font-size:18px;letter-spacing:-1}
a#tag2:link {color:#3D7B66;font-weight:bold}
a#tag2:visited {color:#3D7B66;font-weight:bold}
a#tag2:hover {background-color:#1E4F55;color:#D9FFE5;font-weight:bold;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag3 {color:#00B4B5;font-weight:bold;font-size:15px;letter-spacing:-1}
a#tag3:link {color:#00B4B5;font-weight:bold}
a#tag3:visited {color:#00B4B5;font-weight:bold}
a#tag3:hover {background-color:#1E4F55;color:#D9FFE5;font-weight:bold;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}

#tag4 {color:#7C8A8D;line-height:260%;letter-spacing:-1}
#tag4 a:link {color:#7C8A8D}
#tag4 a:visited {color:#7C8A8D}
#tag4 a:hover {background-color:#1E4F55;color:#D9FFE5;text-decoration:none;selector-dummy:expression(this.hideFocus=true)}		

#tagspace {color:#cccccc;margin:0 6 0 10}
</style>

<?
if($num=strpos($body,"[TAGSEARCHINPUT_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$input_style=$s_tmp[1];
}
if(strlen($input_style)==0) $input_style="width:300px";
$tagsearchinput = "<input type=text name=searchtagname style=\"".$input_style."\" maxlength=50 onkeydown=\"CheckKeyTagSearch()\" onkeyup=\"check_tagvalidate(event, this);\">";


$prtaglist="";
$i=0;
while(@list($key,$val)=@each($taglist)) {
	if($i>0) $prtaglist.="<span id=tagspace>|</span>";
	$prtaglist.="<A id=tag".$tagkey[$key]["rank"]." href=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$tagkey[$key]["tagname"]."')\" onmouseover=\"window.status='".$tagkey[$key]["tagname"]."';return true;\" onmouseout=\"window.status='';return true;\">".$tagkey[$key]["tagname"]."</A>";
	$i++;
}
$tagsearchok="\"javascript:void(0)\" onclick=\"tagCls.searchProc()\"";


$pattern=array("[TAGDATESTART]","[TAGDATEEND]","[TAGSORT1]","[TAGSORT2]","[TAGLIST]","[TAGSEARCHINPUT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}]","[TAGSEARCHOK]");
$replace=array($start_date,$end_date,"\"javascript:void(0)\" onclick=\"tagCls.changeSort('name')\"","\"javascript:void(0)\" onclick=\"tagCls.changeSort('best')\"",$prtaglist,$tagsearchinput,$tagsearchok);
$body = str_replace($pattern,$replace,$body);

echo $body;
