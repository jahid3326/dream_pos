<style>
    label span {
        color: unset;
    }
</style>
<div class="modal fade" id="manageItemsModal" tabindex="-1" aria-labelledby="manageItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageItemsModalLabel">Manage Variations for: <span
                        id="item-product-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- This hidden input is crucial for knowing which pack_product we are editing --}}
                <input type="hidden" id="current-pack-product-id">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="modal-item-search" class="form-control"
                            placeholder="Search by item name or SKU...">
                    </div>
                    <div class="col-md-6">
                        <select id="modal-item-category-filter" class="form-select">
                            <option value="">Filter by Category...</option>
                            @if (isset($childCategories))
                                @foreach ($childCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-sm table-hover" id="modal-items-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;"><input type="checkbox" id="check-all-items"
                                        class="form-check-input"></th>
                                <th>Name</th>
                                <th style="width: 25%;">SKU</th>
                            </tr>
                        </thead>
                        <tbody id="items-checkbox-container">
                            {{-- Rows will be populated here by JavaScript --}}
                            <tr>
                                <td colspan="3" class="text-center text-muted p-4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-items-selections-btn">Save Selections</button>
            </div>
        </div>
    </div>
</div>
