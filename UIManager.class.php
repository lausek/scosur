<?php

require("Smarty3/Smarty.class.php");
require("DataManager.class.php");
require("User.class.php");

class UIManager extends Smarty {

    public $user;

    public function __construct() {
        parent::__construct();
        $this->user = DataManager::get_user_object();
    }

    public function if_logged_in($callback) {

        if($this->user !== NULL){

            trigger_error("Ausgabe von Daten ist <b style='color:green;'>aktiv</b>!", E_USER_WARNING);
            echo "User: {$this->user->uname}<br/>";
            echo "Rolle: {$this->user->role}<br/>";
            echo "Berechtigung: {$this->user->rights}<br/>";
            echo "Klasse: {$this->user->in_class}<br/>";

            $callback();

        }else{

            $this->show_login();

        }

    }

    public function show_exit_message($msg) {

        $this->render("templates/error/message.tpl", ["message" => $msg]);

    }

    public function show_login() {

        $this->render("templates/error/login.tpl");

    }

    public function show_overview() {

        $this->if_logged_in(function() {

            if($this->user->can_vote()) {

                $this->render("my_teachers.tpl", ["teachers" => DataManager::get_teachers($this->user->in_class)]);

            }elseif($this->user->can_view()){

                $this->render("my_classes.tpl", ["classes" => DataManager::get_classes($this->user->id)]);

            }elseif($this->user->can_view_all()){

                $this->render("my_slaves.tpl", ["teachers" => DataManager::get_all_teachers($this->user->in_class)]);

            }

        });

    }

    public function show_vote_for($teacher) {

        $this->if_logged_in(function() use ($teacher) {

            if(!$this->user->can_vote_for($teacher)){

                $this->show_exit_message("Sie können diesen Lehrer nicht bewerten!");

            }elseif($this->user->has_voted_for($teacher)){

                $this->show_exit_message("Für diesen Lehrer liegt bereits eine Bewertung vor. Danke für Nichts!");

            }else{

                $this->render("templates/vote.tpl", ["questions" => DataManager::get_questions($teacher > -1 ? $teacher : NULL)]);

            }

        });

    }

    public function show_eval_for($params) {

        // $params entählt $teacher_id und $class_id
        // Wenn $teacher_id -> dann für alle Klassen die der Lehrer betreut
        // Wenn $teacher_id und Klasse -> dann für die jeweilige Klasse

        $this->if_logged_in(function() use ($params) {

            $teacher = isset($params["teacher"]) ? $params["teacher"] : NULL;
            $class = isset($params["class"]) ? $params["class"] : NULL;

            if($teacher !== $this->user->id) {
                trigger_error("Kein Check, ob Lehrer versucht anderen zu öffnen!");
            }

            if($this->user->has_class($class)) {

                $this->render("templates/view.tpl",
                                ["sheets" => DataManager::eval_teacher($this->user)]);

            }elseif($this->user->can_view_all()){

                if($teacher !== NULL) {

                    $this->render("templates/view.tpl",
                                    ["sheets" => DataManager::eval_teacher($teacher, $class)]);

                }else{

                    throw new Exception("Kein Lehrer mitgegeben", 1);

                }

            }else{

                $this->show_exit_message("Das kannst du dir nicht anschauen");

            }

        });

    }

    public function render($template, $data = NULL) {

        if($data !== NULL) {
            parent::assign("data", $data);
        }

        parent::assign("content", $template);
        parent::display("templates/main.tpl");
        exit;

    }

}

?>
