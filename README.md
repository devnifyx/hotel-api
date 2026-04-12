# Hotel & Travel Reservation System

Hotel & Travel Reservation System is a collaborative hotel reservation platform designed to demonstrate modern web engineering and UI/UX principles. This project features a **shared RESTful PHP backend** and database, serving **multiple independent frontend implementations** created by different team members.

While all frontends provide the same core booking and management functionalities, each student implemented a unique layout and user interface design to showcase individual creativity and technical evaluation.

---

## 🚀 Key Features

### **Shared System Architecture**
- **Unified REST API:** A centralized backend managing hotels, bookings, users, payments, analytics, wishlists, and reviews for all frontends.
- **Defensive Programming:** Implementation of prepared statements, robust error handling, and server-side role-based access control across all operations.
- **Relational Integrity:** Atomic database transactions ensure that updating a payment status automatically confirms the associated booking.
- **API Security:** IP-based rate limiting (100 req/min) and X-API-KEY header authentication for secure cross-origin communication.

## 🛠️ Tech Stack

- **Backend (Shared):** PHP 8.x (Custom RESTful Router), MySQL/MariaDB.
- **Frontend (Individual):** HTML5, CSS3 (Bootstrap 5), Vanilla JavaScript (ES6+).
- **Third-Party APIs:** OpenWeatherMap API for live destination weather data.
- **Testing:** Postman for endpoint validation and transactional logic verification.

---

## 📁 Project Structure

```text
hotel-api/
├── api/                   # SHARED BACKEND (RESTful API)
│   ├── config/            # DB settings & environment constants
│   ├── controllers/       # Business logic (Hotel, Booking, Payment, etc.)
│   ├── uploads/           # Dynamically uploaded hotel imagery
│   └── index.php          # Main entry point & API Router
├── frontend-p1/           # Individual Student Implementation (Version Mohamad Syaher)
├── frontend-p2/           # Individual Student Implementation (Version Muhammad Hilmi)
├── frontend-p3/           # Individual Student Implementation (Version Muhammad Hanif)
│   constants
├── hotel_reservation.sql  # Relational Database Schema & Sample Data
└── README.md              # Project Documentation
```

---

## ⚙️ Installation & Setup

1. **Environment Setup:**
   - Clone the project into your Local Server directory (e.g., `C:\xampp\htdocs\hotel-api`).
   - Start **Apache** and **MySQL** via the XAMPP Control Panel.

2. **Database Initialization:**
   - Create a database named `hotel_reservation` in phpMyAdmin.
   - Import `hotel_reservation.sql` to populate the shared schema and seed data.
   - Verify connection settings in `api/config/database.php`.

3. **Frontend Configuration:**
   - Each frontend implementation may have its own `config.js` or equivalent.
   - For **Frontend P3**, update `WEATHER_API_KEY` in `frontend-p3/config.js` with your [OpenWeatherMap](https://openweathermap.org/api) key.
   - Ensure the `INTERNAL_API_KEY` matches the secret key defined in `api/index.php`.

4. **Accessing the Implementations:**
   - **Frontend P1 MOHAMAD SYAHER IZHAM BIN ISSHAMWIL (AM2412017976):** `http://localhost/hotel-api/frontend-p1/index.html`
   - **Frontend P2 MUHAMMAD HILMI BIN JAIS (AM2412018240):** `http://localhost/hotel-api/frontend-p2/index.html`
   - **Frontend P3 MUHAMMAD HANIF BIN MOHAMAD NIZAM (AM2412017978):** `http://localhost/hotel-api/frontend-p3/index.html`

---

## 🛡️ Core API Endpoints (Shared)

| Resource | Method | Endpoint | Description |
| :--- | :--- | :--- | :--- |
| **Hotels** | `GET` | `/api/hotels` | Retrieve all listings |
| **Bookings**| `POST`| `/api/bookings` | Process new reservation |
| **Payments**| `PUT` | `/api/payments/{id}`| Confirm payment (Updates Booking Status) |
| **Analytics**| `GET` | `/api/analytics/overview`| Real-time admin revenue & activity |
| **Wishlist** | `POST`| `/api/wishlists`| Toggle hotel bookmark for user |

---

## 📝 Authors
Developed by a team of student developers as part of an academic evaluation for the Hotel & Travel Reservation System project. Each frontend iteration reflects the individual creative and technical contribution of a team member.

---
*Hotel & Travel Reservation System — Redefining the hotel booking experience through modular design and relational integrity.*
