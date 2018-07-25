@extends('adminlte::page')

@section('title', 'Produção')

@section('content_header')
<style>
   .modal-body{
      max-height: calc(100vh - 400px);
      overflow-y: auto;
      overflow-x: auto;
   }
</style>
@stop

@section('content')

<div class="row">
   @foreach($indicators as $indicator)
   <div class="col-md-3">
      <div style="background-color: {{ $indicator['color'] }};height:100%;border-radius:5px;margin-bottom: 1em;" title="{{ $indicator['tooltip'] }}">
         <div style="width:100%;display:block;height:50px;">
            <i class="fab fa-accusoft" style="float:left;font-size: 80px;padding-left: 10px;padding-top: 5px;"></i>
            <p style="padding-right:30px;text-align: right;font-size: 32px;font-family:Helvetica;color: #6b6b6b;">{{ $indicator['title'] }}</p>
         </div>
         <div style="width:100%;display:block;">
            <p style="float: none;text-align: right;padding-right: 30px;padding-top: 0px;font-size: 26px;color: #6b6b6b;font-family: Helvetica;">{{ count($indicator['data']) }}</p>
         </div>
         @if (count($indicator['data']) > 0)
            <div style="height:40px;">
               <button type="button" class="btn btn-primary" style="float: right;margin-right: 30px;" data-toggle="modal" data-target="#modal_{{$indicator['id'] }}">Detalhes</button>
            </div>
         @endif
      </div>
     
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
                  @for ($i = 0; $i < count($indicator['data']); $i++)
                  <tr>
                     @foreach($indicator['data'][$i] as $key => $value)
                     <td>{{$value}}</td>
                     @endforeach
                  </tr>
                  @endfor
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