<?php
require_once __DIR__ . '/classes/Page.php';

$page = $_GET['page'] ?? 'login';

if ($page === 'login') {
    require __DIR__ . '/login.php';
    exit;
}

if ($page === 'account_setup') {
    require __DIR__ . '/modules/account_setup.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require __DIR__ . '/components/head.php'; ?>

<body>
    <?php require __DIR__ . '/components/navbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/components/sidebar.php'; ?>

        <main class="main-content">
            <?php Page::display($page); ?>
        </main>
    </div>
</body>
</html>