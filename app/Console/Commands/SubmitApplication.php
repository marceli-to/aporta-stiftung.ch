<?php
namespace App\Console\Commands;
use App\Actions\SubmitXml;
use Illuminate\Console\Command;

class SubmitApplication extends Command
{
  protected $signature = 'submit:application';

  protected $description = 'Submits the application to the external server for processing.';

  public function handle()
  {
    (new SubmitXml())->execute();
  }
}
