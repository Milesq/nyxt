create table borrows (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    bike_id INT NOT NULL,
    user_id INT NOT NULL,

    begin_date DATE NOT NULL,
    end_date DATE
);
