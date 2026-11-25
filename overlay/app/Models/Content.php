<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Content extends Model {
 protected $fillable=['title','kind','path','mime','bytes','sha256','caption','enabled','expires_at'];
 protected function casts(): array {return ['enabled'=>'boolean','expires_at'=>'datetime'];}
 public function publications(){return $this->hasMany(Publication::class);}
}
