@extends('layouts.portal')
@section('title', 'Dashboard')
@section('content')
                <div class="page-heading"><div><p class="eyebrow">BARANGAY ESTANZA <span>/</span> OVERVIEW</p><h1>Welcome back, {{ auth()->user()->name }}</h1><p class="heading-copy">Here’s what’s happening in your community today.</p></div><span class="preview-badge live-badge"><span></span> Live records</span></div>

                <section class="welcome-banner" aria-label="Welcome to Barangay Estanza">
                    <div class="banner-copy"><span class="banner-kicker">A stronger community, together</span><h2>Serving Estanza<br>with care.</h2><p>Your community at a glance.</p></div>
                    <svg class="hall-illustration" viewBox="0 0 780 250" role="img" aria-label="Illustration of a barangay hall surrounded by tropical greenery">
                        <defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#d7e6d4"/><stop offset="1" stop-color="#f5ead1"/></linearGradient><linearGradient id="hall" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#fffdf3"/><stop offset="1" stop-color="#e8dfc5"/></linearGradient></defs>
                        <path fill="url(#sky)" d="M0 0h780v250H0z"/><path fill="#bdcdb1" d="M0 170 92 82l82 75 113-110 109 112 94-72 129 89 86-75 75 53v96H0z"/><path fill="#91aa88" d="m0 191 117-81 92 86 110-76 121 75 100-89 112 92 80-54 48 37v69H0z"/>
                        <path fill="#6d9973" d="M0 224c72-51 108-21 171-43 71-25 117-14 183 18 72 35 145-17 211-10 76 8 128 37 215 18v43H0z"/>
                        <g transform="translate(247 52)"><path fill="#728c64" d="m22 73 115-60 115 60H22Z"/><path fill="#d9c991" d="m35 70 102-51 102 51v12H35z"/><path fill="url(#hall)" stroke="#c7bc9d" d="M48 81h178v113H48z"/><path fill="#f8f3de" stroke="#c7bc9d" d="M26 77h222v19H26z"/><path fill="#a9b49a" d="M116 92h43v102h-43z"/><path fill="#748969" d="M112 91h51v9h-51z"/><path fill="#ece5ca" d="M62 105h38v43H62zM174 105h38v43h-38z"/><path fill="#738b70" d="M68 111h26v31H68zM180 111h26v31h-26z"/><path fill="#cfbd75" d="M124 31h27v29h-27z"/><circle cx="137.5" cy="43" r="8" fill="#82946e"/><path stroke="#c7bc9d" d="M48 158h178M48 172h178"/></g>
                        <g fill="#526f4f"><path d="M85 215c8-48 16-77 35-110-3 39-6 74-13 110h-22Z"/><path d="M79 156c-28-29-38-54-35-76 25 20 41 44 49 71l-14 5ZM111 139c17-35 39-55 67-61-12 29-31 52-58 69l-9-8ZM92 112C73 75 70 47 80 22c19 27 28 54 27 83l-15 7Z"/><path d="M680 218c4-46 11-89 27-128 1 43-1 86-8 128h-19Z"/><path d="M693 135c-27-25-40-49-40-72 24 16 42 37 53 63l-13 9ZM707 118c12-29 30-49 56-58-8 26-24 48-47 65l-9-7ZM686 91c-18-29-23-54-16-77 17 21 27 45 28 71l-12 6Z"/></g>
                    </svg>
                    <span class="banner-sun" aria-hidden="true"></span>
                </section>

                <section class="metrics-grid" aria-label="Community statistics">
                    @foreach ($metrics as $metric)
                        <article class="metric-card">
                            <span class="metric-icon {{ $metric['tone'] }}">
                                @if ($metric['icon'] === 'residents')<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                @elseif ($metric['icon'] === 'requests')<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h8v4H8zM6 5H4v17h16V5h-2M8 12h8M8 16h5"/><circle cx="18" cy="18" r="4" fill="currentColor"/><path d="M18 16v2l1 1" stroke="#fff"/></svg>
                                @else<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Zm0 0v6h6M8 13h5M8 17h4"/><path d="m14 17 2 2 4-4"/></svg>@endif
                            </span>
                            <div class="metric-copy"><span>{{ $metric['label'] }}</span><strong>{{ $metric['value'] }}</strong><small><span class="trend-dot"></span>From saved records</small></div>
                            <span class="metric-watermark" aria-hidden="true">{{ $loop->iteration }}</span>
                        </article>
                    @endforeach
                </section>

                <section class="content-grid">
                    <article class="panel overview-panel" id="reports">
                        <div class="panel-heading"><div class="panel-title"><span class="panel-icon green-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V9M10 19V5M16 19v-7M22 19V3M2 21h21"/></svg></span><div><h2>Community overview</h2><p>Monthly service requests · {{ $year }}</p></div></div></div>
                        <div class="chart" role="img" aria-label="Monthly service request chart for {{ $year }}">
                            <div class="chart-y-axis"><span>{{ $chartMax }}</span><span>{{ round($chartMax * .75) }}</span><span>{{ round($chartMax * .5) }}</span><span>{{ round($chartMax * .25) }}</span><span>0</span></div>
                            <div class="chart-plot"><div class="chart-gridlines"><i></i><i></i><i></i><i></i><i></i></div><div class="chart-bars">@foreach ($monthlyRequests as $index => $value)<div class="chart-column"><div class="chart-bar {{ $index === now()->month - 1 ? 'highlight' : '' }}" style="--bar-height: {{ ($value / $chartMax) * 100 }}%" title="{{ $value }} requests"></div><span>{{ $months[$index] }}</span></div>@endforeach</div></div>
                        </div>
                        <p class="chart-note">Request totals from saved records</p>
                    </article>

                    <article class="panel announcement-panel" id="announcements">
                        <div class="panel-heading"><div class="panel-title"><span class="panel-icon gold-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 11 18-5v12L3 13v-2Zm0 0v2a4 4 0 0 0 4 4h2l2 5h3l-2.2-6.1M21 10v4"/></svg></span><div><h2>Announcements</h2><p>Community updates</p></div></div><a class="text-action" href="{{ route('announcements.index') }}">Manage</a></div>
                        <div class="announcement-card">@if ($announcement)<span class="announcement-label"><span></span> COMMUNITY NOTICE</span><h3>{{ $announcement->title }}</h3><p>{{ $announcement->body }}</p><a href="{{ route('announcements.index') }}">Read announcement <span aria-hidden="true">→</span></a>@else<span class="announcement-label"><span></span> COMMUNITY NOTICE</span><h3>No published announcements</h3><p>Published community updates will appear here.</p><a href="{{ route('announcements.create') }}">Create an announcement <span aria-hidden="true">→</span></a>@endif<svg class="announcement-art" viewBox="0 0 150 130" aria-hidden="true"><path fill="#dce8d5" d="M0 87c28-28 51-27 75-9 23 18 46 13 75-7v59H0z"/><path fill="#829779" d="M100 70c3-29 10-49 24-67 0 27-2 48-8 67h-16Z"/><path fill="#627c5d" d="M112 30c-18-17-24-31-21-44 16 10 27 23 34 40l-13 4ZM123 23c9-19 22-30 40-34-6 17-17 29-33 39l-7-5Z"/><path fill="#c2ae7a" d="M38 79h59v51H38z"/><path fill="#839679" d="m31 81 36-24 37 24H31Z"/><path fill="#f7f2df" d="M45 87h45v43H45z"/><path fill="#788c70" d="M62 100h12v30H62z"/><circle cx="68" cy="74" r="7" fill="#a3ae8a"/></svg></div>
                    </article>
                </section>

                <section class="panel requests-panel" id="requests">
                    <div class="panel-heading requests-heading"><div class="panel-title"><span class="panel-icon green-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Zm0 0v6h6M8 13h8M8 17h8"/></svg></span><div><h2>Recent requests</h2><p>Latest activity from residents</p></div></div><a class="view-all" href="{{ route('requests.index') }}">View all <span aria-hidden="true">›</span></a></div>
                    <div class="table-wrap"><table><thead><tr><th scope="col">Resident</th><th scope="col">Request</th><th scope="col">Date</th><th scope="col">Status</th><th scope="col"><span class="sr-only">Action</span></th></tr></thead><tbody>@forelse ($recentRequests as $serviceRequest)<tr><td><span class="resident-cell"><span class="resident-avatar">{{ strtoupper(substr($serviceRequest->resident_display_name, 0, 1)) }}</span>{{ $serviceRequest->resident_display_name }}</span></td><td>{{ $serviceRequest->request_type }}</td><td>{{ $serviceRequest->created_at->format('M j, Y') }}</td><td><span class="status-pill {{ $serviceRequest->status }}"><i></i>{{ ucfirst($serviceRequest->status) }}</span></td><td><a class="text-action" href="{{ route('requests.review', $serviceRequest) }}">Review</a></td></tr>@empty<tr><td colspan="5" class="empty-table">No service requests have been recorded yet.</td></tr>@endforelse</tbody></table></div>
                    <p class="table-note">Recent service requests recorded by staff</p>
                </section>
@endsection


