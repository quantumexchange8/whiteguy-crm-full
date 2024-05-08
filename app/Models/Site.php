<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Site extends Model
{
    use HasFactory;

    protected $table = "django_site";

    protected $fillable = [
        'domain',
        'name',
    ];

    /**
     * Get the users for the site.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'site_id');
    }

    /**
     * SaleOrder Model
     * Get user's orders.
     */
    public function saleorder(): HasMany
    {
        return $this->hasMany(SaleOrder::class, 'site_id');
    }
    
    /**
     * Announcement Model
     * Get the sites that belong to the announcement.
     */
    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'core_siteannouncement');
    }
}
