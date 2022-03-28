<TITLE>On-line function check</TITLE><PRE>
<!-- (c) 2022 Anjer Apps www.anjer.net-->
HTML-service on-line
<?php // ERROR: if you can read this line in the output file, then PHP parsing is broken !!!

// check php function:
echo "PHP-service functional: PHP version ".phpversion()."\r\n";
// connect to the database server
$db_host = 'localhost';
$db_user = '...';
$db_pass = '...';
$db_name = '...';
echo "Database ";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    header('X-PHP-Response-Code: 404', true, 404);
    echo "error! \n =>Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error ."\n";
} else {
    echo "available: ".$mysqli->host_info . "\n";
}
// conclusion
date_default_timezone_set("Europe/Amsterdam");
echo "Check " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . " succesful on ".date("r (T)");

/* * * * * * * * * * * * * * * * * * *\
*
*   About:
* 
*   Very small and lightweight PHP app that will check site functionality: HTML, PHP, DB (mysql).
*   Written for usage with www.cron-job.org - a nice service that will do cron operations and mail you on errors, all for free (or for a small voluntary fee)
*
*   (c) 2022 
*   Anjer Apps 
*   www.anjer.net
* 
\* * * * * * * * * * * * * * * * * * */
?>
