<?php
require_once __DIR__ . '/DBRepository.php';
require_once __DIR__ . '/IStudentRepository.php';

class StudentRepository extends DBRepository implements IStudentRepository
{
    public function all(): array
    {
        $sql = "SELECT * FROM students ORDER BY student_id";
        return $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(string $id): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM students WHERE student_id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function add(array $student): bool
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO students
            (student_id, firstname, lastname, course, year_level)
            VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $student['student_id'],
            $student['firstname'],
            $student['lastname'],
            $student['course'],
            $student['year_level']
        ]);
    }

    public function update(array $student): array
    {
        $stmt = $this->connection->prepare(
            "UPDATE students
            SET firstname = ?, lastname = ?, course = ?, year_level = ?
            WHERE student_id = ?"
        );

        $stmt->execute([
            $student['firstname'],
            $student['lastname'],
            $student['course'],
            $student['year_level'],
            $student['student_id']
        ]);

        return $this->find($student['student_id']);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM students WHERE student_id = ?"
        );

        return $stmt->execute([$id]);
    }
}