<?php

//##############################################################################
//
// main home page for the site 
// 
//##############################################################################
include "top.php";
$username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");

$query = "SELECT fldTeacherFirstName, fldTeacherLastName FROM tblTeacher WHERE pmkUsername = ?";
$data = array($username);
$clients = $thisDatabaseReader->select($query,$data,1,0,0,0,false,false);

if(empty($clients)) {
  
    include "newTeacher.php";

}else{
    print"Clients is full";
}

include "footer.php"; ?>

