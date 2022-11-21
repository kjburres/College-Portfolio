<HTML>
<HEAD>
    <! Install Bootstrap:>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link rel="stylesheet" href="sectionView.css">
  
</HEAD>
<BODY>

    <! Begin nav bar:>
    <div id="navBar">
        <span><a href="dashboard.php"><img id="hcaLogo" src="https://upload.wikimedia.org/wikipedia/commons/1/14/Hawthorne_Christian_Maroon_Logo.gif"></a></span>
        <a href="dashboard.php"><span class="navBarButton">dashboard</span></a>
        <a href="personalInfo.php"><span class="navBarButton">personal info</span></a>
    </div> <! End Nav Bar>
    
    
    <?php
        $sectionID = $_GET['sectionID'];      //https://scriptverse.academy/tutorials/php-passing-data-via-url.html
        
        //Connect to the server: 

        include_once('dbConnect.php');
        $link = new mysqli($dbhost,$dbuser,$dbpass,$dbname); 

        if ($link->connect_error) {
            print "There was a problem connecting to the database.<BR/>";
            print $link->connect_errno.": {$link->connect_error}";
        } 
    
        //Get the Class Info:
        $classInfoReq = "SELECT CourseName, GradeLevel, CourseDescription, SectionNumber FROM `CourseSections` WHERE SectionID = {$sectionID};";
        $classInfoResult = $link->query($classInfoReq);

        //Get the Instructor Name:
        $instructorReq = "SELECT FirstName, LastName FROM Users as u 
                        JOIN SectionEnrollments as se
                        ON u.UserID = se.PersonID
                        WHERE u.userType = \"F\" AND se.SectionID = {$sectionID};";
        $instructorResult = $link->query($instructorReq);

        //Get the Class List: 
        $classListReq = "SELECT FirstName, LastName, Email FROM `Users` as u
                         JOIN SectionEnrollments as se
                         ON u.userID = se.PersonID
                         WHERE SectionID = " . $sectionID . " AND FacultyStatus = 0;";
        $classList = $link->query($classListReq);
        
    
        //If the Queries return correctly: print the information in the information box: 
        if($classInfoResult == true && $instructorResult == true && $classList == true){
            
            //Call fetch_assoc() on the results to put them in array form:
            $classInfo = $classInfoResult->fetch_assoc();
            $instructor = $instructorResult->fetch_assoc();

            //Assemble the Section Code
            $sectionCode = $classInfo["GradeLevel"] . "_" . $classInfo["SectionNumber"];

            
            print"  <div id=\"courseContainer\">
                        <h2 id=\"courseTitle\">{$sectionCode}: {$classInfo["CourseName"]}</h2>
                        <span class=\"instructor\" style=\"font-weight: bold;\">Instructor: </span> 
                        <span class=\"instructor\" id=\"instructorName\">{$instructor["FirstName"]} {$instructor["LastName"]}</span>
                        <p id=\"courseDescrip\">{$classInfo["CourseDescription"]}</p>

                        <div id=\"classListCont\">
                          <h1 style=\"margin-top: 10vh;\">List of Students</h1>
                          <div id=\"classList\">
                            <table class=\"table table-hover\">
                              <thead>
                                <tr>
                                  <th scope=\"col\">First</th>
                                  <th scope=\"col\">Last</th>
                                  <th scope=\"col\">Email</th>
                                </tr>
                              </thead>
                              <tbody>";
            
            //Print A row in the Class List table for each student:
            while($row = $classList->fetch_assoc()){
                print "<tr>
                        <td>{$row["FirstName"]}</td>
                        <td>{$row["LastName"]}</td>
                        <td>{$row["Email"]}</td>
                       </tr>";
                
            }
            //Close out the HTML
            print "</tbody>
                    </table>
                    </div>
                    </div> 
                    </div>"; //Closes the table, class List, class List container, and course container  

            
        }else{  //If the SQL Queries Fail
            echo "There was a problem getting the information for this class. ";
        }
    
        $link -> close();
    ?>
  

  
  
</BODY>
</HTML>