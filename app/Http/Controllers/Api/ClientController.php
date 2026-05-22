<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Client\CreateClientAPIRequest;
use App\Http\Requests\Api\Client\DeleteClientAPIRequest;
use App\Http\Requests\Api\Client\ReadClientAPIRequest;
use App\Http\Requests\Api\Client\ShowClientAPIRequest;
use App\Http\Requests\Api\Client\UpdateClientAPIRequest;
use App\Http\Resources\ClientResource;
use App\Repositories\ClientRepository;
use App\Repositories\Criteria\FiltersCriteria;
use App\Repositories\Criteria\OrderByCriteria;
use App\Repositories\Criteria\WhereFieldCriteria;
use App\Repositories\Criteria\WithCountCriteria;
use App\Repositories\Criteria\WithRelationshipsCriteria;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(private ClientRepository $clientRepository)
    {
    }

    public function index(ReadClientAPIRequest $request): JsonResponse
    {
        $this->clientRepository->resetCriteria();

        $this->clientRepository->pushCriteria(new WhereFieldCriteria('user_id', session('user_id')));

        if ($filters = $request->input('filters')) {
            $this->clientRepository->pushCriteria(new FiltersCriteria($filters));
        }

        $this->clientRepository->pushCriteria(new WithRelationshipsCriteria(['contacts', 'primaryContact']));
        $this->clientRepository->pushCriteria(new WithCountCriteria(['contacts']));
        $this->clientRepository->pushCriteria(new OrderByCriteria('created_at', 'desc'));

        $clients = $this->clientRepository->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            ClientResource::collection($clients),
            'Clients retrieved successfully'
        );
    }

    public function store(CreateClientAPIRequest $request): JsonResponse
    {
        $input = $request->validated();

        $client = $this->clientRepository->create($input);

        return $this->sendResponse(
            new ClientResource($client),
            'Client saved successfully',
            201
        );
    }

    public function show(ShowClientAPIRequest $request, int $id): JsonResponse
    {
        $this->clientRepository->resetCriteria();

        $this->clientRepository->pushCriteria(new WhereFieldCriteria('user_id', session('user_id')));
        $this->clientRepository->pushCriteria(new WithRelationshipsCriteria(['contacts', 'primaryContact']));
        $this->clientRepository->pushCriteria(new WithCountCriteria(['contacts']));

        $client = $this->clientRepository->find($id);

        return $this->sendResponse(
            new ClientResource($client),
            'Client retrieved successfully'
        );
    }

    public function update(UpdateClientAPIRequest $request, int $id): JsonResponse
    {
        $this->clientRepository->resetCriteria();

        $input = collect($request->validated())->except(['id', 'user_id'])->all();

        $client = $this->clientRepository->update($input, $id);

        return $this->sendResponse(
            new ClientResource($client),
            'Client updated successfully'
        );
    }

    public function destroy(DeleteClientAPIRequest $request, int $id): JsonResponse
    {
        $this->clientRepository->resetCriteria();

        $this->clientRepository->delete($id);

        return $this->sendResponse(true, 'Client deleted successfully');
    }
}
