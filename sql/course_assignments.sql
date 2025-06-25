CREATE TABLE course_assignments (
    id SERIAL PRIMARY KEY,
    course_id INT REFERENCES courses(id) ON DELETE CASCADE,
    tutor_id INT REFERENCES users(id) ON DELETE CASCADE,
    semester_id INT REFERENCES semesters(id) ON DELETE CASCADE,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE course_assignments
ADD COLUMN student_id INT REFERENCES users(id) ON DELETE CASCADE;
SELECT * FROM course_assignments;