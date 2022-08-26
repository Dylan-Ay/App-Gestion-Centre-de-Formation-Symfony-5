<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgramRepository::class)
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberDays;

    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="programs")
     */
    private $session;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="programs")
     */
    private $module;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberDays(): ?int
    {
        return $this->numberDays;
    }

    public function setNumberDays(int $numberDays): self
    {
        $this->numberDays = $numberDays;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function __toString()
    {
        return $this->module->getTitle();
    }
}
