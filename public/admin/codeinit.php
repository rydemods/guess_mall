<?php // hspark
$i           = 0;
$ii          = 0;
$iii         = 0;
$iiii        = 0;
$strcodelist = "<script>\n";
$result       = pmysql_query($sql,get_db_conn());
$selcode_name = "";
while ($row = pmysql_fetch_object($result)) {
    $tmpcode = $row->code_a.$row->code_b.$row->code_c.$row->code_d;

    $strcodelist .= "var clist=new CodeList();
    clist.code='{$tmpcode}';
    clist.code_a='{$row->code_a}';
    clist.code_b='{$row->code_b}';
    clist.code_c='{$row->code_c}';
    clist.code_d='{$row->code_d}';
    clist.type='{$row->type}';
    clist.code_name='".str_replace("'","`",$row->code_name)."';
    clist.list_type='{$row->list_type}';
    clist.detail_type='{$row->detail_type}';
    clist.sort='{$row->sort}';
    clist.group_code='{$row->group_code}';";
    $selected = "false";
    $display  = "none";
    $open     = "close";
    if ($row->type == "L" || $row->type == "T" || $row->type == "LX" || $row->type == "TX") {
        if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c && $code_d == $row->code_d) {
            $selected = "true";
            $strcodelist .= "seltype='{$row->type}';\n";
            $strcodelist .= "selcode='{$code}';\n";
        }
        if ($code_a == $row->code_a) {
            $display = "show";
            $selcode_name .= $row->code_name;
        }
        if ($code_a == $row->code_a && $code_b != "000") {
            $open = "open";
        }
        $strcodelist .= "clist.selected={$selected};\n";
        $strcodelist .= "clist.display='{$display}';\n";
        $strcodelist .= "clist.open='{$open}';\n";
        //$strcodelist .= "lista[{$i}] = clist;\n";
        $strcodelist .= "listaloop[listacount.toString()]=clist;\n";
        $strcodelist .= "listacount=Number(listacount)+1;\n";
        $i++;
    }
    if ($row->type == "LM" || $row->type == "TM" || $row->type == "LMX" || $row->type == "TMX") {
        if ($row->code_c == "000" && $row->code_d == "000") {
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c && $code_d == $row->code_d) {
                $selected = "true";
                $strcodelist .= "seltype='{$row->type}';\n";
                $strcodelist .= "selcode='{$code}';\n";
            }
            if ($code_a == $row->code_a && $code_b != "000") {
                $display = "show";
            }
            if ($code_a == $row->code_a && $code_b == $row->code_b) {
                $selcode_name .= " > ".$row->code_name;
            }
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c != "000") {
                $open = "open";
            }
            $strcodelist .= "clist.selected={$selected};\n";
            $strcodelist .= "clist.display='{$display}';\n";
            $strcodelist .= "clist.open='{$open}';\n";
            //$strcodelist .= "listb[{$ii}] = clist;\n";
            $strcodelist .= "if(!listbcount[clist.code_a]) { listbcount[clist.code_a]=0; }\n";
            $strcodelist .= "listbloop[clist.code_a+listbcount[clist.code_a].toString()]=clist;\n";
            $strcodelist .= "listbcount[clist.code_a]=Number(listbcount[clist.code_a])+1;\n";
            $ii++;
        }else
        if ($row->code_d == "000") {
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c && $code_d == $row->code_d) {
                $selected = "true";
                $strcodelist .= "seltype='{$row->type}';\n";
                $strcodelist .= "selcode='{$code}';\n";
            }
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c != "000") {
                $display = "show";
            }
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c) {
                $selcode_name .= " > ".$row->code_name;
            }
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c && $code_d != "000") {
                $open = "open";
            }
            $strcodelist .= "clist.selected={$selected};\n";
            $strcodelist .= "clist.display='{$display}';\n";
            $strcodelist .= "clist.open='{$open}';\n";
            //$strcodelist .= "listc[{$iii}] = clist;\n";
            $strcodelist .= "if(!listccount[clist.code_a+clist.code_b]) { listccount[clist.code_a+clist.code_b]=0; }\n";
            $strcodelist .= "listcloop[clist.code_a+clist.code_b+listccount[clist.code_a+clist.code_b].toString()]=clist;\n";
            $strcodelist .= "listccount[clist.code_a+clist.code_b]=Number(listccount[clist.code_a+clist.code_b])+1;\n";
            $iii++;
        }else
        if ($row->code_d != "000") {
            if ($code_a == $row->code_a && $code_b == $row->code_b && $code_c == $row->code_c && $code_d == $row->code_d) {
                $strcodelist .= "seltype='{$row->type}';\n";
                $strcodelist .= "selcode='{$code}';\n";
                $selected = "true";
                $display  = "show";
                $open     = "open";
                $selcode_name .= " > ".$row->code_name;
            }
            $strcodelist .= "clist.selected={$selected};\n";
            $strcodelist .= "clist.display='{$display}';\n";
            $strcodelist .= "clist.open='{$open}';\n";
            //$strcodelist .= "listd[{$iiii}] = clist;\n";
            $strcodelist .= "if(!listdcount[clist.code_a+clist.code_b+clist.code_c]) { listdcount[clist.code_a+clist.code_b+clist.code_c]=0; }\n";
            $strcodelist .= "listdloop[clist.code_a+clist.code_b+clist.code_c+listdcount[clist.code_a+clist.code_b+clist.code_c].toString()]=clist;\n";
            $strcodelist .= "listdcount[clist.code_a+clist.code_b+clist.code_c]=Number(listdcount[clist.code_a+clist.code_b+clist.code_c])+1;\n";
            $iiii++;
        }
    }
    $strcodelist .= "clist=null;\n\n";
    $strcodelist .= "selcode_name='".str_replace("'","`",$selcode_name)."';\n";
}
pmysql_free_result($result);
$strcodelist .= "CodeInit();\n";
$strcodelist .= "</script>\n";

echo $strcodelist;
