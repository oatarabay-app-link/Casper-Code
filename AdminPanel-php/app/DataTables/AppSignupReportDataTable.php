<?php

namespace App\DataTables;

use App\Models\AppSignupReport;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class AppSignupReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        //return $dataTable->addColumn('action', 'app_signup_reports.datatables_actions');
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AppSignupReport $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AppSignupReport $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'desc']],
                'buttons' => [
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner'],
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'user_id',
            'email',
            'status' => ['searchable' => false],
            'signup_date',
            'signedin_date',
            'subscription',
            'emails_sent',
            'emails_problems',
            'device',
            'Country',
            'OS',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'app_signup_reports_datatable_'.time();
    }
}
