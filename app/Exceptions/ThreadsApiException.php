<?php

namespace App\Exceptions;

use RuntimeException;

class ThreadsApiException extends RuntimeException
{
    /**
     * @param  array<string, mixed>|null  $errorSummary
     */
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $requestId = null,
        public readonly ?array $errorSummary = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function userMessage(): string
    {
        return match ($this->statusCode) {
            400 => 'Não foi possível processar a autorização. Tente conectar novamente.',
            401, 403 => 'Acesso negado pela API do Threads. Verifique as permissões do app.',
            429 => 'Muitas requisições à API do Threads. Aguarde um momento e tente novamente.',
            default => 'Não foi possível conectar com o Threads. Tente novamente.',
        };
    }
}
