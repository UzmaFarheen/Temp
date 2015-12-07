<?php
session_start();
require 'vendor/autoload.php';
# Creating a client for the s3 bucket
use Aws\Rds\RdsClient;
$client = new Aws\Rds\RdsClient([
 'version' => 'latest',
 'region'  => 'us-east-1'
]);
$result = $client->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-replica',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
# Connecting to the database
$link = mysqli_connect($endpoint,"UzmaFarheen","UzmaFarheen","Project") or die("Error " . mysqli_error($link));
/* Checking the database connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
$backup = uniqid("uzmaBackup",false);
$backupfile=$uploaddir.$backup. '.' . 'sql';
$sqlcommand="mysqldump -u UzmaFarheen -p UzmaFarheen --host=$endpoint Project > $backupfile";
echo $sqlcommand;
exec($sqlcommand);
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$bucket = uniqid("uzmabackup",false);
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);
$s3->waitUntil('BucketExists', array( 'Bucket'=> $bucket));
# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
    'Key' => $backupfile,
    'SourceFile' => $backupfile,
]);  
$sqlurl = $result['ObjectURL'];
echo $sqlurl;
echo "Database backup created successfully and stored in the s3 bucket.";
?>
