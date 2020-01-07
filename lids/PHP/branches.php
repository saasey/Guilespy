<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Branches Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.1
 * 
 */
class Branches {
  
    /**
     * holds all keywords
     *
     * @var array
     */
    public $keywords;
  
    /**
     * holds the origin path and filename
     *
     * @var string
     */
    public $origin;

    /**
     * holds the thumbnail image directory
     *
     * @var string
     */
    public $thumb_dir;
  
    /**
     * holds temporary image data
     *
     * @var string
     */
    public $image_sha1;
  
    /**
     * holds the image crops
     *
     * @var string
     */
    public $crops;

    /**
     * holds the image category name
     *
     * @var string
     */
    public $cat;
    
    public function __construct() {
        $this->thumb_dir = dirname(__DIR__) . "/PHP/../dataset";
        $this->crops = [];
        $this->image_sha1 = "";
        $this->cat = "dataset";
    }

}

?>