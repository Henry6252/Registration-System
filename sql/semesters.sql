CREATE TABLE semesters (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,           -- e.g., "Spring 2025"
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);
