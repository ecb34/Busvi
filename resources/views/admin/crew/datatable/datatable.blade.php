	
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
	                <tr>
	                    @for ($i = 0; $i < count($array_values); $i++)
	                        @if ($i == 0)
	                            <td>
	                                @if ($route)
	                                <a href="{{ route($route . '.' . $link, $value) }}" class="text-success">
	                                    <strong>{!! $value->getAttribute($array_values[$i]) !!}</strong>
	                                </a>
	                                @else
	                                <span class="text-info">
	                                    <strong>{!! $value->getAttribute($array_values[$i]) !!}</strong>
	                                </span>
	                                @endif
	                            </td>
	                        @elseif ($i == 1)
	                        	<td>
	                        		@if ($value->all_day == 1)
	                        			<span class="text-success">
	                        				<i class="fa fa-check" aria-hidden="true"></i>
	                        			</span>
	                        		@else
	                        			<span class="text-danger">
	                        				<i class="fa fa-times" aria-hidden="true"></i>
	                        			</span>
	                        		@endif
	                        	</td>
	                        @elseif ($i == (count($array_values) - 2))
	                        	<td>
	                        		@if (! $value->getAttribute($array_values[$i]))
	                        			<span class="text-bold">
	                        				<i class="fa fa-minus" aria-hidden="true"></i>
	                        			</span>
	                        		@else
	                        			{!! $value->getAttribute($array_values[$i]) !!}
	                        		@endif
	                        	</td>
	                        @elseif ($i == (count($array_values) - 1))
	                        	<td align="center" data-id="{{ $value->id }}"></td>
	                        @else
	                            @if (filter_var($value->getAttribute($array_values[$i]), FILTER_VALIDATE_EMAIL))
	                                <td>
	                                    <a href="mailto:{!! $value->getAttribute($array_values[$i]) !!}" class="text-info">
	                                        {!! $value->getAttribute($array_values[$i]) !!}
	                                    </a>
	                                </td>
	                            @else
	                                <td>{!! $value->getAttribute($array_values[$i]) !!}</td>
	                            @endif
	                        @endif
	                    @endfor
	                </tr>
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