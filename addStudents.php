<?php
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.
include "top.php";
$debug = false;

if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;

//print"()()(";
//print $yourURL;
//print"()()(";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form

$firstName = "";
$lastName = "";
$grade = "grade";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
    // SECTION: 1d form error flags
//
    // Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$firstNameERROR = false;
$lastNameERROR = false;
$gradeERROR = false;
$duplicateERROR = false;

//Sanitize: SECTION 2c.

$firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $firstName;

$lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $lastName;

$grade = htmlentities($_POST["selectGrade"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $grade;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to the database
//$dataRecord = array($dataRecord);

$mailed = false; // have we mailed the information to the user?
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnAdd"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // 
    if (!securityCheck($path_parts, $yourURL,true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to have extra character.";
        $firstNameERROR = true;
    }

    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to have extra character.";
        $lastNameERROR = true;
    }

     if ($_POST["selectGrade"]=="grade") {
         $errorMsg[] = "Please select a grade for your student.";
         $gradeERROR = true;
     } 
   
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        
        if ($debug)
            print "<p>Form is valid</p>";

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //
        // This block saves the data to a database.
        // check if student is already in database
        $checkStudent = "SELECT fldStdtFirstName from  tblStudent WHERE fldStdtFirstName = ? AND fldStdtLastName = ? AND fldGrade = ?";
        $student[] = $firstName;
        $student[] = $lastName;
        $student[] = $grade;
        
        $status = $thisDatabaseReader->select($checkStudent, $student, 1, 2, 0, 0, false, false);
        
        if (!empty($status)) {
            $duplicateERROR = true;
        }
        else {
        $query = "INSERT INTO tblStudent SET fldStdtFirstName=?, fldStdtLastName=?,"
                . " fldGrade=?";
        
        $thisDatabaseWriter->insert($query, $dataRecord, 0, 0, 0, 0, false, false);
        
        // get teacher's username
        $username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8"); 
        $data[] = $username;
        // get student id
        $primarykey = $thisDatabaseWriter->lastInsert();
        
        $data[] = $primarykey;
        $query2 = "INSERT INTO tblTeacherStudent SET fnkUsername=?, fnkStudentId=?";
        $thisDatabaseWriter->insert($query2, $data, 0, 0, 0, false, false);
        }
   
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

<?php
//####################################
//
    // SECTION 3a.
//


// display form
    ?>

<form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">

            <fieldset class="wrapper">
                <legend><span id="welcome">Add students</span></legend>
                 <?php if ($errorMsg) {
                        print '<p class = "mistake">*There appears to be missing information from required field(s).</p>';
                        
                }   if($duplicateERROR){
                    print '<p class = "mistake">'.$firstName.' '.$lastName.' in '.$grade. ' grade is already saved in your records.</p>';
                }
                ?>
                
                
              <!--  <fieldset class="wrapperTwo">-->

                    <fieldset class="student">
                        <!--<legend></legend>-->
                        <label for="txtFirstName" class="required"></label>
                        <input type="text" id="txtFirstName" name="txtFirstName"
                               value="<?php print $firstName; ?>"
                               tabindex="100" maxlength="45" placeholder="First Name"
    <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               autofocus>
                        

                        <label for="txtLastName" class="required"></label>
                        <input type="text" id="txtLastName" name="txtLastName"
                               value="<?php print $lastName; ?>"
                               tabindex="110" maxlength="45" placeholder="Last Name"
    <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               autofocus>
                        	
                        <!--<legend id="listbox">Grade:</legend>-->
                        <select id="selectGrade" 
                                name="selectGrade" 
                                tabindex="520" 
                                <?php if ($gradeERROR) print 'class="mistake"'; ?>>
                             <option <?php if($grade=="grade") print " selected "; ?>
                                value="grade">Grade</option>
                             
                            <option <?php if($grade=="K") print " selected "; ?>
                                value="K">K</option>

                            <option <?php if($grade=="1") print " selected "; ?>
                                value="1">1st</option>

                            <option <?php if($grade=="2") print " selected "; ?>
                                value="2">2nd</option>
                            
                            <option <?php if($grade=="3") print " selected "; ?>
                                value="3">3rd</option>
                            
                            <option <?php if($grade=="4") print " selected "; ?>
                                value="4">4th</option>
                            
                            <option <?php if($grade=="5") print " selected "; ?>
                                value="5">5th</option>
                            
                            <option <?php if($grade=="6") print " selected "; ?>
                                value="6">6th</option>
                            
                            <option <?php if($grade=="7") print " selected "; ?>
                                value="7">7th</option>
                            
                            <option <?php if($grade=="8") print " selected "; ?>
                                value="8">8th</option>
                            
                             <option <?php if($grade=="9") print " selected "; ?>
                                value="9">9th</option>
                             
                              <option <?php if($grade=="10") print " selected "; ?>
                                value="10th">10th</option>
                              
                               <option <?php if($grade=="11") print " selected "; ?>
                                value="11th">11th</option>
                               
                                <option <?php if($grade=="12") print " selected "; ?>
                                value="12th">12th</option>
                            
                        </select>          
                        
                        <input type="submit" class = "submitButton" id="btnAdd" name="btnAdd" value="Add +" tabindex="">

                    </fieldset> <!-- ends contact -->
                    <?php
                    // If its the first time coming to the form or there are errors we are going
                    // to display the form.
                   if (isset($_POST["btnAdd"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    
                   // get teacher's username
                   $username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
                   $values = array($username);
                   $showStudents = "SELECT fldStdtFirstName, fldStdtLastName, fldGrade, fldClass, fldRdgLevel from  tblStudent JOIN tblTeacherStudent WHERE pmkStudentId = fnkStudentId AND fnkUsername = ?";

                   $students = $thisDatabaseReader->select($showStudents, $values, 1, 1, 0, 0, false, false);
                
                   print'<table>';
                   print '<tr>';
                        print '<th>First</th>';
                        print '<th>Last</th>';
                        print '<th>Grade</th>';
                        print '<th>Class</th>';
                        print '<th>Reading Level</th>';
                   print '</tr>';
                   foreach($students as $student) {
                       if ($student[0]==$firstName) print '<tr class = "highlight">';
                       else print '<tr>';
                       for($i=0; $i<5; $i++) {
                          print '<td>';
                          print $student[$i];
                          print '</td>';
                       }
                       
                       print '</tr>';
                   }
                   print'</table>';

               } ?>
                    
                    
                </fieldset> 

            <!--</fieldset>-->
        </form>
    <?php
 // end body submit
?>

</article>