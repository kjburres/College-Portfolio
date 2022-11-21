<?php
    session_start();     //https://stackoverflow.com/questions/2542427/how-do-i-continue-a-session-from-one-page-to-another-with-php
?>
<HTML>

<HEAD>
    <! Include the CSS File for this page:>
    <link rel="stylesheet" href="dashboard.css">
    
    <! Install Bootstrap:>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">



    

</HEAD>

<BODY>

    <?php

        //Connect to the server: 

        include_once('dbConnect.php');
        $link = new mysqli($dbhost,$dbuser,$dbpass,$dbname); 

        if ($link->connect_error) {
            print "There was a problem connecting to the database.<BR/>";
            print $link->connect_errno.": {$link->connect_error}";
        } 


        if(isset($_SESSION['user'])){
            $userID = $_SESSION['user'];
            $studentID;




        ?>

        <! Nav Bar:>
        <div id="navBar">
            <span><a href="dashboard.php"><img id="hcaLogo" src="https://upload.wikimedia.org/wikipedia/commons/1/14/Hawthorne_Christian_Maroon_Logo.gif"></a></span>
            <a href="dashboard.php"><span class="navBarButton">dashboard</span></a>
        <?php
        print"<a href=\"personalInfo.php?userID=" . $userID . "\"><span class=\"navBarButton\">personal info</span></a>";
        ?>
            
        </div> <! End Nav Bar>




        <?php
            
            
            //If the user is a parent, print a menu for them to select the student they are viewing.
            if($_SESSION['userType'] == "P"){
                
                print "<div class=\"container\">
                        <ul class=\"nav nav-tabs\" id=\"childSelector\">";
                
                //Get the children of the parent from the database
                $childrenReq = "SELECT FirstName, UserID FROM `Users` as u
                                JOIN `AuthorizedViewers` as av ON UserID = StudentID
                                WHERE ParentID = {$userID}";
                $children = $link->query($childrenReq);
                
                //if the query returns correctly, put the children into the tab selector
                if($children == true){
                    //CHECK HERE IF THERE IS MORE THAN 1 CHILD
                    //OR DOES IT MATTER?
                    $childNum = 0;
                    while($row = $children->fetch_assoc()){
                        $childNum++;
                        
                        //For each child, print the beginning HTML for the selector tabs:
                        print "<li class=\"nav-item\" id=\"child" . $row["UserID"] . "Tab\" onClick=\"changeChild(".$row["UserID"].")\">
                                    <a class=\"nav-link";
                        
                        //Set the first tab to active
                        if($childNum == 1){
                            print " active";
                            $studentID = $row["UserID"];    //Set the studentID to child 1 so child 1's classes will show.
                        }
                        
                        print "\" aria-current=\"page\" href=\"#\">" . $row["FirstName"] . "</a>
                               </li>";
                        
                    }
                    print "</ul>
                        </div>";     //Close the tab selector
                    
                } //End if query true
                
                        
                
            }else{
                $studentID = $userID;
            } // End if parent
                
                       
            
            //This is only for if the user is a student, or for after the parent has selected a child.
            //https://www.w3schools.com/nodejs/nodejs_mysql_select.asp ??
            



            //Get the classes from the database
            $classReq = "SELECT CourseName, GradeLevel, cs.SectionID, SectionNumber FROM `CourseSections` as cs 
                         JOIN `SectionEnrollments` as se ON cs.SectionID = se.SectionID 
                         JOIN `Users` as u ON se.PersonID = u.UserID 
                         WHERE u.UserID = ".$studentID.";";
            
            $classResult = $link->query($classReq);

            if($classResult == true){

                //insert the HTML class container:
                print"<div class=\"container\" id=\"classContainer\">
                        <div class=\"row\">";



                //Insert the html for each class: 
                $classNum = 0;
                while($row = $classResult->fetch_assoc()){
                    $classNum++;

                    //Set variables already available to go into the html.
                    $sectionID = $row["SectionID"];
                    $sectionCode = $row["GradeLevel"] . " - " . $row["SectionNumber"];
                    $sectionName = $row["CourseName"];
                    
                    //Find the section instructor
                    $instructorReq = "SELECT FirstName, LastName FROM Users as u 
                                    JOIN SectionEnrollments as se
                                    ON u.UserID = se.PersonID
                                    WHERE u.userType = \"F\" AND se.SectionID =" . $row["SectionID"] . ";";
                    $instructor = $link->query($instructorReq)->fetch_assoc();
                    
                    //CONTINUING HTML:

                    /*QUESTION:
                        When the div I'm creating below is clicked, how do I make it so that it not only redirects to sectionView.php, but also sets a $_SESSION variable with the sectionID
                        So that I know what class to query from the database in sectionView.php??? So far I've looked at calling a php function from a javascript function from an 
                        onClick attribute, using ajax (although I'm not too familiar with it), Making this whole section (started in line 27) a form with different submit buttons, etc.
                        But none of them have worked for me so far. I may have done them wrong and would be willing to try any of them again!
                    */
                    print " <div class=\"col\">
                                <a href=\"sectionView.php?sectionID=" . $sectionID . "\">
                                    <button class=\"section\" id=\"class" . $classNum . "\">
                                        <div class=\"sectionCode\">". $sectionCode." </div>
                                        <div class=\"sectionName\">" . $sectionName . "</div>
                                        <div class=\"instructor\">" . $instructor["FirstName"] . " " . $instructor["LastName"] . "</div>
                                    </button>  
                                </a>
                            </div>";        
                            //https://stackoverflow.com/questions/8660149/need-to-make-a-clickable-div-button
                    
                }
                
                print    "</div> <!End Row>    
                </div> <!End Container>";
                

            }else{      //If query true
                echo "There are no registered courses for this student";
            }
            
            
        }else{
            echo "Well, you've made it, but the session ended :(";
        }

        $link -> close();
    ?>







  

</BODY>

</HTML>