<?php

namespace App\Services;

use InvalidArgumentException;

class HashVisualizerService
{
    /**
     * Supported algorithms and their educational metadata.
     * 'toy' is a deliberately weak demonstration hash (NOT cryptographic) used
     * to let learners witness real, reproducible collisions safely.
     */
    public const ALGORITHMS = [
        'md5' => [
            'label' => 'MD5',
            'bits' => 128,
            'status' => 'broken',
            'status_label' => 'Broken (collision attacks demonstrated)',
            'note' => 'Practical collision attacks against MD5 have been published since 2004. It should not be used where collision resistance matters.',
        ],
        'sha1' => [
            'label' => 'SHA-1',
            'bits' => 160,
            'status' => 'deprecated',
            'status_label' => 'Deprecated (collision found in 2017)',
            'note' => 'Google and CWI Amsterdam demonstrated the first full SHA-1 collision ("SHAttered") in 2017. Modern systems should avoid it.',
        ],
        'sha256' => [
            'label' => 'SHA-256',
            'bits' => 256,
            'status' => 'secure',
            'status_label' => 'Currently secure',
            'note' => 'Part of the SHA-2 family recommended by NIST. No practical collision attack is known.',
        ],
        'toy' => [
            'label' => 'Toy Sum-8 (demo only)',
            'bits' => 8,
            'status' => 'toy',
            'status_label' => 'Intentionally weak - for demonstration only',
            'note' => 'Adds up the byte values of the input modulo 256. Its output space is tiny, so collisions are easy to find. Never use this for anything real.',
        ],
    ];

    public function isSupported(string $algorithm): bool
    {
        return array_key_exists($algorithm, self::ALGORITHMS);
    }

    public function info(string $algorithm): array
    {
        if (! $this->isSupported($algorithm)) {
            throw new InvalidArgumentException("Unsupported algorithm: {$algorithm}");
        }

        return self::ALGORITHMS[$algorithm];
    }

    /**
     * Compute the hex digest of the input using the requested algorithm.
     */
    public function digest(string $algorithm, string $input): string
    {
        if (! $this->isSupported($algorithm)) {
            throw new InvalidArgumentException("Unsupported algorithm: {$algorithm}");
        }

        if ($algorithm === 'toy') {
            $sum = 0;
            foreach (unpack('C*', $input) ?: [] as $byte) {
                $sum = ($sum + $byte) % 256;
            }

            return str_pad(dechex($sum), 2, '0', STR_PAD_LEFT);
        }

        return hash($algorithm, $input);
    }

    /**
     * Compare two equal-algorithm hex digests bit-by-bit for avalanche-effect
     * visualization. Returns per-bit match data plus a summary.
     */
    public function bitDifference(string $hexA, string $hexB): array
    {
        $binA = $this->hexToBinaryString($hexA);
        $binB = $this->hexToBinaryString($hexB);

        $length = max(strlen($binA), strlen($binB));
        $binA = str_pad($binA, $length, '0', STR_PAD_LEFT);
        $binB = str_pad($binB, $length, '0', STR_PAD_LEFT);

        $bits = [];
        $diffCount = 0;

        for ($i = 0; $i < $length; $i++) {
            $same = $binA[$i] === $binB[$i];
            if (! $same) {
                $diffCount++;
            }

            $bits[] = [
                'a' => $binA[$i],
                'b' => $binB[$i],
                'same' => $same,
            ];
        }

        return [
            'bits' => $bits,
            'total_bits' => $length,
            'differing_bits' => $diffCount,
            'differing_percentage' => $length > 0 ? round(($diffCount / $length) * 100, 2) : 0.0,
        ];
    }

    private function hexToBinaryString(string $hex): string
    {
        $binary = '';
        foreach (str_split($hex) as $char) {
            $binary .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }

        return $binary;
    }
}
