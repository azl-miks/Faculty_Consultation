<?php
require_once __DIR__ . '/../repositories/DBRepository.php';

$db = new DBRepository();
$connection = $db->getConnection();

$sql = "SELECT
            c.consultation_date,
            c.consultation_time,
            c.student_name,
            c.reason,
            c.status,
            f.faculty_name
        FROM consultations c
        LEFT JOIN faculty_members f
            ON c.faculty_id = f.faculty_id
        ORDER BY c.consultation_date, c.consultation_time";

$schedules = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Consultation Schedule</h1>
<p>View all requested consultation schedules.</p>

<?php if (empty($schedules)): ?>
    <p>No schedules available yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Student</th>
            <th>Faculty</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>

        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?= htmlspecialchars($schedule['consultation_date']) ?></td>
                <td><?= htmlspecialchars($schedule['consultation_time']) ?></td>
                <td><?= htmlspecialchars($schedule['student_name']) ?></td>
                <td><?= htmlspecialchars($schedule['faculty_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($schedule['reason']) ?></td>
                <td><?= htmlspecialchars($schedule['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>