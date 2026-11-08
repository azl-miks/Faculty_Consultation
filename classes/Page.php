<?php

class Page
{
    public static function display(string $page)
    {
        switch ($page) {
            case 'dashboard':
                require __DIR__ . '/../modules/dashboard.php';
                break;

            case 'faculty':
                require __DIR__ . '/../modules/faculty_display.php';
                break;

            case 'faculty_add':
                require __DIR__ . '/../modules/faculty_add.php';
                break;

            case 'faculty_edit':
                require __DIR__ . '/../modules/faculty_edit.php';
                break;

            case 'faculty_delete':
                require __DIR__ . '/../modules/faculty_delete.php';
                break;

            case 'students':
                require __DIR__ . '/../modules/students_display.php';
                break;

            case 'schedule':
                require __DIR__ . '/../modules/schedule_display.php';
                break;

            case 'consultation':
                require __DIR__ . '/../modules/consultation_display.php';
                break;

            default:
                require __DIR__ . '/../modules/dashboard.php';
                break;
        }
    }
}