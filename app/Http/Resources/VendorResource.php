<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'company_name'        => $this->company_name,
            'phone'               => $this->phone,
            'address'             => $this->address,
            'verification_status' => $this->verification_status,
            'verification_notes'  => $this->verification_notes,
            'verified_at'         => $this->verified_at?->toIso8601String(),
            'user'                => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ],
        ];
    }
}
