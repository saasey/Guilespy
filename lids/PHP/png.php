<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * PNG Class
 *
 * Moves, resizes, recolors, crops, and
 * Shrinks files to just above "ambiguous" state
 *
 * @author David Pulse <inland14@live.com>
 * @api v3.1.5
 */
class PNG
{

    public $epochs = 1;

    /**
     *   public function correct_for_cat
     *   @param Branches &$input Make ->cat syntactically corect
     *
     *   @return bool
     */
    public function correct_for_cat(Branches &$input) {
        
        $output = new Branches();
        $temp = [];
        if (is_string($input->cat) && $input->cat != "") {
            $temp[] = $input->cat;
        } else if (is_string($input->cat) && $input->cat == "") {
            $temp[] = "misc";
        } else {
            $temp[] = "misc";
            //echo "Error: Unknown syntax in \$node->cat. It is neither a array nor a string. Choose one!";
            //exit();
        }
        foreach ($input as $k => $v) {
            if ($k == "cat")
                $output->$k = $temp;
            else
                $output->$k = $v;
        }
        $input = $output;
    }

    /**
     *   public function search_imgs_sub_dir
     *   @param Branches &$input   Search through files in sub-dir for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs_sub_dir(Tier $tier, Branches &$input, string $dir, string $bri, string $sub_folder, bool $opt = false)
    {
        $this->correct_for_cat($input);

        foreach (scandir($dir . $sub_folder) as $sub_file) {
            if ($sub_file == '.' || $sub_file == "..") {
                continue;
            }
            if (is_dir($dir . "/" . $sub_folder . "/" . $sub_file)) {
                $tier->search_imgs_sub_dir($tier, $input, $dir, $bri, $sub_folder . "/" . $sub_file, $opt);
                continue;
            }
            if (!\file_exists($dir . "/" . $sub_folder . "/" . $sub_file))
                continue;
            $input->cat[] = str_replace("/","", $sub_folder);
            $input->image_sha1 = $dir . "/" . $sub_folder . "/" . $sub_file;

            if ($opt == true) {
                $this->img_contrast($tier, $input, $sub_file, $bri);
            }
        }
        return $input;
    }

    public function img_contrast(Tier $tier, Branches $input, string $file, string $bri = "")
    {

        $svf = \file_get_contents($input->image_sha1);
        if ($bri == "")
            $bri = \file_get_contents($file);
        $i = 0;
        $intersect = 0;
        while ($i < strlen($bri) && $i < strlen($svf)) {
            if ($bri[$i] == $svf[$i]) {
                $intersect++;
            }
            $i++;
        }
        if ($i > 0 && ($intersect / $i > 0.070 && $intersect / $i < 0.08) || $intersect / $i == 1) {
            $input->crops = array($file, $intersect / $i);
            $tier->label_search($input);
            $RETURN = 0;
            flush();
            \ob_flush();
            return 1;
        }
        return 0;
    }

    /**
     *   public function find_tier
     *   @param Branches $src Gives file source and other information
     *
     *   Shrinks files to just above "ambiguous" state
     *
     *   @return Array
     */
    public function find_tier(Branches $src)
    {
        $temp = "";
        if (is_array($src->cat) && count($src->cat) > 0) {
            $temp = $src->cat[0];
        } else if (!is_array($src->cat) && $src->cat != "") {
            $temp = $src->cat;
        } else if (is_string($src->cat) && $src->cat == "") {
            $temp = "misc";
        } else {
            echo "Error: Unknown syntax in \$node->cat. It is neither a array nor a string. Choose one!";
            exit();
        }
        $src->cat[0] = $temp;
        $src->sha_name = hash_file('SHA1', $src->origin, false);
        if (!is_dir((__DIR__) . "/../dataset/" . $src->cat[0] . "/") && $src->cat != "dataset") {
            \mkdir((__DIR__) . "/../dataset/" . $src->cat[0] . "/");
        }

        $src->image_sha1 = (__DIR__) . "/../dataset/" . $src->cat[0] . "/" . $src->sha_name;
        $src->crops = array($src->sha_name, 0);

        if (\file_exists($src->image_sha1)) {
            return $src;
        }
        $scale = imagecreatefromstring(file_get_contents($src->origin));

        \imagepng($scale, (__DIR__) . "/../dataset/" . $src->cat[0] . "/" . $src->sha_name);

        #$img = imagecreatefrompng($src->image_sha1);
        $this->epoch($src, (__DIR__) . "/../dataset/" . $src->cat[0] . "/" . $src->sha_name);

        return $src;
    }

    /**
     *   public function epoch
     *   @param string $Handle Filename and path
     *
     *   returns brightness of photos
     *
     */
    public function epoch(&$src, $dest)
    {
        $s = 0;
        $file = $src->origin;
        while ($s < $this->epochs)
        {
            $scale = imagecreatefromstring(file_get_contents($file));
            \imagepng($scale, (__DIR__) . "/../dataset/" . $src->cat[0] . "/" . $src->sha_name);
            $file = $this->get_weighted_state($scale, (__DIR__) . "/../dataset/" . $src->cat[0] . "/" . $src->sha_name);
            $s++;
        }
    }

    /**
     *   public function get_weighted_state
     *   @param string $Handle Filename and path
     *
     *   returns brightness of photos
     *
     */
    public function get_weighted_state(&$Handle, $dest)
    {
        $width = imagesx($Handle);
        $height = imagesy($Handle);

        //After return, send to function comparing
        //weight: how much of formula relies on this
        // X1 \__( ) datapoints
        // X2 /$image = imagecreatetruecolor(400, 300);
        $sku_height = 255;
        $sku_width = 127;
        $img = \imagecreatetruecolor(400, $sku_height); // bias can be Height of radiogram/sku (ex. 64,127,256)
        $bg = imagecolorallocate($img, 255, 255, 255);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb_l1 = imagecolorat($Handle, $x, $y);
                $max = max((int) ($rgb_l1 >> 16) & 0xFF, (int) ($rgb_l1 >> 8) % 256 & 0xFF, (int) ($rgb_l1 % 256) & 0xFF);
                $min = min((int) ($rgb_l1 >> 16) & 0xFF, (int) ($rgb_l1 >> 8) % 256 & 0xFF, (int) ($rgb_l1 % 256) & 0xFF);

                imageline($img, (int) ($x) % $sku_width, 0, (int) ($x) % $sku_width, $max, $rgb_l1);
                imageline($img, (int) ($x + 8), $sku_height - $min, (int) ($x + 8), $min, $x % 512); // Bias! $x + (bias)
            }
        }
        //\imagefilter($img, IMG_FILTER_GRAYSCALE);
        \imagepng($img, $dest);
        return $dest;
    }
}
