CREATE TABLE IF NOT EXISTS expert_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=Monday, 6=Sunday',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expert_day (expert_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS expert_unavailable_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    unavailable_date DATE NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_expert_date (expert_id, unavailable_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
