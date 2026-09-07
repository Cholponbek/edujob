<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;

/**
 * Боевой драйвер smspro.nikita.kg (XML/HTTP-протокол, POST
 * http://smspro.nikita.kg/api/message). Формат запроса/ответа и коды
 * статусов — из официальной документации провайдера, не догадка.
 */
class NikitaSmsGateway implements SmsGateway
{
    /**
     * Коды статуса ответа шлюза (раздел 1 протокола) — используются
     * только для читаемых сообщений в логе/исключении.
     */
    private const STATUS_MESSAGES = [
        0 => 'Сообщения успешно приняты к отправке',
        1 => 'Ошибка в формате запроса',
        2 => 'Неверная авторизация',
        3 => 'Недопустимый IP-адрес отправителя',
        4 => 'Недостаточно средств на счету клиента',
        5 => 'Недопустимое имя отправителя (не подтверждено администратором smspro.nikita.kg)',
        6 => 'Сообщение заблокировано по стоп-словам',
        7 => 'Некорректный номер телефона получателя',
        8 => 'Неверный формат времени отправки',
        9 => 'Превышение времени обработки запроса — нужно повторить с тем же id через 5-10 сек',
        10 => 'Отправка заблокирована из-за повторения id',
        11 => 'Тестовый запрос (test=1) — не тарифицирован',
    ];

    public function send(string $phone, string $message): void
    {
        $config = config('sms.nikita');

        // Шлюз принимает номер как 996550123456 либо +996550123456 —
        // нормализация телефона уже сделана вызывающим кодом
        // (PhoneOtpController::normalizePhone), здесь просто убираем "+".
        $phone = ltrim($phone, '+');

        $messageId = strtoupper(Str::random(10));

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><message></message>');
        $xml->addChild('login', htmlspecialchars($config['login'] ?? ''));
        $xml->addChild('pwd', htmlspecialchars($config['password'] ?? ''));
        $xml->addChild('id', $messageId);
        $xml->addChild('sender', htmlspecialchars($config['sender']));
        $xml->addChild('text', htmlspecialchars($message));
        $phones = $xml->addChild('phones');
        $phones->addChild('phone', $phone);

        if ($config['test_mode']) {
            $xml->addChild('test', '1');
        }

        $response = Http::withBody($xml->asXML(), 'application/xml')
            ->post($config['base_url'].'/message');

        if ($response->failed()) {
            Log::error('Nikita SMS gateway HTTP error', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('SMS-шлюз недоступен (HTTP '.$response->status().')');
        }

        $result = @simplexml_load_string($response->body());
        $status = $result !== false ? (int) $result->status : null;

        // 0 — успех, 11 — тестовый запрос без реальной отправки (ожидаемо
        // при test_mode=true), обе ветки — не ошибка.
        if (! in_array($status, [0, 11], true)) {
            $statusMessage = self::STATUS_MESSAGES[$status] ?? 'Неизвестный статус ответа шлюза';

            Log::error('Nikita SMS gateway rejected message', [
                'phone' => $phone,
                'status' => $status,
                'status_message' => $statusMessage,
                'gateway_message' => (string) ($result->message ?? ''),
            ]);

            throw new RuntimeException("SMS-шлюз отклонил сообщение: {$statusMessage}");
        }
    }
}
