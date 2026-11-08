<?php
session_start();
require_once __DIR__ . '/../repositories/DBRepository.php';

$conn = (new DBRepository())->getConnection();
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $id = trim($_POST['user_id'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if (
            !in_array($role, ['student', 'faculty'], true) ||
            $id === '' ||
            $password === ''
        ) {
            throw new Exception('Please complete the required fields.');
        }

        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters.');
        }

        $conn->beginTransaction();

        $check = $conn->prepare(
            'SELECT 1 FROM user_accounts WHERE user_id = ? AND role = ?'
        );
        $check->execute([$id, $role]);

        if ($check->fetchColumn()) {
            throw new Exception('This ID already has a login account.');
        }

        if ($role === 'student') {
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $course = trim($_POST['course'] ?? '');
            $year_level = trim($_POST['year_level'] ?? '');

            if (
                $firstname === '' || $lastname === '' ||
                $course === '' || $year_level === ''
            ) {
                throw new Exception('Please complete all student fields.');
            }

            $check = $conn->prepare(
                'SELECT 1 FROM students WHERE student_id = ?'
            );
            $check->execute([$id]);

            if ($check->fetchColumn()) {
                throw new Exception('This student ID already exists.');
            }

            $save = $conn->prepare(
                'INSERT INTO students
                (student_id, firstname, lastname, course, year_level)
                VALUES (?, ?, ?, ?, ?)'
            );

            $save->execute([
                $id, $firstname, $lastname, $course, $year_level
            ]);

        } else {
            $name = trim($_POST['faculty_name'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $availability = trim($_POST['availability'] ?? '');

            if ($name === '' || $subject === '' || $availability === '') {
                throw new Exception('Please complete all faculty fields.');
            }

            $check = $conn->prepare(
                'SELECT 1 FROM faculty_members WHERE faculty_id = ?'
            );
            $check->execute([$id]);

            if ($check->fetchColumn()) {
                throw new Exception('This faculty ID already exists.');
            }

            $save = $conn->prepare(
                'INSERT INTO faculty_members
                (faculty_id, faculty_name, subject, availability)
                VALUES (?, ?, ?, ?)'
            );

            $save->execute([$id, $name, $subject, $availability]);
        }

        $saveAccount = $conn->prepare(
            'INSERT INTO user_accounts (user_id, role, password_hash)
            VALUES (?, ?, ?)'
        );

        $saveAccount->execute([
            $id,
            $role,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        $conn->commit();
        $success = true;
        $message = 'Account created successfully! You can now log in.';

    } catch (Throwable $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        $message = $e instanceof PDOException
            ? 'Unable to create account. Please check your information and try again.'
            : $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Login Account</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, sans-serif;
            background: #edf5ef;
            color: #234333;
        }

        .signup-box {
            max-width: 480px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 25px #193b2920;
        }

        h1 {
            margin-top: 0;
            color: #245c3d;
            text-align: center;
        }

        p {
            text-align: center;
            color: #647568;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #cbd9ce;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #286744;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4d32;
        }

        .message {
            padding: 10px;
            border-radius: 8px;
            background: #edf5ef;
            overflow-wrap: anywhere;
        }

        .login-link {
            margin-top: 20px;
        }

        a {
            color: #286744;
            font-weight: bold;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="signup-box">
        <h1>Create Login Account</h1>
        <p>Join the Faculty Consultation System.</p>

        <?php if ($message !== ''): ?>
            <p class="message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="index.php?page=account_setup">
            <label>Account Type</label>
            <select name="role" id="role" required>
                <option value="student"
                    <?= ($_POST['role'] ?? '') === 'student' ? 'selected' : '' ?>>
                    Student
                </option>
                <option value="faculty"
                    <?= ($_POST['role'] ?? '') === 'faculty' ? 'selected' : '' ?>>
                    Faculty
                </option>
            </select>

            <label>Student ID or Faculty ID</label>
            <input type="text" name="user_id"
                   placeholder="Example: 25-0500 or F005"
                   value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>"
                   required>

            <div id="student-fields">
                <label>First Name</label>
                <input type="text" name="firstname"
                       value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">

                <label>Last Name</label>
                <input type="text" name="lastname"
                       value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">

                <label>Course</label>
                <input type="text" name="course"
                       placeholder="Example: BS Information Systems"
                       value="<?= htmlspecialchars($_POST['course'] ?? '') ?>">

                <label>Year Level</label>
                <select name="year_level">
                    <option value="">Select year level</option>
                    <option>1st Year</option>
                    <option>2nd Year</option>
                    <option>3rd Year</option>
                    <option>4th Year</option>
                </select>
            </div>

            <div id="faculty-fields" class="hidden">
                <label>Faculty Name</label>
                <input type="text" name="faculty_name"
                       value="<?= htmlspecialchars($_POST['faculty_name'] ?? '') ?>">

                <label>Subject</label>
                <input type="text" name="subject"
                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">

                <label>Availability</label>
                <input type="text" name="availability"
                       placeholder="Example: Monday, 11 AM - 2 PM"
                       value="<?= htmlspecialchars($_POST['availability'] ?? '') ?>">
            </div>

            <label>Password</label>
            <input type="password" name="password"
                   minlength="6" required>

            <button type="submit">Sign Up</button>
        </form>

        <p class="login-link">
            Already have an account?
            <a href="index.php?page=login">Log in</a>
        </p>
    </div>

    <script>
        const role = document.getElementById('role');
        const studentFields = document.getElementById('student-fields');
        const facultyFields = document.getElementById('faculty-fields');

        function updateFields() {
            const isStudent = role.value === 'student';

            studentFields.classList.toggle('hidden', !isStudent);
            facultyFields.classList.toggle('hidden', isStudent);

            studentFields.querySelectorAll('input, select').forEach(field => {
                field.required = isStudent;
            });

            facultyFields.querySelectorAll('input').forEach(field => {
                field.required = !isStudent;
            });
        }

        role.addEventListener('change', updateFields);
        updateFields();
    </script>
</body>
</html>