<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
class MediaInspector {
 public static function inspect(array $data): array {
  $disk=Storage::disk('local'); $path=$data['path']??'';
  if(!is_string($path)||!preg_match('~^campaign-media/[A-Za-z0-9_.-]+$~D',$path)||!$disk->exists($path)) throw ValidationException::withMessages(['data.path'=>'Arquivo inválido.']);
  $file=$disk->path($path); $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file); $kinds=['image/jpeg'=>'image','image/png'=>'image','video/mp4'=>'video']; $bytes=filesize($file);
  if(!isset($kinds[$mime])||!$bytes||$bytes>16*1024*1024) throw ValidationException::withMessages(['data.path'=>'Formato ou tamanho inválido.']);
  return array_merge($data,['mime'=>$mime,'kind'=>$kinds[$mime],'bytes'=>$bytes,'sha256'=>hash_file('sha256',$file)]);
 }
}
