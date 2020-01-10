<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * PNG Class
 *
 * Moves, resizes, recolors, crops, and
 * Shrinks files to just above "ambiguous" state
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.2
 */
class PNG
{

    /**
     *   public function search_imgs_sub_dir
     *   @param Branches &$input   Search through files in sub-dir for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs_sub_dir(Tier $tier, Branches &$input, string $dir, string $bri)
    {
        $RETURN = 0;
        foreach (scandir($dir) as $sub_file) {
            if ($sub_file == '.' || $sub_file == "..") {
                continue;
            }
            if (is_dir($dir . $sub_file)) {
                echo $sub_file;
                $tier->search_imgs_sub_dir($tier, $input, $dir . $sub_file, $bri);
            }
            $input->image_sha1 = $dir . "/" . $sub_file;
            $input->crops[0] = $sub_file;
            $tier->img_contrast($tier, $input, $bri);
        }
        return 0;
    }

    public function img_contrast(Tier $tier, Branches $input, string $bri) {

        $svf = \file_get_contents($input->image_sha1);
        $i = 0;
        $intersect = 0;
        while ($i < strlen($bri) && $i < strlen($svf)) {
            if ($bri[$i] == $svf[$i]) {
                $intersect++;
            }
            $i++;
        }
        if ($i > 0 && $intersect / $i > 0.070) {
            $input->origin = $tier->retrieve_branch_sha($input->crops[0]);
            $input->crops = array($input->crops[0], $intersect / $i);
            $tier->label_search($input);
            $RETURN = 0;
            flush();
            \ob_flush();
        }
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
        
        $src->sha_name = hash_file('SHA1', $src->origin, false);
        if (!is_dir((__DIR__) . "/../dataset/$src->cat/") && $src->cat != "dataset")
            \mkdir((__DIR__) . "/../dataset/$src->cat/");
        $src->image_sha1 = (__DIR__) . "/../dataset/$src->cat/" . $src->sha_name;
        $src->crops = array($src->sha_name, 0);

        if (\file_exists($src->image_sha1)) {
            return $src;
        }
        $scale = imagecreatefromstring(file_get_contents($src->origin));
        
        \imagepng($scale,$src->image_sha1);
        
        #$img = imagecreatefrompng($src->image_sha1);
        $this->get_weighted_state($scale, $src->image_sha1);

        return $src;
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
                $max = max((int)($rgb_l1 >> 16) & 0xFF,(int)($rgb_l1 >> 8)%256 & 0xFF,(int)($rgb_l1%256) & 0xFF);
                $min = min((int)($rgb_l1 >> 16) & 0xFF,(int)($rgb_l1 >> 8)%256 & 0xFF,(int)($rgb_l1%256) & 0xFF);
                
                imageline($img, (int)($x)%$sku_width, 0, (int)($x)%$sku_width, $max, $rgb_l1);
                imageline($img, (int)($x+8), $sku_height - $min, (int)($x+8), $min, $x%512); // Bias! $x + (bias)
            }
        }
        \imagefilter($img,IMG_FILTER_GRAYSCALE);
        \imagepng($img,$dest);
    }
}
