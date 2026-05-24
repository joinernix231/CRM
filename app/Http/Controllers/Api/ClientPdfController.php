<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Client\ShowClientAPIRequest;
use App\Repositories\ClientRepository;
use App\Repositories\Criteria\WhereFieldCriteria;
use App\Repositories\Criteria\WithRelationshipsCriteria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ClientPdfController extends Controller
{
    private const STATUS_LABELS = [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'prospect' => 'Prospecto',
    ];

    public function __construct(private ClientRepository $clientRepository)
    {
    }

    public function show(ShowClientAPIRequest $request, int $id): Response
    {
        $this->clientRepository->resetCriteria();

        $this->clientRepository->pushCriteria(new WhereFieldCriteria('user_id', session('user_id')));
        $this->clientRepository->pushCriteria(new WithRelationshipsCriteria(['contacts']));

        $client = $this->clientRepository->find($id);

        $client->setRelation(
            'contacts',
            $client->contacts->sortBy('name')->sortByDesc('is_primary')->values()
        );

        $statusLabel = self::STATUS_LABELS[$client->status] ?? $client->status;
        $generatedAt = now()->timezone(config('app.timezone'))->format('d/m/Y H:i');

        $pdf = Pdf::loadView('pdf.client-detail', [
            'client' => $client,
            'statusLabel' => $statusLabel,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'portrait');

        $filename = 'cliente-'.Str::slug($client->name).'-'.$client->id.'.pdf';

        return $pdf->download($filename);
    }
}
