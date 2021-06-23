<?php
require_once __DIR__.'/CssMin.php';
require_once __DIR__.'/JShrink/Minifier.php';

class xMin
{
    public static function minifyCss($cnt)
    {
        return CssMin::minify($cnt);
    }

    public static function formatCss($cnt)
    {
        $fr = new CssOtbsFormatter(CssMin::parse($cnt), "  ");
        return (string)$fr;
    }

    public static function minifyJs($cnt)
    {
        try {
            $cnt = \JShrink\Minifier::minify($cnt);
        } catch (Exception $e) {
            ;
        }
        return $cnt;
    }

    public static function formatJs($cnt)
    {
        return ''; // TODO
    }

    private static function simpleMinifyHtml($cnt)
    {
        $expts = array(
        '<pre'=>array('/(<pre *>|<pre [^>]+>)(.*?)(<\/pre *>|<\/pre [^>]+>)/is', null),
        '<style'=>array('/(<style *>|<style [^>]+>)(.*?)(<\/style *>|<\/style [^>]+>)/is',
        function ($c) {
            return self::minifyCss(preg_replace('/^\s*<!--/m', '', $c));
        }),
        '<script'=>array('/(<script *>|<script [^>]+>)(.*?)(<\/script *>|<\/script [^>]+>)/is',
        function ($c) {
            return self::minifyJs(preg_replace('/^\s*<!--/m', '', $c));
        }),
        '<![CDATA['=>array('/(<!\[CDATA\[)(.*?)(\]\]>)/is', null),
        );
        $ptns  = array();
        foreach ($expts as $k => $v) {
            if (false !== strpos($cnt, $k)) {
                $ptns[$k] = $v[0];
                $encp = function ($m) use ($v) {
                    return $m[1].base64_encode($v[1]?$v[1]($m[2]):$m[2]).$m[3];
                };
                $cnt = preg_replace_callback($ptns[$k], $encp, $cnt);
            }
        }
        $cnt = preg_replace(
            array('/^\s+</m', '/\r/', '/\n/', '/\s\s+/'),
            array('<'       , ''    , ''    , ' '      ),
            $cnt
        );
        if (!empty($ptns)) {
            $cnt = preg_replace_callback($ptns, function ($m) {
                return $m[1].base64_decode($m[2]).$m[3];
            }, $cnt);
        }
        return $cnt;
    }

    public static function minifyHtml($cnt)
    {
        return self::simpleMinifyHtml($cnt);
    }

    public static function formatHtml($cnt)
    {
        return '';  // TODO
    }

    private static function genX(callable $func, $dst, array $srcs, $force, $mode)
    {
        if (empty($func) || !is_string($dst) || empty($dst) || empty($srcs)) {
            return;
        }
        if (!file_exists($dst) || $force) {
            $c = '';
            foreach ($srcs as $s) {
                if (!is_string($s) || empty($s) || !is_file($s)) {
                    continue;
                }
                $c = $c . call_user_func($func, file_get_contents($s)) . "\n";
            }
            file_put_contents($dst, $c, LOCK_EX);
            if (is_int($mode)) {
                chmod($dst, $mode);
            }
        }
    }

    public static function genCss($dst, array $srcs, $force = false, $mode = null)
    {
        self::genX('self::minifyCss', $dst, $srcs, $force, $mode);
    }

    public static function genJs($dst, array $srcs, $force = false, $mode = null)
    {
        self::genX('self::minifyJs', $dst, $srcs, $force, $mode);
    }
}
