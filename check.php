<?php // ERROR: if you can read this line in the output file, then PHP parsing is broken !!! <HTML><!--

/* * * * * * * * * * * * * * * * * * * * * * * *\
*
*   About:
* 
*   Very small and lightweight PHP app that will 
*	check site functionality: , PHP, DB's (mysqli).
*   
*	Written for usage with www.cron-job.org 
* 	- a nice service that will do cron
*	operations and mail you on errors, 
*	all for free (or preferably
*	for a small voluntary fee)
*
*   (c) Anjer Apps 
*   www.anjer.net
*	version 3.0
*   May 2022 
* 
\* * * * * * * * * * * * * * * * * * * * * * * */

// settings:
$databases = Array(
    'first database name' => Array (
            'db_host' => 'localhost',
            'db_user' => 'username',
            'db_pass' => 'password',
            'db_name' => 'database_name'
        ),
    'next db' => Array (
            'db_host' => 'localhost',
            'db_user' => 'username',
            'db_pass' => 'password',
            'db_name' => 'database_name'
        ),
    'etc.' => Array (
            'db_host' => 'localhost',
            'db_user' => 'username',
            'db_pass' => 'password',
            'db_name' => 'database_name'
        ),
    );
$f_check_views = true; // default behavior if not specified: check for corrupt VIEW definitions that can impair the restoration of database backups

// init:
if (isset($_REQUEST['view'])) $f_check_views = intval($_REQUEST['view']);
if (isset($_REQUEST['views'])) $f_check_views = intval($_REQUEST['views']);
if (isset($_REQUEST['check_views'])) $f_check_views = intval($_REQUEST['check_views']);
$n_errors_tot = 0;

// check php function:
$output =  "PHP-service functional: PHP version ".phpversion();

foreach($databases as $database => $db){
    unset($views);
    $views = Array();
    // connect to the database server
    $output .= "\r\nDatabase $database ";
    $mysqli = new mysqli($db['db_host'], $db['db_user'], $db['db_pass'], $db['db_name']);
    if ($mysqli->connect_errno) {
        header('X-PHP-Response-Code: 404', true, 404);
        $n_errors_tot +=1;
        $output .= ("ERROR ! \n =>Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error ."\n");
    } else {
        $output .= "available: ".$mysqli->host_info;
        date_default_timezone_set("Europe/Amsterdam");
        if(!isset($f_check_views) || $f_check_views ) {// check for corrupt VIEW definitions that can impair the restoration of database backups:
            $output .=  "; VIEW definitions check:";
            $n_errors = 0; $n_warnings = 0;
            $result = mysqli_query($mysqli, 'SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.tables WHERE TABLE_TYPE LIKE "VIEW";');
            while($row = mysqli_fetch_row($result)) {
                $views[] = $row[1];
            }
            sort($views); //echo "Views found:<PRE>" . implode("\r\n" , $views);
            foreach($views as $view) {
                $result = mysqli_query($mysqli, "SELECT * FROM `$view` LIMIT 0,0;");
                $errno = intval($mysqli->errno);
                $err = $mysqli->error;
                switch ($errno) {
                    case 1104: {
                        $output .= "\nWarning: SET SQL_BIG_SELECTS=1 required for view `$view`\r";
                        $n_warnings +=1;
                        break;
                    }
                    case 0: {
                        // fine, no actions
                        break;
                    }
                    default: {
                        // any other error:
                        if($n_errors_tot==0) header('X-PHP-Response-Code: 424', true, 424);
                        $output .= "\nERROR in view `$view`: $errno - $err\r";
                        $n_errors +=1;
                        $n_errors_tot +=1;
                    }
                }
            }
            $output .= " $n_errors errors, $n_warnings warnings.";
        }
    }
}
$output .= "\r\n\r\nCheck " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . ($n_errors_tot ? " FAILED" : " succesful") . " on ".date("r (T)") . "\r\n";
?><!-- (c) 2022 Anjer Apps www.anjer.net  -->
<TITLE>On-line function check</TITLE><PRE>
HTML-service on-line
<?php //> ERROR: if you can read this line in the output file, then PHP parsing is broken !!! 
die($output); ?>
