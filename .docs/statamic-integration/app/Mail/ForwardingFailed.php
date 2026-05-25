<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to engineering when ForwardApplicationToBackend exhausts retries
 * or hits a permanent 4xx. The submission's full payload is preserved in
 * the failed_jobs table — this email is just the heads-up.
 */
class ForwardingFailed extends Mailable
{
	use Queueable, SerializesModels;

	public function __construct(
		public string $submissionId,
		public string $reason,
	) {}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: "[Aporta] Application forwarding failed: {$this->submissionId}",
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'emails.forwarding-failed',
			with: [
				'submissionId' => $this->submissionId,
				'reason' => $this->reason,
			],
		);
	}
}
