<?php declare(strict_types=1);


namespace My\ConvertCurrToUsd\Service;


use Doctrine\DBAL\Connection;


class ConvertService
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCurrencyFactor(string $iso)
    {
        $stmt = $this->connection->prepare(
            'SELECT factor FROM currency WHERE LOWER(iso_code) = LOWER(?)'
        );
        $stmt->execute([$iso]);
        $fetchCountryId = $stmt->fetchColumn();
        if (!$fetchCountryId) {
            throw new \RuntimeException('Country with iso-code ' . $iso . ' not found');
        }

        return $fetchCountryId;
    }

}
