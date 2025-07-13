<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'success' => $this->status,
            'message' => $this->message,
            'data'    => [
                'token' => $this->resource['token'],
                'admin' => [
                    'id'       => $this->resource['user']->id,
                    'name'     => $this->resource['user']->name,
                    'username' => $this->resource['user']->username,
                    'phone'    => $this->resource['user']->phone,
                    'email'    => $this->resource['user']->email,
                ],
            ],
        ];
    }
}
