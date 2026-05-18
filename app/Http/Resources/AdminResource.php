<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'username'     => $this->username,
            'full_name'    => $this->full_name,
            'email'        => $this->email,
            'power'        => $this->power,
            'role'         => $this->roleName(),
            'last_login_at' => $this->last_login_at,
            'created_at'   => $this->created_at,
        ];
    }
}
