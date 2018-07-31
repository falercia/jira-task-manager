<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class QueryController extends Controller {

   private function getUserNoTask() {
      $indicator = array();
      $indicator['id'] = 'users_no_task';
      $indicator['title'] = 'Usuários sem tarefa';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('Nome', 'E-mail');
      $indicator['tooltip'] = 'Usuários que estão sem tarefas atribuidas no status In Progress ou que estão testando tarefa sem estar atribuido como Tester.';

      $indicator['data'] = DB::select('SELECT 
                               u.name, u.email
                          FROM
                              jira_task_manager.user u
                           WHERE u.is_resource = \'Y\' 
                            AND NOT EXISTS (select 1
                                               from jira_task_manager.task t
                                                                  where (t.assignee_key = u.jira_key and t.status_id = 3)
                                                 or (t.status_id = 10004 and t.tester_assignee_key = u.jira_key)
                  )');

      return $indicator;
   }

   private function getTasksWithoutDate() {
      $indicator = array();
      $indicator['id'] = 'tasks_without_date';
      $indicator['title'] = 'Tarefas sem prazo';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('Key', 'Tarefa', 'Atribuída a', 'Status');
      $indicator['tooltip'] = 'Tarefas que estão sem prazo inicial ou final definidos (todos os status).';

      $indicator['data'] = DB::select('SELECT 
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_name
                                         FROM
                                             jira_task_manager.task t
                                         WHERE
                                             (t.initial_date IS NULL
                                                 OR t.deadline IS NULL)');

      return $indicator;
   }

   private function getTasksRunningWithoutDate() {
      $indicator = array();
      $indicator['id'] = 'tasks_running_without_date';
      $indicator['title'] = 'Tarefas em produção sem prazo';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('Key', 'Tarefa', 'Atribuída a', 'Status');
      $indicator['tooltip'] = 'São tarefas que estão sendo executadas sem prazo.';

      $indicator['data'] = DB::select('SELECT 
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_name
                                      FROM
                                          jira_task_manager.task t
                                      WHERE
                                          (t.initial_date IS NULL OR t.deadline IS NULL)
                                        AND t.status_category_id IN (4)');

      return $indicator;
   }

   private function getTasksOverdue() {
      $indicator = array();
      $indicator['id'] = 'tasks_overdue';
      $indicator['title'] = 'Tarefas atrasadas';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('Key', 'Tarefa', 'Atribuída a', 'Status', 'Data final', 'Qtd. atrasos');
      $indicator['tooltip'] = 'São tarefas que extrapolaram a data de conclusão';

      $indicator['data'] = DB::select('SELECT 
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_name, 
                                              DATE_FORMAT(t.deadline, \'%d/%m/%Y\') as deadline, 
                                             DATEDIFF(date(now()), t.deadline) as days_of_delay
                                      FROM
                                          jira_task_manager.task t
                                      WHERE
                                          t.deadline IS NOT NULL
                                        AND DATE(NOW()) > t.deadline
                                        AND t.status_category_id NOT IN (3)');

      return $indicator;
   }

   private function getTasksForToday() {
      $indicator = array();
      $indicator['id'] = 'tasks_for_today';
      $indicator['title'] = 'Tarefas para hoje';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('Key', 'Tarefa', 'Atribuída a', 'Status');
      $indicator['tooltip'] = 'São tarefas que estão planjedas para serem concluídas hoje.';

      $indicator['data'] = DB::select('SELECT 
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_name
                                      FROM
                                          jira_task_manager.task t
                                      WHERE
                                          t.deadline IS NOT NULL
                                        AND DATE(NOW()) = t.deadline
                                        AND t.status_category_id NOT IN (3)');

      return $indicator;
   }

   public function getIndicators() {
      $indicators = array();

      array_push($indicators, $this->getUserNoTask());
      array_push($indicators, $this->getTasksWithoutDate());
      array_push($indicators, $this->getTasksRunningWithoutDate());
      array_push($indicators, $this->getTasksOverdue());
      array_push($indicators, $this->getTasksForToday());

//      dd($indicators);

      return view('admin.indicators', compact('indicators'));
   }

   public function getUsersTimeTracking(Request $request) {
      $indicators = array();

      $indicator['users_timetracking'] = DB::select('SELECT 
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_name
                                      FROM
                                          jira_task_manager.task t
                                      WHERE
                                          t.deadline IS NOT NULL
                                        AND DATE(NOW()) = t.deadline
                                        AND t.status_category_id NOT IN (3)');
      return view('admin.time_tracking');
   }

   public function showProductivityScreen() {
      $cutDateMessage = 'Disponível apenas para datas a partir de 23/07/2018';
      return view('admin.productivity', compact('cutDateMessage'));
   }

}
