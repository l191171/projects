<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable=[
        'patientname',
        
        'patientname',
        'requestid',
        'sampleid',
        'subject',
        'department',
        'priority',
        'message',
        'ticketid',
    ];
}
