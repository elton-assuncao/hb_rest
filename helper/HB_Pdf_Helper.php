<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe de Helper para Geração de PDF
 */
//require_once(DIR_PATH.DIRECTORY_SEPARATOR."libs/mpdf_lib/mpdf.php");
 
class HB_Pdf_Helper {	
	
	public static function GerarPdf($html,$titulo=null,$paisagem=false,$nomePdf=null)
	{
	    $mpdf = new mPDF();
	 	    
	    $mpdf->allow_charset_conversion=true;
	    $mpdf->charset_in='utf-8';
	    
	    if($paisagem==true){  
	    	$mpdf->CurOrientation='l';
	    }
        
	    //Exibir a pagina inteira no browser
	    //$mpdf->SetDisplayMode('fullpage');	  
	    	   
	    //Cabeçalho: Seta a data/hora completa de quando o PDF foi gerado + um texto no lado direito
	    if($titulo!=null){
	       	$mpdf->SetHeader("{$titulo}");	       
	    }
	 
	    //Rodapé: Seta a data/hora completa de quando o PDF foi gerado + um texto no lado direito
	    $mpdf->SetFooter("{DATE j/m/Y H:i}|{PAGENO}/{nb}| {$titulo}");
	 
	    $mpdf->WriteHTML($html);
	 
	    // define um nome para o arquivo PDF
	    if($nomePdf == null){
	        $nomePdf = time().'.pdf';
	    }	 
	    $mpdf->Output($nomePdf, 'I');
	}
}
 
/* End of file mpdf_pdf_pi.php */
/* Location: ./system/plugins/mpdf_pi.php */