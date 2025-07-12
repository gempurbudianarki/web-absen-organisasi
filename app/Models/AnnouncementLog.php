<?php

// app/Models/AnnouncementLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementLog extends Model
{
    protected $fillable = [
        'announcement_id',
        'learner_id', // Tetap menggunakan nama kolom ini, tapi isinya adalah user_id
        'is_sent',
        'sent_at',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * An announcement log belongs to a User.
     * The foreign key is 'learner_id' but it points to the 'users' table's id.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'learner_id');
    }
}