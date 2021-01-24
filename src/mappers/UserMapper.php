<?php


require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserDto.php';
class UserMapper
{
    public function mapAssocToDto($record)
    {
        if ($record == false) {
            return null;
        }

        return new UserDto(
            $record['id'],
            $record['email'],
            $record['username'],
            $record['image'],
            $record['description'],
            $record['rank'],
            $record['elo']
        );
    }

    public function mapMultipleAssocToDto($records)
    {
        if ($records == false) {
            return null;
        }

        $users = array();
        foreach ($records as $record) {
            $users[] = $this->mapAssocToDto($record);
        }
        return $users;
    }

    public function mapAssocArrayToUser($record)
    {
        if ($record == false) {
            return null;
        }

        return new User(
            $record['id'],
            $record['email'],
            $record['username'],
            $record['password'],
            $record['image'],
            $record['created_at'],
            $record['id_rank'],
            $record['id_user_details']
        );
    }
}