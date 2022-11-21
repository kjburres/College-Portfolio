<HTML>
<HEAD>
    <! Install Bootstrap:>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <link rel="stylesheet" href="personalInfo.css">

    <?php
        session_start();
    ?>


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



            //Print the Nav Bar:
                print "<! Nav Bar:>
                <div id=\"navBar\">
                    <span><a href=\"dashboard.php\"><img id=\"hcaLogo\" src=\"https://upload.wikimedia.org/wikipedia/commons/1/14/Hawthorne_Christian_Maroon_Logo.gif\"></a></span>
                    <a href=\"dashboard.php\"><span class=\"navBarButton\">dashboard</span></a>
                    <a href=\"personalInfo.php?sectionID=" . $userID . "\"><span class=\"navBarButton\">personal info</span></a>   
                </div> <! End Nav Bar>";


                //Print the starter HTML used for all users:
                print "<div class=\"container\" id=\"container\">
                        <div class=\"row\" id=\"mainRow\">

                            <! First Column (Name + Pic): >
                            <div class=\"col-4 parent columnMain\" id=\"mainCol1\">

                            <div class=\"row\" id=\"subRow1Col1\">
                                <div class=\"col\" id=\"subcol1_subRow1Col1\"><h4 id=\"personName\">" . $_SESSION["userFullName"] . "</h4></div>
                                <div class=\"row\"><img id=\"personPic\" src=\"https://www.nyfoundling.org/wp-content/uploads/2019/08/nyf-placeholder-male.jpg\"></div>
                            </div> <!Closes the subRow subRow1Col1>
                            
                            </div> <!closes the first column>
                            
                            <! Second Column (Contact Info): >
                                <div class=\"col-7 columnMain\" id=\"studentInfo\">
                                <! Row 1 in Info Section: >
                                <div class=\"row justify-content-between subRow\" id=\"infoSecRow1\">";
            
            
            
        


            if($_SESSION["userType"] == "F"){
                //Query the Database for the Faculty Information:

                $facEmailReq = "SELECT Email FROM `Users` WHERE UserID = " . $userID;
                $emailRes = $link -> query($facEmailReq);
                
                $facClassReq = "SELECT CourseName, GradeLevel, SectionNumber, se.SectionID FROM `SectionEnrollments` as se 
                JOIN CourseSections as cs ON se.SectionID = cs.SectionID WHERE se.PersonID =" . $userID;
                $classRes = $link -> query($facClassReq);
                
                if($classRes == true && $emailRes == true){
                        $facEmail = $emailRes -> fetch_assoc();
                        
                        print"<div class=\"col-5\" id=\"infoSecRow1Col1\">
                            <div class=\"infoLabel\" id=\"emailLabel\">Email:</div>
                            <div class=\"info\" id=\"email\">". $facEmail["Email"] ."</div>

                            </div> <! closes column in the info section row 1>
                        </div> <!closes Row 1 of info (contact info)>


                        <! Row 2 in Info Section: >
                        <div class=\"row justify-content-start subRow\" id=\"infoSecRow2\">
                            <div class=\"col col-2\" id=\"infoSecRow2Col1\">
                            <div class=\"infoLabel\" id=\"classListLabel\">Classes:</div>
                            </div> <! Closes infoSecRow2Col1>
                            <div class=\"col\" id=\"infoSecRow2Col2\">
                            <div class=\"info\" id=\"clasList\">";
                    
                        while($row = $classRes -> fetch_assoc()){
                            print "<a href=\"sectionView.php?sectionID=" . $row["SectionID"] . "\"><div class=\"info class\">" . $row["GradeLevel"] . "_" . $row["SectionNumber"] . ": " . $row["CourseName"] . "</div></a>";
                            
                        }  
                }else{
                    echo "a query is bad";
                }

                

            }else if($_SESSION["userType"] == "P"){
                //Print the Parent info in the Parent Setup:
                
                //Query the database for the relevant information:
                $contactReq = "SELECT Address, City, State, Zipcode, Phone, Email FROM `Users` WHERE UserID = " .$userID;
                $contactRes = $link -> query($contactReq);

                $childReq = "SELECT FirstName, LastName FROM `Users` as u JOIN `AuthorizedViewers` as auth
                                    ON u.UserID = auth.StudentID
                                    WHERE auth.ParentID = " . $userID;
                $childRes = $link -> query($childReq);

                if($contactRes == true && $childRes == true){
                    $contactInfo = $contactRes -> fetch_assoc();

                    print"<! Row 1 in info section:>
                        <div class=\"col col-7\" id=\"infoSecRow1Col1\">
                        <div class=\"infoLabel\" id=\"addressLabel\">Address:</div> 
                        <div class=\"info\" id=\"address\">" . $contactInfo["Address"] . " " . $contactInfo["City"] . ", " . $contactInfo["State"] . " " . $contactInfo["Zipcode"] . "</div>
                        </div> <!closes column 1 of Row 1 in the info section>

                        <div class=\"col-5\" id=\"infoSecRow1Col2\">
                        <div class=\"infoLabel\" id=\"emailLabel\">Email:</div>
                        <div class=\"info\" id=\"email\">" . $contactInfo["Email"] . "</div>

                        </div> <! closes column 2 in the info section row 1>
                    </div> <!closes Row 1 of info (contact info)>


                    <! Row 2 in info section: >
                    <div class=\"row justify-content-start subRow\" id=\"infoSecRow2\">
                        <div class=\"col col-6\" id=\"infoSecRow2Col1\">
                            <div class=\"infoLabel\" id=\"HomePhoneLabel\">Home Phone:</div>
                            <div class=\"info\" id=\"HomePhone\">" . $contactInfo["Phone"] . "</div>
                        </div> <! closes the homePhone subColumn (infoSecRow2Col1)>
                        <div class=\"col-6\" id=\"infoSecRow2Col2\">
                            <div class=\"infoLabel\" id=\"WorkPhoneLabel\">Work Phone:</div>
                            <div class=\"info\" id=\"workPhone\"></div>
                        </div> <!closes the workPhone subColumn (infoSecRow2Col2)>
                    </div> <!closes infoSecRow2>

                    <! Row 3 in Info Section: >
                    <div class=\"row justify-content-start subRow\" id=\"infoSecRow3\">
                        <div class=\"col col-3\" id=\"infoSecRow3Col1\">
                        <div class=\"infoLabel\" id=\"classListLabel\">Children:</div>
                        </div>
                        <div class=\"col\" id=\"infoSecRow3Col2\">
                        <div class=\"info\" id=\"childList\">";

                        //Print out the Children Names:
                        while($child = $childRes -> fetch_assoc()){
                            print"<div class=\"info class\">" . $child["FirstName"] . " " . $child["LastName"] . "</div>";

                        }
                    
                    //So here, we're looking to close childList, infoSecRow3Col2, infoSecRow3, infoSec, the main row, and the container. 


                }else{
                    echo "There was a problem with the query";
                }

                
                

            }else if($_SESSION["userType"] == "S"){
                //Print the Student information in the Student format:

                //Query the Database for the Relevent Information:
                $contactReq = "SELECT Address, City, State, Zipcode, Phone, Email FROM `Users` WHERE UserID = " .$userID;
                $contactRes = $link -> query($contactReq);

                $parentReq = "SELECT UserID, FirstName, LastName FROM `Users` AS u JOIN `AuthorizedViewers` AS auth ON u.UserID = auth.ParentID WHERE auth.StudentID = " .$userID;
                $parentRes = $link -> query($parentReq);

                $classReq = "SELECT CourseName, GradeLevel, SectionNumber, se.SectionID FROM `SectionEnrollments` as se 
                JOIN CourseSections as cs ON se.SectionID = cs.SectionID WHERE se.PersonID =" . $userID;
                $classRes = $link -> query($classReq);

                if($contactRes == true && $parentRes == true && $classRes == true){
                    $contactInfo = $contactRes -> fetch_assoc();


                print"<div class=\"col col-7\" id=\"infoSecRow1Col1\">
                        <div class=\"infoLabel\" id=\"addressLabel\">Address:</div> 
                        <div class=\"info\" id=\"address\">" . $contactInfo["Address"] . " " . $contactInfo["City"] . ", " . $contactInfo["State"] . " " . $contactInfo["Zipcode"] . "</div>
                        </div> <!closes column 1 in the info section>

                        <div class=\"col-5\" id=\"infoSecRow1Col2\">
                        <div class=\"infoLabel\" id=\"emailLabel\">Email:</div>
                        <div class=\"info\" id=\"email\">" . $contactInfo["Email"] . "</div>

                        </div> <! closes column 2 in the info section row 1>
                    </div> <!closes Row 1 of info (contact info)>


                    <! Row 2 in info section: >
                    <div class=\"row justify-content-start subRow\" id=\"infoSecRow2\">
                        <div class=\"col col-2\" id=\"infoSecRow2Col1\">
                        <div class=\"infoLabel\" id=\"parentLabel\">Parent/Guardians:</div>
                        </div> <! closes infoSecRow2Col1>
                        <div class=\"col-8\" id=\"infoSecRow2Col2\">
                        <div class=\"info\" id=\"parentList\">";

                        
                    //Insert the parent names into the HTML:
                    while($parent = $parentRes -> fetch_assoc()){
                        print "<div class=\"parent\">" . $parent["FirstName"] . " " . $parent["LastName"] . "</div>";
                    }
                    print" </div>
                        </div> <!closes the parentList subColumn (infoSecRow2Col2)>
                    </div> <!closes infoSecRow2 (parents)>

                    <! Row 3 in Info Section: >
                    <div class=\"row justify-content-start subRow\" id=\"infoSecRow3\">
                        <div class=\"col col-2\" id=\"infoSecRow3col1\">
                        <div class=\"infoLabel\" id=\"classListLabel\">Classes:</div>
                        </div>
                        <div class=\"col\" id=\"infoSecRow3Col2\">
                        <div class=\"info\" id=\"clasList\">";

                        //Insert the Class Names into the HTML:

                        while($class = $classRes -> fetch_assoc()){
                            print "<a href=\"sectionView.php?sectionID=" . $class["SectionID"] . "\"><div class=\"info class\">" . $class["GradeLevel"] . "_" . $class["SectionNumber"] . ": " . $class["CourseName"] . "</div></a>";
                        }




                }




                }
                //So here, we're looking to close classList, infoSecRow3Col2, infoSecRow3, infoSec, the main row, and the main container. 6 divs.
        
        }else{
            echo "Please start a session";
        }

    ?>
    <!Close the Containers which all the layouts have in common: >
                    </div> <!close list>
                </div>  <! close Last Column in last row>
            </div> <! close last row>

        </div> <!Closes the second main column>
        </div> <!Closes the main row>

        </div> <!closes the container>
    
    
</BODY>
</HTML>