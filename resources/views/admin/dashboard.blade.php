@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header with gradient -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="dashboard-header p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div style="position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600&q=80') center/cover no-repeat; opacity: 0.05; pointer-events: none;"></div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;">
                            <i class="fas fa-crown me-1"></i> Administrator Access
                        </span>
                    </div>
                    <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Admin Dashboard</h2>
                    <p class="text-white-50 mb-0" style="font-size: 0.9rem;">System overview and management</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - All with consistent blue gradient -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                            <i class="fas fa-users text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.06em;">Total Users</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a; font-size: 2rem;">{{ $totalUsers }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top" style="border-color: #eef2f6 !important;">
                    <span class="text-success"><i class="fas fa-arrow-up me-1"></i> +12%</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">vs last month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                            <i class="fas fa-building text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.06em;">Departments</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a; font-size: 2rem;">{{ $totalDepartments }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top" style="border-color: #eef2f6 !important;">
                    <span class="text-success"><i class="fas fa-arrow-up me-1"></i> +3</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">new this month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                            <i class="fas fa-box text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.06em;">Total Products</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a; font-size: 2rem;">{{ $totalProducts }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top" style="border-color: #eef2f6 !important;">
                    <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> 3 low stock</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0" style="border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                            <i class="fas fa-shopping-cart text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.06em;">Total Orders</span>
                        <h3 class="fw-bold mb-0" style="color: #0f172a; font-size: 2rem;">{{ $totalOrders }}</h3>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top" style="border-color: #eef2f6 !important;">
                    <span class="text-primary"><i class="fas fa-clock me-1"></i> 2 pending</span>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Recent Users Card - Redesigned -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
                <div class="card-header bg-transparent py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #2563eb15, #1e3a8a15); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="color: #2563eb;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Recent Users</h6>
                            <small class="text-muted">Latest registered members</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn" style="background: linear-gradient(135deg, #2563eb, #1e3a8a); color: white; border: none; padding: 0.5rem 1.2rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem;">
                        <i class="fas fa-arrow-right me-2"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="min-width: 600px;">
                            <thead style="background: #f8fafd;">
                                <tr>
                                    <th class="px-4 py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Name</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Email</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Role</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Department</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                <tr style="border-bottom: 1px solid #eef2f6;">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span style="color: #0f172a; font-weight: 600;">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3" style="color: #475569;">{{ $user->email }}</td>
                                    <td class="py-3">
                                        @php
                                            $roleColors = [
                                                'admin' => ['bg' => '#ef444420', 'text' => '#dc2626', 'icon' => 'fa-crown'],
                                                'inventory' => ['bg' => '#2563eb20', 'text' => '#2563eb', 'icon' => 'fa-box'],
                                                'sales' => ['bg' => '#05966920', 'text' => '#059669', 'icon' => 'fa-chart-line'],
                                                'default' => ['bg' => '#64748b20', 'text' => '#475569', 'icon' => 'fa-user']
                                            ];
                                            $roleStyle = $roleColors[$user->role] ?? $roleColors['default'];
                                        @endphp
                                        <span style="background: {{ $roleStyle['bg'] }}; color: {{ $roleStyle['text'] }}; padding: 0.35rem 0.8rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <i class="fas {{ $roleStyle['icon'] }}"></i>
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3" style="color: #475569;">{{ $user->department->name ?? '—' }}</td>
                                    <td class="py-3" style="color: #64748b;">{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent py-3 px-4" style="border-top: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted">Showing {{ $recentUsers->count() }} of {{ $totalUsers }} users</small>
                        <a href="{{ route('admin.users.index') }}" style="color: #2563eb; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                            Manage Users <i class="fas fa-arrow-right ms-2" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15);">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color: #0f172a; font-size: 0.9rem;">
                        <i class="fas fa-bolt me-2" style="color: #f59e0b;"></i>Quick Actions
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn" style="background: #f1f5f9; color: #0f172a; border: none; border-radius: 30px; padding: 0.5rem 1.2rem; font-weight: 600;">
                            <i class="fas fa-plus-circle me-2" style="color: #2563eb;"></i>Add User
                        </a>
                        <a href="#" class="btn" style="background: #f1f5f9; color: #0f172a; border: none; border-radius: 30px; padding: 0.5rem 1.2rem; font-weight: 600;">
                            <i class="fas fa-building me-2" style="color: #059669;"></i>New Department
                        </a>
                        <a href="#" class="btn" style="background: #f1f5f9; color: #0f172a; border: none; border-radius: 30px; padding: 0.5rem 1.2rem; font-weight: 600;">
                            <i class="fas fa-file-export me-2" style="color: #7c3aed;"></i>Generate Report
                        </a>
                        <a href="#" class="btn" style="background: #f1f5f9; color: #0f172a; border: none; border-radius: 30px; padding: 0.5rem 1.2rem; font-weight: 600;">
                            <i class="fas fa-cog me-2" style="color: #64748b;"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional custom styles */
.container-fluid {
    background: #f8fafd;
    min-height: 100vh;
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px -5px rgba(13, 20, 40, 0.2) !important;
}

/* Custom badge styles */
.badge-custom {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Table row hover */
.table-hover tbody tr:hover {
    background: #f1f5f9;
}

/* Gradient text for numbers */
.stat-value {
    background: linear-gradient(135deg, #0f172a, #1e3a8a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Animation for header */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.dashboard-header {
    background-size: 200% 200% !important;
    animation: gradientShift 15s ease infinite;
}
</style>
@endsection