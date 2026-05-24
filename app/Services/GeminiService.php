<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    protected string $model = 'gemini-flash-latest';

    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? '';
    }

    /**
     * Generate response from Gemini API given a structured conversation history.
     *
     * @param  array  $messages  Array of messages like [['role' => 'user', 'content' => 'hello'], ...]
     *
     * @throws \Exception
     */
    public function generateResponse(array $messages): string
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API Error: API Key is missing.');
            throw new \Exception('API Key Gemini belum dikonfigurasi. Silakan tambahkan GEMINI_API_KEY di file .env.');
        }

        // 1. Definisikan System Prompt untuk Pembatasan Topik Skripsi / Akademik
        $systemInstructionText = "Anda adalah SIBIMA Academic Assistant, kecerdasan buatan yang dirancang khusus untuk membantu mahasiswa dalam penyusunan Skripsi/Tugas Akhir. Anda memiliki keahlian mendalam dalam metodologi penelitian, penulisan ilmiah, pencarian literatur, analisis data, dan bimbingan akademik.\n\n"
            ."ATURAN UTAMA:\n"
            ."1. Anda HANYA boleh menjawab pertanyaan yang berkaitan dengan akademik, skripsi, metodologi penelitian, penulisan karya ilmiah, pencarian referensi, atau topik ilmiah terkait skripsi.\n"
            ."2. Jika pengguna menanyakan hal di luar topik skripsi/akademik (seperti hiburan, gosip, resep masakan, tips kecantikan, pemrograman non-akademik, dll.), Anda WAJIB menolak secara sopan dan mengarahkan kembali pengguna ke topik penyusunan skripsi mereka.\n"
            .'3. Berikan jawaban dalam Bahasa Indonesia secara terstruktur, ilmiah, solutif, santun, dan memotivasi mahasiswa.';

        $systemInstruction = [
            'parts' => [
                ['text' => $systemInstructionText],
            ],
        ];

        // 2. Format riwayat pesan agar sesuai dengan format Gemini API (role: user / model)
        $contents = [];
        foreach ($messages as $message) {
            $role = ($message['role'] === 'model' || $message['role'] === 'assistant') ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']],
                ],
            ];
        }

        $endpoint = "{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($endpoint, [
                'contents' => $contents,
                'systemInstruction' => $systemInstruction,
            ]);

            // 3. Penanganan Error HTTP Status
            if ($response->failed()) {
                $status = $response->status();

                if ($status === 429) {
                    throw new \Exception('Batas limit API tercapai. Silakan coba beberapa saat lagi.');
                }

                if ($status >= 500) {
                    throw new \Exception('Layanan Gemini API sedang tidak tersedia (down). Silakan coba lagi nanti.');
                }

                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'Terjadi kesalahan tidak dikenal pada API.';
                throw new \Exception("Gemini API Error (Status {$status}): {$errorMessage}");
            }

            // 4. Penanganan Respons Tidak Valid
            $responseData = $response->json();
            $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($text)) {
                throw new \Exception('Respons dari Gemini API tidak valid atau kosong.');
            }

            return $text;

        } catch (ConnectionException $e) {
            Log::error('Gemini Connection Failure: '.$e->getMessage());
            throw new \Exception('Koneksi ke Gemini API gagal. Pastikan jaringan internet Anda aktif.');
        } catch (\Exception $e) {
            Log::error('Gemini Service Error: '.$e->getMessage());
            throw $e;
        }
    }
}
