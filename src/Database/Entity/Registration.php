<?php

namespace App\Database\Entity;

use App\Database\Entity\Dictionary\Item;
use App\Database\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[ORM\Table(name: 'registration')]
#[ORM\Index(name: 'idx_lesson_starts_at', columns: ['starts_at'])]
#[ORM\Index(name: 'idx_lesson_ends_at', columns: ['ends_at'])]
class Registration extends BaseEntity
{

    public function __construct()
    {
        parent::__construct();
        $this->registeredStudents = new ArrayCollection();
    }

    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: RegisteredStudent::class, mappedBy: 'registration', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public Collection $registeredStudents {
        get {
            return $this->registeredStudents;
        }
        set {
            $this->registeredStudents = $value;
        }
    }

    /**
     * @var string
     */
    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'registration_type', referencedColumnName: 'id', nullable: true)]
    public Item $title {
        get {
            return $this->title;
        }
        set {
            $this->title = $value;
        }
    }

    /**
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column(type: 'datetime_immutable')]
    public ?\DateTimeImmutable $startsAt = null {
        get {
            return $this->startsAt;
        }
        set {
            $this->startsAt = $value;
        }
    }

    /**
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column(type: 'datetime_immutable')]
    public ?\DateTimeImmutable $endsAt = null {
        get {
            return $this->endsAt;
        }
        set {
            $this->endsAt = $value;
        }
    }

    /**
     * @var int
     */
    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    public int $capacity {
        get {
            return $this->capacity;
        }
        set {
            $this->capacity = $value;
        }
    }

    /**
     * @var int
     */
//    #[ORM\Column(type: 'smallint', nullable: true, options: ['unsigned' => true, 'default' => 0],)]
//    public int $registered = 0 {
//        get {
//            return $this->registered;
//        }
//        set {
//            $this->registered = $value;
//        }
//    }

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'specialist', referencedColumnName: 'id', nullable: true)]
    public ?User $specialist = null {
        get {
            return $this->specialist;
        }
        set {
            $this->specialist = $value;
        }
    }

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null {
        get {
            return $this->description;
        }
        set {
            $this->description = $value;
        }
    }

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $teamsMeetingUrl = null {
        get {
            return $this->teamsMeetingUrl;
        }
        set {
            $this->teamsMeetingUrl = $value;
        }
    }

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $eventId = null {
        get {
            return $this->eventId;
        }
        set {
            $this->eventId = $value;
        }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'registration_language', referencedColumnName: 'id', nullable: false)]
    public Item $language {
        get {
            return $this->language;
        }
        set {
            $this->language = $value;
        }
    }
}
