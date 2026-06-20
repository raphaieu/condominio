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
        $message = is_string($this->errorSummary['message'] ?? null)
            ? $this->errorSummary['message']
            : '';

        if (str_contains($message, 'threads_basic') || str_contains($message, 'Threads testers')) {
            return 'Seu usuário ainda não tem acesso de teste ao app na Meta. '
                .'Peça ao administrador do app para adicioná-lo como testador do Threads '
                .'e aceite o convite antes de tentar novamente.';
        }

        return match ($this->statusCode) {
            400 => 'Não foi possível processar a autorização. Tente conectar novamente.',
            401, 403 => 'Acesso negado pela API do Threads. Verifique as permissões do app.',
            429 => 'Muitas requisições à API do Threads. Aguarde um momento e tente novamente.',
            default => 'Não foi possível conectar com o Threads. Tente novamente.',
        };
    }
}
