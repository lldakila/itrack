<?php

function get_files($dir,$barcode) {

	$files = [];

	$dir_files = scandir($dir);

	foreach ($dir_files as $dir_file) {
		
		if ($dir_file == ".") continue;
		if ($dir_file == "..") continue;
		
		if (preg_match("/$barcode/",$dir_file)) {

			// Read image path, convert to base64 encoding
			$dir_file_data = base64_encode(file_get_contents($dir.$dir_file));

			// Format the image SRC:  data:{mime};base64,{data};
			$type = mime_content_type($dir.$dir_file);

			$file = 'data:'.$type.';base64,'.$dir_file_data;
			
			$file_type = explode("/",$type);

			$files[] = array(
				"file"=>$file,
				"type"=>$file_type[1],
				"name"=>$dir_file
			);

		};
		
	};

	return $files;

};

?>