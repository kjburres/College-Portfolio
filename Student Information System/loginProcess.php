<HTML>
<HEAD>
    <?php
        session_start();    //w3schools.com/php/php_sessions.asp
    ?>
    
    
</HEAD>
<BODY>

    
    <?php
        //Connect to the server: 

        include_once('dbConnect.php');
        $link = new mysqli($dbhost,$dbuser,$dbpass,$dbname, 3306); 

        if ($link->connect_error) {
            print "There was a problem connecting to the database.<BR/>";
            print $link->connect_errno.": {$link->connect_error}";
        } 
        else {
            print "Database connection established.";
        }    
        

        //Finding/Setting the User:
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Pull the UserID from the Database:
        $sql = "SELECT UserID, UserType, FirstName, LastName, Password FROM Users WHERE Username = '" . $username . "';"; 
        $result = $link -> query($sql);
        
        if($result !== false){
            //Set
            $userData = $result -> fetch_assoc();

            if(sha1($password) == $userData["Password"]){
                $_SESSION["user"] = $userData["UserID"];      //https://www.w3schools.com/php/php_mysql_select.asp  /  https://www.php.net/manual/en/class.mysqli-result.php
                $_SESSION["userType"] = $userData["UserType"];
                $_SESSION["userFullName"] = $userData["FirstName"] . " " . $userData["LastName"];  
                header("Location: dashboard.php");

            }else{
                echo "I'm sorry, but that password is incorrect. Please try again.";
            }

        }else{
            echo "Oops! That doesn't appear to be a user in our system. Please try again!";
        }



  


    $link -> close();




        


    ?>








</BODY>
</HTML>