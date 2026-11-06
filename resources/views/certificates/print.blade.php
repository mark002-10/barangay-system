<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $serviceRequest->request_type }} · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="certificate-page">
    <div class="certificate-toolbar">
        <a class="secondary-action" href="{{ route('requests.review', $serviceRequest) }}">Back to request</a>
        <p>Review this draft against the barangay’s approved certificate format before signing.</p>
        <button class="primary-action" type="button" onclick="window.print()">Print or save as PDF</button>
    </div>

    <main class="certificate-paper">
        <header class="certificate-heading">
            <p>BARANGAY ESTANZA</p>
            <h1>{{ strtoupper($serviceRequest->request_type) }}</h1>
        </header>

        <div class="certificate-body">
            <p class="certificate-opening">TO WHOM IT MAY CONCERN:</p>
            @if ($serviceRequest->request_type === 'Certificate of Residency')
                <p>This is to certify that <strong>{{ $serviceRequest->resident->full_name }}</strong> is recorded as a resident of Barangay Estanza, with the address:</p>
                <p class="certificate-address">{{ $serviceRequest->resident->address }}</p>
            @else
                <p>This document records that the barangay office reviewed and marked the request for <strong>{{ $serviceRequest->request_type }}</strong> as issued to <strong>{{ $serviceRequest->resident->full_name }}</strong>.</p>
            @endif
            <p>This certificate is issued upon the resident’s request for the following purpose:</p>
            <p class="certificate-purpose">{{ $serviceRequest->purpose }}</p>
            <p>Issued this {{ ($serviceRequest->processed_at ?? $serviceRequest->updated_at)->format('jS') }} day of {{ ($serviceRequest->processed_at ?? $serviceRequest->updated_at)->format('F Y') }}.</p>
        </div>

        <footer class="certificate-footer">
            <div><span>Reference number</span><strong>BE-CERT-{{ str_pad((string) $serviceRequest->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
            <div class="certificate-signature"><span>Authorized signature</span></div>
        </footer>
    </main>
</body>
</html>
