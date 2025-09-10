@extends('adminlte::page')

@section('title', 'Plan lekcji | Kalendarz')

@section('content')

   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0"><i class="nav-icon fas regular fa-calendar-week"></i>&nbsp;&nbsp;Kalendarz</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                  <li class="breadcrumb-item active">Kalendarz</li>
               </ol>
            </div><!-- /.col -->
         </div><!-- /.row -->
      </div><!-- /.container-fluid -->
   </div>
   <!-- /.content-header -->

   <!-- Main content -->
   <section class="content">
      <div class="container-fluid">
         <div class="row">
               <div class="col-sm-2">
                  <!-- select -->
                  <div class="form-group">
                     <label>Wybierz plan</label>
                     <select class="custom-select">
                        <option>option 1</option>
                        <option>option 2</option>
                        <option>option 3</option>
                        <option>option 4</option>
                        <option>option 5</option>
                     </select>
                  </div>
               </div>

         </div>
         <div class="row">
            <div class="col-sm-12">
               <div class="card">
                  <div class="card-body">
                     <div id="calendar"></div>
                  </div>
               </div>
            </div>
         </div>
      </div><!--/. container-fluid -->
   </section>
    <!-- /.content -->

    @vite(['resources/js/calendar.js', 'resources/css/calendar.css'])
@endsection

