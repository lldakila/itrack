<?php

function folder_exist($folder) {

    // Get canonicalized absolute pathname
    $path = realpath($folder);

    // If it exist, check if it's a directory
    if($path !== false AND is_dir($path))
    {
        // Return canonicalized absolute pathname
        return $path;
    }

    // Path/folder does not exist
    return false;
	
}

# delete files
function deleteFolderFiles($id) {
	
	$path = "../../pictures/$id";
	
	if (!file_exists($path)) return false;
	
	$files = glob("$path/*");

	foreach ($files as $file) {
		
		if (is_file($file)) unlink($file);
		
	}

	rmdir($path);
	
	return true;

};

?>