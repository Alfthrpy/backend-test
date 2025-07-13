<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DivisionResource extends ResourceCollection
{
    public $status;
    public $message;

    public function __construct($status = 'success', $message = '',$resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => [
                'divisions' => $this->collection->map(function ($division) {
                    return [
                        'id' => $division->id,
                        'name' => $division->name,
                    ];
                }),
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
            ],
        ];
    }
}
