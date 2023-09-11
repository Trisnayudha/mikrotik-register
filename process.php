<?php
require("routeros_api.class.php");
$API = new RouterosAPI();
$API->debug = false;
$user_mikrotik = "admin";
$password_mikrotik = "";
$ip_mikrotik = "192.168.2.1";

// Get database connection parameters from environment variables
$db_host = getenv('DB_HOST');
$db_username = getenv('DB_USERNAME');
$db_password = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');

if ($db_host === false || $db_username === false || $db_password === false || $db_name === false) {
    die("Database configuration missing or invalid.");
}

// Connect to the database
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

if ($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik)) {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $name = $mysqli->real_escape_string($_POST['name']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $company = $mysqli->real_escape_string($_POST['company']);
    $job = $mysqli->real_escape_string($_POST['job']);

    // Insert registration data into the database
    $insert_query = "INSERT INTO user_registration (username, password, name, phone, company, job) 
                     VALUES ('$username', '$password', '$name', '$phone', '$company', '$job')";
    
    if ($mysqli->query($insert_query)) {
        // Registration data inserted successfully
        // Perform Mikrotik operations here
        
        $API->comm("/ip/hotspot/user/add", array(
            'server' => 'all',
            'name' => $username,
            'password' => $password,
            'profile' => "tamu",
        ));
        $API->disconnect();

        echo "<script>window.location = 'http://192.168.2.2/login/sukses.html';</script>";
    } else {
        echo "Error inserting data into the database: " . $mysqli->error;
    }
    
    $mysqli->close();
} else {
    echo "Mikrotik tidak ada koneksi";
}
?>
