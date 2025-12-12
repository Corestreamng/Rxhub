<?php
/**
 * RxHub Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Sanitize output for HTML
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 */
function formatCurrency($amount, $symbol = '₦') {
    return $symbol . number_format($amount, 2);
}

/**
 * Format currency without decimals
 */
function formatCurrencyShort($amount, $symbol = '₦') {
    return $symbol . number_format($amount, 0);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Format date and time
 */
function formatDateTime($date, $format = 'M d, Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Generate unique order number
 */
function generateOrderNumber() {
    return 'ORD' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

/**
 * Generate unique invoice number
 */
function generateInvoiceNumber() {
    return 'INV' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

/**
 * Generate unique payment reference
 */
function generatePaymentReference() {
    return 'PAY' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipped' => 'info',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'paid' => 'success',
        'unpaid' => 'danger',
        'partial' => 'warning',
        'active' => 'success',
        'inactive' => 'danger',
        'open' => 'success',
        'closed' => 'danger',
        'completed' => 'success',
        'failed' => 'danger',
        'refunded' => 'warning'
    ];
    
    return $classes[$status] ?? 'secondary';
}

/**
 * Get risk badge class
 */
function getRiskBadgeClass($risk) {
    $classes = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger'
    ];
    
    return $classes[$risk] ?? 'secondary';
}

/**
 * Redirect with message
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        Session::flash($type, $message);
    }
    header("Location: {$url}");
    exit();
}

/**
 * JSON response
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function isValidPhone($phone) {
    return preg_match('/^[\+]?[0-9]{10,15}$/', preg_replace('/\s+/', '', $phone));
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get first letter for avatar
 */
function getInitial($name) {
    $trimmed = trim($name ?? '');
    return strtoupper(substr($trimmed ?: 'User', 0, 1));
}

/**
 * Calculate percentage
 */
function calculatePercentage($value, $total) {
    if ($total == 0) return 0;
    return round(($value / $total) * 100, 1);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Check if request is AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 */
function getClientIP() {
    $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

/**
 * Log activity
 */
function logActivity($userType, $userId, $action, $description = '') {
    try {
        $db = Database::getInstance();
        $db->insert('activity_log', [
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => getClientIP()
        ]);
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * Get current sales cycle (YYYY-MM format)
 */
function getCurrentSalesCycle() {
    return date('Y-m');
}

/**
 * Parse CSV file
 */
function parseCSV($filePath, $hasHeader = true) {
    $data = [];
    $headers = [];
    
    if (($handle = fopen($filePath, 'r')) !== false) {
        $row = 0;
        while (($line = fgetcsv($handle)) !== false) {
            if ($hasHeader && $row === 0) {
                $headers = $line;
            } else {
                if ($hasHeader && !empty($headers)) {
                    $data[] = array_combine($headers, $line);
                } else {
                    $data[] = $line;
                }
            }
            $row++;
        }
        fclose($handle);
    }
    
    return $data;
}

/**
 * Generate CSV content
 */
function generateCSV($data, $headers = []) {
    $output = fopen('php://output', 'w');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
}
