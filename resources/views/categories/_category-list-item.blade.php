{{-- This partial renders a single table row <tr> and its children --}}
<tr>
    {{-- Name Column with Indentation and Arrow Icon for Children --}}
    <td style="padding-left: {{ 10 + (30 * $level) }}px;">
        <div class="d-flex align-items-center">
            {{-- ADD THIS @if BLOCK --}}
            @if($category->parent_id)
                {{-- This icon will only show for child categories --}}
                <i class="fas fa-long-arrow-alt-right me-2 text-muted"></i>
            @endif
            
            <strong>{{ $category->name }}</strong>
        </div>
    </td>
    
    {{-- Parent Category Column --}}
    <td>
        {{-- Safely display the parent's name if it exists --}}
        {{ $category->parent->name ?? '—' }}
    </td>

    {{-- Category Logo Column --}}
    <td>
        @if($category->logo)
            <img src="{{ asset('public/storage/' . $category->logo) }}" alt="{{ $category->name }}" width="50" class="img-thumbnail">
        @endif
    </td>

    {{-- Action Column --}}
    <td class="text-end">
        <div class="d-flex gap-0 justify-content-end">
            @if(hasActionPermission('Category', 'update'))
                <a href="{{ route('categories.edit', $category->id) }}" class="me-2 p-2 d-flex align-items-center border rounded">
                    <i data-feather="edit" class="feather-edit"></i>
                </a>
            @endif
            @if(hasActionPermission('Category', 'delete'))
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                        <i data-feather="trash-2" class="feather-trash-2"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>

<!-- Render Children Recursively -->
@if ($category->children && $category->children->count() > 0)
    @foreach ($category->children as $child)
        {{-- Call this same file for each child, increasing the indentation level --}}
        @include('categories._category-list-item', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif