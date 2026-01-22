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
