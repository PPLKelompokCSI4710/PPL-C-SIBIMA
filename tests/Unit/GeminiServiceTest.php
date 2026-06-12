<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set a dummy api key for testing
        Config::set('services.gemini.key', 'dummy-key');
    }

    public function test_can_successfully_generate_response_from_gemini(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Ini adalah jawaban asisten akademik.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GeminiService;
        $response = $service->generateResponse([
            ['role' => 'user', 'content' => 'Bagaimana menentukan metode penelitian skripsi?'],
        ]);

        $this->assertEquals('Ini adalah jawaban asisten akademik.', $response);

        Http::assertSent(function ($request) {
            $this->assertStringContainsString('dummy-key', $request->url());
            $body = $request->data();
            $this->assertArrayHasKey('contents', $body);
            $this->assertArrayHasKey('systemInstruction', $body);
            $this->assertEquals('user', $body['contents'][0]['role']);

            return true;
        });
    }

    public function test_throws_exception_if_api_key_is_missing(): void
    {
        Config::set('services.gemini.key', '');

        $service = new GeminiService;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Key Gemini belum dikonfigurasi.');

        $service->generateResponse([
            ['role' => 'user', 'content' => 'Test'],
        ]);
    }

    public function test_handles_rate_limiting_429_error(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 429),
        ]);

        $service = new GeminiService;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Batas limit kuota API tercapai dari penyedia AI.');

        $service->generateResponse([
            ['role' => 'user', 'content' => 'Test'],
        ]);
    }

    public function test_handles_api_down_500_error(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
        ]);

        $service = new GeminiService;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Layanan Gemini API sedang tidak tersedia (down).');

        $service->generateResponse([
            ['role' => 'user', 'content' => 'Test'],
        ]);
    }

    public function test_handles_invalid_response_format(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [],
            ], 200),
        ]);

        $service = new GeminiService;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Respons dari Gemini API tidak valid atau kosong.');

        $service->generateResponse([
            ['role' => 'user', 'content' => 'Test'],
        ]);
    }

    public function test_sends_multi_turn_history_correctly(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Ini jawaban lanjutan.']]]],
                ],
            ], 200),
        ]);

        $service = new GeminiService;
        $response = $service->generateResponse([
            ['role' => 'user', 'content' => 'Halo'],
            ['role' => 'model', 'content' => 'Halo! Ada yang bisa saya bantu?'],
            ['role' => 'user', 'content' => 'Bagaimana cara buat skripsi?'],
        ]);

        $this->assertEquals('Ini jawaban lanjutan.', $response);

        Http::assertSent(function ($request) {
            $body = $request->data();
            $this->assertCount(3, $body['contents']);
            $this->assertEquals('user', $body['contents'][0]['role']);
            $this->assertEquals('model', $body['contents'][1]['role']);
            $this->assertEquals('user', $body['contents'][2]['role']);

            return true;
        });
    }

    public function test_handles_off_topic_prompt_refusal(): void
    {
        // Simulate Gemini refusing an off-topic question based on the system instruction
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Maaf, saya hanya dapat membantu topik terkait skripsi dan akademik.']]]],
                ],
            ], 200),
        ]);

        $service = new GeminiService;
        $response = $service->generateResponse([
            ['role' => 'user', 'content' => 'Berikan saya resep nasi goreng enak'],
        ]);

        $this->assertEquals('Maaf, saya hanya dapat membantu topik terkait skripsi dan akademik.', $response);
    }
}
