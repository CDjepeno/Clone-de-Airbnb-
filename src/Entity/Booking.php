<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface",message="veuillez rentrer un date valide")
     * @Assert\GreaterThan("today", message="La date d'arriver doit être ultérieure à la date d'aujourd'hui !",groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface",message="veuillez rentrer un date valide")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être plus éloigner que la date d'arriver")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * Callback appelé à chaque fois qu'on crée une réservation
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prepPersist() {
        // Initialisation de la date de réservation 
        if(empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        // Calcule du prix d'un séjour
        if(empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    /**
     * Permet de vérifier la disponibilité d'une annonce
     *
     * @return boolean
     */
    public function isBookableDates() {
        // Les dates qui sont impossible pour une réservation
        $notAvailableDays = $this->ad->getNotAvailableDays();
        
        // Comparer les dates choisies avec les dates impossibles
        
        //Journée qui correspond a ma reservation
        $bookingDays     = $this->getDays();

        // Variable qui permet de convertir les DatesTimestamp en jour chaine de caractère
        $formatDay       = function($day){
            return $day->format('d-m-y');
        };
        
        // Tableau des chaines de caractères des journées qui correspond a ma reservation 
        $days             = array_map($formatDay, $bookingDays);
        
        // Tableau des chaines de caractères des dates qui sont impossible pour une réservation
        $notAvailableDays = array_map($formatDay, $notAvailableDays);

        // Ont vérifie si mes journées correspondent au journée pas disponibles 
        foreach($days as $day) {
            if(in_array( $day, $notAvailableDays)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma reservation
     *
     * @return array Un tableau d'objets DateTime représentant les jours de la réservation
     */
    public function getDays() {
        // Ont Calcule les jours qui se trouvent entre la date d'arrivée et de départ
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
               24*60*60
        );
        // Ont convertie les jours en chaine de caractère par de vrais jours
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('y-m-d',$dayTimestamp));
        }, $result);

        return $days;
    }


    public function getDuration() {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
