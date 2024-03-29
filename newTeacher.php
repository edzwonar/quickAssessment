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
$email = "";
$district = "";
$school = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
    // SECTION: 1d form error flags
//
    // Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$districtERROR = false;
$schoolERROR = false;

//Sanitize: SECTION 2c.

$username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $username;

$firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $firstName;

$lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $lastName;

$email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
$dataRecord[] = $email;

$district = htmlentities($_POST["txtDistrict"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $district;

$school = htmlentities($_POST["txtSchool"], ENT_QUOTES, "UTF-8");
$dataRecord[] = $school;


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
if (isset($_POST["btnSubmit"])) {

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

    if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $emailERROR = true;
    }

    if ($district == "") {
        $errorMsg[] = "Please enter your district";
        $districtERROR = true;
    } elseif (!verifyAlphaNum($district)) {
        $errorMsg[] = "Your district appears to be incorrect.";
        $districtERROR = true;
    }

    if ($school == "") {
        $errorMsg[] = "Please enter your school";
        $schoolERROR = true;
    } elseif (!verifyAlphaNum($school)) {
        $errorMsg[] = "Your school appears to be incorrect.";
        $schoolERROR = true;
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
        
        $query = "INSERT INTO tblTeacher SET pmkUsername=?, fldTeacherFirstName=?, fldTeacherLastName=?,"
                . " fldEmail=?, fldSchoolDistrict=?, fldSchool=?";
        
        $thisDatabaseWriter->insert($query, $dataRecord, 0, 0, 0, 0, false, false);
       
       
       
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2f Create message
        //
        // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).

        $message = '<h2>Your information.</h2>';

        foreach ($_POST as $key => $value) {

            $message .= "<h3>";

            $camelCase = preg_split('/(?=[A-Z])/', substr($key, 3));

            foreach ($camelCase as $one) {
                $message .= $one . " ";
            }
            $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</h3>";
        }


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $email; // the person who filled out the form
        $cc = "";
        $bcc = "";
        $from = "<noreply@yoursite.com>";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "Sign-up Confirmation: " . $todaysDate;
        
        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
   
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
    // 
// 
// 
// If its the first time coming to the form or there are errors we are going
// to display the form.
if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
   /* 
    print "<h2>Your Request has ";

    if (!$mailed) {
        print "not ";
    }

    print "been processed</h2>";

    print "<h3>A copy of this message has ";
    if (!$mailed) {
        print "not ";
    }
    print "been sent</p>";
    print "<h3>To: " . $email . "</h3>";
    print "<h3>Mail Message:</h3>";

    print $message;
  */
    
} else {


    //####################################
    //
        // SECTION 3b Error Messages
    //
        // display any error messages before we print out the form

    if ($errorMsg) {
        print '<div id="errors">';
        print "<ul>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ul>\n";
        print '</div>';
    }


    //####################################
    //
        // SECTION 3c html Form
    //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
      is defined in top.php
      NOTE the line:

      value="<?php print $email; ?>

      this makes the form sticky by displaying either the initial default value (line 35)
      or the value they typed in (line 84)

      NOTE this line:

      <?php if($emailERROR) print 'class="mistake"'; ?>

      this prints out a css class so that we can highlight the background etc. to
      make it stand out that a mistake happened here.

     */
    ?>

        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">

            <fieldset class="wrapper">
                <legend><span id="welcome">Welcome to My Quick Assessment Grader</span></legend>
                 <?php if ($errorMsg) {
                        print '<p class = "mistake">*There appears to be missing information from required field(s).</p>';
                        
                }
                else {
                    print "<p id='signup'>It doesn't look like you have an account yet.  Please register below.</p>";
                }?>
                
                
              <!--  <fieldset class="wrapperTwo">-->

                    <fieldset class="contact">
                        <!--<legend></legend>-->
                        <label for="txtFirstName" class="required"><span <?php if ($firstNameERROR) print 'class="mistake"'; ?>>*</span></label>
                        <input type="text" id="txtFirstName" name="txtFirstName"
                               value="<?php print $firstName; ?>"
                               tabindex="100" maxlength="45" placeholder="First Name"
    <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               autofocus>
                        

                        <label for="txtLastName" class="required"><span <?php if ($lastNameERROR) print 'class="mistake"'; ?>>*</span></label>
                        <input type="text" id="txtLastName" name="txtLastName"
                               value="<?php print $lastName; ?>"
                               tabindex="110" maxlength="45" placeholder="Last Name"
    <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()"
                               autofocus>
                        <br/>

                        <label for="txtEmail" class="required" id="labelEmail"><span <?php if ($emailERROR) print 'class="mistake"'; ?>>*</span></label>
                        <input type="text" id="txtEmail" name="txtEmail"
                               value="<?php print $email; ?>"
                               tabindex="120" maxlength="45" placeholder="Email"
    <?php if ($emailERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()" 
                               >
                        <br>
                        <label for="txtDistrict" class="required" id="labelDistrict"><span <?php if ($districtERROR) print 'class="mistake"'; ?>>*</span></label>
                        <input type="text" id="txtDistrict" name="txtDistrict"
                               value="<?php print $district; ?>"
                               tabindex="120" maxlength="45" placeholder="District"
    <?php if ($districtERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()" 
                               >
                        <br>
                        <label for="txtSchool" class="required" id="labelSchool"><span <?php if ($schoolERROR) print 'class="mistake"'; ?>>*</span></label>
                        <input type="text" id="txtSchool" name="txtSchool"
                               value="<?php print $district; ?>"
                               tabindex="120" maxlength="45" placeholder="School"
    <?php if ($schoolERROR) print 'class="mistake"'; ?>
                               onfocus="this.select()" 
                               >

                    </fieldset> <!-- ends contact -->
                    
                     <!--<legend></legend>-->
                    <input type="submit" class = "submitButton" id="btnSubmit" name="btnSubmit" value="Register" tabindex="" onclick='this.form.action="index.php";'>
                    
                </fieldset> 

            <!--</fieldset>-->
        </form>
    <?php
} // end body submit
?>

</article>
