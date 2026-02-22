<?php

declare(strict_types=1);

namespace DomainDriven\BaseDomainStructure\Responder;

class JsonResponse extends \Illuminate\Http\JsonResponse
{
    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {
        parent::__construct($data, $status, $headers, $options, $json);

        /**
         * Media type according to:
         *
         * @link https://jsonapi.org/format/#jsonapi-media-type
         * @link http://www.iana.org/assignments/media-types/application/vnd.api+json.
         */
        $this->headers->set(key: 'Content-type', values: 'application/vnd.api+json');
        $this->headers->set(key: 'Charset', values: 'utf-8');

        /** Unique request identifier. */
        $this->headers->set(key: 'X-PID', values: 'uuid');

        /** Default encoding. */
        $this->encodingOptions = JSON_UNESCAPED_UNICODE;
    }
}
