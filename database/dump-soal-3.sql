CREATE DATABASE IF NOT EXISTS db_skill_test;
USE db_skill_test;

DROP TABLE IF EXISTS master_items;
DROP TABLE IF EXISTS user;

CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255)
);

CREATE TABLE master_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_name INT,
    items VARCHAR(255),
    estimate_price DECIMAL(15, 2),
    FOREIGN KEY (id_name) REFERENCES user(id)
);

INSERT INTO user (id, name, email) VALUES
(1, 'jonatan christie', 'jonatan.christie@test.com'),
(2, 'los blancos', 'los.blancos@test.com'),
(3, 'mawang', 'mawang@test.com');

INSERT INTO master_items (id, id_name, items, estimate_price) VALUES
(1, 1, 'Lampu bohlam LED 20 WATT', 20000),
(2, 1, 'Mouse wireless logitech', 175000),
(3, 2, 'Snack', 50000),
(4, 2, 'TV Plasma', 2500000),
(5, 3, 'Headphone', 750000),
(6, 3, 'Lemari Besi', 1500000);

SET @discount_price_1 = 0.02;
SET @discount_price_2 = 0.035;
SET @discount_price_3 = 0.05;

SELECT 
    u.name, 
    m.items, 
    CAST(m.estimate_price AS DOUBLE) AS estimate_price,
    CAST(
        CASE 
            WHEN m.estimate_price < 50000 THEN @discount_price_1
            WHEN m.estimate_price >= 50000 AND m.estimate_price <= 1500000 THEN @discount_price_2
            WHEN m.estimate_price > 1500000 THEN @discount_price_3
        END 
    AS DOUBLE) AS `discount (%)`,
    CAST(
        (m.estimate_price - (m.estimate_price * CASE 
            WHEN m.estimate_price < 50000 THEN @discount_price_1
            WHEN m.estimate_price >= 50000 AND m.estimate_price <= 1500000 THEN @discount_price_2
            WHEN m.estimate_price > 1500000 THEN @discount_price_3
        END)) 
    AS DOUBLE) AS fix_price
FROM 
    master_items m
JOIN 
    user u ON m.id_name = u.id;