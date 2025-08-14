@extends('main')

@section('title', 'Policy Detail')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Policy Detail Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Policy Detail</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No Blanko:</label>
                                        <p id="policy-no-blanko">{{ $policy->no_blanko }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>No Policy:</label>
                                        <p>{{ $policy->no_policy }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Consignee:</label>
                                        <p>{{ $policy->consignee }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>No BL:</label>
                                        <p>{{ $policy->no_bl }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Shipping Carrier:</label>
                                        <p>{{ $policy->shipping_carrier }}</p>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Insured Value:</label>
                                        <p>{{ number_format($policy->insured_value, 2) }} {{ $policy->currency }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Status:</label>
                                        <p id="policy-status">{{ ucfirst(str_replace('_', ' ', $policy->status)) }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Premium Price:</label>
                                        <p id="policy-premium">{{ number_format($policy->premium_price, 2) }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Verification Reason:</label>
                                        <p id="policy-reason">{{ $policy->verification_reason ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Submitted by:</label>
                                        <p>{{ $policy->user->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('policies.index') }}" class="btn btn-default">Back to List</a>

                            <div>
                                @can('verify-polis')
                                    @if ($policy->status === 'pending_verification')
                                        <button id="btn-verify" class="btn btn-success" data-id="{{ $policy->id }}">Approve /
                                            Reject</button>
                                    @endif
                                @endcan

                                @can('confirm-payment')
                                    @if ($policy->status === 'pending_payment')
                                        <button id="btn-confirm-payment" class="btn btn-primary" data-id="{{ $policy->id }}">
                                            Confirm Payment ({{ number_format($policy->premium_price, 2) }})
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                    <!-- End Card -->

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Verify (Approve / Reject)
            $('#btn-verify').on('click', function() {
                let policyId = $(this).data('id');
                Swal.fire({
                    title: 'Approve or Reject?',
                    html: `<input type="text" id="reason" class="swal2-input" placeholder="Reason if rejected">`,
                    showDenyButton: true,
                    confirmButtonText: 'Approve',
                    denyButtonText: 'Reject',
                    showCancelButton: true,
                }).then((result) => {
                    let action = null;
                    if (result.isConfirmed) action = 'approve';
                    else if (result.isDenied) action = 'reject';
                    if (!action) return;

                    $.ajax({
                        url: `/policies/${policyId}/verify`,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            action: action,
                            reason: $('#reason').val()
                        },
                        success: function(response) {
                            Swal.fire(
                                action === 'approve' ? 'Approved!' : 'Rejected!',
                                response.success,
                                'success'
                            ).then(() => {
                                $('#btn-verify').remove();
                                $('#policy-status').text(action === 'approve' ?
                                    'verified' : 'rejected');
                                $('#policy-reason').text($('#reason').val() ||
                                    '-');

                                if (action === 'approve') {
                                    $('#btn-confirm-payment').show();
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Failed to process verification.',
                                'error');
                        }
                    });
                });
            });

            // Confirm Payment
            $('#btn-confirm-payment').on('click', function() {
                let policyId = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Payment?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, confirm',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/policies/${policyId}/confirm-payment`,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Confirmed!', response.success, 'success')
                                    .then(() => {
                                        $('#btn-confirm-payment').remove();
                                        $('#policy-status').text('paid');
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Failed to confirm payment.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
