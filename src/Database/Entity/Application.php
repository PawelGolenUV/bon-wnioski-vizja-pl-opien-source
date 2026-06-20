<?php

declare(strict_types=1);

namespace App\Database\Entity;

use AllowDynamicProperties;
use App\Core\Application\ApplicationRepository;
use App\Database\Entity\Application\EducationalProcess;
use App\Database\Entity\Application\LanguageInterpreter;
use App\Database\Entity\Application\SpecialisedEquipment;
use App\Database\Entity\Application\TeachingAssistant;
use App\Database\Entity\Dictionary\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;

#[AllowDynamicProperties]
#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: 'application')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'educational_process' => EducationalProcess::class,
    'language_interpreter' => LanguageInterpreter::class,
    'specialised_equipment' => SpecialisedEquipment::class,
    'teaching_assistant' => TeachingAssistant::class,
])]
abstract class Application extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();

        $this->adaptation_dictionary = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->status = 'Nowy';
    }

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column(type: 'integer', length: 5, nullable: true)]
    public ?int $albumNumber = null {
        get { return $this->albumNumber; }
        set { $this->albumNumber = $value; }
    }

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    public ?string $applicationNumber = null {
        get { return $this->applicationNumber; }
        set { $this->applicationNumber = $value; }
    }

//    #[ORM\Column(name: 'department')]
//    public string $department = '' {
//        get { return $this->department; }
//        set { $this->department = $value; }
//    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'adaptationCard', referencedColumnName: 'id', nullable: true)]
    public ?Item $adaptationCard = null {
        get { return $this->adaptationCard; }
        set { $this->adaptationCard = $value; }
    }

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    public ?string $employeeComment = null {
        get { return $this->employeeComment; }
        set { $this->employeeComment = $value; }
    }

    #[ORM\Column(type: 'datetime_immutable', length: 255, nullable: true)]
    public ?\DateTimeImmutable $employeeCommentDate = null {
        get { return $this->employeeCommentDate; }
        set { $this->employeeCommentDate = $value; }
    }

    #[Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $applicationDetailsSeen = null {
        get { return $this->applicationDetailsSeen; }
        set { $this->applicationDetailsSeen = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'faculty', referencedColumnName: 'id', nullable: true)]
    public ?Item $faculty = null {
        get { return $this->faculty; }
        set { $this->faculty = $value; }
    }

    #[ORM\Column(name: 'dean')]
    public string $dean = '' {
        get { return $this->dean; }
        set { $this->dean = $value; }
    }

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'application', cascade: ['persist'], orphanRemoval: true)]
    public Collection $files {
        get { return $this->files; }
        set { $this->files = $value; }
    }

    #[ORM\Column(type: 'phone_number', nullable: true)]
    public ?\libphonenumber\PhoneNumber $phone = null {
        get { return $this->phone; }
        set { $this->phone = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'study_mode', referencedColumnName: 'id', nullable: true)]
    public ?Item $studyMode = null {
        get { return $this->studyMode; }
        set { $this->studyMode = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Student::class)]
    public Student $student {
        get { return $this->student; }
        set { $this->student = $value; }
    }

    #[ORM\Column(type: 'text', length: 36, nullable: true)]
    public ?string $status = null {
        get { return $this->status; }
        set { $this->status = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'type', referencedColumnName: 'id', nullable: false)]
    public ?Item $type = null {
        get { return $this->type; }
        set { $this->type = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'year', referencedColumnName: 'id', nullable: true)]
    public ?Item $year = null {
        get { return $this->year; }
        set { $this->year = $value; }
    }

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[ORM\JoinColumn(name: 'semester', referencedColumnName: 'id', nullable: true)]
    public ?Item $semester = null {
        get { return $this->semester; }
        set { $this->semester = $value; }
    }
}
