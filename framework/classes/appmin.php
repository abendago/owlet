<?

/**
 * Wrapper class for JSMin javascript minification script
 *
 * based on http://verens.com/archives/2008/05/20/efficient-js-minification-using-php/
 *
 * @author Chris Renner
 */
 
include('JSMin.php');
 
class App_Minifier {
 
    /**
     * Constructor not implemented
     */
    public function __construct() {}
 
    /**
     * Concatenate and minify multiple js files and return filename/path to the merged file
     * @param string $source_dir
     * @param string $cache_dir
     * @param array $scripts
     * @return string
     */
    public static function fetch($source_dir, $cache_dir, $scripts) {
 
        $cache_file = self::get_filename($scripts);
 
        $result = self::compare_files($source_dir, $cache_dir, $scripts, $cache_file);
 
        if(!$result) {
 
            $contents = NULL;
 
            foreach($scripts as $file) {
 
                $contents .= file_get_contents($source_dir . '/' . $file . '.js');
 
            }
 
            // turned off due to performance issues on production 6-9-10
            $code = "";
 
            $minified  = JSMin::minify($contents);
 
            $fp = @fopen($cache_dir . '/' . $cache_file, "w");
            @fwrite($fp, $minified);
            @fclose($fp);
 
	}
 
        return $cache_dir . '/' . $cache_file;
 
    }
 
    /**
     * input array of js file names
     * converts array into string and returns hash of the string
     * as the new filename for the minified js file
     * @param array $scripts
     * @return string
     */
    public static function get_filename($scripts) {
 
        $filename = md5(implode('_', $scripts)) . '.js';
 
        return $filename;
 
    }
 
    /**
     * we're going to compare the modified date of the source files
     * against the hash file if it exists and return true if the hash
     * file is newer and
     * return false if its older or if hash file doesn't exist
     * @param string $source_dir
     * @param string $cache_dir
     * @param array $scripts
     * @param string $cache_file
     * @return boolean
     */
    public static function compare_files($source_dir, $cache_dir, $scripts, $cache_file) {
 
        if(!file_exists($cache_dir . '/' . $cache_file)) {
            return false;
        }
 
        $cache_modified = filemtime($cache_dir . '/' . $cache_file);
 
        foreach($scripts as $source_file) {
 
            $source_modified = filemtime($source_dir . '/' . $source_file . '.js');
 
            if($source_modified &gt; $cache_modified) {
                return false;
            }
 
        }
 
        return true;
 
    }
 
}
?>