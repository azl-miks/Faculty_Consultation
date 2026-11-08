<?php
require_once __DIR__ . '/../repositories/FacultyRepository.php';

$repository = new FacultyRepository();
$id = $_GET['id'] ?? '';
$faculty = $repository->find($id);
$message = '';

if (!$faculty) {
    echo '<h1>Faculty not found</h1>';
    echo '<a href="index.php?page=faculty">Back to Faculty</a>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedFaculty = [
        'faculty_id' => $id,
        'faculty_name' => trim($_POST['faculty_name'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'availability' => trim($_POST['availability'] ?? '')
    ];

    if (
        $updatedFaculty['faculty_name'] === '' ||
        $updatedFaculty['subject'] === '' ||
        $updatedFaculty['availability'] === ''
    ) {
        $message = 'Please fill in all fields.';
    } else {
        $repository->update($updatedFaculty);
        header('Location: index.php?page=faculty');
        exit;
    }

    $faculty = array_merge($faculty, $updatedFaculty);
}
?>

<h1>Edit Faculty</h1>
<p><?= htmlspecialchars($message) ?></p>

<form method="POST">
    <label>Faculty ID</label>
    <input type="text"
           value="<?= htmlspecialchars($faculty['faculty_id']) ?>"
           disabled>

    <label>Faculty Name</label>
    <input type="text" name="faculty_name"
           value="<?= htmlspecialchars($faculty['faculty_name']) ?>" required>

    <label>Subject</label>
    <input type="text" name="subject"
           value="<?= htmlspecialchars($faculty['subject']) ?>" required>

    <label>Availability</label>
    <input type="text" name="availability"
           value="<?= htmlspecialchars($faculty['availability']) ?>" required>

    <button type="submit">Update Faculty</button>
    <a href="index.php?page=faculty">Cancel</a>
</form>