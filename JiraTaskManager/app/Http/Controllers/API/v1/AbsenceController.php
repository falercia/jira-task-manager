<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absence;

class AbsenceController extends Controller {

   private $absence;

   public function index(Absence $absence) {
      $this->absence = $absence;
   }

   public function create() {
      //
   }

   public function store(Request $request) {
      $requestData = $request->only(['user_id', 'initial_date', 'final_date', 'type', 'comment']);

      if (!$insert = $this->absence->create($requestData)) {
         return response()->json('Error');
      }

      return response()->json('Success');
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

}
