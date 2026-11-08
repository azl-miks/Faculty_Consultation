<?php
require_once __DIR__ . '/../repositories/StudentRepository.php';

$repository = new StudentRepository();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['student_id'] ?? '');

    if (isset($_POST['delete'])) {
        if ($id !== '') {
            $repository->delete($id);
        }

        header('Location: index.php?page=students');
        exit;
    }

    $student = [
        'student_id' => $id,
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname' => trim($_POST['lastname'] ?? ''),
        'course' => trim($_POST['course'] ?? ''),
        'year_level' => trim($_POST['year_level'] ?? '')
    ];

    if (in_array('', $student, true)) {
        $message = 'Please complete all fields.';
    } else {
        try {
            if (isset($_POST['add'])) {
                $repository->add($student);
            } elseif (isset($_POST['edit'])) {
                $repository->update($student);
            }

            header('Location: index.php?page=students');
            exit;
        } catch (PDOException $e) {
            $message = 'Unable to save. Check the student ID.';
        }
    }
}

$students = $repository->all();
$editId = $_GET['edit'] ?? '';
?>

<h1>Students</h1>
<p>Manage student records.</p>

<?php if ($message !== ''): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>Add Student</h2>

<form method="POST">
    <input name="student_id" placeholder="Student ID" required>
    <input name="firstname" placeholder="First Name" required>
    <input name="lastname" placeholder="Last Name" required>
    <input name="course" value="ACT/BS Information Systems" required>

    <select name="year_level" required>
        <option>1st Year</option>
        <option selected>2nd Year</option>
        <option>3rd Year</option>
        <option>4th Year</option>
    </select>

    <button type="submit" name="add">Add Student</button>
</form>

<hr>

<h2>Student Records</h2>

<table>
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Course</th>
        <th>Year Level</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <?php if ($editId === $student['student_id']): ?>
                <form method="POST">
                    <td>
                        <?= htmlspecialchars($student['student_id']) ?>
                        <input type="hidden" name="student_id"
                               value="<?= htmlspecialchars($student['student_id']) ?>">
                    </td>
                    <td>
                        <input name="firstname"
                               value="<?= htmlspecialchars($student['firstname']) ?>" required>
                        <input name="lastname"
                               value="<?= htmlspecialchars($student['lastname']) ?>" required>
                    </td>
                    <td>
                        <input type="hidden" name="course"
                               value="<?= htmlspecialchars($student['course']) ?>">
                        <?= htmlspecialchars($student['course']) ?>
                    </td>
                    <td>
                        <input type="hidden" name="year_level"
                               value="<?= htmlspecialchars($student['year_level']) ?>">
                        <?= htmlspecialchars($student['year_level']) ?>
                    </td>
                    <td>
                        <button type="submit" name="edit">Save</button>
                        <a href="index.php?page=students">Cancel</a>
                    </td>
                </form>
            <?php else: ?>
                <td><?= htmlspecialchars($student['student_id']) ?></td>
                <td>
                    <?= htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) ?>
                </td>
                <td><?= htmlspecialchars($student['course']) ?></td>
                <td><?= htmlspecialchars($student['year_level']) ?></td>
                <td>
                    <a href="index.php?page=students&edit=<?= urlencode($student['student_id']) ?>">
                        Edit
                    </a>

                    <form method="POST" style="display:inline">
                        <input type="hidden" name="student_id"
                               value="<?= htmlspecialchars($student['student_id']) ?>">
                        <button type="submit" name="delete"
                                onclick="return confirm('Delete this student?')">
                            Delete
                        </button>
                    </form>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>