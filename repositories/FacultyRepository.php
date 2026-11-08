<?php
require_once __DIR__ . '/DBRepository.php';
require_once __DIR__ . '/IFacultyRepository.php';

class FacultyRepository extends DBRepository implements IFacultyRepository
{
    public function all(): array
    {
        $sql = "SELECT * FROM faculty_members ORDER BY faculty_id";
        return $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(string $id): array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM faculty_members WHERE faculty_id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function add(array $faculty): bool
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO faculty_members
            (faculty_id, faculty_name, subject, availability)
            VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([
            $faculty['faculty_id'],
            $faculty['faculty_name'],
            $faculty['subject'],
            $faculty['availability']
        ]);
    }

    public function update(array $faculty): array
    {
        $stmt = $this->connection->prepare(
            "UPDATE faculty_members
            SET faculty_name = ?, subject = ?, availability = ?
            WHERE faculty_id = ?"
        );

        $stmt->execute([
            $faculty['faculty_name'],
            $faculty['subject'],
            $faculty['availability'],
            $faculty['faculty_id']
        ]);

        return $this->find($faculty['faculty_id']);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM faculty_members WHERE faculty_id = ?"
        );

        return $stmt->execute([$id]);
    }
}