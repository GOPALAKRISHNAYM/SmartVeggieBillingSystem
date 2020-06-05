<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
     h1{
    margin-left:1%; 
 }
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 70%;
  margin-left:-100%;
  margin-top:-30%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.bg {
  background-image: url('download.jpg');
  height: 100%;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}
.button {
  margin-right:-40%;
  margin-top:25%;
margin-left:13%;
  display: inline-block;
  padding: 15px 25px;
  font-size: 24px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  outline: none;
  color: #000000;
  background-color:#0000FF ;
  border: none;
  border-radius: 15px;
  box-shadow: -10px 0px 10px 1px #999;
}

.button:hover {background-color: #add8e6}

.button:active {
  background-color: #3e8e41;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}
.header_menu{
    width:100%;
}

.links{
    width:100%;
    float:left;
    text-align:left;
}
.social_media{
    position:absolute;
    top: 40%;
    right: 10%;
    width: 20%;
    height: 60%;
    width:40%;
    float:left;
    text-align:right;
}
.clearfix{
    clear:both;
}
</style>
</head>
<body>
<form action="bill.php">
<div class="header_menu">
    <div class="bg">
<button class="button" onClick="bill.php">Hurry up...Shop now!!!</button>
    </div>
</div>
<div class="social_media">
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
                $String="Total amount:";
                foreach($data as $row){
                print "<tr>";
                foreach ($row as $name=>$value){
                print " <td>$value</td>";
                } // end field loop
                print "</tr>";
                } // end record loop
                print "</table>";
                } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
                } // end try
              ?>
              </p>
    </div>
</div>
    <div class="clearfix"></div>
</div>
            </form>
</body>
</html>