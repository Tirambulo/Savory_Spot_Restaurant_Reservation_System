# Group Project – Savory Spot Reservation System

## 👥 Group Members
- **Cholo Tirambulo**
- **Julie Ann Babor**
- **Andaya Marben**
- **Pamulan Franco**

---

## 📌 Project Description
This is a web-based table reservation system for **Savory Spot Restaurant**.  
It allows users to:
- View dining areas and tables
- Make new reservations
- Edit or delete existing reservations
- See reservation details with guest info, table type, date, and time

The system also features a responsive interface with a carousel and dynamic modal forms.

---

## ▶️ How to Run the Project

### **1. Install Requirements**
- **XAMPP** or **WAMP** (to run PHP locally)  
- **PHP 8+**  
- **MySQL** enabled  

### **2. Setup Project**
1. Copy the project folder into your server's root directory:
   - **XAMPP:** `C:\xampp\htdocs\`
   - **WAMP:** `C:\wamp64\www\`
2. Start **Apache** and **MySQL** from the XAMPP/WAMP control panel.

### **3. Setup Database**
1. Open **phpMyAdmin** via `http://localhost/phpmyadmin/`.
2. Create a new database named: `Savory_Spot_Restaurant_Reservation_System`.
3. Import the SQL file (if provided) or create tables manually:
   - `users` table for login/signup
   - `reservations` table to store reservation details

### **4. Configure Database Connection**
- Open `classes/Connection.php`.
- Update the database credentials to match your local setup:
```php
private $host = 'localhost';
private $user = 'root';
private $password = ''; // default XAMPP password is empty
private $dbname = 'Savory_Spot_Restaurant_Reservation_System';


