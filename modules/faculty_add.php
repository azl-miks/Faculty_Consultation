<?php
require_once __DIR__ . '/../repositories/FacultyRepository.php';

$repository = new FacultyRepository();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty = [
        'faculty_id' => trim($_POST['faculty_id'] ?? ''),
        'faculty_name' => trim($_POST['faculty_name'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'availability' => trim($_POST['availability'] ?? '')
    ];

    if (in_array('', $faculty, true)) {
        $message = 'Please fill in all fields.';
    } else {
        try {
            $repository->add($faculty);
            header('Location: index.php?page=faculty');
            exit;
        } catch (PDOException $e) {
            $message = 'Unable to add faculty. Check the faculty ID.';
        }
    }
}
?>

<h1>Add Faculty</h1>
<p><?= htmlspecialchars($message) ?></p>

<form method="POST">
    <label>Faculty ID</label>
    <input type="text" name="faculty_id" required>

    <label>Faculty Name</label>
    <input type="text" name="faculty_name" required>

    <label>Subject</label>
    <input type="text" name="subject" required>

    <label>Availability</label>
    <input type="text" name="availability" required>

    <button type="submit">Save Faculty</button>
    <a href="index.php?page=faculty">Cancel</a>
</form>