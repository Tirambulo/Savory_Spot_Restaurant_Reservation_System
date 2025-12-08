<?php
// CRITICAL: Ensure this path is correct relative to Client.php's location
require_once(__DIR__ . '/Connection.php'); 

class Users extends Dbh
{
    // === SIGNUP METHOD ===
    public function signup($email, $hashed_password)
    {
        $conn = $this->connect();

        // 1. Check for duplicate email
        $stmt_check = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt_check->bind_param('s', $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            return 3; // Email already exists
        }
        $stmt_check->close();

        // 2. Insert new user
        $stmt = $conn->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        if (!$stmt) return 0; // Database error

        $stmt->bind_param('ss', $email, $hashed_password);
        
        if ($stmt->execute()) {
            $stmt->close();
            return 1; // Success
        } else {
            $stmt->close();
            return 0; // Database error
        }
    }

    // === LOGIN METHOD ===
    public function login($email, $password)
    {
        $email = trim($email);
        $password = trim($password);

        $conn = $this->connect();
        
        $stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ? LIMIT 1');
        
        if (!$stmt) return ['error' => 'A system error occurred. Please try again.'];

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $generic_error = ['error' => 'Invalid email or password.'];

        if ($result->num_rows === 0) {
            return $generic_error;
        }

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id']; 
            
            return [
                'success' => 'Login successful. Redirecting...',
                'redirect' => '../pages/home.php' 
            ];
        }
        
        return $generic_error; 
    }
}
