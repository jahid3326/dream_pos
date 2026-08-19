@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Dashboard</h4>
                    <h6>Welcome back, {{ Auth::user()->name }}</h6>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="mb-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#ff9f1a;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Total Shipments</div>
                                            <div class="h4 mb-0">{{ $totalShipments ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('shipments.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#2b6ca3;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Pending Processing</div>
                                            <div class="h4 mb-0">{{ $pendingCount ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a
                                            href="{{ route('shipments.index', ['filter' => 'pending']) }}">More infos ›</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#28a745;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">New Notifications</div>
                                            <div class="h4 mb-0">{{ $notifications->count() ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('notifications.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#6f42c1;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Recent Activity</div>
                                            <div class="h4 mb-0">{{ $recentShipments->count() ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('shipments.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Notifications Panel -->
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Notifications</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">Mark All
                                Read</a>
                        </div>
                        <div class="card-body">
                            @if ($notifications->count() > 0)
                                @foreach ($notifications as $notification)
                                    <div class="notification-item border-bottom pb-3 mb-3"
                                        data-id="{{ $notification->id }}">
                                        <div class="d-flex">
                                            <div class="notification-icon me-3">
                                                <i class="fas fa-ship text-primary"></i>
                                            </div>
                                            <div class="notification-content flex-grow-1">
                                                @if ($notification->type === 'App\\Notifications\\NewShipmentNotification')
                                                    <h6 class="mb-1">New Shipment Created</h6>
                                                    <p class="mb-1 text-muted">
                                                        {{ $notification->data['message'] ?? 'New shipment notification' }}
                                                    </p>
                                                    @if (isset($notification->data['shipment_number']))
                                                        <small class="text-primary">Shipment:
                                                            {{ $notification->data['shipment_number'] }}</small>
                                                    @endif
                                                @else
                                                    <p class="mb-1">
                                                        {{ $notification->data['message'] ?? 'New notification' }}</p>
                                                @endif
                                                <small
                                                    class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="notification-actions">
                                                @if (isset($notification->data['action_url']))
                                                    <a href="{{ $notification->data['action_url'] }}"
                                                        class="btn btn-sm btn-outline-primary">View</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pending Shipments -->
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pending Shipments</h5>
                            <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            @if ($pendingShipments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($pendingShipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('shipments.show', $shipment) }}"
                                                            class="text-decoration-none">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $shipment->customer->user->profile_picture ? asset('storage/' . $shipment->customer->user->profile_picture) : asset('storage/images/default_avatar.png') }}"
                                                                    class="rounded me-2" width="32" height="32"
                                                                    style="object-fit: cover;">
                                                                <div>
                                                                    <div class="fw-semibold">
                                                                        {{ $shipment->shipment_number }}</div>
                                                                    <small
                                                                        class="text-muted">{{ $shipment->customer->user->name }}</small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td class="text-end">
                                                        <small
                                                            class="text-muted">{{ $shipment->shipment_date->format('M d') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                                    <p>No pending shipments</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('shipments.index') }}"
                                        class="btn btn-outline-primary w-100 d-flex align-items-center">
                                        <i class="fas fa-list me-2"></i>
                                        All Shipments
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('purchases.index') }}"
                                        class="btn btn-outline-info w-100 d-flex align-items-center">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Purchase Orders
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="#" class="btn btn-outline-success w-100 d-flex align-items-center"
                                        onclick="searchShipments()">
                                        <i class="fas fa-search me-2"></i>
                                        Search Shipments
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="#" class="btn btn-outline-warning w-100 d-flex align-items-center"
                                        onclick="printReports()">
                                        <i class="fas fa-print me-2"></i>
                                        Print Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function markAllAsRead() {
            fetch('{{ route('notifications.markAsRead') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function searchShipments() {
            // Redirect to shipments index with search focus
            window.location.href = '{{ route('shipments.index') }}';
        }

        function printReports() {
            // Implement print functionality
            alert('Print reports functionality coming soon!');
        }
    </script>
@endpush
