<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class EmployeeResource extends ResourceCollection
{
    //define properti
    public $status;
    public $message;
    public $resource;

    /**
     * __construct
     *
     * @param  mixed $status
     * @param  mixed $message
     * @param  mixed $resource
     * @return void
     */
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
    }

    /**
     * toArray
     *
     * @param  mixed $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => [
                'employees' => $this->collection->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'image' => Str::startsWith($employee->image, 'http')
                            ? $employee->image
                            : asset('storage/employees/' . $employee->image),

                        'name' => $employee->name,
                        'phone' => $employee->phone,
                        'division' => [
                            'id' => $employee->division?->id,
                            'name' => $employee->division?->name,
                        ],
                        'position' => $employee->position,
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
