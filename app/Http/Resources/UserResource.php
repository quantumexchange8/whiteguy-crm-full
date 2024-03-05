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
            'site' => $this->site,
            'username' => $this->username,
            'account_id' => $this->account_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_legal_name' => $this->full_legal_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'country_of_citizenship' => $this->country_of_citizenship,
            'account_holder' => $this->account_holder,
            'account_type' => $this->account_type,
            'customer_type' => $this->customer_type,
            'account_manager' => $this->account_manager,
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
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getPermissionNames(),
        ];
    }
}
