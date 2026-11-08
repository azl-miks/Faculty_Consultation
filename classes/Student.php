<?php

require_once __DIR__ . '/Person.php';

class Student extends Person
{
    protected string $studentId;

    public function __construct(
        string $studentId,
        string $firstname,
        string $lastname
    ) {
        parent::__construct($firstname, $lastname);
        $this->studentId = $studentId;
    }

    public function getStudentID(): string
    {
        return $this->studentId;
    }
}