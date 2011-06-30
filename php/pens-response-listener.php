<?php

error_log("headers:");
foreach (getallheaders() as $key => $value){
    error_log("    $key => $value");
}

error_log("data:");
foreach ($_REQUEST as $key => $value){
    error_log("    $key => $value");
}

error_log("request body:");
error_log("    " . urldecode(file_get_contents("php://input")));

?>
