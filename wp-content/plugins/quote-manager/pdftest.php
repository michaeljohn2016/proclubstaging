<?php
/*
 *
 * beginning of embedded custom page template
 * This creates the Mobile Shipping Page
 *
*/
	get_header();
	
	ob_start();
	
	// Call the FPDF Library
	require("fpdf/fpdf.php");
	
	// Create new PDF object
	$pdff = new FPDF( );
	
	// Create a new page
	$pdff->AddPage();
	
	// Define the "style" of the page
	$pdff->SetFont('Arial','B',16);
	
	// Create the content of the PDF
	$pdff->Cell(0,10,'PHP - The Good Parts!');
	
	$pdff->Output();
	ob_end_flush();
	
?>

	<div id="shipping-label-info">
		<button id="pdf-test-button">Create PDF</button>
	</div>

	
<?php
			
	get_footer();
	exit;
?>