<?php
namespace App\Console\Commands;
use App\Actions\CreateXml;
use Illuminate\Console\Command;

class LoadJson extends Command
{
  protected $signature = 'load:json';

  protected $description = 'Loads a json file from /storage/app/json and creates an xml file from it.';

  public function handle()
  {
    // ask for the file name
    $file = $this->ask('Enter the name of the file you want to load (without the .json extension):');

    // check if the file exists
    if (!\Storage::exists('json/' . $file . '.json'))
    {
      $this->error('The file does not exist.');
      return;
    }

    // get the contents of the file
    $json = \Storage::get('json/' . $file . '.json');

    // create the xml
    (new CreateXml())->execute($json);
  }
}
