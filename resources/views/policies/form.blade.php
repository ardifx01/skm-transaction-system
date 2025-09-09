@extends('main')

@section('title', isset($policy) ? 'Edit Policy' : 'Add Policy')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($policy) ? 'Edit Policy' : 'Add Policy' }}</h3>
                        </div>
                        <form id="policyForm" class="form-horizontal">
                            @csrf
                            @if (isset($policy))
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                {{-- Contoh input data polis. Sesuaikan dengan kebutuhan nyata --}}
                                <div class="form-group row">
                                    <label for="no_blanko" class="col-sm-2 col-form-label">No Blanko</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="no_blanko" class="form-control" id="no_blanko"
                                            value="{{ $policy->no_blanko ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="no_policy" class="col-sm-2 col-form-label">No Policy</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="no_policy" class="form-control" id="no_policy"
                                            value="{{ $policy->no_policy ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="consignee" class="col-sm-2 col-form-label">Consignee</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="consignee" class="form-control" id="consignee"
                                            value="{{ $policy->consignee ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="no_bl" class="col-sm-2 col-form-label">No BL</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="no_bl" class="form-control" id="no_bl"
                                            value="{{ $policy->no_bl ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="shipping_carrier" class="col-sm-2 col-form-label">Shipping Carrier</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="shipping_carrier" class="form-control"
                                            id="shipping_carrier" value="{{ $policy->shipping_carrier ?? '' }}" required>
                                    </div>
                                </div>


                                {{-- Checkbox untuk Mata Uang --}}
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Currency</label>
                                    <div class="col-sm-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="currency" value="IDR"
                                                id="currency-idr"
                                                {{ ($policy['currency'] ?? '') == 'IDR' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="currency-idr">IDR</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="currency" value="JPY"
                                                id="currency-jpy"
                                                {{ ($policy['currency'] ?? '') == 'JPY' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="currency-jpy">JPY</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="currency" value="USD"
                                                id="currency-usd"
                                                {{ ($policy['currency'] ?? '') == 'USD' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="currency-usd">USD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="currency" value="SGD"
                                                id="currency-sgd"
                                                {{ ($policy['currency'] ?? '') == 'SGD' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="currency-sgd">SGD</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="insured_value" class="col-sm-2 col-form-label">Insured Value</label>
                                    <div class="col-sm-10">
                                        <input type="number" name="insured_value" class="form-control" id="insured_value"
                                            value="{{ $policy->insured_value ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ route('policies.index') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#policyForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var url =
                    "{{ isset($policy) ? route('policies.update', $policy->id) : route('policies.store') }}";
                var method = "{{ isset($policy) ? 'PUT' : 'POST' }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize() + '&_method=' + method,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (method === 'POST') {
                                form[0].reset();
                            }
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages
                        });
                    }
                });
            });
        });
    </script>
@endpush
