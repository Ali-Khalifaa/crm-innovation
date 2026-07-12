<?php

namespace App\Models;

use App\Traits\SearchFilterTrait;
use App\Traits\TranslationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory , SoftDeletes , TranslationsTrait,SearchFilterTrait;

    protected $table = 'colors';

    /**
     * The attributes that are mass assignable.
    */
    protected $guarded = ['id'];


}
