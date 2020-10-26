<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      "title",
 *      message="Une annonce possède déjà le même titre"
 * )
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min=10,
     *      max=255,
     *      minMessage="Votre titre doit comporter au moins 10 caractères",
     *      maxMessage="Votre titre doit comporter au maximum 255 caractères"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(
     *      message ="Le prix de votre annonce doit être positif"
     * )
     * 
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min=10,
     *      minMessage="Votre introduction doit comporter au moins 10 caractères"
     * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min=100,
     *      minMessage="Votre contenu doit comporter au moins 100 caractères"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *      message="l'url '{{ value }}' n'est pas valide"
     * )
     * 
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *      message ="Le nombre de chambre doit être positif"
     * )
     * 
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
     * 
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Permet d'initailiser le slug !
     * 
     *@ORM\PrePersist
     *@ORM\PreUpdate
     * @return void
     */
    public function createSlug()
    {
        if(empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $user
     * 
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author) {
        foreach($this->comments as $comment) {
            if($comment->getAuthor() === $author) {
                return $comment;
            } 
        }
        return null;
    }

    /**
     * Permet d'obtenir la moyenne des notes pour cette annonce
     *
     * @return float
     */
    public function getAvgRatings() {
        // calculer la somme des notations
        $sum = array_reduce($this->comments->toArray(),function($total, $comment) {
            return $total + $comment->getRating();
        }, 0);
        // faire la moyenne
        if(count($this->comments) > 0) {
            return $sum / count($this->comments);  
        } else {
            return 0;
        }
    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce
     *
     * @return array Un tableau d'objet DateTime représentant les jours d'occupation
     */
    public function getNotAvailableDays() {
        // Est sensé contenir l'ensemble des journées de toute nos réservations pour cette annonce
        $notAvailableDays=[];

        foreach($this->bookings as $booking) {
            // Calculer les jours qui se trouvent entre la date d'arrivée et de départ en seconde
            $result = range(
               $booking->getStartDate()->getTimestamp(),
               $booking->getEndDate()->getTimestamp(),
               24*60*60
            );

            // Ont convertie les jours en chaine de caractère par de vrais jours au format y-m-d
            $days = array_map(function($dayTimestamp){
                return new \DateTime(date('y-m-d', $dayTimestamp));
            }, $result);

            // Ont ajoute c'est jour au tableau $notAvailableDays
            $notAvailableDays = array_merge($notAvailableDays,$days);
        }
        // On retourne le tableau
        return $notAvailableDays;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
