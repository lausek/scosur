<?php

if(!isset($_GET["teacher"])) {
    echo "422: Unprocessable Entity";
    http_response_code(422); // Unprocessable Entity
    exit;
}

require("UIManager.class.php");

(new UIManager)->show_vote_for((int) $_GET["teacher"]);

?>
