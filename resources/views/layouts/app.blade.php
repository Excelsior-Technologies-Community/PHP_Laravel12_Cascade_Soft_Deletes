<!DOCTYPE html>
<html>
<head>
    <title>Laravel Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

<style>

body{
    font-family: Arial;
    background:#f3f4f6;
    margin:0;
}

/* NAVBAR */
.navbar{
    background:#0f172a;
    padding:15px 30px;
}

.navbar a{
    color:white;
    text-decoration:none;
    margin-right:20px;
    font-weight:600;
}

/* CONTAINER */
.container{
    width:90%;
    margin:30px auto;
}

/* HEADER */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

/* CARD */
.card{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
}

/* TABLE */
.table{
    width:100%;
    border-collapse:collapse;
}

.table thead{
    background:#0f172a;
    color:white;
}

.table th{
    padding:14px;
    text-align:left;
}

.table td{
    padding:14px;
    border-bottom:1px solid #eee;
}

.table tbody tr:hover{
    background:#f9fafb;
}

/* BUTTONS */
.btn{
    border:none;
    padding:8px 14px;
    border-radius:6px;
    color:white;
    cursor:pointer;
    text-decoration:none;
}

.btn-success{background:#16a34a;}
.btn-danger{background:#dc2626;}
.btn-warning{background:#f59e0b;}
.btn-primary{background:#2563eb;}

.btn-sm{
    padding:6px 10px;
    font-size:13px;
}

/* BADGE */
.badge{
    background:#2563eb;
    color:white;
    padding:5px 10px;
    border-radius:20px;
}

/* ACTION */
.action-btns{
    display:flex;
    gap:8px;
}

/* FORM */
input,select{
    width:100%;
    padding:10px;
    margin-top:10px;
    margin-bottom:15px;
}

/* ALERT */
.alert{
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
}

.success{
    background:#dcfce7;
    color:#166534;
}

.error{
    background:#fee2e2;
    color:#991b1b;
}

</style>
</head>

<body>

<div class="navbar">
    <a href="/categories">Categories</a>
    <a href="/products">Products</a>
</div>

<div class="container">

@if(session('success'))
<div class="alert success">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert error">
<ul>
@foreach($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

@yield('content')

</div>

<script>
setTimeout(()=>{
document.querySelectorAll('.alert')
.forEach(el=>el.style.display='none');
},3000);
</script>

</body>
</html>
