<?php

interface IStudentRepository
{
    public function all(): array;
    public function find(string $id): array;
    public function add(array $student): bool;
    public function update(array $student): array;
    public function delete(string $id): bool;
}