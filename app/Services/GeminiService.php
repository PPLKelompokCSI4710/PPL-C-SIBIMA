<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    protected string $model = 'gemini-flash-lite-latest';

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
        // Check if a mock response is set in AppSetting (useful for Dusk/Feature testing)
        $mockResponse = \App\Models\AppSetting::get('gemini_mock_response');
        if ($mockResponse !== null) {
            if ($mockResponse === 'FORCE_TIMEOUT' || $mockResponse === 'FORCE_ERROR') {
                throw new \Exception('Koneksi ke Gemini API gagal. Pastikan jaringan internet Anda aktif.');
            }
            if ($mockResponse === 'DYNAMIC') {
                $lastMsg = end($messages)['content'] ?? '';
                if (preg_match('/(cuaca|resep|masak|makan|lelucon|puisi|berita)/i', $lastMsg)) {
                    return "Maaf, saya hanya dapat membantu Anda dalam konteks pendidikan, penyusunan skripsi, dan bimbingan akademik. Silakan ajukan pertanyaan seputar topik tersebut.";
                }
                if (preg_match('/kualitatif/i', $lastMsg)) {
                    return "Metode kualitatif adalah metode penelitian yang fokus pada pemahaman mendalam tentang fenomena sosial.";
                }
                if (preg_match('/langkah menyusunnya/i', $lastMsg)) {
                    return "Langkah menyusun metode kualitatif meliputi penentuan fokus, pengumpulan data, dan analisis tematik.";
                }
                return "Ini adalah jawaban relevan seputar skripsi dan metodologi penelitian akademik.";
            }
            return $mockResponse;
        }

        if (empty($this->apiKey)) {
            Log::error('Gemini API Error: API Key is missing.');
            throw new \Exception('API Key Gemini belum dikonfigurasi. Silakan tambahkan GEMINI_API_KEY di file .env.');
        }

        // 1. Definisikan System Prompt untuk Pembatasan Topik Pendidikan / Skripsi / Bimbingan
        $defaultPrompt = "Anda adalah SIBIMA Academic Assistant, kecerdasan buatan yang dirancang khusus untuk membantu mahasiswa dalam konteks pendidikan, penyusunan Skripsi/Tugas Akhir, dan bimbingan akademik.\n\n"
            ."ATURAN MUTLAK DAN TIDAK BOLEH DILANGGAR:\n"
            ."1. Anda HANYA diizinkan merespons pertanyaan yang secara spesifik berkaitan dengan konteks PENDIDIKAN, SKRIPSI, atau BIMBINGAN AKADEMIK.\n"
            ."2. Jika pengguna menanyakan APAPUN di luar topik pendidikan, skripsi, atau bimbingan (misalnya: membuat lelucon, resep makanan, menulis kode untuk proyek non-akademik, membuat puisi, berita umum, dll.), Anda WAJIB menolak untuk menjawab.\n"
            ."3. Untuk pertanyaan di luar konteks, berikan respons baku berikut (atau variasi sopan serupa): \"Maaf, saya hanya dapat membantu Anda dalam konteks pendidikan, penyusunan skripsi, dan bimbingan akademik. Silakan ajukan pertanyaan seputar topik tersebut.\"\n"
            ."4. Berikan jawaban dalam Bahasa Indonesia secara terstruktur, ilmiah, solutif, santun, dan memotivasi mahasiswa saat menjawab pertanyaan yang valid.";

        $systemInstructionText = \App\Models\AppSetting::get('ai_system_prompt', $defaultPrompt);

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
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'Terjadi kesalahan tidak dikenal pada API.';

                if ($status === 429) {
                    throw new \Exception('Batas limit kuota API tercapai dari penyedia AI. Silakan coba beberapa saat lagi.');
                }

                if ($status === 503) {
                    throw new \Exception('Layanan AI sedang menerima permintaan tinggi (Overloaded). Spikes in demand biasanya sementara. Silakan coba lagi nanti.');
                }

                if ($status >= 500) {
                    throw new \Exception('Layanan Gemini API sedang tidak tersedia (down). Silakan coba lagi nanti.');
                }

                throw new \Exception("Gemini API Error (Status {$status}): {$errorMessage}");
            }

            // 4. Penanganan Respons Tidak Valid
            $responseData = $response->json();
            $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($text)) {
                throw new \Exception('Respons dari Gemini API tidak valid atau kosong.');
            }

            // Hapus format Markdown seperti ** (bold) dan ## (headers) sesuai permintaan
            $text = str_replace('**', '', $text);
            $text = preg_replace('/^#+\s*/m', '', $text); // Menghapus header markdown (#, ##, ###) di awal baris

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
