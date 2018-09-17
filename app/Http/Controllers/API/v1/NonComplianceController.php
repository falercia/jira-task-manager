<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class NonComplianceController extends Controller {

   public function index() {
      //
   }

   public function create() {
      //
   }

   public function store(Request $request) {
      //
   }

   public function show($id) {
      //
   }

   public function edit($id) {
      //
   }

   public function update(Request $request, $id) {
      //
   }

   public function destroy($id) {
      //
   }

   public function processEmail() {
      $nonCompliances = DB::select("SELECT 
                                       u.name,
                                       u.email,
                                       nc.id,
                                       nc.date,
                                       nc.description,
                                       nc.impact,
                                       nc.action_plan,
                                       case
                                          when nc.type = 'AF' then 'FALTA DE ATENÇÂO'
                                          when nc.type = 'MP' then 'PROBLEMAS DE MERGE'
                                          when nc.type = 'DF' then 'FALHA DE DESENVOLVIMENTO'
                                          when nc.type = 'TF' then 'FALHA DE TESTE'
                                           end as type_description,
                                       case
                                          when nc.severity = 'L' then '#70df6f;'
                                          when nc.severity = 'M' then '#eff971'
                                          when nc.severity = 'H' then '#e6634d'
                                           end as severity_color,
                                       case
                                          when nc.severity = 'L' then 'BAIXA'
                                          when nc.severity = 'M' then 'MÉDIA'
                                          when nc.severity = 'H' then 'ALTA'
                                           end as severity_description
                                   FROM
                                       non_compliance nc
                                   INNER JOIN user u
                                      ON u.id = nc.user_id
                                   WHERE nc.notified = 'N' and nc.id = 8");
      $count = 0;
      foreach ($nonCompliances as $nonCompliance) {
         $subject = 'Não conformidade #' . $nonCompliance->id . '/' . Carbon::parse($nonCompliance->date)->format('Y');

         Mail::to($nonCompliance->email)
                 ->cc('fabio.garcia@hubchain.io')
                 ->send(new SendMail($nonCompliance, 'email.non_compliance_template', $subject));

         if (Mail::failures()) {
            return response()->json(Mail::failures());
         }

         DB::table('non_compliance')
                 ->where('id', $nonCompliance->id)
                 ->update(['notified' => 'Y',
                     'notified_date' => Carbon::now()
         ]);
         $count++;
      }

      return response()->json('OK. ' . $count . ' processado(s).');
   }

}
