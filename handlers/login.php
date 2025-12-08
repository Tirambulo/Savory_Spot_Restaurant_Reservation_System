<?php
/**
 * Login and Signup Handler for Savory Spot Booking System.
 * This file handles both POST requests (for API-style login/signup) 
 * and GET requests (to display the login/signup page).
 */
session_start();

ob_start();

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['login']) || isset($_POST['signup']))) {
    
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
    
    header('Content-Type: application/json');
    
    include('../Classes/Client.php'); 
    $client = new Users(); 
    
    
    if (isset($_POST['login'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $response = $client->login($email, $password);
        
    } 
    
    else if (isset($_POST['signup'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($password == '' || $email == '') { 
            $response = ['error' => "Email and Password are required!"];
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['error' => "Email is not valid!"];
        } else if ($confirm_password !== $password) { 
            $response = ['error' => "Passwords do not match."];
        } else if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $response = ['error' => "Password must be at least 8 characters long, with one uppercase letter and one number."];
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $client->signup($email, $hashed_password); 

            if ($insert === 1) {
                $response = ['success' => "Signup successful! Please log in."];
            } else if ($insert === 3) {
                $response = ['error' => "Email already exists"];
            } else {
                $response = ['error' => "Database error"];
            }
        }
    }
    
    ob_end_clean(); 
    
    echo json_encode($response);
    exit;
}

ob_end_flush(); 
?>

<?php include('../includes/header.php'); ?>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
</head>

<style>
    @keyframes slideshow {
        0% { background-image: url('../assets/bg.jpg'); } 33% { background-image: url('../assets/bg.jpg'); } 
        33.01% { background-image: url('../assets/bg1.jpg'); } 66% { background-image: url('../assets/bg1.jpg'); } 
        66.01% { background-image: url('../assets/bg2.jpg'); } 100% { background-image: url('../assets/bg2.jpg'); } 
    }
    .slideshow-bg {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; 
        background-size: cover; background-position: center center; background-repeat: no-repeat;
        animation: slideshow 15s infinite ease-in-out; 
  
        transition: background-image 2s ease-in-out; 
    }
    .bg-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); z-index: 2; 
    }
    
    @keyframes slide-in-right {
        0% { transform: translateX(100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    .right-align-container {
        position: fixed; right: 0; top: 0; z-index: 3; display: flex;
        justify-content: flex-end; align-items: center; min-height: 100vh;
        width: 100%; padding-right: 2rem; 
    }
    .animated-card {
        animation: slide-in-right 0.8s ease-out forwards;
        transform: translateX(100%); opacity: 0; width: 100%; max-width: 400px; 
        position: relative; 
    }
    .hidden-form {
        display: none;
    }
    .text-success { color: green; }
    .text-error { color: red; }
    
    .brand-title {
        font-size: 5rem; 
        font-weight: 800; 
        color: #FFC107; 
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), -1px -1px 2px rgba(255, 255, 255, 0.2); 
    }
    .brand-slogan {
        font-size: 1.5rem; 
        font-weight: 500;
        color: #F5F5F5; 
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7); 
    }
    
    @media (max-width: 768px) {
        .right-align-container { justify-content: center; padding: 1rem; position: absolute; }
        .animated-card { animation: none; opacity: 1; transform: none; max-width: 90%; }
    }
</style>

<div class="slideshow-bg"></div>
<div class="bg-overlay"></div>

<div class="right-align-container">
    
    <div class="absolute left-0 top-0 bottom-0 flex flex-col justify-center items-center h-full w-1/2 text-white p-10 hidden md:flex">
        <h1 class="brand-title">Savory Spot</h1>
        <p class="brand-slogan">
            Experience culinary artistry up close. Your journey starts here.
        </p>
    </div>

    <div class="card shadow-2xl bg-base-100/90 text-neutral-content p-6 border border-amber-500/20 animated-card">
        
        <div id="globalMessage" class="mb-4 text-center font-medium absolute w-full left-0 px-6 top-1"></div>

        <div id="loginPanel" class="card-body p-4">
            <h2 class="card-title text-3xl font-bold text-warning mb-6 justify-center">
                Login to Your Account
            </h2>
            
            <form id="loginForm">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-neutral-content/80">Email Address</span>
                    </label>
                    <input type="email" name="email" id="loginEmail" placeholder="you@example.com" 
                            class="input input-bordered input-md bg-base-200 text-neutral-content" required />
                </div>

                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text text-neutral-content/80">Password</span>
                    </label>
                    <input type="password" name="password" id="loginPassword" placeholder="********" 
                            class="input input-bordered input-md bg-base-200 text-neutral-content" required />
                </div>
                
                <div class="form-control mt-4">
                    <button type="submit" class="btn btn-warning text-base-content font-bold">Log in</button>
                </div>
            </form>

            <p class="text-sm text-center mt-6 text-neutral-content/70">
                Don't have an account? 
                <button id="showSignupBtn" class="link link-hover text-warning font-semibold p-0 border-none bg-transparent cursor-pointer">Sign Up</button>
            </p>
        </div>
        
        <div id="signupPanel" class="card-body p-4 hidden-form">
               <h2 class="card-title text-3xl font-bold text-warning mb-6 justify-center">
                Create a New Account
            </h2>
            
            <form id="signupForm">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-neutral-content/80">Email Address</span>
                    </label>
                    <input type="email" name="email" id="signupEmail" placeholder="you@example.com" 
                            class="input input-bordered input-md bg-base-200 text-neutral-content" required />
                </div>

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text text-neutral-content/80">Password</span>
                    </label>
                    <input type="password" name="password" id="signupPassword" placeholder="Minimum 8 chars, 1 Uppercase, 1 Number" 
                            class="input input-bordered input-md bg-base-200 text-neutral-content" required />
                </div>
                
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text text-neutral-content/80">Confirm Password</span>
                    </label>
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Repeat Password" 
                            class="input input-bordered input-md bg-base-200 text-neutral-content" required />
                </div>
                
                <div class="form-control mt-4">
                    <button type="submit" class="btn btn-warning text-base-content font-bold">Sign Up</button>
                </div>
            </form>

            <p class="text-sm text-center mt-6 text-neutral-content/70">
                Already have an account? 
                <button id="showLoginBtn" class="link link-hover text-warning font-semibold p-0 border-none bg-transparent cursor-pointer">Log in</button>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginPanel = document.getElementById("loginPanel");
    const signupPanel = document.getElementById("signupPanel");
    const showSignupBtn = document.getElementById("showSignupBtn");
    const showLoginBtn = document.getElementById("showLoginBtn");
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    const globalMsg = document.getElementById("globalMessage");

    function toggleForms(showLogin) {
        globalMsg.textContent = '';
        globalMsg.className = 'mb-4 text-center font-medium absolute w-full left-0 px-6 top-1'; 
        
        if (showLogin) {
            loginPanel.classList.remove('hidden-form');
            signupPanel.classList.add('hidden-form');
            signupForm.reset();
        } else {
            loginPanel.classList.add('hidden-form');
            signupPanel.classList.remove('hidden-form');
            loginForm.reset();
        }
    }

    showSignupBtn.addEventListener("click", () => toggleForms(false));
    showLoginBtn.addEventListener("click", () => toggleForms(true));

    function handleResponse(data) {
        globalMsg.textContent = '';

        if (data.success) {
            globalMsg.classList.remove('text-error', 'text-red-500');
            globalMsg.classList.add('text-success', 'text-warning'); 
            globalMsg.textContent = data.success;
            
            if (data.redirect) { 
                loginForm.reset();
                setTimeout(() => {
                    window.location.href = data.redirect || "../pages/home.php"; 
                }, 500); 
            } else {
                if (data.success.includes('Signup')) {
                    setTimeout(() => {
                        toggleForms(true);
                        globalMsg.textContent = ''; 
                    }, 2000); 
                }
            }
        } else if (data.error) {
            globalMsg.classList.remove('text-success', 'text-warning');
            globalMsg.classList.add('text-error', 'text-red-500'); 
            globalMsg.textContent = data.error;
        }
    }
    
    function fetchJsonCheck(response) {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error("Non-JSON/Corrupted Response Text:", text);
                throw new Error("Server returned corrupted data (check for text/whitespace output in PHP files).");
            });
        }
    }

    loginForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(loginForm);
        formData.append("login", true);

        fetch("login.php", { 
            method: "POST",
            body: formData
        })
        .then(fetchJsonCheck)
        .then(handleResponse)
        .catch(err => {
            globalMsg.classList.add('text-error', 'text-red-500');
            globalMsg.textContent = "Login Error: " + err.message;
        });
    });

    signupForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(signupForm);
        formData.append("signup", true);

        fetch("login.php", { 
            method: "POST",
            body: formData
        })
        .then(fetchJsonCheck)
        .then(data => {
            if (data.success) {
                signupForm.reset();
            }
            handleResponse(data);
        })
        .catch(err => {
            globalMsg.classList.add('text-error', 'text-red-500');
            globalMsg.textContent = "Signup Error: " + err.message;
        });
    });
});
</script>

<?php include('../includes/footer.php'); ?>