<?php 

error_reporting(E_ALL ^ E_NOTICE);

require_once  dirname(__FILE__).'/fpdf17/fpdf.php';
require_once  dirname(__FILE__).'/lib.inc.php';

$r = new Receipt();
$r->go();

class Receipt {
	public $data = array ();
	private $pdf, $bp, $rw=210, $rh=103;
	private $autor = '';
	private $direction = '';
	private $title = 'RECIBO DE APORTACIÓN - CUOTA COMUNIDAD DE PROPIETARIOS';
	private $title_extra = 'RECIBO DE APORTACIÓN EXTRAORDINARIA';
	private $months = array ('1'=>'Enero', '2'=>'Febrero', '3'=>'Marzo', '4'=>'Abril', '5'=>'Mayo', '6'=>'Junio', '7'=>'Julio', '8'=>'Agosto',
	                         '9'=>'Septiembre', '10'=>'Ocubre', '11'=>'Noviembre', '12'=>'Diciembre');
	private $current_num  = 0;
	private $current_year = '2014';
	private $to_period    = '';
	private $concept      = '';
	
	public function load_config () {
 		if (!file_exists ('config.txt')) {
 			echo "¡Falta el archivo config.txt!\n";
 			exit ();
 		}
 		
		$f = file_get_contents ('config.txt');
		list ($this->autor, $this->direction) = explode ("\n",$f);
		$this->autor = trim($this->autor);
		$this->direction = trim($this->direction);
	}
	
	
	public function begin_page ($params, $f) {
		$this->bp = dirname(__FILE__).'/img/';

		$this->pdf = new Receipt_PDF('L','mm', array($this->rw, $this->rh));
		$this->pdf->setMargins (0,0,0);
		$this->pdf->SetAutoPageBreak (0);
		$this->pdf->AddPage();
		$this->pdf->setAuthor ($this->autor);
			
		$this->background ();
		$this->content ($params);
			
		$this->pdf->Close();
		$this->pdf->Output($f);
		
		$this->current_num++;
	}
	
	public function background () {
		
		/*
		 $this->pdf->SetDrawColor (180,180,180);
		 $this->pdf->SetLineWidth(0.02);
		 for ($i=1;$i<30;$i++) {
			$this->pdf->Line (6, ($i*4), $this->rw-6, ($i*4), 'D');
		 }
		*/
		
		$this->pdf->SetDrawColor (0,0,0);
		$this->pdf->SetTextColor (0,0,0);
		$this->pdf->SetFillColorStr (255,255,255);
		$this->pdf->SetLineWidth(0.1);
		$this->pdf->Rect (3, 3, $this->rw-6, $this->rh-6, 'D');
		$this->pdf->Rect (4, 4, $this->rw-8, $this->rh-8, 'D');
		
		$this->pdf->SetFont('Arial','',16);
		$this->pdf->SetXY (10, 10);
		if ($this->concept=='') {
		  $this->pdf->MultiCell ($this->rw-20, 2.8, $this->title, '0', '1', 'L', false);
		} else {
		  $this->pdf->MultiCell ($this->rw-20, 2.8, $this->title_extra, '0', '1', 'L', false);
		}
		
		$this->pdf->SetXY (10, 15);
		$this->pdf->SetFont('Arial','B',12);
		$this->pdf->MultiCell ($this->rw-20, 2.8, $this->autor, '0', '1', 'L', false);
		
		$num = substr ($this->current_year, 2) . '/'. sprintf ("%02d",$this->current_num);
		$this->pdf->SetXY (10, 20);
		$this->pdf->SetFont('Arial','B',12);
		$this->pdf->MultiCell ($this->rw-20, 2.8, 'Recibo: ' . $num, '0', '1', 'L', false);
		
	}
	
	public function content (&$params) {
		
		$_d  = 'Barcelona a   <b>' . $params['day'] . '</b>   de   <b>' . $this->months[(int)$params['month']] . '</b>   de   <b>' . $params['year'] . '</b>';
		$this->pdf->setFont('Arial','',12);
		$this->pdf->setXy (10,30);
		$this->pdf->WriteHTML($_d);
		$this->pdf->setXy (10,35);
		$this->pdf->WriteHTML($this->direction);
		
		$_d = "He recibido de <b>". $params['user'] . "</b> del piso <b>".$params['floor']."</b>";
		$this->pdf->setXy (10,45);
		$this->pdf->WriteHTML($_d);
		
		$_d = "La cantidad de <b>#".$params['amount']."#</b> euros ";
		$this->pdf->setXy (10,50);
		$this->pdf->WriteHTML($_d);
		
		if ($this->concept=='') { 
		 $_d = "En concepto de la cuota que corresponde al mes de <b>".$this->months[(int)$params['month']]."</b> ".$params['year'];
		} else {
		 $_d = "En concepto de <b>".$this->concept."</b>";	
		}
		$this->pdf->setXy (10,55);
		$this->pdf->WriteHTML($_d);
		
		$_d = "El Tesorero/Administrador";
		$this->pdf->setXy (10,65);
		$this->pdf->WriteHTML($_d);
		
		$this->pdf->Image ('img/sello.jpg', 10, 70, 40);
		
		$this->signature($params);
	}
	
	public function signature (&$params) {
		 $d = $params ['day'] . $parmas['month'] . $params['year'] . $parmas['amount'] . $params['user'] . $params['floor'] . $params['pass'] . $this->current_num;
		 $_d = strtoupper(md5($d)); 
		
		 $this->pdf->setXy (140,94);
		 $this->pdf->setFont('Arial','',7);
		 $this->pdf->WriteHTML ('MD5SUM: ' . $_d);
	}
	
	
	public function from_aportation () {
		global $argv;
		
		list ($a,$b,$c) = explode ("-",$this->current_year);
		$this->current_year = $a;
		$this->current_date = "$a-$b-$c";
		$this->amount       = $argv[3];
		$this->current_num  = $argv[4];
		$this->concept      = $argv[5];
		
		if ($this->amount=='') {
			echo "Falta indicar la cantidad\n";
			exit ();
		}
		
		if ($this->current_num=='') {
			echo "Falta indicar el numero de recibo\n";
			exit ();
		}
		
		$year    = $this->current_year;
		$users   = file_get_contents ('users.txt');
		$entries = explode ("\n",$users);
		
		foreach ($entries as $entry) {
				if (trim($entry)=='') continue;
				list ($name, $floor, $amout) = explode (";",$entry);
				$name   = trim ($name);
				$floor  = trim ($floor);
				$amount = trim ($this->amount);
					
				$d = 'pdfs/'.$this->current_year;
				if (!is_dir($d)) mkdir ($d);
				$dy = $d . '/'.strtolower(str_replace(" ","_",$floor));
				
				if (!is_dir($dy)) mkdir ($dy);
				$f = $dy.'/'.$this->current_num . '_' . $this->current_date . '_' . strtolower(str_replace(" ","_",$floor)).'_pending.pdf';
					
				$this->begin_page (array ('year'=>$year, 'month'=>$b, 'day'=>$c, 'amount'=>$amount,
						                  'user'=>$name, 'floor'=>$floor), $f);
		}
		
		
	}
	
	
	public function from_month () {
	
		$year    = $this->current_year;
		$users   = file_get_contents ('users.txt');
		$entries = explode ("\n",$users);
	
		list ($year,$month) = explode ("-",$year);
		$this->current_year = $year;
		
		foreach ($entries as $entry) {
			if (trim($entry)=='') continue;
			list ($name, $floor, $amout) = explode (";",$entry);
			$name   = trim ($name);
			$floor  = trim ($floor);
			$amount = trim ($amout);
					
            $d = 'pdfs/'.$year;
			if (!is_dir($d)) mkdir ($d);
			$dy = $d . '/'.strtolower(str_replace(" ","_",$floor));			
			if (!is_dir($dy)) mkdir ($dy);
			$f = $dy.'/'.$this->current_num . '_' . $year . sprintf("%02d",$month) . '_' . strtolower(str_replace(" ","_",$floor)).'_pending.pdf';
					
			$this->begin_page (array ('year'=>$year,
					'month'=>$month,
					'day'=>'1',
					'amount'=>$amount,
					'user'=>$name,
					'floor'=>$floor),
					$f);
			}
	}
	
	public function from_period () {
	
		$year    = $this->current_year;
		$users   = file_get_contents ('users.txt');
		$entries = explode ("\n",$users);
	
		list ($year,$month)     = explode ("-",$year);
		list ($toyear,$tomonth) = explode ("-",$this->to_period);
		$this->current_year = $year;
	
		for ($cm=$month;$cm<=$tomonth;$cm++) { 
		  foreach ($entries as $entry) {
			if (trim($entry)=='') continue;
			list ($name, $floor, $amout) = explode (";",$entry);
			$name   = trim ($name);
			$floor  = trim ($floor);
			$amount = trim ($amout);
				
			$d = 'pdfs/'.$year;
			if (!is_dir($d)) mkdir ($d);
			$dy = $d . '/'.strtolower(str_replace(" ","_",$floor));
			if (!is_dir($dy)) mkdir ($dy);
			$f = $dy.'/'.$this->current_num . '_' . $year . sprintf("%02d",$cm) . '_' . strtolower(str_replace(" ","_",$floor)).'_pending.pdf';
				
			$this->begin_page (
			 array ('year'=>$year,
					'month'=>$cm,
					'day'=>'1',
					'amount'=>$amount,
					'user'=>$name,
					'floor'=>$floor),
					$f
			 );
		}
	   };
	}
		
	
	public function from_year () {
		
		$year    = $this->current_year;
		$users   = file_get_contents ('users.txt');
		$entries = explode ("\n",$users);
		
		for ($i=1;$i<=12;$i++) {
		  foreach ($entries as $entry) {
			if (trim($entry)=='') continue;
			list ($name, $floor, $amout) = explode (";",$entry);
			$name   = trim ($name);
			$floor  = trim ($floor);
			$amount = trim ($amout);
			
			$d = 'pdfs/'.strtolower(str_replace(" ","_",$floor));
			if (!is_dir($d)) mkdir ($d);
			$dy = $d . '/'.$year;
			if (!is_dir($dy)) mkdir ($dy);
			$f = $dy.'/'.$this->current_num . '_' . $year . sprintf("%02d",$i) . '_' . strtolower(str_replace(" ","_",$floor)).'_pending.pdf';
			// $amount = 35; if ($i<=4) $amount = 50;
				 
			$this->begin_page (array ('year'=>$year,
					'month'=>$i,
					'day'=>'1',
					'amount'=>$amount,
					'user'=>$name,
					'floor'=>$floor),
					$f);
			}
				
		}
	}
	
	
	/**
	 * Controlador
	 */
	public function go () {
		global $argv;
		
		$cmd = $argv[1];
		$this->current_year = $argv[2];
		$this->current_num  = $argv[3];
		
		$this->load_config ();
		
		switch ($cmd) {
			case 'year':       
				$this->from_year ();       
				break;
			case 'month':      
				$this->from_month ();      
				break;
			case 'period':
				$this->to_period   = $argv[3];
				$this->current_num = $argv[4];     
				$this->from_period();      
				break;
			case 'aportation': 
				$this->from_aportation (); 
				break;
			default:           
				$this->help ();            
				break; 
		}
		
	}
	
	/**
	 * Muestra mensaje de ayuda
	 */
	public function help () {
		echo "RECEIPT - Generador de recibos para comunidades de vecinos/propietarios\n\n";
		echo "Formato:\n";
		echo "\tphp receipt.php [command] [options]\n\n";
		echo "Parametros:\n";
		echo "\tyear   [year-number] [from-receipt-number] Genera los recibos de todos los vecinos para el periodo indicado.\n";
		echo "\tmonth  [year-number]-[month number] [from-receipt-number] Genera los recibos de todos los vecinos para el periodo indicado.\n";
		echo "\tperiod [from-year-number]-[from-month number] [to-year-number]-[to-month number] [from-receipt-number] Genera los recibos de todos los vecinos para el periodo indicado.\n";
		echo "\taportation [date] [amount] [from-receipt-number] [concept] Genera un recibo de aportacion extraordinaria para la fecha y con el importe indicado\n\n";
		echo "\t[from-receipt-number] Numero de recibo, contador para todos los recibos que se genere\n";
		echo "\t[amount] Importe en EUROS\n";
		echo "\t[date] Fecha en el formato YYYY-MM-DD";
		echo "\n\n";
		echo "Ejemplo:\n";
		echo "\tphp receipt.php year 2014 01\n";
		echo "\tphp receipt.php month 2014-03 10\n";
		echo "\tphp receipt.php aportation 250 2014-04-05 7 \"Arreglo Escalera\" \n";
	}
	
}