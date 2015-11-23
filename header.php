<!-- %%%%%%%%%%%%%%%%%%%%%%     Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->

<header>
    <h1>Jackson and Emilie's awesome final project: helping kids read good</h1>
</header>

<?php 
$username = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
        
print "<p>Welcome ". $username."</p>";

        
?>



<!-- %%%%%%%%%%%%%%%%%%%%% Ends Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->