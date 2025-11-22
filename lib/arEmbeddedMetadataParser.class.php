<?php

/*
 * Embedded metadata parser for EXIF, IPTC and XMP
 */

class arEmbeddedMetadataParser
{
    /**
     * Extract EXIF, IPTC, and XMP metadata from a file
     */
    public static function extract($filePath)
    {
        $out = [];

        if (!file_exists($filePath)) {
            return null;
        }

        //
        // EXIF
        //
        try {
            $exif = @exif_read_data($filePath, null, true, false);
            if (is_array($exif)) {
                foreach ($exif as $section => $data) {
                    foreach ($data as $key => $val) {
                        $out[$key] = $val;
                    }
                }
            }
        } catch (Exception $e) {
            // ignore
        }

        //
        // IPTC
        //
        $info = [];
        @getimagesize($filePath, $info);
        if (!empty($info["APP13"])) {
            $iptc = @iptcparse($info["APP13"]);
            if (is_array($iptc)) {
                foreach ($iptc as $tag => $vals) {
                    $flat = implode(', ', $vals);
                    $out[$tag] = $flat;
                }
            }
        }

        //
        // XMP
        //
        $raw = @file_get_contents($filePath);
        if ($raw && strpos($raw, '<x:xmpmeta') !== false) {
            $start = strpos($raw, '<x:xmpmeta');
            $end   = strpos($raw, '</x:xmpmeta>');
            if ($end !== false) {
                $end += strlen('</x:xmpmeta>');
                $xml = substr($raw, $start, $end - $start);
                try {
                    $xmp = @simplexml_load_string($xml);
                    if ($xmp) {
                        $out['_xmp'] = json_decode(json_encode($xmp), true);
                    }
                } catch (Exception $e) {}
            }
        }

        return $out;
    }


    /**
     * Simple textual summary used in technical metadata
     */
    public static function formatSummary($meta)
    {
        if (!$meta || !is_array($meta)) {
            return '';
        }

        $out = "Technical Metadata:\n";

        $fields = [
            'Make' => 'Camera Make',
            'Model' => 'Camera Model',
            'FNumber' => 'Aperture',
            'ExposureTime' => 'Shutter Speed',
            'ISO' => 'ISO',
            'FocalLength' => 'Focal Length',
            'ImageWidth' => 'Width',
            'ImageHeight' => 'Height'
        ];

        foreach ($fields as $key => $label) {
            if (!empty($meta[$key])) {
                $out .= "$label: {$meta[$key]}\n";
            }
        }

        return trim($out);
    }
}
