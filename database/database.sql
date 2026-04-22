CREATE DATABASE hostel_management;
USE hostel_management;

CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(100),
  role VARCHAR(20)
);

CREATE TABLE hostels (
  hostel_id INT PRIMARY KEY AUTO_INCREMENT,
  hostel_name VARCHAR(50),
  capacity INT
);

CREATE TABLE rooms (
  room_id INT PRIMARY KEY AUTO_INCREMENT,
  hostel_id INT,
  room_number VARCHAR(10),
  capacity INT,
  FOREIGN KEY (hostel_id) REFERENCES hostels(hostel_id)
);

CREATE TABLE students (
  student_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  name VARCHAR(100),
  email VARCHAR(100),
  phone VARCHAR(15),
  hostel_id INT,
  room_id INT,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (hostel_id) REFERENCES hostels(hostel_id),
  FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

CREATE TABLE complaints (
  complaint_id INT PRIMARY KEY AUTO_INCREMENT,
  student_id INT,
  category VARCHAR(50),
  description TEXT,
  status VARCHAR(20),
  created_at DATE,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE mess_menu (
  menu_id INT PRIMARY KEY AUTO_INCREMENT,
  day VARCHAR(10),
  meal_type VARCHAR(20),
  items TEXT
);

CREATE TABLE payments (
  payment_id INT PRIMARY KEY AUTO_INCREMENT,
  student_id INT,
  amount DECIMAL(8,2),
  payment_date DATE,
  status VARCHAR(20),
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

/*New*/

CREATE TABLE application_settings (
    id INT PRIMARY KEY,
    start_date DATE,
    end_date DATE
);

INSERT INTO application_settings (id, start_date, end_date)
VALUES (1, '2026-01-12', '2026-01-15');

CREATE TABLE hostel_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(100) UNIQUE,
    full_name VARCHAR(100),
    department VARCHAR(100),
    distance_from_home INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE hostel_applications
ADD COLUMN register_number VARCHAR(50),
ADD COLUMN personal_email VARCHAR(100),
ADD COLUMN phone VARCHAR(20),
ADD COLUMN gender VARCHAR(10),
ADD COLUMN year_semester VARCHAR(20),
ADD COLUMN dob DATE,
ADD COLUMN pincode VARCHAR(10);

ALTER TABLE hostel_applications
ADD COLUMN distance_km INT;

CREATE TABLE pincode_distance (
    pincode VARCHAR(10) PRIMARY KEY,
    distance_km INT
);

INSERT INTO pincode_distance (pincode, distance_km) VALUES
('600001', 12),
('600045', 28),
('641001', 180),
('560001', 350);

ALTER TABLE hostel_applications
ADD COLUMN annual_income INT,
ADD COLUMN pwd_status ENUM('Yes','No');

ALTER TABLE hostel_applications
ADD COLUMN income_certificate VARCHAR(255),
ADD COLUMN pwd_certificate VARCHAR(255),
ADD COLUMN id_proof VARCHAR(255);

ALTER TABLE students
ADD password_hash VARCHAR(255) NULL;

ALTER TABLE hostel_applications
ADD disability_percentage TINYINT NULL;

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

ALTER TABLE hostel_applications
ADD status VARCHAR(20) DEFAULT 'pending';

CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
   //Adheena
   CREATE TABLE guest_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100),
    guest_student_id VARCHAR(50),
    room_id INT,
    status VARCHAR(30) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

//Table for inmate approval
CREATE TABLE inmate_approvals (
    approval_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    student_id INT,
    approval_status VARCHAR(20) DEFAULT 'Pending',
    FOREIGN KEY (request_id) REFERENCES guest_requests(request_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

// request send to warden
SELECT COUNT(*) 
FROM inmate_approvals
WHERE request_id=1 AND approval_status='Pending';

UPDATE guest_requests
SET status='Approved by Inmates'
WHERE request_id=1;

CREATE TABLE IF NOT EXISTS mess_attendance (
  attendance_id INT PRIMARY KEY AUTO_INCREMENT,
  student_id VARCHAR(50) NOT NULL,
  attendance_date DATE NOT NULL,
  recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_student_date (student_id, attendance_date)
);
