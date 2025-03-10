	
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
	                        @elseif ($i == 4)
	                        	<td>
	                        		@if (isset($value->admin->email))
	                        			<a href="mail:{{ $value->admin->email }}">{{ $value->admin->email }}</a>
	                        		@endif
	                        	</td>
	                        @elseif ($i == 5)
	                        	<td>
	                        		@if ($value->sector)
	                        			{{ $value->sector->name }}
	                        		@endif
	                        	</td>
	                        @elseif ($i == 6)
	                        	<td>
	                        		@if ($value->payed && ! $value->blocked)
	                        			<i class="fa fa-check text-success" aria-hidden="true"></i>
	                        		@else
	                        			<i class="fa fa-times text-danger" aria-hidden="true"></i>
	                        		@endif
	                        	</td>
	                        @elseif ($i == 7)
	                        	<td>
	                        		@if ($value->type)
	                        			<strong><span class="text-primary">Premium</span></strong>
	                        		@else
	                        			<strong><span class="text-warning">Basic</span></strong>
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