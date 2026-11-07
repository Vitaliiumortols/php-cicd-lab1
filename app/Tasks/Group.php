<?php

declare(strict_types=1);

namespace App\Tasks;

class Group
{
    private array $students = [];

    public function __construct(
        public int $groupId,
        public string $name
    ) {}

    public function addStudent(Student $student): void
    {
        $this->students[] = $student;
    }

    public function removeStudent(int $id): void
    {
        foreach ($this->students as $key => $student) {
            if ($student->id === $id) {
                unset($this->students[$key]);
            }
        }

        $this->students = array_values($this->students);
    }

    public function searchStudent(string $name): ?Student
    {
        foreach ($this->students as $student) {
            if (stripos($student->getFullName(), $name) !== false) {
                return $student;
            }
        }

        return null;
    }

    public function getStudents(): array
    {
        return $this->students;
    }
}
