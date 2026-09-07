<?php

use App\Services\Sms\NikitaSmsGateway;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'sms.nikita.base_url' => 'https://smspro.nikita.kg/api',
        'sms.nikita.login' => 'toipoikg',
        'sms.nikita.password' => 'secret',
        'sms.nikita.sender' => 'EduJob',
        'sms.nikita.test_mode' => false,
    ]);
});

function fakeNikitaResponse(int $status, string $message = ''): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
<id>ABC123</id>
<status>{$status}</status>
<phones>1</phones>
<smscnt>1</smscnt>
<message>{$message}</message>
</response>
XML;
}

test('sends the request in the documented XML format', function () {
    Http::fake([
        'smspro.nikita.kg/api/message' => Http::response(fakeNikitaResponse(0), 200),
    ]);

    (new NikitaSmsGateway)->send('+996555123456', 'EduJob: ваш код — 123456');

    Http::assertSent(function ($request) {
        expect($request->url())->toBe('https://smspro.nikita.kg/api/message');

        $xml = new SimpleXMLElement($request->body());

        expect((string) $xml->login)->toBe('toipoikg');
        expect((string) $xml->pwd)->toBe('secret');
        expect((string) $xml->sender)->toBe('EduJob');
        expect((string) $xml->phones->phone)->toBe('996555123456');
        expect(isset($xml->test))->toBeFalse();

        return true;
    });
});

test('test_mode adds the test flag and does not throw on status 11', function () {
    config(['sms.nikita.test_mode' => true]);

    Http::fake([
        'smspro.nikita.kg/api/message' => Http::response(fakeNikitaResponse(11), 200),
    ]);

    (new NikitaSmsGateway)->send('996555123456', 'test');

    Http::assertSent(function ($request) {
        $xml = new SimpleXMLElement($request->body());

        return (string) $xml->test === '1';
    });
});

test('throws when the gateway rejects the message', function () {
    Http::fake([
        'smspro.nikita.kg/api/message' => Http::response(fakeNikitaResponse(2), 200),
    ]);

    (new NikitaSmsGateway)->send('996555123456', 'test');
})->throws(RuntimeException::class);

test('throws on a gateway http error', function () {
    Http::fake([
        'smspro.nikita.kg/api/message' => Http::response('', 500),
    ]);

    (new NikitaSmsGateway)->send('996555123456', 'test');
})->throws(RuntimeException::class);
