<?php

namespace App\Support;

class PhotoPool
{
    public const HERO = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1600&q=80';

    /** @var array<string, list<string>> */
    public const POOLS = [
        'house' => [
            '1568605114967-8130f3a36994',
            '1570129477492-45c003edd2be',
            '1600585154340-be6161a56a0c',
            '1523217582562-09d0def993a6',
            '1449844908441-8829872d2607',
            '1518780664697-55e3ad937233',
            '1583608205776-bfd35f0d9f83',
        ],
        'villa' => [
            '1512917774080-9991f1c4c750',
            '1600596542815-ffad4c1539a9',
            '1580587771525-78b9dba3b914',
            '1613490493576-7fde63acd811',
            '1564013799919-ab600027ffc6',
            '1416331108676-a22ccb276e35',
        ],
        'apartment' => [
            '1600607687939-ce8a6c25118c',
            '1560448204-e02f11c3d0e2',
            '1522708323590-d24dbb6b0267',
            '1560185007-cde436f6a4d0',
            '1484154218962-a197022b5858',
            '1493809842364-78817add7ffb',
            '1554995207-c18c203602cb',
            '1545324418-cc1a3fa10c00',
            '1502005097973-6a7082348e28',
            '1574362848149-11496d93a7c7',
        ],
        'land' => [
            '1747854805840-9be7d5e360e6',
            '1587745890135-20db8c79b027',
            '1655806258669-a21cfe733768',
            '1600270074098-f51a52d71a37',
            '1500382017468-9049fed747ef',
            '1441974231531-c6227db76b6e',
        ],
        'commercial' => [
            '1486406146926-c627a92ad1ab',
            '1497366754035-f200968a6e72',
        ],
    ];

    /** @var array<string, string> keyword order matters — specific words first */
    private const KEYWORDS = [
        'penthouse' => 'apartment',
        'warehouse' => 'commercial',
        'office' => 'commercial',
        'mini flat' => 'apartment',
        'flat' => 'apartment',
        'studio' => 'apartment',
        'apartment' => 'apartment',
        'villa' => 'villa',
        'mansion' => 'villa',
        'duplex' => 'house',
        'bungalow' => 'house',
        'terrace' => 'house',
        'cottage' => 'house',
        'house' => 'house',
        'plot' => 'land',
        'land' => 'land',
    ];

    public static function poolFor(?string $label): ?string
    {
        $label = strtolower(trim((string) $label));

        if ($label === '') {
            return null;
        }

        foreach (self::KEYWORDS as $keyword => $pool) {
            if (str_contains($label, $keyword)) {
                return $pool;
            }
        }

        return null;
    }

    public static function url(?string $pool, int|string $seed, int $index = 0): string
    {
        $ids = self::POOLS[$pool] ?? self::POOLS['house'];
        $id = $ids[(abs(crc32((string) $seed)) + $index) % count($ids)];

        return self::photoUrl($id);
    }

    public static function forLabel(?string $label, int|string $seed, int $index = 0): string
    {
        return self::url(self::poolFor($label) ?? 'house', $seed, $index);
    }

    public static function blog(int|string $seed): string
    {
        $ids = array_merge(
            self::POOLS['house'],
            self::POOLS['villa'],
            self::POOLS['apartment'],
            self::POOLS['commercial'],
        );

        return self::photoUrl($ids[abs(crc32((string) $seed)) % count($ids)]);
    }

    private static function photoUrl(string $id): string
    {
        return 'https://images.unsplash.com/photo-'.$id.'?auto=format&fit=crop&w=1200&q=80';
    }
}
