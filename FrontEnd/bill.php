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
  width: 70%;
  margin-left:15%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.button {
    margin-left:40%;
  display: inline-block;
  padding: 15px 25px;
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

.button:hover {background-color: #0000FF}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}
</style>
 </head>
 <body>
 <h1>
    Smart shopping bill
 </h1>
 <p>
 <?php
  try {
  $con = new PDO('sqlite:C:\\yolo\\pytorch-yolo-v3\\db\\final_bill.db');
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $query = "SELECT * FROM bill";
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
  $TotalAmount=0.0;
  $count = 0.0;
  $String="Total amount:";
  foreach($data as $row){
   print "<tr>";
   foreach ($row as $name=>$value){
   print " <td>$value</td>";
   $count++;
   if(fmod($count,4) == 0){
    $TotalAmount = $TotalAmount + (float)$value;
   }
   } // end field loop
   print "</tr>";
  } // end record loop
  print "<tr><td></td><td></td><td>$String</td><td>$TotalAmount</td></tr>";
  print "</table>";
  } catch(PDOException $e) {
   echo 'ERROR: ' . $e->getMessage();
  } // end try
 ?>
 </p>
 <button class="button" onClick="location.href='payment.html'">Make payment</button>
 </body>
</html>