@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<div class="row">
   @foreach($indicators as $indicator)
   <div class="col-md-4">
      <div style="background-color:{{ $indicator['color'] }}; width:100%; height:100px; border-radius: 1em; align-items: center; display: flex; margin-bottom: 1em;" title="{{ $indicator['tooltip'] }}">
         <i class="fa fa-fw fa-file" style="font-size: 3.5em;"></i>
         <span style="font-size: 2.5em">{{ $indicator['title'] }}</span>
         <span style="font-size: 2.5em"> : </span>
         <span style="font-size: 2.5em">{{ count($indicator['data']) }}</span>
      </div>
      <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal_{{$indicator['id'] }}">
         Ver mais
      </button>
   </div>
   <div class="modal fade" id="modal_{{$indicator['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               <h4 class="modal-title" id="myModalLabel">{{ $indicator['title'] }}</h4>
            </div>
            <div class="modal-body">
               <table class="table table-striped">
                  @foreach($indicator['columns'] as $column)
                  <th>{{ $column }}</th>
                  @endforeach

               </table>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
         </div>
      </div>
   </div>
   @endforeach
</div>

@stop

@section('css')

@stop

@section('js')

@stop