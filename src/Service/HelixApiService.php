<?php

namespace IdapGroup\HelixApiBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use IdapGroup\HelixApiBundle\Exception\ApiResponseException;

class HelixApiService
{
    /**
     * @param Client $client
     * @param string $url
     * @param string $key
     * @param string $secret
     * @param string $culture
     */
    public function __construct(
        private readonly Client $client,
        private readonly string $url,
        private readonly string $key,
        private readonly string $secret,
        private readonly string $culture
    ) {
    }

    /**
     * @param array $errors
     * @return void
     */
    private function processingError(array $errors): void
    {
        throw new ApiResponseException('Helix API Error. ', $errors);
    }

    /**
     * @param string $path
     * @return mixed|void
     * @throws GuzzleException
     */
    public function helixGetRequest(string $path)
    {
        try {
            $response = $this->client->get($this->url . DIRECTORY_SEPARATOR . $path, [
                'auth' => [
                    $this->key,
                    $this->secret
                ]
            ]);

            $requestData = json_decode($response->getBody()->getContents(), true);

            if ($requestData['errors']) {
                $this->processingError($requestData['errors']);
            }

            return $requestData;

        } catch (ClientException $exception) {
            $this->processingError(json_decode($exception->getResponse()->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            $this->processingError(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @return mixed|void
     * @throws GuzzleException
     */
    public function helixPostRequest(string $path, array $data)
    {
        try {
            $response = $this->client->post($this->url . DIRECTORY_SEPARATOR . $path, [
                'content-type' => 'application/json',
                'auth' => [
                    $this->key,
                    $this->secret
                ],
                'json' => $data,
            ]);

            $requestData = json_decode($response->getBody()->getContents(), true);

            if ($requestData['errors']) {
                $this->processingError($requestData['errors']);
            }

            return $requestData;

        } catch (ClientException $exception) {
            $this->processingError(json_decode($exception->getResponse()->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            $this->processingError(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function getProgram(): mixed
    {
        return $this->helixGetRequest('program/get');
    }

    /**
     * @param string|null $culture
     * @return mixed|void
     * @throws GuzzleException
     */
    public function listBankDocuments(string $culture = null)
    {
        $culture = $culture ?? $this->culture;
        $response = $this->helixGetRequest("bankDocument/list/{$culture}");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param int $documentId
     * @param string|null $culture
     * @return mixed|void
     * @throws GuzzleException
     */
    public function downloadBankDocument(int $documentId, string $culture = null)
    {
        $culture = $culture ?? $this->culture;

        return $this->helixGetRequest("bankDocument/download/{$culture}/{$documentId}");
    }

    /**
     * @param int $id
     * @return mixed|null
     * @throws GuzzleException
     */
    public function listCustomerDueDiligenceResponses(int $id): mixed
    {
        return $this->helixGetRequest("customer/answerList/{$id}");
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function listDueDiligenceQuestions(): mixed
    {
        $response = $this->helixGetRequest("program/questionsList");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function dueDiligenceAnswer(array $data): mixed
    {
        return $this->helixPostRequest('customer/answerPost', $data);
    }

    /**
     * @param int $id
     * @return mixed|null
     * @throws GuzzleException
     */
    public function listDueDiligenceResponses(int $id): mixed
    {
        $response = $this->helixGetRequest("customer/answerList/{$id}");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function createCustomer(array $data): mixed
    {
        return $this->helixPostRequest('customer/create', $data);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws GuzzleException
     */
    public function createBusinessCustomer(array $data): mixed
    {
        return $this->helixPostRequest('customer/createBusiness', $data);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws GuzzleException
     */
    public function createApplication(array $data): mixed
    {
        return $this->helixPostRequest('customer/createBusinessApplication', $data);
    }

    /**
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function createAccount(array $data): mixed
    {
        return $this->helixPostRequest('account/create', $data);
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function program(): mixed
    {
        $response = $this->helixGetRequest("program/get");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws GuzzleException
     */
    public function createExternalAccount(array $data): mixed
    {
        return $this->helixPostRequest('externalAccount/create', $data);
    }

    /**
     * @param int $customerId
     * @param int $accountId
     * @return mixed
     * @throws GuzzleException
     */
    public function getAccount(int $customerId, int $accountId): mixed
    {
        $response = $this->helixGetRequest("account/get/{$customerId}/{$accountId}");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param int $customerId
     * @param int $accountId
     * @param string|null $beginDate
     * @param string|null $endDate
     * @return mixed
     * @throws GuzzleException
     */
    public function transactions(int $customerId, int $accountId, ?string $beginDate = null, ?string $endDate = null): mixed
    {
        $today = new \DateTime();

        if (!$beginDate) {
            $beginDate = $today->modify('-30 days')->format('Y-m-d');
        }

        if (!$endDate) {
            $endDate = $today->format('Y--m-d');
        }

        $response = $this->helixGetRequest("transaction/list/{$customerId}/{$accountId}/{$beginDate}/{$endDate}");

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function initiateExternalAccount(array $data): mixed
    {
        return $this->helixPostRequest('externalAccount/initiate', $data);
    }

    /**
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function verifyExternalAccount(array $data): mixed
    {
        return $this->helixPostRequest('externalAccount/verify', $data);
    }

    /**
     * @param int $customerId
     * @param int $externalAccountId
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getExternalAccount(int $customerId, int $externalAccountId): mixed
    {
        $response = $this->helixGetRequest("externalAccount/get/{$customerId}/{$externalAccountId}");

        return isset($response['data']) ? $response['data'] : null;
    }
}