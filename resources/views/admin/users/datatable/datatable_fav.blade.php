	
	<div class="dt-responsive table-responsive">
	    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
	        <table id="{{ $datatable_id }}" class="table table-striped table-hover" cellspacing="0">
	            <thead>
	                <tr>
	                	@if ($titles)
		                    @for ($i = 0; $i < count($titles); $i++)
		                        <th>{!! $titles[$i] !!}</th>
		                    @endfor
		                @else
		                    @for ($i = 0; $i < count($array_values); $i++)
		                        <th>{!! $array_values[$i] !!}</th>
		                    @endfor
		                @endif
	                </tr>
	            </thead>
	            <tbody>
	                @foreach ($values as $value)
	                	@if ($value->company)
			                <tr data-id="{{ $value->id }}">
			                    @for ($i = 0; $i < count($array_values); $i++)
			                        @if ($i == 0)
			                            <td>
			                            	<a href="{{ route('companies.show', $value->company->id) }}">
			                                	{{ $value->company->name_comercial }}
			                                </a>
			                            </td>
			                        @elseif ($i == 1)
			                        	<td>
			                        		@if (isset($value->company->sector))
			                        			{{ $value->company->sector->name }}
			                        		@else
			                        			<span class="text-danger">
			                        				Sector eliminado
			                        			</span>
			                        		@endif
			                        	</td>
			                        {{-- @elseif ($i == (count($array_values) - 1))
			                        	<td>
			                        		<a href="#" class="btn-favourite text-danger">
			                        			<i class="fa fa-heart" aria-hidden="true"></i>
			                        		</a>
			                        	</td> --}}
			                        @else
			                            @if (filter_var($value->getAttribute($array_values[$i]), FILTER_VALIDATE_EMAIL))
			                                <td>
			                                    <a href="mailto:{!! $value->getAttribute($array_values[$i]) !!}" class="text-info">
			                                        {!! $value->getAttribute($array_values[$i]) !!}
			                                    </a>
			                                </td>)
			                            @else
			                                <td>{!! $value->getAttribute($array_values[$i]) !!}</td>
			                            @endif
			                        @endif
			                    @endfor
			                </tr>
			            @endif
	                @endforeach
	            </tbody>
	            <tfoot>
	                <tr>
	                	@if ($titles)
		                    @for ($i = 0; $i < count($titles); $i++)
		                        <th>{!! $titles[$i] !!}</th>
		                    @endfor
		                @else
		                    @for ($i = 0; $i < count($array_values); $i++)
		                        <th>{!! $array_values[$i] !!}</th>
		                    @endfor
		                @endif
	                </tr>
	            </tfoot>
	        </table>
	    </div>
	</div>