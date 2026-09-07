<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPartner extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'school_partners';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'level',
        'logo_path',
        'is_active',
        'order',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
    
    /**
     * Scope a query to only include active partners.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to order partners by display order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
    
    /**
     * Get monogram from school name (first two letters).
     *
     * @return string
     */
    public function getMonogramAttribute()
    {
        // Get the first two letters of the name
        // Handle cases where name might be very short or contain special characters
        $cleanName = preg_replace('/[^a-zA-Z0-9\s]/', '', $this->name);
        $words = explode(' ', trim($cleanName));
        
        // If we have multiple words, take first letter of first two words
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        // Otherwise, take first two letters of the single word
        return strtoupper(substr($cleanName, 0, 2));
    }
}
