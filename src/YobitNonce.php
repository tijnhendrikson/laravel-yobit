<?php namespace Pepijnolivier\Yobit;


use Illuminate\Database\Eloquent\Model;

class YobitNonce extends Model
{
    protected $table = 'yobit_nonces';
    protected $guarded = ['id'];
}