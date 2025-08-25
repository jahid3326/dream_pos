<div class="card mb-3">
    <div class="card-body">
        <label for="pack_name" class="form-label">Pack Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="pack_name" class="form-control" value="{{ old('name', $pack->name ?? '') }}"
            required>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Surface Options
    </div>
    <div class="card-body" id="groups-container">
        {{-- If editing, loop through existing groups and their options --}}
        @if (isset($pack) && $pack->groups->isNotEmpty())
            @foreach ($pack->groups as $groupIndex => $group)
                <div class="card mb-3 group-card" id="group-{{ $groupIndex }}">
                    <input type="hidden" name="groups[{{ $groupIndex }}][id]" value="{{ $group->id }}">
                    <div class="card-header d-flex justify-content-between">
                        <span>Group {{ $groupIndex + 1 }}</span>
                        <button type="button" class="btn-close"
                            onclick="this.closest('.group-card').remove()"></button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Surface:</label>
                            <input type="text" name="groups[{{ $groupIndex }}][surface]" class="form-control"
                                value="{{ $group->surface }}" required>
                        </div>
                        <div class="options-container border p-3">
                            @foreach ($group->options as $optionIndex => $option)
                                <div class="card mb-2 option-card">
                                    <input type="hidden"
                                        name="groups[{{ $groupIndex }}][options][{{ $optionIndex }}][id]"
                                        value="{{ $option->id }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2"><label class="form-label">Option:</label><input
                                                    type="number"
                                                    name="groups[{{ $groupIndex }}][options][{{ $optionIndex }}][option]"
                                                    class="form-control" value="{{ $option->option }}" required></div>
                                            <div class="col-md-6 mb-2"><label class="form-label">Price:</label><input
                                                    type="number" step="0.01"
                                                    name="groups[{{ $groupIndex }}][options][{{ $optionIndex }}][price]"
                                                    class="form-control" value="{{ $option->price }}" required></div>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="this.closest('.option-card').remove()">Remove Option</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success btn-sm"
                                onclick="addOption({{ $groupIndex }})">Add Option</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<div class="mt-3">
    <button type="button" class="btn btn-primary" onclick="addGroup()">Add Group</button>
    <button type="submit" class="btn btn-success">{{ $buttonText ?? 'Save Pack' }}</button>
</div>

@push('scripts')
    <script>
        // Initialize the group index based on existing groups, or 0 for a new pack
        let groupIndex = {{ isset($pack) && $pack->groups ? $pack->groups->count() : 0 }};

        // Add a new group to the main container
        function addGroup() {
            const groupTemplate = `
            <div class="card mb-3 group-card" id="group-${groupIndex}">
                <div class="card-header d-flex justify-content-between">
                    <span>Group ${groupIndex + 1}</span>
                    <button type="button" class="btn-close" onclick="this.closest('.group-card').remove()"></button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Surface:</label>
                        <input type="text" name="groups[${groupIndex}][surface]" class="form-control" required>
                    </div>
                    <div class="options-container border p-3">
                        {{-- New groups will start with one option --}}
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="addOption(${groupIndex})">Add Option</button>
                    </div>
                </div>
            </div>`;

            $('#groups-container').append(groupTemplate);
            addOption(groupIndex); // Add the first default option to the new group
            groupIndex++;
        }

        // Add an option to a specific group
        function addOption(currentGroupIndex) {
            const optionsContainer = $(`#group-${currentGroupIndex} .options-container`);
            // Calculate the next option index within the specific group
            let optionIndex = optionsContainer.find('.option-card').length;

            const optionTemplate = `
            <div class="card mb-2 option-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Option:</label>
                            <input type="number" name="groups[${currentGroupIndex}][options][${optionIndex}][option]" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Price:</label>
                            <input type="number" step="0.01" name="groups[${currentGroupIndex}][options][${optionIndex}][price]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.option-card').remove()">Remove Option</button>
                </div>
            </div>`;

            optionsContainer.append(optionTemplate);
        }

        // If we are on the create page and there are no groups, add one by default.
        @if (!isset($pack) || $pack->groups->isEmpty())
            document.addEventListener('DOMContentLoaded', addGroup);
        @endif
    </script>
@endpush
