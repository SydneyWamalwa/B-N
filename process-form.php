<?php
<?php
header('Content-Type: application/json');

// Enable CORS for local development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['name', 'email', 'interest', 'message'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: {$field}"]);
        exit;
    }
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Configure email settings
$to = 'your-email@domain.com'; // Replace with your email
$subject = 'New Contact Form Submission';
$message = "Name: {$data['name']}\n";
$message .= "Email: {$data['email']}\n";
$message .= "Interest: {$data['interest']}\n";
$message .= "Message: {$data['message']}\n";

$headers = [
    'From' => $data['email'],
    'Reply-To' => $data['email'],
    'X-Mailer' => 'PHP/' . phpversion()
];

// Send email
try {
    if (mail($to, $subject, $message, $headers)) {
        // Save to database (optional)
        // saveToDatabase($data);
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to send email');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function saveToDatabase($data) {
    // Add database logic here if needed
}
?>