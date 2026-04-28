<?php
require_once '../config/Database.php';

class Admin {
    private $conn;
    private $table = 'admins';

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $phone;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    
    public function loginByEmail($email, $password) {
        $query = "SELECT id, username, email, password, full_name FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->full_name = $row['full_name'];
                return true;
            }
        }
        return false;
    }

    public function updateProfile($data) {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, email = :email, phone = :phone 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $data['id']);
        return $stmt->execute();
    }
}
?>