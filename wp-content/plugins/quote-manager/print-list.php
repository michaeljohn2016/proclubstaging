<?php
	require('../../../wp-load.php');
	require('fpdf/fpdf.php');
	global $wpdb;
	
	$query_for_print_list = $wpdb->get_results(
		"
			SELECT DISTINCT c0.meta_value AS manufacturer, c1.meta_value AS club_type, c2.meta_value AS model, IF( c3.meta_value = 'Steel', c5.meta_value, '-' ) AS steel, IF( c4.meta_value = 'Graphite', c5.meta_value, '-' ) AS graphite
			FROM wp_postmeta AS c0
			JOIN wp_postmeta AS c1 ON c1.post_id = c0.post_id
			JOIN wp_postmeta AS c2 ON c2.post_id = c0.post_id
			JOIN wp_postmeta AS c3 ON c3.post_id = c0.post_id
			JOIN wp_postmeta AS c4 ON c4.post_id = c0.post_id
			JOIN wp_postmeta AS c5 ON c5.post_id = c0.post_id
			WHERE c0.meta_key = 'club_manufacturer'
			AND c1.meta_key = 'club_type_field'
			AND c2.meta_key = 'club_model'
			AND c3.meta_key = 'club_shaft_type'
			AND c4.meta_key = 'club_shaft_type'
			AND c5.meta_key = 'club_price'
			AND c2.meta_value != ''
			ORDER BY manufacturer, club_type, model DESC
		"
	);
	$previous_manufacturer = "";
	$previous_model = "";
	$previous_club_type = "";
	$counter = 0;
	class PDF extends FPDF{
		function Header(){
			$this->Image('images/branding-holder2.png',15,5,180);
			$this->Cell(0,30,"",0,300);
		}

		function Footer()
			{
			    // Go to 1.5 cm from bottom
			    $this->SetY(-15);
			    // Select Arial italic 8
			    $this->SetFont('Arial','I',8);
			    // Print centered page number
			    $goodthrough = get_option('qm-good-through');
			    $this->Cell(0,10,'Stated values good through '. $goodthrough.". All prices subject to change.",0,0,'C');
			}
		
		function SetCol($col){
			// Set position at a given column
			$this->col = $col;
			$x = 10+$col*100;
			$this->SetLeftMargin($x);
			$this->SetX($x);
		}
		
		function AcceptPageBreak(){
			// Method accepting or not automatic page break
			if($this->col<1)
			{
				// Go to next column
				$this->SetCol($this->col+1);
				// Set ordinate to top
				//$this->SetY($this->y0);
				$this->SetY(40);
				// Keep on page
				return false;
			}
			else
			{
				// Go back to first column
				$this->SetCol(0);
				// Page break
				return true;
			}
		}

	}
	$pdf = new PDF();
	$pdf->SetFont('Times','',10);
	$pdf->AddPage();
	
	$previous_manufacturer = "";
	$previous_club_type ="";
	$previous_model = "";
	
	/*
	foreach($query_for_print_list as $row){
		if($row->manufacturer == $previous_manufacturer){
			if($row->club_type == $previous_club_type){
				foreach($row as $col){
					//$pdf->Cell(0,5,$row->model." ".$row->steel." ".$row->graphite,0,500);
					$pdf->Cell(25,5,$col,0,0);
				}
				//$this->Ln();
			} else {
				$pdf->Cell(0,5,$row->manufacturer." ".$row->club_type,0,500);
				$pdf->Cell(0,5,$row->model." ".$row->steel." ".$row->graphite,0,500);
			}
		} else {
			$pdf->Cell(0,5,$row->manufacturer,0,500);
			$pdf->Cell(0,5,$row->manufacturer." ".$row->club_type,0,500);
			$pdf->Cell(0,5,$row->model." ".$row->steel." ".$row->graphite,0,500);
		}
		
		$previous_manufacturer = $row->manufacturer;
		$previous_model = $row->model;
		$previous_club_type = $row->club_type;
	}
	*/
	
	foreach($query_for_print_list as $row){
		if($row->manufacturer == $previous_manufacturer){
			if($row->club_type == $previous_club_type){
				$pdf->Cell(60,5,$row->model,0,0);
				$pdf->Cell(20,5,$row->steel,0,0);
				$pdf->Cell(20,5,$row->graphite,0,0);
				$pdf->Ln();
			} else {
				$pdf->SetFillColor(197, 197, 197);
				$pdf->Cell(20,5,$row->manufacturer,0,0,'c',true);
				$pdf->Cell(37,5,$row->club_type,0,0,'c',true);
				$pdf->Cell(19,5,"Steel",0,0,'c',true);
				$pdf->Cell(20,5,"Graphite",0,0,'c',true);
				$pdf->Ln();
				$pdf->SetFillColor(255,255,255);
				$pdf->Cell(60,5,$row->model,0,0);
				$pdf->Cell(20,5,$row->steel,0,0);
				$pdf->Cell(20,5,$row->graphite,0,0);
				$pdf->Ln();
			}
		} else {
			$pdf->SetFillColor(12, 151, 23);
			$pdf->Cell(96,5,$row->manufacturer,0,0,'c',true);
			$pdf->Ln();
			$pdf->SetFillColor(197, 197, 197);
			$pdf->Cell(20,5,$row->manufacturer,0,0,'c',true);
			$pdf->Cell(37,5,$row->club_type,0,0,'c',true);
			$pdf->Cell(19,5,"Steel",0,0,'c',true);
			$pdf->Cell(20,5,"Graphite",0,0,'c',true);
			$pdf->Ln();
			$pdf->SetFillColor(255,255,255);
			$pdf->Cell(60,5,$row->model,0,0);
			$pdf->Cell(20,5,$row->steel,0,0);
			$pdf->Cell(20,5,$row->graphite,0,0);
			$pdf->Ln();
		}
		
		$previous_manufacturer = $row->manufacturer;
		$previous_model = $row->model;
		$previous_club_type = $row->club_type;
	}
	$pdf->Output();
	
?>
