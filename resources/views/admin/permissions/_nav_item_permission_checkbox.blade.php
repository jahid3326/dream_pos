{{-- This is a single item in the permission tree --}}
<div class="list-group-item" style="border: 0; border-left: 4px solid #{{ dechex(crc32($item->type)) }};">
    {{-- Conditionally apply the margin ONLY if the level is greater than 0 --}}
    <div class="d-flex align-items-center" @if($level > 0) style="margin-left: {{ 25 * $level }}px;" @endif>
        
        {{-- Checkbox --}}
        <div class="form-check me-3">
            <input class="form-check-input" type="checkbox" name="nav_item_ids[]" value="{{ $item->id }}" id="nav_{{ $item->id }}"
                {{-- Check the box if the ID is in the assigned list --}}
                @if(in_array($item->id, $assignedNavIds)) checked @endif
            >
        </div>

        {{-- Label and Item Info --}}
        <label class="form-check-label flex-grow-1" for="nav_{{ $item->id }}">
            <i class="ti {{ $item->icon }} me-1"></i>
            <strong>{{ $item->name }}</strong>
            
            {{-- Type Badge --}}
            @if($item->type === 'header') <span class="badge bg-dark">Header</span>
            @elseif($item->type === 'dropdown') <span class="badge bg-warning">Dropdown</span>
            @else <span class="badge bg-primary">Link</span>
            @endif
            
            {{-- Route Name --}}
            @if($item->route)
                <small class="text-muted ms-2"> (Route: {{ $item->route }})</small>
            @endif
        </label>
    </div>
</div>

{{-- If this item has children, loop through them and include this same partial, increasing the indent level --}}
@if ($item->children && $item->children->count() > 0)
    @foreach ($item->children as $child)
        @include('admin.permissions._nav_item_permission_checkbox', ['item' => $child, 'level' => $level + 1])
    @endforeach
@endif