<?php
// ============================================================
//  UserController.php — STUB
//  To be completed by Person 2
// ============================================================

class UserController {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAll() {
        $result = $this->conn->query("SELECT id, name, email, created_at FROM users ORDER BY id ASC");
        $users  = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(["success" => true, "count" => count($users), "data" => $users]);
    }

    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "User not found"]);
            return;
        }
        echo json_encode(["success" => true, "data" => $result->fetch_assoc()]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "name, email, password required"]);
            return;
        }
        $name     = $data['name'];
        $email    = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role     = $data['role'] ?? 'customer'; // Default to customer
        
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "User registered", "id" => $this->conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to register user"]);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "email and password required"]);
            return;
        }
        $email = $data['email'];
        $password = $data['password'];

        $stmt = $this->conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
            return;
        }

        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            unset($user['password']); // Don't send password back
            echo json_encode(["success" => true, "message" => "Login successful", "user" => $user]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        }
    }

    public function update($id) {
        $data  = json_decode(file_get_contents("php://input"), true);
        $name  = $data['name']  ?? null;
        $email = $data['email'] ?? null;
        $password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;
        $role = $data['role'] ?? null;

        $stmt  = $this->conn->prepare("UPDATE users SET name = COALESCE(?,name), email = COALESCE(?,email), password = COALESCE(?,password), role = COALESCE(?,role) WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $password, $role, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "User updated"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to update user"]);
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "User deleted"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete user"]);
        }
    }
}