CREATE DATABASE IF NOT EXISTS movies;
USE movies;

CREATE TABLE directors (
    director_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    birth_date DATE,
    nationality VARCHAR(50)
);

CREATE TABLE genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    release_year INT,
    director_id INT,
    genre_id INT,
    runtime_minutes INT,
    FOREIGN KEY (director_id) REFERENCES directors(director_id),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id)
);

INSERT INTO directors (first_name, last_name, birth_date, nationality) VALUES
('Christopher', 'Nolan', '1970-07-30', 'British-American'),
('Greta', 'Gerwig', '1983-08-04', 'American'),
('Bong', 'Joon-ho', '1969-09-14', 'South Korean'),
('Denis', 'Villeneuve', '1967-10-03', 'Canadian'),
('Ava', 'DuVernay', '1972-08-24', 'American');

INSERT INTO genres (name, description) VALUES
('Sci-Fi', 'Science fiction films typically deal with imaginative concepts such as futuristic science and technology'),
('Drama', 'Drama films are serious presentations or stories with settings or life situations'),
('Comedy', 'Comedy films are designed to make the audience laugh through amusement'),
('Thriller', 'Thriller films are characterized by fast pacing, frequent action, and resourceful heroes who must thwart the plans of villains'),
('Horror', 'Horror films are designed to frighten and invoke our hidden worst fears');

INSERT INTO movies (title, release_year, director_id, genre_id, runtime_minutes) VALUES
('Inception', 2010, 1, 1, 148),
('Barbie', 2023, 2, 3, 114),
('Parasite', 2019, 3, 2, 132),
('Dune', 2021, 4, 1, 155),
('Selma', 2014, 5, 2, 128),
('Interstellar', 2014, 1, 1, 169),
('Little Women', 2019, 2, 2, 135),
('Memories of Murder', 2003, 3, 4, 131),
('Arrival', 2016, 4, 1, 116),
('When They See Us', 2019, 5, 2, 296);
