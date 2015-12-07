<html>
<head><title>Gallery</title>
  <!-- jQuery -->
  <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
  <!-- Fotorama -->
  <link href="fotorama.css" rel="stylesheet">
  <script src="fotorama.js"></script>
</head>
<body>

<?php
//session_start();
//$email = $_POST["email"];
//echo $email;
require 'vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$resultrdb = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'mp1-replica'
   
));
$endpointrdb = $resultrdb['DBInstances'][0]['Endpoint']['Address'];
  //  echo "============\n". $endpointrdb . "================";
$linkrdb = mysqli_connect($endpointrdb,"UzmaFarheen","UzmaFarheen","Project");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else {
//echo "Connection to RDB Success";
}
if(isset($_SESSION['email'])){
$email=$_SESSION['email'];
//echo $email;
$linkrdb->real_query("SELECT * FROM ITMO544 where email='$email'");
$resrdb = $linkrdb->use_result();
//echo "Result set order...\n";
echo '<div align="left" class="fotorama" data-width="100" data-ratio="	100/46" data-max-width="50%">';
while ($row = $resrdb->fetch_assoc()) {

    echo "<img src =\"" .$row['finisheds3url'] . "\"/>";
    
}
echo'</div>';
$linkrdb->real_query("SELECT * FROM ITMO544 where email='$email'");
$resrdb = $linkrdb->use_result();
//echo "Result set order...\n";
echo '<div align="right" class="fotorama" data-width="700" data-ratio="700/467" data-max-width="50%">';
while ($row = $resrdb->fetch_assoc()) {
    echo "<img src =\" " . $row['raws3url'] . "\" />";
    
}
echo'</div>';
}
else
{
echo "No image entered";
$linkrdb->real_query("SELECT raws3url FROM ITMO544");
$resrdb = $linkrdb->use_result();
//echo "Result set order...\n";
echo '<div align="right" class="fotorama" data-width="700" data-ratio="700/467" data-max-width="50%">';
while ($row = $resrdb->fetch_assoc()) {
    echo "<img src =\" " . $row['raws3url'] . "\" />";
    
}
echo'</div>';
}
echo '<div class="error">';
if((isset($_SESSION['alertmsg']))&&($_SESSION['alertmsg'])){
echo "Please confirm subcription to receive notification";
}
echo '</div>';
if((isset($_SESSION['introspec']))&&($_SESSION['introspec'])){
echo "DB Dump in progress....Click home to upload new image: ";
}
echo '</div>';
$linkrdb->close();
session_unset();
echo "<a href='index.php'/>home!!!</a>"
?>



</body>
</html>
