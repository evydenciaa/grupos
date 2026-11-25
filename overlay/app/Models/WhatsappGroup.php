<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WhatsappGroup extends Model {
 protected $fillable=['instance_id','external_id','name','description','invitation_link','member_count','admin_only','is_admin','present','enabled','authorized','authorization_notes','last_message_at','metadata_at','seen_at'];
 protected function casts(): array {return ['admin_only'=>'boolean','is_admin'=>'boolean','present'=>'boolean','enabled'=>'boolean','authorized'=>'boolean','last_message_at'=>'datetime','metadata_at'=>'datetime','seen_at'=>'datetime'];}
 public function instance(){return $this->belongsTo(Instance::class);} public function publications(){return $this->hasMany(Publication::class);}
}
