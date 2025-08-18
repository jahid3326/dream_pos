{{-- Each TR has a data-id and a class indicating its parent --}}
<tr data-id="{{ $category->id }}" class="@if ($category->parent_id) parent-of-{{ $category->parent_id }} @endif"
    @if ($level > 0) style="display: none;" @endif>

    {{-- Name Column with Toggle Button and Indentation --}}
    <td style="padding-left: {{ 15 + 30 * $level }}px;">
        <div class="d-flex align-items-center">
            @if ($category->children->count() > 0)
                {{-- This is the + / - toggle button --}}
                <a href="#" class="toggle-children me-2" data-id="{{ $category->id }}">
                    <i class="fas fa-plus-square text-primary"></i>
                </a>
            @else
                {{-- Add a spacer for alignment if there are no children --}}
                <span style="display: inline-block; width: 24px;"></span>
            @endif
            <span>{{ $category->name }}</span>
        </div>
    </td>

    {{-- Category Logo Column --}}
    <td>
        @if ($category->logo)
            <img src="{{ asset('public/storage/' . $category->logo) }}" alt="{{ $category->name }}" width="50"
                class="img-thumbnail">
        @endif
    </td>

    {{-- Created On Column --}}
    <td>{{ $category->created_at->format('d M, Y') }}</td>

    {{-- Status Column --}}
    <td>
        @if ($category->status)
            <span class="badges bg-lightgreen">Active</span>
        @else
            <span class="badges bg-lightred">Inactive</span>
        @endif
    </td>

    {{-- Action Column --}}
    @if (hasActionPermission('Category', 'update') || hasActionPermission('Category', 'delete'))
        <td class="text-end">
            <div class="d-flex gap-0 justify-content-end">
                @if (hasActionPermission('Category', 'update'))
                    <a href="{{ route('categories.edit', $category->id) }}"
                        class="me-2 p-2 d-flex align-items-center border rounded"><i data-feather="edit"
                            class="feather-edit"></i></a>
                @endif
                @if (hasActionPermission('Category', 'delete'))
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="me-2 p-2 d-flex align-items-center border rounded delete-button"><i
                                data-feather="trash-2" class="feather-trash-2"></i></button>
                    </form>
                @endif
            </div>
        </td>
    @endif
</tr>

<!-- Render Children Recursively -->
@if ($category->children && $category->children->count() > 0)
    @foreach ($category->children as $child)
        @include('categories._category-list-item', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
