<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

   
class Status extends Model
{
     use Searchable;

    protected $guarded = ['id'];

    protected $searchables = ['label'];

    protected $primaryKey = 'id';
    protected $table = 'statuses';
}
