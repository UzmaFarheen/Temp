<?php
echo "Hello World1";
session_start();
var_dump($_POST);
if(!empty($_POST)){
echo $_POST['email'];
echo $_POST['phoneforsms'];
echo $_POST['firstname'];
$_SESSION['firstname']=$_POST['firstname'];
$_SESSION['phoneforsms']=$_POST['phoneforsms'];
$_SESSION['email']=$_POST['email'];
}
else
{
echo "post empty";
}
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
print '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  echo "File is valid, and was successfully uploaded.\n";
}
else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";
require 'vendor/autoload.php';
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
#print_r($s3);
$bucket = uniqid("mpuzma",false);
#$result = $s3->createBucket(array(
#    'Bucket' => $bucket
#));
#
## AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);
#print_r($result);
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $uploadfile,
'ContentType' => $_FILES['userfile']['type'],
'Body' => fopen($uploadfile,'r+')
]);
$url = $result['ObjectURL'];
echo $url;
$thumbimageobj = new Imagick($uploadfile);
$thumbimageobj->thumbnailImage(80,80);
$thumbimageobj->writeImage();
echo "thumbnail";
//print_r($resultfinished);
$resultfinished = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => "FinishedURL".$uploadfile,
'ContentType' => $_FILES['userfile']['type'],
'Body' => fopen($uploadfile,'r+')
]);
$finishedurl = $resultfinished['ObjectURL'];
echo $finishedurl;
$emailtemp = $_POST['useremail'];
$resultfinished = $s3->putBucketLifecycleConfiguration([
		'Bucket' => $bucket, 
		'LifecycleConfiguration' => [
			'Rules' => [ 
				[
				'Expiration' => [
				'Days' => 3,
				],	
			'NoncurrentVersionExpiration' => [
			'NoncurrentDays' => 3,
				],
			'Prefix' => '', 
			'Status' => 'Enabled', 
			],
			],
		],
	]);
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'mp1'
   
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";
$link = mysqli_connect($endpoint,"UzmaFarheen","UzmaFarheen","Project");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else {
echo "Success";
}
#create sns client
$result = new Aws\Sns\SnsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
#print_r($result);
//echo "sns Topic";
$result = $sns->listTopics(array(
));
foreach ($result['Topics'] as $key => $value){
if(preg_match("/snspicture/", $result['Topics'][$key]['TopicArn'])){
$topicARN =$result['Topics'][$key]['TopicArn'];
}
}
$resultsub = $sns->listSubscriptionsByTopic(array(
     //TopicArn is required
    'TopicArn' => $topicARN,
   
));
foreach ($resultsub['Subscriptions'] as $key => $value){
if((preg_match($emailtemp, $resultsub['Subscriptions'][$key]['endpoint']))&&(preg_match("PendingConfirmation", $resultsub['Subscriptions'][$key]['SubscriptionArn']))){
$alertmsg='true';
$_SESSION['alertmsg']=$alertmsg;
}
else{
$alertmsg='false';
$_SESSION['alertmsg']=$alertmsg;
}
}
$uname=$_POST['username'];
$email = $_POST['email'];
$phoneforsms = $_POST['phoneforsms'];
$raws3url = $url; 
$finisheds3url = "none";
$jpegfilename = basename($_FILES['userfile']['name']);
$state=0;
$res = $link->query("SELECT * FROM ITMO544 where email='$email'");
if($res->num_rows>0){
if (!($stmt = $link->prepare("INSERT INTO ITMO544 (uname,email,phoneforsms,raws3url,finisheds3url,jpegfilename,state) VALUES (?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$stmt->bind_param("ssssssi",$uname,$email,$phoneforsms,$raws3url,$finisheds3url,$jpegfilename,$state);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno0 . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
$stmt->close();
$pub = $result->publish(array(
    'TopicArn' => $topicARN,
    // Message is required
    'Subject' => 'Image Uploaded',
    'Message' => 'Imaged Uploaded successfully',
    
    
));
}
else
{
$url    = "temp.php";
   header('Location: ' . $url, true);
   die();
}
#RDB Connection:
$resultrdb = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'mp1-replica'
));
$endpointrdb = $resultrdb['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpointrdb . "================";
$linkrdb = mysqli_connect($endpointrdb,"UzmaFarheen","UzmaFarheen","Project");
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else {
echo "Connection to Read replica Database Success";
}
$linkrdb->real_query("SELECT * FROM ITMO544");
$resrdb = $linkrdb->use_result();
echo "Result set order...\n";
while ($row = $resrdb->fetch_assoc()) {
    echo $row['id'] . " " . $row['email']. " " . $row['phoneforsms'];
}
$linkrdb->close();
$url    = "gallery.php";
   header('Location: ' . $url, true);
  die();
?>
