	
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
	                            	@if ($value->service['name'])
		                                @if ($route)
			                                <a href="{{ route($route . '.' . $link, $value) }}" class="text-success">
			                                    <strong>{{ $value->service['name'] }}</strong>
			                                </a>
		                                @else
			                                <span class="text-info">
			                                    <strong>{{ $value->service['name'] }}</strong>
			                                </span>
		                                @endif
		                            @else
		                            	<p class="text-warning text-bold">No existe</p>
	                            	@endif
	                            </td>
	                        @elseif ($i == 1)
	                        	<td>
	                        		@if ($value->customer)
	                        			{{ $value->customer->name }}
		                            @else
		                            	<p class="text-warning text-bold">No existe</p>
	                        		@endif
	                        	</td>
	                        @elseif ($i == 2)
	                        	<td>
	                        		@if ($value->customer)
	                        			<a href="mailto:{{ $value->customer->email }}">
	                        				{{ $value->customer->email }}
	                        			</a>
		                            @else
		                            	<p class="text-warning text-bold">No existe</p>
	                        		@endif
	                        	</td>
	                        @elseif ($i == 3)
	                        	<td>
	                        		@if ($value->customer)
	                        			<a href="tel:{{ $value->customer->phone }}">
	                        				{{ $value->customer->phone }}
	                        			</a>
		                            @else
		                            	<p class="text-warning text-bold">No existe</p>
	                        		@endif
	                        	</td>
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