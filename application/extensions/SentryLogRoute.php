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
            $this->client->captureMessage(
                $log[0],
                array(
                    'logtime'       => $log[3],
                    'requestUri'    => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
                    'userAgent'     => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
                ),
                array(
                    'level'         => $log[1],
                    'category'      => $log[2],
                )
            );
        }
    }

}
