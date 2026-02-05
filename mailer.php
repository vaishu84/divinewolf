<?php
// File: mailer.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'] ?? '';
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    // Email configuration
    $to = "vaishnavishiurkar@gmail.com"; // Your email address
    $subject = "Divine Wolf Newsletter Subscription";
    
    // Email content
    $message = "
    <html>
    <head>
        <title>New Newsletter Subscription</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { background-color: #333; color: #fff; padding: 10px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 New Newsletter Subscription</h1>
            </div>
            <div class='content'>
                <h2>A new subscriber has joined the Divine Wolf pack!</h2>
                <p><strong>Email Address:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Subscription Date:</strong> " . date('F j, Y') . "</p>
                <p><strong>Time:</strong> " . date('g:i A') . "</p>
                <hr>
                <p>This subscriber will receive exclusive offers, special events, and insider updates from Divine Wolf.</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " Divine Wolf Sports Bar & Grill. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Divine Wolf Newsletter <noreply@divinewolfgrill.com>" . "\r\n";
    $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);
        
        // Optional: Save to database or CSV file
        saveSubscriber($email);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error processing your subscription. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

function saveSubscriber($email) {
    // Option 1: Save to CSV file
    $file = 'subscribers.csv';
    $data = [
        date('Y-m-d H:i:s'),
        $email,
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ];
    
    // Create file if it doesn't exist
    if (!file_exists($file)) {
        $fp = fopen($file, 'w');
        fputcsv($fp, ['Date', 'Email', 'IP Address']);
        fclose($fp);
    }
    
    // Append new subscriber
    $fp = fopen($file, 'a');
    fputcsv($fp, $data);
    fclose($fp);
    
    // Option 2: Save to database (uncomment and configure if needed)
    /*
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "divinewolf";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, subscription_date, ip_address) VALUES (:email, NOW(), :ip)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
        
    } catch(PDOException $e) {
        // Log error but don't show to user
        error_log("Database error: " . $e->getMessage());
    }
    */
}

exit;