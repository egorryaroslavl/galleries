<?php
namespace Egorryaroslavl\Galleries\Models;

use Illuminate\Database\Eloquent\Model;

    class GalleryModel extends Model
    {
        protected $table = 'galleries';
	    public $act = '';

        protected $fillable = [
            'name',
            'alias',
            'description',
            'icon',
            'gallery',
            'pos',
            'public',
            'anons',
            'hit',
            'h1',
            'metatag_title',
            'metatag_description',
            'metatag_keywords' ];

        protected $casts = [
            'public'  => 'boolean',
            'anons'   => 'boolean',
            'hit'     => 'boolean',
            'gallery' => 'array',
        ];
    }
