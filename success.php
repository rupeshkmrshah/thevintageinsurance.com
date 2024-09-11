<?php
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $comments = $_POST['comments'];

    if (!empty($fullname) || !empty($email) || !empty($subject) || !empty($comments)){
        $host = "localhost";
        $dbUsername = "db_username";
        $dbPassword = "db_password";
        $dbName = "db_name";
        
        // Create Connection
        $conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
        if (mysqli_connect_error()){
            die('Connect Error('. mysqli_connect_errno(). ')'. mysqli_connect_error ());
        } else {
            $SELECT = "SELECT email FROM db_tablename WHERE email = ? LIMIT 1";
            $INSERT = "INSERT INTO db_tablename (fullname, email, subject, comments) VALUES (?, ?, ?, ?)";
            
            // Prepare statement
            $stmt = $conn->prepare($SELECT);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($phone);
            $stmt->store_result();
            $rnum = $stmt->num_rows;

            if ($rnum == 0) {
                $stmt->close();
                $stmt = $conn->prepare($INSERT);
                $stmt->bind_param("ssss", $fullname, $email, $subject, $comments);
                $stmt->execute();
                
                // Send email
                $to = "info@example.com";
                $subject = "New Contact Form Submission by ".$fullname;
                $message = "
                Name: $fullname\n
                Email: $email\n
                Subject: $subject\n
                Message: $comments
                ";
                $headers = "From: $email\r\n";
                $headers .= "CC: test@example.com";  // Add CC email address here

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