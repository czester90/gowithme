<?php

namespace Application\Components\Response;


use Zend\View\Model\JsonModel;

class HttpResponse {

    const STATUS_SUCCESS = 200;
    const STATUS_NOT_FOUND = 404;
    const STATUS_VALIDATOR_ERROR = 400;

    /**
     * @param array $data
     *
     * @return string
     */
    public function successResponse($data = [])
    {
        return $this->createSendData($data);
    }

    /**
     * @param array $data
     * @param int $status
     *
     * @return string
     */
    public function errorResponse($data = [], $status = self::STATUS_VALIDATOR_ERROR)
    {
        return $this->createSendData($data, $status);
    }

    /**
     * @param array $data
     * @param int $status
     *
     * @return string
     */
    private function createSendData($data = [], $status = self::STATUS_SUCCESS)
    {
        return new JsonModel([
            'code'      => $status,
            'data'      => $data,
            'requestId' => $this->generateRequestId()
        ]);
    }

    /**
     * Generate Request ID
     *
     * @return string
     */
    private function generateRequestId()
    {
        $date = new \DateTime('now');
        return hash('sha256', $date->format('Y-m-d'));
    }
} 