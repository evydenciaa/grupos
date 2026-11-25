<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Publication extends Model {
 protected $fillable=['whatsapp_group_id','content_id','scheduled_at','status','approved_by','approved_at','caption_snapshot','path_snapshot','hash_snapshot','kind_snapshot','message_id','zaap_id','error','submitted_at'];
 protected function casts(): array {return ['scheduled_at'=>'datetime','approved_at'=>'datetime','submitted_at'=>'datetime'];}
 public function group(){return $this->belongsTo(WhatsappGroup::class,'whatsapp_group_id');} public function content(){return $this->belongsTo(Content::class);}
}
