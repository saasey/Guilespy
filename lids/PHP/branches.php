<?php

namespace lids\PHP;

/**
 * Branches Class
 *
 * @author David Pulse <tcp@null.net>
 * @api 2.5
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
    protected $thumb_dir;
  
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
    
    public function __construct() {
        $this->thumb_dir = dirname(__DIR__) . "/dataset/";
        $this->crops = [];
        $this->image_sha1 = "";
    }

}

?>