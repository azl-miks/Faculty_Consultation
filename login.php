<?php
session_start();
require_once __DIR__ . '/repositories/DBRepository.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['user_id'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $id === '' ||
        $password === '' ||
        !in_array($role, ['student', 'faculty'], true)
    ) {
        $error = 'Please complete all fields.';
    } else {
        try {
            $conn = (new DBRepository())->getConnection();

            $stmt = $conn->prepare(
                'SELECT password_hash FROM user_accounts
                 WHERE user_id = ? AND role = ?'
            );
            $stmt->execute([$id, $role]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account && password_verify($password, $account['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;

                header('Location: index.php?page=dashboard');
                exit;
            }

            $error = 'Invalid ID, password, or account type.';
        } catch (PDOException $e) {
            $error = 'Unable to connect to the database. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require __DIR__ . '/components/head.php'; ?>

<body class="welcome-page">
    <nav class="navbar">
        <a href="index.php?page=login">Faculty Consultation</a>
    </nav>

    <main>
        <section class="login-card">
            <h1>Welcome Back!</h1>
            <p>Sign in to your account</p>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login">
                <label for="role">Account Type</label>
                <select name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>

                <label for="user_id">Student ID or Faculty ID</label>
                <input type="text" name="user_id" id="user_id" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>
</html>