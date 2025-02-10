<?php
namespace App\Mapper;

use App\DTO\DateDTO;
use App\Entity\Date;

class DateMapper
{
    public static function toDTO(Date $date): DateDTO
    {
        $dto = new DateDTO();
        $dto->setName($date->getName());
        return $dto;
    }

    public static function toEntity(DateDTO $dto, ?Date $date = null): Date
    {
        $date = $date ?? new Date();
        $date->setName($dto->getName());
        return $date;
    }

}