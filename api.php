<?php
// api.php - Simplified API with direct database connection

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite';
$db_user = 'root';
$db_pass = '';

// Site configuration
$config = [
    'site_name' => 'My CMS Website',
    'site_url' => 'http://localhost/newcmswebsite/newcmswebsite',
    'upload_dir' => 'uploads/',
    'data_dir' => 'data/'
];

// Set headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight OPTIONS request
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Create database connection directly
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Unknown request'
];

// Route the request based on HTTP method
switch ($method) {
    case 'GET':
        // Fetch content data
        try {
            $data = [];
            
            // Get settings
            $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($settings as $setting) {
                $data[$setting['setting_key']] = $setting['setting_value'];
            }
            
            // Get articles
            $stmt = $conn->query("SELECT id, title, content1, content2, image_url FROM articles ORDER BY id");
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data['articles'] = [];
            foreach ($articles as $article) {
                $data['articles'][] = [
                    'id' => 'article-' . $article['id'],
                    'title' => $article['title'],
                    'content1' => $article['content1'],
                    'content2' => $article['content2'],
                    'imageUrl' => $article['image_url']
                ];
            }
            
            // Get links
            $stmt = $conn->query("SELECT id, text, url, type FROM links ORDER BY sort_order");
            $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data['social_links'] = [];
            foreach ($links as $link) {
                if ($link['type'] === 'social') {
                    $data['social_links'][] = [
                        'id' => 'social-' . $link['id'],
                        'text' => $link['text'],
                        'url' => $link['url']
                    ];
                }
            }
            
            $response = [
                'success' => true,
                'message' => 'Data retrieved successfully',
            ];
            
            // Merge the data with the response
            $response = array_merge($response, $data);
            
        } catch (PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
        break;
        
    case 'POST':
        // Save content data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $response = [
                'success' => false,
                'message' => 'Invalid JSON data'
            ];
            break;
        }
        
        try {
            // Begin transaction
            $conn->beginTransaction();
            
            // Update settings
            if (isset($data['logo'])) {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_text'");
                $stmt->execute([$data['logo']]);
            }
            
            if (isset($data['company_name'])) {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_name'");
                $stmt->execute([$data['company_name']]);
            }
            
            if (isset($data['company_desc'])) {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_desc'");
                $stmt->execute([$data['company_desc']]);
            }
            
            // Handle articles
            if (isset($data['articles']) && is_array($data['articles'])) {
                // First delete all existing articles
                $stmt = $conn->prepare("DELETE FROM articles");
                $stmt->execute();
                
                // Then insert new articles
                foreach ($data['articles'] as $article) {
                    $id = str_replace('article-', '', $article['id']);
                    
                    $stmt = $conn->prepare("INSERT INTO articles (id, title, content1, content2, image_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $id, 
                        $article['title'], 
                        $article['content1'], 
                        $article['content2'], 
                        $article['imageUrl'] ?? null
                    ]);
                }
            }
            
            // Handle social links
            if (isset($data['social_links']) && is_array($data['social_links'])) {
                // First delete all existing social links
                $stmt = $conn->prepare("DELETE FROM links WHERE type = 'social'");
                $stmt->execute();
                
                // Then insert new social links
                $order = 1;
                foreach ($data['social_links'] as $link) {
                    $stmt = $conn->prepare("INSERT INTO links (text, url, type, sort_order) VALUES (?, ?, 'social', ?)");
                    $stmt->execute([$link['text'], $link['url'], $order]);
                    $order++;
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            $response = [
                'success' => true,
                'message' => 'Content saved successfully'
            ];
            
        } catch (PDOException $e) {
            // Rollback on error
            $conn->rollBack();
            
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
        break;
    
    case 'PUT':
        // Update a specific content section
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['id']) || !isset($data['content'])) {
            $response = [
                'success' => false,
                'message' => 'Invalid data for update'
            ];
            break;
        }
        
        try {
            // Determine what's being updated
            if (strpos($data['id'], 'article-') === 0) {
                // Update article
                $id = str_replace('article-', '', $data['id']);
                $stmt = $conn->prepare("UPDATE articles SET content1 = ? WHERE id = ?");
                $stmt->execute([$data['content'], $id]);
            } else if (strpos($data['id'], 'social-') === 0) {
                // Update social link
                $id = str_replace('social-', '', $data['id']);
                $stmt = $conn->prepare("UPDATE links SET url = ? WHERE id = ? AND type = 'social'");
                $stmt->execute([$data['content'], $id]);
            } else if ($data['id'] === 'logo') {
                // Update logo
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_text'");
                $stmt->execute([$data['content']]);
            } else if ($data['id'] === 'company_name') {
                // Update company name
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_name'");
                $stmt->execute([$data['content']]);
            } else if ($data['id'] === 'company_desc') {
                // Update company description
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_desc'");
                $stmt->execute([$data['content']]);
            }
            
            $response = [
                'success' => true,
                'message' => 'Content updated successfully'
            ];
            
        } catch (PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
        break;
        
    case 'DELETE':
        // Delete content (article or social link)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['id'])) {
            $response = [
                'success' => false,
                'message' => 'Invalid data for deletion'
            ];
            break;
        }
        
        try {
            // Determine what's being deleted
            if (strpos($data['id'], 'article-') === 0) {
                // Delete article
                $id = str_replace('article-', '', $data['id']);
                $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
                $stmt->execute([$id]);
            } else if (strpos($data['id'], 'social-') === 0) {
                // Delete social link
                $id = str_replace('social-', '', $data['id']);
                $stmt = $conn->prepare("DELETE FROM links WHERE id = ? AND type = 'social'");
                $stmt->execute([$id]);
            }
            
            $response = [
                'success' => true,
                'message' => 'Content deleted successfully'
            ];
            
        } catch (PDOException $e) {
            $response = [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
        break;
    
    default:
        // Method not allowed
        http_response_code(405);
        $response = [
            'success' => false,
            'message' => 'Method not allowed'
        ];
        break;
}

// Return JSON response
echo json_encode($response);
?>