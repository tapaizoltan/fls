<!-- resources/views/filament/infolists/components/customer-activity-log.blade.php -->
{{-- <ul class="timeline">
    @foreach ($activities->reverse() as $activity)
        <li>
            <span class="timestamp">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
            <p class="activity-description">
                @foreach ($activity->changes['old'] as $key => $value)
                    <strong>{{ $key }}:</strong> {{ $value }}-> {{ $activity->changes['attributes'][$key] }}<br>
                @endforeach
            </p>
        </li>
    @endforeach
</ul>

<style>
    .timeline {
        list-style-type: none;
        padding-left: 0;
    }
    .timeline li {
        margin-bottom: 1rem;
        position: relative;
        padding-left: 1.5rem;
    }
    .timeline .timestamp {
        font-size: 0.85rem;
        color: #888;
    }
    .timeline .activity-description {
        margin: 0;
    }
</style> --}}

<ul class="timeline">
    @foreach ($activities->reverse() as $activity)

        <li class="timeline-item">
            <span class="timeline-item-icon | faded-icon">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
            </span>
            <div class="timeline-item-description">
                <p style="color:gray; font-size: 8pt; margin-top:10px; margin-bottom: -2px;">{{ $activity->created_at->format('Y-m-d H:i') }}</p>
                <p style="font-size:10pt;">
                    {{-- @foreach ($activity->changes['old'] as $key => $value)
                        A pézügyi kockázati szint: {{ $value }} -ról {{ $activity->changes['attributes'][$key] }} -re lett módosítva.
                    @endforeach --}}
                    @if (!empty($activity->changes['old']['financial_risk_rate']))
                    A pézügyi kockázati szint: {{ $activity->changes['old']['financial_risk_rate'] }} -ról {{ $activity->changes['attributes']['financial_risk_rate'] }} -re lett módosítva.
                    @else
                    A pézügyi kockázati szint: {{ $activity->changes['attributes']['financial_risk_rate'] }} -val létre lett hozva.
                    @endif
                </p>
                <p style="color:gray; font-size: 8pt; margin-bottom: -2px;">
                  @if(!empty($activity->changes['attributes']['justification_of_risk']))
                    Az indoklása: {{ $activity->changes['attributes']['justification_of_risk'] }}
                  @else
                    Az indoklása: Létrehozáskor ez az alapértelmezett érték.
                  @endif
                </p>
            </div>
        </li>

        {{-- <li class="timeline-item">
            <span class="timeline-item-icon | faded-icon">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
            </span>
            <div class="timeline-item-description">
                <span class="timestamp">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                <p class="activity-description">
                    @foreach ($activity->changes['old'] as $key => $value)
                        <strong>{{ $key }}:</strong> {{ $value }}-> {{ $activity->changes['attributes'][$key] }}<br>
                    @endforeach
                </p>
            </div>
        </li> --}}

    @endforeach
</ul>

<style>

:root {
  --c-grey-100: #f4f6f8;
  --c-grey-200: #e3e3e3;
  --c-grey-300: #b2b2b2;
  --c-grey-400: #7b7b7b;
  --c-grey-500: #3d3d3d;
  --c-green-500: #52b147;
  --c-orange-500: #d98c24;
  --c-red-500: #c93d33;
}

.timeline {
  margin-left: 10px;
  margin-right: auto;
  flex-direction: column;
  padding: 32px 0 32px 32px;
  border-left: 2px solid var(--c-grey-200);
  font-size: 1.125rem;
}

.dark .timeline {
  margin-left: 10px;
  margin-right: auto;
  flex-direction: column;
  padding: 32px 0 32px 32px;
  border-left: 2px solid var(--c-grey-500);
  font-size: 1.125rem;
}

.timeline-item {
  display: flex;
  gap: 24px;
}
.timeline-item + * {
  margin-top: 24px;
}
.timeline-item + .extra-space {
  margin-top: 48px;
}

.timeline-item-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-left: -48px;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 0 0 6px #fff;
}

.dark .timeline-item-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-left: -48px;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 0 0 6px #171719;
}

.timeline-item-icon svg {
  width: 20px;
  height: 20px;
}
.timeline-item-icon.faded-icon {
  background-color: var(--c-grey-100);
  color: var(--c-grey-400);
}

.dark .timeline-item-icon.faded-icon {
  background-color: var(--c-grey-500);
  color: var(--c-grey-400);
}

.new-comment {
  width: 100%;
}


</style>