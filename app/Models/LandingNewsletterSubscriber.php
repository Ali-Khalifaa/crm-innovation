<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingNewsletterSubscriber extends Model
{
    protected $table = 'landing_newsletter_subscribers';

    protected $fillable = [
        'email',
        'ip_address',
    ];
}
