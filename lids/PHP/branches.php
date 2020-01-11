<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Branches Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.2
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
     * holds image path and name
     *
     * @var string
     */
    public $image_sha1;

    /**
     * holds hashed image name
     *
     * @var string
     */
    public $sha_name;
  
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
        $this->crops = [];
        $this->image_sha1 = "";
        $this->cat = "dataset";
    }

}

?>