<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CasperVPN</title>
</head>
<body>
<h1>Your IP is Blocked</h1>
<h5>Please contact the adminitrator.. </h5>
<?php
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];
$referrer = $_SERVER['HTTP_REFERER'];
if ($referred == "") {
    $referrer = "This page was accessed directly";
}
echo "<b>Visitor IP address:</b><br/>" . $ip . "<br/>";
echo "<b>Browser (User Agent) Info:</b><br/>" . $browser . "<br/>";
echo "<b>Referrer:</b><br/>" . $referrer . "<br/>";

?>

</body>
</html>