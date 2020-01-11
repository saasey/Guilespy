<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Branches Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.4
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
        $this->thumb_dir = dirname(__DIR__) . "/PHP/../dataset";
        $this->crops = [];
        $this->image_sha1 = "";
        $this->cat = "";
    }

    /**
     *   public function add_branch_img
     *   @param Branches &$node Branch to check for existence and add
     *
     *   Inserts new images at end of common list
     *
     *  @return bool
     */
    public function add_branch_img(Branches &$node)
    {
        if (!is_array($node->keywords)) {
            echo "Keywords must be an array. <br/>Please modify $node->origin's Keywords.";
            return $node;
        }
        $png = new PNG();
        $node = $png->find_tier($node);

        if ($this->search_imgs($node)) {
            $this->insert_branch($node);
        }

        return $node;
    }


}

?>