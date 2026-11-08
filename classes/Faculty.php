<?php

require_once __DIR__ . '/Person.php';

class Faculty extends Person
{
    protected string $facultyId;

    public function __construct(
        string $facultyId,
        string $firstname,
        string $lastname
    ) {
        parent::__construct($firstname, $lastname);
        $this->facultyId = $facultyId;
    }

    public function getFacultyId(): string
    {
        return $this->facultyId;
    }
}