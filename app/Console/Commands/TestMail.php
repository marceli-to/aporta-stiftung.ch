<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class TestMail extends Command
{
  protected $signature = 'test:mail';

  protected $description = 'test mail command';

  public function handle()
  {
    $mailtext_user = "Vielen Dank für Ihre Wohnungsbewerbung.\n\nMit dem Einreichen des Formulars bekunden Sie Ihr Interesse für eine Wohnung und wir nehmen Sie in unsere Datenbank auf. Sollten wir ein Wohnungsangebot für Sie haben, melden wir uns bei Ihnen. Haben Sie bitte Verständnis, dass wir nicht allen Interessierten eine Wohnung anbieten können.\n\nBitte beachten Sie, dass die Anmeldung nur 6 Monate gültig ist und danach automatisch gelöscht wird. Sollten Sie nach Ablauf dieser Frist weiterhin eine Wohnung suchen, bitte wir Sie, die Anmeldung rechtzeitig zu verlängern. Bitte schreiben Sie uns dazu eine E-Mail an: wohnung@aporta-stiftung.ch. Wir werden Ihre Anmeldung dann um weitere 6 Monate verlängern.\n\nFreundliche Grüsse\n\nDr. Stephan à Porta-Stiftung";

    \Mail::raw($mailtext_user, function($message) {
      $message->replyTo('noreply@aporta-stiftung.ch');
      $message->subject('Vielen Dank für Ihre Anfrage');
      $message->to('m@marceli.to');
    });
  }
}
