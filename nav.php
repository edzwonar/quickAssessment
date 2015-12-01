<!-- ######################     Main Navigation   ########################## -->
<nav>
    <ul>
        <?php
        /* This sets the current page to not be a link. Repeat this if block for
         *  each menu item */
        if ($path_parts['filename'] == "index") {
            print '<li class="activePage">Home</li>';
        } else {
            print '<li><a href="index.php">Home</a></li>';
        }
        
       /* Quick Assessment Link */
        if ($path_parts['filename'] == "assessment") {
            print '<li class="activePage">Quick Assessment</li>';
        } else {
            print '<li><a href="assessment.php">Quick Assessment</a></li>';
        }
        
        /* Add Students Link */
        if ($path_parts['filename'] == "addStudents") {
            print '<li class="activePage">Add Students</li>';
        } else {
            print '<li><a href="addStudents.php">Add Students</a></li>';
        }
        
        if ($path_parts['filename'] == "gradebook") {
            print '<li class="activePage">My Gradebook</li>';
        } else {
            print '<li><a href="gradebook.php">My Gradebook</a></li>';
        }
        ?>
    </ul>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

