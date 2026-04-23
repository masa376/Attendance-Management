<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'clock_in', 'clock_out', 'date', 'note', 'status',
    ];

    protected $casts = [
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
        'date'      => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function restTimes()
    {
        return $this->hasMany(RestTime::class);
    }


    public function getTotalWorkMinutes():int
    {
        // 退勤していなければ計算不可
        if ($this->clock_out === null) {
            return 0;
        }

        $totalMinutes = $this->clock_in->diffInMinutes($this->clock_out);
        $breakMinutes = $this->getTotalBreakMinutes();

        return max(0, $totalMinutes - $breakMinutes);
    }


    public function getTotalBreakMinutes(): int
    {
        return $this->restTimes
            ->filter(fn($r) => $r->break_end !== null)
            ->sum(fn($r) => $r->break_start
                            ->diffInMinutes($r->break_end));
    }

    public function correctRequest()
    {
        return $this->hasOne(AttendanceCorrectRequest::class);
    }
}
