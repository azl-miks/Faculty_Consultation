<?php
require_once __DIR__ . '/../repositories/FacultyRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=faculty');
    exit;
}

$id = trim($_POST['faculty_id'] ?? '');

if ($id !== '') {
    $repository = new FacultyRepository();
    $repository->delete($id);
}

header('Location: index.php?page=faculty');
exit;