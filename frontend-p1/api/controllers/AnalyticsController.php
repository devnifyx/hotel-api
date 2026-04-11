<?php
// ============================================================
//  AnalyticsController.php — API Analytics
// ============================================================

class AnalyticsController {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getOverview() {
        // Total bookings
        $totalBookingsResult = $this->conn->query("SELECT COUNT(*) as total FROM bookings");
        $totalBookings = $totalBookingsResult->fetch_assoc()['total'];

        // Total users
        $totalUsersResult = $this->conn->query("SELECT COUNT(*) as total FROM users");
        $totalUsers = $totalUsersResult->fetch_assoc()['total'];

        // Total revenue
        $totalRevenueResult = $this->conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'paid'");
        $totalRevenue = $totalRevenueResult->fetch_assoc()['total'] ?? 0;

        // Pending vs Confirmed vs Cancelled
        $statusCountsResult = $this->conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
        $statusCounts = [];
        while ($row = $statusCountsResult->fetch_assoc()) {
            $statusCounts[$row['status']] = $row['count'];
        }

        echo json_encode([
            "success" => true,
            "data" => [
                "total_bookings" => (int)$totalBookings,
                "total_users" => (int)$totalUsers,
                "total_revenue" => (float)$totalRevenue,
                "booking_statuses" => $statusCounts
            ]
        ]);
    }

    public function getAvailability() {
        // Total hotels (rooms)
        $totalHotelsResult = $this->conn->query("SELECT COUNT(*) as total FROM hotels");
        $totalHotels = $totalHotelsResult->fetch_assoc()['total'];

        // Occupied rooms (confirmed/pending bookings for "today")
        $today = date('Y-m-d');
        $occupiedResult = $this->conn->query("SELECT COUNT(DISTINCT hotel_id) as occupied FROM bookings WHERE status IN ('pending', 'confirmed') AND check_in <= '$today' AND check_out >= '$today'");
        $occupied = $occupiedResult->fetch_assoc()['occupied'];

        echo json_encode([
            "success" => true,
            "data" => [
                "total_hotels" => (int)$totalHotels,
                "occupied_today" => (int)$occupied,
                "available_today" => (int)($totalHotels - $occupied)
            ]
        ]);
    }

    public function getUserActivity() {
        // Most active users
        $activeUsersResult = $this->conn->query("
            SELECT u.id, u.name, u.email, COUNT(b.id) as booking_count 
            FROM users u 
            JOIN bookings b ON u.id = b.user_id 
            GROUP BY u.id 
            ORDER BY booking_count DESC 
            LIMIT 5
        ");
        $activeUsers = [];
        while ($row = $activeUsersResult->fetch_assoc()) {
            $activeUsers[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => [
                "top_users" => $activeUsers
            ]
        ]);
    }
}
