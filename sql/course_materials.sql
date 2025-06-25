CREATE TABLE course_materials (
    id SERIAL PRIMARY KEY,
    course_id INTEGER NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_course
        FOREIGN KEY (course_id) 
        REFERENCES courses(id) 
        ON DELETE CASCADE
);
