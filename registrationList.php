<!DOCTYPE html>

<!-- Fig. 19.22: formDatabase.php -->
<!-- Displaying the MailingList database. -->
<html>
   <head>
      <meta charset = "utf-8">
      <title>Search Results</title>
      <style type = "text/css">
         table  { background-color: lightblue;
                  border: 1px solid gray;
                  border-collapse: collapse; }
         th, td { padding: 5px; border: 1px solid gray; }
         tr:nth-child(even) { background-color: white; }
         tr:first-child { background-color: lightgreen; }
      </style>
   </head>
   
   <body>
      <?php
         // build SELECT query
         $query = "SELECT * FROM registrationTable";

         // Connect to MySQL
         if ( !( $database = mysqli_connect( "project3db.cpgbuu2orl7b.us-east-1.rds.amazonaws.com", 
                  "admin", "youcannotseeme" ) ) )
            die( "<p>Could not connect to database</p></body></html>" );

         // open MailingList database
         if ( !mysqli_select_db( $database, "registrationList" ) )
            die( "<p>Could not open registrationList database</p>
               </body></html>" );

         // query MailingList database
         if ( !( $result = mysqli_query( $database, $query ) ) )
         {
            print( "<p>Could not execute query!</p>" );
            die( mysqli_error($database) . "</body></html>" );
         } // end if
      ?><!-- end PHP script -->

      <h1>Registered List</h1>
      <table>
         <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Title</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Chosen Time</th>
         </tr>
         <?php
            // fetch each record in result set
            for ( $counter = 0; $row = mysqli_fetch_row( $result );
               ++$counter )
            {
               // build table to display results
               print( "<tr>" );

               foreach ( $row as $key => $value )
                  print( "<td>$value</td>" );

               print( "</tr>" );
            } // end for

            mysqli_close( $database );
         ?><!-- end PHP script -->
      </table>
   </body>
</html>

