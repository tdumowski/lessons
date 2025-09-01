@extends('adminlte::page')

@section('title', 'Tytuł aplikacji | Tytuł widoku')

@section('content')

   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0">TYTUŁ WIDOKU</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                  <li class="breadcrumb-item active">Tytuł widoku</li>
               </ol>
            </div><!-- /.col -->
         </div><!-- /.row -->
      </div><!-- /.container-fluid -->
   </div>
   <!-- /.content-header -->

   <!-- Main content -->
   <section class="content">
      <div class="container-fluid">

         {{-- HERE'S THE PLACE FOR THE HTML CODE --}}

      </div><!--/. container-fluid -->
   </section>
    <!-- /.content -->

    {{-- @vite(['resources/js/queryGetChatAnswer.js']) --}}

@endsection
