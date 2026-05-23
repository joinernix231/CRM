<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;

class BaseJsonResource extends JsonResource
{
    public static $wrap = 'data';

    /**
     * @param  mixed  $resource
     * @return AnonymousResourceCollection|array<string, mixed>|null
     */
    public static function collection($resource): array|AnonymousResourceCollection|null
    {
        if ($resource instanceof MissingValue) {
            return null;
        }

        if ($resource instanceof AbstractPaginator) {
            return static::paginatedResponse($resource);
        }

        return parent::collection($resource);
    }

    /**
     * Paginated payload (flat meta keys at root, like legacy APIs).
     *
     * @return array<string, mixed>
     */
    protected static function paginatedResponse(AbstractPaginator $paginator): array
    {
        return [
            'success' => true,
            'data' => parent::collection($paginator->items())->resolve(),
            'current_page' => $paginator->currentPage(),
            'first_page_url' => $paginator->url(1),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Use in controllers: return response()->json(ClientResource::paginated($clients));
     *
     * @return array<string, mixed>
     */
    public static function paginated(AbstractPaginator $paginator): array
    {
        return static::paginatedResponse($paginator);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function with($request): array
    {
        return [
            'success' => true,
        ];
    }
}
