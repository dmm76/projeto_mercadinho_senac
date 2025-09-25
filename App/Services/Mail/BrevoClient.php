<?php declare(strict_types=1);

namespace App\Services\Mail;

use RuntimeException;

final class BrevoClient
{
    public function __construct(private string $apiKey) {}

    /**
     * @param array{sender: array{email:string,name?:string}, to: array<int, array{email:string,name?:string}>, subject:string, htmlContent:string, textContent?:string, replyTo?: array{email:string,name?:string}} $payload
     */
    public function send(array $payload): void
    {
        $url = 'https://api.brevo.com/v3/smtp/email';
        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        if (function_exists('curl_init')) {
            $this->sendWithCurl($url, $body);
            return;
        }

        $this->sendWithStream($url, $body);
    }

    private function sendWithCurl(string $url, string $body): void
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Nao foi possivel inicializar CURL para Brevo.');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'api-key: ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            throw new RuntimeException('Falha ao conectar a API Brevo: ' . $error, $errno);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('API Brevo retornou status ' . $status . ': ' . $response);
        }
    }

    private function sendWithStream(string $url, string $body): void
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\napi-key: {$this->apiKey}\r\n",
                'content' => $body,
                'timeout' => 15,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $error = error_get_last();
            $msg = $error !== null ? $error['message'] : 'Resposta vazia.';
            throw new RuntimeException('Falha ao conectar a API Brevo: ' . $msg);
        }

        $status = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $status = (int) $matches[1];
        }

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('API Brevo retornou status ' . $status . ': ' . $response);
        }
    }
}
