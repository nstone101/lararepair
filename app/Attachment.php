<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $table = 'attachments';
}
