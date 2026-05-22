<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Contact\CreateContactAPIRequest;
use App\Http\Requests\Api\Contact\DeleteContactAPIRequest;
use App\Http\Requests\Api\Contact\ReadContactAPIRequest;
use App\Http\Requests\Api\Contact\ShowContactAPIRequest;
use App\Http\Requests\Api\Contact\UpdateContactAPIRequest;
use App\Http\Resources\ContactResource;
use App\Repositories\ContactRepository;
use App\Repositories\Criteria\OrderByCriteria;
use App\Repositories\Criteria\WhereFieldCriteria;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private ContactRepository $contactRepository)
    {
    }

    public function index(ReadContactAPIRequest $request, int $client): JsonResponse
    {
        $this->contactRepository->pushCriteria(new WhereFieldCriteria('client_id', $client));
        $this->contactRepository->pushCriteria(new OrderByCriteria('created_at', 'desc'));

        $contacts = $this->contactRepository->paginate();

        return $this->sendResponse(
            ContactResource::collection($contacts),
            'Contacts retrieved successfully'
        );
    }

    public function store(CreateContactAPIRequest $request, int $client): JsonResponse
    {
        $input = $request->validated();

        $contact = $this->contactRepository->create($input);

        return $this->sendResponse(
            new ContactResource($contact),
            'Contact saved successfully',
            201
        );
    }

    public function show(ShowContactAPIRequest $request, int $client, int $contact): JsonResponse
    {
        $this->contactRepository->pushCriteria(new WhereFieldCriteria('client_id', $client));

        $contact = $this->contactRepository->find($contact);

        return $this->sendResponse(
            new ContactResource($contact),
            'Contact retrieved successfully'
        );
    }

    public function update(UpdateContactAPIRequest $request, int $client, int $contact): JsonResponse
    {
        $input = collect($request->validated())->except(['id', 'client_id'])->all();

        $contact = $this->contactRepository->update($input, $contact);

        return $this->sendResponse(
            new ContactResource($contact),
            'Contact updated successfully'
        );
    }

    public function destroy(DeleteContactAPIRequest $request, int $client, int $contact): JsonResponse
    {
        $this->contactRepository->delete($contact);

        return $this->sendResponse(true, 'Contact deleted successfully');
    }
}
