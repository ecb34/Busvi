<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait;
    public $timestamps = false;

    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'body', 'slug'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];

    protected $appends = ['public_yn', 'private_yn', 'private_user_yn'];

    public function getPublicYnAttribute(){
        return $this->public ? 'Si' : 'No';
    }

    public function getPrivateYnAttribute(){
        return $this->private ? 'Si' : 'No';
    }

    public function getPrivateUserYnAttribute(){
        return $this->private_user ? 'Si' : 'No';
    }

}
