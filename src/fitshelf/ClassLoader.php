<?php 
namespace fitshelf;

class ClassLoader {
    
    /** @var array $spaceMap 
     * default is to try to load everyting (including unnamespaced classes) relative to the current directory. */
    protected $spaceMap = array('' => '.');

    /** @param array $spaceMap with namespaces (or PEAR style prefixes or empty string) as keys  
     * and directories to try load from als values. */
    public function setSpaceMap($spaceMap) {
        $this->spaceMap = $spaceMap;
    }
    
    /** PSR-0 class loading function */
    public function tryLoadClass($className) {
        $toReplace = strpos($className, '\\') === false ? '_' : '\\';
        $relativePath = str_replace($toReplace, DIRECTORY_SEPARATOR, $className) . '.php';
        forEach($this->spaceMap as $space => $dir)
        {
            if (subStr($className, 0, strLen($space)) == $space) {
                $filePath = $dir. DIRECTORY_SEPARATOR. $relativePath;
                //print "<br>\n looking for '$filePath'";
                if (file_exists($filePath)) {
                    //print " FOUND";
                    include $filePath;
                    return true;
                }
            } 
        }
        return false;
    }
    
    public function registerAutoLoad($prepend=false) {
        spl_autoload_register(array($this, 'tryLoadClass'), true, $prepend);
    }
}
?>