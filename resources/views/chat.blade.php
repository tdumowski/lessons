@extends('adminlte::page')

@section('title', 'Plan lekcji | Chat AI')

@section('content')

   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0"><i class="nav-icon fas fa-comments"></i>&nbsp;&nbsp;Chat AI</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                  <li class="breadcrumb-item active">Chat AI</li>
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
            <div class="col-sm-12">
               <div class="card">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-sm-12">
                           <!-- text input -->
                           <div class="form-group">
                              <label>Pytanie do chata</label>
                              <textarea class="form-control" rows="10" id="chatQuestion"></textarea>
                              {{-- <input type="text" class="form-control" placeholder="Wpisz swoje pytanie do chata AI" id="chatQuestion"> --}}
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-sm-2">
                           <button type="button" id="btnChatTrigger" class="btn btn-block bg-gradient-info btn-sm">Wyślij</button>
                        </div>
                     </div>
                     
                     <p></p>
                     <div class="row m-t-20">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label>Odpowiedź chata</label>
                              <textarea class="form-control" rows="25" disabled="" id="chatAnswer"></textarea>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div><!--/. container-fluid -->
   </section>

    <!-- /.content -->

    @vite(['resources/js/queryGetChatAnswer.js'])

@endsection
