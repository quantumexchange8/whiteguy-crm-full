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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'timezone' => $this->timezone,
            'country' => $this->country,
            'account_holder' => $this->account_holder,
            'account_type' => $this->account_type,
            'customer_type' => $this->customer_type,
            'account_manager_id' => $this->account_manager_id,
            'lead_status' => $this->lead_status,
            'client_stage' => $this->client_stage,
            'rank' => $this->rank,
            'remark' => $this->remark,
            'previous_broker_name' => $this->previous_broker_name,
            'kyc_status' => $this->kyc_status,
            'is_active' => $this->is_active,
            'has_crm_access' => $this->has_crm_access,
            'has_leads_access' => $this->has_leads_access,
            'is_staff' => $this->is_staff,
            'is_superuser' => $this->is_superuser,
            'last_login' => $this->last_login,
            'date_joined' => $this->date_joined,
            // 'roles' => $this->getRoleNames(),
            // 'permissions' => $this->getPermissionNames(),
        ];
    }
}
