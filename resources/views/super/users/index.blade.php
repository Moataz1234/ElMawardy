<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Super User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BoxIcons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .user-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Users Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('super.users') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Search Name/Email</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name or email...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">User Type</label>
                                    <select class="form-select" name="usertype">
                                        <option value="">All Types</option>
                                        <option value="super" {{ request('usertype') == 'super' ? 'selected' : '' }}>Super</option>
                                        <option value="admin" {{ request('usertype') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ request('usertype') == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="rabea" {{ request('usertype') == 'rabea' ? 'selected' : '' }}>Rabea</option>
                                        <option value="Acc" {{ request('usertype') == 'Acc' ? 'selected' : '' }}>Acc</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Shop</label>
                                    <select class="form-select" name="shop_name">
                                        <option value="">All Shops</option>
                                        @foreach(\App\Models\Shop::all() as $shop)
                                            <option value="{{ $shop->name }}" {{ request('shop_name') == $shop->name ? 'selected' : '' }}>
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('super.users') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-refresh me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="row">
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bx bx-users me-2"></i>All Users</h5>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('super.users') }}?{{ http_build_query(request()->query()) }}&export=excel" 
                               class="btn btn-outline-success btn-sm">
                                <i class="bx bx-export me-1"></i>Export
                            </a>
                            <span class="badge bg-info fs-6">Total: {{ $users->total() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Shop Name</th>
                                        <th>Created At</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td><strong>{{ $user->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-primary me-3">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ 
                                                $user->usertype == 'super' ? 'bg-danger' : 
                                                ($user->usertype == 'admin' ? 'bg-warning text-dark' : 
                                                ($user->usertype == 'rabea' ? 'bg-info' : 
                                                ($user->usertype == 'Acc' ? 'bg-success' : 'bg-secondary')))
                                            }}">
                                                {{ ucfirst($user->usertype) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->shop_name)
                                                <span class="badge bg-light text-dark">{{ $user->shop_name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $user->updated_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('super.users.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit User">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <form method="POST" 
                                                      action="{{ route('super.users.delete', $user->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger ms-1" 
                                                            title="Delete User">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary ms-1" disabled title="Cannot delete yourself">
                                                        <i class="bx bx-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 