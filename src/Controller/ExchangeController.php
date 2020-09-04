<?php

namespace App\Controller;

use App\Repository\ExchangeRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ExchangeController
 * @package App\Controller
 *
 * @Route(path="/api/")
 */
class ExchangeController
{
    private $exchangeRepository;

    private $client;

    public function __construct(ExchangeRepository $exchangeRepository, HttpClientInterface $client)
    {
        $this->exchangeRepository = $exchangeRepository;
        $this->client = $client;
    }

    /**
     * @Route("exchange", name="exchange", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $from = $data['from_currency'];
            $to = $data['to_currency'];
            $param = "{$from}_{$to}";

            if (empty($from) || empty($to)) {
                throw new \Exception('Expecting mandatory parameters!', 400);
            }

            $response = $this->client->request(
                'GET',
                'https://free.currconv.com/api/v7/convert?q=' . $param . '&compact=ultra&apiKey=6c052614c59da92fbe6f'
            );
    
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception($response->getContent(), $statusCode);
            }
            $content = $response->toArray();            
            $amount = $content['currency'];

            $exchange = $this->exchangeRepository->findOneBy([
                'from_currency' => $from,
                'to_currency' => $to]
            );

            if ($exchange) {
                if ($exchange->getAmount() != $amount) {
                    empty($amount) ? true : $exchange->setAmount($amount);
                    $this->exchangeRepository->updateExchange($exchange);
                    return new JsonResponse(['status' => 'Currency updated!'], Response::HTTP_OK);    
                }
                return new JsonResponse(['status' => 'Currency does not have changes!'], Response::HTTP_OK);
                
            }

            $this->exchangeRepository->saveExhange($from, $to, $amount);

            return new JsonResponse(['status' => 'Currency saved!'], Response::HTTP_CREATED);
        } catch(Exception $e){
            return new JsonResponse(['status' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @Route("exchange", name="get_all_exchange", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $currencies = $this->exchangeRepository->findAll();
        $data = [];

        foreach ($currencies as $currency) {
            $data[] = [
                'id' => $currency->getId(),
                'from' => $currency->getFromCurrency(),
                'to' => $currency->getToCurrency(),
                'amount' => $currency->getAmount(),
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }
}