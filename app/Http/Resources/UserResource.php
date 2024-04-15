<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'site_id' => $this->site_id,
            'username' => $this->username,
            'account_id' => $this->account_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'timezone' => $this->timezone,
            'account_manager_id' => $this->account_manager_id,
            'is_active' => $this->is_active,
            'has_crm_access' => $this->has_crm_access,
            'has_leads_access' => $this->has_leads_access,
            'is_staff' => $this->is_staff,
            'is_superuser' => $this->is_superuser,
            'last_login' => $this->last_login,
            // 'roles' => $this->getRoleNames(),
            // 'permissions' => $this->getPermissionNames(),
        ];
    }
}
