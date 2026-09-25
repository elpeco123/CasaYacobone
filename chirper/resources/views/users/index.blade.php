@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-people-fill me-2" style="color: var(--cy-gold);"></i>Gestión de Usuarios</h1>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-gold">
            <i class="bi bi-person-plus-fill me-1"></i>Nuevo Usuario
        </a>
    </div>

    <div class="card-glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th class="text-center">Rol</th>
                            <th class="d-none d-lg-table-cell">Fecha Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="text-muted">{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if(Auth::id() === $user->id)
                                    <span class="badge bg-info ms-1" style="font-size: 0.7rem;">Tú</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->isAdmin())
                                    <span class="badge" style="background: var(--cy-accent); color: white; padding: 0.35rem 0.7rem; font-size: 0.78rem;">
                                        <i class="bi bi-shield-lock-fill me-1"></i>Administrador
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(143, 188, 212, 0.2); color: #8fbcd4; border: 1px solid rgba(143, 188, 212, 0.4); padding: 0.35rem 0.7rem; font-size: 0.78rem;">
                                        <i class="bi bi-person-badge me-1"></i>Vendedor
                                    </span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-glass btn-sm" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm"
                                                    style="background: rgba(208, 85, 58, 0.15); border: 1px solid rgba(208, 85, 58, 0.3); color: #d0553a;"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
