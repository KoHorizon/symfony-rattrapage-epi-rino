<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\Type\ClientType;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function list(): Response
    {
        $categories = $this->clientRepository->findAll();
        return $this->buildDataResponse($categories);
    }

    public function single(Client $client): Response
    {
        return $this->buildDataResponse($client);
    }

    public function add(Request $request): Response
    {
        $client = new Client();

        $form = $this->createForm(
            ClientType::class,
            $client,
            ['method' => 'POST']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);
        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $this->clientRepository->save($client, true);
        return $this->buildDataResponse($client);

    }

    public function update(Request $request, Client $client): Response
    {
        $form = $this->createForm(
            ClientType::class,
            $client,
            ['method' => 'PUT']
        );

        $parameters = json_decode($request->getContent(), true);
        $form->submit($parameters);
        if (!$form->isValid()) {
            return $this->buildFormErrorResponse($form);
        }

        $this->clientRepository->save($client, true);
        return $this->buildDataResponse($client);

    }

    public function delete(Client $client): Response
    {
        $this->clientRepository->remove($client, true);
        return $this->buildEmptyResponse();
    }
}
