<?php
// ============================================================
//  BookingController.php — Full CRUD for Bookings
//  Person 1 responsibility
// ============================================================

class BookingController {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ── GET /bookings ────────────────────────────────────────
    // Returns all bookings with user + hotel name joined
    public function getAll() {
        $sql = "SELECT b.*, 
                       u.name  AS user_name, 
                       u.email AS user_email,
                       h.name  AS hotel_name,
                       h.location AS hotel_location,
                       h.price_per_night
                FROM bookings b
                LEFT JOIN users  u ON b.user_id  = u.id
                LEFT JOIN hotels h ON b.hotel_id = h.id
                ORDER BY b.created_at DESC";

        $result   = $this->conn->query($sql);
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count"   => count($bookings),
            "data"    => $bookings
        ]);
    }

    // ── GET /bookings/{id} ───────────────────────────────────
    public function getOne($id) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, 
                    u.name  AS user_name, 
                    u.email AS user_email,
                    h.name  AS hotel_name,
                    h.price_per_night
             FROM bookings b
             LEFT JOIN users  u ON b.user_id  = u.id
             LEFT JOIN hotels h ON b.hotel_id = h.id
             WHERE b.id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Booking not found"
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "data"    => $result->fetch_assoc()
        ]);
    }

    // ── POST /bookings ───────────────────────────────────────
    // Create a new booking
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate
        if (empty($data['user_id']) || empty($data['hotel_id']) ||
            empty($data['check_in']) || empty($data['check_out'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "user_id, hotel_id, check_in, check_out are required"
            ]);
            return;
        }

        // Check check_in is before check_out
        if (strtotime($data['check_in']) >= strtotime($data['check_out'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "check_out must be after check_in"
            ]);
            return;
        }

        $user_id   = $data['user_id'];
        $hotel_id  = $data['hotel_id'];
        $check_in  = $data['check_in'];
        $check_out = $data['check_out'];
        $status    = 'pending';

        $stmt = $this->conn->prepare(
            "INSERT INTO bookings (user_id, hotel_id, check_in, check_out, status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisss", $user_id, $hotel_id, $check_in, $check_out, $status);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                "success"    => true,
                "message"    => "Booking created successfully",
                "booking_id" => $this->conn->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to create booking"
            ]);
        }
    }

    // ── PUT /bookings/{id} ───────────────────────────────────
    // Update booking status (pending / confirmed / cancelled)
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        $check = $this->conn->prepare("SELECT id FROM bookings WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Booking not found"
            ]);
            return;
        }

        $status    = $data['status']    ?? null;
        $check_in  = $data['check_in']  ?? null;
        $check_out = $data['check_out'] ?? null;

        // Special case: cancel booking
        if ($status === 'cancelled') {
            // Logic to handle cancellation (e.g., refund if paid, etc. - keeping it simple for now)
        }

        // Validate status value if provided
        $allowed = ['pending', 'confirmed', 'cancelled'];
        if ($status && !in_array($status, $allowed)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "status must be: pending, confirmed, or cancelled"
            ]);
            return;
        }

        $stmt = $this->conn->prepare(
            "UPDATE bookings SET
                status    = COALESCE(?, status),
                check_in  = COALESCE(?, check_in),
                check_out = COALESCE(?, check_out)
             WHERE id = ?"
        );
        $stmt->bind_param("sssi", $status, $check_in, $check_out, $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Booking updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to update booking"
            ]);
        }
    }

    public function cancel($id) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Booking cancelled"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to cancel booking"]);
        }
    }

    // ── DELETE /bookings/{id} ────────────────────────────────
    public function delete($id) {
        $check = $this->conn->prepare("SELECT id FROM bookings WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Booking not found"
            ]);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Booking deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete booking"
            ]);
        }
    }
}