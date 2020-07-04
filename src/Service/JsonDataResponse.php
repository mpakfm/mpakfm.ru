<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 04.07.2020
 * Time: 21:41
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

trait JsonDataResponse
{
    /**
     * @var array
     */
    public $jsonError;
    /**
     * @var bool
     */
    public $jsonResult;

    public function makeJsonResult($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if (!is_bool($this->jsonResult)) {
            $data['result'] = false;
        } else {
            $data['result'] = $this->jsonResult;
        }
        $data['error'] = ($this->jsonError ? $this->jsonError : []);

        if ($this->container->has('serializer')) {
            $json = $this->container->get('serializer')->serialize($data, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ], $context));

            return new JsonResponse($json, $status, $headers, true);
        }

        return new JsonResponse($data, $status, $headers);
    }
}
