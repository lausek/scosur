<?php

require_once("DataInterface.class.php");

class DataManager extends DataInterface {

    public static function group_by_key($key, $ls) {

        $ord = [];

        foreach($ls as $item) {

            $ord[$item[$key]][] = $item;

        }

        return $ord;

    }

    public static function get_user_object() {

        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }

        if(isset($_SESSION["user_data"])) {
            return unserialize($_SESSION["user_data"]);
        }

        if(isset($_POST["uname"]) && isset($_POST["password"])) {
            try{

                $user = new User($_POST["uname"], $_POST["password"]);

                session_destroy();
                session_start();

                $_SESSION["user_data"] = serialize($user);

                return $user;

            }catch(Exception $e){

            }
        }

        return NULL;

    }

    public static function get_single_class($class) {

    }

    public static function get_classes($teacher) {

        return DataInterface::exec_with("SELECT classes.*
                                                FROM teacher_for_class, classes
                                                WHERE teacher_id = ?
                                                AND classes.id = class_id",
                                            [$teacher])->fetchAll();

    }

    public static function get_teachers($class) {

        return DataInterface::exec_with("SELECT users.id, users.name, users.surname
                                            FROM users, teacher_for_class AS t
                                            WHERE t.class_id = ?
                                            AND users.id = t.teacher_id",
                                        [$class])->fetchAll();

    }

    public static function get_all_teachers() {

        return DataInterface::exec_with("SELECT * FROM users WHERE role = 2")->fetchAll();

    }

    public static function get_questions($teacher) {

        return self::exec_with("SELECT * FROM questions WHERE user_id IS NULL OR user_id = ? ORDER BY user_id", [$teacher])->fetchAll();

    }

    public static function eval_teacher($teacher, $class = NULL) {

        /*
            SELECT q.question, q.typ, MIN(a.value), MAX(a.value), AVG(a.value)
            FROM questions AS q, answers AS a
            WHERE a.question_id = q.id
            GROUP BY a.question_id

            V2:
            SELECT q.question,
            	CASE q.typ
            		WHEN 1 THEN ""
            		WHEN 2 THEN ""
            		WHEN 3 THEN GROUP_CONCAT(value SEPARATOR '|')
            	END
            FROM questions AS q, answers AS a
            WHERE a.question_id = q.id
            GROUP BY a.question_id

            V3:
            SELECT q.question,
                CASE q.typ
                    WHEN 1 THEN CONCAT_WS('|',
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = -2),
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = -1),
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = 0),
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = 1),
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = 2)
                                         )
                    WHEN 2 THEN CONCAT_WS('|',
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = 0),
                                         (SELECT COUNT(*) FROM answers WHERE question_id = q.id AND value = 1)
                                         )
                    WHEN 3 THEN GROUP_CONCAT(value SEPARATOR '|')
                END AS answer
            FROM questions AS q, answers AS a
            WHERE a.question_id = q.id
            GROUP BY a.question_id
        */

        function eval_report($teacher, $class) {
            return [
                "class" => $own_class,
                "report" => self::exec_with("", [$teacher, $class])->fetchAll()
            ];
        }

        if($class === NULL) {

            // Hole Bewertung für alle Klassen, welche der Lehrer betreut
            $own_classes = self::exec_with("SELECT id FROM teacher_for_class, classes WHERE teacher_id = ? AND classes.id = class_id", [$teacher->id])->fetchAll();

            foreach($own_classes as $own_class) {
                $result[] = eval_report($teacher, $class);
            }

        }else{

            // Hole Bewertung mit Klasse
            $result[] = eval_report($teacher, $class);

        }

        return $result;
    }

    private static function set_complete($teacher, $answers) {

        $required = self::exec_with("SELECT id FROM questions WHERE user_id IS NULL OR user_id = ?", [$teacher])->fetchAll();

        foreach($required as $id) {
            if(!isset($answers[$id]) || empty($answers[$id])) {
                return false;
            }
        }

        return true;

    }

    public static function try_insert($teacher, $user, $answers) {

        if(!$user->can_vote()) {
            throw new Exception("Du darfst das nicht!!!!!!!!! lel");
        }

        if(!self::set_complete($teacher, $answers)) {
            throw new Exception("Nicht alle benötigten Antworten wurden angegeben");
        }

        parent::get_connection()->beginTransaction();

        $stat = parent::get_connection()->prepare("INSERT INTO answers (student_id, teacher_id, question_id, value) VALUES (?, ?, ?, ?)");
        $stat->bindParam(1, $user->id);
        $stat->bindParam(2, $teacher);
        $stat->bindParam(3, $answer_id);
        $stat->bindParam(4, $answer_value);

        foreach($answers as $answer_id => $answer_value) {
            $stat->execute();
        }

        parent::get_connection()->commit();

    }

}

?>
