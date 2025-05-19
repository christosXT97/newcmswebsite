<?php
//upload.php
// Image upload API with enhanced security and features
require_once('config.php');

// Set headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if it's a POST request for file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']); // Add timestamp to avoid duplicates
        
        // Sanitize filename to prevent directory traversal and other issues
        $fileName = preg_replace("/[^a-zA-Z0-9_.\-]/", "", $fileName);
        
        $targetFile = $config['upload_dir'] . $fileName;
        
        // Get file information
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Check if file is an actual image
        $validTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $validTypes)) {
            // Check if it's actually an image file
            $check = getimagesize($tempFile);
            if ($check !== false) {
                // Get image dimensions
                $width = $check[0];
                $height = $check[1];
                
                // Limit file size (5MB)
                $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                if ($_FILES['image']['size'] > $maxFileSize) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'File is too large. Maximum size is 5MB.'
                    ]);
                    exit;
                }
                
                // Attempt to move the uploaded file
                if (move_uploaded_file($tempFile, $targetFile)) {
                    // Get the public URL to the image
                    $publicUrl = $config['site_url'] . '/' . $config['upload_dir'] . $fileName;
                    
                    // Return success with the URL to the uploaded image
                    echo json_encode([
                        'success' => true,
                        'message' => 'Image uploaded successfully',
                        'url' => $publicUrl,
                        'width' => $width,
                        'height' => $height,
                        'size' => $_FILES['image']['size'],
                        'type' => $fileType
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to move uploaded file'
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'File is not an image'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded or error in upload',
            'error' => $_FILES['image']['error'] ?? 'No file found'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST to upload images.'
    ]);
}
?>