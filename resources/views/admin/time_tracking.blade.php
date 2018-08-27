@extends('adminlte::page')

@section('title', 'Apontamento de Horas')

@section('content_header')
<style>
   .modal-body{
      max-height: calc(100vh - 200px);
      overflow-y: auto;
      overflow-x: auto;
   }
   .nowrap {
      white-space:nowrap;
   }
</style>
@stop

@section('content')
<div class="row">
   <table class="table">
      <tr>
         <td></td>
         @foreach($times as $key => $values)
         <td>
            <table class="table">
               <tr style="background-color: rgb(200,255,100)">
                  <td colspan="3" style="text-align: center">{{ \Carbon\Carbon::parse($key)->format('d/m/Y')}}</td>
               </tr>
               <tr style="background-color: rgb(230,255,190)">
                  <td>Nome</td>
                  <td>Horas</td>
                  <td>Detalhes</td>
               </tr>
               @foreach($values as $value)
               <tr>
                  <td>{{ $value->author_name }}</td>
                  <td>{{ $value->total_h }}</td>
                  <td style="text-align: center">
                     <button class="btn-box-tool" onclick="showDetail('{{$value->author_key}}', '{{ $key}}')"><i class="fa fa-fw fa-file "></i></button> 
                  </td>
               </tr>
               @endforeach 
            </table>
         </td>
         @endforeach
      </tr>
   </table>
</div>

<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Detalhes</h4>
         </div>
         <div class="modal-body">
            <table class="table table-striped" id="table_detail">
               <th>Autor</th>
               <th>Tarefa</th>
               <th>Tempo</th>
               <th>Comentário</th>
               <tbody></tbody>
            </table>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')

@stop

@section('js')
<script>
   function showDetail(user, date) {
   $.post("/admin/time-tracking-detail", {date: date, user: user })
           .done(function(data) {
           data.forEach(function(element) {
           $('#table_detail > tbody:first').append('<tr style="cell">');
           $('#table_detail > tbody:first').append('<td class="nowrap">' + element['author_name'] + '</td>');
           $('#table_detail > tbody:first').append('<td class="nowrap">' + element['task'] + '</td>');
           $('#table_detail > tbody:first').append('<td>' + element['total_h'] + '</td>');
           if (element['comment'] != '') {
           $('#table_detail > tbody:first').append('<td style="text-align: center;"><i class="fa fa-info-circle" aria-hidden="true" title="' + element['comment'] + '"></i></td>');
           } else{
           $('#table_detail > tbody:first').append('<td></td>');
           }
           $('#table_detail > tbody:first').append('</tr>');
           });
           });
   $("#modal_detail").modal();
   }
</script>
@stop