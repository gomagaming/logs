<?php

namespace GomaGaming\Logs\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Junges\Kafka\Facades\Kafka;
use GomaGaming\Logs\Services\LogService;

class ConsumeLogsEvents extends Command
{
    /**
     * @var array
     */
    protected array $messageData;

    /**
     * @var LogService
     */
    protected LogService $logService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumes GomaGaming Logs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Listens to Every GomaGaming Log.
     *
     * @return int
     */
    public function handle(LogService $logService)
    {
        if (config('gomagaminglogs.processing.connection') == 'redis')
        {
            return 0;
        }

        $this->logService = $logService;

        Redis::set('health-check:task:start:'.$this->signature, time());
        Redis::set('health-check:task:start:long-process:'.$this->signature, true);

        $consumer = Kafka::createConsumer([config('gomagaminglogs.processing.kafka.topic')], config('gomagaminglogs.processing.kafka.group'), config('gomagaminglogs.processing.kafka.brokers'));

        if (config('app.env') == 'production') {
            $productionOptions = [
                'security.protocol' => 'SASL_SSL',
                'sasl.mechanisms' => 'PLAIN',
                'sasl.username' => config('gomagaminglogs.processing.kafka.production.api-key'),
                'sasl.password' => config('gomagaminglogs.processing.kafka.production.api-secret'),
            ];

            $consumer = $consumer->withOptions($productionOptions);
        }

        $consumer = $consumer->withAutoCommit()
                            ->withHandler(function (\Junges\Kafka\Contracts\KafkaConsumerMessage $message) {
                                try {
                                    $this->messageData = $message->getBody()['message'];
                                    $this->handleMessage();
                                } catch (\Exception $exception) {
                                    report($exception);
                                }
                            })
                            ->build();

        $consumer->consume();
    }

    /**
     * Handles the Consumer Message.
     *
     * @return void
     */
    protected function handleMessage()
    {
        if (config('gomagaminglogs.process_jobs')) {
            $this->logService->process($this->messageData);
        }
    }
}
