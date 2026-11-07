<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrievanceMessage extends Model
{
    protected $fillable = ['grievance_id', 'sender_id', 'sender_name', 'sender_role', 'text'];

    public function grievance() { return $this->belongsTo(Grievance::class); }

    public function toPayload(): array
    {
        return [
            'id'          => $this->id,
            'sender_id'   => $this->sender_id,
            'sender_name' => $this->sender_name,
            'sender_role' => $this->sender_role,
            'text'        => $this->text,
            'time'        => $this->created_at?->format('M j, g:i A'),
        ];
    }
}
