<?php

function uploadFiles($con,$uploads,$barcode,$id,$path=null) {
	
	$ft = array(
		"jpeg"=>".jpg",
		"pdf"=>".pdf",
		"png"=>".png"
	);

	$con->table = "files";	

	# clear files table for document if there are any
	$con->deleteData(array("document_id"=>$id));
	#

	if (count($uploads["files"])) {
	
		$dir = "../../files";
		if ($path != null) $dir = $path;
		
		if (!folder_exist($dir)) mkdir($dir);		

		foreach ($uploads["files"] as $key => $f) {

			if ($f['file'] == "") continue;

			$imgData = str_replace(' ','+',$f['file']);
			$imgData =  substr($imgData,strpos($imgData,",")+1);
			$imgData = base64_decode($imgData);
			$fileName = "$barcode"."_$key".$ft[$f['type']];
			$filePath = "$dir/$fileName";
			$file = fopen($filePath, 'w');
			fwrite($file, $imgData);
			fclose($file);

			$data = array("document_id"=>$id,"file_name"=>$fileName);
			$con->insertData($data);

		};

	};

};

function deleteFiles($con,$files,$path=null) {
	
	$dir = "../../files";
	if ($path != null) $dir = $path;
	
	foreach ($files as $file) {
		
		if (file_exists($dir."/".$file)) unlink($dir."/".$file);	
		
	};
	
};

function barcode($con,$origin,$office,$com) {
	
	$barcode = "";
	
	$incr = 1;
	
	$sql = "SELECT documents.id, documents.barcode FROM documents WHERE documents.origin = $origin ORDER BY documents.id DESC LIMIT 1";

	$last_barcode = $con->getData($sql);	
	
	if (count($last_barcode)) {

		$last_no = explode("-",$last_barcode[0]['barcode']);
		
		$incr = (isset($last_no[3]))?(int)$last_no[3]:0;
		
		$incr+=1;
		
	};
	
	$barcode = substr($office,0,3)."-".date("m")."-".date("Y")."-".str_pad($incr, 5, '0', STR_PAD_LEFT);
	
	return $barcode;
	
};

?>