<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Functions extends Migration {

   public function up() {
      DB::unprepared($this->getTimeTrackingFromUserFunction());
   }

   public function down() {
      //
   }

   private function getTimeTrackingFromUserFunction() {
      return '
CREATE FUNCTION jira_task_manager.fnc_get_user_timetracking
(p_jira_key varchar(100)
,p_initial_date date
,p_final_date date
) RETURNS INTEGER
BEGIN
   declare v_time_spent_seconds integer;
   
   SELECT 
         SUM(time_spent_seconds) AS total
    INTO v_time_spent_seconds
    FROM
            jira_task_manager.task_worklog t
    WHERE
            t.created_at_jira BETWEEN p_initial_date AND p_final_date
       AND t.author_key = p_jira_key COLLATE utf8_unicode_ci;
      
   return v_time_spent_seconds;
END
';
   }

}
