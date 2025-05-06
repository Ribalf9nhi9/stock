<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Login</title>
    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover-color: #0056b3;
            --background-color: #f4f6f9;
            --box-background-color: #ffffff;
            --text-color: #495057;
            --input-border-color: #ced4da;
            --input-focus-border-color: #80bdff;
            --box-shadow-color: rgba(0, 0, 0, 0.1);
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            font-family: var(--font-family);
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-color);
        }

        .login-container {
            background-color: var(--box-background-color);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--box-shadow-color);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .login-header p {
            margin-top: 5px;
            color: #6c757d;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--input-border-color);
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus {
            border-color: var(--input-focus-border-color);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            /* Basic styling for checkbox */
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .remember-me label {
            margin: 0;
            font-weight: 400;
            color: #6c757d;
            cursor: pointer;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.15s ease-in-out;
        }

        .forgot-password a:hover {
            color: var(--primary-hover-color);
            text-decoration: underline;
        }

        .submit-button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }

        .submit-button:hover {
            background-color: var(--primary-hover-color);
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.15s ease-in-out;
        }

        .footer-links a:hover {
            color: var(--primary-hover-color);
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-container {
                padding: 25px;
                margin: 15px;
            }

            .login-header h1 {
                font-size: 22px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }

            .forgot-password {
                margin-top: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back!</h1>
            <p>Sign in to continue</p>
        </div>

        <!-- The action attribute should point to your backend login handler -->
        <form action="#" method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="submit-button">Sign In</button>

            
        </form>
    </div>

    <!-- No external JavaScript needed for this basic version -->
    <!-- If you need JS functionality (e.g., complex validation), add it here within <script> tags -->
    <script>
        // Example: Basic form validation feedback (optional)
        // const form = document.querySelector('form');
        // form.addEventListener('submit', function(event) {
        //     const emailInput = document.getElementById('email');
        //     const passwordInput = document.getElementById('password');
        //     if (!emailInput.value || !passwordInput.value) {
        //         // You might want more sophisticated validation and user feedback
        //         console.log('Email and password are required.');
        //         // Optionally prevent submission if validation fails
        //         // event.preventDefault(); 
        //     }
        //     // The form will submit to the URL in the 'action' attribute
        // });
    </script>
</body>
</html>

