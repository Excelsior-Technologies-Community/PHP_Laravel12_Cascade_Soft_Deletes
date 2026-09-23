<!DOCTYPE html>
<html>

<head>

    <title>Laravel Cascade Soft Delete Admin</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            color: #1e293b;
        }


        /* NAVBAR */

        .navbar {
            background: #0f172a;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .navbar a:hover {
            color: #93c5fd;
        }

        .navbar-brand {
            font-size: 18px;
            margin-right: 15px;
        }


        /* CONTAINER */

        .container {
            width: 92%;
            max-width: 1400px;
            margin: 30px auto;
        }


        /* HEADER */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .page-header h2 {
            margin-bottom: 5px;
        }


        /* CARD */

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }


        /* FILTER CARD */

        .filter-card {
            margin-bottom: 20px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 10px;
        }

        .filter-grid label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }


        /* SECTION HEADER */

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }

        .section-header h3 {
            margin: 0;
        }


        /* TABLE */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: #0f172a;
            color: white;
        }

        .table th {
            padding: 14px;
            text-align: left;
        }

        .table td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }


        /* BUTTONS */

        .btn {
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .btn-success {
            background: #16a34a;
        }

        .btn-danger {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
        }

        .btn-primary {
            background: #2563eb;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 13px;
        }

        .btn:hover {
            opacity: .9;
        }


        /* BADGE */

        .badge {
            background: #2563eb;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-cascade {
            background: #7c3aed;
        }

        .badge-direct {
            background: #ea580c;
        }


        /* ACTION */

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }


        /* FORM */

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
        }


        /* ALERT */

        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }


        /* STATISTICS */

        .stats-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(200px, 1fr));

            gap: 20px;
            margin-top: 15px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .stat-title {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-top: 10px;
            color: #0f172a;
        }


        /* STATUS */

        .status-deleted {
            display: inline-block;
            margin-left: 5px;
            padding: 4px 7px;
            border-radius: 5px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 11px;
            font-weight: 600;
        }

        .restore-warning {
            color: #b45309;
            background: #fef3c7;
            padding: 6px 9px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .history-deleted {
            color: #dc2626;
            font-weight: 600;
        }

        .history-restored {
            color: #16a34a;
            font-weight: 600;
        }


        /* EMPTY STATE */

        .empty-state {
            text-align: center;
            padding: 35px 20px;
            color: #64748b;
        }

        .empty-state h3 {
            color: #334155;
        }


        /* PAGINATION */

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper svg {
            width: 18px;
            height: 18px;
        }

        .pagination-wrapper > nav > div:first-child {
            display: none;
        }

        .pagination-wrapper nav div:last-child {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pagination-wrapper a,
        .pagination-wrapper span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-decoration: none;
            color: #1e293b;
            background: white;
        }

        .pagination-wrapper a:hover {
            background: #2563eb;
            color: white;
        }

        .pagination-wrapper span[aria-current="page"] {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }


        /* MOBILE */

        @media (max-width: 768px) {

            .container {
                width: 95%;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table {
                white-space: nowrap;
            }

            .navbar {
                padding: 15px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>

<div class="navbar">

    <a
        href="{{ route('categories.index') }}"
        class="navbar-brand"
    >
        🛡️ Cascade Soft Deletes
    </a>

    <a href="{{ route('categories.index') }}">
        Categories
    </a>

    <a href="{{ route('products.index') }}">
        Products
    </a>

    <a href="{{ route('soft-deletes.index') }}">
        🗑️ Deleted Records
    </a>

    <a href="{{ route('soft-deletes.analytics') }}">
        📊 Delete Analytics
    </a>

</div>


<div class="container">

    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="alert error">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    @yield('content')

</div>


<script>

setTimeout(() => {

    document
        .querySelectorAll('.alert')
        .forEach(el => {
            el.style.display = 'none';
        });

}, 4000);

</script>


</body>

</html>