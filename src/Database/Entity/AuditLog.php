<?php

namespace App\Database\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AuditLog extends BaseEntity
{
    /**
     * @param string $entityClass
     * @param array $entityId
     * @param string $action
     * @param array|null $changes
     * @param int|null $userId
     * @param array|null $context
     */
    public function __construct(
        string $entityClass,
        array $entityId,
        string $action,
        ?array $changes,
        ?int $userId,
        ?array $context
    ) {
        parent::__construct();
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
        $this->action = $action;
        $this->changes = $changes;
        $this->userId = $userId;
        $this->context = $context;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @var string
     */
    #[ORM\Column(length: 255)]
    public string $entityClass {
        get {
            return $this->entityClass;
        }
        set {
            $this->entityClass = $value;
        }
    }

    /**
     * @var array
     */
    // Klucz główny encji (gdy złożony — zapisujemy JSON)
    #[ORM\Column(type: Types::JSON)]
    public array $entityId = [];

    /**
     * @var string
     */
    // insert|update|delete
    #[ORM\Column(length: 16)]
    public string $action {
        get {
            return $this->action;
        }
        set {
            $this->action = $value;
        }
    }

    /**
     * @var array|null
     */
    // Zmiany: ["field" => ["old", "new"]]
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $changes = null {
        get {
            return $this->changes;
        }
        set {
            $this->changes = $value;
        }
    }

    /**
     * @var string|int|null
     */
    // Kto wykonał (opcjonalnie)
    #[ORM\Column(nullable: true)]
    private ?string $userId = null {
        get {
            return $this->userId;
        }
        set {
            $this->userId = $value;
        }
    }

    /**
     * @var array|null
     */
    // Dodatkowy kontekst (IP, UA, requestId, itp.)
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    private ?array $context = null {
        get {
            return $this->context;
        }
        set {
            $this->context = $value;
        }
    }
}
