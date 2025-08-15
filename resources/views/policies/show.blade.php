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
                                        <p id="policy-no-policy">{{ $policy->no_policy ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Consignee:</label>
                                        <p id="policy-consignee">{{ $policy->consignee }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>No BL:</label>
                                        <p id="policy-no-bl">{{ $policy->no_bl }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Shipping Carrier:</label>
                                        <p id="policy-shipping-carrier">{{ $policy->shipping_carrier }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>From:</label>
                                        <p id="policy-from">{{ $policy->from ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>To:</label>
                                        <p id="policy-to">{{ $policy->to ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Transhipment At:</label>
                                        <p id="policy-transhipment-at">{{ $policy->transhipment_at ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Value At:</label>
                                        <p id="policy-value-at">{{ $policy->value_at ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Interest Insured:</label>
                                        <p id="policy-interest-insured">{{ $policy->interest_insured ?? '-' }}</p>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Insured Value:</label>
                                        <p id="policy-insured-value">{{ number_format($policy->insured_value, 2) }}
                                            {{ $policy->currency }}</p>
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
                                    @if ($policy->status === 'verified' || $policy->status === 'paid')
                                        <div class="form-group">
                                            <label>Certificate No:</label>
                                            <p id="policy-certificate-no">{{ $policy->certificate_no ?? '-' }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Date of Issue:</label>
                                            <p id="policy-date-of-issue">
                                                {{ $policy->date_of_issue ? \Carbon\Carbon::parse($policy->date_of_issue)->format('d F Y') : '-' }}
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label>Vessel Reg:</label>
                                            <p id="policy-vessel-reg">{{ $policy->vessel_reg ?? '-' }}</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Sailing Date:</label>
                                            <p id="policy-sailing-date">
                                                {{ $policy->sailing_date ? \Carbon\Carbon::parse($policy->sailing_date)->format('d F Y') : '-' }}
                                            </p>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label>Submitted by:</label>
                                        <p>{{ $policy->user->name }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Created At:</label>
                                        <p>{{ $policy->created_at->format('d-m-Y H:i') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Updated At:</label>
                                        <p>{{ $policy->updated_at->format('d-m-Y H:i') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Created By:</label>
                                        <p>{{ $policy->user->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('policies.index') }}" class="btn btn-default">Back to List</a>

                            <div>
                                @can('edit-policies')
                                    @if ($policy->status === 'pending_verification')
                                        <button id="btn-verify" class="btn btn-success" data-toggle="modal"
                                            data-target="#verificationModal" data-id="{{ $policy->id }}">Verify
                                            Policy</button>
                                        <button id="btn-reject" class="btn btn-danger" data-toggle="modal"
                                            data-target="#rejectModal" data-id="{{ $policy->id }}">Reject Policy</button>
                                    @endif
                                @endcan

                                @can('view-payments')
                                    @if ($policy->payment_proof)
                                        <a href="{{ asset('storage/' . $policy->payment_proof) }}" target="_blank"
                                            class="btn btn-info">View Payment Proof</a>
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
                    <!-- End Policy Detail Card -->

                    {{-- Card for Payment Upload (Customer) --}}
                    @if (Auth::user()->hasRole('customer') && $policy->status === 'verified')
                        <div class="card card-info mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Upload Payment Proof</h3>
                            </div>
                            <form id="paymentForm" action="{{ route('policies.upload-payment', $policy->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="payment_proof">File Bukti Bayar (Image/PDF)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="payment_proof"
                                                    name="payment_proof" required>
                                                <label class="custom-file-label" for="payment_proof">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">Upload & Submit</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Verify Policy</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="verificationForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No Blanko:</label>
                                    <p>{{ $policy->no_blanko }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Consignee:</label>
                                    <p>{{ $policy->consignee }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Insured Value:</label>
                                    <p>{{ number_format($policy->insured_value, 2) }} {{ $policy->currency }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Shipping Carrier:</label>
                                    <p>{{ $policy->shipping_carrier }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="modal_from">From</label>
                                    <input type="text" id="modal_from" name="from" class="form-control"
                                        placeholder="Enter departure port" value="{{ $policy->from ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="modal_to">To</label>
                                    <input type="text" id="modal_to" name="to" class="form-control"
                                        placeholder="Enter destination port" value="{{ $policy->to ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="modal_transhipment_at">Transhipment At</label>
                                    <input type="text" id="modal_transhipment_at" name="transhipment_at"
                                        class="form-control" placeholder="Enter transhipment location"
                                        value="{{ $policy->transhipment_at ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="modal_value_at">Value At</label>
                                    <input type="text" id="modal_value_at" name="value_at"
                                        class="form-control currency-input" placeholder="Enter value location"
                                        value="{{ number_format($policy->value_at, 2, '.', ',') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_no_policy">No Policy</label>
                                    <input type="text" id="modal_no_policy" name="no_policy" class="form-control"
                                        placeholder="Enter policy number" value="{{ $policy->no_policy ?? '' }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="modal_certificate_no">Certificate No</label>
                                    <input type="text" id="modal_certificate_no" name="certificate_no"
                                        class="form-control" placeholder="Enter certificate number"
                                        value="{{ $policy->certificate_no ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="modal_date_of_issue">Date of Issue</label>
                                    <input type="date" id="modal_date_of_issue" name="date_of_issue"
                                        class="form-control" value="{{ $policy->date_of_issue ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="modal_vessel_reg">Vessel Reg</label>
                                    <div id="vessel-reg-container">
                                        @php
                                            $vesselRegs = $policy->vessel_reg
                                                ? explode("\n", $policy->vessel_reg)
                                                : [''];
                                        @endphp
                                        @foreach ($vesselRegs as $index => $reg)
                                            <div class="input-group mb-1">
                                                <input type="text" name="vessel_reg[]" class="form-control"
                                                    placeholder="Enter vessel registration" value="{{ $reg }}"
                                                    required>
                                                <div class="input-group-append">
                                                    @if ($loop->first)
                                                        <button type="button" class="btn btn-success add-vessel-reg"><i
                                                                class="fas fa-plus"></i></button>
                                                    @else
                                                        <button type="button" class="btn btn-danger remove-vessel-reg"><i
                                                                class="fas fa-minus"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="modal_sailing_date">Sailing Date</label>
                                    <input type="date" id="modal_sailing_date" name="sailing_date"
                                        class="form-control" value="{{ $policy->sailing_date ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="modal_premium_price">Premium Price</label>
                                    <input type="text" id="modal_premium_price" name="premium_price"
                                        class="form-control currency-input" placeholder="Enter premium price"
                                        value="{{ number_format($policy->premium_price, 2, ',', '.') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="modal_interest_insured">Interest Insured</label>
                                    <input type="text" id="modal_interest_insured" name="interest_insured"
                                        class="form-control" placeholder="Enter interest insured"
                                        value="{{ $policy->interest_insured ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="saveVerificationBtn"
                            data-id="{{ $policy->id }}">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reject Policy</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="modal_verification_reason">Reason for Rejection</label>
                            <textarea id="modal_verification_reason" name="verification_reason" class="form-control"
                                placeholder="Enter reason for rejection" required>{{ $policy->verification_reason ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="saveRejectBtn"
                            data-id="{{ $policy->id }}">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Skrip untuk Verifikasi (Approve/Reject)
            $('#btn-verify').on('click', function() {
                // ... logic to open verificationModal
            });

            $('#btn-reject').on('click', function() {
                // ... logic to open rejectModal
            });

            // Logika untuk menambah dan menghapus input Vessel Reg
            $('#vessel-reg-container').on('click', '.add-vessel-reg', function() {
                let newField = `
                    <div class="input-group mb-1">
                        <input type="text" name="vessel_reg[]" class="form-control" placeholder="Enter vessel registration" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-vessel-reg"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-success add-vessel-reg"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                `;
                $(this).closest('.input-group').after(newField);
                $(this).closest('.input-group-append').html(`
                    <button type="button" class="btn btn-danger remove-vessel-reg"><i class="fas fa-minus"></i></button>
                `);
            });

            $('#vessel-reg-container').on('click', '.remove-vessel-reg', function() {
                let container = $(this).closest('#vessel-reg-container');
                if (container.find('.input-group').length > 1) {
                    // Check if the removed element is the last one
                    let isLast = $(this).closest('.input-group').is(':last-child');
                    $(this).closest('.input-group').remove();

                    // If the last element was removed, add the plus button to the new last element
                    if (isLast) {
                        let newLast = container.find('.input-group').last().find('.input-group-append');
                        newLast.append(`
                            <button type="button" class="btn btn-success add-vessel-reg"><i class="fas fa-plus"></i></button>
                        `);
                    }
                }
            });

            $('#verificationForm').on('submit', function(e) {
                e.preventDefault();
                let policyId = $('#saveVerificationBtn').data('id');

                let formData = $(this).serializeArray();
                formData.forEach(function(item) {
                    if (item.name === 'premium_price' || item.name === 'value_at') {
                        item.value = item.value.replace(/\./g, '');
                    }
                });

                formData.push({
                    name: 'status',
                    value: 'verified'
                });

                $.ajax({
                    url: `/policies/${policyId}/verify`,
                    type: 'POST',
                    data: $.param(formData),
                    success: function(response) {
                        Swal.fire('Approved!', response.success, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        Swal.fire('Error!', errorMessages, 'error');
                    }
                });
            });

            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();
                let policyId = $('#saveRejectBtn').data('id');
                let formData = $(this).serialize() + '&status=rejected';

                $.ajax({
                    url: `/policies/${policyId}/verify`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire('Rejected!', response.success, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        Swal.fire('Error!', errorMessages, 'error');
                    }
                });
            });

            // Skrip untuk Konfirmasi Pembayaran
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
                                        location.reload();
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

            // Handle file input label
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });

            // Handle payment form submit
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire('Success!', response.success, 'success')
                            .then(() => {
                                location.reload();
                            });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        Swal.fire('Error!', errorMessages, 'error');
                    }
                });
            });

            // Fungsi untuk memformat angka dengan pemisah ribuan
            function formatNumberInput(input) {
                let value = input.val().replace(/\./g, '');
                if (value) {
                    value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    input.val(value);
                }
            }

            // Terapkan fungsi format pada input dengan class .currency-input
            $(document).on('keyup', '.currency-input', function() {
                formatNumberInput($(this));
            });

            // Inisialisasi format saat modal dibuka
            $('#verificationModal').on('show.bs.modal', function() {
                $('.currency-input').each(function() {
                    formatNumberInput($(this));
                });
            });
        });
    </script>
@endpush
