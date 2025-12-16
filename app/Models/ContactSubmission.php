<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $table = 'contact_submissions';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'read',
        'replied',
    ];

    protected $casts = [
        'read' => 'boolean',
        'replied' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
