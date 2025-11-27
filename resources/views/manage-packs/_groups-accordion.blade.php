<div class="accordion" id="groupsAccordion-{{ $groups->first()->pack_id ?? '' }}">
    @foreach ($groups as $group)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseGroup{{ $group->id }}">
                    {{ $group->surface }}
                </button>
            </h2>
            <div id="collapseGroup{{ $group->id }}" class="accordion-collapse collapse"
                data-bs-parent="#groupsAccordion-{{ $group->pack_id }}">
                <div class="accordion-body">
                    {{-- Include partial for options --}}
                    @include('manage-packs._options-table', ['options' => $group->options])
                </div>
            </div>
        </div>
    @endforeach
</div>
