<?php
// ============================================================
//  HotelController.php — Full CRUD for Hotels
//  Person 1 responsibility
// ============================================================

class HotelController {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    // ── GET /hotels ─────────────────────────────────────────
    // Returns all hotels
    public function getAll() {
        $check_in  = $_GET['check_in']  ?? null;
        $check_out = $_GET['check_out'] ?? null;

        if ($check_in && $check_out) {
            $sql = "SELECT h.* FROM hotels h 
                    WHERE h.id NOT IN (
                        SELECT b.hotel_id FROM bookings b 
                        WHERE b.status IN ('pending', 'confirmed') 
                        AND NOT (b.check_out <= ? OR b.check_in >= ?)
                    ) ORDER BY h.id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $check_in, $check_out);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql    = "SELECT * FROM hotels ORDER BY id ASC";
            $result = $this->conn->query($sql);
        }

        $hotels = [];
        while ($row = $result->fetch_assoc()) {
            $hotels[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count"   => count($hotels),
            "data"    => $hotels
        ]);
    }

    // ── GET /hotels/{id} ────────────────────────────────────
    // Returns a single hotel by ID
    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Hotel not found"
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "data"    => $result->fetch_assoc()
        ]);
    }

    // ── POST /hotels ────────────────────────────────────────
    // Create a new hotel
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (empty($data['name']) || empty($data['location']) || empty($data['price_per_night'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "name, location, and price_per_night are required"
            ]);
            return;
        }

        $name           = $data['name'];
        $location       = $data['location'];
        $price_per_night = $data['price_per_night'];
        $description    = $data['description'] ?? '';
        $image_url      = $data['image_url'] ?? '';

        $stmt = $this->conn->prepare(
            "INSERT INTO hotels (name, location, price_per_night, description, image_url)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdss", $name, $location, $price_per_night, $description, $image_url);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Hotel created successfully",
                "id"      => $this->conn->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to create hotel"
            ]);
        }
    }

    // ── PUT /hotels/{id} ────────────────────────────────────
    // Update an existing hotel
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        // Check hotel exists first
        $check = $this->conn->prepare("SELECT id FROM hotels WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Hotel not found"
            ]);
            return;
        }

        $name           = $data['name']           ?? null;
        $location       = $data['location']       ?? null;
        $price_per_night = $data['price_per_night'] ?? null;
        $description    = $data['description']    ?? null;
        $image_url      = $data['image_url']      ?? null;

        $stmt = $this->conn->prepare(
            "UPDATE hotels SET
                name            = COALESCE(?, name),
                location        = COALESCE(?, location),
                price_per_night = COALESCE(?, price_per_night),
                description     = COALESCE(?, description),
                image_url       = COALESCE(?, image_url)
             WHERE id = ?"
        );
        $stmt->bind_param("ssdssi", $name, $location, $price_per_night, $description, $image_url, $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Hotel updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to update hotel"
            ]);
        }
    }

    // ── DELETE /hotels/{id} ─────────────────────────────────
    // Delete a hotel
    public function delete($id) {
        $check = $this->conn->prepare("SELECT id FROM hotels WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Hotel not found"
            ]);
            return;
        }

        $stmt = $this->conn->prepare("DELETE FROM hotels WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Hotel deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete hotel"
            ]);
        }
    }
}