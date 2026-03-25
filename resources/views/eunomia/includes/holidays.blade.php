<div class="card card-warning card-outline movil">
    <div class="vacaciones-titulo">
        <h3>En los próximos 15 días no estarán en la oficina algún día...</h3>

        <!-- <div class="card-tools">
            <span data-toggle="tooltip" title="" class="badge badge-secondary" data-original-title="{{ $holidays->count() }} fuera de la oficina">{{ $holidays->count() }}</span>
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
            </button>
        </div> -->
    </div>
    <!-- /.card-header -->
    <div class="card-vacaciones">
        @forelse($holidays as $holiday)
            @if(is_object($holiday) && (isset($holiday->start_date) || isset($holiday->end_date)))
                <p class="badge badge-agua">
                    {{ $holiday->user->name ?? 'Usuario' }} -
                    {{ \Carbon\Carbon::parse($holiday->start_date)->format('d/m/Y') }} a
                    {{ \Carbon\Carbon::parse($holiday->end_date)->format('d/m/Y') }}
                </p>
            @else
                {{-- Fallback: $holiday puede ser un string (nombre) o un array; lo mostramos de forma segura --}}
                <p class="badge badge-agua">{{ is_string($holiday) ? $holiday : ( $holiday['name'] ?? 'Usuario' ) }}</p>
            @endif
        @empty
            <p>Nadie de vacaciones (T-T)</p>
        @endforelse
    </div> <!-- /.card-vacaciones -->

</div> <!-- End card card -->
