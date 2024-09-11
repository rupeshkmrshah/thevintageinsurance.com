<?php
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $servicetype = $_POST['servicetype'];
    $comments = $_POST['comments'];

    if (!empty($fullname) || !empty($email) || !empty($phone) || !empty($serviceType) || !empty($comments)){
        $host = "localhost";
        $dbUsername = "db_username";
        $dbPassword = "db_password";
        $dbName = "db_name";
        
        // Create Connection
        $conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
        if (mysqli_connect_error()){
            die('Connect Error('. mysqli_connect_errno(). ')'. mysqli_connect_error ());
        } else {
            $SELECT = "SELECT phone FROM db_tableName WHERE phone = ? LIMIT 1";
            $INSERT = "INSERT INTO db_tableName (fullname, email, phone, servicetype, comments) VALUES (?, ?, ?, ?, ?)";
            
            // Prepare statement
            $stmt = $conn->prepare($SELECT);
            $stmt->bind_param("i", $phone);
            $stmt->execute();
            $stmt->bind_result($phone);
            $stmt->store_result();
            $rnum = $stmt->num_rows;

            if ($rnum == 0) {
                $stmt->close();
                $stmt = $conn->prepare($INSERT);
                $stmt->bind_param("ssiss", $fullname, $email, $phone, $servicetype, $comments);
                $stmt->execute();
                
                // Send email
                $to = "info@example.com";
                $subject = "You Got an Appointment Request Submission by ".$fullname;
                $message = "
                Name: $fullname\n
                Phone: $phone\n
                Email: $email\n
                Service Type: $servicetype\n
                Message: $comments
                ";
                $headers = "From: $email\r\n";
                $headers .= "CC: info@example.com";  // Add CC email address here

                if (mail($to, $subject, $message, $headers)) {
                    echo "We have received your details. We'll get back to you";
                } else {
                    echo "Sorry, something went wrong while sending the email. Please try again.";
                }
            } else {
                include('404.html');
            }

            $stmt->close();
            $conn->close();
        }
    } else {
        echo "All Fields are required";
        die();
    }
?>