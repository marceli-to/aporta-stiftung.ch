<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class TestEncoding extends Command
{
  protected $signature = 'test:encoding';

  protected $description = 'Test encoding of a string';

  public function handle()
  {
    $value = 'm@marceli.to';

    // Convert the string to UTF-8
    $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');

    dd($value);
  }
}
