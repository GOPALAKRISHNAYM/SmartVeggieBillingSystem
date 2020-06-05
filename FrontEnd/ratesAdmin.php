<!DOCTYPE html>
<html lang = "en-US">
 <head>
 <meta charset = "UTF-8">
 <title>contact.php</title>
 <style>
 h1{
    margin-left:40%; 
 }
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width:30%;
  margin-left:33%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.submit {
  width:15%;
  margin-left:40%;
  display: inline-block;
  padding: 5px 6px;
  font-size: 24px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  outline: none;
  color: #000000;
  background-color: #add8e6;
  border: none;
  border-radius: 15px;
  box-shadow: -10px 0px 10px 1px #999;
}

.submit:hover {background-color: #0000FF}

.submit:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}
input {
    width: 100%;
    height:100%;
    box-sizing: border-box;
}
</style>
 </head>
 <body>
 <h1>
    Update Prices
 </h1>
 <form action="" method="post">
 <p>
 <?php
  try {
  $con = new PDO('sqlite:C:\\yolo\\pytorch-yolo-v3\\db\\final_bill.db');
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $query = "SELECT * FROM rate";
  //first pass just gets the column names
  print "<table>";
  $result = $con->query($query);
  //return only the first row (we only need field names)
  $row = $result->fetch(PDO::FETCH_ASSOC);
  print " <tr>";
  foreach ($row as $field => $value){
   print " <th>$field</th>";
  } // end foreach
  print " </tr>";
  //second query gets the data
  $data = $con->query($query);
  $data->setFetchMode(PDO::FETCH_ASSOC);
  $count = 0.0;
  $name="price";
  $int=1;
  foreach($data as $row){
   print "<tr>";
   foreach ($row as $name=>$value){
   $count++;
   if(fmod($count,2) == 0){
    $name=$name.$int;
    print "<td><input type='text' name='$name' value='$value'></td>";
    echo "<br>";
    $int=$int+1;
    $name="price";
   }
   else{
    print "<td>$value</td>";
   }
   } // end field loop
   print "</tr>";
  } // end record loop
  print "</table>";
  } catch(PDOException $e) {
   echo 'ERROR: ' . $e->getMessage();
  } // end try
 ?>
 </p>

 <input type='submit' value='Upload' name='submit' class='submit' />
 </form>
  <?php
        try {
          $name="price";
          $int=1;
          $a=array();
          $b=array();
          
          $query1 = "SELECT * FROM rate";
          $data1 = $con->query($query1);
          $data1->setFetchMode(PDO::FETCH_ASSOC);

          if (isset($_POST['submit'])) {
          $co=0;
          
          foreach($data1 as $row1){
             foreach ($row1 as $name=>$value){
             $co++;
             if(fmod($co,2) == 0){
              $name=$name.$int;
                 $username= $_POST["$name"];
                 array_push($b,$username);
                 $int=$int+1;
                 $name="price";
             }
             if(fmod($co,2) != 0){
                array_push($a,$value);
               }  
             }
            }
        } 
        $flag=0;
        for ($x = 0; $x < count($a); $x++) {
          $sth = $con->prepare("UPDATE rate SET price = ? WHERE item_name = ?");
          $sth->bindValue(1, $b[$x], PDO::PARAM_INT);
          $sth->bindValue(2, $a[$x], PDO::PARAM_STR);
          $sth->execute();
          $flag=1;
        }
        if($flag==1){
          echo '<script>alert("prices updated successfully...!!!")</script>';
        }
         } catch(PDOException $e) {
             echo 'ERROR: ' . $e->getMessage();
            }

           ?>
        
    </body>
</html>