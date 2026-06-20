<?php

declare(strict_types=1);

namespace App\Logi;

use App\Database\Entity\ErrorLog;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DatabaseLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct(Level::Error, true);
    }

    protected function write(LogRecord $record): void
    {
//        if (!$this->entityManager->isOpen()) {
//            $connection = $this->entityManager->getConnection();
//            $configuration = $this->entityManager->getConfiguration();
//            $this->entityManager =  $this->entityManager->create($connection, $configuration);
//        }


//        $log = new ErrorLog();
//
//        $log->level = $record->level->getName();
//        $log->message = $record->message;
//        $log->channel = $record->channel;
//        $log->context = $this->normalize($record->context);
//        $log->extra = $this->normalize($record->extra);
//
//        $exception = $record->context['exception'] ?? null;
//
//        if ($exception instanceof \Throwable) {
//            $log->exceptionClass = $exception::class;
//            $log->trace = $exception->getTraceAsString();
//        }
//
//        $this->entityManager->persist($log);
//        $this->entityManager->flush();
//    }
//
//    private function normalize(array $data): array
//    {
//        return json_decode(json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR), true) ?? [];
//    }
}
}
