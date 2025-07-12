<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerAttendance extends Model
{
     use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'learner_id', // Kita akan ganti nama ini nanti menjadi user_id
        'date',
        'am_in',
        'am_out',
        'pm_in',
        'pm_out',
    ];

    /**
     * An attendance log belongs to a User.
     *
     * We keep the foreign key as 'learner_id' for now to avoid breaking
     * the database, but we rename the relationship method to 'user'
     * to reflect our new architecture.
     */
    public function user()
    {
        // Eloquent is smart enough to handle this as long as the foreign key is correct.
        // We will rename the column in a future migration.
        return $this->belongsTo(User::class, 'learner_id');
    }
}