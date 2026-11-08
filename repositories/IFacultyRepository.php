<?php

interface IFacultyRepository
{
    public function all(): array;
    public function find(string $id): array;
    public function add(array $faculty): bool;
    public function update(array $faculty): array;
    public function delete(string $id): bool;
}