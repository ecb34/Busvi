<?php

namespace App\DataTables;

use App\Comision;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class ComisionesDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'admin.comisiones.datatable_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Trademark $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Comision $model)
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
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom'       => 'Bfrtip',
                'pageLength' => 50,
                'order'     => [[0, 'asc']],
                'buttons'   => [                    
                     ['extend'  =>'print', 'text' => 'Imprimir'],
                    ['extend'  =>'reset', 'text' => 'Reiniciar'],
                    ['extend'  =>'reload', 'text' => 'Recargar'],
                    [
                         'extend'  => 'collection',
                         'text'    => '<i class="fa fa-download"></i> Exportar',
                         'buttons' => ['csv','excel','pdf'],
                    ],
                ],
                 'language' => ['url' => asset('vendor/datatables/Spanish.json')],
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
            'nombre' =>['name' => 'nombre', 'data' =>'nombre'],
            'porcentaje' =>['name' => 'porcentaje', 'data' =>'porcentaje'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'comisionesdatatable_' . time();
    }
}
