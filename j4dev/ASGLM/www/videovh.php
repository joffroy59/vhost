<?php
$mysqli = new mysqli("mysql51-57.perso", "asglm", "zUAMvCWn", "asglm");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

echo nl2br($mysqli->host_info . "\n");

if (!$mysqli->query("TRUNCATE asgl_session")) {
   echo nl2br("le vigage table a échoué: (" . $mysqli->errno . ") " . $mysqli->error);
}

?>