<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Busvi</title>

	<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">

	<style>
		html,body
		{
			margin: 0;
			padding: 0;
		}

		body
		{
			background: #F5F5F5;
		}

		a
		{
			color: #6ab5df;
		}

		.container
		{
			margin-top: 30px;
			background: #FFFFFF;
			max-width: 768px;
			width: 100%;
			margin: auto;
			font-family: 'Raleway', sans-serif;
		}

		header
		{
			text-align: center;
		}

		header > div
		{
			padding: 25px 0;
		}

		header > .headerTop
		{
			background: #FFF;
		}

		header > .headerDown
		{
			background: #6ab5df;
		}

		header > .headerDown > h1
		{
			color: #FFFFFF;
			margin: 0px;
		}

		.order
		{
			padding: 15px 20px;
			color: #454545;
		}

		table
		{
			border-collapse: collapse;
		}

		.order > .customer > p > span
		{
			font-weight: bold;
		}

		.order > .orderTable > table,
		.order > .orderTable > table > tbody > tr > td > table,
		.order > .orderTable > table > tbody > th > td > table
		{
			width: 100%;
		}

		.order > .orderTable > table > tbody > th
		{
			border: 1px solid #FFF;
		}

		.order > .orderTable > table > tbody > tr
		{
			border: 1px solid #333;
		}

		.order > .orderTable > table > tbody > th:last-child,
		.order > .orderTable > table > tbody > tr:last-child
		{
			border: 0px;
		}

		.order > .orderTable > table > tbody > th:last-child > td,
		.order > .orderTable > table > tbody > tr:last-child > td
		{
			vertical-align: top;
		}

		.order > .orderTable > table > tbody > th:last-child > td:last-child > h4,
		.order > .orderTable > table > tbody > th:last-child > td:first-child > p,
		.order > .orderTable > table > tbody > tr:last-child > td:last-child > h4,
		.order > .orderTable > table > tbody > tr:last-child > td:first-child > p
		{
			margin-top: 0 !important;
		}

		.order > .orderTable > table > tbody > th > td,
		.order > .orderTable > table > tbody > tr > td
		{
			padding: 15px 25px 25px;
		}

		p.name_product
		{
			font-weight: bold;
		}

		p.name_product > span.preorder
		{
			color: #e67e22;
			display: block;
		}
		span.pedido-oferta {
		    color: #fff;
		    font-weight: 500;
		    background-color: #2ecc71;
		    border-radius: 50px;
		    padding: 1px 6px;
		    margin-bottom: 3px;
		    display: inline-block;
		    font-size: 12px;
		}

		.text-uppercase
		{
			text-transform: uppercase;
		}

		.text-center
		{
			text-align: center;
		}

		.btn
		{
			display: inline-block;
		    font-weight: 400;
		    text-align: center;
		    white-space: nowrap;
		    vertical-align: middle;
		    -webkit-user-select: none;
		    -moz-user-select: none;
		    -ms-user-select: none;
		    user-select: none;
		    border: 1px solid transparent;
		    padding: .375rem .75rem;
		    font-size: 1rem;
		    line-height: 1.5;
		    border-radius: .25rem;
		    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
		}

		.btn-primary
		{
		    color: #000;
		    background-color: #6ab5df;
		    border-color: #6ab5df;
		}

		footer
		{
			margin-top: 40px;
			text-align: center;
			padding-bottom: 20px;
		}

		footer > p > a
		{
			color: #000;
		}

		/* Portrait and Landscape */
		@media only screen 
		  and (min-device-width: 375px) 
		  and (max-device-width: 667px) 
		  and (-webkit-min-device-pixel-ratio: 2) { 
				html,body
				{
					min-height: 100%;
				}

				.container
				{
					margin-top: 30px;
					background: #FFFFFF;
					max-width: 100%;
					width: 100%;
					margin: auto;
					font-family: 'Raleway', sans-serif;
					height: 100%;
				}
		}
	</style>
</head>
<body>
	<div class="container">
		<header>
			<div class="headerTop">
				<a href="{{ route('home.index') }}">
					<img src="https://busvi.com/img/busvi_logo.jpg" alt="Busvi" height="150px">
				</a>
			</div>
			<div class="headerDown">
				<h1>
					¡Bienvenid@!
				</h1>
			</div>
		</header>

		<div class="order">
			<div class="customer">
				<p>Has realizado el registro con éxito.</p>
				<p>Estos son los datos de tu registro:<br><br></p>
				<p>Usuario: <span>{{ $data->username }}</span></p>
				<p>eMail: <span>{{ $data->email }}</span></p>
				<br>
				<p>La contraseña... es cosa tuya, nosotros la tenemos cifrada ;)</p>
				<p>Aunque si no la conoces o no la recuerdas, puedes generar una nueva desde aquí:</p>
				<p class="text-center">
					<a href="{{ route('password.request') }}" class="btn btn-primary">
						Cambiar Contraseña
					</a>
				</p>
			</div>
		</div>

		<footer>
			<p>
				Si necesitas ayuda puedes llamar a <a href="tel:918 260 099">918 260 099</a><br>
				o enviar un mail a <a href="mailto:info@busvi.com">info@busvi.com</a>
			</p>
			<p>
				Visitanos en:<br><a href="https://www.busvi.com">www.busvi.com</a>
			</p>
		</footer>
	</div>
</body>
</html>