<?php namespace App\Helpers;

class InformeFichajesPdf extends \fpdf\FPDF_EXTENDED {

	function Header(){
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(0, 10, 'Listado Resumen del registro de jornada (completo)', 0, 1, 'C');

		$this->SetFont('Arial', '', 7);
		$this->Cell(25, 6, ' Empresa:', 'TL');
		$this->SetFont('Arial', '', 7);
		$this->Cell(70, 6, $this->crew->company->name, 'T');
		$this->SetFont('Arial', '', 7);
		$this->Cell(25, 6, ' Trabajador:', 'TL');
		$this->SetFont('Arial', '', 7);
		$this->Cell(0, 6, $this->crew->name.' '.$this->crew->surname, 'TR', 1);

		$this->SetFont('Arial', '', 7);
		$this->Cell(25, 6, ' C.I.F./N.I.F.:', 'TL');
		$this->SetFont('Arial', '', 7);
		$this->Cell(70, 6, $this->crew->company->cif, 'T');
		$this->SetFont('Arial', '', 7);
		$this->Cell(25, 6, ' D.N.I.:', 'TL');
		$this->SetFont('Arial', '', 7);
		$this->Cell(0, 6, $this->crew->dni, 'TR', 1);
		
		$this->SetFont('Arial', '', 7);
		$this->Cell(25, 6, ' Periodo:', 'TLB');
		$this->SetFont('Arial', '', 7);
		$this->Cell(0, 6, date('d/m/Y', $this->inicio).' - '.date('d/m/Y', strtotime('-1 day', $this->fin)), 'TRB', 1);
	}

	function Footer(){
		$this->SetXY(10, -12);
		$this->SetFont('Arial', '', 7);
		$this->Cell(0, 7, 'Página '.$this->PageNo().' de {nb}', 0, 0, 'C');
	}

	private $crew;
	private $fichajes;
	private $inicio;
	private $fin;

	function __construct($crew, $inicio, $fin, $fichajes){

		parent::__construct();
		$this->crew = $crew;
		$this->fichajes = $fichajes;
		$this->inicio = $inicio;
		$this->fin = $fin;

		$this->SetMargins(10, 5);
		$this->SetAutoPageBreak(true, 15);
		$this->AliasNbPages();
		$this->AddPage();
		$this->SetDrawColor(50);

		$this->SetY($this->GetY() + 3);

		$this->SetFillColor(235);
		$this->SetFont('Arial', '', 8);
		$this->Cell(20, 14, 'FECHA', 1, 0, 'C', 1);
		$this->Cell(30, 14, 'HORA ENTRADA', 1, 0, 'C', 1);
		$this->Cell(30, 14, 'HORA SALIDA', 1, 0, 'C', 1);
		$this->Cell(40, 14, 'HORAS ORDINARIAS', 1, 0, 'C', 1);

		$x = $this->GetX();
		$y = $this->GetY();

		$this->Cell(70, 7, 'HORAS COMPLEMENTARIAS', 1, 0, 'C', 1);
		$this->SetXY($x, $y + 7);
		$this->Cell(35, 7, 'PACTADAS', 1, 0, 'C', 1);
		$this->Cell(0, 7, 'VOLUNTARIAS', 1, 1, 'C', 1);

		$ordinarias = 0;

		if(count($fichajes) > 0){
			foreach($fichajes as $fichaje){

				$hora_inicio = strtotime($fichaje->inicio);
				$hora_fin = strtotime($fichaje->fin);
				$horas_realizadas = round(($hora_fin - $hora_inicio) / 60 / 60, 2);

				$ordinarias += $horas_realizadas;

				$this->SetFont('Arial', '', 8);
				$this->Cell(20, 7, date('d/m/Y', $hora_inicio), 1, 0, 'C');
				$this->Cell(30, 7, date('G:i', $hora_inicio), 1, 0, 'C');
				$this->Cell(30, 7, date('G:i', $hora_fin), 1, 0, 'C');
				$this->Cell(40, 7, number_format($horas_realizadas, 2, '.', ''), 1, 0, 'C');
				$this->Cell(35, 7, '', 1, 0, 'C');
				$this->Cell(0,  7, '', 1, 1, 'C');

			}
		} else {
			$this->SetFont('Arial', '', 8);
			$this->Cell(0, 21, 'No se ha realizado ningún turno durante este periodo', 1, 1, 'C');
		}

		$this->SetFont('Arial', 'B', 8);
		$this->SetFillColor(235);
		$this->Cell(20, 10, 'TOTAL', 1, 0, 'C', 1);
		$this->Cell(30, 10, '', 1, 0, 'C', 1);
		$this->Cell(30, 10, '', 1, 0, 'C', 1);
		$this->Cell(40, 10, ($ordinarias > 0) ? number_format($ordinarias, 2, '.', '') : '', 1, 0, 'C', 1);
		$this->Cell(35, 10, '', 1, 0, 'C', 1);
		$this->Cell(0,  10, '', 1, 1, 'C', 1);

		if($this->GetY() > 180) $this->AddPage();

		$this->SetY(-90);
		$this->SetFont('Arial', '', 8);

		$this->Cell(95, 7, 'Firma de la empresa:');
		$this->Cell(95, 7, 'Firma del trabajador:', 0, 1);
		$this->SetY($this->GetY() + 25);

		$this->Cell(95, 7, '');
		$this->Cell(40, 7, 'En');
		$mes = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		$this->Cell(0, 7, ', a '.date('j').' de '.$mes[date('n')].' de '.date('Y'));

		$this->SetY($this->GetY() + 15);
		$this->SetFont('Arial', '', 7);
		$this->MultiCell(0, 4, 'Registro realizado en  cumplimiento de la letra h) del artículo 1 del R.D.-Ley 16/2013, de 20 de diciembre por el que se modifica el artículo 12.5 del E.T., por el que se establece que: La jornada de los trabajadores a tiempo parcial se registrará día a día y se totalizará mensualmente, entregando copia al trabajador, junto con el recibo de salarios, del resumen de todas las  horas realizadas en cada mes, tanto de las ordinarias como de las complementarias en sus distintas modalidades. El empresario deberá conservar los resúmenes mensuales de los registros de jornada durante un período mínimo de cuatro años. El incumplimiento empresarial de estas obligaciones de registro tendrá por consecuencia jurídica la de que el contrato se presuma celebrado a jornada completa, salvo prueba en contrario que acredite el carácter parcial de los servicios.');


	}

}
