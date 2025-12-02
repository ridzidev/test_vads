<?php

namespace App\Services;

use Illuminate\Support\Str;

class CaptchaService
{
    /**
     * Generate CAPTCHA image
     */
    public static function generate(): array
    {
        $width = 200;
        $height = 50;
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $background = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $lineColor = imagecolorallocate($image, 200, 200, 200);
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        
        // Add noise lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }
        
        // Generate random string
        $captchaText = self::generateRandomText();
        
        // Add text to image
        $font = __DIR__ . '/../../resources/fonts/arial.ttf'; // You can use built-in fonts if TTF not available
        
        // Use built-in font if TTF doesn't exist
        if (!file_exists($font)) {
            // Use built-in font
            $x = 10;
            $y = 25;
            imagestring($image, 5, $x, $y, $captchaText, $textColor);
        } else {
            $bbox = imagettfbbox(20, 0, $font, $captchaText);
            $x = (($width - ($bbox[2] - $bbox[0])) / 2);
            $y = (($height - ($bbox[1] - $bbox[7])) / 2) + 15;
            imagettftext($image, 20, 0, $x, $y, $textColor, $font, $captchaText);
        }
        
        // Add random dots
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255)));
        }
        
        // Output
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        // Encode to base64
        $base64 = base64_encode($imageData);
        
        return [
            'image' => 'data:image/png;base64,' . $base64,
            'code' => $captchaText,
        ];
    }
    
    /**
     * Generate random text for CAPTCHA
     */
    private static function generateRandomText(int $length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $captcha = '';
        
        for ($i = 0; $i < $length; $i++) {
            $captcha .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $captcha;
    }
    
    /**
     * Verify CAPTCHA
     */
    public static function verify(string $submitted, string $stored): bool
    {
        return strtoupper($submitted) === strtoupper($stored);
    }
}
