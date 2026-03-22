<?php

namespace Intranet\Exceptions;

use Throwable;

/**
 * Excepció base de domini amb informació per a resposta i notificació.
 */
class IntranetException extends \Exception
{
    /**
     * @var int
     */
    protected int $status;

    /**
     * @var string
     */
    protected string $userMessage;

    /**
     * @var bool
     */
    protected bool $notify;

    /**
     * @var array<string, mixed>
     */
    protected array $context;

    /**
     * @param string $message
     * @param int $status
     * @param string|null $userMessage
     * @param bool $notify
     * @param array<string, mixed> $context
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Error intern',
        int $status = 500,
        ?string $userMessage = null,
        bool $notify = true,
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->status = $status;
        $this->userMessage = $userMessage ?? $message;
        $this->notify = $notify;
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * @return bool
     */
    public function shouldNotify(): bool
    {
        return $this->notify;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
