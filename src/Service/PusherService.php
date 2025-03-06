<?php
namespace App\Service;

use Pusher\Pusher;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PusherService
{
    private Pusher $pusher;

    public function __construct(
        #[Autowire('%pusher.app_id%')] private string $appId,
        #[Autowire('%pusher.key%')] private string $key,
        #[Autowire('%pusher.secret%')] private string $secret,
        #[Autowire('%pusher.cluster%')] private string $cluster
    ) {
        $this->pusher = new Pusher($this->key, $this->secret, $this->appId, [
            'cluster' => $this->cluster,
            'useTLS' => true
        ]);
    }

    public function trigger(string $channel, string $event, array $data): void
    {
        $this->pusher->trigger($channel, $event, $data);
    }
}
