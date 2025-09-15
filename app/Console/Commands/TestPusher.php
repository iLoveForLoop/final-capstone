<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pusher\Pusher;

class TestPusher extends Command
{
    protected $signature = 'test:pusher';
    protected $description = 'Test Pusher connection';

    public function handle()
    {
        try {
            // Use the same approach that worked in tinker
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'useTLS' => true,
                    // Remove any problematic options and use minimal config
                ]
            );

            $result = $pusher->trigger('test-channel', 'test-event', [
                'message' => 'Hello from Laravel!'
            ]);

            if ($result) {
                $this->info('✅ Pusher test message sent successfully!');
                $this->info('Response: ' . json_encode($result));
            } else {
                $this->error('❌ Pusher trigger returned false');
            }
        } catch (\Exception $e) {
            $this->error('❌ Pusher test failed: ' . $e->getMessage());
            $this->error('Error details: ' . $e->getTraceAsString());
        }
    }
}