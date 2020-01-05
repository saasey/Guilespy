<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * PNG Class
 *
 * Moves, resizes, recolors, crops, and
 * Shrinks files to just above "ambiguous" state
 *
 * @author David Pulse <tcp@null.net>
 * @api 2.4.2
 */
class PNG extends Tier
{

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
        $file_sha = hash_file('SHA1', $src->origin, false) . "y";
        $src->image_sha1 = (__DIR__) . "/../dataset/" . $file_sha . "v";
        $src->crops = array($file_sha, 0);
        //touch((__DIR__) . "/../dataset/" . $file);
        if (file_exists((__DIR__) . "/../dataset/" . $file_sha . "v"))
            return $src;
        list($width, $height, $type, $attr) = getimagesize($src->origin);

        $scale = imagecreatefromstring(file_get_contents($src->origin));
        //$scale = imagescale($scale, (int) (300));
        \imagepng($scale,$src->image_sha1);
        
        $img = imagecreatefrompng($src->image_sha1);
        $this->get_weighted_state($scale, $img, $src->image_sha1);

        \imagepng($img,$src->image_sha1);
        return $src;
    }

    /**
     *   public function get_weighted_state
     *   @param string $Handle Filename and path
     *
     *   returns brightness of photos
     *
     */
    public function get_weighted_state(&$Handle, &$img, $dest)
    {
        $width = imagesx($Handle);
        $height = imagesy($Handle);
        
        //After return, send to function comparing
        //weight: how much of formula relies on this
        // X1 \__( ) datapoints
        // X2 /$image = imagecreatetruecolor(400, 300);
        $sku_height = 512;
        
        $img = \imagecreatetruecolor(512, $sku_height); // bias can be Height of radiogram/sku (ex. 64,127,512)
        $bg = imagecolorallocate($img, 255, 255, 255);
        
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb_l1 = imagecolorat($Handle, $x, $y);
                $layer = [($rgb_l1 >> 16) & 0xFF, ($rgb_l1 >> 8) & 0xFF, $rgb_l1 & 0xFF];
                $max = max((int)$layer[0],(int)$layer[1],(int)$layer[2]);
                $min = min((int)$layer[0],(int)$layer[1],(int)$layer[2]);
                
                imageline($img, (int)($x)%512, 0, (int)($x)%512, $max, $x%512);
                imageline($img, (int)($y+2)%512, $sku_height, (int)($y+2)%512, $min, $x%512); // Bias! $x + (bias)
            }
        }
        //\imagefilter($img,IMG_FILTER_GRAYSCALE);
        imagepng($img,$dest);
    }
}
