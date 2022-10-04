<?php

namespace GomaGaming\Logs\Services;

use Junges\Kafka\Facades\Kafka;

class PublishMessageService
{
    /**
     * Publishes a Kafka Message on the given topic.
     *
     * @param  string  $topic
     * @param  mixed  $message
     * @param  array  $headers
     * @param  string  $key
     * @return void
     */
    public static function publish($topic, $message, $headers = [], $key = '')
    {
        $options = [
            'enable.idempotence' => 'true',
            'retries' => 3,
        ];

        if (config('app.env') == 'production') {
            $productionOptions = [
                'security.protocol' => 'SASL_SSL',
                'sasl.mechanisms' => 'PLAIN',
                'sasl.username' => config('gomagaminglogs.processing.kafka.production.api-key'),
                'sasl.password' => config('gomagaminglogs.processing.kafka.production.api-secret'),
            ];

            $options = array_merge($options, $productionOptions);
        }

        $producer = Kafka::publishOn($topic)
            ->withConfigOptions($options)
            ->withBodyKey('message', $message)
            ->withHeaders($headers)
            ->withKafkaKey($key);

        $producer->send();
    }
}
