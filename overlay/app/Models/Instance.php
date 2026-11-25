<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Instance extends Model {
 protected $fillable=['name','external_id','token','client_token','phone','connected','enabled','synced_at'];
 protected function casts(): array {return ['token'=>'encrypted','client_token'=>'encrypted','connected'=>'boolean','enabled'=>'boolean','synced_at'=>'datetime'];}
 protected $hidden=['token','client_token']; public function groups(){return $this->hasMany(WhatsappGroup::class);}
}
