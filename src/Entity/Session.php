<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_end;

    /**
     * @ORM\Column(type="integer")
     */
    private $place_number;

    /**
     * @ORM\ManyToOne(targetEntity=Formation::class, inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\ManyToMany(targetEntity=Intern::class, mappedBy="sessions")
     */
    private $interns;

    /**
     * @ORM\OneToMany(targetEntity=Program::class, mappedBy="session", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"module" = "ASC"})
     */
    private $programs;

    public function __construct()
    {
        $this->interns = new ArrayCollection();
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getPlaceNumber(): ?int
    {
        return $this->place_number;
    }

    public function setPlaceNumber(int $place_number): self
    {
        $this->place_number = $place_number;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection<int, Intern>
     */
    public function getInterns(): Collection
    {
        return $this->interns;
    }

    public function addIntern(Intern $intern): self
    {
        if (!$this->interns->contains($intern)) {
            $this->interns[] = $intern;
            $intern->addSession($this);
        }

        return $this;
    }

    public function removeIntern(Intern $intern): self
    {
        if ($this->interns->removeElement($intern)) {
            $intern->removeSession($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setSession($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getSession() === $this) {
                $program->setSession(null);
            }
        }

        return $this;
    }

    // METHODS

    // Method qui calcul le nombre de places restantes
    public function leftPlacesNumber() : ?int
    {
    
        $internsRegistredNumber = 0;

        for ($i=0; $i <= count($this->interns); $i++) { 
            $internsRegistredNumber = $i;
        }
        
        return  $this->place_number - $internsRegistredNumber;
    }

    // Method qui calcul le nombre de places reservées
    public function reservedPlacesNumber(): ?int
    {
       return $this->place_number - $this->leftPlacesNumber();
    }

    // Change le statut de la session à complet
    public function sessionStatus()
    {
        if ($this->leftPlacesNumber() === 0) {
            return $this->status = "<div class='alert-danger'>Session complète</div>";
        }
    }

    // TO STRING
    public function __toString()
    {
        return $this->formation->getTitle(). " ( du ". $this->date_start->format("d/m/Y"). " au ". $this->date_end->format("d/m/Y"). " )";
    }
}