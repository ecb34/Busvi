<?php

namespace App\DataTables;

use App\CategoriaEvento;
use App\Evento;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Config;
class AdminEventosDataTable extends DataTable
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
     //   $dataTable->editColumn('id', function($row) {
     //           return '<div class="checkbox icheck selectable">
     //           	<label>
     //           		<input type="checkbox" id="'.$row['id'].'" value="'.$row['id'].'">
    //            	</label>
    //            </div>';    
    //        });
        $dataTable->editColumn('organizador', function($row){               
                return $row->organizador_id ? $row->organizador->name : '';
            }) 
            ->editColumn('company_id', function($row){               
                return $row->company_id ? $row->company->name : '';
            })
            ->editColumn('organizador_id', function($row){               
                return $row->organizador_id ? '<a href="'.url('/admin/users/'.$row->organizador_id.'/edit').'">'.$row->organizador->name.'</a>' : '';
            })
            
            ->editColumn('categoria_evento_id', function($row){               
                return $row->categoria_evento_id ? $row->categoria->name : '';
            })    
            ->editColumn('validado', function($row){
                return $row->pagado_a_comercio ? 'SI' : 'NO' ;
            })     
             ->editColumn('pagado_a_comercio', function($row){
                return $row->pagado_a_comercio ? 'SI' : 'NO' ;
            })   
            ->editColumn('desde', function($row){   
                return $row->desde ? $row->desde->format('d-m-Y') : '';
            })
            ->addColumn('asistentes', function($row){
                return count($row->asistentes).(!is_null($row->aforo_maximo) ? '/'.$row->aforo_maximo : '');
            }); 

        $dataTable->filterColumn('company_id', function ($query, $keyword){            
            return $query->whereHas('company', function($q) use($keyword){
                $q->where('name', 'like', '%'.$keyword.'%');
            });
        })->filterColumn('organizador_id', function ($query, $keyword){            
            return $query->whereHas('organizador', function($q) use($keyword){
                $q->where('name', 'like', '%'.$keyword.'%');
                $q->orWhere('surname', 'like', '%'.$keyword.'%');
            });
        });

        $dataTable->rawColumns(['id','action', 'organizador_id']);         
        return $dataTable->addColumn('action', 'admin.eventos.admin_datatable_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Evento $model)
    {
        return $model->newQuery()->with('company', 'categoria', 'organizador');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
       /*$categorias = '';
        foreach (CategoriaEvento::all() as $id => $nombre){
            $categorias .="select.append( '<option value=".$id.">".ucfirst($nombre)."</option>' );  ";
        }    */
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '10%'])
            ->parameters([
                'dom'     => 'Blfrtip',
//
                'order'     => [[1, 'desc']],
                'pageLength' => '50',
                'scrollX' => false,
                'buttons' => [
               //     ['extend'  =>'print', 'text' => 'Imprimir'],
               //     ['extend'  =>'reset', 'text' => 'Reiniciar'],
              //      ['extend'  =>'reload', 'text' => 'Recargar'],
                    [
                         'extend'  => 'collection',
                         'text'    => '<i class="fa fa-download"></i> Exportar',
                //         'buttons' => ['csv','excel','pdf'],
                         'buttons' => ['excel'],
                    ],
               //  ['extend'  =>'colvis', 'text' => 'Columnas'], //Descomentar esta linea para permitir mostrar / ocultar columnas
                ],
                 'initComplete' => "function () {     
                        ultimo = this.api().columns()[0].length -1;                               
                        this.api().columns().every(function () {
                            var column = this;                                          
                            if(column[0] < ultimo){   //En todas las columnas excepto en acciones         
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
                                    case 4:
                                        var select = '<select id=\"validado\" class=\"form-control\">' +
                                                    '<option value=\"\">Todos</option>' +
                                                    '<option value=\"1\">SI</option>' +
                                                    '<option value=\"0\">NO</option>' +                                                 
                                                  '</select>';
                                        $(select).appendTo($(column.footer()).empty())
                                            .on('change', function () {
                                                column.search($(this).val(), false, false, true).draw();
                                            });
                                       
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
       //     ' ' => ['name' => 'id', 'data' => 'id', 'width' => '5%','class'=>'check_all_filter'],            
            trans('app.eventos.fecha') => ['name' => 'desde', 'data' => 'desde'], 
            trans('app.eventos.nombre') => ['name' => 'nombre', 'data' => 'nombre'], 
            trans('app.eventos.company') => ['name' => 'company_id', 'data' => 'company_id'], 
            trans('app.eventos.organizador') => ['name' => 'organizador_id', 'data' => 'organizador_id'], 
            trans('app.eventos.importe') => ['name' => 'precio_final', 'data' => 'precio_final'], 
            trans('app.cheques_regalo.a_pagar_a_comercio') => ['name' => 'precio', 'data' => 'precio'], 
            trans('app.eventos.validado') => ['name' => 'validado', 'data' => 'validado',  'width' => '10%'], 
            trans('app.eventos.asistentes') => ['name' => 'asistentes', 'data' => 'asistentes'],
            trans('app.eventos.pagado') => ['name' => 'pagado_a_comercio', 'data' => 'pagado_a_comercio',  'width' => '10%'], 
        ];
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