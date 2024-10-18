<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #ebeeee;
            /*background: linear-gradient(to right, #74ebd5, #acb6e5);*/
        }

        .intro {
            width: 100%;
            max-width: 500px !important;
            height: 100px;
            background: #fff;
            /*background-color: #143213;*/
            display: flex;
            justify-content: start;
            align-items: center;
            border-radius: 4px;
            margin-bottom: 10px;
            padding: 0 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            color: #050505
        }

        .intro .text {
            margin-left: -30px !important;
        }

        .intro .primary {
            font-size: 24px;
        }
        
        .intro .secondary {
            font-size: 14px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            text-align: center;
            transition: transform 0.3s;
        }

        h1 {
            color: #2980b9;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2980b9;
            outline: none;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 12px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #2471a3;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .register-link {
            text-decoration: none;
            color: #2980b9;
            font-size: 14px;
            display: inline-block;
            transition: color 0.3s;
        }

        .register-link:hover {
            text-decoration: underline;
            color: #1e6f96;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .logo {
            text-align: start;
            width: 65px;
            height: 65px;
            margin-right: 50px !important;
        }
    </style>
</head>

<body>
    <div class="intro d-flex align-items-center">
        <img class="logo me-3" src="./system/pictures/logo(noBG).png" style="max-width: 100px;" /> <!-- Adjust size as needed -->
        <div class="text">
            <div class="primary">Calamba Manpower</div>
            <div class="secondary">Development Center</div>
        </div>
    </div>
    <div class="container d-flex flex-column align-items-center justify-content-center">
        <h1 class="mb-4">Login your Account</h1>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger error-message" role="alert">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="login.php" method="post" class="w-100">
            <div class="mb-3 text-start">
                <label for="username" class="form-label"><strong>Username</strong></label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label"><strong>Password</strong></label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
            </div>
            <div class="button-container">
                <input type="submit" value="Login" class="btn btn-primary w-100">
            </div>
        </form>
        <div class="text-center mt-3">
            <h6 class="d-inline">Don't have an account?</h6>
            <a href="system/registration.html" class="register-link"><h6>Register Here</h6></a>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>