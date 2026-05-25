@extends('layouts.app')

@section('content')
	<h1>Vielen Dank für Ihre Anmeldung</h1>

	<p>
		Ihre Anmeldung ist bei uns eingegangen. Wir prüfen Ihre Angaben und
		melden uns bei Ihnen, sobald wir eine passende Wohnung anbieten können.
	</p>

	{{-- Do NOT echo a reference number here. The backend assigns it
	     asynchronously and it is not yet available at this point. --}}
@endsection
