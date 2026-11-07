<?php

namespace App\Models;

use App\Support\Grievance as Lookup;
use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'title', 'description', 'category', 'status',
        'address', 'has_image', 'image_mime', 'image_data', 'admin_feedback', 'responding_sector', 'message_count',
    ];

    protected $casts = ['message_count' => 'integer', 'has_image' => 'boolean'];

    protected $hidden = ['image_data'];

    /** Every column except the heavy base64 photo. */
    public const LITE = [
        'id', 'user_id', 'user_name', 'title', 'description', 'category', 'status',
        'address', 'has_image', 'image_mime', 'admin_feedback', 'responding_sector',
        'message_count', 'created_at', 'updated_at',
    ];

    public function scopeLite($q) { return $q->select(self::LITE); }

    /** Route-model binding never drags the photo blob into memory. */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->lite()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function messages() { return $this->hasMany(GrievanceMessage::class)->orderBy('id'); }

    public function getImageUrlAttribute(): ?string
    {
        return $this->has_image ? url('/api/grievances/' . $this->id . '/photo') : null;
    }

    /** Shape sent to the browser by the JSON endpoints. */
    public function toPayload(): array
    {
        $cat    = Lookup::CATEGORIES[$this->category] ?? Lookup::CATEGORIES['other'];
        $status = Lookup::STATUSES[$this->status] ?? Lookup::STATUSES['pending'];
        $sector = Lookup::sector($this->responding_sector);

        return [
            'id'               => $this->id,
            'ref'              => 'SNB-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT),
            'user_id'          => $this->user_id,
            'user_name'        => $this->user_name,
            'title'            => $this->title,
            'description'      => $this->description,
            'category'         => $this->category,
            'category_label'   => $cat['label'],
            'category_icon'    => $cat['icon'],
            'status'           => $this->status,
            'status_label'     => $status['label'],
            'status_color'     => $status['color'],
            'status_bg'        => $status['bg'],
            'address'          => $this->address,
            'image_url'        => $this->image_url,
            'admin_feedback'   => $this->admin_feedback,
            'responding_sector'=> $this->responding_sector,
            'sector_name'      => $sector['name'] ?? null,
            'sector_full'      => $sector['full'] ?? null,
            'sector_icon'      => $sector['icon'] ?? null,
            'message_count'    => $this->message_count,
            'submitted_at'     => $this->created_at?->toIso8601String(),
            'submitted_label'  => $this->created_at?->format('M j, Y · g:i A'),
            'updated_label'    => $this->updated_at?->format('M j, Y · g:i A'),
        ];
    }
}
