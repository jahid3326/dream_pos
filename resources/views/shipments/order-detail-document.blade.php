@extends('layouts.app')
@section('title', 'Order Detail Document - ' . $shipment->shipment_number)

@push('styles')
    <style>
        .date-input-wrapper {
            position: relative;
        }

        .upload-btn {
            min-width: 100px;
        }

        .document-section {
            margin-bottom: 2rem;
        }

        .document-files {
            margin-top: 1rem;
        }

        .document-file-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 0.5rem;
            border: 1px solid #e9ecef;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
        }

        .file-name {
            flex-grow: 1;
            font-size: 0.875rem;
        }

        .file-actions {
            display: flex;
            gap: 0.5rem;
        }

        .container-input {
            width: 100px;
        }

        .container-list {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-top: 0.5rem;
            background-color: #f8f9fa;
        }

        .container-item {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin: 0.25rem;
            font-size: 0.875rem;
        }

        .container-item .remove-btn {
            margin-left: 0.5rem;
            cursor: pointer;
            font-weight: bold;
        }

        .type-shipping-section {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-0">#{{ $shipment->shipment_number }}</h4>
                    <small class="text-muted">{{ $shipment->created_at->format('d-m-Y H:i A') }}</small>
                </div>
                <div>
                    <a href="{{ route('shipments.index') }}" class="btn btn-secondary me-2">Back</a>
                    {{-- @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment') --}}
                    <button type="button" class="btn btn-primary" id="saveBtn2" onclick="saveShipmentData()">
                        <i class="fas fa-save me-2" id="saveIcon2"></i>
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="saveSpinner2" role="status"
                            aria-hidden="true"></span>
                        <span id="saveText2">Save</span>
                    </button>
                    {{-- @endif --}}
                </div>
            </div>

            @include('layouts._messages')

            <form id="shipmentForm">
                @csrf
                <!-- Row 1: Date Delivery Estimation + Price Total -->
                @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date Delivery estimation</label>
                            <div class="date-input-wrapper">
                                <input type="date" class="form-control" id="delivery_estimation_date"
                                    value="{{ $shipment->delivery_estimation_date ? $shipment->delivery_estimation_date->format('Y-m-d') : '' }}"
                                    @if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'Shipment') readonly @endif>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Price total</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="total_amount"
                                    value="{{ $shipment->total_amount }}" step="0.01"
                                    @if (!Auth::user() || !Auth::user()->role || Auth::user()->role->name !== 'Shipment') readonly @endif>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Upload Required Documents -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3">Upload Required Document</h5>

                            <div class="row">
                                <!-- Left Column Documents -->
                                <div class="col-md-6">
                                    <!-- Invoice -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Invoice</h6>
                                                <small class="text-muted">Invoice for shipping</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Invoice')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Invoice">
                                            @foreach ($documentTypes['Invoice'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- BL -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">BL</h6>
                                                <small class="text-muted">Delivery instruction with supplier details, PO
                                                    references,
                                                    and documents</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('BL')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-BL">
                                            @foreach ($documentTypes['BL'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Packing List -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Packing List</h6>
                                                <small class="text-muted">Detailed dimensions, weight, unit per carton,
                                                    bonus</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Packing List')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Packing List">
                                            @foreach ($documentTypes['Packing List'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Telex -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Telex</h6>
                                                <small class="text-muted">Communication from ATM if telex release
                                                    applicable</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Telex')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Telex">
                                            @foreach ($documentTypes['Telex'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column Documents -->
                                <div class="col-md-6">
                                    <!-- Fumigation Certificate -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Fumigation Certificate</h6>
                                                <small class="text-muted">Only required is destination fumigating is valid
                                                    (mostly
                                                    wood, fumies, chemist)</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Fumigation Certificate')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Fumigation Certificate">
                                            @foreach ($documentTypes['Fumigation Certificate'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- MSDS / Safety Data -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">MSDS / Safety Data</h6>
                                                <small class="text-muted">Required for chemically, cosmetic or individual
                                                    goods</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('MSDS / Safety Data')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-MSDS / Safety Data">
                                            @foreach ($documentTypes['MSDS / Safety Data'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Insurance -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Insurance</h6>
                                                <small class="text-muted">If received under supplier's policy or we arrange
                                                    it
                                                    by
                                                    local fine insurance</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Insurance')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Insurance">
                                            @foreach ($documentTypes['Insurance'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Other Documents -->
                                    <div class="document-section">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Other Documents</h6>
                                                <small class="text-muted">Any specific Product certificates (CE, etc.)
                                                    export
                                                    licenses, Phono, etc.</small>
                                            </div>
                                            @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment')
                                                <button type="button" class="btn btn-outline-primary upload-btn"
                                                    onclick="uploadDocument('Other Documents')">Upload file</button>
                                            @endif
                                        </div>
                                        <div class="document-files" id="files-Other Documents">
                                            @foreach ($documentTypes['Other Documents'] as $doc)
                                                <div class="document-file-item" data-doc-id="{{ $doc->id }}">
                                                    <span class="file-name">{{ $doc->original_name }}</span>
                                                    <div class="file-actions">
                                                        <a href="{{ $doc->file_url }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">Preview</a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDocument({{ $doc->id }})">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Row 3: Type Shipping + Container -->
                <div class="row">
                    <!-- Type Shipping -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Type Shipping</h5>

                        <!-- First Type Shipping: use shipping_types table (radio buttons) -->
                        <div class="type-shipping-section">
                            <div class="mb-3">
                                <div>
                                    @foreach ($shippingTypes as $type)
                                        @php
                                            $checked =
                                                $shipment->shipping_type_id == $type->id ||
                                                (isset($shipment->type_shipping1) &&
                                                    $shipment->type_shipping1 == $type->name);
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="shipping_type_id"
                                                id="shipping_type_{{ $type->id }}" value="{{ $type->id }}"
                                                {{ $checked ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="shipping_type_{{ $type->id }}">{{ $type->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Second Type Shipping: use shipping_taxes table (radio buttons) -->
                        <div class="type-shipping-section">
                            <div class="mb-3">
                                <div>
                                    @foreach ($shippingTaxes as $tax)
                                        @php
                                            $checkedTax =
                                                $shipment->shipping_tax_id == $tax->id ||
                                                (isset($shipment->type_shipping2) &&
                                                    $shipment->type_shipping2 == $tax->name);
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="shipping_tax_id"
                                                id="shipping_tax_{{ $tax->id }}" value="{{ $tax->id }}"
                                                {{ $checkedTax ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="shipping_tax_{{ $tax->id }}">{{ $tax->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Container -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Container</h5>
                        <label class="form-label">Write to add and press enter</label>
                        <input type="text" class="form-control" id="containerInput"
                            placeholder="Write to add and press enter">
                        <div class="container-list" id="containerList">
                            <!-- Container items will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                {{-- @if (Auth::user() && Auth::user()->role && Auth::user()->role->name === 'Shipment') --}}
                <div class="row mt-4 mb-3">
                    <div class="col-12 d-flex justify-content-start align-items-center gap-2">
                        <a href="{{ route('shipments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveShipmentData()">
                            <i class="fas fa-save me-2" id="saveIcon"></i>
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="saveSpinner" role="status"
                                aria-hidden="true"></span>
                            <span id="saveText">Save</span>
                        </button>
                    </div>
                </div>
                {{-- @endif --}}
            </form>
        </div>
    </div>
    <!-- File Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="document_type" name="document_type">
                        <div class="mb-3">
                            <label class="form-label">Select File</label>
                            <input type="file" class="form-control" name="file"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitUpload()">Upload</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentDocumentType = '';
        let containerItems = [];

        // Initialize container items from existing data
        document.addEventListener('DOMContentLoaded', function() {
            // Load existing container data
            @if ($shipment->container)
                @foreach ($shipment->container as $item)
                    containerItems.push('{{ $item }}');
                @endforeach
                updateContainerDisplay();
            @endif

            // Setup container input handler
            const containerInput = document.getElementById('containerInput');
            containerInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addContainerItem();
                }
            });
        });

        function addContainerItem() {
            const input = document.getElementById('containerInput');
            const value = input.value.trim();

            if (value && !containerItems.includes(value)) {
                containerItems.push(value);
                input.value = '';
                updateContainerDisplay();
            }
        }

        function removeContainerItem(index) {
            containerItems.splice(index, 1);
            updateContainerDisplay();
        }

        function updateContainerDisplay() {
            const containerList = document.getElementById('containerList');
            containerList.innerHTML = '';

            containerItems.forEach((item, index) => {
                const itemElement = document.createElement('span');
                itemElement.className = 'container-item';
                itemElement.innerHTML =
                    `${item}<span class="remove-btn" onclick="removeContainerItem(${index})">&times;</span>`;
                containerList.appendChild(itemElement);
            });
        }

        function uploadDocument(documentType) {
            currentDocumentType = documentType;
            document.getElementById('document_type').value = documentType;
            document.querySelector('#uploadModal .modal-title').textContent = `Upload ${documentType}`;
            new bootstrap.Modal(document.getElementById('uploadModal')).show();
        }

        function submitUpload() {
            const form = document.getElementById('uploadForm');
            const formData = new FormData(form);
            const uploadBtn = document.querySelector('#uploadModal .btn-primary');

            // Show loading state
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading...';

            fetch(`{{ route('shipments.uploadDocument', $shipment) }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = 'Upload';

                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Document uploaded successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload(); // Reload to show the new file
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: data.message || 'Unknown error occurred'
                        });
                    }
                })
                .catch(error => {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = 'Upload';
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'An error occurred while uploading the document'
                    });
                });
        }

        function deleteDocument(documentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the document.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ route('shipments.deleteDocument', ['shipment' => $shipment, 'document' => '__DOC_ID__']) }}`
                            .replace('__DOC_ID__', documentId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json'
                                }
                            })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`[data-doc-id="${documentId}"]`).remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Document has been deleted successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed',
                                    text: data.message || 'Unknown error occurred'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: 'An error occurred while deleting the document'
                            });
                        });
                }
            });
        }

        function saveShipmentData() {
            // Show loading state
            showLoadingState(true);

            // Defensive reads: elements may be hidden/removed programmatically
            const deliveryEl = document.getElementById('delivery_estimation_date');
            const totalEl = document.getElementById('total_amount');

            // Fallback to server-side values when inputs are not present in the DOM
            const deliveryFallback = {!! json_encode(
                $shipment->delivery_estimation_date ? $shipment->delivery_estimation_date->format('Y-m-d') : null,
            ) !!};
            const totalFallback = {!! json_encode($shipment->total_amount ?? 0) !!};

            const deliveryVal = deliveryEl ? deliveryEl.value : deliveryFallback;
            const totalVal = totalEl ? totalEl.value : totalFallback;

            const shippingTypeChecked = document.querySelector('input[name="shipping_type_id"]:checked');
            const shippingTaxChecked = document.querySelector('input[name="shipping_tax_id"]:checked');

            const data = {
                delivery_estimation_date: deliveryVal || null,
                total_amount: totalVal,
                shipping_type_id: (shippingTypeChecked ? shippingTypeChecked.value : null),
                shipping_tax_id: (shippingTaxChecked ? shippingTaxChecked.value : null),
                container: containerItems
            };

            fetch(`{{ route('shipments.update', $shipment) }}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    // If validation error, parse JSON and show detailed messages
                    if (response.status === 422) {
                        return response.json().then(errData => {
                            showLoadingState(false);
                            const errors = errData.errors || {};
                            const messages = [];
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    messages.push(errors[key].join(' '));
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: messages.join('<br>')
                            });
                            // reject promise chain
                            throw new Error('Validation failed');
                        });
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    return response.json();
                })
                .then(data => {
                    showLoadingState(false);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Shipment data saved successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed',
                            text: data.message || 'Unknown error occurred'
                        });
                    }
                })
                .catch(error => {
                    // If we've already shown validation details, avoid double notifications
                    if (error.message === 'Validation failed') return;

                    showLoadingState(false);
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: 'An error occurred while saving: ' + error.message
                    });
                });
        }

        function showLoadingState(loading) {
            // Update both save buttons
            const saveButtons = ['saveBtn', 'saveBtn2'];

            saveButtons.forEach(btnId => {
                const btn = document.getElementById(btnId);
                const icon = document.getElementById(btnId === 'saveBtn' ? 'saveIcon' : 'saveIcon2');
                const spinner = document.getElementById(btnId === 'saveBtn' ? 'saveSpinner' : 'saveSpinner2');
                const text = document.getElementById(btnId === 'saveBtn' ? 'saveText' : 'saveText2');

                if (btn && icon && spinner && text) {
                    if (loading) {
                        btn.disabled = true;
                        icon.classList.add('d-none');
                        spinner.classList.remove('d-none');
                        text.textContent = 'Saving...';
                    } else {
                        btn.disabled = false;
                        icon.classList.remove('d-none');
                        spinner.classList.add('d-none');
                        text.textContent = 'Save';
                    }
                }
            });
        }
    </script>
@endpush
