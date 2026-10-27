<?php

namespace App\Console\Commands;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendRandomExercise extends Command
{
    protected $signature = 'telegram:send-exercise';
    protected $description = 'Send a random exercise with its image to every linked Telegram user';

    public function handle(): int
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN is missing');
            return self::FAILURE;
        }

        $users = User::whereNotNull('telegram_chat_id')->get();
        $this->info('users with telegram_chat_id: ' . $users->count());

        foreach ($users as $user) {
            $exercise = Exercise::whereNotNull('image_path')->inRandomOrder()->first();
            if (!$exercise) {
                $this->warn('no exercise with image_path found');
                continue;
            }

            $caption = "💪 {$exercise->title_ar}\n\n"
                . "{$exercise->description_ar}\n\n"
                . "⏱ المدة: {$exercise->duration_seconds} ثانية";

            $path = public_path(ltrim($exercise->image_path, '/'));
            $base = "https://api.telegram.org/bot{$token}";

            if (is_file($path)) {
                $response = Http::attach('photo', file_get_contents($path), basename($path))
                    ->post("{$base}/sendPhoto", [
                        'chat_id' => $user->telegram_chat_id,
                        'caption' => mb_substr($caption, 0, 1000),
                    ]);
            } else {
                $response = Http::post("{$base}/sendMessage", [
                    'chat_id' => $user->telegram_chat_id,
                    'text'    => $caption,
                ]);
            }

            $this->line("user {$user->id}: " . ($response->successful() ? 'sent' : $response->body()));
            usleep(100000); // 0.1s between messages to stay within Telegram limits
        }

        return self::SUCCESS;
    }
}
