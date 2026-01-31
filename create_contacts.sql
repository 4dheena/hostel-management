CREATE TABLE IF NOT EXISTS contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO contacts (role, name, phone) VALUES
('Warden', 'Mr. Rajesh Kumar', '98765 43210'),
('Matron', 'Mrs. Anitha Thomas', '91234 56789'),
('Hostel Secretary', 'Mr./Ms. Secretary', '90000 12345');
