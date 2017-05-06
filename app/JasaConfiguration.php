<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JasaConfiguration extends Model
{
    protected $fillable = ['configured_by', 'item_id_jasa', 'item_id_produk', 'pembilang', 'penyebut'];

    public function produk()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id_produk');
    }
}
