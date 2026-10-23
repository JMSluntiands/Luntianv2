<?php

namespace App\Support;

use GdImage;

/**
 * Crops empty space (transparent or near-white) around a logo so it fills its display box.
 */
class LogoTrimmer
{
    private const MAX_SIDE = 1600;

    private const PADDING_RATIO = 0.03;

    /**
     * Trims the image at $absolutePath and rewrites it as PNG. Returns false when GD can't read it.
     */
    public static function trimToPng(string $absolutePath): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $data = @file_get_contents($absolutePath);
        $src = $data !== false ? @imagecreatefromstring($data) : false;
        if (! $src instanceof GdImage) {
            return false;
        }

        $img = self::toTrueColorWithAlpha($src);
        $img = self::downscale($img);

        $box = self::contentBox($img);
        if ($box !== null) {
            [$x1, $y1, $x2, $y2] = $box;
            $w = imagesx($img);
            $h = imagesy($img);
            $pad = (int) round(max($x2 - $x1, $y2 - $y1) * self::PADDING_RATIO);
            $x1 = max(0, $x1 - $pad);
            $y1 = max(0, $y1 - $pad);
            $x2 = min($w - 1, $x2 + $pad);
            $y2 = min($h - 1, $y2 + $pad);

            $cropped = imagecrop($img, ['x' => $x1, 'y' => $y1, 'width' => $x2 - $x1 + 1, 'height' => $y2 - $y1 + 1]);
            if ($cropped instanceof GdImage) {
                imagesavealpha($cropped, true);
                $img = $cropped;
            }
        }

        imagesavealpha($img, true);

        return imagepng($img, $absolutePath, 6);
    }

    private static function toTrueColorWithAlpha(GdImage $src): GdImage
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);

        return $dst;
    }

    private static function downscale(GdImage $img): GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $scale = self::MAX_SIDE / max($w, $h);
        if ($scale >= 1) {
            return $img;
        }

        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

        return $dst;
    }

    /**
     * @return array{0:int,1:int,2:int,3:int}|null  [x1, y1, x2, y2] of the visible content
     */
    private static function contentBox(GdImage $img): ?array
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $minX = $w;
        $minY = $h;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($img, $x, $y);
                $alpha = ($c >> 24) & 0x7F;
                if ($alpha >= 110) {
                    continue;
                }
                $r = ($c >> 16) & 0xFF;
                $g = ($c >> 8) & 0xFF;
                $b = $c & 0xFF;
                if ($r >= 245 && $g >= 245 && $b >= 245) {
                    continue;
                }
                if ($x < $minX) {
                    $minX = $x;
                }
                if ($x > $maxX) {
                    $maxX = $x;
                }
                if ($y < $minY) {
                    $minY = $y;
                }
                $maxY = $y;
            }
        }

        if ($maxX < 0) {
            return null;
        }

        return [$minX, $minY, $maxX, $maxY];
    }
}
