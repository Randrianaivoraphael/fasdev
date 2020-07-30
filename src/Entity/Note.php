<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Note
{
    use TimsTamp;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="notes")
     */
    private $task;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }
}
