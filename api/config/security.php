<?php
/**
 * Security Helper Functions
 * CSRF Protection, Input Validation, Encryption
 */

class Security {
    
    /**
     * Generate CSRF Token
     * @return string
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF Token
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input data
     * @param mixed $data
     * @return mixed
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeInput($value);
            }
        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Indian format)
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        $pattern = '/^[+]?[0-9]{10,15}$/';
        return preg_match($pattern, $phone);
    }
    
    /**
     * Hash password
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate API Key
     * @return string
     */
    public static function generateAPIKey() {
        return 'pk_' . bin2hex(random_bytes(32));
    }
    
    /**
     * Generate API Secret
     * @return string
     */
    public static function generateAPISecret() {
        return 'sk_' . bin2hex(random_bytes(32));
    }
    
    /**
     * Encrypt sensitive data
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function encrypt($data, $key) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt sensitive data
     * @param string $data
     * @param string $key
     * @return string|false
     */
    public static function decrypt($data, $key) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    /**
     * Rate limiting check
     * @param string $identifier (IP or user ID)
     * @param int $limit
     * @param int $window (in seconds)
     * @return bool
     */
    public static function checkRateLimit($identifier, $limit = 100, $window = 3600) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . $identifier;
        $current_time = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
            return true;
        }
        
        $elapsed = $current_time - $_SESSION[$key]['start_time'];
        
        if ($elapsed > $window) {
            $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
            return true;
        }
        
        if ($_SESSION[$key]['count'] >= $limit) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Get client IP address
     * @return string
     */
    public static function getClientIP() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
}
?>
