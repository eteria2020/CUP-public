<?php

return array(
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'naming_strategy' => new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(),
                'types' => array(
                    'geometry' => 'CrEOF\Spatial\DBAL\Types\GeometryType',
                    'point' => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
                    'polygon' => 'CrEOF\Spatial\DBAL\Types\Geometry\PolygonType',
                    'linestring' => 'CrEOF\Spatial\DBAL\Types\Geometry\LineStringType',
                ),
                'numeric_functions' => [
                    'ST_AsGeoJson' => 'CrEOF\Spatial\ORM\Query\AST\Functions\PostgreSql\STAsGeoJson'
                ],
                'datetime_functions' => [
                    'DATE_FORMAT' => 'DoctrineExtensions\Query\Postgresql\DateFormat'
                ]
            ),
        ),
    ),
);
