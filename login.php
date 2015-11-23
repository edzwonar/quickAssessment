<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");

$query = "SELECT fldTeacherFirstName, fldTeacherLastName FROM tblTeacher WHERE pmkUsername = ?";
$data = array($username);
$clients = $thisDatabaseReader->select($query,$data,1,0,0,0,false,false);

if(empty($clients)) {
    /*$createTeacher = "INSERT INTO tblTeacher (pmkUsername) VALUES"
            . "(?)";
    $thisDatabaseWriter->insert($createTeacher, $data, 0, 0, 0, 0, false, false);
     */
     include "newTeacher.php";
}


/*if(count($client) > 0) {
 
    $createTeacher = "INSERT INTO tblTeacher (pmkUsername) VALUES"
            . "('?')";
    print $thisDatabaseWriter->testquery($createTeacher, $data, 0, 0, 2, 0, false, false);
    //$thisDatabaseWriter->insert($createTeacher, $values = "", $wheres = 0, $conditions = 0, $quotes = 0, $symbols = 0, $spacesAllowed = false, $semiColonAllowed = false);
  }
 else {
     print 'hi';
 }*/
  
?>