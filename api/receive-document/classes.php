<?php

function uploadFiles($con,$uploads,$barcode,$id,$path,$init) {
	
	$user_id = $_SESSION['itrack_user_id'];
	
	$ft = array(
		"jpeg"=>".jpg",
		"pdf"=>".pdf",
		"png"=>".png"
	);

	$con->table = "files";

	if (count($uploads["files"])) {
	
		$dir = "../../files";
		if ($path != null) $dir = $path;
		
		if (!folder_exist($dir)) mkdir($dir);		

		$incr = 0;
		$files = $con->getData("SELECT * FROM files WHERE document_id = $id AND user_id = $user_id ORDER BY id DESC");
		if (count($files)) {
			$file_and_ext = explode(".",$files[0]['file_name']);
			$name_index = explode("_",$file_and_ext[0]);
			$incr = intval($name_index[1]);
		};

		foreach ($uploads["files"] as $key => $f) {

			if ($f['file'] == "") continue;
			if (isset($f['initial_file'])) if ($f['initial_file']>0) continue;

			$imgData = str_replace(' ','+',$f['file']);
			$imgData =  substr($imgData,strpos($imgData,",")+1);
			$imgData = base64_decode($imgData);
			
			$incr++;

			$fileName = "$barcode"."_$incr".$ft[$f['type']];
			$filePath = "$dir/$fileName";
			$file = fopen($filePath, 'w');
			fwrite($file, $imgData);
			fclose($file);
			
			$data = array("document_id"=>$id,"file_name"=>$fileName,"user_id"=>$user_id);
			if ($init) $data['initial_file'] = 1;
			$con->insertData($data);

		};

	};

};

function deleteFiles($con,$files,$path=null) {
	
	$table = $con->table;
	$con->table = "files";
	
	$dir = "../../files";
	if ($path != null) $dir = $path;
	
	foreach ($files as $file) {
		
		if (file_exists($dir."/".$file['file_name'])) unlink($dir."/".$file['file_name']);
		$delete = $con->deleteData(array("id"=>$file['id']));
		
	};
	
	$con->table = $table;
	
};

function barcode($con,$params) {
	
	$barcode = "";
	
	$series = 1;
	
	$sql = "SELECT documents.id, documents.barcode, documents.doctype_series FROM documents WHERE documents.origin = ".$params['origin']." AND documents.doc_type = ".$params['doctype']." ORDER BY documents.doctype_series DESC LIMIT 1";

	$last_barcode = $con->getData($sql);	
	
	if (count($last_barcode)) {
		
		$series = $last_barcode[0]['doctype_series'];
		
		$series+=1;
		
	};
	
	// $barcode = substr($params['office'],0,3)."-".date("m")."-".date("Y")."-".str_pad($series, 5, '0', STR_PAD_LEFT);
	$barcode = $params['office']."-".$params['doctype_shortname']."-".date("Y")."-".str_pad($series, 5, '0', STR_PAD_LEFT);
	
	$response = array(
		"barcode"=>$barcode,
		"series"=>$series
	);
	
	return $response;
	
};

?>