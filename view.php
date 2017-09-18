<?php

if(isset($_GET["class"])) {
    $params["class"] = $_GET["class"];
}

if(isset($_GET["teacher"])) {
    $params["teacher"] = $_GET["teacher"];
}

if(empty($params)) {
    echo "422: Unprocessable Entity";
    http_response_code(422); // Unprocessable Entity
    exit;
}

require("UIManager.class.php");

(new UIManager)->show_eval_for($params);

?>
