<?php
require_once __DIR__ . '/../repositories/FacultyRepository.php';
require_once __DIR__ . '/../repositories/DBRepository.php';

$facultyRepository = new FacultyRepository();
$facultyMembers = $facultyRepository->all();

$connection = (new DBRepository())->getConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $id = (int) ($_POST['consultation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (in_array($status, ['Pending', 'Approved', 'Rejected'], true)) {
            $stmt = $connection->prepare(
                "UPDATE consultations SET status = ? WHERE consultation_id = ?"
            );
            $stmt->execute([$status, $id]);
        }

        header('Location: index.php?page=consultation');
        exit;
    }

    $studentName = trim($_POST['student_name'] ?? '');
    $facultyId = $_POST['faculty_id'] ?? '';
    $date = $_POST['consultation_date'] ?? '';
    $time = $_POST['consultation_time'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if (
        $studentName === '' || $facultyId === '' ||
        $date === '' || $time === '' || $reason === ''
    ) {
        $message = 'Please complete all fields.';
    } else {
        $stmt = $connection->prepare(
            "INSERT INTO consultations
            (student_name, faculty_id, consultation_date, consultation_time, reason)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([$studentName, $facultyId, $date, $time, $reason]);

        header('Location: index.php?page=consultation&submitted=1');
        exit;
    }
}

if (isset($_GET['submitted'])) {
    $message = 'Consultation request submitted successfully!';
}

$sql = "SELECT c.*, f.faculty_name
        FROM consultations c
        LEFT JOIN faculty_members f ON c.faculty_id = f.faculty_id
        ORDER BY c.consultation_id DESC";

$requests = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Request a Consultation</h1>

<?php if ($message !== ''): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" action="index.php?page=consultation">
    <label>Student Name</label>
    <input type="text" name="student_name" required>

    <label>Select Faculty</label>
    <select name="faculty_id" required>
        <option value="">Choose Faculty</option>

        <?php foreach ($facultyMembers as $faculty): ?>
            <option value="<?= htmlspecialchars($faculty['faculty_id']) ?>">
                <?= htmlspecialchars($faculty['faculty_name']) ?>
                - <?= htmlspecialchars($faculty['subject']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Consultation Date</label>
    <input type="date" name="consultation_date"
           min="<?= date('Y-m-d') ?>" required>

    <label>Consultation Time</label>
    <input type="time" name="consultation_time" required>

    <label>Reason for Consultation</label>
    <textarea name="reason" required></textarea>

    <button type="submit">Submit Request</button>
</form>

<hr>

<h2>Consultation Requests</h2>

<?php if (empty($requests)): ?>
    <p>No consultation requests yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Student</th>
            <th>Faculty</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($requests as $request): ?>
            <tr>
                <td><?= htmlspecialchars($request['student_name']) ?></td>
                <td><?= htmlspecialchars($request['faculty_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($request['consultation_date']) ?></td>
                <td><?= htmlspecialchars($request['consultation_time']) ?></td>
                <td><?= htmlspecialchars($request['reason']) ?></td>
                <td><?= htmlspecialchars($request['status']) ?></td>
                <td>
                    <form method="POST" action="index.php?page=consultation">
                        <input type="hidden" name="consultation_id"
                               value="<?= (int) $request['consultation_id'] ?>">

                        <select name="status">
                            <?php foreach (['Pending', 'Approved', 'Rejected'] as $status): ?>
                                <option value="<?= $status ?>"
                                    <?= $request['status'] === $status ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>