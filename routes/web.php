<?php

Route::get('/', function () {
   return view('welcome');
});

Route::get('/admin/indicators', function () {
   return view('admin.daily_meeting');
});

//Route::get('/admin/indicators', function () {
//   return view('admin.indicators');
//});

/* Route::get('/admin/indicators', 'API\v1\QueryController@getIndicators');
  Route::get('/admin/time_tracking', 'API\v1\QueryController@getUsersTimeTracking');
  Route::get('/admin/productivity', 'API\v1\QueryController@showProductivityScreen');
  Route::post('/admin/time-tracking-detail', 'API\v1\QueryController@getUserTimeTrackingDetail');
 * 
 */

/*Route::get('/test-mail', function () {
   $nonCompliance = DB::select("SELECT 
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
                                       jira_task_manager.non_compliance nc
                                   INNER JOIN jira_task_manager.user u
                                      ON u.id = nc.user_id
                                   WHERE nc.notified = 'N'");
   
   return new App\Mail\SendMail($nonCompliance[7], 'email.non_compliance_template');
});*/
