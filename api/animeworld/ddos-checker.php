<?php

class ddos_checker {
    
    private $max_requests = 50;
    private $time_frame = 60;
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function rate_limit() {
        
        $ip = Helper::getUserIP();
        
        $current_time = time();
        
        $stmt = $this->db->prepare("SELECT * FROM ddos_checker WHERE ip = :ip");
        
        $stmt->execute(['ip' => $ip]);
        
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
        
            $stmt = $this->db->prepare("INSERT INTO ddos_checker (ip, count, time) VALUES (:ip, 1, :time)");
            $stmt->execute([
                'ip' => $ip,
                'time' => $current_time
            ]);
        } else {
            $time_diff = $current_time - $record['time'];
        
            if ($time_diff < $this->time_frame) {
                if ($record['count'] >= $this->max_requests) {
                    http_response_code(429);
                    die("Too many requests. Please try again later.");
                } else {

                    $stmt = $this->db->prepare("UPDATE ddos_checker SET count = count + 1 WHERE ip = :ip");
                    $stmt->execute(['ip' => $ip]);
                }
            } else {

                $stmt = $this->db->prepare("UPDATE ddos_checker SET count = 1, time = :time WHERE ip = :ip");
                $stmt->execute([
                    'ip' => $ip,
                    'time' => $current_time
                ]);
            }
        }
        
        
    }

}