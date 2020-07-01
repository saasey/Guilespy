<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Tier Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.1.0
 *
 */
class Tier extends PNG
{

    /**
     * holds the Branches object
     *
     * @var Branches
     */
    public $head;

    /**
     * holds the Save file IO object
     *
     * @var Save
     */
    public $save;

    public function __construct()
    {
        $this->save = new Save();
        $this->head = [];
    }

    /**
     *   public function retrieve_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Retrieve node in common list
     *
     *  @return void
     */
    public function retrieve_branch_origin(Branches $img)
    {
        foreach ($this->head as $k) {
            if (strtolower($k->origin) == strtolower($img->origin)) {
                return ($k);
            }

        }
        return null;
    }

    /**
     *   public function retrieve_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Retrieve node in common list
     *
     *  @return void
     */
    public function retrieve_branch_sha(string $img)
    {
        foreach ($this->head as $k) {
            if (strtolower($k->sha_name) == strtolower($img)) {
                return ($k);
            }

        }
        return null;
    }

    /**
     *   public function insert_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Adds node in common list
     *
     *  @return void
     */
    public function insert_branch(Branches $img)
    {
        $this->head[] = $img;
        return;

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
        $png = new PNG();
        $node = $png->find_tier($node);

        if ($this->search_imgs($node)) {
            $this->insert_branch($node);
        }

        return $node;
    }

    /**
     *   public function search_imgs
     *   @param Branches &$input   Search through files for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs(Branches &$input)
    {

        if (!file_exists($input->image_sha1) || $input->image_sha1 == "" || $input->image_sha1 == null)
            return 0;
        $bri = (file_get_contents($input->image_sha1));
        echo "<table class='card'><tr><td>Image: $input->origin</td></tr>";
        echo "<tr><td class='top'>";
        echo "<img tag='" . $input->image_sha1 . "' src='" . $input->origin . "' style='height:70px;width:70px'/></td></tr>";
        echo "<tr><td class='bottom'>" . json_encode($input->keywords) . "</td></tr></table>";

        $svf_in = new Branches();

        $this->search_imgs_sub_dir($this, $svf_in, __DIR__ . "/../dataset/", $bri, "", true);
        
        return 1;
    }

    /**
     *   public function label_search
     *   @param string $filename Finds label according to $filename
     *
     *   Runs through common list and gets Label (keywords)
     *
     *   @return string
     */
    public function label_search(Branches &$filename)
    {
        $temp = (array) ($filename->crops);
        if (isset($temp) && count($temp) > 1 && $temp[1] == 100) {
            return 1;
        }
        if (is_array($this->head) && sizeof($this->head) == 0)
            return 0;
        
        foreach ($this->head as $hd) {
            
            if ($hd->origin == $filename->origin) {
                continue;
            }
            $temp_key = null;
            if (!is_array($hd->keywords))
                $temp_key[] = $hd->keywords;
            else
                $temp_key = $hd->keywords;
            $temp_key=implode(', ', $temp_key);
            
            if ($filename->crops[0] == $hd->crops[0]) {
                echo "<table class='card'><tr><td>Keywords: " . $temp_key . "</td></tr>";
                echo "<tr><td class='child'>";
                echo "<img src='" . $hd->origin . "' style='height:70px;width:70px'/>";
                $cat = array_unique($filename->cat);
                echo implode(', ', $cat)  . " ";
                $percent = ($temp[1] == 1) ? 100 : round($temp[1]*1000,4);
                echo $percent . "% Correct</td></tr></table>";
                return 1;
            }
        }
    }

    /**
     *   public function label_search
     *   @param string $filename Finds label according to $filename
     *
     *   Runs through common list and gets Label and relabels it
     *   according to the filename(keywords)
     *
     *   @return string
     */
    public function relabel_img(Branches $filename, array $newkeywords)
    {
        foreach ($this->head as $k) {
            $array_temp = (array) ($k->crops);
            if (in_array($filename->crops, $array_temp)) {
                $k->keywords = $newkeywords;
                return;
            }
        }
    }
}
