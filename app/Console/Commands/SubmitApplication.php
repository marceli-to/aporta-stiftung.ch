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
    // Send an email to m@marceli.to
    \Mail::raw('The application has been submitted.', function($message) {
      $message->subject('Application submitted');
      $message->to('m@marceli.to');
    });
    //(new SubmitXml())->execute();
  }
}
