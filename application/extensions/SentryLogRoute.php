<?php

class SentryLogRoute extends \CLogRoute
{
    public $dsn;

    /**
     *
     * @var \Raven_Client
     */
    private $client;

    public function init()
    {
        parent::init();

        $this->client = new \Raven_Client($this->dsn);
    }

    /**
     * Write log messages
     * @param array $logs list of messages
     */
    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $this->client->capture(array(
                'message' => $log[0],
                'level' => $log[1],
                'tags' => array(
                    $log[2], // category
                ),
            ));
        }
    }

}
