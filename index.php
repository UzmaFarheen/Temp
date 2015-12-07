<?php session_start(); ?>
<html>
<head><title>Test Application</title>
<meta charset="UTF-8">
<style type="text/css">
body {
    background-image: url("http://weknowyourdreams.com/images/cloud/cloud-03.jpg");
    font-family: "Trebuchet MS", Verdana, sans-serif;
    font-size: 16px;
    background-color: dimgrey;
    color: #696969;
    padding: 3px;
}
table {
border: 1px solid black;
}
tr {
border: 1px solid black;
}
td {
    font-family: Georgia, serif;
    border-bottom: 3px solid #cc9900;
    color:#610B21 ;
    font-size: 30px;
border: 1px solid black;
}
label {
    font-family: Georgia, serif;
    border-bottom: 3px solid #cc9900;
    color: #996600;
    font-size: 30px;
}
</style>
</head>
<?php
if((isset($_SESSION['introspec']))&&($_SESSION['introspec'])){
echo "MySQL dump in progress Admin has disabled form! Click on view Images to view gallery ";
}
else
{
//echo 'test';
//echo (isset($_SESSION['introspec']));
?>
<body>
    <div align="right">
<a href='gallery.php'/>View Images if Backup is in progress!</a></br>
<a href='introspection.php'/>DB Backup!</a></br>
</div>
<!-- The data encoding type, enctype, MUST be specified as below -->
 <form enctype="multipart/form-data" action="result.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
  <!-- Name of input element determines name in $_FILES array -->
<table>
<tr>
<td>Send this file:</td>
<td><input name="userfile" type="file" accept="image/png,image/jpeg"></td>
</tr>
<tr><td>Enter Email of user: </td>
<td><input type="email" name="useremail"><br /></td></tr>
<tr>
<td>Enter Phone of user (1-XXX-XXX-XXXX): </td>
<td><input type="phone" name="phoneforsms"></td></tr>
<tr></td><input type="submit" value="Send File" /></td></tr>
</table>
</form>
<label>
<form enctype="multipart/form-data" action="gallery.php" method="POST">

Enter Email of user for gallery to browse: <input type="email" name="email">
<input type="submit" value="Load Gallery" />
</label>
</form>
</body>
</html>
