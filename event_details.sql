CREATE TABLE event_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    description TEXT,
    dates VARCHAR(100) NOT NULL,
    times VARCHAR(100) NOT NULL,
    venue VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
