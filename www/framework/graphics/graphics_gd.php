<?php
/**
 * @file    graphics_gd.php
 * @brief   PHP GD extension interface
 *
 * @author  Frank Hellenkamp <jonas@depage.net>
 * @author  Sebastian Reinhold <sebastian@bitbernd.de>
 **/

namespace depage\graphics;

/**
 * @brief PHP GD extension interface
 *
 * The graphics_gd class provides depage::graphics features using the PHP GD
 * extension.
 **/
class graphics_gd extends graphics {
    // {{{ crop()
    /**
     * @brief   Crop action
     *
     * Applies crop action to $this->image.
     *
     * @param   $width  (int) output width
     * @param   $height (int) output height
     * @param   $x      (int) crop x-offset
     * @param   $y      (int) crop y-offset
     * @return  void
     **/
    protected function crop($width, $height, $x = 0, $y = 0) {
        if (!$this->bypassTest($width, $height, $x, $y)) {
            $newImage = $this->createCanvas($width, $height);

            imagecopy(
                $newImage,
                $this->image,
                ($x > 0) ? 0 : abs($x),
                ($y > 0) ? 0 : abs($y),
                ($x < 0) ? 0 : $x,
                ($y < 0) ? 0 : $y,
                $this->size[0] - abs($x),
                $this->size[1] - abs($y)
            );

            $this->image = $newImage;
            $this->size = array($width, $height);
        }
    }
    // }}}
    // {{{ resize()
    /**
     * @brief   Resize action
     *
     * Applies resize action to $this->image.
     *
     * @param   $width  (int) output width
     * @param   $height (int) output height
     * @return  void
     **/
    protected function resize($width, $height) {
        $newSize = $this->dimensions($width, $height);

        if (!$this->bypassTest($newSize[0], $newSize[1])) {
            $newImage = $this->createCanvas($newSize[0], $newSize[1]);
            imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize[0], $newSize[1], $this->size[0], $this->size[1]);

            $this->image = $newImage;
            $this->size = $newSize;
        }
    }
    // }}}
    // {{{ thumb()
    /**
     * @brief   Thumb action
     *
     * Applies thumb action to $this->image.
     *
     * @param   $width  (int) output width
     * @param   $height (int) output height
     * @return  void
     **/
    protected function thumb($width, $height) {
        if (!$this->bypassTest($width, $height)) {
            $newSize = $this->dimensions($width, null);

            if ($newSize[1] > $height) {
                $newSize = $this->dimensions(null, $height);
                $xOffset = round(($width - $newSize[0]) / 2);
                $yOffset = 0;
            } else {
                $xOffset = 0;
                $yOffset = round(($height - $newSize[1]) / 2);
            }

            $newImage = $this->createCanvas($width, $height);

            imagecopyresampled($newImage, $this->image, $xOffset, $yOffset, 0, 0, $newSize[0], $newSize[1], $this->size[0], $this->size[1]);

            $this->image = $newImage;
            $this->size = array($width, $height);
        }
    }
    // }}}

    // {{{ load()
    /**
     * @brief   Loads image from file
     *
     * Determines image format and loads it to $this->image.
     *
     * @return  void
     **/
    protected function load() {
        if ($this->inputFormat == 'gif' && function_exists('imagecreatefromgif')) {
            //GIF
            $this->image = imagecreatefromgif($this->input);
        } else if ($this->inputFormat == 'jpg') {
            //JPEG
            $this->image = imagecreatefromjpeg($this->input);
        } else if ($this->inputFormat == 'png') {
            //PNG
            $this->image = imagecreatefrompng($this->input);
        } else {
            throw new graphics_exception('Unknown image format.');
        }
    }
    // }}}
    // {{{ save()
    /**
     * @brief   Saves image to file.
     *
     * Adds background and saves $this->image to file.
     *
     * @return  void
     **/
    protected function save() {
        $bg = $this->createBackground($this->size[0], $this->size[1]);
        imagecopy($bg, $this->image, 0, 0, 0, 0, $this->size[0], $this->size[1]);
        $this->image = $bg;

        if ($this->outputFormat == 'gif' && function_exists('imagegif')) {
            imagegif($this->image, $this->output);
        } else if ($this->outputFormat == 'jpg') {
            imagejpeg($this->image, $this->output, $this->getQuality());
        } else if ($this->outputFormat == 'png') {
            $quality = $this->getQuality();
            imagepng($this->image, $this->output, $quality[0], $quality[1]);
        }
    }
    // }}}

    // {{{ getImageSize()
    /**
     * @brief   Determine size of input image
     *
     * @return  void
     **/
    protected function getImageSize() {
        return getimagesize($this->input);
    }
    // }}}

    // {{{ render()
    /**
     * @brief   Main method for image handling.
     *
     * Starts actions, saves image, calls bypass if necessary.
     *
     * @param   $input  (string) input filename
     * @param   $output (string) output filename
     * @return  void
     **/
    public function render($input, $output = null) {
        parent::render($input, $output);

        $this->load();
        $this->processQueue();

        if (
            $this->bypass
            && $this->inputFormat == $this->outputFormat
        ) {
            $this->bypass();
        } else {
            $this->save();
        }
    }
    // }}}

    // {{{ createCanvas()
    /**
     * @brief   Creates transparent canvas with given dimensions
     *
     * @param   $width  (int)       canvas width
     * @param   $height (int)       canvas height
     * @return  $canvas (object)    image resource identifier
     **/
    private function createCanvas($width, $height) {
        $canvas = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $bg);

        return $canvas;
    }
    // }}}
    // {{{ createBackground()
    /**
     * @brief   Creates background with given dimensions
     *
     * Creates image background specified in $this->background
     *
     * @param   $width      (int)       canvas width
     * @param   $height     (int)       canvas height
     * @return  $newImage   (object)    image resource identifier
     **/
    private function createBackground($width, $height) {
        $newImage = imagecreatetruecolor($width, $height);

        if ($this->background[0] == '#') {
            /**
            * uses example from http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
            **/
            $color = substr($this->background, 1);

            if (strlen($color) == 6) {
                list($r, $g, $b) = array(
                    $color[0].$color[1],
                    $color[2].$color[3],
                    $color[4].$color[5]
                );
            } elseif (strlen($color) == 3) {
                list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
            }

            $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

            imagefill($newImage, 0, 0, imagecolorallocate($newImage, $r, $g, $b));
        } else if ($this->background == 'checkerboard') {
            $transLen = 15;
            $transColor = array();
            $transColor[0] = imagecolorallocate ($newImage, 153, 153, 153);
            $transColor[1] = imagecolorallocate ($newImage, 102, 102, 102);
            for ($i = 0; $i * $transLen < $width; $i++) {
                for ($j = 0; $j * $transLen < $height; $j++) {
                    imagefilledrectangle(
                        $newImage,
                        $i * $transLen,
                        $j * $transLen,
                        ($i + 1) * $transLen,
                        ($j + 1) * $transLen,
                        $transColor[$j % 2 == 0 ? $i % 2 : ($i % 2 == 0 ? 1 : 0)]
                    );
                }
            }
        } else if ($this->background == 'transparent') {
            imagefill($newImage, 0, 0, imagecolorallocatealpha($newImage, 255, 255, 255, 127));
            if ($this->outputFormat == 'gif') imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 255, 255, 255, 127));
            imagesavealpha($newImage, true);
        }

        return $newImage;
    }
    // }}}
}
