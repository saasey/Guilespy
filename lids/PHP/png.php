<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * PNG Class
 *
 * Moves, resizes, recolors, crops, and
 * Shrinks files to just above "ambiguous" state
 *
 * @author David Pulse <tcp@null.net>
 * @api 3
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
    public function search_imgs_sub_dir(Branches &$input, string $dir)
    {
        $RETURN = 0;
        if (file_exists(__DIR__ . "/../dataset/$dir/" . $input->crops[0])) {
            $input->thumb_dir .= "/$dir";
            $input->crops[2] = $dir;
            $dir2 = \explode('\\',$input->thumb_dir);
            $dir2 = \explode('/',$dir2[count($dir2)-1]);
            $input->cat = json_encode($dir2[4]);
            return 1;
        }
        foreach (scandir(__DIR__ . "/../dataset/$dir/") as $sub_file) {
            if ($sub_file[0] == '.') {
                continue;
            }
            if (is_dir(__DIR__ . "/../dataset/$dir/" . $sub_file)) {
                $RETURN = $this->search_imgs_sub_dir($input, $dir . $sub_file);
                if ($RETURN == 1)
                    return 1;
                continue;
            }

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
        
        $file_sha = hash_file('SHA1', $src->origin, false) . "yv";
        $src->image_sha1 = (__DIR__) . "/../dataset/" . $file_sha;
        $src->crops = array($file_sha, 0);
        if ($this->search_imgs_sub_dir($src, "") == 1) {
            $src->image_sha1 = $src->thumb_dir . "/" . $file_sha;
            return $src;
        }
        $scale = imagecreatefromstring(file_get_contents($src->origin));
        //$scale = imagescale($scale, (int) (300));
        \imagepng($scale,$src->image_sha1);
        
        #$img = imagecreatefrompng($src->image_sha1);
        $this->get_weighted_state($scale, $src->image_sha1);

        #\imagepng($img,$src->image_sha1);
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
                $layer = [($rgb_l1 >> 16) & 0xFF, ($rgb_l1 >> 8)%256 & 0xFF, $rgb_l1%256 & 0xFF];
                $max = max((int)$layer[0],(int)$layer[1],(int)$layer[2]);
                $min = min((int)$layer[0],(int)$layer[1],(int)$layer[2]);
                
                imageline($img, (int)($x)%$sku_width, 0, (int)($x)%$sku_width, $max, $rgb_l1);
                imageline($img, (int)($x+8), $sku_height - $min, (int)($x+8), $min, $x%512); // Bias! $x + (bias)
            }
        }
        \imagefilter($img,IMG_FILTER_GRAYSCALE);
        \imagepng($img,$dest);
    }
}
