# Tankulan Boundary Workflow (No Official OSM Polygon)

This guide documents a safe fallback when Tankulan has no official polygon boundary in OpenStreetMap (OSM).

## Goal

- Confirm whether an OSM administrative boundary exists.
- Continue internal mapping and analytics using a temporary polygon.
- Keep provenance clear so temporary geometry is not confused with official OSM data.

## 1) Verify OSM Coverage

Run this in Overpass Turbo:

```overpass
[out:json][timeout:60];
area["name"="Philippines"]["boundary"="administrative"]->.ph;

(
  rel(area.ph)["boundary"="administrative"]["name"~"Tankulan",i];
  way(area.ph)["boundary"="administrative"]["name"~"Tankulan",i];
  node(area.ph)["name"~"Tankulan",i];
);
out center tags;

node(area.ph)["name"~"Tankulan",i]->.t;
rel(around.t:15000)["boundary"="administrative"];
out tags center;
```

Expected case right now: only place node(s), no admin boundary relation for Tankulan.

## 2) Temporary Boundary Policy

Use temporary geometry only for internal work when no official OSM boundary exists.

Required metadata fields in GeoJSON properties:

- `source`: `provisional`
- `source_note`: `no official OSM polygon as of 2026-03-25`
- `do_not_upload_to_osm`: `true`
- `name`: `Tankulan (provisional)`
- `pin_lat`: `<latitude of canonical Tankulan pin>`
- `pin_lon`: `<longitude of canonical Tankulan pin>`

Recommended filename:

- `data/boundaries/temporary_boundary_tankulan.geojson`

## 3) Build Temporary Polygon (Example)

If you have a local shapefile source for nearby administrative limits:

```bash
ogr2ogr -f GeoJSON data/boundaries/temporary_boundary_tankulan.geojson source/admin_boundaries.shp -dialect sqlite -sql "SELECT geometry, 'provisional' AS source, 'no official OSM polygon as of 2026-03-25' AS source_note, 'true' AS do_not_upload_to_osm, 'Tankulan (provisional)' AS name, 7.447000 AS pin_lat, 125.809000 AS pin_lon FROM admin_boundaries WHERE NAME LIKE '%Tankulan%'"
```

If no polygon is available, create a point-buffer fallback around a validated center point:

```bash
ogr2ogr -f GeoJSON data/boundaries/temporary_boundary_tankulan.geojson source/tankulan_point.geojson -dialect sqlite -sql "SELECT ST_Buffer(geometry, 0.01) AS geometry, 'provisional' AS source, 'no official OSM polygon as of 2026-03-25' AS source_note, 'true' AS do_not_upload_to_osm, 'Tankulan (provisional)' AS name, 7.447000 AS pin_lat, 125.809000 AS pin_lon FROM tankulan_point"
```

Note: buffer distance must be chosen for your CRS and use case.

## 4) QA Checklist

- Geometry is valid and non-self-intersecting.
- CRS and units are documented.
- Feature has required provisional metadata.
- File is excluded from any OSM upload pipeline.

## 5) Upgrading to Official OSM Data Later

When an official OSM boundary relation appears:

- Replace provisional polygon with official relation geometry.
- Keep a changelog note with replacement date and source relation id.
- Remove provisional warning flags from downstream reports.