<?php

namespace App\Homeowners;

class Person
{
    private string $title;
    private ?string $firstName;
    private string $lastName;
    private ?string $initial;

    public function __construct(string $title, ?string $firstName, string $lastName, ?string $initial = null)
    {
        $this->title = $title;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->initial = $initial;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getInitial(): ?string
    {
        return $this->initial;
    }

    /**
     * Convert the Person object to an array representation
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'initial' => $this->initial
        ];
    }

    /**
     * Create a Person object from an array representation
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['first_name'] ?? null,
            $data['last_name'],
            $data['initial'] ?? null
        );
    }
}
