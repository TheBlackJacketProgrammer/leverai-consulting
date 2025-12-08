<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File Upload Helper
 * 
 * Provides functions for handling file uploads in the application
 */

/**
 * Upload a file and return the file path
 * 
 * @param string $file The file input name from $_FILES
 * @param string $person_type The type of person (e.g., 'complainant', 'complainee')
 * @param string $name The person's name
 * @param string $current_pic The previous file path to be deleted
 * @return string The file path where the file was uploaded or default image path
 */
function upload_file($file, $person_type, $name, $current_pic)
{
    // Check if a new file was uploaded
    if (!isset($_FILES[$file]) || empty($_FILES[$file]['name'])) {
        if($current_pic == "null" || $current_pic == ""){
            return "assets/img/no-image.png";
        }
        else{
            return $current_pic;
        }
    }
    else {
        // Get the original file name and extension
        $original_filename = $_FILES[$file]['name'];
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        
        // Create the new filename with proper extension
        $person_pic = $person_type . "_" . $name;
        if (!empty($file_extension)) {
            $person_pic .= "." . $file_extension;
        }
        
        $location = "./assets/img/people/" . $person_pic;
        
        // Ensure the upload directory exists
        $upload_dir = "./assets/img/people/";
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                log_message('error', 'Failed to create upload directory: ' . $upload_dir);
                return "assets/img/no-image.png";
            }
        }
    }
    
    if($current_pic != $location) {
        if ($current_pic && $current_pic !== "assets/img/no-image.png" && $current_pic !== "null") {
            unlink($current_pic);
        }
        // Upload new file
        move_uploaded_file($_FILES[$file]['tmp_name'], $location);
        return $location;
    }
    else{
        return $current_pic;
    }

}
