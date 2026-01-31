<?php

namespace App\Services;

class DistritoMunicipalGeoService
{
    private ?array $districts = null;

    public function getDistricts(): array
    {
        if ($this->districts !== null) {
            return $this->districts;
        }

        $path = storage_path('app/public/geo/distritos_municipales.geojson');
        if (!file_exists($path)) {
            $this->districts = [];
            return $this->districts;
        }

        $features = cache()->remember('geojson_distritos_municipales_sc', 3600, function () use ($path) {
            $json = json_decode(file_get_contents($path), true);
            return is_array($json) ? ($json['features'] ?? []) : [];
        });

        $districts = [];
        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'] ?? [];

            if (($geometry['type'] ?? null) !== 'MultiPolygon') {
                continue;
            }

            $label = $props['etiqueta'] ?? 'Distrito';
            if (!empty($props['dm_ext'])) {
                $label .= ' - ' . $props['dm_ext'];
            }

            $polygons = $geometry['coordinates'] ?? [];
            $bbox = $this->bboxForMultiPolygon($polygons);

            $districts[] = [
                'id' => $props['dm_id'] ?? null,
                'label' => $label,
                'polygons' => $polygons,
                'bbox' => $bbox,
            ];
        }

        $this->districts = $districts;
        return $this->districts;
    }

    public function findDistrict(float $lng, float $lat): ?array
    {
        $districts = $this->getDistricts();
        $point = [$lng, $lat];

        foreach ($districts as $district) {
            [$minX, $minY, $maxX, $maxY] = $district['bbox'];
            if ($lng < $minX || $lng > $maxX || $lat < $minY || $lat > $maxY) {
                continue;
            }
            if ($this->pointInMultiPolygon($point, $district['polygons'])) {
                return $district;
            }
        }

        return null;
    }

    private function bboxForMultiPolygon(array $multi): array
    {
        $minX = $minY = PHP_FLOAT_MAX;
        $maxX = $maxY = -PHP_FLOAT_MAX;

        foreach ($multi as $polygon) {
            foreach ($polygon as $ring) {
                foreach ($ring as $point) {
                    $x = $point[0] ?? null;
                    $y = $point[1] ?? null;
                    if ($x === null || $y === null) {
                        continue;
                    }
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        return [$minX, $minY, $maxX, $maxY];
    }

    private function pointInRing(array $point, array $ring): bool
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;

        $count = count($ring);
        if ($count < 3) {
            return false;
        }

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $ring[$i][0] ?? null;
            $yi = $ring[$i][1] ?? null;
            $xj = $ring[$j][0] ?? null;
            $yj = $ring[$j][1] ?? null;

            if ($xi === null || $yi === null || $xj === null || $yj === null) {
                continue;
            }

            $intersect = (($yi > $y) !== ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-12) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    private function pointInPolygon(array $point, array $polygon): bool
    {
        if (empty($polygon)) {
            return false;
        }

        $outer = $polygon[0] ?? [];
        if (!$this->pointInRing($point, $outer)) {
            return false;
        }

        for ($i = 1; $i < count($polygon); $i++) {
            if ($this->pointInRing($point, $polygon[$i])) {
                return false;
            }
        }

        return true;
    }

    private function pointInMultiPolygon(array $point, array $multi): bool
    {
        foreach ($multi as $polygon) {
            if ($this->pointInPolygon($point, $polygon)) {
                return true;
            }
        }

        return false;
    }
}
