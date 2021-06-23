<?php
/**
 * SimpleCaptchaGenerator
 *
 * CAPTCHA画像生成ツール
 */
class SimpleCaptchaGenerator
{
    private $fonts = array("Arial");

    private $width = 200;                   //ディフォルトの幅
    private $height = 50;                   //ディフォルトの高さ
    private $bgRange = array(60, 160);      //背景色の範囲
    private $borderRange = array(60,160); //邪魔線の範囲
    private $fgRange = array(100, 255); //文字色の範囲

    private $contrast = 1;//1.24;

    private $sizeRange = array(0.3, 0.6);   //文字の大きさの範囲
    private $rotRange = array(-20, 20); //回転の範囲

    private $maxLine = 10;  //邪魔線の最大本数
    private $maxLineWidth = 5;  //邪魔線の最大幅

    private $blurCount = 1; //ぼかし線の数
    private $blurLevel = array(0.2, 3, 3, 3);   //ぼかしレベル 大きさ、傾き、x, y

    private $wrapWidth = 10;
    private $wrapCount = 1;

    private $noise = false;
    private $lines = true;

    private $debug = false;

    /*
     * Singleton
     */
    private function __construct()
    {
    }

    public static function getInstance()
    {
        static $_inst;
        if (!$_inst) {
            $_inst = new SimpleCaptchaGenerator();
        }

        return $_inst;
    }

    /**
     * 画像を出力して終了
     */
    public function output($text, $width = null, $height = null)
    {
        $img = $this->generate($text, $width, $height);

        if ($this->debug) {
            imagejpeg($img, "test.jpg");
            echo "<img src='test.jpg'>";
            exit;
        }

        header('Content-type: image/jpeg');
        imagejpeg($img);
        imagedestroy($img);

        exit;
    }

    /**
     * 画像生成
     */
    public function generate($text, $width = null, $height = null)
    {
        $self = $this;

        if (null!==$width) {
            $self->width = $width;
        }
        if (null!==$height) {
            $self->height = $height;
        }

        $width = $self->width;
        $height = $self->height;

        $im = imagecreate($width, $height);
        $data = array();

        $maxWidth = $width / strlen($text) - 5;

        /*
         * 文字の大きさとかの準備
         */
        for ($i=0; $i<strlen($text); $i++) {
            $char = substr($text, $i, 1);
            $size = mt_rand($height * $self->sizeRange[0], $height * $self->sizeRange[1]);
            $angle = mt_rand($self->rotRange[0], $self->rotRange[1]);

            $key = array_rand($self->fonts);
            $font = $self->fonts[$key];

            $bbox = imagettfbbox((float)$size, (float)$angle, $font, $char);
            $char_width = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
            $char_height = max($bbox[1], $bbox[3]) - min($bbox[7], $bbox[5]);

            $pos_x = ($i>0) ? $data[$i-1]['pos_x'] + $data[$i-1]['char_width'] : 5;
            $pos_y = ($height + $char_height) / 2;

            $pos_x += mt_rand(0, $width * (1 - $self->sizeRange[1]) / strlen($text) / 2);

            $char_width = max($char_width, $maxWidth);

            $data[$i] = array(
                'char'  => $char,
                'size'  => $size,
                'angle' => $angle,
                'font'  => $font,
                'char_height'   => $char_height,
                'char_width'    => $char_width,
                'pos_x' => $pos_x,
                'pos_y' => $pos_y,
                'color' => 0,
            );

            for ($j=0; $j<$self->blurCount; $j++) {
                $data[$i]['blur'][$j] = array (
                    'size'      => $size*(1+(mt_rand(-$self->blurLevel[0], $self->blurLevel[0])/100)),
                    'angle' => $angle+mt_rand(-$self->blurLevel[1], $self->blurLevel[1]),
                    'pos_x' => $pos_x+mt_rand(-$self->blurLevel[2], $self->blurLevel[2]),
                    'pos_y' => $pos_y+mt_rand(-$self->blurLevel[3], $self->blurLevel[3]),
                );
            }
        }

        //邪魔線の本数
        if (strlen($text) < $self->maxLine) {
            $lineCount = mt_rand(strlen($text)-1, $self->maxLine);
        } else {
            $lineCount = $self->maxLine;
        }

        //色の準備
        $color_bg=imagecolorallocate($im, $self->getBgColor(), $self->getBgColor(), $self->getBgColor());
        $color_border=imagecolorallocate($im, 0, 0, 0);
        for ($i=0; $i<strlen($text); $i++) {
            $data[$i]['color'] = imagecolorallocate($im, $self->getFgColor(), $self->getFgColor(), $self->getFgColor());
        }
        $line_color = array();
        for ($i=0; $i<$lineCount; $i++) {
            $line_color[$i]=imagecolorallocate($im, $self->getBorderColor(), $self->getBorderColor(), $self->getBorderColor());
        }

        /*
         * 邪魔線の記述
         */
        if ($self->lines) {
            $self->drawLines($im, $lineCount, $line_color);
        }

        /*
         * 文字の出力
         */
        $self->drawWords($im, $data);

        /*
         * ノイズの出力
         */
        if ($self->noise) {
            $self->drawNoise($im);
        }

        /*
         * ゆがませる
         */
//      $self->drawWrap($im);

        /*
         * 枠線を記述
         */
        imagerectangle($im, 0, 0, $width-1, $height-1, $color_border);

        return $im;
    }

    public function drawWords($im, $data)
    {
        $l=0;
        foreach ($data as $d) {
            imagettftext($im, $d['size'], $d['angle'], $d['pos_x'], $d['pos_y'], $d['color'], $d['font'], $d['char']);

            for ($j=0; $j<$this->blurCount; $j++) {
                imagettftext($im, $d['blur'][$j]['size'], $d['blur'][$j]['angle'], $d['blur'][$j]['pos_x'], $d['blur'][$j]['pos_y'], $d['color'], $d['font'], $d['char']);
            }
        }
    }

    public function drawLines($im, $lineCount, $line_color)
    {
        $width = $this->width;
        $height = $this->height;

        for ($l=0; $l<$lineCount; $l++) {
            $thick = mt_rand(1, $this->maxLineWidth);
            if ($l % 2 == 1) {  // alternate between top-to-bottom and side-to-side lines
                $line_data[$l] = array( // horizontal
                    0,mt_rand(0, $height),
                    $width,mt_rand(0, $height),
                    $width,0,
                    0,0,
                );
                $line_data[$l][5] = $line_data[$l][3] + $thick;
                $line_data[$l][7] = $line_data[$l][1] + $thick;
            } else {
                $line_data[$l] = array( // vertical
                    mt_rand(0, $width),0,
                    mt_rand(0, $width),$height,
                    0,$height,
                    0,0,
                );
                $line_data[$l][4] = $line_data[$l][2] + $thick;
                $line_data[$l][6] = $line_data[$l][0] + $thick;
            }
            imagefilledpolygon($im, $line_data[$l], 4, $line_color[$l]);
        }
    }

    public function drawWrap($im)
    {
        if ($this->wrapCount < 1) {
            return;
        }

        $cycle = $this->width / ($this->wrapCount * pi() * 2);

        $lamda = create_function('$v', 'return '. $this->wrapWidth.' * sin($v / '. $cycle.');');

        $target = imagecreate($this->width, $this->height);

        for ($i=0; $i<$this->width; $i++) {
            $y = $lamda($i);
            imagecopy($target, $im, $i, $y, $i, 0, 1, $this->height);
        }

//      $cycle = $this->height / ($this->wrapCount * pi() * 2);
//
//      $lamda = create_function('$v','return '. $this->wrapWidth.' * cos($v / '. $cycle.');');
//
//      $target = imagecreate($this->width, $this->height);
//
//      for($i=0; $i<$this->height; $i++){
//          $x = $lamda($i);
//          imagecopy($target, $im, $x, $i, 0, $i, $this->width, 1);
//      }

        imagecopy($im, $target, 0, 0, 0, 0, $this->width, $this->height);
        imagedestroy($target);
    }

    public function getBgColor()
    {
        return $this->contrast * $this->getRandomColor($this->bgRange[0], $this->bgRange[1]);
    }

    public function getBorderColor()
    {
        return $this->contrast * $this->getRandomColor($this->borderRange[0], $this->borderRange[1]);
    }

    public function getFgColor()
    {
        return $this->contrast * $this->getRandomColor($this->fgRange[0], $this->fgRange[1]);
    }

    public function getRandomColor($min, $max)
    {
        $min = max(0, min(255, $min));
        $max = max(0, min(255, $max));
        $out = mt_rand($min, $max);
        //echo $min . "," . $max . " -&gt; " . $out. "<br>";
        return $out;
    }

    public function getFonts()
    {
        return $this->fonts;
    }
    public function setFonts($fonts)
    {
        $this->fonts = $fonts;
    }
    public function getWidth()
    {
        return $this->width;
    }
    public function setWidth($width)
    {
        $this->width = $width;
    }
    public function getHeight()
    {
        return $this->height;
    }
    public function setHeight($height)
    {
        $this->height = $height;
    }
    public function getBgRange()
    {
        return $this->bgRange;
    }
    public function setBgRange($bgRange)
    {
        $this->bgRange = func_get_args();
    }
    public function getFgRange()
    {
        return $this->fgRange;
    }
    public function setFgRange($fgRange)
    {
        $this->fgRange = func_get_args();
    }
    public function getContrast()
    {
        return $this->contrast;
    }
    public function setContrast($contrast)
    {
        $this->contrast = $contrast;
    }
    public function getSizeRange()
    {
        return $this->sizeRange;
    }
    public function setSizeRange($sizeRange)
    {
        $this->sizeRange = $sizeRange;
    }
    public function getRotRange()
    {
        return $this->rotRange;
    }
    public function setRotRange($rotRange)
    {
        $this->rotRange = $rotRange;
    }
    public function getMaxLine()
    {
        return $this->maxLine;
    }
    public function setMaxLine($maxLine)
    {
        $this->maxLine = $maxLine;
    }
    public function getMaxLineWidth()
    {
        return $this->maxLineWidth;
    }
    public function setMaxLineWidth($maxLineWidth)
    {
        $this->maxLineWidth = $maxLineWidth;
    }
    public function getBlurCount()
    {
        return $this->blurCount;
    }
    public function setBlurCount($blurCount)
    {
        $this->blurCount = $blurCount;
    }
    public function getBlurLevel()
    {
        return $this->blurLevel;
    }
    public function setBlurLevel($blurLevel)
    {
        $this->blurLevel = $blurLevel;
    }

    public function getNoise()
    {
        return $this->noise;
    }
    public function setNoise($noise)
    {
        $this->noise = $noise;
    }
    public function getLines()
    {
        return $this->lines;
    }
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    public function getBorderRange()
    {
        return $this->borderRange;
    }
    public function setBorderRange()
    {
        $this->borderRange = func_get_args();
    }
}
