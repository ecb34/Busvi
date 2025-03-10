<?php

namespace App\DataTables;

use App\ChequeRegalo;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Config;
class AdminChequeRegaloDataTable extends DataTable
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
  /*      $dataTable->editColumn('id', function($row) {
                return '<div class="checkbox icheck selectable">
                	<label>
                		<input type="checkbox" id="'.$row['id'].'" value="'.$row['id'].'">

                	</label>
                </div>';    
            });*/
        $dataTable->editColumn('created_at', function($row){                
                return $row->created_at->format('d-m-Y');
            })  
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name : 'Cualquiera';
            })   
             ->editColumn('to_user_id', function($row){               
                return $row->to_user_id ? $row->destinatario->name.' '.$row->destinatario->surname : 'Desconocido';
            })    
            ->editColumn('status', function($row){
                return Config::get('cheques_regalo.estados')[$row->status];
            })     
            ->editColumn('pagado_a_comercio', function($row){
                return $row->pagado_a_comercio ? 'SI' : 'NO' ;
            })   
             ->editColumn('used_at', function($row){   
                return $row->used_at ? $row->used_at->format('d-m-Y') : '';
            }); 
        $dataTable->filterColumn('company_id', function ($query, $keyword){            
            return $query->whereHas('company', function($q) use($keyword){
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
        });      
        $dataTable->rawColumns(['id','action']);         
        return $dataTable->addColumn('action', 'admin.cheques_regalo.admin_datatable_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ChequeRegalo $model)
    {
        $query = $model->newQuery()->with('company', 'destinatario');
        if($this->tipo == 'negocio'){
          $query = $query->where('company_id', \Auth::user()->company_id);
        }
        return $query;
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
            ->addAction(['width' => '10%'])
            ->parameters([
                'dom'     => 'Blfrtip',
//
                'order'     => [[1, 'asc']],
                'pageLength' => '50',
                'scrollX' => false,
                'buttons' => [
               //     ['extend'  =>'print', 'text' => 'Imprimir'],
               //     ['extend'  =>'reset', 'text' => 'Reiniciar'],
               //     ['extend'  =>'reload', 'text' => 'Recargar'],
                    [
                         'extend'  => 'collection',
                         'text'    => '<i class="fa fa-download"></i> Exportar',
                 //        'buttons' => ['csv','excel','pdf'],
                         'buttons' => ['excel'],
                    ],
               //  ['extend'  =>'colvis', 'text' => 'Columnas'], //Descomentar esta linea para permitir mostrar / ocultar columnas
                ],
                "rowCallback" => "function( row, data ) {
                    var id = $(row).find('input[type=\"checkbox\"]').attr('id');
                    if ( $.inArray(id, selected) !== -1 ) {
                        $(row).find('input[type=\"checkbox\"]').iCheck('check');
                    } else {
                        $(row).find('input[type=\"checkbox\"]').iCheck('uncheck');
                    }
                }",
                 'initComplete' => "function () {     
                        ultimo = this.api().columns()[0].length -1;                               
                        this.api().columns().every(function () {
                            var column = this;                                          
                            if( column[0] < ultimo){   //En todas las columnas excepto en acciones         
                                switch(column[0][0]) {
                                    case 0:
                                      var input = document.createElement('input');
                                        input.setAttribute(".'"'."class".'"'.", ".'"'."form-control".'"'.");
                                        $(input).appendTo($(column.footer()).empty())
                                        .on('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });  
                                        $(input).addClass('form-control');
                                        input.setAttribute('type', 'date');

                                    break;
                                    case 3:
                                        var select = '<select id=\"estados\" class=\"form-control\">' +
                                                    '<option value=\"\">Todos</option>' +
                                                    '<option value=\"0\">Pendiente Pago</option>' +
                                                    '<option value=\"1\">Disponible</option>' +
                                                  //  '<option value=\"2\">Parcialmente Usado</option>' +
                                                    '<option value=\"3\">Usado</option>' +
                                                  '</select>';
                                        $(select).appendTo($(column.footer()).empty())
                                            .on('change', function () {
                                                column.search($(this).val(), false, false, true).draw();
                                            });
                                       
                                    break;
                                    case 4:
                                       var input = document.createElement('input');
                                        input.setAttribute(".'"'."class".'"'.", ".'"'."form-control".'"'.");
                                        $(input).appendTo($(column.footer()).empty())
                                        .on('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });  
                                        $(input).addClass('form-control');
                                        input.setAttribute('type', 'date');

                                    break;
                                    case 5:
                                        var select = '<select id=\"pagado_a_comercio\" class=\"form-control\">' +
                                                    '<option value=\"\">Todos</option>' +
                                                    '<option value=\"1\">SI</option>' +
                                                    '<option value=\"0\">NO</option>' +                                                 
                                                  '</select>';
                                        $(select).appendTo($(column.footer()).empty())
                                            .on('change', function () {
                                                column.search($(this).val(), false, false, true).draw();
                                            });
                                       
                                    break;
                                    
                                    default:
                                        var input = document.createElement('input');
                                        input.setAttribute(".'"'."class".'"'.", ".'"'."form-control".'"'.");
                                        $(input).appendTo($(column.footer()).empty())
                                        .on('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });  
                                        $(input).addClass('form-control');  
                                }   
                            }
                        });
                }",
                'fnDrawCallback' => "function() {                   
                    $('input').iCheck({  // INitialize ichecks and select2
                        checkboxClass: 'icheckbox_flat-blue',
                        radioClass: 'iradio_flat-blue',
                        increaseArea: '20%',
                    }); 
                    $('input').on('ifChanged', function(event){
                        
                        var id = $(this).attr('id');
                        var index = $.inArray(id, selected);
    
                        if ( index === -1 ) {
                            selected.push( id );
                        } else {
                            selected.splice( index, 1 );
                        }

                    });
                }",

                
                'language' => ['url' => asset('vendor/datatables/Spanish.json')]
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
            trans('app.cheques_regalo.codigo') => ['name' => 'uuid', 'data' => 'uuid'], 
            trans('app.cheques_regalo.fecha_alta') => ['name' => 'created_at', 'data' => 'created_at'], 
            trans('app.cheques_regalo.importe') => ['name' => 'importe', 'data' => 'importe'], 
            trans('app.cheques_regalo.to_use_in_company') => ['name' => 'company_id', 'data' => 'company_id'], 
            trans('app.cheques_regalo.estado') => ['name' => 'status', 'data' => 'status'], 
            trans('app.cheques_regalo.fecha_uso') => ['name' => 'used_at', 'data' => 'used_at'], 
            trans('app.cheques_regalo.destinatario') => ['name' => 'to_user_id', 'data' => 'to_user_id'], 
            trans('app.cheques_regalo.a_pagar_a_comercio') => ['name' => 'a_pagar_al_negocio', 'data' => 'a_pagar_al_negocio'], 
            trans('app.cheques_regalo.pagado') => ['name' => 'pagado_a_comercio', 'data' => 'pagado_a_comercio',  'width' => '10%'], 
        ];

     /* if(\Auth::user()->role == 'superadmin'){
        return [
            trans('app.cheques_regalo.codigo') => ['name' => 'uuid', 'data' => 'uuid'], 
            trans('app.cheques_regalo.fecha_alta') => ['name' => 'created_at', 'data' => 'created_at'], 
            trans('app.cheques_regalo.importe') => ['name' => 'importe', 'data' => 'importe'], 
            trans('app.cheques_regalo.to_use_in_company') => ['name' => 'company_id', 'data' => 'company_id'], 
            trans('app.cheques_regalo.estado') => ['name' => 'status', 'data' => 'status'], 
            trans('app.cheques_regalo.fecha_uso') => ['name' => 'used_at', 'data' => 'used_at'], 
            trans('app.cheques_regalo.destinatario') => ['name' => 'to_user_id', 'data' => 'to_user_id'], 
            trans('app.cheques_regalo.a_pagar_a_comercio') => ['name' => 'a_pagar_al_negocio', 'data' => 'a_pagar_al_negocio'], 
            trans('app.cheques_regalo.pagado') => ['name' => 'pagado_a_comercio', 'data' => 'pagado_a_comercio',  'width' => '10%'], 
        ];
      }
      else{
        return [
       //     ' ' => ['name' => 'id', 'data' => 'id', 'width' => '5%','class'=>'check_all_filter', 'exportable' => false],            
            trans('app.cheques_regalo.codigo') => ['name' => 'uuid', 'data' => 'uuid'], 
            trans('app.cheques_regalo.fecha_alta') => ['name' => 'created_at', 'data' => 'created_at'], 
            trans('app.cheques_regalo.importe') => ['name' => 'importe', 'data' => 'importe'], 
            trans('app.cheques_regalo.to_use_in_company') => ['name' => 'company_id', 'data' => 'company_id'], 
            trans('app.cheques_regalo.estado') => ['name' => 'status', 'data' => 'status'], 
            trans('app.cheques_regalo.fecha_uso') => ['name' => 'used_at', 'data' => 'used_at'], 
            trans('app.cheques_regalo.destinatario') => ['name' => 'to_user_id', 'data' => 'to_user_id'], 
            trans('app.cheques_regalo.pagado') => ['name' => 'pagado_a_comercio', 'data' => 'pagado_a_comercio',  'width' => '10%'], 
        ];
      }*/
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'cheques_regalo_tabla' . time();
    }
}