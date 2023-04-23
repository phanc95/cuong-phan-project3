<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register page</title>
    <style type = "text/css">
         p       { margin: 0px; }
         .error  { color: red }
         p.head  { font-weight: bold; margin-top: 10px; }
         label   { width: 5em; float: left; }
         html, body    { height:100%;}
         body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('./img/Main_background.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }
         
      </style>
</head>

<body>
    <div class="card">
    <?php 
        
        // MySQL Connection Setup
        $db_host = "project3db.cpgbuu2orl7b.us-east-1.rds.amazonaws.com";
        $db_user = "admin";
        $db_password = "youcannotseeme";
        $db_name = "registrationList";
    
        $database = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    
        if (!$database) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
    
        
        //6 variables that students need to provide
        $id = isset($_POST[ "id" ]) ? $_POST[ "id" ] : "";
        $fname = isset($_POST[ "fname" ]) ? $_POST[ "fname" ] : "";
        $lname = isset($_POST[ "lname" ]) ? $_POST[ "lname" ] : "";
        $ptitle = isset($_POST[ "ptitle" ]) ? $_POST[ "ptitle" ] : "";
        $email = isset($_POST[ "email" ]) ? $_POST[ "email" ] : "";
        $phone = isset($_POST[ "phone" ]) ? $_POST[ "phone" ] : "";
        $time = isset($_POST[ "time" ]) ? $_POST[ "time" ] : "";

        //initializing iserror to false
        $iserror=false;

        //create an array for values for the input fields
        $inputlist = array( "id" => "ID", "fname" => "FirstName", "lname" => "LastName", "ptitle" => "Title", "email" => "Email", "phone" => "Phone");

        // time array
        $timelist = array(
        "Morning, Early",
        "Morning, Middle", 
        "Morning, Late",
        "Afternoon, Early",
        "Afternoon, Middle",
        "Afternoon, Late");

        /*--------------------2nd table----------------------- */
        $time_slots = [
            "Morning, Early",
            "Morning, Middle",
            "Morning, Late",
            "Afternoon, Early",
            "Afternoon, Middle",
            "Afternoon, Late"
        ];
        






        //possible error during the fill-in process
        $formerrors = array(
            "fnameerror" => false, "lnameerror" => false, "iderror" => false,
            "ptitleerror" => false, "emailerror" => false, "phoneerror" => false, "timeerror" => false, "isduplicate" => false);
        
            //make sure that all fields are filled properly
        if ( isset( $_POST["submit"] ) )
        {
            //ID should not be empty, and only 8 digits
            if ($id == "" || !preg_match("/^\d{8}$/", $id)){
                $formerrors[ "iderror" ] = true;
                $iserror = true;
            }
            //check if the firstname and last name is entered properly
            // no empty or contain number or special character
           if ( $fname == "" || !preg_match("/^[a-zA-Z]+$/", $fname))
           {
                $formerrors[ "fnameerror" ] = true;
                $iserror = true;
           } // end if

           if ( $lname == "" || !preg_match("/^[a-zA-Z]+$/", $lname))
           {
                $formerrors[ "lnameerror" ] = true;
                $iserror = true;
           } // end if
           // email should not be empty and qualify the format emailname@domain
           if ( $email == "" || !preg_match("/^[a-zA-Z0-9]+@[a-zA-Z0-9]{1,20}(\.[a-zA-Z0-9]{1,20}){1,3}$/", $email))
           {
                $formerrors[ "emailerror" ] = true;
                $iserror = true;
           } // end if

           //has to be in the format (123)-123-1234
           if ( !preg_match( "/^\d{3}-\d{3}-\d{4}$/", $phone ) )
           {
                $formerrors[ "phoneerror" ] = true;
                $iserror = true;
           } // end if

           if ( $ptitle == "" )
           {
                $formerrors[ "ptitleerror" ] = true;
                $iserror = true;
           } // end if

           if ( $time == "" )
           {
                $formerrors[ "timeerror" ] = true;
                $iserror = true;
           } // end if



            //if there is no error, execute the following
            if(!$iserror)
            {
                /********* */
                // Check if the provided ID already exists in the database
                $check_id_query = "SELECT * FROM registrationTable WHERE ID='$id'";
                $check_id_result = mysqli_query($database, $check_id_query);

                if (mysqli_num_rows($check_id_result) > 0) {
                    // ID already exists, set the duplicate error flag
                    $formerrors["isduplicate"] = true;
                    $iserror = true;
                } else {
                    
                    // Insert the user's information into the registrationTable
                    $query = "INSERT INTO registrationTable (ID, FirstName, LastName, Title, Email, Phone, TimeSlot) VALUES ('$id', '$fname', '$lname', '$ptitle', '$email', '" . mysqli_real_escape_string($database, $phone) . "', '$time')";
                   
                    // Execute the INSERT query
                    if (mysqli_query($database, $query)) {
                        // Update the TimeSlotCounts table

                        $query = "UPDATE TimeSlotCounts SET Count = Count + 1 WHERE TimeSlot = '$time'";
                        
                        mysqli_query($database, $query);
                    } else {
                        print("<p>Could not execute query!</p>");
                        die(mysqli_error($database));
                    }
                    
                    // open registrationList database
                    if ( !mysqli_select_db( $database, "registrationList" ) )
                    {
                        die( "<p>Could not open registrationList database</p>" );
                    }
                    
            
                    //close MySQL
                    mysqli_close( $database );

                    print
                    ("
                        <div class = 'notificationPage'>
                            <p class = 'noti'> Hi $fname. You have been added to the Registration List. </p>
                            
                            <p class = 'head'>The following information has been saved in our database:</p>

                            <p>ID: $id</p>
                            
                            <p>Name: $fname $lname</p>

                            <p>Title: $ptitle</p>
                            
                            <p>Email: $email</p>
                            
                            <p>Phone: $phone</p>
                            
                            <p>Time: $time</p>
                            
                            <p><a href = 'registrationList.php'>Click here to view entire database.</a></p>
                            
                            <p class = 'head'>This is only a sample form. You have not been added to a mailing list.</p>
                        
                        </div></body></html>
                    ");
                    
                    die(); // finish the page
                }
    
                
            }//finish executing if there is no error
        }
        

        



        //if there is a form error, print to the screen the instruction
        if ( $iserror )
        {
            print( "<p class = 'error'>Fields with * need to be filled in properly.</p>" );
        } // end if
        if ($formerrors["isduplicate"]) {
            print("<p class='error'>ID already exists in the database. Please use a different ID.</p>");
        }

        /*---------------------------------------------------------------------------------------*/ 
        //main page html
        print("
        <h1>Sample Registration Form</h1>
        <p>Please fill in all fields and click Register.</p>" 
        );
        print("
        <!-- post form data to dynamicForm.php -->
            <form method = 'post' action = 'index.php'>
        <h2>Student Information</h2>
        <!-- create six text boxes for user input -->" );
        foreach ( $inputlist as $inputname => $inputalt )
        {
            print("
            <div><label>$inputalt:</label><input type = 'text' name = '$inputname' value = '" . $$inputname . "'>
            ");

            if ( $formerrors[ ( $inputname )."error" ] == true )
               print( "<span class = 'error'>*</span>" 
            );

            print( "</div>" );
        } // end foreach

        //if the student enter incorrect format, prompt the following
        if ( $formerrors[ "phoneerror" ] ){
            print( "<p class = 'error'>Must be in the form 123-456-5678" );
        }

        //time slots
        print("
        <h2>Time Available</h2>
        <p>What time would you like to register for demonstration?</p>

            <!-- create drop-down list containing time list -->
            <select name = 'time'>" 
        );
        /*-------------------------- print("<select name='time'>");----------- */
       
        $query = "SELECT * FROM TimeSlotCounts";
        $result = mysqli_query($database, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $time_slot = $row['TimeSlot'];
            $count = $row['Count'];
            $available_spots = 6 - $count;

            $option_text = $time_slot . " (Available Spots: ". $available_spots .")";


            print("<option value='" . $time_slot . "'" . ($time_slot == $time ? " selected>" : ">") . $option_text . "</option>");


        }
        print("</select>");


       
        
         
        print("<!-- create a submit button -->
        <p class = 'head'><input type = 'submit' name = 'submit'
        value = 'Register'></p></form>
        </body></html>" );

        
        


        /************/
        $query = "SELECT Count FROM TimeSlotCounts WHERE TimeSlot = '$time'";
        $result = mysqli_query($database, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $count = $row['Count'];
        } else {
            // Set a default value for $count
            $count = 0;
            // opt warning
            error_log("Warning: TimeSlot not found in the TimeSlotCounts table for TimeSlot = '$time'");
        }

        if ($count >= 6) {
            $formerrors["timeerror"] = true;
            $iserror = true;
        }


        
    ?>
    </div>
</body>
</html>