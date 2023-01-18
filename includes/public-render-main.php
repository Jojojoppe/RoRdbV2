<?php

function rordbv2_render_main(){
    $ret = "";

    // Check rordb_action
    //      rordb_add -> create item
    //      rordb_help -> show help page
    //      rordb_edit -> edit something
    //          check rordb_edit (use id rordb_id
    //              cat -> edit category
    //              loc -> edit location        // both cat and loc should use same function but with catloc variable set
    //              item -> edit item
    //      NULL -> show main page (search items)
    
    if(isset($_GET['rordb_action'])){
        if($_GET['rordb_action']=="rordb_add"){
            ob_start();
            include("public-add.php");
            $ret .= ob_get_contents();
            ob_end_clean();
        }else if($_GET['rordb_action']=="rordb_help"){
            ob_start();
            include("public-help.php");
            $ret .= ob_get_contents();
            ob_end_clean();
        }else if($_GET['rordb_action']=="rordb_edit"){
            if(isset($_GET['rordb_edit'])){
                if($_GET['rordb_edit']=="cat" || $_GET['rordb_edit']=="loc"){
                    ob_start();
                    include("public-edit-catloc.php");
                    $ret .= ob_get_contents();
                    ob_end_clean();
                }else if($_GET['rordb_edit']=="item"){
                    ob_start();
                    include("public-edit-item.php");
                    $ret .= ob_get_contents();
                    ob_end_clean();
                }else{
                    $ret .= "Some error occured... unknown edit type";
                }
            }else{
                $ret .= "Some error occured... unknown action type";
            }
        }
    }else{
        ob_start();
        include("public-home.php");
        $ret .= ob_get_contents();
        ob_end_clean();
    }

    return $ret;
}
