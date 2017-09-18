<?php

$answers = [];
foreach($_POST as $key => $data) {
    $arr = preg_split("/_/", $key, 2);
    if(isset($arr[1])){
      $answers[$arr[1]] = $data;
    }
}

if(empty($answers) || !isset($_GET["teacher"])) {
    echo "422: Unprocessable Entity";
    http_response_code(422); // Unprocessable Entity
    exit;
}

require("UIManager.class.php");

$manager = new UIManager;

try {

    DataManager::try_insert((int) $_GET["teacher"], $manager->user, $answers);
    $manager->show_exit_message("Deine Bewertung wurde eingereicht :)");

}catch(Exception $e) {

    $manager->show_exit_message($e->getMessage());

}

?>
