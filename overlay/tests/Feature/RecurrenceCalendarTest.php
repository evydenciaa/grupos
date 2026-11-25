<?php
namespace Tests\Feature;
use App\Services\RecurrenceCalendar;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
class RecurrenceCalendarTest extends TestCase {
 private function rule(): array{return ['starts_on'=>'2026-11-25','ends_on'=>'2026-11-30','weekdays'=>[1,2,3,4,5,6,7],'times'=>['18:00','09:00','09:00'],'interval_days'=>1];}
 public function test_sorts_deduplicates_and_converts_to_utc(): void {
  $out=RecurrenceCalendar::upcoming($this->rule(),CarbonImmutable::parse('2026-11-25 08:00','America/Sao_Paulo'),2,3);
  $this->assertSame(['2026-11-25 12:00','2026-11-25 21:00','2026-11-26 12:00'],array_map(fn($d)=>$d->format('Y-m-d H:i'),$out));
 }
 public function test_daily_authorization_limit(): void {$this->expectException(\InvalidArgumentException::class);RecurrenceCalendar::validate($this->rule(),1);}
 public function test_interval_days_and_end(): void {
  $rule=array_replace($this->rule(),['interval_days'=>2,'times'=>['09:00']]);
  $out=RecurrenceCalendar::upcoming($rule,CarbonImmutable::parse('2026-11-25 08:00','America/Sao_Paulo'),1);
  $this->assertSame(['25','27','29'],array_map(fn($d)=>$d->format('d'),$out));
 }
 public function test_no_past_occurrences(): void {$this->assertSame([],RecurrenceCalendar::upcoming($this->rule(),CarbonImmutable::parse('2026-12-01','America/Sao_Paulo'),2));}
}
