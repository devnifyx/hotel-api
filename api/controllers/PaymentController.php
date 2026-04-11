<?php
// ============================================================
//  PaymentController.php — STUB
//  To be completed by Person 3
// ============================================================

class PaymentController {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAll() {
        $sql = "SELECT p.*, b.check_in, b.check_out FROM payments p
                LEFT JOIN bookings b ON p.booking_id = b.id
                ORDER BY p.id ASC";
        $result   = $this->conn->query($sql);
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(["success" => true, "count" => count($payments), "data" => $payments]);
    }

    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Payment not found"]);
            return;
        }
        echo json_encode(["success" => true, "data" => $result->fetch_assoc()]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['booking_id']) || empty($data['amount']) || empty($data['method'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "booking_id, amount, method required"]);
            return;
        }
        $booking_id = $data['booking_id'];
        $amount     = $data['amount'];
        $method     = $data['method'];
        $status     = 'unpaid';
        $stmt = $this->conn->prepare("INSERT INTO payments (booking_id, amount, method, status) VALUES (?,?,?,?)");
        $stmt->bind_param("idss", $booking_id, $amount, $method, $status);
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Payment record created", "id" => $this->conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to create payment"]);
        }
    }

    public function update($id) {
        $data   = json_decode(file_get_contents("php://input"), true);
        $status = $data['status'] ?? null;
        $paid_at = ($status === 'paid') ? date('Y-m-d H:i:s') : null;

        $stmt = $this->conn->prepare("UPDATE payments SET status = COALESCE(?,status), paid_at = COALESCE(?,paid_at) WHERE id = ?");
        $stmt->bind_param("ssi", $status, $paid_at, $id);

        if ($stmt->execute()) {
            // If payment is paid, update the booking to confirmed
            if ($status === 'paid') {
                $p_stmt = $this->conn->prepare("SELECT booking_id FROM payments WHERE id = ?");
                $p_stmt->bind_param("i", $id);
                $p_stmt->execute();
                $res = $p_stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $booking_id = $row['booking_id'];
                    $b_stmt = $this->conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
                    $b_stmt->bind_param("i", $booking_id);
                    $b_stmt->execute();
                }
            }
            echo json_encode(["success" => true, "message" => "Payment updated and booking confirmed"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to update payment"]);
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Payment deleted"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete payment"]);
        }
    }
}