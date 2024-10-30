<?php
namespace App\Console\Commands;
use App\Actions\SubmitXmlExisting;
use Illuminate\Console\Command;

class SubmitExisting extends Command
{
  protected $signature = 'submit:existing';

  protected $description = 'Submits the application for existing tenants to the external server for processing.';

  public function handle()
  {
    (new SubmitXmlExisting())->execute();
  }
}
