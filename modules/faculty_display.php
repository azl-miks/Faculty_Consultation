<?php
require_once __DIR__ . '/../repositories/FacultyRepository.php';

$repository = new FacultyRepository();
$facultyMembers = $repository->all();
?>

<h1>Faculty Members</h1>
<p>Select a faculty member for consultation.</p>

<a href="index.php?page=faculty_add">Add Faculty</a>

<table>
    <tr>
        <th>Faculty ID</th>
        <th>Faculty Name</th>
        <th>Subject</th>
        <th>Availability</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($facultyMembers as $faculty): ?>
        <tr>
            <td><?= htmlspecialchars($faculty['faculty_id']) ?></td>
            <td><?= htmlspecialchars($faculty['faculty_name']) ?></td>
            <td><?= htmlspecialchars($faculty['subject']) ?></td>
            <td><?= htmlspecialchars($faculty['availability']) ?></td>
            <td>
                <a href="index.php?page=faculty_edit&id=<?= urlencode($faculty['faculty_id']) ?>">
                    Edit
                </a>

                <form method="POST"
                      action="index.php?page=faculty_delete"
                      style="display:inline"
                      onsubmit="return confirm('Delete this faculty member?')">

                    <input type="hidden" name="faculty_id"
                           value="<?= htmlspecialchars($faculty['faculty_id']) ?>">

                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>