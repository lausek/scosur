<?php

require_once("DataInterface.class.php");

class User {

    public $id, $uname, $role, $rights, $in_class;

    public function __construct($uname, $pw) {
        $this->load_user($uname, $pw); // wirft Exception bei fehlgeschlagenem login
    }

    private function load_user($uname, $pw) {

        $attrs = DataInterface::exec_with("SELECT users.id, role, in_class, rights FROM users JOIN roles ON roles.id = role WHERE uname = ? AND password = PASSWORD(?)",
                                         [$uname, $pw])->fetch();

        if($attrs === false) {
            throw new Exception("User konnte nicht angemeldet werden");
        }

  		$this->uname = $uname;
        $this->id = (int)$attrs["id"];
  		$this->role = (int)$attrs["role"];
        $this->rights = (int)$attrs["rights"];
  		$this->in_class = (int)$attrs["in_class"];

    }

    private function has_class_relation($teacher_id, $class_id) {
        return (bool) DataInterface::exec_with("SELECT EXISTS(SELECT 1 FROM teacher_for_class WHERE teacher_id = ? AND class_id = ? LIMIT 1);",
                                        [$teacher_id, $class_id])->fetchColumn(0);
    }

    public function has_voted_for($teacher) {
        return (bool) DataInterface::exec_with("SELECT EXISTS(SELECT 1 FROM answers WHERE student_id = ? AND teacher_id = ? LIMIT 1)",
                                        [$this->id, $teacher])->fetchColumn(0);
    }

    // Überprüft, ob Lehrer die Klasse betreut
    public function has_class($class) {
        return $this->has_class_relation($this->id, $class);
    }

    // Überprüft, ob Lehrer die Klasse des Schülers betreut
    public function can_vote_for($teacher) {
        return $this->has_class_relation($teacher, $this->in_class);
    }

    public function can_vote() {
  		return $this->rights & 1;
  	}

  	public function can_view() {
  		return $this->rights & 2;
  	}

  	public function can_view_all() {
  		return $this->rights & 4;
  	}

  	public function __sleep() {
  		return ["id", "uname", "role", "rights", "in_class"];
  	}

  	public function __wakeup() {

  	}

}

?>
